<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class CourseFranchiseController extends BaseController
{
    private $courseFranchiseService;
    private $cacheService;
    private $permissionService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->courseFranchiseService = $container->get(CourseFranchiseService::class);
        $this->cacheService = $container->get(CacheService::class);
        $this->permissionService = $container->get(PermissionService::class);
    }

    public function fetch_franchise_data($data)
    {
        $user_role_slug = 'view_franchise';

        // Load assets
        $assets = Asset::load("franchise_list");

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                ['data' => []],
                'Franchise List',
                $assets,
                false,
                false // page_permission
            );
        }

        // Get filter safely
        $record_status = $data['record_status'] ?? 'active';

        // Fetch data (with cache)
        $franchises = $this->courseFranchiseService->getFranchiseData($record_status);

        return $this->page(
            [
                'data' => $franchises,
                'page_type' => 'franchise'
            ],
            'Franchise List',
            $assets,
            false,
            true
        );
    }

    public function manage_franchise_data_view($data)
    {
        // Detect mode (create / edit)
        $isEdit = !empty($data['id']);

        // Load assets
        $assets = Asset::load("manage_franchise_form");

        // Determine franchise ID
        $franchiseId = $this->courseFranchiseService->getFranchiseId($data);

        // Permission based on mode
        $permissionSlug = $isEdit ? 'update_franchise' : 'create_franchise';
        $hasPermission = $this->permissionService->checkUserRolePermission($permissionSlug);

        if (!$hasPermission) {

            return $this->page(
                ['franchise_data' => []],
                'Manage Franchise',
                $assets,
                true,
                false
            );
        }

        // Fetch data only for edit
        $franchiseData = $isEdit
            ? $this->courseFranchiseService->getSingleFranchise($franchiseId)
            : [];

        return $this->page(
            [
                'franchise_data' => $franchiseData,
                'page_type' => 'franchise'
            ],
            'Manage Franchise',
            $assets,
            true,
            true
        );
    }

    public function manage_franchise($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $formDataArr = [];
        $dir = 'franchise';

        // -----------------------------
        // BASIC DATA
        // -----------------------------
        $formDataArr['fran_row_id'] = $post('fran_row_id');

        $isUpdate = ($formDataArr['fran_row_id'] !== 'null');

        $role = $isUpdate ? 'update_franchise' : 'create_franchise';

        if (!$this->permissionService->checkUserRolePermission($role, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have permission!"];
        }

        // -----------------------------
        // CREATE LOGIC
        // -----------------------------
        if (!$isUpdate) {
            $formDataArr['fran_id'] = $this->courseFranchiseService->create_Frnachise_ID();
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

        $formDataArr['seo_url_structure'] = $this->lib
            ->seoUrlStructure($formDataArr['center_name'], 'seo');

        // -----------------------------
        // SLUG VALIDATION
        // -----------------------------
        $slugData = $this->courseFranchiseService
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
        $formDataArr['fran_image'] = $this->lib->handleFileUpload([
            'input'   => 'local_fran_image',
            'hidden'  => $_POST['hidden_fran_image'] ?? '',
            'default' => null,
            'dir'     => $dir,
            'isUpdate'  => $isUpdate,
        ]);

        $formDataArr['fran_pdf_name'] = $this->lib->handleFileUpload([
            'input'   => 'local_fran_pdf',
            'hidden'  => $_POST['hidden_fran_pdf'] ?? '',
            'default' => null,
            'dir'     => $dir,
            'isUpdate'  => $isUpdate,
        ]);

        // -----------------------------
        // DB OPERATION
        // -----------------------------
        $returnArr = $this->courseFranchiseService
            ->saveFranchiseData($formDataArr);

        // -----------------------------
        // RESPONSE HANDLING
        // -----------------------------
        if ($returnArr['check'] === 'success') {

            if (!empty($returnArr['last_insert_id'])) {
                $this->cacheService->purgeSiteCache("franchise");

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

    public function fetch_course_data($data)
    {
        $user_role_slug = 'view_course';

        // Load assets
        $assets = Asset::load("course_list");

        // Permission check
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                ['data' => []],
                'Course List',
                $assets,
                false,
                false
            );
        }

        // Get filter safely
        $record_status = $data['record_status'] ?? 'active';

        // Fetch data (delegated to service)
        $courses = $this->courseFranchiseService->getCourseData($record_status);

        return $this->page(
            [
                'data' => $courses,
                'page_type' => 'course'
            ],
            'Course List',
            $assets,
            false,
            true
        );
    }

    public function manage_course_data_view($data)
    {
        // Load assets
        $assets = Asset::load("manage_course_form");

        // Resolve mode (create / update)
        $courseId = $data['id'] ?? null;
        $isEdit = !empty($courseId);

        $user_role_slug = $isEdit ? 'update_course' : 'create_course';

        // Permission check
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'course_data' => [],
                    'page_type'   => 'course',
                    'mode'        => $isEdit ? 'edit' : 'create'
                ],
                'Manage Course',
                $assets,
                false,
                false
            );
        }

        // Fetch data only if edit mode
        $courseData = $isEdit
            ? $this->courseFranchiseService->getCourseById($courseId)
            : [];

        return $this->page(
            [
                'course_data' => $courseData,
                'page_type'   => 'course',
                'mode'        => $isEdit ? 'edit' : 'create'
            ],
            'Manage Course',
            $assets,
            true,
            true
        );
    }

    public function manage_course($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $formDataArr = [];
        $dir = 'course';

        // -----------------------------
        // BASIC DATA
        // -----------------------------
        $formDataArr['course_id'] = (int) $post('course_id');
        $isUpdate = ($formDataArr['course_id'] > 0);

        $role = $isUpdate ? 'update_course' : 'create_course';

        if (!$this->permissionService->checkUserRolePermission($role, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have permission!"];
        }

        // -----------------------------
        // CORE FIELDS
        // -----------------------------
        $formDataArr['course_title'] = $post('course_title');

        $formDataArr['seo_url_structure'] = $this->lib
            ->seoUrlStructure($formDataArr['course_title'], 'seo');

        // -----------------------------
        // SLUG VALIDATION
        // -----------------------------
        $slugData = $this->courseFranchiseService
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
        $formDataArr['course_thumbnail'] = $this->lib->handleFileUpload([
            'input'   => 'course_thumbnail_local',
            'hidden'  => $_POST['hidden_course_thumbnail'] ?? '',
            'default' => null,
            'dir'     => $dir,
            'isUpdate'  => $isUpdate,
        ]);

        $formDataArr['course_pdf'] = $this->lib->handleFileUpload([
            'input'   => 'local_course_pdf',
            'hidden'  => $_POST['hidden_course_pdf'] ?? '',
            'default' => null,
            'dir'     => $dir,
            'isUpdate'  => $isUpdate,
        ]);

        // -----------------------------
        // DB OPERATION
        // -----------------------------
        $returnArr = $this->courseFranchiseService
            ->saveCourseData($formDataArr);

        // -----------------------------
        // RESPONSE HANDLING
        // -----------------------------
        if ($returnArr['check'] === 'success') {

            // Only purge cache on create (same behavior as before)
            if (!$isUpdate) {
                $this->cacheService->purgeSiteCache("course");
            }

            return $returnArr;
        }

        return ['check' => 'failure', 'message' => "Something went wrong!"];
    }
}
