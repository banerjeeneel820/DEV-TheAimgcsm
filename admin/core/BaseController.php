<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class BaseController
{
    protected $lib;
    protected $cache;
    protected $validator;
    protected $interface;

    public function __construct()
    {
        $this->lib = new GlobalLibraryHandler();
        $this->cache = new CacheService();
        $this->validator = new GlobalValidationController();
        $this->interface = new GlobalInterfaceController();

        // moved from ajax controller
        $this->lib->checkRunTimeFolderExistance();
    }

    protected function checkUserRolePermission($user_role_slug, $fetch_type = "hard")
    {
        $paramArr['user_id'] = $_SESSION['user_id'];
        $paramArr['user_type'] = $_SESSION['user_type'];

        // Fetch roles
        if ($fetch_type === "hard") {
            $userRoleArr = $this->interface->fetch_Current_User_Role($paramArr);
        } else {
            $userRoleArr = $_SESSION['user_role'] ?? [];
        }

        if (!is_array($userRoleArr)) {
            return false;
        }

        // If single role → same behavior (no change)
        if (!is_array($user_role_slug)) {
            return in_array($user_role_slug, $userRoleArr);
        }

        // If multiple roles → ALL must exist
        return count(array_intersect($user_role_slug, $userRoleArr)) === count($user_role_slug);
    }

    protected function json($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        ///header('Content-Type: application/json');
        return json_encode($data);
    }

    protected function get($key)
    {
        return $this->lib->getDataSanitize($key);
    }

    protected function post($key)
    {
        return $this->lib->postDataSanitize($key);
    }

    protected function purgeSiteCache($section)
    {
        if (SERVER_ENV !== "PRODUCTION") {
            return true;
        }

        $userType = $_SESSION['user_type'] ?? null;
        $userId   = $_SESSION['user_id'] ?? null;

        $keys = $this->getCacheKeys($section, $userType, $userId);

        if (!empty($keys)) {
            $this->cache->purge($keys); // supports array
        }

        return true;
    }

    protected function getCacheKeys($section, $userType, $userId)
    {
        switch ($section) {

            case 'student':
                return $this->buildDashboardKeys('student_dashboard', $userType, $userId);

            case 'student_receipts':
                return $this->buildDashboardKeys('receipt_dashboard', $userType, $userId);

            case 'franchise':
                return [
                    "franchise_data_active",
                    "franchise_data_blocked"
                ];

            case 'course':
                return [
                    "course_data",
                    "course_data_active",
                    "course_data_blocked"
                ];

            case 'others':
                return [
                    "news_data",
                    "enquiry_data",
                    "gallery_data"
                ];
        }

        return [];
    }

    protected function buildDashboardKeys($prefix, $userType, $userId)
    {
        $periods = ['today', 'weekly', 'monthly', 'annual'];

        if (in_array($userType, ['developer', 'admin'])) {
            return array_map(fn ($p) => "{$prefix}_{$p}", $periods);
        }

        if ($userType === 'franchise') {
            return array_map(fn ($p) => "{$prefix}_{$p}_{$userId}", $periods);
        }

        return [];
    }

    protected function create_Frnachise_ID()
    {
        //Creating new Franchise id method
        $franchiseDetail = $this->interface->fetch_Last_Franchise_Detail();
        $last_fran_id = $franchiseDetail[0]->fran_id;

        if ($last_fran_id != null) {
            $last_fran_id_part_2 = substr($last_fran_id, 5);
            $last_fran_id_part_2++;
        } else {
            $last_fran_id_part_2 = 1;
        }

        $current_fran_id = "WBMGF" . $last_fran_id_part_2;

        return $current_fran_id;
    }

    protected function create_Student_ID()
    {
        //Creating new Student id method
        $stuIdDetail = $this->interface->fetch_Last_Student_Detail();
        $lst_stu_id = $stuIdDetail['lst_stu_id'];

        if (!empty($lst_stu_id)) {
            $lst_stu_id_part_2 = substr($lst_stu_id, 10);
            $nxt_stu_id = round($lst_stu_id_part_2 + 1);
        } else {
            $lst_stu_id_part_2 = 1;
            $nxt_stu_id = $lst_stu_id_part_2;
        }

        $current_stu_id = "WBTAIMGCSM" . $nxt_stu_id;

        return $current_stu_id;
    }

    protected function create_Tmp_Student_ID($min = 999, $max = 999999, $quantity = 1)
    {
        $numbers = range($min, $max);
        shuffle($numbers);
        $randomNumArr = array_slice($numbers, 0, $quantity);

        return "TMPSTUDENT" . $randomNumArr[0];
    }

    protected function create_Receipt_ID()
    {
        //Creating new Franchise id method
        $receiptDetail = $this->interface->fetch_Last_Receipt_Detail();
        $last_rcpt_id = $receiptDetail[0]->receipt_id;

        if ($last_rcpt_id != null) {
            $last_rcpt_id_pt_2 = substr($last_rcpt_id, 17);
            $last_rcpt_id_pt_2++;
        } else {
            $last_rcpt_id_pt_2 = 1;
        }

        $current_rcpt_id = "WBTAIMGCSMRECEIPT" . $last_rcpt_id_pt_2;

        return $current_rcpt_id;
    }
}
