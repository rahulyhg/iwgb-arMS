<?php

class LanguageDictionary {

    private $dictionary;

    public function __construct() {
        $this->dictionary = \JSONObject::getAll(\Config::Dictionary);
    }

    /**
     * If no translation is available, $s will be returned.
     * This method logs unsuccessful translation attempts as empty dictionary entries.
     *
     * @param $s string The string to be translated, in the fallback language.
     * @param $lang string The two-character language identifier of the target language.
     * @return string If possible, the translated string $s.
     */
    public function get($s, $lang): string {
        if (!empty($this->dictionary[$s])) {
            if (!empty($this->dictionary[$s][$lang])) {
                return $this->dictionary[$s][$lang];
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
     * Write any changes to the in-memory $dictionary to dictionary.json
     */
    private function flush(): void {
        file_put_contents(APP_ROOT . '/config/lang/dictionary.json',
            json_encode($this->dictionary));
    }
}