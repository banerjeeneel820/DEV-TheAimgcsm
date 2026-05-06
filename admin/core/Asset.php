<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class Asset
{
    private static $config;

    public static function load($key)
    {
        if (!self::$config) {
            self::$config = require ROOTPATH . '/config/assets.php';
        }

        if (!isset(self::$config[$key])) {
            return ['css' => [], 'js' => []];
        }

        $item = self::$config[$key];

        // 🔥 If grouped assets
        if (isset($item['groups'])) {
            return self::mergeGroups($item['groups']);
        }

        return $item;
    }

    private static function mergeGroups($groups)
    {
        $css = [];
        $js = [];

        foreach ($groups as $group) {
            $asset = self::$config[$group] ?? [];

            $css = array_merge($css, $asset['css'] ?? []);
            $js = array_merge($js, $asset['js'] ?? []);
        }

        return [
            'css' => array_unique($css),
            'js' => array_unique($js)
        ];
    }
}