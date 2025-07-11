<?php
    $noticeArr = fetchGlobalFaq('notice','main_page');  
	/*print"<pre>";
	print_r($noticeArr);
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
		/*------END HERE -----*/
	</style>
	
	<!--Page Title-->
    <section class="page-title" style="background-image:url(<?=RESOURCE_URL?>images/background/12.jpg);">
    	<div class="auto-container">
        	<div class="row clearfix">
            	<!--Title -->
            	<div class="title-column col-lg-6 col-md-12 col-sm-12">
                	<h1>Important Notices</h1>
                </div>
                <!--Bread Crumb -->
                <div class="breadcrumb-column col-lg-6 col-md-12 col-sm-12">
                    <ul class="bread-crumb clearfix">
                        <li><a href="<?=SITE_URL?>"><span class="icon fas fa-home"></span> Home</a></li>
                        <li class="active"><span class="icon fas fa-arrow-alt-circle-right"></span> Faq</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->

	<!-- Volunter Section -->
	<section class="faq-page-section">
		<div class="auto-container">
			<!-- Title Box -->
			<div class="title-box">
				<div class="text">Below are some frequently asked questions and answers about our products. If you need specific <br> help or your question isn’t answered here, you should head to our forums. You can also read <br> our <a href="#">Terms of Service</a> and <a href="#">Support Policy</a> here.</div>
			</div>
			
			<!--Accordian Box-->
			<ul class="accordion-box">
             <?php 
                foreach($noticeArr as $key => $notice){ 
                if($notice->file_upload_type == "local"){
                    $optional_pdf_path = USER_UPLOAD_DIR.'faq/'.$notice->optional_pdf;

                    if(strlen($notice->optional_pdf)>0 && file_exists($optional_pdf_path)) {   
                      $optional_pdf = USER_UPLOAD_URL.'faq/'.$notice->optional_pdf;
                    }else{
                      $optional_pdf = null;	
                    }

                }else{
                   $optional_pdf = $notice->optional_pdf;
                }
             ?>
				<!--Block-->
				<li class="accordion block">
					<div class="acc-btn"><div class="icon-outer"><span class="icon icon-plus fa fa-plus"></span> <span class="icon icon-minus fa fa-minus"></span></div><?=$notice->title?></div>
					<div class="acc-content">
						<div class="content">
							<div class="text">
								<?=strip_tags(stripslashes($notice->description))?>
								<?php if($optional_pdf !== null) { ?>
								   <br><span style="color:blue;">Download the pdf for more details : <a href="<?=$optional_pdf?>" download>Download</a></span>
							    <?php } ?>	 
							</div>
						</div>
					</div>
				</li>
             <?php } ?>
			</ul>
		</div>
	</section>
	<!-- End Volunter Section -->
	
	<!-- Faq Form Section -->
	<section class="faq-form-section">
		<div class="auto-container">
			<!-- Sec Title -->
			<div class="sec-title centered">
				<h2><span class="theme_color">Tell us any </span> Question</h2>
			</div>
			
			<!-- Faq Form -->
			<div class="default-form style-two">
				<!--Faq Form-->
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
								<input type="text" name="user_city" value="" placeholder="Your City" required>
							</div>	
					    </div>
					    
					    <div class="column col-lg-6 col-md-12 col-sm-12">			

							<div class="form-group">
                                 <select name="enquiry_type" id="enquiry_type" name="enquiry_type" data-placeholder="Choose a Course..." style="border-radius:50px;height:50px;" tabindex="2">
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
                                 <select class="form-control course" name="course_id" id="course_id" data-placeholder="Choose a Course..." style="border-radius:50px;" tabindex="2">
                                   <option selected disabled>Choose a Course...</option>
                                   <?php foreach($courseArr as $course){ 
                                   ?>
                                    <option value="<?=$course->id?>" id="course_<?=$course->id?>"><?=$course->course_title?></option>
                                   <?php } ?>
                                </select>
                            </div>
						
						</div>

                        <div class="column col-lg-4 col-md-4 col-sm-12"> </div>
                        <div class="column col-lg-4 col-md-4 col-sm-12 mb-4"> 
                             <div class="g-recaptcha" id="form_recaptcha_div"></div>
                        </div>
                        <div class="column col-lg-4 col-md-4 col-sm-12"> </div>
						
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
			<!--End Faq Form --> 
			
		</div>
	</section>
	<!-- End Faq Form Section -->
			