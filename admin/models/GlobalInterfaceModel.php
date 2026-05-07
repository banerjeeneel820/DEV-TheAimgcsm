<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class GlobalInterfaceModel extends BaseModel
{

   public function __construct($db)
   {
      parent::__construct($db);
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
      $resultArr = $this->global_Fetch_Single_DB($sql);
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
         $countStudentRow = $this->global_Rows_Count_DB($sql_fetch_current_students);

         if ($countStudentRow > 0) {
            $sql_fetch_user_detail = $sql_fetch_current_students;
         } else {
            $sql_fetch_user_detail = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students_archive WHERE `id`='$user_id'";
         }
      }

      //echo $sql_fetch_user_detail;exit();
      $resultArr = $this->global_Fetch_Single_DB($sql_fetch_user_detail);

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

   public function fetch_Global_Franchise($params = [])
   {
      $queryParams = [];
      $where = [];

      // -----------------------------
      // DEFAULT FILTER
      // -----------------------------
      $recordStatus = $params['record_status'] ?? 'active';

      $where[] = "fran.record_status = ?";
      $queryParams[] = $recordStatus;

      // -----------------------------
      // OPTIONAL FILTERS (FUTURE READY)
      // -----------------------------
      if (!empty($params['search_string'])) {
         $where[] = "(fran.center_name LIKE ? OR fran.center_code LIKE ?)";
         $queryParams[] = '%' . $params['search_string'] . '%';
         $queryParams[] = '%' . $params['search_string'] . '%';
      }

      // -----------------------------
      // WHERE CLAUSE
      // -----------------------------
      $whereSql = !empty($where)
         ? "WHERE " . implode(" AND ", $where)
         : "";

      // -----------------------------
      // SUBQUERY (AGGREGATION)
      // -----------------------------
      $studentCountSubquery = "
           SELECT 
               franchise_id,
               COUNT(*) AS enrolled_student_count
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students
           GROUP BY franchise_id
       ";

      // -----------------------------
      // MAIN QUERY
      // -----------------------------
      $sql = "
           SELECT 
               fran.*,
               COALESCE(stu_count.enrolled_student_count, 0) AS enrolled_student_count
   
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise fran
   
           LEFT JOIN ($studentCountSubquery) AS stu_count
               ON fran.id = stu_count.franchise_id
   
           $whereSql
   
           ORDER BY enrolled_student_count DESC
       ";

      // Debug
      // $this->debugQuery($sql, $queryParams); 

      return $this->global_Fetch_All_DB($sql, $queryParams);
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

      // Debug
      //$this->debugQuery($sql, $params);

      return $this->global_Fetch_All_DB($sql, $params);
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
         $params[] = $dataArr['verified_status'];
      }

      // Student filter
      if (!empty($dataArr['student_id'])) {
         $where[] = "rcpt.stu_id = ?";
         $params[] = $dataArr['student_id'];
      }

      // Course filter
      if (!empty($dataArr['course_id']) && $dataArr['course_id'] > 0) {
         $where[] = "stu.course_id = ?";
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
               stu.franchise_id,
               stu.course_id,
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
      // $this->debugQuery($dataSql, $dataParams);

      $data = $this->global_Fetch_All_DB($dataSql, $dataParams);
      $total = $this->global_Aggregate_Value_DB($countSql, $params);

      return [
         'data' => $data,
         'row_count' => $total,
         'pageNo' => $pageNo,
         'limit' => $limit
      ];
   }

   public function fetch_Single_Student_Receipt($student_id, $dataArr = [])
   {
      $where = [];
      $params = [];

      // =========================
      // BASE CONDITION
      // =========================
      $record_status = !empty($dataArr['record_status']) ? $dataArr['record_status'] : 'active';

      $where[] = "rcpt.record_status = ?";
      $params[] = $record_status;

      // =========================
      // STUDENT FILTER (PRIMARY)
      // =========================
      $where[] = "rcpt.stu_id = ?";
      $params[] = !empty($dataArr['student_id']) ? $dataArr['student_id'] : $student_id;

      // =========================
      // OPTIONAL FILTERS
      // =========================
      if (!empty($dataArr['course_id']) && $dataArr['course_id'] > 0) {
         $where[] = "stu.course_id = ?";
         $params[] = (int)$dataArr['course_id'];
      }

      if (!empty($dataArr['franchise_id']) && $dataArr['franchise_id'] > 0) {
         $where[] = "stu.franchise_id = ?";
         $params[] = (int)$dataArr['franchise_id'];
      }

      // =========================
      // DATE FILTER (INDEX SAFE)
      // =========================
      if (!empty($dataArr['created'])) {
         $startDate = date('Y-m-d', strtotime($dataArr['created']));
         $endDate   = date('Y-m-d', strtotime($dataArr['created'] . ' +1 day'));

         $where[] = "rcpt.created_at >= ? AND rcpt.created_at < ?";
         $params[] = $startDate;
         $params[] = $endDate;
      }

      // =========================
      // SEASON FILTER (FIXED BUG)
      // =========================
      if (!empty($dataArr['receipt_season_start']) && empty($dataArr['receipt_season_end'])) {

         $where[] = "rcpt.created_at >= ?";
         $params[] = $dataArr['receipt_season_start'];
      } elseif (empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {

         $endDate = date('Y-m-d', strtotime($dataArr['receipt_season_end'] . ' +1 day'));

         $where[] = "rcpt.created_at < ?";
         $params[] = $endDate;
      } elseif (!empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {

         $startDate = $dataArr['receipt_season_start'];
         $endDate   = date('Y-m-d', strtotime($dataArr['receipt_season_end'] . ' +1 day'));

         $where[] = "rcpt.created_at >= ? AND rcpt.created_at < ?";
         $params[] = $startDate;
         $params[] = $endDate;
      }

      // =========================
      // FINAL WHERE
      // =========================
      $where_sql = "WHERE " . implode(" AND ", $where);

      // =========================
      // QUERY (RECEIPTS-FIRST)
      // =========================
      $sql = "SELECT 
                  rcpt.id,
                  rcpt.receipt_id,
                  rcpt.category_id,
                  rcpt.receipt_amount,
                  rcpt.late_fine,
                  rcpt.extra_fees,
                  rcpt.extra_fees_description,
                  rcpt.verified_status,
                  rcpt.created_at,
                  rcpt.record_status AS receipt_status,

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
                  stu.stu_dob,
                  stu.stu_result,
                  stu.record_status,
                  stu.created_at AS student_created_at,

                  frn.center_name,
                  crs.course_title,
                  pc.name AS category

               FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt

               INNER JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu 
                  ON rcpt.stu_id = stu.stu_id

               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
                  ON stu.franchise_id = frn.id

               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
                  ON stu.course_id = crs.id

               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc 
                  ON rcpt.category_id = pc.id

               $where_sql

               ORDER BY rcpt.created_at DESC";

      // =========================
      // DEBUG (OPTIONAL)
      // =========================
      //$this->debugQuery($sql, $params); exit;

      $resultArr = $this->global_Fetch_All_DB($sql, $params);

      return $resultArr;
   }

   public function fetch_Global_Receipts($dataArr)
   {
      $where = [];
      $params = [];

      // =========================
      // BASE CONDITION
      // =========================
      $where[] = "rcpt.record_status = ?";
      $params[] = $dataArr['record_status'];

      // =========================
      // OPTIONAL FILTERS
      // =========================
      if (!empty($dataArr['course_id']) && $dataArr['course_id'] > 0) {
         $where[] = "stu.course_id = ?";
         $params[] = (int)$dataArr['course_id'];
      }

      if (!empty($dataArr['franchise_id']) && $dataArr['franchise_id'] > 0) {
         $where[] = "stu.franchise_id = ?";
         $params[] = (int)$dataArr['franchise_id'];
      }

      // =========================
      // DATE FILTERS (FIXED)
      // =========================

      // Single date
      if (!empty($dataArr['created'])) {
         $startDate = date('Y-m-d', strtotime($dataArr['created']));
         $endDate   = date('Y-m-d', strtotime($dataArr['created'] . ' +1 day'));

         $where[] = "rcpt.created_at >= ? AND rcpt.created_at < ?";
         $params[] = $startDate;
         $params[] = $endDate;
      }

      // Season filters
      if (!empty($dataArr['receipt_season_start']) && empty($dataArr['receipt_season_end'])) {

         $where[] = "rcpt.created_at >= ?";
         $params[] = $dataArr['receipt_season_start'];
      } elseif (empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {

         $endDate = date('Y-m-d', strtotime($dataArr['receipt_season_end'] . ' +1 day'));

         $where[] = "rcpt.created_at < ?";
         $params[] = $endDate;
      } elseif (!empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {

         $startDate = $dataArr['receipt_season_start'];
         $endDate   = date('Y-m-d', strtotime($dataArr['receipt_season_end'] . ' +1 day'));

         $where[] = "rcpt.created_at >= ? AND rcpt.created_at < ?";
         $params[] = $startDate;
         $params[] = $endDate;
      }

      // =========================
      // FINAL WHERE CLAUSE
      // =========================
      $where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

      // =========================
      // QUERY
      // =========================
      $sql = "SELECT 
                  rcpt.id,
                  rcpt.receipt_id,
                  rcpt.category_id,
                  rcpt.receipt_amount,
                  rcpt.late_fine,
                  rcpt.extra_fees,
                  rcpt.extra_fees_description,
                  rcpt.verified_status,
                  rcpt.created_at,
                  rcpt.record_status AS receipt_status,

                  stu.id AS student_record_id,
                  stu.stu_id,
                  stu.stu_name,
                  stu.stu_phone,
                  stu.stu_email,
                  stu.stu_course_fees,
                  stu.stu_course_discount,
                  stu.fees_paid_before_dr,
                  stu.image_file_name,
                  stu.stu_qualification,
                  stu.student_status,
                  stu.stu_result,
                  stu.record_status,
                  stu.created_at AS student_created_at,

                  frn.center_name,
                  crs.course_title,
                  pc.name AS category

               FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt

               INNER JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu 
                  ON rcpt.stu_id = stu.stu_id

               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
                  ON stu.franchise_id = frn.id

               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
                  ON stu.course_id = crs.id

               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc 
                  ON rcpt.category_id = pc.id

               $where_sql

               ORDER BY rcpt.id DESC";

      // =========================
      // DEBUG (OPTIONAL)
      // =========================
      // $this->debugQuery($sql, $params); exit;

      $resultArr = $this->global_Fetch_All_DB($sql, $params);

      return $resultArr;
   }

   public function fetch_Receipt_Collection($dataArr)
   {
      $where = [];
      $params = [];

      // =========================
      // BASE CONDITION
      // =========================
      $where[] = "rcpt.record_status = ?";
      $params[] = $dataArr['record_status'];

      // =========================
      // OPTIONAL FILTERS
      // =========================
      if (!empty($dataArr['course_id']) && $dataArr['course_id'] > 0) {
         $where[] = "stu.course_id = ?";
         $params[] = (int)$dataArr['course_id'];
      }

      if (!empty($dataArr['franchise_id']) && $dataArr['franchise_id'] > 0) {
         $where[] = "stu.franchise_id = ?";
         $params[] = (int)$dataArr['franchise_id'];
      }

      if (!empty($dataArr['stu_id'])) {
         $where[] = "rcpt.stu_id = ?";
         $params[] = $dataArr['stu_id'];
      }

      // =========================
      // DATE FILTERS (FIXED + INDEX FRIENDLY)
      // =========================

      // Single date filter
      if (!empty($dataArr['created'])) {
         $startDate = date('Y-m-d', strtotime($dataArr['created']));
         $endDate   = date('Y-m-d', strtotime($dataArr['created'] . ' +1 day'));

         $where[] = "rcpt.created_at >= ? AND rcpt.created_at < ?";
         $params[] = $startDate;
         $params[] = $endDate;
      }

      // Season filters (priority over single date if both passed)
      if (!empty($dataArr['receipt_season_start']) && empty($dataArr['receipt_season_end'])) {

         $where[] = "rcpt.created_at >= ?";
         $params[] = $dataArr['receipt_season_start'];
      } elseif (empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {

         // include full end day
         $endDate = date('Y-m-d', strtotime($dataArr['receipt_season_end'] . ' +1 day'));

         $where[] = "rcpt.created_at < ?";
         $params[] = $endDate;
      } elseif (!empty($dataArr['receipt_season_start']) && !empty($dataArr['receipt_season_end'])) {

         $startDate = $dataArr['receipt_season_start'];
         $endDate   = date('Y-m-d', strtotime($dataArr['receipt_season_end'] . ' +1 day'));

         $where[] = "rcpt.created_at >= ? AND rcpt.created_at < ?";
         $params[] = $startDate;
         $params[] = $endDate;
      }

      // =========================
      // FINAL WHERE CLAUSE
      // =========================
      $where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

      // =========================
      // QUERY
      // =========================
      $sql = "SELECT 
                   COALESCE(SUM(rcpt.receipt_amount), 0) AS receipt_amount,
                   COALESCE(SUM(rcpt.late_fine), 0) AS late_fine,
                   COALESCE(SUM(rcpt.extra_fees), 0) AS extra_fees
               FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt
               INNER JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu 
                   ON rcpt.stu_id = stu.stu_id
               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
                   ON stu.franchise_id = frn.id
               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
                   ON stu.course_id = crs.id
               $where_sql";

      // =========================
      // DEBUG (OPTIONAL)
      // =========================
      // $this->debugQuery($sql, $params);

      $resultArr = $this->global_Fetch_Single_DB($sql, $params);

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
               ON stu.tmp_stu_record_id = tmp_stu.id

         LEFT JOIN ($receiptSubQuery) rcpt 
               ON stu.stu_id = rcpt.stu_id

         LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
               ON stu.franchise_id = frn.id

         LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
               ON stu.course_id = crs.id

         $whereSql

         ORDER BY stu.created_at DESC
      ";

      // Debug if needed
      // $this->debugQuery($sql, $params);

      return $this->global_Fetch_Single_DB($sql, $params);
   }

   public function fetch_Global_Student($dataArr)
   {
      $where = [];
      $params = [];

      // =========================
      // BASE CONDITION
      // =========================
      $where[] = "stu.record_status = ?";
      $params[] = $dataArr['record_status'];

      // =========================
      // VERIFIED STATUS
      // =========================
      if (!empty($dataArr['verified_status'])) {
         $where[] = "stu.verified_status = ?";
         $params[] = $dataArr['verified_status'];
      }

      // =========================
      // STUDENT STATUS
      // =========================
      if (!empty($dataArr['student_status'])) {
         $where[] = "stu.student_status = ?";
         $params[] = $dataArr['student_status'];
      }

      // =========================
      // COURSE FILTER
      // =========================
      if (!empty($dataArr['course_id']) && $dataArr['course_id'] > 0) {
         $where[] = "stu.course_id = ?";
         $params[] = (int)$dataArr['course_id'];
      }

      // =========================
      // FRANCHISE FILTER
      // =========================
      if (!empty($dataArr['franchise_id']) && $dataArr['franchise_id'] > 0) {
         $where[] = "stu.franchise_id = ?";
         $params[] = (int)$dataArr['franchise_id'];
      }

      // =========================
      // RESULT STATUS
      // =========================
      if (!empty($dataArr['result_status'])) {
         $where[] = "stu.stu_result = ?";
         $params[] = $dataArr['result_status'];
      }

      // =========================
      // SINGLE DATE FILTER (INDEX FRIENDLY)
      // =========================
      if (!empty($dataArr['created'])) {
         $date = date('Y-m-d', strtotime($dataArr['created']));
         $where[] = "stu.created_at >= ? AND stu.created_at < ?";
         $params[] = $date . " 00:00:00";
         $params[] = $date . " 23:59:59";
      }

      // =========================
      // DATE RANGE FILTER
      // =========================
      if (!empty($dataArr['search_start'])) {
         $where[] = "stu.created_at >= ?";
         $params[] = $dataArr['search_start'] . " 00:00:00";
      }

      if (!empty($dataArr['search_end'])) {
         $where[] = "stu.created_at <= ?";
         $params[] = $dataArr['search_end'] . " 23:59:59";
      }

      // =========================
      // SEARCH STRING (SAFE LIKE)
      // =========================
      if (!empty($dataArr['search_string'])) {
         $search = "%" . $dataArr['search_string'] . "%";

         $where[] = "(
               stu.stu_id LIKE ? OR
               stu.stu_name LIKE ? OR
               stu.stu_father_name LIKE ? OR
               stu.stu_address LIKE ? OR
               stu.stu_phone LIKE ? OR
               stu.stu_email LIKE ? OR
               stu.stu_gender LIKE ? OR
               stu.stu_qualification LIKE ? OR
               stu.stu_marital_status LIKE ?
           )";

         // push same param multiple times
         for ($i = 0; $i < 9; $i++) {
            $params[] = $search;
         }
      }

      // =========================
      // FINAL WHERE
      // =========================
      $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

      // =========================
      // PAGINATION
      // =========================
      $limit = (int) $dataArr['limit'];
      $pageNo = (int) $dataArr['pageNo'];
      $offset = ($pageNo - 1) * $limit;

      // =========================
      // BASE QUERY
      // =========================
      $baseSql = "
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
               ON stu.franchise_id = frn.id
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
               ON stu.course_id = crs.id
   
           $whereSql
       ";

      // =========================
      // DATA QUERY
      // =========================
      $dataSql = "
           SELECT 
               stu.id,
               stu.stu_id,
               stu.stu_name,
               stu.stu_phone,
               stu.stu_dob,
               stu.record_status,
               stu.verified_status,
               stu.image_file_name,
               stu.student_status,
               stu.stu_result,
               stu.created_at,
   
               frn.center_name,
               crs.course_title
   
           $baseSql
   
           ORDER BY stu.id DESC
           LIMIT ?, ?
       ";

      // =========================
      // COUNT QUERY
      // =========================
      $countSql = "SELECT COUNT(*) as total $baseSql";

      // =========================
      // EXECUTION
      // =========================
      $dataParams = array_merge($params, [$offset, $limit]);

      // Debug if needed
      // $this->debugQuery($dataSql, $dataParams);

      $data = $this->global_Fetch_All_DB($dataSql, $dataParams);
      $total = $this->global_Aggregate_Value_DB($countSql, $params);

      return [
         'data' => $data,
         'row_count' => $total,
         'pageNo' => $pageNo,
         'limit' => $limit
      ];
   }

   public function fetch_Due_Students_Data($dataArr)
   {
      // =========================
      // PAGINATION
      // =========================
      $limit  = (int)$dataArr['limit'];
      $pageNo = (int)$dataArr['pageNo'];
      $offset = ($pageNo - 1) * $limit;

      // =========================
      // WHERE BUILDER
      // =========================
      $where = [];
      $params = [];

      // BASE CONDITIONS
      $where[] = "stu.student_status IN ('admitted', 'continue')";
      $where[] = "stu.stu_result = 'unqualified'";
      $where[] = "COALESCE(stu.stu_course_fees, 0) > 0";
      $where[] = "COALESCE(stu.monthly_course_fees, 0) > 0";
      $where[] = "frn.owned_status = 'yes'";

      $where[] = "stu.record_status = ?";
      $params[] = $dataArr['record_status'];

      // =========================
      // OPTIONAL FILTERS
      // =========================
      if (!empty($dataArr['student_id'])) {
         $where[] = "stu.stu_id = ?";
         $params[] = $dataArr['student_id'];
      }

      if (!empty($dataArr['course_id']) && $dataArr['course_id'] > 0) {
         $where[] = "stu.course_id = ?";
         $params[] = (int)$dataArr['course_id'];
      }

      if (!empty($dataArr['franchise_id']) && $dataArr['franchise_id'] > 0) {
         $where[] = "stu.franchise_id = ?";
         $params[] = (int)$dataArr['franchise_id'];
      }

      $where_sql = "WHERE " . implode(" AND ", $where);

      // =========================
      // BASE DERIVED TABLE
      // =========================
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

               $where_sql

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

         WHERE rcpt.total_paid < base.expected_amount
      ";

      // =========================
      // MAIN QUERY
      // =========================
      $sql_fetch_student = "
         SELECT 
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

         $base_query

         ORDER BY base.id DESC
      ";

      // =========================
      // PAGINATION (SAFE)
      // =========================
      if (empty($dataArr['student_id'])) {
         $sql_fetch_student .= " LIMIT ? OFFSET ?";
         $params[] = $limit;
         $params[] = $offset;
      }

      // =========================
      // COUNT QUERY
      // =========================
      $sql_count = "SELECT COUNT(*) as total $base_query";

      // =========================
      // DEBUG (OPTIONAL)
      // =========================
      // $this->debugQuery($sql_fetch_student, $params); exit;

      // =========================
      // EXECUTION
      // =========================
      $resultArr = [];
      $resultArr['data'] = $this->global_Fetch_All_DB($sql_fetch_student, $params);

      // IMPORTANT: count should NOT include LIMIT params
      $count_params = $params;
      if (empty($dataArr['student_id'])) {
         array_pop($count_params); // offset
         array_pop($count_params); // limit
      }

      $resultArr['row_count'] = $this->global_Aggregate_Value_DB($sql_count, $count_params);
      $resultArr['pageNo'] = $pageNo;
      $resultArr['limit'] = $limit;

      return $resultArr;
   }

   public function fetch_Updated_Markup_Students_Data($dataArr)
   {
      // =========================
      // PAGINATION
      // =========================
      $limit  = (int)$dataArr['limit'];
      $pageNo = (int)$dataArr['pageNo'];
      $offset = ($pageNo - 1) * $limit;

      // =========================
      // WHERE BUILDER
      // =========================
      $where = [];
      $params = [];

      // BASE CONDITIONS
      $where[] = "stu.student_status IN ('admitted', 'continue')";
      $where[] = "stu.stu_result = 'unqualified'";
      $where[] = "COALESCE(stu.stu_course_fees, 0) > 0";
      $where[] = "COALESCE(stu.monthly_course_fees, 0) > 0";
      $where[] = "frn.owned_status = 'yes'";

      $where[] = "stu.record_status = ?";
      $params[] = $dataArr['record_status'];

      // =========================
      // OPTIONAL FILTERS
      // =========================
      if (!empty($dataArr['student_id'])) {
         $where[] = "stu.stu_id = ?";
         $params[] = $dataArr['student_id'];
      }

      if (!empty($dataArr['course_id']) && $dataArr['course_id'] > 0) {
         $where[] = "stu.course_id = ?";
         $params[] = (int)$dataArr['course_id'];
      }

      if (!empty($dataArr['franchise_id']) && $dataArr['franchise_id'] > 0) {
         $where[] = "stu.franchise_id = ?";
         $params[] = (int)$dataArr['franchise_id'];
      }

      $where_sql = "WHERE " . implode(" AND ", $where);

      // =========================
      // BASE QUERY (NO GROUP BY NEEDED)
      // =========================
      $base_query = "
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu
   
           INNER JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
               ON stu.franchise_id = frn.id
   
           INNER JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
               ON stu.course_id = crs.id
   
           $where_sql
       ";

      // =========================
      // MAIN QUERY
      // =========================
      $sql_fetch_student = "
           SELECT 
               stu.id,
               stu.stu_id,
               stu.stu_name,
               stu.stu_phone,
               stu.stu_dob,
               stu.record_status,
               stu.verified_status,
               stu.image_file_name,
               stu.student_status,
               stu.stu_result,
               stu.created_at,
               frn.center_name,
               crs.course_title
   
           $base_query
   
           ORDER BY stu.id DESC
       ";

      // =========================
      // PAGINATION (SAFE)
      // =========================
      if (empty($dataArr['student_id'])) {
         $sql_fetch_student .= " LIMIT ? OFFSET ?";
         $params[] = $limit;
         $params[] = $offset;
      }

      // =========================
      // COUNT QUERY (OPTIMIZED)
      // =========================
      $sql_count = "SELECT COUNT(*) as total $base_query";

      // =========================
      // DEBUG (OPTIONAL)
      // =========================
      // echo $this->debugQuery($sql_fetch_student, $params); exit;

      // =========================
      // EXECUTION
      // =========================
      $resultArr = [];

      $resultArr['data'] = $this->global_Fetch_All_DB($sql_fetch_student, $params);

      // remove limit params for count
      $count_params = $params;
      if (empty($dataArr['student_id'])) {
         array_pop($count_params); // offset
         array_pop($count_params); // limit
      }

      $resultArr['row_count'] = $this->global_Aggregate_Value_DB($sql_count, $count_params);
      $resultArr['pageNo'] = $pageNo;
      $resultArr['limit'] = $limit;

      return $resultArr;
   }

   public function fetch_Fresh_Students($dataArr = [])
   {
      // =========================
      // BASE WHERE CONDITIONS
      // =========================
      $where = [];
      $where[] = "stu.student_status = 'admitted'";
      $where[] = "stu.created_at >= DATE_SUB(CURDATE(), INTERVAL 2 DAY)";

      // =========================
      // OPTIONAL FILTERS
      // =========================
      if (!empty($dataArr['franchise_id'])) {
         $franchise_id = (int)$dataArr['franchise_id']; // safe
         $where[] = "stu.franchise_id = {$franchise_id}";
      }

      // Combine WHERE clause
      $whereClause = "WHERE " . implode(" AND ", $where);

      // =========================
      // MAIN QUERY
      // =========================
      $sql = "
           SELECT 
               stu.id,
               stu.stu_id,
               stu.stu_name,
               stu.stu_father_name,
               stu.stu_phone,
               stu.student_status,
               stu.created_at,
   
               frn.center_name,
               crs.course_title,
   
               rcpt.id AS receipt_row_id,
               rcpt.receipt_id AS receipt_id,
               rcpt.receipt_amount,
               rcpt.extra_fees,
               rcpt.created_at AS receipt_date,
               pc.name AS category
   
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
               ON stu.franchise_id = frn.id
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
               ON stu.course_id = crs.id
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt 
               ON rcpt.id = (
                   SELECT r.id 
                   FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts r
                   WHERE r.stu_id = stu.stu_id
                   ORDER BY r.id ASC 
                   LIMIT 1
               )
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc 
               ON rcpt.category_id = pc.id
   
           $whereClause
   
           ORDER BY stu.id DESC
         ";

      // Debug (optional)
      //echo $sql; exit;

      // =========================
      // EXECUTE
      // =========================
      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr ?: [];
   }

   public function fetch_Tmp_Students($dataArr)
   {
      $where = [];
      $params = [];

      // =========================
      // BASE FILTER
      // =========================
      $where[] = "tmp_stu.record_status = ?";
      $params[] = $dataArr['record_status'];

      // =========================
      // OPTIONAL FILTERS
      // =========================
      if ($dataArr['conversion_status'] !== null) {
         $where[] = "tmp_stu.conversion_status = ?";
         $params[] = $dataArr['conversion_status'];
      }

      if ($dataArr['verified_status'] !== null) {
         $where[] = "tmp_stu.verified_status = ?";
         $params[] = $dataArr['verified_status'];
      }

      if (!empty($dataArr['franchise_id'])) {
         $where[] = "tmp_stu.franchise_id = ?";
         $params[] = (int)$dataArr['franchise_id'];
      }

      if (!empty($dataArr['course_id']) && $dataArr['course_id'] > 0) {
         $where[] = "tmp_stu.course_id = ?";
         $params[] = (int)$dataArr['course_id'];
      }

      if (!empty($dataArr['result_status'])) {
         $where[] = "tmp_stu.stu_result = ?";
         $params[] = $dataArr['result_status'];
      }

      // =========================
      // DATE FILTERS (INDEX FRIENDLY)
      // =========================
      if (!empty($dataArr['created'])) {
         $date = date('Y-m-d', strtotime($dataArr['created']));
         $where[] = "tmp_stu.created_at >= ? AND tmp_stu.created_at < ?";
         $params[] = $date . " 00:00:00";
         $params[] = $date . " 23:59:59";
      }

      if (!empty($dataArr['search_start'])) {
         $where[] = "tmp_stu.created_at >= ?";
         $params[] = $dataArr['search_start'] . " 00:00:00";
      }

      if (!empty($dataArr['search_end'])) {
         $where[] = "tmp_stu.created_at <= ?";
         $params[] = $dataArr['search_end'] . " 23:59:59";
      }

      // =========================
      // SEARCH
      // =========================
      if (!empty($dataArr['search_string'])) {
         $search = "%" . $dataArr['search_string'] . "%";

         $where[] = "(
               tmp_stu.tmp_stu_id LIKE ? OR
               tmp_stu.stu_name LIKE ? OR
               tmp_stu.stu_father_name LIKE ? OR
               tmp_stu.stu_phone LIKE ?
         )";

         // push same param 4 times
         for ($i = 0; $i < 4; $i++) {
            $params[] = $search;
         }
      }

      // =========================
      // FINAL WHERE
      // =========================
      $whereSql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

      // =========================
      // PAGINATION
      // =========================
      $limit = (int)$dataArr['limit'];
      $pageNo = (int)$dataArr['pageNo'];
      $offset = ($pageNo - 1) * $limit;

      // =========================
      // BASE QUERY
      // =========================
      $baseSql = "
         FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu

         LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
               ON tmp_stu.franchise_id = frn.id

         LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
               ON tmp_stu.course_id = crs.id

         $whereSql
      ";

      // =========================
      // DATA QUERY
      // =========================
      $dataSql = "
         SELECT 
               tmp_stu.*,
               frn.center_name,
               crs.course_title

         $baseSql

         ORDER BY tmp_stu.id DESC
         LIMIT ?, ?
      ";

      // =========================
      // COUNT QUERY
      // =========================
      $countSql = "
         SELECT COUNT(DISTINCT tmp_stu.id) as total
         $baseSql
      ";

      // =========================
      // EXECUTION
      // =========================
      $dataParams = array_merge($params, [$offset, $limit]);

      // Debug if needed
      // $this->debugQuery($dataSql, $dataParams);

      $data = $this->global_Fetch_All_DB($dataSql, $dataParams);
      $total = $this->global_Aggregate_Value_DB($countSql, $params);

      return [
         'data' => $data,
         'row_count' => $total,
         'pageNo' => $pageNo,
         'limit' => $limit
      ];
   }

   public function fetch_Student_Admission_Receipt($student_id)
   {
      $sql = "SELECT rcpt.*,pc.name as category FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc ON rcpt.category_id = pc.id WHERE rcpt.stu_id = '$student_id' ORDER BY rcpt.id ASC LIMIT 1";

      $resultArr = $this->global_Fetch_Single_DB($sql);
      return $resultArr;
   }

   public function fetch_Dashboard_Student_Data($dataArr)
   {
      // =========================
      // DATE RANGES
      // =========================
      $today       = date('Y-m-d');
      $weekStart   = date('Y-m-d', strtotime("monday this week"));
      $monthStart  = date('Y-m-d', strtotime("first day of this month"));
      $yearStart   = date('Y-m-d', strtotime("first day of January this year"));

      $fetchType   = $dataArr['fetchType'] ?? 'monthly';
      $franchiseId = (int)($dataArr['franchise_id'] ?? 0);

      // =========================
      // BUILD DATE FILTER
      // =========================
      switch ($fetchType) {
         case 'today':
            $dateCondition = "stu.created_at >= ? AND stu.created_at < DATE_ADD(?, INTERVAL 1 DAY)";
            $params = [$today, $today];
            break;

         case 'weekly':
            $dateCondition = "stu.created_at >= ?";
            $params = [$weekStart];
            break;

         case 'annual':
            $dateCondition = "stu.created_at >= ?";
            $params = [$yearStart];
            break;

         case 'monthly':
         default:
            $dateCondition = "stu.created_at >= ?";
            $params = [$monthStart];
            break;
      }

      // =========================
      // BASE QUERY (REUSABLE)
      // =========================
      $baseQuery = "
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "results rslt 
               ON stu.stu_id = rslt.stu_id
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
               ON stu.franchise_id = frn.id
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
               ON stu.course_id = crs.id
       ";

      // =========================
      // WHERE CLAUSE
      // =========================
      $where = "WHERE $dateCondition";

      if ($franchiseId > 0) {
         $where .= " AND stu.franchise_id = ?";
         $params[] = $franchiseId;

         $totalWhere = "WHERE stu.franchise_id = ?";
         $totalParams = [$franchiseId];
      } else {
         $totalWhere = "WHERE 1";
         $totalParams = [];
      }

      // =========================
      // FINAL QUERIES
      // =========================
      $dataSql = "
           SELECT 
               stu.*, 
               frn.center_name, 
               crs.course_title, 
               rslt.stu_result
           $baseQuery
           $where
           GROUP BY stu.id
           ORDER BY stu.id DESC
       ";

      $countSql = "
           SELECT COUNT(DISTINCT stu.id) as total
           $baseQuery
           $totalWhere
       ";

      // =========================
      // EXECUTION
      // =========================
      $resultArr['data'] = $this->global_Fetch_All_DB($dataSql, $params);
      $resultArr['row_count'] = $this->global_Aggregate_Value_DB($countSql, $totalParams);

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

      $resultArr = $this->global_Fetch_All_DB($sql);

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

      $resultArr['data'] = $this->global_Fetch_All_DB($sql);
      $resultArr['row_count'] = $this->global_Rows_Count_DB($sql_row_count);

      return $resultArr;
   }

   public function fetch_Global_Exams($dataArr = array())
   {

      $record_status = $dataArr['record_status'];

      $where_Clause = "WHERE exm.record_status = '$record_status'";

      $sql = "SELECT exm.*,COUNT(DISTINCT exq.id) as question_count,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exams exm LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions exq ON exm.id = exq.exam_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON exm.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON exm.course_id = crs.id " . $where_Clause . " ORDER BY exm.id DESC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Exam_Questions($exam_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions eqs WHERE eqs.exam_id = '$exam_id' ORDER BY eqs.ordering ASC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Exam_Questions_Limit($dataArr)
   {
      // =========================
      // PAGINATION
      // =========================
      $limit  = (int)($dataArr['limit'] ?? 10);
      $pageNo = (int)($dataArr['page'] ?? 1);
      $offset = ($pageNo - 1) * $limit;

      // =========================
      // WHERE BUILDER
      // =========================
      $where = [];
      $params = [];

      // Mandatory filter
      $where[] = "eqs.exam_id = ?";
      $params[] = $dataArr['exam_id'];

      if (!empty($dataArr['search_string'])) {
         $where[] = "(eqs.ques LIKE ?)";
         $params[] = '%' . $dataArr['search_string'] . '%';
      }

      $where_sql = "WHERE " . implode(" AND ", $where);

      // =========================
      // BASE QUERY (REUSABLE)
      // =========================
      $base_query = "
        FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions eqs
        $where_sql
    ";

      // =========================
      // MAIN DATA QUERY
      // =========================
      $sql_fetch = "
        SELECT 
            eqs.id,
            eqs.exam_id,
            eqs.ques,
            eqs.opt1,
            eqs.opt2,
            eqs.opt3,
            eqs.opt4,
            eqs.cor_ans,
            eqs.marks,
            eqs.record_status,
            eqs.ordering,
            eqs.created_at
        $base_query
        ORDER BY eqs.ordering ASC
    ";

      // Apply pagination
      $sql_fetch .= " LIMIT ? OFFSET ?";
      $params[] = $limit;
      $params[] = $offset;

      // =========================
      // COUNT QUERY
      // =========================
      $sql_count = "SELECT COUNT(*) as total $base_query";

      // =========================
      // EXECUTION
      // =========================
      $resultArr = [];

      // Fetch data
      $resultArr['data'] = $this->global_Fetch_All_DB($sql_fetch, $params);

      // Optional debug
      //$this->debugQuery($sql_fetch, $params);

      // Prepare count params (remove limit + offset)
      $count_params = $params;
      array_pop($count_params); // offset
      array_pop($count_params); // limit

      // Fetch total count
      $resultArr['row_count'] = $this->global_Aggregate_Value_DB($sql_count, $count_params);

      // Extra info
      $resultArr['pageNo'] = $pageNo;
      $resultArr['limit'] = $limit;

      return $resultArr;
   }

   public function fetch_User_Exam_Answers($answerParamArr)
   {

      $exam_id = $answerParamArr['exam_id'];
      $student_id = $answerParamArr['student_id'];

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_answers ea WHERE ea.exam_id = '$exam_id' AND ea.student_id = '$student_id' ORDER BY ea.ques_id ASC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Flagged_Questions($answerParamArr)
   {

      $exam_id = $answerParamArr['exam_id'];
      $student_id = $answerParamArr['student_id'];

      $sql = "SELECT efq.ques_id FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions efq WHERE efq.exam_id = '$exam_id' AND efq.student_id = '$student_id' ORDER BY efq.ques_id ASC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Viewed_Questions($answerParamArr)
   {

      $exam_id = $answerParamArr['exam_id'];
      $student_id = $answerParamArr['student_id'];

      $sql = "SELECT evq.ques_id FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_viewed_questions evq WHERE evq.exam_id = '$exam_id' AND evq.student_id = '$student_id' ORDER BY evq.ques_id ASC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Parent_Category($record_status = 'active')
   {

      //$sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."parent_category ORDER BY id DESC";

      $sql = "SELECT pc.id,pc.parent_category,pc.name,pc.record_status,pc.created_at FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc WHERE pc.record_status = '$record_status' ORDER BY pc.id DESC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Cities($record_status = 'active')
   {

      //$sql = "SELECT * FROM ".DB_AIMGCSM.".".TABLEPREFIX."parent_category ORDER BY id DESC";

      $sql = "SELECT c.* FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "cities c WHERE c.record_status = '$record_status' ORDER BY c.id DESC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

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

      $resultArr['data'] = $this->global_Fetch_All_DB($sql_fetch_enquiry);
      $resultArr['row_count'] = $this->global_Rows_Count_DB($sql_row_count);
      $resultArr['pageNo'] = $dataArr['pageNo'];
      $resultArr['limit'] = $dataArr['limit'];

      return $resultArr;
   }

   public function fetch_Email_Templates($record_status = 'active')
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template et WHERE et.record_status='$record_status' ORDER BY id DESC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_News($dataArr = array())
   {

      $record_status = $dataArr['record_status'];

      $where_Clause = "WHERE nws.record_status = '$record_status'";

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "news nws " . $where_Clause . " ORDER BY nws.id DESC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Single_Data($type, $row_id)
   {
      // =========================
      // TABLE MAPPING (SAFE WHITELIST)
      // =========================
      $allowedTables = [
         'franchise'         => 'franchise',
         'student'           => 'students',
         'course'            => 'course',
         'gallery'           => 'gallery',
         'home_sliders'      => 'home_sliders',
         'student_receipts'  => 'student_receipts',
         'news'              => 'news'
      ];

      // Validate type
      if (!isset($allowedTables[$type])) {
         return null; // or throw exception
      }

      $table = $allowedTables[$type];

      // =========================
      // QUERY
      // =========================
      $sql = "SELECT * 
            FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "$table
            WHERE id = ?
            LIMIT 1";

      $params = [
         (int)$row_id
      ];

      // Optional debug
      // $this->debugQuery($sql, $params);

      $resultArr = $this->global_Fetch_Single_DB($sql, $params);

      return $resultArr;
   }

   public function fetch_Global_Multiple_Data($type, $rowIds)
   {
      $ids = implode(',', array_map('intval', $rowIds));

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "$type 
            WHERE id IN ($ids)";

      return $this->global_Fetch_All_DB($sql);
   }

   public function check_Slug_Availibility($type, $field, $slug)
   {
      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $type . " WHERE `$field`='$slug'";
      //echo $sql;exit();
      $retrunArr = $this->global_Fetch_Single_DB($sql);

      return $retrunArr;
   }

   public function fetch_Single_Parent_Category($parent_category)
   {
      $sql = "SELECT pc.id, pc.parent_category, pc.name 
               FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc 
               WHERE pc.parent_category = ?
               ORDER BY pc.id DESC";

      $resultArr = $this->global_Fetch_All_DB($sql, [$parent_category]);

      return $resultArr;
   }

   public function fetch_Last_Franchise_Detail()
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise ORDER BY fran_id DESC LIMIT 1";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Last_Student_Detail()
   {

      $sql_current_student = "SELECT stu.stu_id FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu ORDER BY id DESC LIMIT 1";
      //echo $sql_current_student;exit;
      $lst_stu_id = $this->global_Fetch_Single_DB($sql_current_student)->stu_id;

      return array('lst_stu_id' => $lst_stu_id);
   }

   public function fetch_Last_Receipt_Detail()
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts ORDER BY id DESC LIMIT 1";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Receipt_Detail($receipt_id)
   {
      $sql = "SELECT 
                  rcpt.*,
                  pc.name AS category
               FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt
               LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc 
                  ON rcpt.category_id = pc.id
               WHERE rcpt.id = ?
               LIMIT 1";

      $params = [
         (int)$receipt_id
      ];

      // Optional debug
      // $this->debugQuery($sql, $params);

      $resultArr = $this->global_Fetch_Single_DB($sql, $params);

      return $resultArr;
   }

   public function fetch_Student_Exam_Detail($exam_id)
   {

      $sql = "SELECT exm.*,COUNT(DISTINCT exq.id) as question_count,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exams exm LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions exq ON exm.id = exq.exam_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON exm.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON exm.course_id = crs.id WHERE exm.id = '$exam_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Email_Template_Detail($template_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template WHERE `id` = '$template_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_News_Detail($news_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "news WHERE `id` = '$news_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

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

      $resultArr = $this->global_CRUD_DB($sql);

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
      $params = [];

      // Common fields
      $commonFields = [
         'stu_name' => $stuDataArr['stu_name'],
         'stu_father_name' => $stuDataArr['stu_father_name'],
         'stu_phone' => $stuDataArr['stu_phone'],
         'stu_email' => $stuDataArr['stu_email'],
         'stu_gender' => $stuDataArr['stu_gender'],
         'stu_marital_status' => $stuDataArr['stu_marital_status'],
         'stu_address' => $stuDataArr['stu_address'],
         'course_id' => $stuDataArr['course_id'],
         'stu_qualification' => $stuDataArr['stu_qualification'],
         'stu_course_fees' => $stuDataArr['stu_course_fees'],
         'monthly_course_fees' => $stuDataArr['monthly_course_fees'],
         'month_exclude_receipt' => $stuDataArr['month_exclude_receipt'],
         'stu_course_discount' => $stuDataArr['stu_course_discount'],
         'fees_paid_before_dr' => $stuDataArr['fees_paid_before_dr'],
         'student_status' => $stuDataArr['student_status'],
         'stu_result' => $stuDataArr['stu_result'],
         'franchise_id' => $stuDataArr['franchise_id'],
         'stu_dob' => $stuDataArr['stu_dob'],
         'record_status' => $stuDataArr['record_status'],
         'conversion_status' => $stuDataArr['conversion_status'],
         'image_file_name' => $stuDataArr['image_file_name'],
         'stu_notes' => $stuDataArr['stu_notes']
      ];

      // Fields ONLY for update
      $updateOnlyFields = [
         'verified_status' => $stuDataArr['verified_status']
      ];

      // Decide UPDATE or INSERT
      if (!empty($stuDataArr['stu_row_id']) && $stuDataArr['stu_row_id'] !== "null") {

         // Merge fields for update
         $fields = array_merge($commonFields, $updateOnlyFields);

         // UPDATE
         $setParts = [];

         foreach ($fields as $column => $value) {
            $setParts[] = "$column = ?";
            $params[] = $value;
         }

         $sql = "
               UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students 
               SET " . implode(", ", $setParts) . ", updated_at = NOW()
               WHERE id = ?
           ";

         $params[] = $stuDataArr['stu_row_id'];
      } else {

         // INSERT (exclude verified_status)
         $fields = $commonFields;

         // INSERT
         $fields['stu_id'] = $stuDataArr['stu_id']; // only for insert

         $columns = [];
         $placeholders = [];

         foreach ($fields as $column => $value) {
            $columns[] = $column;
            $placeholders[] = "?";
            $params[] = $value;
         }

         $sql = "
               INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "students 
               (" . implode(", ", $columns) . ", created_at)
               VALUES (" . implode(", ", $placeholders) . ", NOW())
           ";
      }

      // Debug (optional)
      // $this->debugQuery($sql, $params);

      return $this->global_CRUD_DB($sql, $params);
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

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function manage_Temp_Student($stuDataArr)
   {

      $id = $stuDataArr['id'];

      $stu_name = $stuDataArr['stu_name'];
      $stu_father_name = $stuDataArr['stu_father_name'];
      $stu_phone = $stuDataArr['stu_phone'];

      $course_id = $stuDataArr['course_id'];
      $franchise_id = $stuDataArr['franchise_id'];

      $advanced_fees = $stuDataArr['advanced_fees'];

      if ($id != 'null') {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students SET `stu_name` = '$stu_name', `stu_father_name` = '$stu_father_name',`stu_phone` = '$stu_phone', `course_id` = '$course_id', `franchise_id` = '$franchise_id', `advanced_fees` = '$advanced_fees',`verified_status` = 'n', `updated_at` = now() WHERE `id`='$id'";
      } else {
         //student id 
         $tmp_stu_id = $stuDataArr['tmp_stu_id'];

         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students SET `tmp_stu_id` = '$tmp_stu_id', `stu_name` = '$stu_name', `stu_father_name` = '$stu_father_name',`stu_phone` = '$stu_phone', `course_id` = '$course_id', `franchise_id` = '$franchise_id', `advanced_fees` = '$advanced_fees', `created_at` = now()";
      }

      //echo $sql;exit();

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_student_monthly_course_fees($paramArr)
   {
      // =========================
      // VALIDATION + SANITIZATION
      // =========================
      if (empty($paramArr['stu_id'])) {
         return [
            'check' => 'failure',
            'message' => 'Student ID is required'
         ];
      }

      if (!isset($paramArr['monthly_course_fees'])) {
         return [
            'check' => 'failure',
            'message' => 'Monthly course fees is required'
         ];
      }

      $stu_id = $paramArr['stu_id'];
      $monthly_course_fees = (int)$paramArr['monthly_course_fees'];

      // Optional: prevent negative values
      if ($monthly_course_fees < 0) {
         return [
            'check' => 'failure',
            'message' => 'Monthly course fees cannot be negative'
         ];
      }

      // =========================
      // QUERY (PARAM BINDING)
      // =========================
      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students 
               SET monthly_course_fees = ?, updated_at = NOW() 
               WHERE stu_id = ?";

      $params = [
         $monthly_course_fees,
         $stu_id
      ];

      // =========================
      // DEBUG (OPTIONAL)
      // =========================
      // $this->debugQuery($sql, $params); exit;

      // =========================
      // EXECUTION
      // =========================
      $resultArr = $this->global_CRUD_DB($sql, $params);

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

      $resultArr = $this->global_CRUD_DB($sql);

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

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function fetch_Last_Question_Ordering($exam_id)
   {
      $sql = "SELECT eqs.ordering FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions eqs WHERE eqs.exam_id='$exam_id' ORDER BY eqs.ordering DESC LIMIT 1";

      //echo $sql;exit;

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function import_Exam_Questions($questionData)
   {

      $exam_id = $questionData['exam_id'];

      $ques = $this->escape($questionData['ques'] ?? '');
      $opt1 = $this->escape($questionData['opt1'] ?? '');
      $opt2 = $this->escape($questionData['opt2'] ?? '');
      $opt3 = $this->escape($questionData['opt3'] ?? '');
      $opt4 = $this->escape($questionData['opt4'] ?? '');
      $cor_ans = (int)$questionData['cor_ans'];
      $marks = (int)$questionData['marks'];
      $ordering = (int)$questionData['ordering'];

      $record_status = $questionData['record_status'];

      $sql_insert_question = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions SET `exam_id` = '$exam_id',`ques` = '$ques',`opt1`='$opt1',`opt2`='$opt2',`opt3`='$opt3',`opt4`='$opt4',`cor_ans`='$cor_ans',`marks`='$marks',`ordering`='$ordering',`record_status`='$record_status',`updated_at` = now()";

      //echo $sql_insert_question;exit();

      //Call save exam questions
      $this->global_CRUD_DB($sql_insert_question);
   }

   public function update_Exam_Questions($data)
   {
      $exam_id = (int)$data['exam_id'];
      $createList = $data['create'] ?? [];
      $updateList = $data['update'] ?? [];

      mysqli_begin_transaction($this->db);

      try {

         // =============================
         // 1. BULK INSERT (CREATE)
         // =============================
         if (!empty($createList)) {

            $values = [];
            $createdQuestions = [];
            $updatedQuestions = [];

            foreach ($createList as $q) {

               $ques = $this->escape($q['ques'] ?? '');
               $opt1 = $this->escape($q['opt1'] ?? '');
               $opt2 = $this->escape($q['opt2'] ?? '');
               $opt3 = $this->escape($q['opt3'] ?? '');
               $opt4 = $this->escape($q['opt4'] ?? '');
               $cor_ans = (int)$q['cor_ans'];
               $marks = (int)$q['marks'];
               $ordering = (int)$q['ordering'];
               $record_status = $this->escape($q['record_status'] ?? '');

               $values[] = "(
                    '$exam_id',
                    '$ques',
                    '$opt1',
                    '$opt2',
                    '$opt3',
                    '$opt4',
                    '$cor_ans',
                    '$marks',
                    '$ordering',
                    '$record_status',
                    NOW()
                )";
            }

            $sqlInsert = "
                INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions
                (exam_id, ques, opt1, opt2, opt3, opt4, cor_ans, marks, ordering, record_status, updated_at)
                VALUES " . implode(',', $values);

            $returnArr = $this->global_CRUD_DB($sqlInsert);

            $q['id'] = $returnArr['last_insert_id'];

            if ($returnArr['check'] == 'success') {
               $createdQuestions[] = $q;
            }
         }

         // =============================
         // 2. UPDATE EXISTING
         // =============================
         if (!empty($updateList)) {

            foreach ($updateList as $q) {

               $id = (int)$q['id'];

               $ques = $this->escape($q['ques'] ?? '');
               $opt1 = $this->escape($q['opt1'] ?? '');
               $opt2 = $this->escape($q['opt2'] ?? '');
               $opt3 = $this->escape($q['opt3'] ?? '');
               $opt4 = $this->escape($q['opt4'] ?? '');
               $cor_ans = (int)$q['cor_ans'];
               $marks = (int)$q['marks'];
               $ordering = (int)$q['ordering'];
               $record_status = $this->escape($q['record_status'] ?? '');

               $sqlUpdate = "
                    UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions
                    SET 
                        ques = '$ques',
                        opt1 = '$opt1',
                        opt2 = '$opt2',
                        opt3 = '$opt3',
                        opt4 = '$opt4',
                        cor_ans = '$cor_ans',
                        marks = '$marks',
                        ordering = '$ordering',
                        record_status = '$record_status',
                        updated_at = NOW()
                    WHERE id = '$id' AND exam_id = '$exam_id'
                ";

               $returnArr = $this->global_CRUD_DB($sqlUpdate);

               if ($returnArr['check'] == 'success') {
                  $updatedQuestions[] =  $q;
               }
            }
         }

         // =============================
         // COMMIT
         // =============================
         mysqli_commit($this->db);

         return [
            'check' => 'success',
            'message' => 'Questions updated successfully',
            'data' => [
               'created' => $createdQuestions,
               'updated' => $updatedQuestions
            ]
         ];
      } catch (Exception $e) {

         mysqli_rollback($this->db);

         return [
            'check' => 'failure',
            'message' => 'Transaction failed: ' . $e->getMessage()
         ];
      }
   }

   public function delete_All_Questions($exam_id)
   {
      $sql = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions WHERE `exam_id` = '$exam_id'";

      //echo $sql;exit;

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function save_Exam_Questions_Order($ordeData)
   {

      $exam_id = $ordeData['exam_id'];
      $question_id = $ordeData['question_id'];
      $ordering = $ordeData['ordering'];

      $sql_update_ordering = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions SET `ordering` = '$ordering',`updated_at`=now() WHERE `exam_id` = '$exam_id' AND `id` = '$question_id'";

      //echo $sql_update_ordering;exit;

      $resultArr = $this->global_CRUD_DB($sql_update_ordering);

      return $resultArr;
   }

   public function update_Exam_Validation_Log($flagArr)
   {

      $student_id = $flagArr['student_id'];
      $exam_id = $flagArr['exam_id'];

      //Check if this question is already flagged
      $query_log_caluse = "efq.exam_id = '$exam_id' AND efq.student_id = '$student_id'";

      $sql_count_log = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_log efq WHERE " . $query_log_caluse;

      //echo $sql_count_log;exit();

      $row_count = $this->global_Rows_Count_DB($sql_count_log);

      if ($row_count > 0) {
         return array('check' => 'failure', 'message' => "Exam has already started");
      } else {

         $sql_insert_log = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_log SET `exam_id` = '$exam_id',`student_id` = '$student_id',`status`='started',`started_at` = now()";

         //echo $sql_insert_log;exit();

         //Call save exam questions
         $resultArr = $this->global_CRUD_DB($sql_insert_log);

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

      $resultArr = $this->global_CRUD_DB($sql_delete_answer);

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
               $resultArr = $this->global_CRUD_DB($sql_insert_answer);
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

      $row_count = $this->global_Rows_Count_DB($sql_count_flagged);

      if ($row_count > 0) {
         $flag_status = 'deleted';

         $sql_delete_answer = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions WHERE `exam_id` = '$exam_id' AND `ques_id` = '$ques_id' AND `student_id` = '$student_id'";

         //echo $sql_delete_answer;exit();

         $resultArr = $this->global_CRUD_DB($sql_delete_answer);
      } else {
         $flag_status = 'added';

         $sql_insert_flag = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions SET `exam_id` = '$exam_id',`student_id` = '$student_id',`ques_id`='$ques_id',`updated_at` = now()";

         //echo $sql_insert_flag;exit();

         //Call save exam questions
         $resultArr = $this->global_CRUD_DB($sql_insert_flag);
      }

      if ($resultArr['check'] == "success") {

         //Check if this question is attempted
         $sql_check_answered = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_answers ea WHERE ea.exam_id = '$exam_id' AND ea.ques_id = '$ques_id' AND ea.student_id = '$student_id'";

         //echo $sql_check_answered;exit;

         $answered_row_count = $this->global_Rows_Count_DB($sql_check_answered);

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

      $row_count = $this->global_Rows_Count_DB($sql_count_viewed);

      if ($row_count > 0) {
         return array('check' => 'failure', 'qId' => $ques_id);
      } else {

         $sql_insert_view = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_viewed_questions SET `exam_id` = '$exam_id',`student_id` = '$student_id',`ques_id`='$ques_id',`updated_at` = now()";

         //echo $sql_insert_view;exit();

         //Call save exam questions
         $resultArr = $this->global_CRUD_DB($sql_insert_view);

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

      $resultArr = $this->global_CRUD_DB($sql);

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

      $resultArr = $this->global_CRUD_DB($sql);

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

      $resultArr = $this->global_CRUD_DB($sql);

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
      // $this->debugQuery($sql, $params);

      // =========================
      // EXECUTE
      // =========================
      $resultArr = $this->global_CRUD_DB($sql, $params);

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

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Student_Admission_Receipt($receiptDataArr)
   {
      $receipt_row_id = $receiptDataArr['receipt_row_id'];
      $receipt_amount = $receiptDataArr['receipt_amount'];

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts SET `receipt_amount` = '$receipt_amount',`updated_at` = now() WHERE `id`='$receipt_row_id'";

      $resultArr = $this->global_CRUD_DB($sql);

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

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function edit_Post_Category($updateDataArr)
   {

      $categoryArr = $updateDataArr['category_id'];
      $post_type = $updateDataArr['post_type'];
      $post_id = $updateDataArr['post_id'];

      $sql_delete_category = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category WHERE post_type = '$post_type' AND `post_id`='$post_id'";

      //echo $sql_delete_category;exit;

      $this->global_CRUD_DB($sql_delete_category);

      //print_r($updateDataArr);exit;
      if (count($categoryArr) > 0) {
         foreach ($categoryArr as $index => $category_id) {

            $category_data = $this->escape($category['category_id'] ?? '');

            $sql_insert_category = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category SET `post_type` = '$post_type',`post_id` = '$post_id',`category_id`='$category_id',`updated_at` = now()";

            //echo $sql_insert_meta;exit();

            $resultArr = $this->global_CRUD_DB($sql_insert_category);
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

      $resultArr = $this->global_CRUD_DB($sql);

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

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function import_Global_City($paramArr)
   {

      $name = $paramArr['name'];
      $record_status = $paramArr['record_status'];

      $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "cities SET `name` = '$name',`record_status` = '$record_status',`created_at` = now()";

      //echo $sql;exit();

      $resultArr = $this->global_CRUD_DB($sql);

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

   public function fetch_Global_Single_Franchise_By_Uid($fran_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise WHERE fran_id = '$fran_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

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
               
      // Debug
      //$this->debugQuery($sql, $params);         

      $resultArr = $this->global_Fetch_Single_DB($sql, $params);

      return $resultArr;
   }

   public function fetch_Tmp_Single_Student($id)
   {

      $sql = "SELECT tmp_stu.*,frn.center_name,frn.fran_email,frn.fran_phone,frn.fran_address,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON tmp_stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON tmp_stu.course_id = crs.id WHERE id = '$id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Single_Profile_Student($stu_id)
   {

      $sql = "SELECT stu.*,rslt.stu_result as student_result,rslt.result_date,rslt.file_upload_type as result_upload_type,rslt.result_pdf,frn.center_name,bth.batch_name,bth.start_date,bth.end_date,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "results rslt ON stu.stu_id = rslt.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "batch bth ON stu.batch_id = bth.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id WHERE stu.stu_id = '$stu_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Detail_Single_Student($student_id)
   {
      $where = [];
      $params = [];

      // Allow search by stu_id OR id
      $where[] = "(stu.stu_id = ? OR stu.id = ?)";
      $params[] = $student_id;
      $params[] = $student_id;

      $whereSql = "WHERE " . implode(" AND ", $where);

      $sql = "
           SELECT 
               stu.*,
               frn.center_name,
               crs.course_title,
               rslt.stu_result,
               COUNT(DISTINCT rcpt.id) AS receipt_count
   
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "results rslt 
               ON stu.stu_id = rslt.stu_id
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt 
               ON stu.stu_id = rcpt.stu_id
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn 
               ON stu.franchise_id = frn.id
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs 
               ON stu.course_id = crs.id
   
           $whereSql
       ";

      // Important because of COUNT()
      $sql .= " GROUP BY stu.id LIMIT 1";

      // Debug (optional)
      // $this->debugQuery($sql, $params);

      $resultArr = $this->global_Fetch_Single_DB($sql, $params);

      return $resultArr;
   }

   public function fetch_Detail_Single_Student_Receipt($student_id)
   {

      $sql = "SELECT stu.*,frn.center_name,crs.course_title,COUNT(DISTINCT rcpt.id) as receipt_count FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt ON stu.stu_id = rcpt.stu_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON stu.course_id = crs.id WHERE stu.stu_id = '$student_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

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

      $resultArr = $this->global_Fetch_Single_DB($sql, [$row_id]);

      return $resultArr;
   }

   public function fetch_Global_Single_Course($course_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "course WHERE id = '$course_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Single_Blog($blog_id)
   {

      $sql = "SELECT bg.*,GROUP_CONCAT(DISTINCT poc.category_id) as category_string FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "blog bg LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category poc ON bg.id = poc.post_id WHERE bg.id = '$blog_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function update_Global_Record_Status($type, $rowIds, $record_status)
   {
      // -----------------------------
      // TYPE CONFIG MAP
      // -----------------------------
      $typeConfig = [
         'franchise'        => ['table' => 'franchise',         'column' => 'id'],
         'course'           => ['table' => 'course',            'column' => 'id'],
         'gallery'          => ['table' => 'gallery',           'column' => 'id'],
         'home_sliders'     => ['table' => 'home_sliders',      'column' => 'id'],
         'student'          => ['table' => 'students',          'column' => 'id'],
         'temp_student'     => ['table' => 'temp_students',     'column' => 'id'],
         'student_receipts' => ['table' => 'student_receipts',  'column' => 'id'],
         'exam'             => ['table' => 'exams',             'column' => 'id'],
         'parent_category'  => ['table' => 'parent_category',   'column' => 'id'],
         'cities'           => ['table' => 'cities',            'column' => 'id'],
         'email_template'   => ['table' => 'email_template',    'column' => 'id'],
         'news'             => ['table' => 'news',              'column' => 'id'],
         'enquiry'          => ['table' => 'enquiry',           'column' => 'id'],
      ];

      // -----------------------------
      // VALIDATE TYPE
      // -----------------------------
      if (!isset($typeConfig[$type])) {
         return ['responseArr' => ['check' => 'failure', 'message' => 'Invalid type']];
      }

      $table  = $typeConfig[$type]['table'];
      $column = $typeConfig[$type]['column'];

      if (empty($rowIds)) {
         return ['responseArr' => ['check' => 'failure', 'message' => 'Invalid IDs']];
      }

      $idsString = implode(',', $rowIds);

      // -----------------------------
      // BULK UPDATE QUERY
      // -----------------------------
      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "$table 
            SET `record_status` = '$record_status', `updated_at` = NOW()
            WHERE `$column` IN ($idsString)";

      $resultArr['responseArr'] = $this->global_CRUD_DB($sql);

      // -----------------------------
      // SPECIAL LOGIC (STUDENT ONLY)
      // -----------------------------
      if ($type === 'student' && $resultArr['responseArr']['check'] === 'success') {

         // Bulk update receipts
         $this->block_Student_Dependent_Receipt_Data($rowIds, $record_status);

         // Bulk update temp students
         $this->block_Student_Dependent_Temp_Data($rowIds, $record_status);
      }

      return $resultArr;
   }

   public function update_Bulk_Student_Status($paramArr)
   {
      $updateParts = [];

      if (!empty($paramArr['record_status'])) {
         $record_status = $paramArr['record_status'];
         $updateParts[] = "`record_status` = '$record_status'";
      }

      if (!empty($paramArr['student_status'])) {
         $student_status = $paramArr['student_status'];
         $updateParts[] = "`student_status` = '$student_status'";
      }

      if (!empty($paramArr['result_status'])) {
         $result_status = $paramArr['result_status'];
         $updateParts[] = "`stu_result` = '$result_status'";
      }

      $updateParts[] = "`updated_at` = NOW()";

      if (empty($updateParts) || empty($paramArr['row_ids'])) {
         return ['responseArr' => ['check' => 'failure', 'message' => 'No data to update']];
      }

      $updateString = implode(', ', $updateParts);
      $idsString = implode(',', $paramArr['row_ids']);

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students 
               SET $updateString 
               WHERE `id` IN ($idsString)";

      $resultArr['responseArr'] = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Global_Featured_Status($type, $rowIds, $featured_status)
   {
      // -----------------------------
      // TYPE CONFIG MAP
      // -----------------------------
      $typeConfig = [
         'franchise'    => ['table' => 'franchise',     'column' => 'id'],
         'course'       => ['table' => 'course',        'column' => 'id'],
         'gallery'      => ['table' => 'gallery',       'column' => 'id'],
         'news'         => ['table' => 'news',          'column' => 'id'],
         // add more if needed later
      ];

      // -----------------------------
      // VALIDATE TYPE
      // -----------------------------
      if (!isset($typeConfig[$type])) {
         return [
            'responseArr' => [
               'check' => 'failure',
               'message' => 'Invalid type'
            ]
         ];
      }

      $table  = $typeConfig[$type]['table'];
      $column = $typeConfig[$type]['column'];

      if (empty($rowIds)) {
         return [
            'responseArr' => [
               'check' => 'failure',
               'message' => 'Invalid IDs'
            ]
         ];
      }

      $idsString = implode(',', $rowIds);

      // -----------------------------
      // BULK UPDATE QUERY
      // -----------------------------
      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "$table 
            SET `featured_status` = '$featured_status',
                `updated_at` = NOW()
            WHERE `$column` IN ($idsString)";

      $resultArr['responseArr'] = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Global_Verified_Status($type, $rowIds, $verified_status)
   {
      // -----------------------------
      // TYPE CONFIG MAP
      // -----------------------------
      $typeConfig = [
         'franchise'    => ['table' => 'franchise',     'column' => 'id'],
         'course'       => ['table' => 'course',        'column' => 'id'],
         'gallery'      => ['table' => 'gallery',       'column' => 'id'],
         'news'         => ['table' => 'news',          'column' => 'id'],
      ];

      // -----------------------------
      // VALIDATE TYPE
      // -----------------------------
      if (!isset($typeConfig[$type])) {
         return [
            'responseArr' => [
               'check' => 'failure',
               'message' => 'Invalid type'
            ]
         ];
      }

      $table  = $typeConfig[$type]['table'];
      $column = $typeConfig[$type]['column'];

      if (empty($rowIds)) {
         return [
            'responseArr' => [
               'check' => 'failure',
               'message' => 'Invalid IDs'
            ]
         ];
      }

      $idsString = implode(',', $rowIds);

      // -----------------------------
      // BULK UPDATE QUERY
      // -----------------------------
      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "$table 
            SET `verified_status` = '$verified_status',
                `updated_at` = NOW()
            WHERE `$column` IN ($idsString)";

      $resultArr['responseArr'] = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Tmp_Student_Verified_Status($id, $verified_status)
   {

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu SET `verified_status`='$verified_status',`updated_at` = now() WHERE tmp_stu.id = '$id'";
      //echo $sql;exit();
      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Tmp_Student_Conversion_Status($id, $conversion_status)
   {

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu SET `conversion_status`='$conversion_status',`updated_at` = now() WHERE tmp_stu.tmp_id = '$id'";
      //echo $sql;exit();
      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Receipt_Verified_Status($receipt_id, $verified_status)
   {

      if ($verified_status == 'n') {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt SET `verified_status`='$verified_status',`updated_at` = now() WHERE rcpt.receipt_id = '$receipt_id'";
      } else {
         $edit_description = serialize(array());
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt SET `verified_status`='$verified_status',`edit_description`='$edit_description', `updated_at` = now() WHERE rcpt.receipt_id = '$receipt_id'";
      }

      //echo $sql;exit();
      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Student_Verified_Status($student_id, $verified_status)
   {

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu SET `verified_status`='$verified_status',`updated_at` = now() WHERE stu.stu_id = '$student_id'";
      //echo $sql;exit();
      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function block_Student_Dependent_Receipt_Data($studentIds, $record_status)
   {

      if (empty($studentIds)) return;

      $idsString = implode(',', $studentIds);

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts rcpt
               SET rcpt.record_status = '$record_status',
                   rcpt.updated_at = NOW()
               WHERE rcpt.stu_id IN (
                   SELECT stu.stu_id 
                   FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu
                   WHERE stu.id IN ($idsString)
               )";

      $this->global_CRUD_DB($sql);
   }

   public function block_Student_Dependent_Temp_Data($studentIds, $record_status)
   {
      if (empty($studentIds)) return;

      $idsString = implode(',', $studentIds);

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp
            SET tmp.record_status = '$record_status',
                tmp.updated_at = NOW()
            WHERE tmp.id IN (
                SELECT stu.tmp_stu_record_id 
                FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu
                WHERE stu.id IN ($idsString)
                AND stu.tmp_stu_record_id IS NOT NULL
            )";

      $this->global_CRUD_DB($sql);
   }

   public function delete_Student_By_Id($stu_id)
   {
      //Deleting student result  
      $sql_delete_receipt = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students WHERE stu_id = '$stu_id'";
      //echo $sql_delete_receipt;exit;
      $this->global_CRUD_DB($sql_delete_receipt);
   }

   public function delete_Student_Dependent_Data($stu_ids)
   {

      if (empty($stu_ids)) {
         return;
      }

      $idsString = implode(',', $stu_ids);

      // -----------------------------
      // BULK DELETE (ONE QUERY)
      // -----------------------------
      $sql = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts 
            WHERE `stu_id` IN ($idsString)";

      $this->global_CRUD_DB($sql);
   }

   public function delete_Post_Category_Data($post_type, $post_ids)
   {

      if (empty($post_ids)) {
         return;
      }

      $idsString = implode(',', $post_ids);

      // -----------------------------
      // BULK DELETE (ONE QUERY)
      // -----------------------------
      $sql = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category 
            WHERE `post_type` = '$post_type'
            AND `post_id` IN ($idsString)";

      $this->global_CRUD_DB($sql);
   }

   public function delete_Global_Data($deleteParam)
   {
      $type   = $deleteParam['type'];
      $rowIds = $deleteParam['rowIds'] ?? [];

      // -----------------------------
      // TYPE CONFIG MAP
      // -----------------------------
      $typeConfig = [
         'franchise'        => ['table' => 'franchise',        'column' => 'id'],
         'course'           => ['table' => 'course',           'column' => 'id'],
         'gallery'          => ['table' => 'gallery',          'column' => 'id'],
         'home_sliders'     => ['table' => 'home_sliders',     'column' => 'id'],
         'student'          => ['table' => 'students',         'column' => 'id'],
         'temp_student'     => ['table' => 'temp_students',    'column' => 'id'],
         'student_receipts' => ['table' => 'student_receipts', 'column' => 'id'],
         'parent_category'  => ['table' => 'parent_category',  'column' => 'id'],
         'cities'           => ['table' => 'cities',           'column' => 'id'],
         'email_template'   => ['table' => 'email_template',   'column' => 'id'],
         'feedback'         => ['table' => 'feedback',         'column' => 'id'],
         'news'             => ['table' => 'news',             'column' => 'id'],
         'enquiry'          => ['table' => 'enquiry',          'column' => 'id'],
         'student'          => ['table' => 'students',         'column' => 'id'],
         'exam'             => ['table' => 'exams',            'column' => 'id'],
         'exam_questions'   => ['table' => 'exam_questions',   'column' => 'id'],
      ];

      // -----------------------------
      // VALIDATE TYPE
      // -----------------------------
      if (!isset($typeConfig[$type])) {
         return [
            'responseArr' => [
               'check' => 'failure',
               'message' => 'Invalid type'
            ]
         ];
      }

      $table  = $typeConfig[$type]['table'];
      $column = $typeConfig[$type]['column'];

      if (empty($rowIds)) {
         return [
            'responseArr' => [
               'check' => 'failure',
               'message' => 'Invalid IDs'
            ]
         ];
      }

      $idsString = implode(',', $rowIds);

      // -----------------------------
      // SPECIAL PRE-FETCH (STUDENT)
      // -----------------------------
      $studentIds = [];
      if ($type === 'student') {
         $studentData = $this->fetch_Global_Multiple_Data($type, $rowIds);
         if (!empty($studentData)) {
            foreach ($studentData as $stu) {
               $studentIds[] = $stu->stu_id;
            }
         }
      }

      // -----------------------------
      // BULK DELETE QUERY
      // -----------------------------
      $sql = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "$table 
            WHERE `$column` IN ($idsString)";

      $resultArr['responseArr'] = $this->global_CRUD_DB($sql);

      // -----------------------------
      // POST DELETE LOGIC
      // -----------------------------
      if ($resultArr['responseArr']['check'] === 'success') {

         if ($type === 'student' && !empty($studentIds)) {
            // Deleting all student's receipt data
            $this->delete_Student_Dependent_Data($studentIds);
         }

         if ($type === 'gallery' && !empty($studentIds)) {
            // Deleting all gallery category records
            $this->delete_Post_Category_Data($type, $rowIds);
         }
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

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function fetch_Gallery_Arr($record_status = 'active')
   {

      $sql = "SELECT g.*, GROUP_CONCAT(DISTINCT pc.name) as category_string FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery g LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category poc ON ( g.id=poc.post_id AND poc.post_type='gallery' ) LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc ON poc.category_id = pc.id WHERE g.record_status = '$record_status' GROUP BY g.id ORDER BY g.id DESC";

      //echo $sql;exit;

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Gallery_Item_Detail($media_id)
   {

      //Inserting institute general meta info
      $sql = "SELECT g.*,GROUP_CONCAT(DISTINCT poc.category_id) as category_string FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery g LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category poc ON ( g.id=poc.post_id AND poc.post_type='gallery' ) WHERE g.id= '$media_id'";

      //echo $sql;exit;

      $resultArr = $this->global_Fetch_Single_DB($sql);

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

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Slider_Detail($slider_id)
   {

      //Inserting institute general meta info
      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "home_sliders s WHERE s.id= '$slider_id'";

      //echo $sql;exit;

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Single_Account($username)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE user_login = '$username'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

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

   public function fetch_Email_Template_Detail($email_code)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template et WHERE et.code='$email_code'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Global_Site_Setting_Detail()
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "site_setting WHERE `update_id` = 'UPDATE_THE_AIMGCSM_SITE_SETTINGS'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

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

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Global_Site_Setting($data)
   {
      $table = DB_AIMGCSM . "." . TABLEPREFIX . "site_setting";

      // -----------------------------
      // BUILD SET CLAUSE DYNAMICALLY
      // -----------------------------
      $setParts = [];

      foreach ($data as $column => $value) {
         if ($value === null) {
            $setParts[] = "`$column` = NULL";
         } else {
            $value = $this->escape($value);
            $setParts[] = "`$column` = '$value'";
         }
      }

      // always update timestamp
      $setParts[] = "`updated_at` = NOW()";

      $setClause = implode(', ', $setParts);

      // -----------------------------
      // FINAL QUERY
      // -----------------------------
      $sql = "UPDATE $table 
            SET $setClause
            WHERE `update_id` = 'UPDATE_THE_AIMGCSM_SITE_SETTINGS'";

      return $this->global_CRUD_DB($sql);
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

      $resultArr = $this->global_CRUD_DB($sql);

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

      $resultArr = $this->global_Fetch_Single_DB($sql_count_jobs);

      return $resultArr;
   }

   public function manage_Queue_Jobs(array $data)
   {
      // -----------------------------
      // VALIDATE ACTION
      // -----------------------------
      $action = $data['action'] ?? null;

      if (!in_array($action, ['create', 'update'], true)) {
         return ['check' => 'failure', 'message' => 'Invalid action'];
      }

      // -----------------------------
      // COMMON TABLE
      // -----------------------------
      $table = DB_AIMGCSM . "." . TABLEPREFIX . "queue_jobs";

      // -----------------------------
      // CREATE JOB
      // -----------------------------
      if ($action === "create") {

         $jobType = $this->escape($data['job_type'] ?? '');

         if (empty($jobType)) {
            return ['check' => 'failure', 'message' => 'Job type required'];
         }

         $sql = "INSERT INTO $table 
                (`job_type`, `created_at`) 
                VALUES ('$jobType', NOW())";
      }

      // -----------------------------
      // UPDATE JOB
      // -----------------------------
      if ($action === "update") {

         $task_id  = (int) ($data['task_id'] ?? 0);
         $status   = $this->escape($data['status'] ?? '');
         $response = $this->escape($data['response'] ?? '');

         if (empty($status)) {
            return ['check' => 'failure', 'message' => 'Status required'];
         }

         $allowedStatuses = ['pending', 'running', 'failed', 'success'];

         if (!in_array($status, $allowedStatuses, true)) {
            return ['check' => 'failure', 'message' => 'Invalid status'];
         }

         $sql = "UPDATE $table 
                SET `response` = '$response', 
                    `status` = '$status', 
                    `updated_at` = NOW()
                WHERE `id` = $task_id";
      }

      // -----------------------------
      // EXECUTE QUERY
      // -----------------------------
      return $this->global_CRUD_DB($sql);
   }
}
