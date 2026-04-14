<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once(__DIR__ . "/../constants.php");

defined('ROOTPATH') or exit('No direct script access allowed');
//print_r($_POST);exit;

$action = $_POST['action'];
//Creating object for global model controller
$GlobalInterfaceControllerObj = new GlobalInterfaceController();

//Creating object for global library
$GlobalLibraryHandlerObj = new GlobalLibraryHandler();

//Creating object for global validation controller
$GlobalValidationControllerObj = new GlobalValidationController();

//Checking runtime folder existance 
$GlobalLibraryHandlerObj->checkRunTimeFolderExistance();

switch ($action) {


  case 'sendStudentReceipt':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    $idData = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['row_id']));
    $type = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['type']));
    $user_role_slug = "update_receipt";

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //checking if the feeded data is a string or not
      if (strpos($idData, ',') > 0) {
        $rowIdArr = explode(',', $idData);
      } else {
        $rowIdArr = array($idData);
      }

      if (count($rowIdArr) > 0) {
        //print_r($formDataArr);exit;

        //Call update global status modify method
        foreach ($rowIdArr as $index => $row_id) {

          $receipt_id = $row_id;
          //Fetching student receipt detail
          $studentReceiptData = $GlobalInterfaceControllerObj->fetch_Single_Receipt_Data($receipt_id);
          //Configuring email param array
          $emailParamArr['invoice_date']  = date('jS F, Y', time());
          $emailParamArr['receiver_name'] = $studentReceiptData->stu_name;
          $emailParamArr['receiver_email'] = $studentReceiptData->stu_email;
          $emailParamArr['stu_phone'] = $studentReceiptData->stu_phone;
          $emailParamArr['stu_id'] = $studentReceiptData->stu_id;

          $emailParamArr['course'] = $studentReceiptData->course_title;
          $emailParamArr['franchise'] = $studentReceiptData->center_name;

          $sdate = date("y-m-d", strtotime($studentReceiptData->receipt_season_start));
          $edate = date("y-m-d", strtotime($studentReceiptData->receipt_season_end));

          $start_date = date('jS F, Y', strtotime($sdate));
          $end_date = date('jS F, Y', strtotime($edate));

          $receipt_season = $start_date . " to " . $end_date;

          $emailParamArr['receipt_id'] = $studentReceiptData->receipt_id;
          $emailParamArr['receipt_season'] = $receipt_season;
          $emailParamArr['receipt_status'] = ucfirst($studentReceiptData->receipt_status);
          $emailParamArr['receipt_amount'] = '<i class="fa fa-inr" aria-hidden="true"></i> ' . $studentReceiptData->receipt_amount;

          if ($formDataArr['receipt_pdf'] !== null) {
            $emailParamArr['attachment_path'] = USER_UPLOAD_DIR . $dir . '/' . $formDataArr['receipt_pdf'];
          } else {
            $emailParamArr['attachment_path'] = null;
          }

          $emailParamArr['email_code'] = 'student-receipt-invoice';
          //print_r($emailParamArr);exit;
          $sendMailResult = $GlobalLibraryHandlerObj->php_mailer_send_mail($emailParamArr);

          if ($sendMailResult) {
            $returnArr = array('check' => 'success');
          }
        }
      } else {
        $returnArr = array("check" => "failure", "message" => "You haven't selected any data!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;







  case "checkUserEmailAvailability":
    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    $formDataArr['user_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_email']));
    $formDataArr['user_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_type']));
    $formDataArr['user_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_id']));
    //Call check user email method
    $returnArr = $GlobalInterfaceControllerObj->check_User_Email_Availability($formDataArr);
    echo json_encode($returnArr);
    break;

  case "exportStudentReceiptData":
    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();
    $formatedReceiptArr = array();

    $receipt_row_id = $_POST['receipt_row_id'];

    //Fetch reecipt detail
    $receiptDetailArr = $GlobalInterfaceControllerObj->fetch_Single_Receipt_Data($receipt_row_id);

    $sdate = date("y-m-d", strtotime($receiptDetailArr->receipt_season_start));
    $edate = date("y-m-d", strtotime($receiptDetailArr->receipt_season_end));

    $start_date = date('jS F, Y', strtotime($sdate));
    $end_date = date('jS F, Y', strtotime($edate));

    $receipt_season = $start_date . " to " . $end_date;

    $formatedReceiptArr[0] = $receiptDetailArr->id;
    $formatedReceiptArr[1] = $receiptDetailArr->stu_name;
    $formatedReceiptArr[2] = $receiptDetailArr->stu_email;
    $formatedReceiptArr[3] = $receiptDetailArr->stu_phone;
    $formatedReceiptArr[4] = $receiptDetailArr->stu_id;
    $formatedReceiptArr[5] = $receiptDetailArr->course_title;
    $formatedReceiptArr[6] = $receiptDetailArr->center_name;
    $formatedReceiptArr[7] = $receiptDetailArr->receipt_id;
    $formatedReceiptArr[8] = $receipt_season;
    $formatedReceiptArr[9] = ucfirst($receiptDetailArr->receipt_status);
    $formatedReceiptArr[10] = $receiptDetailArr->receipt_amount;

    header('Content-Type: text/csv; charset=utf-8');

    header('Content-Disposition: attachment; filename=DevelopersData.csv');

    $output = fopen("php://output", "w");
    fputcsv($output, array('ID', 'Student Name', 'STUDENT EMAIL', 'STUDENT CONTACT NO', 'STUDENT ID', 'COURSE', 'FRANCHISE', 'RECEIPT ID', 'RECEIPT SEASON', 'RECEIPT STATUS', 'RECEIPT AMOUNT'));

    fputcsv($output, $formatedReceiptArr);

    fclose($output);

    break;

  case 'updateSiteSettings':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = "update_site_setting";
    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $dir = 'others';
      $formDataArr['title'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['title']));

      $formDataArr['contact_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['contact_email']));

      $formDataArr['phone'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['phone']));

      $formDataArr['career_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['career_email']));

      $formDataArr['business_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['business_email']));

      $formDataArr['facebook_link'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['facebook_link']));

      $formDataArr['youtube_link'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['youtube_link']));

      $formDataArr['twitter_link'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['twitter_link']));

      $formDataArr['skype_link'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['skype_link']));

      $formDataArr['instagram_link'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['instagram_link']));

      $formDataArr['telegram_link'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['telegram_link']));

      $formDataArr['linkdin_link'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['linkdin_link']));

      $formDataArr['copyright'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['copyright']));

      $formDataArr['address'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['address']));

      $formDataArr['feedback_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['feedback_status']));

      $formDataArr['maintenance_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['maintenance_status']));

      $formDataArr['site_caching'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['site_caching']));

      $formDataArr['description'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['description']));

      //Uploading company signature and fetching uploaded file name
      if ($_FILES["signature"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('signature', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['signature'] = $uploadReturnArr['fileName'];
          if ($_POST['hidden_signature'] !== "signature.jpg") {
            unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_signature']);
          }
        }
      } else {
        if (strlen($_POST['hidden_signature']) > 0) {
          $formDataArr['signature'] = $_POST['hidden_signature'];
        } else {
          $formDataArr['signature'] = 'signature.jpg';
        }
      }

      //Uploading company image and fetching uploaded file name
      if ($_FILES["logo"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('logo', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['logo'] = $uploadReturnArr['fileName'];
          if ($_POST['hidden_logo'] !== "company.png") {
            unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_logo']);
          }
        }
      } else {
        if (strlen($_POST['hidden_logo']) > 0) {
          $formDataArr['logo'] = $_POST['hidden_logo'];
        } else {
          $formDataArr['logo'] = 'company.png';
        }
      }

      //Uploading company header logo and fetching uploaded file name
      if ($_FILES["header_logo"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('header_logo', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['header_logo'] = $uploadReturnArr['fileName'];
          unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_header_logo']);
        }
      } else {
        if (!empty($_POST['hidden_header_logo'])) {
          $formDataArr['header_logo'] = $_POST['hidden_header_logo'];
        } else {
          $formDataArr['header_logo'] = null;
        }
      }

      //Uploading company sticky header logo and fetching uploaded file name
      if ($_FILES["sticky_logo"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('sticky_logo', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['sticky_logo'] = $uploadReturnArr['fileName'];
          unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_sticky_logo']);
        }
      } else {
        if (!empty($_POST['hidden_sticky_logo'])) {
          $formDataArr['sticky_logo'] = $_POST['hidden_sticky_logo'];
        } else {
          $formDataArr['sticky_logo'] = null;
        }
      }

      //Uploading company footer logo and fetching uploaded file name
      if ($_FILES["footer_logo"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('footer_logo', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['footer_logo'] = $uploadReturnArr['fileName'];
          unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_footer_logo']);
        }
      } else {
        if (!empty($_POST['hidden_footer_logo'])) {
          $formDataArr['footer_logo'] = $_POST['hidden_footer_logo'];
        } else {
          $formDataArr['footer_logo'] = null;
        }
      }

      //Uploading company footer logo and fetching uploaded file name
      if ($_FILES["favicon"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('favicon', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['favicon'] = $uploadReturnArr['fileName'];
          unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_favicon']);
        }
      } else {
        if (!empty($_POST['hidden_favicon'])) {
          $formDataArr['favicon'] = $_POST['hidden_favicon'];
        } else {
          $formDataArr['favicon'] = null;
        }
      }

      //print_r($formDataArr);exit; 
      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->update_Global_Site_Setting($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case "fetchStudentDetailInModal":
    $student_id = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['student_id']));

    $studentDetailArr = json_decode(json_encode($GlobalInterfaceControllerObj->fetch_Global_Single_Student($student_id)), true);

    $student_image_path = USER_UPLOAD_DIR . 'student/' . $studentDetailArr['image_file_name'];

    if (!strlen($studentDetailArr['image_file_name']) > 0 || !file_exists($student_image_path)) {
      $studentDetailArr['student_dp'] = "https://source.unsplash.com/600x300/?student";
    } else {
      $studentDetailArr['student_dp'] = USER_UPLOAD_URL . 'student/' . $studentDetailArr['image_file_name'];
    }

    $studentDetailArr['stu_dob'] = date('jS F, Y', strtotime($studentDetailArr['stu_dob']));

    $studentDetailArr['stu_result'] = ucfirst($studentDetailArr['stu_result']);

    $studentDetailArr['stu_gender'] = ucfirst($studentDetailArr['stu_gender']);
    $studentDetailArr['stu_marital_status'] = ucfirst($studentDetailArr['stu_marital_status']);

    if ($studentDetailArr['student_status'] != 'course_complete') {
      $studentDetailArr['student_status'] = ucfirst($studentDetailArr['student_status']);
    } else {
      $studentDetailArr['student_status'] = "Course Complete";
    }

    $studentDetailArr['course_default_fees'] = (int)0;
    $studentDetailArr['advance_fees_date'] = date('jS F, Y', strtotime($studentDetailArr['advance_fees_date']));

    echo json_encode(array("check" => "success", "studentDetail" => $studentDetailArr));

    break;

  case 'studentArchiveHandler':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    //$GlobalInterfaceControllerObj->restore_Student_From_Archive(5046);exit;

    $idData = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['row_id']));
    $type = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['type']));
    $user_role_slug = "update_student";

    if ($type == "current") {
      $action = "archived";
    } else {
      $action = "restored";
    }

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //checking if the feeded data is a string or not
      if (strpos($idData, ',') > 0) {
        $rowIdArr = explode(',', $idData);
      } else {
        $rowIdArr = array($idData);
      }

      if (count($rowIdArr) > 0) {

        $responseArr = array();

        //Call update global status modify method
        foreach ($rowIdArr as $index => $row_id) {
          if ($type == "current") {
            $responseArr[$index] = $GlobalInterfaceControllerObj->archive_Global_Student($row_id);
          } else {
            $responseArr[$index] = $GlobalInterfaceControllerObj->restore_Student_From_Archive($row_id);
          }
        }

        $returnArr = array("check" => "success", "response" => $responseArr);
      } else {
        $returnArr = array("check" => "failure", "message" => "You haven't selected any data!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'updateStudentProfile':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    $user_role_slug = "update_student_profile";

    //print_r($_POST);exit;

    $formDataArr['stu_row_id'] = $_SESSION['user_id'];

    if ($_SESSION['user_type'] == "student") {

      //Checking if we are creating a franchise or trying to modify one
      if ($formDataArr['stu_row_id'] == 'null') {
        $formDataArr['stu_id'] = $GlobalLibraryHandlerObj->create_Student_ID();
      }

      //determining franchise password
      $stu_pass = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_pass']));
      $confirm_pass = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_pass']));

      if (strlen($stu_pass) > 0 && ($stu_pass == $confirm_pass)) {
        $formDataArr['stu_pass'] = md5($stu_pass);
        $formDataArr['stu_og_pass'] = $stu_pass;
      } else {
        $formDataArr['stu_pass'] = $_POST['stu_hidden_password'];
        $formDataArr['stu_og_pass'] = $_POST['stu_hidden_og_password'];
      }

      //Storing form data into form data array
      $dir = 'student';
      $formDataArr['stu_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_name']));
      $formDataArr['stu_father_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_father_name']));

      $formDataArr['stu_phone'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_phone']));
      $formDataArr['stu_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_email']));

      $formDataArr['stu_gender'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_gender']));
      $formDataArr['stu_marital_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_marital_status']));
      $formDataArr['stu_address'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_address']));

      $formDataArr['stu_qualification'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_qualification']));

      $formDataArr['stu_dob'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_dob']));
      $formDataArr['stu_address'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_address']));
      $formDataArr['stu_description'] = $GlobalLibraryHandlerObj->encodeTextArea($_POST['stu_description']);

      $formDataArr['file_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['file_upload_type']));

      if ($formDataArr['file_upload_type'] == "local") {

        //Uploading franchise image and  fetching uploaded file name
        if ($_FILES["local_stu_image"]["size"] > 0) {
          $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('local_stu_image', $dir);
          //checking file upload return data
          if ($uploadReturnArr['check'] == 'success') {
            $formDataArr['image_file_name'] = $uploadReturnArr['fileName'];
          } else {
            echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload file!"));
            exit;
          }
        } else {
          if ($user_role_slug == "update_student") {
            $formDataArr['image_file_name'] = $_POST['hidden_stu_image'];
          } else {
            echo json_encode(array('check' => 'failure', 'msg' => "You need to upload an image for this student!"));
            exit;
          }
        }
      } else {
        $formDataArr['image_file_name'] =  mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_image_cdn']));
      }

      //print_r($formDataArr);exit; 
      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Student_Profile($formDataArr);

      if ($returnArr['check'] == 'success' && $user_role_slug == "update_student") {
        if ($formDataArr['file_upload_type'] == "cdn" || $_FILES["local_stu_image"]["size"] > 0) {
          //unlinking previous file from server 
          if ($user_role_slug == "update_student") {
            //unlinking uploaded file from server 
            unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_stu_image']);
          }
        }
      } else {
        if ($formDataArr['file_upload_type'] == "local") {
          //unlinking uploaded file from server 
          unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['image_file_name']);
        }
        $returnArr = array('check' => 'failure', 'message' => "Something went wrong!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;
}
