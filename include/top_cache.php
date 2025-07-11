<?php
   defined('ROOTPATH') OR exit('No direct script access allowed');

	switch($route){

		case "default":
			$cachefile = 'cached-home-page.html';
			break;

		case "course":
		    $cachefile = 'cached-course-page.html';
		    break;

		case "course-detail":
		    $course_slug = $urlSegmentArr['params'][1];
		    $cachefile = 'cached-course-detail-page-'.$course_slug.'.html';
		    break;    	

		case "franchise":
		    $cachefile = 'cached-franchise-page.html';
		    break; 

		case "franchise-detail":
		    $franchise_slug = $urlSegmentArr['params'][1];
		    $cachefile = 'cached-franchise-detail-page-'.$franchise_slug.'.html';
		    break;     

		case "gallery":
		    $cachefile = 'cached-gallery-page.html';
		    break;    

		case "about-us":
		    $cachefile = 'cached-about-us-page.html';
		    break;   

		case "notice":
		    $cachefile = 'cached-notice-page.html';
		    break;        

		case "faq":
		    $cachefile = 'cached-faq-page.html';
		    break;  

		case "testimonial":
		    $cachefile = 'cached-testimonial-page.html';
		    break;              

		case "blog":
		    $cachefile = 'cached-blog-page.html';
		    break; 

		case "blog-detail":
		    $blog_slug = $urlSegmentArr['params'][1];
		    $cachefile = 'cached-blog-detail-page-'.$blog_slug.'.html';
		    break;                   

		case "contact-us":
		    $cachefile = 'cached-contact-us-page.html';
		    break;

		case "under-maintenance":
		    $cachefile = 'cached-under-maintenance-page.html';
		    break;   

		default:
		   $cachefile = null;
	}

	if(!empty($cachefile) && $siteCachingStatus == "active"){
		$cachefilePath = ROOTPATH.'/cache/'.$cachefile;
		$cachetime = APP_CACHE_TIME;

		//Serve from the cache if it is younger than $cachetime
		if (file_exists($cachefilePath) && time() - $cachetime < filemtime($cachefilePath)) {
		    //echo "<!-- Cached copy, generated ".date('H:i', filemtime($cachefilePath))." -->\n";
		    readfile($cachefilePath);
		    exit;
		}
		ob_start(); // Start the output buffer
	}else{
		$cachefilePath = null;
	}
?>