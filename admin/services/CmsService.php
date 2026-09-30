<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class CmsService
{
    public function __construct(
        private GlobalInterfaceModel $model,
        private GlobalLibraryHandler $lib,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | View gallery data helper methods
    |--------------------------------------------------------------------------
    */
    public function getGalleryList($recordStatus)
    {
        return $this->model
            ->fetch_Gallery_Arr($recordStatus);
    }

    public function getGalleryCategories($type)
    {
        return $this->model
            ->fetch_Single_Parent_Category($type);
    }

    public function getGalleryDetails($media_id)
    {
        return $this->model
            ->fetch_Gallery_Item_Detail($media_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage gallery data helper methods
    |--------------------------------------------------------------------------
    */
    public function saveGalleryData($formDataArr)
    {
        return $this->model->manage_Global_Media($formDataArr);
    }

    public function editPostCategory($formDataArr)
    {
        return $this->model->edit_Post_Category($formDataArr);
    }

    public function fetchSingleParentCategory($type)
    {
        return $this->model->fetch_Single_Parent_Category($type);
    }

    /*
    |--------------------------------------------------------------------------
    | View category data helper methods
    |--------------------------------------------------------------------------
    */
    public function getParentCategoryData($recordStatus)
    {
        $params = [];
        $params['record_status'] = $recordStatus;

        return $this->model
            ->fetch_Parent_Category($params);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage category data helper methods
    |--------------------------------------------------------------------------
    */
    public function manageParentCategory($formDataArr)
    {
        // Refactor model method first
        return $this->model->manage_Parent_Category($formDataArr);
    }

    /*
    |--------------------------------------------------------------------------
    | View category data helper methods
    |--------------------------------------------------------------------------
    */
    public function getSliderData($params)
    {
        // Refactor model method first
        return $this->model
            ->fetch_Slider_Arr($params);
    }

    public function getSliderDetail($id)
    {
        // Refactor model method first
        return $this->model
            ->fetch_Slider_Detail($id);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage home slider helper methods
    |--------------------------------------------------------------------------
    */
    public function manageHomeSlider(
        array $formDataArr,
        array $uploadReturnArr,
        $hiddenBannerImage,
        bool $isUpdate
    ) {
        $dir = 'home_sliders';

        // -----------------------------
        // Save Slider
        // -----------------------------

        $returnArr = $this->model->manage_Home_Slider($formDataArr);

        // -----------------------------
        // Handle Save Failure
        // -----------------------------

        if ($returnArr['check'] !== 'success') {

            // Rollback newly uploaded image
            if (
                $uploadReturnArr['check'] === 'success' &&
                !empty($formDataArr['banner_image'])
            ) {
                $newFilePath = USER_UPLOAD_DIR
                    . $dir . '/'
                    . $formDataArr['banner_image'];

                if (file_exists($newFilePath)) {
                    unlink($newFilePath);
                }
            }

            return [
                'check' => 'failure',
                'message' => "Something went wrong!"
            ];
        }

        // -----------------------------
        // Cleanup Old Image
        // -----------------------------

        if ($isUpdate && !empty($hiddenBannerImage)) {

            $shouldDeleteOldImage =
                ($formDataArr['file_upload_type'] === 'local' &&
                    $uploadReturnArr['check'] === 'success'
                ) ||
                $formDataArr['file_upload_type'] === 'cdn';

            if ($shouldDeleteOldImage) {

                $oldFilePath = USER_UPLOAD_DIR
                    . $dir . '/'
                    . $hiddenBannerImage;

                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }
        }

        // -----------------------------
        // Return Result
        // -----------------------------

        return $returnArr;
    }

    /*
    |--------------------------------------------------------------------------
    | View cities data helper methods
    |--------------------------------------------------------------------------
    */
    public function getCityData($recordStatus)
    {
        return $this->model
            ->fetch_Global_Cities($recordStatus);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage home slider helper methods
    |--------------------------------------------------------------------------
    */
    public function manageGlobalCity(array $formDataArr)
    {
        return $this->model->manage_Global_City($formDataArr);
    }

}
