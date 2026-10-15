<?php

namespace App\Middleware;

use App\Core\Container;
use App\Core\Request;

defined('ROOTPATH') or exit('No direct script access allowed');

class AdminMiddleware
{
    private Container $container;
    private $lib;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->lib = $container->get('lib');
    }

    public function handle(array $request, callable $next)
    {
        if (!$this->requireAuth()) {
            return false;
        }

        // Perform admin runtime setup only after authentication succeeds.
        $this->lib->checkRunTimeFolderExistance();

        return $next($request);
    }

    private function requireAuth(): bool
    {
        $path = trim(Request::path(), '/');
        $legacyRoute = Request::get('route');

        $isLoggedIn = !empty($_SESSION['user_id']);

        $isLoginEntry =
            $path === 'admin' ||
            $legacyRoute === 'login';

        // Not logged in: allow only the login entry.
        if (!$isLoggedIn) {
            if ($isLoginEntry) {
                return true;
            }

            // AJAX clients should receive a status code, not an HTML redirect.
            if (
                strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') ===
                    'xmlhttprequest'
            ) {
                http_response_code(401);
                header('Content-Type: application/json; charset=utf-8');

                echo json_encode([
                    'success' => false,
                    'message' => 'Authentication required.',
                ]);

                return false;
            }

            header('Location: ' . SITE_URL);
            return false;
        }

        // A logged-in user visiting the login entry should be redirected away.
        if ($isLoginEntry) {
            header('Location: ' . SITE_URL);
            return false;
        }

        return true;
    }

    public function terminate(array $request, $response): void
    {
        // Optional post-request cleanup.
    }
}
