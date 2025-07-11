<?php
  $course_slug = $urlSegmentArr['params'][1];
  $course_title = seoUrl($course_slug,'r_seo'); 
  $courseDetailArr = fetchCourseDetail($course_title);

  //$dateValue = strtotime($courseDetailArr->created_at);   
  $dateValue = time();                  
  //$yr = date("Y", $dateValue) ." "; 
  $month = date("m", $dateValue)." "; 
  $date = date("d", $dateValue); 

  //Configuring course file data
  if(strlen($courseDetailArr->course_thumbnail)>0 && file_exists(USER_UPLOAD_DIR.'course/'.$courseDetailArr->course_thumbnail)){
     $course_thumbnail = USER_UPLOAD_URL.'course/'.$courseDetailArr->course_thumbnail;
  }else{
     $course_thumbnail = RESOURCE_URL.'images/preview.jpg';
  }

  if(strlen($courseDetailArr->course_pdf)>0 && file_exists(USER_UPLOAD_DIR.'course/'.$courseDetailArr->course_pdf)){
     $course_pdf = USER_UPLOAD_URL.'course/'.$courseDetailArr->course_pdf;
  }

  /*print"<pre>";
  print_r($courseDetailArr);
  print"</pre>";*/
?>
	
	<!--Page Title-->
    <section class="page-title" style="background-image:url(<?=RESOURCE_URL?>images/background/12.jpg);">
    	<div class="auto-container">
        	<div class="row clearfix">
            	<!--Title -->
            	<div class="title-column col-lg-6 col-md-12 col-sm-12">
                	<h1>Course Detail</h1>
                </div>
                <!--Bread Crumb -->
                <div class="breadcrumb-column col-lg-6 col-md-12 col-sm-12">
                    <ul class="bread-crumb clearfix">
                        <li><a href="<?=SITE_URL?>"><span class="icon fas fa-home"></span> Home</a></li>
                        <li class="active"><span class="icon fas fa-arrow-alt-circle-right"></span> Course Detail</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->
	
	<!--Sidebar Page Container-->
    <div class="sidebar-page-container" id="page_content">
    	<div class="auto-container">
        	<div class="row clearfix">
            	
                <!--Content Side-->
                <div class="content-side col-lg-8 col-md-12 col-sm-12">
					<div class="news-detail">
						<div class="inner-box">
							<div class="image">
								<img src="<?=$course_thumbnail?>" alt="<?=$courseDetailArr->course_title?>" style="width:770px;height:450px;"/>
							</div>
							<div class="lower-content">
								<div class="content">
									<div class="date-outer">
										<div class="date"><?=$date?></div>
                                        <div class="month"><?=$monthArr[ceil($month-1)]?></div>
									</div>
                                    <div class="post-meta">
                                        <h3><?=$courseDetailArr->course_title?></h3>
                                    </div>
									
									<div class="text">
									   <?=$courseDetailArr->course_description?>
									</div>

                                    <div class="text">
                                        <?=strip_tags(stripslashes($faq->description))?>
                                        <?php if($course_pdf !== null) { ?>
                                           <br><span style="color:blue;">Download the pdf for more details : <a href="<?=$course_pdf?>" download><i class="fa fa-download"></i>&nbsp;Download</a></span>
                                        <?php } ?>      
                                    </div>
									<!--Social Box-->
                                    <div class="row pl-0 pt-3">
    								   <div class="col-lg-12 col-md-12 col-sm-12">
                                         <span class="shareit">Share:</span>
    									  <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
                                            <a class="a2a_button_facebook"></a>
                                            <a class="a2a_button_twitter"></a>
                                            <a class="a2a_button_email"></a>
                                            <a class="a2a_button_linkedin"></a>
                                            <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                                          </div>
    								   </div>
                                    </div>   
							  </div>
						  </div>
					  </div>
                  </div>
                </div>
                <!--End Comments Area-->
					

                <!--Sidebar Side-->
                <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                    <aside class="sidebar sticky_sidebar">
                       
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
                                    <input type="email" class="form-control" name="user_email" id="user_email" value="" placeholder="Your email" required>
                                </div>

                                <div class="form-group">
                                    <input type="number" class="form-control" name="user_phone" value="" placeholder="Your Contact No" required>
                                </div>

                                <div class="form-group">
                                    <input type="text" class="form-control" name="user_city" id="user_city" value="" placeholder="Your City" required>
                                </div>

                                <div class="form-group">
                                     <select class="form-control course" name="course_id" id="course_id" data-placeholder="Choose a Course..." required>
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
                        
                    </aside>
                 </div>
			
			</div>
		</div>
	</div>
	
	