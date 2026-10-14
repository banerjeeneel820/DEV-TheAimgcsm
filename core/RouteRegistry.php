<?php

namespace App\Core;

defined('ROOTPATH') or exit('No direct script access allowed');

class RouteRegistry
{
    private static ?array $routes = null;

    public static function all(): array
    {
        if (self::$routes !== null) {
            return self::$routes;
        }

        // Load legacy routes first.
        $legacy = [
            'get' => require ROOTPATH . '/config/page_routes.php',
            'ajax' => require ROOTPATH . '/config/ajax_routes.php',
            'post' => require ROOTPATH . '/config/post_routes.php',
        ];

        // Register new-style routes.
        if (!Route::isLoaded()) {
            require ROOTPATH . '/config/routes.php';
            Route::markLoaded();
        }

        $registered = Route::all();

        // Merge new routes into their respective legacy groups.
        foreach ($registered as $route) {
            $group = match ($route['method']) {
                'GET' => 'get',
                'AJAX' => 'ajax',
                'POST' => 'post',
                default => null,
            };

            if ($group === null) {
                continue;
            }

            $legacy[$group][$route['uri']] = $route['action'];
        }

        self::$routes = $legacy;

        return self::$routes;
    }

    public static function clear(): void
    {
        self::$routes = null;
        Route::clear();
    }
}