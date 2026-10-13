<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class Router
{
    protected static $container;

    public static function handle($container)
    {
        self::$container = $container; // store container

        $method = Request::method();

        if ($method === 'POST') {
            self::handlePost();
        } else {
            self::handleGet();
        }
    }

    private static function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    private static function handlePost()
    {
        if (self::isAjax()) {
            self::dispatch('AjaxDispatcher', 'handle', true);
        } else {
            self::dispatch('PostDispatcher', 'handle', false);
        }
    }


    private static function handleGet()
    {
        $requestData = Request::all();

        // Resolve either a path-based route or a legacy ?route= value.
        $route = self::resolveGetRoute();

        /*
     * Preserve the current authentication behavior for now.
     * We can introduce public frontend routes separately.
     */
        if (empty($_SESSION['user_id'])) {
            $route = 'login';
        }

        // Ensure RouteDispatcher receives the resolved route.
        $requestData['route'] = $route;

        $pageData = self::dispatch(
            'RouteDispatcher',
            'get_PageContent',
            false,
            $requestData
        );

        $view = new ViewEngine(self::$container, $route, $pageData);
        $view->render();

        exit;
    }

    private static function resolveGetRoute()
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($requestUri, PHP_URL_PATH) ?: '/';

        // Remove the project base directory, e.g. /theaimgcsm
        $basePath = rtrim(
            str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')),
            '/'
        );

        if ($basePath !== '' && $basePath !== '/' && strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
        }

        // Normalize the route
        $route = trim($path, '/');

        // Preserve legacy query-string routing
        if ($route === '' || $route === 'index.php') {
            return Request::get('route', 'home');
        }

        return $route;
    }


    /**
     * Central Dispatch Method
     */

    private static function dispatch(
        $controllerName,
        $method,
        $isAjax = false,
        $data = null
    ) {
        if (!class_exists($controllerName)) {
            self::error("Controller {$controllerName} not found");
        }

        // Controllers receive the container.
        $controller = new $controllerName(self::$container);

        if (!method_exists($controller, $method)) {
            self::error("Method {$method} not found in {$controllerName}");
        }

        // Pass explicitly supplied request data, or use the normal request data.
        $response = $controller->$method(
            $data ?? Request::all()
        );

        if ($isAjax) {
            return;
        }

        return $response;
    }

    private static function error($msg)
    {
        http_response_code(400);
        echo json_encode(['error' => $msg]);
        exit;
    }
}
