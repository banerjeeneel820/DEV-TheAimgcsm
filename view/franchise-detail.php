<?php
  $franchise_slug = $urlSegmentArr['params'][1];
  $center_name = seoUrl($franchise_slug,'r_seo'); 
  $franchiseDetailArr = fetchFranchiseDetail($center_name);

  $dateValue = strtotime($courseDetailArr->created_at);                     
  //$yr = date("Y", $dateValue) ." "; 
  $month = date("m", $dateValue)." "; 
  $date = date("d", $dateValue); 

  //Configuring franchise file data
  if(strlen($franchiseDetailArr->fran_image)>0 && file_exists(USER_UPLOAD_DIR.'franchise/'.$franchiseDetailArr->fran_image)){
     $franchise_thumbnail = USER_UPLOAD_URL.'franchise/'.$franchiseDetailArr->fran_image;
  }else{
     $franchise_thumbnail = ADMIN_RESOURCE_URL.'images/preview.jpg';
  }

  //Configuring franchise file data
  if(strlen($franchiseDetailArr->fran_pdf_name)>0 && file_exists(USER_UPLOAD_DIR.'franchise/'.$franchiseDetailArr->fran_pdf_name)){
     $franchise_pdf = USER_UPLOAD_URL.'franchise/'.$franchiseDetailArr->fran_pdf_name;
  }else{
     $franchise_pdf = ADMIN_RESOURCE_URL.'images/COMPUTER-COURSE.pdf';
  }
  
  /*print"<pre>";
  print_r($franchiseDetailArr);
  print"</pre>";*/
?>
	
	<!--Page Title-->
    <section class="page-title" style="background-image:url(<?=RESOURCE_URL?>images/background/12.jpg);">
    	<div class="auto-container">
        	<div class="row clearfix">
            	<!--Title -->
            	<div class="title-column col-lg-6 col-md-12 col-sm-12">
                	<h1>Franchise Detail</h1>
                </div>
                <!--Bread Crumb -->
                <div class="breadcrumb-column col-lg-6 col-md-12 col-sm-12">
                    <ul class="bread-crumb clearfix">
                        <li><a href="<?=SITE_URL?>"><span class="icon fas fa-home"></span> Home</a></li>
                        <li class="active"><span class="icon fas fa-arrow-alt-circle-right"></span> Franchise Detail</li>
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
								<img src="<?=$franchise_thumbnail?>" alt="<?=$franchiseDetailArr->center_name?>" style="width:770px;height:450px;"/>
							</div>

                            <section class="cardBlk">
                                <h3 class="blockHeading">Highlights of <?=$franchiseDetailArr->center_name?></h3>
                                <div class="highlightContainer">
                                    <div class="highlightContent">
                                        <table class="table table-bordered">
                                           <tbody>
                                            <tr>
                                                <td style="width: 527.115px;"><strong>Franchise Contact Person:</strong></td>
                                                <td style="width: 535.219px;"><?=$franchiseDetailArr->owner_name?></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 527.115px;"><strong>Franchise Contact No:</strong></td>
                                                <td style="width: 535.219px;"><?=$franchiseDetailArr->fran_phone?></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 527.115px;"><strong>Franchise Email:</strong></td>
                                                <td style="width: 535.219px;"><?=$franchiseDetailArr->fran_email?></td>
                                            </tr>
                                            <tr>
                                                <td style="width: 527.115px;"><strong>Franchise Address:</strong></td>
                                                <td style="width: 535.219px;"><?=$franchiseDetailArr->fran_address?></td>
                                            </tr>                                           
                                           </tbody>
                                        </table>
                                    </div>
                                    <div class="brochureContent">
                                        <a href="<?=$franchise_pdf?>" class="btn btn-primary btn-sm" download><i class="fa fa-download"></i>&nbsp;Download Brochure</a>
                                    </div>        
                                </div>
                            </section>

							<div class="lower-content">
								<div class="content">
									<div class="date-outer">
										<div class="date"><?=$date?></div>
                                        <div class="month"><?=$monthArr[ceil($month-1)]?></div>
									</div>
                                    <div class="post-meta">
                                        <h3><?=$franchiseDetailArr->center_name?></h3>
                                    </div>
									
									<div class="text">
									   <?=$franchiseDetailArr->fran_description?>
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
	