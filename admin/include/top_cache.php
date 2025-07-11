<?php
   defined('ROOTPATH') OR exit('No direct script access allowed');
   
	//Determining current pgae record status
	if(!empty($_GET['record_status'])){
		$record_status = $_GET['record_status'];
	}else{
		$record_status = 'active';
	}

	$platform = $_SESSION['platform'];
	$user_id = $_SESSION['user_id'];

	switch($this->page_route){

		case "home":
		  if($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'developer'){
		  	
		  	   if(!empty($_GET['dataType'])){
		  	 	   
		  	 	    if(!empty($_GET['fetchType'])){
                   $fetchType = $_GET['fetchType'];
		  	 	    }else{
                   $fetchType = 'monthly';
		  	 	    }

                if($_GET['dataType'] == "student"){
                   $cachefile = 'cached-main-dashboard-student-'.$user_id.'-'.$fetchType.'-'.$platform.'.html';
                }else{
                   $cachefile = 'cached-main-dashboard-receipt-'.$user_id.'-'.$fetchType.'-'.$platform.'.html';
                }

			  	}else{
			  	 	$cachefile = 'cached-main-dashboard-'.$user_id.'-'.$platform.'.html';
			  	}	 

		  }
		  elseif($_SESSION['user_type'] == 'franchise'){
		  	  
		  	  if(!empty($_GET['dataType'])){
		  	 	   
		  	 	    if(!empty($_GET['fetchType'])){
                   $fetchType = $_GET['fetchType'];
		  	 	    }else{
                   $fetchType = 'monthly';
		  	 	    }

                if($_GET['dataType'] == "student"){
                   $cachefile = 'cached-franchise-dashboard-'.$user_id.'-student-'.$fetchType.'-'.$platform.'.html';
                }else{
                   $cachefile = 'cached-franchise-dashboard-'.$user_id.'-receipt-'.$fetchType.'-'.$platform.'.html';
                }

			  	}else{
			  	 	$cachefile = 'cached-franchise-dashboard-'.$user_id.'-'.$platform.'.html';
			  	}	 
     
		  }
		  elseif($_SESSION['user_type'] == 'student'){
		  	 $cachefile = 'cached-student-dashboard-'.$user_id.'-'.$platform.'.html';
		  }
		  break;

      case "view_franchises":  
        $cachefile = 'cached-franchise-list-'.$user_id.'-'.$record_status.'-'.$platform.'.html';
        break;

      case "view_courses":  
        $cachefile = 'cached-course-list-'.$user_id.'-'.$record_status.'-'.$platform.'.html';
        break; 

      case "view_batches":  

        $param_string = $user_id.'-'.$record_status.'-'.$platform;
        
        if(!empty($_GET['course_id'])){
           $param_string .= '-'.$_GET['course_id'];
        } 

        if(!empty($_GET['franchise_id'])){
           $param_string .= '-'.$_GET['franchise_id'];
        }

        $cachefile = 'cached-batch-list-'.$param_string.'.html';	 
        
        break;    		  

		case "view_students":
		case "view_results":
		case "view_receipts":

		    $param_string = $user_id.'-'.$record_status.'-'.$platform;
          
          if(!empty($_GET['fetch_type'])){
	          $param_string .= '-'.$_GET['fetch_type'];
	       } 

		    if(!empty($_GET['pageNo'])){
	          $param_string .= '-'.$_GET['pageNo'];
	       } 
        
	       if(!empty($_GET['course_id'])){
	          $param_string .= '-'.$_GET['course_id'];
	       } 

	       if(!empty($_GET['franchise_id'])){
	          $param_string .= '-'.$_GET['franchise_id'];
	       }

	       if(!empty($_GET['limit'])){
	          $param_string .= '-'.$_GET['limit'];
	       }

	       if($this->page_route == "view_students"){
             $cachefile = 'cached-student-list-'.$param_string.'.html';	
	       }
	       elseif($this->page_route == "view_results"){
             $cachefile = 'cached-result-list-'.$param_string.'.html';	 
	       }
	       elseif($this->page_route == "view_receipts"){
             
		       if(!empty($_GET['dataType'])){
		          $param_string .= '-'.$_GET['dataType'];
		       }

		       if(!empty($_GET['receipt_season_start'])){
		          $param_string .= '-'.strtotime($_GET['receipt_season_start']);
		       }

		       if(!empty($_GET['receipt_season_end'])){
		          $param_string .= '-'.strtotime($_GET['receipt_season_end']);
		       }
             
             $cachefile = 'cached-receipt-list-'.$param_string.'.html';	 
	       }

		    break;  

		case "manage_receipt":
		   $param_string = $_GET['stu_id'].'-'.$record_status.'-'.$platform;    

		   if(!empty($_GET['receipt_season_start'])){
	         $param_string .= '-'.strtotime($_GET['receipt_season_start']);
	      }

	      if(!empty($_GET['receipt_season_end'])){
	         $param_string .= '-'.strtotime($_GET['receipt_season_end']);
	      }
	       
	      $cachefile = 'cached-single-student-receipt-list-'.$param_string.'.html';	
	      break;

		case "gallery":  
		  if(!empty($_GET['type']) && $_GET['type'] == 'add'){
           $cachefile = 'cached-gallery-create-'.$record_status.'.html';  
		  }else{
		  	  $cachefile = 'cached-gallery-list-'.$record_status.'.html';
		  }
		  break;

		case "view_blog":
		    $cachefile = 'cached-blog-list-'.$record_status.'.html';
		    break;

		case "view_category":
		    $cachefile = 'cached-category-list-'.$record_status.'.html';
		    break;

		case "view_faqs":
		    $cachefile = 'cached-faq-list-'.$record_status.'.html';
		    break; 

		case "view_newsletter":
		    $cachefile = 'cached-subscriber-list-'.$record_status.'.html';
		    break;

		case "view_metas":
		    $cachefile = 'cached-meta-list-'.$record_status.'.html';
		    break;  

		case "view_email_templates":
		    $cachefile = 'cached-email-template-list-'.$record_status.'.html';
		    break; 

		case "home_sliders":
		    
	       if(!empty($_GET['type']) && $_GET['type'] == 'add'){
	          $cachefile = 'cached-home-slider-add.html';
	       }else{
	       	 if(!empty($_GET['slider_type'])){
	            $cachefile = 'cached-home-slider-list-'.$_GET['slider_type'].'-'.$record_status.'.html'; 
		       }else{
		       	$cachefile = 'cached-home-slider-list-header-'.$record_status.'.html';
		       }
	       }
		    break;   

		case "manage_cities":
		    $cachefile = 'cached-cities-list-'.$record_status.'.html';
		    break;

		case "view_feedback":  

        $param_string = $record_status;
        
        if(!empty($_GET['feedback_type'])){
           $param_string .= '-'.$_GET['feedback_type'];
        } 

        if(!empty($_GET['user_type'])){
           $param_string .= '-'.$_GET['user_type'];
        }

        if(!empty($_GET['user_id'])){
           $param_string .= '-'.$_GET['user_id'];
        }

        $cachefile = 'cached-feedback-list-'.$param_string.'.html';	 
        
        break;

      case "view_enquiry":  

        $param_string = $record_status;

        if(!empty($_GET['course_id'])){
           $param_string .= '-'.$_GET['course_id'];
        }

        if(!empty($_GET['pageNo'])){
           $param_string .= '-'.$_GET['pageNo'];
        }

        if(!empty($_GET['limit'])){
           $param_string .= '-'.$_GET['limit'];
        }

        if(!empty($_GET['enquiry_type'])){
           $param_string .= '-'.$_GET['enquiry_type'];
        } 

        $cachefile = 'cached-feedback-list-'.$param_string.'.html';	 
        
        break;

      case "add_franchise":
      case "add_course":
      case "add_batch":
      case "add_student":                                  
      case "add_blog":
      case "add_email_template":
      case "add_faq":
      case "add_feedback":
         $param_string = str_replace("_","-new-",$this->page_route);
         $cachefile = 'cached-'.$param_string.'.html';
         break;
   
		default:
		   $cachefile = null;
	}
   
	if(!empty($cachefile) && $this->site_setting_data->site_caching == "active"){
		$cachefilePath = APP_CACHE_DIR.$cachefile;
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