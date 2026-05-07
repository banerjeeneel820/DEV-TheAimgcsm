<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class Container
{
    private $instances = [];
    private $bindings = [];

    public function __construct()
    {
        $this->bindings = require ROOTPATH . '/config/bindings.php';
    }

    public function get($key)
    {
        // singleton behavior
        if (!isset($this->instances[$key])) {

            if (!isset($this->bindings[$key])) {
                throw new Exception("Service '{$key}' not found");
            }

            $this->instances[$key] = $this->bindings[$key]($this);
        }

        return $this->instances[$key];
    }
}