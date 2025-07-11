<?php
  $testimonialParamArr['protocol'] = 'main_page';
  //Fetching course array
  $testimonialArr = fetchStudentTestimonial($testimonialParamArr);

  /*print"<pre>";
  print_r($courseArr);
  print"</pre>";*/
?>	
<style>
	#testiminial-description {
	  height:140px;
	  overflow: hidden;
	}

	#testiminial-description:hover {
	  overflow-y: auto;
	}
</style>	
	<!-- Testimonial Section -->
	<section class="testimonial-page-section">
		<div class="auto-container">
			<div class="row clearfix">
		  <?php 
		     foreach($testimonialArr as $index => $content){ 
		     	 if($content->file_upload_type == "local"){
             $student_thumbnail =  USER_UPLOAD_URL.'student/'.$content->image_file_name;
		     	 }else{
             $student_thumbnail =  $content->image_file_name;
		     	 }
		  ?>
					<!-- Testimonial Block Two -->
					<div class="testimonial-block-two col-lg-6 col-md-6 col-sm-12">
						<div class="inner-box">
							<div class="quote-icon flaticon-two-quotes"></div>
							<div class="image-outer">
								<div class="image">
									<img src="<?=$student_thumbnail?>" alt="" />
								</div>
							</div>
							<div class="text" id="testiminial-description"><?=strip_tags(stripslashes($content->description))?></div>
							<h5><?=$content->stu_name?></h5>
							<!--<div class="designation">Designation:&nbsp;Student,&nbsp;Course Name:&nbsp;<?=$content->course_title?>,&nbsp;Franchise:&nbsp;<?=$content->center_name?></div>-->
							<div class="designation">Student</div>
						</div>
					</div>
			<?php } ?>	
			</div>
		</div>
	</section>
	<!-- End Volunter Section -->