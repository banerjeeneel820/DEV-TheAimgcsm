<?php
  $blog_slug = $urlSegmentArr['params'][1];
  $blogDetailArr = fetchBlogDetail($blog_slug);
  //$blog_excerpt = get_Gloabl_Content_Excerpt($blogDetailArr->blog_description,200);
  $dateValue = strtotime($blogDetailArr->created_at);                     
  //$yr = date("Y", $dateValue) ." "; 
  $month = date("m", $dateValue)." "; 
  $date = date("d", $dateValue); 

  if($blogDetailArr->file_upload_type == "local"){
     $blog_thumbnail =  USER_UPLOAD_URL.'blog/'.$blogDetailArr->featured_image;
  }else{
     $blog_thumbnail =  $blogDetailArr->featured_image;
  } 

  $courseparamArr['protocol'] = 'main_page';
  //Fetching course array
  $courseArr = fetchGlobalCourse($courseparamArr);
  /*print"<pre>";
  print_r($blogDetailArr);
  print"</pre>";*/
?>
	
	<!--Page Title-->
    <section class="page-title" style="background-image:url(<?=RESOURCE_URL?>images/background/12.jpg);">
    	<div class="auto-container">
        	<div class="row clearfix">
            	<!--Title -->
            	<div class="title-column col-lg-6 col-md-12 col-sm-12">
                	<h1>News Detail</h1>
                </div>
                <!--Bread Crumb -->
                <div class="breadcrumb-column col-lg-6 col-md-12 col-sm-12">
                    <ul class="bread-crumb clearfix">
                        <li><a href="index-2.html"><span class="icon fas fa-home"></span> Home</a></li>
                        <li class="active"><span class="icon fas fa-arrow-alt-circle-right"></span> News Detail</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->
	
	<!--Sidebar Page Container-->
    <div class="sidebar-page-container">
    	<div class="auto-container">
        	<div class="row clearfix">
            	
                <!--Content Side-->
                <div class="content-side col-lg-8 col-md-12 col-sm-12">
					<div class="news-detail">
						<div class="inner-box">
							<div class="image">
								<img src="<?=$blog_thumbnail?>" alt="<?=$blogDetailArr->blog_title?>" style="width:770px;height:450px;"/>
							</div>
							<div class="lower-content">
								<div class="content">
									<div class="date-outer">
										<div class="date"><?=$date?></div>
                                        <div class="month"><?=$monthArr[ceil($month-1)]?></div>
									</div>
									<ul class="post-meta">
										<!--<li><span class="icon flaticon-chat-comment-oval-speech-bubble-with-text-lines"></span>Comments 10</li>-->
										<li><span class="icon far fa-folder-open"></span><?=$blogDetailArr->category_string?></li>
									</ul>
									<h3><?=$blogDetailArr->blog_title?></h3>
									<div class="text">
									  <?=$blogDetailArr->blog_description?>
									</div>
									<!--Social Box-->
									<ul class="social-box">
                                        <li class="share">Share: </li>
										 <div class="a2a_kit a2a_kit_size_32 a2a_default_style">
                                            <a class="a2a_button_facebook"></a>
                                            <a class="a2a_button_twitter"></a>
                                            <a class="a2a_button_email"></a>
                                            <a class="a2a_button_linkedin"></a>
                                            <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
                                        </div>
									</ul>
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
                                <input type="hidden" name="enquiry_type" value="course">
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
                                    <input type="text" class="form-control" name="user_city" value="" placeholder="Your City" required>
                                </div>

                                <div class="form-group">
                                     <select class="form-control course" name="course_id" id="course_id" data-placeholder="Choose a Course..." tabindex="2">
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
                                    <li><span class="icon fas fa-phone-volume"></span>123 456 7890</li>
                                    <li><span class="icon fas fa-envelope"></span>info@khidmat.com use contact Form</li>
                                </ul>
                            </div>
                        </div>
                        
                    </aside>
                 </div>
			
			</div>
		</div>
	</div>
	
	<script>
      //Define global variables & callback function for recaptcha 
      var captchaWidgetId;
      var onloadCallback = function() {
         // Renders the HTML element with id 'example1' as a reCAPTCHA widget.
         // The id of the reCAPTCHA widget is assigned to 'widgetId1'.
         captchaWidgetId = grecaptcha.render('form_recaptcha_div', {
           'sitekey' : '6LdJ398UAAAAALCcgKy69mXlTjI4sfz682uHR0_e',
           'theme' : 'light'
         });
      }  
        
      //function to check unique email id for user  
      function check_user_email(user_email){
     
          if(user_email.length >0){
            var regularExp = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            var chkEmail = regularExp.test(user_email);

          if(!chkEmail){
              swal({
                 title: "Oops!",
                 text: "Enter a proper email!",
                 type: "error"
              });
              $('#create').attr('disabled',true);
              return false;
           }
          }else{
            swal({
               title: "Oops!",
               text: "This field is required!",
               type: "error"
            });
            $('#create').attr('disabled',true);
            return false;
          }
     }

     //handling user enquiry form
     $(document).on('submit', '#user_enquiry_form', function(event){
       event.preventDefault();  

       var course_id = $('#course_id').val();
       var course_name = $('#course_'+course_id).text();
       //putting course name for further use   
       $('#course_name').val(course_name);

       var user_email = $('#user_email').val();
       //validate user email
       check_user_email(user_email);

       $.ajax({
          url:ajaxCallUrl,
          method:'POST',
          data: new FormData(this),
          contentType:false,
          processData:false,
          beforeSend: function() {
             $('#contact_submit').html('Connecting <i class="fa fa-spinner fa-spin"></i>').attr('disabled',true); 
          },
          success:function(responseData){
              var data = JSON.parse(responseData);
              //Disabling loader
              $('#contact_submit').html('Submit').attr('disabled',false); 
              //reseting captcha
              grecaptcha.reset(captchaWidgetId);
              //reseting select 2
              $("#select2-course_id-container"). text('Choose a Course...');
             //console.log(responseData);
             if(data.check == 'success'){
                //reseting form data
                $('#user_enquiry_form')[0].reset();
                //show sweetalert success
                swal("Success!", "Your enquiry is reached to us successfully! We shall contact you shortly.", "success");
                return true; 
             }else{
                //show sweetalert success
                 if(data.message.length>0){
                   var message = data.message;
                }else{
                   var message = "Something went wrong";
                }
                swal("Error!",message, "error");
                return false;
             }
          }
         });
     }); 
</script>
    