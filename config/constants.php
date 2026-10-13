<?php
defined('ROOTPATH') or exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Server Time Zone
|--------------------------------------------------------------------------
*/
if (!defined('TIMEZONE')) {
    define('TIMEZONE', 'Asia/Kolkata');
}

/*
|--------------------------------------------------------------------------
| Session
|--------------------------------------------------------------------------
*/
// if ($_GET['route'] != "logout") {
//     session_start();
// }

/*
|--------------------------------------------------------------------------
| Server Environment
|--------------------------------------------------------------------------
| Set this to:
| STAGING / PRODUCTION
|--------------------------------------------------------------------------
*/
if (!defined('SERVER_ENV')) {
    define('SERVER_ENV', 'STAGING');
}

/*
|--------------------------------------------------------------------------
| Database Configuration
|--------------------------------------------------------------------------
*/
if (!defined('HOST')) {
    define('HOST', 'localhost');
}

if (!defined('MYSQL_USER')) {
    define('MYSQL_USER', 'root');
}

if (!defined('MYSQL_PASS')) {
    define('MYSQL_PASS', '');
}

if (!defined('DB_AIMGCSM')) {
    define('DB_AIMGCSM', 'dbs5583308');
}

if (!defined('TABLEPREFIX')) {
    define('TABLEPREFIX', 'theaimgc_dev_');
}


/*
|--------------------------------------------------------------------------
| Protocol
|--------------------------------------------------------------------------
*/
if (!defined('SERVER_PROTOCOL')) {
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off')
        || (!empty($_SERVER['REQUEST_SCHEME']) && strtolower($_SERVER['REQUEST_SCHEME']) === 'https')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
    );

    define('SERVER_PROTOCOL', $isHttps ? 'https://' : 'http://');
}

/*
|--------------------------------------------------------------------------
| Host Name
|--------------------------------------------------------------------------
*/
if (!defined('HOST_NAME')) {
    define('HOST_NAME', $_SERVER['HTTP_HOST'] ?? 'localhost');
}

/*
|--------------------------------------------------------------------------
| Project Base URI
|--------------------------------------------------------------------------
| With the root index.php:
| /theaimgcsm/index.php -> /theaimgcsm/
|--------------------------------------------------------------------------
*/
if (!defined('PROJECT_ROOT_URI')) {
    $scriptName = str_replace(
        '\\',
        '/',
        $_SERVER['SCRIPT_NAME'] ?? '/index.php'
    );

    $projectUri = rtrim(dirname($scriptName), '/');

    // Root-level projects should use "/" rather than "/."
    if ($projectUri === '.' || $projectUri === '/') {
        $projectUri = '';
    }

    define('PROJECT_ROOT_URI', $projectUri . '/');
}

/*
|--------------------------------------------------------------------------
| Application URI Prefixes
|--------------------------------------------------------------------------
| These are URL prefixes, not physical directory paths.
|--------------------------------------------------------------------------
*/
if (!defined('ROOT_URI')) {
    define('ROOT_URI', PROJECT_ROOT_URI);
}

if (!defined('FRONT_ROOT_URI')) {
    define('FRONT_ROOT_URI', PROJECT_ROOT_URI);
}

if (!defined('ADMIN_ROOT_URI')) {
    define('ADMIN_ROOT_URI', PROJECT_ROOT_URI . 'admin/');
}

/*
|--------------------------------------------------------------------------
| Site URLs
|--------------------------------------------------------------------------
*/
if (!defined('PROJECT_SITE_URL')) {
    define(
        'PROJECT_SITE_URL',
        SERVER_PROTOCOL . HOST_NAME . PROJECT_ROOT_URI
    );
}

if (!defined('SITE_URL')) {
    define('SITE_URL', PROJECT_SITE_URL);
}

if (!defined('FRONT_SITE_URL')) {
    define('FRONT_SITE_URL', PROJECT_SITE_URL);
}

if (!defined('ADMIN_SITE_URL')) {
    define('ADMIN_SITE_URL', PROJECT_SITE_URL . 'admin/');
}

/*
|--------------------------------------------------------------------------
| Resource URL
|--------------------------------------------------------------------------
*/
if (!defined('RESOURCE_URL')) {
    define('RESOURCE_URL', PROJECT_SITE_URL . 'assets/');
}

/*
|--------------------------------------------------------------------------
| Upload Directory & URL
|--------------------------------------------------------------------------
*/
if (!defined('USER_UPLOAD_DIR')) {
    define('USER_UPLOAD_DIR', ROOTPATH . '/uploads/');
}

if (!defined('USER_UPLOAD_URL')) {
    define('USER_UPLOAD_URL', SITE_URL . 'uploads/');
}

/*
|--------------------------------------------------------------------------
| Backup Directory & URL
|--------------------------------------------------------------------------
*/
if (!defined('SITE_BACKUP_DIR')) {
    define('SITE_BACKUP_DIR', ROOTPATH . '/backup/');
}

if (!defined('SITE_BACKUP_URL')) {
    define('SITE_BACKUP_URL', SITE_URL . 'backup/');
}

/*
|--------------------------------------------------------------------------
| Cache Configuration
|--------------------------------------------------------------------------
*/
if (!defined('APP_CACHE_DIR')) {
    define('APP_CACHE_DIR', ROOTPATH . '/cache/');
}

if (!defined('APP_CACHE_TIME')) {
    define('APP_CACHE_TIME', 86400);
}

/*
|--------------------------------------------------------------------------
| Application Default Salt
|--------------------------------------------------------------------------
*/
if (!defined('APP_DEFAULT_SALT')) {
    define(
        'APP_DEFAULT_SALT',
        'MIND_#%_YOUR_@_OWN_!^&(?)_BUSINESS_[{NO_CRACK_SALT}]'
    );
}