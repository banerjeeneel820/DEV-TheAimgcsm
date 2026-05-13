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

        require_once ROOTPATH . '/libraries/sidebar_helper.php';

        // =========================
        // CONFIG
        // =========================
        require_once ROOTPATH . '/config/constants.php';

        require_once ROOTPATH . '/config/sidebar.php';

        // =========================
        // ERROR REPORTING
        // =========================
        switch (SERVER_ENV) {

            case 'PRODUCTION':

                error_reporting(0);
                ini_set('display_errors', 0);

                break;

            case 'STAGING':
                error_reporting(0);
                ini_set('display_errors', 0);

                break;
                
            default:
                error_reporting(0);
                error_reporting(E_ALL);
                ini_set('display_errors', 1);

                break;
        }

        // =========================
        // TIMEZONE
        // =========================
        date_default_timezone_set(TIMEZONE);

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