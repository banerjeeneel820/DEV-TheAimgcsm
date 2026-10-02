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
    public function fetch_category_data($data)
    {
        $user_role_slug = 'view_category';

        // Load assets
        $assets = Asset::load("category_list");

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'category_data' => [],
                    'page_type' => 'parent_category'
                ],
                'Category List',
                $assets,
                false,
                false // page_permission
            );
        }

        // Get filter safely
        $record_status = $data['record_status'] ?? 'active';

        // Fetch category data through service
        $categories = $this->cmsService->getParentCategoryData($record_status);

        //var_dump($categories);exit;

        return $this->page(
            [
                'category_data' => $categories,
                'page_type' => 'parent_category'
            ],
            'Category List',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage parent category data methods
    |--------------------------------------------------------------------------
    */

    public function manage_parent_category($data)
    {
        $formDataArr = [];

        // Helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Basic Data & Permission
        // -----------------------------

        $formDataArr['row_id'] = $post('row_id');

        $isUpdate = (int) $formDataArr['row_id'] > 0;

        $user_role_slug = $isUpdate
            ? 'update_category'
            : 'create_category';

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // Category Fields
        // -----------------------------

        $formDataArr['category']        = $post('category');
        $formDataArr['parent_category'] = $post('parent_category');
        $formDataArr['record_status']   = $post('record_status');

        // -----------------------------
        // Save
        // -----------------------------

        return $this->cmsService
            ->manageParentCategory($formDataArr);
    }

    /*
    |--------------------------------------------------------------------------
    | View home slider data methods
    |--------------------------------------------------------------------------
    */
    public function manage_home_slider_data_view($data)
    {
        $user_role_slug = 'manage_home_slider';

        // Load assets
        $assets = Asset::load("home_sliders");

        // Get request parameters safely
        $type = $data['type'] ?? null;
        $record_status = $data['record_status'] ?? 'active';
        $slider_type = $data['slider_type'] ?? null;

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        // Common page data
        $pageData = [
            'page_type' => 'home_sliders',
            'type' => $type
        ];

        // Handle unauthorized access
        if (!$hasPermission) {
            $pageData['slider_data'] = [];

            return $this->page(
                $pageData,
                'Home Sliders',
                $assets,
                false,
                false
            );
        }

        // Handle slider listing
        if (empty($type)) {

            $pageData['slider_data'] = $this->cmsService->getSliderData([
                'record_status' => $record_status,
                'slider_type' => $slider_type
            ]);
        }

        // Handle adding a new slider
        elseif ($type === 'add') {

            // No existing slider data required

        }

        // Handle editing an existing slider
        else {

            $slider_id = $data['id'] ?? null;

            $pageData['slider_data'] = $slider_id
                ? $this->cmsService->getSliderDetail($slider_id)
                : [];
        }

        return $this->page(
            $pageData,
            'Home Sliders',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage home slider data methods
    |--------------------------------------------------------------------------
    */
    public function manage_home_slider($data)
    {
        $formDataArr = [];
        $dir = 'home_sliders';

        // Helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Permission Check
        // -----------------------------

        $user_role_slug = 'manage_home_slider';

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // Basic Data
        // -----------------------------

        $formDataArr['slider_id']        = $post('slider_id');
        $formDataArr['slider_type']      = $post('slider_type');
        $formDataArr['banner_title']     = $post('banner_title');
        $formDataArr['banner_text']      = $post('banner_text');
        $formDataArr['banner_link']      = $post('banner_link');
        $formDataArr['file_upload_type'] = $post('file_upload_type');
        $formDataArr['record_status']    = $post('record_status');

        $isUpdate = (int) $formDataArr['slider_id'] > 0;

        // -----------------------------
        // File Handling
        // -----------------------------

        $hiddenBannerImage = $post('hidden_banner_image');

        $uploadReturnArr = ['check' => 'skip'];

        if ($formDataArr['file_upload_type'] === 'local') {

            $file = $_FILES['banner_image_local'] ?? null;

            if (!empty($file['size'])) {

                // Validate image
                $validation = $this->lib->validateFile($file, 'image');

                if ($validation['check'] !== 'success') {
                    return $validation;
                }

                // Upload image
                $uploadReturnArr = $this->lib->upload_file(
                    'banner_image_local',
                    $dir
                );

                if ($uploadReturnArr['check'] !== 'success') {
                    return [
                        'check' => 'failure',
                        'message' => "File upload failed!"
                    ];
                }

                $formDataArr['banner_image'] = $uploadReturnArr['fileName'];
            } else {

                // Retain existing image
                $formDataArr['banner_image'] = $hiddenBannerImage;
            }
        } else {

            // CDN image
            $formDataArr['banner_image'] = $post('banner_image_cdn');
        }

        // -----------------------------
        // Save & Post-Save Operations
        // -----------------------------

        return $this->cmsService->manageHomeSlider(
            $formDataArr,
            $uploadReturnArr,
            $hiddenBannerImage,
            $isUpdate
        );
    }

    /*
    |--------------------------------------------------------------------------
    | View cities data methods
    |--------------------------------------------------------------------------
    */
    public function manage_city_data_view($data)
    {
        $user_role_slug = 'manage_city_db';

        // Load assets
        $assets = Asset::load("city_list");

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                ['city_data' => []],
                'City List',
                $assets,
                false,
                false // page_permission
            );
        }

        // Get filter safely
        $record_status = $data['record_status'] ?? 'active';

        // Fetch city data through service
        $cities = $this->cmsService->getCityData($record_status);

        return $this->page(
            [
                'city_data' => $cities,
                'page_type' => 'cities'
            ],
            'City List',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage cities data methods
    |--------------------------------------------------------------------------
    */
    public function manage_global_city($data)
    {
        $formDataArr = [];

        // Helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Permission Check
        // -----------------------------

        if (!$this->permissionService->checkUserRolePermission('manage_city_db', "hard")) {
            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // City Data
        // -----------------------------

        $formDataArr['row_id']        = $post('row_id');
        $formDataArr['name']          = $post('city');
        $formDataArr['record_status'] = $post('record_status');

        // -----------------------------
        // Save
        // -----------------------------

        return $this->cmsService
            ->manageGlobalCity($formDataArr);
    }

    /*
    |--------------------------------------------------------------------------
    | View email template data methods
    |--------------------------------------------------------------------------
    */
    public function fetch_email_template_data($data)
    {
        $user_role_slug = 'view_template';

        // Load assets
        $assets = Asset::load("email_templates");

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                ['email_template_data' => []],
                'Email Templates',
                $assets,
                false,
                false // page_permission
            );
        }

        // Get filter safely
        $record_status = $data['record_status'] ?? 'active';

        // Fetch email templates through service
        $emailTemplates = $this->cmsService->getEmailTemplates($record_status);

        return $this->page(
            [
                'email_template_data' => $emailTemplates,
                'page_type' => 'email_template'
            ],
            'Email Templates',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage email template data view methods
    |--------------------------------------------------------------------------
    */
    public function manage_email_template_data_view($data)
    {
        // Load assets
        $assets = Asset::load("email_template_form");

        // Get template ID safely
        $template_id = (int) ($data['id'] ?? 0);

        // Determine permission based on create/edit mode
        if ($template_id > 0) {
            $user_role_slug = 'update_template';
        } else {
            $user_role_slug = 'create_template';
        }

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'email_template_details' => [],
                    'page_type' => 'email_template'
                ],
                'Email Template',
                $assets,
                false,
                false // page_permission
            );
        }

        // Default data for create mode
        $templateDetails = [];

        // Fetch existing template for edit mode
        if ($template_id > 0) {
            $templateDetails = $this->cmsService->getEmailTemplateDetail($template_id);
        }

        return $this->page(
            [
                'email_template_details' => $templateDetails,
                'page_type' => 'email_template'
            ],
            'Email Template',
            $assets,
            true,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage email template data methods
    |--------------------------------------------------------------------------
    */
    public function manage_email_template($data)
    {
        $formDataArr = [];

        // Helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Determine Action Type
        // -----------------------------

        $formDataArr['template_id'] = $post('template_id');

        $isUpdate = (int) $formDataArr['template_id'] > 0;

        $user_role_slug = $isUpdate
            ? 'update_template'
            : 'create_template';

        // -----------------------------
        // Permission Check
        // -----------------------------

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return [
                'check'   => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // Template Data
        // -----------------------------

        $formDataArr['subject']       = $post('subject');
        $formDataArr['code']          = $post('code');
        $formDataArr['email_for']     = $post('email_for');
        $formDataArr['record_status'] = $post('record_status');
        $formDataArr['variables']     = $post('variables');
        $formDataArr['from_email']    = $post('from_email');
        $formDataArr['from_name']     = $post('from_name');
        $formDataArr['cc_email']      = $post('cc_email');
        $formDataArr['template']      = $post('template');

        // -----------------------------
        // Save
        // -----------------------------

        return $this->cmsService
            ->manageEmailTemplate($formDataArr);
    }

}
