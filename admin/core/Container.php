<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class Container
{
    private $instances = [];

    public function get($key)
    {
        if (!isset($this->instances[$key])) {
            $this->instances[$key] = $this->resolve($key);
        }

        return $this->instances[$key];
    }

    private function resolve($key)
    {
        switch ($key) {

            // CORE
            case 'db':
                return new Database();

            case 'lib':
                return new GlobalLibraryHandler();

            // MODELS
            case 'interfaceModel':
                return new GlobalInterfaceModel(
                    $this->get('db')
                );

            // SERVICES
            case 'permissionService':
                return new PermissionService(
                    $this->get('interfaceModel'),
                    $this->get('lib')
                );
            
            case 'globalValidationController':   
                return new GlobalValidationController(
                    $this->get('lib'),
                );     
        
            case 'cacheService':   
                return new CacheService();         

            case 'studentService':
                return new StudentService(
                    $this->get('interfaceModel'),
                    $this->get('lib'),
                    $this->get('permissionService'),
                    $this->get('cacheService'),
                    $this->get('studentReceiptService'),
                    $this->get('globalValidationController')
                );
            
            case 'studentReceiptService':   
                return new StudentReceiptService(
                    $this->get('interfaceModel'),
                    $this->get('lib'),
                ); 

            case 'courseFranchiseService':
                return new CourseFranchiseService(
                    $this->get('interfaceModel'),
                    $this->get('lib')
                );
                

            case 'utilityService':
                return new UtilityService(
                    $this->get('interfaceModel'),
                    $this->get('lib')
                );

            default:
                throw new Exception("Service '{$key}' not found");
        }
    }
}