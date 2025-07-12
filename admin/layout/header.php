<?php
if (empty($_SESSION['user_id'])) {
    $url = SITE_URL;
    header("Location: $url");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="shortcut icon" href="<?= RESOURCE_URL ?>images/fav.png" type="image/x-icon">
    <link rel="icon" href="<?= RESOURCE_URL ?>images/fav.png" type="image/x-icon">

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= 'THE AIMGCSM Admin-' . $page_title ?></title>

    <link href="<?= RESOURCE_URL ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= RESOURCE_URL ?>font-awesome/css/font-awesome.css" rel="stylesheet">

    <?php
    if (!empty($cssPluginArr)) {
        foreach ($cssPluginArr as $key => $cssFile) {
    ?>
            <link href="<?= RESOURCE_URL ?>css/plugins/<?= $cssFile ?>.css" rel="stylesheet">
    <?php }
    } ?>

    <link href="<?= RESOURCE_URL ?>css/animate.css" rel="stylesheet">
    <link href="<?= RESOURCE_URL ?>css/style.css" rel="stylesheet">
    <link href="<?= RESOURCE_URL ?>css/custom.css" rel="stylesheet">

    <!--Mainly Jquery parent js -->
    <script src="<?= RESOURCE_URL ?>js/jquery-3.1.1.min.js"></script>

    <!-- Header color change script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/geopattern/1.2.3/js/geopattern.min.js"></script>
</head>

<body>
    <div id="wrapper">
        <div id="preloader" class="show"></div>
        <nav class="navbar-default navbar-static-side sidebar-position" id="sidebar-wrapper" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav metismenu" id="side-menu">
                    <li class="nav-header">

                        <div class="dropdown profile-element">
                            <a href="<?= $_SESSION['user_profile_pic'] ?>" id="logo" data-fancybox="gallery" data-caption="Company Logo"> <img alt="image" class="rounded-circle" src="<?= USER_UPLOAD_URL . 'others/' . $this->site_setting_data->logo ?>" style="height: 60px;width: 70px;" /></a>
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

                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_dashboard") || $_SESSION['user_type'] == 'student') { ?>
                        <li <?php if (!$_GET['route'] || $_GET['route'] == "home") echo "class='active'"; ?>>
                            <a href="<?= SITE_URL ?>"><i class="fa fa-desktop"></i> <span class="nav-label">Dashboard</span></a>
                        </li>
                    <?php } ?>



                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_franchise")) { ?>
                        <li <?php if (in_array($_GET['route'], array('view_franchises', 'add_franchise', 'edit_franchise'))) {
                                echo "class='active'";
                            } ?>>

                            <a href="javascript:void(0)"><i class="fa fa-university"></i> <span class="nav-label">Franchises </span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_franchise")) { ?>

                                    <li <?php if ($_GET['route'] == "view_franchises") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=view_franchises"> <i class="fa fa-list"></i> Franchise List</a>
                                    </li>

                                <?php } ?>

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("create_franchise")) { ?>

                                    <li <?php if ($_GET['route'] == "add_franchise") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=add_franchise"> <i class="fa fa-plus-circle"></i> Add New Franchise</a>
                                    </li>

                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_course")) { ?>

                        <li <?php if ($_GET['route'] == "view_courses" || $_GET['route'] == "add_course" || $_GET['route'] == "edit_course") echo "class='active'"; ?>>

                            <a href="javascript:void(0)"><i class="fa fa-laptop"></i> <span class="nav-label">Courses </span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_course")) { ?>

                                    <li <?php if ($_GET['route'] == "view_courses") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=view_courses"> <i class="fa fa-list"></i> Course List</a>
                                    </li>

                                <?php } ?>

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("create_course")) { ?>

                                    <li <?php if ($_GET['route'] == "add_course") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=add_course"> <i class="fa fa-plus-circle"></i> Add New Course</a>
                                    </li>

                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>


                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_student")) { ?>

                        <li <?php if ($_GET['route'] == "view_students" || $_GET['route'] == "add_student" || $_GET['route'] == "edit_student" || $_GET['route'] == "student_admission" || $_GET['route'] == "manage_temp_students") echo "class='active'"; ?>>

                            <a href="javascript:void(0)"><i class="fa fa-mortar-board"></i> <span class="nav-label">Students </span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_student")) { ?>

                                    <li <?php if ($_GET['route'] == "view_students") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=view_students"> <i class="fa fa-list"></i> Student List</a>
                                    </li>

                                <?php } ?>

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_student") && ($_SESSION['user_type'] == 'franchise' ? ($_SESSION['owned_status'] == 'yes' ? true : false) : true)) { ?>

                                    <li <?php if ($_GET['route'] == "manage_temp_students") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=manage_temp_students"> <i class="fa fa-list"></i>Temporary Students</a>
                                    </li>

                                <?php } ?>

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("create_student")) { ?>

                                    <li <?php if ($_GET['route'] == "add_student") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=add_student"> <i class="fa fa-plus-circle"></i> Add New Student</a>
                                    </li>

                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_receipt")) { ?>

                        <li <?php if ($_GET['route'] == "view_receipts" || $_GET['route'] == "view_due_students" || $_GET['route'] == "manage_receipt") echo "class='active'"; ?>>
                            <a href="javascript:void(0)"><i class="fa fa-money"></i> <span class="nav-label">Receipts </span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_receipt")) { ?>

                                    <li <?php if ($_GET['route'] == "view_receipts") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=view_receipts"> <i class="fa fa-list"></i> Regular Receipts</a>
                                    </li>

                                <?php } ?>

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_due_students")) { ?>

                                    <li <?php if ($_GET['route'] == "view_due_students") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=view_due_students"> <i class="fa fa-users"></i> View Due Students</a>
                                    </li>

                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_exam")) { ?>

                        <li <?php if ($_GET['route'] == "view_exams" || $_GET['route'] == "add_exam" || $_GET['route'] == "edit_exam") echo "class='active'"; ?>>

                            <a href="javascript:void(0)"><i class="fa fa-laptop"></i> <span class="nav-label">Exams </span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_exam")) { ?>

                                    <li <?php if ($_GET['route'] == "view_exams") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=view_exams"> <i class="fa fa-list"></i> Exam List</a>
                                    </li>

                                <?php } ?>

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("create_course")) { ?>

                                    <li <?php if ($_GET['route'] == "add_exam") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=add_exam"> <i class="fa fa-plus-circle"></i> Add New Exam</a>
                                    </li>

                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_gallery")) { ?>

                        <li <?php if ($_GET['route'] == "gallery") echo "class='active'"; ?>>
                            <a href="<?= SITE_URL ?>?route=gallery"><i class="fa fa-picture-o"></i> <span class="nav-label">Gallery</span></a>
                        </li>
                    <?php } ?>

                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("manage_home_slider") || $this->globalLibraryHandlerObj->checkUserRolePermission("manage_city_db")) { ?>

                        <li <?php if ($_GET['route'] == "home_sliders" || $_GET['route'] == "manage_cities") echo "class='active'"; ?>>

                            <a href="javascript:void(0)"><i class="fa fa-pencil-square-o"></i> <span class="nav-label">CMS Management </span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("manage_home_slider")) { ?>

                                    <li <?php if ($_GET['route'] == "home_sliders") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=home_sliders"> <i class="fa fa-picture-o"></i> Home Slider</a>
                                    </li>

                                <?php } ?>

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("manage_city_db")) { ?>

                                    <li <?php if ($_GET['route'] == "manage_cities") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=manage_cities"> <i class="fa fa-building-o"></i> Manage City DB</a>
                                    </li>

                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>


                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_category")) { ?>

                        <li <?php if ($_GET['route'] == "view_category") echo "class='active'"; ?>>
                            <a href="<?= SITE_URL ?>?route=view_category"><i class="fa fa-sitemap"></i> <span class="nav-label">Category</span></a>
                        </li>
                    <?php } ?>

                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_template")) { ?>

                        <li <?php if ($_GET['route'] == "view_email_templates" || $_GET['route'] == "add_email_template" || $_GET['route'] == "edit_email_template") echo "class='active'"; ?>>
                            <a href="javascript:void(0)"><i class="fa fa-inbox"></i> <span class="nav-label">Email Templates </span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_template")) { ?>
                                    <li <?php if ($_GET['route'] == "view_email_templates") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=view_email_templates"> <i class="fa fa-list"></i>Email Template List</a>
                                    </li>

                                <?php } ?>

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("create_template")) { ?>
                                    <li <?php if ($_GET['route'] == "add_email_template") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=add_email_template"> <i class="fa fa-plus-circle"></i>Add New Template</a>
                                    </li>

                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_news")) { ?>

                        <li <?php if ($_GET['route'] == "view_news" || $_GET['route'] == "add_news" || $_GET['route'] == "edit_news") echo "class='active'"; ?>>
                            <a href="javascript:void(0)"><i class="fa fa-question-circle"></i> <span class="nav-label">News </span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_news")) { ?>

                                    <li <?php if ($_GET['route'] == "view_news") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=view_news"> <i class="fa fa-list"></i>News List</a>
                                    </li>

                                <?php } ?>

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("create_news")) { ?>

                                    <li <?php if ($_GET['route'] == "add_news") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=add_news"> <i class="fa fa-plus-circle"></i>Add New News</a>
                                    </li>

                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>


                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("view_enquiry")) { ?>
                        <li <?php if ($_GET['route'] == "view_enquiry") echo "class='active'"; ?>>
                            <a href="<?= SITE_URL ?>?route=view_enquiry"><i class="fa fa-envelope-o"></i> <span class="nav-label">Enquiry</span></a>
                        </li>
                    <?php } ?>


                    <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("update_site_setting") || $this->globalLibraryHandlerObj->checkUserRolePermission("manage_profile") && $_SESSION['user_type'] != 'student') { ?>

                        <li <?php if ($_GET['route'] == "edit_profile" || $_GET['route'] == "edit_admin_profile" || $_GET['route'] == "edit_site_setting") echo "class='active'"; ?>>

                            <a href="javascript:void(0)"><i class="fa fa-cogs"></i> <span class="nav-label">Settings </span> <span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level">

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("manage_profile")) { ?>

                                    <li <?php if ($_GET['route'] == "edit_profile") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=edit_profile"> <i class="fa fa-user-circle"></i>Manage Profile</a>
                                    </li>

                                    <?php if ($_SESSION['user_type'] == "developer") { ?>
                                        <li <?php if ($_GET['route'] == "edit_admin_profile") echo "class='active'"; ?>>
                                            <a href="<?= SITE_URL ?>?route=edit_admin_profile"> <i class="fa fa-user-circle"></i>Manage Admin Profile</a>
                                        </li>
                                    <?php } ?>
                                <?php } ?>

                                <?php if ($this->globalLibraryHandlerObj->checkUserRolePermission("update_site_setting")) { ?>
                                    <li <?php if ($_GET['route'] == "edit_site_setting") echo "class='active'"; ?>>
                                        <a href="<?= SITE_URL ?>?route=edit_site_setting"> <i class="fa fa-cog"></i>Site Settings</a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                    <?php } ?>

                    <?php if ($_SESSION['user_type'] == 'student' && $this->globalLibraryHandlerObj->checkUserRolePermission("manage_profile")) { ?>

                        <li <?php if ($_GET['route'] == "edit_profile") echo "class='active'"; ?>>
                            <a href="<?= SITE_URL ?>?route=edit_profile"><i class="fa fa-cog"></i> <span class="nav-label">Profile Settings</span></a>
                        </li>
                    <?php } ?>

                    <li <?php if ($_GET['route'] == "logout") echo "class='active'"; ?>>
                        <a href="<?= SITE_URL ?>?route=logout" onclick="return confirm('Sure to exit from the system?');"><i class="fa fa-sign-out"></i> <span class="nav-label">Log out</span></a>
                    </li>
                </ul>
            </div>
        </nav>

        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" id="geopattern">
                    <div class="navbar-header">
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-6 col-xs-12">
                                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                            </div>

                            <div class="col-lg-10 col-md-10 col-sm-12">
                            </div>
                        </div>

                        <input type='hidden' id='geo_varity' value='<?= rand(999, 999999) ?>'>
                    </div>

                    <ul class="nav navbar-top-links navbar-right">
                        <li>
                            <a href="<?= SITE_URL . "?route=logout" ?>" onclick="return confirm('Sure to exit from the system?');">
                                <i class="fa fa-sign-out"></i>Log out
                            </a>
                        </li>
                    </ul>

                </nav>
            </div>

            <div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-8">
                    <h2>Basic Form</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= SITE_URL ?>">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a>Forms</a>
                        </li>
                        <li class="breadcrumb-item active">
                            <strong>Basic Form</strong>
                        </li>
                    </ol>
                </div>

                <div class="col-lg-4">
                    <div class="title-action">

                        <?php if (($_SESSION['user_type'] == "admin" || $_SESSION['user_type'] == "developer") || ($_SESSION['user_type'] == "franchise" && $_SESSION['owned_status'] == "yes")) { ?>
                            <a href="<?= SITE_URL ?>?route=student_admission">
                                <button type="button" class="btn btn-success" data-ctype="current_page" data-toggle="tooltip" data-placement="left" title="Special student admission module is here for faster student registration. This module will create student and admission receipt in one click"><i class="fa fa-universal-access"></i> Admission Special</button>
                            </a>
                        <?php } ?>

                        <?php if ($_SESSION['user_type'] == "admin" || $_SESSION['user_type'] == "developer" || $_SESSION['user_type'] == "franchise") { ?>
                            <button type="button" class="btn btn-primary cleanRuntimeUpload" data-toggle="tooltip" data-placement="bottom" title="Clean Runtime Garbage Files From Server"><i class="fa fa-recycle"></i></button>
                        <?php } ?>

                        <?php if ($_SESSION['user_type'] == "developer") { ?>
                            <!-- <button type="button" class="btn btn-danger clearSiteCache" data-ctype="all_pages" data-toggle="tooltip" data-placement="bottom" title="Clear Server Cache Memory"><i class="fa fa-trash"></i></button> -->
                        <?php } ?>

                    </div>
                </div>
            </div>

            <div id="right-sidebar">
                <div class="sidebar-container">

                    <ul class="nav nav-tabs navs-3">
                        <li>
                            <a class="nav-link active" data-toggle="tab" href="#notifications"> Notes </a>
                        </li>
                        <li>
                            <a class="nav-link" data-toggle="tab" href="#activitylog"> Projects </a>
                        </li>
                        <li>
                            <a class="nav-link" data-toggle="tab" href="#settings"> <i class="fa fa-gear"></i> </a>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <div id="notifications" class="tab-pane active">

                            <div class="sidebar-title">
                                <h3> <i class="fa fa-comments-o"></i> Latest Notes</h3>
                                <small><i class="fa fa-tim"></i> You have 10 new message.</small>
                            </div>

                            <div>

                                <div class="sidebar-message">
                                    <a href="#">
                                        <div class="float-left text-center">
                                            <img alt="image" class="rounded-circle message-avatar" src="<?= RESOURCE_URL . 'images/default-user-avatar.jpg' ?>">

                                            <div class="m-t-xs">
                                                <i class="fa fa-star text-warning"></i>
                                                <i class="fa fa-star text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="media-body">

                                            There are many variations of passages of Lorem Ipsum available.
                                            <br>
                                            <small class="text-muted">Today 4:21 pm</small>
                                        </div>
                                    </a>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- Back to top button -->
            <a id="button_to_top"></a>

            <script>
                var SITE_URL = "<?= SITE_URL ?>";
                var RESOURCE_URL = "<?= RESOURCE_URL ?>";
                var RESOURCE_URL = "<?= RESOURCE_URL ?>";
                var user_type = "<?= $_SESSION['user_type'] ?>";
                var ajaxControllerHandler = "<?= SITE_URL ?>controller/callAjaxController.php";
                var exportTableDataController = "<?= SITE_URL ?>controller/exportTableDataController.php";
                var importTableDataController = "<?= SITE_URL ?>controller/importTableDataController.php";
                var backupControllerHandler = "<?= SITE_URL ?>controller/backupController.php";
                $(document).ready(function() {
                    var pattern = GeoPattern.generate('Neel');
                    $('#geopattern').css('background-image', pattern.toDataUrl());
                    var geo_varity = $('#geo_varity').val();
                    pattern = GeoPattern.generate(geo_varity);
                    $('#geopattern').css('background-image', pattern.toDataUrl());
                });
            </script>