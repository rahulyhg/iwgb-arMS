<?php

class JSONObject {

    /**
     * @param $config
     * @param $name
     * @return bool|mixed
     */
    public static function get($config, $name) {
        $data = self::getAll($config);
        foreach ($data as $item) {
            if ($item['name'] == $name) {
                return $item;
            }
        }
        return false;
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

    /**
     * @param $name
     * @return array
     */
    public static function getSchema($name) {
        return self::load("/public/config/schema/$name.schema.json");
    }

    private static function loadJSON($config) {
        return self::load("/public/config/$config.json")[$config];
    }

    private static function load($file) {
        return json_decode(file_get_contents(APP_ROOT . $file), true);
    }


}