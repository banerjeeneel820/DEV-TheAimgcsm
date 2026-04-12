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
}
