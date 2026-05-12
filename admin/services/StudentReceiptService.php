<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class StudentReceiptService
{

    public function __construct(
        private GlobalInterfaceModel $model,
        private GlobalLibraryHandler $lib,
        private PermissionService $permissionService,
        private CacheService $cacheService,
        private GlobalValidationController $validator
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | View receipt data helper methods
    |--------------------------------------------------------------------------
    */
    public function fetchReceiptCategory($category)
    {
        return  $this->model->fetch_Single_Parent_Category($category);
    }

    public function fetchStudentDetails($id)
    {
        return $this->model->fetch_Global_Single_Student($id);
    }

    public function fetchReceiptDetails($id)
    {
        return $this->model->fetch_Single_Receipt_Data($id);
    }

    public function fetchReceiptData($filters)
    {
        return $this->model->fetch_Global_Receipt($filters);
    }

    public function fetchStudentReceiptSummary($filters)
    {
        return $this->model->fetch_Student_Receipt_Summary($filters);
    }

    public function prepareReceiptFilters($data)
    {
        $filters = [

            'record_status' =>
            $data['record_status'] ?? 'active',

            'verified_status' =>
            $data['verified_status'] ?? null,

            'student_id' =>
            !empty($data['stu_id'])
                ? trim($data['stu_id'])
                : null,

            'pageNo' =>
            !empty($data['pageNo'])
                ? (int)$data['pageNo']
                : 1,

            'limit' => 20
        ];

        // =========================
        // Course Filter
        // =========================
        if (!empty($data['course_id'])) {

            $filters['course_id'] =
                (int)$data['course_id'];
        }

        // =========================
        // Franchise Filter
        // =========================
        if ($_SESSION['user_type'] === 'franchise') {

            $filters['franchise_id'] =
                (int)$_SESSION['user_id'];
        } elseif (!empty($data['franchise_id'])) {

            $filters['franchise_id'] =
                (int)$data['franchise_id'];
        }

        // =========================
        // Date Filters
        // =========================
        foreach ([
            'created',
            'receipt_season_start',
            'receipt_season_end'
        ]
            as $field) {

            if (!empty($data[$field])) {

                $filters[$field] =
                    $this->lib->formatDateDB($data[$field]);
            }
        }

        return $filters;
    }

    /*
    |--------------------------------------------------------------------------
    | Manage receipt data helper methods
    |--------------------------------------------------------------------------
    */
    public function prepareRequestData()
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        return [

            'receipt_row_id' => $post('receipt_row_id'),

            'receipt_id' => $post('receipt_id'),

            'student_id' => $post('stu_id'),

            'record_status' => $post('record_status'),

            'send_mail' => $post('send_mail'),

            'category_id' => $post('category_id'),

            'receipt_amount' => (int)$post('receipt_amount'),

            'late_fine' => (int)$post('late_fine'),

            'extra_fees' => (int)$post('extra_fees'),

            'extra_fees_description' =>
            $post('extra_fees_description'),

            'raw_validation_data' => [

                'student_id' => $post('stu_id'),

                'record_status' => $post('record_status'),

                'send_mail' => $post('send_mail'),

                'category_id' => $post('category_id'),

                'receipt_amount' => $post('receipt_amount'),

                'extra_fees' => $post('extra_fees'),

                'extra_fees_description' =>
                $post('extra_fees_description')
            ]
        ];
    }

    public function validateStudentReceiptData($payloadRawData)
    {
        $this->validator
            ->validateStudentReceiptData($payloadRawData);
    }

    public function isUpdateRequest($payload)
    {
        return !empty($payload['receipt_row_id']);
    }

    public function checkManagePermission($isUpdate)
    {
        $permissionSlug =
            $isUpdate
            ? 'update_receipt'
            : 'create_receipt';

        if (
            !$this->permissionService
            ->checkUserRolePermission($permissionSlug, 'hard')
        ) {

            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        return ['check' => 'success'];
    }

    public function fetchExistingReceipt(
        $isUpdate,
        $payload
    ) 
    {
        if (!$isUpdate) {
            return null;
        }
    
        return $this->model
            ->fetch_Receipt_Detail(
                $payload['receipt_row_id']
            );
    }

    public function calculateDueFees(
        $stuReceiptDetails,
        $receiptDetailArr,
        $payload,
        $isUpdate
    ) 
    {
    
        $course_fee =
            !empty($stuReceiptDetails->stu_course_fees)
            ? (int)$stuReceiptDetails->stu_course_fees
            : (int)$stuReceiptDetails->course_default_fees;
    
        $course_due_fees =
            $course_fee
            - (int)$stuReceiptDetails->stu_course_discount
            - (int)$stuReceiptDetails->course_fees_paid
            - (int)$stuReceiptDetails->advanced_fees
            - (int)$stuReceiptDetails->fees_paid_before_dr;
    
        if ($isUpdate) {
    
            $course_due_fees +=
                (int)$receiptDetailArr->receipt_amount;
        }
    
        return max(0, $course_due_fees);
    }

    public function validateBusinessRules(
        $courseDueFees,
        $payload
    ) 
    {
    
        if (
            $courseDueFees == 0
            && $payload['category_id'] != 109501
        ) {
    
            return [
                'check' => 'failure',
                'message' => 'This student has cleared their fees!'
            ];
        }
    
        if ($payload['receipt_amount'] <= 0) {
    
            return [
                'check' => 'failure',
                'message' => 'Invalid receipt amount!'
            ];
        }
    
        if ($payload['receipt_amount'] > $courseDueFees) {
    
            return [
                'check' => 'failure',
                'message' => 'Receipt amount is greater than due course fees!'
            ];
        }
    
        return ['check' => 'success'];
    }

    public function validateFranchiseAccess($studentId) 
    {
        if ($_SESSION['user_type'] !== 'franchise') {
            return ['check' => 'success'];
        }
    
        $studentDetailArr =
            $this->model
            ->fetch_Detail_Single_Student($studentId);
    
        if (
            $studentDetailArr->franchise_id
            != $_SESSION['user_id']
        ) {
    
            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }
    
        return ['check' => 'success'];
    }

    public function buildReceiptSavePayload(
        $payload,
        $receiptDetailArr,
        $isUpdate
    ) 
    {
        $savePayload = [
    
            'receipt_row_id' =>
                $payload['receipt_row_id'],
    
            'receipt_id' =>
                $isUpdate
                ? $payload['receipt_id']
                : $this->create_Receipt_ID(),
    
            'student_id' =>
                $payload['student_id'],
    
            'record_status' =>
                $payload['record_status'],
    
            'category_id' =>
                $payload['category_id'],
    
            'receipt_amount' =>
                $payload['receipt_amount'],
    
            'late_fine' =>
                $payload['late_fine'],
    
            'extra_fees' =>
                $payload['extra_fees'],
    
            'extra_fees_description' =>
                $payload['extra_fees']
                ? $payload['extra_fees_description']
                : null
        ];
    
        /*
        |--------------------------------------------------------------------------
        | ORIGINAL VALUES
        |--------------------------------------------------------------------------
        */
        if (!$isUpdate) {
    
            $savePayload += [
    
                'original_receipt_amount' =>
                    $payload['receipt_amount'],
    
                'original_late_fine' =>
                    $payload['late_fine'],
    
                'original_extra_fees' =>
                    $payload['extra_fees']
            ];
        }
    
        /*
        |--------------------------------------------------------------------------
        | UPDATE CHANGES
        |--------------------------------------------------------------------------
        */
        if ($isUpdate) {
    
            $changes = [];
    
            $original = [
    
                'receipt_amount' =>
                    (int)$receiptDetailArr->og_receipt_amount,
    
                'late_fine' =>
                    (int)$receiptDetailArr->og_late_fine,
    
                'extra_fees' =>
                    (int)$receiptDetailArr->og_extra_fees
            ];
    
            if (
                $payload['receipt_amount']
                < $original['receipt_amount']
            ) {
    
                $changes[] =
                    "Receipt amount reduced from Rs. {$original['receipt_amount']} to Rs. {$payload['receipt_amount']}.";
            }
    
            if (
                $payload['late_fine']
                < $original['late_fine']
            ) {
    
                $changes[] =
                    "Late fine reduced from Rs. {$original['late_fine']} to Rs. {$payload['late_fine']}.";
            }
    
            if (
                $payload['extra_fees']
                < $original['extra_fees']
            ) {
    
                $changes[] =
                    "Additional fees reduced from Rs. {$original['extra_fees']} to Rs. {$payload['extra_fees']}.";
            }
    
            $savePayload['verified_status'] =
                !empty($changes)
                ? 'n'
                : 'y';
    
            $savePayload['edit_description'] =
                !empty($changes)
                ? serialize($changes)
                : null;
        }
    
        return $savePayload;
    }

    public function create_Receipt_ID()
    {
        //Creating new Franchise id method
        $receiptDetail = $this->model->fetch_Last_Receipt_Detail();
        $last_rcpt_id = $receiptDetail[0]->receipt_id;

        if ($last_rcpt_id != null) {
            $last_rcpt_id_pt_2 = substr($last_rcpt_id, 17);
            $last_rcpt_id_pt_2++;
        } else {
            $last_rcpt_id_pt_2 = 1;
        }

        $current_rcpt_id = "WBTAIMGCSMRECEIPT" . $last_rcpt_id_pt_2;

        return $current_rcpt_id;
    }

    public function saveReceiptData($savePayload)
    {
        return $this->model
        ->manage_Student_Receipt($savePayload);
    }

    public function handlePostSaveOperations(
        $saveResult,
        $receiptDetailArr,
        $savePayload,
        $isUpdate
    ) {
    
        if ($isUpdate) {
    
            $receipt_id =
                $savePayload['receipt_row_id'];
    
            $receiptDetails =
                $receiptDetailArr;
    
            if (!empty($savePayload['edit_description'])) {
    
                $file =
                    USER_UPLOAD_DIR
                    . "runtime_upload/Receipt_{$receiptDetails->receipt_id}.pdf";
    
                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    
        else {
    
            $receipt_id =
                $saveResult['last_insert_id'];
    
            $receiptDetails =
                $this->model
                ->fetch_Single_Receipt_Data($receipt_id);
    
            $this->cacheService
                ->purgeSiteCache('student_receipts');
        }
    
        return [
    
            'receipt_id' => $receipt_id,
    
            'receipt_details' => $receiptDetails
        ];
    }

    public function handleReceiptMail(
        $payload,
        $stuReceiptDetails,
        $receiptPdfRslt
    ) {
    
        if ($payload['send_mail'] !== 'yes') {
            return;
        }
    
        $emailParamArr = [
    
            'receiver_name' =>
                $stuReceiptDetails->stu_name,
    
            'receiver_email' =>
                $stuReceiptDetails->stu_email,
    
            'attachment_path' =>
                $receiptPdfRslt['file_upload_dir'] ?? null,
    
            'email_subject' =>
                $receiptPdfRslt['email_subject'],
    
            'email_template' =>
                $receiptPdfRslt['email_template']
        ];
    
        $this->lib->php_mailer_send_mail($emailParamArr);
    }

    public function buildSuccessResponse(
        $receiptPdfRslt,
        $receiptData,
        $isUpdate
    ) 
    {
    
        $response = [
    
            'check' => 'success',
    
            'file_url' =>
                $receiptPdfRslt['file_url'] ?? null,
    
            'receipt_id' =>
                $receiptData['receipt_details']->receipt_id
        ];
    
        if (!$isUpdate) {
    
            $response['last_insert_id'] =
                $receiptData['receipt_id'];
        }
    
        return $response;
    }

    private function getStuReceiptEmailCode($category)
    {
        return match ($category) {
            'Admission Fees' => 'student-admission-receipt-invoice',
            'Tuition Fees'   => 'student-monthly-receipt-invoice',
            default          => 'student-other-receipt-invoice',
        };
    }

    private function calculateStuReceiptData($receipt, $student)
    {
        $courseFees   = $student->stu_course_fees ?? $student->course_fees ?? 0;
        $lateFees     = (int) ($receipt->late_fine ?? 0);
        $extraFees    = (int) ($receipt->extra_fees ?? 0);
        $discount     = (int) ($student->stu_course_discount ?? 0);
        $advanceFees  = (int) ($student->advanced_fees ?? 0);

        $receiptAmount = (int)$receipt->receipt_amount + $lateFees + $extraFees;

        $dueAmount = $courseFees
            - (int)$student->fees_paid_before_dr
            - (int)$student->course_fees_paid
            - $discount
            - $advanceFees;

        return [
            'courseFees'        => $courseFees,
            'lateFees'          => $lateFees,
            'extraFees'         => $extraFees,
            'discount'          => $discount,
            'advanceFees'       => $advanceFees,
            'receiptAmount'     => $receiptAmount,
            'dueAmount'         => $dueAmount,
            'netCourseFees'     => $courseFees - $discount,
            'totalFeesPaid'     => (int)$student->course_fees_paid + $advanceFees,
            'advanceFeesTitle'  => $advanceFees
                ? "Advance Fees submitted on " . date('jS F, Y', strtotime($student->advance_fees_date))
                : "No advance fees available!"
        ];
    }

    private function buildStuReceiptTemplateVars($receipt, $student, $site, $template, $calc)
    {
        return [
            "{SITE_ADDR}"        => FRONT_SITE_URL,
            "{COMPANY_NAME}"     => $site->title,
            "{COMPANY_EMAIL}"    => $student->fran_email,
            "{CONTACT_NO}"       => $student->fran_phone,
            "{COMPANY_ADDRESS}"  => $student->fran_address,
            "{COMPANY_LOGO}"     => USER_UPLOAD_URL . 'others/' . $site->logo,
            "{COMPANY_SIGNATURE}" => USER_UPLOAD_URL . 'others/' . $site->signature,

            "{EMAIL_TITLE}"      => $template->subject,
            "{INVOICE_DATE}"     => date('jS F, Y', strtotime($receipt->created_at)),

            "{STUDENT_NAME}"     => $student->stu_name,
            "{STUDENT_CONTACT}"  => $student->stu_phone,
            "{STUDENT_ID}"       => $student->stu_id,
            "{CONVERSION_STATUS}" => $student->conversion_status == 'y' ? 'Converted' : 'Recent',

            "{COURSE}"           => $student->course_title,
            "{FRANCHISE}"        => $student->center_name,

            "{RECEIPT_TITLE}"    => "Receipt of " . date('jS F, Y'),
            "{RECEIPT_ID}"       => $receipt->receipt_id,
            "{RECEIPT_TYPE}"     => ucfirst($receipt->category),

            "{RECEIPT_AMOUNT}"   => $receipt->receipt_amount,
            "{LATE_FEES}"        => $calc['lateFees'],
            "{EXTRA_FEES}"       => $calc['extraFees'],
            "{EXTRA_FEES_DESC}"  => $receipt->extra_fees_description ?? "No Additional Fees Applied",

            "{ADVANCE_FEES}"     => $calc['advanceFees'],
            "{ADVANCE_FEES_TITLE}" => $calc['advanceFeesTitle'],

            "{COURSE_FEES}"      => $calc['courseFees'],
            "{DISCOUNT_AMOUNT}"  => $calc['discount'],
            "{NET_COURSE_FEES}"  => $calc['netCourseFees'],
            "{COURSE_FEES_PAID}" => $calc['totalFeesPaid'],

            "{DUE_BALANCE}"      => $calc['dueAmount'],
            "{TOTAL_AMOUNT}"     => $calc['receiptAmount'],

            "{FEES_PAID_BFR_DR}" => $student->fees_paid_before_dr ?? 0,
        ];
    }

    private function buildTempReceiptTemplateVars($tmpStudent, $site)
    {
        return [
            "{SITE_ADDR}"         => FRONT_SITE_URL,
            "{COMPANY_NAME}"      => $site->title,
            "{COMPANY_EMAIL}"     => $tmpStudent->fran_email,
            "{CONTACT_NO}"        => $tmpStudent->fran_phone,
            "{COMPANY_ADDRESS}"   => $tmpStudent->fran_address,
            "{COMPANY_LOGO}"      => USER_UPLOAD_URL . 'others/' . $site->logo,
            "{COMPANY_SIGNATURE}" => USER_UPLOAD_URL . 'others/' . $site->signature,

            "{INVOICE_DATE}"      => date('jS F, Y'),

            "{STUDENT_NAME}"      => $tmpStudent->stu_name,
            "{STUDENT_FATHER}"    => $tmpStudent->stu_father_name,
            "{STUDENT_CONTACT}"   => $tmpStudent->stu_phone,
            "{TEMP_STUDENT_ID}"   => $tmpStudent->tmp_stu_id,

            "{COURSE}"            => $tmpStudent->course_title,
            "{FRANCHISE}"         => $tmpStudent->center_name,

            "{RECEIPT_AMOUNT}"    => $tmpStudent->advanced_fees,
            "{TOTAL_AMOUNT}"      => $tmpStudent->advanced_fees,
            "{RECEIPT_TYPE}"      => "Advance Fees"
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Calculate total receipt collection helper methods
    |--------------------------------------------------------------------------
    */
    public function prepareReceiptCollectionFilters()
    {
        $post = fn($key) =>
            $this->lib->postDataSanitize($key);

        $filters = [

            'record_status' =>
                $post('record_status'),

            'course_id' =>
                $post('course_id'),

            'franchise_id' =>
                $post('franchise_id'),

            'stu_id' =>
                $post('student_id')
        ];

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL DATE FILTERS
        |--------------------------------------------------------------------------
        */
        if ($post('created')) {

            $filters['created'] =
                $this->formatReceiptDate(
                    $post('created')
                );
        }

        if ($post('receipt_season_start')) {

            $filters['receipt_season_start'] =
                $this->formatReceiptDate(
                    $post('receipt_season_start')
                );
        }

        if ($post('receipt_season_end')) {

            $filters['receipt_season_end'] =
                $this->formatReceiptDate(
                    $post('receipt_season_end')
                );
        }

        return $filters;
    }

    private function formatReceiptDate($date) {
    
        $date =
            str_replace('/', '-', $date);
    
        return date(
            'Y-m-d',
            strtotime($date)
        );
    }

    public function checkReceiptViewPermission()
    {
        $user_role_slug = 'view_receipt';

        if (
            !$this->permissionService
            ->checkUserRolePermission(
                $user_role_slug,
                'hard'
            )
        ) {

            return [

                'check' => 'failure',

                'message' =>
                    "You don't have the permission to perform this action!"
            ];
        }

        return [
            'check' => 'success'
        ];
    }
    
    public function fetchReceiptCollection($filters) 
    {
    
        return json_decode(
            json_encode(
                $this->model
                ->fetch_Receipt_Collection($filters)
            ),
            true
        );
    }

    public function buildReceiptCollectionResponse($receiptDataArr) 
    {
    
        return [
    
            'check' => 'success',
    
            'receiptData' =>
                $receiptDataArr,
    
            'message' =>
                'Receipt Collection was successfully fetched!'
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Calculate total receipt collection helper methods
    |--------------------------------------------------------------------------
    */
    public function generateTempReceiptPdf($id)
    {
        // -----------------------------
        // FETCH DATA
        // -----------------------------
        $tmpStudent = $this->model->fetch_Tmp_Single_Student($id);

        if (empty($tmpStudent)) {
            return ['check' => 'failure', 'message' => 'Student not found'];
        }

        // -----------------------------
        // FILE PATH
        // -----------------------------
        $fileName = "TEMPRCPT_" . $tmpStudent->tmp_stu_id . ".pdf";

        $file_upload_dir = USER_UPLOAD_DIR . 'runtime_upload/' . $fileName;
        $file_url        = USER_UPLOAD_URL . 'runtime_upload/' . $fileName;

        // -----------------------------
        // CACHE CHECK
        // -----------------------------
        if (file_exists($file_upload_dir)) {
            return [
                'check' => 'success',
                'file_upload_dir' => $file_upload_dir,
                'file_url' => $file_url
            ];
        }

        // -----------------------------
        // PREPARE DATA
        // -----------------------------
        $site = $this->model->fetch_Global_Site_Setting_Detail();
        $template = $this->model
            ->fetch_Email_Template_Detail('student-temp-receipt-invoice')->template;

        $swap = $this->buildTempReceiptTemplateVars($tmpStudent, $site);

        // -----------------------------
        // RENDER
        // -----------------------------
        $html = strtr($template, $swap);

        // -----------------------------
        // GENERATE PDF
        // -----------------------------
        PdfFactory::generate($html, $file_upload_dir, 'created_receipt');

        return [
            'check' => 'success',
            'file_upload_dir' => $file_upload_dir,
            'file_url' => $file_url
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Common helper method for generating receipt pdf
    |--------------------------------------------------------------------------
    */
    public function createStudentReceiptPdf($receipt_id)
    {
        // -----------------------------
        // FETCH DATA
        // -----------------------------
        $receipt = $this->model
            ->fetch_Single_Receipt_Data($receipt_id);

        if (empty($receipt)) {
            return ['check' => 'failure', 'message' => 'Invalid receipt data'];
        }

        $student = $this->fetchStudentDetails($receipt->stu_id, $receipt->created_at);

        // -----------------------------
        // FILE PATH
        // -----------------------------
        $fileName = "Receipt_{$receipt->receipt_id}.pdf";

        $file_upload_dir = USER_UPLOAD_DIR . "runtime_upload/$fileName";
        $file_url        = USER_UPLOAD_URL . "runtime_upload/$fileName";

        // -----------------------------
        // TEMPLATE CONFIG
        // -----------------------------
        $email_code = $this->getStuReceiptEmailCode($receipt->category);

        $site = $this->model->fetch_Global_Site_Setting_Detail();
        $template = $this->model->fetch_Email_Template_Detail($email_code);

        // -----------------------------
        // CALCULATIONS
        // -----------------------------
        $calc = $this->calculateStuReceiptData($receipt, $student);

        // -----------------------------
        // TEMPLATE VARIABLES
        // -----------------------------
        $swap = $this->buildStuReceiptTemplateVars($receipt, $student, $site, $template, $calc);

        // -----------------------------
        // RENDER TEMPLATE
        // -----------------------------
        $html = strtr($template->template, $swap);

        // -----------------------------
        // GENERATE PDF (IF NOT EXISTS)
        // -----------------------------
        if (!file_exists($file_upload_dir)) {

            PdfFactory::generate($html, $file_upload_dir, 'created_receipt');
        }

        // -----------------------------
        // RESPONSE
        // -----------------------------
        return [
            'check'           => 'success',
            'email_subject'   => $template->subject,
            'email_template'  => $html,
            'file_upload_dir' => $file_upload_dir,
            'file_url'        => $file_url
        ];
    }
}
