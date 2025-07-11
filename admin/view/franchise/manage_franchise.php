<?php
  if(isset($_GET['id'])){
    $fran_row_id = $_GET['id'];
    $franDetailArr = $pageContent['pageData']['frnachise_data'];

    //Configuring franchise file data
    if(strlen($franDetailArr->fran_image)>0 && file_exists(USER_UPLOAD_DIR.'franchise/'.$franDetailArr->fran_image)){
        $franchise_thumbnail = USER_UPLOAD_URL.'franchise/'.$franDetailArr->fran_image;
    }else{
        $franchise_thumbnail = RESOURCE_URL.'images/preview.jpg';
    }

    //Configuring franchise file data
    if(strlen($franDetailArr->fran_pdf_name)>0 && file_exists(USER_UPLOAD_DIR.'franchise/'.$franDetailArr->fran_pdf_name)){
       $franchise_pdf = USER_UPLOAD_URL.'franchise/'.$franDetailArr->fran_pdf_name;
    }else{
       $franchise_pdf = RESOURCE_URL.'images/COMPUTER-COURSE.pdf';
    }

  }else{
    $fran_row_id = null;
  }
  //Configuring user role array
  $userRoleArr = unserialize($franDetailArr->user_role);

  if(!is_array($userRoleArr)){
     $userRoleArr = array();
  }

  //echo $franDetailArr->fran_og_pass;

  /*print"<pre>";
  print_r($franDetailArr);
  print"</pre>";*/

?>
         
        <div class="wrapper wrapper-content animated fadeInRight"> 
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>General Franchise Details</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content" id="manage_franchise_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <form id="manage_franchise_form" class="needs-validation" method="post" onsubmit="return false;">
                              <input type="hidden" name="action" id="action" value="manageGlobalFranchise">
                              <input type="hidden" name="fran_row_id" id="fran_row_id" value="<?=(!empty($franDetailArr)?$franDetailArr->id:'null')?>">

                               <input type="hidden" name="fran_hidden_password" value="<?=$franDetailArr->fran_pass?>"> 
                               <input type="hidden" name="fran_hidden_og_password" value="<?=$franDetailArr->fran_og_pass?>">

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Franchise Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="center_name" placeholder="Enter Franchise Name..." value="<?=(isset($franDetailArr)?$franDetailArr->center_name:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>
                               
                               <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Owner Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Owner Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="owner_name" placeholder="Enter Franchise Owner Name..." value="<?=(isset($franDetailArr)?$franDetailArr->owner_name:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right">
                                    <label class="col-sm-2 col-form-label">Franchise Contact No <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Contact No"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="number" class="form-control" name="fran_phone" placeholder="Enter Franchise Contact No..." value="<?=(isset($franDetailArr)?$franDetailArr->fran_phone:'')?>" required>
                                     </div>   
                                    </div>

                                    <label class="col-sm-2 col-form-label">Franchise ID <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Franchise ID"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="text" class="form-control" value="<?=(isset($franDetailArr)?$franDetailArr->fran_id:'')?>" readonly>
                                      </div>   
                                    </div>

                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Franchise Email <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Email"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="email" class="form-control" id="fran_email" name="fran_email" placeholder="Enter Franchise Email..." value="<?=(isset($franDetailArr)?$franDetailArr->fran_email:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>
                                
                                <div class="form-group row"> 
                                     <label class="col-sm-2 col-form-label text-right">Owned Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Owned Status"><i class="fa fa-question-circle"></i></span></label>
                                     <div class="col-sm-10 pt-1">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="yes" name="owned_status" <?=($franDetailArr->owned_status)=='yes'?'checked':''?> /> <i></i>Owned </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="no" name="owned_status" <?=($franDetailArr->owned_status)=='no'?'checked':''?>> <i></i> Autonomous </label>
                                    </div>
                                </div>   
                                <div class="hr-line-dashed"></div>  

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Franchise Image <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                    <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                      <?php if(isset($_GET['id'])){ ?>  
                                        <div class="col-sm-2 pb-3 pl-0">
                                           <h4>Current image</h4>
                                           <a href="<?=$franchise_thumbnail?>" data-fancybox="gallery" data-caption="<?=$franDetailArr->center_name?>">  
                                              <img id="current_image_review" src="<?=$franchise_thumbnail?>" alt="franchise image" style="height: 100px;width: 100px;" />
                                           </a>
                                         </div>
                                        <?php } ?> 
                                        <div class="<?php (isset($_GET['id']) ? 'col-sm-10' : 'col-sm-12')?> pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="fran_preview_image" data-fancybox="gallery" data-caption="<?=$franDetailArr->center_name?>"> 
                                           <img id="image_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                      
                                      <div class="btn-group" id="fran_image_local_div">
                                          <label title="Upload a file" for="franchiseImage" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="local_fran_image" id="franchiseImage" class="hide" />
                                              Upload a featured image of the franchise...
                                          </label>    
                                      </div>

                                    </div>
                                    <input type="hidden" name="hidden_fran_image" value="<?=$franDetailArr->fran_image?>">
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Franchise Address <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Address"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="fran_address" placeholder="Enter Franchise Address..." value="<?=(isset($franDetailArr)?$franDetailArr->fran_address:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Franchise Password <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Password"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group show_hide_password" id="input_password_div">
                                        <input type="password" class="form-control input_password" id="fran_pass" name="fran_pass" placeholder="Enter Franchise Password..." autocomplete="off">
                                        <div class="input-group-addon">
                                            <a href="javascript:void(0);"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                        </div>
                                     </div> 
                                     <span id="fran_pswd_err_msg" style="color: red;"></span>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Repeat Password <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Repeat Franchise Password"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group show_hide_password" id="confirm_password_div">
                                        <input type="password" class="form-control input_password" id="confirm_fran_pass" name="confirm_fran_pass" autocomplete="off" placeholder="Repeat Franchise Password...">
                                        <div class="input-group-addon">
                                            <a href="javascript:void(0);"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                        </div>
                                     </div> 
                                     <span id="fran_cnfrmpass_err_msg" style="color: red;"></span>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Franchise Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Status"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-1">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="record_status" <?=($franDetailArr->record_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" name="record_status" <?=($franDetailArr->record_status)=='blocked'?'checked':''?> required> <i></i> Blocked </label>
                                    </div>

                                    <label class="col-sm-2 col-form-label text-right">Featured Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Featured Status"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-1">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="featured_status" <?=($franDetailArr->featured_status)=='active'?'checked':''?> /> <i></i>Active </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="inactive" name="featured_status" <?=($franDetailArr->featured_status)=='inactive'?'checked':''?>> <i></i> Inactive </label>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Franchise Detail Pdf <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a pdf file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-9">
                                       <div class="row pl-3">
                                         <img id="image_upload_preview" src="<?=RESOURCE_URL.'images/pdf_preview.png'?>" alt="your image" style="height: 100px;width: 100px;" />
                                       </div>

                                       <div class="row pl-1 pt-1 pb-2">
                                         <div class="col-sm-12">
                                           <?php if(isset($_GET['id'])){ ?>
                                              <a href="<?=$franchise_pdf?>" data-fancybox="gallery" data-caption="<?=$franDetailArr->center_name?>"><i class="fa fa-file-pdf-o" aria-hidden="true"></i>&nbsp;   
                                              View Current PDF
                                              </a> 
                                           <?php } ?>   
                                           <span class='d-none' data-toggle="modal" data-target="#viewPdfModal" id='preview_fran_pdf'>
                                              <a style="color:green;" class='pl-2' data-toggle="tooltip" data-placement="bottom" title="View PDF Preview Before Upload">   
                                                <i class="fa fa-eye" aria-hidden="true"></i> PDF Preview
                                              </a>
                                           </span>   
                                         </div>  
                                       </div>  

                                       <div class="btn-group" id="fran_pdf_local_div">
                                          <label title="Upload a file" for="franchisePdf" class="btn btn-primary">
                                              <input type="file" accept="application/pdf" name="local_fran_pdf" id="franchisePdf" class="hide" />
                                              Upload a Pdf containg detail of the franchise...
                                          </label>    
                                      </div>

                                    </div>
                                    <input type="hidden" name="hidden_fran_pdf" value="<?=$franDetailArr->fran_pdf_name?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="forum-title pr-38 border-bottom mb-4">
                                   <h5>User Permission Management:</h5> 
                                </div>   

                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Dashboard <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select dashboard to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_dashboard" name="user_role[]" <?=(in_array("view_dashboard", $userRoleArr)?'checked':'')?>> Dashboard </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_franchise"> <input type="checkbox" data-menu="franchise" class="show_role_menu" id="show_this_in_menu_franchise"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div> 
 
                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Franchise <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Frnachise to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_franchise" name="user_role[]" class="sub_role_franchise sub_role_single" data-parent-menu="franchise" <?=(in_array("view_franchise", $userRoleArr)?'checked':'')?>> View Frnachise </label>
                                       
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="create_franchise" name="user_role[]" class="sub_role_franchise sub_role_single" data-parent-menu="franchise" <?=(in_array("create_franchise", $userRoleArr)?'checked':'')?>> Create Frnachise </label>
                                       
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="update_franchise" name="user_role[]" class="sub_role_franchise sub_role_single" data-parent-menu="franchise" <?=(in_array("update_franchise", $userRoleArr)?'checked':'')?>> Update Frnachise </label>
                                       
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="delete_franchise" name="user_role[]" class="sub_role_franchise sub_role_single" data-parent-menu="franchise" <?=(in_array("delete_franchise", $userRoleArr)?'checked':'')?>> Delete Frnachise </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                  <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_course"> <input type="checkbox" data-menu="course" class="show_role_menu" id="show_this_in_menu_course"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div> 

                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Course <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Course to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_course" name="user_role[]" class="sub_role_course sub_role_single" data-parent-menu="course" <?=(in_array("view_course", $userRoleArr)?'checked':'')?>> View Course </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="create_course" name="user_role[]" class="sub_role_course sub_role_single" data-parent-menu="course" <?=(in_array("create_course", $userRoleArr)?'checked':'')?>> Create Course </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="update_course" name="user_role[]" class="sub_role_course sub_role_single" data-parent-menu="course" <?=(in_array("update_course", $userRoleArr)?'checked':'')?>> Update Course </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="delete_course" name="user_role[]" class="sub_role_course sub_role_single" data-parent-menu="course" <?=(in_array("delete_course", $userRoleArr)?'checked':'')?>> Delete Course </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>


                                 <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_student"> <input type="checkbox" data-menu="student" class="show_role_menu" id="show_this_in_menu_student"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div> 

                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Student <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Student to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_student" name="user_role[]" class="sub_role_student sub_role_single" data-parent-menu="student" <?=(in_array("view_student", $userRoleArr)?'checked':'')?>> View Student </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="create_student" name="user_role[]" class="sub_role_student sub_role_single" data-parent-menu="student" <?=(in_array("create_student", $userRoleArr)?'checked':'')?>> Create Student </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="update_student" name="user_role[]" class="sub_role_student sub_role_single" data-parent-menu="student" <?=(in_array("update_student", $userRoleArr)?'checked':'')?>> Update Student </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="delete_student" name="user_role[]" class="sub_role_student sub_role_single" data-parent-menu="student" <?=(in_array("delete_student", $userRoleArr)?'checked':'')?>> Delete Student </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Result <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Result to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="update_result" name="user_role[]" class="sub_role_result sub_role_single" data-parent-menu="result" <?=(in_array("update_result", $userRoleArr)?'checked':'')?>> Update Result </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                  <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_receipt"> <input type="checkbox" data-menu="receipt" class="show_role_menu" id="show_this_in_menu_receipt"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div> 

                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Receipt <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Receipt to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_receipt" name="user_role[]" class="sub_role_receipt sub_role_single" data-parent-menu="receipt" <?=(in_array("view_receipt", $userRoleArr)?'checked':'')?>> View Receipt </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="create_receipt" name="user_role[]" class="sub_role_receipt sub_role_single" data-parent-menu="receipt" <?=(in_array("create_receipt", $userRoleArr)?'checked':'')?>> Create Receipt </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="update_receipt" name="user_role[]" class="sub_role_receipt sub_role_single" data-parent-menu="receipt" <?=(in_array("update_receipt", $userRoleArr)?'checked':'')?>> Update Receipt </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="delete_receipt" name="user_role[]" class="sub_role_receipt sub_role_single" data-parent-menu="receipt" <?=(in_array("delete_receipt", $userRoleArr)?'checked':'')?>> Delete Receipt </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_due_students" name="user_role[]" class="sub_role_receipt sub_role_single" data-parent-menu="receipt" <?=(in_array("view_due_students", $userRoleArr)?'checked':'')?>> View Due Students </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_exam"> <input type="checkbox" data-menu="exam" class="show_role_menu" id="show_this_in_menu_exam"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div>  

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Exam <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Receipt to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_exam" name="user_role[]" class="sub_role_exam sub_role_single" data-parent-menu="exam" <?=(in_array("view_exam", $userRoleArr)?'checked':'')?>> View Exam </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="create_exam" name="user_role[]" class="sub_role_exam sub_role_single" data-parent-menu="exam" <?=(in_array("create_exam", $userRoleArr)?'checked':'')?>> Create Exam </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="update_exam" name="user_role[]" class="sub_role_exam sub_role_single" data-parent-menu="exam" <?=(in_array("update_exam", $userRoleArr)?'checked':'')?>> Update Exam </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="delete_exam" name="user_role[]" class="sub_role_exam sub_role_single" data-parent-menu="exam" <?=(in_array("delete_exam", $userRoleArr)?'checked':'')?>> Delete Exam </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                  <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_receipt"> <input type="checkbox" data-menu="gallery" class="show_role_menu" id="show_this_in_menu_gallery"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div> 

                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Gallery <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Gallery to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_gallery" name="user_role[]" class="sub_role_gallery sub_role_single" data-parent-menu="gallery" <?=(in_array("view_gallery", $userRoleArr)?'checked':'')?>> View Gallery </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="create_gallery" name="user_role[]" class="sub_role_gallery sub_role_single" data-parent-menu="gallery" <?=(in_array("create_gallery", $userRoleArr)?'checked':'')?>> Create Gallery </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="update_gallery" name="user_role[]" class="sub_role_gallery sub_role_single" data-parent-menu="gallery" <?=(in_array("update_gallery", $userRoleArr)?'checked':'')?>> Update Gallery </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="delete_gallery" name="user_role[]" class="sub_role_gallery sub_role_single" data-parent-menu="gallery" <?=(in_array("delete_gallery", $userRoleArr)?'checked':'')?>> Delete Gallery </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                  <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_cms"> <input type="checkbox" data-menu="cms" class="show_role_menu" id="show_this_in_menu_cms"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div> 

                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Content Management <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select CMS to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="manage_home_slider" name="user_role[]" class="sub_role_cms sub_role_single" data-parent-menu="cms" <?=(in_array("manage_home_slider", $userRoleArr)?'checked':'')?>> Home Slider </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="manage_city_db" name="user_role[]" class="sub_role_cms sub_role_single" data-parent-menu="cms" <?=(in_array("manage_city_db", $userRoleArr)?'checked':'')?>> City Data Management </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                 <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_receipt"> <input type="checkbox" data-menu="category" class="show_role_menu" id="show_this_in_menu_category"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div>  

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Category <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Category to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_category" name="user_role[]" class="sub_role_category sub_role_single" data-parent-menu="category" <?=(in_array("view_category", $userRoleArr)?'checked':'')?>> View Category </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="create_category" name="user_role[]" class="sub_role_category sub_role_single" data-parent-menu="category" <?=(in_array("create_category", $userRoleArr)?'checked':'')?>> Create Category </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="update_category" name="user_role[]" class="sub_role_category sub_role_single" data-parent-menu="category" <?=(in_array("update_category", $userRoleArr)?'checked':'')?>> Update Category </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="delete_category" name="user_role[]" class="sub_role_category sub_role_single" data-parent-menu="category" <?=(in_array("delete_category", $userRoleArr)?'checked':'')?>> Delete Category </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                 <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_receipt"> <input type="checkbox" data-menu="email_template" class="show_role_menu" id="show_this_in_menu_email_template"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div>   

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Email Template <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Email Template to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_template" name="user_role[]" class="sub_role_email_template sub_role_single" data-parent-menu="email_template" <?=(in_array("view_template", $userRoleArr)?'checked':'')?>> View Email Template </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="create_template" name="user_role[]" class="sub_role_email_template sub_role_single" data-parent-menu="email_template" <?=(in_array("create_template", $userRoleArr)?'checked':'')?>> Create Email Template </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="update_template" name="user_role[]" class="sub_role_email_template sub_role_single" data-parent-menu="email_template" <?=(in_array("update_template", $userRoleArr)?'checked':'')?>> Update Email Template </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="delete_template" name="user_role[]" class="sub_role_email_template sub_role_single" data-parent-menu="email_template" <?=(in_array("delete_template", $userRoleArr)?'checked':'')?>> Delete Email Template </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_receipt"> <input type="checkbox" data-menu="news" class="show_role_menu" id="show_this_in_menu_news"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div>  

                               <div class="form-group row"><label class="col-sm-2 col-form-label text-right"> News <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select News to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="view_news" name="user_role[]" class="sub_role_news sub_role_single" data-parent-menu="news" <?=(in_array("view_news", $userRoleArr)?'checked':'')?>> View News </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="create_news" name="user_role[]" class="sub_role_news sub_role_single" data-parent-menu="news" <?=(in_array("create_news", $userRoleArr)?'checked':'')?>> Create News </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="update_news" name="user_role[]" class="sub_role_news sub_role_single" data-parent-menu="news" <?=(in_array("update_news", $userRoleArr)?'checked':'')?>> Update News </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="delete_news" name="user_role[]" class="sub_role_news sub_role_single" data-parent-menu="news" <?=(in_array("delete_news", $userRoleArr)?'checked':'')?>> Delete News </label>
                                    </div>
                                 </div>

                                <div class="hr-line-dashed"></div>
                                
                                <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_enquiry"> <input type="checkbox" data-menu="enquiry" class="show_role_menu" id="show_this_in_menu_enquiry"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div>  

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Enquiry <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Enquiry to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" name="user_role[]" value="view_enquiry" <?=(in_array("view_enquiry", $userRoleArr)?'checked':'')?> class="sub_role_enquiry sub_role_single" data-parent-menu="enquiry"> View Enquiry </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" name="user_role[]" value="delete_enquiry" <?=(in_array("delete_enquiry", $userRoleArr)?'checked':'')?> class="sub_role_enquiry sub_role_single" data-parent-menu="enquiry"> Delete Enquiry </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Manage Profile <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Edit Profile to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> <input type="checkbox" value="manage_profile" name="user_role[]" <?=(in_array("manage_profile", $userRoleArr)?'checked':'')?>> Manage Profile </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                 <div class="row">
                                   <div class="col-sm-2"></div>
                                   <div class="col-sm-3">
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_settings"> <input type="checkbox" data-menu="settings" class="show_role_menu" id="show_this_in_menu_settings"> Check all options </label>
                                    </div>
                                    <div class="col-sm-7"></div>
                                 </div>  
      
                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Manage Site Setting <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Newsletter to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

                                 <div class="col-sm-10">
                                        <label class="checkbox-inline i-checks pl-2 pt-2"> 
                                          <input type="checkbox" value="update_site_setting" name="user_role[]" <?=(in_array("update_site_setting", $userRoleArr)?'checked':'')?> class="sub_role_settings sub_role_single" data-parent-menu="settings"> Update Site Setting 
                                        </label>

                                        <label class="checkbox-inline i-checks pl-2 pt-2"> 
                                          <input type="checkbox" value="manage_site_backup" name="user_role[]" <?=(in_array("manage_site_backup", $userRoleArr)?'checked':'')?> class="sub_role_settings sub_role_single" data-parent-menu="settings"> Manage Site Backup 
                                        </label>
                                    </div>
                                 </div>

                                 <div class="hr-line-dashed"></div>

                                <div class="forum-title pr-38 border-bottom mb-4">
                                   <h5>Franchise Description</h5> 
                                </div>  

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Franchise Description <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter a event details"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-10">
                                            <textarea class="tinymce" name="fran_description">
                                               <?=(!empty($franDetailArr)?stripslashes($franDetailArr->fran_description):'')?>
                                          </textarea>
                                      </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=view_franchises" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
                                        <button class="btn btn-success btn-sm" id="create" type="submit" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="Save"><i class="fa fa-save"></i> Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Popup -->
        <div id="viewPdfModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>PDF Preview</h3>
                        <button type="button" class="close" data-dismiss="modal">
                            &times;</button>
                        <h4 class="modal-title">
                        </h4>
                    </div>
                    <div class="modal-body">
                       <iframe src="<?=($franDetailArr->pdf_upload_type=='cdn'? (strpos($franDetailArr->fran_pdf_name,'drive.google.com')?$franchise_pdf_cdn:''):'')?>" title="W3Schools Free Online Web Tutorials" id='pdfViewer' width="750" height="460"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            Close</button>
                    </div>
                </div>
            </div>
        </div>
    

        <!-- Custom JS -->
       <script>
          function readURL(input,type) {
            if (input.files && input.files[0]) {
                var reader = new FileReader(); 

                reader.onload = function (e) {
                    if(type == 'fran_image'){
                      $('#image_upload_preview').attr('src', e.target.result);
                      $('#fran_preview_image').attr('href', e.target.result);
                    }else{
                      $('#pdfViewer').attr('src',  e.target.result);
                    } 
                }
                reader.readAsDataURL(input.files[0]);
            }
         } 
         
         //function media url validation
         function checkMediaURL(url) {
            if(url.match(/\.(jpeg|jpg|gif|png|pdf)$/) != null){
                return true;
            }
            else if(url.includes('drive.google.com')) {
                return true;
            }else{
                toastr.info("Please use a url ends with jpeg,jpg,gif,png or pdf or host the resource at Google Drive and use the sharable link direcly here.", "Suggestion!",{timeOut: 10000,closeButton:true,progressBar:true}); 
                return false;
            }
         }

         function formatGoogleImg(url){
            var urlId = url.match(/[-\w]{25,}/);
            return "https://drive.google.com/uc?export=view&id="+urlId;
         }

         $(document).ready(function () {

             //Removing local storage data for validation
             localStorage.removeItem('user_email');
             localStorage.removeItem('user_email_check_rspns');

             $('.i-checks').iCheck({
                 checkboxClass: 'icheckbox_square-green',
                 radioClass: 'iradio_square-green',
             });

             $('.sub_role_single').each(function(index, value) {
               var check_all_menu = $(this).data('parent-menu'); 
               var checkbox_class = 'sub_role_'+check_all_menu;  
               var count_checked_checkbox = $('input:checkbox.'+checkbox_class+':checked').length;
               var count_checkbox = $('input:checkbox.'+checkbox_class).length;

               //console.log(count_checkbox+'***'+count_checked_checkbox);

               if(count_checkbox == count_checked_checkbox){
                 $('#show_this_in_menu_'+check_all_menu).removeAttr('checked');
                 $('#show_this_in_menu_'+check_all_menu).prop('checked', true);
                 $('#show_this_in_menu_'+check_all_menu).iCheck('update');
               }else{
                 $('#show_this_in_menu_'+check_all_menu).removeAttr('checked');
                 $('#show_this_in_menu_'+check_all_menu).prop('checked', false);
                 $('#show_this_in_menu_'+check_all_menu).iCheck('update');
               }
             }); 

             //Checking if all child menus are checked or not
             var check_all_menu = $('.sub_role_single').data('parent-menu');
             var checkbox_class = 'sub_role_'+check_all_menu; 

             var count_checked_checkbox = $('input:checkbox.'+checkbox_class+':checked').length;
             var count_checkbox = $('input:checkbox.'+checkbox_class).length;

             //console.log(count_checkbox+'***'+count_checked_checkbox);

             if(count_checkbox == count_checked_checkbox){
                $('#show_this_in_menu_'+check_all_menu).removeAttr('checked');
                $('#show_this_in_menu_'+check_all_menu).prop('checked', true);
                $('#show_this_in_menu_'+check_all_menu).iCheck('update');
             }else{
                $('#show_this_in_menu_'+check_all_menu).removeAttr('checked');
                $('#show_this_in_menu_'+check_all_menu).prop('checked', false);
                $('#show_this_in_menu_'+check_all_menu).iCheck('update');
             }

            //show_in_this_menu check/uncheck handler
             $(document).on('ifChanged','.show_role_menu',function(){
                var parent_id = $(this).data('menu');
                
                if (this.checked) {
                  $('.sub_role_'+parent_id).iCheck('check');
                }else{
                  $('.sub_role_'+parent_id).iCheck('uncheck');
                }
             }); 
        
            //Show/hide password handler in form
            $(document).on('click','.show_hide_password a',function(event) {
                 event.preventDefault();
                 var parent_div_id = $(this).parent().parent().closest('div').attr('id');
                
                 if($('#'+parent_div_id+' input').attr("type") == "text"){
                    $('#'+parent_div_id+' input').attr('type', 'password');
                    $('#'+parent_div_id+' i').addClass( "fa-eye-slash" );
                    $('#'+parent_div_id+' i').removeClass( "fa-eye" );
                 }else if($('#'+parent_div_id+' input').attr("type") == "password"){
                    $('#'+parent_div_id+' input').attr('type', 'text');
                    $('#'+parent_div_id+' i').removeClass( "fa-eye-slash" );
                    $('#'+parent_div_id+' i').addClass( "fa-eye" );
                 }
             }); 

            //subrole check/uncheck handler
            $(document).on('ifChanged','.sub_role_single',function(){
                var parent_id = $(this).data('parent-menu');
                var checkbox_class = 'sub_role_'+parent_id;
                var count_checked_checkbox = $('input:checkbox.'+checkbox_class+':checked').length;
                var count_checkbox = $('input:checkbox.'+checkbox_class).length;
                //console.log(count_checkbox+'***'+count_checked_checkbox);
                if(count_checked_checkbox < count_checkbox){
                   $('#show_this_in_menu_'+parent_id).removeAttr('checked');
                   $('#show_this_in_menu_'+parent_id).prop('checked', false);
                   $('#show_this_in_menu_'+parent_id).iCheck('update');
                }else{
                   $('#show_this_in_menu_'+parent_id).removeAttr('checked');
                   $('#show_this_in_menu_'+parent_id).prop('checked', true);
                   $('#show_this_in_menu_'+parent_id).iCheck('update');
                }
             }); 
             
             /*Summernote HTML5 Text Editor*/
             tinyMCE.init({
                selector: 'textarea.tinymce',
                height: 250,
                plugins: "link image media code",
                toolbar: 'undo redo | styleselect | forecolor | bold italic | alignleft aligncenter alignright alignjustify | '+
                         'outdent indent | media | link image | code',
                setup : function(ed){
                     ed.on('NodeChange', function(e){
                         tinyMCE.triggerSave();
                         $("#" + ed.id).valid();
                         //console.log('the event object ' + e);
                         //console.log('the editor object ' + ed);
                         //console.log('the content ' + ed.getContent());
                     });
                }
            });
            /*------- Ends Here ---------*/

            //function to check unique email id for user  
            function check_user_email(user_email){
              
              var fran_row_id = $("#fran_row_id").val();
              var formData = {action:"checkUserEmailAvailability",user_email:user_email,user_type:"franchise",user_id:fran_row_id}
              //Check email validity
              if(user_email.length >0){
                var regularExp = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
                var chkEmail = regularExp.test(user_email);

                if(!chkEmail){
                  toastr.error("Enter a proper email!","Oops!")
                  $('#create').attr('disabled',true);
                  return false;
                }else{
                 
                  var local_user_email =  localStorage.getItem("user_email"); 
                  var checkUniqueInputEmail = local_user_email!=null?(user_email.localeCompare(local_user_email) == 0?false:true):true;

                  if(checkUniqueInputEmail){
                     $.ajax({
                          url:ajaxControllerHandler,
                          method:'POST',
                          data: formData,
                          async:false,
                          beforeSend: function() {
                             //$('.tooltip').hide();
                             $('#manage_franchise_loader').addClass('sk-loading');
                           },
                           success:function(responseData){
                              var result = JSON.parse(responseData);
                              //console.log(result);
                              $('#manage_franchise_loader').removeClass('sk-loading');
                              localStorage.setItem("user_email", user_email);  
                              
                              if(result.check == "success"){
                                $('#create').attr('disabled',false);
                                localStorage.setItem("user_email_check_rspns", "success"); 
                                return true; 
                              }else{
                                toastr.error(result.message, "Error!"); 
                                $('#create').attr('disabled',true);
                                localStorage.setItem("user_email_check_rspns", "failure"); 
                                return false; 
                              }
                           }
                      });
                  }else{
                      var user_email_check_rspns = localStorage.getItem('user_email_check_rspns');

                      if(user_email_check_rspns == "failure"){
                         toastr.error("This email is taken by other franchise, Please try another email.", "Error!"); 
                         return false;
                      }else{
                         return true;
                      }
                  }   
                 
                }
              }else{
                toastr.error("This field is required!","Oops!");
                $('#create').attr('disabled',true);
                return false;
              }
            }

            //Check user email on change
            $(document).on("blur","#fran_email",function(){
               var user_email = $(this).val(); 
               $('.tooltip').remove();
               check_user_email(user_email);
            });

            //verify franchise image file type before uploading into server
            $("#franchiseImage").change(function () {
                var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only formats are allowed : "+fileExtension.join(', '));
                }else{
                   readURL(this,'fran_image'); 
                }
            });

            //verify franchise pdf file type before uploading into server
            $("#franchisePdf").change(function (event) {
                var fileExtension = ['pdf'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only formats are allowed : "+fileExtension.join(', '));
                }else{
                   readURL(this,'fran_pdf');
                   $('#preview_fran_pdf').removeClass('d-none');
                }
            });

            //Handling image upload type checkbox
            $(document).on('ifChanged', '.i-checks.image_upload_type input', function (e) {
                e.preventDefault();

                var image_upload_type = $(this).val();

                if(image_upload_type == 'local'){
                   //show & hide requied div  
                   $('#fran_image_local_div').removeClass('d-none');
                   $('#fran_image_cdn_div').addClass('d-none');
                   $('.image_selection_warning').addClass('d-none');
                }
                else if(image_upload_type == 'cdn'){
                   //show & hide requied div 
                   $('#fran_image_cdn_div').removeClass('d-none');
                   $('#fran_image_local_div').addClass('d-none');
                   $('.image_selection_warning').addClass('d-none'); 
                }else{
                   //show & hide requied div 
                   $('#fran_image_cdn_div').addClass('d-none');
                   $('#fran_image_local_div').addClass('d-none');
                   $('.image_selection_warning').removeClass('d-none'); 
                }
             });

            //Handling pdf upload type checkbox
            $(document).on('ifChanged', '.i-checks.pdf_upload_type input', function (e) {
                e.preventDefault();

                var pdf_upload_type = $(this).val();

                if(pdf_upload_type == 'local'){
                   //show & hide requied div  
                   $('#fran_pdf_local_div').removeClass('d-none');
                   $('#fran_pdf_cdn_div').addClass('d-none');
                   $('.pdf_selection_warning').addClass('d-none');
                }
                else if(pdf_upload_type == 'cdn'){
                   //show & hide requied div 
                   $('#fran_pdf_cdn_div').removeClass('d-none');
                   $('#fran_pdf_local_div').addClass('d-none');
                   $('.pdf_selection_warning').addClass('d-none'); 
                }else{
                   //show & hide requied div 
                   $('#fran_pdf_cdn_div').addClass('d-none');
                   $('#fran_pdf_local_div').addClass('d-none');
                   $('.pdf_selection_warning').removeClass('d-none'); 
                }
             });

            //franchise image url on blur handler 
            $(document).on('blur','#fran_image_cdn',function(){
                 var media_url = $(this).val();

                 //console.log(checkMediaURL(media_url));

                 if(!checkMediaURL(media_url)){
                    toastr.error("Please add a valid url of the image", "Error!"); 
                    media_url = RESOURCE_URL+'images/preview.jpg';
                    return false;  
                 }else{
                    toastr.success("Franchise image is successfully fetched.", "Success!"); 
                 }

                 if(media_url.includes('drive.google.com')) {
                   var formattedUrl = formatGoogleImg(media_url);  
                 }else{
                   var formattedUrl = media_url; 
                 }
                 
                 $('#image_upload_preview').attr('src', formattedUrl);
                 $('#fran_preview_image').attr('href', formattedUrl);
            }); 

            //franchise pdf url on blur handler 
            $(document).on('blur','#fran_pdf_cdn',function(){
                 var media_url = $(this).val();

                 //console.log(checkMediaURL(media_url));

                 if(!checkMediaURL(media_url)){
                    toastr.warning("This isn't a valid url, default url is added", "Warning!"); 
                    formattedUrl = RESOURCE_URL+'images/COMPUTER-COURSE.pdf';
                 }else{
                    toastr.success("Franchise image is successfully fetched.", "Success!"); 
                   
                    if(media_url.includes('drive.google.com')) {
                      var formattedUrl = formatGoogleImg(media_url);  
                    }else{
                      var formattedUrl = media_url; 
                    }
                 }

                 $('#pdfViewer').attr('src', formattedUrl);
                 $('#preview_fran_pdf').removeClass('d-none');      
            }); 

            $(document).on('keyup', '.input_password', function (e) {
                var password = $('#fran_pass').val(); 
                var confirm_password = $('#confirm_fran_pass').val(); 

                $('.tooltip').remove();

                if(!password.length>0 && confirm_password.length>0){
                     $('#fran_pswd_err_msg').text('Please repeat the password entered on confirm password!');
                     $('#fran_cnfrmpass_err_msg').text('');
                     $('#create').attr('disabled',true);
                     return false;
                }

                else if(password.length>0 && !confirm_password.length>0){
                    $('#fran_pswd_err_msg').text('');
                    $('#fran_cnfrmpass_err_msg').text('Please confirm your current password!');
                    $('#create').attr('disabled',true);
                    return false;
                }else{

                    if(password == confirm_password){
                        $('#fran_pswd_err_msg').text('');
                        $('#fran_cnfrmpass_err_msg').text('');
                        $('#create').attr('disabled',false);
                        return true;
                    }else{
                        $('#fran_pswd_err_msg').text('');
                        $('#fran_cnfrmpass_err_msg').text('Password and Confirm Password does not match!');
                        $('#create').attr('disabled',true);
                        return false;
                    }    
                }   
            });

            $(document).on('ifChanged','input[name="user_role[]"]',function(){

                //Validate if user role is empty
                if($('input[name="user_role[]"]:checked').length == 0){
                   toastr.error("User role can't be empty!","Error!"); 
                   $('#create').attr('disabled',true);
                }else{
                   $('#create').attr('disabled',false); 
                }
                
            });

            function validateFranchiseForm(){
               
                var fran_email = $('#fran_email').val();
                var password = $('#fran_pass').val();
                var confirm_password = $('#confirm_fran_pass').val();

                $('.tooltip').remove();

                if( (password.length>0) && (password != confirm_password) ){
                    toastr.error("Password and Confirm Password does not match!","Error!");
                    return false;
                }

                //Validate if user role is empty
                if($('input[name="user_role[]"]:checked').length == 0){
                   toastr.error("User role can't be empty!","Error!"); 
                   $('#create').attr('disabled',true);
                   return false;
                }else{
                   $('#create').attr('disabled',false); 
                }

                //Validate user email
                var validateUserEmail = check_user_email(fran_email);

                if(validateUserEmail != undefined){
                    if(validateUserEmail){
                       $('#create').attr('disabled',false);
                    }else{
                       $('#create').attr('disabled',true);  
                       return false;
                    }
                }else{
                    var user_email_check_rspns = localStorage.getItem('user_email_check_rspns');

                    if(user_email_check_rspns == "success"){
                        $('#create').attr('disabled',false);
                    }else{
                        $('#create').attr('disabled',true); 
                        return false;
                    }
                }

                return true;
            }

            $(document).on('submit', '#manage_franchise_form', function(event){
                event.preventDefault();
                
                var fran_row_id = $('#fran_row_id').val();

                var validateForm = validateFranchiseForm();

                if(!validateForm){
                    return false;
                }

                $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: new FormData(this),
                  contentType:false,
                  processData:false,
                  beforeSend: function() {
                     $('#manage_franchise_loader').addClass('sk-loading');
                     $('#create').attr('disabled',true);
                  },
                  success:function(responseData){
                      var data = JSON.parse(responseData);
                      $('#create').attr('disabled',false);
                     //console.log(responseData);
                     if(data.check == 'success'){
                        //reseting form data
                        $('#manage_franchise_form')[0].reset();
                        //Clearing textarea, tagsinput & Dropdowns
                        $('.note-editable').html('');
                        //Clearing image preview data
                        $('#image_upload_preview').attr('src', '<?=RESOURCE_URL.'images/preview.jpg'?>');
                        //Disabling loader
                        $('#manage_franchise_loader').removeClass('sk-loading');
                        //show sweetalert success

                        if(data.last_insert_id>0){
                          var successText = "Franchise has been successfully created!";
                          var redirect_url = SITE_URL+"?route=edit_franchise&id="+data.last_insert_id;
                        }else{
                          var successText = "Franchise has been successfully updated!";
                          var redirect_url = SITE_URL+"?route=edit_franchise&id="+fran_row_id;
                        } 

                        swal({
                            title: "Great!",
                            text: successText,
                            type: "success"
                        },function() {
                            window.location = redirect_url;
                        });
                        return true; 
                     }else{
                       //Disabling loader
                        $('#manage_franchise_loader').removeClass('sk-loading');
                        //show sweetalert success
                         if(data.message.length>0){
                           var message = data.message;
                        }else{
                           var message = "Something went wrong";
                        }
                        swal({
                            title: "Oops!",
                            text: message,
                            type: "error"
                        });
                        return false;
                     }
                  }
                 });
             
             });

          });
     </script>

