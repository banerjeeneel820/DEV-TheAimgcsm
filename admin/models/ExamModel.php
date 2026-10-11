<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ExamModel extends BaseModel
{
   public function __construct(Database $database)
   {
      parent::__construct($database);
   }

   public function fetch_Global_Exams($dataArr = array())
   {

      $record_status = $dataArr['record_status'];

      $where_Clause = "WHERE exm.record_status = '$record_status'";

      $sql = "SELECT exm.*,COUNT(DISTINCT exq.id) as question_count,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exams exm LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions exq ON exm.id = exq.exam_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON exm.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON exm.course_id = crs.id " . $where_Clause . " ORDER BY exm.id DESC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Student_Exam_Detail($exam_id)
   {

      $sql = "SELECT exm.*,COUNT(DISTINCT exq.id) as question_count,frn.center_name,crs.course_title FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exams exm LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions exq ON exm.id = exq.exam_id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise frn ON exm.franchise_id = frn.id LEFT JOIN " . DB_AIMGCSM . "." . TABLEPREFIX . "course crs ON exm.course_id = crs.id WHERE exm.id = '$exam_id'";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_Single_DB($sql);

      return $resultArr;
   }

   public function manage_Global_Exam($examDataArr)
   {

      $exam_id = $examDataArr['exam_id'];

      //Constructing query variable
      $name = $examDataArr['name'];
      $franchise_id = $examDataArr['franchise_id'];
      $course_id = $examDataArr['course_id'];
      $total_marks = $examDataArr['total_marks'];
      $hours = $examDataArr['hours'];
      $minutes = $examDataArr['minutes'];
      $exam_date = $examDataArr['exam_date'];
      $instructions = $examDataArr['instructions'];
      $record_status = $examDataArr['record_status'];

      $optional_pdf = $examDataArr['optional_pdf'];

      if ($exam_id > 0) {
         $sql = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "exams SET `name` = '$name',`franchise_id` = '$franchise_id',`course_id` = '$course_id',`total_marks` = '$total_marks',`hours` = '$hours',`minutes` = '$minutes',`exam_date` = '$exam_date',`instructions` = '$instructions',`optional_pdf` = '$optional_pdf',`record_status` = '$record_status',`updated_at`=now() WHERE `id`='$exam_id'";
      } else {
         $sql = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exams SET `name` = '$name',`franchise_id` = '$franchise_id',`course_id` = '$course_id',`total_marks` = '$total_marks',`hours` = '$hours',`minutes` = '$minutes',`exam_date` = '$exam_date',`instructions` = '$instructions',`optional_pdf` = '$optional_pdf',`record_status` = '$record_status',`created_at` = now()";
      }

      //echo $sql;exit;

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function fetch_Exam_Questions($exam_id)
   {

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions eqs WHERE eqs.exam_id = '$exam_id' ORDER BY eqs.ordering ASC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Exam_Questions_Limit($dataArr)
   {
      // =========================
      // PAGINATION
      // =========================
      $limit  = (int)($dataArr['limit'] ?? 10);
      $pageNo = (int)($dataArr['page'] ?? 1);
      $offset = ($pageNo - 1) * $limit;

      // =========================
      // WHERE BUILDER
      // =========================
      $where = [];
      $params = [];

      // Mandatory filter
      $where[] = "eqs.exam_id = ?";
      $params[] = $dataArr['exam_id'];

      if (!empty($dataArr['search_string'])) {
         $where[] = "(eqs.ques LIKE ?)";
         $params[] = '%' . $dataArr['search_string'] . '%';
      }

      $where_sql = "WHERE " . implode(" AND ", $where);

      // =========================
      // BASE QUERY (REUSABLE)
      // =========================
      $base_query = "
        FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions eqs
        $where_sql
    ";

      // =========================
      // MAIN DATA QUERY
      // =========================
      $sql_fetch = "
        SELECT 
            eqs.id,
            eqs.exam_id,
            eqs.ques,
            eqs.opt1,
            eqs.opt2,
            eqs.opt3,
            eqs.opt4,
            eqs.cor_ans,
            eqs.marks,
            eqs.record_status,
            eqs.ordering,
            eqs.created_at
        $base_query
        ORDER BY eqs.ordering ASC
    ";

      // Apply pagination
      $sql_fetch .= " LIMIT ? OFFSET ?";
      $params[] = $limit;
      $params[] = $offset;

      // =========================
      // COUNT QUERY
      // =========================
      $sql_count = "SELECT COUNT(*) as total $base_query";

      // =========================
      // EXECUTION
      // =========================
      $resultArr = [];

      // Fetch data
      $resultArr['data'] = $this->global_Fetch_All_DB($sql_fetch, $params);

      // Optional debug
      //$this->debugQuery($sql_fetch, $params);

      // Prepare count params (remove limit + offset)
      $count_params = $params;
      array_pop($count_params); // offset
      array_pop($count_params); // limit

      // Fetch total count
      $resultArr['row_count'] = $this->global_Aggregate_Value_DB($sql_count, $count_params);

      // Extra info
      $resultArr['pageNo'] = $pageNo;
      $resultArr['limit'] = $limit;

      return $resultArr;
   }

   public function update_Exam_Questions($data)
   {
      $exam_id = (int)$data['exam_id'];
      $createList = $data['create'] ?? [];
      $updateList = $data['update'] ?? [];

      mysqli_begin_transaction($this->db);

      try {

         // =============================
         // 1. BULK INSERT (CREATE)
         // =============================
         if (!empty($createList)) {

            $values = [];
            $createdQuestions = [];
            $updatedQuestions = [];

            foreach ($createList as $q) {

               $ques = $this->escape($q['ques'] ?? '');
               $opt1 = $this->escape($q['opt1'] ?? '');
               $opt2 = $this->escape($q['opt2'] ?? '');
               $opt3 = $this->escape($q['opt3'] ?? '');
               $opt4 = $this->escape($q['opt4'] ?? '');
               $cor_ans = (int)$q['cor_ans'];
               $marks = (int)$q['marks'];
               $ordering = (int)$q['ordering'];
               $record_status = $this->escape($q['record_status'] ?? '');

               $values[] = "(
                    '$exam_id',
                    '$ques',
                    '$opt1',
                    '$opt2',
                    '$opt3',
                    '$opt4',
                    '$cor_ans',
                    '$marks',
                    '$ordering',
                    '$record_status',
                    NOW()
                )";
            }

            $sqlInsert = "
                INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions
                (exam_id, ques, opt1, opt2, opt3, opt4, cor_ans, marks, ordering, record_status, updated_at)
                VALUES " . implode(',', $values);

            $returnArr = $this->global_CRUD_DB($sqlInsert);

            $q['id'] = $returnArr['last_insert_id'];

            if ($returnArr['check'] == 'success') {
               $createdQuestions[] = $q;
            }
         }

         // =============================
         // 2. UPDATE EXISTING
         // =============================
         if (!empty($updateList)) {

            foreach ($updateList as $q) {

               $id = (int)$q['id'];

               $ques = $this->escape($q['ques'] ?? '');
               $opt1 = $this->escape($q['opt1'] ?? '');
               $opt2 = $this->escape($q['opt2'] ?? '');
               $opt3 = $this->escape($q['opt3'] ?? '');
               $opt4 = $this->escape($q['opt4'] ?? '');
               $cor_ans = (int)$q['cor_ans'];
               $marks = (int)$q['marks'];
               $ordering = (int)$q['ordering'];
               $record_status = $this->escape($q['record_status'] ?? '');

               $sqlUpdate = "
                    UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions
                    SET 
                        ques = '$ques',
                        opt1 = '$opt1',
                        opt2 = '$opt2',
                        opt3 = '$opt3',
                        opt4 = '$opt4',
                        cor_ans = '$cor_ans',
                        marks = '$marks',
                        ordering = '$ordering',
                        record_status = '$record_status',
                        updated_at = NOW()
                    WHERE id = '$id' AND exam_id = '$exam_id'
                ";

               $returnArr = $this->global_CRUD_DB($sqlUpdate);

               if ($returnArr['check'] == 'success') {
                  $updatedQuestions[] =  $q;
               }
            }
         }

         // =============================
         // COMMIT
         // =============================
         mysqli_commit($this->db);

         return [
            'check' => 'success',
            'message' => 'Questions updated successfully',
            'data' => [
               'created' => $createdQuestions,
               'updated' => $updatedQuestions
            ]
         ];
      } catch (Exception $e) {

         mysqli_rollback($this->db);

         return [
            'check' => 'failure',
            'message' => 'Transaction failed: ' . $e->getMessage()
         ];
      }
   }

   public function save_Exam_Questions_Order($ordeData)
   {

      $exam_id = $ordeData['exam_id'];
      $question_id = $ordeData['question_id'];
      $ordering = $ordeData['ordering'];

      $sql_update_ordering = "UPDATE " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions SET `ordering` = '$ordering',`updated_at`=now() WHERE `exam_id` = '$exam_id' AND `id` = '$question_id'";

      //echo $sql_update_ordering;exit;

      $resultArr = $this->global_CRUD_DB($sql_update_ordering);

      return $resultArr;
   }

   public function delete_All_Questions($exam_id)
   {
      $sql = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_questions WHERE `exam_id` = '$exam_id'";

      //echo $sql;exit;

      $resultArr = $this->global_CRUD_DB($sql);

      return $resultArr;
   }

   public function update_Exam_Validation_Log($flagArr)
   {

      $student_id = $flagArr['student_id'];
      $exam_id = $flagArr['exam_id'];

      //Check if this question is already flagged
      $query_log_caluse = "efq.exam_id = '$exam_id' AND efq.student_id = '$student_id'";

      $sql_count_log = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_log efq WHERE " . $query_log_caluse;

      //echo $sql_count_log;exit();

      $row_count = $this->global_Rows_Count_DB($sql_count_log);

      if ($row_count > 0) {
         return array('check' => 'failure', 'message' => "Exam has already started");
      } else {

         $sql_insert_log = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_log SET `exam_id` = '$exam_id',`student_id` = '$student_id',`status`='started',`started_at` = now()";

         //echo $sql_insert_log;exit();

         //Call save exam questions
         $resultArr = $this->global_CRUD_DB($sql_insert_log);

         if ($resultArr['check'] == "success") {
            $_SESSION['exam_started'] = "true";
            $_SESSION['exam_id'] = $exam_id;
         }

         return $resultArr;
      }
   }

   public function update_Exam_Answer($postData)
   {

      $student_id = $_SESSION['user_id'];
      $exam_id = $postData['exam_id'];
      $answers = $postData['answers'];

      $answerArr = array();
      $newAnswerArr = array();
      $removedAnswerArr = array();
      $flagArr = array();

      //Fetch flagged questions
      $addParamsArr['exam_id'] = $exam_id;
      $addParamsArr['student_id'] = $student_id;

      //Formatting answers array
      $answeredQuestionsArr = $this->fetch_User_Exam_Answers($addParamsArr);

      foreach ($answeredQuestionsArr as $aindex => $answer) {
         $answerArr[$aindex] = $answer->ques_id;
      }

      //Formatting flagged questions array
      $flagQuestions = $this->fetch_Flagged_Questions($addParamsArr);

      foreach ($flagQuestions as $findex => $flag) {
         $flagArr[$findex] = $flag->ques_id;
      }

      $sql_delete_answer = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_answers WHERE `exam_id` = '$exam_id' AND `student_id` = '$student_id'";

      //echo $sql_delete_answer;exit;

      $resultArr = $this->global_CRUD_DB($sql_delete_answer);

      //print_r($answers);exit;

      if (!empty($answers)) {
         foreach ($answers as $index => $answer) {
            $ques_id = $answer['qid'];
            $selection = $answer['selection'];

            if (array_key_exists('selection', $answer)) {

               $newAnswerArr[$index] = $ques_id;

               $sql_insert_answer = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_answers SET `exam_id` = '$exam_id',`student_id` = '$student_id',`ques_id`='$ques_id',`answer`='$selection',`created_at` = now()";

               //echo $sql_insert_answer;exit();

               //Call save exam questions
               $resultArr = $this->global_CRUD_DB($sql_insert_answer);
            }
         }

         $newAnswerArr = array_values($newAnswerArr);
      } else {
         $newAnswerArr = array();
      }

      //Finding removed answer array
      foreach ($answerArr as $rindex => $canswer) {
         if (!in_array($canswer, $newAnswerArr)) {
            $removedAnswerArr[$rindex] = $canswer;
         }
      }

      $removedAnswerArr = array_values($removedAnswerArr);

      if ($resultArr['check'] == "success") {
         return array(
            'check' => 'success',
            'answeredQuestions' => $newAnswerArr,
            'removedAnswerArr' => $removedAnswerArr,
            'flaggedQuestions' => $flagArr
         );
      } else {
         return $resultArr;
      }
   }

   public function fetch_User_Exam_Answers($answerParamArr)
   {

      $exam_id = $answerParamArr['exam_id'];
      $student_id = $answerParamArr['student_id'];

      $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_answers ea WHERE ea.exam_id = '$exam_id' AND ea.student_id = '$student_id' ORDER BY ea.ques_id ASC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function fetch_Flagged_Questions($answerParamArr)
   {

      $exam_id = $answerParamArr['exam_id'];
      $student_id = $answerParamArr['student_id'];

      $sql = "SELECT efq.ques_id FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions efq WHERE efq.exam_id = '$exam_id' AND efq.student_id = '$student_id' ORDER BY efq.ques_id ASC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   }

   public function update_Flag_Question_Exam($flagArr)
   {

      $student_id = $_SESSION['user_id'];
      $exam_id = $flagArr['exam_id'];
      $ques_id = $flagArr['ques_id'];

      //Check if this question is already flagged
      $query_flag_caluse = "efq.exam_id = '$exam_id' AND efq.ques_id = '$ques_id' AND efq.student_id = '$student_id'";

      $sql_count_flagged = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions efq WHERE " . $query_flag_caluse;

      //echo $sql_count_flagged;exit();

      $row_count = $this->global_Rows_Count_DB($sql_count_flagged);

      if ($row_count > 0) {
         $flag_status = 'deleted';

         $sql_delete_answer = "DELETE FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions WHERE `exam_id` = '$exam_id' AND `ques_id` = '$ques_id' AND `student_id` = '$student_id'";

         //echo $sql_delete_answer;exit();

         $resultArr = $this->global_CRUD_DB($sql_delete_answer);
      } else {
         $flag_status = 'added';

         $sql_insert_flag = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_flaged_questions SET `exam_id` = '$exam_id',`student_id` = '$student_id',`ques_id`='$ques_id',`updated_at` = now()";

         //echo $sql_insert_flag;exit();

         //Call save exam questions
         $resultArr = $this->global_CRUD_DB($sql_insert_flag);
      }

      if ($resultArr['check'] == "success") {

         //Check if this question is attempted
         $sql_check_answered = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_answers ea WHERE ea.exam_id = '$exam_id' AND ea.ques_id = '$ques_id' AND ea.student_id = '$student_id'";

         //echo $sql_check_answered;exit;

         $answered_row_count = $this->global_Rows_Count_DB($sql_check_answered);

         //var_dump($answered_row_count);exit;

         if ($flag_status == "added") {

            if ($answered_row_count > 0) {
               $flag_status = "attempted_review";
            } else {
               $flag_status = "added_reveiw";
            }
         } else {
            if ($answered_row_count > 0) {
               $flag_status = "attempted";
            } else {
               $flag_status = "deleted";
            }
         }
         return array('check' => 'success', 'flag_status' => $flag_status, 'qId' => $ques_id);
      } else {
         return $resultArr;
      }
   }

   public function update_Viewed_Question_Exam($flagArr)
   {

      $student_id = $_SESSION['user_id'];
      $exam_id = $flagArr['exam_id'];
      $ques_id = $flagArr['ques_id'];

      //Check if this question is already flagged
      $query_viewed_caluse = "efq.exam_id = '$exam_id' AND efq.ques_id = '$ques_id' AND efq.student_id = '$student_id'";

      $sql_count_viewed = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_viewed_questions efq WHERE " . $query_viewed_caluse;

      //echo $sql_count_viewed;exit();

      $row_count = $this->global_Rows_Count_DB($sql_count_viewed);

      if ($row_count > 0) {
         return array('check' => 'failure', 'qId' => $ques_id);
      } else {

         $sql_insert_view = "INSERT INTO " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_viewed_questions SET `exam_id` = '$exam_id',`student_id` = '$student_id',`ques_id`='$ques_id',`updated_at` = now()";

         //echo $sql_insert_view;exit();

         //Call save exam questions
         $resultArr = $this->global_CRUD_DB($sql_insert_view);

         if ($resultArr['check'] == "success") {
            return array('check' => 'success', 'qId' => $ques_id);
         } else {
            return $resultArr;
         }
      }
   }

   public function fetch_Viewed_Questions($answerParamArr)
   {

      $exam_id = $answerParamArr['exam_id'];
      $student_id = $answerParamArr['student_id'];

      $sql = "SELECT evq.ques_id FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "exam_viewed_questions evq WHERE evq.exam_id = '$exam_id' AND evq.student_id = '$student_id' ORDER BY evq.ques_id ASC";

      //echo $sql;exit();

      $resultArr = $this->global_Fetch_All_DB($sql);

      return $resultArr;
   } 

}   