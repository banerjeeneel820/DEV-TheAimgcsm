<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class UtilityService
{

    public function __construct(
        private UtilityModel $utilityModel,
        private GlobalLibraryHandler $lib,
    ){}

    /*
    |--------------------------------------------------------------------------
    | Update different record status helper methods
    |--------------------------------------------------------------------------
    */    
    public function updateRecordStatus($type, $ids, $status)
    {
        return $this->utilityModel->update_Global_Record_Status($type, $ids, $status);
    }

    public function updateFeaturedStatus($type, $ids, $status)
    {
        return $this->utilityModel->update_Global_Featured_Status($type, $ids, $status);
    }

    public function updateVerified_Status($type, $ids, $status)
    {
        return $this->utilityModel->update_Global_Verified_Status($type, $ids, $status);
    }

    /*
    |--------------------------------------------------------------------------
    | Delete records helper methods
    |--------------------------------------------------------------------------
    */ 
    public function fetchMultipleRecordData($type, $ids)
    {
        return $this->utilityModel->fetch_Global_Multiple_Data($type, $ids);
    }

    public function deleteRecordData($paramArr)
    {
        return $this->utilityModel->delete_Global_Data($paramArr);
    }

    /*
    |--------------------------------------------------------------------------
    | Manage queue jobs helper methods
    |--------------------------------------------------------------------------
    */ 
    public function checkTaskStatus($job_type = null)
    {
        return $this->utilityModel->check_Task_Status($job_type);
    }

    public function manageQueueJobs($payload)
    {
        return $this->utilityModel->manage_Queue_Jobs($payload);
    }

    /*
    |--------------------------------------------------------------------------
    | Update site settings helper methods
    |--------------------------------------------------------------------------
    */ 
    public function getSiteSettings()
    {
        return $this->utilityModel->fetch_Global_Site_Setting_Detail();
    }
    
    public function updateSiteSetting($payload)
    {
        return $this->utilityModel->update_Global_Site_Setting($payload);
    }

    
}