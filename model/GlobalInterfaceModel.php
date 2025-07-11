<?php
 
    abstract class SFI_Model {
        
        private $db;
        private $globalSecretPassword;
        
        public function __construct(){
          $this->db = DB::$WRITELINK;
        }

        public function global_Fetch_All_DB($sql){
            
            $result = $this->db->query($sql);
            
            if(!$result){
              die($this->db->error);
              return false;
            }else{
               while($row = $result->fetch_object()) {
                $rows[] = $row;
               }
               return $rows;
            }
           
        }

        public function global_Rows_Count_DB($sql){
            
            $result = $this->db->query($sql);
            
            if(!$result){
              die($this->db->error);
              return false;
            }else{ 
              $row_count = $result->num_rows;
              return $row_count;
            }  
        }
        
        public function global_CRUD_DB($sql){
            
            $returnArr = array();
            $result = $this->db->query($sql);
            
            if($result){
              $last_insert_id = $this->db->insert_id;  
              $returnArr = array("check" => "success", "message" => "Query has been successfully excuted!","last_insert_id"=>$last_insert_id);    
            }else{
              $returnArr = array("check" => "failure", "message" => "Something went wrong!");  
            }
            return $returnArr;
        }

        public function global_Fetch_Single_DB($sql){
            
            //$rows = array();
            $result = $this->db->query($sql);

            if(!$result){
              die($this->db->error);
              return false;
            }else{
              $single_row = $result->fetch_object();
              return $single_row;
            }            
        }
         
 }

/**
 * GLOBAL MODEL HANDLING CLASS
 */

 class GlobalInterfaceModel extends SFI_Model {

     private $db_conn;

     public function __construct(){
       parent::__construct(); 
       $this->db_conn = DB::$WRITELINK;
     }

    public function verify_User_Status($dataArr){
         $user_id = $dataArr['user_id'];
         $user_type = $dataArr['user_type'];
         $action_type = $dataArr['action_type'];
         $rand_auth_factor = $dataArr['rand_auth_factor'];

         switch ($user_type) {
          
            case 'developer':
              $user_table = "global_support_admin";
              $user_status_field = 'user_status';
              $query_conditional_clause = "`id`='$user_id'"; //" AND `".$user_status_field."` = 'blocked'";

              $sql_check_user_status = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX.$user_table." WHERE ".$query_conditional_clause;
              break;

            case 'admin':
              $user_table = "global_support_admin";
              $user_status_field = 'user_status';
              $query_conditional_clause = "`id`='$user_id'"; //" AND `".$user_status_field."` = 'blocked'";

              $sql_check_user_status = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX.$user_table." WHERE ".$query_conditional_clause;
              break;

            case 'franchise':
              $user_table = "franchise";
              $user_status_field = 'record_status';
              $query_conditional_clause = "`id`='$user_id'"; //" AND `".$user_status_field."` = 'blocked'";

              $sql_check_user_status = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX.$user_table." WHERE ".$query_conditional_clause;
              break; 

            case 'student':
              $user_table = "student";
              $user_status_field = 'record_status';
              $query_conditional_clause = "WHERE stu.id='$user_id'"; // "AND `".$user_status_field."` = 'blocked'";

              $sql_check_user_status = "SELECT stu.*,frn.center_name,crs.course_title FROM ".DB_AIMGCSM.".".TABLEPREFIX."student stu LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."franchise frn ON stu.franchise_id = frn.id LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."course crs ON stu.course_id = crs.id ".$query_conditional_clause;
              break; 

            case 'newsletter':
              $user_table = "newsletter";
              $user_status_field = 'record_status';
              $query_conditional_clause = "`id`='$user_id'"; // "AND `".$user_status_field."` = 'blocked'";

              $sql_check_user_status = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX.$user_table." WHERE ".$query_conditional_clause;
              break;       
         }

         //echo $sql_check_user_status;exit;
         //executing query
         $user_detail = parent::global_Fetch_Single_DB($sql_check_user_status);

         $user_id = $user_detail->id;

         $user_auth_token = md5(base64_encode(APP_DEFAULT_SALT.$user_id.$rand_auth_factor));
         //Configuring query to check user status
         $sql_check_user_auth = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX.$user_table." WHERE `id`='$user_id' AND `user_auth_token`='$user_auth_token'";
         //echo $sql_check_user_auth;exit;
        
         //executing query
         $user_row_count = parent::global_Rows_Count_DB($sql_check_user_auth);

         if($user_row_count>0){
           $updated_auth_token = md5(base64_encode(APP_DEFAULT_SALT.$user_id.rand(1111,9999)));
           //Configuring query to update user auth token
           $sql_update_auth_token = "UPDATE ".DB_AIMGCSM.".".TABLEPREFIX.$user_table." SET `user_auth_token` = '$updated_auth_token',`".$user_status_field."`='active' WHERE `id`='$user_id'";
           //echo $sql_update_auth_token;exit;
           //executing query
           parent::global_CRUD_DB($sql_update_auth_token);
           //unsubscribe a subscriber
           if($user_type == "newsletter" && $action_type == "unsubscribe_newsletter"){
              $unsubscribeRslt = $this->unsubscribe_newsletter_user($user_detail->email);
              if($unsubscribeRslt['check'] == "success"){
                 return array('check'=>'success','user_detail'=>$user_detail);
              }
           }
           return array('check'=>'success','user_detail'=>$user_detail);
         }else{
           return array('check'=>'failure');
         }
     }

     public function send_User_Verification_Link($dataArr){
       $user_email = $dataArr['user_email']; 
       $user_type = $dataArr['user_type']; 
       $verification_type = $dataArr['verification_type'];

       if($verification_type == "resend_user_verification_link"){
         $page_link = 'verify-user'; 
         $action_type = 'user_sign_up_verification';
       }

       elseif($verification_type == "resend_forget_password_link"){
         $page_link = 'forget_password'; 
         $action_type = 'user_forget_password';
       }

       elseif($verification_type == "send_subscriber_welcome_mail"){
         $page_link = 'unsunscribe-newsletter'; 
         $action_type = 'unsubscribe_newsletter';
       }

       switch ($user_type) {
         case 'developer':
         case 'admin':
            $user_table = "global_support_admin";
            $sql_select_user = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."global_support_admin gsa WHERE gsa.user_email  = '$user_email' AND gsa.user_type='$user_type'";
            break;

         case 'student':
            $user_table = "student";  
            $sql_select_user = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."student stu WHERE stu.stu_email  = '$user_email'"; //" AND stu.record_status = 'active'"; 
           break;

         case 'franchise':
            $user_table = "franchise"; 
            $sql_select_user = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."franchise fran WHERE fran.fran_email  = '$user_email'"; //" AND fran.record_status = 'active'"; 
           break;  

         case 'newsletter':
           $user_table = "newsletter"; 
           $sql_select_user = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."newsletter nwsltr WHERE nwsltr.email  = '$user_email'"; //" AND nwsltr.record_status = 'blocked'"; 
           break;  
       }

       //echo $sql_select_user;exit;
       $user_row_count = parent::global_Rows_Count_DB($sql_select_user); 
 
       if($user_row_count>0){
          $user_detail = parent::global_Fetch_Single_DB($sql_select_user);
          $rand_auth_factor = base64_encode(rand(1111,9999));
          $user_id = $user_detail->id;
          $user_auth_token = md5(base64_encode(APP_DEFAULT_SALT.$user_id.$rand_auth_factor));

          $sql_update_user_auth_token = "UPDATE ".DB_AIMGCSM.".".TABLEPREFIX.$user_type." SET `user_auth_token` = '$user_auth_token' WHERE `id`='$user_id'";
          //echo $sql_update_user_auth_token;exit;
          //Updating user auth token for newly created user
          $update_user_auth_token_result = parent::global_CRUD_DB($sql_update_user_auth_token);

          //Configuring hash key
          $confirm_hash_key = base64_encode(json_encode(array('user_id'=>$user_id,'user_type'=>$user_type,'rand_auth_factor'=>$rand_auth_factor,'action_type'=>$action_type)));
          
          //Configuring activation link
          if($verification_type == 'resend_forget_password_link'){
             $user_activation_link = SITE_URL.'admin/?action=forget_password&hash='.$confirm_hash_key;
          }
          elseif($verification_type == 'resend_user_verification_link' || $verification_type == "send_subscriber_welcome_mail"){
            $user_activation_link = SITE_URL.$page_link.'/'.$confirm_hash_key;   
          }

          //echo $user_activation_link;exit;

          if($update_user_auth_token_result['check'] == 'success'){
            return array('check'=>'success','user_type'=>$user_type,'rand_auth_factor'=>$rand_auth_factor,'user_detail'=>$user_detail,'user_activation_link'=>$user_activation_link);
          }else{
            return array('check'=>'failure','msg'=>'Something went wrong; Please try again.');
          }  
       }else{
         return array('check'=>'failure','msg'=>'This email is not registered with us!Please enter the email you provided during signup.');
       }
     }

     public function unsubscribe_newsletter_user($email){
        $sql_update_newsletter_user = "UPDATE ".DB_AIMGCSM.".".TABLEPREFIX."newsletter SET `record_status` = 'blocked' WHERE `email`='$email'";
        //echo $sql_update_newsletter_user;exit;
        $returnArr = parent::global_CRUD_DB($sql_update_newsletter_user);
        return $returnArr;
     }

     public function fetch_Global_Site_Setting_Detail() {

         $sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."site_setting WHERE `update_id` = 'UPDATE_THE_AIMGCSM_SITE_SETTINGS'";

         //echo $sql;exit();
         
         $resultArr = parent::global_Fetch_Single_DB($sql);

         return $resultArr;
     }


     public function fetch_Student_Detail($paramArr) {
         
         $student_password = $paramArr['student_password'];

         $studentID = $paramArr['studentID'];

         $where_Clause = "WHERE stu.stu_id = '$studentID'";

         $sql = "SELECT COALESCE(stu.stu_id,'Not Available') as stu_id,COALESCE(stu.stu_name,'Not Available') as stu_name,COALESCE(stu.stu_father_name,'Not Available') as stu_father_name,COALESCE(stu.stu_address,'Not Available') as stu_address,COALESCE(stu.stu_phone,'Not Available') as stu_phone,COALESCE(stu.stu_email,'Not Available') as stu_email,COALESCE(stu.stu_gender,'Not Available') as stu_gender,COALESCE(stu.stu_dob,'Not Available') as stu_dob,COALESCE(stu.image_file_name,'Not Available') as image_file_name,COALESCE(stu. stu_qualification,'Not Available') as stu_qualification,COALESCE(stu.stu_marital_status,'Not Available') as stu_marital_status,COALESCE(stu.student_status,'Not Available') as student_status,COALESCE(stu.stu_result,'Not Available') as stu_result,COALESCE(stu.record_status,'Not Available') as record_status,COALESCE(stu. created_at,'Not Available') as created_at,COALESCE(frn.center_name,'Not Available') as center_name,COALESCE(crs.course_title,'Not Available') as course_title FROM ".DB_AIMGCSM.".".TABLEPREFIX."students stu LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."franchise frn ON stu.franchise_id = frn.id LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."results rslt ON stu.stu_id = rslt.stu_id LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."course crs ON stu.course_id = crs.id ".$where_Clause;

         //echo $sql;exit();
         
         $checkStuentExists = parent::global_Rows_Count_DB($sql);

         if($checkStuentExists>0){
           $studentDetailArr = json_decode(json_encode(parent::global_Fetch_Single_DB($sql)),true);
           if($studentDetailArr['record_status'] == 'active'){
              $returnArr = array('check'=>'success','studentDetail'=>$studentDetailArr);
           }else{
              $errorMessage = "You are not an active student anymore! Please <a href='".SITE_URL."contact' style='color:blue'>contact</a> our support team for help.";
              $returnArr = array('check'=>'failure','message'=>$errorMessage);
           }   
         }else{
            $errorMessage = "You are not an active student anymore! Please <a href='".SITE_URL."contact' style='color:blue'>contact</a> our support team for help.";
            $returnArr = array('check'=>'failure','message'=>$errorMessage);
         }

         return $returnArr;
     }

     public function fetch_Email_Template_Detail($email_code) {
         
         $sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."email_template et WHERE et.code='$email_code'";

         //echo $sql;exit();
         
         $resultArr = parent::global_Fetch_Single_DB($sql);

         return $resultArr;
     }

     public function fetch_Global_Course($dataArr) {

         $protocol = $dataArr['protocol'];

         switch ($protocol) {
           case 'home_page':
                    
             $sql = "SELECT crs.*, (COUNT(DISTINCT stu.id)) as enrolled_student_count FROM ".DB_AIMGCSM.".".TABLEPREFIX."course crs LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."students stu ON crs.id = stu.course_id WHERE crs.record_status = 'active' GROUP BY crs.id  ORDER BY enrolled_student_count DESC";

             //echo $sql;exit;
           
             break;
           
           case 'footer':
                    
             $sql = "SELECT crs.*, (COUNT(DISTINCT stu.id)) as enrolled_student_count FROM ".DB_AIMGCSM.".".TABLEPREFIX."course crs LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."students stu ON crs.id = stu.course_id WHERE crs.record_status = 'active' GROUP BY crs.id  ORDER BY enrolled_student_count DESC LIMIT 0,3";

             //echo $sql;exit;
           
             break; 

           case 'main_page':

            $sql = "SELECT crs.*, (COUNT(DISTINCT stu.id)) as enrolled_student_count FROM ".DB_AIMGCSM.".".TABLEPREFIX."course crs LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."students stu ON crs.id = stu.course_id WHERE crs.record_status = 'active' GROUP BY crs.id  ORDER BY enrolled_student_count DESC"; 

            //echo $sql;exit;            

            break;
         } 

         $resultArr = parent::global_Fetch_All_DB($sql);
         
         return $resultArr;
     }

     public function count_Global_Students() {
         $sql_row_count = "SELECT stu.id FROM ".DB_AIMGCSM.".".TABLEPREFIX."students stu ORDER BY stu.created_at"; 

         //echo $sql_row_count;exit;
         $count_students = parent::global_Rows_Count_DB($sql_row_count);
         return $count_students;
     }

     public function fetch_Slider_Arr($dataArr) {

         $slider_type = $dataArr['slider_type'];

         $sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."home_sliders WHERE `record_status` = 'active' AND `slider_type`='$slider_type'";

         //echo $sql;exit();
         
         $resultArr = parent::global_Fetch_All_DB($sql);

         return $resultArr;
     }

     public function fetch_Global_Franchise($dataArr) {

         $protocol = $dataArr['protocol'];

         switch ($protocol) {
           case 'home_page':
                    
             $sql = "SELECT fran.*,(COUNT(DISTINCT stu.id)) as enrolled_student_count FROM ".DB_AIMGCSM.".".TABLEPREFIX."franchise fran LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."students stu ON fran.id = stu.franchise_id WHERE fran.record_status = 'active' GROUP BY fran.id ORDER BY enrolled_student_count DESC";
           
             break;

            case 'main_page':

              $sql = "SELECT fran.*,(COUNT(DISTINCT stu.id)) as enrolled_student_count FROM ".DB_AIMGCSM.".".TABLEPREFIX."franchise fran LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."students stu ON fran.id = stu.franchise_id WHERE fran.record_status = 'active' GROUP BY fran.id ORDER BY enrolled_student_count DESC";

              break;
          } 
         
         //echo $sql;exit;
         
         $resultArr = parent::global_Fetch_All_DB($sql); 

         return $resultArr;
     }

     public function fetch_Global_News($protocol) {

        switch ($protocol) {
           case 'featured':

             $sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."news nws WHERE nws.record_status='active' AND nws.featured_status = 'active' ORDER BY nws.id ASC";
             //echo $sql;exit;
             $resultArr = parent::global_Fetch_All_DB($sql); 

             break;

          case 'main_page':

             $sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."news nws WHERE nws.record_status='active' ORDER BY nws.id ASC";
             //echo $sql;exit;
             $resultArr = parent::global_Fetch_All_DB($sql); 

             break;   
         }     

         return $resultArr;
     }

     public function fetch_Student_Testimonial($dataArr) {

         $protocol = $dataArr['protocol'];

         switch ($protocol) {
           case 'home_page':
                    
             $sql = "SELECT fdb.*,stu.stu_name,stu.file_upload_type,stu.image_file_name,fran.center_name,crs.course_title FROM ".DB_AIMGCSM.".".TABLEPREFIX."feedback fdb LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."students stu ON fdb.user_id = stu.stu_id LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."franchise fran ON stu.franchise_id = fran.id LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."course crs ON stu.course_id = crs.id WHERE fdb.feedback_type = 'testimonial' AND stu.id is not null ORDER BY stu.created_at";
           
             break;

            case 'main_page':

              $sql = "SELECT fdb.*,stu.stu_name,stu.file_upload_type,stu.image_file_name,fran.center_name,crs.course_title FROM ".DB_AIMGCSM.".".TABLEPREFIX."feedback fdb LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."students stu ON fdb.user_id = stu.stu_id LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."franchise fran ON stu.franchise_id = fran.id LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."course crs ON stu.course_id = crs.id WHERE fdb.feedback_type = 'testimonial' AND stu.id is not null ORDER BY stu.created_at";

              break;
          } 
         //echo $sql;exit;
         
         $resultArr = parent::global_Fetch_All_DB($sql); 

         return $resultArr;
     }

     public function fetch_Global_Gallery($dataArr) {
         //pagination property
         $protocol = $dataArr['protocol'];
         $limit = $dataArr['limit'];
         $pageNo = $dataArr['pageNo'];

         $offset = ($pageNo-1) * $limit;

         switch ($protocol) {
           case 'home_page':
                    
             $sql = "SELECT g.*, GROUP_CONCAT(DISTINCT pc.name) as category_string FROM ".DB_AIMGCSM.".".TABLEPREFIX."gallery g LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."post_category poc ON ( g.id=poc.post_id AND poc.post_type='gallery' ) LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."parent_category pc ON poc.category_id = pc.id WHERE g.featured_status = 'active' GROUP BY g.id DESC LIMIT 0,6";  
             //echo $sql;exit;
             $resultArr = parent::global_Fetch_All_DB($sql);
           
             break;

            case 'main_page':

              $sql = "SELECT g.*, GROUP_CONCAT(DISTINCT pc.name) as category_string FROM ".DB_AIMGCSM.".".TABLEPREFIX."gallery g LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."post_category poc ON ( g.id=poc.post_id AND poc.post_type='gallery' ) LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."parent_category pc ON poc.category_id = pc.id WHERE g.record_status = 'active' GROUP BY g.id DESC LIMIT $offset, $limit";  

              $sql_row_count = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."gallery g ORDER BY g.id DESC";
              //echo $sql;exit;
              $resultArr['data'] = parent::global_Fetch_All_DB($sql); 
              $resultArr['row_count'] = parent::global_Rows_Count_DB($sql_row_count); 

              break;
          } 
                    
         return $resultArr;
     }

     public function fetch_Single_Parent_Category($type) {        
         
         $sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."parent_category WHERE `parent_category`='$type' ORDER BY id DESC";

         //echo $sql;exit();
         
         $resultArr = parent::global_Fetch_All_DB($sql);

         return $resultArr;
     }

     public function fetch_Blog_Detail($blog_slug) {
         
         $sql = "SELECT b.id,b.blog_title,b.seo_url_structure,b.file_upload_type,b.featured_image,b.blog_description,b.created_at, GROUP_CONCAT(DISTINCT pc.name) as category_string FROM ".DB_AIMGCSM.".".TABLEPREFIX."blog b LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."post_category poc ON ( b.id=poc.post_id AND poc.post_type='blog' ) LEFT JOIN ".DB_AIMGCSM.".".TABLEPREFIX."parent_category pc ON poc.category_id = pc.id WHERE `seo_url_structure`='$blog_slug'";

         //echo $sql;exit();
         $resultArr = parent::global_Fetch_Single_DB($sql);

         return $resultArr;
     }

     public function fetch_Course_Detail($course_title) {
         
         $sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."course c WHERE c.course_title = '$course_title'";

         //echo $sql;exit();

         //echo $sql;exit();
         $resultArr = parent::global_Fetch_Single_DB($sql);

         return $resultArr;
     }  

     public function fetch_Franchise_Detail($center_name) {
         
         $sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."franchise fran WHERE fran.center_name = '$center_name'";

         //echo $sql;exit();

         //echo $sql;exit();
         $resultArr = parent::global_Fetch_Single_DB($sql);

         return $resultArr;
     }  

      public function fetch_User_Search_City($city_name) {
         
         $sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."cities c WHERE c.name LIKE '%$city_name%'";

         //echo $sql;exit();

         //echo $sql;exit();
         $resultArr = parent::global_Fetch_All_DB($sql);

         return $resultArr;
     }  

     public function create_Global_Enquiry($insertDataArr) {
                  
         $enquiry_type = $insertDataArr['enquiry_type'];
         $user_name = $insertDataArr['user_name'];
         $user_email = $insertDataArr['user_email'];
         
         $user_phone = $insertDataArr['user_phone'];
         $user_city = $insertDataArr['user_city'];
         $subject = $insertDataArr['subject'];         
         $user_message = $insertDataArr['user_message'];         
         
        
         $sql = "INSERT INTO ".DB_AIMGCSM.".".TABLEPREFIX."enquiry SET `enquiry_type` = '$enquiry_type',`user_name` = '$user_name',`user_email` = '$user_email',`user_phone` = '$user_phone',`user_city` = '$user_city',`subject` = '$subject',`user_message` = '$user_message',`created_at` = now()";

         //echo $sql;exit();
             
         $resultArr = parent::global_CRUD_DB($sql);

         return $resultArr;
     } 

     public function create_Global_Newsletter($email) {

         //Check if this user is already a subscriber
         $sql_check_user = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."newsletter nwsltr WHERE nwsltr.email  = '$email'";
         //echo $sql_check_user;exit; 
         $user_row_count = parent::global_Rows_Count_DB($sql_check_user);  
         
         if($user_row_count == 0){
           $sql = "INSERT INTO ".DB_AIMGCSM.".".TABLEPREFIX."newsletter SET `email` = '$email',`created_at` = now()";  
           //echo $sql;exit();
           $resultArr = parent::global_CRUD_DB($sql);
         }else{
           $resultArr = array('check'=>'failure','message'=>'You are already subscribed with us! Please try another email.');
         } 
         return $resultArr;
     } 
  }
  
?> 