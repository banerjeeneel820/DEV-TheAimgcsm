<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class CmsService
{
    public function __construct(
        private CmsModel $cmsModel,
        private GlobalLibraryHandler $lib,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | View gallery data helper methods
    |--------------------------------------------------------------------------
    */
    public function getGalleryList($params)
    {
        return $this->cmsModel
            ->fetch_Gallery_Arr($params);
    }

    public function fetchGalleryCount($params)
    {
        return $this->cmsModel
            ->fetch_Gallery_Count($params);
    }

    public function getGalleryCategories($type)
    {
        return $this->cmsModel
            ->fetch_Single_Parent_Category($type);
    }

    public function getGalleryDetails($media_id)
    {
        $params = [];
        $params['media_id'] = $media_id;

        return $this->cmsModel
            ->fetch_Gallery_Item_Detail($params);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage gallery data helper methods
    |--------------------------------------------------------------------------
    */
    public function saveGalleryData($formDataArr)
    {
        return $this->cmsModel->manage_Global_Media($formDataArr);
    }

    public function editPostCategory($formDataArr)
    {
        return $this->cmsModel->edit_Post_Category($formDataArr);
    }

    public function fetchSingleParentCategory($type)
    {
        return $this->cmsModel->fetch_Single_Parent_Category($type);
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

        return $this->cmsModel
            ->fetch_Parent_Category($params);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage category data helper methods
    |--------------------------------------------------------------------------
    */
    public function manageParentCategory($formDataArr)
    {
        return $this->cmsModel->manage_Parent_Category($formDataArr);
    }

    /*
    |--------------------------------------------------------------------------
    | View category data helper methods
    |--------------------------------------------------------------------------
    */
    public function getSliderData($params)
    {
        // Refactor $this->cmsModel method first
        return $this->cmsModel
            ->fetch_Slider_Arr($params);
    }

    public function getSliderDetail($id)
    {
        $params = [];
        $params['slider_id'] = $id;

        // Refactor $this->cmsModel method first
        return $this->cmsModel
            ->fetch_Slider_Detail($params);
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

        $returnArr = $this->cmsModel->manage_Home_Slider($formDataArr);

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
    public function getCityData($params = [])
    {
        return $this->cmsModel
            ->fetch_Global_Cities($params);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage home slider helper methods
    |--------------------------------------------------------------------------
    */
    public function manageGlobalCity(array $formDataArr)
    {
        return $this->cmsModel->manage_Global_City($formDataArr);
    }

    /*
    |--------------------------------------------------------------------------
    | View enquiry data helper methods
    |--------------------------------------------------------------------------
    */
    public function getEnquiryData($params = [])
    {
        return $this->cmsModel->fetch_Global_Enquiry($params);
    }
}
