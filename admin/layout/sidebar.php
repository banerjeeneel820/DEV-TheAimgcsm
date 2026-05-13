<?php
defined('ROOTPATH') or exit('No direct script access allowed');


foreach ($sidebarMenus as $menu): 

    /*
    |--------------------------------------------------------------------------
    | Skip Hidden Menus
    |--------------------------------------------------------------------------
    */

    if (!canShowMenu($menu, $permissionService)) {
        continue;
    }

    /*
    |--------------------------------------------------------------------------
    | Active Parent Menu
    |--------------------------------------------------------------------------
    */

    $isParentActive = in_array(
        $currentRoute,
        $menu['routes']
    );

    /*
    |--------------------------------------------------------------------------
    | Has Children
    |--------------------------------------------------------------------------
    */

    $hasChildren = !empty($menu['children']);

    ?>

    <li class="<?= $isParentActive ? 'active' : '' ?>">

        <?php if ($hasChildren): ?>

            <a href="javascript:void(0)">

                <i class="<?= $menu['icon'] ?>"></i>

                <span class="nav-label">
                    <?= $menu['title'] ?>
                </span>

                <span class="fa arrow"></span>

            </a>

            <ul class="nav nav-second-level collapse <?= $isParentActive ? 'in' : '' ?>">

                <?php foreach ($menu['children'] as $child): ?>

                    <?php

                    /*
                    |--------------------------------------------------------------------------
                    | Skip Hidden Child Menus
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !canShowMenu(
                            $child,
                            $permissionService
                        )
                    ) {
                        continue;
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Active Child
                    |--------------------------------------------------------------------------
                    */

                    $isChildActive =
                        $currentRoute == $child['route'];

                    ?>

                    <li class="<?= $isChildActive ? 'active' : '' ?>">

                        <a href="<?= SITE_URL . $child['url'] ?>">

                            <i class="<?= $child['icon'] ?>"></i>

                            <?= $child['title'] ?>

                        </a>

                    </li>

                <?php endforeach; ?>

            </ul>

        <?php else: ?>

            <a href="<?= $menu['url'] ?>">

                <i class="<?= $menu['icon'] ?>"></i>

                <span class="nav-label">
                    <?= $menu['title'] ?>
                </span>

            </a>

        <?php endif; ?>

    </li>

<?php endforeach; ?>