<?php

defined('ROOTPATH') or exit('No direct script access allowed');


/**
 * Check whether a menu has the required permission.
 *
 * @param array $menu
 * @param object $permissionService
 * @return bool
 */
function hasMenuPermission(array $menu, $permissionService): bool
{
    // No permission restriction
    if (empty($menu['permission'])) {
        return true;
    }

    // Normalize single permission to array
    $permissions = (array) $menu['permission'];

    // Default mode = OR
    $mode = strtoupper($menu['permission_mode'] ?? 'OR');


    /*
    |--------------------------------------------------------------------------
    | AND Mode
    |--------------------------------------------------------------------------
    */

    if ($mode === 'AND') {

        foreach ($permissions as $permission) {

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

    foreach ($permissions as $permission) {

        if (
            $permissionService
                ->checkUserRolePermission($permission)
        ) {
            return true;
        }
    }

    return false;
}


/**
 * Determine whether a menu or any of its children
 * should be displayed.
 *
 * @param array $menu
 * @param object $permissionService
 * @return bool
 */
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
    | Parent Permission
    |--------------------------------------------------------------------------
    */

    if (hasMenuPermission($menu, $permissionService)) {
        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Child Permission
    |--------------------------------------------------------------------------
    */

    if (!empty($menu['children'])) {

        foreach ($menu['children'] as $child) {

            if (canShowMenu($child, $permissionService)) {
                return true;
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Nothing Allowed
    |--------------------------------------------------------------------------
    */

    return false;
}