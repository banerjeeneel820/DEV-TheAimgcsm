<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class StudentReceiptService
{
    private $interface;
    private $lib;

    public function __construct($interface, $lib)
    {
        $this->interface = $interface;
        $this->lib = $lib;
    }

    public function create_Receipt_ID()
    {
        //Creating new Franchise id method
        $receiptDetail = $this->interface->fetch_Last_Receipt_Detail();
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

    public function createStudentReceiptPdf($receipt_id)
    {
        // -----------------------------
        // FETCH DATA
        // -----------------------------
        $receipt = $this->interface
            ->fetch_Single_Receipt_Data($receipt_id);

        if (empty($receipt)) {
            return ['check' => 'failure', 'message' => 'Invalid receipt data'];
        }

        $student = $this->interface
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

        $site = $this->interface->fetch_Global_Site_Setting_Detail();
        $template = $this->interface->fetch_Email_Template_Detail($email_code);

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

    public function generateTempReceiptPdf($id)
    {
        // -----------------------------
        // FETCH DATA
        // -----------------------------
        $tmpStudent = $this->interface->fetch_Tmp_Single_Student($id);

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
        $site = $this->interface->fetch_Global_Site_Setting_Detail();
        $template = $this->interface
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
}
