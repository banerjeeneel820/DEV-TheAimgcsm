<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class UtilityController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function update_global_status_record($data)
    {
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

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
        if (!$this->GlobalLibraryHandlerObj->checkUserRolePermission($roleMap[$type], "hard")) {
            return ['check' => 'failure', 'message' => "You don't have permission!"];
        }

        // -----------------------------
        // PREPARE IDS
        // -----------------------------
        if (empty($idData)) {
            return ['check' => 'failure', 'message' => "No data selected!"];
        }

        $rowIds = strpos($idData, ',') !== false
            ? explode(',', $idData)
            : [$idData];

        $rowIds = array_filter(array_map('intval', $rowIds));

        if (empty($rowIds)) {
            return ['check' => 'failure', 'message' => "Invalid IDs!"];
        }

        // -----------------------------
        // CALL MODEL (SINGLE CALL)
        // -----------------------------
        $response = $this->GlobalInterfaceControllerObj
            ->update_Global_Record_Status($type, $rowIds, $recordStatus);

        if ($response['responseArr']['check'] !== 'success') {
            return ['check' => 'failure', 'message' => 'Update failed!'];
        }

        // -----------------------------
        // CACHE PURGE
        // -----------------------------
        $this->GlobalLibraryHandlerObj->purgeSiteCache($type);

        return ['check' => 'success', 'message' => 'Status updated successfully!'];
    }

    public function update_global_featured_record($data)
    {
        //Declaring necessary variables
        $formDataArr = array();
        $returnArr = array();

        $row_id = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['row_id']));
        $type = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['type']));
        $featured_status = mysqli_real_escape_string(DB::$WRITELINK, trim($_POST['featured_status']));

        switch ($type) {

            case 'franchise':
                $user_role_slug = "update_franchise";
                break;

            case 'course':
                $user_role_slug = "update_course";
                break;

            case 'gallery':
                $user_role_slug = "update_gallery";
                break;

            case 'news':
                $user_role_slug = "update_news";
                break;
        }

        //check action permission        
        $checkActionPermission = $this->GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

        if ($checkActionPermission) {
            //Call update global status modify method
            $returnArr = $this->GlobalInterfaceControllerObj->update_Global_Featured_Status($type, $row_id, $featured_status);
            if ($returnArr["responseArr"]["check"] == "success") {
                $returnArr = array("check" => "success", "message" => "Query has been successfully executed!");
            }
        } else {
            $returnArr = array('check' => 'failure', 'message' => "You don't have the permission to perform this action!");
        }
    }
}
