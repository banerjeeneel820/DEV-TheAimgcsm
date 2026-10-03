<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class AuthService
{

    public function __construct(
        private GlobalInterfaceModel $model,
        private GlobalLibraryHandler $lib,
    ){}

    /*
    |--------------------------------------------------------------------------
    | Check user login helper methods
    |--------------------------------------------------------------------------
    */
    public function checkUserLogin($params)
    {
        return $this->model->check_User_Login($params);
    }

    /*
    |--------------------------------------------------------------------------
    | Edit developer profile data helper methods
    |--------------------------------------------------------------------------
    */
    public function getDevProfileData($user_id)
    {
        return $this->model->fetch_Developer_Profile_Data($user_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Edit admin profile data helper methods
    |--------------------------------------------------------------------------
    */
    public function getAdminProfileData($user_id)
    {
        return $this->model->fetch_Admin_Profile_Data($user_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Edit franchise profile data helper methods
    |--------------------------------------------------------------------------
    */
    public function getFranchiseProfileData($user_id)
    {
        return $this->model->fetch_Global_Single_Franchise($user_id);
    }

    /*
    |--------------------------------------------------------------------------
    | Save profile data helper methods
    |--------------------------------------------------------------------------
    */
    public function manageProfileData($params)
    {
        return $this->model->manage_Profile_Data($params);
    }

    /*
    |--------------------------------------------------------------------------
    | Save franchise profile data helper methods
    |--------------------------------------------------------------------------
    */
    public function editFranchiseProfile($params)
    {
        return $this->model->edit_Franchise_Profile($params);
    }

    /*
    |--------------------------------------------------------------------------
    | Check email availability helper methods
    |--------------------------------------------------------------------------
    */
    public function checkUserEmailAvailability($payload)
    {
        return $this->model->check_user_email_availability($payload);
    }
}