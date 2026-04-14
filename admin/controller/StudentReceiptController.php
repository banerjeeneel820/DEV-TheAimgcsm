<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class StudentReceiptController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function manage_receipt($data)
    {
        $formDataArr = [];
        $returnArr = [];
        $dir = 'receipt';

        // helper
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

        $rawData = [
            'student_id' => $post('stu_id'),
            'record_status' => $post('record_status'),
            'send_mail' => $post('send_mail'),
            'category_id' => $post('category_id'),
            'receipt_amount' => $post('receipt_amount'),
            'extra_fees' => $post('extra_fees'),
            'extra_fees_description' => $post('extra_fees_description')
        ];

        $validationResult = $this->GlobalValidationControllerObj->validateStudentReceiptData($rawData);

        if ($validationResult['check'] == 'failure') {
            echo json_encode($validationResult);
            exit;
        }

        // ===== CREATE / UPDATE =====
        $isUpdate = !empty($post('receipt_row_id'));

        if ($isUpdate) {
            $user_role_slug = 'update_receipt';
            $formDataArr['receipt_row_id'] = $post('receipt_row_id');
            $formDataArr['receipt_id'] = $post('receipt_id');
        } else {
            $user_role_slug = 'create_receipt';
            $formDataArr['receipt_row_id'] = null;
            $formDataArr['receipt_id'] = $this->GlobalLibraryHandlerObj->create_Receipt_ID();
        }

        $formDataArr['category_id'] = $post('category_id');
        $send_mail = $post('send_mail');

        // ===== PERMISSION =====
        if (!$this->GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard")) {
            echo json_encode(['check' => 'failure', 'message' => "You don't have the permission to perform this action!"]);
            exit;
        }

        // ===== FETCH EXISTING RECEIPT =====
        $receiptDetailArr = $isUpdate
            ? $this->GlobalInterfaceControllerObj->fetch_Receipt_Detail($formDataArr['receipt_row_id'])
            : null;

        // ===== VALIDATE RECEIPT EXISTS =====
        if ($isUpdate && empty($receiptDetailArr)) {
            echo json_encode([
                'check' => 'failure',
                'message' => 'Invalid receipt! Receipt does not exist.'
            ]);
            exit;
        }

        // ===== INPUT =====
        $formDataArr['student_id'] = $post('stu_id');
        $formDataArr['record_status'] = $post('record_status');

        $receipt_amount = (int)$post('receipt_amount');
        $late_fine = (int)$post('late_fine');
        $extra_fees = (int)$post('extra_fees');

        $formDataArr += [
            'receipt_amount' => $receipt_amount,
            'late_fine' => $late_fine,
            'extra_fees' => $extra_fees
        ];

        // ===== ORIGINAL VALUES (CREATE) =====
        if (!$isUpdate) {
            $formDataArr += [
                'original_receipt_amount' => $receipt_amount,
                'original_late_fine' => $late_fine,
                'original_extra_fees' => $extra_fees
            ];
        }

        // ===== STUDENT DETAILS =====
        $stuReceiptDetails = $this->GlobalInterfaceControllerObj->fetch_Global_Single_Student($formDataArr['student_id']);

        // ===== VALIDATE RECEIPT EXISTS =====
        if (empty($stuReceiptDetails)) {
            echo json_encode([
                'check' => 'failure',
                'message' => 'Invalid student! Student does not exist.'
            ]);
            exit;
        }

        $course_fee = !empty($stuReceiptDetails->stu_course_fees)
            ? (int)$stuReceiptDetails->stu_course_fees
            : (int)$stuReceiptDetails->course_default_fees;

        // ===== DUE FEES =====
        $course_due_fees = $course_fee
            - (int)$stuReceiptDetails->stu_course_discount
            - (int)$stuReceiptDetails->course_fees_paid
            - (int)$stuReceiptDetails->advanced_fees
            - (int)$stuReceiptDetails->fees_paid_before_dr;

        if ($isUpdate) {
            $course_due_fees += (int)$receiptDetailArr->receipt_amount;
        }

        $course_due_fees = max(0, $course_due_fees);

        // ===== VALIDATION =====
        if ($course_due_fees == 0 && $formDataArr["category_id"] != 109501) {
            echo json_encode(['check' => 'failure', 'message' => 'This student has cleared their fees!']);
            exit;
        }

        if ($receipt_amount <= 0) {
            echo json_encode(['check' => 'failure', 'message' => 'Invalid receipt amount!']);
            exit;
        }

        if ($receipt_amount > $course_due_fees) {
            echo json_encode(['check' => 'failure', 'message' => 'Receipt amount is greater than due course fees!']);
            exit;
        }

        // ===== FRANCHISE CHECK =====
        if ($_SESSION['user_type'] == "franchise") {
            $studentDetailArr = $this->GlobalInterfaceControllerObj->fetch_Detail_Single_Student($formDataArr['student_id']);

            if ($studentDetailArr->franchise_id != $_SESSION['user_id']) {
                echo json_encode(['check' => 'failure', 'message' => "You don't have the permission to perform this action!"]);
                exit;
            }
        }

        // ===== EXTRA FEES DESC =====
        $formDataArr['extra_fees_description'] = $extra_fees
            ? $post('extra_fees_description')
            : null;

        // ===== UPDATE LOGIC =====
        if ($isUpdate) {

            $changes = [];

            $original = [
                'receipt_amount' => (int)$receiptDetailArr->og_receipt_amount,
                'late_fine' => (int)$receiptDetailArr->og_late_fine,
                'extra_fees' => (int)$receiptDetailArr->og_extra_fees
            ];

            if ($receipt_amount < $original['receipt_amount']) {
                $changes[] = "Receipt amount reduced from Rs. {$original['receipt_amount']} to Rs. $receipt_amount.";
            }

            if ($late_fine < $original['late_fine']) {
                $changes[] = "Late fine reduced from Rs. {$original['late_fine']} to Rs. $late_fine.";
            }

            if ($extra_fees < $original['extra_fees']) {
                $changes[] = "Additional fees reduced from Rs. {$original['extra_fees']} to Rs. $extra_fees.";
            }

            $formDataArr['verified_status'] = !empty($changes) ? 'n' : 'y';
            $formDataArr['edit_description'] = !empty($changes) ? serialize($changes) : null;
        }

        // ===== SAVE =====
        $returnArr = $this->GlobalInterfaceControllerObj->manage_Student_Receipt($formDataArr);

        if ($returnArr['check'] != 'success') {
            echo json_encode(['check' => 'failure', 'message' => "Something went wrong!"]);
            exit;
        }

        // ===== FETCH RECEIPT =====
        if ($isUpdate) {
            $receipt_id = $formDataArr['receipt_row_id'];
            $receiptDetails = $receiptDetailArr;

            if (!empty($formDataArr['edit_description'])) {
                $file = USER_UPLOAD_DIR . "runtime_upload/Receipt_{$receiptDetails->receipt_id}.pdf";
                if (file_exists($file)) unlink($file);
            }
        } else {
            $receipt_id = $returnArr['last_insert_id'];
            $receiptDetails = $this->GlobalInterfaceControllerObj->fetch_Single_Receipt_Data($receipt_id);

            $this->GlobalLibraryHandlerObj->purgeSiteCache("student_receipts");
        }

        // ===== PDF GENERATION =====
        $receiptPdfRslt = $this->GlobalLibraryHandlerObj->createStudentReceiptPdf($receipt_id);

        // ===== EMAIL =====
        if ($send_mail == "yes") {

            $emailParamArr = [
                'receiver_name' => $stuReceiptDetails->stu_name,
                'receiver_email' => $stuReceiptDetails->stu_email,
                'attachment_path' => $receiptPdfRslt['file_upload_dir'] ?? null,
                'email_subject' => $receiptPdfRslt['email_subject'],
                'email_template' => $receiptPdfRslt['email_template']
            ];

            $this->GlobalLibraryHandlerObj->php_mailer_send_mail($emailParamArr);
        }

        // ===== FINAL RESPONSE =====
        $returnArr = [
            'check' => 'success',
            'file_url' => $receiptPdfRslt['file_url'] ?? null,
            'receipt_id' => $receiptDetails->receipt_id
        ];

        if (!$isUpdate) {
            $returnArr['last_insert_id'] = $receipt_id;
        }
    }

    public function fetch_total_receipt($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = "view_receipt";

        if (!$this->GlobalLibraryHandlerObj->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Basic Filters
        // -----------------------------
        $formDataArr['record_status'] = $post('record_status');
        $formDataArr['course_id']     = $post('course_id');
        $formDataArr['franchise_id']  = $post('franchise_id');
        $formDataArr['stu_id']        = $post('student_id');

        // -----------------------------
        // Date Helper
        // -----------------------------
        $formatDate = function ($date) {
            $date = str_replace('/', '-', $date);
            return date('Y-m-d', strtotime($date));
        };

        // -----------------------------
        // Optional Date Filters
        // -----------------------------
        if ($post('created')) {
            $formDataArr['created'] = $formatDate($post('created'));
        }

        if ($post('receipt_season_start')) {
            $formDataArr['receipt_season_start'] = $formatDate($post('receipt_season_start'));
        }

        if ($post('receipt_season_end')) {
            $formDataArr['receipt_season_end'] = $formatDate($post('receipt_season_end'));
        }

        // -----------------------------
        // Fetch Data
        // -----------------------------
        $receiptDataArr = json_decode(
            json_encode(
                $this->GlobalInterfaceControllerObj->fetch_Receipt_Collection($formDataArr)
            ),
            true
        );

        // -----------------------------
        // Response
        // -----------------------------
        return [
            'check'        => 'success',
            'receiptData'  => $receiptDataArr,
            'message'      => "Receipt Collection was successfully fetched!"
        ];
    }

    public function export_student_receipt($data)
    {
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

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
        if (!$this->GlobalLibraryHandlerObj->checkUserRolePermission('create_receipt', "hard")) {
            return ['check' => 'failure', 'message' => "You don't have permission!"];
        }

        // -----------------------------
        // GENERATE PDF
        // -----------------------------
        $receiptPdfRslt = $this->createStudentReceiptPdf($receipt_row_id);

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

    private function createStudentReceiptPdf($receipt_id)
    {
        // -----------------------------
        // PERMISSION
        // -----------------------------
        if (!$this->GlobalLibraryHandlerObj->checkUserRolePermission('create_receipt', "hard")) {
            return ['check' => 'failure', 'message' => "You don't have permission!"];
        }

        // -----------------------------
        // FETCH DATA
        // -----------------------------
        $receipt = $this->GlobalInterfaceControllerObj
            ->fetch_Single_Receipt_Data($receipt_id);

        if (empty($receipt)) {
            return ['check' => 'failure', 'message' => 'Invalid receipt data'];
        }

        $student = $this->GlobalInterfaceControllerObj
            ->fetch_Global_Single_Student($receipt->stu_id, $receipt->created_at);

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

        $site = $this->GlobalLibraryHandlerObj->fetchSiteSettingDetail();
        $template = $this->GlobalInterfaceControllerObj->fetch_Email_Template_Detail($email_code);

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

            PdfFactory::generate($html, $file_upload_dir);
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

    public function export_temp_student_receipt($data)
    {
        $post = fn ($key) => $this->GlobalLibraryHandlerObj->postDataSanitize($key);

        $tmp_id = $post('tmp_id');

        // -----------------------------
        // PERMISSION CHECK
        // -----------------------------
        if (!$this->GlobalLibraryHandlerObj->checkUserRolePermission('create_receipt', "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        if (empty($tmp_id)) {
            return ['check' => 'failure', 'message' => 'Invalid student ID'];
        }

        // -----------------------------
        // FETCH DATA
        // -----------------------------
        $tmpStudent = $this->GlobalInterfaceControllerObj->fetch_Tmp_Single_Student($tmp_id);

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
        // RETURN IF ALREADY EXISTS (CACHE)
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
        $site = $this->GlobalLibraryHandlerObj->fetchSiteSettingDetail();

        $template = $this->GlobalInterfaceControllerObj
            ->fetch_Email_Template_Detail('student-temp-receipt-invoice')->template;

        $swap = [
            "{SITE_ADDR}"        => FRONT_SITE_URL,
            "{COMPANY_NAME}"     => $site->title,
            "{COMPANY_EMAIL}"    => $tmpStudent->fran_email,
            "{CONTACT_NO}"       => $tmpStudent->fran_phone,
            "{COMPANY_ADDRESS}"  => $tmpStudent->fran_address,
            "{COMPANY_LOGO}"     => USER_UPLOAD_URL . 'others/' . $site->logo,
            "{COMPANY_SIGNATURE}" => USER_UPLOAD_URL . 'others/' . $site->signature,
            "{INVOICE_DATE}"     => date('jS F, Y'),
            "{STUDENT_NAME}"     => $tmpStudent->stu_name,
            "{STUDENT_FATHER}"   => $tmpStudent->stu_father_name,
            "{STUDENT_CONTACT}"  => $tmpStudent->stu_phone,
            "{TEMP_STUDENT_ID}"  => $tmpStudent->tmp_stu_id,
            "{COURSE}"           => $tmpStudent->course_title,
            "{FRANCHISE}"        => $tmpStudent->center_name,
            "{RECEIPT_AMOUNT}"   => $tmpStudent->advanced_fees,
            "{TOTAL_AMOUNT}"     => $tmpStudent->advanced_fees,
            "{RECEIPT_TYPE}"     => "Advance Fees"
        ];

        // -----------------------------
        // TEMPLATE REPLACE
        // -----------------------------
        $html = str_replace(array_keys($swap), array_values($swap), $template);

        // -----------------------------
        // GENERATE PDF (USING FACTORY)
        // -----------------------------
        PdfFactory::generate($html, $file_upload_dir);

        return [
            'check' => 'success',
            'file_upload_dir' => $file_upload_dir,
            'file_url' => $file_url
        ];
    }
}
