<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class TemplateController extends BaseController
{
    private $permissionService;
    private $templateService;

    public function __construct($container)
    {
        parent::__construct($container);
        $this->permissionService = $container->get(PermissionService::class);
        $this->templateService = $container->get(TemplateService::class);
    }

    /*
    |--------------------------------------------------------------------------
    | View email template data methods
    |--------------------------------------------------------------------------
    */
    public function fetch_email_template_data($data)
    {
        $user_role_slug = 'view_template';

        // Load assets
        $assets = Asset::load("email_templates");

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                ['email_template_data' => []],
                'Email Templates',
                $assets,
                false,
                false // page_permission
            );
        }

        // Get filter safely
        $record_status = $data['record_status'] ?? 'active';

        // Fetch email templates through service
        $emailTemplates = $this->templateService->getEmailTemplates($record_status);

        return $this->page(
            [
                'email_template_data' => $emailTemplates,
                'page_type' => 'email_template'
            ],
            'Email Templates',
            $assets,
            false,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage email template data view methods
    |--------------------------------------------------------------------------
    */
    public function manage_email_template_data_view($data)
    {
        // Load assets
        $assets = Asset::load("email_template_form");

        // Get template ID safely
        $template_id = (int) ($data['id'] ?? 0);

        // Determine permission based on create/edit mode
        if ($template_id > 0) {
            $user_role_slug = 'update_template';
        } else {
            $user_role_slug = 'create_template';
        }

        // Permission check (centralized)
        $hasPermission = $this->permissionService->checkUserRolePermission($user_role_slug);

        if (!$hasPermission) {
            return $this->page(
                [
                    'email_template_details' => [],
                    'page_type' => 'email_template'
                ],
                'Email Template',
                $assets,
                false,
                false // page_permission
            );
        }

        // Default data for create mode
        $templateDetails = [];

        // Fetch existing template for edit mode
        if ($template_id > 0) {
            $templateDetails = $this->templateService->getEmailTemplateDetail($template_id);
        }

        return $this->page(
            [
                'email_template_details' => $templateDetails,
                'page_type' => 'email_template'
            ],
            'Email Template',
            $assets,
            true,
            true
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Manage email template data methods
    |--------------------------------------------------------------------------
    */
    public function manage_email_template($data)
    {
        $formDataArr = [];

        // Helper
        $post = fn ($key) => $this->lib->postDataSanitize($key);

        // -----------------------------
        // Determine Action Type
        // -----------------------------

        $formDataArr['template_id'] = $post('template_id');

        $isUpdate = (int) $formDataArr['template_id'] > 0;

        $user_role_slug = $isUpdate
            ? 'update_template'
            : 'create_template';

        // -----------------------------
        // Permission Check
        // -----------------------------

        if (!$this->permissionService->checkUserRolePermission($user_role_slug, "hard")) {
            return [
                'check'   => 'failure',
                'message' => "You don't have the permission to perform this action!"
            ];
        }

        // -----------------------------
        // Template Data
        // -----------------------------

        $formDataArr['subject']       = $post('subject');
        $formDataArr['code']          = $post('code');
        $formDataArr['email_for']     = $post('email_for');
        $formDataArr['record_status'] = $post('record_status');
        $formDataArr['variables']     = $post('variables');
        $formDataArr['from_email']    = $post('from_email');
        $formDataArr['from_name']     = $post('from_name');
        $formDataArr['cc_email']      = $post('cc_email');
        $formDataArr['template']      = $post('template');

        // -----------------------------
        // Save
        // -----------------------------

        return $this->templateService
            ->manageEmailTemplate($formDataArr);
    }
}
