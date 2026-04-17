<?php 
defined('ROOTPATH') or exit('No direct script access allowed');

class CacheService
{
    private $mem;

    public function __construct()
    {
        if (SERVER_ENV === "PRODUCTION") {
            $this->mem = new Memcached();
            $this->mem->addServer("127.0.0.1", 11211);
        } else {
            $this->mem = null;
        }
    }

    public function get($key, callable $callback)
    {
        if ($this->mem === null) {
            return $callback();
        }

        $data = $this->mem->get($key);

        if ($data !== false) { // important fix
            return $data;
        }

        $data = $callback();
        $this->mem->set($key, $data);

        return $data;
    }

    public function purge($keys)
    {
        if ($this->mem === null) {
            return;
        }

        // Normalize to array
        $keys = is_array($keys) ? $keys : [$keys];

        foreach ($keys as $key) {
            $this->mem->delete($key);
        }
    }
}