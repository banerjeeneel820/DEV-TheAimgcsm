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
        <!-- <div id="preloader" class="show"></div> -->

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

                    <?php include ROOTPATH . "/layout/sidebar.php"; ?>

                    <?php //include ROOTPATH . "/layout/sidebar_old.php"; ?>
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
                var ajaxControllerHandler = "<?= SITE_URL ?>index.php";
                var exportTableDataController = "<?= SITE_URL ?>controller/exportTableDataController.php";
                var importTableDataController = "<?= SITE_URL ?>controller/importTableDataController.php";
                $(document).ready(function() {
                    var pattern = GeoPattern.generate('Neel');
                    $('#geopattern').css('background-image', pattern.toDataUrl());
                    var geo_varity = $('#geo_varity').val();
                    pattern = GeoPattern.generate(geo_varity);
                    $('#geopattern').css('background-image', pattern.toDataUrl());
                });
            </script>