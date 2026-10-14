<?php
namespace App\Models\Admin;

use App\Core\Database;
use App\Core\BaseModel;

defined('ROOTPATH') or exit('No direct script access allowed');

class DashboardModel extends BaseModel
{
   public function __construct(Database $database)
   {
      parent::__construct($database);
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
}   