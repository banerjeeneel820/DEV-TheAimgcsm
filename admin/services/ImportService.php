<?php

class ImportService
{   
    private $interface;
    private $lib;
    private $excelService;

    public function __construct($interface, $lib)
    {
        $this->interface = $interface;
        $this->lib = $lib;

        $this->excelService = new ExcelService();
    }

    public function importExamQuestions($post, $files)
    {
        $excel = $this->excelService->read($files['import_data_file']);

        if ($excel['check'] === 'failure') {
            return $excel;
        }

        $rows = $excel['data'];

        foreach ($rows as $line) {

            if (count(array_filter(array_slice($line, 0, 7))) < 7) {
                return ['check' => 'failure', 'message' => 'Invalid format'];
            }

            $exam_id = $line[0];

            $order = $this->interface
                ->fetch_Last_Question_Ordering($exam_id);

            $params = [
                'exam_id' => $line[0],
                'ques' => $line[1],
                'opt1' => $line[2],
                'opt2' => $line[3],
                'opt3' => $line[4],
                'opt4' => $line[5],
                'cor_ans' => $line[6],
                'ordering' => $order->ordering + 1,
                'record_status' => 'active'
            ];

            $this->interface->import_Exam_Questions($params);
        }

        return $this->lib->success('Data import completed successfully!');
    }

    public function importCity($post, $files)
    {
        $excel = $this->excelService->read($files['import_data_file']);

        if ($excel['check'] === 'failure') {
            return $excel;
        }

        foreach ($excel['data'] as $line) {

            if (empty($line[0])) continue;

            $this->interface->import_Global_City([
                'name' => $line[0],
                'record_status' => 'blocked'
            ]);
        }

        return $this->lib->success('Data import completed successfully!');
    }

    public function importStuMonthlyFee($post, $files)
    {
        $excel = $this->excelService->read($files['import_data_file']);

        if ($excel['check'] === 'failure') {
            return $excel;
        }

        $currentUserType = $_SESSION['user_type'];
        $currentUserID = $_SESSION['user_id'];

        foreach ($excel['data'] as $line) {

            if (empty($line[0])) continue;

            $stu_id = trim($line[0]);
            $fees   = trim($line[1]);

            $student = json_decode(json_encode(
                $this->interface
                    ->fetch_Global_Single_Student($stu_id)
            ), true);

            if (
                $currentUserType === "franchise" &&
                $student['franchise_id'] != $currentUserID
            ) {
                continue;
            }

            $this->interface
                ->update_student_monthly_course_fees([
                    'stu_id' => $stu_id,
                    'monthly_course_fees' => $fees
                ]);
        }

        return $this->lib->success('Data import completed successfully!');
    }
}
