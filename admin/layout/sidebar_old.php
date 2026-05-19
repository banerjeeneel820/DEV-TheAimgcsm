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

             <?php if ($this->permissionService->checkUserRolePermission("view_dashboard") || $_SESSION['user_type'] == 'student') { ?>
                 <li <?php if (!$_GET['route'] || $_GET['route'] == "home") echo "class='active'"; ?>>
                     <a href="<?= SITE_URL ?>">
                         <i class="fa fa-desktop"></i>
                         <span class="nav-label">Dashboard</span>
                     </a>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("view_franchise")) { ?>
                 <li <?php if (in_array($_GET['route'], array('view_franchises', 'add_franchise', 'edit_franchise'))) {
                            echo "class='active'";
                        } ?>>

                     <a href="javascript:void(0)"><i class="fa fa-university"></i>
                         <span class="nav-label">Franchises </span>
                         <span class="fa arrow"></span>
                     </a>
                     <ul class="nav nav-second-level">

                         <?php if ($this->permissionService->checkUserRolePermission("view_franchise")) { ?>

                             <li <?php if ($_GET['route'] == "view_franchises") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=view_franchises">
                                     <i class="fa fa-list"></i> Franchise List
                                 </a>
                             </li>

                         <?php } ?>

                         <?php if ($this->permissionService->checkUserRolePermission("create_franchise")) { ?>

                             <li <?php if ($_GET['route'] == "add_franchise") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=add_franchise">
                                     <i class="fa fa-plus-circle"></i> Add New Franchise
                                 </a>
                             </li>

                         <?php } ?>
                     </ul>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("view_course")) { ?>

                 <li <?php if ($_GET['route'] == "view_courses" || $_GET['route'] == "add_course" || $_GET['route'] == "edit_course") echo "class='active'"; ?>>

                     <a href="javascript:void(0)"><i class="fa fa-laptop"></i>
                         <span class="nav-label">Courses </span>
                         <span class="fa arrow"></span>
                     </a>
                     <ul class="nav nav-second-level">

                         <?php if ($this->permissionService->checkUserRolePermission("view_course")) { ?>

                             <li <?php if ($_GET['route'] == "view_courses") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=view_courses">
                                     <i class="fa fa-list"></i> Course List
                                 </a>
                             </li>

                         <?php } ?>

                         <?php if ($this->permissionService->checkUserRolePermission("create_course")) { ?>

                             <li <?php if ($_GET['route'] == "add_course") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=add_course">
                                     <i class="fa fa-plus-circle"></i> Add New Course
                                 </a>
                             </li>

                         <?php } ?>
                     </ul>
                 </li>
             <?php } ?>


             <?php if ($this->permissionService->checkUserRolePermission("view_student")) { ?>

                 <li <?php if ($_GET['route'] == "view_students" || $_GET['route'] == "add_student" || $_GET['route'] == "edit_student" || $_GET['route'] == "student_admission" || $_GET['route'] == "manage_temp_students") echo "class='active'"; ?>>

                     <a href="javascript:void(0)"><i class="fa fa-mortar-board"></i>
                         <span class="nav-label">Students</span>
                         <span class="fa arrow"></span>
                     </a>
                     <ul class="nav nav-second-level">

                         <?php if ($this->permissionService->checkUserRolePermission("view_student")) { ?>

                             <li <?php if ($_GET['route'] == "view_students") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=view_students">
                                     <i class="fa fa-list"></i> Student List
                                 </a>
                             </li>

                         <?php } ?>

                         <?php if ($this->permissionService->checkUserRolePermission("view_student") && ($_SESSION['user_type'] == 'franchise' ? ($_SESSION['owned_status'] == 'yes' ? true : false) : true)) { ?>

                             <li <?php if ($_GET['route'] == "manage_temp_students") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=manage_temp_students">
                                     <i class="fa fa-list"></i>Temporary Students
                                 </a>
                             </li>

                         <?php } ?>

                         <?php if ($this->permissionService->checkUserRolePermission("create_student")) { ?>

                             <li <?php if ($_GET['route'] == "add_student") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=add_student">
                                     <i class="fa fa-plus-circle"></i> Add New Student
                                 </a>
                             </li>

                         <?php } ?>
                     </ul>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("view_receipt")) { ?>

                 <li <?php if ($_GET['route'] == "view_receipts" || $_GET['route'] == "view_due_students" || $_GET['route'] == "manage_receipt") echo "class='active'"; ?>>
                     <a href="javascript:void(0)"><i class="fa fa-money"></i>
                         <span class="nav-label">Receipts </span>
                         <span class="fa arrow"></span>
                     </a>
                     <ul class="nav nav-second-level">

                         <?php if ($this->permissionService->checkUserRolePermission("view_receipt")) { ?>

                             <li <?php if ($_GET['route'] == "view_receipts") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=view_receipts">
                                     <i class="fa fa-list"></i> Regular Receipts
                                 </a>
                             </li>

                         <?php } ?>

                         <?php if ($this->permissionService->checkUserRolePermission("view_due_students")) { ?>

                             <li <?php if ($_GET['route'] == "view_due_students") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=view_due_students">
                                     <i class="fa fa-users"></i> View Due Students
                                 </a>
                             </li>

                         <?php } ?>
                     </ul>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("view_exam")) { ?>

                 <li <?php if ($_GET['route'] == "view_exams" || $_GET['route'] == "add_exam" || $_GET['route'] == "edit_exam" || $_GET['route'] == "manage_questions") echo "class='active'"; ?>>

                     <a href="javascript:void(0)"><i class="fa fa-laptop"></i>
                         <span class="nav-label">Exams </span>
                         <span class="fa arrow"></span>
                     </a>
                     <ul class="nav nav-second-level">

                         <?php if ($this->permissionService->checkUserRolePermission("view_exam")) { ?>

                             <li <?php if ($_GET['route'] == "view_exams") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=view_exams">
                                     <i class="fa fa-list"></i> Exam List
                                 </a>
                             </li>

                         <?php } ?>

                         <?php if ($this->permissionService->checkUserRolePermission("create_exam")) { ?>

                             <li <?php if ($_GET['route'] == "add_exam") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=add_exam">
                                     <i class="fa fa-plus-circle"></i> Add New Exam
                                 </a>
                             </li>

                         <?php } ?>
                     </ul>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("view_gallery")) { ?>

                 <li <?php if ($_GET['route'] == "gallery") echo "class='active'"; ?>>
                     <a href="<?= SITE_URL ?>?route=gallery">
                         <i class="fa fa-picture-o"></i>
                         <span class="nav-label">Gallery</span>
                     </a>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("manage_home_slider") || $this->permissionService->checkUserRolePermission("manage_city_db")) { ?>

                 <li <?php if ($_GET['route'] == "home_sliders" || $_GET['route'] == "manage_cities") echo "class='active'"; ?>>

                     <a href="javascript:void(0)"><i class="fa fa-pencil-square-o"></i>
                         <span class="nav-label">CMS Management </span>
                         <span class="fa arrow"></span>
                     </a>
                     <ul class="nav nav-second-level">

                         <?php if ($this->permissionService->checkUserRolePermission("manage_home_slider")) { ?>

                             <li <?php if ($_GET['route'] == "home_sliders") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=home_sliders">
                                     <i class="fa fa-picture-o"></i> Home Slider
                                 </a>
                             </li>

                         <?php } ?>

                         <?php if ($this->permissionService->checkUserRolePermission("manage_city_db")) { ?>

                             <li <?php if ($_GET['route'] == "manage_cities") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=manage_cities">
                                     <i class="fa fa-building-o"></i> Manage City DB
                                 </a>
                             </li>

                         <?php } ?>
                     </ul>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("view_category")) { ?>

                 <li <?php if ($_GET['route'] == "view_category") echo "class='active'"; ?>>
                     <a href="<?= SITE_URL ?>?route=view_category">
                         <i class="fa fa-sitemap"></i>
                         <span class="nav-label">Category</span>
                     </a>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("view_template")) { ?>

                 <li <?php if ($_GET['route'] == "view_email_templates" || $_GET['route'] == "add_email_template" || $_GET['route'] == "edit_email_template") echo "class='active'"; ?>>
                     <a href="javascript:void(0)"><i class="fa fa-inbox"></i>
                         <span class="nav-label">Email Templates </span>
                         <span class="fa arrow"></span>
                     </a>
                     <ul class="nav nav-second-level">

                         <?php if ($this->permissionService->checkUserRolePermission("view_template")) { ?>
                             <li <?php if ($_GET['route'] == "view_email_templates") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=view_email_templates">
                                     <i class="fa fa-list"></i>Email Template List
                                 </a>
                             </li>

                         <?php } ?>

                         <?php if ($this->permissionService->checkUserRolePermission("create_template")) { ?>
                             <li <?php if ($_GET['route'] == "add_email_template") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=add_email_template">
                                     <i class="fa fa-plus-circle"></i>Add New Template
                                 </a>
                             </li>

                         <?php } ?>
                     </ul>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("view_news")) { ?>

                 <li <?php if ($_GET['route'] == "view_news" || $_GET['route'] == "add_news" || $_GET['route'] == "edit_news") echo "class='active'"; ?>>
                     <a href="javascript:void(0)"><i class="fa fa-question-circle"></i>
                         <span class="nav-label">News </span>
                         <span class="fa arrow"></span>
                     </a>
                     <ul class="nav nav-second-level">

                         <?php if ($this->permissionService->checkUserRolePermission("view_news")) { ?>

                             <li <?php if ($_GET['route'] == "view_news") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=view_news">
                                     <i class="fa fa-list"></i>News List
                                 </a>
                             </li>

                         <?php } ?>

                         <?php if ($this->permissionService->checkUserRolePermission("create_news")) { ?>

                             <li <?php if ($_GET['route'] == "add_news") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=add_news">
                                     <i class="fa fa-plus-circle"></i>Add New News
                                 </a>
                             </li>

                         <?php } ?>
                     </ul>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("view_enquiry")) { ?>
                 <li <?php if ($_GET['route'] == "view_enquiry") echo "class='active'"; ?>>
                     <a href="<?= SITE_URL ?>?route=view_enquiry">
                         <i class="fa fa-envelope-o"></i>
                         <span class="nav-label">Enquiry</span>
                     </a>
                 </li>
             <?php } ?>

             <?php if ($this->permissionService->checkUserRolePermission("update_site_setting") || $this->permissionService->checkUserRolePermission("manage_profile") && $_SESSION['user_type'] != 'student') { ?>

                 <li <?php if ($_GET['route'] == "edit_profile" || $_GET['route'] == "edit_admin_profile" || $_GET['route'] == "edit_site_setting") echo "class='active'"; ?>>

                     <a href="javascript:void(0)"><i class="fa fa-cogs"></i>
                         <span class="nav-label">Settings </span>
                         <span class="fa arrow"></span>
                     </a>
                     <ul class="nav nav-second-level">

                         <?php if ($this->permissionService->checkUserRolePermission("manage_profile")) { ?>

                             <li <?php if ($_GET['route'] == "edit_profile") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=edit_profile">
                                     <i class="fa fa-user-circle"></i>Manage Profile
                                 </a>
                             </li>

                             <?php if ($_SESSION['user_type'] == "developer") { ?>
                                 <li <?php if ($_GET['route'] == "edit_admin_profile") echo "class='active'"; ?>>
                                     <a href="<?= SITE_URL ?>?route=edit_admin_profile">
                                         <i class="fa fa-user-circle"></i>Manage Admin Profile
                                     </a>
                                 </li>
                             <?php } ?>
                         <?php } ?>

                         <?php if ($this->permissionService->checkUserRolePermission("update_site_setting")) { ?>
                             <li <?php if ($_GET['route'] == "edit_site_setting") echo "class='active'"; ?>>
                                 <a href="<?= SITE_URL ?>?route=edit_site_setting">
                                     <i class="fa fa-cog"></i>Site Settings
                                 </a>
                             </li>
                         <?php } ?>
                     </ul>
                 </li>
             <?php } ?>

             <li <?php if ($_GET['route'] == "logout") echo "class='active'"; ?>>
                 <a href="<?= SITE_URL ?>?route=logout" onclick="return confirm('Sure to exit from the system?');">
                     <i class="fa fa-sign-out"></i>
                     <span class="nav-label">Log out</span>
                 </a>
             </li>

         </ul>
     </div>
 </nav>