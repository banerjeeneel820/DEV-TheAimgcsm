<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ExamService
{
    public function __construct(
        private GlobalInterfaceModel $model,
        private GlobalLibraryHandler $lib,
        private PermissionService $permissionService,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | View exam data helper methods
    |--------------------------------------------------------------------------
    */
    public function prepareExamFilters($data)
    {
        return [
            'record_status' => $data['record_status'] ?? 'active'
        ];
    }

    public function getViewExams($filters)
    {
        return $this->model->fetch_Global_Exams($filters);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage exam view data helper methods
    |--------------------------------------------------------------------------
    */
    public function getExamDetails($exam_id)
    {
        return $this->model->fetch_Student_Exam_Detail($exam_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage exam data methods
    |--------------------------------------------------------------------------
    */
    public function prepareExamFormData()
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $formDataArr = [];

        // -----------------------------
        // Basic Data
        // -----------------------------
        $formDataArr['exam_id'] = $post('exam_id');

        // -----------------------------
        // Core Fields
        // -----------------------------
        $formDataArr['name']          = $post('name');
        $formDataArr['franchise_id']  = $post('franchise_id');
        $formDataArr['course_id']     = $post('course_id');
        $formDataArr['total_marks']   = $post('total_marks');
        $formDataArr['hours']         = $post('hours');
        $formDataArr['minutes']       = $post('minutes');
        $formDataArr['instructions']  = $post('instructions');
        $formDataArr['record_status'] = $post('record_status');

        // -----------------------------
        // Date Handling
        // -----------------------------
        $examDate = $post('exam_date');

        if (!empty($examDate)) {
            $examDate = str_replace('/', '-', $examDate);
            $formDataArr['exam_date'] = date('Y-m-d', strtotime($examDate));
        }

        return $formDataArr;
    }

    public function validateExamPermission($isUpdate)
    {
        $user_role_slug = $isUpdate
            ? 'update_exam'
            : 'create_exam';

        if (
            !$this->permissionService->checkUserRolePermission(
                $user_role_slug,
                "hard"
            )
        ) {
            return [
                'check'  => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        return ['check' => 'success'];
    }

    public function handleExamPdfUpload($isUpdate, $dir)
    {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        $uploadReturnArr = [
            'check'     => 'skip',
            'file_name' => null
        ];

        if (!empty($_FILES["local_exam_pdf"]["size"])) {

            $upload = $this->lib->upload_file(
                'local_exam_pdf',
                $dir
            );

            if ($upload['check'] !== 'success') {
                return [
                    'check'  => 'failure',
                    'message' => "An error occurred while trying to upload exam pdf!"
                ];
            }

            $uploadReturnArr['check']     = 'success';
            $uploadReturnArr['file_name'] = $upload['fileName'];
        } else {

            $uploadReturnArr['file_name'] = $isUpdate
                ? $post('hidden_optional_pdf')
                : null;
        }

        return $uploadReturnArr;
    }

    public function saveExamData($data)
    {
        return $this->model->manage_Global_Exam($data);
    }

    public function handleExamSaveResponse(
        $returnArr,
        $uploadReturnArr,
        $formDataArr,
        $isUpdate,
        $dir
    ) {
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        if ($returnArr['check'] === 'success') {

            // Delete old pdf after successful update
            if (
                $isUpdate &&
                $uploadReturnArr['check'] === 'success' &&
                !empty($_FILES["local_exam_pdf"]["size"])
            ) {

                $oldPdf = $post('hidden_optional_pdf');

                if (!empty($oldPdf)) {
                    unlink(USER_UPLOAD_DIR . $dir . '/' . $oldPdf);
                }
            }

            return $returnArr;
        }

        // Delete newly uploaded pdf if DB failed
        if (
            $uploadReturnArr['check'] === 'success' &&
            !empty($formDataArr['optional_pdf'])
        ) {

            unlink(
                USER_UPLOAD_DIR .
                    $dir .
                    '/' .
                    $formDataArr['optional_pdf']
            );
        }

        return [
            'check'  => 'failure',
            'message' => "Something went wrong!"
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | View exam's question data helper methods
    |--------------------------------------------------------------------------
    */
    public function getExamQuestions($exam_id)
    {
        return $this->model->fetch_Exam_Questions($exam_id);
    }

    public function getExamQuestionsLimit($exam_id)
    {
        return $this->model->fetch_Exam_Questions_Limit($exam_id);
    }

    public function updateExamQuestions($payload)
    {
        return $this->model->update_Exam_Questions($payload);
    }

    public function saveQuestionsOrder($data)
    {
        return $this->model->save_Exam_Questions_Order($data);
    }

    public function deleteAllQuestions($exam_id)
    {
        return $this->model->delete_All_Questions($exam_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Student exam's handler methods
    |--------------------------------------------------------------------------
    */
    public function updateExamValidationLog($formDataArr)
    {
        return $this->model->update_Exam_Validation_Log($formDataArr);
    }

    public function updateExamAnswer($postData)
    {
        return $this->model->update_Exam_Answer($postData);
    }

    public function updateFlagQuestionExam($postData)
    {
        return $this->model->update_Flag_Question_Exam($postData);
    }

    public function updateViewedQuestionExam($postData)
    {
        return $this->model->update_Viewed_Question_Exam($postData);
    }
}
