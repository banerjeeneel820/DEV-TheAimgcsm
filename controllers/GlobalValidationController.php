<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class GlobalValidationController
{   
    private $globalErrorArr; 

    public function __construct(
        private GlobalLibraryHandler $lib
    ){}

    public function validateGlobalStudentData($data)
    {
        $ownedStatus = $data['fran_own_status'] ?? 'no';

        // ===== REQUIRED FIELDS (COMMON) =====
        $requiredFields = [
            'stu_name',
            'stu_father_name',
            'stu_phone',
            'course_id',
            'franchise_id',
            'record_status',
            'student_status',
            'stu_result'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $this->globalErrorArr[] = ucfirst(str_replace('_', ' ', $field)) . " is required";
            }
        }

        // ===== PHONE VALIDATION =====
        if (!empty($data['stu_phone'])) {
            if (!preg_match('/^[0-9]{10}$/', $data['stu_phone'])) {
                $this->globalErrorArr[] = "Student phone must be exactly 10 digits";
            }
        }

        // ===== FEES VALIDATION (CONDITIONAL) =====
        // Only validate fees if franchise is OWNED
        if ($ownedStatus == "yes") {

            // ---- Course Fees ----
            if (!isset($data['stu_course_fees']) || $data['stu_course_fees'] === '' || $data['stu_course_fees'] === null) {
                $this->globalErrorArr[] = "Course fees is required";
            } elseif (!is_numeric($data['stu_course_fees']) || $data['stu_course_fees'] <= 0) {
                $this->globalErrorArr[] = "Course fees must be greater than 0";
            }

            // ---- Monthly Fees ----
            if (!isset($data['monthly_course_fees']) || $data['monthly_course_fees'] === '' || $data['monthly_course_fees'] === null) {
                $this->globalErrorArr[] = "Monthly course fees is required";
            } elseif (!is_numeric($data['monthly_course_fees']) || $data['monthly_course_fees'] <= 0) {
                $this->globalErrorArr[] = "Monthly course fees must be greater than 0";
            }

            // ---- Monthly <= Total ----
            if (
                isset($data['stu_course_fees'], $data['monthly_course_fees']) &&
                is_numeric($data['stu_course_fees']) &&
                is_numeric($data['monthly_course_fees'])
            ) {
                if ($data['monthly_course_fees'] > $data['stu_course_fees']) {
                    $this->globalErrorArr[] = "Monthly fees cannot be greater than total course fees";
                }
            }
        }

        // ===== FINAL RETURN =====
        if (!empty($this->globalErrorArr)) {
            return [
                'check' => 'failure',
                'message' => implode(', ', $this->globalErrorArr)
            ];
        }

        return ['check' => 'success'];
    }

    public function validateStudentReceiptData($data)
    {
        // reset error array (important if reused)
        $this->globalErrorArr = [];

        // ===== REQUIRED FIELDS =====
        $requiredFields = [
            'student_id',
            'record_status',
            'send_mail',
            'category_id',
            'receipt_amount'
        ];

        $labels = [
            'student_id' => 'Student',
            'record_status' => 'Record status',
            'send_mail' => 'Send mail',
            'category_id' => 'Category',
            'receipt_amount' => 'Receipt amount'
        ];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $this->globalErrorArr[] = ($labels[$field] ?? $field) . " is required";
            }
        }

        // ===== RECEIPT AMOUNT =====
        if (isset($data['receipt_amount'])) {
            if (!is_numeric($data['receipt_amount']) || $data['receipt_amount'] <= 0) {
                $this->globalErrorArr[] = "Receipt amount must be greater than 0";
            }
        }

        // ===== EXTRA FEES VALIDATION =====
        if (isset($data['extra_fees']) && is_numeric($data['extra_fees']) && $data['extra_fees'] > 0) {

            if (empty($data['extra_fees_description'])) {
                $this->globalErrorArr[] = "Extra fees description is required when extra fees is applied";
            }
        }

        // ===== FINAL RETURN =====
        if (!empty($this->globalErrorArr)) {
            return [
                'check' => 'failure',
                'message' => implode(', ', $this->globalErrorArr)
            ];
        }

        return ['check' => 'success'];
    }

}