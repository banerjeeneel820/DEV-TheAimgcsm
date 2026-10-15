<?php

namespace App\Middleware;

use App\Core\Container;

defined('ROOTPATH') or exit('No direct script access allowed');

class AdminMiddleware
{
    private $container;
    private $lib;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->lib = $container->get('lib');
    }

    /**
     * Runs before the controller action.
     */
    public function handle(array $request, callable $next)
    {
        if (!$this->requireAuth()) {
            return null;
        }
    
        $this->lib->checkRunTimeFolderExistance();
    
        return $next($request);
    }

    /**
     * Move the existing BaseController::requireAuth()
     * logic here, preserving your current login behavior.
     *
     * Return true when authenticated.
     * When unauthenticated, redirect as appropriate and
     * return false instead of calling exit/die.
     */
    private function requireAuth(): bool
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
        $route = $_GET['route'] ?? null;

        $isLoggedIn = !empty($_SESSION['user_id']);

        // Normalize the path.
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }

        // Allow the login entry for unauthenticated users.
        if (!$isLoggedIn) {
            if (
                (strpos($path, '/admin') !== false && empty($route)) ||
                $route === 'login'
            ) {
                return true;
            }

            // Authentication failed: redirect, but don't exit here.
            header('Location: ' . SITE_URL);
            return false;
        }

        // Prevent authenticated users from opening the login route.
        if ($route === 'login') {
            header('Location: ' . SITE_URL);
            return false;
        }

        return true;
    }

    /**
     * Runs after the response has been sent.
     */
    public function terminate(array $request, $response): void
    {
        // Optional post-response work can go here later.
        // For example: request logging or cleanup.
    }
}
