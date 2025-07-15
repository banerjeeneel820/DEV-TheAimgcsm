<?php

include_once("../constants.php");

$action = $_REQUEST['action'];
//creating global interface object
$GlobalInterfaceObj = new GlobalInterfaceModel();

switch ($action) {
    //print_r($_POST);exit; 
  case 'contact-form':
    //trigger exception in a "try" block
    try {
      send_mail($_POST);
      echo json_encode(array('check' => 'success'));
    }

    //catch exception
    catch (Exception $e) {
      echo json_encode(array('check' => 'failure'));
    }
    //print_r($return_data);

    break;

  case 'newsletter':
    //trigger exception in a "try" block
    try {
      send_mail($_POST);
      echo json_encode(array('check' => 'success'));
    }

    //catch exception
    catch (Exception $e) {
      echo json_encode(array('check' => 'failure'));
    }
    //print_r($return_data);

    break;

  case 'killTheSiteNow':

    $kill_status = $_POST['status'];

    if ($kill_status = "instant_kill") {
      echo json_encode(array('check' => 'success', 'msg' => 'Site got killed!'));
    } else {
      echo json_encode(array('check' => 'failure', 'msg' => 'Please provide a kill status!'));
    }

    break;

  case "createNewsletter":

    $email = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['subscriber_email']));
    //Call create subscriber method
    $returnArr = $GlobalInterfaceObj->create_Global_Newsletter($email);

    if ($returnArr['check'] == "success") {
      $dataArr['user_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['subscriber_email']));
      $dataArr['user_type'] = "newsletter";

      $dataArr['verification_type'] = 'resend_user_verification_link';
      $sendVerificationLinkResult = sendUserVerificationLink($dataArr);
      //print_r($sendVerificationLinkResult);exit;
      $userDetailArr = $sendVerificationLinkResult['user_detail'];
      $emailParamArr['user_activation_link'] = $sendVerificationLinkResult['user_activation_link'];

      $emailParamArr['receiver_name'] = "Subscriber";
      $emailParamArr['receiver_email'] = $userDetailArr->email;

      $emailParamArr['email_code'] = 'user-signup-verification';
      $sendMailResult = php_mailer_send_mail($emailParamArr);
      if ($sendMailResult) {
        echo json_encode(array('check' => 'success', 'msg' => 'Verification link has been sent successfully to your email.'));
      } else {
        echo json_encode(array('check' => 'failure', 'msg' => 'Something went wrong! Please try again.'));
      }
    } else {
      echo json_encode($returnArr);
    }

    break;

  case "fetchStudentDetail":
    //Validating captch & collecting response 
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $validate_captcha = checkCaptchaResponse($recaptcha_response);

    if ($validate_captcha) {
      $paramArr['studentID'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['studentID']));

      //Verifying student detail
      $returnArr = $GlobalInterfaceObj->fetch_Student_Detail($paramArr);

      if ($returnArr['check'] == "success") {
        $studentDetailArr = $returnArr['studentDetail'];

        $student_image_path = USER_UPLOAD_DIR . 'student/' . $studentDetailArr['image_file_name'];

        if (!strlen($studentDetailArr['image_file_name']) > 0 || !file_exists($student_image_path)) {
          $studentDetailArr['student_dp'] = ADMIN_RESOURCE_URL . "images/default-user-avatar.jpg";
        } else {
          $studentDetailArr['student_dp'] = USER_UPLOAD_URL . 'student/' . $studentDetailArr['image_file_name'];
        }

        if ($studentDetailArr['student_status'] == "course_complete") {
          $studentDetailArr['student_status'] = "Course Complete";
        } else {
          $studentDetailArr['student_status'] = ucfirst($studentDetailArr['student_status']);
        }

        $studentDetailArr['stu_result'] = ucfirst($studentDetailArr['stu_result']);

        $studentDetailArr['stu_dob'] = date('jS F, Y', strtotime($studentDetailArr['stu_dob']));

        $studentDetailArr['stu_gender'] = ucfirst($studentDetailArr['stu_gender']);
        $studentDetailArr['stu_marital_status'] = ucfirst($studentDetailArr['stu_marital_status']);
        $studentDetailArr['student_status'] = ucfirst($studentDetailArr['student_status']);

        echo json_encode(array("check" => "success", "studentDetail" => $studentDetailArr));
      } else {
        echo json_encode($returnArr);
      }
    } else {
      echo json_encode(array('check' => 'failure', 'message' => 'Not a valid captcha response; Please try again.'));
      exit;
    }

    break;

  case "unsubscribeNewsletter":
    $email = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['unsubscribe_email']));

    $dataArr['user_email'] = $email;
    $dataArr['user_type'] = "newsletter";
    $dataArr['verification_type'] = 'send_subscriber_welcome_mail';
    $sendVerificationLinkResult = sendUserVerificationLink($dataArr);
    //print_r($sendVerificationLinkResult);exit;
    $emailParamArr['user_activation_link'] = $sendVerificationLinkResult['user_activation_link'];

    $emailParamArr['receiver_name'] = "Subscriber";
    $emailParamArr['receiver_email'] = $email;

    $emailParamArr['email_code'] = 'user-newsletter-unsubscribe';
    $sendMailResult = php_mailer_send_mail($emailParamArr);
    if ($sendMailResult) {
      echo json_encode(array('check' => 'success', 'msg' => 'Verification link has been sent successfully to your email.'));
    } else {
      echo json_encode(array('check' => 'failure', 'msg' => 'Something went wrong! Please try again.'));
    }
    break;

  case 'resend_user_verification_link':
    if ($_POST['action'] == 'resend_user_verification_link') {
      $dataArr['user_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_email']));
      $dataArr['user_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_type']));

      $dataArr['verification_type'] = 'resend_user_verification_link';
      $sendVerificationLinkResult = sendUserVerificationLink($dataArr);

      //print_r($sendVerificationLinkResult);exit;

      $userDetailArr = $sendVerificationLinkResult['user_detail'];
      $emailParamArr['user_activation_link'] = $sendVerificationLinkResult['user_activation_link'];

      if ($dataArr['user_type'] == 'student') {
        $emailParamArr['receiver_name'] = $userDetailArr->stu_name;
        $emailParamArr['receiver_email'] = $userDetailArr->stu_email;
      } else if ($dataArr['user_type'] == 'franchise') {
        $emailParamArr['receiver_name'] = $userDetailArr->center_name;
        $emailParamArr['receiver_email'] = $userDetailArr->fran_email;
      } else if ($dataArr['user_type'] == 'newsletter') {
        $emailParamArr['receiver_name'] = "Subscriber";
        $emailParamArr['receiver_email'] = $userDetailArr->email;
      }

      $emailParamArr['email_code'] = 'user-signup-verification';
      $sendMailResult = php_mailer_send_mail($emailParamArr);
      if ($sendMailResult) {
        echo json_encode(array('check' => 'success', 'msg' => 'Verification link has been sent successfully to your email.'));
      } else {
        echo json_encode(array('check' => 'failure', 'msg' => 'Something went wrong! Please try again.'));
      }
    }
    break;

  case 'createGlobalEnquiry':
    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();
    //Validating captch & collecting response 
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $validate_captcha = checkCaptchaResponse($recaptcha_response);

    if ($validate_captcha) {

      $formDataArr['enquiry_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['enquiry_type']));
      $formDataArr['user_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_name']));

      $formDataArr['user_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_email']));
      $formDataArr['user_phone'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_phone']));

      $formDataArr['user_city'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_city']));
      //Check if enquiry type is course or not
      if ($formDataArr['enquiry_type'] == 'course') {
        $formDataArr['subject'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_id']));
      } else {
        $formDataArr['subject'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['subject']));
      }

      $formDataArr['user_message'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_message']));

      //Call create global hotel method
      $returnArr = $GlobalInterfaceObj->create_Global_Enquiry($formDataArr);

      if ($returnArr['check'] == "success") {
        //Send mail to contact person
        $emailParamArr['receiver_name'] = $formDataArr['user_name'];
        $emailParamArr['receiver_email'] = $formDataArr['user_email'];

        if ($formDataArr['enquiry_type'] == 'course') {
          $emailParamArr['subject'] = $_POST['course_name'];
        } else {
          $emailParamArr['subject'] = $formDataArr['subject'];
        }
        $emailParamArr['course_page_url'] = SITE_URL . "course";

        $emailParamArr['email_code'] = 'user-enquiry-response';
        $sendMailResult = php_mailer_send_mail($emailParamArr);
        if ($sendMailResult) {
          echo json_encode(array('check' => 'success', 'msg' => 'Your enquiry is reached to us successfully! We shall contact you shortly..'));
          exit;
        } else {
          echo json_encode(array('check' => 'failure', 'msg' => 'Something went wrong! Please try again.'));
          exit;
        }
      } else {
        echo json_encode(array('check' => 'failure', 'msg' => 'Something went wrong! Please try again.'));
        exit;
      }
    } else {
      echo json_encode(array('check' => 'failure', 'msg' => 'Not a valid captcha response; Please try again.'));
      exit;
    }
    break;

  case 'findUserCity':

    $cityFilteredArr = array();
    $city_name =  mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['city']));

    //Call create global hotel method
    $returnArr = $GlobalInterfaceObj->fetch_User_Search_City($city_name);

    foreach ($returnArr as $index => $city) {
      $cityFilteredArr[$index] = $city->name;
    }

    if (count($cityFilteredArr) > 0) {
      echo json_encode(array('check' => 'success', 'cities' => $cityFilteredArr));
    } else {
      echo json_encode(array('check' => 'failure', 'cities' => $cityFilteredArr));
    }

    break;

  case "clearCacheFolder":

    $cache_file_dir = APP_CACHE_DIR;

    $currentCacheFile = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['currentCacheFile']));
    $currentCacheFilePath = $cache_file_dir . $currentCacheFile;
    //Removing current cache file from server
    unlink($currentCacheFilePath);

    echo json_encode(array('check' => 'success', 'message' => 'Cache memory is successfully cleaned!'));
    break;
}
