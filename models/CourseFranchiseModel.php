<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class CourseFranchiseModel extends BaseModel
{

   public function __construct(Database $database)
   {
      parent::__construct($database);
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

   public function fetch_Global_Single_Course($course_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "course WHERE id = '$course_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function fetch_Last_Franchise_Detail()
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise ORDER BY fran_id DESC LIMIT 1";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function check_Slug_Availibility($type, $field, $slug)
   {
      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $type . " WHERE `$field`='$slug'";
      //echo $sql;exit();
      $retrunArr = $this->global_Fetch_Single_DB($sql);

      return $retrunArr;
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

}   