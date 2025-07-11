<?php
  
  if($_GET['actionType'] == 'manage_student'){
      if(isset($_GET['student_id'])){
        $student_id = $_GET['student_id'];
        $studentDetails = $pageContent['pageData']['student_data'];
        $stu_id = $studentDetails->stu_id;
      }
      elseif(isset($_GET['tmp_id'])){
        $tmp_id = $_GET['tmp_id']; 
        $studentDetails = $pageContent['pageData']['student_data'];
      }else{
        $student_id = 'null';
        $stu_id = 'null';
        $studentDetails = array();
      }

      //Franchise data
      $franchiseArr = $pageContent['pageData']['franchise_data'];
      //Course data
      $courseArr = $pageContent['pageData']['course_data']; 

      //Fetching receipt category list
      $categoryList = $pageContent['pageData']['category_data']; 
  }else{
     $studentList = $pageContent['pageData']['student_list'];
  }    

  //Fetching page action permission
  $viewPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("view_student"); 
  $createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_student"); 
  $updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_student"); 

  $updateReceiptPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_receipt"); 
  
  /*print"<pre>";
  print_r($studentDetails);
  print"</pre>";*/

?>
     <div class="wrapper wrapper-content animated fadeInRight">

            <?php if(!empty($student_id)){ ?>
                <div class="row <?=(!empty($student_id)?'':'d-none')?>" id="manage_student_form_div">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Student Admission Form</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>   

                        <div class="ibox-content content_div_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <strong>
                            <form id="manage_admission_form" class="needs-validation" method="post" onsubmit="return false;" novalidate>
                              <input type="hidden" name="action" id="action" value="manageStudentAdmission">
                              <input type="hidden" name="student_id" id="student_id" value="<?=$student_id?>">
                              <input type="hidden" name="tmp_id" id="tmp_id" value="null">
                              <input type="hidden" name="stu_id" id="stu_id" value="<?=(!empty($stu_id)?$stu_id:'null')?>">

                               <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Student Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_name" placeholder="Enter Student Name..." value="<?=(isset($studentDetails)?$studentDetails->stu_name:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Father's Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student's Father Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_father_name" placeholder="Enter Student's Father Name..." value="<?=(isset($studentDetails)?$studentDetails->stu_father_name:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Student Contact No <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student's Phone No"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_phone" placeholder="Enter Student's Phone No..." value="<?=(isset($studentDetails)?$studentDetails->stu_phone:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Select Course <span class=" cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select a course for student from Dropdown"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10 pr-0" id="state_loader_div">
                                        <div class="input-group">
                                            <select class="course_id" name="course_id" id="course_id" data-placeholder="Choose a Course first..." tabindex="2"required>
                                              <option></option>
                                               <?php foreach($courseArr as $course){ 
                                              ?>
                                                <option value="<?=$course->id?>" <?=($course->id == $studentDetails->course_id ?'selected':'')?>><?=$course->course_title?></option>
                                              <?php } ?>
                                           </select>
                                           <input type="hidden" name="course_name" id="course_name" value="">
                                       </div>
                                    </div>
                                </div>    

                                <div class="hr-line-dashed"></div>  

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Select Franchise <span class=" cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select a Franchise for student from Dropdown"><i class="fa fa-question-circle"></i></span></label>
                                      <div class="col-sm-4 pr-0" id="state_loader_div">
                                        <div class="input-group">
                                            <select class="franchise_id" name="franchise_id" id="franchise_id" data-placeholder="Choose a Franchise first..." tabindex="2" <?=($_SESSION['user_type'] == 'franchise'?'disabled':'')?> required>
                                              <option></option>
                                               <?php foreach($franchiseArr as $franchise){ 
                                              ?>
                                                <option value="<?=$franchise->id?>" <?=(!empty($_GET['student_id'])?($franchise->id == $studentDetails->franchise_id?'selected':''):($franchise->id == $_SESSION['user_id']?'selected':''))?>><?=$franchise->center_name?></option>
                                              <?php } ?>
                                           </select>
                                       </div>
                                    </div>
                                    
                                    <?php if(empty($_GET['student_id'])) { ?>  
                                        <label class="col-sm-2 col-form-label text-right">Receipt Category <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Student's Receipt Category"><i class="fa fa-question-circle"></i></span></label>
                                             
                                        <div class="col-sm-4">
                                           <div class="input-group">
                                                <?php if($_SESSION['user_type'] != 'franchise'){ ?>
                                                    <select class="category_id" name="category_id" id="category_id" data-placeholder="Choose a Category..." tabindex="2" required>
                                                      <option></option>
                                                       <?php foreach($categoryList as $category){ 
                                                      ?>
                                                        <option value="<?=$category->id?>" <?=(stripos($category->name, 'admiss') !== false ?'selected':'')?>><?=$category->name?></option>
                                                      <?php } ?>
                                                   </select>
                                                <?php }else{ ?>    
                                                    <?php 
                                                       foreach($categoryList as $category){ 
                                                          if(stripos($category->name, 'admiss') !== false){
                                                    ?>
                                                      <input type="text" class="form-control" id="category_txt" value="<?=$category->name?>" readonly>
                                                      <input type="hidden" name="category_id" value="<?=$category->id?>"> 
                                                    <?php } } ?>
                                                <?php } ?>    
                                           </div>
                                        </div>
                                    <?php } ?>    
                                </div>   
                                <div class="hr-line-dashed"></div>


                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Total Course Fees <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Total Course Fees of Student"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_course_fees" placeholder="Enter Student's Total Course Fees..." value="<?=(isset($studentDetails)?$studentDetails->stu_course_fees:'')?>">
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
                                        <input type="text" class="form-control" name="stu_course_discount" placeholder="Enter Student's Course Disount..." value="<?=(isset($studentDetails)?$studentDetails->stu_course_discount:'')?>">
                                     </div>   
                                    </div> 

                                    <label class="col-sm-2 col-form-label text-right">Fees Paid before DR <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Total Course Fees Paid before Digital Receipt by the Student"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="fees_paid_before_dr" placeholder="Enter Total Course Fees Paid before Digital Receipt by the Student..." value="<?=(isset($studentDetails)?$studentDetails->fees_paid_before_dr:'')?>">
                                     </div>   
                                    </div>
                                </div>
                                
                                <div class="hr-line-dashed"></div>


                                <?php if(empty($_GET['student_id'])) { ?>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label text-right">Admission Fees <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student's Receipt Amount"><i class="fa fa-question-circle"></i></span></label>
                                       
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="receipt_amount" id="og_receipt_amount" placeholder="Enter Student's Receipt Amount..." value="">
                                         </div>   
                                        </div>

                                        <label class="col-sm-2 col-form-label text-right">Registration Fees <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter any additional if any"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="extra_fees" id="extra_fees" placeholder="Enter any additional if any..." value="">
                                         </div>   
                                        </div>

                                    </div>                               
                                    <div class="hr-line-dashed"></div>

                               <?php } ?>   

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=student_admission"><button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Close Create Student Form" data-original-title="Save"><i class="fa fa-reply"></i></button></a>
                                        
                                        <?php if(empty($_GET['student_id'])){ ?> 
                                            <button type="button" class="btn btn-warning btn-sm" id="preview_receipt" data-toggle="tooltip" title="Preview Receipt Data"><i class="fa fa-eye"></i> Preview Receipt</button>
                                        <?php } ?>    

                                        <button class="btn btn-primary btn-sm" id="manage" type="submit" data-toggle="tooltip" title="<?=(!empty($_GET['student_id'])?'Update Student':'Create Student')?>" class="btn btn-success" data-original-title="Save"><i class="fa fa-save"></i> <?=(!empty($_GET['student_id'])?'Update Student':'Create Student')?></button>
                                    </div>
                                </div>
                            </form>
                            </strong>
                        </div>
                    </div>
                </div>
             </div>
            <?php } ?> 
            
            <?php if(!empty($tmp_id)){ ?> 
                <div class="row <?=(!empty($tmp_id)?'':'d-none')?>" id="manage_student_form_div">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Convert Temporary Student To Parmanent Form</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>   

                        <div class="ibox-content content_div_loader">
                           <div class="sk-spinner sk-spinner-wave">
                               <div class="sk-rect1"></div>
                               <div class="sk-rect2"></div>
                               <div class="sk-rect3"></div>
                               <div class="sk-rect4"></div>
                               <div class="sk-rect5"></div>
                            </div>
                            <strong>
                            <form id="manage_admission_form" class="needs-validation" method="post" onsubmit="return false;" novalidate>
                              <input type="hidden" name="action" id="action" value="manageStudentAdmission">
                              <input type="hidden" name="student_id" id="student_id" value="null">
                              <input type="hidden" name="stu_id" id="stu_id" value="null">
                              <input type="hidden" name="tmp_id" id="tmp_id" value="<?=$tmp_id?>">

                               <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Student Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_name" placeholder="Enter Student Name..." value="<?=(isset($studentDetails)?$studentDetails->stu_name:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Student Father Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student's Father Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_father_name" placeholder="Enter Student's Father Name..." value="<?=(isset($studentDetails)?$studentDetails->stu_father_name:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Student Contact No <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student's Phone No"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="stu_phone" placeholder="Enter Student's Phone No..." value="<?=(isset($studentDetails)?$studentDetails->stu_phone:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Select Course <span class=" cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select a course for student from Dropdown"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pr-0" id="state_loader_div">
                                        <div class="input-group">
                                            <select class="course_id" name="course_id" id="course_id" data-placeholder="Choose a Course first..." tabindex="2"required>
                                              <option></option>
                                               <?php foreach($courseArr as $course){ 
                                              ?>
                                                <option value="<?=$course->id?>" <?=($course->id == $studentDetails->course_id ?'selected':'')?>><?=$course->course_title?></option>
                                              <?php } ?>
                                           </select>
                                           <input type="hidden" name="course_name" id="course_name" value="">
                                       </div>
                                    </div>

                                    <label class="col-sm-2 col-form-label text-right">Select Franchise <span class=" cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select a Franchise for student from Dropdown"><i class="fa fa-question-circle"></i></span></label>
                                      <div class="col-sm-4 pr-0" id="state_loader_div">
                                        <div class="input-group">
                                            <select class="franchise_id" name="franchise_id" id="franchise_id" data-placeholder="Choose a Franchise first..." tabindex="2" <?=($_SESSION['user_type'] == 'franchise'?'disabled':'')?> required>
                                              <option></option>
                                               <?php foreach($franchiseArr as $franchise){ 
                                              ?>
                                                <option value="<?=$franchise->id?>" <?=(!empty($_GET['tmp_id'])?($franchise->id == $studentDetails->franchise_id?'selected':''):($franchise->id == $_SESSION['user_id']?'selected':''))?>><?=$franchise->center_name?></option>
                                              <?php } ?>
                                           </select>
                                       </div>
                                    </div>
                                </div>    

                                <div class="hr-line-dashed"></div>  

                                <div class="form-group row">
                                    <?php if(empty($_GET['student_id'])) { ?>  
                                        <label class="col-sm-2 col-form-label text-right">Receipt Category <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Student's Receipt Category"><i class="fa fa-question-circle"></i></span></label>
                                             
                                        <div class="col-sm-4">
                                           <div class="input-group">
                                                <?php if($_SESSION['user_type'] != 'franchise'){ ?>
                                                    <select class="category_id" name="category_id" id="category_id" data-placeholder="Choose a Category..." tabindex="2" required>
                                                      <option></option>
                                                       <?php foreach($categoryList as $category){ 
                                                      ?>
                                                        <option value="<?=$category->id?>" <?=(stripos($category->name, 'admiss') !== false ?'selected':'')?>><?=$category->name?></option>
                                                      <?php } ?>
                                                   </select>
                                                <?php }else{ ?>    
                                                    <?php 
                                                       foreach($categoryList as $category){ 
                                                          if(stripos($category->name, 'admiss') !== false){
                                                    ?>
                                                      <input type="text" class="form-control" id="category_txt" value="<?=$category->name?>" readonly>
                                                      <input type="hidden" name="category_id" value="<?=$category->id?>"> 
                                                    <?php } } ?>
                                                <?php } ?>    
                                           </div>
                                        </div>
                                    <?php } ?>   

                                    <label class="col-sm-2 col-form-label text-right">Advanced Fees <span class=" cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Advanced fees submited by this student"><i class="fa fa-question-circle"></i></span></label>
                                      
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="text" class="form-control" value="<?=(!empty($studentDetails)?$studentDetails->advanced_fees:'')?>" readonly>
                                        <input type="hidden" name="advanced_fees" value="<?=(!empty($studentDetails)?$studentDetails->advanced_fees:'')?>">
                                        <input type="hidden" name="tmp_stu_record_id" value="<?=(!empty($_GET['tmp_id'])?$_GET['tmp_id']:'')?>">
                                      </div>   
                                     </div>

                                </div>   
                                <div class="hr-line-dashed"></div>

                                <?php if($_SESSION['user_type'] == 'franchise'? (empty($_GET['student_id'])?true:false):true) { ?>

                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label text-right">Total Course Fees <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Total Course Fees of Student"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="stu_course_fees" placeholder="Enter Student's Total Course Fees..." value="">
                                         </div>   
                                        </div>

                                         <label class="col-sm-2 col-form-label text-right">Course Discount <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student Total Course Discount"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="stu_course_discount" placeholder="Enter Student's Course Disount..." value="">
                                         </div>   
                                        </div> 
                                    </div>    

                                    <div class="hr-line-dashed"></div>  

                                <?php } ?>        

                                <?php if(empty($_GET['student_id'])) { ?>
                                    <div class="form-group row">

                                        <label class="col-sm-2 col-form-label text-right">Admission Fees <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student's Receipt Amount"><i class="fa fa-question-circle"></i></span></label>
                                       
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="receipt_amount" id="og_receipt_amount" placeholder="Enter Student's Receipt Amount..." value="">
                                         </div>   
                                        </div>

                                        <label class="col-sm-2 col-form-label text-right">Registration Fees <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter any additional if any"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-4">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="extra_fees" id="extra_fees" placeholder="Enter any additional if any..." value="">
                                         </div>   
                                        </div>

                                    </div>                               
                                    <div class="hr-line-dashed"></div>

                               <?php } ?>   

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=student_admission"><button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Close Create Student Form" data-original-title="Save"><i class="fa fa-reply"></i></button></a>
                                        
                                        <?php if(empty($_GET['student_id'])){ ?> 
                                            <button type="button" class="btn btn-warning btn-sm" id="preview_receipt" data-toggle="tooltip" title="Preview Receipt Data"><i class="fa fa-eye"></i> Preview Receipt</button>
                                        <?php } ?>    

                                        <button class="btn btn-primary btn-sm" id="manage" type="submit" data-toggle="tooltip" title="<?=(!empty($_GET['student_id'])?'Update Student':'Create Student')?>" class="btn btn-success" data-original-title="Save"><i class="fa fa-save"></i> <?=(!empty($_GET['student_id'])?'Update Student':'Create Student')?></button>
                                    </div>
                                </div>
                            </form>
                            </strong>
                        </div>
                    </div>
                </div>
             </div> 
            <?php } ?> 

            <div class="row <?=(!empty($student_id) || !empty($tmp_id)?'d-none':'')?>" id="admitted_student_list">
               <div class="col-lg-12">

                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Newly Admitted Student List of Last 2 Days</h5>
                        <div class="ibox-tools">
                            <?php if($createPermission){ ?>  
                                 <a href="<?=SITE_URL?>?route=student_admission&actionType=manage_student" class="table-action-primary" data-toggle="tooltip" data-placement="bottom" title="Create New Student"><i class="fa fa-plus-circle"></i></a>
                             <?php }?>

                              <a href="<?=SITE_URL?>?route=student_admission" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Student Data"><i class="fa fa-refresh"></i></a>
                        </div>
                    </div>
                    <div class="ibox-content content_div_loader">

                        <div class="sk-spinner sk-spinner-wave">
                            <div class="sk-rect1"></div>
                            <div class="sk-rect2"></div>
                            <div class="sk-rect3"></div>
                            <div class="sk-rect4"></div>
                            <div class="sk-rect5"></div>
                        </div> 

                        <div class="project-list">
                         <div class="table-responsive project-list">

                            <a href="javascript:void(0)" id="export_receipt_href" style="display:none" download>
                              <button type="button" id="hidden_export_receipt_button">Export</button>
                            </a>

                            <table class="table table-striped table-bordered table-hover dataTables-example text-center">
                                <thead class="cursor-pointer">
                                    <tr>
                                        <th>#</th>
                                        <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Student Name">Student Name</th>

                                        <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Student Basic Information">Student Info</th>

                                         <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Course and Franchise Information">Course Info</th>

                                        <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Created Student and Receipt ID">Created ID</th>

                                        <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Created Receipt Information">Receipt Info<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                        <!--<th class="sorting_desc_disabled notexport" data-toggle="tooltip" data-placement="bottom" title="Student Status">Status<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>-->

                                        <th class="sorting_desc_disabled notexport">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php 
                                    foreach($studentList as $index => $student){
           
                                        $receiptInfo = $this->globalLibraryHandlerObj->fetch_Student_Admission_Receipt($student->stu_id);
                                        
                                        $total_receipt_amount = round( (int)$receiptInfo->receipt_amount+ (int)$receiptInfo->extra_fees);
                                  ?> 
                                      <tr>
                                        <td><?=$index+1?></td>

                                        <td class="project-title">
                                            <a href="<?=SITE_URL?>?route=edit_course&id=<?=$student->id?>" data-toggle="tooltip" data-placement="bottom" title="Student Name:  <?=$student->stu_name?>">
                                                <?=$student->stu_name?></a>
                                            <br/>
                                            <small>Created <?=date('jS F, Y',strtotime($student->created_at))?></small>
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student Father Name: <?=$student->stu_father_name?>"><?=$student->stu_father_name?></span><br/>
                                            <small><strong>Phone No:</strong> <?=$student->stu_phone?></small>
                                        </td>

                                       <td class="project-title" style="width:20%;">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student Course: <?=$student->course_title?>"><?=$student->course_title?></span><br/>
                                            <small><strong>Franchise:</strong> <?=$student->center_name?></small>
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student ID: <?=$student->stu_id?>"><?=$student->stu_id?></span><br/>
                                             <?php if($receiptInfo->receipt_amount>0){ ?> 
                                                <small><strong>Receipt ID:</strong> <?=$receiptInfo->receipt_id?></small>
                                             <?php } ?>    
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Receipt Amount: <?=$total_receipt_amount?>"><i class="fa fa-inr"></i> <?=sprintf("%.2f",$total_receipt_amount)?></span><br/>
                                            <small><strong>Receipt Type:</strong> <?=ucfirst($receiptInfo->category)?></small>
                                        </td>

                                        <!--<td class="project-status">
                                            <span class="label label-primary cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student Status: <?=ucfirst($student->student_status)?>"><?=ucfirst($student->student_status)?></span>   
                                        </td>-->

                                        <td class="project-status">
                                           
                                             <span class="dropdown">
                                              <button class="btn btn-success product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                             <ul class="dropdown-menu">
                                               <?php if($updatePermission){ ?>  
                                                   <li>
                                                     <a href="<?=SITE_URL?>?route=student_admission&actionType=manage_student&student_id=<?=$student->id?>" class="#" data-toggle="tooltip" data-placement="bottom" title="Edit this Student"><i class="fa fa-pencil"></i> Edit Student</a>
                                                   </li>
                                                <?php } ?>
                                                
                                                <?php if($updateReceiptPermission && $receiptInfo->receipt_amount>0){ ?>   

                                                   <li>
                                                     <a href="<?=SITE_URL.'?route=view_receipts&actionType=edit&rcpt_id='.$receiptInfo->id?>" target="_blank"  data-toggle="tooltip" data-placement="bottom" title="Edit Admission Receipt"><i class="fa fa-money"></i> Edit Receipt</a>
                                                   </li>
                                                <?php } ?>   

                                                <?php if($receiptInfo->receipt_amount>0){ ?> 

                                                    <li>
                                                      <a href="javascript:void(0);" class="exportReceiptData" data-toggle="tooltip" data-placement="bottom" title="Print PDF file for this receipt" data-rid="<?=$receiptInfo->id?>" data-rcptid="<?=$receiptInfo->receipt_id?>" data-extype="print">
                                                          <i class="fa fa-print"></i> Print Receipt
                                                      </a>
                                                    </li>


                                                    <li>
                                                       <a href="javascript:void(0);" class="exportReceiptData" data-toggle="tooltip" data-placement="bottom" title="Download PDF file for this receipt" data-rid="<?=$receiptInfo->id?>" data-rcptid="<?=$receiptInfo->receipt_id?>" data-extype="download">
                                                            <i class="fa fa-download"></i> Download
                                                        </a> 
                                                    </li>

                                                <?php } ?>    
                                               
                                             </ul>
                                           </span>  

                                          </td>
                                        </tr>
                                   <?php } ?>    
                                </tbody>
                            </table>
                        </div>
                       </div> 
                    </div>
                </div>
              </div>
            </div>
        </div> 

        <!-- Modal window div-->
        <div class="modal fade show" id="receiptPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
             <div class="modal-dialog modal-lg">
                 <div class="modal-content animated fadeIn">
                    <div class="modal-header">
                        <h3 class="modal-title" id="receipt_modal_title">Receipt Preview Window</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">×</span>
                        </button>
                    </div>

                    <div class="modal-body">
                      <div class="row">  
                          <div class="col-lg-6 col-md-6 col-sm-12">
                               <div class="form-group">
                                 <label>Receipt Category:</label> 
                                 <input type="text" id="preview_receipt_category" class="form-control" readonly>
                               </div>
                           </div>  

                            <div class="col-lg-6 col-md-6 col-sm-12">
                               <div class="form-group">
                                 <label>Receipt Amount:</label> 
                                 <input type="text" id="preview_receipt_amount" class="form-control" readonly>
                               </div>
                           </div>  

                           <div class="col-lg-6 col-md-6 col-sm-12">
                               <div class="form-group">
                                 <label>Additional Fees:</label> 
                                 <input type="text" id="preview_extra_fees" class="form-control" readonly>
                               </div>
                           </div>  

                       </div>      
                    </div>      
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                   </div>
                </div>
            </div>
        </div>                    
        <!-- Modal ends here -->

       
        <!-- Custom JS -->
       <script>
         //Removing dynamically generated file from server
         function removeFileFromServer(file_upload_dir){
            var formData = {action:"removeFileFromServer",file_upload_dir:file_upload_dir};
            
            $.ajax({
              url:ajaxControllerHandler,
              method:'POST',
              data: formData,
              beforeSend: function() {
                 //$('.tooltip').hide();
                 //$('.content_div_loader').addClass('sk-loading');
              },
              success:function(responseData){
                  var result = JSON.parse(responseData);
                  //console.log(result);
                  return true; 
               }
            });
         }

         $(document).ready(function () {

            //Multiple select course
            $('.course_id').select2({width: "98.5%"});
            $('.franchise_id').select2({width: "98.5%"});
            $('.category_id').select2({width: "98.5%"});

            $(document).on('change','.select2',function(event){
                $(this).valid();
            });

            setInterval(function(){ 
               //$("#manage").click();
            },2000);

            $(document).on('click', '#preview_receipt', function(event){
                
                if($('#category_id').length>0){
                    var receipt_category_data = $('#category_id').select2('data');

                    if(receipt_category_data[0].id != ''){
                      $('#preview_receipt_category').val(receipt_category_data[0].text);
                    }else{
                      $('#preview_receipt_category').val('No input provided!');
                    }
                }else{
                    var receipt_category = $('#category_txt').val(); 

                    if(receipt_category){
                      $('#preview_receipt_category').val(receipt_category);
                    }else{
                      $('#preview_receipt_category').val('No input provided!');
                    }
                }    
                var receipt_amount = $('#og_receipt_amount').val();
                var extra_fees = $('#extra_fees').val();
                
                if(receipt_amount){
                  $('#preview_receipt_amount').val('Rs. '+receipt_amount); 
                }else{
                  $('#preview_receipt_amount').val('No input provided!');
                }

                if(extra_fees){
                  $('#preview_extra_fees').val('Rs. '+extra_fees); 
                }else{
                  $('#preview_extra_fees').val('No input provided!');
                }
              
                $('#receiptPreviewModal').modal('show'); 
            });

            //Handling student admission form
            $(document).on('submit', '#manage_admission_form', function(event){
               event.preventDefault();
               var student_id = $('#student_id').val();

               //Populating course name input value
               var courseData = $('#course_id').select2('data');
               $("#course_name").val(courseData[0].text);
               
               var formData = new FormData(this);

               swal({
                   title: "Are you sure?",
                   text: "You may ne be able to update the receipt later?",
                   type: "warning",
                   showCancelButton: true,
                   confirmButtonColor: "#DD6B55",
                   confirmButtonText: "Yes, Go ahead!",
                   closeOnConfirm: true
               }, function () {
                   $.ajax({
                      url:ajaxControllerHandler,
                      method:'POST',
                      data: formData,
                      contentType:false,
                      processData:false,
                      beforeSend: function() {
                         //$('.content_div_loader').addClass('sk-loading');
                         //$('#manage').attr('disabled',true);
                      },
                     success:function(responseData){
                       var data = JSON.parse(responseData);
                       $('#manage').attr('disabled',false);
                       //console.log(responseData);
                       if(data.check == 'success'){
                        //reseting form data
                        //$('#manage_admission_form')[0].reset();
                        //Disabling loader
                        $('.content_div_loader').removeClass('sk-loading');
                        //show sweetalert success

                        //var redirect_url = SITE_URL+"?route=student_admission&actionType=manage_student&student_id=";

                        if(data.last_insert_id>0){
                          var successText = "<b>Student has been successfully created!<br> Your student id is :- "+data.stu_id+
                                            "<br> Student course is :- "+data.course+'</b>';
                          //redirect_url += data.last_insert_id;
                          var redirect_url = SITE_URL+"?route=student_admission&actionType=manage_student";                  
                        }else{
                          var successText = "<b>Student has been successfully updated!<br> Your student id is :- "+data.stu_id+
                                            "<br> Student course is :- "+data.course+'</b>';
                          //redirect_url += student_id;
                          var redirect_url = SITE_URL+"?route=student_admission";                  
                        } 
                        
                        setTimeout(function(){
                           swal({
                                title: "Great!",
                                html:true,
                                text: successText,
                                type: "success"
                            },function() {
                                window.location = redirect_url;
                            });  
                        },500); 

                        return true; 
                     }else{
                       //Disabling loader
                        $('.content_div_loader').removeClass('sk-loading');
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

            //Handling export receipt pdf
            $(document).on('click', '.exportReceiptData', function(event){
                event.preventDefault();
                
                var receipt_row_id = $(this).data('rid');
                var receipt_id = $(this).data('rcptid');
                var export_type = $(this).data('extype');

                var formData = {action:"exportStudentReceiptPdf",receipt_row_id:receipt_row_id};

                $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: formData,
                  beforeSend: function() {
                     $('.tooltip').hide();
                     $('.content_div_loader').addClass('sk-loading');
                  },
                  success:function(responseData){
                      var result = JSON.parse(responseData);
                      //console.log(result);
                      setTimeout(function() {
                        //Disabling loader
                        $('.content_div_loader').removeClass('sk-loading');

                        //console.log(export_type);

                        if(export_type == "download"){
                           $('#export_receipt_href').attr("href",result.file_url);
                           $( "#hidden_export_receipt_button").click(); 
                        }else{
                           //Generating dynamic button for print pdf
                           var printPdfBtn = '<button id="print_receipt_btn" onclick="printJS(\''+result.file_url+'\')" style="display:none;">'+'<i class="fa fa-print"></i> Print</button>';
                           $("body").append(printPdfBtn); 
                           $( "#print_receipt_btn").click();          
                        }
                      }, 500);

                      //Removing file from server
                      setTimeout(function() {
                        removeFileFromServer(result.file_upload_dir);
                      }, 5000);  
                      return true; 
                   }
                });
            });

        });
     </script>
  </body>
