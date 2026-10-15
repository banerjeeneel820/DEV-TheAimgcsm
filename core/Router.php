<?php

namespace App\Core;

defined('ROOTPATH') or exit('No direct script access allowed');

class Router
{
    private static ?Container $container = null;

    public static function handle(Container $container): void
    {
        self::$container = $container;

        $httpMethod = strtoupper(Request::method());
        $isAjax = self::isAjax();

        if ($httpMethod === 'GET') {
            $routePath = self::resolveGetRoute();
            $routeMethod = 'GET';
        } elseif ($httpMethod === 'POST' && $isAjax) {
            $routePath = trim(Request::path(), '/');
            $routeMethod = 'AJAX';
        } elseif ($httpMethod === 'POST') {
            $routePath = trim(Request::path(), '/');
            $routeMethod = 'POST';
        } else {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        $routePath = trim($routePath, '/');

        $routeDefinition = RouteRegistry::match(
            $routeMethod,
            $routePath
        );

        $requestData = Request::all();
        $requestData['route'] = $routePath;

        if ($routeDefinition !== null) {
            $middleware = $routeDefinition['middleware'] ?? [];

            $middlewareDispatcher = new MiddlewareDispatcher($container);

            $result = $middlewareDispatcher->run(
                $middleware,
                $requestData,
                function (array $request) use (
                    $routeMethod,
                    $routePath
                ) {
                    return self::dispatchMatchedRoute(
                        $routeMethod,
                        $routePath,
                        $request
                    );
                }
            );

            // Middleware returned false: the request was already handled
            // (for example, with a redirect or a 401 JSON response).
            if ($result === false) {
                return;
            }

            return;
        }

        self::handleMissingRoute($routeMethod, $routePath);
    }

    private static function dispatchMatchedRoute(
        string $routeMethod,
        string $routePath,
        array $request
    ): bool {
        $container = self::$container;

        if ($container === null) {
            throw new \RuntimeException('Router container is not initialized.');
        }

        if ($routeMethod === 'GET') {
            $dispatcher = new RouteDispatcher($container);

            $pageData = $dispatcher->get_PageContent($request);

            $viewEngine = new ViewEngine(
                $container,
                $routePath,
                $pageData
            );

            $viewEngine->render();

            return true;
        }

        if ($routeMethod === 'AJAX') {
            $dispatcher = new AjaxDispatcher($container);
            $dispatcher->handle($request);

            return true;
        }

        if ($routeMethod === 'POST') {
            $dispatcher = new PostDispatcher($container);
            $dispatcher->handle($request);

            return true;
        }

        http_response_code(405);
        echo 'Method Not Allowed';

        return true;
    }

    private static function handleMissingRoute(
        string $routeMethod,
        string $routePath
    ): void {
        if ($routeMethod === 'AJAX') {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Route not found.',
            ]);
            return;
        }

        if ($routeMethod === 'POST') {
            http_response_code(404);
            echo 'Route Not Found';
            return;
        }

        $container = self::$container;

        if ($container === null) {
            http_response_code(404);
            echo 'Page Not Found';
            return;
        }

        $dispatcher = new RouteDispatcher($container);

        $pageData = $dispatcher->get_PageContent([
            'route' => $routePath,
        ]);

        $viewEngine = new ViewEngine(
            $container,
            $routePath,
            $pageData
        );

        $viewEngine->render();
    }

    private static function resolveGetRoute(): string
    {
        $path = trim(Request::path(), '/');

        // A root URL can still use a query-string route during migration,
        // e.g. /?route=login.
        if ($path === '' || $path === 'index.php') {
            return trim((string) Request::get('route', 'home'), '/');
        }

        return $path;
    }

    private static function isAjax(): bool
    {
        return strtolower(
            $_SERVER['HTTP_X_REQUESTED_WITH'] ?? ''
        ) === 'xmlhttprequest';
    }
}
