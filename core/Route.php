<?php

namespace App\Core;

defined('ROOTPATH') or exit('No direct script access allowed');

class Route
{
    private static array $routes = [];
    private static array $groups = [];
    private static bool $loaded = false;

    /**
     * Register a GET route.
     */
    public static function get(string $uri, array $action): RouteDefinition
    {
        return self::register('GET', $uri, $action);
    }

    /**
     * Register a normal POST route.
     */
    public static function post(string $uri, array $action): RouteDefinition
    {
        return self::register('POST', $uri, $action);
    }

    /**
     * Register an AJAX route.
     */
    public static function ajax(string $uri, array $action): RouteDefinition
    {
        return self::register('AJAX', $uri, $action);
    }

    /**
     * Register a route definition.
     */
    private static function register(
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
                'Route action must contain a controller class and method.'
            );
        }

        $group = self::getGroupAttributes();

        $uri = trim(
            $group['prefix'] . '/' . trim($uri, '/'),
            '/'
        );

        $definition = new RouteDefinition(
            $method,
            $uri,
            $action,
            $group['namePrefix']
        );

        self::$routes[] = $definition;

        return $definition;
    }

    /**
     * Assign a name to a registered route.
     */
    public static function assignName(
        RouteDefinition $definition,
        string $name
    ): void {
        foreach (self::$routes as $route) {
            if ($route !== $definition && $route->getName() === $name) {
                throw new \InvalidArgumentException(
                    "Duplicate route name: {$name}"
                );
            }
        }

        $definition->setName($name);
    }

    /**
     * Return all registered routes.
     */
    public static function all(): array
    {
        return array_map(
            static fn (RouteDefinition $route) => $route->toArray(),
            self::$routes
        );
    }

    /**
     * Reset the registry.
     * Useful for isolated tests.
     */
    public static function clear(): void
    {
        self::$routes = [];
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

    /**
     * Generate a URL from a named route.
     *
     * Parameters matching {parameter} become path segments.
     * Remaining parameters become query-string parameters.
     */
    public static function url(string $name, array $parameters = []): string
    {
        // Ensure route definitions have been loaded.
        RouteRegistry::all();

        // Search the actual registered route definitions.
        foreach (self::all() as $route) {
            if (($route['name'] ?? null) !== $name) {
                continue;
            }

            $path = $route['uri'];

            // Replace parameters such as {id} in the route path.
            $path = preg_replace_callback(
                '/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/',
                function ($matches) use (&$parameters, $name) {
                    $key = $matches[1];

                    if (
                        !array_key_exists($key, $parameters) ||
                        !is_scalar($parameters[$key])
                    ) {
                        throw new \InvalidArgumentException(
                            "Missing or invalid parameter '{$key}' for route '{$name}'."
                        );
                    }

                    $value = rawurlencode((string) $parameters[$key]);
                    unset($parameters[$key]);

                    return $value;
                },
                $path
            );

            $url = rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');

            // Remaining parameters become query-string parameters.
            $query = http_build_query(
                $parameters,
                '',
                '&',
                PHP_QUERY_RFC3986
            );

            if ($query !== '') {
                $url .= '?' . $query;
            }

            return $url;
        }

        throw new \InvalidArgumentException(
            "Named route '{$name}' not found."
        );
    }

    // Constructing group routes
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
        $prefix = '';
        $namePrefix = '';

        foreach (self::$groups as $group) {
            $groupPrefix = trim($group['prefix'] ?? '', '/');

            if ($groupPrefix !== '') {
                $prefix = trim($prefix . '/' . $groupPrefix, '/');
            }

            // Support both "as" and "name" for the name prefix.
            $currentNamePrefix = $group['as']
                ?? $group['name']
                ?? '';

            if ($currentNamePrefix !== '') {
                $namePrefix .= rtrim($currentNamePrefix, '.') . '.';
            }
        }

        return [
            'prefix' => $prefix,
            'namePrefix' => $namePrefix,
        ];
    }
}

/**
 * Represents one registered route.
 */
class RouteDefinition
{
    private string $method;
    private string $uri;
    private array $action;
    private ?string $name = null;
    private string $namePrefix;

    public function __construct(
        string $method,
        string $uri,
        array $action,
        string $namePrefix = ''
    ) {
        $this->method = $method;
        $this->uri = $uri;
        $this->action = $action;
        $this->namePrefix = $namePrefix;
    }

    /**
     * Give this route a name.
     */
    public function name(string $name): self
    {
        $name = trim($name);

        if ($name === '') {
            throw new \InvalidArgumentException(
                'Route name cannot be empty.'
            );
        }

        Route::assignName(
            $this,
            $this->namePrefix . ltrim($name, '.')
        );

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function toArray(): array
    {
        return [
            'method' => $this->method,
            'uri'    => $this->uri,
            'action' => $this->action,
            'name'   => $this->name,
        ];
    }
}
