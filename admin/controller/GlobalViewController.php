<?php    
    defined('ROOTPATH') OR exit('No direct script access allowed');
    
    class GlobalViewController extends BaseController
	{
    	private $page_route;
    	private $page_title;
    	private $page_assets;
    	private $page_content;
		private $isTinyEditorAllowed;
    	private $site_setting_data;
		private $GlobalViewDataControllerObj;

		private $utilityService;

    	public function __construct($route,$_dataArr){
			parent::__construct();
    		
			$this->page_route = $route;
    		$this->page_content = $_dataArr;
    		$this->page_title = $_dataArr['pageData']['page_title'];
            $this->page_assets = $_dataArr['assetData']; 
    		$this->site_setting_data = $this->page_content['site_setting_data'];
			$this->isTinyEditorAllowed = $_dataArr['pageData']['tiny_allowed'] ?? true;
            
    		//Creating object for global view data class
			$this->GlobalViewDataControllerObj = new GlobalViewDataController();

			//Creating object for utility service class
			$this->utilityService = new UtilityService($this->interface,$this->lib);
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
				  	 include_once(ROOTPATH."/views/utility/main_dashboard.php");
				  }
				  elseif($_SESSION['user_type'] == 'franchise'){
				  	 include_once(ROOTPATH."/views/franchise/franchise_dashboard.php");
				  }
				  elseif($_SESSION['user_type'] == 'student'){
				  	 include_once(ROOTPATH."/views/exam/student_exam_list.php");
				  }
				  break;

				case "view_franchises":
				    include_once(ROOTPATH."/views/franchise/view_franchises.php");
				    break;

				case "view_courses":
				    include_once(ROOTPATH."/views/course/view_courses.php");
				    break; 

				case "view_batches":
				    include_once(ROOTPATH."/views/batch/view_batches.php");
				    break;    

				case "view_students":
				    include_once(ROOTPATH."/views/student/view_students.php");
				    break; 

				case "view_receipts":
				    include_once(ROOTPATH."/views/receipt/manage_receipt.php");
				    break;  
				
				case "view_due_students":
					include_once(ROOTPATH. "/view/receipt/view_due_students.php");	
					break; 

				case "view_exams":
				    include_once(ROOTPATH."/views/exam/view_exams.php");
				    break;

				case "start_exam":
				    include_once(ROOTPATH."/views/exam/start_student_exam.php");
				    break;                    	

				case "view_category":
				    include_once(ROOTPATH."/views/category/view_category.php");
				    break;

				case "view_news":
				    include_once(ROOTPATH."/views/news/view_news.php");
				    break;     

				case "view_email_templates":
				    include_once(ROOTPATH."/views/email_template/view_email_templates.php");
				    break; 

				case "view_enquiry":
				    include_once(ROOTPATH."/views/enquiry/view_enquiry.php");
				    break;                        

				case "gallery":
				    include_once(ROOTPATH."/views/gallery/gallery.php");
				    break;
				
				case "add_franchise":
				case "edit_franchise":
				    include_once(ROOTPATH."/views/franchise/manage_franchise.php");
				    break;  

				case "add_course":
				case "edit_course":
				    include_once(ROOTPATH."/views/course/manage_course.php");
				    break;

				case "add_student":
				case "edit_student":
				    include_once(ROOTPATH."/views/student/manage_student.php");
				    break; 

				case "clone_student":    
				    include_once(ROOTPATH."/views/student/clone_student.php");
				    break; 

				case "student_admission":
				    include_once(ROOTPATH."/views/student/manage_student_admission.php");
				    break; 

				case "manage_temp_students":
				    include_once(ROOTPATH."/views/student/manage_temp_students.php");
				    break;  

				case "add_exam":
				case "edit_exam":
				    include_once(ROOTPATH."/views/exam/manage_exam.php");
				    break;    

				case "manage_questions":
				    include_once(ROOTPATH."/views/exam/manage_questions.php");
				    break;               

				case "add_email_template":
				case "edit_email_template":
				    include_once(ROOTPATH."/views/email_template/manage_email_template.php");
				    break; 

				case "add_news":
				case "edit_news":
				    include_once(ROOTPATH."/views/news/manage_news.php");
				    break;                       

				case 'no_access':
					include_once(ROOTPATH."/views/utility/no_access.php");
					break;

				case 'under_maintenance':
					include_once(ROOTPATH."/views/utility/under_maintenance.php");
					break;		                  
				
				case 'login':
					include_once(ROOTPATH."/views/utility/login.php");
					break;

				case 'debug':
					include_once(ROOTPATH."/views/utility/debug.php");
					break;	

				case "home_sliders":
				    include_once(ROOTPATH."/views/settings/home_sliders.php");
				    break;	

				case "manage_cities":
				    include_once(ROOTPATH."/views/settings/manage_cities.php");
				    break;	    

				case "edit_site_setting":
				    include_once(ROOTPATH."/views/settings/edit_site_setting.php");
				    break;

				case "edit_profile":
				case "edit_admin_profile":
				case "edit_franchise_profile":
				   if($_SESSION['user_type'] == "franchise"){
                      include_once(ROOTPATH."/views/franchise/edit_franchise_profile.php");  
				   }
				   elseif($_SESSION['user_type'] == "student"){
				  	  include_once(ROOTPATH."/views/student/edit_student_profile.php");
				   }else{
				  	  include_once(ROOTPATH."/views/settings/edit_profile.php");
				   }
				    
				   break; 

				case 'logout':
				    session_destroy();
				    header("Location: ".SITE_URL);
					break;               
				    	
				default:
				  if($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'developer'){
				  	 //include_once(ROOTPATH."/views/utility/main_dashboard.php");
				  }

				  elseif($_SESSION['user_type'] == 'student'){
				  	 include_once(ROOTPATH."/views/student/student_dashboard.php");
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