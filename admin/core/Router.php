<?php
defined('ROOTPATH') OR exit('No direct script access allowed');

class Router
{
    public static function handle()
    {
        $method = Request::method();

        if ($method === 'POST') {
            self::handlePost();
        } else {
            self::handleGet();
        }
    }

    private static function handlePost()
    {   
        // Dispatch to AjaxController
        self::dispatch('AjaxController', 'handle', true);
    }

    private static function handleGet()
    {
        if (!empty($_SESSION['user_id'])) {
            $route = Request::get('route', 'home');
        } else {
            $route = 'login';
        }

        // Dispatch to GlobalPageContentController
        $pageData = self::dispatch('GlobalPageController', 'get_PageContent', false, $route);

        // Render view
        $view = new GlobalViewController($route, $pageData);
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

        $controller = ($param !== null)
        ? new $controllerName($param)
        : new $controllerName();

        if (!method_exists($controller, $method)) {
            self::error("Method {$method} not found in {$controllerName}");
        }

        $response = $controller->$method(Request::all());

        // AjaxController already handles json response via BaseController::json()
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