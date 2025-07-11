<?php
  //Fetching course data
  $courseparamArr['protocol'] = 'home_page';
  $courseArr = fetchGlobalCourse($courseparamArr); 
?>
	
	<!--Page Title-->
    <section class="page-title" style="background-image:url(<?=RESOURCE_URL?>images/background/12.jpg);">
    	<div class="auto-container">
        	<div class="row clearfix">
            	<!--Title -->
            	<div class="title-column col-lg-6 col-md-12 col-sm-12">
                	<h1>Contact Us</h1>
                </div>
                <!--Bread Crumb -->
                <div class="breadcrumb-column col-lg-6 col-md-12 col-sm-12">
                    <ul class="bread-crumb clearfix">
                        <li><a href="<?=SITE_URL?>"><span class="icon fas fa-home"></span> Home</a></li>
                        <li class="active"><span class="icon fas fa-arrow-alt-circle-right"></span> Contact</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->
	
	<!-- Map Section 
	<section class="map-section">
		<div class="auto-container">
			<div class="map-outer">
				<div class="map-canvas"
					data-zoom="12"
					data-lat="-37.817085"
					data-lng="144.955631"
					data-type="roadmap"
					data-hue="#ffc400"
					data-title="Envato"
					data-icon-path="images/icons/map-marker.png"
					data-content="Melbourne VIC 3000, Australia<br><a href='mailto:info@youremail.com'>info@youremail.com</a>">
				</div>
			</div>
		</div>
	</section>
	<!-- End Map Section -->
	
	<!-- Contact Section -->
	<section class="contact-section" style="background-image: url(<?=RESOURCE_URL?>images/background/map-pattern-1.png)">
		<div class="auto-container">
			<!-- Sec Title -->
			<div class="sec-title centered">
				<h2><span class="theme_color">Contact </span> Us</h2>
				<div class="text">Thank you very much for your interest in our company and our services </div>
			</div>
			<div class="row clearfix">
			
				<!-- Column -->
				<div class="info-column col-lg-4 col-md-6 col-sm-12" id="page_content">
					<div class="inner-column">
						<div class="icon-box">
							<span class="icon flaticon-location"></span>
						</div>
						<h3>Address:</h3>
						<div class="text">North, 24 Pargana, Kolkata</div>
					</div>
				</div>
				
				<!-- Column -->
				<div class="info-column col-lg-4 col-md-6 col-sm-12">
					<div class="inner-column">
						<div class="icon-box">
							<span class="icon flaticon-call"></span>
						</div>
						<h3>Phone:</h3>
						<div class="text"><?=strtolower($siteSettingArr->phone)?></div>
					</div>
				</div>
				
				<!-- Column -->
				<div class="info-column col-lg-4 col-md-6 col-sm-12">
					<div class="inner-column">
						<div class="icon-box">
							<span class="icon flaticon-email-1"></span>
						</div>
						<h3>Email:</h3>
						<div class="text">I<?=strtolower($siteSettingArr->contact_email)?></div>
					</div>
				</div>
				
			</div>
		
			<!-- Default Form -->
			<div class="default-form contact-form">
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
								<input type="email" name="user_email" id="user_email" value="" placeholder="Your Email Address">
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
                                 <select name="enquiry_type" id="enquiry_type" name="enquiry_type" tabindex="2" required>
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
                                 <select name="course_id" id="course_id" tabindex="2">
                                   <option selected disabled>Choose a Course...</option>
                                   <?php foreach($courseArr as $course){ 
                                   ?>
                                    <option value="<?=$course->id?>" id="course_<?=$course->id?>"><?=$course->course_title?></option>
                                   <?php } ?>
                                </select>
                            </div>
						</div>
                        
                        <div class="column col-lg-4 col-md-4 col-sm-12"> </div>
                        <div class="column col-lg-6 col-md-6 col-sm-12 mb-4"> 
                             <div class="g-recaptcha" id="form_recaptcha_div"></div>
                        </div>
                        <div class="column col-lg-2 col-md-2 col-sm-12"> </div>

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
	</section>
	<!-- End Contact Section -->
	
	<!--Clients Section-->
    <section class="clients-section">
        <div class="outer-container">
            
            <div class="sponsors-outer">
                <!--Sponsors Carousel-->
                <ul class="sponsors-carousel owl-carousel owl-theme">
                    <li class="slide-item"><figure class="image-box"><a href="#"><img src="<?=RESOURCE_URL?>images/clients/1.png" alt=""></a></figure></li>
                    <li class="slide-item"><figure class="image-box"><a href="#"><img src="<?=RESOURCE_URL?>images/clients/2.png" alt=""></a></figure></li>
                    <li class="slide-item"><figure class="image-box"><a href="#"><img src="<?=RESOURCE_URL?>images/clients/3.png" alt=""></a></figure></li>
                    <li class="slide-item"><figure class="image-box"><a href="#"><img src="<?=RESOURCE_URL?>images/clients/4.png" alt=""></a></figure></li>
                    <li class="slide-item"><figure class="image-box"><a href="#"><img src="<?=RESOURCE_URL?>images/clients/5.png" alt=""></a></figure></li>
                    <li class="slide-item"><figure class="image-box"><a href="#"><img src="<?=RESOURCE_URL?>images/clients/1.png" alt=""></a></figure></li>
					<li class="slide-item"><figure class="image-box"><a href="#"><img src="<?=RESOURCE_URL?>images/clients/2.png" alt=""></a></figure></li>
                </ul>
            </div>
            
        </div>
    </section>
    <!--End Clients Section-->
   