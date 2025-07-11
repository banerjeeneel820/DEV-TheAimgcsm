<?php
  //Fetching faq data
  $latestNewsArr = array_slice($newsArr,0,5);

  $countStudentRecords = countGlobalStudents();
?>

    <style>
		hr {
		  border: 0;
		  display:block;
		  width: 100%;               
		  background-color:#eeeeee;
		  height: 1px;
		}
	</style>
	
	<!--Page Title-->
    <section class="page-title" style="background-image:url(<?=RESOURCE_URL?>images/background/12.jpg);">
    	<div class="auto-container">
        	<div class="row clearfix">
            	<!--Title -->
            	<div class="title-column col-lg-6 col-md-12 col-sm-12">
                	<h1>About Us</h1>
                </div>
                <!--Bread Crumb -->
                <div class="breadcrumb-column col-lg-6 col-md-12 col-sm-12">
                    <ul class="bread-crumb clearfix">
                        <li><a href="<?=SITE_URL?>"><span class="icon fas fa-home"></span> Home</a></li>
                        <li class="active"><span class="icon fas fa-arrow-alt-circle-right"></span> About Us</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->
	
	<!-- Welcome Section -->
	<section class="welcome-section" id="page_content">
		<div class="auto-container">
			<div class="row clearfix">
				
				<!-- Content Column -->
				<div class="content-column col-lg-6 col-md-12 col-sm-12">
					<div class="inner-column">
						<h2>Welcome To <span class="theme_color">THE</span> AIMGCSM</h2>
						<div class="bold-text">We help students in pursuit of academic excellence.</div>
						<div class="text">The AIMGCSM is a leading Educational institution based out of Habrah and Ashoknagar.We are an Autonomous Body Regd. Under <b>Govt. Of WB REG NO. IV-5203</b> based On <b>TR Act.1882</b>, Govt. Of India. Inspired By <b>National Task Force on IT & SD</b>, Govt. Of India. Empanelled Under: <b>NPS- NITI AAYOG</b> Formally known- Planning ommission , Govt. Of India.Deptt. Of Labour, NCT, Delhi, Govt. Of India .Regd. Under <b>Ministry Of Small & Medium Enterprise - MSME</b> , Govt. Of India .</div>
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
	
	<!-- Services Section -->
	<section class="services-section-two style-two">
		<div class="auto-container">
			<!-- Sec Title -->
			<div class="sec-title centered">
				<h2><span class="theme_color">Mission & </span> Goals</h2>
				<div class="text">Many children and poor people are at high risk <br> of severe malnutrition</div>
			</div>
			<div class="row clearfix">
				
				<!-- Service Block Two -->
				<div class="service-block-two col-lg-6 col-md-12 col-sm-12">
					<div class="inner-box wow fadeInLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
						<div class="icon-box">
							<span class="icon flaticon-money-bag"></span>
						</div>
						<h3><a href="<?=SITE_URL?>?route=causes-single">Value for Money</a></h3>
						<div class="text">We offer courses at low and minimal fees compare than other institutions wihtout compromising our educational quality.</div>
					</div>
				</div>
				
				<!-- Service Block Two -->
				<div class="service-block-two col-lg-6 col-md-12 col-sm-12">
					<div class="inner-box wow fadeInRight" data-wow-delay="0ms" data-wow-duration="1500ms">
						<div class="icon-box">
							<span class="icon flaticon-donation-2"></span>
						</div>
						<h3><a href="<?=SITE_URL?>?route=causes-single">Transformative Education</a></h3>
						<div class="text">Ensuring student participation in career enhancement activities through exchange programs, student enterprise, countless training and work-based learning.</div>
					</div>
				</div>
				
				<!-- Service Block Two -->
				<div class="service-block-two col-lg-6 col-md-12 col-sm-12">
					<div class="inner-box wow fadeInLeft" data-wow-delay="300ms" data-wow-duration="1500ms">
						<div class="icon-box">
							<span class="icon flaticon-house-1"></span>
						</div>
						<h3><a href="<?=SITE_URL?>?route=causes-single">Seeking beyond Boundaries</a></h3>
						<div class="text">To invest in faculty development to upskill them in designing and developing curricula, pursuing experiential learning.We offer courses based on latest technology on industry.</div>
					</div>
				</div>
				
				<!-- Service Block Two -->
				<div class="service-block-two col-lg-6 col-md-12 col-sm-12">
					<div class="inner-box wow fadeInRight" data-wow-delay="300ms" data-wow-duration="1500ms">
						<div class="icon-box">
							<span class="icon flaticon-world"></span>
						</div>
						<h3><a href="<?=SITE_URL?>?route=causes-single">Global Outlook</a></h3>
						<div class="text">To encourage pursuit of knowledge and support the development of curricula from global perspective also enable students to explore and acquire leadership qualities.</div>
					</div>
				</div>
                
				<div class="row">
				      <div class="col-md-2 text-center">
				      	 <img src="<?=RESOURCE_URL?>images/resource/146.gif"class="img-responsive" style="float:left;margin:10px;" height="250" >
				      </div>
				      <div class="col-md-10">
				        <p>The All India Mahatma Gandhi Computer Sakshatra Mission having the An Autonomous Body Regd. Under Govt. Of WB REG NO. IV-5203  based On TR Act.1882, Govt. Of India, empanelled Under: NPS- NITI AAYOG, Govt. Of India.Deptt. Of Labour, NCT, Delhi, Govt. Of India Regd. Under MHRD - CR, Govt. Of India Working in a different Programme & Commercial Training Organization is also certified ISO 9001:2015 Org.</p>
				        <p>After Years of successful conducting of various commercial training programs, our Organization decided to work in Information & Technology for all, according to call of the Indian Government. For fulfilling the development dream of Indian Government there will be requirement of 25 lac I.T. and more than 15 Lac other ancillary requirement of computer operator/specialist.</p>
				      </div>
				</div>  
				<hr>	
			    <div class="row">
			       <div class="col-md-2 text-center">
			    	  <img src="<?=RESOURCE_URL?>images/resource/onlineadd.jpg"class="img-responsive" style="float:left;margin:10px;" height="250" ><br>
			       </div>
			      <div class="col-md-10">
			        <p>Our mission is to provide technical, non-technical and higher education at very affordable charges to every group of society in Urban & Rural areas of Indian.</p>
			        <p>In present time, some big Institution run their one-year or more than one-year programme in higher charges, Due to which the middle class families of our societies cannot afford the load of their charges. Our mission studied them properly and decided to provide better higher technical education in computer, making a found at "The All Indian Mahatma Gandhi Computer Saksharta Mission" all over India. </p>
			      </div>
			    </div>
			</div>
		</div>
	</section>
	
	<!-- Call To Action Section -->
	
	<!-- Contact Us Form Section -->
	<section class="donate-form-section" style="background-image:url(<?=RESOURCE_URL?>edulearn_images/bg/testimonial-bg.jpg)">
		<div class="auto-container">
			<div class="row clearfix">
				
				<!-- Image Column -->
				<div class="image-column col-lg-6 col-md-12 col-sm-12" style="margin-top:200px;">
					<div class="inner-column wow slideInLeft" data-wow-delay="0ms" data-wow-duration="1500ms">
						<div class="image">
							<!--<img src="<?=RESOURCE_URL?>edulearn_images/choose-us/1.png" alt="contact-us" />-->
						</div>
					</div>
				</div>
				
				<!-- Form Column -->
				<div class="form-column col-lg-6 col-md-12 col-sm-12 contact-us">
					<div class="inner-column">
						<!-- Sec Title -->
						<div class="sec-title light centered">
							<h2><span class="theme_color">Contact </span> Us</h2>
							<div class="text">We are always looking out and timely help disadvantaged, <br> See our latest campaign and if can you pledonaa</div>
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
		                                 <select name="course_id" id="course_id" style="border-radius:50px;height:50px;" tabindex="2">
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
	<!-- End Donate Form Section -->
	
	<!-- Faq Section -->
	<section class="faq-section style-two">
		<div class="auto-container">
			<div class="row clearfix">
				
				<!-- Accordian Column -->
				<div class="accordian-column col-lg-6 col-md-12 col-sm-12">
					<div class="inner-column">
						<!-- Sec Title -->
						<div class="sec-title">
							<h2><span class="theme_color">some</span> news</h2>
						</div>
						
						<!--Accordian Box-->
						<ul class="accordion-box">
                           <?php foreach($latestNewsArr as $key => $news){ ?>
								<!--Block-->
								<li class="accordion block">
									<div class="acc-btn"><div class="icon-outer"><span class="icon icon-plus fa fa-plus"></span> <span class="icon icon-minus fa fa-minus"></span></div><?=$news->title?></div>
									<div class="acc-content">
										<div class="content">
											<div class="text"><?=strip_tags(stripslashes($news->description))?></div>
										</div>
									</div>
								</li>
                            <?php } ?>
                             <li><a href="<?=SITE_URL?>faq" class="theme-btn btn-style-three">Read More News</a></li>
						</ul>
					</div>
				</div>
				
				<!-- Image Column -->
				<div class="image-column col-lg-6 col-md-12 col-sm-12">
					<div class="inner-column wow zoomIn" data-wow-delay="0ms" data-wow-duration="1500ms">
						<div class="image">
							<img src="<?=RESOURCE_URL?>edulearn_images/about/about.jpg" alt="" />
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</section>
	<!-- End Faq Section -->

	
  