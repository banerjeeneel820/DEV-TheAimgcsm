<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ExamController extends BaseController
{
    private $examService;
    private $permissionService;
    private $courseFranchiseService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->examService = $container->get(ExamService::class);
        $this->permissionService = $container->get(PermissionService::class);
        $this->courseFranchiseService = $container->get(CourseFranchiseService::class);
    }

    /*
    |--------------------------------------------------------------------------
    | View exam data methods
    |--------------------------------------------------------------------------
    */
    public function fetch_exam_data($data = [])
    {
        $user_role_slug = 'view_exam';

        // =========================
        // Assets
        // =========================
        $assets = Asset::load("exam_list");

        // =========================
        // Permission
        // =========================
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'exam_data' => [],
                    'page_type' => 'exams'
                ],
                'Exam List',
                $assets,
                false,
                false
            );
        }

        // =========================
        // Prepare Filters
        // =========================
        $filters = $this->examService->prepareExamFilters($data);

        // =========================
        // Fetch Exams
        // =========================
        $examData = $this->examService->getViewExams($filters);

        // =========================
        // Final Response
        // =========================
        return $this->page(
            [
                'exam_data' => $examData,
                'page_type' => 'exams'
            ],
            'Exam List',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage exam view data methods
    |--------------------------------------------------------------------------
    */
    public function manage_exam_data_view($data = [])
    {
        $type    = 'exams';
        $exam_id = isset($data['id']) ? (int)$data['id'] : 0;

        // =========================
        // Assets
        // =========================
        $assets = Asset::load("manage_exam_form");

        // =========================
        // Determine Permission Slug
        // =========================
        $user_role_slug = $exam_id > 0 ? 'update_exam' : 'create_exam';

        // =========================
        // Permission
        // =========================
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'exam_details'  => [],
                    'franchise_data' => [],
                    'course_data'   => [],
                    'page_type'     => $type
                ],
                'Manage Exam',
                $assets,
                false,
                false
            );
        }

        // =========================
        // Fetch Static Data
        // =========================
        $activeData = $this->courseFranchiseService->fetch_Active_Course_Franchise_Data();

        // =========================
        // Fetch Exam Details
        // =========================
        $examDetails = [];

        if ($exam_id > 0) {
            $examDetails = $this->examService->getExamDetails($exam_id);
        }

        // =========================
        // Final Response
        // =========================
        return $this->page(
            [
                'exam_details'   => $examDetails,
                'franchise_data' => $activeData['franchise'],
                'course_data'    => $activeData['course'],
                'page_type'      => $type
            ],
            'Manage Exam',
            $assets,
            true,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage exam data methods
    |--------------------------------------------------------------------------
    */
    public function manage_exam($data)
    {
        $dir = 'exam';

        // =========================
        // Prepare Form Data
        // =========================
        $formDataArr = $this->examService->prepareExamFormData();

        $isUpdate = !empty($formDataArr['exam_id']) && $formDataArr['exam_id'] > 0;

        // =========================
        // Permission Check
        // =========================
        $permission = $this->examService->validateExamPermission($isUpdate);

        if ($permission['check'] === 'failure') {
            return $permission;
        }

        // =========================
        // Handle File Upload
        // =========================
        $uploadReturnArr = $this->examService->handleExamPdfUpload(
            $isUpdate,
            $dir
        );

        if ($uploadReturnArr['check'] === 'failure') {
            return $uploadReturnArr;
        }

        $formDataArr['optional_pdf'] = $uploadReturnArr['file_name'];

        // =========================
        // Save Exam Data
        // =========================
        $returnArr = $this->examService->saveExamData($formDataArr);

        // =========================
        // Post Processing
        // =========================
        return $this->examService->handleExamSaveResponse(
            $returnArr,
            $uploadReturnArr,
            $formDataArr,
            $isUpdate,
            $dir
        );
    }

    /*
    |--------------------------------------------------------------------------
    | View exam's question data methods
    |--------------------------------------------------------------------------
    */
    public function manage_exam_question_view($data = [])
    {
        $type    = 'exams';
        $exam_id = isset($data['exm_id'])
            ? (int)$data['exm_id']
            : 0;

        // =========================
        // Assets
        // =========================
        $assets = Asset::load("manage_exam_questions");

        // =========================
        // Invalid Exam
        // =========================
        if ($exam_id <= 0) {

            return $this->page(
                [
                    'exam_details' => [],
                    'questions'    => [],
                    'page_type'    => $type
                ],
                'Manage Exam Questions',
                $assets,
                false,
                false
            );
        }

        // =========================
        // Permission
        // =========================
        $hasPermission = $this->permissionService
            ->checkUserRolePermission('update_exam');

        if (!$hasPermission) {

            return $this->page(
                [
                    'exam_details' => [],
                    'questions'    => [],
                    'page_type'    => $type
                ],
                'Manage Exam Questions',
                $assets,
                false,
                false
            );
        }

        // =========================
        // Fetch Data
        // =========================
        $examDetails = $this->examService
            ->getExamDetails($exam_id);

        $questions = $this->examService
            ->getExamQuestions($exam_id);

        // =========================
        // Final Response
        // =========================
        return $this->page(
            [
                'exam_details' => $examDetails,
                'questions'    => $questions,
                'page_type'    => $type
            ],
            'Manage Exam Questions',
            $assets,
            false,
            true
        );
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

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Fetch Questions
        // -----------------------------
        $questions = $this->examService->getExamQuestions($exam_id);

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

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Basic Data
        // -----------------------------
        $paramArr['exam_id'] = $post('exam_id') ?? NULL;
        $paramArr['search_string'] = $post('search_string');

        // -----------------------------
        // Pagination Data
        // -----------------------------
        $paramArr['page']   = $post('page') ?? 1;
        $paramArr['limit']  = $post('limit') ?? 10;

        // -----------------------------
        // Fetch Questions
        // -----------------------------
        $returnArr = $this->examService->getExamQuestionsLimit($paramArr);

        // -----------------------------
        // Response
        // -----------------------------
        return [
            'check'     => 'success',
            'questions' => $returnArr['data'],
            'total_count' => $returnArr['row_count']
        ];
    }

    public function manage_exam_questions($data)
    {
        // -----------------------------
        // Permission Check
        // -----------------------------
        $user_role_slug = 'update_exam';

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return [
                'check' => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // Raw Input
        // -----------------------------
        $postData = $_POST;

        // -----------------------------
        // Validate Exam ID
        // -----------------------------
        $exam_id = $postData['exam_id'];

        if (empty($exam_id) || !is_numeric($exam_id)) {
            return [
                'check' => 'failure',
                'message' => "Invalid exam ID"
            ];
        }

        $createList = $postData['create'] ?? [];
        $updateList = $postData['update'] ?? [];

        // -----------------------------
        // Sanitization Function
        // -----------------------------
        $sanitizeQuestion = function ($q, $isUpdate = false) {
            return [
                'id'            => $isUpdate ? (int)($q['id'] ?? 0) : null,
                'ordering'      => (int)($q['ordering'] ?? 0),
                'ques'          => trim($q['ques'] ?? ''),
                'opt1'          => trim($q['opt1'] ?? ''),
                'opt2'          => trim($q['opt2'] ?? ''),
                'opt3'          => trim($q['opt3'] ?? ''),
                'opt4'          => trim($q['opt4'] ?? ''),
                'cor_ans'       => (int)($q['cor_ans'] ?? 0),
                'marks'         => (int)($q['marks'] ?? 0),
                'record_status' => $q['record_status'] ?? 'active',
            ];
        };

        // -----------------------------
        // Process Create
        // -----------------------------
        $cleanCreate = [];

        foreach ($createList as $q) {
            $cleanCreate[] = $sanitizeQuestion($q, false);
        }

        // -----------------------------
        // Process Update
        // -----------------------------
        $cleanUpdate = [];

        foreach ($updateList as $q) {
            if (empty($q['id'])) continue; // safety

            $cleanUpdate[] = $sanitizeQuestion($q, true);
        }

        // -----------------------------
        // Final Payload
        // -----------------------------
        $payload = [
            'exam_id' => (int)$exam_id,
            'create'  => $cleanCreate,
            'update'  => $cleanUpdate
        ];

        // -----------------------------
        // DB Operation
        // -----------------------------
        $result = $this->examService->updateExamQuestions($payload);

        if ($result['check'] === 'success') {

            $returnArr = [
                'check' => 'success',
                'message' => $result['message'] ?? 'Questions saved successfully',
                'created' => $result['data']['created'] ?? [],
                'updated' => $result['data']['updated'] ?? []
            ];
        } else {
            $returnArr = [
                'check' => 'failure',
                'message' => $result['message'] ?? 'Something went wrong'
            ];
        }

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

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // Fetch Current Ordering
        // -----------------------------
        $current_questions = $this->examService
            ->getExamQuestions($formDataArr['exam_id']);

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

            $returnArr = $this->examService
                ->saveQuestionsOrder($formDataArr);
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

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
        }

        // -----------------------------
        // DB Operation
        // -----------------------------
        $returnArr = $this->examService->deleteAllQuestions($exam_id);

        // -----------------------------
        // Response
        // -----------------------------
        return $returnArr;
    }

    /*
    |--------------------------------------------------------------------------
    | Student exam's handler methods
    |--------------------------------------------------------------------------
    */
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
        $returnArr = $this->examService
            ->updateExamValidationLog($formDataArr);

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
        $returnArr = $this->examService
            ->updateExamAnswer($postData);

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
        $returnArr = $this->examService
            ->updateFlagQuestionExam($formDataArr);

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
        $returnArr = $this->examService
            ->updateViewedQuestionExam($formDataArr);

        // -----------------------------
        // Response
        // -----------------------------
        return $returnArr;
    }
}
