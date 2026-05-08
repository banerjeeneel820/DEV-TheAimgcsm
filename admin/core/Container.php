<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class Container
{
    /*
    |--------------------------------------------------------------------------
    | Singleton Instances
    |--------------------------------------------------------------------------
    */
    private array $instances = [];

    /*
    |--------------------------------------------------------------------------
    | Manual Bindings
    |--------------------------------------------------------------------------
    */
    private array $bindings = [];

    public function __construct()
    {
        $this->bindings = require ROOTPATH . '/config/bindings.php';
    }

    /*
    |--------------------------------------------------------------------------
    | Resolve Dependency
    |--------------------------------------------------------------------------
    */
    public function get(string $key)
    {
        /*
        |--------------------------------------------------------------------------
        | Return Existing Singleton
        |--------------------------------------------------------------------------
        */
        if (isset($this->instances[$key])) {
            return $this->instances[$key];
        }

        /*
        |--------------------------------------------------------------------------
        | Manual Binding
        |--------------------------------------------------------------------------
        */
        if (isset($this->bindings[$key])) {

            $this->instances[$key] = $this->bindings[$key]($this);

            return $this->instances[$key];
        }

        /*
        |--------------------------------------------------------------------------
        | Auto Resolve Class
        |--------------------------------------------------------------------------
        */
        if (!class_exists($key)) {
            throw new Exception("Service or class '{$key}' not found");
        }

        $reflection = new ReflectionClass($key);

        /*
        |--------------------------------------------------------------------------
        | Check Instantiable
        |--------------------------------------------------------------------------
        */
        if (!$reflection->isInstantiable()) {
            throw new Exception("Class '{$key}' is not instantiable");
        }

        $constructor = $reflection->getConstructor();

        /*
        |--------------------------------------------------------------------------
        | No Constructor
        |--------------------------------------------------------------------------
        */
        if (!$constructor) {

            $instance = new $key();

            $this->instances[$key] = $instance;

            return $instance;
        }

        /*
        |--------------------------------------------------------------------------
        | Resolve Constructor Dependencies
        |--------------------------------------------------------------------------
        */
        $dependencies = [];

        foreach ($constructor->getParameters() as $parameter) {

            $type = $parameter->getType();

            /*
            |--------------------------------------------------------------------------
            | Untyped Parameter
            |--------------------------------------------------------------------------
            */
            if (!$type || $type->isBuiltin()) {

                throw new Exception(
                    "Cannot resolve parameter '{$parameter->getName()}' in class '{$key}'"
                );
            }

            $dependencies[] = $this->get(
                $type->getName()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Create Instance
        |--------------------------------------------------------------------------
        */
        $instance = $reflection->newInstanceArgs($dependencies);

        /*
        |--------------------------------------------------------------------------
        | Store Singleton
        |--------------------------------------------------------------------------
        */
        $this->instances[$key] = $instance;

        return $instance;
    }
}