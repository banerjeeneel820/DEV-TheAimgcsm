<?php
    ob_start();
	//Loading Constants
	require_once("constants.php");

	//print_r($_SESSION);exit;
    
	//Getting page route
	if(!empty($_SESSION['user_id'])){
      if(!empty($_GET["route"])){
        $page_Route = $_GET["route"];
      }else{
        $page_Route = 'home';
      }		
	}else{
	  $page_Route = 'login';	
	}  

	if(!empty($_SESSION['user_id'])){
		//Creating controller handler object
		$GlobalPageContentControllerObj = new GlobalPageContentController($page_Route);
		//Calling controller method
		$globalPageContent = $GlobalPageContentControllerObj->get_PageContent();
	}else{
		$globalPageContent = array();
	}

	//print_r($globalPageContent);exit;

	//Creating view object
	$GlobalViewObj = new GlobalViewController($page_Route,$globalPageContent);

	//Loading view method
	$GlobalViewObj->render();
	ob_end_flush();
?>