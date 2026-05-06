<?php
defined('ROOTPATH') OR exit('No direct script access allowed');

class PostDispatcher extends BaseController
{
    private $routes;

    public function __construct($container)
    {
        parent::__construct($container);

        // Load post routes
        $this->routes = require ROOTPATH . '/config/post_routes.php';
    }

    public function handle($data)
    {
        $action = $data['action'] ?? null;

        if (!$action || !isset($this->routes[$action])) {
            // For normal POST, better to redirect instead of JSON
            header("Location: index.php?route=not_found");
            exit;
        }

        [$controllerName, $method] = $this->routes[$action];

        //require_once(ROOTPATH . "/controller/" . $controllerName . ".php");

        $controller = new $controllerName();

        $response = $controller->$method($data);

        // For normal POST → redirect or load view
        if (isset($response['redirect'])) {
            header("Location: " . $response['redirect']);
            exit;
        }

        return $response;
    }
}