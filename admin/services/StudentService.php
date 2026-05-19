<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class StudentService
{
    public function __construct(
        private GlobalInterfaceModel $model,
        private GlobalLibraryHandler $lib,
        private PermissionService $permissionService,
        private CacheService $cacheService,
        private StudentReceiptService $studentReceiptService,
        private GlobalValidationController $validator
    ) {}

    /*
    |--------------------------------------------------------------------------
    | View student data helper methods
    |--------------------------------------------------------------------------
    */
    public function prepareStudentFilters($data)
    {
        $filters = [
            'record_status' => $data['record_status'] ?? 'active',
            'pageNo'        => isset($data['pageNo']) ? (int)$data['pageNo'] : 1,
            'limit'         => 50
        ];

        // optional filters
        foreach ([
            'student_status',
            'result_status',
            'verified_status'
        ] as $field) {
            if (!empty($data[$field])) {
                $filters[$field] = $this->lib->get($field);
            }
        }

        // course
        if (!empty($data['course_id']) && (int)$data['course_id'] > 0) {
            $filters['course_id'] = (int)$data['course_id'];
        }

        // franchise logic
        if ($_SESSION['user_type'] === 'franchise') {
            $filters['franchise_id'] = (int)$_SESSION['user_id'];
        } elseif (!empty($data['franchise_id']) && (int)$data['franchise_id'] > 0) {
            $filters['franchise_id'] = (int)$data['franchise_id'];
        }

        // search
        if (!empty($data['search_string'])) {
            $filters['search_string'] = trim($data['search_string']);
        }

        // date filters
        foreach (['created', 'search_start', 'search_end'] as $field) {
            if (!empty($data[$field])) {
                $filters[$field] = $this->lib->formatDateDB($data[$field]);
            }
        }

        return $filters;
    }

    public function getviewStudents($filters)
    {
        return  $this->model->fetch_Global_Student($filters);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage student data view helper methods
    |--------------------------------------------------------------------------
    */
    public function resolveManageStudentViewPermission($isUpdate)
    {
        $slug = $isUpdate ? 'update_student' : 'create_student';

        return $this->permissionService->checkUserRolePermission($slug);
    }

    public function getStudentDataWithAccessCheck($studentId)
    {
        $student = $this->model
            ->fetch_Global_Single_Student($studentId);

        if (empty($student)) {
            return [];
        }

        // franchise ownership check
        if ($_SESSION['user_type'] === "franchise") {
            if ($student->franchise_id != $_SESSION['user_id']) {
                return false; // signal permission denied
            }
        }

        return $student;
    }

    /*
    |--------------------------------------------------------------------------
    | Manage student data helper methods
    |--------------------------------------------------------------------------
    */
    public function create_Student_ID()
    {
        //Creating new Student id method
        $stuIdDetail = $this->model->fetch_Last_Student_Detail();
        $lst_stu_id = $stuIdDetail['lst_stu_id'];

        if (!empty($lst_stu_id)) {
            $lst_stu_id_part_2 = substr($lst_stu_id, 10);
            $nxt_stu_id = round($lst_stu_id_part_2 + 1);
        } else {
            $lst_stu_id_part_2 = 1;
            $nxt_stu_id = $lst_stu_id_part_2;
        }

        $current_stu_id = "WBTAIMGCSM" . $nxt_stu_id;

        return $current_stu_id;
    }

    public function buildStudentContext()
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $stuId = $post('stu_row_id');
        $isUpdate = !empty($stuId) && $stuId !== "null";

        $student = $isUpdate
            ? $this->model->fetch_Detail_Single_Student($stuId)
            : null;

        if ($isUpdate && empty($student)) {
            return $this->lib->fail("Student not found");
        }

        $userType = $_SESSION['user_type'];
        $userId   = $_SESSION['user_id'];

        $ownedStatus = "yes";

        if ($userType === "franchise") {
            $franchise = $this->model->fetch_Global_Single_Franchise($userId);
            $ownedStatus = $franchise->owned_status;

            if ($isUpdate && $student->franchise_id != $userId) {
                return $this->lib->fail("You don't have the permission to perform this action!");
            }
        }

        return [
            'post'         => $post,
            'stu_id'       => $stuId,
            'isUpdate'     => $isUpdate,
            'student'      => $student,
            'userType'     => $userType,
            'userId'       => $userId,
            'ownedStatus'  => $ownedStatus,
            'actionType'   => $post('action_type'),
            'roleSlug'     => $isUpdate ? 'update_student' : 'create_student'
        ];
    }

    public function validateManageStudentAccess($context)
    {
        if (isset($context['check']) && $context['check'] === 'failure') {
            return $context;
        }

        if (!$this->permissionService->checkUserRolePermission($context['roleSlug'], "hard")) {
            return $this->lib->fail("You don't have the permission to perform this action!");
        }
    }

    public function buildStudentFormData($context)
    {
        $formData = $this->mapStudentCommonFields($context);

        if ($context['userType'] === 'franchise') {
            return $this->handleFranchiseStudent($formData, $context);
        }

        return $this->handleAdminStudent($formData, $context);
    }

    public function mapStudentCommonFields($context)
    {
        $post = $context['post'];

        $formDataArr = [
            'stu_row_id'         => $context['stu_id'],
            'stu_name'           => $post('stu_name'),
            'stu_father_name'    => $post('stu_father_name'),
            'stu_phone'          => $post('stu_phone'),
            'stu_email'          => $post('stu_email'),
            'stu_address'        => $post('stu_address'),
            'course_id'          => $post('course_id'),
            'stu_qualification'  => $post('stu_qualification'),
            'stu_gender'         => $post('stu_gender') ?: 'none',
            'stu_marital_status' => $post('stu_marital_status') ?: 'none',
            'stu_notes'          => $post('stu_notes'),
            'stu_dob'            => $this->lib->formatDateDB($post('stu_dob')),
        ];

        foreach (['stu_course_fees', 'monthly_course_fees', 'month_exclude_receipt', 'stu_course_discount', 'fees_paid_before_dr'] as $f) {
            $formDataArr[$f] = $post($f);
        }

        return $formDataArr;
    }

    public function handleFranchiseStudent($formData, $context)
    {
        $formData['franchise_id'] = $context['userId'];

        if ($context['ownedStatus'] === "no") {
            return $this->handleRestrictedFranchise($formData, $context);
        }

        return $this->handleFlexibleFranchise($formData, $context);
    }

    private function handleRestrictedFranchise($formData, $context)
    {
        $student  = $context['student'];
        $isUpdate = $context['isUpdate'];

        if ($isUpdate) {
            // Preserve existing locked fields
            foreach (['student_status', 'record_status', 'stu_result', 'conversion_status'] as $field) {
                $formData[$field] = $student->$field;
            }
        } else {
            // Default values for new student
            $formData += [
                'student_status'     => "admitted",
                'record_status'      => "blocked",
                'stu_result'         => "unqualified",
                'conversion_status'  => 'n',
                'stu_id'             => $this->create_Student_ID()
            ];
        }

        // Restricted financial fields → always null
        foreach ([
            'stu_course_fees',
            'monthly_course_fees',
            'month_exclude_receipt',
            'fees_paid_before_dr'
        ] as $field) {
            $formData[$field] = null;
        }

        return $formData;
    }

    private function handleFlexibleFranchise($formData, $context)
    {
        $post     = $context['post'];
        $student  = $context['student'];
        $isUpdate = $context['isUpdate'];

        // Status fields (fallback logic)
        $formData['student_status'] = $post('student_status')
            ?? ($isUpdate ? $student->student_status : 'admitted');

        $formData['conversion_status'] = $post('conversion_status')
            ?? ($isUpdate ? $student->conversion_status : 'n');

        $formData['stu_result'] = $post('stu_result')
            ?? ($isUpdate ? $student->stu_result : 'unqualified');

        $formData['record_status'] = $post('record_status')
            ?? ($isUpdate ? $student->record_status : 'active');

        // Generate student ID only on create
        if (!$isUpdate) {
            $formData['stu_id'] = $this->create_Student_ID();
        }

        // Financial + flexible fields
        foreach ([
            'stu_course_fees',
            'monthly_course_fees',
            'month_exclude_receipt',
            'stu_course_discount',
            'fees_paid_before_dr'
        ] as $field) {
            $formData[$field] = $post($field);
        }

        return $formData;
    }

    public function handleAdminStudent($formData, $context)
    {
        $post = $context['post'];
        $student = $context['student'];

        $formData['franchise_id'] = $post('franchise_id');

        return $this->applyStatusFields($formData, $post, $student, $context['isUpdate']);
    }

    public function validateStudentData($formData, $context)
    {
        $validationData = $formData;
        $validationData['fran_own_status'] = $context['ownedStatus'];

        $result = $this->validator->validateGlobalStudentData($validationData);

        if ($result['check'] === 'failure') {
            return $result;
        }
    }

    private function handleImageFallback(&$formData, $context)
    {
        $post       = $context['post'];
        $isUpdate   = $context['isUpdate'];
        $actionType = $context['actionType'];
        $dir        = 'student';

        $oldFile = $post('hidden_stu_image');

        // ===== UPDATE CASE =====
        if ($isUpdate) {
            $formData['image_file_name'] = $oldFile ?: null;
            return ['check' => 'skip'];
        }

        // ===== CLONE CASE =====
        if ($actionType === "clone") {

            if (!empty($oldFile)) {

                $ext = pathinfo($oldFile, PATHINFO_EXTENSION);
                $newFileName = $this->lib->generateRandomString() . '_' . time() . '.' . $ext;

                $source = USER_UPLOAD_DIR . $dir . '/' . $oldFile;
                $dest   = USER_UPLOAD_DIR . $dir . '/' . $newFileName;

                if (file_exists($source) && copy($source, $dest)) {
                    $formData['image_file_name'] = $newFileName;
                } else {
                    $formData['image_file_name'] = null;
                }
            } else {
                $formData['image_file_name'] = null;
            }

            return ['check' => 'success']; // cloning treated as success
        }

        // ===== CREATE CASE =====
        $formData['image_file_name'] = null;

        return ['check' => 'skip'];
    }

    public function handleStudentImageUpload(&$formData, $context)
    {
        $dir = 'student';
        $post = $context['post'];

        if (!empty($_FILES["local_stu_image"]["size"])) {

            $upload = $this->lib->upload_file('local_stu_image', $dir);

            if ($upload['check'] !== 'success') {
                return $this->lib->fail("Image upload failed!");
            }

            $formData['image_file_name'] = $upload['fileName'];
            return $upload;
        }

        return $this->handleImageFallback($formData, $context);
    }

    public function saveStudent($formData)
    {
        return $this->model->manage_Global_Student($formData);
    }

    public function handlePostSave($result, $formData, $context, $upload)
    {
        if ($result['check'] !== 'success') {
            $this->rollbackImage($upload);
            return $this->lib->fail("Something went wrong!");
        }

        if (!$context['isUpdate']) {
            $this->cacheService->purgeSiteCache("student");
        }

        $result['stu_id'] = $context['isUpdate']
            ? $context['post']('stu_id')
            : $formData['stu_id'];

        $result['course'] = $context['post']('course_name');

        return $result;
    }

    private function applyStatusFields($formData, $post, $student, $isUpdate)
    {
        // Use fallback if value is null OR empty string
        $getValue = function ($field, $default) use ($post) {
            $value = $post($field);
            return ($value !== null && $value !== '') ? $value : $default;
        };

        $formData['student_status'] = $getValue(
            'student_status',
            $isUpdate ? $student->student_status : 'admitted'
        );

        $formData['conversion_status'] = $getValue(
            'conversion_status',
            $isUpdate ? $student->conversion_status : 'n'
        );

        $formData['stu_result'] = $getValue(
            'stu_result',
            $isUpdate ? $student->stu_result : 'unqualified'
        );

        $formData['record_status'] = $getValue(
            'record_status',
            $isUpdate ? $student->record_status : 'active'
        );

        return $formData;
    }

    private function rollbackImage($uploadResult)
    {
        if (
            empty($uploadResult) ||
            !isset($uploadResult['check']) ||
            $uploadResult['check'] !== 'success'
        ) {
            return;
        }

        if (empty($uploadResult['fileName'])) {
            return;
        }

        $filePath = USER_UPLOAD_DIR . 'student/' . $uploadResult['fileName'];

        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Manage student admission data view helper methods
    |--------------------------------------------------------------------------
    */
    public function extractAdmissionInputs($data)
    {
        $studentId = !empty($data['student_id']) ? (int)$data['student_id'] : null;
        $tmpId     = !empty($data['id']) ? trim($data['id']) : null;
        $action    = $data['actionType'] ?? null;

        return [$studentId, $tmpId, $action];
    }

    public function isRestrictedFranchise()
    {
        return $_SESSION['user_type'] === 'franchise'
            && $_SESSION['owned_status'] === 'no';
    }

    public function resolveAdmissionPermission($isUpdate)
    {
        $slug = $isUpdate ? 'update_student' : 'create_student';

        return $this->permissionService->checkUserRolePermission($slug);
    }

    public function fetchReceiptCategory($category)
    {
        return  $this->model->fetch_Single_Parent_Category($category);
    }

    public function resolveAdmissionStudentData($studentId, $tmpId)
    {
        if (!empty($studentId)) {
            return $this->model
                ->fetch_Global_Single_Student($studentId) ?: [];
        }

        if (!empty($tmpId)) {
            return $this->model
                ->fetch_Tmp_Single_Student($tmpId) ?: [];
        }

        return [];
    }

    public function fetchFreshStudents($filters)
    {
        return  $this->model->fetch_Fresh_Students($filters);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage student admission data helper methods
    |--------------------------------------------------------------------------
    */
    public function validateAdmissionPermissions($isUpdate)
    {
        $studentSlug = $isUpdate ? 'update_student' : 'create_student';

        if (!$this->permissionService->checkUserRolePermission($studentSlug, "hard")) {
            return ['check' => 'failure', 'message' => "No permission"];
        }

        if (!$this->permissionService->checkUserRolePermission('create_receipt', "hard")) {
            return ['check' => 'failure', 'message' => "No permission to create receipt"];
        }

        return true;
    }

    public function getStudentOrFail($studentId)
    {
        $student = $this->model->fetch_Detail_Single_Student($studentId);

        if (empty($student)) {
            return ['check' => 'failure', 'message' => 'Student not found'];
        }

        return $student;
    }

    public function prepareAdmissionData($post, $isUpdate, $studentDetail)
    {
        $data = [];

        // basic
        $data['student_id'] = $post('student_id');
        $data['stu_name']   = $post('stu_name');
        $data['stu_father_name'] = $post('stu_father_name');
        $data['stu_phone']  = $post('stu_phone');
        $data['course_id']  = $post('course_id');

        // fees
        $data['stu_course_fees']     = $post('stu_course_fees');
        $data['monthly_course_fees'] = $post('monthly_course_fees');
        $data['stu_course_discount'] = $post('stu_course_discount');
        $data['fees_paid_before_dr'] = $post('fees_paid_before_dr');

        // franchise logic
        [$franchiseId, $verifiedStatus] = $this->resolveFranchiseLogic(
            $post,
            $isUpdate,
            $studentDetail,
            $data
        );

        $data['franchise_id']   = $franchiseId;
        $data['verified_status'] = $verifiedStatus;

        // status
        $data['student_status'] = "admitted";
        $data['record_status']  = "active";

        // new student
        if (!$isUpdate) {
            $data['stu_id'] = $this->create_Student_ID();
        }

        // temp
        $data['tmp_stu_record_id'] = $post('tmp_stu_record_id') ?: null;

        return $data;
    }

    public function manageStudentAdmission($formattedPayload)
    {
        return $this->model->manage_Student_Admission($formattedPayload);
    }

    private function resolveFranchiseLogic($post, $isUpdate, $studentDetail, $data)
    {
        if ($_SESSION['user_type'] == "franchise" && $_SESSION['owned_status'] == "yes") {

            $franchiseId = $_SESSION['user_id'];

            if ($isUpdate && $studentDetail->franchise_id != $franchiseId) {
                return [null, null]; // will fail later if needed
            }

            $verified = $isUpdate &&
                ($data['stu_course_discount'] != $studentDetail->stu_course_discount)
                ? 'n'
                : ($studentDetail->verified_status ?? 'y');
        } else {
            $franchiseId = $post('franchise_id');
            $verified    = $isUpdate ? 'y' : 'y';
        }

        return [$franchiseId, $verified];
    }

    public function handleReceiptAndRollback($studentResult, $formData, $post, $isUpdate)
    {
        $amount = (float) $post('receipt_amount');

        if (
            $studentResult['last_insert_id'] > 0 &&
            !$isUpdate &&
            $amount > 0
        ) {
            $receipt = $this->createAdmissionReceipt($formData, $post);

            if ($receipt['check'] === 'failure') {

                $this->model->delete_Student_By_Id($formData['stu_id']);

                return [
                    'check'   => 'failure',
                    'message' => 'Student created but receipt failed. Rolled back.'
                ];
            }
        }

        return true;
    }

    public function handleTempConversion($post)
    {
        $id = $post('id');

        if (!empty($id)) {
            $this->model->update_Tmp_Student_Conversion_Status($id, 'y');
        }
    }

    public function buildAdmissionResponse($studentResult, $formData, $post, $isUpdate)
    {
        return [
            ...$studentResult,
            'stu_id' => $isUpdate ? $post('stu_id') : $formData['stu_id'],
            'course' => $post('course_name')
        ];
    }

    private function createAdmissionReceipt($formDataArr, $post)
    {
        $receipt_role_slug = 'create_receipt';

        // permission check
        if (!$this->permissionService->checkUserRolePermission($receipt_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "No permission to create receipt"];
        }

        $receiptAmount = (float) $post('receipt_amount');

        if ($receiptAmount <= 0) {
            return ['check' => 'skip']; // nothing to do
        }

        $receiptFormArr = [
            'receipt_id'             => $this->studentReceiptService->create_Receipt_ID(),
            'stu_id'                 => $formDataArr['stu_id'],
            'category_id'            => $post('category_id'),
            'receipt_amount'         => $receiptAmount,
            'extra_fees'             => $post('extra_fees'),
            'extra_fees_description' => "Registration Fees",
            'record_status'          => 'active'
        ];

        return $this->model
            ->create_Student_Admission_Receipt($receiptFormArr);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage temp student admission data view methods
    |--------------------------------------------------------------------------
    */
    public function fetchTmpSingleStudent($id)
    {
        return $this->model->fetch_Tmp_Single_Student($id);
    }

    public function prepareTempStudentFilters($data)
    {
        $filters = [
            'record_status' => $data['record_status'] ?? 'active',
            'pageNo'        => !empty($data['pageNo'])
                ? (int)$data['pageNo']
                : 1,
            'limit'         => 20
        ];

        // =========================
        // Franchise Filter
        // =========================
        if ($_SESSION['user_type'] == 'franchise') {

            $filters['franchise_id'] =
                (int)$_SESSION['user_id'];
        } elseif (!empty($data['franchise_id'])) {

            $filters['franchise_id'] =
                (int)$data['franchise_id'];
        }

        // =========================
        // Course Filter
        // =========================
        if (!empty($data['course_id'])) {

            $filters['course_id'] =
                (int)$data['course_id'];
        }

        // =========================
        // Search
        // =========================
        if (!empty($data['search_string'])) {

            $filters['search_string'] =
                trim($data['search_string']);
        }

        // =========================
        // Date Filters
        // =========================
        foreach (['created', 'search_start', 'search_end']
            as $field) {

            if (!empty($data[$field])) {

                $filters[$field] =
                    $this->lib->formatDateDB($data[$field]);
            }
        }

        // =========================
        // Status Filters
        // =========================
        if (!empty($data['verified_status'])) {

            $filters['verified_status'] =
                $data['verified_status'];
        }

        // =========================
        // Conversion Status
        // =========================
        if (!empty($data['conversion_status'])) {

            $filters['conversion_status'] =
                $data['conversion_status'];
        } else {

            $hasFilters =
                !empty($data['verified_status']) ||
                !empty($data['course_id']) ||
                !empty($data['search_string']) ||
                !empty($data['created']) ||
                !empty($data['search_start']) ||
                !empty($data['search_end']) ||
                ($_SESSION['user_type'] !== 'franchise'
                    &&
                    !empty($data['franchise_id'])
                );

            $filters['conversion_status'] =
                $hasFilters ? null : 'n';
        }

        return $filters;
    }

    public function fetchTmpStudents($filters)
    {
        return $this->model->fetch_Tmp_Students($filters);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage temp student admission data helper methods
    |--------------------------------------------------------------------------
    */
    public function fetchTempStudentDetails($id)
    {
        return $this->model->fetch_Detail_Single_Student($id);
    }

    public function create_Tmp_Student_ID($min = 999, $max = 999999, $quantity = 1)
    {
        $numbers = range($min, $max);
        shuffle($numbers);
        $randomNumArr = array_slice($numbers, 0, $quantity);

        return "TMPSTUDENT" . $randomNumArr[0];
    }

    public function saveTempStudent($formDataArr)
    {
        return $this->model->manage_Temp_Student($formDataArr);
    }

    /*
    |--------------------------------------------------------------------------
    | Update student status helper methods
    |--------------------------------------------------------------------------
    */
    public function saveStudentStatus($formDataArr)
    {
        return $this->model->manage_Student_Status($formDataArr);
    }

    public function saveBulkStudentStatus($formDataArr)
    {
        return $this->model->update_Bulk_Student_Status($formDataArr);
    }

    /*
    |--------------------------------------------------------------------------
    | Fetch student data modal helper methods
    |--------------------------------------------------------------------------
    */
    public function fetchStudentDetails($id)
    {
        return $this->model->fetch_Global_Single_Student($id);
    }
}
