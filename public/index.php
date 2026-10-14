<?php
ob_start();

define('ROOTPATH', __DIR__);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// bootstrap
require ROOTPATH . '/core/Bootstrap.php';

Bootstrap::init();

$container = new Container();

Router::handle($container);

ob_end_flush();
