<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once(__DIR__ . "/../constants.php");

defined('ROOTPATH') or exit('No direct script access allowed');
//print_r($_POST);exit;

$action = $_POST['action'];
//Creating object for global controller
$GlobalInterfaceControllerObj = new GlobalInterfaceController();
//Creating object for global library
$GlobalLibraryHandlerObj = new GlobalLibraryHandler();

//Checking runtime folder existance 
$GlobalLibraryHandlerObj->checkRunTimeFolderExistance();

switch ($action) {

  case 'check_user_login':
    if ($_POST['action'] == 'check_user_login') {
      $paramArr['user_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_email']));
      $paramArr['user_pswd'] = md5(mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_pswd'])));
      $paramArr['user_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_type']));
      $paramArr['user_signin_method'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_signin_method']));

      $returnArr = $GlobalLibraryHandlerObj->checkUserLogin($paramArr);

      if ($returnArr['check'] == 'success') {
        //Setting cookies for browser
        if ($_POST['remember_me'] == 'on') {
          setcookie('user_email', $_POST['user_email'], time() + 86400 * 30);
          setcookie('user_pswd', $_POST['user_pswd'], time() + 86400 * 30);
        } else {
          setcookie('user_email', '', time() + 86400 * 30);
          setcookie('user_pswd', '', time() + 86400 * 30);
        }
      }
      echo json_encode($returnArr);
    }
    break;

  case 'manageGlobalFranchise':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $formDataArr['fran_row_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_row_id']));

    if ($formDataArr['fran_row_id'] != 'null') {
      $user_role_slug = 'update_franchise';
    } else {
      $user_role_slug = 'create_franchise';
    }

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //Checking if we are creating a franchise or trying to modify one
      if ($formDataArr['fran_row_id'] == 'null') {
        $formDataArr['fran_id'] = $GlobalLibraryHandlerObj->create_Frnachise_ID();
      }

      //determining franchise password
      $fran_pass = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_pass']));

      if (strlen($fran_pass) > 0) {
        $formDataArr['fran_pass'] = md5($fran_pass);
        $formDataArr['fran_og_pass'] = $fran_pass;
      } else {
        $formDataArr['fran_pass'] = $_POST['fran_hidden_password'];
        $formDataArr['fran_og_pass'] = $_POST['fran_hidden_og_password'];
      }

      //Storing form data into form data array
      $dir = 'franchise';
      $formDataArr['center_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['center_name']));

      //Configuring blog seo url structure 
      $formDataArr['seo_url_structure'] = $GlobalLibraryHandlerObj->seoUrlStructure($formDataArr['center_name'], 'seo');

      if ($formDataArr['fran_row_id'] != 'null') {

        $fran_slug_find_id = $GlobalLibraryHandlerObj->checkSlugAvailibility('franchise', 'seo_url_structure', $formDataArr['seo_url_structure'])->id;

        if (!empty($fran_slug_find_id) && $fran_slug_find_id != $formDataArr['fran_row_id']) {
          echo json_encode(array('check' => 'failure', 'message' => 'This title is already taken; Please try another.'));
          return false;
        }
      } else {
        if ($GlobalLibraryHandlerObj->checkSlugAvailibility('franchise', 'seo_url_structure', $formDataArr['seo_url_structure'])->id > 0) {
          echo json_encode(array('check' => 'failure', 'message' => 'This title is already taken; Please try another.'));
          return false;
        }
      }

      $formDataArr['owner_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['owner_name']));

      $formDataArr['fran_phone'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_phone']));
      $formDataArr['fran_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_email']));

      $formDataArr['fran_address'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_address']));
      $formDataArr['owned_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['owned_status']));
      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));
      $formDataArr['featured_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['featured_status']));

      $formDataArr['user_role'] = serialize($_POST['user_role']);

      $formDataArr['fran_description'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_description']));

      $formDataArr['image_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['image_upload_type']));
      $formDataArr['pdf_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['pdf_upload_type']));

      if ($_FILES["local_fran_image"]["size"] > 0) {
        $uploadImgReturnArr = $GlobalLibraryHandlerObj->upload_file('local_fran_image', $dir);
        //checking file upload return data
        if ($uploadImgReturnArr['check'] == 'success') {
          $formDataArr['fran_image'] = $uploadImgReturnArr['fileName'];
        } else {
          echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload franchise image!"));
          exit;
        }
      } else {
        if ($formDataArr['fran_row_id'] != 'null') {
          $formDataArr['fran_image'] = $_POST['hidden_fran_image'];
        } else {
          $formDataArr['fran_image'] = null;
        }
      }

      if ($_FILES["local_fran_pdf"]["size"] > 0) {
        $uploadPdfReturnArr = $GlobalLibraryHandlerObj->upload_file('local_fran_pdf', $dir);
        //checking file upload return data
        if ($uploadPdfReturnArr['check'] == 'success') {
          $formDataArr['fran_pdf_name'] = $uploadPdfReturnArr['fileName'];
        } else {
          echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload franchise pdf!"));
          exit;
        }
      } else {
        if ($formDataArr['fran_row_id'] != 'null') {
          $formDataArr['fran_pdf_name'] = $_POST['hidden_fran_pdf'];
        } else {
          $formDataArr['fran_pdf_name'] = null;
        }
      }

      //print_r($formDataArr);exit; 
      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Global_Franchise($formDataArr);

      if ($returnArr['check'] == 'success') {
        if ($returnArr['last_insert_id'] > 0) {
          //Purge franchise data cache
          $GlobalLibraryHandlerObj->purgeSiteCache("franchise");
          $returnArr = array('check' => 'success', 'message' => "Franchise has been successfully created!", "last_insert_id" => $returnArr['last_insert_id']);
        } else {
          //unlinking previous file from server 
          if ($formDataArr['fran_row_id'] != 'null') {
            if ($_FILES["local_fran_image"]["size"] > 0 && $uploadImgReturnArr['check'] == 'success') {
              //unlinking uploaded file from server 
              unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_fran_image']);
            }

            if ($_FILES["local_fran_pdf"]["size"] > 0 && $uploadPdfReturnArr['check'] == 'success') {
              //unlinking uploaded file from server 
              unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_fran_pdf']);
            }
          }
        }
      } else {
        if ($_FILES["local_fran_image"]["size"] > 0 && $uploadImgReturnArr['check'] == 'success') {
          //unlinking uploaded file from server 
          unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['fran_image']);
        }

        if ($_FILES["local_fran_pdf"]["size"] > 0 && $uploadPdfReturnArr['check'] == 'success') {
          unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['fran_pdf_name']);
        }
        $returnArr = array('check' => 'failure', 'message' => "Something went wrong!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageGlobalCourse':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $formDataArr['course_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_id']));

    if ($formDataArr['course_id'] > 0) {
      $user_role_slug = 'update_course';
    } else {
      $user_role_slug = 'create_course';
    }

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //Storing form data into form data array
      $dir = 'course';
      $formDataArr['course_title'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_title']));
      //Configuring blog seo url structure 
      $formDataArr['seo_url_structure'] = $GlobalLibraryHandlerObj->seoUrlStructure($formDataArr['course_title'], 'seo');

      if ($formDataArr['course_id'] > 0) {

        $course_slug_find_id = $GlobalLibraryHandlerObj->checkSlugAvailibility('course', 'seo_url_structure', $formDataArr['seo_url_structure'])->id;

        if (!empty($course_slug_find_id) && $course_slug_find_id != $formDataArr['course_id']) {
          echo json_encode(array('check' => 'failure', 'message' => 'This title is already taken; Please try another.'));
          return false;
        }
      } else {
        if ($GlobalLibraryHandlerObj->checkSlugAvailibility('course', 'seo_url_structure', $formDataArr['seo_url_structure'])->id > 0) {
          echo json_encode(array('check' => 'failure', 'message' => 'This title is already taken; Please try another.'));
          return false;
        }
      }

      $formDataArr['course_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_fees']));
      $formDataArr['course_duration'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_duration']));

      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));
      $formDataArr['featured_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['featured_status']));
      $formDataArr['course_description'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_description']));

      $formDataArr['image_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['image_upload_type']));
      $formDataArr['pdf_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['pdf_upload_type']));

      if ($_FILES["course_thumbnail_local"]["size"] > 0) {
        $uploadImgReturnArr = $GlobalLibraryHandlerObj->upload_file('course_thumbnail_local', $dir);
        //checking file upload return data
        if ($uploadImgReturnArr['check'] == 'success') {
          $formDataArr['course_thumbnail'] = $uploadImgReturnArr['fileName'];
        } else {
          echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload franchise image!"));
          exit;
        }
      } else {
        if ($formDataArr['course_id'] > 0) {
          $formDataArr['course_thumbnail'] = $_POST['hidden_course_thumbnail'];
        } else {
          $formDataArr['course_thumbnail'] = null;
        }
      }

      if ($_FILES["local_course_pdf"]["size"] > 0) {
        $uploadPdfReturnArr = $GlobalLibraryHandlerObj->upload_file('local_course_pdf', $dir);
        //checking file upload return data
        if ($uploadPdfReturnArr['check'] == 'success') {
          $formDataArr['course_pdf'] = $uploadPdfReturnArr['fileName'];
        } else {
          echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload franchise pdf!"));
          exit;
        }
      } else {
        if ($formDataArr['course_id'] > 0) {
          $formDataArr['course_pdf'] = $_POST['hidden_course_pdf'];
        } else {
          $formDataArr['course_pdf'] = null;
        }
      }

      //print_r($formDataArr);exit; 
      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Global_Course($formDataArr);

      if ($returnArr['check'] == "success") {

        //unlinking previous file from server 
        if ($formDataArr['course_id'] > 0) {
          if ($_FILES["course_thumbnail_local"]["size"] > 0 && $uploadImgReturnArr['check'] == 'success') {
            //unlinking uploaded file from server 
            unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_course_thumbnail']);
          }

          if ($_FILES["local_course_pdf"]["size"] > 0 && $uploadPdfReturnArr['check'] == 'success') {
            //unlinking uploaded file from server 
            unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_course_pdf']);
          }
        } else {
          //Purge course data cache
          $GlobalLibraryHandlerObj->purgeSiteCache("course");
        }
      } else {
        if ($_FILES["course_thumbnail_local"]["size"] > 0 && $uploadImgReturnArr['check'] == 'success') {
          //unlinking uploaded file from server 
          unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['course_thumbnail']);
        }

        if ($_FILES["local_course_pdf"]["size"] > 0 && $uploadPdfReturnArr['check'] == 'success') {
          unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['course_pdf']);
        }
        $returnArr = array('check' => 'failure', 'message' => "Something went wrong!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'fetchStudentBatch':

    //Declaring necessary variables
    $paramArr = array();
    $returnArr = array();

    //print_r($_POST);exit;
    $paramArr['franchise_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['franchise_id']));
    $paramArr['course_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_id']));

    $batchList = $GlobalInterfaceControllerObj->fetch_Dependent_Batch_Arr($paramArr);

    if (count($batchList) > 0) {
      echo json_encode(array('check' => 'success', 'batchList' => $batchList));
    } else {
      echo json_encode(array('check' => 'failure', 'batchList' => array()));
    }

    break;

  case 'manageGlobalStudent':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    //echo json_encode(array('check'=>'success','stu_id'=>'6143'));exit;

    $action_type = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['action_type']));
    $formDataArr['stu_row_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_row_id']));

    if (!empty($formDataArr['stu_row_id']) && $formDataArr['stu_row_id'] != "null") {
      $user_role_slug = 'update_student';
    } else {
      $user_role_slug = 'create_student';
    }

    if (!empty($formDataArr['stu_row_id']) && $formDataArr['stu_row_id'] != "null") {
      $studentDetailArr =  $GlobalInterfaceControllerObj->fetch_Detail_Single_Student($formDataArr['stu_row_id']);
    }

    //Fetching student detail to check if this is a valid action
    if ($_SESSION['user_type'] == "franchise") {

      $franchise_id = $_SESSION['user_id'];

      if (!empty($formDataArr['stu_row_id']) && $formDataArr['stu_row_id'] != "null") {
        //Checking if this student belongs to this franchise
        if ($studentDetailArr->franchise_id != $_SESSION['user_id']) {
          echo json_encode(array('check' => 'failure', 'message' => "You don't have the permission to perform this action!"));
          exit;
        }
      }

      $franchiseDetailArr = $GlobalInterfaceControllerObj->fetch_Global_Single_Franchise($franchise_id);
      $owned_status = $franchiseDetailArr->owned_status;
    } else {
      $owned_status = "yes";
    }

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //Storing form data into form data array
      $dir = 'student';
      $formDataArr['stu_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_name']));
      $formDataArr['stu_father_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_father_name']));

      $formDataArr['stu_phone'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_phone']));
      $formDataArr['stu_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_email']));

      $formDataArr['stu_gender'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_gender']));
      $formDataArr['stu_marital_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_marital_status']));
      $formDataArr['stu_address'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_address']));

      $formDataArr['course_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_id']));

      $formDataArr['stu_qualification'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_qualification']));

      if ($_SESSION['user_type'] == 'franchise') {
        $formDataArr['franchise_id'] = $_SESSION['user_id'];

        if ($owned_status == "no") {

          if ($user_role_slug == "update_student") {
            $formDataArr['student_status'] = $studentDetailArr->student_status;
            $formDataArr['record_status'] = $studentDetailArr->record_status;
            $formDataArr['stu_result'] = $studentDetailArr->stu_result;
            $formDataArr['conversion_status'] = $studentDetailArr->conversion_status;
          } else {
            $formDataArr['student_status'] = "admitted";
            $formDataArr['record_status'] = "blocked";
            $formDataArr['stu_result'] = "unqualified";
            $formDataArr['conversion_status'] = 0;
            $formDataArr['stu_id'] = $GlobalLibraryHandlerObj->create_Student_ID();
          }
          $formDataArr['stu_course_fees'] = null;
          $formDataArr['stu_course_fees'] = null;
          $formDataArr['monthly_course_fees'] = null;
          $formDataArr['month_exclude_receipt'] = null;
          $formDataArr['fees_paid_before_dr'] = null;
        } else {
          if (!empty($_POST['student_status'])) {
            $formDataArr['student_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['student_status']));
            $formDataArr['conversion_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['conversion_status']));
          } else {
            if ($user_role_slug == "update_student") {
              $formDataArr['student_status'] = $studentDetailArr->student_status;
              $formDataArr['conversion_status'] = $studentDetailArr->conversion_status;
            } else {
              $formDataArr['student_status'] = "admitted";
            }
          }

          if (!empty($_POST['stu_result'])) {
            $formDataArr['stu_result'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_result']));
          } else {
            if ($user_role_slug == "update_student") {
              $formDataArr['stu_result'] = $studentDetailArr->stu_result;
            } else {
              $formDataArr['stu_result'] = "unqualified";
            }
          }

          if (!empty($_POST['record_status'])) {
            $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));
          } else {
            if ($user_role_slug == "update_student") {
              $formDataArr['record_status'] = $studentDetailArr->record_status;
            } else {
              $formDataArr['record_status'] = "active";
            }
          }

          //Checking if we are creating a franchise or trying to modify one
          if ($formDataArr['stu_row_id'] == 'null') {
            $formDataArr['stu_id'] = $GlobalLibraryHandlerObj->create_Student_ID();
          }
          $formDataArr['stu_course_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_course_fees']));
          $formDataArr['monthly_course_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['monthly_course_fees']));
          $formDataArr['month_exclude_receipt'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['month_exclude_receipt']));
          $formDataArr['stu_course_discount'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_course_discount']));
          $formDataArr['fees_paid_before_dr'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fees_paid_before_dr']));

          if (!empty($formDataArr['stu_row_id']) && $formDataArr['stu_row_id'] != "null") {
            if ($formDataArr['stu_course_discount'] != $studentDetailArr->stu_course_discount || $formDataArr['fees_paid_before_dr'] != $studentDetailArr->fees_paid_before_dr) {
              $formDataArr['verified_status'] = '0';
            } else {
              $formDataArr['verified_status'] = $studentDetailArr->verified_status;
            }
          }
        }
      } else {

        $formDataArr['franchise_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['franchise_id']));

        if (!empty($_POST['student_status'])) {
          $formDataArr['student_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['student_status']));
        } else {
          if ($user_role_slug == "update_student") {
            $formDataArr['student_status'] = $studentDetailArr->student_status;
          } else {
            $formDataArr['student_status'] = "admitted";
          }
        }

        if (!empty($_POST['stu_result'])) {
          $formDataArr['stu_result'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_result']));
        } else {
          if ($user_role_slug == "update_student") {
            $formDataArr['stu_result'] = $studentDetailArr->stu_result;
          } else {
            $formDataArr['stu_result'] = "unqualified";
          }
        }

        if (!empty($_POST['record_status'])) {
          $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));
        } else {
          if ($user_role_slug == "update_student") {
            $formDataArr['record_status'] = $studentDetailArr->record_status;
          } else {
            $formDataArr['record_status'] = "active";
          }
        }

        if (!empty($_POST['conversion_status'])) {
          $formDataArr['conversion_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['conversion_status']));
        } else {
          if ($user_role_slug == "update_student") {
            $formDataArr['conversion_status'] = $studentDetailArr->conversion_status;
          }
        }

        //Checking if we are creating a franchise or trying to modify one
        if ($formDataArr['stu_row_id'] == 'null') {
          $formDataArr['stu_id'] = $GlobalLibraryHandlerObj->create_Student_ID();
        }
        $formDataArr['stu_course_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_course_fees']));
        $formDataArr['monthly_course_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['monthly_course_fees']));
        $formDataArr['month_exclude_receipt'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['month_exclude_receipt']));
        $formDataArr['stu_course_discount'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_course_discount']));
        $formDataArr['fees_paid_before_dr'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fees_paid_before_dr']));

        if (!empty($formDataArr['stu_row_id']) && $formDataArr['stu_row_id'] != "null") {
          $formDataArr['verified_status'] = '1';
        }
      }

      $stu_dob = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_dob']));
      $stu_dob = str_replace('/', '-', $stu_dob);
      $formDataArr['stu_dob'] = date('Y-m-d', strtotime($stu_dob));

      $formDataArr['stu_address'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_address']));

      $formDataArr['stu_notes'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_notes']));

      //Uploading student image and fetching uploaded file name 
      if ($_FILES["local_stu_image"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('local_stu_image', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['image_file_name'] = $uploadReturnArr['fileName'];
        } else {
          echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload student image!"));
          exit;
        }
      } else {
        if ($user_role_slug == "update_student" || $action_type == "clone") {
          $formDataArr['image_file_name'] = $_POST['hidden_stu_image'];
        } else {
          $formDataArr['image_file_name'] = null;
        }
      }

      //print_r($formDataArr);exit; 
      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Global_Student($formDataArr);

      if ($returnArr['check'] == 'success') {

        //unlinking previous file from server 
        if ($user_role_slug == "update_student" && $uploadReturnArr['check'] == 'success') {
          if ($_FILES["local_stu_image"]["size"] > 0) {
            //unlinking uploaded file from server 
            unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_stu_image']);
          }
        }

        if ($user_role_slug == "update_student") {
          $returnArr['stu_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_id']));
        } else {
          $returnArr['stu_id'] = $formDataArr['stu_id'];
          //Purge student data cache
          $GlobalLibraryHandlerObj->purgeSiteCache("student");
        }
        $returnArr['course'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_name']));
      } else {
        if ($_FILES["local_stu_image"]["size"] > 0 && $uploadReturnArr['check'] == 'success') {
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

  case 'manageStudentAdmission':

    //Declaring necessary variables
    $formDataArr = array();
    $studentReturnArr = array();
    $receiptReturnArr = array();

    //print_r($_POST);exit;

    //echo json_encode(array('check'=>'success',"last_insert_id"=>6168));exit;

    $formDataArr['student_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['student_id']));

    if (!empty($formDataArr['student_id']) && $formDataArr['student_id'] != "null") {
      $user_role_slug = 'update_student';
    } else {
      $user_role_slug = 'create_student';
    }

    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      if ($user_role_slug == "update_student") {
        //Fetching current student
        $studentDetailArr =  $GlobalInterfaceControllerObj->fetch_Detail_Single_Student($formDataArr['student_id']);
      }

      if ($_SESSION['user_type'] == "franchise" && $_SESSION['owned_status'] == "yes") {

        $franchise_id = $_SESSION['user_id'];

        $formDataArr['stu_course_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_course_fees']));
        $formDataArr['monthly_course_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['monthly_course_fees']));
        $formDataArr['stu_course_discount'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_course_discount']));
        $formDataArr['fees_paid_before_dr'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fees_paid_before_dr']));

        if (!empty($formDataArr['student_id']) && $formDataArr['student_id'] != "null") {
          //Checking if this student belongs to this franchise
          if ($studentDetailArr->franchise_id != $_SESSION['user_id']) {
            echo json_encode(array('check' => 'failure', 'message' => "You don't have the permission to perform this action!"));
            exit;
          }

          if ($formDataArr['stu_course_discount'] != $studentDetailArr->stu_course_discount) {
            $formDataArr['verified_status'] = '0';
          } else {
            $formDataArr['verified_status'] = $studentDetailArr->verified_status;
          }
        }

        $franchiseDetailArr = $GlobalInterfaceControllerObj->fetch_Global_Single_Franchise($franchise_id);
        $owned_status = $franchiseDetailArr->owned_status;
      } else {
        $franchise_id = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['franchise_id']));
        $formDataArr['stu_course_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_course_fees']));
        $formDataArr['monthly_course_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['monthly_course_fees']));
        $formDataArr['stu_course_discount'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_course_discount']));
        $formDataArr['fees_paid_before_dr'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fees_paid_before_dr']));

        if (!empty($formDataArr['student_id']) && $formDataArr['student_id'] != "null") {
          $formDataArr['verified_status'] = '1';
        }
      }

      $formDataArr['stu_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_name']));
      $formDataArr['stu_father_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_father_name']));
      $formDataArr['stu_phone'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_phone']));

      //Checking if we are creating a franchise or trying to modify one
      if ($user_role_slug == 'create_student') {
        $formDataArr['stu_id'] = $GlobalLibraryHandlerObj->create_Student_ID();
      }

      if (!empty($_POST['tmp_stu_record_id'])) {
        $formDataArr['tmp_stu_record_id'] = $_POST['tmp_stu_record_id'];
      } else {
        $formDataArr['tmp_stu_record_id'] = null;
      }

      $formDataArr['course_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_id']));
      $formDataArr['franchise_id'] = $franchise_id;
      $formDataArr['student_status'] = "admitted";
      $formDataArr['record_status'] = "active";

      $receipt_amount = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_amount']));

      //print_r($formDataArr);exit;

      //Call manage student admission method
      $studentReturnArr = $GlobalInterfaceControllerObj->manage_Student_Admission($formDataArr);

      if ($studentReturnArr['check'] == 'success' && $studentReturnArr['last_insert_id'] > 0 && $user_role_slug == "create_student" && $_POST['receipt_amount'] > 0) {

        $receipt_role_slug = 'create_receipt';

        $receiptFormArr['receipt_id'] = $GlobalLibraryHandlerObj->create_Receipt_ID();
        $receiptFormArr['stu_id'] = $formDataArr['stu_id'];
        $receiptFormArr['category_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['category_id']));

        $receiptFormArr['receipt_amount'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_amount']));
        $receiptFormArr['extra_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['extra_fees']));
        $receiptFormArr['extra_fees_description'] = "Registration Fees";
        $receiptFormArr['record_status'] = 'active';

        //check action permission        
        $checkReceiptPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($receipt_role_slug, "hard");

        //Call manage receipt hotel method
        $receiptReturnArr = $GlobalInterfaceControllerObj->create_Student_Admission_Receipt($receiptFormArr);
      }

      if ($studentReturnArr['check'] == 'success' && !empty($_POST['tmp_id'])) {
        //Updating temporary student conversion status
        $tmp_id = $_POST['tmp_id'];
        $GlobalInterfaceControllerObj->update_Tmp_Student_Conversion_Status($tmp_id, '1');
      }

      if ($studentReturnArr['check'] == 'success') {
        $returnArr = $studentReturnArr;

        if ($user_role_slug == "update_student") {
          $returnArr['stu_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_id']));
        } else {
          $returnArr['stu_id'] = $formDataArr['stu_id'];
        }
        $returnArr['course'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_name']));
      } else {
        $returnArr = array("check" => "failure", "message" => "Something went wrong!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageTempStudents':

    //Declaring necessary variables
    $formDataArr = array();
    $studentReturnArr = array();

    //print_r($_POST);exit;

    //echo json_encode(array('check'=>'success',"last_insert_id"=>6168));exit;

    $formDataArr['tmp_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['tmp_id']));

    if (!empty($formDataArr['tmp_id']) && $formDataArr['tmp_id'] != "null") {
      $user_role_slug = 'update_student';
    } else {
      $user_role_slug = 'create_student';
    }

    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      if ($_SESSION['user_type'] == "franchise") {

        $franchise_id = $_SESSION['user_id'];

        if (!empty($formDataArr['tmp_id']) && $formDataArr['tmp_id'] != "null") {
          //Checking if this student belongs to this franchise
          $studentDetailArr =  $GlobalInterfaceControllerObj->fetch_Detail_Single_Student($formDataArr['tmp_id']);
          if ($studentDetailArr->franchise_id != $_SESSION['user_id']) {
            echo json_encode(array('check' => 'failure', 'message' => "You don't have the permission to perform this action!"));
            exit;
          }
        }

        $franchiseDetailArr = $GlobalInterfaceControllerObj->fetch_Global_Single_Franchise($franchise_id);
        $owned_status = $franchiseDetailArr->owned_status;
      } else {
        $franchise_id = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['franchise_id']));
      }

      $formDataArr['stu_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_name']));
      $formDataArr['stu_father_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_father_name']));
      $formDataArr['stu_phone'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_phone']));

      //Checking if we are creating a franchise or trying to modify one
      if ($formDataArr['tmp_id'] == 'null') {
        $formDataArr['tmp_stu_id'] = $GlobalLibraryHandlerObj->create_Tmp_Student_ID();
      }

      $formDataArr['course_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_id']));
      $formDataArr['franchise_id'] = $franchise_id;

      $formDataArr['advanced_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_amount']));

      //print_r($formDataArr);exit;

      //Call manage student admission method
      $returnArr = $GlobalInterfaceControllerObj->manage_Temp_Student($formDataArr);

      if ($returnArr['check'] == 'success') {

        if ($user_role_slug == "update_student") {
          $returnArr['tmp_stu_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['tmp_stu_id']));
        } else {
          $returnArr['tmp_stu_id'] = $formDataArr['tmp_stu_id'];
        }
        $returnArr['course'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_name']));
      } else {
        $returnArr = array("check" => "failure", "message" => "Something went wrong!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageAdmissionReceipt':

    $formDataArr = array();
    $returnArr = array();

    $user_role_slug = "update_receipt";

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $formDataArr['receipt_row_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_row_id']));
      $formDataArr['receipt_amount'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_amount']));

      //Call manage receipt hotel method
      $returnArr = $GlobalInterfaceControllerObj->update_Student_Admission_Receipt($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageStudentReceipt':

    //print_r($_POST);exit;

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    $dir = 'receipt';

    //Storing form data into formdata array
    //Checking if we are creating a receipt or trying to modify one
    if (empty($_POST['receipt_row_id'])) {
      $user_role_slug = 'create_receipt';
      $formDataArr['receipt_id'] = $GlobalLibraryHandlerObj->create_Receipt_ID();
      $formDataArr['receipt_row_id'] = null;
    } else {
      $user_role_slug = 'update_receipt';
      $formDataArr['receipt_row_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_row_id']));
      $formDataArr['receipt_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_id']));
    }

    $formDataArr['category_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['category_id']));

    $send_mail = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['send_mail']));

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //Fetch receipt details
      $receiptDetailArr =  $GlobalInterfaceControllerObj->fetch_Receipt_Detail($formDataArr['receipt_row_id']);

      $formDataArr['student_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_id']));

      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));
      $formDataArr['receipt_amount'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_amount']));
      $formDataArr['late_fine'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['late_fine']));
      $formDataArr['extra_fees'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['extra_fees']));

      //Fetch student course fees details
      $stuReceiptDetails = $GlobalInterfaceControllerObj->fetch_Global_Single_Student($formDataArr['student_id']);

      if (!empty($_POST['receipt_row_id'])) {

        if (!empty($stuReceiptDetails->stu_course_fees)) {
          $course_due_fees = (int)$stuReceiptDetails->stu_course_fees - (int)$stuReceiptDetails->stu_course_discount - (int)$stuReceiptDetails->course_fees_paid - (int)$stuReceiptDetails->advanced_fees - (int)$stuReceiptDetails->fees_paid_before_dr + (int)$receiptDetailArr->receipt_amount;
        } else {
          $course_due_fees = (int)$stuReceiptDetails->course_default_fees - (int)$stuReceiptDetails->stu_course_discount - (int)$stuReceiptDetails->course_fees_paid - (int)$stuReceiptDetails->advanced_fees - (int)$stuReceiptDetails->fees_paid_before_dr + (int)$receiptDetailArr->receipt_amount;
        }
      } else {

        if (!empty($stuReceiptDetails->stu_course_fees)) {
          $course_due_fees = (int)$stuReceiptDetails->stu_course_fees - (int)$stuReceiptDetails->stu_course_discount - (int)$stuReceiptDetails->course_fees_paid - (int)$stuReceiptDetails->advanced_fees - (int)$stuReceiptDetails->fees_paid_before_dr;
        } else {
          $course_due_fees = (int)$stuReceiptDetails->course_default_fees - (int)$stuReceiptDetails->stu_course_discount - (int)$stuReceiptDetails->course_fees_paid - (int)$stuReceiptDetails->advanced_fees - (int)$stuReceiptDetails->fees_paid_before_dr;
        }
      }

      if ($formDataArr['receipt_amount'] > $course_due_fees) {
        echo json_encode(array('check' => 'failure', 'message' => 'Receipt amount is greater than due course fees!'));
        exit;
      } elseif ($course_due_fees == 0 && $formDataArr["category_id"] != 109501) {
        echo json_encode(array('check' => 'failure', 'message' => 'This student has cleared their fees!'));
        exit;
      }

      //Fetching student detail to check if this is a valid action
      if ($_SESSION['user_type'] == "franchise") {
        $studentDetailArr =  $GlobalInterfaceControllerObj->fetch_Detail_Single_Student($formDataArr['student_id']);

        if ($studentDetailArr->franchise_id != $_SESSION['user_id']) {
          echo json_encode(array('check' => 'failure', 'message' => "You don't have the permission to perform this action!"));
          exit;
        }
      }

      if (!empty($formDataArr['extra_fees'])) {
        $formDataArr['extra_fees_description'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['extra_fees_description']));
      } else {
        $formDataArr['extra_fees_description'] = null;
      }

      if (!empty($formDataArr['receipt_row_id'])) {

        $receipt_edit_desc = array();

        $current_receipt_amount = $receiptDetailArr->receipt_amount;
        $current_late_fine = $receiptDetailArr->late_fine;
        $current_extra_fees = $receiptDetailArr->extra_fees;

        $updated_receipt_amount = !empty($formDataArr['receipt_amount']) ? $formDataArr['receipt_amount'] : 0;
        $updated_late_fine = !empty($formDataArr['late_fine']) ? $formDataArr['late_fine'] : 0;
        $updated_extra_fees = !empty($formDataArr['extra_fees']) ? $formDataArr['extra_fees'] : 0;

        //echo $updated_receipt_amount."***".$updated_late_fine."***".$updated_extra_fees;exit;


        if ($updated_receipt_amount < $current_receipt_amount) {
          $receipt_edit_desc[0] = "Receipt amount reduced from Rs. " . $current_receipt_amount . " to Rs. " . $updated_receipt_amount . ".";
        }

        if ($updated_late_fine < $current_late_fine) {
          $receipt_edit_desc[1] = "Late fine reduced from Rs. " . $current_late_fine . " to Rs. " . $updated_late_fine . ".";
        }

        if ($updated_extra_fees < $current_extra_fees) {
          $receipt_edit_desc[2] = "Additional fine reduced from Rs. " . $current_extra_fees . " to Rs. " . $updated_extra_fees . ".";
        }

        if (!empty($receipt_edit_desc) || $receiptDetailArr->verified_status == '0') {
          $formDataArr['verified_status'] = '0';
        } else {
          $formDataArr['verified_status'] = '1';
        }

        if (!empty($receipt_edit_desc)) {
          $formDataArr['edit_description'] = serialize(array_values($receipt_edit_desc));
        } else {
          if ($receiptDetailArr->verified_status == '1') {
            $formDataArr['edit_description'] = serialize(array_values($receipt_edit_desc));
          } else {
            $formDataArr['edit_description'] = $receiptDetailArr->edit_description;
          }
        }
      }

      //print_r($formDataArr);exit;

      //Call manage receipt hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Student_Receipt($formDataArr);

      if ($returnArr['check'] == 'success') {

        if (!empty($_POST['receipt_row_id'])) {
          $receipt_id = $formDataArr['receipt_row_id'];
        } else {
          $receipt_id = $returnArr['last_insert_id'];
        }

        //Receipt details
        $receiptDetails = $GlobalInterfaceControllerObj->fetch_Single_Receipt_Data($receipt_id);

        if ($send_mail == "yes") {

          //Create student receipt pdf
          $receiptPdfRslt = $GlobalLibraryHandlerObj->createStudentReceiptPdf($receipt_id);

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

          $emailParamArr['receipt_id'] = $studentReceiptData->receipt_id;
          $emailParamArr['receipt_season'] = $receipt_season;
          $emailParamArr['receipt_status'] = ucfirst($studentReceiptData->receipt_status);
          $emailParamArr['receipt_amount'] = '<i class="fa fa-inr" aria-hidden="true"></i> ' . $studentReceiptData->receipt_amount;

          if ($formDataArr['receipt_pdf'] !== null) {
            $emailParamArr['attachment_path'] = USER_UPLOAD_DIR . $dir . '/' . $formDataArr['receipt_pdf'];
          } else {
            $emailParamArr['attachment_path'] = null;
          }

          $emailParamArr['email_code'] = 'student-monthly-receipt-invoice';
          //print_r($emailParamArr);exit;
          $sendMailResult = $GlobalLibraryHandlerObj->php_mailer_send_mail($emailParamArr);

          if ($sendMailResult) {
            if (!empty($_POST['receipt_row_id'])) {
              $returnArr = array('check' => 'success', 'file_url' => $receiptPdfRslt['file_url'], 'receipt_id' => $receiptDetails->receipt_id);
            } else {
              $returnArr = array('check' => 'success', 'file_url' => $receiptPdfRslt['file_url'], 'last_insert_id' => $receipt_id, 'receipt_id' => $receiptDetails->receipt_id);
            }
          }
        } else {
          if (!empty($_POST['receipt_row_id'])) {
            $returnArr = array('check' => 'success', 'file_url' => $receiptPdfRslt['file_url'], 'receipt_id' => $receiptDetails->receipt_id);
          } else {
            //Purge student data cache
            $GlobalLibraryHandlerObj->purgeSiteCache("student_receipts");
            $returnArr = array('check' => 'success', 'file_url' => $receiptPdfRslt['file_url'], 'last_insert_id' => $receipt_id, 'receipt_id' => $receiptDetails->receipt_id);
          }
        }
      } else {
        $returnArr = array('check' => 'failure', 'message' => "Something went wrong!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageProfileData':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = "manage_profile";
    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //Storing form data into formdata array
      $formDataArr['user_nicename'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_nicename']));
      $formDataArr['user_contact'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_contact']));
      $formDataArr['user_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_email']));

      if (isset($_POST['user_status'])) {
        $formDataArr['user_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_status']));
      } else {
        $formDataArr['user_status'] = 'active';
      }

      if (strlen($_POST['user_pass']) > 0) {
        $formDataArr['user_pass'] = md5(mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_pass'])));
      } else {
        $formDataArr['user_pass'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_hidden_password']));
      }

      $formDataArr['user_role'] =  serialize($_POST['user_role']);

      if ($_POST['page_route'] == 'edit_admin_profile') {
        $formDataArr['user_type'] = 'admin';
      } else {
        $formDataArr['user_type'] = $_SESSION['user_type'];
      }

      //print_r($formDataArr);exit;
      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Profile_Data($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    $post_id = $formDataArr['user_type'];
    $action_text = "updated";

    if ($_SESSION['user_type'] == $formDataArr["user_type"]) {
      $activity_text = " their profile data";
    } else {
      $activity_text = " profile data of, " . $formDataArr["user_type"];
    }

    if ($returnArr["check"] == "success") {
      $user_action = "has successfully " . $action_text . $activity_text;
      $attempt_status = "successful";
    } else {
      $user_action = "has attempted to " . rtrim($action_text, 'd') . $activity_text;
      $attempt_status = "restricted";
    }

    echo json_encode($returnArr);

    break;

  case 'fetchReceiptTotal':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;
    $user_role_slug = "view_receipt";

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));

      $formDataArr['course_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_id']));
      $formDataArr['franchise_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['franchise_id']));

      if (!empty($_POST['created'])) {
        $created_at = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['created']));
        $created_at = str_replace('/', '-', $created_at);
        $formDataArr['created'] = date('Y-m-d', strtotime($created_at));
      }


      if (!empty($_POST['receipt_season_start'])) {
        $receipt_season_start = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_season_start']));
        $receipt_season_start = str_replace('/', '-', $receipt_season_start);
        $formDataArr['receipt_season_start'] = date('Y-m-d', strtotime($receipt_season_start));
      }

      if (!empty($_POST['receipt_season_end'])) {
        $receipt_season_end = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_season_end']));
        $receipt_season_end = str_replace('/', '-', $receipt_season_end);
        $formDataArr['receipt_season_end'] = date('Y-m-d', strtotime($receipt_season_end));
      }

      $formDataArr['stu_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['student_id']));

      //print_r($formDataArr);exit;

      //fetching receipt data
      $receiptDataArr = json_decode(json_encode($GlobalInterfaceControllerObj->fetch_Receipt_Collection($formDataArr)), true);
      $returnArr = array('check' => 'success', 'receiptData' => $receiptDataArr, 'message' => "Receipt Collection was successfully fetched!");
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;


  case 'manageGlobalExam':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    //Storing form data into formdata array
    $formDataArr['exam_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['exam_id']));

    if ($formDataArr['exam_id'] > 0) {
      $user_role_slug = 'update_exam';
    } else {
      $user_role_slug = 'create_exam';
    }

    $dir = 'exam';

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $formDataArr['name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['name']));
      $formDataArr['franchise_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['franchise_id']));
      $formDataArr['course_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['course_id']));
      $formDataArr['total_marks'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['total_marks']));
      $formDataArr['hours'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hours']));
      $formDataArr['minutes'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['minutes']));
      $formDataArr['instructions'] = mysqli_real_escape_string(DB::$WRITELINK, trim(($_POST['instructions'])));
      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));

      $exam_date = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['exam_date']));
      $exam_date = str_replace('/', '-', $exam_date);
      $formDataArr['exam_date'] = date('Y-m-d', strtotime($exam_date));

      //Uploading student image and fetching uploaded file name 
      if ($_FILES["local_exam_pdf"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('local_exam_pdf', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['optional_pdf'] = $uploadReturnArr['fileName'];
        } else {
          echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload news pdf!"));
          exit;
        }
      } else {
        if ($formDataArr['exam_id'] > 0) {
          $formDataArr['optional_pdf'] = $_POST['hidden_optional_pdf'];
        } else {
          $formDataArr['optional_pdf'] = null;
        }
      }

      //print_r($formDataArr);exit; 
      //Call manage email template method
      $returnArr = $GlobalInterfaceControllerObj->manage_Global_Exam($formDataArr);

      if ($returnArr['check'] == 'success') {

        //unlinking previous file from server 
        if ($formDataArr['exam_id'] > 0) {
          if ($_FILES["local_exam_pdf"]["size"] > 0 && $uploadReturnArr['check'] == 'success') {
            //unlinking uploaded file from server 
            unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_optional_pdf']);
          }
        }
      } else {
        if ($_FILES["local_exam_pdf"]["size"] > 0 && $uploadReturnArr['check'] == 'success') {
          //unlinking uploaded file from server 
          unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['optional_pdf']);
        }
        $returnArr = array('check' => 'failure', 'message' => "Something went wrong!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'fetchAllQuestions':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = 'update_exam';

    $exam_id = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['exam_id']));

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {
      //Call to save exam questions ordering
      $questions = $GlobalInterfaceControllerObj->fetch_Exam_Questions($exam_id);

      $returnArr = array('check' => 'success', 'questions' => $questions);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageExamQuestions':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;
    $postData = $_POST;

    $user_role_slug = 'update_exam';

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $question_count = count($_POST['questions']);

      //Call save exam questions
      $returnArr = $GlobalInterfaceControllerObj->update_Exam_Questions($postData);

      echo json_encode($returnArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    break;


  case 'sortExamQuestions':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();
    $currentQuestionArr = array();

    //print_r($_POST);exit;

    $formDataArr['exam_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['exam_id']));
    $questions = $_POST['questions'];

    $user_role_slug = 'update_exam';

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {
      //Formatting current qustions ordering
      $current_questions = $GlobalInterfaceControllerObj->fetch_Exam_Questions($formDataArr['exam_id']);

      foreach ($current_questions as $cindex => $question) {
        $crntOrdrin = (int)$question->ordering - 1;
        $currentQuestionArr[$crntOrdrin] = $question->id;
      }

      $diffQuestionSortArr = array_diff_assoc($questions, $currentQuestionArr);

      foreach ($diffQuestionSortArr as $index => $id) {
        $formDataArr['question_id'] = $id;
        $formDataArr['ordering'] = $index + 1;

        //Call to save exam questions ordering
        $returnArr = $GlobalInterfaceControllerObj->save_Exam_Questions_Order($formDataArr);
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'deleteAllQuestions':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = 'update_exam';

    $exam_id = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['exam_id']));

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {
      //Call to save exam questions ordering
      $returnArr = $GlobalInterfaceControllerObj->delete_All_Questions($exam_id);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'setExamValidationLog':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = 'update_exam';

    //check action permission        

    if ($_SESSION['user_type'] == "student") {

      $formDataArr['exam_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['exam_id']));

      //Call save exam questions
      $returnArr = $GlobalInterfaceControllerObj->update_Exam_Validation_Log($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;


  case 'manageExamAnswer':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;
    $postData = $_POST;

    if ($_SESSION['user_type'] == "student") {

      //Call save exam questions
      $returnArr = $GlobalInterfaceControllerObj->update_Exam_Answer($postData);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'flagQuestionForReview':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = 'update_exam';

    //check action permission        

    if ($_SESSION['user_type'] == "student") {

      $formDataArr['exam_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['exam_id']));
      $formDataArr['ques_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['qId']));

      //Call save exam questions
      $returnArr = $GlobalInterfaceControllerObj->update_Flag_Question_Exam($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'recordViewdQuestions':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = 'update_exam';

    //check action permission        

    if ($_SESSION['user_type'] == "student") {

      $formDataArr['exam_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['exam_id']));
      $formDataArr['ques_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['qId']));

      //Call save exam questions
      $returnArr = $GlobalInterfaceControllerObj->update_Viewed_Question_Exam($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'changeStudentStatus':

    //print_r($_POST);exit;

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    $status_type = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['status_type']));

    if ($status_type == "status") {
      $user_role_slug = 'update_student';
    } else {
      $user_role_slug = 'update_result';
    }

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {
      //Storing form data into formdata array
      $formDataArr['student_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['stu_id']));
      $formDataArr['status_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['status_type']));

      if ($formDataArr['status_type'] == "status") {
        $formDataArr['student_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['status_data']));
      } else {
        $formDataArr['stu_result'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['status_data']));
      }

      //print_r($formDataArr);exit;

      //Call change student status method
      $returnArr = $GlobalInterfaceControllerObj->manage_Student_Status($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageGalleryItem':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $dir = 'gallery';

    //Storing form basic data into formdata array
    $formDataArr['media_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['media_id']));

    if ($formDataArr['media_id'] > 0) {
      $user_role_slug = 'update_gallery';
    } else {
      $user_role_slug = 'create_gallery';
    }

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $formDataArr['title'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['title']));

      $formDataArr['content_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['content_type']));

      if ($formDataArr['content_type'] == 'image') {
        $formDataArr['file_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['file_upload_type']));

        if ($formDataArr['file_upload_type'] == "local") {

          //Uploading featured image & fetching image path details
          if ($_FILES["local_media_image"]["size"] > 0) {
            $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('local_media_image', $dir);
            if ($uploadReturnArr['check'] == 'success') {
              $formDataArr['content'] = $uploadReturnArr['fileName'];
              //unlink(USER_UPLOAD_DIR.$dir.'/'.$_POST['hidden_media_content']);
            } else {
              echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload file!"));
              exit;
            }
          } else {
            $formDataArr['content_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hidden_content_type']));
            $formDataArr['file_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hidden_file_upload_type']));
            $formDataArr['content'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hidden_media_content']));
          }
        } else {
          if (!empty($_POST['media_image_cdn'])) {
            $formDataArr['content'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['media_image_cdn']));
          } else {
            $formDataArr['content_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hidden_content_type']));
            $formDataArr['file_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hidden_file_upload_type']));
            $formDataArr['content'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hidden_media_content']));
          }
        }
      } else {
        $formDataArr['file_upload_type'] = "cdn";
        if (!empty($_POST['video_url'])) {
          $formDataArr['content'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['video_url']));
        } else {
          $formDataArr['content_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hidden_content_type']));
          $formDataArr['file_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hidden_file_upload_type']));
          $formDataArr['content'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hidden_media_content']));
        }
      }

      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));
      $formDataArr['featured_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['featured_status']));

      //print_r($formDataArr);exit;

      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Global_Media($formDataArr);

      if ($returnArr['check'] == 'success') {
        if (!$returnArr['last_insert_id'] > 0) {
          $post_id = $formDataArr['media_id'];
          if ($formDataArr['content_type'] == "image") {
            if ($formDataArr['file_upload_type'] == "cdn") {
              //unlinking previous file from server 
              if ($formDataArr['media_id'] > 0 && $_POST['hidden_file_upload_type'] == "local") {
                //unlinking uploaded file from server 
                unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_media_content']);
              }
            } else {
              //unlinking previous file from server 
              if ($formDataArr['media_id'] > 0) {
                if ($_FILES["local_media_image"]["size"] > 0) {
                  //unlinking uploaded file from server 
                  unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_media_content']);
                }
              }
            }
          } else {
            if ($formDataArr['media_id'] > 0) {
              //unlinking uploaded file from server 
              unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_media_content']);
            }
          }
        } else {
          $post_id = $returnArr['last_insert_id'];
        }
      } else {
        if ($formDataArr['content_type'] == "image") {
          if ($formDataArr['file_upload_type'] == "local") {
            //unlinking uploaded file from server 
            unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['content']);
          }
        }
        $returnArr = array('check' => 'failure', 'message' => "Something went wrong!");
      }
      //Inserting category id for this current post
      $updateCategoryArr['post_type'] = "gallery";
      $updateCategoryArr['post_id'] = $post_id;
      $updateCategoryArr['category_id'] = $_POST['category_id'];
      //print_r($updateCategoryArr);exit;
      //Calling insert array method from globalinterface controller
      $GlobalInterfaceControllerObj->edit_Post_Category($updateCategoryArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'galleryBulkUploader':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_FILES);exit;

    $dir = 'gallery';

    //Storing form basic data into formdata array
    $formDataArr['media_id'] = null;

    $user_role_slug = 'create_gallery';

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $categoryIdArr = [];

      $formDataArr['title'] = 'Gallery-' . rand();

      //Fetch all category list
      $categoryListArr = json_decode(json_encode($GlobalInterfaceControllerObj->fetch_Single_Parent_Category($dir)), true);

      $shuffeledCatArr = array_values($GlobalLibraryHandlerObj->shuffle_assoc($categoryListArr));

      foreach ($shuffeledCatArr as $index => $category) {
        if ($index % 2 == 0) {
          $categoryIdArr[$index] = $category['id'];
        }
      }

      $formDataArr['content_type'] = 'image';
      $formDataArr['file_upload_type'] = 'local';

      //Uploading file
      $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('file', $dir);

      if ($uploadReturnArr['check'] == 'success') {
        $formDataArr['content'] = $uploadReturnArr['fileName'];
        //unlink(USER_UPLOAD_DIR.$dir.'/'.$_POST['hidden_media_content']);
      } else {
        echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload file!"));
        exit;
      }

      $formDataArr['record_status'] = 'active';
      $formDataArr['featured_status'] = 'inactive';

      //print_r($formDataArr);exit;

      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Global_Media($formDataArr);

      if ($returnArr['check'] == 'success') {
        //Inserting category id for this current post
        $updateCategoryArr['post_type'] = "gallery";
        $updateCategoryArr['post_id'] = $returnArr['last_insert_id'];
        $updateCategoryArr['category_id'] = $categoryIdArr;
        //Calling insert array method from globalinterface controller
        $GlobalInterfaceControllerObj->edit_Post_Category($updateCategoryArr);

        $message = $formDataArr['title'] . " has been successfully uploaded!";
        $returnArr = array('check' => 'success', 'message' => $message);
      } else {
        $returnArr = array('check' => 'failure', 'message' => $returnArr['msg']);
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageParentCategory':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    //Storing form data into formdata array
    $formDataArr['row_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['row_id']));

    if ($formDataArr['row_id'] > 0) {
      $user_role_slug = 'update_category';
    } else {
      $user_role_slug = 'create_category';
    }

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $formDataArr['category'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['category']));
      $formDataArr['parent_category'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['parent_category']));
      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));

      //print_r($formDataArr);exit;
      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Parent_Category($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageGlobalCity':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    //Storing form data into formdata array
    $formDataArr['row_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['row_id']));

    $user_role_slug = 'manage_city_db';

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $formDataArr['name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['city']));
      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));

      //print_r($formDataArr);exit;
      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Global_City($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageProfileData':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = "manage_profile";
    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //Storing form data into formdata array
      $formDataArr['user_nicename'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_nicename']));
      $formDataArr['user_contact'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_contact']));
      $formDataArr['user_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_email']));

      if (isset($_POST['user_status'])) {
        $formDataArr['user_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_status']));
      } else {
        $formDataArr['user_status'] = 'active';
      }

      if (strlen($_POST['user_pass']) > 0) {
        $formDataArr['user_pass'] = md5(mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_pass'])));
      } else {
        $formDataArr['user_pass'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['user_hidden_password']));
      }

      $formDataArr['user_role'] =  serialize($_POST['user_role']);

      if ($_POST['page_route'] == 'edit_admin_profile') {
        $formDataArr['user_type'] = 'admin';
      } else {
        $formDataArr['user_type'] = $_SESSION['user_type'];
      }

      //print_r($formDataArr);exit;
      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->manage_Profile_Data($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    $post_id = $formDataArr['user_type'];
    $action_text = "updated";

    if ($_SESSION['user_type'] == $formDataArr["user_type"]) {
      $activity_text = " their profile data";
    } else {
      $activity_text = " profile data of, " . $formDataArr["user_type"];
    }

    if ($returnArr["check"] == "success") {
      $user_action = "has successfully " . $action_text . $activity_text;
      $attempt_status = "successful";
    } else {
      $user_action = "has attempted to " . rtrim($action_text, 'd') . $activity_text;
      $attempt_status = "restricted";
    }

    echo json_encode($returnArr);

    break;

  case 'manageEmailTemplate':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    //Storing form data into formdata array
    $formDataArr['template_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['template_id']));

    if ($formDataArr['template_id'] > 0) {
      $user_role_slug = 'update_template';
    } else {
      $user_role_slug = 'create_template';
    }

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $formDataArr['subject'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['subject']));

      $formDataArr['code'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['code']));

      $checkSlugId = $GlobalLibraryHandlerObj->checkSlugAvailibility('email_template', 'code', $formDataArr['code'])->id;

      if ($formDataArr['template_id'] > 0) {
        if (($checkSlugId != null) && ($formDataArr['template_id'] != $checkSlugId)) {
          $returnArr = (array('check' => 'failure', 'message' => 'This code is already available; Please try another.'));
          return $returnArr;
          exit;
        }
      } else {
        if ($checkSlugId != null) {
          $returnArr = (array('check' => 'failure', 'message' => 'This code is already available; Please try another.'));
          return $returnArr;
          exit;
        }
      }

      $formDataArr['email_for'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['email_for']));

      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));

      $formDataArr['variables'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['variables']));
      $formDataArr['from_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['from_email']));

      $formDataArr['from_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['from_name']));

      $formDataArr['cc_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['cc_email']));

      $formDataArr['template'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['template']));

      //print_r($formDataArr);exit; 
      //Call manage email template method
      $returnArr = $GlobalInterfaceControllerObj->manage_Global_Email_Template($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'manageHomeSlider':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    //Storing form data into formdata array
    $formDataArr['slider_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['slider_id']));

    $user_role_slug = 'manage_home_slider';

    $dir = 'home_sliders';

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $formDataArr['slider_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['slider_type']));

      $formDataArr['banner_title'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['banner_title']));

      $formDataArr['banner_text'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['banner_text']));

      $formDataArr['banner_link'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['banner_link']));

      $formDataArr['file_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['file_upload_type']));

      $formDataArr['file_upload_type'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['file_upload_type']));

      if ($formDataArr['file_upload_type'] == "local") {

        //Uploading franchise PDF and fetching uploaded PDF name
        if ($_FILES["banner_image_local"]["size"] > 0) {
          $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('banner_image_local', $dir);
          //checking file upload return data
          if ($uploadReturnArr['check'] == 'success') {
            $formDataArr['banner_image'] = $uploadReturnArr['fileName'];
          } else {
            echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload file!"));
            exit;
          }
        } else {
          $formDataArr['banner_image'] =  mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['hidden_banner_image']));
        }
      } else {
        $formDataArr['banner_image'] =  mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['banner_image_cdn']));
      }

      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));

      //print_r($formDataArr);exit; 
      //Call manage email template method
      $returnArr = $GlobalInterfaceControllerObj->manage_Home_Slider($formDataArr);

      if ($returnArr['check'] == 'success') {

        if ($formDataArr['file_upload_type'] == "cdn") {
          //unlinking previous file from server 
          if ($formDataArr['slider_id'] > 0) {
            //unlinking uploaded file from server 
            unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_banner_image']);
          }
        } else {
          //unlinking previous file from server 
          if ($formDataArr['slider_id'] > 0) {
            if ($_FILES["banner_image_local"]["size"] > 0) {
              //unlinking uploaded file from server 
              unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_banner_image']);
            }
          }
        }
      } else {
        if ($formDataArr['file_upload_type'] == "local") {
          //unlinking uploaded file from server 
          unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['banner_image']);
        }
        $returnArr = array('check' => 'failure', 'message' => "Something went wrong!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;


  case 'manageGlobalNews':

    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    //Storing form data into formdata array
    $formDataArr['news_id'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['news_id']));

    if ($formDataArr['news_id'] > 0) {
      $user_role_slug = 'update_news';
    } else {
      $user_role_slug = 'create_news';
    }

    $dir = 'news';

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      $formDataArr['title'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['title']));

      $formDataArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));

      $formDataArr['featured_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['featured_status']));

      $formDataArr['description'] = mysqli_real_escape_string(DB::$WRITELINK, trim(($_POST['description'])));

      //Uploading student image and fetching uploaded file name 
      if ($_FILES["local_news_pdf"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('local_news_pdf', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['optional_pdf'] = $uploadReturnArr['fileName'];
        } else {
          echo json_encode(array('check' => 'failure', 'msg' => "An error occurred while trying to upload news pdf!"));
          exit;
        }
      } else {
        if ($formDataArr['news_id'] > 0) {
          $formDataArr['optional_pdf'] = $_POST['hidden_optional_pdf'];
        } else {
          $formDataArr['optional_pdf'] = null;
        }
      }

      //print_r($formDataArr);exit; 
      //Call manage email template method
      $returnArr = $GlobalInterfaceControllerObj->manage_Global_News($formDataArr);

      if ($returnArr['check'] == 'success') {

        //unlinking previous file from server 
        if ($formDataArr['news_id'] > 0) {
          if ($_FILES["local_news_pdf"]["size"] > 0 && $uploadReturnArr['check'] == 'success') {
            //unlinking uploaded file from server 
            unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_optional_pdf']);
          }
        }
      } else {
        if ($_FILES["local_news_pdf"]["size"] > 0 && $uploadReturnArr['check'] == 'success') {
          //unlinking uploaded file from server 
          unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['optional_pdf']);
        }
        $returnArr = array('check' => 'failure', 'message' => "Something went wrong!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'updateStudentBulkStatus':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    $idData = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['row_id']));
    $paramArr['record_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));
    $paramArr['student_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['student_status']));
    $paramArr['result_status'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['result_status']));

    $user_role_slug = "update_student";

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

          $paramArr['row_id'] = $row_id;

          $returnArr = $GlobalInterfaceControllerObj->update_Bulk_Student_Status($paramArr);

          if ($returnArr["responseArr"]["check"] == "success") {
            $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
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


  case 'updateGlobalStatusRecord':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    $idData = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['row_id']));
    $type = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['type']));
    $record_status = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['record_status']));

    switch ($type) {

      case 'franchise':
        $user_role_slug = "update_franchise";
        break;

      case 'course':
        $user_role_slug = "update_course";
        break;

      case 'gallery':
        $user_role_slug = "update_gallery";
        break;

      case 'home_sliders':
        $user_role_slug = "manage_home_slider";
        break;

      case 'student':
        $user_role_slug = "update_student";
        break;

      case 'temp_student':
        $user_role_slug = "update_student";
        break;

      case 'student_receipts':
        $user_role_slug = "update_receipt";
        break;

      case 'exam':
        $user_role_slug = "update_exam";
        break;

      case 'parent_category':
        $user_role_slug = "update_category";
        break;

      case 'cities':
        $user_role_slug = "manage_city_db";
        break;

      case 'email_template':
        $user_role_slug = "update_template";
        break;

      case 'news':
        $user_role_slug = "update_news";
        break;

      case 'enquiry':
        $user_role_slug = "delete_enquiry";
        break;
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
        //print_r($formDataArr);exit;

        //Call update global status modify method
        foreach ($rowIdArr as $index => $row_id) {
          $returnArr = $GlobalInterfaceControllerObj->update_Global_Record_Status($type, $row_id, $record_status);
          if ($returnArr["responseArr"]["check"] == "success") {
            $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
          }
        }
        //Purge franchise data cache
        $GlobalLibraryHandlerObj->purgeSiteCache($type);
      } else {
        $returnArr = array("check" => "failure", "message" => "You haven't selected any data!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'globalFeaturedStatusUpdate':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    $row_id = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['row_id']));
    $type = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['type']));
    $featured_status = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['featured_status']));

    switch ($type) {

      case 'franchise':
        $user_role_slug = "update_franchise";
        break;

      case 'course':
        $user_role_slug = "update_course";
        break;

      case 'gallery':
        $user_role_slug = "update_gallery";
        break;

      case 'news':
        $user_role_slug = "update_news";
        break;
    }

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {
      //Call update global status modify method
      $returnArr = $GlobalInterfaceControllerObj->update_Global_Featured_Status($type, $row_id, $featured_status);
      if ($returnArr["responseArr"]["check"] == "success") {
        $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'updateTempStudentVerifiedStatus':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = "update_student";
    $tmp_id = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['tmp_id']));
    $verified_status = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['verified_status']));

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {
      //Call update global status modify method
      $returnArr = $GlobalInterfaceControllerObj->update_Tmp_Student_Verified_Status($tmp_id, $verified_status);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'updateReceiptVerifiedStatus':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = "update_receipt";
    $receipt_id = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['receipt_id']));
    $verified_status = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['verified_status']));

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {
      //Call update global status modify method
      $returnArr = $GlobalInterfaceControllerObj->update_Receipt_Verified_Status($receipt_id, $verified_status);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case 'updateStudentVerifiedStatus':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $user_role_slug = "update_student";
    $student_id = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['student_id']));
    $verified_status = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['verified_status']));

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {
      //Call update global status modify method
      $returnArr = $GlobalInterfaceControllerObj->update_Student_Verified_Status($student_id, $verified_status);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

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

  case 'deleteGlobalData':

    //Declaring necessary variables
    $deleteParam = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $idData = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['row_id']));
    $type = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['type']));

    $deleteParam['type'] = $type;

    //checking if the feeded data is a string or not
    if (strpos($idData, ',') > 0) {
      $rowIdArr = explode(',', $idData);
    } else {
      $rowIdArr = array($idData);
    }

    switch ($type) {

      case 'franchise':
        $user_role_slug = "delete_franchise";
        break;

      case 'course':
        $user_role_slug = "delete_course";
        break;

      case 'home_sliders':
        $user_role_slug = "manage_home_slider";
        break;

      case 'gallery':
        $user_role_slug = "delete_gallery";
        break;

      case 'student':
        $user_role_slug = "delete_student";
        break;

      case 'temp_student':
        $user_role_slug = "delete_student";
        break;

      case 'student_receipts':
        $user_role_slug = "delete_receipt";
        break;

      case 'parent_category':
        $user_role_slug = "delete_category";
        break;

      case 'cities':
        $user_role_slug = "manage_city_db";
        break;

      case 'email_template':
        $user_role_slug = "delete_template";
        break;

      case 'news':
        $user_role_slug = "delete_news";
        break;

      case 'enquiry':
        $user_role_slug = "delete_enquiry";
        break;
    }


    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      if (count($rowIdArr) > 0) {

        foreach ($rowIdArr as $index => $row_id) {

          $deleteParam['row_id'] = $row_id;

          switch ($type) {

            case 'franchise':
              //Call update global carousel method
              $GlobalLibraryHandlerObj->remove_File_From_Server($type, $row_id);
              //Call delete current record method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;

            case 'course':
              //Call update global carousel method
              $GlobalLibraryHandlerObj->remove_File_From_Server($type, $row_id);
              //Call delete current record method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;

            case 'student':
              //Call update global carousel method
              $GlobalLibraryHandlerObj->remove_File_From_Server($type, $row_id);
              //Call update global carousel method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;

            case 'temp_student':
              //Call update global carousel method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;

            case 'gallery':
              //Call update global carousel method
              $GlobalLibraryHandlerObj->remove_File_From_Server($type, $row_id);
              //Call update global carousel method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;

            case 'home_sliders':
              //Call update global carousel method
              $GlobalLibraryHandlerObj->remove_File_From_Server($type, $row_id);
              //Call update global carousel method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;

            case 'student_receipts':
              //Call update global carousel method
              $GlobalLibraryHandlerObj->remove_File_From_Server($type, $row_id);
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }

            case 'parent_category':
              //Call update global carousel method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;

            case 'cities':
              //Call update global carousel method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;

            case 'email_template':
              //Call update global carousel method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;

            case 'news':
              //Call update global carousel method
              $GlobalLibraryHandlerObj->remove_File_From_Server($type, $row_id);
              //Call update global carousel method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;

            case 'enquiry':
              //Call update global carousel method
              $returnArr = $GlobalInterfaceControllerObj->delete_Global_Data($deleteParam);

              if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
              }
              break;
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

  case 'manageFranchiseProfile':

    //Declaring necessary variables
    $formDataArr = array();
    $returnArr = array();

    //print_r($_POST);exit;

    $formDataArr['fran_row_id'] = $_SESSION['user_id'];

    $user_role_slug = 'manage_profile';

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //determining franchise password
      $fran_pass = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_pass']));

      if (strlen($fran_pass) > 0) {
        $formDataArr['fran_pass'] = md5($fran_pass);
        $formDataArr['fran_og_pass'] = $fran_pass;
      } else {
        $formDataArr['fran_pass'] = $_POST['fran_hidden_password'];
        $formDataArr['fran_og_pass'] = $_POST['fran_hidden_og_password'];
      }

      //Storing form data into form data array
      $dir = 'franchise';
      $formDataArr['center_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['center_name']));
      $formDataArr['owner_name'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['owner_name']));

      $formDataArr['fran_phone'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_phone']));
      $formDataArr['fran_email'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_email']));

      $formDataArr['fran_address'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_address']));

      $formDataArr['fran_description'] = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['fran_description']));

      //Uploading franchise image and  fetching uploaded file name
      if ($_FILES["fran_image"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('fran_image', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['fran_image'] = $uploadReturnArr['fileName'];
          if ($formDataArr['fran_row_id'] > 0) {
            if ($_POST['hidden_fran_image'] !== "profile_small_old.png") {
              unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_fran_image']);
            }
          }
        } else {
          $formDataArr['fran_image'] = 'profile_small_old.png';
        }
      } else {
        if ($formDataArr['fran_row_id'] > 0) {
          $formDataArr['fran_image'] = $_POST['hidden_fran_image'];
        } else {
          $formDataArr['fran_image'] = 'profile_small_old.png';
        }
      }

      //Uploading franchise PDF and fetching uploaded PDF name
      if ($_FILES["fran_pdf_name"]["size"] > 0) {
        $uploadReturnArr = $GlobalLibraryHandlerObj->upload_file('fran_pdf_name', $dir);
        //checking file upload return data
        if ($uploadReturnArr['check'] == 'success') {
          $formDataArr['fran_pdf_name'] = $uploadReturnArr['fileName'];
          if ($formDataArr['fran_row_id'] > 0) {
            if ($_POST['hidden_course_pdf'] !== "COMPUTER-COURSE.pdf") {
              unlink(USER_UPLOAD_DIR . $dir . '/' . $_POST['hidden_fran_pdf']);
            }
          }
        } else {
          $formDataArr['fran_pdf_name'] = 'COMPUTER-COURSE.pdf';
        }
      } else {
        if ($formDataArr['fran_row_id'] > 0) {
          $formDataArr['fran_pdf_name'] = $_POST['hidden_fran_pdf'];
        } else {
          $formDataArr['fran_pdf_name'] = 'COMPUTER-COURSE.pdf';
        }
      }

      //print_r($formDataArr);exit; 
      //Call create global hotel method
      $returnArr = $GlobalInterfaceControllerObj->edit_Franchise_Profile($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);
    break;

  case "exportStudentReceiptPdf":
    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();
    $formatedReceiptArr = array();

    $receipt_row_id = $_POST['receipt_row_id'];
    $user_role_slug = 'create_receipt';

    $receiptPdfRslt = $GlobalLibraryHandlerObj->createStudentReceiptPdf($receipt_row_id);

    if ($receiptPdfRslt['check'] == "success") {
      $returnArr = array('check' => 'success', 'file_upload_dir' => $receiptPdfRslt['file_upload_dir'], 'file_url' => $receiptPdfRslt['file_url']);
    } else {
      $returnArr = array('check' => 'failure', 'message' => $receiptPdfRslt['message']);
    }

    echo json_encode($returnArr);
    break;

  case "exportTempStudentReceipt":
    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();
    $formatedReceiptArr = array();

    $tmp_id = $_POST['tmp_id'];
    $user_role_slug = 'create_receipt';

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //Fetch reecipt detail
      $tmpStudentDetails = $GlobalInterfaceControllerObj->fetch_Tmp_Single_Student($tmp_id);

      $file_upload_dir =  USER_UPLOAD_DIR . 'runtime_upload/' . "TEMPRCPT_" . $tmpStudentDetails->tmp_stu_id . '.pdf';
      $file_url = USER_UPLOAD_URL . 'runtime_upload/' . "TEMPRCPT_" . $tmpStudentDetails->tmp_stu_id . '.pdf';

      if (!file_exists($file_upload_dir)) {

        $pdfParamArr = array();

        $pdfParamArr['email_code'] = 'student-temp-receipt-invoice';

        $pdfParamArr['receiver_name'] = $tmpStudentDetails->stu_name;
        $pdfParamArr['student_contact'] = $tmpStudentDetails->stu_phone;
        $pdfParamArr['site_addr'] = FRONT_SITE_URL;
        //fetching site setting detail
        $siteSettingArr = $GlobalLibraryHandlerObj->fetchSiteSettingDetail();
        $pdfParamArr['company_name'] = $siteSettingArr->title;

        //configure company logo        
        $pdfParamArr['company_logo'] = USER_UPLOAD_URL . 'others/' . $siteSettingArr->logo;
        $pdfParamArr['company_signature'] = USER_UPLOAD_URL . 'others/' . $siteSettingArr->signature;

        $pdfParamArr['company_contact_email'] = $tmpStudentDetails->fran_email; //$siteSettingArr->contact_email;
        $pdfParamArr['company_contact_no'] = $tmpStudentDetails->fran_phone; //$siteSettingArr->phone;
        $pdfParamArr['company_address'] = $tmpStudentDetails->fran_address; //$siteSettingArr->address;

        //fetching required email template detail
        $emailTemplateArr = $GlobalLibraryHandlerObj->fetchEmailTemplateDetail($pdfParamArr['email_code']);
        $pdfParamArr['email_template'] = $emailTemplateArr->template;

        //$totalFeesCleared = round((int)$receiptDetailArr->course_fees_paid + (int)$receiptDetailArr->receipt_amount);
        $dueAmount = round((int)$courseFees - (int)$studentDetails->course_fees_paid - (int)$discount_fees);

        //create a list of the variables to be swapped in the html template
        $swap_var = array(
          "{SITE_ADDR}" => $pdfParamArr['site_addr'],
          "{COMPANY_NAME}" => $pdfParamArr['company_name'],
          "{COMPANY_EMAIL}" => $pdfParamArr['company_contact_email'],
          "{CONTACT_NO}" => $pdfParamArr['company_contact_no'],
          "{COMPANY_ADDRESS}" => $pdfParamArr['company_address'],
          "{COMPANY_LOGO}" => $pdfParamArr['company_logo'],
          "{COMPANY_SIGNATURE}" => $pdfParamArr['company_signature'],
          "{INVOICE_DATE}" => date('jS F, Y', strtotime(date('Y-m-d'))),  //date('jS F, Y',strtotime($receiptDetailArr->created_at));
          "{STUDENT_NAME}" => $tmpStudentDetails->stu_name,
          "{STUDENT_FATHER}" => $tmpStudentDetails->stu_father_name,
          "{STUDENT_CONTACT}" => $tmpStudentDetails->stu_phone,
          "{TEMP_STUDENT_ID}" => $tmpStudentDetails->tmp_stu_id,
          "{COURSE}" => $tmpStudentDetails->course_title,
          "{FRANCHISE}" => $tmpStudentDetails->center_name,
          "{RECEIPT_AMOUNT}" => $tmpStudentDetails->advanced_fees,
          "{TOTAL_AMOUNT}" => $tmpStudentDetails->advanced_fees,
          "{RECEIPT_TYPE}" => "Advance Fees"
        );

        //print_r($swap_var);exit;

        //search and replace for predefined variables, like SITE_ADDR, {NAME}, {lOGO}, {CUSTOM_URL} etc
        foreach (array_keys($swap_var) as $key) {
          if (strlen($key) > 2 && trim($swap_var[$key]) != '') {
            $pdfParamArr['email_template'] = str_replace($key, $swap_var[$key], $pdfParamArr['email_template']);
          }
        }
        //echo $pdfParamArr['email_template'];exit;

        $html_code = $pdfParamArr['email_template'];

        //echo $html_code;exit;

        $dompdf = new Pdf();
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->load_html($html_code);
        //(Optional) Setup the paper size and orientation 
        $dompdf->setPaper('a4', 'portrait');
        //$customPaper = array(0,0,360,360);
        //$dompdf->set_paper($customPaper);
        $dompdf->render();
        $file = $dompdf->output();
        file_put_contents($file_upload_dir, $file);

        //Returning generated pdf
        $returnArr = array('check' => 'success', 'file_upload_dir' => $file_upload_dir, 'file_url' => $file_url);
      } else {
        //Returning exis pdf
        $returnArr = array('check' => 'success', 'file_upload_dir' => $file_upload_dir, 'file_url' => $file_url);
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);
    break;

  case "exportStudentResultPdf":
    //Decalring necessary variables
    $formDataArr = array();
    $returnArr = array();
    $formatedReceiptArr = array();

    $student_id = $_POST['student_id'];
    $user_role_slug = 'update_result';

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //Fetching student receipt detail
      $studentDataArr = $GlobalInterfaceControllerObj->fetch_Single_Profile_Student($student_id);
      //Configuring email param array
      $pdfParamArr = array();
      $pdfParamArr['email_code'] = 'student-result-pdf';

      $pdfParamArr['receiver_name'] = $receiptDetailArr->stu_name;
      $pdfParamArr['receiver_email'] = $receiptDetailArr->stu_email;
      $pdfParamArr['site_addr'] = FRONT_SITE_URL;
      //fetching site setting detail
      $siteSettingArr = $GlobalLibraryHandlerObj->fetchSiteSettingDetail();
      $pdfParamArr['company_name'] = $siteSettingArr->title;

      //configure company logo        
      $pdfParamArr['company_logo'] = USER_UPLOAD_URL . 'others/' . $siteSettingArr->logo;
      $pdfParamArr['company_signature'] = USER_UPLOAD_URL . 'others/' . $siteSettingArr->signature;

      $pdfParamArr['company_contact_email'] = $siteSettingArr->contact_email;
      $pdfParamArr['company_contact_no'] = $siteSettingArr->phone;
      $pdfParamArr['company_address'] = $siteSettingArr->address;

      //fetching required email template detail
      $emailTemplateArr = $GlobalLibraryHandlerObj->fetchEmailTemplateDetail($pdfParamArr['email_code']);
      $pdfParamArr['email_subject'] = $emailTemplateArr->subject;
      $pdfParamArr['sender_name'] = $emailTemplateArr->from_name;
      $pdfParamArr['sender_email'] = $emailTemplateArr->from_email;
      $pdfParamArr['cc_email'] = $emailTemplateArr->cc_email;
      $pdfParamArr['email_template'] = $emailTemplateArr->template;


      //create a list of the variables to be swapped in the html template
      $swap_var = array(
        "{SITE_ADDR}" => $pdfParamArr['site_addr'],
        "{COMPANY_NAME}" => $pdfParamArr['company_name'],
        "{COMPANY_EMAIL}" => $pdfParamArr['company_contact_email'],
        "{COMPANY_CONTACT_NO}" => $pdfParamArr['company_contact_no'],
        "{COMPANY_ADDRESS}" => $pdfParamArr['company_address'],
        "{COMPANY_LOGO}" => $pdfParamArr['company_logo'],
        "{COMPANY_SIGNATURE}" => $pdfParamArr['company_signature'],
        "{EMAIL_TITLE}" => $pdfParamArr['email_subject'],
        "{INVOICE_DATE}" => date('jS F, Y', time()),

        "{STUDENT_NAME}" => $studentDataArr->stu_name,
        "{FATHER_NAME}" => $studentDataArr->stu_father_name,
        "{STUDENT_EMAIL}" => $studentDataArr->stu_email,
        "{STUDENT_CONTACT}" => $studentDataArr->stu_phone,
        "{STUDENT_ID}" => $studentDataArr->stu_id,

        "{STUDENT_ADDRESS}" => $studentDataArr->stu_address,
        "{STUDENT_GENDER}" => ucfirst($studentDataArr->stu_gender),
        "{STUDENT_DOB}" => date('jS F, Y', strtotime($studentDataArr->stu_dob)),
        "{STUDENT_QUALIFICATION}" => $studentDataArr->stu_qualification,
        "{STUDENT_MARITAL_STATUS}" => ucfirst($studentDataArr->stu_marital_status),

        "{STUDENT_STATUS}" => ucfirst($studentDataArr->student_status),

        "{STUDENT_RESULT}" => ucfirst($studentDataArr->student_result),
        "{RESULT_DATE}" => date('jS F, Y', strtotime($studentDataArr->result_date)),

        "{COURSE}" => $studentDataArr->course_title,
        "{FRANCHISE}" => $studentDataArr->center_name,
      );

      //print_r($swap_var);exit;

      //search and replace for predefined variables, like SITE_ADDR, {NAME}, {lOGO}, {CUSTOM_URL} etc
      foreach (array_keys($swap_var) as $key) {
        if (strlen($key) > 2 && trim($swap_var[$key]) != '') {
          $pdfParamArr['email_template'] = str_replace($key, $swap_var[$key], $pdfParamArr['email_template']);
        }
      }
      //echo $pdfParamArr['email_template'];exit;

      $file_upload_dir =  USER_UPLOAD_DIR . 'runtime_upload/' . "Result_" . $studentDataArr->course_title . '.pdf';
      $file_url = USER_UPLOAD_URL . 'runtime_upload/' . "Result_" . $studentDataArr->course_title . '.pdf';
      $html_code = $pdfParamArr['email_template'];

      $dompdf = new Pdf();
      $dompdf->set_option('isRemoteEnabled', true);
      $dompdf->load_html($html_code);
      //(Optional) Setup the paper size and orientation 
      //$dompdf->setPaper('A4', 'landscape'); 
      $dompdf->render();
      $file = $dompdf->output();
      file_put_contents($file_upload_dir, $file);
      //chmod("$file_upload_dir", 0644);
      $returnArr =  array('check' => 'success', 'file_upload_dir' => $file_upload_dir, 'file_url' => $file_url);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    //Returning generated pdf
    echo json_encode($returnArr);
    exit;

    break;

  case "cleanDynamicContentFolder":
    $file_upload_dir = USER_UPLOAD_DIR . "runtime_upload/";

    //check action permission        
    $siteSettingPermission = $GlobalLibraryHandlerObj->checkUserRolePermission("update_site_setting", "hard");

    if ($siteSettingPermission == false && $_SESSION['user_type'] == "franchise") {
      $siteSettingPermission = true;
    }

    if ($siteSettingPermission) {
      $files = glob($file_upload_dir . '/*'); // get all file names
      foreach ($files as $file) { // iterate files
        if (is_file($file)) {
          unlink($file); // delete file
        }
      }
      $returnArr = array('check' => 'success', 'message' => 'All unnecessary files are deleted from server!');
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);
    break;

  case "clearCacheFolder":

    $user_role_slug = 'update_site_setting';
    $cache_file_dir = APP_CACHE_DIR;

    //check action permission        
    //$updateSiteSettingPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug,"hard");  

    if ($_SESSION['user_type'] == "developer") {
      $files = glob($cache_file_dir . '/*'); // get all file names
      foreach ($files as $file) { // iterate files
        if (is_file($file)) {
          unlink($file); // delete file
        }
      }
      $returnArr = array('check' => 'success', 'message' => 'Cache memory is successfully cleaned!');
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);

    break;

  case "clearCacheFolder":

    $user_role_slug = 'update_site_setting';
    $cache_file_dir = APP_CACHE_DIR;
    $clearType = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['clearType']));

    if ($clearType == "current_page") {
      $currentCacheFile = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['currentCacheFile']));
      $currentCacheFilePath = $cache_file_dir . $currentCacheFile;
      //Removing current cache file from server
      unlink($currentCacheFilePath);
      echo json_encode(array('check' => 'success', 'message' => 'Cache memory is successfully cleaned!'));
    } else {
      //check action permission        
      $updateSiteSettingPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

      if ($updateSiteSettingPermission === true) {
        $files = glob($cache_file_dir . '/*'); // get all file names
        foreach ($files as $file) { // iterate files
          if (is_file($file)) {
            unlink($file); // delete file
          }
        }
        $returnArr = array('check' => 'success', 'message' => 'Cache memory is successfully cleaned!');
      } else {
        $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
      }

      echo json_encode($returnArr);
    }

    break;

  case "setSiteCachingStatus":

    $user_role_slug = "update_site_setting";

    //check action permission        
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {
      $site_caching = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['status']));

      if ($site_caching == "true") {
        $formDataArr['site_caching'] = 'active';
      } else {
        $formDataArr['site_caching'] = 'inactive';
      }
      $returnArr = $GlobalInterfaceControllerObj->update_Site_Caching_Status($formDataArr);
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);
    break;

  case "removeFileFromServer":
    $file_upload_dir = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['file_upload_dir']));

    unlink($file_upload_dir);
    echo json_encode(array('check' => 'success'));
    break;

  case "createBackupOnServer":
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission("manage_site_backup");
    $cookie_name = "backupCount";

    //setcookie("backupCount", "", time() - 3600, "/");

    if ($checkActionPermission) {

      if (!empty($_COOKIE[$cookie_name])) {
        $backupCount = $_COOKIE[$cookie_name];
      } else {
        $backupCount = 0;
      }

      $backupLimit = $_SESSION['user_type'] == "developer" ? true : ($backupCount == 2 ? false : true);
      $setCookie = $_SESSION['user_type'] == "developer" ? false : true;

      //var_dump($_COOKIE[$cookie_name]);exit;

      if ($backupLimit) {

        //Fetch current site backup files
        $siteBackupFiles = $GlobalLibraryHandlerObj->fetchSiteBackupFiles();

        // DB file path
        $dbFilePath = SITE_BACKUP_DIR . 'theaimgcsm_' . date('Y-m-d_H-i-s') . '_' . time() . '_db_backup.sql';

        //Upload file path
        $uploadsFilePath = SITE_BACKUP_DIR . 'uploads_' . date('Y-m-d_H-i-s') .'_'. time(). '_backup.zip';

        //Creating database backup file 
        $dbFileCreated = $GlobalLibraryHandlerObj->createDBBak($dbFilePath);

        //Take bakup of uploads folder if database bakup file successfully created
        if ($dbFileCreated) {
          $zipFileCreated = $GlobalLibraryHandlerObj->createUploadsZip($uploadsFilePath);

          if ($zipFileCreated) {
            //Remove all previous site backup files
            foreach ($siteBackupFiles as $index => $file) {
              $file_url = SITE_BACKUP_DIR . $file->name;
              unlink($file_url);
            }

            if ($setCookie) {
              //Set backup count in cookies
              $newBackupCount = intval($backupCount + 1);
              setcookie($cookie_name, $newBackupCount, time() + (86400 * 1), "/");
            }

            $returnArr = array('check' => 'success', "message" => "Backup is successfully created!");
          } else {
            unlink($dbFilePath);
            $returnArr = array('check' => 'failure', "message" => "Backup failed!");
          }
        } else {
          $returnArr = array('check' => 'failure', "message" => "Backup failed!");
        }
      } else {
        $returnArr = array('check' => 'failure', "message" => "Backup limit exhausted!");
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    echo json_encode($returnArr);
    break;

  case "createSiteBackupQueueJob":
    $checkActionPermission = $GlobalLibraryHandlerObj->checkUserRolePermission("manage_site_backup");
    $cookie_name = "backupCount";

    //setcookie("backupCount", "", time() - 3600, "/");

    if ($checkActionPermission) {

      // Check if there is already a pending task in db
      $checkPendingTask = $GlobalInterfaceControllerObj->check_Task_Status();
      // Check if there's a running task
      $checkRunningTask = $GlobalInterfaceControllerObj->check_Task_Status("running");

      if (empty($checkPendingTask) && empty($checkRunningTask)) {

        if (!empty($_COOKIE[$cookie_name])) {
          $backupCount = $_COOKIE[$cookie_name];
        } else {
          $backupCount = 0;
        }

        $backupLimit = $_SESSION['user_type'] == "developer" ? true : ($backupCount == 2 ? false : true);
        $setCookie = $_SESSION['user_type'] == "developer" ? false : true;

        //var_dump($_COOKIE[$cookie_name]);exit;

        if ($backupLimit) {

          $formDataArr['action'] = "create";
          $formDataArr['job_type'] = "site_backup_creation";

          //Call create queue job method
          $createRspns = $GlobalInterfaceControllerObj->manage_Queue_Jobs($formDataArr);

          if ($createRspns['check'] == "success") {

            if ($setCookie) {
              //Set backup count in cookies
              $newBackupCount = intval($backupCount + 1);
              setcookie($cookie_name, $newBackupCount, time() + (86400 * 1), "/");
            }
            
            $returnArr = array('check' => 'success', "message" => "Backup job is successfully queued!");
          } else {
            $returnArr = array('check' => 'failure', "message" => "Something went wrong, please try later!");
          }
        } else {
          $returnArr = array('check' => 'failure', "message" => "Backup limit exhausted!");
        }
      } else {
        $returnArr = array('check' => 'failure', 'message' => "There is already a pending task on the queue!");
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
