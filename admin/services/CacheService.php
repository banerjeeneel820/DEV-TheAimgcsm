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

    // Purge admin cache
    public function purgeSiteCache($section)
    {
        if (SERVER_ENV !== "PRODUCTION") {
            return true;
        }

        $userType = $_SESSION['user_type'] ?? null;
        $userId   = $_SESSION['user_id'] ?? null;

        $keys = $this->getCacheKeys($section, $userType, $userId);

        if (!empty($keys)) {
            $this->purge($keys); // supports array
        }

        return true;
    }

    public function getCacheKeys($section, $userType, $userId)
    {
        switch ($section) {

            case 'student':
                return $this->buildDashboardKeys('student_dashboard', $userType, $userId);

            case 'student_receipts':
                return $this->buildDashboardKeys('receipt_dashboard', $userType, $userId);

            case 'franchise':
                return [
                    "franchise_data_active",
                    "franchise_data_blocked"
                ];

            case 'course':
                return [
                    "course_data",
                    "course_data_active",
                    "course_data_blocked"
                ];

            case 'others':
                return [
                    "news_data",
                    "enquiry_data",
                    "gallery_data"
                ];
        }

        return [];
    }

    public function buildDashboardKeys($prefix, $userType, $userId)
    {
        $periods = ['today', 'weekly', 'monthly', 'annual'];

        if (in_array($userType, ['developer', 'admin'])) {
            return array_map(fn ($p) => "{$prefix}_{$p}", $periods);
        }

        if ($userType === 'franchise') {
            return array_map(fn ($p) => "{$prefix}_{$p}_{$userId}", $periods);
        }

        return [];
    }
    // End here
}
