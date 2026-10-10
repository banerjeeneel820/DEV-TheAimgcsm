<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class DashboardController extends BaseController
{
    private $permissionService;
    private $cacheService;
    private $newsService;
    private $courseFranchiseService;
    private $cmsService;
    private $examService;
    private $dashboardService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->permissionService = $container->get(PermissionService::class);
        $this->cacheService = $container->get(CacheService::class);
        $this->newsService = $container->get(NewsService::class);
        $this->courseFranchiseService = $container->get(CourseFranchiseService::class);
        $this->cmsService = $container->get(CmsService::class);
        $this->examService = $container->get(ExamService::class);
        $this->dashboardService = $container->get(DashboardService::class);
    }

    public function fetch_dashboard_data($data)
    {
        if ($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'developer' || $_SESSION['user_type'] == 'franchise') {
            return $this->fetch_user_dashboard_data($data);
        } elseif ($_SESSION['user_type'] == 'student') {
            return $this->fetch_student_exam_dashboard($data);
        }
    }

    public function fetch_user_dashboard_data($data)
    {
        // Permission configuration
        $user_role_slug = 'view_dashboard';
        $backup_role_slug = 'manage_site_backup';

        // Load assets
        $assets = Asset::load("dashboard");

        // Centralized permission checks
        $hasPermission = $this->permissionService
            ->checkUserRolePermission($user_role_slug);

        $canManageBackup = $this->permissionService
            ->checkUserRolePermission($backup_role_slug);

        // Return early when dashboard access is denied
        if (!$hasPermission) {
            return $this->page(
                ['page' => 'dashboard'],
                'Dashboard',
                $assets,
                false,
                false
            );
        }

        // Current user context
        $userType = $_SESSION['user_type'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;

        // Validate dashboard filters
        $dataType = $data['dataType'] ?? '';
        $allowedPeriods = ['today', 'weekly', 'monthly', 'annual'];

        $requestedPeriod = $data['fetchType'] ?? 'weekly';

        if (!in_array($requestedPeriod, $allowedPeriods, true)) {
            $requestedPeriod = 'weekly';
        }

        // Only apply the requested period to the selected dashboard widget.
        $studentPeriod = $dataType === 'student'
            ? $requestedPeriod
            : 'weekly';

        $receiptPeriod = $dataType === 'receipt'
            ? $requestedPeriod
            : 'weekly';

        // Base query parameters
        $studentParams = [
            'fetchType' => $studentPeriod
        ];

        $receiptParams = [
            'fetchType' => $receiptPeriod
        ];

        // Franchise-specific data scope
        if ($userType === 'franchise') {
            $studentParams['franchise_id'] = $userId;
            $receiptParams['franchise_id'] = $userId;
        }

        // Common listing parameters
        $enquiryParams = [
            'limit' => 20,
            'pageNo' => 1,
            'record_status' => 'active'
        ];

        $activeParams = [
            'record_status' => 'active'
        ];

        // Initialize page data
        $pageData = [
            'page_type' => 'dashboard',
            'site_bak_files' => [],
            'news_data' => [],
            'course_data' => [],
            'student_data' => [],
            'receipt_data' => [],
            'enquiry_data' => [],
            'gallery_data' => []
        ];

        // Site backup data
        if ($canManageBackup) {
            $pageData['site_bak_files'] =
                $this->dashboardService->getSiteBackupFiles();
        }

        // News data
        $pageData['news_data'] = $this->cacheService->get(
            'news_data',
            fn () => $this->newsService->getNewsData($activeParams)
        );

        // Course data
        $pageData['course_data'] = $this->cacheService->get(
            'course_data',
            fn () => $this->courseFranchiseService->fetch_Active_Course_Franchise_Data()['course']
        );

        // Dashboard data for developer and admin
        if (in_array($userType, ['developer', 'admin'], true)) {

            $pageData['student_data'] = $this->cacheService->get(
                'student_dashboard_' . $studentPeriod,
                fn () => $this->dashboardService
                    ->getStudentDashboardData($studentParams)
            );

            $pageData['receipt_data'] = $this->cacheService->get(
                'receipt_dashboard_' . $receiptPeriod,
                fn () => $this->dashboardService
                    ->getReceiptDashboardData($receiptParams)
            );

            $pageData['enquiry_data'] = $this->cacheService->get(
                'enquiry_data',
                fn () => $this->cmsService->getEnquiryData($enquiryParams)
            );
        }

        // Dashboard data for franchise users
        elseif ($userType === 'franchise' && $userId) {

            // Retrieve franchise details
            $franchise = $this->courseFranchiseService
                ->getFranchiseDetail($userId);

            $ownedStatus = is_object($franchise)
                ? ($franchise->owned_status ?? 'no')
                : ($franchise['owned_status'] ?? 'no');

            // Student dashboard data
            $studentCacheKey = "student_dashboard_{$studentPeriod}_{$userId}";

            $pageData['student_data'] = $this->cacheService->get(
                $studentCacheKey,
                fn () => $this->dashboardService
                    ->getStudentDashboardData($studentParams)
            );

            // Receipt dashboard is available to owned franchises only
            if ($ownedStatus === 'yes') {

                $receiptCacheKey = "receipt_dashboard_{$receiptPeriod}_{$userId}";

                $pageData['receipt_data'] = $this->cacheService->get(
                    $receiptCacheKey,
                    fn () => $this->dashboardService
                        ->getReceiptDashboardData($receiptParams)
                );
            } else {

                // Get filters safely
                $params = [
                    'record_status' => $data['record_status'] ?? 'active',
                ];

                // Gallery data for non-owned franchises
                $pageData['gallery_data'] = $this->cacheService->get(
                    'gallery_data',
                    fn () => $this->cmsService
                        ->fetchGalleryCount($params)
                );
            }
        }

        return $this->page(
            $pageData,
            'Dashboard',
            $assets,
            false,
            true
        );
    }

    public function fetch_student_exam_dashboard($data)
    {
        // Check user permission
        $permission = true;

        // Load page assets
        $assets = Asset::load("student_exam_dashboard");

        // Fetch and validate record status
        $recordStatus = $data['record_status'] ?? 'active';

        // Fetch exam records through the service layer
        $examData = $this->examService->getExamData([
            'record_status' => $recordStatus
        ]);

        // Prepare page data
        $pageData = [
            'page_type' => 'exams',
            'exam_data' => $examData
        ];

        return $this->page(
            $pageData,
            'Student Exam Dashboard',
            $assets,
            false,
            $permission
        );
    }
}
