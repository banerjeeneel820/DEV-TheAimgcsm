<?php
  //print_r($_GET);
  $dataArr['action'] = 'blog';
  $dataArr['table'] = 'blog';
  $dataArr['protocol'] = 'main_page';
  $dataArr['limit'] = 5;  // Number of entries to show in a page.

  // Look for a GET variable page if not found default is 1. 
  if($urlSegmentArr['params'][1] == 'page'){
    $dataArr['pageNo'] = $urlSegmentArr['params'][2];
  }else{
    $dataArr['pageNo'] = 1;
  }

  $returnArr = global_Pagination_Handler($dataArr);

  $blogDataArr = $returnArr['listingArr'];
  $pageLink = $returnArr['pageLink'];  

  $courseparamArr['protocol'] = 'main_page';
  //Fetching course array
  $courseArr = fetchGlobalCourse($courseparamArr);
  
  /*print"<pre>";
  print_r($returnArr);
  print"</pre>";*/
?>
	
	<!--Page Title-->
    <section class="page-title" style="background-image:url(<?=RESOURCE_URL?>images/background/12.jpg);">
    	<div class="auto-container">
        	<div class="row clearfix">
            	<!--Title -->
            	<div class="title-column col-lg-6 col-md-12 col-sm-12">
                	<h1>Latest News</h1>
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
    <div class="sidebar-page-container">
    	<div class="auto-container">
        <div class="row clearfix">
            	
          <!--Content Side-->
          <div class="content-side col-lg-8 col-md-12 col-sm-12">
  			  <?php 
                 foreach($blogDataArr as $key=> $blog){
                   $blog_excerpt = get_Gloabl_Content_Excerpt($blog->blog_description,200);
                   $dateValue = strtotime($blog->created_at);                     
				   //$yr = date("Y", $dateValue) ." "; 
				   $month = date("m", $dateValue)." "; 
				   $date = date("d", $dateValue); 

                   if($blog->file_upload_type == "local"){
                     $blog_thumbnail =  USER_UPLOAD_URL.'blog/'.$blog->featured_image;
                   }else{
                     $blog_thumbnail =  $blog->featured_image;
                   } 
  		      ?>
					
					<!-- News Block Four -->
					<div class="news-block-four">
						<div class="inner-box">
							<div class="image">
								<a href="<?=SITE_URL.'blog-detail/'.$blog->seo_url_structure?>"><img src="<?=$blog_thumbnail?>" alt="<?=SITE_URL.'blog-detail/'.$blog->seo_url_structure?>" style="width:770px;height:450px;"/></a>
								<div class="read-more"><a href="<?=SITE_URL.'blog-detail/'.$blog->seo_url_structure?>" class="more">Read More</a></div>
							</div>
							<div class="lower-content">
								<div class="content">
									<div class="date-outer">
										<div class="date"><?=$date?></div>
										<div class="month"><?=$monthArr[ceil($month-1)]?></div>
									</div>
									<h3><a href="<?=SITE_URL.'blog-detail/'.$blog->seo_url_structure?>"><?=$blog->blog_title?></a></h3>
									<ul class="post-meta">
										<!--<li><a href="<?=SITE_URL?>blog-detail/"><span class="icon flaticon-chat-comment-oval-speech-bubble-with-text-lines"></span>Comments 10</a></li>-->
										<li><a href="<?=SITE_URL.'blog-detail/'.$blog->seo_url_structure?>"><span class="icon far fa-folder-open"></span><?=$blog->category_string?></a></li>
									</ul>
									<div class="text"><?=$blog_excerpt?></div>
								</div>
							</div>
						</div>
					</div>
					<?php } ?>
					
					<!--Post Share Options-->
					<div class="styled-pagination text-center">
						<ul class="clearfix">
						  <?=$pageLink?>	
						</ul>
					</div>
					
				</div>
				
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

                                <div class="form-group autocomplete">
                                    <input type="text" class="form-control" name="user_city" id="user_city" value="" placeholder="Your City" required>
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

     