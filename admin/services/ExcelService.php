<?php
defined('ROOTPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ExcelService
{
    private $styleArray;

    public function __construct()
    {
        //Excel sheet header style array
        $this->styleArray = [
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            ],
            'borders' => [
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
            /*'outline' => [
              'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
              'color' => array('argb' => 'FFFF0000'),
          ],*/
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startColor' => [
                    'argb' => 'FFFF0000',
                ],
                'endColor' => [
                    'argb' => 'FFA0A0A0',
                ],
            ],
        ];
    }

    public function exportStudent($students, $criteriaText)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $writer = new Xlsx($spreadsheet);

        // ==========================
        // DEFAULT FONT
        // ==========================
        $spreadsheet->getDefaultStyle()->getFont()
            ->setName('Arial')
            ->setSize(10);

        // ==========================
        // TOP META ROWS (TITLE + FILTERS)
        // ==========================
        $sheet->insertNewRowBefore(1, 3);

        $sheet->setCellValue('A1', 'Student Report');
        $sheet->setCellValue('A2', 'Generated on: ' . date('d M Y h:i A'));
        $sheet->setCellValue('A3', 'Filters: ' . $criteriaText);

        $sheet->mergeCells('A1:P1');
        $sheet->mergeCells('A2:P2');
        $sheet->mergeCells('A3:P3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setWrapText(true);

        // ==========================
        // COLUMN SETUP
        // ==========================
        $columns = range('A', 'P');

        $sheet->getColumnDimension('A')->setWidth(40, 'pt');

        foreach (array_slice($columns, 1) as $col) {
            $sheet->getColumnDimension($col)->setWidth(130, 'pt');
        }

        // ==========================
        // HEADER ROW (ROW 4)
        // ==========================
        $headers = [
            'SL No.', 'Student Name', "Father's Name", 'Student Email',
            'Contact No', 'Student ID', 'Course', 'Franchise',
            'Date of Birth', 'Gender', 'Qualification', 'Student Address',
            'Marital Status', 'Student Status', 'Receipt Count', 'Result'
        ];

        $headerRow = 4;

        foreach ($headers as $index => $title) {
            $sheet->setCellValue($columns[$index] . $headerRow, $title);
        }

        $headerRange = 'A4:P4';

        $sheet->getStyle($headerRange)->applyFromArray($this->styleArray);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

        // ==========================
        // DATA
        // ==========================
        if (!empty($students)) {

            $row = 5;
            $dataStartRow = $row;

            foreach ($students as $index => $student) {

                $sheet->fromArray([
                    $index + 1,
                    $student['stu_name'],
                    $student['stu_father_name'],
                    $student['stu_email'],
                    $student['stu_phone'],
                    $student['stu_id'],
                    $student['course_title'],
                    $student['center_name'],
                    $student['stu_dob'],
                    $student['stu_gender'],
                    $student['stu_qualification'],
                    $student['stu_address'],
                    $student['stu_marital_status'],
                    $student['student_status'],
                    $student['receipt_count'],
                    $student['stu_result']
                ], null, 'A' . $row);

                $row++;
            }

            // Apply alignment once (performance boost)
            $dataRange = 'A' . $dataStartRow . ':P' . ($row - 1);

            $sheet->getStyle($dataRange)->getAlignment()
                ->setHorizontal('center')
                ->setVertical('center')
                ->setWrapText(true);
        }

        // ==========================
        // SAVE FILE (FIXED BUG)
        // ==========================
        $filename = 'Student_Data_' . time() . '.xlsx';

        $filePath = USER_UPLOAD_DIR . 'runtime_upload/' . $filename;
        $fileUrl  = USER_UPLOAD_URL . 'runtime_upload/' . $filename;

        $writer->save($filePath);

        return [
            'check' => 'success',
            'file_upload_dir' => $filePath,
            'file_url' => $fileUrl
        ];
    }

    public function exportReceipt($receipts, $criteriaText)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $writer = new Xlsx($spreadsheet);

        // ==========================
        // DEFAULT FONT
        // ==========================
        $spreadsheet->getDefaultStyle()->getFont()
            ->setName('Arial')
            ->setSize(10);

        // ==========================
        // TOP META ROWS
        // ==========================
        $sheet->insertNewRowBefore(1, 3);

        $sheet->setCellValue('A1', 'Receipt Report');
        $sheet->setCellValue('A2', 'Generated on: ' . date('d M Y h:i A'));
        $sheet->setCellValue('A3', 'Filters: ' . $criteriaText);

        $sheet->mergeCells('A1:O1');
        $sheet->mergeCells('A2:O2');
        $sheet->mergeCells('A3:O3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setWrapText(true);

        // ==========================
        // COLUMN SETUP
        // ==========================
        $columns = range('A', 'O');

        // Widths
        $sheet->getColumnDimension('A')->setWidth(40, 'pt');
        foreach (array_slice($columns, 1) as $col) {
            $sheet->getColumnDimension($col)->setWidth(130, 'pt');
        }

        // ==========================
        // HEADER ROW
        // ==========================
        $headers = [
            'SL No.', 'Receipt ID', 'Receipt Created', 'Receipt Amount(Rs.)',
            'Late Fine(Rs.)', 'Additional Fees(Rs.)', 'Additional Fees Desc',
            'Student Name', 'Student Email', 'Contact No', 'Student ID',
            'Student Result', 'Course', 'Franchise', 'Verified Status'
        ];

        $headerRow = 4;

        foreach ($headers as $index => $title) {
            $sheet->setCellValue($columns[$index] . $headerRow, $title);
        }

        $headerRange = 'A4:O4';

        $sheet->getStyle($headerRange)->applyFromArray($this->styleArray);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

        // ==========================
        // DATA
        // ==========================
        if (!empty($receipts)) {

            $row = 5;
            $dataStartRow = $row;

            foreach ($receipts as $index => $receipt) {

                $sheet->fromArray([
                    $index + 1,
                    $receipt['receipt_id'],
                    $receipt['created_at'],
                    $receipt['receipt_amount'],
                    $receipt['late_fine'],
                    $receipt['extra_fees'],
                    $receipt['extra_fees_description'],
                    $receipt['stu_name'],
                    $receipt['stu_email'],
                    $receipt['stu_phone'],
                    $receipt['stu_id'],
                    ucfirst($receipt['stu_result']),
                    $receipt['course_title'],
                    $receipt['center_name'],
                    $receipt['verified_status']
                ], null, 'A' . $row);

                $row++;
            }

            // Apply alignment ONCE (big performance gain)
            $dataRange = 'A' . $dataStartRow . ':O' . ($row - 1);

            $sheet->getStyle($dataRange)->getAlignment()
                ->setHorizontal('center')
                ->setVertical('center')
                ->setWrapText(true);
        }

        // ==========================
        // SAVE FILE (FIXED BUG)
        // ==========================
        $filename = 'Receipt_Data_' . time() . '.xlsx';

        $filePath = USER_UPLOAD_DIR . 'runtime_upload/' . $filename;
        $fileUrl  = USER_UPLOAD_URL . 'runtime_upload/' . $filename;

        $writer->save($filePath);

        return [
            'check' => 'success',
            'file_upload_dir' => $filePath,
            'file_url' => $fileUrl
        ];
    }

    public function exportFranchise($franchises, $criteriaText)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $writer = new Xlsx($spreadsheet);

        // ==========================
        // DEFAULT FONT
        // ==========================
        $spreadsheet->getDefaultStyle()->getFont()
            ->setName('Arial')
            ->setSize(10);

        // ==========================
        // TOP META ROWS
        // ==========================
        $sheet->insertNewRowBefore(1, 3);

        $sheet->setCellValue('A1', 'Franchise Report');
        $sheet->setCellValue('A2', 'Generated on: ' . date('d M Y h:i A'));
        $sheet->setCellValue('A3', 'Filters: ' . $criteriaText);

        $sheet->mergeCells('A1:I1');
        $sheet->mergeCells('A2:I2');
        $sheet->mergeCells('A3:I3');

        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A2:A3')->getFont()->setSize(10);
        $sheet->getStyle('A3')->getAlignment()->setWrapText(true);

        // ==========================
        // COLUMN SETUP
        // ==========================
        $columns = range('A', 'I');

        $sheet->getColumnDimension('A')->setWidth(40, 'pt');

        foreach (array_slice($columns, 1) as $col) {
            $sheet->getColumnDimension($col)->setWidth(150, 'pt');
        }

        // ==========================
        // HEADER ROW (ROW 4)
        // ==========================
        $headers = [
            'SL No.', 'Franchise Name', 'Owner Name', 'Franchise ID',
            'Contact No', 'Franchise Email', 'Franchise Address',
            'Owned Status', 'Total No of Student Enrolled'
        ];

        $headerRow = 4;

        foreach ($headers as $index => $title) {
            $sheet->setCellValue($columns[$index] . $headerRow, $title);
        }

        $headerRange = 'A4:I4';

        $sheet->getStyle($headerRange)->applyFromArray($this->styleArray);
        $sheet->getStyle($headerRange)->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

        // ==========================
        // DATA
        // ==========================
        if (!empty($franchises)) {

            $row = 5;
            $dataStartRow = $row;

            foreach ($franchises as $index => $franchise) {

                $sheet->fromArray([
                    $index + 1,
                    $franchise['center_name'],
                    $franchise['owner_name'],
                    $franchise['fran_id'],
                    $franchise['fran_phone'],
                    $franchise['fran_email'],
                    $franchise['fran_address'],
                    $franchise['owned_status'], // no formatting now (as requested)
                    $franchise['enrolled_student_count']
                ], null, 'A' . $row);

                $row++;
            }

            // Apply alignment once (performance boost)
            $dataRange = 'A' . $dataStartRow . ':I' . ($row - 1);

            $sheet->getStyle($dataRange)->getAlignment()
                ->setHorizontal('center')
                ->setVertical('center')
                ->setWrapText(true);
        }

        // ==========================
        // SAVE FILE
        // ==========================
        $filename = 'Franchise_Data_' . time() . '.xlsx';

        $filePath = USER_UPLOAD_DIR . 'runtime_upload/' . $filename;
        $fileUrl  = USER_UPLOAD_URL . 'runtime_upload/' . $filename;

        $writer->save($filePath);

        return [
            'check' => 'success',
            'file_upload_dir' => $filePath,
            'file_url' => $fileUrl
        ];
    }
}
