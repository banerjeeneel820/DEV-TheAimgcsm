<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class CmsModel extends BaseModel
{

   public function __construct(Database $database)
   {
      parent::__construct($database);
   }

   public function fetch_Gallery_Arr($params = [])
   {
      $queryParams = [];
      $where = [];

       // Pagination
      $limit = max(1, (int) ($params['limit'] ?? 10));
      $pageNo = max(1, (int) ($params['pageNo'] ?? 1));
      $offset = ($pageNo - 1) * $limit;

      // Record status
      $recordStatus = $params['record_status'] ?? 'active';

      $where[] = "g.record_status = ?";
      $queryParams[] = $recordStatus;

      // Optional search
      if (!empty($params['search_string'])) {
         $where[] = "(g.title LIKE ? OR g.description LIKE ?)";
         $searchString = '%' . $params['search_string'] . '%';

         $queryParams[] = $searchString;
         $queryParams[] = $searchString;
      }

      $whereSql = !empty($where)
         ? "WHERE " . implode(" AND ", $where)
         : "";

      $sqlBase = "
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery g
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category poc
               ON g.id = poc.post_id
               AND poc.post_type = 'gallery'
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc
               ON poc.category_id = pc.id
   
           $whereSql
       ";
      
       // Fetch paginated enquiry records
       $sqlFetch = "
         SELECT
            g.*,
            GROUP_CONCAT(DISTINCT pc.name) AS category_string
         $sqlBase
         GROUP BY g.id
         ORDER BY g.id DESC
         LIMIT $offset, $limit
      "; 
      
      $resultArr['data'] = $this->global_Fetch_All_DB(
         $sqlFetch,
         $queryParams
      ); 

       // Count total matching records
      $sqlRowCount = "
      SELECT g.id
      $sqlBase
      GROUP BY g.id
      ";

      // Debug
      // $this->debugQuery($sqlRowCount, $queryParams);

      $resultArr['row_count'] = $this->global_Rows_Count_DB(
         $sqlRowCount,
         $queryParams
      );

      $resultArr['pageNo'] = $pageNo;
      $resultArr['limit'] = $limit;

      // print"<pre>";
      // print_r($resultArr);
      // print"</pre>";exit;

      return $resultArr;
   }

   public function fetch_Gallery_Count($params = [])
   {
      $queryParams = [];
      $where = [];

      // Record status
      $recordStatus = $params['record_status'] ?? 'active';

      $where[] = "g.record_status = ?";
      $queryParams[] = $recordStatus;

      $whereSql = !empty($where)
         ? "WHERE " . implode(" AND ", $where)
         : "";

      $sqlBase = "
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery g
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category poc
               ON g.id = poc.post_id
               AND poc.post_type = 'gallery'
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc
               ON poc.category_id = pc.id
   
           $whereSql
       ";
      
      // Count total matching records
      $sqlRowCount = "
      SELECT g.id
      $sqlBase
      GROUP BY g.id
      ";

      // Debug
      // $this->debugQuery($sqlRowCount, $queryParams);

      return $this->global_Rows_Count_DB($sqlRowCount, $queryParams);
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

   public function fetch_Gallery_Item_Detail($params = [])
   {
      $mediaId = (int) ($params['media_id'] ?? 0);

      $sql = "
           SELECT
               g.*,
               GROUP_CONCAT(DISTINCT poc.category_id) AS category_string
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery g
   
           LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category poc
               ON g.id = poc.post_id
               AND poc.post_type = 'gallery'
   
           WHERE g.id = ?
   
           GROUP BY g.id
       ";

      $queryParams = [$mediaId];

      return $this->global_Fetch_Single_DB($sql, $queryParams);
   }

   public function manage_Global_Media($params = [])
   {
      $mediaId       = (int) ($params['media_id'] ?? 0);
      $title         = $params['title'] ?? '';
      $fileUploadType = $params['file_upload_type'] ?? '';
      $contentType   = $params['content_type'] ?? '';
      $content       = $params['content'] ?? '';
      $recordStatus  = $params['record_status'] ?? 'active';
      $featuredStatus = $params['featured_status'] ?? 'n';

      if ($mediaId > 0) {

         $sql = "
            UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery
            SET
                `title` = ?,
                `file_upload_type` = ?,
                `content_type` = ?,
                `content` = ?,
                `record_status` = ?,
                `featured_status` = ?,
                `updated_at` = NOW()
            WHERE `id` = ?
        ";

         $queryParams = [
            $title,
            $fileUploadType,
            $contentType,
            $content,
            $recordStatus,
            $featuredStatus,
            $mediaId
         ];
      } else {

         $sql = "
            INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "gallery
            (
                `title`,
                `file_upload_type`,
                `content_type`,
                `content`,
                `record_status`,
                `featured_status`,
                `created_at`
            )
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ";

         $queryParams = [
            $title,
            $fileUploadType,
            $contentType,
            $content,
            $recordStatus,
            $featuredStatus
         ];
      }

      return $this->global_CRUD_DB($sql, $queryParams);
   }

   public function edit_Post_Category($params = [])
   {
      $categoryArr = $params['category_id'] ?? [];
      $postType    = $params['post_type'] ?? '';
      $postId      = (int) ($params['post_id'] ?? 0);

      // Ensure category IDs are an array
      if (!is_array($categoryArr)) {
         $categoryArr = [];
      }

      // Remove existing category associations
      $sqlDelete = "
        DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category
        WHERE `post_type` = ?
        AND `post_id` = ?
    ";

      $deleteResult = $this->global_CRUD_DB(
         $sqlDelete,
         [$postType, $postId]
      );

      if (($deleteResult['check'] ?? '') !== 'success') {
         return $deleteResult;
      }

      // Insert updated category associations
      foreach ($categoryArr as $categoryId) {

         $categoryId = (int) $categoryId;

         $sqlInsert = "
            INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "post_category
            (
                `post_type`,
                `post_id`,
                `category_id`,
                `updated_at`
            )
            VALUES (?, ?, ?, NOW())
        ";

         $resultArr = $this->global_CRUD_DB(
            $sqlInsert,
            [$postType, $postId, $categoryId]
         );

         if (($resultArr['check'] ?? '') !== 'success') {
            return $resultArr;
         }
      }

      return ['check' => 'success'];
   }

   public function fetch_Parent_Category($params = [])
   {
      $queryParams = [];
      $where = [];

      // -----------------------------
      // DEFAULT FILTER
      // -----------------------------
      $recordStatus = $params['record_status'] ?? 'active';

      $where[] = "pc.record_status = ?";
      $queryParams[] = $recordStatus;

      // -----------------------------
      // OPTIONAL FILTERS (FUTURE READY)
      // -----------------------------
      if (!empty($params['search_string'])) {
         $where[] = "pc.name LIKE ?";
         $queryParams[] = '%' . $params['search_string'] . '%';
      }

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
               pc.id,
               pc.parent_category,
               pc.name,
               pc.record_status,
               pc.created_at
   
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category pc
   
           $whereSql
   
           ORDER BY pc.id DESC
       ";

      // Debug
      // $this->debugQuery($sql, $queryParams);

      return $this->global_Fetch_All_DB($sql, $queryParams);
   }

   public function manage_Parent_Category($params = [])
   {
      $queryParams = [];

      // -----------------------------
      // INPUT PARAMETERS
      // -----------------------------
      $rowId = (int) ($params['row_id'] ?? 0);
      $category = $params['category'] ?? '';
      $parentCategory = $params['parent_category'] ?? '';
      $recordStatus = $params['record_status'] ?? 'active';

      // -----------------------------
      // UPDATE EXISTING CATEGORY
      // -----------------------------
      if ($rowId > 0) {

         $sql = "
               UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category
               SET
                  `name` = ?,
                  `parent_category` = ?,
                  `record_status` = ?,
                  `updated_at` = NOW()
               WHERE `id` = ?
         ";

         $queryParams = [
            $category,
            $parentCategory,
            $recordStatus,
            $rowId
         ];
      } else {

         // -----------------------------
         // INSERT NEW CATEGORY
         // -----------------------------
         $sql = "
               INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "parent_category
               SET
                  `name` = ?,
                  `parent_category` = ?,
                  `record_status` = ?,
                  `created_at` = NOW()
         ";

         $queryParams = [
            $category,
            $parentCategory,
            $recordStatus
         ];
      }

      // Debug
      // $this->debugQuery($sql, $queryParams);

      return $this->global_CRUD_DB($sql, $queryParams);
   }

   public function fetch_Slider_Arr($params = [])
   {
      $queryParams = [];
      $where = [];

      // -----------------------------
      // DEFAULT FILTER
      // -----------------------------
      $recordStatus = $params['record_status'] ?? 'active';

      $where[] = "s.record_status = ?";
      $queryParams[] = $recordStatus;

      // -----------------------------
      // OPTIONAL FILTERS
      // -----------------------------
      if (!empty($params['slider_type'])) {
         $where[] = "s.slider_type = ?";
         $queryParams[] = $params['slider_type'];
      }

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
               s.*
   
           FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "home_sliders s
   
           $whereSql
   
           ORDER BY s.id ASC
       ";

      // Debug
      // $this->debugQuery($sql, $queryParams);

      return $this->global_Fetch_All_DB($sql, $queryParams);
   }

   public function fetch_Slider_Detail($params = [])
   {
      $queryParams = [];

      // -----------------------------
      // INPUT PARAMETERS
      // -----------------------------
      $sliderId = (int) ($params['slider_id'] ?? 0);

      // -----------------------------
      // MAIN QUERY
      // -----------------------------
      $sql = "
        SELECT
            s.*

        FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "home_sliders s

        WHERE s.id = ?
    ";

      $queryParams[] = $sliderId;

      // Debug
      // $this->debugQuery($sql, $queryParams);

      return $this->global_Fetch_Single_DB($sql, $queryParams);
   }

   public function manage_Home_Slider($params = [])
   {
      $sliderId      = (int) ($params['slider_id'] ?? 0);
      $sliderType    = $params['slider_type'] ?? '';
      $bannerTitle   = $params['banner_title'] ?? '';
      $bannerText    = $params['banner_text'] ?? '';
      $bannerLink    = $params['banner_link'] ?? '';
      $fileUploadType = $params['file_upload_type'] ?? '';
      $bannerImage   = $params['banner_image'] ?? '';
      $recordStatus  = $params['record_status'] ?? 'active';

      if ($sliderId > 0) {

         $sql = "
            UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "home_sliders
            SET
                `slider_type` = ?,
                `banner_title` = ?,
                `banner_text` = ?,
                `banner_link` = ?,
                `file_upload_type` = ?,
                `banner_image` = ?,
                `record_status` = ?,
                `updated_at` = NOW()
            WHERE `id` = ?
        ";

         $queryParams = [
            $sliderType,
            $bannerTitle,
            $bannerText,
            $bannerLink,
            $fileUploadType,
            $bannerImage,
            $recordStatus,
            $sliderId
         ];
      } else {

         $sql = "
            INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "home_sliders
            (
                `slider_type`,
                `banner_title`,
                `banner_text`,
                `banner_link`,
                `file_upload_type`,
                `banner_image`,
                `record_status`,
                `created_at`
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ";

         $queryParams = [
            $sliderType,
            $bannerTitle,
            $bannerText,
            $bannerLink,
            $fileUploadType,
            $bannerImage,
            $recordStatus
         ];
      }

      return $this->global_CRUD_DB($sql, $queryParams);
   }

   public function fetch_Global_Cities($params = [])
   {
      $queryParams = [];
      $where = [];

      // Pagination
      $limit = max(1, (int) ($params['limit'] ?? 10));
      $pageNo = max(1, (int) ($params['pageNo'] ?? 1));
      $offset = ($pageNo - 1) * $limit;

      // -----------------------------
      // DEFAULT FILTER
      // -----------------------------
      $recordStatus = $params['record_status'] ?? 'active';

      $where[] = "c.record_status = ?";
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
      $sqlBase = "
        FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "cities c

        $whereSql
      ";

      // Fetch paginated enquiry records
      $sqlFetch = "
        SELECT
        c.*
        $sqlBase
        ORDER BY c.id DESC
        LIMIT $offset, $limit
      ";

      $resultArr['data'] = $this->global_Fetch_All_DB(
         $sqlFetch,
         $queryParams
      );

      // Count total matching records
      $sqlRowCount = "
      SELECT c.id
      $sqlBase
      ";

      // Debug
      // $this->debugQuery($sqlFetch, $queryParams);

      $resultArr['row_count'] = $this->global_Rows_Count_DB(
         $sqlRowCount,
         $queryParams
      );

      $resultArr['pageNo'] = $pageNo;
      $resultArr['limit'] = $limit;

      return $resultArr;
   }

   public function manage_Global_City($params = [])
   {
      // -----------------------------
      // INPUT PARAMETERS
      // -----------------------------
      $rowId = (int) ($params['row_id'] ?? 0);
      $name = $params['name'] ?? '';
      $recordStatus = $params['record_status'] ?? 'active';

      // -----------------------------
      // UPDATE EXISTING CITY
      // -----------------------------
      if ($rowId > 0) {

         $sql = "
               UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "cities
               SET
                   `name` = ?,
                   `record_status` = ?,
                   `updated_at` = NOW()
               WHERE `id` = ?
           ";

         $queryParams = [
            $name,
            $recordStatus,
            $rowId
         ];
      } else {

         // -----------------------------
         // INSERT NEW CITY
         // -----------------------------
         $sql = "
               INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "cities
               SET
                   `name` = ?,
                   `record_status` = ?,
                   `created_at` = NOW()
           ";

         $queryParams = [
            $name,
            $recordStatus
         ];
      }

      // Debug
      // $this->debugQuery($sql, $queryParams);

      return $this->global_CRUD_DB($sql, $queryParams);
   }

   public function fetch_Global_Enquiry($params = [])
   {
      $queryParams = [];
      $where = [];

      // Pagination
      $limit = max(1, (int) ($params['limit'] ?? 10));
      $pageNo = max(1, (int) ($params['pageNo'] ?? 1));
      $offset = ($pageNo - 1) * $limit;

      // Record status
      $recordStatus = $params['record_status'] ?? 'active';

      $where[] = "enq.record_status = ?";
      $queryParams[] = $recordStatus;

      // Optional enquiry type
      if (!empty($params['enquiry_type'])) {
         $where[] = "enq.enquiry_type = ?";
         $queryParams[] = $params['enquiry_type'];
      }

      // Optional course
      $courseId = (int) ($params['course_id'] ?? 0);

      if ($courseId > 0) {
         $where[] = "crs.id = ?";
         $queryParams[] = $courseId;
      }

      $whereSql = "WHERE " . implode(" AND ", $where);

      $sqlBase = "
        FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "enquiry enq

        LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs
            ON enq.subject = crs.id

        $whereSql
    ";

      // Fetch paginated enquiry records
      $sqlFetch = "
        SELECT
            enq.id,
            enq.user_name,
            enq.user_email,
            enq.user_phone,
            enq.user_city,
            enq.enquiry_type,
            enq.subject,
            enq.user_message,
            enq.record_status,
            enq.created_at,
            crs.course_title
        $sqlBase
        ORDER BY enq.id DESC
        LIMIT $offset, $limit
      ";

      $resultArr['data'] = $this->global_Fetch_All_DB(
         $sqlFetch,
         $queryParams
      );

      // Count total matching records
      $sqlRowCount = "
      SELECT enq.id
      $sqlBase
      ";

      // Debug
      // $this->debugQuery($sqlRowCount, $queryParams);

      $resultArr['row_count'] = $this->global_Rows_Count_DB(
         $sqlRowCount,
         $queryParams
      );

      $resultArr['pageNo'] = $pageNo;
      $resultArr['limit'] = $limit;

      return $resultArr;
   }

}   