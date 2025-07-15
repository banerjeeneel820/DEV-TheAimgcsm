<?php
  /*$courseparamArr['protocol'] = 'main_page';
  $courseArr = fetchGlobalCourse($courseparamArr);
  print"<pre>";
  print_r($courseArr);
  print"</pre>";*/
?>

	<!--Page Title-->
    <section class="page-title" style="background-image:url(<?=RESOURCE_URL?>images/background/12.jpg);">
    	<div class="auto-container">
        	<div class="row clearfix">
            	<!--Title -->
            	<div class="title-column col-lg-6 col-md-12 col-sm-12">
                	<h1>Course Page</h1>
                </div>
                <!--Bread Crumb -->
                <div class="breadcrumb-column col-lg-6 col-md-12 col-sm-12">
                    <ul class="bread-crumb clearfix">
                        <li><a href="<?=SITE_URL?>"><span class="icon fas fa-home"></span> Home</a></li>
                        <li class="active"><span class="icon fas fa-arrow-alt-circle-right"></span> Latest News</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->
	
   
    <!--Sidebar Page Container-->
    <div class="sidebar-page-container" id="page_content">
        <div class="auto-container">
             
             <div class="shimmer-preview">
                 <div class="full-row-box shimmer-shine"></div>
             </div>
             
             <div class="div-content d-none">  
                 <div class="alert alert-success text-center ml-3" role="alert">
                  Our all course details have been given below.&nbsp;If you have any question please don’t hesitate to contact us @ <?=$siteSettingArr->phone?>
                </div>
             </div>   
        <div class="row clearfix">
                
          <!--Content Side-->
          <div class="content-side col-lg-8 col-md-12 col-sm-12">
             
             <div class="row">
               <div class="col-lg-12">
                 
                 <div class="shimmer-preview">
                    <?php for($pi=1;$pi<=60;$pi++){ ?> 
                      <div class="table-box shimmer-shine"></div>
                    <?php } ?>
                 </div>
                 
                 <div class="div-content">   
                    <div class="table-responsive">
                       <table class="dataTables styled-table text-center">
                            <thead class="cursor-pointer">
                                <tr>
                                    <th>Course</th>
                                    <th>Course Title</th>
                                    <th>Course Duration</th>
                                    <th>Download Brochure</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                                foreach($courseArr as $index => $content){
                                     
                                    $course_slug = seoUrl($content->course_title,'seo');
                                     
                                    $course_thumbnail = USER_UPLOAD_DIR.'course/'.$content->course_thumbnail;

                                    if (!strlen($content->course_thumbnail)>0 || !file_exists($course_thumbnail)) {   
                                      $course_thumbnail = ADMIN_RESOURCE_URL.'images/preview.jpg'; 
                                    }else{
                                      $course_thumbnail = USER_UPLOAD_URL.'course/'.$content->course_thumbnail;
                                    }

                                    if(strlen($content->course_pdf)>0 && file_exists(USER_UPLOAD_DIR.'course/'.$content->course_pdf)){
                                       $course_pdf = USER_UPLOAD_URL.'course/'.$content->course_pdf;
                                    }else{
                                       $course_pdf = ADMIN_RESOURCE_URL.'images/COMPUTER-COURSE.pdf';
                                    }
                              ?> 
                                  <tr>
                                    <td class="client-avatar">
                                         <a href="<?=$course_thumbnail?>" data-fancybox="gallery"  data-caption="<?=$content->course_title?>">
                                        <img alt="image" src="<?=$course_thumbnail?>" style="height:55px;width:55px;">
                                        </a>
                                    </td>

                                    <td class="project-title">
                                        <span>
                                            <?=$content->course_title?>
                                        </span>
                                    </td>

                                    <td class="project-title">
                                       Course Duration:&nbsp;<?=$content->course_duration?>
                                    </td>
                                    
                                    <td class="project-status">
                                        <a href="<?=SITE_URL.'course-detail/'.$course_slug?>" class="btn btn-warning btn-circle btn-sm" data-toogle="tooltip" data-placement="top" title="View Course Details"><i class="fa fa-eye" style="color:#ffff;"></i></a>  
                                         
                                       <a href="<?=$course_pdf?>" class="btn btn-success btn-circle btn-sm exportReceiptData" download><i class="fa fa-download" style="color:#ffff;"></i></a>      
                                    </td>

                                 </tr>
                               <?php } ?>    
                            </tbody>
                       </table>
                    </div>   
                 </div> 
                    
                </div>   
             </div>   
                    
          </div>
                
            <!--Sidebar Side-->
            <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                <aside class="sidebar sticky_sidebar" id="page_sidebar">
                    
                    <div class="shimmer-preview">
                        <?php for($pi=1;$pi<=12;$pi++){ ?> 
                          <div class="form-row-box shimmer-shine"></div>
                        <?php } ?>
                    </div>

                    <div class="div-content d-none"> 
                        <!--Category Blog-->
                        <div class="sidebar-widget categories-blog">
                            <div class="sidebar-title">
                                <h2>Course Enquiry</h2>
                            </div>
                            <form method="post" id="user_enquiry_form" onsubmit="return false;">
                                <input type="hidden" name="action" id="action" value="createGlobalEnquiry">
                                <input type="hidden" name="enquiry_type" id="enquiry_type" value="course">
                                <input type="hidden" name="course_name" id="course_name" value="">
                                <!-- Form Group -->
                                <div class="form-group">
                                    <input type="text" class="form-control" name="user_name" value="" placeholder="Your name" required>
                                </div>

                                <div class="form-group">
                                    <input type="email" class="form-control" name="user_email" id="user_email" value="" placeholder="Your email">
                                </div>

                                <div class="form-group">
                                    <input type="number" class="form-control" name="user_phone" value="" placeholder="Your Contact No" required>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="user_city" id="user_city" value="" placeholder="Your City" required>
                                </div>

                                <div class="form-group">
                                     <select class="form-control course" name="course_id" id="course_id" data-placeholder="Choose a Course..."required>
                                       <option selected disabled>Choose a Course</option>
                                       <?php foreach($courseArr as $course){ 
                                       ?>
                                        <option value="<?=$course->id?>" id="course_<?=$course->id?>"><?=$course->course_title?></option>
                                       <?php } ?>
                                    </select>
                                </div>

                                <div class="form-group mt-4">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0">
                                      <div class="g-recaptcha" id="form_recaptcha_div"></div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <textarea name="user_message" class="form-control" style="height:80px;" placeholder="Add a quick note..."></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" id="contact_submit" class="btn btn-success" style="width:100%">Submit</button>
                                </div>    
                            </form>   
                        </div>
                        
                        <!-- Help Widget -->
                        <div class="sidebar-widget help-widget">
                            <div class="sidebar-title">
                                <h2>Need Help?</h2>
                            </div>
                            <div class="widget-content">
                                <div class="text">if you have any question please don’t hesitate to contact us</div>
                                <ul class="list">
                                    <li><span class="icon fas fa-phone-volume"></span>Call us @ <?=$siteSettingArr->phone?></li>
                                    <li><span class="icon fas fa-envelope"></span>Reach us @ <?=strtolower($siteSettingArr->contact_email)?></li>
                                </ul>
                            </div>
                        </div>
                    </div> 
                </aside>
            </div>
        
        </div>
    </div>
</div>

