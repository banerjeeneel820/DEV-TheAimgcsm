<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class NewsController extends BaseController
{
    private $permissionService;
    private $newsService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->permissionService = $container->get(PermissionService::class);
        $this->newsService = $container->get(Newservice::class);
    }

    /*
    |--------------------------------------------------------------------------
    | View news view data methods
    |--------------------------------------------------------------------------
    */
    public function fetch_news_data($data)
    {
        $user_role_slug = 'view_news';

        // Load assets
        $assets = Asset::load("news_list");

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'news_data' => [],
                    'page_type' => 'news'
                ],
                'News List',
                $assets,
                false,
                false // page_permission
            );
        }

        // Get filter safely
        $record_status = $data['record_status'] ?? 'active';

        // Fetch news through CMS service
        $news = $this->newsService->getNewsData([
            'record_status' => $record_status
        ]);

        return $this->page(
            [
                'news_data' => $news,
                'page_type' => 'news'
            ],
            'News List',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | View news details data methods
    |--------------------------------------------------------------------------
    */
    public function manage_news_data_view($data)
    {
        // Load assets
        $assets = Asset::load("news_form");

        // Get news ID safely
        $news_id = (int) ($data['id'] ?? 0);

        // Determine permission based on create/edit mode
        $user_role_slug = $news_id > 0
            ? 'update_news'
            : 'create_news';

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        // Handle unauthorized access
        if (!$hasPermission) {
            return $this->page(
                [
                    'news_details' => [],
                    'page_type' => 'news'
                ],
                'Manage News',
                $assets,
                false,
                false // page_permission
            );
        }

        // Default data for create mode
        $newsDetails = [];

        // Fetch existing news details for edit mode
        if ($news_id > 0) {
            $newsDetails = $this->newsService->getNewsDetail($news_id);
        }

        return $this->page(
            [
                'news_details' => $newsDetails,
                'page_type' => 'news'
            ],
            'Manage News',
            $assets,
            true,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage news data methods
    |--------------------------------------------------------------------------
    */
    public function manage_global_news($data)
    {
        $formDataArr = [];

        // Helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Basic Data
        // -----------------------------

        $formDataArr['news_id']        = $post('news_id');
        $formDataArr['title']          = $post('title');
        $formDataArr['record_status']  = $post('record_status');
        $formDataArr['featured_status'] = $post('featured_status');
        $formDataArr['description']    = $post('description');

        // -----------------------------
        // Permission Check
        // -----------------------------

        $isUpdate = (int) $formDataArr['news_id'] > 0;

        $user_role_slug = $isUpdate
            ? 'update_news'
            : 'create_news';

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return [
                'check'   => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // Existing PDF
        // -----------------------------

        $formDataArr['hidden_optional_pdf'] = $post('hidden_optional_pdf');

        // -----------------------------
        // Save
        // -----------------------------

        return $this->newsService->manageGlobalNews($formDataArr);
    }
}
