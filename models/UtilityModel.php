<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class UtilityModel extends BaseModel
{
   public function __construct(Database $database)
   {
      parent::__construct($database);
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

   public function fetch_Global_Multiple_Data($type, $rowIds)
   {
      $ids = implode(',', array_map('intval', $rowIds));

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "$type 
            WHERE id IN ($ids)";

      return $this->global_Fetch_All_DB($sql);
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

   public function fetch_Global_Site_Setting_Detail()
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "site_setting WHERE `update_id` = 'UPDATE_THE_AIMGCSM_SITE_SETTINGS'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

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

   public function check_Slug_Availibility($type, $field, $slug)
   {
      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $type . " WHERE `$field`='$slug'";
      //echo $sql;exit();
      $retrunArr = $this->global_Fetch_Single_DB($sql);

      return $retrunArr;
   }
}   