<?php

class JSONObject {

    public function __construct($config, $name) {
        foreach (self::loadJSON($config) as $node) {
            if ($node['name'] == $name) {
                foreach ($node as $key => $value) {
                    $this->{$key} = $value;
                }
            }
        }
    }

    /**
     * @param $config
     * @return mixed A JSON config file in full
     */
    public static function getAllItems($config) {
        return self::loadJSON($config);
    }

    /**
     * @param $config
     * @return array All elements' Name and Display fields in the config file $config.
     */
    public static function getItemsMetadata($config) {
        $items = [];
        foreach (self::loadJSON($config) as $node) {
            $items[] = [
                'name'      => $node['name'],
                'display'   => $node['display'],
            ];
        }
        return $items;
    }

    private static function loadJSON($config) {
        return json_decode(file_get_contents(APP_ROOT . "/config/$config.json"), true)[$config];
    }
}