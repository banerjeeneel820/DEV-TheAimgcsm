<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class UtilityController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function update_global_status_status($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $idData       = $post('row_id');
        $type         = $post('type');
        $recordStatus = $post('record_status');

        // -----------------------------
        // ROLE MAP
        // -----------------------------
        $roleMap = [
            'franchise'        => 'update_franchise',
            'course'           => 'update_course',
            'gallery'          => 'update_gallery',
            'home_sliders'     => 'manage_home_slider',
            'student'          => 'update_student',
            'temp_student'     => 'update_student',
            'student_receipts' => 'update_receipt',
            'exam'             => 'update_exam',
            'parent_category'  => 'update_category',
            'cities'           => 'manage_city_db',
            'email_template'   => 'update_template',
            'news'             => 'update_news',
            'enquiry'          => 'delete_enquiry',
        ];

        if (!isset($roleMap[$type])) {
            return ['check' => 'failure', 'message' => 'Invalid type!'];
        }

        // -----------------------------
        // PERMISSION
        // -----------------------------
        if (!$this->checkUserRolePermission($roleMap[$type], "hard")) {
            return ['check' => 'failure', 'message' => "You don't have permission!"];
        }

        // -----------------------------
        // PREPARE IDS
        // -----------------------------
        if (empty($idData)) {
            return ['check' => 'failure', 'message' => "No data selected!"];
        }

        $rowIds = array_map('trim', explode(',', $idData));
        $rowIds = array_filter(array_map('intval', (array)$rowIds));

        if (empty($rowIds)) {
            return ['check' => 'failure', 'message' => "Invalid IDs!"];
        }

        // -----------------------------
        // CALL MODEL (SINGLE CALL)
        // -----------------------------
        $response = $this->interface
            ->update_Global_Record_Status($type, $rowIds, $recordStatus);

        if ($response['responseArr']['check'] !== 'success') {
            return ['check' => 'failure', 'message' => 'Update failed!'];
        }

        // -----------------------------
        // CACHE PURGE
        // -----------------------------
        $this->purgeSiteCache($type);

        return ['check' => 'success', 'message' => 'Status updated successfully!'];
    }

    public function update_global_featured_status($data)
    {
        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $idData          = $post('row_id');
        $type            = $post('type');
        $featured_status = $post('featured_status');

        // Permission mapping
        $roleMap = [
            'franchise' => 'update_franchise',
            'course'    => 'update_course',
            'gallery'   => 'update_gallery',
            'news'      => 'update_news',
        ];

        $user_role_slug = $roleMap[$type];

        // Check permission
        $checkActionPermission = $this->lib
            ->checkUserRolePermission($user_role_slug, "hard");

        if (!$checkActionPermission) {
            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // PREPARE IDS
        // -----------------------------
        if (empty($idData)) {
            return ['check' => 'failure', 'message' => "No data selected!"];
        }

        $rowIds = array_map('trim', explode(',', $idData));
        $rowIds = array_filter(array_map('intval', (array)$rowIds));

        if (empty($rowIds)) {
            return ['check' => 'failure', 'message' => "Invalid IDs!"];
        }

        if (!isset($roleMap[$type])) {
            return ['check' => 'failure', 'message' => 'Invalid type provided'];
        }

        // Call updated query method
        $result = $this->interface
            ->update_Global_Featured_Status($type, $rowIds, $featured_status);

        if ($result["responseArr"]["check"] === "success") {
            return [
                "check" => "success",
                "message" => "Records updated successfully!"
            ];
        }

        return [
            "check" => "failure",
            "message" => "Something went wrong!"
        ];
    }

    public function update_global_verified_status($data)
    {
        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $idData          = $post('row_id');
        $type            = $post('type');
        $verified_status = $post('verified_status');

        // Permission mapping
        $roleMap = [
            'student' => 'update_student',
            'temp_student'    => 'update_student',
            'student_receipts'   => 'update_receipt',
        ];

        $user_role_slug = $roleMap[$type];

        // Check permission
        $checkActionPermission = $this->lib
            ->checkUserRolePermission($user_role_slug, "hard");

        if (!$checkActionPermission) {
            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // PREPARE IDS
        // -----------------------------
        if (empty($idData)) {
            return ['check' => 'failure', 'message' => "No data selected!"];
        }

        $rowIds = array_map('trim', explode(',', $idData));
        $rowIds = array_filter(array_map('intval', (array)$rowIds));

        if (empty($rowIds)) {
            return ['check' => 'failure', 'message' => "Invalid IDs!"];
        }

        if (!isset($roleMap[$type])) {
            return ['check' => 'failure', 'message' => 'Invalid type provided'];
        }

        // Call updated query method
        $result = $this->interface
            ->update_Global_Verified_Status($type, $rowIds, $verified_status);

        if ($result["responseArr"]["check"] === "success") {
            return [
                "check" => "success",
                "message" => "Records updated successfully!"
            ];
        }

        return [
            "check" => "failure",
            "message" => "Something went wrong!"
        ];
    }

    public function delete_global_data($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $idData = $post('row_id');   // "1,2,3" or "5"
        $type   = $post('type');

        // -----------------------------
        // ROLE MAP
        // -----------------------------
        $roleMap = [
            'franchise'        => 'delete_franchise',
            'course'           => 'delete_course',
            'home_sliders'     => 'manage_home_slider',
            'gallery'          => 'delete_gallery',
            'student'          => 'delete_student',
            'temp_student'     => 'delete_student',
            'student_receipts' => 'delete_receipt',
            'parent_category'  => 'delete_category',
            'cities'           => 'manage_city_db',
            'email_template'   => 'delete_template',
            'news'             => 'delete_news',
            'enquiry'          => 'delete_enquiry',
        ];

        // -----------------------------
        // VALIDATE TYPE
        // -----------------------------
        if (!isset($roleMap[$type])) {
            return ['check' => 'failure', 'message' => 'Invalid type!'];
        }

        // -----------------------------
        // PERMISSION CHECK
        // -----------------------------
        if (!$this->checkUserRolePermission($roleMap[$type], "hard")) {
            return ['check' => 'failure', 'message' => "You don't have permission!"];
        }

        // -----------------------------
        // PREPARE IDS
        // -----------------------------
        if (empty($idData)) {
            return ['check' => 'failure', 'message' => "No data selected!"];
        }

        $rowIds = array_map('trim', explode(',', $idData));
        $rowIds = array_filter(array_map('intval', $rowIds));

        if (empty($rowIds)) {
            return ['check' => 'failure', 'message' => "Invalid IDs!"];
        }

        // -----------------------------
        // FETCH DATA BEFORE DELETE (for file removal)
        // -----------------------------
        $allRecords = $this->interface
            ->fetch_Global_Multiple_Data($type, $rowIds);

        // -----------------------------
        // BULK DELETE
        // -----------------------------
        $response = $this->interface
            ->delete_Global_Data([
                'type'   => $type,
                'rowIds' => $rowIds
            ]);

        if ($response['responseArr']['check'] !== 'success') {
            return ['check' => 'failure', 'message' => 'Delete failed!'];
        }

        // -----------------------------
        // REMOVE FILES (LOOP ONLY HERE)
        // -----------------------------
        if (!empty($allRecords)) {
            foreach ($allRecords as $record) {
                $this->lib->remove_File_From_Server($type, $record);
            }
        }

        return ['check' => 'success', 'message' => 'Data deleted successfully!'];
    }

    public function clean_runtime_content($data)
    {
        $dir = USER_UPLOAD_DIR . "runtime_upload/";

        // -----------------------------
        // PERMISSION CHECK
        // -----------------------------
        $hasPermission = $this->lib
            ->checkUserRolePermission("update_site_setting", "hard");

        // allow franchise override
        if (!$hasPermission && ($_SESSION['user_type'] ?? '') === "franchise") {
            $hasPermission = true;
        }

        if (!$hasPermission) {
            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // VALIDATE DIRECTORY
        // -----------------------------
        if (!is_dir($dir)) {
            return [
                'check' => 'failure',
                'message' => 'Runtime directory not found!'
            ];
        }

        // -----------------------------
        // DELETE FILES
        // -----------------------------
        foreach (glob($dir . '*') as $file) {
            if (is_file($file)) {
                @unlink($file); // suppress warning if file already removed
            }
        }

        return [
            'check' => 'success',
            'message' => 'All runtime files cleaned successfully!'
        ];
    }

    public function clear_site_cache($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $cacheDir = APP_CACHE_DIR;
        $clearType = $post('clearType');

        // -----------------------------
        // CLEAR CURRENT FILE
        // -----------------------------
        if ($clearType === "current_page") {

            $fileName = basename($post('currentCacheFile'));
            $filePath = $cacheDir . $fileName;

            if (is_file($filePath)) {
                unlink($filePath);
            }

            return [
                'check' => 'success',
                'message' => 'Cache cleared successfully!'
            ];
        }

        // -----------------------------
        // PERMISSION CHECK
        // -----------------------------
        if (!$this->checkUserRolePermission('update_site_setting', "hard")) {
            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // CLEAR ALL CACHE
        // -----------------------------
        foreach (glob($cacheDir . '*') as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        return [
            'check' => 'success',
            'message' => 'Cache memory is successfully cleaned!'
        ];
    }

    public function remove_file_from_server($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $filePath = $post('file_upload_dir');

        // -----------------------------
        // VALIDATION
        // -----------------------------
        if (empty($filePath)) {
            return ['check' => 'failure', 'message' => 'Invalid file path'];
        }

        // Normalize path
        $realPath = realpath($filePath);

        // -----------------------------
        // SECURITY CHECK (VERY IMPORTANT)
        // -----------------------------
        $allowedDirs = [
            realpath(USER_UPLOAD_DIR),
            realpath(APP_CACHE_DIR)
        ];

        $isAllowed = false;

        foreach ($allowedDirs as $dir) {
            if ($dir && strpos($realPath, $dir) === 0) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            return ['check' => 'failure', 'message' => 'Unauthorized file path'];
        }

        // -----------------------------
        // DELETE FILE
        // -----------------------------
        if ($realPath && is_file($realPath)) {
            unlink($realPath);

            return ['check' => 'success', 'message' => 'File removed successfully'];
        }

        return ['check' => 'failure', 'message' => 'File not found'];
    }

    public function create_site_backup_queue_job($data)
    {
        $cookieName = "backupCount";

        // -----------------------------
        // PERMISSION CHECK
        // -----------------------------
        if (!$this->checkUserRolePermission("manage_site_backup")) {
            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // CHECK EXISTING TASKS
        // -----------------------------
        $hasPending = $this->interface->check_Task_Status();
        $hasRunning = $this->interface->check_Task_Status("running");

        if (!empty($hasPending) || !empty($hasRunning)) {
            return [
                'check' => 'failure',
                'message' => "There is already a pending task on the queue!"
            ];
        }

        // -----------------------------
        // BACKUP LIMIT LOGIC
        // -----------------------------
        $isDeveloper = ($_SESSION['user_type'] ?? '') === "developer";

        $backupCount = isset($_COOKIE[$cookieName])
            ? (int) $_COOKIE[$cookieName]
            : 0;

        if (!$isDeveloper && $backupCount >= 2) {
            return [
                'check' => 'failure',
                'message' => "Backup limit exhausted!"
            ];
        }

        // -----------------------------
        // CREATE QUEUE JOB
        // -----------------------------
        $payload = [
            'action'   => "create",
            'job_type' => "site_backup_creation"
        ];

        $response = $this->interface->manage_Queue_Jobs($payload);

        if ($response['check'] !== "success") {
            return [
                'check' => 'failure',
                'message' => "Something went wrong, please try later!"
            ];
        }

        // -----------------------------
        // UPDATE COOKIE (NON-DEVELOPER)
        // -----------------------------
        if (!$isDeveloper) {
            $newCount = $backupCount + 1;
            setcookie($cookieName, $newCount, time() + (86400 * 1), "/");
        }

        return [
            'check' => 'success',
            'message' => "Backup job is successfully queued!"
        ];
    }

    public function update_site_setting($data)
    {
        $formDataArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // PERMISSION CHECK
        // -----------------------------
        if (!$this->checkUserRolePermission("update_site_setting", "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        $dir = 'others';

        // -----------------------------
        // BASIC FIELDS
        // -----------------------------
        $fields = [
            'title', 'contact_email', 'phone', 'career_email', 'business_email',
            'facebook_link', 'youtube_link', 'twitter_link', 'skype_link',
            'instagram_link', 'telegram_link', 'linkdin_link', 'copyright',
            'address', 'feedback_status', 'maintenance_status',
            'site_caching', 'description'
        ];

        foreach ($fields as $field) {
            $formDataArr[$field] = $post($field);
        }

        // -----------------------------
        // FILE UPLOADS (USING HELPER)
        // -----------------------------

        // signature
        $formDataArr['signature'] = $this->lib->handleFileUpload([
            'input'   => 'signature',
            'hidden'  => $post('hidden_signature'),
            'default' => 'signature.jpg',
            'dir'     => $dir,
            'isUpdate' => true
        ]);

        // logo
        $formDataArr['logo'] = $this->lib->handleFileUpload([
            'input'   => 'logo',
            'hidden'  => $post('hidden_logo'),
            'default' => 'company.png',
            'dir'     => $dir,
            'isUpdate' => true
        ]);

        // header logo
        $formDataArr['header_logo'] = $this->lib->handleFileUpload([
            'input'   => 'header_logo',
            'hidden'  => $post('hidden_header_logo'),
            'default' => null,
            'dir'     => $dir,
            'isUpdate' => true
        ]);

        // sticky logo
        $formDataArr['sticky_logo'] = $this->lib->handleFileUpload([
            'input'   => 'sticky_logo',
            'hidden'  => $post('hidden_sticky_logo'),
            'default' => null,
            'dir'     => $dir,
            'isUpdate' => true
        ]);

        // footer logo
        $formDataArr['footer_logo'] = $this->lib->handleFileUpload([
            'input'   => 'footer_logo',
            'hidden'  => $post('hidden_footer_logo'),
            'default' => null,
            'dir'     => $dir,
            'isUpdate' => true
        ]);

        // favicon
        $formDataArr['favicon'] = $this->lib->handleFileUpload([
            'input'   => 'favicon',
            'hidden'  => $post('hidden_favicon'),
            'default' => null,
            'dir'     => $dir,
            'isUpdate' => true
        ]);

        // -----------------------------
        // DB OPERATION
        // -----------------------------
        $returnArr = $this->interface
            ->update_Global_Site_Setting($formDataArr);

        // -----------------------------
        // SESSION UPDATE (HERE)
        // -----------------------------
        if ($returnArr['check'] === 'success' && !empty($formDataArr['logo'])) {
            $_SESSION['user_profile_pic'] = USER_UPLOAD_URL . 'others/' . $formDataArr['logo'];
        }

        return $returnArr;
    }

}
