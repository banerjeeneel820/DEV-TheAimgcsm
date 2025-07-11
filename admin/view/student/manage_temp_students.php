<?php
  
  if(isset($_GET['record_status'])){
    if($_GET['record_status'] == 'active'){
       $record_status = 'active'; 
    }else{
       $record_status = 'blocked'; 
    }
  }else{
    $record_status = 'active'; 
  }
  
  if($_GET['actionType'] == 'manage_student'){
      if(isset($_GET['tmp_id'])){
        $tmp_id = $_GET['tmp_id'];
        $studentDetails = $pageContent['pageData']['student_data'];
        $tmp_stu_id = $studentDetails->tmp_stu_id;
      }else{
        $tmp_id = 'null';
        $tmp_stu_id = 'null';
        $studentDetails = array();
      }

      //Franchise data
      $franchiseArr = $pageContent['pageData']['franchise_data'];
      //Course data
      $courseArr = $pageContent['pageData']['course_data']; 

  }else{
     
     //Franchise data
     $franchiseArr = $pageContent['pageData']['franchise_data'];
     //Course data
     $courseArr = $pageContent['pageData']['course_data']; 

     $studentPagedData = $pageContent['pageData']['student_data'];

     $studentList = $studentPagedData['data'];
     $pageNo = $studentPagedData['pageNo'];
     $rowCount = $studentPagedData['row_count'];
     $limit = $studentPagedData['limit'];
     $offset = ($pageNo -1)*$limit;
     $totalPageNo = ceil($rowCount/$limit);   

     $conversion_status = $_GET['conversion_status'];
     $verified_status = $_GET['verified_status'];
     
     $queries = array();
     parse_str($_SERVER['QUERY_STRING'], $queries);

     $extra_query_str = '';

     foreach ($queries as $key => $query_val) {
         if($key != "route" && $key != "pageNo"){
           $extra_query_str .= "&".$key."=".$query_val;
         }  
     }
  }  

  //Fetching page action permission
  $viewPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("view_student"); 
  $createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_student"); 
  $updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_student"); 
  $deletePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("delete_student"); 

  $updateReceiptPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_receipt"); 

  if($_SESSION['user_type'] == 'franchise'){

    if(!empty($_GET['record_status']) || !empty($_GET['verified_status']) || !empty($_GET['course_id']) || !empty($_GET['search_string']) || !empty($_GET['created']) || !empty($_GET['search_start']) || !empty($_GET['search_end'])){

       $default_page = false;

    }else{
       $default_page = true;
    }

 }else{

    if(!empty($_GET['record_status']) || !empty($_GET['verified_status']) || !empty($_GET['course_id']) || !empty($_GET['franchise_id']) || !empty($_GET['search_string']) || !empty($_GET['created']) || !empty($_GET['search_start']) || !empty($_GET['search_end'])){
        
       $default_page = false;
    }else{
       $default_page = true;
    }  

 }  

 /*print"<pre>";
 print_r($studentList);
 print"</pre>";*/

?>
     <div class="wrapper wrapper-content animated fadeInRight">
            
            <?php if($_GET['actionType'] == 'manage_student'){ ?>
                <div class="row" id="manage_student_form_div">
                  <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Temporary Student Creation Form</h5>
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
                            <form id="manage_temp_student_form" class="needs-validation" method="post" onsubmit="return false;" novalidate>
                              <input type="hidden" name="action" id="action" value="manageTempStudents">
                              <input type="hidden" name="tmp_id" id="tmp_id" value="<?=$tmp_id?>">
                              <input type="hidden" name="tmp_stu_id" id="tmp_stu_id" value="<?=(!empty($tmp_stu_id)?$tmp_stu_id:'null')?>">

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
                                                <option value="<?=$franchise->id?>" <?=(!empty($_GET['tmp_id'])?($franchise->id == $studentDetails->franchise_id?'selected':''):($franchise->id == $_SESSION['user_id']?'selected':''))?>><?=$franchise->center_name?></option>
                                              <?php } ?>
                                           </select>
                                       </div>
                                    </div>
                                    
                                    <label class="col-sm-2 col-form-label text-right">Receipt Amount <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student's Receipt Amount"><i class="fa fa-question-circle"></i></span></label>
                                       
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="receipt_amount" id="og_receipt_amount" placeholder="Enter Student's Receipt Amount..." value="<?=$studentDetails->advanced_fees?>" required>
                                     </div>   
                                    </div>
                                </div>   
                                <div class="hr-line-dashed"></div>       
  

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=manage_temp_students&conversion_status=<?=($studentDetails->conversion_status == '0' ? 'n':'y')?>"><button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Close Create Student Form" title="Save"><i class="fa fa-reply"></i></button></a>
                                        
                                        <button class="btn btn-primary btn-sm" id="manage" type="submit" data-toggle="tooltip" title="<?=(!empty($_GET['tmp_id'])?'Update Student':'Create Student')?>" class="btn btn-success" title="Save"><i class="fa fa-save"></i> <?=(!empty($_GET['tmp_id'])?'Update Student':'Create Student')?></button>
                                    </div>
                                </div>
                            </form>
                            </strong>
                        </div>
                    </div>
                </div>
             </div>
            <?php } ?> 
            
            <?php if(!isset($tmp_id)){ ?>

              <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Fetch Student's Receipts Based on Selected Parameters</h5>
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
                           <form id="fetch_student_receipt_records" onsubmit="return false;">
                            <input type="hidden" name="page_route" id="page_route" value="<?=$_GET['route']?>">
                             <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-12 pr-0">
                                  <select class="record_status" name="record_status" id="record_status" required>
                                    <option></option>
                                    <option value="active" <?=(($record_status =='active' || $record_status =='')?'selected':'')?>>Active</option>  
                                    <option value="blocked" <?=($record_status=='blocked'?'selected':'')?>>Blocked</option>  
                                  </select>

                                   <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select Receipt Status"><i class="fa fa-question-circle"></i></span>
                                </div>

                                <div class="col-lg-3 col-md-3 col-sm-12 pl-0">
                                  <select class="form-control-sm form-control input-s-sm inline conversion_status" name="conversion_status" id="conversion_status">
                                    <option></option>
                                    <option value="n" <?=(($conversion_status =='n' || $default_page === true)?'selected':'')?>>Not Converted</option>  
                                    <option value="y" <?=($conversion_status=='y'?'selected':'')?>>Converted</option>  
                                  </select>

                                  <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select Student Conversion Status to Proceed"><i class="fa fa-question-circle"></i></span>
                                </div>

                                 <div class="col-lg-3 col-md-3 col-sm-12 pl-0"> 
                                    <div class="input-daterange input-group">
                                      <input type="text" class="form-control-sm form-control datepicker" name="created" id="created" value="<?=$_GET['created']?>" placeholder="Date of Creation" autocomplete="off">
                                      
                                      <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select a date range"><i class="fa fa-question-circle"></i></span>
                                    </div>
                                 </div>

                                 <div class="col-lg-3 col-md-3 col-sm-12 m-b-xs pl-0">
                                  <div class="input-group">
                                    <input type="text" class="form-control" name="search_string" id="search_string" placeholder="Search by Student's Details..." value="<?=(isset($_GET['search_string'])?$_GET['search_string']:'')?>">

                                    <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Enter Student ID"><i class="fa fa-question-circle"></i></span>

                                  </div>  
                                </div>
                              </div>

                              <div class="row">
                                <div class="col-lg-3 col-md-3 col-sm-12 ml-0 pr-0">
                                    <select class="course_id" name="course_id" id="course_id" data-placeholder="Search by a Course..." tabindex="2">
                                       <option></option>
                                       <?php foreach($courseArr as $course){ 
                                       ?>
                                        <option value="<?=$course->id?>" <?=($_GET['course_id'] == $course->id ?'selected':'')?>><?=$course->course_title?></option>
                                       <?php } ?>
                                    </select>

                                    <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Search by a Course"><i class="fa fa-question-circle"></i></span>
                                 </div>
                                 
                                 <div class="col-lg-3 col-md-3 col-sm-12 pl-0">   
                                    <select class="franchise_id" name="franchise_id" id="franchise_id" data-placeholder="Search by a Franchise..." tabindex="2" <?=($_SESSION['user_type'] == 'franchise'?'disabled':'')?>>
                                      <option></option>
                                       <?php foreach($franchiseArr as $franchise){ 
                                      ?>
                                        <option value="<?=$franchise->id?>" <?=($_SESSION['user_type'] == 'franchise'?($_SESSION['user_id'] == $franchise->id ?'selected':''):($_GET['franchise_id'] == $franchise->id ?'selected':''))?>><?=$franchise->center_name?></option>
                                      <?php } ?>
                                    </select>

                                    <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Search by a Franchise"><i class="fa fa-question-circle"></i></span>
                                </div>  

                                <div class="col-lg-3 col-md-3 col-sm-12 pl-0">
                                  <select class="form-control-sm form-control input-s-sm inline verified_status" name="verified_status" id="verified_status">
                                    <option></option>
                                    <option value="n" <?=(($verified_status =='n')?'selected':'')?>>Not Verified</option>  
                                    <option value="y" <?=($verified_status=='y'?'selected':'')?>>Verified</option>  
                                  </select>

                                  <span class="cursor-pointer pl-1 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select Verified Status to Proceed"><i class="fa fa-question-circle"></i></span>
                                </div>  

                                 <div class="col-lg-3 col-md-3 col-sm-12 pl-0 pr-4"> 

                                    <div class="input-daterange input-group" style="width:104%;">
                                      <input type="text" class="form-control-sm form-control datepicker" name="search_start" id="search_start" value="<?=$_GET['search_start']?>" placeholder="Starting Date" autocomplete="off">

                                      <span class="input-group-addon">to</span>

                                      <input type="text" class="form-control-sm form-control datepicker" name="search_end" id="search_end" value="<?=$_GET['search_end']?>" placeholder="Ending Date" autocomplete="off">

                                      <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select a date range"><i class="fa fa-question-circle"></i></span>
                                     
                                    </div>
                                 </div>
                              </div>  

                              <div class="row mt-2">
                                  <div class="col-lg-4 col-md-4 col-sm-12 ml-0">
                                       <button class="btn btn-primary" type="submit" id="fetch_item_data" data-toggle="tooltip" data-placement="bottom" title="Fetch Temporary Student's Data"><i class="fa fa-search"></i> Fetch Student Data</button>   
                                  </div>
                              </div>
                          </form>
                        </div>
                    </div>
                  </div>
                </div>  

            <?php } ?>     

            <?php if(!isset($tmp_id)){ ?>  

                <div class="row" id="admitted_student_list">
                   <div class="col-lg-12">

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Temporary <?=($_GET['conversion_status'] == 'y' ? 'Converted': '')?> Students List</h5>
                            <div class="ibox-tools">
                                <a href="<?=SITE_URL?>?route=manage_temp_students" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Student Data"><i class="fa fa-refresh"></i></a>

                                <?php if($createPermission){ ?>  
                                     <a href="<?=SITE_URL?>?route=manage_temp_students&actionType=manage_student" class="table-action-primary" data-toggle="tooltip" data-placement="bottom" title="Create New Student"><i class="fa fa-plus-circle"></i></a>
                                 <?php }?>

                                  <?php if($record_status == 'active'){ ?>
                                      <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid = "all" data-type="temp_student" data-ptype="Temporary Student" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
                                  <?php }else{ ?>   
                                      <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid = "all" data-type="temp_student" data-ptype="Temporary Student" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>

                                      <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid = "all" data-type="temp_student" data-ptype="Temporary Student" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
                                 <?php } ?>     
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
                                
                                <input type="text" class="form-control form-control-sm m-b-xs" id="student_tbl_filter" placeholder="Search in student table by student name, id, phone no or franchise...">

                                <div class="my-2">
                                    <?php if(count($studentList)>0){ ?>
                                        <strong>Showing <?=$offset+1?> to <?=( count($studentList) == $limit ? $limit*$pageNo : count($studentList) )?> of <?=$rowCount?> entries</strong>
                                    <?php }else{ ?>
                                        <strong>No Data Found!</strong>
                                    <?php } ?>        
                                </div>    

                                <a href="javascript:void(0)" id="export_receipt_href" style="display:none" download>
                                  <button type="button" id="hidden_export_receipt_button">Export</button>
                                </a>

                                <table class="table table-striped table-bordered table-hover text-center" id="student_list_tbl">
                                    <thead class="cursor-pointer">
                                        <tr>
                                            <th class="notexport">
                                                <div class="pretty p-image p-plain checkAll ml-2">
                                                   <input type="checkbox" id="checkAll" />
                                                   <div class="state">
                                                      <img class="image" src="<?=RESOURCE_URL?>images/checkbox.png">
                                                      <label></label>
                                                   </div>
                                                </div>
                                            </th>
                                            <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Student Name">Student Name</th>

                                            <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Student Basic Information">Student Info</th>

                                             <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Course and Franchise Information">Course Info</th>

                                            <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Created Student and Receipt ID">Student ID</th>

                                            <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Created Receipt Information">Receipt Info<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                            <!--<th class="sorting_desc_disabled notexport" data-toggle="tooltip" data-placement="bottom" title="Student Status">Status<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>-->

                                            <th class="sorting_desc_disabled notexport">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php
                                          $total_collection = 0; 
                                          foreach($studentList as $index => $student){
                                            
                                            $total_collection = (int)$total_collection + (int)$student->advanced_fees;
                                      ?> 
                                          <tr id="tmp_tr_<?=$student->tmp_id?>" style="background-color:<?=(($_SESSION['user_type']!= 'franchise' && $student->verified_status == '0') ? '#f1d0d0;':'')?>">
                                            
                                            <td style="width: 6%;">
                                              <div class="pretty p-image p-plain selectAllItem ml-2">
                                                   <input type="checkbox" class="singleCheck" id="<?=$student->tmp_id?>" value="<?=$student->tmp_id?>"/>
                                                   <div class="state">
                                                      <img class="image" src="<?=RESOURCE_URL?>images/checkbox.png">
                                                      <label class="cursor-pointer selectAllItem" for="<?=$student->tmp_id?>"></label>
                                                   </div>
                                                </div> 
                                            </td>

                                            <td class="project-title">
                                                <a href="<?=SITE_URL?>?route=manage_temp_students&actionType=manage_student&tmp_id=<?=$student->tmp_id?>" data-toggle="tooltip" data-placement="bottom" title="Student Name:  <?=$student->stu_name?>">
                                                    <?=$student->stu_name?></a>
                                                <br/>
                                                <small>Created <?=date('jS F, Y',strtotime($student->created_at))?></small><br>
                                                <small><strong>Conversion Status: <?=($student->conversion_status == 0? 'Not Converted':'Converted')?></strong></small>
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
                                                <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student ID: <?=$student->tmp_stu_id?>">Student Temporary Id:<br> <b><?=$student->tmp_stu_id?></b></span><br/>
                                            </td>

                                            <td class="project-title">
                                                <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Receipt Amount: <?=$student->advanced_fees?>"><i class="fa fa-inr"></i> <?=sprintf("%.2f",$student->advanced_fees)?></span><br/>
                                                <small><strong>Receipt Type:</strong> Advance Fees</small>
                                            </td>

                                            <td class="project-status">
                                               
                                                 <span class="dropdown">
                                                  <button class="btn btn-success product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                                 <ul class="dropdown-menu">
                                                   <?php if($updatePermission){ ?>  
                                                       <li>
                                                         <a href="<?=SITE_URL?>?route=manage_temp_students&actionType=manage_student&tmp_id=<?=$student->tmp_id?>" class="#" data-toggle="tooltip" data-placement="bottom" title="Edit this Student"><i class="fa fa-pencil"></i> Edit Student</a>
                                                       </li>
                                                       
                                                       <?php if($updatePermission && $student->conversion_status == 0){ ?>
                                                           <li>
                                                             <a href="<?=SITE_URL?>?route=student_admission&actionType=manage_student&tmp_id=<?=$student->tmp_id?>" class="#" data-toggle="tooltip" data-placement="bottom" title="Convert to Parmanent Student"><i class="fa fa-exchange"></i> Convert to Main</a>
                                                           </li>
                                                        <?php }?>   
                                                    <?php } ?>
                                                    
                                                    <?php if($updatePermission && ($_SESSION['user_type'] == "admin" || $_SESSION['user_type'] == "developer")){ ?> 

                                                        <li>
                                                            <a href="javascript:void(0)" id="item_<?=$student->tmp_id?>" class="verified_action" data-vstatus="<?=($student->verified_status=='1'?'0':'1')?>" data-tid="<?=$student->tmp_id?>" data-toggle="tooltip" data-placement="bottom" title="Make this student's status <?=($student->verified_status=='1'?'not verified':'verified')?>"><i class="<?=($student->verified_status=='1'?'fa fa-check-circle':'fa fa-info-circle')?>"></i> <?=($student->verified_status=='1'?'Verified':'Not-Verified')?> 
                                                            </a>
                                                        </li>
                                                        
                                                    <?php } ?>

                                                    <li>
                                                      <a href="javascript:void(0);" class="exportTempReceiptData" data-toggle="tooltip" data-placement="bottom" title="Print PDF file for this receipt" data-rid="<?=$student->tmp_id?>" data-extype="print">
                                                          <i class="fa fa-print"></i> Print Receipt
                                                      </a>
                                                    </li>

                                                    <li>
                                                       <a href="javascript:void(0);" class="exportTempReceiptData" data-toggle="tooltip" data-placement="bottom" title="Download PDF file for this receipt" data-rid="<?=$student->tmp_id?>" data-extype="download">
                                                            <i class="fa fa-download"></i> Download
                                                        </a> 
                                                    </li>

                                                    <?php if($student->record_status == 'active'){?>

                                                         <li>
                                                           <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$student->tmp_id ?>" data-type="temp_student" data-ptype="Temporary Student" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Student"><i class="fa fa-trash"></i> Block Student</a>
                                                        </li>

                                                    <?php }else{ ?>

                                                        <li>
                                                         <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$student->tmp_id ?>" data-type="temp_student" data-ptype="Temporary Student" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore this Student"><i class="fa fa-refresh"></i> Restore Student</a>
                                                       </li> 

                                                        <?php if($deletePermission){ ?>
                                                           <li>
                                                             <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$student->tmp_id ?>" data-type="temp_student" data-ptype="Temporary Student" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete this Student"><i class="fa fa-times"></i> Delete Student</a>
                                                           </li>
                                                        <?php } ?> 

                                                    <?php } ?>    
                                                 </ul>
                                               </span>  

                                              </td>
                                            </tr>
                                       <?php } ?>    
                                    </tbody>
                                     <?php if(count($studentList)>0){ ?>
                                        <div class="alert alert-success text-center" role="alert">
                                          Total Collection of fees deposited by the temporary students on <?=count($studentList )?>&nbsp;occasions : <i class="fa fa-inr"></i> <?=$total_collection?>
                                        </div>
                                    <?php } ?>    
                                </table>
                             </div>

                              <nav aria-label="Student Page navigation">
                                  <ul class="pagination">

                                    <?php 
                                       if($totalPageNo > 1){
                                           if($pageNo == 1){
                                              $pervious_link = "javascript:void(0);";
                                           }else{
                                              $pervious_link = SITE_URL.'?route=manage_temp_students'.$extra_query_str.'&pageNo='.($pageNo-1);
                                           }
                                           $next_link = SITE_URL.'?route=manage_temp_students'.$extra_query_str.'&pageNo='.($pageNo+1);
                                       }
                                    ?>
                                    
                                    <li class="page-item <?=($pageNo == 1?'disabled':'')?>">
                                      <a class="page-link" href="<?=$pervious_link?>" tabindex="-1">Previous</a>
                                    </li>

                                    <?php 
                                        for($page=1;$page<=$totalPageNo;$page++){ 
                                          if($page < 6 && $pageNo < 5){
                                    ?>

                                        <li class="page-item <?=($page==$pageNo?'active':'')?>">
                                          <a class="page-link" href="<?=SITE_URL.'?route=manage_temp_students'.$extra_query_str.'&pageNo='.$page?>"><?=$page?></a>
                                        </li>

                                    <?php 
                                        }elseif($pageNo >= 5 && $page != $totalPageNo){ 
                                           if($page ==1){
                                    ?>

                                        <li class="page-item <?=($page==$pageNo?'active':'')?>">
                                          <a class="page-link" href="<?=SITE_URL.'?route=manage_temp_students'.$extra_query_str.'&pageNo=1'?>"><?=$page?></a>
                                        </li>
                                    
                                    <?php }elseif($page == $pageNo-2){ ?>   

                                        <li class="page-item">
                                          <a class="page-link" href="javascript:void(0);">...</a>
                                        </li>

                                    <?php }elseif($page == $pageNo-1 || $page == $pageNo || $page == $pageNo+1){ ?>     
                                        
                                         <li class="page-item <?=($page==$pageNo?'active':'')?>">
                                          <a class="page-link" href="<?=SITE_URL.'?route=manage_temp_students'.$extra_query_str.'&pageNo='.$page?>"><?=$page?></a>
                                        </li>

                                    <?php }elseif($page == $pageNo+2){ ?>   

                                        <li class="page-item">
                                          <a class="page-link" href="javascript:void(0);">...</a>
                                        </li>  

                                    <?php 
                                        } }elseif($pageNo == $totalPageNo){ 
                                             if($page ==1){
                                    ?>    

                                        <li class="page-item <?=($page==$pageNo?'active':'')?>">
                                          <a class="page-link" href="<?=SITE_URL.'?route=manage_temp_students'.$extra_query_str.'&pageNo=1'?>"><?=$page?></a>
                                        </li>

                                    <?php }elseif($page >= $totalPageNo-4){ ?>     

                                        <li class="page-item <?=($page==$pageNo?'active':'')?>">
                                          <a class="page-link" href="<?=SITE_URL.'?route=manage_temp_students'.$extra_query_str.'&pageNo=1'?>"><?=$page?></a>
                                        </li>  

                                    <?php 
                                        } }elseif($page == 6 && $pageNo < 5){ 
                                    ?>    

                                        <li class="page-item">
                                          <a class="page-link" href="javascript:void(0);">...</a>
                                        </li>

                                    <?php }elseif($page == $totalPageNo){ ?> 

                                        <li class="page-item <?=($page==$pageNo?'active':'')?>">
                                          <a class="page-link" href="<?=SITE_URL.'?route=manage_temp_students'.$extra_query_str.'&pageNo='.$page?>"><?=$page?></a>
                                        </li>

                                    <?php } } ?>    
                                    
                                    <li class="page-item">
                                      <a class="page-link" href="<?=$next_link?>">Next</a>
                                    </li>
                                  </ul>
                                </nav>
                           </div> 
                        </div>
                    </div>
                  </div>
                </div>

            <?php } ?>    
        </div> 
       
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
            $('.record_status').select2({placeholder:"Select a record status",width: "84%",allowClear: true});
            $('.conversion_status').select2({placeholder:"Select a conversion status",width: "84%",allowClear: true});
            $('.verified_status').select2({placeholder:"Select a verified status",width: "93%",allowClear: true});
            $('.course_id').select2({placeholder:"Select a course",width: "84%",allowClear: true});
            $('.franchise_id').select2({placeholder:"Select a franchise",width: "84%",allowClear: true});

             /*---Input date & time control--*/
            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

            $('.datepicker').datepicker({
                  format: "dd/mm/yyyy",
                  todayBtn: "linked",
                  keyboardNavigation: true,
                  todayHighlight: true,
                  //startDate: today,
                  forceParse: false,
                  calendarWeeks: true,
                  autoclose: true
            });
            /*------- Ends Here ---------*/ 

            $(document).on('change','.course_id,.record_status,.conversion_status,.franchise_id',function(event){
                $(this).valid();
            });

            setInterval(function(){ 
               //$("#manage").click();
            },2000);

            $('#student_list_tbl').filterTable('#student_tbl_filter');

            //Handling student admission form
            $(document).on('submit', '#manage_temp_student_form', function(event){
               event.preventDefault();
               var tmp_id = $('#tmp_id').val();

               //Populating course name input value
               var courseData = $('#course_id').select2('data');
               $("#course_name").val(courseData[0].text);
               
               var formData = new FormData(this);

               /*swal({
                   title: "Are you sure?",
                   text: "You may ne be able to update the receipt later?",
                   type: "warning",
                   showCancelButton: true,
                   confirmButtonColor: "#DD6B55",
                   confirmButtonText: "Yes, Go ahead!",
                   closeOnConfirm: true
               }, function () {*/
                   $.ajax({
                      url:ajaxControllerHandler,
                      method:'POST',
                      data: formData,
                      contentType:false,
                      processData:false,
                      beforeSend: function() {
                         $('.content_div_loader').addClass('sk-loading');
                         $('#manage').attr('disabled',true);
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

                        //var redirect_url = SITE_URL+"?route=manage_temp_students&actionType=manage_student&tmp_id=";

                        if(data.last_insert_id>0){
                          var successText = "<b>Student has been successfully created!<br> Your student id is :- "+data.tmp_stu_id+
                                            "<br> Student course is :- "+data.course+'</b>';
                          //redirect_url += data.last_insert_id;
                          var redirect_url = SITE_URL+"?route=manage_temp_students&actionType=manage_student";                  
                        }else{
                          var successText = "<b>Student has been successfully updated!<br> Your student id is :- "+data.tmp_stu_id+
                                            "<br> Student course is :- "+data.course+'</b>';
                          //redirect_url += tmp_id;
                          var redirect_url = SITE_URL+"?route=manage_temp_students";                  
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
               //});   
             
            });

            //Configuring fetching all page records fetching params
            $(document).on('submit', '#fetch_student_receipt_records', function(event){
              event.preventDefault();
              var search_string = $('#search_string').val();
              var record_status = $('#record_status').val();
              var page_route = $('#page_route').val();

              var course_id = $('#course_id').val();
              var franchise_id = $('#franchise_id').val();
              var created = $('#created').val();

              var search_start = $('#search_start').val(); 
              var search_end = $('#search_end').val();

              var conversion_status = $('#conversion_status').val();
              var verified_status = $('#verified_status').val();
              
              if(record_status === null){
                  window.location = SITE_URL+"?route="+page_route;
              }else{
                $('#fetch_item_data').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Fetching').attr('disabled',true);
                 setTimeout(function(){
                 $('#fetch_item_data').html('<i class="fa fa-search"></i> Fetch Student Data').attr('disabled',false);
                 //show sweetalert success
                 swal({
                  title: "Great!",
                  text: "Data has been successfully fetched!",
                  type: "success",
                  allowEscapeKey : false,
                  allowOutsideClick: false
                 },function(){
              
                   var redirect_url = SITE_URL+"?route="+page_route;

                   if(conversion_status){
                      redirect_url += "&conversion_status="+conversion_status;
                   }   

                   if(verified_status){
                      redirect_url += "&verified_status="+verified_status;
                   }   

                   if(search_string){
                      redirect_url += "&search_string="+search_string;
                   }

                   if(search_start.length>0){
                      redirect_url += "&search_start="+search_start;
                   }
                    
                   if(search_end.length>0){
                      redirect_url += "&search_end="+search_end;
                   } 

                   if(course_id>0){
                      redirect_url += "&course_id="+course_id;
                   }

                   if(franchise_id>0){
                      redirect_url += "&franchise_id="+franchise_id;
                   } 

                   if(created.length>0){
                      redirect_url += "&created="+created;
                   }   

                   redirect_url += "&record_status="+record_status;
        
                   window.location = redirect_url;              
                                    
                 });},500);
                 return true;  
              } 
           });

            //Handling export receipt pdf
            $(document).on('click', '.exportTempReceiptData', function(event){
                event.preventDefault();
                
                var tmp_id = $(this).data('rid');
                var export_type = $(this).data('extype');

                var formData = {action:"exportTempStudentReceipt",tmp_id:tmp_id};

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

            /*Status change handler*/
            $(document).on('click','.verified_action',function(){
              var action = "updateTempStudentVerifiedStatus";  
              var tmp_id = $(this).data('tid'); 
              var verified_status = $(this).data('vstatus');

              var thisItem = $(this);

              if(verified_status == '1'){
                var toastrText = 'This student has been marked as verified successfully!';
              }else{
                var toastrText = 'This student has been marked as not verified successfully!';
              }
              //show toastr success
              toastr.options = {
                 closeButton: true,
                 progressBar: true,
                 showMethod: 'slideDown',
                 timeOut: 2000,
              };

              var formData = {action:action,tmp_id:tmp_id,verified_status:verified_status};
              
              $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: formData,
                  beforeSend: function() {
                     //$('.content_div_loader').addClass('sk-loading');
                  },
                  success:function(responseData){
                      //console.log(responseData); 
                      var data = JSON.parse(responseData);
                      //Disabling loader
                      $('.content_div_loader').removeClass('sk-loading');

                      //Check response
                      if(data.check == 'success'){
                          if(verified_status == '1'){
                            $(thisItem).data('vstatus', '0');
                            $(thisItem).attr('title',"Make this student's status not verified!");
                            $(thisItem).html('<i class="fa fa-check-circle"></i> Verified');

                            //Chnage table tr background color
                            $("#tmp_tr_"+tmp_id).css({'background-color':''});
                            //Show success toast
                            toastr.success(toastrText, 'Success!');

                          }else{
                            $(thisItem).data('vstatus', '1');
                            $(thisItem).attr('title',"Make this student's status verified!");
                            $(thisItem).html('<i class="fa fa-info-circle"></i> Not Verified');
                            //Chnage table tr background color
                            $("#tmp_tr_"+tmp_id).css({'background-color':'#f1d0d0'});
                            //Show warning toast
                            toastr.warning(toastrText, 'Success!');
                          }  
                          return true; 
                      }else{
                          if(data.message.length>0){
                            var toastrErrorText = data.message;
                          }else{
                            var toastrErrorText = 'Something went wrong! Please try again.'
                          }
                          //show toastr error
                          toastr.options.onHidden = function() { window.location.reload(); }
                          toastr.error(toastrErrorText, 'Error!');
                          return false;
                      } 

                   }
                 });
             });

        });
     </script>
  </body>
