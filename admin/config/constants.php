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
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https')
        || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
    );

    define('SERVER_PROTOCOL', is_ssl() == true ? 'https://' : 'http://');
}

/*
|--------------------------------------------------------------------------
| Host Name
|--------------------------------------------------------------------------
*/
if (!defined('HOST_NAME')) {
    define('HOST_NAME', $_SERVER['HTTP_HOST']);
}

/*
|--------------------------------------------------------------------------
| Current Script Path
|--------------------------------------------------------------------------
| Example:
| /theaimgcsm/admin/index.php
|--------------------------------------------------------------------------
*/
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);

/*
|--------------------------------------------------------------------------
| Script Directory
|--------------------------------------------------------------------------
| Example:
| /theaimgcsm/admin
|--------------------------------------------------------------------------
*/
$scriptDir = rtrim(dirname($scriptName), '/');

/*
|--------------------------------------------------------------------------
| Root URI
|--------------------------------------------------------------------------
| Example:
| /theaimgcsm/admin/
|--------------------------------------------------------------------------
*/
if (!defined('ROOT_URI')) {
    define('ROOT_URI', $scriptDir . '/');
}

/*
|--------------------------------------------------------------------------
| Front Root URI
|--------------------------------------------------------------------------
| Example:
| /theaimgcsm/
|--------------------------------------------------------------------------
*/
$frontUri = preg_replace('#/admin$#', '', $scriptDir);

if (!defined('FRONT_ROOT_URI')) {
    define('FRONT_ROOT_URI', rtrim($frontUri, '/') . '/');
}

/*
|--------------------------------------------------------------------------
| Site URLs
|--------------------------------------------------------------------------
*/
if (!defined('SITE_URL')) {
    define('SITE_URL', SERVER_PROTOCOL . HOST_NAME . ROOT_URI);
}

if (!defined('FRONT_SITE_URL')) {
    define('FRONT_SITE_URL', SERVER_PROTOCOL . HOST_NAME . FRONT_ROOT_URI);
}

/*
|--------------------------------------------------------------------------
| Resource URL
|--------------------------------------------------------------------------
*/
if (!defined('RESOURCE_URL')) {
    define('RESOURCE_URL', SITE_URL . 'assets/');
}

/*
|--------------------------------------------------------------------------
| Upload Directory & URL
|--------------------------------------------------------------------------
*/
if (!defined('USER_UPLOAD_DIR')) {
    define('USER_UPLOAD_DIR', ROOTPATH . '/../uploads/');
}

if (!defined('USER_UPLOAD_URL')) {
    define('USER_UPLOAD_URL', FRONT_SITE_URL . 'uploads/');
}

/*
|--------------------------------------------------------------------------
| Backup Directory & URL
|--------------------------------------------------------------------------
*/
if (!defined('SITE_BACKUP_DIR')) {
    define('SITE_BACKUP_DIR', ROOTPATH . '/../backup/');
}

if (!defined('SITE_BACKUP_URL')) {
    define('SITE_BACKUP_URL', FRONT_SITE_URL . 'backup/');
}

/*
|--------------------------------------------------------------------------
| Cache Configuration
|--------------------------------------------------------------------------
*/
if (!defined('APP_CACHE_DIR')) {
    define('APP_CACHE_DIR', ROOTPATH . '/../cache/');
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