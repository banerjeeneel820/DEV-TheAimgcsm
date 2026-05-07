<?php
defined('ROOTPATH') OR exit('No direct script access allowed');

class AjaxDispatcher extends BaseController
{
    private $routes;
    protected $container;

    public function __construct($container)
    {
        parent::__construct($container);

        $this->container = $container;

        // load routes
        $this->routes = require ROOTPATH . '/config/ajax_routes.php';
    }

    public function handle($data)
    {
        $action = $data['action'] ?? null;

        if (!$action || !isset($this->routes[$action])) {
            return $this->json([
                'status' => false,
                'message' => 'Invalid action'
            ], 400);
        }

        [$controllerName, $method] = $this->routes[$action];

        // Load controller (if no autoload)
        //require_once(ROOTPATH . "/controller/" . $controllerName . ".php");

        $controller = new $controllerName($this->container);

        if (!method_exists($controller, $method)) {
            return $this->json([
                'status' => false,
                'message' => 'Invalid method'
            ], 400);
        }

        $response = $controller->$method($data);

        echo $this->json($response);
    }
}