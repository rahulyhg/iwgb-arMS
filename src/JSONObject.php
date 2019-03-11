<?php

class JSONObject {

    /**
     * @param string $config
     * @param string $name
     * @param bool $wrap Wrap the item as a 1-length array of $config (can be schema-compliant)
     * @return bool|array
     */
    public static function get(string $config, string $name, bool $wrap = false) {
        $item = self::findItem(self::getAll($config), $name);
        if ($wrap) {
            $item = [$config => [0 => $item]];
        }
        return $item;
    }

    /**
     * @param array $json
     * @param string $name
     * @return bool|array
     */
    public static function findItem(array $json, string $name) {
        foreach ($json as $item) {
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
        return self::load(APP_ROOT . "/public/config/schema/$name.schema.json");
    }

    private static function loadJSON($config) {
        return self::load(APP_ROOT . "/public/config/$config.json")[$config];
    }

    private static function load($file) {
        return json_decode(file_get_contents(APP_ROOT . $file), true);
    }


}