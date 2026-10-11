<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class StudentReceiptModel extends BaseModel
{
   public function __construct(Database $database)
   {
      parent::__construct($database);
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

   public function fetch_Last_Receipt_Detail()
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "student_receipts ORDER BY id DESC LIMIT 1";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

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

   public function fetch_Tmp_Single_Student($id)
   {

      $sql = "SELECT tmp_stu.*,frn.center_name,frn.fran_email,frn.fran_phone,frn.fran_address,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "temp_students tmp_stu LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON tmp_stu.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON tmp_stu.course_id = crs.id WHERE tmp_stu.id = '$id'";

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

   public function fetch_Email_Template_Detail($email_code)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template et WHERE et.code='$email_code'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
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
}   