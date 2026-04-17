<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ImportController extends BaseController
{
    private $importService;

    public function __construct()
    {   
        parent::__construct();
        $this->importService = new ImportService();
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

                if (!$this->checkUserRolePermission($user_role_slug_arr, "hard")) {
                    return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
                }
                // Return export import if user has permission
                return $this->importService->importStudent($post);

            case 'city':
                return $this->importService->importCity($post);

            case 'students_monthly_fees':
                return $this->importService->importStuMonthlyFee($post);

            default:
                return ['check' => 'failure', 'message' => 'Invalid import type'];
        }
    }
}