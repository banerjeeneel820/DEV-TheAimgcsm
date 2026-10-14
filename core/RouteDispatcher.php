<?php
namespace App\Core;

use App\Core\BaseController;
use App\Core\RouteRegistry;

defined('ROOTPATH') or exit('No direct script access allowed');

class RouteDispatcher extends BaseController
{
     private $routes;
     private $viewLayouts;
     protected $container;
     private $utilityService;
     private $globalReturnArr = [];

     public function __construct($container)
     {
          parent::__construct($container);
          $this->container = $container;

          $routes = RouteRegistry::all();
          $this->routes = $routes['get'] ?? [];
          
          $this->viewLayouts = require ROOTPATH . '/config/view_layouts.php';

          $this->utilityService = $this->container->get('utilityService');
          $this->globalReturnArr['check_site_maintenance'] = $this->check_Site_Maintenance_Status();
     }

     private function check_Site_Maintenance_Status()
     {
          //Fetch site setting
          $this->globalReturnArr['site_setting_data'] = $this->utilityService->getSiteSettings();

          if ($this->globalReturnArr['site_setting_data']->maintenance_status == 'inactive') {
               return false;
          } else {
               return true;
          }
     }

     public function get_PageContent($data)
     {
          $route = trim($data['route'] ?? 'home', '/');

          //var_dump($route);exit;

          if (!isset($this->routes[$route])) {
               return $this->errorPage(404);
          }

          [$controllerName, $method] = $this->routes[$route];

          if (!class_exists($controllerName)) {
               return $this->errorPage(404);
          }

          $controller = new $controllerName($this->container);

          if (!method_exists($controller, $method)) {
               return $this->errorPage(404);
          }

          // Call the existing domain controller.
          $pageData = $controller->$method($data);

          if (!is_array($pageData)) {
               return $this->mergeWithGlobal([]);
          }

          // Infer application from the URL prefix if it is configured.
          $application = $this->getApplicationFromRoute($route);

          if ($application !== null) {
               $existingViewConfig = $pageData['viewConfig'] ?? [];

               // Explicit controller configuration takes precedence.
               $pageData['viewConfig'] = array_merge(
                    ['application' => $application],
                    $existingViewConfig
               );
          }

          return $this->mergeWithGlobal($pageData);
     }

     private function getApplicationFromRoute($route)
     {
          $segments = explode('/', trim($route, '/'));
          $candidate = $segments[0] ?? '';

          $config = $this->viewLayouts[$candidate] ?? null;

          if (
               is_array($config) &&
               !empty($config['enabled']) &&
               !empty($config['view_root'])
          ) {
               return $candidate;
          }

          // Legacy routes may not contain an application prefix.
          return null;
     }


     private function mergeWithGlobal($pageData = [])
     {
          if (!empty($pageData)) {
               return array_merge($this->globalReturnArr, $pageData);
          } else {
               return $this->globalReturnArr;
          }
     }

     private function errorPage($status = 404)
     {
          return [
               'pageData' => [
                    'page_title' => 'Page Not Found',
                    'tiny_allowed' => false,
                    'status' => $status,
                    'page_permission' => false
               ],
               'assetData' => []
          ];
     }
}
