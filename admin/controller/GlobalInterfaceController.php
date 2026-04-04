<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class GlobalInterfaceController
{

   private $conn;

   public function __construct()
   {
      $this->conn = new GlobalInterfaceModel();
   }

   private function debugQuery($sql, $params = [])
   {
      if (!empty($params)) {
         foreach ($params as $param) {
            if (is_null($param)) {
               $value = "NULL";
            } elseif (is_numeric($param)) {
               $value = $param;
            } else {
               $value = "'" . addslashes($param) . "'";
            }

            $sql = preg_replace('/\?/', $value, $sql, 1);
         }
      }

      echo "<pre>";
      echo $sql;
      echo "</pre>";
      exit; // stop execution after debug
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

            // ✅ FIXED LOGIC
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

      $resultArr['row_count'] = $this->conn->global_Rows_Count_DB($sql_check_user, $params);

      if ($resultArr['row_count'] > 0) {

         session_regenerate_id();

         $userDetail = $this->conn->global_Fetch_Single_DB($sql_check_user, $params);

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
         $row_count = $this->conn->global_Rows_Count_DB($sql_validate_user_email, $params_email);

         if ($row_count > 0) {

            $sql_validate_user_email_pass = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $user_table . " WHERE " . $query_email_pass_caluse;
            $row_count = $this->conn->global_Rows_Count_DB($sql_validate_user_email_pass, $params_email_pass);

            if ($row_count > 0) {
               $authErrorMsg = "Your account has been blocked, Please contact the administrator for further help!";
            } else {
               $authErrorMsg = "You have entered a wrong password!";
            }
         } else {

            // EXTRA CASE FOR STUDENTS ARCHIVE
            if ($user_table == "students") {

               $sql_validate_user_email = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students_archive WHERE " . $query_email_caluse;
               $row_count = $this->conn->global_Rows_Count_DB($sql_validate_user_email, $params_email);

               if ($row_count > 0) {

                  $sql_validate_user_email_pass = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students_archive WHERE " . $query_email_pass_caluse;
                  $row_count = $this->conn->global_Rows_Count_DB($sql_validate_user_email_pass, $params_email_pass);

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

   public function check_User_Email_Availability($paramArr)
   {

      $user_email = $paramArr['user_email'];
      $user_type = $paramArr['user_type'];

      switch ($user_type) {

         case 'student':

            $where_Clause = "WHERE stu.stu_email  = '$user_email'";

            if ($paramArr['user_id'] != null) {
               $user_id = $paramArr['user_id'];
               $where_Clause .= " AND stu.id!='$user_id'";
            }

            $sql_check_user = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu " . $where_Clause;
            break;

         case 'franchise':

            $where_Clause = "WHERE fran.fran_email  = '$user_email'";

            if ($paramArr['user_id'] != null) {
               $user_id = $paramArr['user_id'];
               $where_Clause .= " AND fran.id!='$user_id'";
            }

            $sql_check_user = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise fran " . $where_Clause;
            break;

         case 'newsletter':

            $where_Clause = "WHERE nwsltr.email  = '$user_email'";

            if ($paramArr['user_id'] != null) {
               $user_id = $paramArr['user_id'];
               $where_Clause .= " AND nwsltr.id!='$user_id'";
            }

            $sql_check_user = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "newsletter nwsltr " . $where_Clause;
            break;
      }

      //echo $sql_check_user;exit;

      //executing query
      $user_row_count = $this->conn->global_Rows_Count_DB($sql_check_user);

      if ($user_row_count > 0) {
         return array('check' => 'failure', 'user_row_count' => $user_row_count, "message" => "This email is already taken; Please try another email.");
      } else {
         return array('check' => 'success', 'user_row_count' => 0);
      }
   }

   public function fetch_Current_User_Role($paramArr = array())
   {
      $user_type = $paramArr['user_type'];
      $user_id = $paramArr['user_id'];


      if ($user_type == 'admin' || $user_type == 'developer') {
         $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE `user_type` = '$user_type' AND `id` = '$user_id' AND `user_status` = 'active'";
      } elseif ($user_type == 'franchise') {
         $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise WHERE `id`='$user_id'";
      } else {
         $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students WHERE `id`='$user_id'";
      }

      //echo $sql;exit();
      $resultArr = $this->conn->global_Fetch_Single_DB($sql);
      $userRoleArr = unserialize($resultArr->user_role);

      return $userRoleArr;
   }

   public function fetch_Current_User_Detail($paramArr = array())
   {
      $user_type = $paramArr['user_type'];
      $user_id = $paramArr['user_id'];

      if ($user_type == 'admin' || $user_type == 'developer') {
         $sql_fetch_user_detail = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE `user_type` = '$user_type' AND `id` = '$user_id' AND `user_status` = 'active'";
      } elseif ($user_type == 'franchise') {
         $sql_fetch_user_detail = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise WHERE `id`='$user_id'";
      } else {
         $sql_fetch_current_students = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students WHERE `id`='$user_id'";
         $countStudentRow = $this->conn->global_Rows_Count_DB($sql_fetch_user_detail);

         if ($countStudentRow > 0) {
            $sql_fetch_user_detail = $sql_fetch_current_student;
         } else {
            $sql_fetch_user_detail = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students_archive WHERE `id`='$user_id'";
         }
      }

      //echo $sql_fetch_user_detail;exit();
      $resultArr = $this->conn->global_Fetch_Single_DB($sql_fetch_user_detail);

      return $resultArr;
   }

   public function fetch_Admin_Profile_Data($user_id)
   {

      if ($_SESSION['user_type'] == 'developer') {
         $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE `user_type` = 'admin' AND `user_status` = 'active'";
      } else {
         $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE `user_type` = 'admin' AND `user_status` = 'active' AND `id`='$user_id'";
      }

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Developer_Profile_Data($user_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE `user_type` = 'developer' AND `id`='$user_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Franchise($record_status = 'active')
   {
      $params = [];
      $where = [];

      // Filter
      $where[] = "fran.record_status = ?";
      $params[] = $record_status;

      $whereSql = "WHERE " . implode(" AND ", $where);

      // Pre-aggregated student count (better performance)
      $studentCountSubquery = "
        SELECT 
            franchise_id,
            COUNT(*) AS enrolled_student_count
        FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students
        GROUP BY franchise_id
    ";

      $sql = "
        SELECT 
            fran.*,
            IFNULL(stu_count.enrolled_student_count, 0) AS enrolled_student_count

        FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise fran

        LEFT JOIN ($studentCountSubquery) stu_count
            ON fran.id = stu_count.franchise_id

        $whereSql

        ORDER BY enrolled_student_count DESC
    ";

      return $this->conn->global_Fetch_All_DB($sql, $params);
   }

   public function fetch_Global_Course($record_status = 'active')
   {
      $params = [];
      $where = [];

      // Filter
      $where[] = "crs.record_status = ?";
      $params[] = $record_status;

      $whereSql = "WHERE " . implode(" AND ", $where);

      // Pre-aggregated student count
      $studentCountSubquery = "
         SELECT 
               course_id,
               COUNT(*) AS no_of_stu_enrld
         FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students
         GROUP BY course_id
      ";

      $sql = "
         SELECT 
               crs.*,
               IFNULL(stu_count.no_of_stu_enrld, 0) AS no_of_stu_enrld

         FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs

         LEFT JOIN ($studentCountSubquery) stu_count
               ON crs.id = stu_count.course_id

         $whereSql

         ORDER BY no_of_stu_enrld DESC
      ";

      return $this->conn->global_Fetch_All_DB($sql, $params);
   }

   public function fetch_Global_Receipt($dataArr = [])
   {
      $where = [];
      $params = [];

      // Mandatory filter
      $where[] = "rcpt.record_status = ?";
      $params[] = $dataArr['record_status'];

      // Verified status
      if (!empty($dataArr['verified_status'])) {
         $where[] = "rcpt.verified_status = ?";
         $params[] = ($dataArr['verified_status'] === 'y') ? 1 : 0;
      }

      // Student filter
      if (!empty($dataArr['student_id'])) {
         $where[] = "rcpt.stu_id = ?";
         $params[] = $dataArr['student_id'];
      }

      // Course filter
      if (!empty($dataArr['course_id']) && $dataArr['course_id'] > 0) {
         $where[] = "crs.id = ?";
         $params[] = $dataArr['course_id'];
      }

      // Franchise filter
      if (!empty($dataArr['franchise_id']) && $dataArr['franchise_id'] > 0) {
         $where[] = "stu.franchise_id = ?";
         $params[] = $dataArr['franchise_id'];
      }

      // Single date filter (index-friendly)
      if (!empty($dataArr['created'])) {
         $date = date('Y-m-d', strtotime($dataArr['created']));
         $where[] = "rcpt.created_at >= ? AND rcpt.created_at < ?";
         $params[] = $date . " 00:00:00";
         $params[] = $date . " 23:59:59";
      }

      // Date range filters
      if (!empty($dataArr['receipt_season_start'])) {
         $where[] = "rcpt.created_at >= ?";
         $params[] = $dataArr['receipt_season_start'] . " 00:00:00";
      }

      if (!empty($dataArr['receipt_season_end'])) {
         $where[] = "rcpt.created_at <= ?";
         $params[] = $dataArr['receipt_season_end'] . " 23:59:59";
      }

      $whereSql = "WHERE " . implode(" AND ", $where);

      // Pagination
      $limit = (int) $dataArr['limit'];
      $pageNo = (int) $dataArr['pageNo'];
      $offset = ($pageNo - 1) * $limit;

      // Base query
      $baseSql = "
         FROM theaimgc_dev_student_receipts rcpt

         INNER JOIN theaimgc_dev_students stu 
               ON rcpt.stu_id = stu.stu_id

         LEFT JOIN theaimgc_dev_franchise frn 
               ON stu.franchise_id = frn.id

         LEFT JOIN theaimgc_dev_course crs 
               ON stu.course_id = crs.id

         LEFT JOIN theaimgc_dev_parent_category pc 
               ON rcpt.category_id = pc.id

         $whereSql
      ";

      // Data query
      $dataSql = "
         SELECT 
               rcpt.id,
               rcpt.receipt_id,
               rcpt.category_id,
               rcpt.receipt_amount,
               rcpt.late_fine,
               rcpt.extra_fees,
               rcpt.created_at,
               rcpt.record_status AS receipt_status,
               rcpt.verified_status,
               rcpt.edit_description,

               stu.id AS student_record_id,
               stu.stu_id,
               stu.stu_name,
               stu.stu_phone,
               stu.stu_email,
               stu.image_file_name,
               stu.stu_qualification,
               stu.stu_course_fees,
               stu.stu_course_discount,
               stu.fees_paid_before_dr,
               stu.student_status,
               stu.stu_result,
               stu.record_status,
               stu.created_at AS student_created_at,

               frn.center_name,
               crs.course_title,
               pc.name AS category

         $baseSql

         ORDER BY rcpt.id DESC
         LIMIT ?, ?
      ";

      // Count query
      $countSql = "SELECT COUNT(*) as total $baseSql";

      // Execute queries
      $dataParams = array_merge($params, [$offset, $limit]);

      // Debug
      //$this->debugQuery($dataSql, $dataParams);

      $data = $this->conn->global_Fetch_All_DB($dataSql, $dataParams);
      $total = $this->conn->global_Count_Value_DB($countSql, $params);

      return [
         'data' => $data,
         'row_count' => $total,
         'pageNo' => $pageNo,
         'limit' => $limit
      ];
   }

   public function fetch_Single_Student_Receipt($student_id, $dataArr = array())
   {

      if (!empty($dataArr)) {
         $record_status = $dataArr['record_status'];
      } else {
         $record_status = 'active';
      }

      $where_Clause = "WHERE rcpt.record_status = '$record_status'";

      if (!empty($dataArr['student_id'])) {
         $student_id = $dataArr['student_id'];
         $where_Clause .= " AND rcpt.stu_id = '$student_id'";
      }

      if ($dataArr['course_id'] > 0) {
         $course_id = $dataArr['course_id'];
         $where_Clause .= "AND crs.id = '$course_id'";
      }

      if ($dataArr['franchise_id'] > 0) {
         $franchise_id = $dataArr['franchise_id'];
         $where_Clause .= "AND frn.id = '$franchise_id'";
      }

      if ($dataArr['created'] > 0) {
         $created_at = date('Y-m-d', strtotime($dataArr['created']));
         $where_Clause .= "AND DATE(rcpt.created_at) = '$created_at'";
      }

      if (!empty($dataArr['receipt_season_start']) && empty($dataArr['receipt_season_end'])) {
         $receipt_season_start = $dataArr['receipt_season_start'];
         $where_Clause .= "AND rcpt.receipt_season_start >='$receipt_season_start'";
      } else if (empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {
         $receipt_season_end = $dataArr['receipt_season_end'];
         $where_Clause .= "AND rcpt.receipt_season_end <='$receipt_season_end'";
      } else if (!empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {
         $receipt_season_start = $dataArr['receipt_season_start'];
         $receipt_season_end = $dataArr['receipt_season_end'];
         $where_Clause .= "AND rcpt.receipt_season_start BETWEEN '$receipt_season_start' AND '$receipt_season_end'";
      }

      $sql = "SELECT rcpt.id,rcpt.receipt_id,rcpt.category_id,rcpt.receipt_amount,rcpt.created_at,rcpt.record_status as receipt_status,stu.id as student_record_id,stu.stu_id,stu.stu_name,stu.stu_phone,stu.stu_email,stu.image_file_name,stu.stu_qualification,stu.stu_course_fees,stu.stu_course_discount,stu.fees_paid_before_dr,stu.student_status,stu.stu_dob,stu.stu_result,stu.record_status,stu.created_at as student_created_at,frn.center_name,crs.course_title,pc.name as category FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt ON rcpt.stu_id = stu.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc ON rcpt.category_id = pc.id " . $where_Clause . " ORDER BY rcpt.created_at DESC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Receipts($dataArr)
   {

      $record_status = $dataArr['record_status'];

      $where_Clause = "WHERE rcpt.record_status = '$record_status'";

      if ($dataArr['course_id'] > 0) {
         $course_id = $dataArr['course_id'];
         $where_Clause .= " AND stu.course_id = '$course_id'";
      }

      if ($dataArr['franchise_id'] > 0) {
         $franchise_id = $dataArr['franchise_id'];
         $where_Clause .= " AND stu.franchise_id = '$franchise_id'";
      }

      if ($dataArr['created'] > 0) {
         $created_at = date('Y-m-d', strtotime($dataArr['created']));
         $where_Clause .= " AND DATE(rcpt.created_at) = '$created_at'";
      }

      if (!empty($dataArr['receipt_season_start']) && empty($dataArr['receipt_season_end'])) {
         $receipt_season_start = $dataArr['receipt_season_start'];
         $where_Clause .= " AND DATE(rcpt.created_at) >='$receipt_season_start'";
      } else if (empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {
         $receipt_season_end = $dataArr['receipt_season_end'];
         $where_Clause .= " AND DATE(rcpt.created_at) <='$receipt_season_end'";
      } else if (!empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {
         $receipt_season_start = $dataArr['receipt_season_start'];
         $receipt_season_end = $dataArr['receipt_season_end'];
         $where_Clause .= " AND DATE(rcpt.created_at) BETWEEN '$receipt_season_start' AND '$receipt_season_end'";
      }

      $sql = "SELECT rcpt.id,rcpt.receipt_id,rcpt.category_id,rcpt.receipt_amount,rcpt.created_at,rcpt.record_status as receipt_status,stu.id as student_record_id,stu.stu_id,stu.stu_name,stu.stu_phone,stu.stu_email,stu.stu_course_fees,stu.stu_course_discount,stu.fees_paid_before_dr,stu.image_file_name,stu.stu_qualification,stu.student_status,stu.record_status,stu.created_at as student_created_at,frn.center_name,crs.course_title,pc.name as category FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt ON rcpt.stu_id = stu.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc ON rcpt.category_id = pc.id " . $where_Clause . " ORDER BY rcpt.id DESC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Receipt_Collection($dataArr)
   {

      $record_status = $dataArr['record_status'];

      $where_Clause = "WHERE rcpt.record_status = '$record_status'";

      if ($dataArr['course_id'] > 0) {
         $course_id = $dataArr['course_id'];
         $where_Clause .= " AND stu.course_id = '$course_id'";
      }

      if ($dataArr['franchise_id'] > 0) {
         $franchise_id = $dataArr['franchise_id'];
         $where_Clause .= " AND stu.franchise_id = '$franchise_id'";
      }

      if (!empty($dataArr['created'])) {
         $created_at = date('Y-m-d', strtotime($dataArr['created']));
         $where_Clause .= " AND DATE(rcpt.created_at) = '$created_at'";
      }

      if (!empty($dataArr['stu_id'])) {
         $stu_id = $dataArr['stu_id'];
         $where_Clause .= " AND rcpt.stu_id = '$stu_id'";
      }

      if (!empty($dataArr['receipt_season_start']) && empty($dataArr['receipt_season_end'])) {
         $receipt_season_start = $dataArr['receipt_season_start'];
         $where_Clause .= " AND DATE(rcpt.created_at) >='$receipt_season_start'";
      } else if (empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {
         $receipt_season_end = $dataArr['receipt_season_end'];
         $where_Clause .= " AND DATE(rcpt.created_at) <='$receipt_season_end'";
      } else if (!empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {
         $receipt_season_start = $dataArr['receipt_season_start'];
         $receipt_season_end = $dataArr['receipt_season_end'];
         $where_Clause .= " AND DATE(rcpt.created_at) BETWEEN '$receipt_season_start' AND '$receipt_season_end'";
      }

      $sql = "SELECT SUM(rcpt.receipt_amount) as receipt_amount, SUM(rcpt.late_fine) as late_fine, SUM(rcpt.extra_fees) as extra_fees FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt ON rcpt.stu_id = stu.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id " . $where_Clause . " ORDER BY rcpt.id DESC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Student_Receipt_Summary($dataArr)
   {
      $where = [];
      $params = [];

      // Mandatory filters
      $where[] = "stu.record_status = ?";
      $params[] = $dataArr['record_status'];

      $where[] = "stu.stu_id = ?";
      $params[] = $dataArr['student_id'];

      // Optional filter
      if (!empty($dataArr['franchise_id'])) {
         $where[] = "stu.franchise_id = ?";
         $params[] = $dataArr['franchise_id'];
      }

      $whereSql = "WHERE " . implode(" AND ", $where);

      // Pre-aggregated receipts (BIG performance win)
      $receiptSubQuery = "
         SELECT 
               stu_id,
               COUNT(id) AS receipt_count,
               SUM(receipt_amount) AS total_paid
         FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts
         WHERE record_status = 'active' GROUP BY stu_id
      ";

      $sql = "
         SELECT 
               stu.id,
               stu.stu_id,
               stu.stu_name,
               stu.stu_phone,
               stu.stu_course_fees,
               stu.stu_course_discount,
               stu.fees_paid_before_dr,
               stu.stu_email,
               stu.image_file_name,
               stu.created_at,

               tmp_stu.advanced_fees,
               tmp_stu.created_at AS advance_fees_date,

               frn.center_name,
               crs.course_title,
               crs.course_fees AS course_default_fees,

               IFNULL(rcpt.receipt_count, 0) AS receipt_count,
               IFNULL(rcpt.total_paid, 0) AS course_fees_paid

         FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu

         LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu 
               ON stu.tmp_stu_record_id = tmp_stu.tmp_id

         LEFT JOIN ($receiptSubQuery) rcpt 
               ON stu.stu_id = rcpt.stu_id

         LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
               ON stu.franchise_id = frn.id

         LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
               ON stu.course_id = crs.id

         $whereSql

         ORDER BY stu.created_at DESC
      ";

      return $this->conn->global_Fetch_Single_DB($sql, $params);
   }

   public function fetch_Global_Student($dataArr)
   {

      //pagination property
      $limit = $dataArr['limit'];
      $pageNo = $dataArr['pageNo'];
      $offset = ($pageNo - 1) * $limit;

      $record_status = $dataArr['record_status'];
      $student_status = $dataArr['student_status'];

      $where_Clause = "WHERE stu.record_status = '$record_status'";

      if (!empty($dataArr['verified_status'])) {
         if ($dataArr['verified_status'] == 'y') {
            $verified_status = '1';
         } else {
            $verified_status = '0';
         }
         $where_Clause .= " AND stu.verified_status = '$verified_status'";
      }

      if (!empty($dataArr['student_status'])) {
         $student_status = $dataArr['student_status'];
         $where_Clause .= " AND stu.student_status = '$student_status'";
      }

      if ($dataArr['course_id'] > 0) {
         $course_id = $dataArr['course_id'];
         $where_Clause .= " AND stu.course_id = '$course_id'";
      }

      if ($dataArr['franchise_id'] > 0) {
         $franchise_id = $dataArr['franchise_id'];
         $where_Clause .= " AND stu.franchise_id = '$franchise_id'";
      }

      if (!empty($dataArr['result_status'])) {
         $result_status = $dataArr['result_status'];
         $where_Clause .= " AND stu.stu_result = '$result_status'";
      }

      if ($dataArr['created'] > 0) {
         $created_at = date('Y-m-d', strtotime($dataArr['created']));
         $where_Clause .= " AND DATE(stu.created_at) = '$created_at'";
      }

      if (!empty($dataArr['search_start']) && empty($dataArr['search_end'])) {
         $search_start = $dataArr['search_start'];
         $where_Clause .= " AND DATE(stu.created_at) >='$search_start'";
      } else if (empty($dataArr['search_start']) && !empty($dataArr['search_end'])) {
         $search_end = $dataArr['search_end'];
         $where_Clause .= " AND DATE(stu.created_at) <='$search_end'";
      } else if (!empty($dataArr['search_start']) && !empty($dataArr['search_end'])) {
         $search_start = $dataArr['search_start'];
         $search_end = $dataArr['search_end'];
         $where_Clause .= " AND DATE(stu.created_at) BETWEEN '$search_start' AND '$search_end'";
      }

      if (!empty($dataArr['search_string'])) {
         $string = $dataArr['search_string'];
         $where_Clause .= " AND (stu.stu_id LIKE '%$string%' OR stu.stu_name LIKE '%$string%' OR stu.stu_father_name LIKE '%$string%' OR stu.stu_address LIKE '%$string%' OR stu.stu_phone LIKE '%$string%' OR stu.stu_email LIKE '%$string%' OR stu.stu_gender LIKE '%$string%' OR stu.stu_qualification LIKE '%$string%' OR stu.stu_marital_status LIKE '%$string%')";
      }

      //$sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."students WHERE `record_status` = '$record_status' ORDER BY id DESC";

      $sql_fetch_student = "SELECT stu.id,stu.stu_id,stu.stu_name,stu.stu_phone,stu.stu_dob,stu.record_status,stu.verified_status,stu.image_file_name,stu.student_status,stu.stu_result,stu.created_at,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id " . $where_Clause . " GROUP BY stu.id ORDER BY stu.id DESC";

      $sql_fetch_student_limit = $sql_fetch_student . " LIMIT $offset, $limit";

      //echo $sql_fetch_student_limit;exit();

      //$resultArr = $this->conn->global_Fetch_All_DB($sql); 

      $resultArr['data'] = $this->conn->global_Fetch_All_DB($sql_fetch_student_limit);
      $resultArr['row_count'] = $this->conn->global_Rows_Count_DB($sql_fetch_student);
      $resultArr['pageNo'] = $dataArr['pageNo'];
      $resultArr['limit'] = $dataArr['limit'];

      return $resultArr;
   }

   public function fetch_Due_Students_Data($dataArr)
   {
      // pagination property
      $limit = $dataArr['limit'];
      $pageNo = $dataArr['pageNo'];
      $offset = ($pageNo - 1) * $limit;

      $record_status = $dataArr['record_status'];

      // OPTIONAL FILTERS
      $extra_filters = "";

      if (isset($dataArr['student_id']) && $dataArr['student_id'] !== null) {
         $extra_filters .= " AND stu.stu_id = '" . $dataArr['student_id'] . "'";
      }

      if (!empty($dataArr['course_id']) && $dataArr['course_id'] > 0) {
         $extra_filters .= " AND stu.course_id = '" . $dataArr['course_id'] . "'";
      }

      if (!empty($dataArr['franchise_id']) && $dataArr['franchise_id'] > 0) {
         $extra_filters .= " AND stu.franchise_id = '" . $dataArr['franchise_id'] . "'";
      }

      // MAIN QUERY WITH DERIVED TABLE
      $base_query = "
   
      FROM (
         SELECT 
            stu.*,
            frn.center_name,
            crs.course_title,
   
            LEAST(
               (
                  GREATEST(
                     CEIL(
                        (
                           DATEDIFF(NOW(), stu.created_at) / 30.44
                           - COALESCE(stu.month_exclude_receipt, 0)
                        )
                     ),
                     0
                  )
                  * COALESCE(stu.monthly_course_fees, 0)
               ),
               (
                  COALESCE(stu.stu_course_fees, 0)
                  - COALESCE(stu.stu_course_discount, 0)
               )
            ) AS expected_amount
   
         FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu
   
         INNER JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
            ON stu.franchise_id = frn.id
   
         INNER JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
            ON stu.course_id = crs.id
   
         WHERE 
            stu.student_status IN ('admitted', 'continue') 
            AND stu.stu_result = 'unqualified' 
            AND COALESCE(stu.stu_course_fees, 0) > 0
            AND COALESCE(stu.monthly_course_fees, 0) > 0
            AND frn.owned_status = 'yes'
            AND stu.record_status = '$record_status'
            $extra_filters
   
      ) base
   
      INNER JOIN (
         SELECT 
            stu_id, 
            SUM(COALESCE(receipt_amount, 0)) AS total_paid
         FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts
         WHERE record_status = 'active'
         GROUP BY stu_id
      ) rcpt 
         ON base.stu_id = rcpt.stu_id
   
      WHERE 
         rcpt.total_paid < base.expected_amount
      ";

      // MAIN FETCH QUERY
      $sql_fetch_student = "SELECT 
         base.id,
         base.stu_id,
         base.stu_name,
         base.stu_phone,
         base.stu_dob,
         base.record_status,
         base.verified_status,
         base.image_file_name,
         base.student_status,
         base.stu_result,
         base.created_at,
         base.center_name,
         base.course_title,
   
         GREATEST(base.expected_amount - rcpt.total_paid, 0) AS total_due
   
      " . $base_query . "
   
      ORDER BY base.id DESC";

      // PAGINATION
      if (!array_key_exists('student_id', $dataArr) || $dataArr['student_id'] == null) {
         $sql_fetch_student .= " LIMIT $offset, $limit";
      }

      // COUNT QUERY
      $sql_count_rec = "SELECT COUNT(*) as total " . $base_query;

      // Debug
      // echo $sql_fetch_student; exit;

      $resultArr['data'] = $this->conn->global_Fetch_All_DB($sql_fetch_student);
      $resultArr['row_count'] = $this->conn->global_Count_Value_DB($sql_count_rec);
      $resultArr['pageNo'] = $dataArr['pageNo'];
      $resultArr['limit'] = $dataArr['limit'];

      return $resultArr;
   }

   public function fetch_Updated_Markup_Students_Data($dataArr)
   {
      //pagination property
      $limit = $dataArr['limit'];
      $pageNo = $dataArr['pageNo'];
      $offset = ($pageNo - 1) * $limit;

      $record_status = $dataArr['record_status'];

      $where_Clause = " WHERE stu.student_status IN ('admitted', 'continue') AND stu.stu_result = 'unqualified' AND stu.stu_course_fees > 0 AND 
      stu.monthly_course_fees > 0 AND frn.owned_status = 'yes' AND stu.record_status = '$record_status'";

      if (isset($dataArr['student_id']) && $dataArr['student_id'] !== null) {
         $student_id = $dataArr['student_id'];
         $where_Clause .= " AND stu.stu_id = '" . $student_id . "'";
      }

      if ($dataArr['course_id'] > 0) {
         $course_id = $dataArr['course_id'];
         $where_Clause .= " AND stu.course_id = '$course_id'";
      }

      if ($dataArr['franchise_id'] > 0) {
         $franchise_id = $dataArr['franchise_id'];
         $where_Clause .= " AND stu.franchise_id = '$franchise_id'";
      }

      $sql_fetch_student = "SELECT stu.id,stu.stu_id,stu.stu_name,stu.stu_phone,stu.stu_dob,stu.record_status,stu.verified_status,stu.image_file_name,stu.student_status,
      stu.stu_result,stu.created_at,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX .
         "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX .
         "student_receipts rcpt ON stu.stu_id = rcpt.stu_id" . $where_Clause . " GROUP BY stu.id ORDER BY stu.id DESC";

      if (!array_key_exists('student_id', $dataArr) || $dataArr['student_id'] == null) {
         $sql_fetch_student = $sql_fetch_student . " LIMIT $offset, $limit";
      }

      $sql_count_rec = "SELECT stu.id,stu.stu_id,stu.stu_name,stu.stu_phone,stu.stu_dob,stu.record_status,stu.verified_status,stu.image_file_name,stu.student_status,
      stu.stu_result,stu.created_at,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX .
         "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX .
         "student_receipts rcpt ON stu.stu_id = rcpt.stu_id" . $where_Clause . " GROUP BY stu.id ORDER BY stu.id DESC";

      //echo $sql_fetch_student;exit();

      $resultArr['data'] = $this->conn->global_Fetch_All_DB($sql_fetch_student);
      $resultArr['row_count'] = $this->conn->global_Rows_Count_DB($sql_count_rec);
      $resultArr['pageNo'] = $dataArr['pageNo'];
      $resultArr['limit'] = $dataArr['limit'];

      return $resultArr;
   }

   public function fetch_Fresh_Students($dataArr)
   {

      $where_Clause = "WHERE stu.student_status = 'admitted' AND stu.created_at >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)";

      if (!empty($dataArr['franchise_id'])) {
         $franchise_id = $dataArr['franchise_id'];
         $where_Clause .= " AND stu.franchise_id = '$franchise_id'";
      }

      $sql = "SELECT stu.id,stu.stu_id,stu.stu_name,stu.stu_father_name,stu.stu_phone,stu.student_status,stu.created_at,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id " . $where_Clause . " GROUP BY stu.id ORDER BY stu.id DESC";

      //echo $sql;exit(); 

      $resultArr = $this->conn->global_Fetch_All_DB($sql);
      return $resultArr;
   }

   public function fetch_Tmp_Students($dataArr)
   {

      //pagination property
      $limit = $dataArr['limit'];
      $pageNo = $dataArr['pageNo'];
      $offset = ($pageNo - 1) * $limit;

      $record_status = $dataArr['record_status'];

      $where_Clause = "WHERE tmp_stu.record_status = '$record_status'";

      if ($dataArr['conversion_status'] != 'null') {
         $conversion_status = $dataArr['conversion_status'];
         $where_Clause .= " AND tmp_stu.conversion_status = '$conversion_status'";
      }

      if ($dataArr['verified_status'] != 'null') {
         $verified_status = $dataArr['verified_status'];
         $where_Clause .= " AND tmp_stu.verified_status = '$verified_status'";
      }

      if (!empty($dataArr['franchise_id'])) {
         $franchise_id = $dataArr['franchise_id'];
         $where_Clause .= " AND tmp_stu.franchise_id = '$franchise_id'";
      }

      if ($dataArr['course_id'] > 0) {
         $course_id = $dataArr['course_id'];
         $where_Clause .= " AND tmp_stu.course_id = '$course_id'";
      }

      if (!empty($dataArr['result_status'])) {
         $result_status = $dataArr['result_status'];
         $where_Clause .= " AND tmp_stu.stu_result = '$result_status'";
      }

      if ($dataArr['created'] > 0) {
         $created_at = date('Y-m-d', strtotime($dataArr['created']));
         $where_Clause .= " AND DATE(tmp_stu.created_at) = '$created_at'";
      }

      if (!empty($dataArr['search_start']) && empty($dataArr['search_end'])) {
         $search_start = $dataArr['search_start'];
         $where_Clause .= " AND DATE(tmp_stu.created_at) >='$search_start'";
      } else if (empty($dataArr['search_start']) && !empty($dataArr['search_end'])) {
         $search_end = $dataArr['search_end'];
         $where_Clause .= " AND DATE(tmp_stu.created_at) <='$search_end'";
      } else if (!empty($dataArr['search_start']) && !empty($dataArr['search_end'])) {
         $search_start = $dataArr['search_start'];
         $search_end = $dataArr['search_end'];
         $where_Clause .= " AND DATE(tmp_stu.created_at) BETWEEN '$search_start' AND '$search_end'";
      }

      if (!empty($dataArr['search_string'])) {
         $string = $dataArr['search_string'];
         $where_Clause .= " AND (tmp_stu.tmp_stu_id LIKE '%$string%' OR tmp_stu.stu_name LIKE '%$string%' OR tmp_stu.stu_father_name LIKE '%$string%' OR tmp_stu.stu_phone LIKE '%$string%')";
      }

      $sql = "SELECT tmp_stu.*,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON tmp_stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON tmp_stu.course_id = crs.id " . $where_Clause . " GROUP BY tmp_stu.tmp_id ORDER BY tmp_stu.tmp_id DESC";

      $sql_fetch_student_limit = $sql . " LIMIT $offset, $limit";

      //echo $sql_fetch_student_limit;exit();

      //$resultArr = $this->conn->global_Fetch_All_DB($sql); 

      $resultArr['data'] = $this->conn->global_Fetch_All_DB($sql_fetch_student_limit);
      $resultArr['row_count'] = $this->conn->global_Rows_Count_DB($sql);
      $resultArr['pageNo'] = $dataArr['pageNo'];
      $resultArr['limit'] = $dataArr['limit'];

      return $resultArr;
   }

   public function fetch_Student_Admission_Receipt($student_id)
   {
      $sql = "SELECT rcpt.*,pc.name as category FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc ON rcpt.category_id = pc.id WHERE rcpt.stu_id = '$student_id' ORDER BY rcpt.id ASC LIMIT 1";

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);
      return $resultArr;
   }

   public function fetch_Dashboard_Student_Data($dataArr)
   {

      $current_week_first_day = date('Y/m/d', strtotime("monday this week"));
      $current_month_first_day = date('Y/m/d', strtotime("first day of this month"));
      $current_year_first_day = date('Y/m/d', strtotime("first day of January this year"));
      $today = date('Y/m/d');

      $fetchType = $dataArr['fetchType'];

      switch ($fetchType) {
         case 'today':
            $where_Clause = "WHERE DATE(stu.created_at) = '$today'";
            break;

         case 'weekly':
            $where_Clause = "WHERE DATE(stu.created_at) >= '$current_week_first_day'";
            break;

         case 'monthly':
            $where_Clause = "WHERE DATE(stu.created_at) >= '$current_month_first_day'";
            break;

         case 'annual':
            $where_Clause = "WHERE DATE(stu.created_at) >= '$current_year_first_day'";
            break;

         default:
            $where_Clause = "WHERE DATE(stu.created_at) >= '$current_month_first_day'";
            break;
      }

      if ($dataArr['franchise_id'] > 0) {
         $franchise_id = $dataArr['franchise_id'];
         $where_Clause .= " AND stu.franchise_id = '$franchise_id'";
         $where_total_clause = "WHERE stu.franchise_id = '$franchise_id'";
      } else {
         $where_total_clause = "WHERE stu.id IS NOT NULL";
      }

      $sql = "SELECT stu.*,frn.center_name,crs.course_title,rslt.stu_result FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "results rslt ON stu.stu_id = rslt.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id " . $where_Clause . " GROUP BY stu.id ORDER BY stu.id DESC";

      $sql_row_count = "SELECT stu.*,frn.center_name,crs.course_title,rslt.stu_result FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "results rslt ON stu.stu_id = rslt.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id " . $where_total_clause . " GROUP BY stu.id ORDER BY stu.id DESC";

      //echo $sql;exit();

      $resultArr['data'] = $this->conn->global_Fetch_All_DB($sql);
      $resultArr['row_count'] = $this->conn->global_Rows_Count_DB($sql_row_count);

      return $resultArr;
   }

   public function fetch_Global_Student_Recipts($dataArr)
   {

      $record_status = $dataArr['record_status'];

      $where_Clause = "WHERE stu.record_status = '$record_status'";

      if ($dataArr['course_id'] > 0) {
         $course_id = $dataArr['course_id'];
         $where_Clause .= "AND crs.id = '$course_id'";
      }

      if ($dataArr['franchise_id'] > 0) {
         $franchise_id = $dataArr['franchise_id'];
         $where_Clause .= "AND frn.id = '$franchise_id'";
      }

      if (!empty($dataArr['search_string'])) {
         $string = $dataArr['search_string'];
         $where_Clause .= "AND stu.stu_id LIKE '%$string%' OR stu.stu_id LIKE '%$string%' OR stu.stu_name LIKE '%$string%' OR stu.stu_father_name LIKE '%$string%' OR stu.stu_address LIKE '%$string%' OR stu.stu_phone LIKE '%$string%' OR stu.stu_email LIKE '%$string%' OR stu.stu_gender LIKE '%$string%' OR stu.stu_qualification LIKE '%$string%' OR stu.stu_marital_status LIKE '%$string%'";
      }

      if ($dataArr['created'] > 0) {
         $created_at = date('Y-m-d', strtotime($dataArr['created']));
         $where_Clause .= "AND DATE(stu.created_at) = '$created_at'";
      }

      if (!empty($dataArr['search_start']) && empty($dataArr['search_end'])) {
         $search_start = $dataArr['search_start'];
         $where_Clause .= "AND DATE(stu.created_at) >='$search_start'";
      } else if (empty($dataArr['search_start']) && !empty($dataArr['search_end'])) {
         $search_end = $dataArr['search_end'];
         $where_Clause .= "AND DATE(stu.created_at) <='$search_end'";
      } else if (!empty($dataArr['search_start']) && !empty($dataArr['search_end'])) {
         $search_start = $dataArr['search_start'];
         $search_end = $dataArr['search_end'];
         $where_Clause .= "AND DATE(stu.created_at) BETWEEN '$search_start' AND '$search_end'";
      }

      $sql = "SELECT stu.id,stu.stu_id,stu.stu_name,stu.stu_father_name,stu.stu_phone,stu.stu_email,stu.stu_gender,stu.stu_marital_status,stu.image_file_name,stu.stu_qualification,stu.stu_course_fees,stu.stu_course_discount,stu.fees_paid_before_dr,stu.stu_address,stu.student_status,stu.stu_result,stu.stu_dob,stu.record_status,stu.created_at,frn.center_name,crs.course_title,COUNT(DISTINCT rcpt.id) as receipt_count FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt ON stu.stu_id = rcpt.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id " . $where_Clause . " GROUP BY stu.stu_id DESC";

      //echo $sql;exit();

      $sql_row_count = "SELECT stu.id FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu WHERE stu.record_status='active' ORDER BY stu.id DESC";

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Dashboard_Receipt_Data($dataArr)
   {

      $current_week_first_day = date('Y/m/d', strtotime("monday this week"));
      $current_month_first_day = date('Y/m/d', strtotime("first day of this month"));
      $current_year_first_day = date('Y/m/d', strtotime("first day of January this year"));
      $today = date('Y-m-d');

      $fetchType = $dataArr['fetchType'];

      switch ($fetchType) {
         case 'today':
            $where_Clause = "WHERE DATE(rcpt.created_at) = '$today' OR rcpt.created_at BETWEEN '$today' AND '$today 23:59:59'";
            break;

         case 'weekly':
            $where_Clause = "WHERE DATE(rcpt.created_at) >= '$current_week_first_day'";
            break;

         case 'monthly':
            $where_Clause = "WHERE DATE(rcpt.created_at) >= '$current_month_first_day'";
            break;

         case 'annual':
            $where_Clause = "WHERE DATE(rcpt.created_at) >= '$current_year_first_day'";
            break;

         default:
            $where_Clause = "WHERE DATE(rcpt.created_at) >= '$current_month_first_day'";
            break;
      }

      if ($dataArr['franchise_id'] > 0) {
         $franchise_id = $dataArr['franchise_id'];
         $where_Clause .= " AND stu.franchise_id = '$franchise_id'";
         $where_total_clause = "WHERE stu.franchise_id = '$franchise_id'";
      } else {
         $where_total_clause = "WHERE stu.id IS NOT NULL";
      }

      $sql = "SELECT rcpt.id,rcpt.receipt_id,rcpt.receipt_amount,rcpt.late_fine,rcpt.extra_fees,rcpt.record_status as receipt_status,rcpt.verified_status,rcpt.created_at,stu.id as student_record_id,stu.stu_id,stu.stu_name,stu.stu_phone,stu.stu_email,stu.image_file_name,stu.stu_qualification,stu.stu_course_fees,stu.stu_course_discount,stu.fees_paid_before_dr,stu.student_status,stu.record_status,stu.created_at as student_created_at,frn.center_name,crs.course_title,pc.name as category FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu ON rcpt.stu_id = stu.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc ON rcpt.category_id = pc.id " . $where_Clause . " ORDER BY rcpt.id DESC";

      $sql_row_count = "SELECT rcpt.id,rcpt.receipt_id,rcpt.receipt_amount,rcpt.late_fine,rcpt.extra_fees,rcpt.record_status as receipt_status,rcpt.verified_status,rcpt.created_at,stu.id as student_record_id,stu.stu_id,stu.stu_name,stu.stu_phone,stu.stu_email,stu.image_file_name,stu.stu_qualification,stu.stu_course_fees,stu.stu_course_discount,stu.fees_paid_before_dr,stu.student_status,stu.record_status,stu.created_at as student_created_at,frn.center_name,crs.course_title,pc.name as category FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu ON rcpt.stu_id = stu.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc ON rcpt.category_id = pc.id " . $where_total_clause . " ORDER BY rcpt.id DESC";

      //echo $sql;exit();

      $resultArr['data'] = $this->conn->global_Fetch_All_DB($sql);
      $resultArr['row_count'] = $this->conn->global_Rows_Count_DB($sql_row_count);

      return $resultArr;
   }

   public function fetch_Global_Exams($dataArr = array())
   {

      $record_status = $dataArr['record_status'];

      $where_Clause = "WHERE exm.record_status = '$record_status'";

      $sql = "SELECT exm.*,COUNT(DISTINCT exq.id) as question_count,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exams exm LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions exq ON exm.id = exq.exam_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON exm.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON exm.course_id = crs.id " . $where_Clause . " ORDER BY exm.id DESC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Exam_Questions($exam_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions eqs WHERE eqs.exam_id = '$exam_id' ORDER BY eqs.ordering ASC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_User_Exam_Answers($answerParamArr)
   {

      $exam_id = $answerParamArr['exam_id'];
      $student_id = $answerParamArr['student_id'];

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_answers ea WHERE ea.exam_id = '$exam_id' AND ea.student_id = '$student_id' ORDER BY ea.ques_id ASC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Flagged_Questions($answerParamArr)
   {

      $exam_id = $answerParamArr['exam_id'];
      $student_id = $answerParamArr['student_id'];

      $sql = "SELECT efq.ques_id FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions efq WHERE efq.exam_id = '$exam_id' AND efq.student_id = '$student_id' ORDER BY efq.ques_id ASC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Viewed_Questions($answerParamArr)
   {

      $exam_id = $answerParamArr['exam_id'];
      $student_id = $answerParamArr['student_id'];

      $sql = "SELECT evq.ques_id FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_viewed_questions evq WHERE evq.exam_id = '$exam_id' AND evq.student_id = '$student_id' ORDER BY evq.ques_id ASC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Parent_Category($record_status = 'active')
   {

      //$sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."parent_category ORDER BY id DESC";

      $sql = "SELECT pc.id,pc.parent_category,pc.name,pc.record_status,pc.created_at FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc WHERE pc.record_status = '$record_status' ORDER BY pc.id DESC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Cities($record_status = 'active')
   {

      //$sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."parent_category ORDER BY id DESC";

      $sql = "SELECT c.* FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "cities c WHERE c.record_status = '$record_status' ORDER BY c.id DESC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Enquiry($dataArr)
   {

      //pagination property
      $limit = $dataArr['limit'];
      $pageNo = $dataArr['pageNo'];
      $offset = ($pageNo - 1) * $limit;

      $record_status = $dataArr['record_status'];

      $where_Clause = "WHERE enq.record_status = '$record_status'";

      if (strlen($dataArr['enquiry_type']) > 0) {
         $enquiry_type = $dataArr['enquiry_type'];
         $where_Clause .= "AND enq.enquiry_type = '$enquiry_type'";
      }

      if ($dataArr['course_id'] > 0) {
         $course_id = $dataArr['course_id'];
         $where_Clause .= "AND crs.id = '$course_id'";
      }

      $sql_fetch_enquiry = "SELECT enq.id,enq.user_name,enq.user_email,enq.user_phone,enq.user_city,enq.enquiry_type,enq.subject,enq.user_message,enq.record_status,enq.created_at,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "enquiry enq LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON enq.subject = crs.id " . $where_Clause . " ORDER BY enq.id DESC LIMIT $offset,$limit";

      //echo $sql_fetch_enquiry;exit();

      $sql_row_count = "SELECT enq.id,enq.user_name,enq.user_email,enq.user_phone,enq.user_city,enq.enquiry_type,enq.subject,enq.user_message,enq.record_status,enq.created_at,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "enquiry enq LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON enq.subject = crs.id " . $where_Clause . " ORDER BY enq.id DESC";

      $resultArr['data'] = $this->conn->global_Fetch_All_DB($sql_fetch_enquiry);
      $resultArr['row_count'] = $this->conn->global_Rows_Count_DB($sql_row_count);
      $resultArr['pageNo'] = $dataArr['pageNo'];
      $resultArr['limit'] = $dataArr['limit'];

      return $resultArr;
   }

   public function fetch_Email_Templates($record_status = 'active')
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template et WHERE et.record_status='$record_status' ORDER BY id DESC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_News($dataArr = array())
   {

      $record_status = $dataArr['record_status'];

      $where_Clause = "WHERE nws.record_status = '$record_status'";

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "news nws " . $where_Clause . " ORDER BY nws.id DESC";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Single_Data($type, $row_id)
   {
      switch ($type) {

         case 'franchise':
            $where_Clause = "`id` = '$row_id'";
            break;

         case 'student':
            $type = "students";
            $where_Clause = "`id` = '$row_id'";
            break;

         case 'course':
            $where_Clause = "`id` = '$row_id'";
            break;

         case 'gallery':
            $where_Clause = "`id` = '$row_id'";
            break;

         case 'home_sliders':
            $where_Clause = "`id` = '$row_id'";
            break;

         case 'student':
            $where_Clause = "`id` = '$row_id'";
            break;

         case 'student_receipts':
            $where_Clause = "`id` = '$row_id'";
            break;

         case 'news':
            $where_Clause = "`id` = '$row_id'";
            break;
      }

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $type . " WHERE " . $where_Clause;
      //echo $sql;exit;
      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function check_Slug_Availibility($type, $field, $slug)
   {
      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $type . " WHERE `$field`='$slug'";
      //echo $sql;exit();
      $retrunArr = $this->conn->global_Fetch_Single_DB($sql);

      return $retrunArr;
   }

   public function fetch_Single_Parent_Category($parent_category)
   {
      $sql = "SELECT pc.id, pc.parent_category, pc.name 
               FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc 
               WHERE pc.parent_category = ?
               ORDER BY pc.id DESC";

      $resultArr = $this->conn->global_Fetch_All_DB($sql, [$parent_category]);

      return $resultArr;
   }

   public function fetch_Last_Franchise_Detail()
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise ORDER BY fran_id DESC LIMIT 1";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Last_Student_Detail()
   {

      $sql_current_student = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students ORDER BY id DESC LIMIT 1";
      //echo $sql_current_student;exit;
      $lst_stu_id = $this->conn->global_Fetch_Single_DB($sql_current_student)->stu_id;

      return array('lst_stu_id' => $lst_stu_id);
   }

   public function fetch_Last_Receipt_Detail()
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts ORDER BY id DESC LIMIT 1";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Receipt_Detail($receipt_id)
   {

      $sql = "SELECT rcpt.*,pc.name as category FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc ON rcpt.category_id = pc.id WHERE rcpt.id='$receipt_id'";
      //echo $sql;exit(); 
      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Student_Exam_Detail($exam_id)
   {

      $sql = "SELECT exm.*,COUNT(DISTINCT exq.id) as question_count,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exams exm LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions exq ON exm.id = exq.exam_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON exm.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON exm.course_id = crs.id WHERE exm.id = '$exam_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Email_Template_Detail($template_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template WHERE `id` = '$template_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_News_Detail($news_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "news WHERE `id` = '$news_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function manage_Global_Franchise($franDataArr)
   {

      $fran_row_id = $franDataArr['fran_row_id'];

      $fran_pass = $franDataArr['fran_pass'];
      $fran_og_pass = $franDataArr['fran_og_pass'];

      $center_name = $franDataArr['center_name'];
      $seo_url_structure = $franDataArr['seo_url_structure'];
      $owner_name = $franDataArr['owner_name'];
      $fran_phone = $franDataArr['fran_phone'];
      $fran_email = $franDataArr['fran_email'];
      $fran_address = $franDataArr['fran_address'];
      $owned_status = $franDataArr['owned_status'];
      $record_status = $franDataArr['record_status'];
      $featured_status = $franDataArr['featured_status'];
      $fran_description = $franDataArr['fran_description'];

      $fran_image = $franDataArr['fran_image'];
      $fran_pdf_name = $franDataArr['fran_pdf_name'];
      $user_role = $franDataArr['user_role'];

      if ($fran_row_id != 'null') {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise SET `fran_pass` = '$fran_pass',`fran_og_pass` = '$fran_og_pass',`center_name` = '$center_name',`seo_url_structure` = '$seo_url_structure', `owner_name` = '$owner_name',`fran_phone` = '$fran_phone', `fran_email`= '$fran_email', `fran_address` = '$fran_address', `owned_status` = '$owned_status', `record_status` = '$record_status',`featured_status` = '$featured_status', `fran_description` = '$fran_description',`fran_image` = '$fran_image',`fran_pdf_name` = '$fran_pdf_name',`user_role` = '$user_role',`updated_at` = now() WHERE `id`='$fran_row_id'";

         //echo $sql;exit();
      } else {
         //franchise id 
         $fran_id = $franDataArr['fran_id'];

         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise SET  `fran_id` = '$fran_id',`fran_pass` = '$fran_pass',`fran_og_pass` = '$fran_og_pass',`center_name` = '$center_name',`seo_url_structure` = '$seo_url_structure', `owner_name` = '$owner_name',`fran_phone` = '$fran_phone', `fran_email`= '$fran_email', `fran_address` = '$fran_address',`owned_status` = '$owned_status', `record_status` = '$record_status',`featured_status` = '$featured_status',`fran_description` = '$fran_description',`fran_image` = '$fran_image',`fran_pdf_name` = '$fran_pdf_name',`user_role` = '$user_role', `created_at` = now()";

         //echo $sql;exit();
      }

      $resultArr = $this->conn->global_CRUD_DB($sql);

      if ($resultArr["check"] == "success" && $_SESSION['user_type'] == 'franchise') {
         $_SESSION['user_role'] = unserialize($user_role);
         return $resultArr;
      } else {
         return $resultArr;
      }

      return $resultArr;
   }

   public function manage_Global_Student($stuDataArr)
   {

      $stu_row_id = $stuDataArr['stu_row_id'];

      $stu_name = $stuDataArr['stu_name'];
      $stu_father_name = $stuDataArr['stu_father_name'];
      $stu_phone = $stuDataArr['stu_phone'];
      $stu_email = $stuDataArr['stu_email'];
      $stu_gender = !empty($stuDataArr['stu_gender']) ? $stuDataArr['stu_gender'] : 'none';
      $stu_marital_status = !empty($stuDataArr['stu_marital_status']) ? $stuDataArr['stu_marital_status'] : 'none';
      $stu_address = $stuDataArr['stu_address'];

      $course_id = $stuDataArr['course_id'];

      $stu_qualification = $stuDataArr['stu_qualification'];

      $student_status = $stuDataArr['student_status'];
      $stu_result = $stuDataArr['stu_result'];
      $franchise_id = $stuDataArr['franchise_id'];
      $stu_dob = $stuDataArr['stu_dob'];
      $record_status = $stuDataArr['record_status'];
      $verified_status = $stuDataArr['verified_status'];
      $conversion_status = $stuDataArr['conversion_status'];

      $stu_course_fees = $stuDataArr['stu_course_fees'];
      $monthly_course_fees = $stuDataArr['monthly_course_fees'];
      $month_exclude_receipt = $stuDataArr['month_exclude_receipt'];
      $stu_course_discount = $stuDataArr['stu_course_discount'];
      $fees_paid_before_dr = $stuDataArr['fees_paid_before_dr'];

      $stu_address = $stuDataArr['stu_address'];
      $image_file_name = $stuDataArr['image_file_name'];
      $stu_notes = $stuDataArr['stu_notes'];

      if (!empty($stu_row_id) && $stu_row_id != "null") {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students SET `stu_name` = '$stu_name', `stu_father_name` = '$stu_father_name',`stu_phone` = '$stu_phone', `stu_email`= '$stu_email', `stu_gender` = '$stu_gender', `stu_marital_status` = '$stu_marital_status', `stu_address` = '$stu_address', `course_id` = '$course_id', `stu_qualification` = '$stu_qualification',`stu_course_fees` = '$stu_course_fees', `monthly_course_fees` = '$monthly_course_fees', `month_exclude_receipt` = '$month_exclude_receipt', `stu_course_discount` = '$stu_course_discount', `fees_paid_before_dr` = '$fees_paid_before_dr', `student_status` = '$student_status', `stu_result` = '$stu_result', `franchise_id` = '$franchise_id', `stu_dob` = '$stu_dob', `record_status` = '$record_status', `verified_status` = '$verified_status', `conversion_status` = '$conversion_status', `stu_address` = '$stu_address',`image_file_name` = '$image_file_name',`stu_notes` = '$stu_notes', `updated_at` = now() WHERE `id`='$stu_row_id'";
      } else {
         //student id 
         $stu_id = $stuDataArr['stu_id'];

         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "students SET `stu_id` = '$stu_id',`stu_name` = '$stu_name', `stu_father_name` = '$stu_father_name',`stu_phone` = '$stu_phone', `stu_email`= '$stu_email', `stu_gender` = '$stu_gender', `stu_marital_status` = '$stu_marital_status', `stu_address` = '$stu_address', `course_id` = '$course_id',`stu_qualification` = '$stu_qualification',`stu_course_fees` = '$stu_course_fees', `monthly_course_fees` = '$monthly_course_fees', `month_exclude_receipt` = '$month_exclude_receipt', `stu_course_discount` = '$stu_course_discount', `fees_paid_before_dr` = '$fees_paid_before_dr', `student_status` = '$student_status', `stu_result` = '$stu_result', `franchise_id` = '$franchise_id', `stu_dob` = '$stu_dob', `record_status` = '$record_status',`image_file_name` = '$image_file_name',`stu_notes` = '$stu_notes', `created_at` = now()";
      }

      //echo $sql;exit();

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function manage_Student_Admission($stuDataArr)
   {

      $student_id = $stuDataArr['student_id'];

      $stu_name = $stuDataArr['stu_name'];
      $stu_father_name = $stuDataArr['stu_father_name'];
      $stu_phone = $stuDataArr['stu_phone'];

      $course_id = $stuDataArr['course_id'];
      $franchise_id = $stuDataArr['franchise_id'];

      $student_status = $stuDataArr['student_status'];
      $record_status = $stuDataArr['record_status'];

      $stu_course_fees = $stuDataArr['stu_course_fees'];
      $monthly_course_fees = $stuDataArr['monthly_course_fees'];
      $stu_course_discount = $stuDataArr['stu_course_discount'];
      $fees_paid_before_dr = $stuDataArr['fees_paid_before_dr'];

      $verified_status = $stuDataArr['verified_status'];

      if ($student_id != 'null') {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students SET `stu_name` = '$stu_name', `stu_father_name` = '$stu_father_name',`stu_phone` = '$stu_phone', `course_id` = '$course_id', `franchise_id` = '$franchise_id', `stu_course_fees` = '$stu_course_fees', `monthly_course_fees` = '$monthly_course_fees', `stu_course_discount` = '$stu_course_discount', `fees_paid_before_dr` = '$fees_paid_before_dr', `student_status` = '$student_status', `record_status` = '$record_status', `verified_status` = '$verified_status', `updated_at` = now() WHERE `id`='$student_id'";
      } else {
         //student id 
         $stu_id = $stuDataArr['stu_id'];
         $tmp_stu_record_id = $stuDataArr['tmp_stu_record_id'];

         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "students SET `stu_id` = '$stu_id', `tmp_stu_record_id` = '$tmp_stu_record_id', `stu_name` = '$stu_name', `stu_father_name` = '$stu_father_name',`stu_phone` = '$stu_phone', `course_id` = '$course_id', `franchise_id` = '$franchise_id', `stu_course_fees` = '$stu_course_fees', `monthly_course_fees` = '$monthly_course_fees', `stu_course_discount` = '$stu_course_discount', `fees_paid_before_dr` = '$fees_paid_before_dr', `student_status` = '$student_status', `record_status` = '$record_status', `created_at` = now()";
      }

      //echo $sql;exit();

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function manage_Temp_Student($stuDataArr)
   {

      $tmp_id = $stuDataArr['tmp_id'];

      $stu_name = $stuDataArr['stu_name'];
      $stu_father_name = $stuDataArr['stu_father_name'];
      $stu_phone = $stuDataArr['stu_phone'];

      $course_id = $stuDataArr['course_id'];
      $franchise_id = $stuDataArr['franchise_id'];

      $advanced_fees = $stuDataArr['advanced_fees'];

      if ($tmp_id != 'null') {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students SET `stu_name` = '$stu_name', `stu_father_name` = '$stu_father_name',`stu_phone` = '$stu_phone', `course_id` = '$course_id', `franchise_id` = '$franchise_id', `advanced_fees` = '$advanced_fees',`verified_status` = '0', `updated_at` = now() WHERE `tmp_id`='$tmp_id'";
      } else {
         //student id 
         $tmp_stu_id = $stuDataArr['tmp_stu_id'];

         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students SET `tmp_stu_id` = '$tmp_stu_id', `stu_name` = '$stu_name', `stu_father_name` = '$stu_father_name',`stu_phone` = '$stu_phone', `course_id` = '$course_id', `franchise_id` = '$franchise_id', `advanced_fees` = '$advanced_fees', `created_at` = now()";
      }

      //echo $sql;exit();

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_student_monthly_course_fees($paramArr)
   {
      $stu_id = $paramArr['stu_id'];
      $monthly_course_fees = $paramArr['monthly_course_fees'];

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students SET `monthly_course_fees` = '$monthly_course_fees',`updated_at` = now() WHERE `stu_id`='$stu_id'";

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function manage_Global_Course($courseDataArr)
   {

      $course_id = $courseDataArr['course_id'];

      $course_title = $courseDataArr['course_title'];
      $seo_url_structure = $courseDataArr['seo_url_structure'];
      $course_fees = $courseDataArr['course_fees'];
      $course_duration = $courseDataArr['course_duration'];

      $record_status = $courseDataArr['record_status'];
      $featured_status = $courseDataArr['featured_status'];
      $course_description = $courseDataArr['course_description'];
      $course_thumbnail = $courseDataArr['course_thumbnail'];
      $course_pdf = $courseDataArr['course_pdf'];

      if ($course_id > 0) {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "course SET `course_title` = '$course_title',`seo_url_structure` = '$seo_url_structure',`course_fees` = '$course_fees',`course_duration` = '$course_duration',`record_status` = '$record_status',`featured_status` = '$featured_status',`course_description` = '$course_description',`course_thumbnail` = '$course_thumbnail', `course_pdf` = '$course_pdf', `updated_at` = now() WHERE `id`='$course_id'";

         //echo $sql;exit();
      } else {
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "course SET `course_title` = '$course_title',`seo_url_structure` = '$seo_url_structure',`course_fees` = '$course_fees',`course_duration` = '$course_duration',`record_status` = '$record_status',`featured_status` = '$featured_status',`course_description` = '$course_description',`course_thumbnail` = '$course_thumbnail',`course_pdf` = '$course_pdf',`created_at` = now()";

         //echo $sql;exit();
      }

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function manage_Global_Exam($examDataArr)
   {

      $exam_id = $examDataArr['exam_id'];

      //Constructing query variable
      $name = $examDataArr['name'];
      $franchise_id = $examDataArr['franchise_id'];
      $course_id = $examDataArr['course_id'];
      $total_marks = $examDataArr['total_marks'];
      $hours = $examDataArr['hours'];
      $minutes = $examDataArr['minutes'];
      $exam_date = $examDataArr['exam_date'];
      $instructions = $examDataArr['instructions'];
      $record_status = $examDataArr['record_status'];

      $optional_pdf = $examDataArr['optional_pdf'];

      if ($exam_id > 0) {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "exams SET `name` = '$name',`franchise_id` = '$franchise_id',`course_id` = '$course_id',`total_marks` = '$total_marks',`hours` = '$hours',`minutes` = '$minutes',`exam_date` = '$exam_date',`instructions` = '$instructions',`optional_pdf` = '$optional_pdf',`record_status` = '$record_status',`updated_at`=now() WHERE `id`='$exam_id'";
      } else {
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exams SET `name` = '$name',`franchise_id` = '$franchise_id',`course_id` = '$course_id',`total_marks` = '$total_marks',`hours` = '$hours',`minutes` = '$minutes',`exam_date` = '$exam_date',`instructions` = '$instructions',`optional_pdf` = '$optional_pdf',`record_status` = '$record_status',`created_at` = now()";
      }

      //echo $sql;exit;

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function fetch_Last_Question_Ordering($exam_id)
   {
      $sql = "SELECT eqs.ordering FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions eqs WHERE eqs.exam_id='$exam_id' ORDER BY eqs.ordering DESC LIMIT 1";

      //echo $sql;exit;

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function import_Exam_Questions($questionData)
   {

      $exam_id = $questionData['exam_id'];

      $ques = mysqli_real_escape_string(DB::$WRITELINK, trim($questionData['ques']));
      $opt1 = mysqli_real_escape_string(DB::$WRITELINK, trim($questionData['opt1']));
      $opt2 = mysqli_real_escape_string(DB::$WRITELINK, trim($questionData['opt2']));
      $opt3 = mysqli_real_escape_string(DB::$WRITELINK, trim($questionData['opt3']));
      $opt4 = mysqli_real_escape_string(DB::$WRITELINK, trim($questionData['opt4']));
      $cor_ans = mysqli_real_escape_string(DB::$WRITELINK, trim($questionData['cor_ans']));
      $marks = mysqli_real_escape_string(DB::$WRITELINK, trim($questionData['marks']));
      $ordering = mysqli_real_escape_string(DB::$WRITELINK, trim($questionData['ordering']));

      $record_status = $questionData['record_status'];

      $sql_insert_question = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions SET `exam_id` = '$exam_id',`ques` = '$ques',`opt1`='$opt1',`opt2`='$opt2',`opt3`='$opt3',`opt4`='$opt4',`cor_ans`='$cor_ans',`marks`='$marks',`ordering`='$ordering',`record_status`='$record_status',`updated_at` = now()";

      //echo $sql_insert_question;exit();

      //Call save exam questions
      $this->conn->global_CRUD_DB($sql_insert_question);
   }

   public function update_Exam_Questions($postData)
   {

      $exam_id = $postData['exam_id'];
      $questions = $postData['questions'];

      $sql_delete_question = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions WHERE `exam_id` = '$exam_id'";

      //echo $sql_delete_question;exit;

      $this->conn->global_CRUD_DB($sql_delete_question);

      //print_r($questions);exit;

      foreach ($questions as $index => $question) {

         $ques = mysqli_real_escape_string(DB::$WRITELINK, trim($question['ques']));
         $opt1 = mysqli_real_escape_string(DB::$WRITELINK, trim($question['opt1']));
         $opt2 = mysqli_real_escape_string(DB::$WRITELINK, trim($question['opt2']));
         $opt3 = mysqli_real_escape_string(DB::$WRITELINK, trim($question['opt3']));
         $opt4 = mysqli_real_escape_string(DB::$WRITELINK, trim($question['opt4']));
         $cor_ans = mysqli_real_escape_string(DB::$WRITELINK, trim($question['cor_ans']));
         $marks = mysqli_real_escape_string(DB::$WRITELINK, trim($question['marks'])) ?? 1;
         $ordering = $this->fetch_Last_Question_Ordering($exam_id)->ordering + 1;

         if (!empty($question['record_status'])) {
            $record_status = $question['record_status'];
         } else {
            $record_status = 'active';
         }

         $sql_insert_question = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions SET `exam_id` = '$exam_id',`ques` = '$ques',`opt1`='$opt1',`opt2`='$opt2',`opt3`='$opt3',`opt4`='$opt4',`cor_ans`='$cor_ans',`marks`='$marks',`ordering`='$ordering',`record_status`='$record_status',`updated_at` = now()";

         //echo $sql_insert_question;exit();

         //Call save exam questions
         $resultArr = $this->conn->global_CRUD_DB($sql_insert_question);
      }

      return $resultArr;
   }

   public function delete_All_Questions($exam_id)
   {
      $sql = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions WHERE `exam_id` = '$exam_id'";

      //echo $sql;exit;

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function save_Exam_Questions_Order($ordeData)
   {

      $exam_id = $ordeData['exam_id'];
      $question_id = $ordeData['question_id'];
      $ordering = $ordeData['ordering'];

      $sql_update_ordering = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions SET `ordering` = '$ordering',`updated_at`=now() WHERE `exam_id` = '$exam_id' AND `id` = '$question_id'";

      //echo $sql_update_ordering;exit;

      $resultArr = $this->conn->global_CRUD_DB($sql_update_ordering);

      return $resultArr;
   }

   public function update_Exam_Validation_Log($flagArr)
   {

      $student_id = $_SESSION['user_id'];
      $exam_id = $flagArr['exam_id'];

      //Check if this question is already flagged
      $query_log_caluse = "efq.exam_id = '$exam_id' AND efq.student_id = '$student_id'";

      $sql_count_log = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_log efq WHERE " . $query_log_caluse;

      //echo $sql_count_log;exit();

      $row_count = $this->conn->global_Rows_Count_DB($sql_count_log);

      if ($row_count > 0) {
         return array('check' => 'failure', 'message' => "Exam has already started");
      } else {

         $sql_insert_log = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_log SET `exam_id` = '$exam_id',`student_id` = '$student_id',`status`='started',`started_at` = now()";

         //echo $sql_insert_log;exit();

         //Call save exam questions
         $resultArr = $this->conn->global_CRUD_DB($sql_insert_log);

         if ($resultArr['check'] == "success") {
            $_SESSION['exam_started'] = "true";
            $_SESSION['exam_id'] = $exam_id;
         }

         return $resultArr;
      }
   }

   public function update_Exam_Answer($postData)
   {

      $student_id = $_SESSION['user_id'];
      $exam_id = $postData['exam_id'];
      $answers = $postData['answers'];

      $answerArr = array();
      $newAnswerArr = array();
      $removedAnswerArr = array();
      $flagArr = array();

      //Fetch flagged questions
      $addParamsArr['exam_id'] = $exam_id;
      $addParamsArr['student_id'] = $student_id;

      //Formatting answers array
      $answeredQuestionsArr = $this->fetch_User_Exam_Answers($addParamsArr);

      foreach ($answeredQuestionsArr as $aindex => $answer) {
         $answerArr[$aindex] = $answer->ques_id;
      }

      //Formatting flagged questions array
      $flagQuestions = $this->fetch_Flagged_Questions($addParamsArr);

      foreach ($flagQuestions as $findex => $flag) {
         $flagArr[$findex] = $flag->ques_id;
      }

      $sql_delete_answer = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_answers WHERE `exam_id` = '$exam_id' AND `student_id` = '$student_id'";

      //echo $sql_delete_answer;exit;

      $resultArr = $this->conn->global_CRUD_DB($sql_delete_answer);

      //print_r($answers);exit;

      if (!empty($answers)) {
         foreach ($answers as $index => $answer) {
            $ques_id = $answer['qid'];
            $selection = $answer['selection'];

            if (array_key_exists('selection', $answer)) {

               $newAnswerArr[$index] = $ques_id;

               $sql_insert_answer = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_answers SET `exam_id` = '$exam_id',`student_id` = '$student_id',`ques_id`='$ques_id',`answer`='$selection',`created_at` = now()";

               //echo $sql_insert_answer;exit();

               //Call save exam questions
               $resultArr = $this->conn->global_CRUD_DB($sql_insert_answer);
            }
         }

         $newAnswerArr = array_values($newAnswerArr);
      } else {
         $newAnswerArr = array();
      }

      //Finding removed answer array
      foreach ($answerArr as $rindex => $canswer) {
         if (!in_array($canswer, $newAnswerArr)) {
            $removedAnswerArr[$rindex] = $canswer;
         }
      }

      $removedAnswerArr = array_values($removedAnswerArr);

      if ($resultArr['check'] == "success") {
         return array(
            'check' => 'success',
            'answeredQuestions' => $newAnswerArr,
            'removedAnswerArr' => $removedAnswerArr,
            'flaggedQuestions' => $flagArr
         );
      } else {
         return $resultArr;
      }
   }

   public function update_Flag_Question_Exam($flagArr)
   {

      $student_id = $_SESSION['user_id'];
      $exam_id = $flagArr['exam_id'];
      $ques_id = $flagArr['ques_id'];

      //Check if this question is already flagged
      $query_flag_caluse = "efq.exam_id = '$exam_id' AND efq.ques_id = '$ques_id' AND efq.student_id = '$student_id'";

      $sql_count_flagged = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions efq WHERE " . $query_flag_caluse;

      //echo $sql_count_flagged;exit();

      $row_count = $this->conn->global_Rows_Count_DB($sql_count_flagged);

      if ($row_count > 0) {
         $flag_status = 'deleted';

         $sql_delete_answer = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions WHERE `exam_id` = '$exam_id' AND `ques_id` = '$ques_id' AND `student_id` = '$student_id'";

         //echo $sql_delete_answer;exit();

         $resultArr = $this->conn->global_CRUD_DB($sql_delete_answer);
      } else {
         $flag_status = 'added';

         $sql_insert_flag = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions SET `exam_id` = '$exam_id',`student_id` = '$student_id',`ques_id`='$ques_id',`updated_at` = now()";

         //echo $sql_insert_flag;exit();

         //Call save exam questions
         $resultArr = $this->conn->global_CRUD_DB($sql_insert_flag);
      }

      if ($resultArr['check'] == "success") {

         //Check if this question is attempted
         $sql_check_answered = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_answers ea WHERE ea.exam_id = '$exam_id' AND ea.ques_id = '$ques_id' AND ea.student_id = '$student_id'";

         //echo $sql_check_answered;exit;

         $answered_row_count = $this->conn->global_Rows_Count_DB($sql_check_answered);

         //var_dump($answered_row_count);exit;

         if ($flag_status == "added") {

            if ($answered_row_count > 0) {
               $flag_status = "attempted_review";
            } else {
               $flag_status = "added_reveiw";
            }
         } else {
            if ($answered_row_count > 0) {
               $flag_status = "attempted";
            } else {
               $flag_status = "deleted";
            }
         }
         return array('check' => 'success', 'flag_status' => $flag_status, 'qId' => $ques_id);
      } else {
         return $resultArr;
      }
   }

   public function update_Viewed_Question_Exam($flagArr)
   {

      $student_id = $_SESSION['user_id'];
      $exam_id = $flagArr['exam_id'];
      $ques_id = $flagArr['ques_id'];

      //Check if this question is already flagged
      $query_viewed_caluse = "efq.exam_id = '$exam_id' AND efq.ques_id = '$ques_id' AND efq.student_id = '$student_id'";

      $sql_count_viewed = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_viewed_questions efq WHERE " . $query_viewed_caluse;

      //echo $sql_count_viewed;exit();

      $row_count = $this->conn->global_Rows_Count_DB($sql_count_viewed);

      if ($row_count > 0) {
         return array('check' => 'failure', 'qId' => $ques_id);
      } else {

         $sql_insert_view = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_viewed_questions SET `exam_id` = '$exam_id',`student_id` = '$student_id',`ques_id`='$ques_id',`updated_at` = now()";

         //echo $sql_insert_view;exit();

         //Call save exam questions
         $resultArr = $this->conn->global_CRUD_DB($sql_insert_view);

         if ($resultArr['check'] == "success") {
            return array('check' => 'success', 'qId' => $ques_id);
         } else {
            return $resultArr;
         }
      }
   }

   public function manage_Global_Email_Template($templateDataArr)
   {

      $template_id = $templateDataArr['template_id'];
      $subject = $templateDataArr['subject'];
      $code = $templateDataArr['code'];
      $email_for = $templateDataArr['email_for'];

      //Constructing status variable
      if (isset($templateDataArr['record_status'])) {
         $record_status = $templateDataArr['record_status'];
         $record_status .= "`record_status` = '$record_status',";
      }

      $variables = $templateDataArr['variables'];
      $from_email = $templateDataArr['from_email'];
      $from_name = $templateDataArr['from_name'];
      $cc_email =  $templateDataArr['cc_email'];
      $template = $templateDataArr['template'];

      if ($template_id > 0) {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template SET `subject` = '$subject', `code` = '$code', `email_for` = '$email_for',`variables` = '$variables', `from_email` = '$from_email', `from_name` = '$from_name',`cc_email`='$cc_email', `template` = '$template',`updated_at`=now() WHERE `id`='$template_id'";
      } else {
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template SET `subject` = '$subject', `code` = '$code', `email_for` = '$email_for',`variables` = '$variables', `from_email` = '$from_email', `from_name` = '$from_name',`cc_email`='$cc_email',`template` = '$template', `created_at` = now()";
      }

      //echo $sql;exit;

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }


   public function manage_Home_Slider($sliderDataArr)
   {
      $slider_id = $sliderDataArr['slider_id'];

      $slider_type = $sliderDataArr['slider_type'];
      $banner_title = $sliderDataArr['banner_title'];

      //Constructing status variable
      $banner_text = $sliderDataArr['banner_text'];
      $banner_link = $sliderDataArr['banner_link'];

      $file_upload_type = $sliderDataArr['file_upload_type'];
      $banner_image = $sliderDataArr['banner_image'];

      $record_status = $sliderDataArr['record_status'];

      if ($slider_id > 0) {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "home_sliders SET `slider_type` = '$slider_type',`banner_title` = '$banner_title',`banner_text` = '$banner_text',`banner_link` = '$banner_link',`file_upload_type` = '$file_upload_type', `banner_image` = '$banner_image',`record_status` = '$record_status',`updated_at`=now() WHERE `id`='$slider_id'";
      } else {
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "home_sliders SET `slider_type` = '$slider_type',`banner_title` = '$banner_title',`banner_text` = '$banner_text',`banner_link` = '$banner_link',`file_upload_type` = '$file_upload_type', `banner_image` = '$banner_image',`record_status` = '$record_status',`created_at` = now()";
      }

      //echo $sql;exit;

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function manage_Global_News($newsDataArr)
   {

      $news_id = $newsDataArr['news_id'];
      $title = $newsDataArr['title'];

      //Constructing status variable
      $record_status = $newsDataArr['record_status'];
      $featured_status = $newsDataArr['featured_status'];

      $optional_pdf = $newsDataArr['optional_pdf'];

      $description = $newsDataArr['description'];

      if ($news_id > 0) {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "news SET `title` = '$title',`description` = '$description',`optional_pdf` = '$optional_pdf',`record_status` = '$record_status',`featured_status` = '$featured_status',`updated_at`=now() WHERE `id`='$news_id'";
      } else {
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "news SET `title` = '$title',`description` = '$description',`optional_pdf` = '$optional_pdf',`record_status` = '$record_status',`featured_status` = '$featured_status',`created_at` = now()";
      }

      //echo $sql;exit;

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function manage_Student_Receipt($receiptDataArr)
   {
      $receipt_row_id = $receiptDataArr['receipt_row_id'] ?? null;

      // Common fields
      $params = [];

      if (!empty($receipt_row_id)) {

         // =========================
         // UPDATE
         // =========================
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts 
                SET 
                    category_id = ?,
                    receipt_amount = ?,
                    late_fine = ?,
                    extra_fees = ?,
                    extra_fees_description = ?,
                    record_status = ?,
                    verified_status = ?,
                    edit_description = ?,
                    updated_at = NOW()
                WHERE id = ?";

         $params = [
            (int)$receiptDataArr['category_id'],
            (int)$receiptDataArr['receipt_amount'],
            (int)$receiptDataArr['late_fine'],
            (int)$receiptDataArr['extra_fees'],
            $receiptDataArr['extra_fees_description'] ?? null,
            $receiptDataArr['record_status'],
            $receiptDataArr['verified_status'],
            $receiptDataArr['edit_description'],
            (int)$receipt_row_id
         ];
      } else {

         // =========================
         // INSERT
         // =========================
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts 
                (
                    category_id,
                    stu_id,
                    receipt_id,
                    receipt_amount,
                    late_fine,
                    extra_fees,
                    extra_fees_description,
                    record_status,
                    og_receipt_amount,
                    og_late_fine,
                    og_extra_fees,
                    created_at
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";

         $params = [
            (int)$receiptDataArr['category_id'],
            $receiptDataArr['student_id'],
            $receiptDataArr['receipt_id'],
            (int)$receiptDataArr['receipt_amount'],
            (int)$receiptDataArr['late_fine'],
            (int)$receiptDataArr['extra_fees'],
            $receiptDataArr['extra_fees_description'] ?? null,
            $receiptDataArr['record_status'],
            (int)$receiptDataArr['original_receipt_amount'],
            (int)$receiptDataArr['original_late_fine'],
            (int)$receiptDataArr['original_extra_fees']
         ];
      }

      // =========================
      // DEBUG (OPTIONAL)
      // =========================
      // echo $this->debugQuery($sql, $params); exit;

      // =========================
      // EXECUTE
      // =========================
      $resultArr = $this->conn->global_CRUD_DB($sql, $params);

      return $resultArr;
   }

   public function create_Student_Admission_Receipt($receiptDataArr)
   {

      $receipt_id = $receiptDataArr['receipt_id'];

      $stu_id = $receiptDataArr['stu_id'];

      $category_id = $receiptDataArr['category_id'];

      $editType = $receiptDataArr['editType'];

      $receipt_amount = $receiptDataArr['receipt_amount'];
      $extra_fees = $receiptDataArr['extra_fees'];
      $extra_fees_description = $receiptDataArr['extra_fees_description'];

      $record_status = $receiptDataArr['record_status'];

      $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts SET `category_id` = '$category_id',`stu_id` = '$stu_id', `receipt_id` = '$receipt_id', `receipt_amount` = '$receipt_amount', `extra_fees` = '$extra_fees', `extra_fees_description` = '$extra_fees_description', `record_status` = '$record_status',`created_at`= now()";

      //echo $sql;exit;   

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Student_Admission_Receipt($receiptDataArr)
   {
      $receipt_row_id = $receiptDataArr['receipt_row_id'];
      $receipt_amount = $receiptDataArr['receipt_amount'];

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts SET `receipt_amount` = '$receipt_amount',`updated_at` = now() WHERE `id`='$receipt_row_id'";

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function manage_Student_Status($statusDataArr)
   {

      $student_id = $statusDataArr['student_id'];

      if ($statusDataArr['status_type'] == "status") {

         $student_status = $statusDataArr['student_status'];

         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students SET `student_status` = '$student_status',`updated_at` = now() WHERE `stu_id`='$student_id'";
      } else {
         $stu_result = $statusDataArr['stu_result'];

         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students SET `stu_result` = '$stu_result',`updated_at` = now() WHERE `stu_id`='$student_id'";
      }

      //echo $sql;exit;   

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function edit_Post_Category($updateDataArr)
   {

      $categoryArr = $updateDataArr['category_id'];
      $post_type = $updateDataArr['post_type'];
      $post_id = $updateDataArr['post_id'];

      $sql_delete_category = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category WHERE post_type = '$post_type' AND `post_id`='$post_id'";

      //echo $sql_delete_category;exit;

      $this->conn->global_CRUD_DB($sql_delete_category);

      //print_r($updateDataArr);exit;
      if (count($categoryArr) > 0) {
         foreach ($categoryArr as $index => $category_id) {

            $category_data = mysqli_real_escape_string(DB::$WRITELINK, trim($category['category_id']));

            $sql_insert_category = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category SET `post_type` = '$post_type',`post_id` = '$post_id',`category_id`='$category_id',`updated_at` = now()";

            //echo $sql_insert_meta;exit();

            $resultArr = $this->conn->global_CRUD_DB($sql_insert_category);
         }
      } else {
         $resultArr = array('check' => 'success');
      }

      return $resultArr;
   }

   public function manage_Parent_Category($updateDataArr)
   {

      $row_id = $updateDataArr['row_id'];
      $category = $updateDataArr['category'];
      $parent_category = $updateDataArr['parent_category'];
      $record_status = $updateDataArr['record_status'];

      if ($row_id > 0) {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category SET `name` = '$category', `parent_category` = '$parent_category', `record_status` = '$record_status',`updated_at` = now() WHERE `id`='$row_id'";
      } else {
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category SET `name` = '$category', `parent_category` = '$parent_category', `record_status` = '$record_status',`created_at` = now()";
      }

      //echo $sql;exit();

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function manage_Global_City($updateDataArr)
   {

      $row_id = $updateDataArr['row_id'];
      $name = $updateDataArr['name'];
      $record_status = $updateDataArr['record_status'];

      if ($row_id > 0) {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "cities SET `name` = '$name', `record_status` = '$record_status',`updated_at` = now() WHERE `id`='$row_id'";
      } else {
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "cities SET `name` = '$name',`record_status` = '$record_status',`created_at` = now()";
      }

      //echo $sql;exit();

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function import_Global_City($paramArr)
   {

      $name = $paramArr['name'];
      $record_status = $paramArr['record_status'];

      $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "cities SET `name` = '$name',`record_status` = '$record_status',`created_at` = now()";

      //echo $sql;exit();

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Single_Franchise($franchise_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise WHERE id = '$franchise_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Single_Franchise_By_Uid($fran_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise WHERE fran_id = '$fran_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Single_Student($student_id, $receipt_timestamp = null)
   {
      $receiptParams = [];
      $whereParams = [];
      $params = [];

      // Receipt
      $receipt_condition = "";
      if (!empty($receipt_timestamp)) {
         $receipt_condition = "WHERE created_at <= ?";
         $receiptParams[] = $receipt_timestamp;
      }

      // Student
      if (is_numeric($student_id)) {
         $where_clause = "stu.id = ?";
         $whereParams[] = $student_id;
      } else {
         $where_clause = "stu.stu_id = ?";
         $whereParams[] = $student_id;
      }

      // Final order must match SQL
      $params = array_merge($receiptParams, $whereParams);

      $sql = "SELECT 
                   stu.*,
                   COALESCE(rcpt.total_paid, 0) as course_fees_paid,
                   tmp_stu.advanced_fees,
                   tmp_stu.created_at as advance_fees_date,
                   crs.course_title,
                   crs.course_fees as course_default_fees,
                   fran.center_name,
                   fran.fran_email,
                   fran.fran_phone,
                   fran.fran_address
   
               FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu
   
               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu 
                   ON stu.tmp_stu_record_id = tmp_stu.tmp_id
   
               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
                   ON stu.course_id = crs.id
   
               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise fran 
                   ON stu.franchise_id = fran.id
   
               LEFT JOIN (
                   SELECT stu_id, SUM(receipt_amount) AS total_paid
                   FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts
                   $receipt_condition
                   GROUP BY stu_id
               ) rcpt ON stu.stu_id = rcpt.stu_id
   
               WHERE $where_clause
               LIMIT 1";

      $resultArr = $this->conn->global_Fetch_Single_DB($sql, $params);

      return $resultArr;
   }

   public function fetch_Tmp_Single_Student($tmp_id)
   {

      $sql = "SELECT tmp_stu.*,frn.center_name,frn.fran_email,frn.fran_phone,frn.fran_address,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON tmp_stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON tmp_stu.course_id = crs.id WHERE tmp_id = '$tmp_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Single_Profile_Student($stu_id)
   {

      $sql = "SELECT stu.*,rslt.stu_result as student_result,rslt.result_date,rslt.file_upload_type as result_upload_type,rslt.result_pdf,frn.center_name,bth.batch_name,bth.start_date,bth.end_date,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "results rslt ON stu.stu_id = rslt.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "batch bth ON stu.batch_id = bth.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id WHERE stu.stu_id = '$stu_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Detail_Single_Student($student_id)
   {

      $sql = "SELECT stu.*,frn.center_name,crs.course_title,rslt.stu_result,COUNT(DISTINCT rcpt.id) as receipt_count FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "results rslt ON stu.stu_id = rslt.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt ON stu.stu_id = rcpt.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id WHERE stu.stu_id = '$student_id' OR stu.id = '$student_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Detail_Single_Student_Receipt($student_id)
   {

      $sql = "SELECT stu.*,frn.center_name,crs.course_title,COUNT(DISTINCT rcpt.id) as receipt_count FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt ON stu.stu_id = rcpt.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id WHERE stu.stu_id = '$student_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Single_Receipt_Data($row_id)
   {
      $sql = "SELECT 
                  rcpt.id,
                  rcpt.stu_id,
                  rcpt.receipt_id,
                  rcpt.receipt_amount,
                  rcpt.late_fine,
                  rcpt.extra_fees,
                  rcpt.extra_fees_description,
                  rcpt.category_id,
                  rcpt.record_status as receipt_status,
                  rcpt.created_at,
                  pc.name as category

               FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt

               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc 
                  ON rcpt.category_id = pc.id

               WHERE rcpt.id = ?
               LIMIT 1";

      $resultArr = $this->conn->global_Fetch_Single_DB($sql, [$row_id]);

      return $resultArr;
   }

   public function fetch_Global_Single_Course($course_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "course WHERE id = '$course_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Single_Blog($blog_id)
   {

      $sql = "SELECT bg.*,GROUP_CONCAT(DISTINCT poc.category_id) as category_string FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "blog bg LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category poc ON bg.id = poc.post_id WHERE bg.id = '$blog_id'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function update_Global_Record_Status($type, $row_id, $record_status)
   {

      switch ($type) {

         case 'franchise':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'course':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'gallery':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'home_sliders':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'student':
            $type = "students";
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'temp_student':
            $type = "temp_students";
            $modify_Clause = "`tmp_id` = '$row_id'";
            break;

         case 'student_receipts':
            $modify_Clause = "`id` = '$row_id'";
            $type = "student_receipts";
            break;

         case 'exam':
            $type = "exams";
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'parent_category':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'cities':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'email_template':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'news':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'enquiry':
            $modify_Clause = "`id` = '$row_id'";
            break;
      }

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . $type . " SET `record_status`='$record_status',`updated_at` = now() WHERE " . $modify_Clause;
      //echo $sql;exit();
      $resultArr['responseArr'] = $this->conn->global_CRUD_DB($sql);

      if ($type == "students" && $resultArr['responseArr']['check'] == 'success') {
         //Fetch student details
         $studentDetails = $this->fetch_Global_Single_Student($row_id);
         //Calling update record status of current student receipt
         $this->block_Student_Dependent_Receipt_Data($studentDetails->stu_id, $record_status);

         //Calling update record status of current student temp data if this is a converted student
         if (!empty($studentDetails->tmp_stu_record_id)) {
            $this->block_Student_Dependent_Temp_Data($studentDetails->tmp_stu_record_id, $record_status);
         }
      }

      return $resultArr;
   }

   public function update_Bulk_Student_Status($paramArr)
   {

      $update_String = '';
      $row_id = $paramArr['row_id'];

      if (!empty($paramArr['record_status'])) {
         $record_status = $paramArr['record_status'];
         $update_String .= "`record_status` = '$record_status',";
      }

      if (!empty($paramArr['student_status'])) {
         $student_status = $paramArr['student_status'];
         $update_String .= "`student_status` = '$student_status',";
      }

      if (!empty($paramArr['result_status'])) {
         $result_status = $paramArr['result_status'];
         $update_String .= "`stu_result` = '$result_status',";
      }

      $update_String .= "`updated_at` = now()";

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students SET " . $update_String . " WHERE `id` = '$row_id'";
      //echo $sql;exit();
      $resultArr['responseArr'] = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Global_Featured_Status($type, $row_id, $featured_status)
   {

      switch ($type) {

         case 'franchise':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'course':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'gallery':
            $modify_Clause = "`id` = '$row_id'";
            break;

         case 'news':
            $modify_Clause = "`id` = '$row_id'";
            break;
      }

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . $type . " SET `featured_status`='$featured_status',`updated_at` = now() WHERE " . $modify_Clause;
      //echo $sql;exit();
      $resultArr['responseArr'] = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Tmp_Student_Verified_Status($tmp_id, $verified_status)
   {

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu SET `verified_status`='$verified_status',`updated_at` = now() WHERE tmp_stu.tmp_id = '$tmp_id'";
      //echo $sql;exit();
      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Tmp_Student_Conversion_Status($tmp_id, $conversion_status)
   {

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu SET `conversion_status`='$conversion_status',`updated_at` = now() WHERE tmp_stu.tmp_id = '$tmp_id'";
      //echo $sql;exit();
      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Receipt_Verified_Status($receipt_id, $verified_status)
   {

      if ($verified_status == '0') {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt SET `verified_status`='$verified_status',`updated_at` = now() WHERE rcpt.receipt_id = '$receipt_id'";
      } else {
         $edit_description = serialize(array());
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt SET `verified_status`='$verified_status',`edit_description`='$edit_description', `updated_at` = now() WHERE rcpt.receipt_id = '$receipt_id'";
      }

      //echo $sql;exit();
      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Student_Verified_Status($student_id, $verified_status)
   {

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu SET `verified_status`='$verified_status',`updated_at` = now() WHERE stu.stu_id = '$student_id'";
      //echo $sql;exit();
      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function block_Student_Dependent_Receipt_Data($stu_id, $record_status)
   {
      //Deleting student result  
      $sql_block_receipt = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt SET `record_status`='$record_status',`updated_at` = now() WHERE rcpt.stu_id = '$stu_id'";
      //echo $sql_block_receipt;exit;
      $this->conn->global_CRUD_DB($sql_block_receipt);
   }

   public function block_Student_Dependent_Temp_Data($tmp_id, $record_status)
   {
      //Deleting student result  
      $sql_block_temp_student = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu SET `record_status`='$record_status',`updated_at` = now() WHERE tmp_stu.tmp_id = '$tmp_id'";
      //echo $sql_block_temp_student;exit;
      $this->conn->global_CRUD_DB($sql_block_temp_student);
   }

   public function delete_Student_Dependent_Data($stu_id)
   {
      //Deleting student result  
      $sql_delete_receipt = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts WHERE stu_id = '$stu_id'";
      //echo $sql_delete_receipt;exit;
      $this->conn->global_CRUD_DB($sql_delete_receipt);
   }

   public function delete_Post_Category_Data($post_type, $post_id)
   {
      $sql_delete_meta = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category WHERE post_type = '$post_type' AND `post_id`='$post_id'";
      //echo $sql_delete_meta;exit;
      $this->conn->global_CRUD_DB($sql_delete_meta);
   }

   public function delete_Global_Data($deleteParam)
   {
      $type = $deleteParam['type'];
      $row_id = $deleteParam['row_id'];

      switch ($type) {

         case 'franchise':
            $delete_Clause = "`id` = '$row_id'";
            break;

         case 'course':
            $delete_Clause = "`id` = '$row_id'";
            break;

         case 'gallery':
            $delete_Clause = "`id` = '$row_id'";
            break;

         case 'home_sliders':
            $delete_Clause = "`id` = '$row_id'";
            break;

         case 'student':
            $type = "students";
            $delete_Clause = "`id` = '$row_id'";
            break;

         case 'temp_student':
            $type = "temp_students";
            $delete_Clause = "`tmp_id` = '$row_id'";
            break;

         case 'student_receipts':
            $delete_Clause = "`id` = '$row_id'";
            $type = "student_receipts";
            break;

         case 'parent_category':
            $delete_Clause = "`id` = '$row_id'";
            break;

         case 'cities':
            $delete_Clause = "`id` = '$row_id'";
            break;

         case 'email_template':
            $delete_Clause = "`id` = '$row_id'";
            break;

         case 'feedback':
            $delete_Clause = "`id` = '$row_id'";
            break;

         case 'news':
            $delete_Clause = "`id` = '$row_id'";
            break;

         case 'enquiry':
            $delete_Clause = "`id` = '$row_id'";
            break;
      }

      if ($type == "students") {
         $studentDetail = $this->fetch_Global_Single_Student($row_id);
         $stu_id = $studentDetail->stu_id;
      }

      $sql_general_delete = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $type . " WHERE " . $delete_Clause;
      //echo $sql_general_delete;exit;
      $resultArr['responseArr'] = $this->conn->global_CRUD_DB($sql_general_delete);

      if ($type == "students" && $resultArr['responseArr']['check'] == 'success') {
         $this->delete_Student_Dependent_Data($stu_id);
      }

      if ($type == "gallery" && $resultArr['responseArr']['check'] == 'success') {
         $this->delete_Post_Category_Data($type, $row_id);
      }

      return $resultArr;
   }

   public function manage_Global_Media($itemDataArr)
   {

      $media_id = $itemDataArr['media_id'];

      $title = $itemDataArr['title'];
      $seo_url_structure = $itemDataArr['seo_url_structure'];

      $content_type = $itemDataArr['content_type'];
      $file_upload_type = $itemDataArr['file_upload_type'];
      $content = $itemDataArr['content'];
      $record_status = $itemDataArr['record_status'];
      $featured_status = $itemDataArr['featured_status'];

      if ($media_id > 0) {
         //Inserting institute general meta info
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery SET `title` = '$title',`file_upload_type`= '$file_upload_type',`content_type`= '$content_type',`content`= '$content', `record_status` = '$record_status', `featured_status` = '$featured_status', `updated_at` = now() WHERE `id`='$media_id'";
      } else {
         //Inserting institute general meta info
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery SET `title` = '$title',`file_upload_type`= '$file_upload_type',`content_type`= '$content_type',`content`= '$content', `record_status` = '$record_status', `featured_status` = '$featured_status', `created_at` = now()";
      }

      //echo $sql;exit;

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function fetch_Gallery_Arr($record_status = 'active')
   {

      $sql = "SELECT g.*, GROUP_CONCAT(DISTINCT pc.name) as category_string FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery g LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category poc ON ( g.id=poc.post_id AND poc.post_type='gallery' ) LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc ON poc.category_id = pc.id WHERE g.record_status = '$record_status' GROUP BY g.id ORDER BY g.id DESC";

      //echo $sql;exit;

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Gallery_Item_Detail($media_id)
   {

      //Inserting institute general meta info
      $sql = "SELECT g.*,GROUP_CONCAT(DISTINCT poc.category_id) as category_string FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery g LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category poc ON ( g.id=poc.post_id AND poc.post_type='gallery' ) WHERE g.id= '$media_id'";

      //echo $sql;exit;

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Slider_Arr($paramArr)
   {

      $record_status = $paramArr['record_status'];
      $where_Clause = "WHERE s.record_status = '$record_status'";

      if (!empty($paramArr['slider_type'])) {
         $slider_type = $paramArr['slider_type'];
         $where_Clause .= " AND s.slider_type = '$slider_type'";
      }

      $sql = "SELECT s.* FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "home_sliders s " . $where_Clause . " ORDER BY s.id";

      //echo $sql;exit;

      $resultArr = $this->conn->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Slider_Detail($slider_id)
   {

      //Inserting institute general meta info
      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "home_sliders s WHERE s.id= '$slider_id'";

      //echo $sql;exit;

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Single_Account($username)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE user_login = '$username'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

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

      $resultArr = $this->conn->global_CRUD_DB($sql_update_profile);

      if ($resultArr["check"] == "success") {
         $_SESSION['username'] = $user_nicename;
         $_SESSION['user_role'] = unserialize($user_role);
         return $resultArr;
      } else {
         return $resultArr;
      }
   }

   public function fetch_Email_Template_Detail($email_code)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template et WHERE et.code='$email_code'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Site_Setting_Detail()
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "site_setting WHERE `update_id` = 'UPDATE_THE_AIMGCSM_SITE_SETTINGS'";

      //echo $sql;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql);

      return $resultArr;
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

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Global_Site_Setting($updateDataArr)
   {

      $title = $updateDataArr['title'];
      $contact_email = $updateDataArr['contact_email'];
      $phone = $updateDataArr['phone'];
      $career_email = $updateDataArr['career_email'];
      $business_email = $updateDataArr['business_email'];
      $facebook_link = $updateDataArr['facebook_link'];
      $youtube_link = $updateDataArr['youtube_link'];
      $twitter_link = $updateDataArr['twitter_link'];
      $skype_link = $updateDataArr['skype_link'];
      $instagram_link =  $updateDataArr['instagram_link'];
      $linkdin_link = $updateDataArr['linkdin_link'];
      $copyright = $updateDataArr['copyright'];
      $address = $updateDataArr['address'];
      $description = $updateDataArr['description'];

      $signature = $updateDataArr['signature'];
      $logo = $updateDataArr['logo'];
      $header_logo = $updateDataArr['header_logo'];
      $sticky_logo = $updateDataArr['sticky_logo'];
      $footer_logo = $updateDataArr['footer_logo'];
      $favicon = $updateDataArr['favicon'];

      $feedback_status = $updateDataArr['feedback_status'];
      $maintenance_status = $updateDataArr['maintenance_status'];
      $site_caching = $updateDataArr['site_caching'];

      $sql_update_setting = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "site_setting SET `title` = '$title', `contact_email` = '$contact_email',`phone`='$phone',`career_email` = '$career_email', `business_email`= '$business_email',  `facebook_link` = '$facebook_link', `youtube_link` = '$youtube_link', `twitter_link` = '$twitter_link',`skype_link`='$skype_link', `instagram_link` = '$instagram_link', `linkdin_link` = '$linkdin_link', `copyright` = '$copyright', `address` = '$address', `description` = '$description',`signature` = '$signature',`logo` = '$logo', `header_logo` = '$header_logo', `sticky_logo` = '$sticky_logo', `footer_logo` = '$footer_logo', `favicon` = '$favicon', `feedback_status` = '$feedback_status',`maintenance_status` = '$maintenance_status',`site_caching` = '$site_caching',`updated_at`=now() WHERE `update_id`='UPDATE_THE_AIMGCSM_SITE_SETTINGS'";

      //echo $sql_update_setting;exit();

      $resultArr = $this->conn->global_CRUD_DB($sql_update_setting);

      if ($resultArr['check'] == "success") {
         $_SESSION['user_profile_pic'] = USER_UPLOAD_URL . 'others/' . $logo;
         return $resultArr;
      } else {
         return $resultArr;
      }
   }

   public function manage_Student_Profile($stuDataArr)
   {

      $stu_row_id = $stuDataArr['stu_row_id'];

      $stu_pass = $stuDataArr['stu_pass'];
      $stu_og_pass = $stuDataArr['stu_og_pass'];

      $stu_name = $stuDataArr['stu_name'];
      $stu_father_name = $stuDataArr['stu_father_name'];
      $stu_phone = $stuDataArr['stu_phone'];
      $stu_email = $stuDataArr['stu_email'];
      $stu_gender = $stuDataArr['stu_gender'];
      $stu_marital_status = $stuDataArr['stu_marital_status'];
      $stu_address = $stuDataArr['stu_address'];

      $stu_qualification = $stuDataArr['stu_qualification'];

      $stu_dob = $stuDataArr['stu_dob'];
      $stu_address = $stuDataArr['stu_address'];
      $stu_description = $stuDataArr['stu_description'];

      $file_upload_type = $stuDataArr['file_upload_type'];
      $image_file_name = $stuDataArr['image_file_name'];

      if ($stu_row_id > 0) {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students SET `stu_pass` = '$stu_pass',`stu_og_pass` = '$stu_og_pass',`stu_name` = '$stu_name', `stu_father_name` = '$stu_father_name',`stu_phone` = '$stu_phone', `stu_email`= '$stu_email', `stu_gender` = '$stu_gender', `stu_marital_status` = '$stu_marital_status', `stu_address` = '$stu_address', `stu_qualification` = '$stu_qualification',`stu_dob` = '$stu_dob',`stu_address` = '$stu_address', `stu_description` = '$stu_description',`file_upload_type` = '$file_upload_type', `image_file_name` = '$image_file_name', `updated_at` = now() WHERE `id`='$stu_row_id'";
      }
      //echo $sql;exit();

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function check_Task_Status($status = null)
   {

      if ($status) {
         $sql_count_jobs = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "queue_jobs WHERE `status` = '$status'";
      } else {
         $sql_count_jobs = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "queue_jobs WHERE `status` IN ('pending', 'failed')";
      }

      //echo $sql_count_flagged;exit();

      $resultArr = $this->conn->global_Fetch_Single_DB($sql_count_jobs);

      return $resultArr;
   }

   public function manage_Queue_Jobs($cronDetailArr)
   {

      $action = $cronDetailArr['action'];
      $job_type = $cronDetailArr['job_type'];

      if ($action == "update") {

         $status = $cronDetailArr['status'];
         $response = $cronDetailArr['response'];

         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "queue_jobs SET `response` = '$response', `status` = '$status' WHERE `status` IN ('pending','running','failed')";
      } else {
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "queue_jobs SET `job_type` = '$job_type', `created_at` = now()";
      }

      //echo $sql;exit();

      $resultArr = $this->conn->global_CRUD_DB($sql);

      return $resultArr;
   }
}
