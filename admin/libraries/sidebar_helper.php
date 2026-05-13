<?php

defined('ROOTPATH') or exit('No direct script access allowed');

function canShowMenu(array $menu, $permissionService): bool
{
    /*
    |--------------------------------------------------------------------------
    | Custom Condition
    |--------------------------------------------------------------------------
    */

    if (
        isset($menu['custom_condition']) &&
        is_callable($menu['custom_condition'])
    ) {

        return $menu['custom_condition']();
    }

    /*
    |--------------------------------------------------------------------------
    | No Permission
    |--------------------------------------------------------------------------
    */

    if (empty($menu['permissions'])) {
        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | Permission Mode
    |--------------------------------------------------------------------------
    */

    $mode = strtoupper($menu['permission_mode'] ?? 'OR');

    /*
    |--------------------------------------------------------------------------
    | AND Mode
    |--------------------------------------------------------------------------
    */

    if ($mode === 'AND') {

        foreach ($menu['permissions'] as $permission) {

            if (
                !$permissionService
                    ->checkUserRolePermission($permission)
            ) {
                return false;
            }
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | OR Mode
    |--------------------------------------------------------------------------
    */

    foreach ($menu['permissions'] as $permission) {

        if (
            $permissionService
                ->checkUserRolePermission($permission)
        ) {
            return true;
        }
    }

    return false;
}