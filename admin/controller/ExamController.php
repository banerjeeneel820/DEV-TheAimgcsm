<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ExamController extends BaseController
{
    private $utilityService;

    public function __construct()
    {
        parent::__construct();
        $this->utilityService = new UtilityService($this->interface, $this->lib);
    }

    public function manage_exam($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];
        $dir = 'exam';

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Basic Data
        // -----------------------------
        $formDataArr['exam_id'] = $post('exam_id');

        $isUpdate = !empty($formDataArr['exam_id']) && $formDataArr['exam_id'] > 0;
        $user_role_slug = $isUpdate ? 'update_exam' : 'create_exam';

        // -----------------------------
        // Permission Check
        // -----------------------------
        if (!$this->utilityService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

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

        // -----------------------------
        // File Upload (PDF)
        // -----------------------------
        $uploadReturnArr = ['check' => 'skip'];

        if (!empty($_FILES["local_exam_pdf"]["size"])) {

            $uploadReturnArr = $this->lib->upload_file('local_exam_pdf', $dir);

            if ($uploadReturnArr['check'] !== 'success') {
                return ['check' => 'failure', 'msg' => "An error occurred while trying to upload exam pdf!"];
            }

            $formDataArr['optional_pdf'] = $uploadReturnArr['fileName'];
        } else {
            $formDataArr['optional_pdf'] = $isUpdate
                ? $post('hidden_optional_pdf')
                : null;
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->interface->manage_Global_Exam($formDataArr);

        // -----------------------------
        // Post Processing
        // -----------------------------
        if ($returnArr['check'] == 'success') {

            if (
                $isUpdate &&
                $uploadReturnArr['check'] === 'success' &&
                !empty($_FILES["local_exam_pdf"]["size"])
            ) {
                unlink(USER_UPLOAD_DIR . $dir . '/' . $post('hidden_optional_pdf'));
            }
        } else {

            if ($uploadReturnArr['check'] === 'success') {
                unlink(USER_UPLOAD_DIR . $dir . '/' . $formDataArr['optional_pdf']);
            }

            return ['check' => 'failure', 'message' => "Something went wrong!"];
        }

        return $returnArr;
    }

    public function fetch_all_questions($data)
    {
        //Declaring necessary variables
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Basic Data
        // -----------------------------
        $exam_id = $post('exam_id');

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = 'update_exam';

        if (!$this->utilityService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Fetch Questions
        // -----------------------------
        $questions = $this->interface->fetch_Exam_Questions($exam_id);

        // -----------------------------
        // Response
        // -----------------------------
        return [
            'check'     => 'success',
            'questions' => $questions
        ];
    }

    public function fetch_limited_questions($data)
    {
        //Declaring necessary variables
        $paramArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = 'update_exam';

        if (!$this->utilityService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Basic Data
        // -----------------------------
        $paramArr['exam_id'] = $post('exam_id');

        // -----------------------------
        // Pagination Data
        // -----------------------------
        $paramArr['offset'] = $post('offset') ?? 0;
        $paramArr['limit']  = $post('limit') ?? 10;

        // -----------------------------
        // Fetch Questions
        // -----------------------------
        $questions = $this->interface->fetch_Exam_Questions_Limit($paramArr);

        // -----------------------------
        // Response
        // -----------------------------
        return [
            'check'     => 'success',
            'questions' => $questions
        ];
    }

    public function manage_exam_questions($data)
    {
        //Declaring necessary variables
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = 'update_exam';

        if (!$this->utilityService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $postData = $_POST; // keeping as-is (array structure required)

        // optional (kept for compatibility, even if unused)
        $question_count = isset($postData['questions']) ? count($postData['questions']) : 0;

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->interface->update_Exam_Questions($postData);

        // -----------------------------
        // Response
        // -----------------------------
        return $returnArr;
    }

    public function sort_exam_questions($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $currentQuestionArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Basic Data
        // -----------------------------
        $formDataArr['exam_id'] = $post('exam_id');
        $questions = $_POST['questions'] ?? [];

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = 'update_exam';

        if (!$this->utilityService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Fetch Current Ordering
        // -----------------------------
        $current_questions = $this->interface
            ->fetch_Exam_Questions($formDataArr['exam_id']);

        foreach ($current_questions as $question) {
            $currentIndex = (int)$question->ordering - 1;
            $currentQuestionArr[$currentIndex] = $question->id;
        }

        // -----------------------------
        // Find Differences
        // -----------------------------
        $diffQuestionSortArr = array_diff_assoc($questions, $currentQuestionArr);

        // -----------------------------
        // Update Changed Order Only
        // -----------------------------
        foreach ($diffQuestionSortArr as $index => $id) {

            $formDataArr['question_id'] = $id;
            $formDataArr['ordering'] = $index + 1;

            $returnArr = $this->interface
                ->save_Exam_Questions_Order($formDataArr);
        }

        // -----------------------------
        // Response
        // -----------------------------
        return $returnArr ?: ['check' => 'success'];
    }

    public function delete_all_questions($data)
    {
        //Declaring necessary variables
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Basic Data
        // -----------------------------
        $exam_id = $post('exam_id');

        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = 'update_exam';

        if (!$this->utilityService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->interface->delete_All_Questions($exam_id);

        // -----------------------------
        // Response
        // -----------------------------
        return $returnArr;
    }

    public function set_exam_validation_log($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Access Check (Student Only)
        // -----------------------------
        if ($_SESSION['user_type'] !== "student") {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $formDataArr['exam_id']    = $post('exam_id');
        $formDataArr['student_id'] = $_SESSION['user_id'];

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->interface
            ->update_Exam_Validation_Log($formDataArr);

        // -----------------------------
        // Response
        // -----------------------------
        return $returnArr;
    }

    public function manage_exam_answer($data)
    {
        //Declaring necessary variables
        $returnArr = [];

        // -----------------------------
        // Access Check (Student Only)
        // -----------------------------
        if ($_SESSION['user_type'] !== "student") {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $postData = $_POST; // keeping raw (structured/nested data)

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->interface
            ->update_Exam_Answer($postData);

        // -----------------------------
        // Response
        // -----------------------------
        return $returnArr;
    }

    public function flag_question_review($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Access Check (Student Only)
        // -----------------------------
        if ($_SESSION['user_type'] !== "student") {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $formDataArr['exam_id'] = $post('exam_id');
        $formDataArr['ques_id'] = $post('qId');

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->interface
            ->update_Flag_Question_Exam($formDataArr);

        // -----------------------------
        // Response
        // -----------------------------
        return $returnArr;
    }

    public function record_viewd_questions($data)
    {
        //Declaring necessary variables
        $formDataArr = [];
        $returnArr = [];

        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Access Check (Student Only)
        // -----------------------------
        if ($_SESSION['user_type'] !== "student") {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Collect Data
        // -----------------------------
        $formDataArr['exam_id'] = $post('exam_id');
        $formDataArr['ques_id'] = $post('qId');

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->interface
            ->update_Viewed_Question_Exam($formDataArr);

        // -----------------------------
        // Response
        // -----------------------------
        return $returnArr;
    }
}
