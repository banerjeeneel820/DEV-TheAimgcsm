<!DOCTYPE html>
<html>

<head>
<meta charset="utf-8">
<title><?=$title?></title> 

<!-- Responsive -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<!--Meta tags end -->

<link rel="shortcut icon" href="<?=SITE_URL.'uploads/others/'.$siteSettingArr->favicon?>" type="image/x-icon">
<link rel="icon" href="<?=SITE_URL.'uploads/others/'.$siteSettingArr->favicon?>" type="image/x-icon">

<!-- Stylesheets -->
<!--<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">-->
<link href="<?=RESOURCE_URL?>css/bootstrap.css" rel="stylesheet">

<link href="<?=RESOURCE_URL?>css/style.css" rel="stylesheet">
<link href="<?=RESOURCE_URL?>css/responsive.css" rel="stylesheet">

<!-- <link href="<?=RESOURCE_URL?>css/font-awesome.min.css" rel="stylesheet"> -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<?php if($urlSegmentArr['route'] == 'course' || $urlSegmentArr['route'] == 'franchise'){ ?>
    <!-- Datatable css--> 
    <link href="<?=ADMIN_RESOURCE_URL?>css/plugins/dataTables/datatables.min.css" rel="stylesheet">
<?php } ?>    

<link href="<?=RESOURCE_URL?>css/custom.css" rel="stylesheet">

<!-- Including mother js library for avail it for others -->
<script src="<?=RESOURCE_URL?>js/jquery.js"></script>

<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"></script>

<script type="text/javascript">
  //Decalring global variables
  var SITE_URL = "<?=SITE_URL?>";
  var ajaxCallUrl = "<?=SITE_URL?>ajax/callAjaxUrl.php"; 
</script>     

 <style>
    .headfer-des{
        position: relative;
        color: red;
        text-align: center;
        background-color: #ffffff;
        background-position: left top;
        background-repeat: no-repeat;
        font-size: 16px;
        line-height: 1.7em;
        padding: 20px 90px 25px 90px;
    }
</style>
    
</head>

<body class="hidden-bar-wrapper">

<div class="page-wrapper">
    
    <!-- Preloader -->
    <div class="preloader"></div>
    <!--<div class="book_preload">
        <div class="book">
            <div class="book__page"></div>
            <div class="book__page"></div>
            <div class="book__page"></div>
        </div>
    </div>-->
    
    <!-- Main Header-->
    <header class="main-header">
        <!--Header Top-->
        <div class="header-top">
            <div class="auto-container">
                <div class="row clearfix">
                    <!--Top Left-->
                    <div class="top-left col-lg-5 col-md-12 col-sm-12">
                        <ul>
                            <li>Welcome to the THE ALL INDIA MGCSM</li>
                        </ul>
                    </div>
                    
                    <!--Top Right-->
                    <div class="top-right col-lg-7 col-md-12 col-sm-12">
                        <div class="question">You have any question? <a href="tel:+440-98-5298">+91 <?=$siteSettingArr->phone?></a></div>
                        <!--Social Box-->
                        <ul class="social-box">
                            <li><a href="<?=$siteSettingArr->facebook_link?>" target="_blank"><span class="fab fa-facebook-f"></span></a></li>
                            <li><a href="<?=$siteSettingArr->twitter_link?>" target="_blank"><span class="fab fa-twitter"></span></a></li>
                            <li><a href="<?=$siteSettingArr->linkdin_link?>" target="_blank"><span class="fab fa-linkedin-in"></span></a></li>
                            <li><a href="<?=$siteSettingArr->youtube_link?>" target="_blank"><span class="fab fa-youtube"></span></a></li>
                            <li><a href="<?=$siteSettingArr->instagram_link?>" target="_blank"><span class="fab fa-instagram"></span></a></li>
                        </ul>
                    </div>
                
                </div>
            </div>
        </div>
        
        <!--Header-Upper-->
        <div class="header-upper">
            <div class="auto-container">
                <div class="clearfix">
                    
                    <div class="pull-left logo-box">
                        <div class="logo"><a href="index-2.html"><img src="<?=SITE_URL.'uploads/others/'.$siteSettingArr->header_logo?>" alt="The AIMGCSM" title="The AIMGCSM" style="width: 230px;height: 80px;"></a></div>
                    </div>
                    
                    <div class="pull-right upper-right">
                        <div class="info-outer clearfix">
                        
                            <!--Info Box-->
                            <div class="upper-column info-box">
                                <div class="icon-box"><span class="flaticon-home-1"></span></div>
                                <ul>
                                    <li><span>North, 24 Pargana</span> <br> PIN CODE- 700065, Kolkata</li>
                                </ul>
                            </div>
                            
                            <!--Info Box-->
                            <div class="upper-column info-box">
                                <div class="icon-box"><span class="flaticon-envelope"></span></div>
                                <ul>
                                    <li><span>West Bengal</span> <br> <?=strtolower($siteSettingArr->contact_email)?></li>
                                </ul>
                            </div>
                            
                            <!--Info Box-->
                            <div class="upper-column info-box">
                                <div class="icon-box"><span class="flaticon-stopwatch"></span></div>
                                <ul>
                                    <li><span>Working Hours</span> <br> Mon-Sat:9.30am to 7.00pm</li>
                                </ul>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                
            </div>
        </div>
        <!--End Header Upper-->

        <div class="header-upper">
            <div class="auto-container">
                <div class="clearfix">
                    <div class="col-lg-12 col-md-12 col-sm-12">

                        <div class="headfer-des pt-0">
                           <img src="<?=RESOURCE_URL?>images/resource/sitename.gif" alt="The AIMGCSM">
                           <h4 style="color:#3333FF;padding-top: 10px;">An  ISO 9001:2015 Certified Organization</h4>
                               <span style="box-shadow: 0px 0px 20px rgb(0 0 0 / 15%);">An Autonomous Body Regd. Under Govt. Of WB REG NO. IV-5203 based On TR Act.1882, Govt. Of India. Inspired By National Task Force on IT &amp; SD , Govt. Of India . Empanelled Under: NPS- NITI AAYOG Formally known- Planning ommission , Govt. Of India.Deptt. Of Labour, NCT, Delhi, Govt. Of India .Regd. Under  Ministry Of Small &amp; Medium  Enterprise - MSME , Govt. Of India .
                             </span>
                       </div> 
                    </div> 
                </div>
            </div>
        </div>           
        
        <!--Header Lower-->
        <div class="header-lower">
            <div class="auto-container">
                <div class="nav-outer clearfix">
                    <!-- Main Menu -->
                    <nav class="main-menu navbar-expand-md">
                        <div class="navbar-header">
                            <!-- Toggle Button -->      
                            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                        </div>
                        
                        <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent">
                            <ul class="navigation clearfix">
                                <li class="<?php if(!$urlSegmentArr['route'])echo 'current';?>"><a href="<?=SITE_URL?>">Home</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'about-us'){echo 'current';}?>"><a href="<?=SITE_URL?>about-us/">About</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'course' || $urlSegmentArr['route'] == 'course-single'){echo 'current';}?>"><a href="<?=SITE_URL?>course/">Course</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'franchise'){echo 'current';}?>"><a href="<?=SITE_URL?>franchise/">Franchise</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'student-verification'){echo 'current';}?>"><a href="<?=SITE_URL?>student-verification">Student Verification</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'news'){echo 'current';}?>"><a href="<?=SITE_URL?>news/">News</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'gallery'){echo 'current';}?>"><a href="<?=SITE_URL?>gallery/">Gallery</a></li>
                                
                                <li class="<?php if($urlSegmentArr['route'] == 'contact-us'){echo 'current';}?>"><a href="<?=SITE_URL?>contact-us/">Contact us</a></li>

                                 <?php if(isset($_SESSION['user_email'])) { ?>
                                    <li class="dropdown"><a href="#"><i class="fa fa-user"></i> Your Profile</a>
                                        <ul>
                                            <li><a href="<?=SITE_URL.'admin'?>" target="_blank">User Dashboard</a></li>
                                            <li><a href="<?=SITE_URL.'logout'?>">Logout</a></li>
                                        </ul>
                                    </li>
                                <?php }else{ ?>  
                                   <li class="dropdown <?php if($urlSegmentArr['route'] == 'register'){echo 'current';}?>"><a href="#"><i class="fa fa-user"></i> Log in</a>
                                        <ul>
                                          <li><a href="<?=SITE_URL.'admin?login_type=admin'?>" target="_blank">Admin Login</a></li>
                                          <li><a href="<?=SITE_URL.'admin?login_type=franchise'?>" target="_blank">Franchise Login</a></li>
                                          <li><a href="<?=SITE_URL.'student-verification'?>">Student Login</a></li>
                                        </ul>
                                    </li>
                                <?php } ?>   
                            </ul>
                        </div>
                    </nav>
                    
                    <!-- Main Menu End-->
                    <div class="outer-box clearfix">
                        <!--Search Btn
                        <div href="#modal-popup-2" class="navsearch-button xs-modal-popup"><i class="icon flaticon-magnifying-glass-1"></i></div>-->
                        
                        <!-- Main Menu End-->
                        <div class="nav-box">
                            <div class="nav-btn nav-toggler navSidebar-button"><span class="icon flaticon-menu-1"></span></div>
                        </div>                      
                    </div>
                </div>
            </div>
        </div>
        <!--End Header Lower-->
        
        <!--Sticky Header-->
        <div class="sticky-header">
            <div class="auto-container clearfix">
                <!--Logo-->
                <div class="logo pull-left">
                    <a href="<?=SITE_URL?>" class="img-responsive"><img src="<?=SITE_URL.'uploads/others/'.$siteSettingArr->sticky_logo?>" alt="The AIMGCSM" title="THE AIMGCSM" style="width: 160px;height:60px;"></a>
                </div>
                
                <!--Right Col-->
                <div class="right-col pull-right">
                    <!-- Main Menu -->
                    <nav class="main-menu navbar-expand-md">
                        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent1" aria-controls="navbarSupportedContent1" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        
                        <div class="navbar-collapse collapse clearfix" id="navbarSupportedContent1">
                            <ul class="navigation clearfix">
                                <li class="<?php if(!$urlSegmentArr['route'])echo 'current';?>"><a href="<?=SITE_URL?>">Home</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'about-us'){echo 'current';}?>"><a href="<?=SITE_URL?>about-us/">About</a></li>
                                                               
                                <li class="<?php if($urlSegmentArr['route'] == 'course' || $urlSegmentArr['route'] == 'course-single'){echo 'current';}?>"><a href="<?=SITE_URL?>course/">Course</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'franchise'){echo 'current';}?>"><a href="<?=SITE_URL?>franchise/">Franchise</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'student-verification'){echo 'current';}?>"><a href="<?=SITE_URL?>student-verification/">Student Verification</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'news'){echo 'current';}?>"><a href="<?=SITE_URL?>news/">News</a></li>

                                <li class="<?php if($urlSegmentArr['route'] == 'gallery'){echo 'current';}?>"><a href="<?=SITE_URL?>gallery/">Gallery</a></li>

                                 <?php if(isset($_SESSION['user_email'])) { ?>
                                    <li class="dropdown"><a href="#"><i class="fa fa-user"></i> Your Profile</a>
                                        <ul>
                                            <li><a href="<?=SITE_URL.'admin'?>" target="_blank">User Dashboard</a></li>
                                            <li><a href="<?=SITE_URL.'logout'?>">Logout</a></li>
                                        </ul>
                                    </li>
                                <?php }else{ ?>  
                                   <li class="dropdown <?php if($urlSegmentArr['route'] == 'register'){echo 'current';}?>"><a href="#"><i class="fa fa-user"></i> Login</a>
                                        <ul>
                                          <li><a href="<?=SITE_URL.'admin?login_type=admin'?>" target="_blank">Admin Login</a></li>
                                          <li><a href="<?=SITE_URL.'admin?login_type=franchise'?>" target="_blank">Franchise Login</a></li>
                                          <li><a href="<?=SITE_URL.'student-verification'?>" target="_blank">Student Login</a></li>
                                        </ul>
                                    </li>
                                <?php } ?> 
                                
                                <li class="<?php if($urlSegmentArr['route'] == 'contact-us'){echo 'current';}?>"><a href="<?=SITE_URL?>contact-us/">Contact us</a></li>

                            </ul>
                        </div>
                    </nav><!-- Main Menu End-->
                </div>
                
            </div>
        </div>
        <!--End Sticky Header-->
    
    </header>
    <!--End Main Header -->
   
    <!---Cookies Div--->
    <div id="cookie-notice" class="cookie-notification">
      <div class="container">
        <div class="wp-block-column cookie-svg">
        <div>
         <img src="https://fadzrinmadu.github.io/hosted-assets/cookie-consent-box-using-html-css-and-javascript/cookie.png" alt="">
        </div>
        </div>
        <div class="wp-block-column cookie-content">
          <div class="ct-cookies-content">We use cookies to ensure that we give you the best experience on our website. For more info read our <a href="https://wpfy.org/privacy-policy/" target="_blank">Privacy Statement</a> &amp; <a href="https://wpfy.org/cookie-policy/" target="_blank">Cookies Policy</a>.</div></div>
          <button class="ct-accept" onclick="acceptCookie();">Accept</button><button class="ct-close" onclick="myFunction()" >×</button>
      </div>
    </div>
    <!---End here--->

    <!--What'sapp Widget
    <a href="https://api.whatsapp.com/send?phone=9831649099" class="float-whatsapp" target="_blank">
        <i class="fa fa-whatsapp my-float-whatsapp"></i>
    </a>-->
