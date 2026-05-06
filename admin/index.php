<?php
ob_start();

require_once("constants.php");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$container = new Container();

Router::handle($container);

ob_end_flush();