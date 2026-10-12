<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class TemplateModel extends BaseModel
{
   public function __construct(Database $database)
   {
      parent::__construct($database);
   }

   public function fetch_Email_Templates($params = [])
   {
      $queryParams = [];
      $where = [];

      // -----------------------------
      // DEFAULT FILTER
      // -----------------------------
      $recordStatus = $params['record_status'] ?? 'active';

      $where[] = "et.record_status = ?";
      $queryParams[] = $recordStatus;

      // -----------------------------
      // WHERE CLAUSE
      // -----------------------------
      $whereSql = !empty($where)
         ? "WHERE " . implode(" AND ", $where)
         : "";

      // -----------------------------
      // MAIN QUERY
      // -----------------------------
      $sql = "
           SELECT
               et.*
   
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template et
   
           $whereSql
   
           ORDER BY et.id DESC
       ";

      // Debug
      // $this->debugQuery($sql, $queryParams);

      return $this->global_Fetch_All_DB($sql, $queryParams);
   }

   public function fetch_Global_Email_Template_Detail($template_id = null)
   {
      // -----------------------------
      // INPUT PARAMETERS
      // -----------------------------
      $templateId = (int) ($template_id ?? 0);

      // -----------------------------
      // MAIN QUERY
      // -----------------------------
      $sql = "
           SELECT
               et.*
   
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template et
   
           WHERE et.id = ?
       ";

      $queryParams = [
         $templateId
      ];

      // Debug
      // $this->debugQuery($sql, $queryParams);

      return $this->global_Fetch_Single_DB($sql, $queryParams);
   }

   public function check_Slug_Availibility($type, $field, $slug)
   {
      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . $type . " WHERE `$field`='$slug'";
      //echo $sql;exit();
      $retrunArr = $this->global_Fetch_Single_DB($sql);

      return $retrunArr;
   }

   public function manage_Global_Email_Template($params = [])
   {
      // -----------------------------
      // INPUT PARAMETERS
      // -----------------------------
      $templateId = (int) ($params['template_id'] ?? 0);

      $subject   = $params['subject'] ?? '';
      $code      = $params['code'] ?? '';
      $emailFor  = $params['email_for'] ?? '';
      $variables = $params['variables'] ?? '';
      $fromEmail = $params['from_email'] ?? '';
      $fromName  = $params['from_name'] ?? '';
      $ccEmail   = $params['cc_email'] ?? '';
      $template  = $params['template'] ?? '';

      // -----------------------------
      // UPDATE EXISTING TEMPLATE
      // -----------------------------
      if ($templateId > 0) {

         $set = [
            "`subject` = ?",
            "`code` = ?",
            "`email_for` = ?",
            "`variables` = ?",
            "`from_email` = ?",
            "`from_name` = ?",
            "`cc_email` = ?",
            "`template` = ?",
            "`updated_at` = NOW()"
         ];

         $queryParams = [
            $subject,
            $code,
            $emailFor,
            $variables,
            $fromEmail,
            $fromName,
            $ccEmail,
            $template
         ];

         // -----------------------------
         // OPTIONAL RECORD STATUS
         // -----------------------------
         if (array_key_exists('record_status', $params)) {
            $set[] = "`record_status` = ?";
            $queryParams[] = $params['record_status'];
         }

         $sql = "
               UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template
               SET " . implode(", ", $set) . "
               WHERE `id` = ?
           ";

         $queryParams[] = $templateId;
      } else {

         // -----------------------------
         // INSERT NEW TEMPLATE
         // -----------------------------
         $columns = [
            "`subject`",
            "`code`",
            "`email_for`",
            "`variables`",
            "`from_email`",
            "`from_name`",
            "`cc_email`",
            "`template`",
            "`created_at`"
         ];

         $values = [
            "?",
            "?",
            "?",
            "?",
            "?",
            "?",
            "?",
            "?",
            "NOW()"
         ];

         $queryParams = [
            $subject,
            $code,
            $emailFor,
            $variables,
            $fromEmail,
            $fromName,
            $ccEmail,
            $template
         ];

         // -----------------------------
         // OPTIONAL RECORD STATUS
         // -----------------------------
         if (array_key_exists('record_status', $params)) {
            $columns[] = "`record_status`";
            $values[] = "?";
            $queryParams[] = $params['record_status'];
         }

         $sql = "
               INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "email_template
               (" . implode(", ", $columns) . ")
               VALUES (" . implode(", ", $values) . ")
           ";
      }

      // Debug
      // $this->debugQuery($sql, $queryParams);

      return $this->global_CRUD_DB($sql, $queryParams);
   }
}   