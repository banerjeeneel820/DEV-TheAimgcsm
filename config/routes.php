
<?php
defined('ROOTPATH') OR exit('No direct script access allowed');

return [
    'get' => require ROOTPATH . '/config/page_routes.php',
    'ajax' => require ROOTPATH . '/config/ajax_routes.php',
    'post' => require ROOTPATH . '/config/post_routes.php',
];
