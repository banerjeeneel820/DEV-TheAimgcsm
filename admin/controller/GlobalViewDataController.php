<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class GlobalViewDataController extends BaseController
{
  
  private $utilityService;
  private $globalReturnArr;  
  private $memObj;

  public function __construct()
  { 
    parent::__construct();
    $this->globalReturnArr = [];
    $this->utilityService = new UtilityService($this->interface,$this->lib);
  }

  private function getDashboardData($type, $fetchType, $params, $franchise_id = null)
  {
    $fetchType = $fetchType ?: 'weekly';

    $cacheKey = "{$type}_dashboard_{$fetchType}";
    
    if ($franchise_id) {
        $cacheKey .= "_{$franchise_id}";
    }

    $callback = function () use ($type, $params) {
        if ($type === 'student') {
            return $this->interface
                ->fetch_Dashboard_Student_Data($params);
        }

        return $this->interface
            ->fetch_Dashboard_Receipt_Data($params);
    };

    return $this->cache->get($cacheKey, $callback);
  }

  public function fetchUserDashboardData()
  {
    $this->globalReturnArr['page_type'] = 'dashboard';

    $page_permission = $this->utilityService->checkUserRolePermission('view_dashboard');

    $site_backup_permission = $this->utilityService->checkUserRolePermission('manage_site_backup');

    if (!$page_permission) {
        $this->globalReturnArr['data'] = ['page' => 'dashboard'];
        $this->globalReturnArr['page_permission'] = false;
        return $this->globalReturnArr;
    }

    // -------------------------
    // SAFE GET HANDLING (SANITIZED)
    // -------------------------
    $dataType = isset($_GET['dataType']) 
        ? $this->get('dataType')
        : null;

    $fetchType = isset($_GET['fetchType']) 
        ? $this->get('fetchType')
        : 'weekly';

    // fallback safety
    $fetchType = !empty($fetchType) ? $fetchType : 'weekly';

    // -------------------------
    // PARAMS SETUP
    // -------------------------
    $stuParamArr = [
        'fetchType' => ($dataType === 'student') ? $fetchType : 'weekly'
    ];

    $rcptParamArr = [
        'fetchType' => ($dataType === 'receipt') ? $fetchType : 'weekly'
    ];

    if ($_SESSION['user_type'] === "franchise") {
        $stuParamArr['franchise_id']  = (int) $_SESSION['user_id'];
        $rcptParamArr['franchise_id'] = (int) $_SESSION['user_id'];
    }

    $enquiryParamArr = [
        'limit' => 20,
        'pageNo' => 1,
        'record_status' => 'active'
    ];

    // -------------------------
    // SITE BACKUP
    // -------------------------
    $this->globalReturnArr['site_bak_files'] = $site_backup_permission
        ? $this->lib->fetchSiteBackupFiles()
        : [];

    // -------------------------
    // CACHED COMMON DATA
    // -------------------------
    $this->globalReturnArr['news_data'] = $this->cache->get(
        "news_data",
        fn() => $this->interface->fetch_Global_News(['record_status' => 'active'])
    );

    $this->globalReturnArr['course_data'] = $this->cache->get(
        "course_data",
        fn() => $this->interface->fetch_Global_Course()
    );

    // -------------------------
    // BACKUP QUEUE STATUS
    // -------------------------
    $this->globalReturnArr['site_backup_queue'] =
        !empty($this->interface->check_Task_Status());

    // -------------------------
    // ROLE BASED LOGIC
    // -------------------------
    $userType = $_SESSION['user_type'];

    if (in_array($userType, ['developer', 'admin'])) {

        $this->globalReturnArr['student_data'] = $this->getDashboardData(
            'student',
            $fetchType,
            $stuParamArr
        );

        $this->globalReturnArr['receipt_data'] = $this->getDashboardData(
            'receipt',
            $fetchType,
            $rcptParamArr
        );

        $this->globalReturnArr['enquiry_data'] = $this->cache->get(
            "enquiry_data",
            fn() => $this->interface->fetch_Global_Enquiry($enquiryParamArr)
        );

    } elseif ($userType === 'franchise') {

        $franchise_id = (int) $_SESSION['user_id'];

        $franchise = $this->interface
            ->fetch_Global_Single_Franchise($franchise_id);

        $owned_status = $franchise->owned_status ?? 'no';

        $this->globalReturnArr['student_data'] = $this->getDashboardData(
            'student',
            $fetchType,
            $stuParamArr,
            $franchise_id
        );

        if ($owned_status === 'yes') {
            $this->globalReturnArr['receipt_data'] = $this->getDashboardData(
                'receipt',
                $fetchType,
                $rcptParamArr,
                $franchise_id
            );
        } else {
            $this->globalReturnArr['gallery_data'] = $this->cache->get(
                "gallery_data",
                fn() => $this->interface->fetch_Gallery_Arr('active')
            );
        }
    }

    $this->globalReturnArr['page_permission'] = true;

    return $this->globalReturnArr;
  }

  public function view_Franchise_Required_Data()
  {
    $user_role_slug = 'view_franchise';
    $type = 'franchise';

    $this->globalReturnArr['page_title'] = "View Franchise";
    $this->globalReturnArr['page_type'] = $type;
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {
      if (isset($_GET['record_status'])) {
        $record_status = $_GET['record_status'];
      } else {
        $record_status = 'active';
      }

      //Call read global blog method
      $this->globalReturnArr['data'] = $this->interface->fetch_Global_Franchise($record_status);

      if ($this->memObj == null) {
        $this->globalReturnArr['data'] = $this->interface->fetch_Global_Franchise($record_status);
      } else {
        $response = $this->memObj->get("franchise_data_$record_status");
        //Check if data stored in memcached
        if ($response) {
          $this->globalReturnArr['data'] = $response;
        } else {
          $response = $this->interface->fetch_Global_Franchise($record_status);
          $this->memObj->set("franchise_data_$record_status", $response);
          //Set data into a key of memcached
          $this->globalReturnArr['data'] = $response;
        }
      }
    } else {
      $this->globalReturnArr['data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function edit_Franchise_Profile_Data()
  {
    $franchise_id = $_SESSION['user_id'];

    $user_role_slug = 'manage_profile';
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {
      //Fetching franchise detail
      $this->globalReturnArr['frnachise_data'] = $this->interface->fetch_Global_Single_Franchise($franchise_id);
    } else {
      $this->globalReturnArr['frnachise_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_Franchise_Required_Data($fetch_type)
  {

    if ($_SESSION['user_type'] == 'franchise') {
      $franchise_id = $_SESSION['user_id'];
    } else {
      $franchise_id = $_GET['id'];
    }

    $type = 'franchise';

    if (isset($_GET['id'])) {
      $user_role_slug = 'update_franchise';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetching franchise detail
        $this->globalReturnArr['frnachise_data'] = $this->interface->fetch_Global_Single_Franchise($franchise_id);
      } else {
        $this->globalReturnArr['frnachise_data'] = array();
      }
    } else {
      $user_role_slug = 'create_franchise';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['frnachise_data'] = array();
    }

    $this->globalReturnArr['page_type'] = $type;

    return $this->globalReturnArr;
  }

  public function view_Course_Required_Data()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission('view_course');
    $type = 'course';
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Fetch course data based on parameters
      if ($this->memObj == null) {
        $this->globalReturnArr['data'] = $this->interface->fetch_Global_Course($record_status);
      } else {
        $response = $this->memObj->get("course_data_$record_status");
        //Check if data stored in memcached
        if ($response) {
          $this->globalReturnArr['data'] = $response;
        } else {
          $response = $this->interface->fetch_Global_Course($record_status);
          $this->memObj->set("course_data_$record_status", $response);
          //Set data into a key of memcached
          $this->globalReturnArr['data'] = $response;
        }
      }
    } else {
      $this->globalReturnArr['data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_Course_Required_Data()
  {
    $course_id = $_GET['id'];
    $type = 'course';

    if (isset($_GET['id'])) {
      $user_role_slug = 'update_course';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetching course detail
        $this->globalReturnArr['course_data'] = $this->interface->fetch_Global_Single_Course($course_id);
      } else {
        $this->globalReturnArr['course_data'] = array();
      }
    } else {
      $user_role_slug = 'create_course';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['course_data'] = array();
    }

    $this->globalReturnArr['page_type'] = $type;

    return $this->globalReturnArr;
  }

  public function fetch_Active_Course_Franchise_Data()
  {

    $activeCourseFranchiseArr = [];

    //Fetch franchise data based on memcached
    if ($this->memObj == null) {
      $activeCourseFranchiseArr['franchise'] = $this->interface->fetch_Global_Franchise("active");
    } else {
      $response = $this->memObj->get("franchise_data_active");
      //Check if data stored in memcached
      if ($response) {
        $activeCourseFranchiseArr['franchise'] = $response;
      } else {
        $response = $this->interface->fetch_Global_Franchise("active");
        $this->memObj->set("franchise_data_active", $response);
        //Set data into a key of memcached
        $activeCourseFranchiseArr['franchise'] = $response;
      }
    }

    //Fetch course data based on memcached
    if ($this->memObj == null) {
      $activeCourseFranchiseArr['course'] = $this->interface->fetch_Global_Course("active");
    } else {
      $response = $this->memObj->get("course_data_active");
      //Check if data stored in memcached
      if ($response) {
        $activeCourseFranchiseArr['course'] = $response;
      } else {
        $response = $this->interface->fetch_Global_Course("active");
        $this->memObj->set("course_data_active", $response);
        //Set data into a key of memcached
        $activeCourseFranchiseArr['course'] = $response;
      }
    }

    return $activeCourseFranchiseArr;
  }

  public function fetchStudentExamDashboard()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = true;

    $type = 'exams';
    //Fetching record status
    if (isset($_GET['record_status'])) {
      $dataArr['record_status'] = $_GET['record_status'];
    } else {
      $dataArr['record_status'] = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Call read global news method
      $returnArr = $this->interface->fetch_Global_Exams($dataArr);
      $this->globalReturnArr['exam_data'] = $returnArr;
    } else {
      $this->globalReturnArr['exam_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function view_Student_Required_Data()
  {
    $type = 'student';
    $this->lib = $this->lib;

    // =========================
    // PERMISSION
    // =========================
    $hasPermission = $this->utilityService->checkUserRolePermission('view_student');

    $this->globalReturnArr['page_permission'] = $hasPermission;
    $this->globalReturnArr['page_type'] = $type;

    if (!$hasPermission) {
        return [
            'page_permission' => false,
            'page_type' => $type,
            'franchise_data' => [],
            'course_data' => [],
            'student_data' => []
        ];
    }

    // =========================
    // BASE DATA
    // =========================
    $dataArr = [
        'record_status' => isset($_GET['record_status']) 
            ? $this->get('record_status') 
            : 'active',

        'pageNo' => isset($_GET['pageNo']) 
            ? (int)$this->get('pageNo') 
            : 1,

        'limit' => 50
    ];

    // =========================
    // OPTIONAL FILTERS
    // =========================
    if (!empty($_GET['student_status'])) {
        $dataArr['student_status'] = $this->get('student_status');
    }

    if (!empty($_GET['result_status'])) {
        $dataArr['result_status'] = $this->get('result_status');
    }

    if (!empty($_GET['verified_status'])) {
        $dataArr['verified_status'] = $this->get('verified_status');
    }

    if (!empty($_GET['course_id']) && (int)$_GET['course_id'] > 0) {
        $dataArr['course_id'] = (int)$this->get('course_id');
    }

    // =========================
    // FRANCHISE LOGIC
    // =========================
    if ($_SESSION['user_type'] === 'franchise') {
        $dataArr['franchise_id'] = (int)$_SESSION['user_id'];
    } elseif (!empty($_GET['franchise_id']) && (int)$_GET['franchise_id'] > 0) {
        $dataArr['franchise_id'] = (int)$this->get('franchise_id');
    }

    // =========================
    // SEARCH
    // =========================
    if (!empty($_GET['search_string'])) {
        $dataArr['search_string'] = trim(
            $this->get('search_string')
        );
    }

    if (!empty($_GET['created'])) {
        $created = $this->get('created');
        $dataArr['created'] = $this->lib->formatDateDB($created);
    }

    if (!empty($_GET['search_start'])) {
        $search_start = $this->get('search_start');
        $dataArr['search_start'] = $this->lib->formatDateDB($search_start);
    }

    if (!empty($_GET['search_end'])) {
        $search_end = $this->get('search_end');
        $dataArr['search_end'] = $this->lib->formatDateDB($search_end);
    }

    // =========================
    // FETCH STATIC DATA
    // =========================
    $activeData = $this->fetch_Active_Course_Franchise_Data();

    $this->globalReturnArr['franchise_data'] = $activeData['franchise'];
    $this->globalReturnArr['course_data'] = $activeData['course'];

    // =========================
    // FETCH STUDENTS
    // =========================
    $this->globalReturnArr['student_data'] =
    $this->interface->fetch_Global_Student($dataArr);

    return $this->globalReturnArr;
  } 

  public function manage_Student_Required_Data()
  {
      $type = 'student';
  
      // =========================
      // SANITIZED INPUT
      // =========================
      $student_id = $this->get('id');
      $student_id = !empty($student_id) ? (int)$student_id : null;
  
      // =========================
      // UPDATE / CREATE LOGIC
      // =========================
      if (!empty($student_id)) {
  
          // UPDATE
          $this->globalReturnArr['page_permission'] =
              $this->utilityService->checkUserRolePermission('update_student');
  
          if ($this->globalReturnArr['page_permission']) {
  
              $studentData = $this->interface
                  ->fetch_Global_Single_Student($student_id);
  
              $this->globalReturnArr['student_data'] = $studentData ?: [];
  
              // Extra safety check (important)
              if (!empty($studentData) && $_SESSION['user_type'] == "franchise") {
                  if ($studentData->franchise_id != $_SESSION['user_id']) {
                      $this->globalReturnArr['page_permission'] = false;
                  }
              }
  
          } else {
              $this->globalReturnArr['student_data'] = [];
          }
  
      } else {
  
          // CREATE
          $this->globalReturnArr['page_permission'] =
              $this->utilityService->checkUserRolePermission('create_student');
  
          $this->globalReturnArr['student_data'] = [];
      }
  
      // =========================
      // COMMON DATA
      // =========================
      $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
  
      $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
      $this->globalReturnArr['course_data']    = $activeCourseFranchiseList['course'];
  
      // =========================
      // FINAL RESPONSE
      // =========================
      $this->globalReturnArr['page_type'] = $type;
  
      return $this->globalReturnArr;
  }

  public function manage_Student_Admission_Data()
  {
    $type = 'student_admission';
    $receipt_category_type = 'receipt';

    // =========================
    // SANITIZED INPUTS
    // =========================
    $student_id = $this->get('student_id');
    $id     = $this->get('id');
    $actionType = $this->get('actionType');

    // Type safety (important)
    $student_id = !empty($student_id) ? (int)$student_id : null;
    $id     = !empty($id) ? trim($id) : null;

    // =========================
    // FRANCHISE RESTRICTION
    // =========================
    if ($_SESSION['user_type'] == 'franchise' && $_SESSION['owned_status'] == 'no') {

        $this->globalReturnArr['page_permission'] = false;
        $this->globalReturnArr['student_list'] = [];

        return $this->globalReturnArr;
    }

    // =========================
    // ACTION: MANAGE STUDENT
    // =========================
    if ($actionType === "manage_student") {

        // =====================
        // UPDATE STUDENT
        // =====================
        if (!empty($student_id)) {

            $this->globalReturnArr['page_permission'] =
                $this->utilityService->checkUserRolePermission('update_student');

            if ($this->globalReturnArr['page_permission']) {
                $this->globalReturnArr['student_data'] =
                    $this->interface->fetch_Global_Single_Student($student_id);
            } else {
                $this->globalReturnArr['student_data'] = [];
            }

        // =====================
        // CREATE FROM TEMP
        // =====================
        } elseif (!empty($id)) {

            $this->globalReturnArr['page_permission'] =
                $this->utilityService->checkUserRolePermission('create_student');

            $this->globalReturnArr['student_data'] =
                $this->interface->fetch_Tmp_Single_Student($id);

        // =====================
        // CREATE NEW
        // =====================
        } else {

            $this->globalReturnArr['page_permission'] =
                $this->utilityService->checkUserRolePermission('create_student');

            $this->globalReturnArr['student_data'] = [];
        }

        // =========================
        // COMMON DATA
        // =========================
        $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();

        $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
        $this->globalReturnArr['course_data']    = $activeCourseFranchiseList['course'];

        $this->globalReturnArr['category_data'] =
            $this->interface->fetch_Single_Parent_Category($receipt_category_type);

    } 
    // =========================
    // VIEW STUDENTS
    // =========================
    else {

        $this->globalReturnArr['page_permission'] =
            $this->utilityService->checkUserRolePermission('view_student');

        if ($this->globalReturnArr['page_permission']) {

            $dataArr = [];

            if ($_SESSION['user_type'] == 'franchise') {
                $dataArr['franchise_id'] = (int)$_SESSION['user_id'];
            } else {
                $dataArr['franchise_id'] = null;
            }

            $this->globalReturnArr['student_list'] =
                $this->interface->fetch_Fresh_Students($dataArr);

        } else {
            $this->globalReturnArr['student_list'] = [];
        }
    }

    $this->globalReturnArr['page_type'] = $type;

    return $this->globalReturnArr;
  }

  public function manage_Temp_Student_Data()
  {
    $type = 'student_admission';

    // =========================
    // SANITIZED INPUTS
    // =========================
    $this->lib = $this->lib;

    $id        = $this->get('id');
    $actionType    = $this->get('actionType');
    $record_status = $this->get('record_status') ?: 'active';

    $course_id     = (int) $this->get('course_id');
    $franchise_id  = (int) $this->get('franchise_id');
    $pageNo        = (int) $this->get('pageNo') ?: 1;

    $search_string     = $this->get('search_string');
    $created           = $this->get('created');
    $search_start      = $this->get('search_start');
    $search_end        = $this->get('search_end');
    $conversion_status = $this->get('conversion_status');
    $verified_status   = $this->get('verified_status');

    $id = !empty($id) ? trim($id) : null;

    // =========================
    // FRANCHISE RESTRICTION
    // =========================
    if ($_SESSION['user_type'] == 'franchise' && $_SESSION['owned_status'] == 'no') {
        return [
            'page_permission' => false,
            'student_list' => [],
            'page_type' => $type
        ];
    }

    // =========================
    // ACTION: MANAGE STUDENT
    // =========================
    if ($actionType === "manage_student") {

        if (!empty($id)) {

            $this->globalReturnArr['page_permission'] =
                $this->utilityService->checkUserRolePermission('update_student');

            $this->globalReturnArr['student_data'] =
                $this->globalReturnArr['page_permission']
                ? $this->interface->fetch_Tmp_Single_Student($id)
                : [];

        } else {

            $this->globalReturnArr['page_permission'] =
                $this->utilityService->checkUserRolePermission('create_student');

            $this->globalReturnArr['student_data'] = [];
        }

        // COMMON DATA
        $activeData = $this->fetch_Active_Course_Franchise_Data();
        $this->globalReturnArr['franchise_data'] = $activeData['franchise'];
        $this->globalReturnArr['course_data']    = $activeData['course'];
    }

    // =========================
    // VIEW TEMP STUDENTS
    // =========================
    else {

        $this->globalReturnArr['page_permission'] =
            $this->utilityService->checkUserRolePermission('view_student');

        if ($this->globalReturnArr['page_permission']) {

            $dataArr = [
                'record_status' => $record_status,
                'pageNo'        => $pageNo,
                'limit'         => 20
            ];

            // =====================
            // FRANCHISE FILTER
            // =====================
            if ($_SESSION['user_type'] == 'franchise') {
                $dataArr['franchise_id'] = (int)$_SESSION['user_id'];
            } elseif ($franchise_id > 0) {
                $dataArr['franchise_id'] = $franchise_id;
            }

            // =====================
            // OPTIONAL FILTERS
            // =====================
            if ($course_id > 0) {
                $dataArr['course_id'] = $course_id;
            }

            if (!empty($search_string)) {
                $dataArr['search_string'] = trim($search_string);
            }

            if (!empty($created)) {
                $dataArr['created'] = $this->lib->formatDateDB($created);
            }

            if (!empty($search_start)) {
                $dataArr['search_start'] = $this->lib->formatDateDB($search_start);
            }

            if (!empty($search_end)) {
                $dataArr['search_end'] = $this->lib->formatDateDB($search_end);
            }

            // =====================
            // STATUS FILTERS
            // =====================
            $dataArr['verified_status'] = !empty($verified_status) ? $verified_status : null;

            // CLEANED conversion_status LOGIC
            if (!empty($conversion_status)) {

                $dataArr['conversion_status'] = $conversion_status;

            } else {

                $hasFilters = (
                    !empty($record_status) ||
                    !empty($verified_status) ||
                    $course_id > 0 ||
                    (!empty($search_string)) ||
                    (!empty($created)) ||
                    (!empty($search_start)) ||
                    (!empty($search_end)) ||
                    ($_SESSION['user_type'] !== 'franchise' && $franchise_id > 0)
                );

                $dataArr['conversion_status'] = $hasFilters ? null : 'n';
            }

            // =====================
            // COMMON DATA
            // =====================
            $activeData = $this->fetch_Active_Course_Franchise_Data();

            $this->globalReturnArr['franchise_data'] = $activeData['franchise'];
            $this->globalReturnArr['course_data']    = $activeData['course'];

            // =====================
            // FETCH DATA
            // =====================
            $this->globalReturnArr['student_data'] =
                $this->interface->fetch_Tmp_Students($dataArr);

        } else {
            $this->globalReturnArr['student_data'] = [];
        }
    }

    // =========================
    // FINAL RESPONSE
    // =========================
    $this->globalReturnArr['page_type'] = $type;

    return $this->globalReturnArr;
  }

  public function manage_Receipt_Required_Data()
  {
    $type = 'receipt';

    $this->globalReturnArr['page_type'] = $type;

    // =========================
    // SANITIZED INPUTS
    // =========================
    $student_id = $this->get('stu_id');
    $actionType = $this->get('actionType');
    $receipt_id = $this->get('rcpt_id');

    $student_id = !empty($student_id) ? trim($student_id) : null;
    $receipt_id = !empty($receipt_id) ? (int)$receipt_id : null;

    // =========================
    // ACTION MODE (CREATE / UPDATE)
    // =========================
    if (!empty($actionType)) {

        // Fetch category (common)
        $this->globalReturnArr['category_data'] =
            $this->interface->fetch_Single_Parent_Category($type);

        if ($actionType === "create") {

            // CREATE
            $this->globalReturnArr['page_permission'] =
                $this->utilityService->checkUserRolePermission('create_receipt');

            if ($this->globalReturnArr['page_permission']) {
                $this->globalReturnArr['receipt_data'] = [];
                $this->globalReturnArr['student_data'] =
                    $this->interface->fetch_Global_Single_Student($student_id);
            } else {
                $this->globalReturnArr['receipt_data'] = [];
                $this->globalReturnArr['student_data'] = [];
            }

        } else {

            // UPDATE
            $this->globalReturnArr['page_permission'] =
                $this->utilityService->checkUserRolePermission('update_receipt');

            if ($this->globalReturnArr['page_permission']) {

                $receiptData =
                    $this->interface->fetch_Single_Receipt_Data($receipt_id);

                $this->globalReturnArr['receipt_data'] = $receiptData ?: [];

                $this->globalReturnArr['student_data'] =
                    !empty($receiptData)
                        ? $this->interface
                            ->fetch_Global_Single_Student($receiptData->stu_id)
                        : [];

            } else {
                $this->globalReturnArr['receipt_data'] = [];
            }
        }

        return $this->globalReturnArr;
    }

    // =========================
    // LIST VIEW
    // =========================
    $this->globalReturnArr['page_permission'] =
        $this->utilityService->checkUserRolePermission('view_receipt');

    // Base filters
    $dataArr = [
        'record_status' => $this->get('record_status') ?: 'active',
        'verified_status' => $this->get('verified_status') ?: null,
        'student_id' => $student_id,
        'pageNo' => (int)($this->get('pageNo') ?: 1),
        'limit' => 20
    ];

    // Optional filters
    if (!empty($_GET['course_id']) && (int)$_GET['course_id'] > 0) {
        $dataArr['course_id'] = (int)$this->get('course_id');
    }

    // Franchise logic
    if ($_SESSION['user_type'] === 'franchise') {
        $dataArr['franchise_id'] = (int)$_SESSION['user_id'];
    } elseif (!empty($_GET['franchise_id']) && (int)$_GET['franchise_id'] > 0) {
        $dataArr['franchise_id'] = (int)$this->get('franchise_id');
    }

    // Dates
    if (!empty($_GET['created'])) {
        $created = $this->get('created');
        $dataArr['created'] = $this->lib->formatDateDB($created);
    }

    if (!empty($_GET['receipt_season_start'])) {
        $receipt_season_start = $this->get('receipt_season_start');
        $dataArr['receipt_season_start'] = $this->lib->formatDateDB($receipt_season_start);
    }

    if (!empty($_GET['receipt_season_end'])) {
        $receipt_season_end = $this->get('receipt_season_end');
        $dataArr['receipt_season_end'] = $this->lib->formatDateDB($receipt_season_end);
    }

    // =========================
    // FETCH COMMON DATA
    // =========================
    $activeData = $this->fetch_Active_Course_Franchise_Data();

    $this->globalReturnArr['franchise_data'] = $activeData['franchise'];
    $this->globalReturnArr['course_data']    = $activeData['course'];

    // =========================
    // FETCH RECEIPTS
    // =========================
    if ($this->globalReturnArr['page_permission']) {

        $this->globalReturnArr['receipt_data'] =
            $this->interface->fetch_Global_Receipt($dataArr);

        $this->globalReturnArr['student_data'] =
            !empty($student_id)
                ? $this->interface->fetch_Student_Receipt_Summary($dataArr)
                : [];

    } else {
        $this->globalReturnArr['receipt_data'] = [];
        $this->globalReturnArr['student_data'] = [];
    }

    return $this->globalReturnArr;
  }

  public function view_Due_Students_Data()
  {
    $type = 'due_students';

    // =========================
    // PERMISSION
    // =========================
    $this->globalReturnArr['page_permission'] =
        $this->utilityService->checkUserRolePermission('view_due_students');

    $this->globalReturnArr['page_type'] = $type;

    // =========================
    // COMMON DATA (ALWAYS LOAD)
    // =========================
    $activeData = $this->fetch_Active_Course_Franchise_Data();

    $this->globalReturnArr['franchise_data'] = $activeData['franchise'];
    $this->globalReturnArr['course_data']    = $activeData['course'];

    // =========================
    // EARLY EXIT (NO PERMISSION)
    // =========================
    if (!$this->globalReturnArr['page_permission']) {
        $this->globalReturnArr['student_data'] = [];
        return $this->globalReturnArr;
    }

    // =========================
    // SANITIZED INPUTS
    // =========================
    $student_id = $this->get('stu_id');
    $fetchType  = $this->get('fetchType');

    $student_id = !empty($student_id) ? trim($student_id) : null;

    // =========================
    // BASE PARAMS
    // =========================
    $dataArr = [
        'record_status' => $this->get('record_status') ?: 'active',
        'student_id'    => $student_id,
        'pageNo'        => (int)($this->get('pageNo') ?: 1),
        'limit'         => 20
    ];

    // =========================
    // OPTIONAL FILTERS
    // =========================
    if (!empty($_GET['course_id']) && (int)$_GET['course_id'] > 0) {
        $dataArr['course_id'] = (int)$this->get('course_id');
    }

    // Franchise logic
    if ($_SESSION['user_type'] === 'franchise') {
        $dataArr['franchise_id'] = (int)$_SESSION['user_id'];
    } elseif (!empty($_GET['franchise_id']) && (int)$_GET['franchise_id'] > 0) {
        $dataArr['franchise_id'] = (int)$this->get('franchise_id');
    }

    // =========================
    // FETCH DATA
    // =========================
    if (empty($fetchType) || $fetchType === 'dueList') {

        $this->globalReturnArr['student_data'] =
            $this->interface
                ->fetch_Due_Students_Data($dataArr);

    } else {

        $this->globalReturnArr['student_data'] =
            $this->interface
                ->fetch_Updated_Markup_Students_Data($dataArr);
    }

    return $this->globalReturnArr;
  }

  public function fetch_Gallery_Item_Detail()
  {

    $type = 'gallery';
    $this->globalReturnArr['page_type'] = $type;

    //Fetching record status
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    if (empty($_GET['type'])) {
      $user_role_slug = 'view_gallery';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch gallery item
        $this->globalReturnArr['gallery_data'] = $this->interface->fetch_Gallery_Arr($record_status);
      } else {
        $this->globalReturnArr['gallery_data'] = array();
      }
    } else if ($_GET['type'] == 'add') {
      $user_role_slug = 'create_gallery';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch all category list
        $this->globalReturnArr['category_data'] = $this->interface->fetch_Single_Parent_Category($type);
      } else {
        $this->globalReturnArr['category_data'] = array();
      }
    } else {
      $user_role_slug = 'update_gallery';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        $media_id = $_GET['id'];
        $this->globalReturnArr['gallery_data'] = $this->interface->fetch_Gallery_Item_Detail($media_id);
        //Fetch all category list
        $this->globalReturnArr['category_data'] = $this->interface->fetch_Single_Parent_Category($type);
      } else {
        $this->globalReturnArr['gallery_data'] = array();
        $this->globalReturnArr['category_data'] = array();
      }
    }

    return $this->globalReturnArr;
  }

  public function view_Category_Required_Data()
  {

    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission('view_category');

    $type = 'parent_category';

    //Fetching record status
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      $this->globalReturnArr['category_data'] = $this->interface->fetch_Parent_Category($record_status);
    } else {
      $this->globalReturnArr['category_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function view_Cities_Required_Data()
  {

    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission('manage_city_db');

    $type = 'cities';

    //Fetching record status
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      $this->globalReturnArr['city_data'] = $this->interface->fetch_Global_Cities($record_status);
    } else {
      $this->globalReturnArr['city_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function view_Enquiry_Required_Data()
  {

    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission('view_enquiry');

    $type = 'enquiry';

    //Fetching record status
    if (isset($_GET['record_status'])) {
      $dataArr['record_status'] = $_GET['record_status'];
    } else {
      $dataArr['record_status'] = 'active';
    }

    //Fetching record status
    if (isset($_GET['pageNo'])) {
      $dataArr['pageNo'] = $_GET['pageNo'];
    } else {
      $dataArr['pageNo'] = 1;
    }

    //Fetching record status
    if (isset($_GET['limit'])) {
      $dataArr['limit'] = $_GET['limit'];
    } else {
      $dataArr['limit'] = 200;
    }

    if (strlen($_GET['enquiry_type']) > 0) {
      $dataArr['enquiry_type'] = $_GET['enquiry_type'];
    }

    if ($_GET['course_id'] > 0) {
      $dataArr['course_id'] = $_GET['course_id'];
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Fetch all active course & franchise list
      $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();
      $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];

      $this->globalReturnArr['enquiry_data'] = $this->interface->fetch_Global_Enquiry($dataArr);
    } else {
      $this->globalReturnArr['enquiry_data'] = array();
      $this->globalReturnArr['course_data']  = array();
    }

    return $this->globalReturnArr;
  }

  public function view_Email_Templates_Required_Data()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission('view_template');

    $type = 'email_template';
    //Fetching record status
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }
    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Call read global email template method
      $returnArr = $this->interface->fetch_Email_Templates($record_status);
      $this->globalReturnArr['email_template_data'] = $returnArr;
    } else {
      $this->globalReturnArr['email_template_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_Email_Template_Required_Data()
  {
    $type = 'email_template';
    $template_id = $_GET['id'];

    $this->globalReturnArr['page_type'] = $type;
    if ($template_id > 0) {
      $user_role_slug = 'update_template';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch email template detail
        $this->globalReturnArr['email_template_details'] = $this->interface->fetch_Global_Email_Template_Detail($template_id);
      } else {
        $this->globalReturnArr['email_template_details'] = array();
      }
    } else {
      $user_role_slug = 'create_template';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['email_template_details'] = array();
    }
    return $this->globalReturnArr;
  }

  public function view_News_Required_Data()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission('view_news');

    $type = 'news';
    //Fetching record status
    if (isset($_GET['record_status'])) {
      $dataArr['record_status'] = $_GET['record_status'];
    } else {
      $dataArr['record_status'] = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Call read global news method
      $returnArr = $this->interface->fetch_Global_News($dataArr);
      $this->globalReturnArr['news_data'] = $returnArr;
    } else {
      $this->globalReturnArr['news_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_News_Required_Data()
  {
    $type = 'email_template';
    $news_id = $_GET['id'];

    $this->globalReturnArr['page_type'] = $type;
    if ($news_id > 0) {
      $user_role_slug = 'update_news';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch news detail
        $this->globalReturnArr['news_details'] = $this->interface->fetch_Global_News_Detail($news_id);
      } else {
        $this->globalReturnArr['news_details'] = array();
      }
    } else {
      $user_role_slug = 'create_news';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['news_details'] = array();
    }
    return $this->globalReturnArr;
  }

  public function manage_Home_Sliders_Required_Data()
  {

    $type = 'home_sliders';
    $this->globalReturnArr['page_type'] = $type;

    //Fetching record status
    if (isset($_GET['record_status'])) {
      $record_status = $_GET['record_status'];
    } else {
      $record_status = 'active';
    }

    if (isset($_GET['slider_type'])) {
      $slider_type = $_GET['slider_type'];
    } else {
      $slider_type = null;
    }

    if (empty($_GET['type'])) {
      $user_role_slug = 'manage_home_slider';

      $paramArr['record_status'] = $record_status;
      $paramArr['slider_type'] = $slider_type;

      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch gallery item
        $this->globalReturnArr['slider_data'] = $this->interface->fetch_Slider_Arr($paramArr);
      } else {
        $this->globalReturnArr['slider_data'] = array();
      }
    } else if ($_GET['type'] == 'add') {
      $user_role_slug = 'manage_home_slider';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);
    } else {
      $user_role_slug = 'manage_home_slider';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        $slider_id = $_GET['id'];
        $this->globalReturnArr['slider_data'] = $this->interface->fetch_Slider_Detail($slider_id);
        //Fetch all category list
      } else {
        $this->globalReturnArr['slider_data'] = array();
      }
    }

    return $this->globalReturnArr;
  }

  public function view_Exam_Required_Data()
  {
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission('view_exam');

    $type = 'exams';
    //Fetching record status
    if (isset($_GET['record_status'])) {
      $dataArr['record_status'] = $_GET['record_status'];
    } else {
      $dataArr['record_status'] = 'active';
    }

    $this->globalReturnArr['page_type'] = $type;

    if ($this->globalReturnArr['page_permission']) {
      //Call read global news method
      $returnArr = $this->interface->fetch_Global_Exams($dataArr);
      $this->globalReturnArr['exam_data'] = $returnArr;
    } else {
      $this->globalReturnArr['exam_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_Exam_Required_Data()
  {
    $type = 'exams';
    $exam_id = $_GET['id'];

    //Fetching record status
    $record_status = 'active';

    $this->globalReturnArr['page_type'] = $type;
    if ($exam_id > 0) {
      $user_role_slug = 'update_exam';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch news detail
        $this->globalReturnArr['exam_details'] = $this->interface->fetch_Student_Exam_Detail($exam_id);

        //Fetch all active course & franchise list
        $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();

        $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
        $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];
      } else {
        $this->globalReturnArr['exam_details'] = array();
      }
    } else {
      $user_role_slug = 'create_exam';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);
      $this->globalReturnArr['exam_details'] = array();

      //Fetch all active course & franchise list
      $activeCourseFranchiseList = $this->fetch_Active_Course_Franchise_Data();

      $this->globalReturnArr['franchise_data'] = $activeCourseFranchiseList['franchise'];
      $this->globalReturnArr['course_data'] = $activeCourseFranchiseList['course'];
    }

    return $this->globalReturnArr;
  }

  public function manage_Exam_Questions_Required_Data()
  {
    $type = 'exams';
    $exam_id = $_GET['exm_id'];

    //Fetching record status
    $record_status = 'active';

    $this->globalReturnArr['page_type'] = $type;
    if ($exam_id > 0) {
      $user_role_slug = 'update_exam';
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

      if ($this->globalReturnArr['page_permission']) {
        //Fetch news detail
        $this->globalReturnArr['exam_details'] = $this->interface->fetch_Student_Exam_Detail($exam_id);

        //Fetch news detail
        $this->globalReturnArr['questions'] = $this->interface->fetch_Exam_Questions($exam_id);
      } else {
        $this->globalReturnArr['questions'] = array();
      }
    } else {
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = false;
      $this->globalReturnArr['questions'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_Start_Exam()
  {
    $type = 'exams';
    $exam_id = $_GET['exm_id'];

    //Fetching record status
    $record_status = 'active';

    $this->globalReturnArr['page_type'] = $type;
    if ($exam_id > 0) {
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = true;

      if ($this->globalReturnArr['page_permission']) {
        //Fetch news detail
        $this->globalReturnArr['exam_details'] = $this->interface->fetch_Student_Exam_Detail($exam_id);

        //Fetch news detail
        $this->globalReturnArr['questions'] = $this->interface->fetch_Exam_Questions($exam_id);
      } else {
        $this->globalReturnArr['questions'] = array();
      }
    } else {
      //Check user permission for this section
      $this->globalReturnArr['page_permission'] = false;
      $this->globalReturnArr['questions'] = array();
    }

    return $this->globalReturnArr;
  }

  public function edit_Admin_Profile_Required_Data()
  {

    $user_id = $_SESSION['user_id'];
    $type = 'edit_profile';

    $this->globalReturnArr['page_type'] = $type;

    $user_role_slug = 'manage_profile';
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {

      if ($user_id > 0) {
        //Fetching franchise detail
        $this->globalReturnArr['profile_data'] = $this->interface->fetch_Admin_Profile_Data($user_id);
      } else {
        $this->globalReturnArr['profile_data'] = array();
      }
    } else {
      $this->globalReturnArr['profile_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function edit_Developer_Profile_Required_Data()
  {

    $user_id = $_SESSION['user_id'];
    $type = 'edit_profile';

    $this->globalReturnArr['page_type'] = $type;

    $user_role_slug = 'manage_profile';
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {

      if ($user_id > 0) {
        //Fetching franchise detail
        $this->globalReturnArr['profile_data'] = $this->interface->fetch_Developer_Profile_Data($user_id);
      } else {
        $this->globalReturnArr['profile_data'] = array();
      }
    } else {
      $this->globalReturnArr['profile_data'] = array();
    }

    return $this->globalReturnArr;
  }

  public function manage_Site_Setting_Required_Data()
  {
    $type = 'site_setting';

    $this->globalReturnArr['page_type'] = $type;

    $user_role_slug = 'update_site_setting';
    //Check user permission for this section
    $this->globalReturnArr['page_permission'] = $this->utilityService->checkUserRolePermission($user_role_slug);

    if ($this->globalReturnArr['page_permission']) {
      //Fetch site setting detail
      $this->globalReturnArr['site_settings'] = $this->interface->fetch_Global_Site_Setting_Detail();
    } else {
      $this->globalReturnArr['site_settings'] = array();
    }


    return $this->globalReturnArr;
  }

}
