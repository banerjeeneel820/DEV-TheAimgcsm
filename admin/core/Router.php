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
        if (!empty($_SESSION['user_id'])) {
            $route = Request::get('route', 'home');
        } else {
            $route = 'login';
        }

        // 👇 pass route as param
        $pageData = self::dispatch('RouteDispatcher', 'get_PageContent', false, $route);

        // 👇 inject container into view if needed later
        $view = new ViewEngine(self::$container, $route, $pageData);
        $view->render();

        exit;
    }

    /**
     * Central Dispatch Method
     */
    private static function dispatch($controllerName, $method, $isAjax = false, $param = null)
    {
        if (!class_exists($controllerName)) {
            self::error("Controller {$controllerName} not found");
        }

        // ALWAYS pass container now
        $controller = ($param !== null)
            ? new $controllerName(self::$container, $param)
            : new $controllerName(self::$container);

        if (!method_exists($controller, $method)) {
            self::error("Method {$method} not found in {$controllerName}");
        }

        $response = $controller->$method(Request::all());

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