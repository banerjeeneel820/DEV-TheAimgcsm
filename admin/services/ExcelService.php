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
    public function exportStudent($students)
    {
        $spreadsheet = new Spreadsheet();
        $writer = new Xlsx($spreadsheet);
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->setActiveSheetIndex(0);

        $spreadsheet->getActiveSheet()->getStyle('A1:P1')->applyFromArray($this->styleArray);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Arial');
        $spreadsheet->getDefaultStyle()->getFont()->setSize(10);

        //Set sheet header cloumn width
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(40, 'pt');
        $cellHeaderArr = array('B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P');

        foreach ($cellHeaderArr as $cell) {
            $spreadsheet->getActiveSheet()->getColumnDimension($cell)->setWidth(130, 'pt');
        }
        //cell text alignment
        $spreadsheet->getActiveSheet()->getStyle('A1:P1')->getAlignment()->setHorizontal('center');
        $spreadsheet->getActiveSheet()->getStyle('A1:P1')->getAlignment()->setVertical('center');

        $sheet->setCellValue('A1', 'SL No.');
        $sheet->setCellValue('B1', 'Student Name');
        $sheet->setCellValue('C1', "Father's Name");
        $sheet->setCellValue('D1', 'Student Email');
        $sheet->setCellValue('E1', 'Contact No');
        $sheet->setCellValue('F1', 'Student ID');
        $sheet->setCellValue('G1', 'Course');
        $sheet->setCellValue('H1', 'Franchise');
        $sheet->setCellValue('I1', 'Date of Birth');
        $sheet->setCellValue('J1', 'Gender');
        $sheet->setCellValue('K1', 'Qualification');
        $sheet->setCellValue('L1', 'Student Address');
        $sheet->setCellValue('M1', 'Marital Status');
        $sheet->setCellValue('N1', 'Student Status');
        $sheet->setCellValue('O1', 'Receipt Count');
        $sheet->setCellValue('P1', 'Result');

        $i = 2;
        foreach ($students as $index => $student) {

            if ($student['student_status'] == "course_complete") {
                $student_status = "Course Complete";
            } else {
                $student_status = ucfirst($student['student_status']);
            }
            //cell text alignment
            $spreadsheet->getActiveSheet()->getStyle('A' . $i . ':P' . $i)->getAlignment()->setHorizontal('center');
            $spreadsheet->getActiveSheet()->getStyle('A' . $i . ':P' . $i)->getAlignment()->setVertical('center');

            //Wrap text
            $spreadsheet->getActiveSheet()->getStyle('A' . $i . ':P' . $i)->getAlignment()->setWrapText(true);

            $sheet->setCellValue('A' . $i, $index++);
            $sheet->setCellValue('B' . $i, $student['stu_name']);
            $sheet->setCellValue('C' . $i, $student['stu_father_name']);
            $sheet->setCellValue('D' . $i, $student['stu_email']);
            $sheet->setCellValue('E' . $i, $student['stu_phone']);
            $sheet->setCellValue('F' . $i, $student['stu_id']);
            $sheet->setCellValue('G' . $i, $student['course_title']);
            $sheet->setCellValue('H' . $i, $student['center_name']);
            $sheet->setCellValue('I' . $i, date("jS F, Y", strtotime($student['stu_dob'])));
            $sheet->setCellValue('J' . $i, ucfirst($student['stu_gender']));
            $sheet->setCellValue('K' . $i, $student['stu_qualification']);
            $sheet->setCellValue('L' . $i, $student['stu_address']);
            $sheet->setCellValue('M' . $i, ucfirst($student['stu_marital_status']));
            $sheet->setCellValue('N' . $i, $student_status);
            $sheet->setCellValue('O' . $i, $student['receipt_count']);
            $sheet->setCellValue('P' . $i, ucfirst($student['stu_result']));
            $i++;
        }

        $filePath =  USER_UPLOAD_DIR . 'runtime_upload/Student_Data_' . time() . '.xlsx';
        $fileUrl = USER_UPLOAD_URL . 'runtime_upload/Student_Data_' . time() . '.xlsx';

        $writer->save($filePath);


        return [
            'check' => 'success',
            'file_upload_dir' => $filePath,
            'file_url' => $fileUrl
        ];
    }
}
