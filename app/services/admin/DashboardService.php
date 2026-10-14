<?php
namespace App\Services\Admin;

use App\Models\Admin\DashboardModel;
use App\Libraries\GlobalLibraryHandler;

defined('ROOTPATH') or exit('No direct script access allowed');

class DashboardService
{
    public function __construct(
        private DashboardModel $dashboardModel,
        private GlobalLibraryHandler $lib,
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | View news data helper methods
    |--------------------------------------------------------------------------
    */
    public function getSiteBackupFiles()
    {
        return $this->lib->fetchSiteBackupFiles();
    }

    public function getStudentDashboardData($params = [])
    {
        return $this->dashboardModel->fetch_Dashboard_Student_Data($params);
    }

    public function getReceiptDashboardData($params = [])
    {
        return $this->dashboardModel->fetch_Dashboard_Receipt_Data($params);
    }
}
