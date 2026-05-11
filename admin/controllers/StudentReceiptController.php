<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class StudentReceiptController extends BaseController
{

    private $studentReceiptService;
    private $permissionService;
    private $courseFranchiseService;

    public function __construct($container)
    {
        parent::__construct($container);

        $this->studentReceiptService = $container->get(StudentReceiptService::class);
        $this->permissionService = $container->get(PermissionService::class);
        $this->courseFranchiseService = $container->get(CourseFranchiseService::class);
    }

    /*
    |--------------------------------------------------------------------------
    | View receipt data methods
    |--------------------------------------------------------------------------
    */
    public function manage_receipt_data_view($data)
    {
        $type = 'receipt';

        // =========================
        // Assets
        // =========================
        $assets = Asset::load("student_receipt_list");

        // =========================
        // Request Type
        // =========================
        $actionType = $this->lib->get('actionType');

        // =========================
        // Create / Update View
        // =========================
        if (!empty($actionType)) {

            return $this->handleReceiptManageView(
                $assets,
                $type,
                $actionType
            );
        }

        // =========================
        // Receipt Listing View
        // =========================
        return $this->handleReceiptListView(
            $assets,
            $type,
            $data
        );
    }

    private function handleReceiptManageView(
        $assets,
        $type,
        $actionType
    ) {

        $student_id = trim($this->lib->get('stu_id'));
        $receipt_id = (int)$this->lib->get('rcpt_id');

        $isCreate = $actionType === "create";

        $user_role_slug = $isCreate
            ? 'create_receipt'
            : 'update_receipt';

        // =========================
        // Permission
        // =========================
        $hasPermission = $this->permissionService
            ->checkUserRolePermission($user_role_slug);

        // =========================
        // Category Data (Common)
        // =========================
        $categoryData = $this->studentReceiptService
            ->fetchReceiptCategory($type);

        if (!$hasPermission) {

            return $this->page(
                [
                    'receipt_data'  => [],
                    'student_data'  => [],
                    'category_data' => $categoryData,
                    'page_type'     => $type
                ],
                'Manage Receipt',
                $assets,
                false,
                false
            );
        }

        // =========================
        // Create Mode
        // =========================
        if ($isCreate) {

            $studentData = !empty($student_id)
                ? $this->studentReceiptService->fetchStudentDetails($student_id)
                : [];

            return $this->page(
                [
                    'receipt_data'  => [],
                    'student_data'  => $studentData,
                    'category_data' => $categoryData,
                    'page_type'     => $type
                ],
                'Create Receipt',
                $assets,
                false,
                true
            );
        }

        // =========================
        // Update Mode
        // =========================
        $receiptData = $this->studentReceiptService
            ->fetchReceiptDetails($receipt_id);

        $studentData = !empty($receiptData)
            ? $this->studentReceiptService
            ->fetchStudentDetails(
                $receiptData->stu_id
            )
            : [];

        return $this->page(
            [
                'receipt_data'  => $receiptData ?: [],
                'student_data'  => $studentData,
                'category_data' => $categoryData,
                'page_type'     => $type
            ],
            'Update Receipt',
            $assets,
            false,
            true
        );
    }

    private function handleReceiptListView(
        $assets,
        $type,
        $data
    ) {

        $user_role_slug = 'view_receipt';

        // =========================
        // Permission
        // =========================
        $hasPermission = $this->permissionService
            ->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {

            return $this->page(
                [
                    'receipt_data'  => [],
                    'student_data'  => [],
                    'franchise_data' => [],
                    'course_data'   => [],
                    'page_type'     => $type
                ],
                'Receipt List',
                $assets,
                false,
                false
            );
        }

        // =========================
        // Filters
        // =========================
        $filters = $this->studentReceiptService
            ->prepareReceiptFilters($data);

        // =========================
        // Common Data
        // =========================
        $activeData = $this->courseFranchiseService
            ->fetch_Active_Course_Franchise_Data();

        // =========================
        // Receipts
        // =========================
        $receiptData = $this->studentReceiptService
            ->fetchReceiptData($filters);

        // =========================
        // Student Summary
        // =========================
        $studentData = !empty($filters['student_id'])
            ? $this->studentReceiptService
            ->fetchStudentReceiptSummary($filters)
            : [];

        return $this->page(
            [
                'receipt_data'   => $receiptData,
                'student_data'   => $studentData,
                'franchise_data' => $activeData['franchise'],
                'course_data'    => $activeData['course'],
                'page_type'      => $type
            ],
            'Receipt List',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage receipt data methods
    |--------------------------------------------------------------------------
    */
    public function manage_receipt(array $data)
    {
        /*
        |--------------------------------------------------------------------------
        | PREPARE REQUEST DATA
        |--------------------------------------------------------------------------
        */
        $payload = $this->studentReceiptService->prepareRequestData();

        /*
        |--------------------------------------------------------------------------
        | VALIDATION
        |--------------------------------------------------------------------------
        */
        $validationResult =
            $this->studentReceiptService
            ->validateStudentReceiptData($payload['raw_validation_data']);

        if ($validationResult['check'] === 'failure') {
            return $validationResult;
        }

        /*
        |--------------------------------------------------------------------------
        | CREATE / UPDATE
        |--------------------------------------------------------------------------
        */
        $isUpdate = $this->studentReceiptService->isUpdateRequest($payload);

        /*
        |--------------------------------------------------------------------------
        | PERMISSION
        |--------------------------------------------------------------------------
        */
        $permissionCheck = $this->studentReceiptService->checkManagePermission($isUpdate);

        if ($permissionCheck['check'] === 'failure') {
            return $permissionCheck;
        }

        /*
        |--------------------------------------------------------------------------
        | EXISTING RECEIPT
        |--------------------------------------------------------------------------
        */
        $receiptDetailArr =
            $this->studentReceiptService->fetchExistingReceipt(
                $isUpdate,
                $payload
            );

        if ($isUpdate && empty($receiptDetailArr)) {

            return [
                'check' => 'failure',
                'message' => 'Invalid receipt! Receipt does not exist.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | STUDENT DETAILS
        |--------------------------------------------------------------------------
        */
        $stuReceiptDetails =
            $this->studentReceiptService->fetchStudentDetails(
                $payload['student_id']
            );

        if (empty($stuReceiptDetails)) {

            return [
                'check' => 'failure',
                'message' => 'Invalid student! Student does not exist.'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DUE FEES
        |--------------------------------------------------------------------------
        */
        $courseDueFees =
            $this->studentReceiptService->calculateDueFees(
                $stuReceiptDetails,
                $receiptDetailArr,
                $payload,
                $isUpdate
            );

        /*
        |--------------------------------------------------------------------------
        | BUSINESS VALIDATION
        |--------------------------------------------------------------------------
        */
        $businessValidation =
            $this->studentReceiptService->validateBusinessRules(
                $courseDueFees,
                $payload
            );

        if ($businessValidation['check'] === 'failure') {
            return $businessValidation;
        }

        /*
        |--------------------------------------------------------------------------
        | FRANCHISE VALIDATION
        |--------------------------------------------------------------------------
        */
        $franchiseValidation =
            $this->studentReceiptService->validateFranchiseAccess(
                $payload['student_id']
            );

        if ($franchiseValidation['check'] === 'failure') {
            return $franchiseValidation;
        }

        /*
        |--------------------------------------------------------------------------
        | BUILD SAVE PAYLOAD
        |--------------------------------------------------------------------------
        */
        $savePayload =
            $this->studentReceiptService->buildReceiptSavePayload(
                $payload,
                $receiptDetailArr,
                $isUpdate
            );

        /*
        |--------------------------------------------------------------------------
        | SAVE RECEIPT
        |--------------------------------------------------------------------------
        */
        $saveResult =
            $this->studentReceiptService
            ->saveReceiptData($savePayload);

        if ($saveResult['check'] !== 'success') {

            return [
                'check' => 'failure',
                'message' => 'Something went wrong!'
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | POST SAVE OPERATIONS
        |--------------------------------------------------------------------------
        */
        $receiptData =
            $this->studentReceiptService->handlePostSaveOperations(
                $saveResult,
                $receiptDetailArr,
                $savePayload,
                $isUpdate
            );

        /*
        |--------------------------------------------------------------------------
        | PDF GENERATION
        |--------------------------------------------------------------------------
        */
        $receiptPdfRslt =
            $this->studentReceiptService
            ->createStudentReceiptPdf(
                $receiptData['receipt_id']
            );

        /*
        |--------------------------------------------------------------------------
        | EMAIL
        |--------------------------------------------------------------------------
        */
        $this->studentReceiptService->handleReceiptMail(
            $payload,
            $stuReceiptDetails,
            $receiptPdfRslt
        );

        /*
        |--------------------------------------------------------------------------
        | SUCCESS RESPONSE
        |--------------------------------------------------------------------------
        */
        return $this->studentReceiptService->buildSuccessResponse(
            $receiptPdfRslt,
            $receiptData,
            $isUpdate
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Calculate total receipt collection methods
    |--------------------------------------------------------------------------
    */
    public function fetch_total_receipt($data)
    {
        /*
        |--------------------------------------------------------------------------
        | PREPARE FILTER DATA
        |--------------------------------------------------------------------------
        */
        $filters =
            $this->studentReceiptService->prepareReceiptCollectionFilters();

        /*
        |--------------------------------------------------------------------------
        | PERMISSION CHECK
        |--------------------------------------------------------------------------
        */
        $permissionCheck =
            $this->studentReceiptService->checkReceiptViewPermission();

        if ($permissionCheck['check'] === 'failure') {
            return $permissionCheck;
        }

        /*
        |--------------------------------------------------------------------------
        | FETCH RECEIPT COLLECTION
        |--------------------------------------------------------------------------
        */
        $receiptDataArr =
            $this->studentReceiptService->fetchReceiptCollection($filters);

        /*
        |--------------------------------------------------------------------------
        | RESPONSE
        |--------------------------------------------------------------------------
        */
        return $this->studentReceiptService->buildReceiptCollectionResponse(
            $receiptDataArr
        );
    }

    public function export_student_receipt($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // INPUT
        // -----------------------------
        $receipt_row_id = $post('receipt_row_id');

        if (empty($receipt_row_id)) {
            return ['check' => 'failure', 'message' => 'Invalid receipt ID!'];
        }

        // -----------------------------
        // PERMISSION CHECK
        // -----------------------------
        if (!$this->permissionService->checkUserRolePermission('create_receipt', "hard")) {
            return ['check' => 'failure', 'message' => "You don't have permission!"];
        }

        // -----------------------------
        // GENERATE PDF
        // -----------------------------
        $receiptPdfRslt = $this->studentReceiptService->createStudentReceiptPdf($receipt_row_id);

        // -----------------------------
        // RESPONSE
        // -----------------------------
        if ($receiptPdfRslt['check'] === 'success') {
            return [
                'check'           => 'success',
                'file_upload_dir' => $receiptPdfRslt['file_upload_dir'],
                'file_url'        => $receiptPdfRslt['file_url']
            ];
        }

        return [
            'check'   => 'failure',
            'message' => $receiptPdfRslt['message'] ?? 'Failed to generate receipt PDF'
        ];
    }

    public function export_temp_student_receipt($data)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);
        $id = $post('id');

        // PERMISSION
        if (!$this->permissionService->checkUserRolePermission('create_receipt', "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // VALIDATION
        if (empty($id)) {
            return ['check' => 'failure', 'message' => 'Invalid student ID'];
        }

        // 👉 Delegate to service
        return $this->studentReceiptService->generateTempReceiptPdf($id);
    }
}
