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
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];

        // helper (same as reference)
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

        // -----------------------------
        // Basic Data
        // -----------------------------
        $formDataArr['fran_row_id'] = $post('fran_row_id');

        $user_role_slug = ($formDataArr['fran_row_id'] != 'null')
            ? 'update_franchise'
            : 'create_franchise';

        //check action permission        
        $checkActionPermission = $this->GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

        if (!$checkActionPermission) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Create / Update Logic
        // -----------------------------
        if ($formDataArr['fran_row_id'] == 'null') {
            $formDataArr['fran_id'] = $this->GlobalLibraryHandlerObj->create_Frnachise_ID();
        }

        // -----------------------------
        // Password Handling
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
        // Core Fields
        // -----------------------------
        $dir = 'franchise';

        $formDataArr['center_name'] = $post('center_name');
        $formDataArr['seo_url_structure'] = $this->GlobalLibraryHandlerObj
            ->seoUrlStructure($formDataArr['center_name'], 'seo');

        // -----------------------------
        // Slug Validation
        // -----------------------------
        $slugData = $this->GlobalLibraryHandlerObj
            ->checkSlugAvailibility('franchise', 'seo_url_structure', $formDataArr['seo_url_structure']);

        if ($formDataArr['fran_row_id'] != 'null') {
            if (!empty($slugData->id) && $slugData->id != $formDataArr['fran_row_id']) {
                echo json_encode(['check' => 'failure', 'message' => 'This title is already taken; Please try another.']);
                return false;
            }
        } else {
            if (!empty($slugData->id)) {
                echo json_encode(['check' => 'failure', 'message' => 'This title is already taken; Please try another.']);
                return false;
            }
        }

        // -----------------------------
        // Other Fields
        // -----------------------------
        $formDataArr['owner_name']     = $post('owner_name');
        $formDataArr['fran_phone']     = $post('fran_phone');
        $formDataArr['fran_email']     = $post('fran_email');
        $formDataArr['fran_address']   = $post('fran_address');
        $formDataArr['owned_status']   = $post('owned_status');
        $formDataArr['record_status']  = $post('record_status');
        $formDataArr['featured_status'] = $post('featured_status');
        $formDataArr['fran_description'] = $post('fran_description');

        $formDataArr['image_upload_type'] = $post('image_upload_type');
        $formDataArr['pdf_upload_type']   = $post('pdf_upload_type');

        // Arrays should NOT go through sanitizer
        $formDataArr['user_role'] = isset($_POST['user_role'])
            ? serialize($_POST['user_role'])
            : null;

        // -----------------------------
        // File Uploads
        // -----------------------------
        if ($_FILES["local_fran_image"]["size"] > 0) {
            $uploadImgReturnArr = $this->GlobalLibraryHandlerObj->upload_file('local_fran_image', $dir);

            if ($uploadImgReturnArr['check'] == 'success') {
                $formDataArr['fran_image'] = $uploadImgReturnArr['fileName'];
            } else {
                echo json_encode(['check' => 'failure', 'msg' => "Error uploading franchise image!"]);
                exit;
            }
        } else {
            $formDataArr['fran_image'] = ($formDataArr['fran_row_id'] != 'null')
                ? $post('hidden_fran_image')
                : null;
        }

        if ($_FILES["local_fran_pdf"]["size"] > 0) {
            $uploadPdfReturnArr = $this->GlobalLibraryHandlerObj->upload_file('local_fran_pdf', $dir);

            if ($uploadPdfReturnArr['check'] == 'success') {
                $formDataArr['fran_pdf_name'] = $uploadPdfReturnArr['fileName'];
            } else {
                echo json_encode(['check' => 'failure', 'msg' => "Error uploading franchise pdf!"]);
                exit;
            }
        } else {
            $formDataArr['fran_pdf_name'] = ($formDataArr['fran_row_id'] != 'null')
                ? $post('hidden_fran_pdf')
                : null;
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->GlobalInterfaceControllerObj->manage_Global_Franchise($formDataArr);

        // -----------------------------
        // Response Handling
        // -----------------------------
        if ($returnArr['check'] == 'success') {

            if ($returnArr['last_insert_id'] > 0) {

                $this->GlobalLibraryHandlerObj->purgeSiteCache("franchise");

                return [
                    'check' => 'success',
                    'message' => "Franchise has been successfully created!",
                    'last_insert_id' => $returnArr['last_insert_id']
                ];
            }
        } else {

            // rollback uploaded files
            if (!empty($uploadImgReturnArr) && $uploadImgReturnArr['check'] == 'success') {
                unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['fran_image']);
            }

            if (!empty($uploadPdfReturnArr) && $uploadPdfReturnArr['check'] == 'success') {
                unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['fran_pdf_name']);
            }

            return ['check' => 'failure', 'message' => "Something went wrong!"];
        }

        return $returnArr;
    }

    public function manage_course($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

        // -----------------------------
        // Basic Data
        // -----------------------------
        $formDataArr['course_id'] = (int) $post('course_id');

        $user_role_slug = ($formDataArr['course_id'] > 0)
            ? 'update_course'
            : 'create_course';

        // -----------------------------
        // Permission Check
        // -----------------------------
        $checkActionPermission = $this->GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard");

        if (!$checkActionPermission) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Core Fields
        // -----------------------------
        $dir = 'course';

        $formDataArr['course_title'] = $post('course_title');
        $formDataArr['seo_url_structure'] = $this->GlobalLibraryHandlerObj
            ->seoUrlStructure($formDataArr['course_title'], 'seo');

        // -----------------------------
        // Slug Validation
        // -----------------------------
        $slugData = $this->GlobalLibraryHandlerObj
            ->checkSlugAvailibility('course', 'seo_url_structure', $formDataArr['seo_url_structure']);

        if ($formDataArr['course_id'] > 0) {
            if (!empty($slugData->id) && $slugData->id != $formDataArr['course_id']) {
                echo json_encode(['check' => 'failure', 'message' => 'This title is already taken; Please try another.']);
                return false;
            }
        } else {
            if (!empty($slugData->id)) {
                echo json_encode(['check' => 'failure', 'message' => 'This title is already taken; Please try another.']);
                return false;
            }
        }

        // -----------------------------
        // Other Fields
        // -----------------------------
        $formDataArr['course_fees']        = $post('course_fees');
        $formDataArr['course_duration']    = $post('course_duration');
        $formDataArr['record_status']      = $post('record_status');
        $formDataArr['featured_status']    = $post('featured_status');
        $formDataArr['course_description'] = $post('course_description');

        $formDataArr['image_upload_type']  = $post('image_upload_type');
        $formDataArr['pdf_upload_type']    = $post('pdf_upload_type');

        // -----------------------------
        // File Uploads
        // -----------------------------
        if ($_FILES["course_thumbnail_local"]["size"] > 0) {

            $uploadImgReturnArr = $this->GlobalLibraryHandlerObj->upload_file('course_thumbnail_local', $dir);

            if ($uploadImgReturnArr['check'] === 'success') {
                $formDataArr['course_thumbnail'] = $uploadImgReturnArr['fileName'];
            } else {
                echo json_encode(['check' => 'failure', 'msg' => "Error uploading course image!"]);
                exit;
            }
        } else {
            $formDataArr['course_thumbnail'] = ($formDataArr['course_id'] > 0)
                ? $post('hidden_course_thumbnail')
                : null;
        }

        if ($_FILES["local_course_pdf"]["size"] > 0) {

            $uploadPdfReturnArr = $this->GlobalLibraryHandlerObj->upload_file('local_course_pdf', $dir);

            if ($uploadPdfReturnArr['check'] === 'success') {
                $formDataArr['course_pdf'] = $uploadPdfReturnArr['fileName'];
            } else {
                echo json_encode(['check' => 'failure', 'msg' => "Error uploading course pdf!"]);
                exit;
            }
        } else {
            $formDataArr['course_pdf'] = ($formDataArr['course_id'] > 0)
                ? $post('hidden_course_pdf')
                : null;
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->GlobalInterfaceControllerObj->manage_Global_Course($formDataArr);

        // -----------------------------
        // Response Handling
        // -----------------------------
        if ($returnArr['check'] === "success") {

            if ($formDataArr['course_id'] > 0) {

                // Remove old files if replaced
                if (!empty($uploadImgReturnArr) && $uploadImgReturnArr['check'] === 'success') {
                    unlink(USER_UPLOAD_DIR . $dir . '/' . $post('hidden_course_thumbnail'));
                }

                if (!empty($uploadPdfReturnArr) && $uploadPdfReturnArr['check'] === 'success') {
                    unlink(USER_UPLOAD_DIR . $dir . '/' . $post('hidden_course_pdf'));
                }
            } else {
                // New record → purge cache
                $this->GlobalLibraryHandlerObj->purgeSiteCache("course");
            }
        } else {

            // rollback uploaded files
            if (!empty($uploadImgReturnArr) && $uploadImgReturnArr['check'] === 'success') {
                unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['course_thumbnail']);
            }

            if (!empty($uploadPdfReturnArr) && $uploadPdfReturnArr['check'] === 'success') {
                unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['course_pdf']);
            }

            $returnArr = ['check' => 'failure', 'message' => "Something went wrong!"];
        }

        return $returnArr;
    }
}
