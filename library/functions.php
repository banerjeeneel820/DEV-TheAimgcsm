<?php
     
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;  
    
    function verifyUserStatus($dataArr){
	    
	    $GlobalInterfaceObj = new GlobalInterfaceModel();
	    
	    $verifyUserRslt = $GlobalInterfaceObj->verify_User_Status($dataArr);

	    //print_r($verifyUserRslt);exit;

	    $user_type = $dataArr['user_type'];
	    $action_type = $dataArr['action_type'];
        
	    if($verifyUserRslt['check'] == 'success' && $action_type == "user_sign_up_verification"){
           $userDetailArr = $verifyUserRslt['user_detail'];
	       $dataArr['user_email'] = $userDetailArr->email;
           $dataArr['user_type'] = $user_type;	
          
           
           if($user_type == "student"){
              $emailParamArr['receiver_name'] =  $userDetailArr->stu_name;
              $emailParamArr['receiver_email'] = $userDetailArr->stu_email; 
              $emailParamArr['stu_id'] = $userDetailArr->stu_id; 
              $emailParamArr['stu_phone'] = $userDetailArr->stu_phone; 
              $emailParamArr['stu_og_pass'] = $userDetailArr->stu_og_pass; 
              $emailParamArr['course_name'] = $userDetailArr->course_title; 
              $emailParamArr['franchise'] = $userDetailArr->center_name; 
              $emailParamArr['email_code'] = 'student-welcome-mail';
           }
           elseif($user_type == "franchise"){
              $emailParamArr['receiver_name'] =  $userDetailArr->owner_name;
              $emailParamArr['receiver_email'] = $userDetailArr->fran_email; 
              $emailParamArr['center_name'] =  $userDetailArr->center_name;
              $emailParamArr['fran_id'] =  $userDetailArr->fran_id;
              $emailParamArr['fran_phone'] = $userDetailArr->fran_phone; 
              $emailParamArr['fran_og_pass'] =  $userDetailArr->fran_og_pass;
              $emailParamArr['email_code'] = 'franchise-welcome-mail';
           }

           elseif($user_type == "newsletter"){
             $emailParamArr['receiver_name'] = "Subscriber";
             $emailParamArr['receiver_email'] = $userDetailArr->email; 

             $dataArr['verification_type'] = 'send_subscriber_welcome_mail';
             $sendVerificationLinkResult = sendUserVerificationLink($dataArr);
             //print_r($sendVerificationLinkResult);exit;
             $emailParamArr['user_activation_link'] = $sendVerificationLinkResult['user_activation_link'];
             $emailParamArr['email_code'] = 'newsletter-welcome-mail';
           }
           
           $emailParamArr['invoice_date'] = date('jS F, Y',time());
           $emailParamArr['admin_login_link'] = SITE_URL."admin/";
	       //print_r($emailParamArr);exit; 
	       
           $sendMailResult = php_mailer_send_mail($emailParamArr);
	       if($sendMailResult){
	          return array('check'=>'success','msg'=>'Verification link has been sent successfully to your email.');
	       }else{
	          return array('check'=>'failure','msg'=>'Something went wrong! Please try again.');
	       }   
	    }else{
           return $verifyUserRslt;
	    }
	}

    function countGlobalStudents(){
      $returnArr = array();
      $GlobalInterfaceObj = new GlobalInterfaceModel();
      $countStudentRecords = $GlobalInterfaceObj->count_Global_Students();

      return $countStudentRecords;
    } 

	function sendUserVerificationLink($dataArr){
      $returnArr = array();
      $GlobalInterfaceObj = new GlobalInterfaceModel();
      $returnArr = $GlobalInterfaceObj->send_User_Verification_Link($dataArr);

      return $returnArr;
    } 
	
	function fetchSingleParentCategory($type){
	    
	    $GlobalInterfaceObj = new GlobalInterfaceModel();
	    
	    $returnDataArr = $GlobalInterfaceObj->fetch_Single_Parent_Category($type);

	    return $returnDataArr;
	}

	function fetchGlobalCourse($dataArr){
	    
	    $GlobalInterfaceObj = new GlobalInterfaceModel();
	    
	    $courseDataArr = $GlobalInterfaceObj->fetch_Global_Course($dataArr);

	    return $courseDataArr;
	}

	function fetchGlobalFranchise($dataArr){
	    
	    $GlobalInterfaceObj = new GlobalInterfaceModel();
	    
	    $franchiseDataArr = $GlobalInterfaceObj->fetch_Global_Franchise($dataArr);

	    return $franchiseDataArr;
	}

    function fetchSliderArr($dataArr){
        
        $GlobalInterfaceObj = new GlobalInterfaceModel();
        
        $franchiseDataArr = $GlobalInterfaceObj->fetch_Slider_Arr($dataArr);

        return $franchiseDataArr;
    }

    function fetchGlobalNews($protocol){
        
        $GlobalInterfaceObj = new GlobalInterfaceModel();
        
        $franchiseDataArr = $GlobalInterfaceObj->fetch_Global_News($protocol);

        return $franchiseDataArr;
    }

    function fetchStudentTestimonial($dataArr){
        
        $GlobalInterfaceObj = new GlobalInterfaceModel();
        
        $courseDataArr = $GlobalInterfaceObj->fetch_Student_Testimonial($dataArr);

        return $courseDataArr;
    }

	function fetchGlobalGallery($dataArr){
	    
	    $GlobalInterfaceObj = new GlobalInterfaceModel();
	    
	    $galleryDataArr = $GlobalInterfaceObj->fetch_Global_Gallery($dataArr);

	    return $galleryDataArr;
	}

    //Curl call method
    function curl_request($url,$post_data=array()){
        //echo $url;exit; 
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $json = curl_exec($curl);
        $returnArr = json_decode($json, true);
        return $returnArr;
     }
     
     // Check if captcha response is a valid one:
     function checkCaptchaResponse($recaptcha_response){
        //echo $recaptcha_response;exit;
        /*if( ini_get('allow_url_fopen') ) {
            die('allow_url_fopen is enabled. file_get_contents should work well');
        } else {
            die('allow_url_fopen is disabled. file_get_contents would not work');
        }
        exit;*/
         // Build POST request:
        $recaptcha_source = 'https://www.google.com/recaptcha/api/siteverify';
        $recaptcha_secret = '6LdJ398UAAAAAHAe4Dr1HVo7OtUkop4FsV0FNvKJ';

        //Make and decode POST request:
        $recaptcha_url = $recaptcha_source . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response;
        $recaptchaServerResponseArr = curl_request($recaptcha_url);
        //$recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
        //$recaptchaServerResponseArr = json_decode($recaptcha);
        
        //print_r($recaptchaServerResponseArr);exit;

        // Take action based on the score returned:
        if ($recaptchaServerResponseArr['success'] == 1) {
            return true;
        } else {
            return false;
        }
     }

	function send_mail($formData){

	 	//print_r($formData);exit;

	 	if (!defined("PHP_EOL")) define("PHP_EOL", "\r\n");
        
       	$headers = "From: ".$formData['email']. "\r\n" .
				   "CC: alex390390820@gmail.com";
		$recipient = 'banerjeeneel.live@gmail.com';
		$subject   = 'Landing Page Customer Details';
		$body      = "Input from submitted form:".PHP_EOL;
		//$redirect  = LANDING_URL.'thanks.html';

		// loop through form input
		foreach ($_POST as $key => $value) {
		  if($key != 'action'){	
		    $body .= ucfirst($key) . ' = ' . $value . PHP_EOL;
		  }  
		}

		// additional (client) information
		$body .= PHP_EOL."Additional (client) information:".PHP_EOL
		    . 'Date = ' . date('Y-m-d H:i') . PHP_EOL
		    . 'Browser = ' . $_SERVER['HTTP_USER_AGENT'] . PHP_EOL
		    . 'IP Address = ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL
		    . 'Hostname = ' . gethostbyaddr($_SERVER['REMOTE_ADDR']) . PHP_EOL;

		 /*print"<pre>";
		 print $body;
         print"</pre>";  
		 exit;*/

		// send email
		if(mail($recipient, $subject, $body,$headers)){
			return true; 
		}else{
	        throw new Exception("Error! Something went wrong; Unable to sent mail.");
		}
	}

	function send_transaction_mail($from = ADMIN_EMAIL,$to, $body) {
	    
	    if (!defined("PHP_EOL")) define("PHP_EOL", "\r\n"); 
	    $headers  = "MIME-Version: 1.0" . PHP_EOL;
	    $headers .= "Content-type:text/html;charset=UTF-8" . PHP_EOL;
	    
	    $subject = 'Order Confirmation';
	    // More headers
	    $headers .= 'From:'.$from.PHP_EOL;
	    //echo $from."***".$to."***".$subject."***".$body;exit;
	    if(mail($to,$subject,$body,$headers)){
	        return true;
	    }else{
	        return false;
	    }
	 }

	 function fetchSiteSettingDetail(){
        $GlobalInterfaceObj = new GlobalInterfaceModel();    
        $siteSettingArr = $GlobalInterfaceObj->fetch_Global_Site_Setting_Detail();

        return $siteSettingArr;
    }

    function fetchEmailTemplateDetail($email_code){
        $GlobalInterfaceObj = new GlobalInterfaceModel();        
        $campaignDataArr = $GlobalInterfaceObj->fetch_Email_Template_Detail($email_code);

        return $campaignDataArr;
    }

	function configure_email_body($emailParam){
        /*print"<pre>";
        print_r($emailParam);
        print"</pre>";*/

        $emailReturnParamArr = array();
        $emailReturnParamArr['email_code'] = $emailParam['email_code'];
        $emailReturnParamArr['receiver_name'] = $emailParam['receiver_name'];
        $emailReturnParamArr['receiver_email'] = $emailParam['receiver_email'];
        $emailReturnParamArr['site_addr'] = SITE_URL;
        //fetching site setting detail
        $siteSettingArr = fetchSiteSettingDetail();
        $emailReturnParamArr['company_name'] = $siteSettingArr->title;
        
        //configure company logo        
        $emailReturnParamArr['company_logo'] = USER_UPLOAD_URL.'others/'.$siteSettingArr->logo;
        $emailReturnParamArr['company_signature'] = USER_UPLOAD_URL.'others/'.$siteSettingArr->signature;
        
        $emailReturnParamArr['company_contact_email'] = $siteSettingArr->contact_email;
        $emailReturnParamArr['company_contact_no'] = $siteSettingArr->phone;
        $emailReturnParamArr['company_address'] = $siteSettingArr->address;
       
        //fetching required email template detail
        $emailTemplateArr = fetchEmailTemplateDetail($emailReturnParamArr['email_code']);
        $emailReturnParamArr['email_subject'] = $emailTemplateArr->subject;
        $emailReturnParamArr['sender_name'] = $emailTemplateArr->from_name;
        $emailReturnParamArr['sender_email'] = $emailTemplateArr->from_email;
        $emailReturnParamArr['cc_email'] = $emailTemplateArr->cc_email;
        $emailReturnParamArr['email_template'] = $emailTemplateArr->template;

        switch ($emailReturnParamArr['email_code']) {

             case 'user-enquiry-response':

                //create a list of the variables to be swapped in the html template
                $swap_var = array(
                    "{SITE_ADDR}" => $emailReturnParamArr['site_addr'],
                    "{COMPANY_NAME}" => $emailReturnParamArr['company_name'],
                    "{COMPANY_EMAIL}" => $emailReturnParamArr['company_contact_email'],
                    "{COMPANY_CONTACT_NO}" => $emailReturnParamArr['company_contact_no'],
                    "{COMPANY_ADDRESS}" => $emailReturnParamArr['company_address'],
                    "{COMPANY_LOGO}" => $emailReturnParamArr['company_logo'],
                    "{EMAIL_TITLE}" => $emailReturnParamArr['email_subject'],
                    "{USER_NAME}" => $emailReturnParamArr['receiver_name'],
                    "{USER_SUBJECT}" => $emailParam['subject'],
                    "{COURSE_PAGE_URL}" => $emailParam['course_page_url']
                );
                break;        
        }
        //print_r($swap_var);exit;
        
        //search and replace for predefined variables, like SITE_ADDR, {NAME}, {lOGO}, {CUSTOM_URL} etc
        foreach (array_keys($swap_var) as $key){
            if (strlen($key) > 2 && trim($swap_var[$key]) != ''){
              $emailReturnParamArr['email_template'] = str_replace($key, $swap_var[$key], $emailReturnParamArr['email_template']);
            }
        }
        //echo $emailReturnParamArr['email_template'];exit;
        return $emailReturnParamArr;
    }

    function php_mailer_send_mail($paramArr){
        //print_r($paramArr);exit;
        $emailReturnArr = configure_email_body($paramArr);
        //print_r($emailReturnArr);exit;
      
        //Collecting necessary variables to configure send mail
        $fromEmail = $emailReturnArr['sender_email'];
        $fromName  = $emailReturnArr['sender_name'];
        $toEmail   = $emailReturnArr['receiver_email'];
        $toName    = $emailReturnArr['receiver_name'];

        $ccEmail   = $emailReturnArr['cc_email'];
    
        if(array_key_exists('file_path', $paramArr)){
           $filePath  = $emailReturnArr['attachment_path'];
        }
        $email_subject = $emailReturnArr['email_subject'];
        $body = $emailReturnArr['email_template'];
    
        //echo $body."<br>".$filePath;exit;
        //Including php mailer class
        //require_once(ROOTPATH.'/library/PHP_MAILER/class.phpmailer.php');
        
        $mail = new PHPMailer;
        $mail->IsSMTP();                                //Sets Mailer to send message using SMTP
        //$mail->SMTPDebug = 2;                           //debugging: 1 = errors and messages, 2 = messages only
        $mail->Host = 'smtp.gmail.com';                 //Sets the SMTP hosts of your Email hosting, this for Godaddy
        $mail->Port = '465';                            //Sets the default SMTP server port

        $mail->SMTPSecure = 'ssl';                      //Sets connection prefix. Options are "", "ssl" or "tls"
        //Whether to use SMTP authentication
        $mail->SMTPAuth = true;
        //Sets SMTP authentication. Utilizes the Username and Password variables
        $mail->Username = 'testsmtpsentmail@gmail.com';    //Sets SMTP username
        $mail->Password = 'dowlpeberazpiqbk';                  //Sets SMTP password
        $mail->From = $fromEmail;                       //Sets the From email address for the message
        $mail->FromName = $fromName;                    //Sets the From name of the message
        $mail->AddAddress($toEmail, $toName);           //Adds a "To" address
        $mail->addCC($ccEmail,'Admin');                         //Add cc
        $mail->WordWrap = 50;                           //Sets word wrapping on the body of the message to a given number of characters
        $mail->IsHTML(true);                            //Sets message type to HTML
        $mail->AddAttachment($filePath);                //Adds an attachment from a path on the filesystem
        $mail->Subject = $email_subject;                //Sets the Subject of the message
        $mail->Body = $body;                            
        //Set not to check ssl encryption    
        /*$mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );*/                       
        //$return_Result = $mail->Send();
        
        //echo $return_Result;exit;
    
        //return $return_Result;
        if($mail->Send()){
            return true;
        }
        else{   
          //echo "Mailer Error: " . $mail->ErrorInfo;
          return false;
        }
    }

    function fetchCourseDetail($course_title){
        
        $GlobalInterfaceObj = new GlobalInterfaceModel();
        
        $campaignDataArr = $GlobalInterfaceObj->fetch_Course_Detail($course_title);

        return $campaignDataArr;
    }

    function fetchFranchiseDetail($center_name){
        
        $GlobalInterfaceObj = new GlobalInterfaceModel();
        
        $campaignDataArr = $GlobalInterfaceObj->fetch_Franchise_Detail($center_name);

        return $campaignDataArr;
    }

	function global_Pagination_Handler($dataArr){
            
        //print_r($dataArr);exit;

	 	$returnArr = array();

	 	$GlobalInterfaceObj = new GlobalInterfaceModel();
       
	 	$table = $dataArr['table'];
        
        $action = $dataArr['action'];

	 	$protocol = $dataArr['protocol'];

	 	$pn = $dataArr['pageNo'];

	 	$limit = $dataArr['limit'];

	 	if(isset($dataArr['tag'])){
	 		$tag = $dataArr['tag'];
	 	}

        //Fetching News data for pagination
	 	switch ($table) {
         
             case 'gallery':
                 $returnData = $GlobalInterfaceObj->fetch_Global_Gallery($dataArr);
                 break;

            case 'blog':
                 $returnData = $GlobalInterfaceObj->fetch_Global_Blog($dataArr);
                 break;     
        } 

        if($protocol == 'tag'){
        	$tag_seo_title = seoUrl($tag,'seo');
        }

        $listingArr = $returnData['data'];     		    
	    
        /*print"<pre>";
	    print_r($listingArr);
	    print"</pre>";exit;*/
           
        //Fetching total news records for building link
        //$total_records = $GlobalInterfaceObj->fetch_Global_Row_Count($table);
        $total_records = $returnData['row_count'];
        //var_dump($total_records);exit;

	   // Number of pages required. 
       $total_pages = ceil($total_records / $limit);   

      //Page link variable is declared
       $pageLink = ""; 

       if($pn>1){
            $k = $pn-1;
            if($protocol != 'tag'){ 
	            
	            if($protocol != 'search'){
	            	$pageLink .= "<li><a href='".SITE_URL.$action."/page/".$k."/' aria-label='Previous'><span aria-hidden='true' class='flaticon-back'></span></a></li>";
	            }else{

	            	$pageLink .= "<li><a href='".SITE_URL.$action."/page/".$k."/true/' aria-label='Previous'><span aria-hidden='true' class='flaticon-back'></span></a></li>";
	            }
	        }else{
               $pageLink .= "<li><a href='".SITE_URL.$action."/tag/".$tag_seo_title."/page/".$k."/' aria-label='Previous'><span aria-hidden='true' class='flaticon-back'></span></a></li>";
	        }    

       } 

       for ($i=1; $i<=$total_pages; $i++) {

           if ($i==$pn) {
           	  if($protocol != 'tag'){

	                if($protocol != 'search'){ 
	                  
	                   $pageLink .= "<li class='active'><a href='".SITE_URL.$action."/page/"
	                               .$i."/'>".$i."</a></li>";
	                 }else{
	                 	$pageLink .=
	                  "<li class='active'><a href='".SITE_URL.$action."/page/"
	                               .$i."/true/'>".$i."</a></li>"; 
                    }

		       }else{
		       	  $pageLink .= "<li class='active'><a href='".SITE_URL.$action."/page/"
	                               .$i."/tag/".$tag_seo_title."/'>".$i."</a></li>";
		       }                     
           }else {
              if($protocol != 'tag'){

	           	   if($protocol != 'search'){ 

	                    $pageLink .= "<li><a href='".SITE_URL.$action."/page/".$i."/'> 
	                              ".$i."</a></li>";
	                }else{

	                	$pageLink .= "<li><a href='".SITE_URL.$action."/page/".$i."/true/'> 
	                              ".$i."</a></li>";
	                }

		       }else{
                   $pageLink .= "<li><a href='".SITE_URL.$action."/tag/".$tag_seo_title."/page/"
	                               .$i."/'>".$i."</a></li>";  
		       }                     
           }
       }

       if($pn<$total_pages){
            $k = $pn+1; 
            if($protocol != 'tag'){ 
	            if($protocol != 'search'){             
	            
	                 $pageLink .= "<li><a href='".SITE_URL.$action."/page/".$k ."/' aria-label='Next'><span aria-hidden='true' class='flaticon-right-arrow'></span></a></li>"; 
	             }else{
                     
                     $pageLink .= "<li><a href='".SITE_URL.$action."/page/".$k ."/true/' aria-label='Next'><span aria-hidden='true' class='flaticon-right-arrow'></span></a></li>";

	             }
	        }else{
	        	$pageLink .= "<li><a href='".SITE_URL.$action."/tag/".$tag_seo_title."/page/".$k."/' aria-label='Next'><span aria-hidden='true' class='flaticon-right-arrow'></span></a></li>"; 
	        }          
       }

       $returnArr['listingArr'] = $listingArr;
       $returnArr['pageLink'] = $pageLink;

	   return $returnArr;
	}

	function getUrlSegment(){
		$urlSegmentArr = array();
		$paramArr = array();
		if(strlen(ROOT_URI)>1){
		  $url_array = explode('/', str_replace(ROOT_URI,'',$_SERVER['REQUEST_URI']));
		}else{
          $url_array = explode('/', ltrim($_SERVER['REQUEST_URI'],'/'));
		}	
		
		foreach($url_array as $key => $val){
			if($key === 0){
              $urlSegmentArr['route'] = $val;
			}else{
			  $paramArr[$key] = $val;
			}
			$urlSegmentArr['params'] = $paramArr;
		}  

		return $urlSegmentArr;
	}

    function get_Gloabl_Content_Excerpt($content,$length){
            
          $end = '...&nbsp;';

          $content = strip_tags($content);

	      if (strlen($content) > $length) {

	           // truncate string
	           $stringCut = substr($content, 0, $length);

	           // make sure it ends in a word so assassinate doesn't become ass...
	           $excerpt = substr($stringCut, 0, strrpos($stringCut, ' ')).$end;
	      } else {
	          $excerpt = strip_tags($content);
	      }
   
          return $excerpt;
      }

     function seoUrl($string,$type) {

     	switch ($type) {
     		case 'seo':
     			//Lower case everything
			    $string = strtolower($string);
			    //Make alphanumeric (removes all other characters)
			    $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
			    //Clean up multiple dashes or whitespaces
			    $string = preg_replace("/[\s-]+/", " ", $string);
			    //Convert whitespaces and underscore to dash
			    $string = preg_replace("/[\s_]/", "-", $string);
     			break;
     		
     		case 'r_seo':
     			//Convert dashes into whitespaces
			    $string = preg_replace("/[\s-]/", " ", $string);
     			break;
     	}
	    
	    return $string;
	} 
 
?>