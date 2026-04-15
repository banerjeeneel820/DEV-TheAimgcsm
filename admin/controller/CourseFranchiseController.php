<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class CourseFranchiseController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function manage_franchise($data)
    {
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

        $formDataArr = [];
        $dir = 'franchise';

        // -----------------------------
        // BASIC DATA
        // -----------------------------
        $formDataArr['fran_row_id'] = $post('fran_row_id');

        $isUpdate = ($formDataArr['fran_row_id'] !== 'null');

        $role = $isUpdate ? 'update_franchise' : 'create_franchise';

        if (!$this->GlobalLibraryHandlerObj->checkUserRolePermission($role, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have permission!"];
        }

        // -----------------------------
        // CREATE LOGIC
        // -----------------------------
        if (!$isUpdate) {
            $formDataArr['fran_id'] = $this->GlobalLibraryHandlerObj->create_Frnachise_ID();
        }

        // -----------------------------
        // PASSWORD
        // -----------------------------
        $fran_pass = $post('fran_pass');

        if (!empty($fran_pass)) {
            $formDataArr['fran_pass'] = md5($fran_pass);
            $formDataArr['fran_og_pass'] = $fran_pass;
        } else {
            $formDataArr['fran_pass'] = $post('fran_hidden_password');
            $formDataArr['fran_og_pass'] = $post('fran_hidden_og_password');
        }

        // -----------------------------
        // CORE FIELDS
        // -----------------------------
        $formDataArr['center_name'] = $post('center_name');

        $formDataArr['seo_url_structure'] = $this->GlobalLibraryHandlerObj
            ->seoUrlStructure($formDataArr['center_name'], 'seo');

        // -----------------------------
        // SLUG VALIDATION
        // -----------------------------
        $slugData = $this->GlobalLibraryHandlerObj
            ->checkSlugAvailibility('franchise', 'seo_url_structure', $formDataArr['seo_url_structure']);

        if (!empty($slugData->id) && (!$isUpdate || $slugData->id != $formDataArr['fran_row_id'])) {
            return ['check' => 'failure', 'message' => 'This title is already taken; Please try another.'];
        }

        // -----------------------------
        // OTHER FIELDS
        // -----------------------------
        $fields = [
            'owner_name',
            'fran_phone',
            'fran_email',
            'fran_address',
            'owned_status',
            'record_status',
            'featured_status',
            'fran_description',
            'image_upload_type',
            'pdf_upload_type'
        ];

        foreach ($fields as $field) {
            $formDataArr[$field] = $post($field);
        }

        // Arrays (no sanitize)
        $formDataArr['user_role'] = isset($_POST['user_role'])
            ? serialize($_POST['user_role'])
            : null;

        // -----------------------------
        // FILE HANDLING (USING HELPER)
        // -----------------------------
        $formDataArr['fran_image'] = $this->GlobalLibraryHandlerObj->handleFileUpload([
            'input'   => 'local_fran_image',
            'hidden'  => $_POST['hidden_fran_image'] ?? '',
            'default' => null,
            'dir'     => $dir,
            'isUpdate'  => $isUpdate,
        ]);

        $formDataArr['fran_pdf_name'] = $this->GlobalLibraryHandlerObj->handleFileUpload([
            'input'   => 'local_fran_pdf',
            'hidden'  => $_POST['hidden_fran_pdf'] ?? '',
            'default' => null,
            'dir'     => $dir,
            'isUpdate'  => $isUpdate,
        ]);

        // -----------------------------
        // DB OPERATION
        // -----------------------------
        $returnArr = $this->GlobalInterfaceControllerObj
            ->manage_Global_Franchise($formDataArr);

        // -----------------------------
        // RESPONSE HANDLING
        // -----------------------------
        if ($returnArr['check'] === 'success') {

            if (!empty($returnArr['last_insert_id'])) {
                $this->GlobalLibraryHandlerObj->purgeSiteCache("franchise");

                return [
                    'check' => 'success',
                    'message' => "Franchise has been successfully created!",
                    'last_insert_id' => $returnArr['last_insert_id']
                ];
            }

            return ['check' => 'success', 'message' => 'Franchise updated successfully!'];
        }

        // No manual rollback needed anymore (handled in helper or can be extended)
        return ['check' => 'failure', 'message' => "Something went wrong!"];
    }

    public function manage_course($data)
    {
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

        $formDataArr = [];
        $dir = 'course';

        // -----------------------------
        // BASIC DATA
        // -----------------------------
        $formDataArr['course_id'] = (int) $post('course_id');
        $isUpdate = ($formDataArr['course_id'] > 0);

        $role = $isUpdate ? 'update_course' : 'create_course';

        if (!$this->GlobalLibraryHandlerObj->checkUserRolePermission($role, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have permission!"];
        }

        // -----------------------------
        // CORE FIELDS
        // -----------------------------
        $formDataArr['course_title'] = $post('course_title');

        $formDataArr['seo_url_structure'] = $this->GlobalLibraryHandlerObj
            ->seoUrlStructure($formDataArr['course_title'], 'seo');

        // -----------------------------
        // SLUG VALIDATION
        // -----------------------------
        $slugData = $this->GlobalLibraryHandlerObj
            ->checkSlugAvailibility('course', 'seo_url_structure', $formDataArr['seo_url_structure']);

        if (!empty($slugData->id) && (!$isUpdate || $slugData->id != $formDataArr['course_id'])) {
            return ['check' => 'failure', 'message' => 'This title is already taken; Please try another.'];
        }

        // -----------------------------
        // OTHER FIELDS
        // -----------------------------
        $fields = [
            'course_fees',
            'course_duration',
            'record_status',
            'featured_status',
            'course_description',
            'image_upload_type',
            'pdf_upload_type'
        ];

        foreach ($fields as $field) {
            $formDataArr[$field] = $post($field);
        }

        // -----------------------------
        // FILE HANDLING (USING HELPER)
        // -----------------------------
        $formDataArr['course_thumbnail'] = $this->GlobalLibraryHandlerObj->handleFileUpload([
            'input'   => 'course_thumbnail_local',
            'hidden'  => $_POST['hidden_course_thumbnail'] ?? '',
            'default' => null,
            'dir'     => $dir,
            'isUpdate'  => $isUpdate,
        ]);

        $formDataArr['course_pdf'] = $this->GlobalLibraryHandlerObj->handleFileUpload([
            'input'   => 'local_course_pdf',
            'hidden'  => $_POST['hidden_course_pdf'] ?? '',
            'default' => null,
            'dir'     => $dir,
            'isUpdate'  => $isUpdate,
        ]);

        // -----------------------------
        // DB OPERATION
        // -----------------------------
        $returnArr = $this->GlobalInterfaceControllerObj
            ->manage_Global_Course($formDataArr);

        // -----------------------------
        // RESPONSE HANDLING
        // -----------------------------
        if ($returnArr['check'] === 'success') {

            // Only purge cache on create (same behavior as before)
            if (!$isUpdate) {
                $this->GlobalLibraryHandlerObj->purgeSiteCache("course");
            }

            return $returnArr;
        }

        return ['check' => 'failure', 'message' => "Something went wrong!"];
    }
}
