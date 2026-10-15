<?php

namespace App\Core;

defined('ROOTPATH') or exit('No direct script access allowed');

class MiddlewareDispatcher
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function run(
        array $middlewareClasses,
        array $request,
        callable $destination
    ) {
        $middlewareInstances = [];

        foreach ($middlewareClasses as $middlewareClass) {
            if (!class_exists($middlewareClass)) {
                throw new \RuntimeException(
                    "Middleware class not found: {$middlewareClass}"
                );
            }

            // Pass the current container, not a newly created Container.
            $middlewareInstances[] = new $middlewareClass($this->container);
        }

        $pipeline = array_reduce(
            array_reverse($middlewareInstances),
            function (callable $next, object $middleware): callable {
                return function (array $request) use ($middleware, $next) {
                    return $middleware->handle($request, $next);
                };
            },
            $destination
        );

        $response = null;

        try {
            $response = $pipeline($request);

            return $response;
        } finally {
            foreach (array_reverse($middlewareInstances) as $middleware) {
                if (method_exists($middleware, 'terminate')) {
                    $middleware->terminate($request, $response);
                }
            }
        }
    }
}
