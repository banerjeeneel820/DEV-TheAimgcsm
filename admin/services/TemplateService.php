<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class TemplateService
{
    public function __construct(
        private GlobalInterfaceModel $model,
        private GlobalLibraryHandler $lib,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | View email template data helper methods
    |--------------------------------------------------------------------------
    */
    public function getEmailTemplates($record_status = 'active')
    {
        $params = [];
        $params['record_status'] = $record_status;

        return $this->model->fetch_Email_Templates($params);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage email template data view helper methods
    |--------------------------------------------------------------------------
    */
    public function getEmailTemplateDetail($template_id)
    {
        return $this->model->fetch_Global_Email_Template_Detail($template_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage email template helper methods
    |--------------------------------------------------------------------------
    */
    public function manageEmailTemplate(array $formDataArr)
    {
        // -----------------------------
        // Determine Action Type
        // -----------------------------

        $templateId = (int) $formDataArr['template_id'];

        $isUpdate = $templateId > 0;

        // -----------------------------
        // Code Availability Check
        // -----------------------------

        $existingTemplate = $this->model
            ->check_Slug_Availibility(
                'email_template',
                'code',
                $formDataArr['code']
            );

        $existingId = $existingTemplate->id ?? null;

        $isDuplicate = !empty($existingId)
            && (!$isUpdate || (int) $existingId !== $templateId);

        if ($isDuplicate) {
            return [
                'check'   => 'failure',
                'message' => 'This code is already available; Please try another.'
            ];
        }

        // -----------------------------
        // Normalize email template 
        // before passing to model
        // -----------------------------

        $template = $formDataArr['template'] ?? '';

        $formDataArr['template'] = $this->lib->formatEscapedHtmlContent($template);

        // -----------------------------
        // Save Template
        // -----------------------------

        return $this->model
            ->manage_Global_Email_Template($formDataArr);
    }
}
