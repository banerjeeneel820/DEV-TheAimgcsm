<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ImportController extends BaseController
{
    private $importService;
    private $permissionService;

    public function __construct($container)
    {   
        parent::__construct($container);

        $this->importService = $container->get(ImportService::class);
        $this->permissionService = $container->get(PermissionService::class);
    }

    public function handle_import_data($data)
    {
        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);
        
        $type = $post('import_table') ?? '';
        
        switch ($type) {
            case 'exam_questions':
                // Check user permission
                $user_role_slug_arr = ['update_exam','view_student','update_student','manage_city_db'];

                if (!$this->permissionService->checkUserRolePermission($user_role_slug_arr, "hard")) {
                    return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
                }
                // Return export import if user has permission
                return $this->importService->importExamQuestions($post, $_FILES);
                break;

            case 'city':
                return $this->importService->importCity($post, $_FILES);
                break;

            case 'students_monthly_fees':
                return $this->importService->importStuMonthlyFee($post, $_FILES);
                break;

            default:
                return ['check' => 'failure', 'message' => 'Invalid import type'];
        }
    }
}