
<?php

defined('ROOTPATH') or exit('No direct script access allowed');

return [

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
    ],

    'none' => [
        'enabled' => false,
    ],

    'layout_routes' => [
        'login' => 'none',
        'logout' => 'none',
    ],

];