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

}
