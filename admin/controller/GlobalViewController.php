<?php    
    defined('ROOTPATH') OR exit('No direct script access allowed');
    
    class GlobalViewController{

    	private $page_route;
    	private $page_title;
    	private $page_assets;
    	private $page_content;
		private $isTinyEditorAllowed;
    	private $site_setting_data;
    	private $globalLibraryHandlerObj;

    	public function __construct($route,$_dataArr){
    		$this->page_route = $route;
    		$this->page_content = $_dataArr;
    		$this->page_title = $_dataArr['pageData']['page_title'];
            $this->page_assets = $_dataArr['assetData']; 
    		$this->site_setting_data = $this->page_content['site_setting_data'];
			$this->isTinyEditorAllowed = $_dataArr['pageData']['tiny_allowed'] ?? true;
            
    		//Creating object for global library class
    		$this->globalLibraryHandlerObj = new GlobalLibraryHandler();
    	}

    	public function render(){

    		$pageContent =  $this->page_content;
			$page_title = $this->page_title;
			$isTinyAllowed = $this->isTinyEditorAllowed;

    		if(empty($this->page_route) && empty($_SESSION['user_id'])){
			    $this->page_route = 'login';
			}

			if($this->page_route != 'login' && $this->page_route != 'logout'){
                $cssPluginArr = $this->page_assets['css'];
  				//Header file included
				include_once(ROOTPATH."/layout/header.php");
			}

			if(!$this->page_content['pageData']['page_permission']){
				if($this->page_route != 'login' && $this->page_route != 'logout'){
					$this->page_route = 'no_access';
				}
			}

			if($_SESSION['user_type'] != 'admin' && $_SESSION['user_type'] != 'developer'){
				if(!$this->page_content['check_site_maintenance'] && $this->page_route != 'login' && $this->page_route != 'logout'){
                    $this->page_route = 'under_maintenance';
				}
			}

			switch($this->page_route){

				case "home":
				  if($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'developer'){
				  	 include_once(ROOTPATH."/view/utility/main_dashboard.php");
				  }
				  elseif($_SESSION['user_type'] == 'franchise'){
				  	 include_once(ROOTPATH."/view/franchise/franchise_dashboard.php");
				  }
				  elseif($_SESSION['user_type'] == 'student'){
				  	 include_once(ROOTPATH."/view/exam/student_exam_list.php");
				  }
				  break;

				case "view_franchises":
				    include_once(ROOTPATH."/view/franchise/view_franchises.php");
				    break;

				case "view_courses":
				    include_once(ROOTPATH."/view/course/view_courses.php");
				    break; 

				case "view_batches":
				    include_once(ROOTPATH."/view/batch/view_batches.php");
				    break;    

				case "view_students":
				    include_once(ROOTPATH."/view/student/view_students.php");
				    break; 

				case "view_receipts":
				    include_once(ROOTPATH."/view/receipt/manage_receipt.php");
				    break;  
				
				case "view_due_students":
					include_once(ROOTPATH. "/view/receipt/view_due_students.php");	
					break; 

				case "view_exams":
				    include_once(ROOTPATH."/view/exam/view_exams.php");
				    break;

				case "start_exam":
				    include_once(ROOTPATH."/view/exam/start_student_exam.php");
				    break;                    	

				case "view_category":
				    include_once(ROOTPATH."/view/category/view_category.php");
				    break;

				case "view_news":
				    include_once(ROOTPATH."/view/news/view_news.php");
				    break;     

				case "view_email_templates":
				    include_once(ROOTPATH."/view/email_template/view_email_templates.php");
				    break; 

				case "view_enquiry":
				    include_once(ROOTPATH."/view/enquiry/view_enquiry.php");
				    break;                        

				case "gallery":
				    include_once(ROOTPATH."/view/gallery/gallery.php");
				    break;
				
				case "add_franchise":
				case "edit_franchise":
				    include_once(ROOTPATH."/view/franchise/manage_franchise.php");
				    break;  

				case "add_course":
				case "edit_course":
				    include_once(ROOTPATH."/view/course/manage_course.php");
				    break;

				case "add_student":
				case "edit_student":
				    include_once(ROOTPATH."/view/student/manage_student.php");
				    break; 

				case "clone_student":    
				    include_once(ROOTPATH."/view/student/clone_student.php");
				    break; 

				case "student_admission":
				    include_once(ROOTPATH."/view/student/manage_student_admission.php");
				    break; 

				case "manage_temp_students":
				    include_once(ROOTPATH."/view/student/manage_temp_students.php");
				    break;  

				case "add_exam":
				case "edit_exam":
				    include_once(ROOTPATH."/view/exam/manage_exam.php");
				    break;    

				case "manage_questions":
				    include_once(ROOTPATH."/view/exam/manage_questions.php");
				    break;               

				case "add_email_template":
				case "edit_email_template":
				    include_once(ROOTPATH."/view/email_template/manage_email_template.php");
				    break; 

				case "add_news":
				case "edit_news":
				    include_once(ROOTPATH."/view/news/manage_news.php");
				    break;                       

				case 'no_access':
					include_once(ROOTPATH."/view/utility/no_access.php");
					break;

				case 'under_maintenance':
					include_once(ROOTPATH."/view/utility/under_maintenance.php");
					break;		                  
				
				case 'login':
					include_once(ROOTPATH."/view/utility/login.php");
					break;

				case 'debug':
					include_once(ROOTPATH."/view/utility/debug.php");
					break;	

				case "home_sliders":
				    include_once(ROOTPATH."/view/settings/home_sliders.php");
				    break;	

				case "manage_cities":
				    include_once(ROOTPATH."/view/settings/manage_cities.php");
				    break;	    

				case "edit_site_setting":
				    include_once(ROOTPATH."/view/settings/edit_site_setting.php");
				    break;

				case "edit_profile":
				case "edit_admin_profile":
				case "edit_franchise_profile":
				   if($_SESSION['user_type'] == "franchise"){
                      include_once(ROOTPATH."/view/franchise/edit_franchise_profile.php");  
				   }
				   elseif($_SESSION['user_type'] == "student"){
				  	  include_once(ROOTPATH."/view/student/edit_student_profile.php");
				   }else{
				  	  include_once(ROOTPATH."/view/settings/edit_profile.php");
				   }
				    
				   break; 

				case 'logout':
				    session_destroy();
				    header("Location: ".SITE_URL);
					break;               
				    	
				default:
				  if($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'developer'){
				  	 //include_once(ROOTPATH."/view/utility/main_dashboard.php");
				  }

				  elseif($_SESSION['user_type'] == 'student'){
				  	 include_once(ROOTPATH."/view/student/student_dashboard.php");
				  }

			}
			if($this->page_route != 'login' && $this->page_route != 'logout'){
				$jsPluginArr = $this->page_assets['js'];
				//Footer file included
				include_once(ROOTPATH."/layout/footer.php");
			}

    	}
    	
    }
	
?>