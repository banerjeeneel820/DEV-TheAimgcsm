<?php

namespace App\Core;

defined('ROOTPATH') or exit('No direct script access allowed');

class Route
{
    private static array $routes = [];
    private static array $groups = [];
    private static bool $loaded = false;

    public static function get(string $uri, array $action): RouteDefinition
    {
        return self::register('GET', $uri, $action);
    }

    public static function post(string $uri, array $action): RouteDefinition
    {
        return self::register('POST', $uri, $action);
    }

    public static function ajax(string $uri, array $action): RouteDefinition
    {
        return self::register('AJAX', $uri, $action);
    }

    public static function register(
        string $method,
        string $uri,
        array $action
    ): RouteDefinition {
        if (
            count($action) !== 2 ||
            !is_string($action[0]) ||
            !is_string($action[1])
        ) {
            throw new \InvalidArgumentException(
                'A route action must be [ControllerClass::class, "method"].'
            );
        }

        $group = self::getGroupAttributes();

        $prefix = trim($group['prefix'] ?? '', '/');
        $uri = trim($uri, '/');

        $fullUri = trim(
            ($prefix !== '' ? $prefix . '/' : '') . $uri,
            '/'
        );

        $route = new RouteDefinition(
            strtoupper($method),
            $fullUri,
            $action,
            $group['namePrefix'] ?? '',
            $group['middleware'] ?? []
        );

        self::$routes[] = $route;

        return $route;
    }

    public static function group(array $attributes, callable $callback): void
    {
        self::$groups[] = $attributes;

        try {
            $callback();
        } finally {
            array_pop(self::$groups);
        }
    }

    private static function getGroupAttributes(): array
    {
        $prefixes = [];
        $namePrefixes = [];
        $middleware = [];

        foreach (self::$groups as $group) {
            if (!empty($group['prefix'])) {
                $prefixes[] = trim($group['prefix'], '/');
            }

            if (!empty($group['as'])) {
                $namePrefixes[] = $group['as'];
            }

            $groupMiddleware = $group['middleware'] ?? [];

            if (is_string($groupMiddleware)) {
                $groupMiddleware = [$groupMiddleware];
            }

            foreach ($groupMiddleware as $middlewareClass) {
                if (!in_array($middlewareClass, $middleware, true)) {
                    $middleware[] = $middlewareClass;
                }
            }
        }

        return [
            'prefix' => implode('/', array_filter($prefixes)),
            'namePrefix' => implode('', $namePrefixes),
            'middleware' => $middleware,
        ];
    }

    public static function all(): array
    {
        return array_map(
            static fn (RouteDefinition $route) => $route->toArray(),
            self::$routes
        );
    }

    public static function clear(): void
    {
        self::$routes = [];
        self::$groups = [];
        self::$loaded = false;
    }

    public static function isLoaded(): bool
    {
        return self::$loaded;
    }

    public static function markLoaded(): void
    {
        self::$loaded = true;
    }

    public static function url(string $name, array $parameters = []): string
    {
        foreach (self::all() as $route) {
            if (($route['name'] ?? null) !== $name) {
                continue;
            }

            $uri = trim($route['uri'], '/');

            foreach ($parameters as $key => $value) {
                $uri = str_replace(
                    '{' . $key . '}',
                    rawurlencode((string) $value),
                    $uri
                );
            }

            if (preg_match('/\{[^}]+\}/', $uri)) {
                throw new \InvalidArgumentException(
                    "Missing parameters for route: {$name}"
                );
            }

            // Include the application's installation directory.
            $scriptName = str_replace(
                '\\',
                '/',
                $_SERVER['SCRIPT_NAME'] ?? '/index.php'
            );

            $basePath = rtrim(dirname($scriptName), '/');

            if ($basePath === '.' || $basePath === '/') {
                $basePath = '';
            }

            $url = $basePath . '/' . $uri;

            return '/' . ltrim($url, '/');
        }

        throw new \InvalidArgumentException(
            "Route not found: {$name}"
        );
    }
}

class RouteDefinition
{
    private string $method;
    private string $uri;
    private array $action;
    private ?string $name;
    private string $namePrefix;
    private array $middleware;

    public function __construct(
        string $method,
        string $uri,
        array $action,
        string $namePrefix = '',
        array $middleware = []
    ) {
        $this->method = strtoupper($method);
        $this->uri = trim($uri, '/');
        $this->action = $action;
        $this->namePrefix = $namePrefix;
        $this->name = null;
        $this->middleware = $middleware;
    }

    public function name(string $name): self
    {
        $this->name = $this->namePrefix . $name;

        return $this;
    }

    public function middleware(array|string $middleware): self
    {
        $middleware = is_array($middleware)
            ? $middleware
            : [$middleware];

        $this->middleware = array_values(
            array_unique(array_merge($this->middleware, $middleware))
        );

        return $this;
    }

    public function getMiddleware(): array
    {
        return $this->middleware;
    }

    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'uri' => $this->uri,
            'action' => $this->action,
            'name' => $this->name,
            'middleware' => $this->middleware,
        ];
    }
}
