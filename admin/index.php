<?php
ob_start();

require_once("constants.php");

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

Router::handle();

ob_end_flush();