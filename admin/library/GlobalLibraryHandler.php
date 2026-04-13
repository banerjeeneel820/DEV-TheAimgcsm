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


  private $GlobalInterfaceControllerObj;
  private $memObj;

  public function __construct()
  {
    $this->GlobalInterfaceControllerObj = new GlobalInterfaceController();
    if (SERVER_ENV == "PRODUCTION") {
      $this->memObj = new Memcached();
      $this->memObj->addServer("127.0.0.1", 11211);
    } else {
      $this->memObj = null;
    }
  }

  public function buildBackUrl($newRoute)
  {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $currentUrl = $_SERVER['REQUEST_URI'];

    $queryString = parse_url($currentUrl, PHP_URL_QUERY);
    parse_str($queryString ?? '', $params);

    // remove unwanted params
    unset($params['route'], $params['id']);

    // build params with route FIRST
    $finalParams = array_merge(
      ['route' => $newRoute],
      $params
    );

    $newQuery = http_build_query($finalParams);
    $path = strtok($currentUrl, '?');

    return $scheme . '://' . $host . $path . '?' . $newQuery;
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
      $userRoleArr = $this->GlobalInterfaceControllerObj->fetch_Current_User_Role($paramArr);
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

  public function purgeSiteCache($section)
  {
    if (SERVER_ENV == "PRODUCTION") {
      switch ($section) {
        case 'student':
          if ($_SESSION['user_type'] == 'developer' || $_SESSION['user_type'] == 'admin') {
            $this->memObj->delete("student_dashboard_today");
            $this->memObj->delete("student_dashboard_weekly");
            $this->memObj->delete("student_dashboard_monthly");
            $this->memObj->delete("student_dashboard_annual");
          } elseif ($_SESSION['user_type'] == 'franchise') {
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
          } elseif ($_SESSION['user_type'] == 'franchise') {
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
    return true;
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
    $allowedExtensions = ['zip', 'txt'];

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

  public function create_Frnachise_ID()
  {
    //Creating new Franchise id method
    $franchiseDetail = $this->GlobalInterfaceControllerObj->fetch_Last_Franchise_Detail();
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
    $stuIdDetail = $this->GlobalInterfaceControllerObj->fetch_Last_Student_Detail();
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
    $receiptDetail = $this->GlobalInterfaceControllerObj->fetch_Last_Receipt_Detail();
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

  public function remove_File_From_Server($type, $resultArr)
  {

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
        $receiptPdfDir =  USER_UPLOAD_DIR . 'runtime_upload/' . "Receipt_" . $resultArr->receipt_id . '.pdf';
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
        $featuredImageDir = USER_UPLOAD_DIR . $type . '/' . $resultArr->content;
        if ($featuredImageDir && file_exists($featuredImageDir)) {
          $newsPdfDir = USER_UPLOAD_DIR . $type . '/' . $resultArr->optional_pdf;
        }
        //Removing file from server
        unlink($newsPdfDir);
        break;

      default:
        return true;
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
    $returnArr = $this->GlobalInterfaceControllerObj->check_Slug_Availibility($type, $field, $slug);
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

    $campaignDataArr = $this->GlobalInterfaceControllerObj->fetch_Email_Template_Detail($email_code);

    return $campaignDataArr;
  }

  public function fetchSiteSettingDetail()
  {

    $siteSettingArr = $this->GlobalInterfaceControllerObj->fetch_Global_Site_Setting_Detail();

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
    //$emailReturnArr = $this->configure_email_body($paramArr);
    //print_r($emailReturnArr);exit;

    //Collecting necessary variables to configure send mail
    $fromEmail = $paramArr['sender_email'];
    $fromName  = $paramArr['sender_name'];
    $toEmail   = $paramArr['receiver_email'];
    $toName    = $paramArr['receiver_name'];

    $ccEmail   = $paramArr['cc_email'];

    if (array_key_exists('attachment_path', $paramArr)) {
      $filePath  = $paramArr['attachment_path'];
    }
    $email_subject = $paramArr['email_subject'];
    $body = $paramArr['email_template'];

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
    if (array_key_exists('attachment_path', $paramArr)) {
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
      if ($paramArr['attachment_type'] == 'dynamic') {
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
      $receiptDetailArr = $this->GlobalInterfaceControllerObj->fetch_Single_Receipt_Data($receipt_id);
      $studentDetails = $this->GlobalInterfaceControllerObj->fetch_Global_Single_Student($receiptDetailArr->stu_id, $receiptDetailArr->created_at);

      $file_upload_dir =  USER_UPLOAD_DIR . 'runtime_upload/' . "Receipt_" . $receiptDetailArr->receipt_id . '.pdf';
      $file_url = USER_UPLOAD_URL . 'runtime_upload/' . "Receipt_" . $receiptDetailArr->receipt_id . '.pdf';

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
      $pdfParamArr['email_subject'] = $emailTemplateArr->subject;
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
        "{CONVERSION_STATUS}" => $studentDetails->conversion_status == 'y' ? 'Converted' : 'Recent',
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

      if (!file_exists($file_upload_dir)) {

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
      }

      //Returning file path and email template for student receipt
      $returnArr = array(
        'check' => 'success', 'email_subject' => $pdfParamArr['email_subject'],
        'email_template' => $pdfParamArr['email_template'],
        'file_upload_dir' => $file_upload_dir, 'file_url' => $file_url
      );
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

  public function validateFile($file, $type = 'image')
  {
    // no file uploaded
    if (empty($file) || !isset($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
      return ['check' => 'failure', 'message' => 'No valid file uploaded'];
    }

    // allowed mime types
    $allowedTypes = [
      'image' => ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'],
      'pdf'   => ['application/pdf'],
      'doc'   => [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
      ]
    ];

    // get actual mime type (more secure than extension)
    $fileMimeType = mime_content_type($file['tmp_name']);

    if (!isset($allowedTypes[$type])) {
      return ['check' => 'failure', 'message' => 'Invalid validation type'];
    }

    if (!in_array($fileMimeType, $allowedTypes[$type])) {
      return [
        'check' => 'failure',
        'message' => "Invalid file type. Only " . $type . " files are allowed."
      ];
    }

    return ['check' => 'success'];
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
        $columns = array_map(function ($val) {
          return "`$val`";
        }, array_keys($data));
        $values = array_map(function ($val) use ($mysqli) {
          return "'" . $mysqli->real_escape_string($val) . "'";
        }, array_values($data));

        fwrite($fp, "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");\n");
      }
      fwrite($fp, "\n\n");
    }

    fclose($fp);

    // Create zip file
    $zip = new ZipArchive();
    $zipFilePath = $filePath . '.zip';

    if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
      // Add the SQL file to the zip
      $zip->addFile($filePath, basename($filePath));
      $zip->close();

      $createDbBak = true;
    } else {
      $createDbBak = false;
    }

    // Delete the original SQL file
    if (file_exists($filePath)) {
      if (!unlink($filePath)) {
        $removeDBAftrBakupErr = array('check' => "failure", 'filePath' => $filePath, 'message' => "Sql dump isn't get deleted");
        // Log the error
        $this->logServerData($removeDBAftrBakupErr);
      }
    }

    return $createDbBak == true ? true : false;
  }

  public function createUploadsZip($zipFile)
  {
    $sourceFolder = realpath(USER_UPLOAD_DIR);
    if (!$sourceFolder) return false;

    $zip = new ZipArchive();
    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
      return false;
    }

    $files = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($sourceFolder, RecursiveDirectoryIterator::SKIP_DOTS),
      RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
      $filePath = $file->getRealPath();
      $relativePath = substr($filePath, strlen($sourceFolder) + 1);

      if ($file->isDir()) {
        $zip->addEmptyDir($relativePath);
      } else {
        $zip->addFile($filePath, $relativePath);
      }
    }

    $zip->close();

    return file_exists($zipFile);
  }

  public function createUploadsZipChunk()
  {
    $sourceFolder = realpath(USER_UPLOAD_DIR);
    if (!$sourceFolder) return false;

    $folders = ['course', 'franchise', 'gallery', 'home_sliders', 'news', 'others', 'student'];
    $createdZips = [];

    foreach ($folders as $folder) {
      $folderPath = $sourceFolder . DIRECTORY_SEPARATOR . $folder;

      if (!is_dir($folderPath)) continue;

      $zipFile = SITE_BACKUP_DIR . "{$folder}_" . date('Y-m-d_H-i-s') . "_backup.zip";

      $zip = new ZipArchive();
      if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        continue;
      }

      $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folderPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
      );

      foreach ($files as $name => $file) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($folderPath) + 1);

        if ($file->isDir()) {
          $zip->addEmptyDir($relativePath);
        } else {
          $zip->addFile($filePath, $relativePath);
        }
      }

      $zip->close();
      $createdZips[] = $zipFile;
    }

    return !empty($createdZips) ? true : false;
  }

  public function buildExportCriteria($params)
  {
    $filters = [];

    // Record Status
    if (!empty($params['record_status'])) {
      $filters[] = "Status: " . ucfirst($params['record_status']);
    }

    // Course
    if (!empty($params['course_id'])) {
      // Ideally fetch course name from DB
      $filters[] = "Course: " . $params['course_name'];
    }

    // Franchise
    if (!empty($params['franchise_id'])) {
      // Ideally fetch franchise name from DB
      $filters[] = "Franchise: " . $params['franchise_name'];
    }

    // Created filter (today / this month etc.)
    if (!empty($params['created'])) {
      $filters[] = "Created: " . ucfirst($params['created']);
    }

    // Date Range
    if (!empty($params['receipt_season_start']) && !empty($params['receipt_season_end'])) {
      $filters[] = "Date: " . date('d M Y', strtotime($params['receipt_season_start'])) .
        " to " . date('d M Y', strtotime($params['receipt_season_end']));
    }

    // Student ID
    if (!empty($params['student_id'])) {
      $filters[] = "Student ID: " . $params['student_id'];
    }

    // Protocol (source)
    if (!empty($params['protocol'])) {
      $source = ($params['protocol'] == 'dashboard') ? 'Dashboard' : 'Receipt Module';
      $filters[] = "Source: " . $source;
    }

    return !empty($filters) ? implode(' | ', $filters) : 'No Filters Applied';
  }

  // Method to log request data in a file
  public function logServerData($fileName, $logDataArr = [])
  {

    $logDataJson = json_encode($logDataArr, JSON_PRETTY_PRINT);

    // Get the current timestamp.
    $timestamp = date('Y-m-d H:i:s');

    // Open the file in "append" mode, creating it if it doesn't exist.
    $file = fopen($fileName, 'a');

    if ($file) {

      fwrite($file, "===========================================\n");
      fwrite($file, "Timestamp: $timestamp\n");
      fwrite($file, "payload ==> $logDataJson\n");
      fwrite($file, "===========================================\n");
      fclose($file);
    } else {
      // Handle the case where the file couldn't be opened.
      echo "Failed to open the file for logging.";
    }
  }

  //Curl call method
  protected function curl_request($url, $post_data = array())
  {
    //echo $url;exit; 
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $json = curl_exec($curl);
    $returnArr = json_decode($json, true);
    return $returnArr;
  }

  // Check if captcha response is a valid one:
  public function checkCaptchaResponse($recaptcha_response)
  {
    //echo $recaptcha_response;exit;

    // Build POST request:
    $recaptcha_source = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_secret = '6LdJ398UAAAAAHAe4Dr1HVo7OtUkop4FsV0FNvKJ';

    //Make and decode POST request:
    $recaptcha_url = $recaptcha_source . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response;
    $recaptchaServerResponseArr = $this->curl_request($recaptcha_url);

    //print_r($recaptchaServerResponseArr);exit;

    // Take action based on the score returned:
    if ($recaptchaServerResponseArr['success'] == 1) {
      return true;
    } else {
      return false;
    }
  }

  // helper for sanitization
  private function inputDataSanitize($key, $method = 'post', $escape = false)
  {
    $sources = [
      'get' => $_GET,
      'post' => $_POST,
      'request' => $_REQUEST
    ];

    $data = $sources[strtolower($method)] ?? [];

    if (!isset($data[$key])) {
      return null;
    }

    $value = trim($data[$key]);

    if ($escape) {
      return mysqli_real_escape_string(DB::$WRITELINK, $value);
    }

    return $value;
  }

  public function getDataSanitize($key, $default = null)
  {
    $value = $this->inputDataSanitize($key, 'get', false);
    return $value !== null ? $value : $default;
  }

  public function postDataSanitize($key, $default = null)
  {
    $value = $this->inputDataSanitize($key, 'post', true);
    return $value !== null ? $value : $default;
  }
}
