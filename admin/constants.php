<?php
//if($_GET['route'] != "logout"){ 
session_start();
//}
error_reporting(0);
//ini_set("display_errors", 1);

if (!defined("HOST")) {
	define("HOST", "localhost");
}
if (!defined("MYSQL_USER")) {
	define("MYSQL_USER", "theaimgc_user");
}
if (!defined("MYSQL_PASS")) {
	define("MYSQL_PASS", "EPWW?-*HQU1w");
}
if (!defined("DB_AIMGCSM")) {
	define("DB_AIMGCSM", "theaimgc__computers");
}
if (!defined("TABLEPREFIX")) {
	define("TABLEPREFIX", "theaimgc_dev_");
}

date_default_timezone_set("Asia/Kolkata");

//CHECK SSL
function is_ssl()
{
	// Check if SSL
	if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || (isset($_SERVER['HTTPS']) && (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))) {
		$_SERVER['HTTPS'] = true;
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
		$_SERVER['HTTPS'] = true;
	} else {
		$_SERVER['HTTPS'] = false;
	}

	if ($_SERVER['HTTPS'] == true) {
		return true;
	} else {
		return false;
	}
}

/**Auto configuring required PATH & URL constants for app environment*/
if (!defined("SERVER_ENV")) {
	define("SERVER_ENV", "PRODUCTION"); //set this into `PRODUCTION` when deployed in live
}
if (!defined("SERVER_PROTOCOL")) {
	define("SERVER_PROTOCOL", is_ssl() == true ? 'https://' : 'http://');
}
if (!defined("ROOTPATH")) {
	define('ROOTPATH', dirname(__FILE__));
}
if (!defined("HOST_NAME")) {
	define('HOST_NAME', $_SERVER['HTTP_HOST']);
}
if (!defined("ROOT_URI")) {
	define('ROOT_URI',  str_replace('\\', '/', substr(ROOTPATH, strlen($_SERVER['DOCUMENT_ROOT']))) . '/');
}
if (!defined("FRONT_ROOT_URI")) {
	define('FRONT_ROOT_URI',  str_replace('admin/', '', ROOT_URI));
}
if (!defined("SITE_URL")) {
	define("SITE_URL", SERVER_PROTOCOL . "theaimgcsm.com/admin/");
	//define("SITE_URL",SERVER_PROTOCOL.HOST_NAME.ROOT_URI);
}
if (!defined("FRONT_SITE_URL")) {
	define("FRONT_SITE_URL", SERVER_PROTOCOL . "theaimgcsm.com/");
	//define("FRONT_SITE_URL",SERVER_PROTOCOL.HOST_NAME.FRONT_ROOT_URI);
}
if (!defined("RESOURCE_URL")) {
	define("RESOURCE_URL", SITE_URL . "assets/");
}
if (!defined("USER_UPLOAD_DIR")) {
	define("USER_UPLOAD_DIR", ROOTPATH . "/../uploads/");
}
if (!defined("USER_UPLOAD_URL")) {
	define("USER_UPLOAD_URL", FRONT_SITE_URL . "uploads/");
}
if (!defined("SITE_BACKUP_DIR")) {
	define("SITE_BACKUP_DIR", ROOTPATH . "/../backup/");
}
if (!defined("SITE_BACKUP_URL")) {
	define("SITE_BACKUP_URL", FRONT_SITE_URL . "backup/");
}
if (!defined("APP_CACHE_DIR")) {
	define("APP_CACHE_DIR", ROOTPATH . "/../cache/");
}
if (!defined("APP_CACHE_TIME")) {
	define("APP_CACHE_TIME", 86400);
}
/*------------ End Here --------*/

/*-------------App default salt----------*/
if (!defined("APP_DEFAULT_SALT")) {
	define("APP_DEFAULT_SALT", "MIND_#%_YOUR_@_OWN_!^&(?)_BUSINESS_[{NO_CRACK_SALT}]");
}
/*-------------End here------------------*/

/*-------------Local text API KEY----------*/
/*if(!defined("LOCAL_TEXT_API_KEY")){	
  	define("LOCAL_TEXT_API_KEY", "NTY1NjY3NzczODUxNDczMTcxNzkzNzZkNDI2MTQzNDg=");
  }*/
if (!defined("SINCH_PLAN_ID")) {
	define("SINCH_PLAN_ID", "035965c634444c0aa425bd7e83da6b02");
}
if (!defined("SINCH_API_TOKEN")) {
	define("SINCH_API_TOKEN", "3e28763eef4a4cc686fb5ebb5910bbfd");
}
if (!defined("MY_SINCH_NO")) {
	define("MY_SINCH_NO", "447520651436");
}
/*-------------End here------------------*/

//DB class include
include_once("database/DB.php");
if (!DB::$connected) {
	DB::connect();
}

//Declaring variables
$globalPageContent = array();

//Model included
require_once(ROOTPATH . "/model/GlobalInterfaceModel.php");

//Controller included
require_once(ROOTPATH . "/controller/GlobalInterfaceController.php");

//Controller handler included
require_once(ROOTPATH . "/controller/GlobalPageContentController.php");

//View Controller included
require_once(ROOTPATH . "/controller/GlobalViewController.php");

// somewhere early in your project's loading, require the Composer autoloader
// see: http://getcomposer.org/doc/00-intro.md
require ROOTPATH . '/../vendor/autoload.php';

//Library loader included
require_once(ROOTPATH . "/library/GlobalLibraryHandler.php");  

	//define("MAIL_USERNAME", "");
	//define("MAIL_PASSWORD", "");
	//define("MAIL_HOST", "smtp.gmail.com");
	//define("EMAIL_FROM", "");
	//Sdefine("ACCOUNTS_EMAIL", "");	
