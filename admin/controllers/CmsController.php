<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class CmsController extends BaseController
{
    private $permissionService;
    private $cmsService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->permissionService = $container->get(PermissionService::class);
        $this->cmsService = $container->get(CmsService::class);
    }

    /*
    |--------------------------------------------------------------------------
    | View gallery view data methods
    |--------------------------------------------------------------------------
    */
    public function fetch_gallery_data($data = [])
    {
        $type       = 'gallery';
        $actionType = $data['type'] ?? '';
        $recordStatus = $data['record_status'] ?? 'active';

        // =========================
        // Assets
        // =========================
        $assets = Asset::load("gallery_list");

        // =========================
        // Gallery List View
        // =========================
        if (empty($actionType)) {

            $hasPermission = $this->permissionService
                ->checkUserRolePermission('view_gallery');

            if (!$hasPermission) {

                return $this->page(
                    [
                        'gallery_data' => [],
                        'page_type'    => $type
                    ],
                    'Gallery List',
                    $assets,
                    false,
                    false
                );
            }

            $galleryData = $this->cmsService
                ->getGalleryList($recordStatus);

            return $this->page(
                [
                    'gallery_data' => $galleryData,
                    'page_type'    => $type
                ],
                'Gallery List',
                $assets,
                false,
                true
            );
        }

        // =========================
        // Add Gallery View
        // =========================
        if ($actionType === 'add') {

            $hasPermission = $this->permissionService
                ->checkUserRolePermission('create_gallery');

            if (!$hasPermission) {

                return $this->page(
                    [
                        'category_data' => [],
                        'page_type'     => $type
                    ],
                    'Add Gallery',
                    $assets,
                    false,
                    false
                );
            }

            $categoryData = $this->cmsService
                ->getGalleryCategories($type);

            return $this->page(
                [
                    'category_data' => $categoryData,
                    'page_type'     => $type
                ],
                'Add Gallery',
                $assets,
                false,
                true
            );
        }

        // =========================
        // Edit Gallery View
        // =========================
        $hasPermission = $this->permissionService
            ->checkUserRolePermission('update_gallery');

        if (!$hasPermission) {

            return $this->page(
                [
                    'gallery_data'  => [],
                    'category_data' => [],
                    'page_type'     => $type
                ],
                'Update Gallery',
                $assets,
                false,
                false
            );
        }

        $media_id = isset($data['id'])
            ? (int)$data['id']
            : 0;

        $galleryData = $this->cmsService
            ->getGalleryDetails($media_id);

        $categoryData = $this->cmsService
            ->getGalleryCategories($type);

        return $this->page(
            [
                'gallery_data'  => $galleryData,
                'category_data' => $categoryData,
                'page_type'     => $type
            ],
            'Update Gallery',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage gallery data methods
    |--------------------------------------------------------------------------
    */
    public function manage_gallery($data)
    {
        $formDataArr = [];
        $returnArr = [];
        $dir = 'gallery';

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Basic Data & Permission
        // -----------------------------
        $formDataArr['media_id'] = $post('media_id');
        $isUpdate = $formDataArr['media_id'] > 0;

        $user_role_slug = $isUpdate ? 'update_gallery' : 'create_gallery';

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Basic Fields
        // -----------------------------
        $formDataArr['title'] = $post('title');
        $formDataArr['content_type'] = $post('content_type');

        $hiddenContent       = $post('hidden_media_content');
        $hiddenType          = $post('hidden_content_type');
        $hiddenUploadType    = $post('hidden_file_upload_type');

        $uploadReturnArr = ['check' => 'skip'];

        // -----------------------------
        // Content Handling
        // -----------------------------
        if ($formDataArr['content_type'] === 'image') {

            $formDataArr['file_upload_type'] = $post('file_upload_type');

            if ($formDataArr['file_upload_type'] === "local") {

                if (!empty($_FILES["local_media_image"]["size"])) {

                    $uploadReturnArr = $this->lib->upload_file('local_media_image', $dir);

                    if ($uploadReturnArr['check'] !== 'success') {
                        return ['check' => 'failure', 'msg' => "File upload failed!"];
                    }

                    $formDataArr['content'] = $uploadReturnArr['fileName'];
                } else {
                    // fallback to hidden
                    $formDataArr['content_type']     = $hiddenType;
                    $formDataArr['file_upload_type'] = $hiddenUploadType;
                    $formDataArr['content']          = $hiddenContent;
                }
            } else {
                // CDN image
                $cdn = $post('media_image_cdn');

                if (!empty($cdn)) {
                    $formDataArr['content'] = $cdn;
                } else {
                    $formDataArr['content_type']     = $hiddenType;
                    $formDataArr['file_upload_type'] = $hiddenUploadType;
                    $formDataArr['content']          = $hiddenContent;
                }
            }
        } else {
            // VIDEO
            $formDataArr['file_upload_type'] = "cdn";

            $video = $post('video_url');

            if (!empty($video)) {
                $formDataArr['content'] = $video;
            } else {
                $formDataArr['content_type']     = $hiddenType;
                $formDataArr['file_upload_type'] = $hiddenUploadType;
                $formDataArr['content']          = $hiddenContent;
            }
        }

        // -----------------------------
        // Other Fields
        // -----------------------------
        $formDataArr['record_status']   = $post('record_status');
        $formDataArr['featured_status'] = $post('featured_status');

        // -----------------------------
        // Save
        // -----------------------------
        $returnArr = $this->cmsService
            ->saveGalleryData($formDataArr);

        if ($returnArr['check'] !== 'success') {

            // rollback uploaded file
            if (
                $formDataArr['content_type'] === "image" &&
                $formDataArr['file_upload_type'] === "local" &&
                $uploadReturnArr['check'] === 'success'
            ) {
                unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['content']);
            }

            return ['check' => 'failure', 'message' => "Something went wrong!"];
        }

        // -----------------------------
        // Determine Post ID
        // -----------------------------
        $post_id = $returnArr['last_insert_id'] > 0
            ? $returnArr['last_insert_id']
            : $formDataArr['media_id'];

        // -----------------------------
        // Cleanup Old File (Update Case)
        // -----------------------------
        if ($isUpdate && !empty($hiddenContent)) {

            $hiddenFilePath = USER_UPLOAD_DIR . $dir . '/' . $hiddenContent;

            if (
                $formDataArr['content_type'] === "image" &&
                (
                    ($formDataArr['file_upload_type'] === "cdn" && $hiddenUploadType === "local") ||
                    ($formDataArr['file_upload_type'] === "local" && $uploadReturnArr['check'] === 'success')
                )
            ) {
                if (file_exists($hiddenFilePath)) {
                    unlink($hiddenFilePath);
                }
            }

            if ($formDataArr['content_type'] !== "image") {
                if (file_exists($hiddenFilePath)) {
                    unlink($hiddenFilePath);
                }
            }
        }

        // -----------------------------
        // Category Mapping
        // -----------------------------
        $updateCategoryArr = [
            'post_type'   => "gallery",
            'post_id'     => $post_id,
            'category_id' => $post('category_id')
        ];

        $this->cmsService
            ->editPostCategory($updateCategoryArr);

        return $returnArr;
    }

    public function gallery_bulk_uploader($data)
    {
        $formDataArr = [];
        $returnArr = [];
        $dir = 'gallery';

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = 'create_gallery';

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // File Validation (Image Only)
        // -----------------------------
        $validation = $this->lib->validateFile($_FILES['file'], 'image');

        if ($validation['check'] !== 'success') {
            return $validation;
        }

        // -----------------------------
        // Prepare Data
        // -----------------------------
        $formDataArr['media_id'] = null;
        $formDataArr['title'] = 'Gallery-' . time();
        $formDataArr['content_type'] = 'image';
        $formDataArr['file_upload_type'] = 'local';
        $formDataArr['record_status'] = 'active';
        $formDataArr['featured_status'] = 'inactive';

        // -----------------------------
        // Category Selection
        // -----------------------------
        $categoryListArr = json_decode(
            json_encode(
                $this->cmsService->fetchSingleParentCategory($dir)
            ),
            true
        );

        $shuffled = array_values($this->lib->shuffle_assoc($categoryListArr));

        $categoryIdArr = [];
        foreach ($shuffled as $index => $category) {
            if ($index % 2 === 0) {
                $categoryIdArr[] = $category['id'];
            }
        }

        // -----------------------------
        // File Upload
        // -----------------------------
        $uploadReturnArr = $this->lib->upload_file('file', $dir);

        if ($uploadReturnArr['check'] !== 'success') {
            return ['check' => 'failure', 'message' => "File upload failed!"];
        }

        $formDataArr['content'] = $uploadReturnArr['fileName'];

        // -----------------------------
        // Save
        // -----------------------------
        $returnArr = $this->cmsService
            ->saveGalleryData($formDataArr);

        if ($returnArr['check'] !== 'success') {

            // rollback uploaded file
            if (file_exists(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['content'])) {
                unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['content']);
            }

            return ['check' => 'failure', 'message' => $returnArr['msg'] ?? "Something went wrong!"];
        }

        // -----------------------------
        // Category Mapping
        // -----------------------------
        $this->cmsService->editPostCategory([
            'post_type'   => "gallery",
            'post_id'     => $returnArr['last_insert_id'],
            'category_id' => $categoryIdArr
        ]);

        // -----------------------------
        // Response
        // -----------------------------
        return [
            'check'   => 'success',
            'message' => $formDataArr['title'] . " has been successfully uploaded!"
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | View parent category data methods
    |--------------------------------------------------------------------------
    */
    public function manage_parent_category($data)
    {
        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $formDataArr = [];

        // basic fields
        $formDataArr['row_id'] = $post('row_id');

        $isUpdate = !empty($formDataArr['row_id']) && $formDataArr['row_id'] > 0;
        $user_role_slug = $isUpdate ? 'update_category' : 'create_category';

        // permission check (early return)
        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // assign remaining fields
        $formDataArr['category'] = $post('category');
        $formDataArr['parent_category'] = $post('parent_category');
        $formDataArr['record_status'] = $post('record_status');

        // call interface
        return $this->model->manage_Parent_Category($formDataArr);
    }

    public function manage_global_city($data)
    {
        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $formDataArr = [];

        // permission check (early return)
        if (!$this->permissionService->checkUserRolePermission('manage_city_db', "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // assign fields
        $formDataArr['row_id'] = $post('row_id');
        $formDataArr['name'] = $post('city');
        $formDataArr['record_status'] = $post('record_status');

        // call interface
        return $this->model->manage_Global_City($formDataArr);
    }

    public function manage_home_slider($data)
    {
        $formDataArr = [];
        $dir = 'home_sliders';

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = 'manage_home_slider';

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $formDataArr['slider_id']    = $post('slider_id');
        $formDataArr['slider_type']  = $post('slider_type');
        $formDataArr['banner_title'] = $post('banner_title');
        $formDataArr['banner_text']  = $post('banner_text');
        $formDataArr['banner_link']  = $post('banner_link');
        $formDataArr['file_upload_type'] = $post('file_upload_type');
        $formDataArr['record_status']    = $post('record_status');

        $isUpdate = !empty($formDataArr['slider_id']) && $formDataArr['slider_id'] > 0;

        // -----------------------------
        // File Handling
        // -----------------------------
        $uploadReturnArr = ['check' => 'skip'];

        if ($formDataArr['file_upload_type'] === "local") {

            $validation = $this->lib->validateFile($_FILES['banner_image_local'], 'image');

            if ($validation['check'] !== 'success') {
                return $validation;
            }

            if (!empty($_FILES["banner_image_local"]["size"])) {

                $uploadReturnArr = $this->lib->upload_file('banner_image_local', $dir);

                if ($uploadReturnArr['check'] !== 'success') {
                    return ['check' => 'failure', 'msg' => "File upload failed!"];
                }

                $formDataArr['banner_image'] = $uploadReturnArr['fileName'];
            } else {
                $formDataArr['banner_image'] = $post('hidden_banner_image');
            }
        } else {
            $formDataArr['banner_image'] = $post('banner_image_cdn');
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->model->manage_Home_Slider($formDataArr);

        // -----------------------------
        // Post Operation (File Cleanup)
        // -----------------------------
        if ($returnArr['check'] === 'success') {

            if ($isUpdate) {

                // delete old file if new upload OR switched to CDN
                if (
                    ($formDataArr['file_upload_type'] === 'local' && $uploadReturnArr['check'] === 'success') ||
                    ($formDataArr['file_upload_type'] === 'cdn')
                ) {
                    $oldFile = $post('hidden_banner_image');

                    if (!empty($oldFile)) {
                        @unlink(USER_UPLOAD_DIR . $dir . '/' . $oldFile);
                    }
                }
            }
        } else {

            // rollback uploaded file
            if ($uploadReturnArr['check'] === 'success') {
                @unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['banner_image']);
            }

            return ['check' => 'failure', 'message' => "Something went wrong!"];
        }

        return $returnArr;
    }

    // Separate Controller
    public function manage_email_template($data)
    {
        $formDataArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Determine Action Type
        // -----------------------------
        $formDataArr['template_id'] = $post('template_id');
        $isUpdate = !empty($formDataArr['template_id']) && $formDataArr['template_id'] > 0;

        $user_role_slug = $isUpdate ? 'update_template' : 'create_template';

        // -----------------------------
        // Permission Check
        // -----------------------------
        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $formDataArr['subject']      = $post('subject');
        $formDataArr['code']         = $post('code');
        $formDataArr['email_for']    = $post('email_for');
        $formDataArr['record_status'] = $post('record_status');
        $formDataArr['variables']    = $post('variables');
        $formDataArr['from_email']   = $post('from_email');
        $formDataArr['from_name']    = $post('from_name');
        $formDataArr['cc_email']     = $post('cc_email');
        $formDataArr['template']     = $post('template');

        // -----------------------------
        // Slug (Code) Validation
        // -----------------------------
        $existingId = $this->model
            ->check_Slug_Availibility('email_template', 'code', $formDataArr['code'])
            ->id ?? null;

        if (
            ($isUpdate && !empty($existingId) && $existingId != $formDataArr['template_id']) ||
            (!$isUpdate && !empty($existingId))
        ) {
            return [
                'check'   => 'failure',
                'message' => 'This code is already available; Please try another.'
            ];
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        return $this->model
            ->manage_Global_Email_Template($formDataArr);
    }

    public function manage_global_news($data)
    {
        $formDataArr = [];
        $dir = 'news';

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Determine Action Type
        // -----------------------------
        $formDataArr['news_id'] = $post('news_id');
        $isUpdate = !empty($formDataArr['news_id']) && $formDataArr['news_id'] > 0;

        $user_role_slug = $isUpdate ? 'update_news' : 'create_news';

        // -----------------------------
        // Permission Check
        // -----------------------------
        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $formDataArr['title']           = $post('title');
        $formDataArr['record_status']   = $post('record_status');
        $formDataArr['featured_status'] = $post('featured_status');
        $formDataArr['description']     = $post('description');

        // -----------------------------
        // File Handling (PDF)
        // -----------------------------
        $uploadReturnArr = ['check' => 'skip'];

        if (!empty($_FILES["local_news_pdf"]["size"])) {

            // validate file (using your new helper)
            $validation = $this->lib
                ->validateFile($_FILES['local_news_pdf'], 'pdf');

            if ($validation['check'] !== 'success') {
                return $validation;
            }

            $uploadReturnArr = $this->lib
                ->upload_file('local_news_pdf', $dir);

            if ($uploadReturnArr['check'] !== 'success') {
                return ['check' => 'failure', 'msg' => "News PDF upload failed!"];
            }

            $formDataArr['optional_pdf'] = $uploadReturnArr['fileName'];
        } else {
            $formDataArr['optional_pdf'] = $isUpdate
                ? $post('hidden_optional_pdf')
                : null;
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->model
            ->manage_Global_News($formDataArr);

        // -----------------------------
        // Post Operation (File Cleanup)
        // -----------------------------
        if ($returnArr['check'] === 'success') {

            if (
                $isUpdate &&
                $uploadReturnArr['check'] === 'success'
            ) {
                $oldFile = $post('hidden_optional_pdf');

                if (!empty($oldFile)) {
                    @unlink(USER_UPLOAD_DIR . $dir . '/' . $oldFile);
                }
            }
        } else {

            // rollback uploaded file
            if ($uploadReturnArr['check'] === 'success') {
                @unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['optional_pdf']);
            }

            return ['check' => 'failure', 'message' => "Something went wrong!"];
        }

        return $returnArr;
    }
}
