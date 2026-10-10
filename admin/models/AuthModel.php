<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class AuthModel extends BaseModel
{

   public function __construct(Database $database)
   {
      parent::__construct($database);
   }

   public function check_User_Login($paramArr = array())
   {
      $user_type = $paramArr['user_type'];
      $params = [];
      $params_email = [];
      $params_email_pass = [];

      switch ($user_type) {

         case 'developer':
            $user_table = "global_support_admin";
            $user_email = $paramArr['user_email'];
            $user_pswd = $paramArr['user_pswd'];

            $query_conditional_clause = "user_email = ? AND user_pass = ? AND user_type = 'developer' AND user_status = 'active'";
            $params = [$user_email, $user_pswd];

            $query_email_caluse = "user_email = ? AND user_type = 'developer'";
            $params_email = [$user_email];

            $query_email_pass_caluse = "user_email = ? AND user_type = 'developer' AND user_pass = ?";
            $params_email_pass = [$user_email, $user_pswd];
            break;

         case 'admin':
            $user_table = "global_support_admin";
            $user_email = $paramArr['user_email'];
            $user_pswd = $paramArr['user_pswd'];

            // ✅ FIXED LOGIC (important)
            $query_conditional_clause = "(user_email = ? OR user_type = ?) AND user_pass = ? AND user_type = 'admin' AND user_status = 'active'";
            $params = [$user_email, $user_email, $user_pswd];

            $query_email_caluse = "user_email = ? AND user_type = 'admin'";
            $params_email = [$user_email];

            $query_email_pass_caluse = "user_email = ? AND user_type = 'admin' AND user_pass = ?";
            $params_email_pass = [$user_email, $user_pswd];
            break;

         case 'franchise':
            $user_table = "franchise";
            $user_email = $paramArr['user_email'];
            $user_pswd = $paramArr['user_pswd'];

            // FIXED LOGIC
            $query_conditional_clause = "(fran_email = ? OR fran_id = ?) AND fran_pass = ? AND record_status = 'active'";
            $params = [$user_email, $user_email, $user_pswd];

            $query_email_caluse = "fran_email = ?";
            $params_email = [$user_email];

            $query_email_pass_caluse = "fran_email = ? AND fran_pass = ?";
            $params_email_pass = [$user_email, $user_pswd];
            break;

         case 'exam':
            $user_table = "students";
            $stu_id = $paramArr['user_email'];
            $user_type = "student";

            $query_conditional_clause = "stu_id = ? AND student_status = 'continue' AND stu_result = 'unqualified' AND record_status = 'active'";
            $params = [$stu_id];

            $query_email_caluse = "stu_id = ?";
            $params_email = [$stu_id];

            $query_email_pass_caluse = "stu_id = ? AND record_status = 'active' AND stu_result = 'unqualified'";
            $params_email_pass = [$stu_id];
            break;

         default:
            $user_table = "global_support_admin";
            $user_type = "admin";
            $user_email = $paramArr['user_email'];
            $user_pswd = $paramArr['user_pswd'];

            $query_conditional_clause = "user_email = ? AND user_pass = ?";
            $params = [$user_email, $user_pswd];
            break;
      }

      // MAIN QUERY
      $sql_check_user = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $user_table . " WHERE " . $query_conditional_clause;

      $resultArr['row_count'] = $this->global_Rows_Count_DB($sql_check_user, $params);

      if ($resultArr['row_count'] > 0) {

         session_regenerate_id();

         $userDetail = $this->global_Fetch_Single_DB($sql_check_user, $params);

         $_SESSION['user_id'] = $userDetail->id;

         $siteSettingArr = $this->fetch_Global_Site_Setting_Detail();

         if ($user_type == 'admin' || $user_type == 'developer') {
            $_SESSION['user_name']  = $userDetail->user_nicename;
            $_SESSION['user_email'] = $userDetail->user_email;
            $_SESSION['user_profile_pic'] = USER_UPLOAD_URL . 'others/' . $siteSettingArr->logo;
            $_SESSION['user_role'] = unserialize($userDetail->user_role);
         } elseif ($user_type == 'franchise') {
            $_SESSION['user_name']  = $userDetail->center_name;
            $_SESSION['user_email'] = $userDetail->fran_email;
            $_SESSION['owned_status'] = $userDetail->owned_status;
            $_SESSION['user_profile_pic'] = USER_UPLOAD_URL . 'franchise/' . $userDetail->fran_image;
            $_SESSION['user_role'] = unserialize($userDetail->user_role);
         } elseif ($user_type == 'student') {
            $_SESSION['stu_id']  = $userDetail->stu_id;
            $_SESSION['user_name']  = $userDetail->stu_name;
            $_SESSION['user_email'] = $userDetail->stu_email;
            $_SESSION['record_status'] = $userDetail->record_status;
            $_SESSION['user_profile_pic'] = USER_UPLOAD_URL . 'student/' . $userDetail->image_file_name;
         }

         $_SESSION['user_type'] = $user_type;

         // runtime folder
         $runtime_upload_dir_path = USER_UPLOAD_DIR . 'runtime_upload/';
         if (!file_exists($runtime_upload_dir_path)) {
            mkdir($runtime_upload_dir_path);
            chmod($runtime_upload_dir_path, 0755);
         }

         return ['check' => 'success', 'user_detail' => $userDetail, 'msg' => 'You have successfully logged in!'];
      } else {

         // VALIDATION CHECK
         $sql_validate_user_email = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $user_table . " WHERE " . $query_email_caluse;
         $row_count = $this->global_Rows_Count_DB($sql_validate_user_email, $params_email);

         if ($row_count > 0) {

            $sql_validate_user_email_pass = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $user_table . " WHERE " . $query_email_pass_caluse;
            $row_count = $this->global_Rows_Count_DB($sql_validate_user_email_pass, $params_email_pass);

            if ($row_count > 0) {
               $authErrorMsg = "Your account has been blocked, Please contact the administrator for further help!";
            } else {
               $authErrorMsg = "You have entered a wrong password!";
            }
         } else {

            // EXTRA CASE FOR STUDENTS ARCHIVE
            if ($user_table == "students") {

               $sql_validate_user_email = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students_archive WHERE " . $query_email_caluse;
               $row_count = $this->global_Rows_Count_DB($sql_validate_user_email, $params_email);

               if ($row_count > 0) {

                  $sql_validate_user_email_pass = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students_archive WHERE " . $query_email_pass_caluse;
                  $row_count = $this->global_Rows_Count_DB($sql_validate_user_email_pass, $params_email_pass);

                  if ($row_count > 0) {
                     $authErrorMsg = "Your account has been blocked, Please contact the administrator for further helps!";
                  } else {
                     $authErrorMsg = "You have entered a wrong password!";
                  }
               } else {
                  $authErrorMsg = "This email isn't registered with us!";
               }
            } else {
               $authErrorMsg = "This email isn't registered with us!";
            }
         }

         return ['check' => 'failure', 'msg' => $authErrorMsg];
      }
   }

   public function fetch_Global_Site_Setting_Detail()
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "site_setting WHERE `update_id` = 'UPDATE_THE_AIMGCSM_SITE_SETTINGS'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Developer_Profile_Data($user_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE `user_type` = 'developer' AND `id`='$user_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Admin_Profile_Data($user_id = null)
   {

      if ($_SESSION['user_type'] == 'developer') {
         $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE `user_type` = 'admin' AND `user_status` = 'active'";
      } else {
         $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE `user_type` = 'admin' AND `user_status` = 'active' AND `id`='$user_id'";
      }

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Single_Franchise($franchise_id)
   {
      $where = [];
      $params = [];

      $where[] = "(fran.id = ?)";
      $params[] = $franchise_id;

      $whereSql = "WHERE " . implode(" AND ", $where);

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise fran $whereSql";;

      // Important because of COUNT()
      $sql .= " GROUP BY fran.id LIMIT 1";

      // Debug (optional)
      // $this->debugQuery($sql, $params);

      $resultArr = $this->global_Fetch_Single_DB($sql, $params);

      return $resultArr;
   }

   public function manage_Profile_Data($formDataArr)
   {

      $resultArr = array();

      $user_type = $formDataArr['user_type'];

      $user_nicename = $formDataArr['user_nicename'];
      $user_contact = $formDataArr['user_contact'];
      $user_email = $formDataArr['user_email'];
      $user_status = $formDataArr['user_status'];
      $user_pass = $formDataArr['user_pass'];
      $user_role = $formDataArr['user_role'];

      //Query for updating profile
      $sql_update_profile = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin SET `user_nicename` = '$user_nicename',`user_contact` = '$user_contact',`user_email` = '$user_email',`user_status` = '$user_status',`user_pass` = '$user_pass',`user_role` = '$user_role' WHERE `user_type` = '$user_type'";

      //echo $sql_update_profile;exit();

      $resultArr = $this->global_CRUD_DB($sql_update_profile);

      if ($resultArr["check"] == "success") {
         $_SESSION['username'] = $user_nicename;
         $_SESSION['user_role'] = unserialize($user_role);
         return $resultArr;
      } else {
         return $resultArr;
      }
   }

   public function edit_Franchise_Profile($franDataArr)
   {

      $fran_row_id = $franDataArr['fran_row_id'];

      $fran_pass = $franDataArr['fran_pass'];
      $center_name = $franDataArr['center_name'];
      $owner_name = $franDataArr['owner_name'];
      $fran_phone = $franDataArr['fran_phone'];
      $fran_email = $franDataArr['fran_email'];
      $fran_address = $franDataArr['fran_address'];
      $fran_description = $franDataArr['fran_description'];
      $fran_image = $franDataArr['fran_image'];
      $fran_pdf_name = $franDataArr['fran_pdf_name'];

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise SET `fran_pass` = '$fran_pass',`center_name` = '$center_name', `owner_name` = '$owner_name',`fran_phone` = '$fran_phone', `fran_email`= '$fran_email', `fran_address` = '$fran_address',`fran_description` = '$fran_description', `fran_image` = '$fran_image', `fran_pdf_name` = '$fran_pdf_name',`updated_at` = now() WHERE `id`='$fran_row_id'";

      //echo $sql;exit();

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function check_User_Email_Availability(array $data)
   {
      $email   = $this->escape($data['user_email'] ?? '');
      $type    = $data['user_type'] ?? '';
      $userId  = (int) ($data['user_id'] ?? 0);

      // -----------------------------
      // TYPE CONFIG MAP
      // -----------------------------
      $typeConfig = [
         'student'   => ['table' => 'students',  'alias' => 'stu',  'column' => 'stu_email'],
         'franchise' => ['table' => 'franchise', 'alias' => 'fran', 'column' => 'fran_email'],
      ];

      // -----------------------------
      // VALIDATION
      // -----------------------------
      if (!isset($typeConfig[$type])) {
         return ['check' => 'failure', 'message' => 'Invalid user type'];
      }

      if (empty($email)) {
         return ['check' => 'failure', 'message' => 'Email required'];
      }

      $table  = $typeConfig[$type]['table'];
      $alias  = $typeConfig[$type]['alias'];
      $column = $typeConfig[$type]['column'];

      // -----------------------------
      // BUILD QUERY
      // -----------------------------
      $where = "$alias.$column = '$email'";

      if ($userId > 0) {
         $where .= " AND $alias.id != $userId";
      }

      $sql = "SELECT COUNT(*) as total 
            FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "$table $alias 
            WHERE $where";

      // -----------------------------
      // EXECUTE
      // -----------------------------
      $count  = $this->global_Aggregate_Value_DB($sql);

      // -----------------------------
      // RESPONSE
      // -----------------------------
      if ($count > 0) {
         return [
            'check' => 'failure',
            'user_row_count' => $count,
            'message' => "This email is already taken; Please try another email."
         ];
      }

      return [
         'check' => 'success',
         'user_row_count' => 0
      ];
   }
}   