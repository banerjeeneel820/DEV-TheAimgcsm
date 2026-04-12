<?php
defined('ROOTPATH') OR exit('No direct script access allowed');

class Request
{
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function get($key = null, $default = null)
    {
        if ($key === null) return $_GET;
        return $_GET[$key] ?? $default;
    }

    public static function post($key = null, $default = null)
    {
        if ($key === null) return $_POST;
        return $_POST[$key] ?? $default;
    }

    public static function input($key, $default = null)
    {
        return self::post($key) ?? self::get($key) ?? $default;
    }

    public static function all()
    {
        return array_merge($_GET, $_POST);
    }
}