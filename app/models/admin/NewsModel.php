<?php
namespace App\Models\Admin;

use App\Core\Database;
use App\Core\BaseModel;

defined('ROOTPATH') or exit('No direct script access allowed');

class NewsModel extends BaseModel
{
   public function __construct(Database $database)
   {
      parent::__construct($database);
   }

   public function fetch_Global_News($params = [])
   {
      $queryParams = [];
      $where = [];

      // -----------------------------
      // DEFAULT FILTER
      // -----------------------------
      $recordStatus = $params['record_status'] ?? 'active';

      $where[] = "nws.record_status = ?";
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
               nws.*
   
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "news nws
   
           $whereSql
   
           ORDER BY nws.id DESC
       ";

      // Debug
      // $this->debugQuery($sql, $queryParams);

      return $this->global_Fetch_All_DB($sql, $queryParams);
   }

   public function fetch_Global_News_Detail($news_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "news WHERE `id` = '$news_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

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
}   