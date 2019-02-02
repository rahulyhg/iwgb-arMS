<?php

class JSONObject {

    /**
     * @param $config
     * @param $name
     * @return bool|mixed
     */
    public static function get($config, $name) {
        $data = self::getAll($config);
        return !empty($data[$name]) ? $data[$name] : false;
    }

    /**
     * @param $config
     * @return array A JSON config file in full
     */
    public static function getAll($config) {
        return self::loadJSON($config);
    }

    /**
     * @param $config
     * @return array All elements' Name and Display fields in the config file $config.
     */
    public static function getMetadata($config) {
        $items = [];
        foreach (self::loadJSON($config) as $node) {
            $items[] = [
                'name'      => $node['name'],
                'display'   => $node['display'],
            ];
        }
        return $items;
    }

    protected static function loadJSON($config) {
        return json_decode(file_get_contents(APP_ROOT . "/config/$config.json"), true)[$config];
    }
}