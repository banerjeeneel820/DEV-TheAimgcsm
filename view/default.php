<?php
 //Fetching blog array
 $headerSliderParamArr['slider_type'] = 'header';
 $headerSliderArr = fetchSliderArr($headerSliderParamArr);

 $footerSliderParamArr['slider_type'] = 'footer';
 $footerSliderArr = fetchSliderArr($footerSliderParamArr);

 $mediaParamArr['protocol'] = 'home_page';
 
 //Fetching course data
 $latestCourseArr = array_slice($courseArr,0,3);
 
 //Fetching franchise data
 $latestFranchiseArr = array_slice($franchiseArr,0,3);

 $countStudentRecords = countGlobalStudents();

 //Fetching course data
 $mediaDataArr = fetchGlobalGallery($mediaParamArr);

 //Fetch all gallery category for gallery
 $type = 'gallery';
 $mediaCategoryArr = fetchSingleParentCategory($type);

 /*print"<pre>";
 print_r($mediaCategoryArr);
 print"</pre>";*/
?>

    <style>
	   /*----SELECT 2 MODIFIED CSS ---*/
	  .select2-selection__rendered {
		    line-height: 50px !important;
		    border-radius: 55px;
		}
		.select2-container .select2-selection--single {
		    height: 50px !important;
		    border-radius: 50px;
		}
		.select2-selection__arrow {
		    height: 45px !important;
		}
		.select2-container--default .select2-selection--single .select2-selection__rendered {
		    line-height: 44px!important;
		}
		/*------END HERE -----*/
	</style>
	
	<!--Main Slider-->
    <section class="main-slider">
    	
        <div class="main-slider-carousel owl-carousel owl-theme">
          <?php 
             foreach($headerSliderArr as $index=>$slider){ 
                
                $banner_title = explode('-',$slider->banner_title);
             	$banner_text = explode('-',$slider->banner_text);
          ?>  
           
            <div class="slide" id="home-slider-1" style="background: linear-gradient( rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2) ), url(<?=USER_UPLOAD_URL.'home_sliders/'.$slider->banner_image?>);-webkit-background-size:cover;-moz-background-size:cover;-o-background-size: cover;background-size: cover;background-position: center;">
                <div class="auto-container">
                	<div class="content clearfix">
						<h2><?=$banner_title[0]?><br> <?=$banner_title[1]?></h2>
						<div class="text"><?=$banner_text[0]?><br> <?=$banner_text[1]?></div>
                    	<div class="link-box">
							<a href="<?=SITE_URL.$slider->banner_link?>" class="theme-btn btn-style-two">Read More</a>
						</div>
                    </div>
                </div>
            </div>

	     <?php } ?>		
        </div>
		
    </section>
    <!--End Main Slider-->
	
	<!-- Call To Action Section -->
	<section class="call-to-action-section">
		<div class="auto-container">
			<div class="clearfix">
				<div class="pull-left">
					<h2>Take a Fresh Start on Your Career Toady!</h2>
					<div class="text">Take our industry based courses to and take a fresh start on you career.</div>
				</div>
				<div class="pull-right">
					<a href="javascript:void(0);" data-target=".contact-us" class="theme-btn btn-style-two scroll-to-target">Contact Us</a>
				</div>
			</div>
		</div>
	</section>
	<!-- End Call To Action Section -->
	
	<!-- Welcome Section -->
	<section class="welcome-section">
		<div class="auto-container">
			<div class="row clearfix">
				
				<!-- Content Column -->
				<div class="content-column col-lg-6 col-md-12 col-sm-12">
					<div class="inner-column">
						<h2>Welcome To <span class="theme_color">THE</span> AIMGCSM</h2>
						<div class="bold-text">We help students in pursuit of academic excellence.</div>
						<div class="text">The AIMGCSM is a leading Educational institution based out of Habrah and Ashoknagar.The AIMGCSM has established itself as a high quality education provider with prime focus on holistic learning and imbibing competitive abilities in students.We are an <b>ISO 9001:2015 Certified Organization.</b><!--The AIMGCSM promises to become one of the leading institution with an acknowledged reputation for excellence in guide and teaching. With its outstanding faculty, induxtry based teaching standards, and innovative academic programmes, AIMGCSM intends to set a new benchmark in the All India Computer Literacy Mission.--></div>
						<a href="javascript:void(0);" class="theme-btn btn-style-three scroll-to-target" data-target=".contact-us">Join Us Now</a>
					</div>
				</div>
				
				<!-- Video Column -->
				<div class="video-column col-lg-6 col-md-12 col-sm-12">
					<div class="inner-column">
						
						<!--Video Box-->
                        <div class="video-box wow fadeInRight" data-wow-delay="0ms" data-wow-duration="1500ms">
                            <figure class="video-image">
                                <img src="<?=RESOURCE_URL?>edulearn_images/about/about2.jpg" alt="">
                            </figure>
                            <a href="https://www.youtube.com/watch?v=kxPCFljwJws" class="lightbox-image overlay-box"><span class="flaticon-play-button"><i class="ripple"></i></span></a>
                        </div>
						
					</div>
				</div>
				
			</div>
		</div>
	</section>
	<!-- End Welcome Section -->
	
	<!-- Counter Section -->
	<section class="counter-section pb-0" style="background-image:url(<?=RESOURCE_URL?>images/background/1.jpg)">
		<div class="auto-container">
		
			<!-- Fact Counter -->
			<div class="fact-counter">
				<div class="row clearfix">

					<!--Column-->
					<div class="column counter-column col-lg-3 col-md-6 col-sm-12">
						<div class="inner wow fadeInLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
							<div class="content">
								<div class="icon flaticon-lightbulb"></div>
								<div class="count-outer count-box">
									<span class="count-text" data-speed="2500" data-stop="<?=count($franchiseArr)?>">0</span>
								</div>
								<h4 class="counter-title">Franchise</h4>
							</div>
						</div>
					</div>

					<!--Column-->
					<div class="column counter-column col-lg-3 col-md-6 col-sm-12">
						<div class="inner wow fadeInLeft" data-wow-delay="300ms" data-wow-duration="1500ms">
							<div class="content">
								<div class="icon flaticon-startup-1"></div>
								<div class="count-outer count-box alternate">
									<span class="count-text" data-speed="2500" data-stop="<?=count($courseArr)?>">0</span>+
								</div>
								<h4 class="counter-title">Courses</h4>
							</div>
						</div>
					</div>

					<!--Column-->
					<div class="column counter-column col-lg-3 col-md-6 col-sm-12">
						<div class="inner wow fadeInLeft" data-wow-delay="600ms" data-wow-duration="1500ms">
							<div class="content">
								<div class="icon flaticon-process"></div>
								<div class="count-outer count-box">
									<span class="count-text" data-speed="2000" data-stop="50">0</span>+
								</div>
								<h4 class="counter-title">Insctructors</h4>
							</div>
						</div>
					</div>
					
					<!--Column-->
					<div class="column counter-column col-lg-3 col-md-6 col-sm-12">
						<div class="inner wow fadeInLeft" data-wow-delay="900ms" data-wow-duration="1500ms">
							<div class="content">
								<div class="icon flaticon-sketch"></div>
								<div class="count-outer count-box">
									<span class="count-text" data-speed="2500" data-stop="<?=$countStudentRecords?>">0</span>
								</div>
								<h4 class="counter-title">Satisfied Students</h4>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</section>
	<!-- End Counter Section -->
	
	<!-- Causes Section -->
	<section class="causes-section pb-0">
		<div class="auto-container">
			<!-- Sec Title -->
			<div class="sec-title">
				<div class="clearfix">
					<div class="pull-left">
						<h2><span class="theme_color">Our Recent</span> Courses</h2>
						<div class="text">Check out our latest and most popular courses to turn your carrier.</div>
					</div>
					<div class="pull-right">
						<a href="<?=SITE_URL?>course/" class="theme-btn btn-style-four">View All Courses</a>
					</div>
				</div>
			</div>
			
			<div class="row clearfix">
				
				<?php 
	               foreach($latestCourseArr as $key=> $course){

	               	$course_slug = seoUrl($course->course_title,'seo');

	                $course_excerpt = get_Gloabl_Content_Excerpt($course->course_description,120);
	                
                    $course_thumbnail = USER_UPLOAD_DIR.'course/'.$course->course_thumbnail;

                    if (!strlen($course->course_thumbnail)>0 || !file_exists($course_thumbnail)) {   
                      $course_thumbnail = ADMIN_RESOURCE_URL.'images/preview.jpg'; 
                    }else{
                      $course_thumbnail = USER_UPLOAD_URL.'course/'.$course->course_thumbnail;
                    }
	            ?>   	
					<!--Causes Block-->
	                <div class="causes-block col-lg-4 col-md-6 col-sm-12">
	                	<div class="inner-box wow fadeInUp" data-wow-delay="0ms" data-wow-duration="1500ms">
	                    	<div class="image">
	                            <?php if($course->record_status == 'active'){ ?>
								 <div class="ribbon">POPULAR</div>
	                            <?php } ?>  
	                        	<a href="<?=SITE_URL.'course-detail/'.$course_slug?>"><img src="<?=$course_thumbnail?>" alt="<?=$course->course_title?>" style="height:270px;width:370px;"/></a>
								<a href="<?=SITE_URL.'course'?>" class="like-icon"></a>
	                        </div>
	                        <div class="lower-content">
	                        	<h3><a href="<?=SITE_URL.'course-detail/'.$course_slug?>"><?=$course->course_title?></a></h3>
	                            <div class="content">
	                            	<div class="text"><?=$course_excerpt?><a href="<?=SITE_URL.'course'?>">Read More</a></div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
				<?php } ?>
			</div>
		</div>
	</section>
	<!-- End Causes Section -->
	
	<!-- Contact Us Form Section -->
	<section class="donate-form-section" style="background-image:url(<?=RESOURCE_URL?>edulearn_images/bg/testimonial-bg.jpg)">
		<div class="auto-container">
			<div class="row clearfix">
				
				<!-- Image Column -->
				<div class="image-column col-lg-6 col-md-6 col-sm-12" style="margin-top:200px;">
					<div class="inner-column wow slideInLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
						<div class="image">
							<img src="<?=RESOURCE_URL?>edulearn_images/choose-us/1.png" alt="contact-us" />
						</div>
					</div>
				</div>
				
				<!-- Form Column -->
				<div class="form-column col-lg-6 col-md-6 col-sm-12 contact-us">
					<div class="inner-column">
						<!-- Sec Title -->
						<div class="sec-title light centered">
							<h2><span class="theme_color">Contact </span> Us</h2>
							<div class="text" style="color:#ffff;">We are always looking out and timely help disadvantaged, <br> See our latest campaign and if can you pledonaa</div>
						</div>
						
						<!-- Default Form -->
						<div class="default-form style-two">
						<form id="user_enquiry_form" method="post" onsubmit="return false;" id="contact-form">
						   <input type="hidden" name="action" value="createGlobalEnquiry">
						   <input type="hidden" name="course_name" id="course_name" value="">
							<div class="row clearfix">
							
								<!-- Column -->
								<div class="column col-lg-6 col-md-12 col-sm-12">
								
									<!-- Form Group -->
									<div class="form-group">
										<input type="text" name="user_name" value="" placeholder="Your name" required>
									</div>
									<!-- Form Group -->
									<div class="form-group">
										<input type="email" name="user_email" id="user_email" value="" placeholder="Your Email Address" required>
									</div>
							    </div>
		                        
		                        <div class="column col-lg-6 col-md-12 col-sm-12">
							       <!-- Form Group -->
									<div class="form-group">
										<input type="number" name="user_phone" value="" placeholder="Your Contact No" required>
									</div>

									<div class="form-group">
										<input type="text" name="user_city" id="user_city" value="" placeholder="Your City" required>
									</div>	
							    </div>
							    
							    <div class="column col-lg-6 col-md-12 col-sm-12">			

									<div class="form-group">
		                                 <select name="enquiry_type" id="enquiry_type" name="enquiry_type" style="border-radius:50px;height:50px;" required>
		                                   <option selected disabled>Choose a enquiry type</option>
		                                   <option value="course">Course</option>
		                                   <option value="others">Others</option>
		                                </select>
		                            </div>
		                         </div>  

		                         <div class="column col-lg-6 col-md-12 col-sm-12"> 

		                            <div class="form-group d-none" id="subject_div">
										<input type="text" class="form-control" name="subject" id="subject" value="" placeholder="Enter a subject">
									</div>

		                             <div class="form-group d-none" id="course_div">
		                                 <select name="course_id" id="course_id" style="border-radius:50px;height:50px;">
		                                   <option selected disabled>Choose a Course...</option>
		                                   <?php foreach($courseArr as $course){ 
		                                   ?>
		                                    <option value="<?=$course->id?>" id="course_<?=$course->id?>"><?=$course->course_title?></option>
		                                   <?php } ?>
		                                </select>
		                            </div>
								</div>

								<div class="column col-lg-3 col-md-3 col-sm-12"> </div>
		                        <div class="column col-lg-4 col-md-4 col-sm-12 mb-4"> 
		                             <div class="g-recaptcha" id="form_recaptcha_div"></div>
		                        </div>
		                        <div class="column col-lg-5 col-md-5 col-sm-12"> </div>
								
								<!-- Column -->
								<div class="column col-lg-12 col-md-12 col-sm-12">
									<!-- Form Group -->
									<div class="form-group">
										<textarea name="user_message" class="form-control" style="height:200px;resize:vertical;" placeholder="Enter your message here..."></textarea>
									</div>
									
								</div>
								
							</div>
							
							<div class="form-group text-center col-lg-12 col-md-12 col-sm-12">
								<button type="submit" id="contact_submit" class="theme-btn btn-style-three"><span class="txt">SEND NOW</span></button>
							</div>                                     
						</form>
						</div>
						<!--End Default Form-->
						
					</div>
				</div>
				
			</div>
		</div>
	</section>
	<!-- End Contact Form Section -->
	
	<!-- Goal Section -->
	<section class="goal-section" style="background-image:url(<?=RESOURCE_URL?>images/background/2.jpg)">
		<div class="auto-container">
			<!-- Sec Title -->
			<div class="sec-title centered">
				<h2><span class="theme_color">Mission & </span> Goals</h2>
				<div class="text">Many children and poor people are at high risk <br> of severe malnutrition</div>
			</div>
			<div class="row clearfix">

				<!-- Goal Block -->
				<div class="goal-block col-lg-4 col-md-6 col-sm-12">
					<div class="inner-box wow fadeInUp" data-wow-delay="0ms" data-wow-duration="1500ms">
						<div class="hover-one"></div>
						<div class="hover-two"></div>
						<div class="icon-box">
							<span class="icon flaticon-donation-1"></span>
						</div>
						<h3><a href="causes-single.html">Transformative Educational Experiecnce</a></h3>
						<div class="text">Developing deep disciplinary knowledge, problem solving ability, leadership, communication and interpersonal skills.</div>
					</div>
				</div>
				
				<!-- Goal Block -->
				<div class="goal-block col-lg-4 col-md-6 col-sm-12">
					<div class="inner-box wow fadeInUp" data-wow-delay="300ms" data-wow-duration="1500ms">
						<div class="hover-one"></div>
						<div class="hover-two"></div>
						<div class="icon-box">
							<span class="icon flaticon-donation-2"></span>
						</div>
						<h3><a href="causes-single.html">Global Outlook</a></h3>
						<div class="text">To encourage pursuit of knowledge and support the development of curricula from global perspective also enable students to explore and acquire leadership qualities.</div>
					</div>
				</div>
				
				<!-- Goal Block -->
				<div class="goal-block col-lg-4 col-md-6 col-sm-12">
					<div class="inner-box wow fadeInUp" data-wow-delay="600ms" data-wow-duration="1500ms">
						<div class="hover-one"></div>
						<div class="hover-two"></div>
						<div class="icon-box">
							<span class="icon flaticon-house-1"></span>
						</div>
						<h3><a href="causes-single.html">Seeking beyond Boundaries</a></h3>
						<div class="text">To invest in faculty development to upskill them in designing and developing curricula, pursuing experiential learning.We offer courses based on latest technology on industry.</div>
					</div>
				</div>
			
			</div>
		</div>
	</section>	

	<!-- Call To Action Section -->
    <section class="call-to-action-section-two" style="background-image: url(<?=RESOURCE_URL?>edulearn_images/slider/home3/slide1.jpg)">
    	<div class="auto-container">
			<h2>Have some questions? Read our News</h2>
			<div class="text">Our in depth News section will answer most of your questions.But if you can't find what you are looking for you can also ask us any question in there.Head to our news section now.</div>
			<a href="<?=SITE_URL?>news" class="theme-btn btn-style-five">View News</a>
        </div>
	</section>		
	<!-- Call To Action Section -->		
	
	<!-- Causes Section -->
	<section class="causes-section pb-0">
		<div class="auto-container">
			<!-- Sec Title -->
			<div class="sec-title">
				<div class="clearfix">
					<div class="pull-left">
						<h2><span class="theme_color">Our</span> Branches</h2>
						<div class="text">Our most visited and popular franchise. Is any one of them is near to you?</div>
					</div>
					<div class="pull-right">
						<a href="<?=SITE_URL?>franchise/" class="theme-btn btn-style-four">View All Franchise</a>
					</div>
				</div>
			</div>
			
			<div class="row clearfix">
				
				<?php 
	               foreach($latestFranchiseArr as $key=> $franchise){
	               	
	               	$center_slug = seoUrl($franchise->center_name,'seo');

	                $franchise_excerpt = get_Gloabl_Content_Excerpt($franchise->fran_description,120);
	                
                    $franchise_thumbnail = USER_UPLOAD_DIR.'franchise/'.$franchise->fran_image;

                    if (!strlen($franchise->fran_image)>0 || !file_exists($franchise_thumbnail)) {   
                      $franchise_thumbnail = ADMIN_RESOURCE_URL.'images/preview.jpg'; 
                    }else{
                      $franchise_thumbnail = USER_UPLOAD_URL.'franchise/'.$franchise->fran_image;
                    }
	            ?>   	
					<!--Causes Block-->
	                <div class="causes-block col-lg-4 col-md-6 col-sm-12">
	                	<div class="inner-box wow fadeInUp" data-wow-delay="0ms" data-wow-duration="1500ms">
	                    	<div class="image">
	                            <?php if($franchise->record_status == 'active'){ ?>
								 <div class="ribbon">Most Visited</div>
	                            <?php } ?>  
	                        	<a href="<?=SITE_URL.'franchise-detail/'.$center_slug?>"><img src="<?=$franchise_thumbnail?>" alt="<?=$franchise->center_name?>" style="height:270px;width:370px;"/></a>
								<a href="<?=SITE_URL.'franchise-detail/'.$center_slug?>" class="like-icon"></a>
	                        </div>
	                        <div class="lower-content">
	                        	<h3><a href="<?=SITE_URL.'franchise-detail/'.$center_slug?>"><?=substr($franchise->center_name,0,23)?></a></h3>
	                            <div class="content">
	                            	<div class="text"><?=$franchise_excerpt?><a href="<?=SITE_URL.'franchise'?>">Read More</a></div>
	                            </div>
	                        </div>
	                    </div>
	                </div>
				<?php } ?>
			</div>
		</div>
	</section>
	<!-- End Causes Section -->

	<!--Clients Section
    <section class="clients-section">
        <div class="outer-container">
            
            <div class="sponsors-outer">
                <ul class="sponsors-carousel owl-carousel owl-theme">
                 <?php foreach($footerSliderArr as $index => $slider){ ?>
                    <li class="slide-item"><figure class="image-box"><a href="<?=$slider->banner_link?>"><img src="<?=USER_UPLOAD_URL.'home_sliders/'.$slider->banner_image?>" alt="<?=$slider->banner_link?>" style="width: 250px;height: 100px;"></a></figure></li>
                 <?php } ?>  
                </ul>
            </div>
        </div>
    </section>
    <!--End Clients Section-->
	