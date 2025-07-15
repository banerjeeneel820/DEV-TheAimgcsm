<?php

    defined('ROOTPATH') OR exit('No direct script access allowed');

    class GlobalPageContentController{

        private $_page_handler;
        private $GlobalLibraryHandlerObj;
        private $GlobalInterfaceControllerObj;
        private $globalReturnArr = array();

        public function __construct($page_Action){
            $this->_page_handler = $page_Action;
            $this->GlobalLibraryHandlerObj = new GlobalLibraryHandler();
            $this->GlobalInterfaceControllerObj = new GlobalInterfaceController();
            $this->globalReturnArr['check_site_maintenance'] = $this->check_Site_Maintenance_Status();
        }

        private function check_Site_Maintenance_Status(){
            //Fetch site setting
            $this->globalReturnArr['site_setting_data'] = $this->GlobalLibraryHandlerObj->fetchSiteSettingDetail();
            
            if($this->globalReturnArr['site_setting_data']->maintenance_status == 'inactive'){
               return false;
            }else{
               return true;
            }
        }

        public function get_PageContent(){

            //echo $this->_page_handler;exit;

            switch ($this->_page_handler){

                case 'view_franchises':
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->view_Franchise_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Franchise List";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','fancybox/jquery.fancybox.min','pretty-checkbox/pretty-checkbox.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min','fancybox/jquery.fancybox');
                     
                     return $this->globalReturnArr;
                     break;

                case 'view_courses':
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->view_Course_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Course List";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','fancybox/jquery.fancybox.min','pretty-checkbox/pretty-checkbox.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min','fancybox/jquery.fancybox');

                     return $this->globalReturnArr;
                     break;  

                case 'view_students':
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->view_Student_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Student List";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','fancybox/jquery.fancybox.min','pretty-checkbox/pretty-checkbox.min','select2/select2.min','datapicker/datepicker3');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','table-filter/filter-table.min','sweetalert/sweetalert.min','fancybox/jquery.fancybox','select2/select2.full.min','datapicker/bootstrap-datepicker');

                     return $this->globalReturnArr;
                     break;

                case 'view_receipts':
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Receipt_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Receipt List";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','fancybox/jquery.fancybox.min','pretty-checkbox/pretty-checkbox.min','select2/select2.min','datapicker/datepicker3','iCheck/custom','printThis/print.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min','table-filter/filter-table.min','fancybox/jquery.fancybox','select2/select2.full.min','datapicker/bootstrap-datepicker','iCheck/icheck.min','printThis/print.min');

                     return $this->globalReturnArr;
                     break;  
                
                case 'view_due_students':
                    $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->view_Due_Students_Data();

                    $this->globalReturnArr['pageData']['page_title'] = "Due Students List";

                    $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                    $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','fancybox/jquery.fancybox.min','pretty-checkbox/pretty-checkbox.min','select2/select2.min','datapicker/datepicker3','iCheck/custom','printThis/print.min');

                    $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min','table-filter/filter-table.min','fancybox/jquery.fancybox','select2/select2.full.min','datapicker/bootstrap-datepicker','iCheck/icheck.min','printThis/print.min');

                    return $this->globalReturnArr;
                    break;       

                 case 'view_exams':
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->view_Exam_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Exam List";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','pretty-checkbox/pretty-checkbox.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min');

                     return $this->globalReturnArr;
                     break;                             

                case 'view_category':
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->view_Category_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Category List";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','pretty-checkbox/pretty-checkbox.min','iCheck/custom');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min','iCheck/icheck.min');

                     return $this->globalReturnArr;
                     break;

                case 'gallery':
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->fetch_Gallery_Item_Detail();

                     $this->globalReturnArr['pageData']['page_title'] = "Gallery List";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','fancybox/jquery.fancybox.min','pretty-checkbox/pretty-checkbox.min','select2/select2.min','iCheck/custom','dropzone/dropzone.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min','fancybox/jquery.fancybox','select2/select2.full.min','iCheck/icheck.min','dropzone/dropzone.min');

                    return $this->globalReturnArr;
                    break;   

               case 'view_email_templates':
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->view_Email_Templates_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Email Template List";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','pretty-checkbox/pretty-checkbox.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min');

                     return $this->globalReturnArr;
                     break; 

                case 'view_news':
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->view_News_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "News List";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','pretty-checkbox/pretty-checkbox.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min');

                     return $this->globalReturnArr;
                     break;      

                case 'view_enquiry':
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->view_Enquiry_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Enquiry List";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','select2/select2.min','pretty-checkbox/pretty-checkbox.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min','select2/select2.full.min');

                     return $this->globalReturnArr;
                     break;                    

                case "add_franchise":
                case "edit_franchise":
                     $fetch_type = 'manage_franchise';  
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Franchise_Required_Data($fetch_type);

                     $this->globalReturnArr['pageData']['page_title'] = "Manage Franchise";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = true;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom','fancybox/jquery.fancybox.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','iCheck/icheck.min','fancybox/jquery.fancybox');

                     return $this->globalReturnArr;
                     break;

                case "add_course":
                case "edit_course":
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Course_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Manage Course";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = true;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom','fancybox/jquery.fancybox.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','iCheck/icheck.min','fancybox/jquery.fancybox');

                     return $this->globalReturnArr;
                     break;  

                case "add_student":
                case "edit_student":
                case "clone_student":
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Student_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Manage Student";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = true;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom','select2/select2.min','datapicker/datepicker3','fancybox/jquery.fancybox.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','iCheck/icheck.min','select2/select2.full.min','datapicker/bootstrap-datepicker','fancybox/jquery.fancybox');

                     return $this->globalReturnArr;
                     break;

                case "student_admission":
                      $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Student_Admission_Data();

                      $this->globalReturnArr['pageData']['page_title'] = "Manage Student Admission";

                      $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                      $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','dataTables/datatables.min','select2/select2.min','printThis/print.min');

                      $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min','select2/select2.full.min','printThis/print.min');

                     return $this->globalReturnArr;
                     break; 

                case "manage_temp_students":
                      $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Temp_Student_Data();

                      $this->globalReturnArr['pageData']['page_title'] = "Manage Temporary Students";

                      $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                      $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','select2/select2.min','datapicker/datepicker3','pretty-checkbox/pretty-checkbox.min','printThis/print.min');

                      $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','table-filter/filter-table.min','sweetalert/sweetalert.min','select2/select2.full.min','datapicker/bootstrap-datepicker','printThis/print.min');

                     return $this->globalReturnArr;
                     break;    

                case "add_exam":
                case "edit_exam":
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Exam_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Manage Exam";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = true;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom','select2/select2.min','datapicker/datepicker3','fancybox/jquery.fancybox.min');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','select2/select2.full.min','iCheck/icheck.min','datapicker/bootstrap-datepicker','fancybox/jquery.fancybox');

                     return $this->globalReturnArr;
                     break;

                case "manage_questions":
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Exam_Questions_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Manage Exam Questions";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = true;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','iCheck/icheck.min');

                     return $this->globalReturnArr;
                     break; 

                 case "start_exam":
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Start_Exam();

                     $this->globalReturnArr['pageData']['page_title'] = "Exam Page";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','iCheck/icheck.min');

                     return $this->globalReturnArr;
                     break;                        

                case "add_email_template":
                case "edit_email_template":
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Email_Template_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Manage Email Templates";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = true;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','iCheck/icheck.min');

                     return $this->globalReturnArr;
                     break;

                case "add_news":
                case "edit_news":
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_News_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Manage News";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = true;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','iCheck/icheck.min');

                     return $this->globalReturnArr;
                     break;     

                case 'home_sliders':
                  //configuring page title
                  $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Home_Sliders_Required_Data();

                  $this->globalReturnArr['pageData']['page_title'] = "Manage Home Sliders";

                  $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                  $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','fancybox/jquery.fancybox.min','pretty-checkbox/pretty-checkbox.min','iCheck/custom');

                  $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min','fancybox/jquery.fancybox','iCheck/icheck.min');

                  return $this->globalReturnArr;
                  break; 

                case 'manage_cities':
                  //configuring page title
                  $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->view_Cities_Required_Data();

                  $this->globalReturnArr['pageData']['page_title'] = "Manage Cities";

                  $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                  $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','pretty-checkbox/pretty-checkbox.min','iCheck/custom');

                  $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min','iCheck/icheck.min');

                  return $this->globalReturnArr;
                  break;     

                case 'edit_site_setting':
                  //configuring page title
                  $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->manage_Site_Setting_Required_Data();

                  $this->globalReturnArr['pageData']['page_title'] = "Manage Site Settings";

                  $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                  $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom','fancybox/jquery.fancybox.min');

                  $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','iCheck/icheck.min','fancybox/jquery.fancybox');

                  return $this->globalReturnArr;
                  break;   

                case 'edit_admin_profile': 
                     $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->edit_Admin_Profile_Required_Data();

                     $this->globalReturnArr['pageData']['page_title'] = "Manage Admin Prifile";

                     $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                     $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom');

                     $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','iCheck/icheck.min');

                     return $this->globalReturnArr;
                     break;

                case 'edit_profile':

                    if($_SESSION['user_type'] == 'developer'){
                      $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->edit_Developer_Profile_Required_Data();
                    }  

                    elseif($_SESSION['user_type'] == 'admin'){
                      $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->edit_Admin_Profile_Required_Data();
                    }

                    elseif($_SESSION['user_type'] == 'franchise'){
                      $fetch_type = 'edit_profile';  
                      $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->edit_Franchise_Profile_Data($fetch_type);
                    }

                    $this->globalReturnArr['pageData']['page_title'] = "Manage My Profile";

                    $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                    $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','iCheck/custom');

                    $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','iCheck/icheck.min');

                    return $this->globalReturnArr;
                    break;

                default:
                   
                  if($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'developer'){
                    $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->fetchUserDashboardData();
                  }

                  elseif($_SESSION['user_type'] == 'franchise'){
                    $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->fetchUserDashboardData();
                  }

                  elseif($_SESSION['user_type'] == 'student'){
                    $this->globalReturnArr['pageData'] = $this->GlobalLibraryHandlerObj->fetchStudentExamDashboard();
                  }

                  $this->globalReturnArr['pageData']['page_title'] = "Manage Dashboard";

                  $this->globalReturnArr['pageData']['tiny_allowed'] = false;

                  if($_SESSION['user_type'] != "student"){

                      $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','sweetalert/sweetalert','footable/footable.core','printThis/print.min','fancybox/jquery.fancybox.min');

                      $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','sweetalert/sweetalert.min','footable/footable.all.min','printThis/print.min','fancybox/jquery.fancybox');
                  }else{
                      $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min','dataTables/datatables.min','sweetalert/sweetalert','pretty-checkbox/pretty-checkbox.min');

                      $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min','dataTables/datatables.min','dataTables/dataTables.bootstrap4.min','sweetalert/sweetalert.min');
                  }    

                  return $this->globalReturnArr;
            }
        }

    }
    
?>
 