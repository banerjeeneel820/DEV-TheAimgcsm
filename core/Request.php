<?php
namespace App\Core;

defined('ROOTPATH') or exit('No direct script access allowed');

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

    public static function path()
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($requestUri, PHP_URL_PATH) ?: '/';

        // Root index.php is expected to be /theaimgcsm/index.php.
        $scriptName = str_replace(
            '\\',
            '/',
            $_SERVER['SCRIPT_NAME'] ?? '/index.php'
        );

        $basePath = rtrim(dirname($scriptName), '/');

        if ($basePath === '.' || $basePath === '/') {
            $basePath = '';
        }

        // Strip the project base path, not the application prefix.
        if (
            $basePath !== '' &&
            ($path === $basePath || strpos($path, $basePath . '/') === 0)
        ) {
            $path = substr($path, strlen($basePath));
        }

        $path = trim($path, '/');

        // A direct request to index.php uses legacy action routing.
        if ($path === 'index.php') {
            return '';
        }

        return $path;
    }
}
