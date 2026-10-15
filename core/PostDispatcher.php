<?php

namespace App\Core;

defined('ROOTPATH') or exit('No direct script access allowed');

class PostDispatcher extends BaseController
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

        $route = RouteRegistry::match('POST', $path);

        if ($route === null) {
            http_response_code(404);
            echo 'POST Route Not Found';
            return;
        }

        [$controllerName, $methodName] = $route['action'];

        if (!class_exists($controllerName)) {
            http_response_code(500);
            echo 'Controller Class Not Found';
            return;
        }

        $controller = new $controllerName($this->container);

        if (!method_exists($controller, $methodName)) {
            http_response_code(500);
            echo 'Controller Method Not Found';
            return;
        }

        $response = $controller->$methodName($data);

        if (is_array($response) && !empty($response['redirect'])) {
            header('Location: ' . $response['redirect']);
            return;
        }

        if (is_string($response)) {
            echo $response;
        }
    }
}
