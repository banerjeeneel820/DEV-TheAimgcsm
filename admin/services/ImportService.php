<?php

class ImportService
{   

    public function __construct(
        private GlobalInterfaceModel $model,
        private GlobalLibraryHandler $lib,
        private ExcelService $excelService
    ){}

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

            $order = $this->model
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

            $this->model->import_Exam_Questions($params);
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

            $this->model->import_Global_City([
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
                $this->model
                    ->fetch_Global_Single_Student($stu_id)
            ), true);

            if (
                $currentUserType === "franchise" &&
                $student['franchise_id'] != $currentUserID
            ) {
                continue;
            }

            $this->model
                ->update_student_monthly_course_fees([
                    'stu_id' => $stu_id,
                    'monthly_course_fees' => $fees
                ]);
        }

        return $this->lib->success('Data import completed successfully!');
    }
}
