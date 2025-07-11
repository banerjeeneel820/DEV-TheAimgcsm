<?php
  
  if(isset($_GET['id'])){
    $stu_row_id = $_GET['id'];
    $studentDetailArr = $pageContent['pageData']['student_data'];

    //Configuring franchise file data
    if(strlen($studentDetailArr->image_file_name)>0 && file_exists(USER_UPLOAD_DIR.'student/'.$studentDetailArr->image_file_name)){
       $student_thumbnail = USER_UPLOAD_URL.'student/'.$studentDetailArr->image_file_name;
    }else{
       $student_thumbnail = RESOURCE_URL.'images/preview.jpg';
    }
  }else{
    $stu_row_id = 'null';
  }
  //Franchise data
  $franchiseArr = $pageContent['pageData']['franchise_data'];
  //Course data
  $courseArr = $pageContent['pageData']['course_data']; 

  $updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_student"); 

  if($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'developer' && $updatePermission == true){
     $statusUpdatePermission = true;
  }
  
  elseif($_SESSION['user_type'] == 'franchise' && $_SESSION['owned_status'] == 'yes' && $updatePermission == true){
     $statusUpdatePermission = true;
  }else{
     $statusUpdatePermission = false;
  }
  
  /*print"<pre>";
  print_r($pageContent['student_data']);
  print"</pre>";*/

?>
     <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>General Student Details</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>   

                        <div class="ibox-content" id="manage_student_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <form class="needs-validation" id="manage_student_form" method="post" onsubmit="return false;" novalidate>
                              <input type="hidden" name="action" id="action" value="manageGlobalStudent">
                              <input type="hidden" name="stu_row_id" id="stu_row_id" value="<?=(isset($studentDetailArr)?$studentDetailArr->id:'null')?>">
                              <input type="hidden" name="stu_id" id="stu_id" value="<?=(isset($studentDetailArr)?$studentDetailArr->stu_id:'null')?>">
                              <input type="hidden" name="action_type" id="action_type" value="<?=(isset($studentDetailArr)?'update':'create')?>">
                              
                              <strong> 
                               <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Student Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_name" placeholder="Enter Student Name..." value="<?=(isset($studentDetailArr)?$studentDetailArr->stu_name:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>
                               
                               <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Father Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student's Father Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_father_name" placeholder="Enter Student's Father Name..." value="<?=(isset($studentDetailArr)?$studentDetailArr->stu_father_name:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right">
                                    <label class="col-sm-2 col-form-label">Student Contact No <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Contact No"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="number" class="form-control" name="stu_phone" placeholder="Enter Student Contact No..." value="<?=(isset($studentDetailArr)?$studentDetailArr->stu_phone:'')?>" required>
                                     </div>   
                                    </div>

                                    <label class="col-sm-2 col-form-label">Student ID <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student ID"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="text" class="form-control" value="<?=(isset($studentDetailArr)?$studentDetailArr->stu_id:'')?>" readonly>
                                      </div>   
                                    </div>

                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Student Email <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Email"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="email" class="form-control" id="stu_email" name="stu_email" placeholder="Enter Student Email..." value="<?=(isset($studentDetailArr)?$studentDetailArr->stu_email:'')?>">
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Student Image <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                   <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                      <?php if(isset($_GET['id'])){ ?>  
                                         <div class="col-sm-2 pb-3 pl-0">
                                            <h4>Current image</h4>
                                            <a href="<?=$student_thumbnail?>" data-fancybox="gallery" data-caption="<?=$studentDetailArr->stu_name?>">  
                                                <img id="current_image_review" src="<?=$student_thumbnail?>" alt="student image" style="height: 100px;width: 100px;" />
                                            </a>  
                                          </div>
                                        <?php } ?> 

                                        <div class="<?php (isset($_GET['id']) ? 'col-sm-10' : 'col-sm-12')?> pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="stu_preview_image" data-fancybox="gallery" data-caption="<?=$studentDetailArr->stu_name?>"> 
                                           <img id="image_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                      
                                      <div class="btn-group">
                                          <label title="Upload a file" for="studentImage" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="local_stu_image" id="studentImage" class="hide" />
                                              Upload a featured image of the student...
                                          </label>    
                                      </div>

                                    </div>
                                    <input type="hidden" name="hidden_stu_image" value="<?=$studentDetailArr->image_file_name?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Student Address <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Address"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_address" placeholder="Enter Student Address..." value="<?=(isset($studentDetailArr)?$studentDetailArr->stu_address:'')?>">
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                      <label class="col-sm-2 col-form-label text-right">Select Franchise <span class=" cursor-pointer" data-toggle="tooltip" data-placement="left" title="Select a Franchise for student from Dropdown"><i class="fa fa-question-circle"></i></span></label>
                                      <div class="col-sm-10 pr-0" id="state_loader_div">
                                        <div class="input-group">
                                            <select class="franchise" name="franchise_id" id="franchise_id" data-placeholder="Choose a Franchise first..." tabindex="2" <?=($_SESSION['user_type'] == 'franchise'?'disabled':'')?> required>
                                              <option></option>
                                               <?php foreach($franchiseArr as $franchise){ 
                                              ?>
                                                <option value="<?=$franchise->id?>" <?=($_SESSION['user_type'] == 'franchise'?($_SESSION['user_id'] == $franchise->id?'selected':''):($franchise->id == $studentDetailArr->franchise_id ?'selected':''))?>><?=$franchise->center_name?></option>
                                              <?php } ?>
                                           </select>
                                       </div>

                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Select Course <span class=" cursor-pointer" data-toggle="tooltip" data-placement="left" title="Select a course for student from Dropdown"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10 pr-0" id="state_loader_div">
                                        <div class="input-group">
                                            <select class="course" name="course_id" id="course_id" data-placeholder="Choose a Course first..." tabindex="2"required>
                                              <option></option>
                                               <?php foreach($courseArr as $course){ 
                                              ?>
                                                <option value="<?=$course->id?>" <?=($course->id == $studentDetailArr->course_id ?'selected':'')?>><?=$course->course_title?></option>
                                              <?php } ?>
                                           </select>
                                           <input type="hidden" name="course_name" id="course_name" value="">
                                       </div>
                                    </div>
                                </div>    
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Student Date of Birth <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose Student Gender"><i class="fa fa-question-circle"></i></span></label>

                                    <div class="col-sm-4">
                                        <div class="input-group date">
                                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                            <input type="text" class="form-control" name="stu_dob" id="stu_dob" value="<?=(isset($studentDetailArr)?date('d-m-Y',strtotime($studentDetailArr->stu_dob)):'')?>" autocomplete="off">
                                          <span id="stu_dob_err_msg" style="color: red;"></span>
                                        </div>
                                    </div>

                                    <label class="col-sm-2 col-form-label text-right">Record Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose Student Record Status"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-1">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="record_status" <?=($studentDetailArr->record_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" name="record_status" <?=($studentDetailArr->record_status)=='blocked'?'checked':''?> required> <i></i> Blocked </label>
                                    </div>
                                 </div>   
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Student Gender <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose Student Gender"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-1">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="male" name="stu_gender" <?=($studentDetailArr->stu_gender)=='male'?'checked':''?> /> <i></i>Male </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="female" name="stu_gender" <?=($studentDetailArr->stu_gender)=='female'?'checked':''?>> <i></i> Female </label>
                                    </div>

                                    <label class="col-sm-2 col-form-label text-right">Merital Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose Student Merital Status"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-1">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="married" name="stu_marital_status" <?=($studentDetailArr->stu_marital_status)=='married'?'checked':''?> /> <i></i>Married </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="unmarried" name="stu_marital_status" <?=($studentDetailArr->stu_marital_status)=='unmarried'?'checked':''?>> <i></i> Unmarried </label>
                                    </div>
                                 </div>   
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                  <label class="col-sm-2 col-form-label text-right">Qualification <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Qualification"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_qualification" placeholder="Enter Student Qualification..." value="<?=(isset($studentDetailArr)?$studentDetailArr->stu_qualification:'')?>">
                                     </div>   
                                    </div>

                                     <label class="col-sm-2 col-form-label text-right">Conversion Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose If this student is upgraded to higher level of current course or not"><i class="fa fa-question-circle"></i></span></label>
                                     <div class="col-sm-4 pt-1">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="0" name="conversion_status" <?=($studentDetailArr->conversion_status)=='0'?'checked':''?> /> <i></i>Recent </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="1" name="conversion_status" <?=($studentDetailArr->conversion_status)=='1'?'checked':''?>> <i></i> Converted </label>
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>
                                
                                <?php if($statusUpdatePermission) { ?>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label text-right">Total Course Fees <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Total Course Fees of Student"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="stu_course_fees" placeholder="Enter Student's Total Course Fees..." value="<?=(isset($studentDetailArr)?$studentDetailArr->stu_course_fees:'')?>" required>
                                         </div>   
                                        </div>

                                        <label class="col-sm-2 col-form-label text-right">Monthly Course Fees <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Total Course Discount"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="monthly_course_fees" placeholder="Enter Student's Monthly Course Fees..." value="<?=(isset($studentDetailArr)?$studentDetailArr->monthly_course_fees:'')?>" required>
                                         </div>   
                                        </div> 
                                    </div>    

                                    <div class="hr-line-dashed"></div>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label text-right">Course Discount <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Total Course Discount"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="stu_course_discount" placeholder="Enter Student's Course Disount..." value="<?=(isset($studentDetailArr)?$studentDetailArr->stu_course_discount:'')?>">
                                         </div>   
                                        </div> 

                                        <label class="col-sm-2 col-form-label text-right">Fees Paid before DR <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Total Course Fees Paid before Digital Receipt by the Student"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="fees_paid_before_dr" placeholder="Enter Total Course Fees Paid before Digital Receipt by the Student..." value="<?=(isset($studentDetailArr)?$studentDetailArr->fees_paid_before_dr:'')?>">
                                         </div>   
                                        </div>
                                    </div>
                                    
                                    <div class="hr-line-dashed"></div>    
                                <?php } ?>    
                                
                                <?php if($statusUpdatePermission){ ?>
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label text-right">Student Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose Student Record Status"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4 pt-1">
                                          <select class="form-control-sm form-control input-s-sm inline student_status" name="student_status" id="student_status" required>
                                            <option selected="" disabled="" value="">Select a Student Status</option>
                                            <option value="admitted" <?=($studentDetailArr->student_status)=='admitted'?'selected':''?>>Admitted</option>  
                                            <option value="continue" <?=($studentDetailArr->student_status)=='continue'?'selected':''?>>Continue</option>
                                            <option value="course_complete" <?=($studentDetailArr->student_status)=='course_complete'?'selected':''?>>Course Complete</option>
                                            <option value="dropout" <?=($studentDetailArr->student_status)=='dropout'?'selected':''?>>Dropout</option>  
                                          </select>
                                        </div>

                                        <label class="col-sm-2 col-form-label text-right">Months Exclude from Receipt <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Total Course Fees Paid before Digital Receipt by the Student"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="month_exclude_receipt" placeholder="Enter Months to Exclude from Receipt..." value="<?=(isset($studentDetailArr)?$studentDetailArr->month_exclude_receipt:'')?>">
                                          </div>   
                                        </div>
                                        <div class="hr-line-dashed"></div> 
                                    </div>    
                                    <div class="hr-line-dashed"></div>  

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label text-right">Student Result <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose Student Record Status"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4 pt-1">
                                           <label class="checkbox-inline i-checks pl-2"> <input type="radio" value="qualified" name="stu_result" <?=($studentDetailArr->stu_result)=='qualified'?'checked':''?> required> <i></i> Qualified </label>
                                           <label class="checkbox-inline i-checks pl-2"> <input type="radio" value="unqualified" name="stu_result" <?=($studentDetailArr->stu_result)=='unqualified'?'checked':''?> required> <i></i> Unqualified </label>
                                        </div>

                                        <label class="col-sm-2 col-form-label text-right">Clone Student <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose if you want to clone this student"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4 pt-1">
                                           <label class="checkbox-inline i-checks pl-2"> <input type="radio" value="yes" name="clone_student" required> <i></i> Yes </label>
                                           <label class="checkbox-inline i-checks pl-2"> <input type="radio" value="no" name="clone_student" checked required> <i></i> No </label>
                                        </div>
                                    </div>
                                    <div class="hr-line-dashed"></div>       
                                <?php } ?>       

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Student Notes <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter student notes if any"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                        <textarea class="tinymce" name="stu_notes">
                                           <?=(!empty($studentDetailArr)?stripslashes($studentDetailArr->stu_notes):'')?>
                                        </textarea>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                                </strong>

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=view_students" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
                                        <button class="btn btn-success btn-sm" id="create" type="submit" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="Save"><i class="fa fa-save"></i> Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div> 

       
        <!-- Custom JS -->
       <script>
          function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader(); 

                reader.onload = function (e) {
                   $('#image_upload_preview').attr('src', e.target.result);
                   $('#stu_preview_image').attr('href', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
         } 

         function checkMediaURL(url) {
            return(url.match(/\.(jpeg|jpg|gif|png|pdf)$/) != null);
         }

         $(document).ready(function () {
             //Declaring datepicker triiger validation variable
             var trigger = false;

             $('.i-checks').iCheck({
                 checkboxClass: 'icheckbox_square-green',
                 radioClass: 'iradio_square-green',
             });

            /*---Input date & time control--*/
              $('.input-group.date').datepicker({
                  format: "dd/mm/yyyy",
                  todayBtn: "linked",
                  keyboardNavigation: true,
                  todayHighlight: true,
                  //startDate: today,
                  forceParse: false,
                  calendarWeeks: true,
                  autoclose: true
              }).on("changeDate", function (e) {
                  
                  var today = new Date();

                  var stuDobStr = $('#stu_dob').val(); 
                  var dateParts = stuDobStr.split("/");
                  var stuDobObj = new Date(+dateParts[2], dateParts[1] - 1, +dateParts[0]); 

                  var stu_age = new Date(new Date() - stuDobObj).getFullYear() - 1970;
                  
                  //console.log(stu_age);    
                  if(stu_age<5){
                    $('#stu_dob_err_msg').text('Student age must be at least 5 to enter the course!');
                    $('#stu_dob').val('');
                    $('#create').attr('disabled',true);
                  }else{
                    $('#stu_dob_err_msg').text('');
                    $('#create').attr('disabled',false);
                  }
                  return false;
             });
            /*------- Ends Here ---------*/ 

            //Multiple select course
            $('.course').select2({width: "98.5%",allowClear:true});
            $('.franchise').select2({width: "98.5%",allowClear:true});

            $('.course,.franchise').on('change',function(){
                $(this).valid();
            });
             
             /*Tinymce HTML5 Text Editor*/
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
              var stu_row_id = $("#stu_row_id").val();
              var formData = {action:"checkUserEmailAvailability",user_email:user_email,user_type:"student"}
              //Check email validity
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
               }else{
                 //console.log(formData); 
                 if(stu_row_id == "null"){
                    $.ajax({
                      url:ajaxControllerHandler,
                      method:'POST',
                      data: formData,
                      beforeSend: function() {
                         //$('.tooltip').hide();
                         $('#manage_student_loader').addClass('sk-loading');
                       },
                       success:function(responseData){
                          var result = JSON.parse(responseData);
                          //console.log(result);
                          $('#manage_student_loader').removeClass('sk-loading');
                          if(result.check == "success"){
                            $('#create').attr('disabled',false);
                            return true; 
                          }else{
                            toastr.error(result.message, "Error!"); 
                            $('#create').attr('disabled',true);
                            return false; 
                          }
                          
                       }
                   });
                 }else{
                    $('#create').attr('disabled',false);
                    return true;
                 }

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

            //verify franchise image file type before uploading into server
            $("#studentImage").change(function () {
                var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only formats are allowed : "+fileExtension.join(', '));
                }else{
                   readURL(this); 
                }
            });

            $(document).on('submit', '#manage_student_form', function(event){
                event.preventDefault();
                
                var stu_row_id = parseInt($('#stu_row_id').val());
                //var stu_email = $('#stu_email').val();

               //Checking if the email is valid
               //check_user_email(stu_email);
               var clone_student = $('input[name="clone_student"]:checked').val(); 
               
               //Populating course name input value
               var courseData = $('#course_id').select2('data');
               $("#course_name").val(courseData[0].text);

               $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: new FormData(this),
                  contentType:false,
                  processData:false,
                  beforeSend: function() {
                     $('#manage_student_loader').addClass('sk-loading');
                     $('#create').attr('disabled',true);
                  },
                  success:function(responseData){
                      var data = JSON.parse(responseData);
                      $('#create').attr('disabled',false);
                     //console.log(responseData);
                     if(data.check == 'success'){
                        //reseting form data
                        $('#manage_student_form')[0].reset();
                        //Clearing textarea, tagsinput & Dropdowns
                        $('.note-editable').html('');
                        //Clearing image preview data
                        $('#image_upload_preview').attr('src', 'http://placehold.it/100x100');
                        //Disabling loader
                        $('#manage_student_loader').removeClass('sk-loading');
                        //show sweetalert success

                        if(data.last_insert_id>0){
                          var successText = "<b>Student has been successfully created!<br> Your student id is :- "+data.stu_id+
                                            "<br> Student course is :- "+data.course+'</b>';
                          //var redirect_url = SITE_URL+"?route=edit_student&id="+data.last_insert_id;
                          var student_id = data.last_insert_id;
                        }else{
                          var successText = "<b>Student has been successfully updated!<br> Your student id is :- "+data.stu_id+
                                            "<br> Student course is :- "+data.course+'</b>';
                          //var redirect_url = SITE_URL+"?route=edit_student&id="+stu_row_id;
                          var student_id = data.stu_id;
                        } 
                        
                        if(stu_row_id>0){

                            if(clone_student == "yes"){
                               var redirect_url = SITE_URL+"?route=clone_student&id="+student_id;
                            }else{
                               var redirect_url = SITE_URL+"?route=view_students";
                            }

                        }else{
                            if(clone_student == "yes"){
                               var redirect_url = SITE_URL+"?route=clone_student&id="+student_id;
                            }else{
                               var redirect_url = SITE_URL+"?route=add_student";
                            }
                        }

                        swal({
                            title: "Great!",
                            html:true,
                            text: successText,
                            type: "success",
                            allowEscapeKey : false,
                            allowOutsideClick: false
                        },function() {
                            //window.location = redirect_url;
                            window.open(redirect_url, "_blank");
                            location.reload();
                        });
                        return true; 
                     }else{
                       //Disabling loader
                        $('#manage_student_loader').removeClass('sk-loading');
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
  </body>
