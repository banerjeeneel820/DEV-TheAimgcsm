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

        if (!Route::isLoaded()) {
            require ROOTPATH . '/config/routes.php';
            Route::markLoaded();
        }

        self::$routes = Route::all();

        return self::$routes;
    }

    public static function match(string $method, string $uri): ?array
    {
        $method = strtoupper($method);
        $uri = trim($uri, '/');

        foreach (self::all() as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (trim($route['uri'], '/') === $uri) {
                return $route;
            }
        }

        return null;
    }

    public static function clear(): void
    {
        self::$routes = null;
        Route::clear();
    }
}
