<?php
  $profileDataArr = $pageContent['pageData']['profile_data'];
  //Configuring user role array
  $userRoleArr = unserialize($profileDataArr->user_role);
  /*print"<pre>";
  print_r($profileDataArr);
  print"</pre>";*/

?>
         
        <div class="wrapper wrapper-content animated fadeInRight"> 
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Edit Profile Data</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content" id="edit_profile_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <form id="manage_profile_form" class="needs-validation" method="post" onsubmit="return false;">
                              <input type="hidden" name="action" id="action" value="manageProfileData">
                              <input type="hidden" name="page_route" id="page_route" value="<?=$_GET['route']?>">

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Admin Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Admin Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="user_nicename" placeholder="Enter Admin Name..." value="<?=(isset($profileDataArr)?$profileDataArr->user_nicename:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>
                               

                                <div class="form-group row text-right">
                                    <label class="col-sm-2 col-form-label">Admin Contact No <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Admin Contact No"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="number" class="form-control" name="user_contact" placeholder="Enter Admin Contact No..." value="<?=(isset($profileDataArr)?$profileDataArr->user_contact:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Admin Email <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Admin Email"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="email" class="form-control" id="user_email" name="user_email" placeholder="Enter Admin Email..." value="<?=(isset($profileDataArr)?$profileDataArr->user_email:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <?php if($_SESSION['user_type'] == "developer"){ ?> 
                                    <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Admin Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Status"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-9 pt-2">
                                           <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="user_status" <?=($profileDataArr->user_status)=='active'?'checked':''?> /> <i></i>Active </label>
                                           <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" name="user_status" <?=($profileDataArr->user_status)=='blocked'?'checked':''?>> <i></i> Blocked </label>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>
                                <?php } ?>    

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Admin Password <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Admin Password"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group show_hide_password" id="input_password_div">
                                        <input type="password" class="form-control input_password" id="user_pass" name="user_pass" placeholder="Enter Admin Password..." autocomplete="off">
                                         <div class="input-group-addon">
                                            <a href="javascript:void(0);"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                         </div>
                                     </div>  
                                     <span id="user_pswd_err_msg" style="color: red;"></span>  
                                     <input type="hidden" name="user_hidden_password" value="<?=$profileDataArr->user_pass?>">  
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Repeat Password <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Repeat Admin Password"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group show_hide_password" id="confirm_password_div">
                                        <input type="password" class="form-control input_password" id="confirm_user_pass" name="confirm_user_pass" autocomplete="off" placeholder="Repeat Admin Password...">

                                         <div class="input-group-addon">
                                            <a href="javascript:void(0);"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                         </div>
                                     </div> 
                                     <span id="user_cnfrmpass_err_msg" style="color: red;"></span>     
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="forum-title pr-38 border-bottom mb-4">
                                   <h5>User Permission Management</h5> 
                                </div>  

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Dashboard <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select dashboard to show in menu for this user"><i class="fa fa-question-circle"></i></span></label>

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
                                          <label class="i-checks pl-2 pt-2" for="show_this_in_menu_receipt"> <input type="checkbox" data-menu="enquiry" class="show_role_menu" id="show_this_in_menu_enquiry"> Check all options </label>
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

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=edit_profile" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
                                        <button class="btn btn-success btn-sm" id="update" type="submit" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="Save"><i class="fa fa-save"></i> Save</button>
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
                       <iframe src="" title="W3Schools Free Online Web Tutorials" id='pdfViewer' width="750" height="460"></iframe>
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

         $(document).ready(function () {

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

             //show_in_this_menu check/uncheck handler
             $(document).on('ifChanged','.show_role_menu',function(){
                var parent_id = $(this).data('menu');
                if (this.checked) {
                  $('.sub_role_'+parent_id).iCheck('check');
                }else{
                  $('.sub_role_'+parent_id).iCheck('uncheck');
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
                  $('#update').attr('disabled',true);
                  return false;
               }
              }else{
                swal({
                   title: "Oops!",
                   text: "This field is required!",
                   type: "error"
                });
                $('#update').attr('disabled',true);
                return false;
              }
            }

            $(document).on('keyup', '.input_password', function (e) {
                var password = $('#user_pass').val(); 
                var confirm_password = $('#confirm_user_pass').val(); 

                $('.tooltip').remove();

                if(!password.length>0 && confirm_password.length>0){
                     $('#user_pswd_err_msg').text('Please repeat the password entered on confirm password!');
                     $('#user_cnfrmpass_err_msg').text('');
                     $('#update').attr('disabled',true);
                     return false;
                }

                else if(password.length>0 && !confirm_password.length>0){
                    $('#user_pswd_err_msg').text('');
                    $('#user_cnfrmpass_err_msg').text('Please confirm your current password!');
                    $('#update').attr('disabled',true);
                    return false;
                }else{

                    if(password == confirm_password){
                        $('#user_pswd_err_msg').text('');
                        $('#user_cnfrmpass_err_msg').text('');
                        $('#update').attr('disabled',false);
                        return true;
                    }else{
                        $('#user_pswd_err_msg').text('');
                        $('#user_cnfrmpass_err_msg').text('Password and Confirm Password does not match!');
                        $('#update').attr('disabled',true);
                        return false;
                    }    
                }   
            });

            function validateFranchiseForm(){
               
                var user_email = $('#user_email').val();
                var password = $('#user_pass').val();
                var confirm_password = $('#confirm_user_pass').val();

                $('.tooltip').remove();

                if( (password.length>0) && (password != confirm_password) ){
                    toastr.error("Password and Confirm Password does not match!","Error!");
                    return false;
               }else{
                  return true;
               }

               //Validate user email
               var validateUserEmail = check_user_email(user_email);

               if(validateUserEmail){
                  $('#update').attr('disabled',false);
                  return true;
               }else{
                  $('#update').attr('disabled',true);  
                  return false;
               }
              
            }

            $(document).on('submit', '#manage_profile_form', function(event){
                event.preventDefault();
                
                var page_route = $('#page_route').val();
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
                     //$('#edit_profile_loader').addClass('sk-loading');
                     //$('#update').attr('disabled',true);
                  },
                  success:function(responseData){
                      var data = JSON.parse(responseData);
                      $('#update').attr('disabled',false);
                     //console.log(responseData);
                     if(data.check == 'success'){
                        //reseting form data
                        $('#manage_profile_form')[0].reset();
                        //Disabling loader
                        $('#edit_profile_loader').removeClass('sk-loading');
                        //show sweetalert success
                        var successText = "Profile has been successfully updated!";
                        var redirect_url = SITE_URL+"?route="+page_route;
                        
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
                        $('#edit_profile_loader').removeClass('sk-loading');
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

