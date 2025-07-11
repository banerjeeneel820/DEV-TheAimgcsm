
   <!--Contact Form
   <div class="nb-form">
		<p class="title"><i class="fa fa-envelope"></i> Send Us a Message</p>
	    <img src="<?=RESOURCE_URL.'images/close.png'?>" id="minimize_contact_form" alt="close-contact-form" class="user-icon d-none">
	    <p class="message">Please leave your query by fill up the form below.</p>

	    <form method="post" id="user_enquiry_form" onsubmit="return false;">
	        <input type="hidden" name="action" id="action" value="createGlobalEnquiry">
	        <input type="hidden" name="enquiry_type" id="enquiry_type" value="course">
	        <input type="hidden" name="course_name" id="course_name" value="">
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

	        <div class="form-group mb-0">
	             <select class="form-control" name="course_id" id="course_id" tabindex="2" required>
	               <option selected disabled>Choose a Course</option>
	               <?php foreach($courseArr as $course){ 
	               ?>
	                <option value="<?=$course->id?>" id="course_<?=$course->id?>"><?=$course->course_title?></option>
	               <?php } ?>
	            </select>
	        </div>

	         <div class="form-group">
	            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0">
	              <div class="g-recaptcha" id="form_recaptcha_div"></div>
	            </div>
	        </div>

	        <div class="form-group">
	            <textarea name="user_message" class="form-control" style="height:80px;" placeholder="Add a quick note..."></textarea>
	        </div>
	        
	        <div class="form-group">
	            <input type="submit" id="contact_submit" value="Submit">
	        </div>    
	    </form>  
   </div>
   <!--End Here-->

    <!--Main Footer-->
    <footer class="main-footer" style="background-image:url(<?=RESOURCE_URL?>edulearn_images/bg/testimonial-bg.jpg)">
		<div class="auto-container">
        	<!--Widgets Section-->
            <div class="widgets-section">
            	<div class="row clearfix">
                	
                    <!--Footer Column-->
                    <div class="footer-column col-lg-4 col-md-6 col-sm-12">
						<div class="footer-widget logo-widget">
                        	<div class="logo">
                            	<a href="<?=SITE_URL?>"><img src="<?=SITE_URL.'uploads/others/'.$siteSettingArr->footer_logo?>" alt="The AIMGCSM" style="width: 230px;height: 90px;" /></a>
                            </div>
							<div class="text"><?=strip_tags(stripslashes($siteSettingArr->description))?></div>
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
					
					<!--Footer Column-->
                    <div class="footer-column col-lg-4 col-md-6 col-sm-12">
                    	<div class="footer-widget links-widget">
							<h2>Our Courses</h2>
							<ul class="footer-list">
								<?php foreach($footerCourseData as $index => $course){ ?>
									<li><a href="<?=SITE_URL?>course"><?=$course->course_title?></a></li>
								<?php } ?>	
								<li><a href="<?=SITE_URL?>course">Show More</a></li>
							</ul>
						</div>
                    </div>
					
					<!--Footer Column-->
                    <div class="footer-column col-lg-4 col-md-6 col-sm-12">
						<div class="footer-widget info-widget">
                        	<h2>Contact Us</h2>
							<ul class="list-style-one">
								<li><span class="icon fas fa-map-marker-alt"></span><?=$siteSettingArr->address?></li>
								<li><span class="icon fas fa-phone"></span>Support: <a href="tel:+034-256-7850">+91&nbsp;<?=$siteSettingArr->phone?></a></li>
								<li><span class="icon fas fa-envelope-open"></span>Email: <a href="mailto:info@example.com"><?=$siteSettingArr->contact_email?></a></li>
							</ul>
							<!--Emailed Form-->
						</div>
					</div>
					
				</div>
			</div>

			
			<!-- Footer Bottom -->
			<div class="footer-bottom">
				<div class="clearfix">
					<div class="pull-left">
						<div class="copyright">Copyrights © 2019 <a href="<?=SITE_URL?>">THE AIMGCSM.</a> All rights reserved.</div>
					</div>
					<div class="pull-right">
						<ul class="footer-nav">
							<li><a href="<?=SITE_URL.'about-us'?>">About Us</a></li>
							<li><a href="#">Privacy Policy</a></li>
							<li><a href="#">Sitemap</a></li>
							<li><a href="#">Terms of Use</a></li>
						</ul>
					</div>
				</div>
			</div>
			
	   </div>
</footer>
	
</div>
<!--End pagewrapper-->

<!--Scroll to top-->
<div class="scroll-to-top scroll-to-target" data-target="html"><span class="fa fa-arrow-up"></span></div>

<!-- Color Palate / Color Switcher -->

<!--<div class="color-palate">
    <div class="color-trigger">
        <i class="fa fa-paint-brush"></i>
    </div>
    <div class="color-palate-head">
        <h6>Choose Your Color</h6>
    </div>
    <div class="various-color clearfix">
        <div class="colors-list">
            <span class="palate default-color active" data-theme-file="<?=RESOURCE_URL?>css/color-themes/default-theme.css"></span>
            <span class="palate green-color" data-theme-file="<?=RESOURCE_URL?>css/color-themes/green-theme.css"></span>
            <span class="palate blue-color" data-theme-file="<?=RESOURCE_URL?>css/color-themes/blue-theme.css"></span>
            <span class="palate orange-color" data-theme-file="<?=RESOURCE_URL?>css/color-themes/orange-theme.css"></span>
            <span class="palate purple-color" data-theme-file="<?=RESOURCE_URL?>css/color-themes/purple-theme.css"></span>
            <span class="palate teal-color" data-theme-file="<?=RESOURCE_URL?>css/color-themes/teal-theme.css"></span>
            <span class="palate brown-color" data-theme-file="<?=RESOURCE_URL?>css/color-themes/brown-theme.css"></span>
            <span class="palate yellow-color" data-theme-file="<?=RESOURCE_URL?>css/color-themes/yellow-color.css"></span>
        </div>
    </div>
	
	<ul class="box-version option-box"> <li class="box">Boxed</li> <li>Full width</li></ul>
	<ul class="rtl-version option-box"> <li class="rtl">RTL Version</li> <li>LTR Version</li> </ul>
	
    <!--<a href="#" class="purchase-btn">Purchase now $17</a>
    
    <div class="palate-foo">
        <span>You can change  colors and layout of the theme of here.</span>
    </div>

</div>-->

<!-- sidebar cart item -->
<div class="xs-sidebar-group info-group">
	<div class="xs-overlay xs-bg-black"></div>
	<div class="xs-sidebar-widget">
		<div class="sidebar-widget-container">
			<div class="widget-heading">
				<a href="#" class="close-side-widget">
					X
				</a>
			</div>
			<div class="sidebar-textwidget">
				
				<!-- Sidebar Info Content -->
            <div class="sidebar-info-contents">
				<div class="content-inner">
					<div class="logo">
						<a href="<?=SITE_URL?>"><img src="<?=SITE_URL.'uploads/others/'.$siteSettingArr->footer_logo?>" alt="The AIMGCSM" style="width: 230px;height: 90px;" /></a>
					</div>
					<div class="content-box">
						<h2>About Us</h2>
						<p class="text"><?=strip_tags(stripslashes($siteSettingArr->description))?></p>
						<a href="<?=SITE_URL?>contact-us" target="_blank" class="theme-btn btn-style-three"><i class="fa fa-envelope"></i> Contact Us</a>
					</div>
					<div class="contact-info">
						<h2>Contact Info</h2>
						<ul class="list-style-one">
							<li><span class="icon flaticon-map-1"></span><?=$siteSettingArr->address?></li>
							<li><span class="icon flaticon-telephone"></span>+91&nbsp;<?=$siteSettingArr->phone?></li>
							<li><span class="icon flaticon-message-1"></span><?=$siteSettingArr->contact_email?></li>
							<li><span class="icon flaticon-timetable"></span>Week Days: 09.00 to 18.00 Sunday: Closed</li>
						</ul>
					</div>
					<!-- Social Box -->
					<ul class="social-box">
						<li class="facebook"><a href="#" class="fab fa-facebook-f"></a></li>
						<li class="twitter"><a href="#" class="fab fa-twitter"></a></li>
						<li class="linkedin"><a href="#" class="fab fa-linkedin-in"></a></li>
						<li class="instagram"><a href="#" class="fab fa-instagram"></a></li>
						<li class="youtube"><a href="#" class="fab fa-youtube"></a></li>
					</ul>
				</div>
			</div>
				
		   </div>
		</div>
	 </div>
  </div>
  <!-- END sidebar widget item -->

	<!-- xs modal -->
	<div class="zoom-anim-dialog mfp-hide modal-searchPanel" id="modal-popup-2">
	    <div class="xs-search-panel">
	        <form action="<?=SITE_URL?>" method="POST" class="xs-search-group">
	            <input type="search" class="form-control" name="search" id="search" placeholder="Search">
	            <button type="submit" class="search-button"><i class="icon icon-search"></i></button>
	        </form>
	    </div>
	</div><!-- End xs modal -->
	<!-- end language switcher strart -->
	<script src="<?=RESOURCE_URL?>js/popper.min.js"></script>
	<script src="<?=RESOURCE_URL?>js/bootstrap.min.js"></script>
	<script src="<?=RESOURCE_URL?>js/appear.js"></script>
	<script src="<?=RESOURCE_URL?>js/owl.js"></script>
	<script src="<?=RESOURCE_URL?>js/nav-tool.js"></script>

	<?php if($urlSegmentArr['route'] == 'gallery'){ ?>
		<script src="<?=RESOURCE_URL?>js/jquery.fancybox.js"></script>
	<?php } ?>

	<?php if($urlSegmentArr['route'] == 'gallery'){ ?>
		<script src="<?=RESOURCE_URL?>js/mixitup.js"></script>
	<?php } ?>	

	<script src="<?=ADMIN_RESOURCE_URL?>js/plugins/validate/jquery.validate.min.js"></script>

	<?php if($urlSegmentArr['route'] == 'course' || $urlSegmentArr['route'] == 'franchise'){ ?>
		<!-- Datatable script--> 
		<script src="<?=ADMIN_RESOURCE_URL?>js/plugins/dataTables/datatables.min.js"></script>
		<script src="<?=ADMIN_RESOURCE_URL?>js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>
	<?php } ?>	

	<script src="<?=RESOURCE_URL?>js/jquery.magnific-popup.min.js"></script>
	<script src="<?=RESOURCE_URL?>js/main.js"></script>
	<script src="<?=RESOURCE_URL?>js/script.js"></script>
		
	<!--Sweet alert cdn include -->
	<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
	<!-- Sticky sidebar js library -->
	
	<script src="<?=RESOURCE_URL?>js/sticky/sticky-sidebar-scroll.min.js"></script>
	<!--- Share button for blog --->
	<script async src="https://static.addtoany.com/menu/page.js"></script>
	
	<script src="<?=RESOURCE_URL?>js/custom.js"></script>

	<script async src='https://d2mpatx37cqexb.cloudfront.net/delightchat-whatsapp-widget/embeds/embed.min.js'></script>
      
</body>
</html>
