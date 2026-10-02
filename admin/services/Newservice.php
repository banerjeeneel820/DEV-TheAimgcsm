<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class Newservice
{
    public function __construct(
        private GlobalInterfaceModel $model,
        private GlobalLibraryHandler $lib,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | View news data helper methods
    |--------------------------------------------------------------------------
    */
    public function getNewsData($params = [])
    {
        return $this->model->fetch_Global_News($params);
    }

    /*
    |--------------------------------------------------------------------------
    | View news data helper methods
    |--------------------------------------------------------------------------
    */

    public function getNewsDetail($news_id)
    {
        return $this->model->fetch_Global_News_Detail($news_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage news data helper methods
    |--------------------------------------------------------------------------
    */
    public function manageGlobalNews(array $formDataArr)
    {
        $dir = 'news';

        // -----------------------------
        // Determine Action Type
        // -----------------------------

        $newsId = (int) $formDataArr['news_id'];

        $isUpdate = $newsId > 0;

        $oldPdf = $formDataArr['hidden_optional_pdf'] ?? '';

        // -----------------------------
        // Remove Internal Field
        // -----------------------------

        unset($formDataArr['hidden_optional_pdf']);

        // -----------------------------
        // PDF Upload
        // -----------------------------

        $uploadReturnArr = ['check' => 'skip'];

        if (!empty($_FILES['local_news_pdf']['size'])) {

            // Validate PDF
            $validation = $this->lib->validateFile(
                $_FILES['local_news_pdf'],
                'pdf'
            );

            if ($validation['check'] !== 'success') {
                return $validation;
            }

            // Upload PDF
            $uploadReturnArr = $this->lib->upload_file(
                'local_news_pdf',
                $dir
            );

            if ($uploadReturnArr['check'] !== 'success') {
                return [
                    'check'   => 'failure',
                    'message' => "News PDF upload failed!"
                ];
            }

            $formDataArr['optional_pdf'] = $uploadReturnArr['fileName'];
        } else {

            // Keep existing PDF during update
            $formDataArr['optional_pdf'] = $isUpdate
                ? $oldPdf
                : null;
        }

        // -----------------------------
        // Normalize news description 
        // before passing to model
        // -----------------------------

        $description = $formDataArr['description'] ?? '';

        $formDataArr['description'] = $this->lib->formatEscapedHtmlContent($description);

        // -----------------------------
        // Save News
        // -----------------------------

        $returnArr = $this->model
            ->manage_Global_News($formDataArr);

        // -----------------------------
        // Handle Save Failure
        // -----------------------------

        if ($returnArr['check'] !== 'success') {

            // Rollback newly uploaded PDF
            if (
                $uploadReturnArr['check'] === 'success' &&
                !empty($formDataArr['optional_pdf'])
            ) {
                $newFilePath = USER_UPLOAD_DIR
                    . $dir . '/'
                    . $formDataArr['optional_pdf'];

                if (file_exists($newFilePath)) {
                    unlink($newFilePath);
                }
            }

            return [
                'check'   => 'failure',
                'message' => "Something went wrong!"
            ];
        }

        // -----------------------------
        // Cleanup Old PDF
        // -----------------------------

        if (
            $isUpdate &&
            $uploadReturnArr['check'] === 'success' &&
            !empty($oldPdf)
        ) {
            $oldFilePath = USER_UPLOAD_DIR
                . $dir . '/'
                . $oldPdf;

            if (file_exists($oldFilePath)) {
                unlink($oldFilePath);
            }
        }

        // -----------------------------
        // Return Result
        // -----------------------------

        return $returnArr;
    }
}
