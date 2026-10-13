
<?php

defined('ROOTPATH') or exit('No direct script access allowed');

return [

    'default_application' => 'admin',

    'admin' => [
        'enabled' => true,

        'view_root' => ROOTPATH . '/views/admin/',
        'layout_root' => ROOTPATH . '/layout/admin/',

        'header' => 'header.php',
        'footer' => 'footer.php',

        'sidebar' => ROOTPATH . '/config/sidebar.php',

        'variables' => [
            'css' => 'cssPluginArr',
            'js'  => 'jsPluginArr',
        ],

        'none' => [
            'view_root' => ROOTPATH . '/views/admin/',
            'layout' => null,

            'fallbacks' => [
                403 => 'errors/403.php',
                404 => 'errors/404.php',
            ],
        ],

        'fallbacks' => [
            403 => 'errors/403.php',
            404 => 'errors/404.php',
        ],
    ],

    'frontend' => [
        'enabled' => true,

        'view_root' => ROOTPATH . '/views/frontend/',
        'layout_root' => ROOTPATH . '/layout/frontend/',

        'header' => 'header.php',
        'footer' => 'footer.php',

        'sidebar' => null,

        'variables' => [
            'css' => 'cssPluginArr',
            'js'  => 'jsPluginArr',
        ],

        'none' => [
            'view_root' => ROOTPATH . '/views/frontend/',
            'layout' => null,

            'fallbacks' => [
                403 => 'errors/403.php',
                404 => 'errors/404.php',
            ],
        ],

        'fallbacks' => [
            403 => 'errors/403.php',
            404 => 'errors/404.php',
        ],
    ],

    'layout_routes' => [
        'login' => 'none',
        'logout' => 'none',
    ],

];