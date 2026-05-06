<?php

defined('ROOTPATH') or exit('No direct script access allowed');

class RouteDispatcher extends BaseController
{
     private $routes;
     protected $container;
     private $utilityService;
     private $globalReturnArr = [];

     public function __construct($container)
     {
          parent::__construct($container);
          $this->container = $container;

          $this->routes = require ROOTPATH . '/config/page_routes.php';
          
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
          $route = $data['route'] ?? 'home';

          if (isset($this->routes[$route])) {

               [$controllerName, $method] = $this->routes[$route];

               //require_once(ROOTPATH . "/controller/{$controllerName}.php");

               $controller = new $controllerName($this->container);

               if (!method_exists($controller, $method)) {
                    return $this->errorPage(403);
               }

               // Call domain controller
               $pageData = $controller->$method($data);

               //$this->dd($pageData);

               return $this->mergeWithGlobal($pageData);
          }else{
               return $this->errorPage(404);
          }
     }

     private function mergeWithGlobal($pageData)
     {
          return array_merge($this->globalReturnArr, $pageData);
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
