<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class DashboardController extends BaseController
{   
    private $permissionService;
    private $globalReturnArr = [];
    
    public function __construct()
    {
        parent::__construct();
        $this->permissionService = new PermissionService($this->model, $this->lib);
    }

    public function fetch_dashboard_data()
    {
        if ($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'developer') {
            $this->globalReturnArr['pageData'] = $this->GlobalViewDataControllerObj->fetchUserDashboardData();
        } elseif ($_SESSION['user_type'] == 'franchise') {
            $this->globalReturnArr['pageData'] = $this->GlobalViewDataControllerObj->fetchUserDashboardData();
        } elseif ($_SESSION['user_type'] == 'student') {
            $this->globalReturnArr['pageData'] = $this->GlobalViewDataControllerObj->fetchStudentExamDashboard();
        }

        $this->globalReturnArr['pageData']['page_title'] = "Manage Dashboard";

        $this->globalReturnArr['pageData']['tiny_allowed'] = false;

        if ($_SESSION['user_type'] != "student") {

            $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min', 'sweetalert/sweetalert', 'footable/footable.core', 'printThis/print.min', 'fancybox/jquery.fancybox.min');

            $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min', 'sweetalert/sweetalert.min', 'footable/footable.all.min', 'printThis/print.min', 'fancybox/jquery.fancybox');
        } else {
            $this->globalReturnArr['assetData']['css'] = array('toastr/toastr.min', 'dataTables/datatables.min', 'sweetalert/sweetalert', 'pretty-checkbox/pretty-checkbox.min');

            $this->globalReturnArr['assetData']['js'] = array('toastr/toastr.min', 'dataTables/datatables.min', 'dataTables/dataTables.bootstrap4.min', 'sweetalert/sweetalert.min');
        }

        return $this->globalReturnArr;
    }
}
