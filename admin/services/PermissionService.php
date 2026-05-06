<?php
defined('ROOTPATH') or exit('No direct script access allowed');

class PermissionService
{   
    private $model;
    private $lib;

    public function __construct($model, $lib)
    {
        $this->model = $model;
        $this->lib = $lib;
    }

    public function checkUserRolePermission($user_role_slug, $fetch_type = "hard")
    {
        $paramArr['user_id'] = $_SESSION['user_id'];
        $paramArr['user_type'] = $_SESSION['user_type'];

        // Fetch roles
        if ($fetch_type === "hard") {
            $userRoleArr = $this->model->fetch_Current_User_Role($paramArr);
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
}    