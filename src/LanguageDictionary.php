<?php

class LanguageDictionary {

    private $dictionary;

    public function __construct() {
        $this->dictionary = \JSONObject::getAll(\Config::Dictionary);
    }

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


    private function flush(): void {
        file_put_contents(APP_ROOT . '/config/lang/dictionary.json',
            json_encode($this->dictionary));
    }
}