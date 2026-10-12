<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class StudentModel extends BaseModel
{
   public function __construct(Database $database)
   {
      parent::__construct($database);
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
                   ON stu.tmp_stu_record_id = tmp_stu.id
   
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

   public function fetch_Last_Student_Detail()
   {

      $sql_current_student = "SELECT stu.stu_id FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students stu ORDER BY id DESC LIMIT 1";
      //echo $sql_current_student;exit;
      $lst_stu_id = $this->global_Fetch_Single_DB($sql_current_student)->stu_id;

      return array('lst_stu_id' => $lst_stu_id);
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

   public function fetch_Single_Parent_Category($parent_category)
   {
      $sql = "SELECT pc.id, pc.parent_category, pc.name 
               FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc 
               WHERE pc.parent_category = ?
               ORDER BY pc.id DESC";

      $resultArr = $this->global_Fetch_All_DB($sql, [$parent_category]);

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

   public function delete_Student_By_Id($stu_id)
   {
      //Deleting student result  
      $sql_delete_receipt = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students WHERE stu_id = '$stu_id'";
      //echo $sql_delete_receipt;exit;
      $this->global_CRUD_DB($sql_delete_receipt);
   }

   public function update_Tmp_Student_Conversion_Status($id, $conversion_status)
   {

      $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu SET `conversion_status`='$conversion_status',`updated_at` = now() WHERE tmp_stu.id = '$id'";
      //echo $sql;exit();
      $resultArr = $this->global_CRUD_DB($sql);

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

   public function fetch_Tmp_Single_Student($id)
   {

      $sql = "SELECT tmp_stu.*,frn.center_name,frn.fran_email,frn.fran_phone,frn.fran_address,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON tmp_stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON tmp_stu.course_id = crs.id WHERE tmp_stu.id = '$id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

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

}   