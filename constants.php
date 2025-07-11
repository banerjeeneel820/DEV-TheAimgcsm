<?php
    session_start();
    error_reporting(0);
	//ini_set("display_errors", 1);
    if(!defined("HOST")){	
		define("HOST", "localhost");
	}
	if(!defined("MYSQL_USER")){		
		define("MYSQL_USER", "theaimgc_user");
	}
	if(!defined("MYSQL_PASS")){		
		define("MYSQL_PASS", "EPWW?-*HQU1w");
	}
	if(!defined("DB_AIMGCSM")){		
		define("DB_AIMGCSM", "theaimgc__computers");
	}
	if(!defined("TABLEPREFIX")){		
		define("TABLEPREFIX", "theaimgc_dev_");
	}	
	
	date_default_timezone_set("Asia/Kolkata");
	
	//CHECK SSL
	function is_ssl() {
	   // Check if SSL
		if ((isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) || (isset($_SERVER['HTTPS']) && (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443))) {
			$_SERVER['HTTPS'] = true;
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
			$_SERVER['HTTPS'] = true;
		} else {
			$_SERVER['HTTPS'] = false;
		}

		if($_SERVER['HTTPS'] == true){
			return true;
		}else{
			return false;
		}
	}

	/**Auto configuring required PATH & URL constants for app environment*/
	if (!defined("SERVER_ENV")) {
    	define("SERVER_ENV", "PRODUCTION"); //set this into `STAGING`/ `PRODUCTION` when deployed in local/live
    }
	if(!defined("SERVER_PROTOCOL")){	
		define("SERVER_PROTOCOL", is_ssl() == true ?'https://':'http://');
	}
	if(!defined("ROOTPATH")){		
		define('ROOTPATH', dirname(__FILE__));
	}	 
	if(!defined("HOST_NAME")){	  
		define('HOST_NAME', $_SERVER['HTTP_HOST']);
	}
	if(!defined("ROOT_URI")){		
		define('ROOT_URI',  str_replace('\\','/',substr(ROOTPATH, strlen($_SERVER[ 'DOCUMENT_ROOT' ]))).'/');
	}
    if(!defined("SITE_URL")){	 
    	define("SITE_URL",SERVER_PROTOCOL."theaimgcsm.com/");
		//define("SITE_URL",SERVER_PROTOCOL.HOST_NAME.ROOT_URI);
	}	
    if(!defined("RESOURCE_URL")){	 
		define("RESOURCE_URL",SITE_URL."assets/");
	}
	if(!defined("ADMIN_RESOURCE_URL")){	 	
    	define("ADMIN_RESOURCE_URL",SITE_URL."admin/assets/");
    }
    if(!defined("USER_UPLOAD_DIR")){	  
		define("USER_UPLOAD_DIR",ROOTPATH."/uploads/");
	}
	if(!defined("USER_UPLOAD_URL")){	 	
		define("USER_UPLOAD_URL",SITE_URL."uploads/");
	}
	if(!defined("APP_CACHE_DIR")){		
		define("APP_CACHE_DIR",ROOTPATH."/cache/");
	}		
	if(!defined("APP_CACHE_TIME")){		
		define("APP_CACHE_TIME",86400);
	}
    /*------------ End Here --------*/

    /*-------------App default salt----------*/
    define("APP_DEFAULT_SALT", "MIND_#%_YOUR_@_OWN_!^&(?)_BUSINESS_[{NO_CRACK_SALT}]");
    /*-------------End here------------------*/
	
    //Model include
	include_once(ROOTPATH."/model/GlobalInterfaceModel.php");
	//DB class include                                
	include_once("database/DB.php");
	if(!DB::$connected) { DB::connect(); }

	// somewhere early in your project's loading, require the Composer autoloader
  	// see: http://getcomposer.org/doc/00-intro.md
	require ROOTPATH.'/vendor/autoload.php';

	//Function is included
	include_once(ROOTPATH."/library/functions.php");

	//Global Page content handler
	//include_once('helpers/GlobalSeoHandler.php');
	
	/** Defining all mail const */
	//define("ADMIN_EMAIL", "neel@localhost");
	//define("ADMIN_RECEIVE_EMAIL", "alex@localhost");
	//define("MAIL_USERNAME", "");
	//define("MAIL_PASSWORD", "");
	//define("MAIL_HOST", "smtp.gmail.com");
	//define("EMAIL_FROM", "");
	//define("ACCOUNTS_EMAIL", "");	
?>