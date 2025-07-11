<?php
//ob_start();
//session_start();
//This is for testing

//Including config file for system
require_once("constants.php");

//configuring layout content
$siteSettingArr = fetchSiteSettingDetail();

//Check if the site is under maintainance
$checkSiteMaintenance = $siteSettingArr->maintenance_status;
$siteCachingStatus = "active";

if($checkSiteMaintenance == "inactive"){
   $route = "under-maintenance";
}else{
	
	//print_r($_GET);

   //Fetching url segment
   $urlSegmentArr = getUrlSegment();
   //Getting page route
   $route = $urlSegmentArr['route'];

   $monthArr = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July ', 'August', 'September', 'October', 'November', 'December', );
   
   if(!empty($urlSegmentArr['params'][1])){
     $page_slug = $urlSegmentArr['params'][1];
   }else{
     $page_slug = null;
   } 

   if(!$route){
	  $route = 'default';
   }	

    if(!$page_slug){
	  $page_slug = 'default';
   }	

	//Configuring params to fetch record
	$courseparamArr['protocol'] = 'home_page';
	$franparamArr['protocol'] = 'main_page';

	$franchiseArr = fetchGlobalFranchise($franparamArr);
   $footerFranchiseArr = array_slice($franchiseArr,0,4);

	//Fetching course array
	$courseArr = fetchGlobalCourse($courseparamArr);
	$footerCourseData = array_slice($courseArr,0,3);
   
   //Fetching news data
	$newsArr = fetchGlobalNews('featured');
}

if(strpos($route,'-')>0){
	$routeBreaked = explode('-', $route);
	$title = 'Welcome to THE AIMGCSM-'.ucfirst($routeBreaked[0]).'-'.ucfirst($routeBreaked[1]);
}
    
/*Ends here*/

$alterFooterAllowedPageArr = array('default','about-us','gallery','project');

/*----- Rendring Page content block starts here-----*/
if($route != 'debug' && $route != 'under-maintenance'){
	//Top Cache File included
  //include_once(ROOTPATH."/include/top_cache.php");
	include_once("layout/header.php");
}	

switch($route){

	case "default":
		include_once(ROOTPATH."/view/default.php");
		break;

	case "debug":
		include_once(ROOTPATH."/view/debug.php");
		break;	

	case "course":
	    include_once(ROOTPATH."/view/course.php");
	    break;

	case "course-detail":
	    include_once(ROOTPATH."/view/course-detail.php");
	    break;    	

	case "franchise":
	    include_once(ROOTPATH."/view/franchise.php");
	    break; 

	case "franchise-detail":
	    include_once(ROOTPATH."/view/franchise-detail.php");
	    break;     

	case "gallery":
	    include_once(ROOTPATH."/view/gallery.php");
	    break;    

	case "about-us":
	    include_once(ROOTPATH."/view/about-us.php");
	    break;   

	case "news":
	    include_once(ROOTPATH."/view/news.php");
	    break;        

	case "student-verification":
	    include_once(ROOTPATH."/view/student_verification.php");
	    break;                   

	case "contact-us":
	    include_once(ROOTPATH."/view/contact-us.php");
	    break;

	case "verify-user":
	case "unsunscribe-newsletter":
	    include_once(ROOTPATH."/view/verify-user.php");
	    break;    

	case "under-maintenance":
	    include_once(ROOTPATH."/view/under_maintenance.php");
	    break;   
    
    case "invalid-parent":
	    include_once(ROOTPATH."/view/invalid-parent.php");
	    break; 

	case "logout":
	  session_destroy();
	  header("location: ".SITE_URL);    
	  break;        	

	default: 
		include_once(ROOTPATH."/view/404.php");
 }

 if($route != 'debug' && $route != 'under-maintenance'){
	 if(in_array($route, $alterFooterAllowedPageArr)){
	 	include_once("layout/footer-2.php");
	 }else{
	   include_once("layout/footer.php");
	 } 
	 //Buttom Cache File included
   //include_once(ROOTPATH."/include/bottom_cache.php");
 }	 	
 /*---- End here ----*/	
 //ob_end_flush();	
?>