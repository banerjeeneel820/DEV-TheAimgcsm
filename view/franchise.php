<?php
  /*print"<pre>";
  print_r($franchiseArr);
  print"</pre>";*/
?>
	
	<!--Page Title-->
    <section class="page-title" style="background-image:url(<?=RESOURCE_URL?>images/background/12.jpg);">
    	<div class="auto-container">
        	<div class="row clearfix">
            	<!--Title -->
            	<div class="title-column col-lg-6 col-md-12 col-sm-12">
                	<h1>Franchise Page</h1>
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
    <div class="sidebar-page-container mt-0" id="page_content">
        <div class="auto-container">
           <div class="shimmer-preview">
                 <div class="full-row-box shimmer-shine"></div>
           </div> 

           <div class="div-content d-none">  
              <div class="alert alert-success text-center ml-3" role="alert">
                 Our all centers details have been given below.&nbsp;If you have any question please don’t hesitate to contact us @ <?=$siteSettingArr->phone?>
              </div>  
           </div>       
        <div class="row clearfix">
          <!--Content Side-->
          <div class="content-side col-lg-8 col-md-12 col-sm-12">
             
             <div class="row">
               <div class="col-lg-12">
                    <div class="shimmer-preview">
                        <?php for($pi=1;$pi<=60;$pi++){ ?> 
                          <div class="table-box shimmer-shine"></div>
                        <?php } ?>
                    </div>
                    
                    <div class="div-content d-none">    
                      <table class="dataTables styled-table text-center">
                        <thead class="cursor-pointer">
                            <tr>
                                <th>Francihse</th>
                                <th>Center Name</th>
                                <th>Contact Person</th>
                                <th>Contact No</th>           
                                <th>Download Brochure</th>
                            </tr>
                        </thead>
                        <tbody>
                          <?php 
                            foreach($franchiseArr as $index => $content){
                                 
                                $center_slug = seoUrl($content->center_name,'seo');

                                $franchise_thumbnail = USER_UPLOAD_DIR.'franchise/'.$content->fran_image;

                                if (!strlen($content->fran_image)>0 || !file_exists($franchise_thumbnail)) {   
                                   $franchise_thumbnail = RESOURCE_URL.'images/preview.jpg'; 
                                }else{
                                   $franchise_thumbnail = USER_UPLOAD_URL.'franchise/'.$content->fran_image;
                                }

                                if(strlen($content->fran_pdf_name)>0 && file_exists(USER_UPLOAD_DIR.'franchise/'.$content->fran_pdf_name)){
                                   $franchise_pdf = USER_UPLOAD_URL.'franchise/'.$content->fran_pdf_name;
                                }else{
                                   $franchise_pdf = RESOURCE_URL.'images/COMPUTER-COURSE.pdf';
                                }
                          ?> 
                              <tr>
                                <input type="hidden" id="franName_<?=$content->id?>" value="<?=$content->center_name?>">
                                <input type="hidden" id="cpName_<?=$content->id?>" value="<?=$content->owner_name?>">
                                <input type="hidden" id="contactNo_<?=$content->id?>" value="<?=$content->fran_phone?>">
                                <input type="hidden" id="emailID_<?=$content->id?>" value="<?=$content->fran_email?>">
                                <input type="hidden" id="franAddress_<?=$content->id?>" value="<?=$content->fran_address?>">
                                <input type="hidden" id="franPdf_<?=$content->id?>" value="<?=$franchise_pdf?>">
                                <input type="hidden" id="franSlug_<?=$content->id?>" value="<?=$center_slug?>">
                                <td>
                                   <a href="<?=$franchise_thumbnail?>" data-fancybox="gallery"  data-caption="<?=$content->center_name?>">
                                    <img alt="image" src="<?=$franchise_thumbnail?>" style="height:55px;width:55px;">
                                    </a> 
                                </td>

                                <td class="project-title">
                                    <span data-toggle="tooltip" data-placement="top" title="Click here to view franchise details" style="cursor:pointer;" onclick="redirectToDetails(<?=$content->id?>)">
                                      <?=$content->center_name?>
                                    </span>   
                                </td>

                                <td class="project-title">
                                   <?=$content->owner_name?>
                                </td>

                                <td class="project-title">
                                   <?=$content->fran_phone?>
                                </td>

                                <td class="project-status">
                                   <a href="javascript:void(0);" class="btn btn-info btn-circle btn-sm" data-toogle="tooltip" data-placement="top" title="View Franchise Details" onclick="showFranchiseDetail(<?=$content->id?>)"><i class="fa fa-eye" style="color:#ffff;"></i></a>  
                                    
                                   <a href="<?=$franchise_pdf?>" class="btn btn-success btn-circle btn-sm exportReceiptData" data-toogle="tooltip" data-placement="top" title="Download Brochure" download><i class="fa fa-download" style="color:#ffff;"></i></a>
                                </td>
                             </tr>
                           <?php } ?>    
                        </tbody>
                      </table>
                    </div>  
               </div> 
             </div>   
                    
          </div>
                
            <!--Sidebar Side-->
            <div class="sidebar-side col-lg-4 col-md-12 col-sm-12">
                <aside class="sidebar sticky_sidebar">

                     <div class="shimmer-preview">
                        <?php for($pi=1;$pi<=12;$pi++){ ?> 
                          <div class="form-row-box shimmer-shine"></div>
                        <?php } ?>
                    </div>
                    
                    <div class="div-content d-none"> 
                        <!-- Search 
                        <div class="sidebar-widget search-box">
                            <form method="post" action="https://expert-themes.com/html/khidmat/contact.html">
                                <div class="form-group">
                                    <input type="search" name="search-field" value="" placeholder="Search..." required>
                                    <button type="submit"><span class="icon fa fa-search"></span></button>
                                </div>
                            </form>
                        </div>-->
                        
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
                                    <input type="text" class="form-control" name="user_city" id="user_city" value="" placeholder="Your City" autocomplete="off" required>
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
                    </div>    
                    
                </aside>
            </div>
        
        </div>
    </div>
</div>


<div class="modal fade" id="showFranDetail" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="insModalTitle">Highlights of {Franchise Name}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

         <section class="cardBlk">
            <div class="highlightContainer">
                <div class="highlightContent">
                    <table class="table table-bordered">
                       <tbody>
                        <tr>
                            <td style="width: 527.115px;"><strong>Franchise Contact Person:</strong></td>
                            <td style="width: 535.219px;" id="franCpModal"></td>
                        </tr>
                        <tr>
                            <td style="width: 527.115px;"><strong>Franchise Contact No:</strong></td>
                            <td style="width: 535.219px;" id="franCNModal"></td>
                        </tr>
                        <tr>
                            <td style="width: 527.115px;"><strong>Franchise Email:</strong></td>
                            <td style="width: 535.219px;" id="franEmailModal"></td>
                        </tr>
                        <tr>
                            <td style="width: 527.115px;"><strong>Franchise Address:</strong></td>
                            <td style="width: 535.219px;" id="franAddressModal"></td>
                        </tr>                                           
                       </tbody>
                    </table>
                </div>
                <div class="brochureContent">
                    <a href="javascript:void(0);" id="franPdfModalSrc" class="btn btn-primary btn-sm" download><i class="fa fa-download"></i>&nbsp;Download Brochure</a>
                </div>        
            </div>
        </section>
         
      </div>
      <div class="modal-footer">
         <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </form>  
  </div>
</div>
</div>              
<!-- Modal ends here -->

<script>
    function showFranchiseDetail(franRowId){
       var franName = $("#franName_"+franRowId).val(); 
       var cpName = $("#cpName_"+franRowId).val();
       var contactNo = $("#contactNo_"+franRowId).val();
       var emailID = $("#emailID_"+franRowId).val();
       var franAddress = $("#franAddress_"+franRowId).val();
       var franPdf = $("#franPdf_"+franRowId).val();

       //Populating data into modal
       $("#insModalTitle").text('Highlights of '+franName);
       $("#franCpModal").text(cpName);
       $("#franCNModal").text(contactNo);
       $("#franEmailModal").text(emailID);
       $("#franAddressModal").text(franAddress);
       $("#franPdfModalSrc").attr('src',franPdf);
       
       $("#showFranDetail").modal('show');
    }

    function redirectToDetails(franRowId){
        var franSlug = $("#franSlug_"+franRowId).val();
        //Redirecting to franchise detail page
        window.location = SITE_URL+'franchise-detail/'+franSlug;
    }
</script>    
