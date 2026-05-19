<!-- Admin Sidebar Nav Section -->
<nav class="navbar-default navbar-static-side sidebar-position" id="sidebar-wrapper" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">

                <div class="dropdown profile-element">
                    <a href="<?= $_SESSION['user_profile_pic'] ?>" id="logo" data-fancybox="gallery" data-caption="Company Logo"> <img alt="image" class="rounded-circle" src="<?= USER_UPLOAD_URL . 'others/' . $site_setting_data->logo ?>" style="height: 60px;width: 70px;" /></a>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold"><?= $_SESSION['user_name'] ?></span>
                        <span class="text-muted text-xs block"><?= ucfirst($_SESSION['user_type']) ?> <b class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="dropdown-item" href="<?= SITE_URL . '?route=edit_profile' ?>">Profile</a></li>
                        <li><a class="dropdown-item" href="<?= FRONT_SITE_URL ?>" target="_blank">Visit Site</a></li>
                        <li class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= SITE_URL . '?route=logout' ?>" onclick="return confirm('Sure to exit from the system?');">Logout</a></li>
                    </ul>
                </div>

                <div class="logo-element">
                    TAG
                </div>
            </li>

            <?php

            foreach ($sidebarMenus as $menu) :

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

                    <?php if ($hasChildren) : ?>

                        <a href="javascript:void(0)">

                            <i class="<?= $menu['icon'] ?>"></i>

                            <span class="nav-label">
                                <?= $menu['title'] ?>
                            </span>

                            <span class="fa arrow"></span>

                        </a>

                        <ul class="nav nav-second-level collapse <?= $isParentActive ? 'in' : '' ?>">

                            <?php foreach ($menu['children'] as $child) : ?>

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

                    <?php else : ?>

                        <a href="<?= $menu['url'] ?>">

                            <i class="<?= $menu['icon'] ?>"></i>

                            <span class="nav-label">
                                <?= $menu['title'] ?>
                            </span>

                        </a>

                    <?php endif; ?>

                </li>

            <?php endforeach; ?>

        </ul>
    </div>
</nav>