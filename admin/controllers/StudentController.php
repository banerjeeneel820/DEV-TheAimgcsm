<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class StudentController extends BaseController
{

    private $studentService;
    private $permissionService;
    private $courseFranchiseService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->studentService = $container->get('studentService');
        $this->permissionService = $container->get('permissionService');
        $this->courseFranchiseService = $container->get('courseFranchiseService');
    }

    // View student data methods start here
    public function fetch_student_data($data)
    {
        $user_role_slug = 'view_student';

        // =========================
        // Assets
        // =========================
        $assets = Asset::load("student_list");

        // =========================
        // Permission
        // =========================
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'student_data'   => [],
                    'franchise_data' => [],
                    'course_data'    => [],
                    'page_type'      => 'student'
                ],
                'Student List',
                $assets,
                false,
                false
            );
        }

        // =========================
        // Prepare Filters
        // =========================
        $filters = $this->studentService->prepareStudentFilters($data);

        // =========================
        // Fetch Static Data
        // =========================
        $activeData = $this->courseFranchiseService->fetch_Active_Course_Franchise_Data();

        // =========================
        // Fetch Students
        // =========================
        $students = $this->studentService->getviewStudents($filters);

        // =========================
        // Final Response
        // =========================
        return $this->page(
            [
                'student_data'   => $students,
                'franchise_data' => $activeData['franchise'],
                'course_data'    => $activeData['course'],
                'page_type'      => 'student'
            ],
            'Student List',
            $assets,
            false,
            true
        );
    }
    // View student data methods ends here

    // Manage student data view methods start here
    public function manage_student_data_view($data)
    {
        $assets = Asset::load('manage_student_form');
        $type   = 'student';

        $studentId = !empty($data['id']) ? $data['id'] : null;
        $isUpdate  = !empty($studentId);

        // =========================
        // Permission
        // =========================
        $hasPermission = $this->studentService->resolveManageStudentViewPermission($isUpdate);

        if (!$hasPermission) {
            return $this->page(
                [
                    'student_data'   => [],
                    'franchise_data' => [],
                    'course_data'    => [],
                    'page_type'      => $type
                ],
                'Manage Student',
                $assets,
                false,
                false
            );
        }

        // =========================
        // Fetch Student (if update)
        // =========================
        $studentData = $isUpdate
            ? $this->studentService->getStudentDataWithAccessCheck($studentId)
            : [];

        // If access denied during ownership check
        if ($studentData === false) {
            return $this->page(
                [
                    'student_data'   => [],
                    'franchise_data' => [],
                    'course_data'    => [],
                    'page_type'      => $type
                ],
                'Manage Student',
                $assets,
                false,
                false
            );
        }

        // =========================
        // Common Data (Service)
        // =========================
        $activeData = $this->courseFranchiseService->fetch_Active_Course_Franchise_Data();

        // =========================
        // Final Response
        // =========================
        return $this->page(
            [
                'student_data'   => $studentData,
                'franchise_data' => $activeData['franchise'],
                'course_data'    => $activeData['course'],
                'page_type'      => $type
            ],
            'Manage Student',
            $assets,
            true,
            true
        );
    }
    // Manage student data view methods ends here

    // Manage student data methods start here
    public function manage_student($data)
    {
        $context = $this->studentService->buildStudentContext();

        $this->studentService->validateManageStudentAccess($context);

        $formData = $this->studentService->buildStudentFormData($context);

        $this->studentService->validateStudentData($formData, $context);

        $uploadResult = $this->studentService->handleStudentImageUpload($formData, $context);

        $result = $this->studentService->saveStudent($formData);

        return $this->studentService->handlePostSave($result, $formData, $context, $uploadResult);
    }
    // Manage student data methods ends here

    // Manage student admission data view methods start here
    public function manage_student_admission_data_view($data)
    {
        $assets = Asset::load('student_admission_list');
        $type   = 'student_admission';

        // =========================
        // Extract Inputs
        // =========================
        [$studentId, $tmpId, $actionType] = $this->studentService->extractAdmissionInputs($data);

        // =========================
        // Franchise Restriction
        // =========================
        if ($this->studentService->isRestrictedFranchise()) {
            return $this->page(
                [
                    'student_list' => [],
                    'page_type'    => $type
                ],
                'Student Admission',
                $assets,
                false,
                false
            );
        }

        // =========================
        // ACTION: MANAGE STUDENT
        // =========================
        if ($actionType === "manage_student") {
            return $this->handleManageStudentAdmission(
                $studentId,
                $tmpId,
                $assets,
                $type
            );
        }

        // =========================
        // ACTION: VIEW STUDENTS
        // =========================
        return $this->handleViewStudents($assets, $type);
    }

    private function handleManageStudentAdmission($studentId, $tmpId, $assets, $type)
    {
        $isUpdate = !empty($studentId);

        // =========================
        // Permission
        // =========================
        $hasPermission = $this->studentService->resolveAdmissionPermission($isUpdate);

        if (!$hasPermission) {
            return $this->page(
                [
                    'student_data'   => [],
                    'franchise_data' => [],
                    'course_data'    => [],
                    'category_data'  => [],
                    'page_type'      => $type
                ],
                'Manage Admission',
                $assets,
                false,
                false
            );
        }

        // =========================
        // Student Data
        // =========================
        $studentData = $this->studentService->resolveAdmissionStudentData($studentId, $tmpId);

        // =========================
        // Common Data (Service)
        // =========================
        $activeData = $this->courseFranchiseService
            ->fetch_Active_Course_Franchise_Data();

        $categoryData = $this->studentService
            ->fetchReceiptCategory('receipt');

        // =========================
        // Final Response
        // =========================
        return $this->page(
            [
                'student_data'   => $studentData,
                'franchise_data' => $activeData['franchise'],
                'course_data'    => $activeData['course'],
                'category_data'  => $categoryData,
                'page_type'      => $type
            ],
            'Manage Admission',
            $assets,
            false,
            true
        );
    }

    private function handleViewStudents($assets, $type)
    {
        $hasPermission = $this->permissionService
            ->checkUserRolePermission('view_student');

        if (!$hasPermission) {
            return $this->page(
                [
                    'student_list' => [],
                    'page_type'    => $type
                ],
                'Student Admission',
                $assets,
                false,
                false
            );
        }

        $filters = [];

        if ($_SESSION['user_type'] === 'franchise') {
            $filters['franchise_id'] = (int)$_SESSION['user_id'];
        }

        $students = $this->studentService->fetchFreshStudents($filters);

        return $this->page(
            [
                'student_list' => $students,
                'page_type'    => $type
            ],
            'Student Admission',
            $assets,
            false,
            true
        );
    }
    // Manage student admission data view methods ends here

    // Manage student admission data methods start here
    public function manage_student_admission($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $studentId = $post('student_id');
        $isUpdate  = !empty($studentId) && $studentId !== "null";

        // 1. Permission
        $permissionCheck = $this->studentService->validateAdmissionPermissions($isUpdate);
        if ($permissionCheck !== true) return $permissionCheck;

        // 2. Fetch student if update
        $studentDetail = $isUpdate
            ? $this->studentService->getStudentOrFail($studentId)
            : null;

        if (is_array($studentDetail)) return $studentDetail; // failure case

        // 3. Prepare form data
        $formData = $this->studentService->prepareAdmissionData($post, $isUpdate, $studentDetail);

        // 4. Save student
        $studentResult = $this->studentService->manageStudentAdmission($formData);

        if ($studentResult['check'] !== 'success') {
            return ['check' => 'failure', 'message' => "Something went wrong!"];
        }

        // 5. Handle receipt + rollback
        $receiptResult = $this->studentService->handleReceiptAndRollback(
            $studentResult,
            $formData,
            $post,
            $isUpdate
        );

        if ($receiptResult !== true) return $receiptResult;

        // 6. Temp conversion
        $this->studentService->handleTempConversion($post);

        // 7. Final response
        return $this->studentService->buildAdmissionResponse($studentResult, $formData, $post, $isUpdate);
    }
    // Manage student admission data methods ends here

    // Manage temp student admission data methods start here
    public function manage_temp_student($data) : Array
    {
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Basic Data
        // -----------------------------
        $formDataArr['id'] = $post('id');

        $isUpdate = !empty($formDataArr['id']) && $formDataArr['id'] != "null";
        $user_role_slug = $isUpdate ? 'update_student' : 'create_student';

        // -----------------------------
        // Permission Check
        // -----------------------------
        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Franchise Logic
        // -----------------------------
        if ($_SESSION['user_type'] == "franchise") {

            $franchise_id = $_SESSION['user_id'];

            if ($isUpdate) {
                $studentDetailArr = $this->studentService
                    ->fetchTempStudentData($formDataArr['id']);

                if ($studentDetailArr->franchise_id != $franchise_id) {
                    return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
                }
            }

            // kept for future use (as in your original code)
            // $franchiseDetailArr = $this->model
            //     ->fetch_Global_Single_Franchise($franchise_id);

            // $owned_status = $franchiseDetailArr->owned_status;
        } else {
            $franchise_id = $post('franchise_id');
        }

        // -----------------------------
        // Core Fields
        // -----------------------------
        $formDataArr['stu_name']        = $post('stu_name');
        $formDataArr['stu_father_name'] = $post('stu_father_name');
        $formDataArr['stu_phone']       = $post('stu_phone');
        $formDataArr['course_id']       = $post('course_id');
        $formDataArr['franchise_id']    = $franchise_id;

        $formDataArr['advanced_fees']   = $post('receipt_amount');

        // -----------------------------
        // Create Temp Student ID
        // -----------------------------
        if (!$isUpdate) {
            $formDataArr['tmp_stu_id'] = $this->studentService->create_Tmp_Student_ID();
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->studentService->saveTempStudent($formDataArr);

        // -----------------------------
        // Response Handling
        // -----------------------------
        if ($returnArr['check'] == 'success') {

            $returnArr['tmp_stu_id'] = $isUpdate
                ? $post('tmp_stu_id')
                : $formDataArr['tmp_stu_id'];

            $returnArr['course'] = $post('course_name');
        } else {
            $returnArr = ['check' => 'failure', 'message' => "Something went wrong!"];
        }

        return $returnArr;
    }
    // Manage temp student admission data methods ends here

    // Update student status methods start here
    public function change_student_status($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Determine Status Type & Permission
        // -----------------------------
        $status_type = $post('status_type');
        $user_role_slug = ($status_type === "status") ? 'update_student' : 'update_result';

        // -----------------------------
        // Permission Check
        // -----------------------------
        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $formDataArr['student_id'] = $post('stu_id');
        $formDataArr['status_type'] = $status_type;

        $statusData = $post('status_data');

        if ($status_type === "status") {
            $formDataArr['student_status'] = $statusData;
        } else {
            $formDataArr['stu_result'] = $statusData;
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->studentService
            ->saveStudentStatus($formDataArr);

        // -----------------------------
        // Response
        // -----------------------------
        return $returnArr;
    }

    public function update_student_bulk_status($data)
    {
        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = "update_student";

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $idData = $post('row_id');

        // -----------------------------
        // Prepare IDs
        // -----------------------------
        if (empty($idData)) {
            return ['check' => 'failure', 'message' => "You haven't selected any data!"];
        }

        $rowIdArr = array_map('trim', explode(',', $idData));
        $rowIdArr = array_filter(array_map('intval', (array)$rowIdArr));

        if (empty($rowIdArr)) {
            return ['check' => 'failure', 'message' => "Invalid data provided!"];
        }

        $paramArr = [
            'row_ids'       => $rowIdArr,
            'record_status' => $post('record_status'),
            'student_status' => $post('student_status'),
            'result_status' => $post('result_status')
        ];

        // -----------------------------
        // Process Bulk Update
        // -----------------------------
        $response = $this->studentService->saveBulkStudentStatus($paramArr);

        return $response['responseArr']['check'] === 'success'
            ? ['check' => 'success', 'message' => 'Bulk update successful']
            : ['check' => 'failure', 'message' => 'Bulk update failed'];
    }
    // Update student status methods ends here

    // Fetch student data methods starts here
    public function fetch_student_detail_modal($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // PERMISSION CHECK
        // -----------------------------
        if (!$this->permissionService->checkUserRolePermission("view_student", "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // FETCH STUDENT
        // -----------------------------
        $student_id = (int) $post('student_id');

        $student = $this->studentService
            ->fetchStudentDetails($student_id);

        if (empty($student)) {
            return ['check' => 'failure', 'message' => "Student not found!"];
        }

        // -----------------------------
        // FRANCHISE ACCESS CONTROL
        // -----------------------------
        if (
            $_SESSION['user_type'] === 'franchise' &&
            $student->franchise_id != $_SESSION['user_id']
        ) {
            return ['check' => 'failure', 'message' => "You don't have permission to view this student!"];
        }

        // -----------------------------
        // IMAGE HANDLING
        // -----------------------------
        $imagePath = USER_UPLOAD_DIR . 'student/' . $student->image_file_name;

        $student->student_dp = (!empty($student->image_file_name) && file_exists($imagePath))
            ? USER_UPLOAD_URL . 'student/' . $student->image_file_name
            : 'https://source.unsplash.com/600x300/?student';

        // -----------------------------
        // DATA FORMATTING
        // -----------------------------
        $student->stu_dob = !empty($student->stu_dob)
            ? date('jS F, Y', strtotime($student->stu_dob))
            : null;

        $student->advance_fees_date = !empty($student->advance_fees_date)
            ? date('jS F, Y', strtotime($student->advance_fees_date))
            : null;

        $student->stu_result = ucfirst((string)$student->stu_result);
        $student->stu_gender = ucfirst((string)$student->stu_gender);
        $student->stu_marital_status = ucfirst((string)$student->stu_marital_status);

        $student->student_status = ($student->student_status === 'course_complete')
            ? 'Course Complete'
            : ucfirst((string)$student->student_status);

        // -----------------------------
        // DEFAULT VALUES
        // -----------------------------
        $student->course_default_fees = 0;

        // -----------------------------
        // RESPONSE
        // -----------------------------
        return [
            'check' => 'success',
            'studentDetail'  => $student
        ];
    }
    // Fetch student data methods ends here
}
