<?php
defined('ROOTPATH') or exit('No direct script access allowed');

use Dompdf\Dompdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Pdf extends Dompdf
{
  public function __construct()
  {
    parent::__construct();
  }
}

class GlobalLibraryHandler
{

  private $globalReturnArr = [];
  private $GlobalControllerInterfaceObj;
  private $memObj;

  public function __construct()
  {
    $this->GlobalControllerInterfaceObj = new GlobalInterfaceController();
    if (SERVER_ENV == "PRODUCTION") {
      $this->memObj = new Memcached();
      $this->memObj->addServer("127.0.0.1", 11211);
    } else {
      $this->memObj = null;
    }
  }

  public function checkUserLogin($paramArr)
  {
    $returnArr = array();
    $returnArr = $this->GlobalControllerInterfaceObj->check_User_Login($paramArr);

    return $returnArr;
  }

  public function checkRunTimeFolderExistance()
  {
    //Check runtime folder existance
    $runtime_upload_dir_path = USER_UPLOAD_DIR . 'runtime_upload/';
    if (!file_exists($runtime_upload_dir_path)) {
      mkdir("$runtime_upload_dir_path");
      chmod("$runtime_upload_dir_path", 0755);
    }
  }

  public function checkUserRolePermission($user_role_slug, $fetch_type = "hard")
  {
    $returnArr = array();
    $paramArr['user_id'] = $_SESSION['user_id'];
    $paramArr['user_type'] = $_SESSION['user_type'];

    if ($fetch_type == "hard") {
      //Fetch current user role
      $userRoleArr = $this->GlobalControllerInterfaceObj->fetch_Current_User_Role($paramArr);
    } else {
      //Fetch current user role
      $userRoleArr = $_SESSION['user_role'];
    }

    //echo $fetch_type;
    /*print"<pre>";
    print_r($userRoleArr);exit;*/

    //Check user permission 
    if (is_array($userRoleArr)) {
      if (in_array($user_role_slug, $userRoleArr)) {
        return true;
      } else {
        return false;
      }
    } else {
      return false;
    }
  }

  public function purgeSiteCache($section){
    switch ($section) {
      case 'student':
          if ($_SESSION['user_type'] == 'developer' || $_SESSION['user_type'] == 'admin') {
            $this->memObj->delete("student_dashboard_today");
            $this->memObj->delete("student_dashboard_weekly");
            $this->memObj->delete("student_dashboard_monthly");
            $this->memObj->delete("student_dashboard_annual");
          }

          elseif($_SESSION['user_type'] == 'franchise'){
            $franchise_id = $_SESSION['user_id'];
            $this->memObj->delete("student_dashboard_today_$franchise_id");
            $this->memObj->delete("student_dashboard_weekly_$franchise_id");
            $this->memObj->delete("student_dashboard_monthly_$franchise_id");
            $this->memObj->delete("student_dashboard_annual_$franchise_id");
          }
        break;
      
      case 'student_receipts':
          if ($_SESSION['user_type'] == 'developer' || $_SESSION['user_type'] == 'admin') {
            $this->memObj->delete("receipt_dashboard_today");
            $this->memObj->delete("receipt_dashboard_weekly");
            $this->memObj->delete("receipt_dashboard_monthly");
            $this->memObj->delete("receipt_dashboard_annual");
          }

          elseif($_SESSION['user_type'] == 'franchise'){
            $franchise_id = $_SESSION['user_id'];
            $this->memObj->delete("receipt_dashboard_today_$franchise_id");
            $this->memObj->delete("receipt_dashboard_weekly_$franchise_id");
            $this->memObj->delete("receipt_dashboard_monthly_$franchise_id");
            $this->memObj->delete("receipt_dashboard_annual_$franchise_id");
          }
        break;  
      
      case 'franchise':
          $this->memObj->delete("franchise_data_active");
          $this->memObj->delete("franchise_data_blocked");
        break;  
      
      case 'course':
          $this->memObj->delete("course_data");
          $this->memObj->delete("course_data_active");
          $this->memObj->delete("course_data_blocked");
        break;    
      
      case 'others':
          $this->memObj->delete("news_data");
          $this->memObj->delete("enquiry_data");
          $this->memObj->delete("gallery_data");
        break;  

      default:
        # code...
        break;
    }
  }

  public function fetchSiteBackupFiles()
  {
    // Specify the folder path
    $folderPath = SITE_BACKUP_DIR;
    
    // Create backup folder if not exists
    if (!file_exists($folderPath)) {
        mkdir($folderPath, 0777, true);
    }

    // Initialize an empty array to store file details
    $fileDetails = [];

    // Open the folder
    $directory = opendir($folderPath);

    // Define allowed file extensions
    $allowedExtensions = ['zip', 'sql'];

    // Loop through each file in the folder
    while (($file = readdir($directory)) !== false) {
      // Skip "." and ".." entries
      if ($file != '.' && $file != '..') {
        // Full path of the file
        $filePath = $folderPath . '/' . $file;

        // Get file details
        $sizeBytes = filesize($filePath);

        // Convert size to megabytes
        $sizeMB = round($sizeBytes / (1024 * 1024), 2);

        $creationDate = filectime($filePath);
        $fileType = mime_content_type($filePath);
        $fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);

        // Format the creation date
        $formattedCreationDate = date('Y-m-d H:i:s', $creationDate);

        if (in_array($fileExtension, $allowedExtensions)) {
          // Add file details to the array
          $siteBakFilesArr[] = [
            'name' => $file,
            'size' => $sizeMB,
            'creation_date' => $formattedCreationDate,
            'file_type' => $fileType,
          ];
        }
      }
    }

    // Close the directory handle
    closedir($directory);

    return json_decode(json_encode($siteBakFilesArr), FALSE);
  }

  public function fetchUserDashboardData()
  {
    $user_role_slug = 'view_dashboard';
    $site_bakup_persmission = "manage_site_backup";
    $type = 'dashboard';
    $this->globalReturnArr['page_type'] = $type;
    //Check user permission for this section
    $page_permission = $this->checkUserRolePermission($user_role_slug);

    $site_bakup_permission = $this->checkUserRolePermission($site_bakup_persmission);

    if ($page_permission) {
      $stuParamArr = array();
      $rcptParamArr = array();

      if ($_GET['dataType'] == "student" && isset($_GET['fetchType'])) {
        $stuParamArr['fetchType'] = $_GET['fetchType'];
      } else {
        $stuParamArr['fetchType'] = "weekly";
      }

      if ($_GET['dataType'] == "receipt" && isset($_GET['fetchType'])) {
        $rcptParamArr['fetchType'] = $_GET['fetchType'];
      } else {
        $rcptParamArr['fetchType'] = "weekly";
      }

      if ($_SESSION['user_type'] == "franchise") {
        $stuParamArr['franchise_id'] = $_SESSION['user_id'];
        $rcptParamArr['franchise_id'] = $_SESSION['user_id'];
      }

      $enquiryParamArr['limit'] = 20;
      $enquiryParamArr['pageNo'] = 1;
      $enquiryParamArr['record_status'] = 'active';
      $galleryParamArr['record_status'] = 'active';

      $newsParamArr['record_status'] = 'active';

      //Fetch site backup data based on user type
      if ($site_bakup_permission) {

        $siteBackupFiles = $this->fetchSiteBackupFiles();

        $this->globalReturnArr['site_bak_files'] = $siteBackupFiles;
      } else {
        $this->globalReturnArr['site_bak_files'] = [];
      }

      //Fetch news data based on parameters
      if ($this->memObj == null) {
        $this->globalReturnArr['news_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_News($newsParamArr);
      } else {
        $response = $this->memObj->get("news_data");
        //Check if data stored in memcached
        if ($response) {
          $this->globalReturnArr['news_data'] = $response;
        } else {
          $response = $this->GlobalControllerInterfaceObj->fetch_Global_News($newsParamArr);
          $this->memObj->set("news_data", $response);
          //Set data into a key of memcached
          $this->globalReturnArr['news_data'] = $response;
        }
      }

      //Fetch course data based on parameters
      if ($this->memObj == null) {
        $this->globalReturnArr['course_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Course();
      } else {
        $response = $this->memObj->get("course_data");
        //Check if data stored in memcached
        if ($response) {
          $this->globalReturnArr['course_data'] = $response;
        } else {
          $response = $this->GlobalControllerInterfaceObj->fetch_Global_Course();
          $this->memObj->set("course_data", $response);
          //Set data into a key of memcached
          $this->globalReturnArr['course_data'] = $response;
        }
      }

      if ($_SESSION['user_type'] == 'developer' || $_SESSION['user_type'] == 'admin') {

        //Fetch student data based on parameters
        if ($this->memObj == null) {
          $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
        } else {
          if ($_GET['dataType'] == "student" && $_GET['fetchType'] == "today") {
            $response = $this->memObj->get("student_dashboard_today");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['student_data'] = $response;
            } else {
              $studentData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
              $this->memObj->set("student_dashboard_today", $studentData);
              //Set data into a key of memcached
              $this->globalReturnArr['student_data'] = $studentData;
            }
          } elseif ($_GET['dataType'] == "student" && $_GET['fetchType'] == "weekly") {
            $response = $this->memObj->get("student_dashboard_weekly");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['student_data'] = $response;
            } else {
              $studentData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
              $this->memObj->set("student_dashboard_weekly", $studentData);
              //Set data into a key of memcached
              $this->globalReturnArr['student_data'] = $studentData;
            }
          } elseif ($_GET['dataType'] == "student" && $_GET['fetchType'] == "monthly") {
            $response = $this->memObj->get("student_dashboard_monthly");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['student_data'] = $response;
            } else {
              $studentData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
              $this->memObj->set("student_dashboard_monthly", $studentData);
              //Set data into a key of memcached
              $this->globalReturnArr['student_data'] = $studentData;
            }
          } elseif ($_GET['dataType'] == "student" && $_GET['fetchType'] == "annual") {
            $response = $this->memObj->get("student_dashboard_annual");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['student_data'] = $response;
            } else {
              $studentData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
              $this->memObj->set("student_dashboard_annual", $studentData);
              //Set data into a key of memcached
              $this->globalReturnArr['student_data'] = $studentData;
            }
          } else {
            $response = $this->memObj->get("student_dashboard_weekly");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['student_data'] = $response;
            } else {
              $studentData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
              $this->memObj->set("student_dashboard_weekly", $studentData);
              //Set data into a key of memcached
              $this->globalReturnArr['student_data'] = $studentData;
            }
          }
        }

        //Fetch receipt data based on parameters
        if ($this->memObj == null) {
          $this->globalReturnArr['receipt_data'] = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
        } else {
          if ($_GET['dataType'] == "receipt" && $_GET['fetchType'] == "today") {
            $response = $this->memObj->get("receipt_dashboard_today");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['receipt_data'] = $response;
            } else {
              $receiptData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
              $this->memObj->set("receipt_dashboard_today", $receiptData);
              //Set data into a key of memcached
              $this->globalReturnArr['receipt_data'] = $receiptData;
            }
          } elseif ($_GET['dataType'] == "receipt" && $_GET['fetchType'] == "weekly") {
            $response = $this->memObj->get("receipt_dashboard_weekly");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['receipt_data'] = $response;
            } else {
              $receiptData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
              $this->memObj->set("receipt_dashboard_weekly", $receiptData);
              //Set data into a key of memcached
              $this->globalReturnArr['receipt_data'] = $receiptData;
            }
          } elseif ($_GET['dataType'] == "receipt" && $_GET['fetchType'] == "monthly") {
            $response = $this->memObj->get("receipt_dashboard_monthly");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['receipt_data'] = $response;
            } else {
              $receiptData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
              $this->memObj->set("receipt_dashboard_monthly", $receiptData);
              //Set data into a key of memcached
              $this->globalReturnArr['receipt_data'] = $receiptData;
            }
          } elseif ($_GET['dataType'] == "receipt" && $_GET['fetchType'] == "annual") {
            $response = $this->memObj->get("receipt_dashboard_annual");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['receipt_data'] = $response;
            } else {
              $receiptData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
              $this->memObj->set("receipt_dashboard_annual", $receiptData);
              //Set data into a key of memcached
              $this->globalReturnArr['receipt_data'] = $receiptData;
            }
          }else{
            $response = $this->memObj->get("receipt_dashboard_weekly");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['receipt_data'] = $response;
            } else {
              $receiptData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
              $this->memObj->set("receipt_dashboard_weekly", $receiptData);
              //Set data into a key of memcached
              $this->globalReturnArr['receipt_data'] = $receiptData;
            }
          }
        }

        //Fetch student data based on parameters
        if ($this->memObj == null) {
          $this->globalReturnArr['enquiry_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Enquiry($enquiryParamArr);
        } else {
          $response = $this->memObj->get("enquiry_data");
          //Check if data stored in memcached
          if ($response) {
            $this->globalReturnArr['enquiry_data'] = $response;
          } else {
            $response = $this->GlobalControllerInterfaceObj->fetch_Global_Enquiry($enquiryParamArr);
            $this->memObj->set("enquiry_data", $response);
            //Set data into a key of memcached
            $this->globalReturnArr['enquiry_data'] = $response;
          }
        }
      } elseif ($_SESSION['user_type'] == 'franchise') {

        $franchise_id = $_SESSION['user_id'];

        $franchiseDetailArr = $this->GlobalControllerInterfaceObj->fetch_Global_Single_Franchise($franchise_id);
        $owned_status = $franchiseDetailArr->owned_status;

        if ($this->memObj == null) {
          $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
        } else {
          if ($_GET['dataType'] == "student" && $_GET['fetchType'] == "today") {
            $response = $this->memObj->get("student_dashboard_today_$franchise_id");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['student_data'] = $response;
            } else {
              $studentData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
              $this->memObj->set("student_dashboard_today_$franchise_id", $studentData);
              //Set data into a key of memcached
              $this->globalReturnArr['student_data'] = $studentData;
            }
          } elseif ($_GET['dataType'] == "student" && $_GET['fetchType'] == "weekly") {
            $response = $this->memObj->get("student_dashboard_weekly_$franchise_id");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['student_data'] = $response;
            } else {
              $studentData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
              $this->memObj->set("student_dashboard_weekly_$franchise_id", $studentData);
              //Set data into a key of memcached
              $this->globalReturnArr['student_data'] = $studentData;
            }
          } elseif ($_GET['dataType'] == "student" && $_GET['fetchType'] == "monthly") {
            $response = $this->memObj->get("student_dashboard_monthly_$franchise_id");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['student_data'] = $response;
            } else {
              $studentData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
              $this->memObj->set("student_dashboard_monthly_$franchise_id", $studentData);
              //Set data into a key of memcached
              $this->globalReturnArr['student_data'] = $studentData;
            }
          } elseif ($_GET['dataType'] == "student" && $_GET['fetchType'] == "annual") {
            $response = $this->memObj->get("student_dashboard_annual_$franchise_id");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['student_data'] = $response;
            } else {
              $studentData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
              $this->memObj->set("student_dashboard_annual_$franchise_id", $studentData);
              //Set data into a key of memcached
              $this->globalReturnArr['student_data'] = $studentData;
            }
          } else {
            $response = $this->memObj->get("student_dashboard_weekly_$franchise_id");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['student_data'] = $response;
            } else {
              $studentData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Student_Data($stuParamArr);
              $this->memObj->set("student_dashboard_weekly_$franchise_id", $studentData);
              //Set data into a key of memcached
              $this->globalReturnArr['student_data'] = $studentData;
            }
          }
        }

        if ($owned_status == 'yes') {
          //Fetch receipt data based on parameters
          if ($this->memObj == null) {
            $this->globalReturnArr['receipt_data'] = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
          } else {
            if ($_GET['dataType'] == "receipt" && $_GET['fetchType'] == "today") {
              $response = $this->memObj->get("receipt_dashboard_today_$franchise_id");
              //Check if data stored in memcached
              if ($response) {
                $this->globalReturnArr['receipt_data'] = $response;
              } else {
                $receiptData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
                $this->memObj->set("receipt_dashboard_today_$franchise_id", $receiptData);
                //Set data into a key of memcached
                $this->globalReturnArr['receipt_data'] = $receiptData;
              }
            } elseif ($_GET['dataType'] == "receipt" && $_GET['fetchType'] == "weekly") {
              $response = $this->memObj->get("receipt_dashboard_weekly_$franchise_id");
              //Check if data stored in memcached
              if ($response) {
                $this->globalReturnArr['receipt_data'] = $response;
              } else {
                $receiptData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
                $this->memObj->set("receipt_dashboard_weekly_$franchise_id", $receiptData);
                //Set data into a key of memcached
                $this->globalReturnArr['receipt_data'] = $receiptData;
              }
            } elseif ($_GET['dataType'] == "receipt" && $_GET['fetchType'] == "monthly") {
              $response = $this->memObj->get("receipt_dashboard_monthly_$franchise_id");
              //Check if data stored in memcached
              if ($response) {
                $this->globalReturnArr['receipt_data'] = $response;
              } else {
                $receiptData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
                $this->memObj->set("receipt_dashboard_monthly_$franchise_id", $receiptData);
                //Set data into a key of memcached
                $this->globalReturnArr['receipt_data'] = $receiptData;
              }
            } elseif ($_GET['dataType'] == "receipt" && $_GET['fetchType'] == "annual") {
              $response = $this->memObj->get("receipt_dashboard_annual_$franchise_id");
              //Check if data stored in memcached
              if ($response) {
                $this->globalReturnArr['receipt_data'] = $response;
              } else {
                $receiptData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
                $this->memObj->set("receipt_dashboard_annual_$franchise_id", $receiptData);
                //Set data into a key of memcached
                $this->globalReturnArr['receipt_data'] = $receiptData;
              }
            }else{
              $response = $this->memObj->get("receipt_dashboard_weekly_$franchise_id");
              //Check if data stored in memcached
              if ($response) {
                $this->globalReturnArr['receipt_data'] = $response;
              } else {
                $receiptData = $this->GlobalControllerInterfaceObj->fetch_Dashboard_Receipt_Data($rcptParamArr);
                $this->memObj->set("receipt_dashboard_weekly_$franchise_id", $receiptData);
                //Set data into a key of memcached
                $this->globalReturnArr['receipt_data'] = $receiptData;
              }
            }
          }
        } else {
          //Fetch gallery item
          if ($this->memObj == null) {
            $this->globalReturnArr['gallery_data'] = $this->GlobalControllerInterfaceObj->fetch_Gallery_Arr($galleryParamArr['record_status']);
          } else {
            $response = $this->memObj->get("gallery_data");
            //Check if data stored in memcached
            if ($response) {
              $this->globalReturnArr['gallery_data'] = $response;
            } else {
              $response = $this->GlobalControllerInterfaceObj->fetch_Gallery_Arr($galleryParamArr['record_status']);
              $this->memObj->set("gallery_data", $response);
              //Set data into a key of memcached
              $this->globalReturnArr['gallery_data'] = $response;
            }
          }
        }
      }
    } else {
      $this->globalReturnArr['data'] = array('page' => 'dashboard');
    }

    $this->globalReturnArr['page_permission'] = $page_permission;

    return $this->globalReturnArr;
  }

  public function view_Franchise_Required_Data()
  {
    $user_role_slug = 'view_franchise';
    $type = 'franchise';

    $this->globalReturnArr['page_title'] = "View Franchise";
    $this->globalReturnArr['page_type'] = $type;
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {
      if (isset($_GET['record_status'])) {
        $record_status = $_GET['record_status'];
      } else {
        $record_status = 'active';
      }
      
      //Call read global blog method
      $this->globalReturnArr['data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Franchise($record_status);

      if ($this->memObj == null) {
        $this->globalReturnArr['data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Franchise($record_status);
      } else {
        $response = $this->memObj->get("franchise_data_$record_status");
        //Check if data stored in memcached
        if ($response) {
          $this->globalReturnArr['data'] = $response;
        } else {
          $response = $this->GlobalControllerInterfaceObj->fetch_Global_Franchise($record_status);
          $this->memObj->set("franchise_data_$record_status", $response);
          //Set data into a key of memcached
          $this->globalReturnArr['data'] = $response;
        }
      }
    } else {
      $this->globalReturnArr['data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function view_Course_Required_Data()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission('view_course');
    $type = 'course';
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Fetch course data based on parameters
      if ($this->memObj == null) {
        $this->globalReturnArr['data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Course($record_status);
      } else {
        $response = $this->memObj->get("course_data_$record_status");
        //Check if data stored in memcached
        if ($response) {
          $this->globalReturnArr['data'] = $response;
        } else {
          $response = $this->GlobalControllerInterfaceObj->fetch_Global_Course($record_status);
          $this->memObj->set("course_data_$record_status", $response);
          //Set data into a key of memcached
          $this->globalReturnArr['data'] = $response;
        }
      }

    } else {
      $this->globalReturnArr['data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function fetch_Active_Course_Franchise_Data(){
     
     $activeCourseFranchiseArr = [];

     //Fetch franchise data based on memcached
     if ($this->memObj == null) {
        $activeCourseFranchiseArr['franchise'] = $this->GlobalControllerInterfaceObj->fetch_Global_Franchise("active");
     } else {
        $response = $this->memObj->get("franchise_data_active");
        //Check if data stored in memcached
        if ($response) {
          $activeCourseFranchiseArr['franchise'] = $response;
        } else {
          $response = $this->GlobalControllerInterfaceObj->fetch_Global_Franchise("active");
          $this->memObj->set("franchise_data_active", $response);
          //Set data into a key of memcached
          $activeCourseFranchiseArr['franchise'] = $response;
        }
     }

      //Fetch course data based on memcached
      if ($this->memObj == null) {
        $activeCourseFranchiseArr['course'] = $this->GlobalControllerInterfaceObj->fetch_Global_Course("active");
      } else {
        $response = $this->memObj->get("course_data_active");
        //Check if data stored in memcached
        if ($response) {
          $activeCourseFranchiseArr['course'] = $response;
        } else {
          $response = $this->GlobalControllerInterfaceObj->fetch_Global_Course("active");
          $this->memObj->set("course_data_active", $response);
          //Set data into a key of memcached
          $activeCourseFranchiseArr['course'] = $response;
        }
     }

     return $activeCourseFranchiseArr;

  }

  public function view_Student_Required_Data()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission('view_student');
    $type = 'student';
    //Fetching record status
    if (isset($_GET['record_status'])) {
      $dataArr['record_status'] = $_GET['record_status'];
    } else {
      $dataArr['record_status'] = 'active';
    }

    //adding page type into returndata array 
    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {

      //Fetch all active course & franchise list
      $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
      
      $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
      $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];

      if (isset($_GET['pageNo'])) {
        $dataArr['pageNo'] = $_GET['pageNo'];
      } else {
        $dataArr['pageNo'] = 1;
      }

      $dataArr['limit'] = 50;

      //Fetching record status
      if (!empty($_GET['student_status'])) {
        $dataArr['student_status'] = $_GET['student_status'];
      } else {
        $dataArr['student_status'] = null;
      }

      if (!empty($_GET['result_status'])) {
        $dataArr['result_status'] = $_GET['result_status'];
      }

      if (!empty($_GET['verified_status'])) {
        $dataArr['verified_status'] = $_GET['verified_status'];
      }

      if ($_GET['course_id'] > 0) {
        $dataArr['course_id'] = $_GET['course_id'];
      }

      if ($_SESSION['user_type'] == 'franchise') {
        $dataArr['franchise_id'] = $_SESSION['user_id'];
      } elseif ($_GET['franchise_id'] > 0) {
        $dataArr['franchise_id'] = $_GET['franchise_id'];
      }

      if (!empty($_GET['search_string'])) {
        $dataArr['search_string'] = $_GET['search_string'];
      }

      if ($_GET['created'] > 0) {
        $created = $_GET['created'];
        $created = str_replace('/', '-', $created);
        $dataArr['created'] = date('Y-m-d', strtotime($created));
      }

      if (strlen($_GET['search_start']) > 0) {
        $search_start = $_GET['search_start'];
        $search_start = str_replace('/', '-', $search_start);
        $dataArr['search_start'] = date('Y-m-d', strtotime($search_start));
      }

      if (strlen($_GET['search_end']) > 0) {
        $search_end = $_GET['search_end'];
        $search_end = str_replace('/', '-', $search_end);
        $dataArr['search_end'] = date('Y-m-d', strtotime($search_end));
      }

      //Call read global student method
      $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Student($dataArr);
    } else {
      $this->globalReturnArr['franchise_data'] = array();
      $this->globalReturnArr['course_data'] = array();
      $this->globalReturnArr['student_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function create_Frnachise_ID()
  {
    //Creating new Franchise id method
    $franchiseDetail = $this->GlobalControllerInterfaceObj->fetch_Last_Franchise_Detail();
    $last_fran_id = $franchiseDetail[0]->fran_id;

    if ($last_fran_id != null) {
      $last_fran_id_part_2 = substr($last_fran_id, 5);
      $last_fran_id_part_2++;
    } else {
      $last_fran_id_part_2 = 1;
    }

    $current_fran_id = "WBMGF" . $last_fran_id_part_2;

    return $current_fran_id;
  }

  public function create_Student_ID()
  {
    //Creating new Student id method
    $stuIdDetail = $this->GlobalControllerInterfaceObj->fetch_Last_Student_Detail();
    $lst_stu_id = $stuIdDetail['lst_stu_id'];

    if (!empty($lst_stu_id)) {
      $lst_stu_id_part_2 = substr($lst_stu_id, 10);
      $nxt_stu_id = round($lst_stu_id_part_2 + 1);
    } else {
      $lst_stu_id_part_2 = 1;
      $nxt_stu_id = $lst_stu_id_part_2;
    }

    $current_stu_id = "WBTAIMGCSM" . $nxt_stu_id;

    return $current_stu_id;
  }

  public function create_Tmp_Student_ID($min = 999, $max = 999999, $quantity = 1)
  {
    $numbers = range($min, $max);
    shuffle($numbers);
    $randomNumArr = array_slice($numbers, 0, $quantity);

    return "TMPSTUDENT" . $randomNumArr[0];
  }

  public function create_Receipt_ID()
  {
    //Creating new Franchise id method
    $receiptDetail = $this->GlobalControllerInterfaceObj->fetch_Last_Receipt_Detail();
    $last_rcpt_id = $receiptDetail[0]->receipt_id;

    if ($last_rcpt_id != null) {
      $last_rcpt_id_pt_2 = substr($last_rcpt_id, 17);
      $last_rcpt_id_pt_2++;
    } else {
      $last_rcpt_id_pt_2 = 1;
    }

    $current_rcpt_id = "WBTAIMGCSMRECEIPT" . $last_rcpt_id_pt_2;

    return $current_rcpt_id;
  }

  public function view_Exam_Required_Data()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission('view_exam');

    $type = 'exams';
    //Fetching record status
    if (isset($_GET['record_status'])) {
      $dataArr['record_status'] = $_GET['record_status'];
    } else {
      $dataArr['record_status'] = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Call read global news method
      $returnArr = $this->GlobalControllerInterfaceObj->fetch_Global_Exams($dataArr);
      $this->globalReturnArr['exam_data'] = $returnArr;
    } else {
      $this->globalReturnArr['exam_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function fetchStudentExamDashboard()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = true;

    $type = 'exams';
    //Fetching record status
    if (isset($_GET['record_status'])) {
      $dataArr['record_status'] = $_GET['record_status'];
    } else {
      $dataArr['record_status'] = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Call read global news method
      $returnArr = $this->GlobalControllerInterfaceObj->fetch_Global_Exams($dataArr);
      $this->globalReturnArr['exam_data'] = $returnArr;
    } else {
      $this->globalReturnArr['exam_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function fetch_Gallery_Item_Detail()
  {

    $type = 'gallery';
    $this->globalReturnArr['page_type'] = $type;

    //Fetching record status
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    if (empty($_GET['type'])) {
      $user_role_slug = 'view_gallery';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch gallery item
        $this->globalReturnArr['gallery_data'] = $this->GlobalControllerInterfaceObj->fetch_Gallery_Arr($record_status);
      } else {
        $this->globalReturnArr['gallery_data'] = array();
      }
    } else if ($_GET['type'] == 'add') {
      $user_role_slug = 'create_gallery';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch all category list
        $this->globalReturnArr['category_data'] = $this->GlobalControllerInterfaceObj->fetch_Single_Parent_Category($type);
      } else {
        $this->globalReturnArr['category_data'] = array();
      }
    } else {
      $user_role_slug = 'update_gallery';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        $media_id = $_GET['id'];
        $this->globalReturnArr['gallery_data'] = $this->GlobalControllerInterfaceObj->fetch_Gallery_Item_Detail($media_id);
        //Fetch all category list
        $this->globalReturnArr['category_data'] = $this->GlobalControllerInterfaceObj->fetch_Single_Parent_Category($type);
      } else {
        $this->globalReturnArr['gallery_data'] = array();
        $this->globalReturnArr['category_data'] = array();
      }
    }

    return $this->globalReturnArr;
  }

  public function view_Category_Required_Data()
  {

    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission('view_category');

    $type = 'parent_category';

    //Fetching record status
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      $this->globalReturnArr['category_data'] = $this->GlobalControllerInterfaceObj->fetch_Parent_Category($record_status);
    } else {
      $this->globalReturnArr['category_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function view_Cities_Required_Data()
  {

    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission('manage_city_db');

    $type = 'cities';

    //Fetching record status
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      $this->globalReturnArr['city_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Cities($record_status);
    } else {
      $this->globalReturnArr['city_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function view_Enquiry_Required_Data()
  {

    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission('view_enquiry');

    $type = 'enquiry';

    //Fetching record status
    if (isset($_GET['record_status'])) {
      $dataArr['record_status'] = $_GET['record_status'];
    } else {
      $dataArr['record_status'] = 'active';
    }

    //Fetching record status
    if (isset($_GET['pageNo'])) {
      $dataArr['pageNo'] = $_GET['pageNo'];
    } else {
      $dataArr['pageNo'] = 1;
    }

    //Fetching record status
    if (isset($_GET['limit'])) {
      $dataArr['limit'] = $_GET['limit'];
    } else {
      $dataArr['limit'] = 200;
    }

    if (strlen($_GET['enquiry_type']) > 0) {
      $dataArr['enquiry_type'] = $_GET['enquiry_type'];
    }

    if ($_GET['course_id'] > 0) {
      $dataArr['course_id'] = $_GET['course_id'];
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Fetch all active course & franchise list
      $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
      $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];

      $this->globalReturnArr['enquiry_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Enquiry($dataArr);
    } else {
      $this->globalReturnArr['enquiry_data'] = array();
      $this->globalReturnArr['course_data']  = array();
    }

    return $this->globalReturnArr;
  }

  public function view_Email_Templates_Required_Data()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission('view_template');

    $type = 'email_template';
    //Fetching record status
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }
    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Call read global email template method
      $returnArr = $this->GlobalControllerInterfaceObj->fetch_Email_Templates($record_status);
      $this->globalReturnArr['email_template_data'] = $returnArr;
    } else {
      $this->globalReturnArr['email_template_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function view_News_Required_Data()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission('view_news');

    $type = 'news';
    //Fetching record status
    if (isset($_GET['record_status'])) {
      $dataArr['record_status'] = $_GET['record_status'];
    } else {
      $dataArr['record_status'] = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Call read global news method
      $returnArr = $this->GlobalControllerInterfaceObj->fetch_Global_News($dataArr);
      $this->globalReturnArr['news_data'] = $returnArr;
    } else {
      $this->globalReturnArr['news_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function fetchGlobalSingleFranchise($franchise_id)
  {
    return $this->GlobalControllerInterfaceObj->fetch_Global_Single_Franchise($franchise_id);
  }

  public function edit_Franchise_Profile_Data()
  {
    $franchise_id = $_SESSION['user_id'];

    $user_role_slug = 'manage_profile';
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {
      //Fetching franchise detail
      $this->globalReturnArr['frnachise_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Single_Franchise($franchise_id);
    } else {
      $this->globalReturnArr['frnachise_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_Franchise_Required_Data($fetch_type)
  {

    if ($_SESSION['user_type'] == 'franchise') {
      $franchise_id = $_SESSION['user_id'];
    } else {
      $franchise_id = $_GET['id'];
    }

    $type = 'franchise';

    if (isset($_GET['id'])) {
      $user_role_slug = 'update_franchise';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetching franchise detail
        $this->globalReturnArr['frnachise_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Single_Franchise($franchise_id);
      } else {
        $this->globalReturnArr['frnachise_data'] = array();
      }
    } else {
      $user_role_slug = 'create_franchise';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['frnachise_data'] = array();
    }

    $this->globalReturnArr['page_type'] = $type;

    return $this->globalReturnArr;
  }

  public function edit_Admin_Profile_Required_Data()
  {

    $user_id = $_SESSION['user_id'];
    $type = 'edit_profile';

    $this->globalReturnArr['page_type'] = $type;

    $user_role_slug = 'manage_profile';
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {

      if ($user_id > 0) {
        //Fetching franchise detail
        $this->globalReturnArr['profile_data'] = $this->GlobalControllerInterfaceObj->fetch_Admin_Profile_Data($user_id);
      } else {
        $this->globalReturnArr['profile_data'] = array();
      }
    } else {
      $this->globalReturnArr['profile_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function edit_Developer_Profile_Required_Data()
  {

    $user_id = $_SESSION['user_id'];
    $type = 'edit_profile';

    $this->globalReturnArr['page_type'] = $type;

    $user_role_slug = 'manage_profile';
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {

      if ($user_id > 0) {
        //Fetching franchise detail
        $this->globalReturnArr['profile_data'] = $this->GlobalControllerInterfaceObj->fetch_Developer_Profile_Data($user_id);
      } else {
        $this->globalReturnArr['profile_data'] = array();
      }
    } else {
      $this->globalReturnArr['profile_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_Course_Required_Data()
  {
    $course_id = $_GET['id'];
    $type = 'course';

    if (isset($_GET['id'])) {
      $user_role_slug = 'update_course';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetching course detail
        $this->globalReturnArr['course_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Single_Course($course_id);
      } else {
        $this->globalReturnArr['course_data'] = array();
      }
    } else {
      $user_role_slug = 'create_course';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['course_data'] = array();
    }

    $this->globalReturnArr['page_type'] = $type;

    return $this->globalReturnArr;
  }

  public function manage_Student_Admission_Data()
  {
    $student_id = $_GET['student_id'];
    $tmp_id = $_GET['tmp_id'];
    $type = 'student_admission';
    $receipt_category_type = 'receipt';
    $record_status = 'active';

    if ($_SESSION['user_type'] == 'franchise' && $_SESSION['owned_status'] == 'no') {

      $this->globalReturnArr['page_permission'] = false;
      $this->globalReturnArr['student_list'] = array();

      return $this->globalReturnArr;
    }

    if ($_GET['actionType'] == "manage_student") {

      if (isset($_GET['student_id'])) {
        $user_role_slug = 'update_student';
        //Check user permission for this section
        $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

        if ($this->globalReturnArr['page_permission']) {
          //Fetching batch detail
          $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Single_Student($student_id);
        } else {
          $this->globalReturnArr['student_data'] = array();
        }
      } elseif (!empty($_GET['tmp_id'])) {
        $user_role_slug = 'create_student';

        //Check user permission for this section
        $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

        //Fetching Temporary Student detail
        $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Tmp_Single_Student($tmp_id);
      } else {
        $user_role_slug = 'create_student';
        //Check user permission for this section
        $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);
        $this->globalReturnArr['student_data'] = array();
      }

      //Fetch all active course & franchise list
      $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
      
      $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
      $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];

      //Fetch all category list
      $this->globalReturnArr['category_data'] = $this->GlobalControllerInterfaceObj->fetch_Single_Parent_Category($receipt_category_type);
    } else {
      $user_role_slug = 'view_student';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        if ($_SESSION['user_type'] == 'franchise') {
          $dataArr['franchise_id'] = $_SESSION['user_id'];
        } else {
          $dataArr['franchise_id'] = null;
        }
        //Fetching batch detail
        $this->globalReturnArr['student_list'] = $this->GlobalControllerInterfaceObj->fetch_Fresh_Students($dataArr);
      } else {
        $this->globalReturnArr['student_list'] = array();
      }
    }

    $this->globalReturnArr['page_type'] = $type;

    return $this->globalReturnArr;
  }

  public function manage_Temp_Student_Data()
  {

    $tmp_id = $_GET['tmp_id'];
    $type = 'student_admission';
    $record_status = 'active';

    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    $dataArr['record_status'] = $record_status;

    if ($_SESSION['user_type'] == 'franchise' && $_SESSION['owned_status'] == 'no') {

      $this->globalReturnArr['page_permission'] = false;
      $this->globalReturnArr['student_list'] = array();

      return $this->globalReturnArr;
    }

    if ($_GET['actionType'] == "manage_student") {

      if (isset($_GET['tmp_id'])) {
        $user_role_slug = 'update_student';
        //Check user permission for this section
        $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

        if ($this->globalReturnArr['page_permission']) {
          //Fetching Temporary Student detail
          $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Tmp_Single_Student($tmp_id);
        } else {
          $this->globalReturnArr['student_data'] = array();
        }
      } else {
        $user_role_slug = 'create_student';
        //Check user permission for this section
        $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);
        $this->globalReturnArr['student_data'] = array();
      }

      //Fetch all active course & franchise list
      $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
      
      $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
      $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];
    } else {
      $user_role_slug = 'view_student';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        if ($_SESSION['user_type'] == 'franchise') {
          $dataArr['franchise_id'] = $_SESSION['user_id'];
        } else {
          $dataArr['franchise_id'] = null;
        }

        if ($_GET['course_id'] > 0) {
          $dataArr['course_id'] = $_GET['course_id'];
        }

        if ($_SESSION['user_type'] == 'franchise') {
          $dataArr['franchise_id'] = $_SESSION['user_id'];
        } elseif ($_GET['franchise_id'] > 0) {
          $dataArr['franchise_id'] = $_GET['franchise_id'];
        }

        if (!empty($_GET['search_string'])) {
          $dataArr['search_string'] = $_GET['search_string'];
        }

        if ($_GET['created'] > 0) {
          $created = $_GET['created'];
          $created = str_replace('/', '-', $created);
          $dataArr['created'] = date('Y-m-d', strtotime($created));
        }

        if (strlen($_GET['search_start']) > 0) {
          $search_start = $_GET['search_start'];
          $search_start = str_replace('/', '-', $search_start);
          $dataArr['search_start'] = date('Y-m-d', strtotime($search_start));
        }

        if (strlen($_GET['search_end']) > 0) {
          $search_end = $_GET['search_end'];
          $search_end = str_replace('/', '-', $search_end);
          $dataArr['search_end'] = date('Y-m-d', strtotime($search_end));
        }

        $_get_conversion_status = $_GET['conversion_status'];
        $_get_verified_status = $_GET['verified_status'];

        if (!empty($_get_verified_status)) {
          if ($_GET['verified_status'] == 'y') {
            $dataArr['verified_status'] = '1';
          } else {
            $dataArr['verified_status'] = '0';
          }
        } else {
          $dataArr['verified_status'] = 'null';
        }

        if (!empty($_get_conversion_status)) {
          if ($_GET['conversion_status'] == 'y') {
            $dataArr['conversion_status'] = '1';
          } else {
            $dataArr['conversion_status'] = '0';
          }
        } else {

          if ($_SESSION['user_type'] == 'franchise') {

            if (!empty($_GET['record_status']) || !empty($_get_verified_status) || !empty($_GET['course_id']) || !empty($_GET['search_string']) || !empty($_GET['created']) || !empty($_GET['search_start']) || !empty($_GET['search_end'])) {

              $dataArr['conversion_status'] = 'null';
            } else {
              $dataArr['conversion_status'] = '0';
            }
          } else {
            if (!empty($_GET['record_status']) || !empty($_get_verified_status) || !empty($_GET['course_id']) || !empty($_GET['franchise_id']) || !empty($_GET['search_string']) || !empty($_GET['created']) || !empty($_GET['search_start']) || !empty($_GET['search_end'])) {

              $dataArr['conversion_status'] = 'null';
            } else {
              $dataArr['conversion_status'] = '0';
            }
          }
        }

        //Fetch all active course & franchise list
        $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
        
        $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
        $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];

        if (isset($_GET['pageNo'])) {
          $dataArr['pageNo'] = $_GET['pageNo'];
        } else {
          $dataArr['pageNo'] = 1;
        }
  
        $dataArr['limit'] = 20;

        //Fetching batch detail
        $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Tmp_Students($dataArr);
      } else {
        $this->globalReturnArr['student_data'] = array();
      }
    }

    $this->globalReturnArr['page_type'] = $type;

    return $this->globalReturnArr;
  }

  public function fetch_Student_Admission_Receipt($student_id)
  {
    $receiptDetails = $this->GlobalControllerInterfaceObj->fetch_Student_Admission_Receipt($student_id);
    return $receiptDetails;
  }


  public function manage_Student_Required_Data()
  {

    $student_id = $_GET['id'];

    $type = 'student';
    $record_status = 'active';

    if (!empty($student_id)) {
      $user_role_slug = 'update_student';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetching franchise detail
        $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Single_Student($student_id);
        $batchParamArr['franchise_id'] = $this->globalReturnArr['student_data']->franchise_id;
        $batchParamArr['course_id'] = $this->globalReturnArr['student_data']->course_id;

        if ($_SESSION['user_type'] == "franchise") {
          if ($this->globalReturnArr['student_data']->franchise_id != $_SESSION['user_id']) {
            $this->globalReturnArr['page_permission'] = false;
          }
        }
      } else {
        $this->globalReturnArr['student_data'] = array();
      }
    } else {
      $user_role_slug = 'create_student';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['student_data'] = array();
    }

    //Fetch all active course & franchise list
    $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
      
    $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
    $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];

    $this->globalReturnArr['page_type'] = $type;

    return $this->globalReturnArr;
  }

  

  public function view_Due_Students_Data(){

    $user_role_slug = 'view_due_students';
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {
      //Construting Query Params Array
      if (isset($_GET['record_status'])) {
        $dataArr['record_status'] = $_GET['record_status'];
      } else {
        $dataArr['record_status'] = 'active';
      }

      if (!empty($_GET['stu_id'])) {
        $dataArr['student_id'] = $_GET['stu_id'];
      } else {
        $dataArr['student_id'] = null;
      }
  
      if ($_GET['course_id'] > 0) {
        $dataArr['course_id'] = $_GET['course_id'];
      }
  
      if ($_SESSION['user_type'] == 'franchise') {
        $dataArr['franchise_id'] = $_SESSION['user_id'];
      } else {
        if ($_GET['franchise_id'] > 0) {
          $dataArr['franchise_id'] = $_GET['franchise_id'];
        }
      }
  
      if (isset($_GET['pageNo'])) {
        $dataArr['pageNo'] = $_GET['pageNo'];
      } else {
        $dataArr['pageNo'] = 1;
      }
  
      $dataArr['limit'] = 20;
      //Fetch all active course & franchise list
      $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
      
      $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
      $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];

      if (!isset($_GET['fetchType']) && $_GET['fetchType'] == "dueList") {
         //Student details
        $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Due_Students_Data($dataArr);
      }else{
         //Student details
        $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Updated_Markup_Students_Data($dataArr);
      }

    }else{
       //Fetch all active course & franchise list
       $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();

      $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
      $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];
      //Student details
      $this->globalReturnArr['student_data'] = [];
    }

    // print"<pre>";
    // print_r($this->globalReturnArr['student_data']);
    // print"</pre>";
    // exit;

    return $this->globalReturnArr;
  }

  public function manage_Receipt_Required_Data()
  {
    $student_id = $_GET['stu_id'];
    $dataArr['student_id'] = $student_id;
    $type = 'receipt';

    $this->globalReturnArr['page_type'] = $type;

    if (!empty($_GET['actionType'])) {

      //Fetch all category list
      $this->globalReturnArr['category_data'] = $this->GlobalControllerInterfaceObj->fetch_Single_Parent_Category($type);

      if ($_GET['actionType'] == "create") {
        $user_role_slug = 'create_receipt';
        //Check user permission for this section
        $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

        if ($this->globalReturnArr['page_permission']) {
          $this->globalReturnArr['receipt_data'] = array();
          //Student details
          $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Single_Student($student_id);
        } else {
          $this->globalReturnArr['receipt_data'] = array();
          $this->globalReturnArr['student_data'] = array();
        }
      } else {
        $user_role_slug = 'update_receipt';
        //Check user permission for this section
        $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

        if ($this->globalReturnArr['page_permission']) {
          $receipt_id = $_GET['rcpt_id'];

          //Receipt details
          $this->globalReturnArr['receipt_data'] = $this->GlobalControllerInterfaceObj->fetch_Single_Receipt_Data($receipt_id);

          $stu_id = $this->globalReturnArr['receipt_data']->stu_id;

          //Student details
          $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Single_Student($stu_id);
        } else {
          $this->globalReturnArr['receipt_data'] = array();
        }
      }
    } else {
      //Fetching record status
      if (isset($_GET['record_status'])) {
        $dataArr['record_status'] = $_GET['record_status'];
      } else {
        $dataArr['record_status'] = 'active';
      }

      if (!empty($_GET['verified_status'])) {
        $dataArr['verified_status'] = $_GET['verified_status'];
      }

      if (!empty($_GET['stu_id'])) {
        $dataArr['student_id'] = $_GET['stu_id'];
      } else {
        $dataArr['student_id'] = null;
      }

      if ($_GET['course_id'] > 0) {
        $dataArr['course_id'] = $_GET['course_id'];
      }

      if ($_SESSION['user_type'] == 'franchise') {
        $dataArr['franchise_id'] = $_SESSION['user_id'];
      } else {
        if ($_GET['franchise_id'] > 0) {
          $dataArr['franchise_id'] = $_GET['franchise_id'];
        }
      }

      if ($_GET['created'] > 0) {
        $created = $_GET['created'];
        $created = str_replace('/', '-', $created);
        $dataArr['created'] = date('Y-m-d', strtotime($created));
      }

      if (strlen($_GET['receipt_season_start']) > 0) {
        $receipt_season_start = $_GET['receipt_season_start'];
        $receipt_season_start = str_replace('/', '-', $receipt_season_start);
        $dataArr['receipt_season_start'] = date('Y-m-d', strtotime($receipt_season_start));
      }

      if (strlen($_GET['receipt_season_end']) > 0) {
        $receipt_season_end = $_GET['receipt_season_end'];
        $receipt_season_end = str_replace('/', '-', $receipt_season_end);
        $dataArr['receipt_season_end'] = date('Y-m-d', strtotime($receipt_season_end));
      }

      if (isset($_GET['pageNo'])) {
        $dataArr['pageNo'] = $_GET['pageNo'];
      } else {
        $dataArr['pageNo'] = 1;
      }

      $dataArr['limit'] = 20;

      $user_role_slug = 'view_receipt';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {

        if (!empty($student_id)) {
          //Student details
          $this->globalReturnArr['student_data'] = $this->GlobalControllerInterfaceObj->fetch_Student_Receipt_Summary($dataArr);

          //Fetch all receipt list
          $this->globalReturnArr['receipt_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Receipt($dataArr);
        } else {

          if ($_GET['record_status'] == "blocked" || !empty($_GET['course_id']) || !empty($_GET['franchise_id']) || !empty($_GET['created']) || !empty($_GET['receipt_season_start']) || !empty($_GET['receipt_season_end']) || !empty($_GET['verified_status'])) {
            //Fetch all receipt list
            $this->globalReturnArr['receipt_data'] = $this->GlobalControllerInterfaceObj->fetch_Global_Receipt($dataArr);
          } else {
            $this->globalReturnArr['receipt_data'] = array();
          }

          //Student details
          $this->globalReturnArr['student_data'] = array();
        }

        //Fetch all active course & franchise list
        $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
      
        $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
        $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];
      } else {
        //Student details
        $this->globalReturnArr['student_data'] = array();

        //Fetch all active course & franchise list
        $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
        
        $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
        $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];

        $this->globalReturnArr['receipt_data'] = array();
      }
    }

    return $this->globalReturnArr;
  }

  public function manage_Exam_Required_Data()
  {
    $type = 'exams';
    $exam_id = $_GET['id'];

    //Fetching record status
    $record_status = 'active';

    $this->globalReturnArr['page_type'] = $type;
    if ($exam_id > 0) {
      $user_role_slug = 'update_exam';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch news detail
        $this->globalReturnArr['exam_details'] = $this->GlobalControllerInterfaceObj->fetch_Student_Exam_Detail($exam_id);
       
        //Fetch all active course & franchise list
        $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
        
        $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
        $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];
      } else {
        $this->globalReturnArr['exam_details'] = array();
      }
    } else {
      $user_role_slug = 'create_exam';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['exam_details'] = array();
      
      //Fetch all active course & franchise list
      $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
      
      $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
      $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];
    }

    return $this->globalReturnArr;
  }

  public function manage_Exam_Questions_Required_Data()
  {
    $type = 'exams';
    $exam_id = $_GET['exm_id'];

    //Fetching record status
    $record_status = 'active';

    $this->globalReturnArr['page_type'] = $type;
    if ($exam_id > 0) {
      $user_role_slug = 'update_exam';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch news detail
        $this->globalReturnArr['exam_details'] = $this->GlobalControllerInterfaceObj->fetch_Student_Exam_Detail($exam_id);

        //Fetch news detail
        $this->globalReturnArr['questions'] = $this->GlobalControllerInterfaceObj->fetch_Exam_Questions($exam_id);
      } else {
        $this->globalReturnArr['questions'] = array();
      }
    } else {
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = false;
      $this->globalReturnArr['questions'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_Start_Exam()
  {
    $type = 'exams';
    $exam_id = $_GET['exm_id'];

    //Fetching record status
    $record_status = 'active';

    $this->globalReturnArr['page_type'] = $type;
    if ($exam_id > 0) {
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = true;

      if ($this->globalReturnArr['page_permission']) {
        //Fetch news detail
        $this->globalReturnArr['exam_details'] = $this->GlobalControllerInterfaceObj->fetch_Student_Exam_Detail($exam_id);

        //Fetch news detail
        $this->globalReturnArr['questions'] = $this->GlobalControllerInterfaceObj->fetch_Exam_Questions($exam_id);
      } else {
        $this->globalReturnArr['questions'] = array();
      }
    } else {
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = false;
      $this->globalReturnArr['questions'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_Email_Template_Required_Data()
  {
    $type = 'email_template';
    $template_id = $_GET['id'];

    $this->globalReturnArr['page_type'] = $type;
    if ($template_id > 0) {
      $user_role_slug = 'update_template';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch email template detail
        $this->globalReturnArr['email_template_details'] = $this->GlobalControllerInterfaceObj->fetch_Global_Email_Template_Detail($template_id);
      } else {
        $this->globalReturnArr['email_template_details'] = array();
      }
    } else {
      $user_role_slug = 'create_template';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['email_template_details'] = array();
    }
    return $this->globalReturnArr;
  }

  public function manage_News_Required_Data()
  {
    $type = 'email_template';
    $news_id = $_GET['id'];

    $this->globalReturnArr['page_type'] = $type;
    if ($news_id > 0) {
      $user_role_slug = 'update_news';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch news detail
        $this->globalReturnArr['news_details'] = $this->GlobalControllerInterfaceObj->fetch_Global_News_Detail($news_id);
      } else {
        $this->globalReturnArr['news_details'] = array();
      }
    } else {
      $user_role_slug = 'create_news';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['news_details'] = array();
    }
    return $this->globalReturnArr;
  }

  public function remove_File_From_Server($type, $row_id)
  {
    $resultArr = $this->GlobalControllerInterfaceObj->fetch_Global_Single_Data($type, $row_id);

    switch ($type) {
      case 'franchise':
        $featuredImageDir = USER_UPLOAD_DIR . $type . '/' . $resultArr->fran_image;
        $franchisePdfDir = USER_UPLOAD_DIR . $type . '/' . $resultArr->fran_pdf_name;
        //unlinking files
        if ($featuredImageDir && file_exists($featuredImageDir)) {
          unlink($featuredImageDir);
        }

        if ($franchisePdfDir && file_exists($franchisePdfDir)) {
          unlink($franchisePdfDir);
        }
        break;

      case 'course':
        $featuredImageDir = USER_UPLOAD_DIR . $type . '/' . $resultArr->course_thumbnail;
        $coursePdfDir = USER_UPLOAD_DIR . $type . '/' . $resultArr->course_pdf;
        //Removing file from server
        if ($featuredImageDir && file_exists($featuredImageDir)) {
          unlink($featuredImageDir);
        }

        if ($coursePdfDir && file_exists($coursePdfDir)) {
          unlink($coursePdfDir);
        }
        break;

      case 'student':
        $featuredImageDir = USER_UPLOAD_DIR . $type . '/' . $resultArr->image_file_name;
        //unlinking files
        if ($featuredImageDir && file_exists($featuredImageDir)) {
          unlink($featuredImageDir);
        }
        break;

      case 'student_receipts':
        $receiptPdfDir = USER_UPLOAD_DIR . 'receipt/' . $resultArr->receipt_pdf;
        //unlinking files
        if ($receiptPdfDir && file_exists($receiptPdfDir)) {
          unlink($receiptPdfDir);
        }
        break;

      case 'gallery':
        $featuredImageDir = USER_UPLOAD_DIR . $type . '/' . $resultArr->content;
        //unlinking files
        if ($featuredImageDir && file_exists($featuredImageDir)) {
          unlink($featuredImageDir);
        }
        break;

      case 'home_sliders':
        $featuredImageDir = USER_UPLOAD_DIR . $type . '/' . $resultArr->banner_image;
        //unlinking files
        if ($featuredImageDir && file_exists($featuredImageDir)) {
          unlink($featuredImageDir);
        }
        break;

      case 'news':
        if ($featuredImageDir && file_exists($featuredImageDir)) {
          $newsPdfDir = USER_UPLOAD_DIR . $type . '/' . $resultArr->optional_pdf;
        }
        //Removing file from server
        unlink($newsPdfDir);
        break;
    }
    return true;
  }

  public function delete_Global_File($type, $dir, $file_type, $row_id)
  {

    $GlobalInterfaceObj = new GlobalInterfaceModel();
    $resultArr = $GlobalInterfaceObj->fetch_Global_Single_Data($type, $row_id);

    $fileDir = USER_UPLOAD_DIR . $dir . '/' . $resultArr->$file_type;

    if ($fileDir && file_exists($fileDir)) {
      unlink($fileDir);
    }
    //print $featuredImageDir."<br>";
    return true;
  }

  public function config_Required_Upload_Dir()
  {
    $uploadDirArr = array('article', 'brochure', 'campus', 'default', 'event', 'gallery', 'institute');
    if (!is_dir(USER_UPLOAD_DIR)) {
      mkdir(USER_UPLOAD_DIR);
      foreach ($uploadDirArr as $index => $dir) {
        if (!is_dir(USER_UPLOAD_DIR . $dir)) {
          mkdir(USER_UPLOAD_DIR . $dir);
          /*$uploadDir = strtoupper($dir).'_UPLOAD_DIR';
            $uploadUrl = strtoupper($dir).'_UPLOAD_URL';
            $_SESSION['UPLOAD'][$uploadDir] = USER_UPLOAD_DIR.$dir.'/';
            $_SESSION['UPLOAD'][$uploadUrl] = USER_UPLOAD_URL.$dir.'/';*/
        }
      }
    }
  }

  public function checkSlugAvailibility($type, $field, $slug)
  {
    $returnArr = $this->GlobalControllerInterfaceObj->check_Slug_Availibility($type, $field, $slug);
    return $returnArr;
  }

  public function get_Gloabl_Content_Excerpt($content, $length)
  {

    $end = '...&nbsp;';

    $content = strip_tags($content);

    if (strlen($content) > $length) {

      // truncate string
      $stringCut = substr($content, 0, $length);

      // make sure it ends in a word so assassinate doesn't become ass...
      $excerpt = substr($stringCut, 0, strrpos($stringCut, ' ')) . $end;
    } else {
      $excerpt = strip_tags($content);
    }

    return $excerpt;
  }

  public function findTimeDiff($dateFrom)
  {

    $today = date('Y-m-d H:i:s');
    $dateTo = new DateTime($today);
    $dateFrom = new DateTime($dateFrom);
    $intervalObj = $dateFrom->diff($dateTo);

    return $intervalObj->format('%y years %m months and %d days and %h hours and %m minutes and %s seconds');
  }

  public function seoUrlStructure($string, $type)
  {

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

  public function shuffle_assoc($list)
  {
    if (!is_array($list)) return $list;

    $keys = array_keys($list);
    shuffle($keys);
    $random = array();
    foreach ($keys as $key) {
      $random[$key] = $list[$key];
    }
    return $random;
  }

  public function getDriveIdFromUrl($url)
  {
    preg_match('~/d/\K[^/]+(?=/)~', $url, $result);
    return $result[0];
  }

  public function encodeTextArea($data)
  {
    $data = trim($data);
    $data = addslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  public function decodeTextArea($data)
  {
    $data = stripslashes($data);
    return $data;
  }

  public function fetchEmailTemplateDetail($email_code)
  {

    $campaignDataArr = $this->GlobalControllerInterfaceObj->fetch_Email_Template_Detail($email_code);

    return $campaignDataArr;
  }

  public function manage_Home_Sliders_Required_Data()
  {

    $type = 'home_sliders';
    $this->globalReturnArr['page_type'] = $type;

    //Fetching record status
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    if (isset($_GET['slider_type'])) {
      $slider_type = $_GET['slider_type'];
    } else {
      $slider_type = null;
    }

    if (empty($_GET['type'])) {
      $user_role_slug = 'manage_home_slider';

      $paramArr['record_status'] = $record_status;
      $paramArr['slider_type'] = $slider_type;

      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch gallery item
        $this->globalReturnArr['slider_data'] = $this->GlobalControllerInterfaceObj->fetch_Slider_Arr($paramArr);
      } else {
        $this->globalReturnArr['slider_data'] = array();
      }
    } else if ($_GET['type'] == 'add') {
      $user_role_slug = 'manage_home_slider';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);
    } else {
      $user_role_slug = 'manage_home_slider';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        $slider_id = $_GET['id'];
        $this->globalReturnArr['slider_data'] = $this->GlobalControllerInterfaceObj->fetch_Slider_Detail($slider_id);
        //Fetch all category list
      } else {
        $this->globalReturnArr['slider_data'] = array();
      }
    }

    return $this->globalReturnArr;
  }

  public function manage_Site_Setting_Required_Data()
  {
    $type = 'site_setting';

    $this->globalReturnArr['page_type'] = $type;

    $user_role_slug = 'update_site_setting';
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {
      //Fetch site setting detail
      $this->globalReturnArr['site_settings'] = $this->GlobalControllerInterfaceObj->fetch_Global_Site_Setting_Detail();
    } else {
      $this->globalReturnArr['site_settings'] = array();
    }


    return $this->globalReturnArr;
  }

  public function fetchSiteSettingDetail()
  {

    $siteSettingArr = $this->GlobalControllerInterfaceObj->fetch_Global_Site_Setting_Detail();

    return $siteSettingArr;
  }

  public function configure_email_body($emailParam)
  {
    /*print"<pre>";
        print_r($emailParam);
        print"</pre>";*/

    $emailReturnParamArr = array();
    $emailReturnParamArr['email_code'] = $emailParam['email_code'];
    $emailReturnParamArr['receiver_name'] = $emailParam['receiver_name'];
    $emailReturnParamArr['receiver_email'] = $emailParam['receiver_email'];
    $emailReturnParamArr['site_addr'] = FRONT_SITE_URL;
    //fetching site setting detail
    $siteSettingArr = $this->fetchSiteSettingDetail();
    $emailReturnParamArr['company_name'] = $siteSettingArr->title;

    //configure company logo        
    $emailReturnParamArr['company_logo'] = USER_UPLOAD_URL . 'others/' . $siteSettingArr->logo;
    $emailReturnParamArr['company_signature'] = USER_UPLOAD_URL . 'others/' . $siteSettingArr->signature;

    $emailReturnParamArr['company_contact_email'] = $siteSettingArr->contact_email;
    $emailReturnParamArr['company_contact_no'] = $siteSettingArr->phone;
    $emailReturnParamArr['company_address'] = $siteSettingArr->address;

    //fetching required email template detail
    $emailTemplateArr = $this->fetchEmailTemplateDetail($emailReturnParamArr['email_code']);
    $emailReturnParamArr['email_subject'] = $emailTemplateArr->subject;
    $emailReturnParamArr['sender_name'] = $emailTemplateArr->from_name;
    $emailReturnParamArr['sender_email'] = $emailTemplateArr->from_email;
    $emailReturnParamArr['cc_email'] = $emailTemplateArr->cc_email;
    $emailReturnParamArr['email_template'] = $emailTemplateArr->template;

    switch ($emailReturnParamArr['email_code']) {

      case 'student-receipt-invoice':
        //create a list of the variables to be swapped in the html template
        $swap_var = array(
          "{SITE_ADDR}" => $emailReturnParamArr['site_addr'],
          "{COMPANY_NAME}" => $emailReturnParamArr['company_name'],
          "{COMPANY_EMAIL}" => $emailReturnParamArr['company_contact_email'],
          "{COMPANY_CONTACT_NO}" => $emailReturnParamArr['company_contact_no'],
          "{COMPANY_ADDRESS}" => $emailReturnParamArr['company_address'],
          "{COMPANY_LOGO}" => $emailReturnParamArr['company_logo'],
          "{COMPANY_SIGNATURE}" => $emailReturnParamArr['company_signature'],
          "{EMAIL_TITLE}" => $emailReturnParamArr['email_subject'],
          "{INVOICE_DATE}" => $emailParam['invoice_date'],
          "{STUDENT_NAME}" => $emailReturnParamArr['receiver_name'],
          "{STUDENT_EMAIL}" => $emailParam['receiver_email'],
          "{STUDENT_CONTACT}" => $emailParam['stu_phone'],
          "{STUDENT_ID}" => $emailParam['stu_id'],
          "{COURSE}" => $emailParam['course'],
          "{FRANCHISE}" => $emailParam['franchise'],
          "{RECEIPT_ID}" => $emailParam['receipt_id'],
          "{RECEIPT_SEASON}" => $emailParam['receipt_season'],
          "{RECEIPT_STATUS}" => $emailParam['receipt_status'],
          "{RECEIPT_AMOUNT}" => $emailParam['receipt_amount']
        );
        break;

      default:
        # code...
        break;
    }

    //search and replace for predefined variables, like SITE_ADDR, {NAME}, {lOGO}, {CUSTOM_URL} etc
    foreach (array_keys($swap_var) as $key) {
      if (strlen($key) > 2 && trim($swap_var[$key]) != '') {
        $emailReturnParamArr['email_template'] = str_replace($key, $swap_var[$key], $emailReturnParamArr['email_template']);
      }
    }

    if ($emailReturnParamArr['email_code'] == "student-receipt-invoice") {

      if ($emailParam['attachment_path'] !== null) {
        $emailReturnParamArr['attachment_path'] = $emailParam['attachment_path'];
        $emailReturnParamArr['attachment_type'] = "local";
      } else {

        $file_upload_dir =  USER_UPLOAD_DIR . 'runtime_upload/' . "Receipt_" . $emailParam['receipt_id'] . '.pdf';

        $html_code = $emailReturnParamArr['email_template'];

        $dompdf = new Pdf();
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->load_html($html_code);
        $dompdf->render();
        $file = $dompdf->output();
        file_put_contents($file_upload_dir, $file);

        $emailReturnParamArr['attachment_path'] = $file_upload_dir;
        $emailReturnParamArr['attachment_type'] = "dynamic";
      }
    }
    //echo $emailReturnParamArr['email_template'];exit;
    return $emailReturnParamArr;
  }

  public function php_mailer_send_mail($paramArr)
  {
    //print_r($paramArr);exit;
    $emailReturnArr = $this->configure_email_body($paramArr);
    //print_r($emailReturnArr);exit;

    //Collecting necessary variables to configure send mail
    $fromEmail = $emailReturnArr['sender_email'];
    $fromName  = $emailReturnArr['sender_name'];
    $toEmail   = $emailReturnArr['receiver_email'];
    $toName    = $emailReturnArr['receiver_name'];

    $ccEmail   = $emailReturnArr['cc_email'];

    if (array_key_exists('attachment_path', $emailReturnArr)) {
      $filePath  = $emailReturnArr['attachment_path'];
    }
    $email_subject = $emailReturnArr['email_subject'];
    $body = $emailReturnArr['email_template'];

    //echo $body."<br>".$filePath;exit;
    //Including php mailer class
    //require_once(ROOTPATH.'/library/PHP_MAILER/class.phpmailer.php');

    $mail = new PHPMailer;
    $mail->IsSMTP();                                //Sets Mailer to send message using SMTP
    //$mail->SMTPDebug = 1;                           //debugging: 1 = errors and messages, 2 = messages only
    $mail->Host = 'smtp.gmail.com';                 //Sets the SMTP hosts of your Email hosting, this for Godaddy
    $mail->Port = '465';                            //Sets the default SMTP server port

    $mail->SMTPSecure = 'ssl';                      //Sets connection prefix. Options are "", "ssl" or "tls"
    //Whether to use SMTP authentication
    $mail->SMTPAuth = true;
    //Sets SMTP authentication. Utilizes the Username and Password variables
    $mail->Username = 'testsmtpsentmail@gmail.com';    //Sets SMTP username
    $mail->Password = 'dowlpeberazpiqbk';                    //Sets SMTP password //"dowlpeberazpiqbk"
    $mail->From = $fromEmail;                       //Sets the From email address for the message
    $mail->FromName = $fromName;                    //Sets the From name of the message
    $mail->AddAddress($toEmail, $toName);           //Adds a "To" address
    $mail->addCC($ccEmail, 'Admin');                         //Add cc
    $mail->WordWrap = 50;                           //Sets word wrapping on the body of the message to a given number of characters
    $mail->IsHTML(true);                            //Sets message type to HTML 
    $mail->Subject = $email_subject;                //Sets the Subject of the message
    $mail->Body = $body;
    if (array_key_exists('attachment_path', $emailReturnArr)) {
      $mail->AddAttachment($filePath);              //Adds an attachment from a path on the filesystem
      //$mail->addStringAttachment($filePath,"Local Attachment");
    }
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
    if ($mail->Send()) {
      if ($emailReturnArr['attachment_type'] == 'dynamic') {
        unlink($filePath);   //Removing dynamically generated file
      }
      return true;
    } else {
      echo "Mailer Error: " . $mail->ErrorInfo;
      return false;
    }
  }

  public function createStudentReceiptPdf($receipt_id)
  {

    $user_role_slug = 'create_receipt';

    //check action permission        
    $checkActionPermission = $this->checkUserRolePermission($user_role_slug, "hard");

    if ($checkActionPermission) {

      //Fetch reecipt detail
      $receiptDetailArr = $this->GlobalControllerInterfaceObj->fetch_Single_Receipt_Data($receipt_id);
      $studentDetails = $this->GlobalControllerInterfaceObj->fetch_Global_Single_Student($receiptDetailArr->stu_id, $receiptDetailArr->created_at);

      $file_upload_dir =  USER_UPLOAD_DIR . 'runtime_upload/' . "Receipt_" . $receiptDetailArr->receipt_id . '.pdf';
      $file_url = USER_UPLOAD_URL . 'runtime_upload/' . "Receipt_" . $receiptDetailArr->receipt_id . '.pdf';

      if (!file_exists($file_upload_dir)) {

        $pdfParamArr = array();

        if ($receiptDetailArr->category == 'Admission Fees') {
          $pdfParamArr['email_code'] = 'student-admission-receipt-invoice';
        } elseif ($receiptDetailArr->category == 'Tuition Fees') {
          $pdfParamArr['email_code'] = 'student-monthly-receipt-invoice';
        } else {
          $pdfParamArr['email_code'] = 'student-other-receipt-invoice';
        }

        $pdfParamArr['receiver_name'] = $studentDetails->stu_name;
        $pdfParamArr['receiver_email'] = $studentDetails->stu_email;
        $pdfParamArr['student_contact'] = $studentDetails->stu_phone;
        $pdfParamArr['site_addr'] = FRONT_SITE_URL;
        //fetching site setting detail
        $siteSettingArr = $this->fetchSiteSettingDetail();
        $pdfParamArr['company_name'] = $siteSettingArr->title;

        //configure company logo        
        $pdfParamArr['company_logo'] = USER_UPLOAD_URL . 'others/' . $siteSettingArr->logo;
        $pdfParamArr['company_signature'] = USER_UPLOAD_URL . 'others/' . $siteSettingArr->signature;

        $pdfParamArr['company_contact_email'] = $studentDetails->fran_email; //$siteSettingArr->contact_email;
        $pdfParamArr['company_contact_no'] = $studentDetails->fran_phone; //$siteSettingArr->phone;
        $pdfParamArr['company_address'] = $studentDetails->fran_address; //$siteSettingArr->address;

        //fetching required email template detail
        $emailTemplateArr = $this->fetchEmailTemplateDetail($pdfParamArr['email_code']);
        $pdfParamArr['email_subject'] = $emailTemplateArr->subject;
        $pdfParamArr['sender_name'] = $emailTemplateArr->from_name;
        $pdfParamArr['sender_email'] = $emailTemplateArr->from_email;
        $pdfParamArr['cc_email'] = $emailTemplateArr->cc_email;
        $pdfParamArr['email_template'] = $emailTemplateArr->template;

        if (!empty($studentDetails->stu_course_fees)) {
          $courseFees = $studentDetails->stu_course_fees;
        } else {
          $courseFees = $studentDetails->course_fees;
        }

        if (!empty($receiptDetailArr->late_fine)) {
          $late_fees = $receiptDetailArr->late_fine;
        } else {
          $late_fees = (int)0;
        }

        if (!empty($receiptDetailArr->extra_fees)) {
          $extra_fees = $receiptDetailArr->extra_fees;
        } else {
          $extra_fees = (int)0;
        }

        if (!empty($studentDetails->stu_course_discount)) {
          $discount_fees = $studentDetails->stu_course_discount;
        } else {
          $discount_fees = (int)0;
        }

        if (!empty($studentDetails->advanced_fees)) {
          $advanceFeesTitle = "Advance Fees submited on " . date('jS F, Y', strtotime($studentDetails->advance_fees_date));
          $advanced_fees = $studentDetails->advanced_fees;
        } else {
          $advanceFeesTitle = "No advance fees is available!";
          $advanced_fees = (int)0;
        }

        $receiptAmount = round((int)$receiptDetailArr->receipt_amount + (int)$late_fees + (int)$extra_fees);

        //$totalFeesCleared = round((int)$receiptDetailArr->course_fees_paid + (int)$receiptDetailArr->receipt_amount);
        $dueAmount = round((int)$courseFees - (int)$studentDetails->fees_paid_before_dr - (int)$studentDetails->course_fees_paid - (int)$discount_fees - (int)$advanced_fees);

        $receiptTitle = "Receipt of " . date('jS F, Y', strtotime(date('Y-m-d')));

        $net_course_fees = (int)$courseFees - (int)$studentDetails->stu_course_discount;
        $total_course_fees_paid = (int)$studentDetails->course_fees_paid + (int)$advanced_fees;

        //create a list of the variables to be swapped in the html template
        $swap_var = array(
          "{SITE_ADDR}" => !empty($pdfParamArr['site_addr']) ? $pdfParamArr['site_addr'] : "Not available!",
          "{COMPANY_NAME}" => !empty($pdfParamArr['company_name']) ? $pdfParamArr['company_name'] : "Not available!",
          "{COMPANY_EMAIL}" => !empty($pdfParamArr['company_contact_email']) ? $pdfParamArr['company_contact_email'] : "Not available!",
          "{CONTACT_NO}" => !empty($pdfParamArr['company_contact_no']) ? $pdfParamArr['company_contact_no'] : "Not available!",
          "{COMPANY_ADDRESS}" => !empty($pdfParamArr['company_address']) ? $pdfParamArr['company_address'] : "Not available!",
          "{COMPANY_LOGO}" => $pdfParamArr['company_logo'],
          "{COMPANY_SIGNATURE}" => $pdfParamArr['company_signature'],
          "{EMAIL_TITLE}" => !empty($pdfParamArr['email_subject']) ? $pdfParamArr['email_subject'] : "Not available!",
          "{INVOICE_DATE}" => date('jS F, Y', strtotime($receiptDetailArr->created_at)),
          "{STUDENT_NAME}" => !empty($pdfParamArr['receiver_name']) ? $pdfParamArr['receiver_name'] : "Not available!",
          "{STUDENT_CONTACT}" => !empty($pdfParamArr['student_contact']) ? $pdfParamArr['student_contact'] : "Not available!",
          "{STUDENT_ID}" => $studentDetails->stu_id,
          "{CONVERSION_STATUS}" => $studentDetails->conversion_status == 1 ? 'Converted' : 'Recent',
          "{COURSE}" => $studentDetails->course_title,
          "{FRANCHISE}" => $studentDetails->center_name,
          "{RECEIPT_TITLE}" => $receiptTitle,
          "{RECEIPT_ID}" => $receiptDetailArr->receipt_id,
          "{RECEIPT_TYPE}" => ucfirst($receiptDetailArr->category),
          "{RECEIPT_AMOUNT}" => !empty($receiptDetailArr->receipt_amount) ? $receiptDetailArr->receipt_amount : "Not available!",
          "{LATE_FEES}" => !empty($late_fees) ? $late_fees : "Not available!",
          "{ADVANCE_FEES_TITLE}" => $advanceFeesTitle,
          "{ADVANCE_FEES}" => $advanced_fees,
          "{FEES_PAID_BFR_DR}" => !empty($studentDetails->fees_paid_before_dr) ? $studentDetails->fees_paid_before_dr : "0",
          "{EXTRA_FEES}" => !empty($extra_fees) ? $extra_fees : "0",
          "{EXTRA_FEES_DESC}" => !empty($receiptDetailArr->extra_fees_description) ? $receiptDetailArr->extra_fees_description : "No Additional Fees Applied",
          "{DUE_BALANCE}" => !empty($dueAmount) ? $dueAmount : (int)0,
          "{TOTAL_AMOUNT}" => !empty($receiptAmount) ? $receiptAmount : "Not available!",
          "{COURSE_FEES}" => !empty($courseFees) ? $courseFees : "Not available!",
          "{DISCOUNT_AMOUNT}" => !empty($studentDetails->stu_course_discount) ? $studentDetails->stu_course_discount : "Not available!",
          "{NET_COURSE_FEES}" => !empty($net_course_fees) ? $net_course_fees : "Not available!",
          "{COURSE_FEES_PAID}" => !empty($total_course_fees_paid) ? $total_course_fees_paid : "Not available!"
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

        $dompdf = new Pdf();
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->load_html($html_code);
        //(Optional) Setup the paper size and orientation 
        //$dompdf->setPaper('A6', 'portrait'); 
        $customPaper = array(0, 0, 280, 600);
        $dompdf->setPaper($customPaper);
        $dompdf->render();
        $file = $dompdf->output();
        file_put_contents($file_upload_dir, $file);

        //Returning generated pdf
        $returnArr = array('check' => 'success', 'file_upload_dir' => $file_upload_dir, 'file_url' => $file_url);
      } else {
        $returnArr = array('check' => 'success', 'file_upload_dir' => $file_upload_dir, 'file_url' => $file_url);
      }
    } else {
      $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
    }

    return $returnArr;
  }

  public function generateRandomString($length = 10)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
  }

  public function compressImage($sourcePath, $targetPath, $compressedQuality = 30)
  {
    // Get image dimensions
    list($width, $height, $imageType) = getimagesize($sourcePath);

    // Check MIME type
    $allowedMimeTypes = array(
      IMAGETYPE_JPEG => 'image/jpeg',
      IMAGETYPE_PNG => 'image/png',
      IMAGETYPE_GIF => 'image/gif',
    );

    if (!isset($allowedMimeTypes[$imageType])) {
      echo "Unsupported image format.";
      return false;
    }

    // Create source image
    switch ($imageType) {
      case IMAGETYPE_JPEG:
        $sourceImage = imagecreatefromjpeg($sourcePath);
        break;
      case IMAGETYPE_PNG:
        $sourceImage = imagecreatefrompng($sourcePath);
        break;
      case IMAGETYPE_GIF:
        $sourceImage = imagecreatefromgif($sourcePath);
        break;
      default:
        echo "Unsupported image format.";
        return false;
    }

    // Create resized image with the same dimensions
    $resizedImage = imagecreatetruecolor($width, $height);
    imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $width, $height, $width, $height);

    // Save the compressed image to the target path
    $success = imagejpeg($resizedImage, $targetPath, $compressedQuality);

    // Free up memory
    imagedestroy($sourceImage);
    imagedestroy($resizedImage);

    return $success;
  }

  public function upload_file($file_name, $dir)
  {

    $targetDir = USER_UPLOAD_DIR . $dir . '/';
    $allowTypeArr = array(
      'jpg' => 'image/jpeg',
      'png' => 'image/png',
      'gif' => 'image/gif',
      'pdf' => 'application/pdf'
    );

    if (isset($_FILES[$file_name])) {
      //Defining necessary variable for current function
      $allowedFileSize = (int)10485760;

      try {
        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
          !isset($_FILES[$file_name]['error']) ||
          is_array($_FILES[$file_name]['error'])
        ) {
          throw new RuntimeException('Invalid parameters.');
        }

        // Check $_FILES[$file_name]['error'] value.
        switch ($_FILES[$file_name]['error']) {
          case UPLOAD_ERR_OK:
            break;
          case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file sent.');
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('Exceeded filesize limit.');
          default:
            throw new RuntimeException('Unknown errors.');
        }

        // You should also check filesize here.
        if ($_FILES[$file_name]['size'] > $allowedFileSize) {
          throw new RuntimeException('Exceeded filesize limit.');
        }

        // DO NOT TRUST $_FILES[$file_name]['mime'] VALUE !!
        // Check MIME Type by yourself.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
          $finfo->file($_FILES[$file_name]['tmp_name']),
          $allowTypeArr,
          true
        )) {
          throw new RuntimeException('Invalid file format.');
        }

        // You should name it uniquely.
        // DO NOT USE $_FILES[$file_name]['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.

        // $extension = explode('.', $_FILES[$file_name]['name']);
        // $fileName = rand() . '.' . $extension[1];
        //$new_name =  basename($_FILES[$file_name]['name']);

        // Generate a random image name
        $randomName = $this->generateRandomString() . '_' . time();

        // Create target directory if it doesn't exist
        if (!is_dir($targetDir)) {
          mkdir($targetDir, 0755, true);
        }


        $fileExtension = array_search($finfo->file($_FILES[$file_name]['tmp_name']), $allowTypeArr);
        $fileName = $randomName . '.' . $fileExtension;
        $targetFilePath = $targetDir . $fileName;

        if ($fileExtension != "pdf") :
          if (!$this->compressImage($_FILES[$file_name]['tmp_name'], $targetFilePath)) {
            throw new RuntimeException('Failed to compress uploaded file.');
          } else {
            return array('check' => 'success', 'fileName' => $fileName, 'message' => 'File is uploaded successfully.');
          }
        else :
          if (!move_uploaded_file($_FILES[$file_name]['tmp_name'], $targetFilePath)) {
            throw new RuntimeException('Failed to move uploaded file.');
          } else {
            return array('check' => 'success', 'fileName' => $fileName, 'message' => 'File is uploaded successfully.');
          }
        endif;
      } catch (RuntimeException $e) {
        $error_message =  $e->getMessage();
        return array('check' => 'failure', 'message' => $error_message);
      }
    }
  }

    public function createDBBak($filePath)
  {
    // Database configuration
    $host = HOST;
    $username = MYSQL_USER;
    $password = MYSQL_PASS;
    $dbName = DB_AIMGCSM;

    $mysqli = new mysqli($host, $username, $password, $dbName);

    if ($mysqli->connect_errno) {
        die("Failed to connect to MySQL: " . $mysqli->connect_error);
    }

    $fp = fopen($filePath, 'w');

    $mysqli->set_charset("utf8");

    // Get all tables
    $tables_result = $mysqli->query("SHOW TABLES");
    while ($row = $tables_result->fetch_row()) {
        $table = $row[0];

        // Write DROP TABLE
        fwrite($fp, "DROP TABLE IF EXISTS `$table`;\n");

        // Write CREATE TABLE
        $create_table_result = $mysqli->query("SHOW CREATE TABLE `$table`");
        $create_table_row = $create_table_result->fetch_row();
        fwrite($fp, $create_table_row[1] . ";\n\n");

        // Write INSERT statements
        $data_result = $mysqli->query("SELECT * FROM `$table`");
        while ($data = $data_result->fetch_assoc()) {
            $columns = array_map(function($val) { return "`$val`"; }, array_keys($data));
            $values = array_map(function($val) use ($mysqli) {
                return "'" . $mysqli->real_escape_string($val) . "'";
            }, array_values($data));

            fwrite($fp, "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");\n");
        }
        fwrite($fp, "\n\n");
    }

    fclose($fp);

   return true;
  }

 public function createUploadsZip()
{
    // Define source directory
    $sourceFolder = realpath(USER_UPLOAD_DIR);
    if (!$sourceFolder) return false;

    // Get all subdirectories
    $folders = ['course', 'franchise', 'gallery', 'home_sliders', 'news', 'others', 'student'];
    
    // Array to store created zip files
    $createdZips = [];

    foreach ($folders as $folder) {
        $folderPath = $sourceFolder . DIRECTORY_SEPARATOR . $folder;

        // Check if folder exists
        if (!is_dir($folderPath)) continue;

        // Define zip file name
        $zipFile = SITE_BACKUP_DIR . "{$folder}_" . date('Y-m-d_H-i-s') . "_backup.zip";

        // Use shell command for fast zipping
        $zipCommand = "zip -r " . escapeshellarg($zipFile) . " " . escapeshellarg($folderPath) . " > /dev/null 2>&1";
        
        exec($zipCommand, $output, $zipResult);

        if ($zipResult === 0) {
            $createdZips[] = $zipFile;
        }
    }

    return !empty($createdZips) ? true : false;
}

}
