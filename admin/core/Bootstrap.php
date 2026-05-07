<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class Bootstrap
{
    public static function init()
    {
        // =========================
        // HELPERS
        // =========================
        require_once ROOTPATH . '/libraries/helpers.php';

        // =========================
        // CONFIG
        // =========================
        require_once ROOTPATH . '/config/constants.php';

        // =========================
        // TIMEZONE
        // =========================
        date_default_timezone_set("Asia/Kolkata");

        // =========================
        // COMPOSER AUTOLOAD
        // =========================
        require_once ROOTPATH . '/../vendor/autoload.php';

        // =========================
        // CLASS AUTOLOAD
        // =========================
        spl_autoload_register(function ($class) {

            $paths = [
                ROOTPATH . '/core/',
                ROOTPATH . '/controllers/',
                ROOTPATH . '/models/',
                ROOTPATH . '/libraries/',
                ROOTPATH . '/services/',
                ROOTPATH . '/helpers/',
                ROOTPATH . '/config/',
            ];

            foreach ($paths as $path) {

                $file = $path . $class . '.php';

                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }
        });
    }
}