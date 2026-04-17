<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class UtilityService
{
    private $interface;
    private $lib;

    public function __construct($interface, $lib)
    {
        $this->interface = $interface;
        $this->lib = $lib;
    }

    public function checkUserRolePermission($user_role_slug, $fetch_type = "hard")
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

    public function create_Student_ID()
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

    public function create_Tmp_Student_ID($min = 999, $max = 999999, $quantity = 1)
    {
        $numbers = range($min, $max);
        shuffle($numbers);
        $randomNumArr = array_slice($numbers, 0, $quantity);

        return "TMPSTUDENT" . $randomNumArr[0];
    }

    public function create_Receipt_ID()
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
