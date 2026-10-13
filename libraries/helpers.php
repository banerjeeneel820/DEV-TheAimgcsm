<?php
defined('ROOTPATH') or exit('No direct script access allowed');

function is_ssl()
{
    if (
        (isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == '1'))
        ||
        (isset($_SERVER['SERVER_PORT']) &&
            $_SERVER['SERVER_PORT'] == 443)
    ) {
        return true;
    }

    if (
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        ||
        (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) &&
            $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
    ) {
        return true;
    }

    return false;
}

function dd(...$vars)
{
  echo '<pre style="
        background:#1e1e1e;
        color:#dcdcdc;
        padding:15px;
        border-radius:8px;
        font-size:14px;
        line-height:1.5;
        overflow:auto;
    ">';

  foreach ($vars as $index => $var) {
    echo "🔹 Variable " . ($index + 1) . ":\n\n";
    var_dump($var);
    echo "\n\n--------------------------\n\n";
  }

  echo '</pre>';

  die();
}