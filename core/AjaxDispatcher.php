<?php
namespace App\Core;

use App\Core\BaseController;

defined('ROOTPATH') OR exit('No direct script access allowed');

class AjaxDispatcher extends BaseController
{
    private $routes;
    protected $container;

    public function __construct($container)
    {
        parent::__construct($container);

        $this->container = $container;

        // Load the central route configuration.
        $this->routes = require ROOTPATH . '/config/routes.php';
    }

    public function handle($data)
    {
        $ajaxRoutes = $this->routes['ajax'] ?? [];

        // Prefer URL-based routing.
        $path = Request::path();

        if ($path !== '' && isset($ajaxRoutes[$path])) {
            [$controllerName, $method] = $ajaxRoutes[$path];
        } else {
            // Backward compatibility for legacy action-based requests.
            $action = $data['action'] ?? null;

            if (!$action || !isset($ajaxRoutes[$action])) {
                $this->json([
                    'status' => false,
                    'message' => 'Invalid AJAX route',
                ], 400);

                echo $this->json([
                    'status' => false,
                    'message' => 'Invalid AJAX route',
                ], 400);

                return;
            }

            [$controllerName, $method] = $ajaxRoutes[$action];
        }

        $controller = new $controllerName($this->container);

        if (!method_exists($controller, $method)) {
            echo $this->json([
                'status' => false,
                'message' => 'Invalid method',
            ], 400);

            return;
        }

        $response = $controller->$method($data);

        echo $this->json($response);
    }
}
