<?php
use App\Core\Route;

defined('ROOTPATH') or exit('No direct script access allowed');

if (!function_exists('is_ssl')) {
    function is_ssl()
    {
        if (
            (isset($_SERVER['HTTPS']) &&
                ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1'))
            ||
            (isset($_SERVER['SERVER_PORT']) &&
                $_SERVER['SERVER_PORT'] == 443)
        ) {
            return true;
        }

        if (
            (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
                $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
            ||
            (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) &&
                $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
        ) {
            return true;
        }

        return false;
    }
}    

if (!function_exists('dd')) {
    function dd(...$vars)
    {
    echo '<pre style="
            background:#1e1e1e;
            color:#dcdcdc;
            padding:15px;
            border-radius:8px;
            font-size:14px;
            line-height:1.5;
            overflow:auto;
        ">';

    foreach ($vars as $index => $var) {
        echo "🔹 Variable " . ($index + 1) . ":\n\n";
        var_dump($var);
        echo "\n\n--------------------------\n\n";
    }

    echo '</pre>';

    die();
    }
}    

if (!function_exists('route')) {
    function route(string $name, array $parameters = []): string
    {
        return htmlspecialchars(
            Route::url($name, $parameters),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}