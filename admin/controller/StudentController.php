<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class StudentController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function manage_student($data)
    {
        $formDataArr = [];
        $validationDataArr = [];
        $returnArr = [];
        $dir = 'student';

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $action_type = $post('action_type');
        $formDataArr['stu_row_id'] = $post('stu_row_id');

        $isUpdate = !empty($formDataArr['stu_row_id']) && $formDataArr['stu_row_id'] != "null";
        $user_role_slug = $isUpdate ? 'update_student' : 'create_student';

        // fetch student if update
        if ($isUpdate) {
            $studentDetailArr = $this->interface->fetch_Detail_Single_Student($formDataArr['stu_row_id']);
        }

        // check for valid student data
        if ($isUpdate && empty($studentDetailArr)) {
            return ['check' => 'failure', 'message' => 'Student not found'];
        }

        // franchise check
        if ($_SESSION['user_type'] == "franchise") {

            $franchise_id = $_SESSION['user_id'];

            if ($isUpdate && $studentDetailArr->franchise_id != $franchise_id) {
                return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
            }

            $franchiseDetailArr = $this->interface->fetch_Global_Single_Franchise($franchise_id);
            $owned_status = $franchiseDetailArr->owned_status;
        } else {
            $owned_status = "yes";
        }

        // permission check
        if (!$this->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // ===== COMMON FIELDS =====
        $formDataArr['stu_name'] = $post('stu_name');
        $formDataArr['stu_father_name'] = $post('stu_father_name');
        $formDataArr['stu_phone'] = $post('stu_phone');
        $formDataArr['stu_email'] = $post('stu_email');
        $formDataArr['stu_address'] = $post('stu_address');
        $formDataArr['course_id'] = $post('course_id');
        $formDataArr['stu_qualification'] = $post('stu_qualification');

        $formDataArr['stu_gender'] = $post('stu_gender') ?: 'none';
        $formDataArr['stu_marital_status'] = $post('stu_marital_status') ?: 'none';

        // ===== USER TYPE LOGIC =====
        if ($_SESSION['user_type'] == 'franchise') {

            $formDataArr['franchise_id'] = $_SESSION['user_id'];

            if ($owned_status == "no") {

                if ($isUpdate) {
                    foreach (['student_status', 'record_status', 'stu_result', 'conversion_status'] as $f) {
                        $formDataArr[$f] = $studentDetailArr->$f;
                    }
                } else {
                    $formDataArr += [
                        'student_status' => "admitted",
                        'record_status' => "blocked",
                        'stu_result' => "unqualified",
                        'conversion_status' => 'n',
                        'stu_id' => $this->create_Student_ID()
                    ];
                }

                // restricted fields
                foreach (['stu_course_fees', 'monthly_course_fees', 'month_exclude_receipt', 'fees_paid_before_dr'] as $f) {
                    $formDataArr[$f] = null;
                }
            } else {

                // flexible franchise
                $formDataArr['student_status'] = $post('student_status') ?? ($isUpdate ? $studentDetailArr->student_status : 'admitted');
                $formDataArr['conversion_status'] = $post('conversion_status') ?? ($isUpdate ? $studentDetailArr->conversion_status : 'n');
                $formDataArr['stu_result'] = $post('stu_result') ?? ($isUpdate ? $studentDetailArr->stu_result : 'unqualified');
                $formDataArr['record_status'] = $post('record_status') ?? ($isUpdate ? $studentDetailArr->record_status : 'active');

                if (!$isUpdate) {
                    $formDataArr['stu_id'] = $this->create_Student_ID();
                }

                foreach (['stu_course_fees', 'monthly_course_fees', 'month_exclude_receipt', 'stu_course_discount', 'fees_paid_before_dr'] as $f) {
                    $formDataArr[$f] = $post($f);
                }
            }
        } else {

            // admin
            $formDataArr['franchise_id'] = $post('franchise_id');

            $formDataArr['student_status'] = $post('student_status') ?? ($isUpdate ? $studentDetailArr->student_status : 'admitted');
            $formDataArr['conversion_status'] = $post('conversion_status') ?? ($isUpdate ? $studentDetailArr->conversion_status : null);
            $formDataArr['stu_result'] = $post('stu_result') ?? ($isUpdate ? $studentDetailArr->stu_result : 'unqualified');
            $formDataArr['record_status'] = $post('record_status') ?? ($isUpdate ? $studentDetailArr->record_status : 'active');

            if (!$isUpdate) {
                $formDataArr['stu_id'] = $this->create_Student_ID();
            }

            foreach (['stu_course_fees', 'monthly_course_fees', 'month_exclude_receipt', 'stu_course_discount', 'fees_paid_before_dr'] as $f) {
                $formDataArr[$f] = $post($f);
            }
        }

        if ($isUpdate) {

            if (
                isset($formDataArr['stu_course_discount'], $formDataArr['fees_paid_before_dr']) &&
                ($formDataArr['stu_course_discount'] != $studentDetailArr->stu_course_discount ||
                    $formDataArr['fees_paid_before_dr'] != $studentDetailArr->fees_paid_before_dr
                )
            ) {
                $formDataArr['verified_status'] = 'n';
            } else {
                $formDataArr['verified_status'] = $studentDetailArr->verified_status;
            }
        }

        // ===== DATE =====
        $dobInput = $post('stu_dob');

        if (!empty($dobInput)) {
            $dob = str_replace('/', '-', $dobInput);
            $formDataArr['stu_dob'] = date('Y-m-d', strtotime($dob));
        } else {
            $formDataArr['stu_dob'] = null;
        }

        $formDataArr['stu_notes'] = $post('stu_notes');

        // ===== VALIDATE STUDENT DATA BEFORE CREATE OR UPDATE =====
        $validationDataArr = $formDataArr;
        $validationDataArr['fran_own_status'] = $owned_status;
        $validationResult = $this->validator->validateGlobalStudentData($validationDataArr);

        if ($validationResult['check'] == 'failure') {
            return $validationResult;
        }

        // ===== FILE UPLOAD =====
        $uploadReturnArr = ['check' => 'skip'];

        if (!empty($_FILES["local_stu_image"]["size"])) {

            $uploadReturnArr = $this->lib->upload_file('local_stu_image', $dir);

            if ($uploadReturnArr['check'] != 'success') {
                return ['check' => 'failure', 'msg' => "Image upload failed!"];
            }

            $formDataArr['image_file_name'] = $uploadReturnArr['fileName'];
        } else {
            if ($isUpdate) {
                $formDataArr['image_file_name'] = $post('hidden_stu_image');
            } elseif ($action_type == "clone") {

                $oldFile = $post('hidden_stu_image');

                if (!empty($oldFile)) {

                    $ext = pathinfo($oldFile, PATHINFO_EXTENSION);
                    $newFileName = $this->lib->generateRandomString() . '_' . time() . '.' . $ext;

                    $source = USER_UPLOAD_DIR . $dir . '/' . $oldFile;
                    $dest   = USER_UPLOAD_DIR . $dir . '/' . $newFileName;

                    if (file_exists($source) && copy($source, $dest)) {
                        $formDataArr['image_file_name'] = $newFileName;
                    } else {
                        $formDataArr['image_file_name'] = null;
                    }
                } else {
                    $formDataArr['image_file_name'] = null;
                }
            } else {
                $formDataArr['image_file_name'] = null;
            }
        }

        // ===== SAVE =====
        $returnArr = $this->interface->manage_Global_Student($formDataArr);

        if ($returnArr['check'] == 'success') {

            // delete old image
            if (
                $isUpdate &&
                $action_type != "clone" &&
                $uploadReturnArr['check'] == 'success' &&
                !empty($_FILES["local_stu_image"]["size"])
            ) {
                $filePath = USER_UPLOAD_DIR . $dir . '/' . $post('hidden_stu_image');
                if (!empty($hiddenImage) && file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            $returnArr['stu_id'] = $isUpdate ? $post('stu_id') : $formDataArr['stu_id'];
            $returnArr['course'] = $post('course_name');

            if (!$isUpdate) {
                $this->purgeSiteCache("student");
            }
        } else {

            if ($uploadReturnArr['check'] == 'success') {
                $filePath = USER_UPLOAD_DIR . $dir . '/' . $post('image_file_name');
                if (!empty($hiddenImage) && file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            return ['check' => 'failure', 'message' => "Something went wrong!"];
        }

        return $returnArr;
    }

    public function manage_student_admission($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $studentReturnArr = [];
        $receiptReturnArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Basic Data
        // -----------------------------
        $formDataArr['student_id'] = $post('student_id');

        $isUpdate = !empty($formDataArr['student_id']) && $formDataArr['student_id'] != "null";
        $user_role_slug = $isUpdate ? 'update_student' : 'create_student';
        $receipt_role_slug = 'create_receipt';

        // -----------------------------
        // Permission Check
        // -----------------------------
        if (!$this->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // permission check
        if (!$this->checkUserRolePermission($receipt_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "No permission to create receipt"];
        }

        // -----------------------------
        // Fetch Student (if update)
        // -----------------------------
        if ($isUpdate) {
            $studentDetailArr = $this->interface
                ->fetch_Detail_Single_Student($formDataArr['student_id']);
        }

        // check for valid student data
        if ($isUpdate && empty($studentDetailArr)) {
            return ['check' => 'failure', 'message' => 'Student not found'];
        }

        // -----------------------------
        // Fees & Franchise Logic
        // -----------------------------
        $formDataArr['stu_course_fees']     = $post('stu_course_fees');
        $formDataArr['monthly_course_fees'] = $post('monthly_course_fees');
        $formDataArr['stu_course_discount'] = $post('stu_course_discount');
        $formDataArr['fees_paid_before_dr'] = $post('fees_paid_before_dr');

        if ($_SESSION['user_type'] == "franchise" && $_SESSION['owned_status'] == "yes") {

            $franchise_id = $_SESSION['user_id'];

            if ($isUpdate && $studentDetailArr->franchise_id != $franchise_id) {
                return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
            }

            if ($isUpdate) {
                $formDataArr['verified_status'] =
                    ($formDataArr['stu_course_discount'] != $studentDetailArr->stu_course_discount)
                    ? 'n'
                    : $studentDetailArr->verified_status;
            }
        } else {

            $franchise_id = $post('franchise_id');

            if ($isUpdate) {
                $formDataArr['verified_status'] = 'y';
            }
        }

        // -----------------------------
        // Basic Fields
        // -----------------------------
        $formDataArr['stu_name']         = $post('stu_name');
        $formDataArr['stu_father_name']  = $post('stu_father_name');
        $formDataArr['stu_phone']        = $post('stu_phone');
        $formDataArr['course_id']        = $post('course_id');
        $formDataArr['franchise_id']     = $franchise_id;

        $formDataArr['student_status']   = "admitted";
        $formDataArr['record_status']    = "active";

        // -----------------------------
        // Create Student ID
        // -----------------------------
        if (!$isUpdate) {
            $formDataArr['stu_id'] = $this->create_Student_ID();
        }

        // -----------------------------
        // Temp Record
        // -----------------------------
        $formDataArr['tmp_stu_record_id'] = $post('tmp_stu_record_id') ?: null;

        // -----------------------------
        // Save Student
        // -----------------------------
        $studentReturnArr = $this->interface->manage_Student_Admission($formDataArr);

        // -----------------------------
        // Receipt Creation + Rollback
        // -----------------------------
        $receiptAmount = (float) $post('receipt_amount');
        $receiptResult = ['check' => 'skip'];

        if (
            $studentReturnArr['check'] == 'success' &&
            $studentReturnArr['last_insert_id'] > 0 &&
            !$isUpdate && $receiptAmount > 0
        ) {
            $receiptResult = $this->createAdmissionReceipt($formDataArr, $post);

            // ROLLBACK if receipt fails
            if ($receiptResult['check'] === 'failure') {

                // delete created student (rollback)
                $this->interface
                    ->delete_Student_By_Id($formDataArr['stu_id']);

                return [
                    'check'   => 'failure',
                    'message' => 'Student created but receipt failed. Operation rolled back.'
                ];
            }
        }

        // -----------------------------
        // Temp Conversion Update
        // -----------------------------
        $tmp_id = $post('tmp_id');

        if ($studentReturnArr['check'] == 'success' && !empty($tmp_id)) {
            $this->interface->update_Tmp_Student_Conversion_Status($tmp_id, 'y');
        }

        // -----------------------------
        // Final Response
        // -----------------------------
        if ($studentReturnArr['check'] == 'success') {

            $returnArr = $studentReturnArr;

            $returnArr['stu_id'] = $isUpdate
                ? $post('stu_id')
                : $formDataArr['stu_id'];

            $returnArr['course'] = $post('course_name');
        } else {
            $returnArr = ['check' => 'failure', 'message' => "Something went wrong!"];
        }

        return $returnArr;
    }

    private function createAdmissionReceipt($formDataArr, $post)
    {
        $receipt_role_slug = 'create_receipt';

        // permission check
        if (!$this->checkUserRolePermission($receipt_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "No permission to create receipt"];
        }

        $receiptAmount = (float) $post('receipt_amount');

        if ($receiptAmount <= 0) {
            return ['check' => 'skip']; // nothing to do
        }

        $receiptFormArr = [
            'receipt_id'             => $this->create_Receipt_ID(),
            'stu_id'                 => $formDataArr['stu_id'],
            'category_id'            => $post('category_id'),
            'receipt_amount'         => $receiptAmount,
            'extra_fees'             => $post('extra_fees'),
            'extra_fees_description' => "Registration Fees",
            'record_status'          => 'active'
        ];

        return $this->interface
            ->create_Student_Admission_Receipt($receiptFormArr);
    }

    public function manage_temp_student($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Basic Data
        // -----------------------------
        $formDataArr['tmp_id'] = $post('tmp_id');

        $isUpdate = !empty($formDataArr['tmp_id']) && $formDataArr['tmp_id'] != "null";
        $user_role_slug = $isUpdate ? 'update_student' : 'create_student';

        // -----------------------------
        // Permission Check
        // -----------------------------
        if (!$this->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Franchise Logic
        // -----------------------------
        if ($_SESSION['user_type'] == "franchise") {

            $franchise_id = $_SESSION['user_id'];

            if ($isUpdate) {
                $studentDetailArr = $this->interface
                    ->fetch_Detail_Single_Student($formDataArr['tmp_id']);

                if ($studentDetailArr->franchise_id != $franchise_id) {
                    return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
                }
            }

            // kept for future use (as in your original code)
            // $franchiseDetailArr = $this->interface
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
            $formDataArr['tmp_stu_id'] = $this->create_Tmp_Student_ID();
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->interface->manage_Temp_Student($formDataArr);

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
        if (!$this->checkUserRolePermission($user_role_slug, "hard")) {
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
        $returnArr = $this->interface
            ->manage_Student_Status($formDataArr);

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

        if (!$this->checkUserRolePermission($user_role_slug, "hard")) {
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
        $response = $this->interface->update_Bulk_Student_Status($paramArr);

        return $response['responseArr']['check'] === 'success'
            ? ['check' => 'success', 'message' => 'Bulk update successful']
            : ['check' => 'failure', 'message' => 'Bulk update failed'];
    }

    public function fetch_student_detail_modal($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // PERMISSION CHECK
        // -----------------------------
        if (!$this->checkUserRolePermission("view_student", "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // FETCH STUDENT
        // -----------------------------
        $student_id = (int) $post('student_id');

        $student = $this->interface
            ->fetch_Global_Single_Student($student_id);

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
}
