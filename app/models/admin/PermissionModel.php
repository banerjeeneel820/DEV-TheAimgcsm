<?php
namespace App\Models\Admin;

use App\Core\Database;
use App\Core\BaseModel;

defined('ROOTPATH') or exit('No direct script access allowed');

class PermissionModel extends BaseModel
{
   public function __construct(Database $database)
   {
      parent::__construct($database);
   }

   public function fetch_Current_User_Role($paramArr = array())
   {
      $user_type = $paramArr['user_type'];
      $user_id = $paramArr['user_id'];


      if ($user_type == 'admin' || $user_type == 'developer') {
         $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "global_support_admin WHERE `user_type` = '$user_type' AND `id` = '$user_id' AND `user_status` = 'active'";
      } elseif ($user_type == 'franchise') {
         $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "franchise WHERE `id`='$user_id'";
      } else {
         $sql = "SELECT * FROM " . DB_AIMGCSM . "." . TABLEPREFIX . "students WHERE `id`='$user_id'";
      }

      //echo $sql;exit();
      $resultArr = $this->global_Fetch_Single_DB($sql);
      $userRoleArr = unserialize($resultArr->user_role);

      return $userRoleArr;
   }

}   