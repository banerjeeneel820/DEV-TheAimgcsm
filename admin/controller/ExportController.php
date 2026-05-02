<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class ExportController extends BaseController
{
    private $exportService;

    public function __construct()
    {   
        parent::__construct();
        
        $this->exportService = new ExportService($this->interface,$this->lib);
    }

    public function handle_export_data($data)
    {
        // helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);
        
        $type = $post('export_table') ?? '';
        
        switch ($type) {
            case 'student':
                // Check user permission
                $user_role_slug_arr = ['view_student','update_student'];

                if (!$this->checkUserRolePermission($user_role_slug_arr, "hard")) {
                    return ['check' => 'failure', 'message' => "You don't have the permission to perform this action!"];
                }
                // Return export data if user has permission
                return $this->exportService->exportStudent($post);
                break;

            case 'receipt':
                return $this->exportService->exportReceipt($post);
                break;

            case 'franchise':
                return $this->exportService->exportFranchise($post);
                break;

            default:
                return ['check' => 'failure', 'message' => 'Invalid export type'];
        }
    }
}