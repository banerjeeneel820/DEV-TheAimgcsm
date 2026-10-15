<?php

namespace App\Core;

defined('ROOTPATH') or exit('No direct script access allowed');

class AjaxDispatcher extends BaseController
{
    private Container $container;

    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    public function handle(array $data): void
    {
        $path = trim($data['route'] ?? Request::path(), '/');

        $route = RouteRegistry::match('AJAX', $path);

        if ($route === null) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'AJAX route not found.',
            ]);
            return;
        }

        [$controllerName, $methodName] = $route['action'];

        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Controller class not found.',
            ]);
            return;
        }

        $controller = new $controllerName($this->container);

        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Controller method not found.',
            ]);
            return;
        }

        $response = $controller->$methodName($data);

        // The existing controller can return data for the dispatcher to encode.
        if (is_string($response)) {
            echo $response;
            return;
        }

        echo $this->json($response);
    }
}
