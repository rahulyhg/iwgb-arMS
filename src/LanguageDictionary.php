<?php

class LanguageDictionary {

    private $dictionary;
    private $language;
    private $fallback;

    public function __construct(string $language, string $fallback) {
        $this->dictionary = \JSONObject::getAll(\Config::Dictionary);
        $this->language = $language;
        $this->fallback = $fallback;

    }

    public function get($content): string {
        if (is_array($content)) {
            return $this->getContent($content);
        } else {
            return $this->getString($content);
        }
    }

    /**
     * If no translation is available, $s will be returned.
     * This method logs unsuccessful translation attempts as empty dictionary entries.
     *
     * @param $s string The string to be translated, in the fallback language.
     * @return string If possible, the translated string $s.
     */
    public function getString($s): string {
        if (!empty($this->dictionary[$s])) {
            if (!empty($this->dictionary[$s][$this->language])) {
                return $this->dictionary[$s][$this->language];
            } else {
                return $s;
            }
        } else {
            $this->dictionary[$s] = [];
            $this->flush();
            return $s;
        }
    }

    /**
     * @param $content array of translations conformant to content.schema.json
     * @return string
     */
    public function getContent($content): string {
        if (!empty($content[$this->language])) {
            return $content[$this->language];
        } else {
            return $content[$this->fallback];
        }
    }

    /**
     * Write any changes to the in-memory $dictionary to dictionary.json
     */
    private function flush(): void {
        file_put_contents(APP_ROOT . '/config/lang/dictionary.json',
            json_encode($this->dictionary));
    }
}