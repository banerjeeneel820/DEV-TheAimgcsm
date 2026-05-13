<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class BaseController
{
    protected $lib;

    public function __construct($container)
    {
        $this->lib = $container->get('lib');

        // Check user auth
        $this->requireAuth();

        // Moved from ajax controller
        $this->lib->checkRunTimeFolderExistance();
    }

    private function requireAuth()
    {
        $path  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $route = $_GET['route'] ?? null;

        $isLoggedIn = !empty($_SESSION['user_id']);

        // normalize path
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        // ===== ALLOW LOGIN ACCESS =====
        if (!$isLoggedIn) {

            // Case 1: /admin (your login entry)
            if (strpos($path, '/admin') !== false && empty($route)) {
                return;
            }

            // Case 2: explicit login route
            if ($route === 'login') {
                return;
            }

            // Otherwise → redirect to login entry
            header("Location: " . SITE_URL);
            exit;
        }

        // ===== OPTIONAL: prevent logged-in user from seeing login =====
        if ($isLoggedIn && $route === 'login') {
            header("Location: " . SITE_URL);
            exit;
        }
    }

    protected function page($data, $title, $assets = [], $tiny = false, $permission = true)
    {
        return [
            'pageData' => array_merge($data, [
                'page_title' => $title,
                'tiny_allowed' => $tiny,
                'page_permission' => $permission
            ]),
            'assetData' => $assets,
        ];
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        ///header('Content-Type: application/json');
        return json_encode($data);
    }

}
