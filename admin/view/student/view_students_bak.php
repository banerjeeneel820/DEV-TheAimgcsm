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

  $verified_status = $_GET['verified_status'];

  //Course data
  $courseArr = $pageContent['pageData']['course_data'];
  //Franchise data
  $franchiseArr = $pageContent['pageData']['franchise_data'];
  $studentPagedData = $pageContent['pageData']['student_data'];

  $studentListArr = $studentPagedData['data'];
  $pageNo = $studentPagedData['pageNo'];
  $rowCount = $studentPagedData['row_count'];
  $limit = $studentPagedData['limit'];

  $totalPageNo = ceil($rowCount/$limit);

  /*echo $pageNo."<br>";
  echo $rowCount."<br>";
  echo $limit."<br>";
  echo $totalPageNo."<br>";*/

  if(!empty($_GET['student_status'])){
    $student_status = $_GET['student_status'];   
  }else{
    $student_status = null;
  }

  if(!empty($_GET['result_status'])){
    $result_status = $_GET['result_status'];   
  }else{
    $result_status = null;
  }

  //Fetching page action permission
  $viewFeedbackPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("view_feedback"); 
  $createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_student"); 
  $updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_student"); 
  $deletePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("delete_student"); 

  $resultUpdatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_result"); 
  $createReceiptPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_receipt"); 

  if($_SESSION['user_type'] == 'franchise'){
    if($_SESSION['owned_status'] == "yes" && $updatePermission == true){
        $showStatusDropdown = true;
    }else{
        $showStatusDropdown = false;
    }
  }
  elseif($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'developer'){
     if($updatePermission){
        $showStatusDropdown = true;
     }else{
        $showStatusDropdown = false;
     }
  }

  if($showStatusDropdown == false && $resultUpdatePermission == false){
     $showResultData = true;
  }else{
     $showResultData = false;
  }

  $queries = array();
  parse_str($_SERVER['QUERY_STRING'], $queries);

  $extra_query_str = '';

  foreach ($queries as $key => $query_val) {
      if($key != "route" && $key != "pageNo"){
        $extra_query_str .= "&".$key."=".$query_val;
      }  
  }

  /*print'<pre>';
  print_r($studentListArr);
  print'</pre>';exit;*/
?>

<style>
   /* .modal-dialog {
        margin-top: 6rem;
    }
    .modal-dialog .modal-content .modal-header {
        margin: -6rem 0 -1rem;
        box-shadow: none;
    }
    .modal-dialog .modal-content .modal-header img {
        width: 140px;
        margin-right: auto;
        margin-left: auto;
        box-shadow: 0 8px 17px 0 rgb(0 0 0 / 20%), 0 6px 20px 0 rgb(0 0 0 / 19%);
    }

    .rounded-circle {
        border-radius: 50% !important;
    }*/
</style>
                    
            <div class="wrapper wrapper-content fadeInRight">  
                 <div class="row">
                    <div class="col-lg-9">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h5>Fetch Student based on their status </h5>
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
                               <form id="fetch_all_student_records" class="<?=($_GET['fetch_type'] == 'all' || !isset($_GET['fetch_type'])?'':'d-none')?>" onsubmit="return false;">
                                 <div class="row">
                                    <div class="col-lg-4 m-b-xs">
                                      <select class="form-control-sm form-control input-s-sm inline record_status" name="record_status" id="record_status" data-placeholder="Select a Data type to proceed" required>
                                        <option></option>
                                        <option value="active" <?=(($record_status =='active' || $record_status =='')?'selected':'')?>>Active</option>  
                                        <option value="blocked" <?=($record_status=='blocked'?'selected':'')?>>Blocked</option>  
                                      </select>

                                       <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Choose Record Status"><i class="fa fa-question-circle"></i></span>
                                    </div>

                                    <div class="col-lg-4 m-b-xs pl-0">
                                      <select class="form-control-sm form-control input-s-sm inline student_status" name="student_status" id="student_status" data-placeholder="Select Student Status">
                                        <option></option>
                                        <option value="admitted" <?=(($student_status =='admitted')?'selected':'')?>>Admitted</option>
                                        <option value="continue" <?=(($student_status =='continue')?'selected':'')?>>Continue</option>
                                        <option value="course_complete" <?=(($student_status =='course_complete')?'selected':'')?>>Course Complete</option>
                                        <option value="dropout" <?=(($student_status =='dropout')?'selected':'')?>>Dropout</option>  
                                        <?php if(isset($record_limit)){ ?>
                                         <option value="<?=$total_records?>" <?=($record_limit == $total_records?'selected':'')?>>Fetch All Records</option>  
                                        <?php } ?> 
                                      </select>

                                      <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Search students by their student status"><i class="fa fa-question-circle"></i></span>
                                    </div>


                                    <div class="col-lg-4 m-b-xs pl-0">
                                      <select class="form-control-sm form-control input-s-sm inline result_status" name="result_status" id="result_status" data-placeholder="Select Student Result Type">
                                          <option></option>
                                          <option value="qualified" <?=(($result_status =='qualified')?'selected':'')?>>Qualified</option>
                                          <option value="unqualified" <?=(($result_status =='unqualified')?'selected':'')?>>Unqualified</option>   
                                      </select>

                                       <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Search student by student result."><i class="fa fa-question-circle"></i></span>
                                    </div>

                                    <input type="hidden" name="page_route" id="page_route" value="<?=$_GET['route']?>">
                                 
                                </div>

                                <div class="row">
                                   <div class="col-lg-4 ml-0">
                                        <select class="course" name="course_id" id="course_id" data-placeholder="Choose a Course first..." tabindex="2">
                                           <option></option>
                                           <?php foreach($courseArr as $course){ 
                                           ?>
                                            <option value="<?=$course->id?>" <?=($_GET['course_id'] == $course->id ?'selected':'')?>><?=$course->course_title?></option>
                                           <?php } ?>
                                        </select>

                                        <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Select a Course"><i class="fa fa-question-circle"></i></span>
                                     </div>
                                     
                                     <div class="col-lg-4 col-md-4 col-sm-12 pl-0">   
                                        <select class="franchise" name="franchise_id" id="franchise_id" data-placeholder="Choose a Franchise first..." tabindex="2" <?=($_SESSION['user_type'] == 'franchise'?'disabled':'')?>>
                                          <option></option>
                                           <?php foreach($franchiseArr as $franchise){ 
                                          ?>
                                            <option value="<?=$franchise->id?>" <?=($_SESSION['user_type'] == 'franchise'?($_SESSION['user_id'] == $franchise->id ?'selected':''):($_GET['franchise_id'] == $franchise->id ?'selected':''))?>><?=$franchise->center_name?></option>
                                          <?php } ?>
                                        </select>

                                        <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Select a Franchise"><i class="fa fa-question-circle"></i></span>
                                    </div>  

                                     <div class="col-lg-4 col-md-4 col-sm-12 m-b-xs pl-0">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="search_string" id="search_string" placeholder="Search by Student's Details..." value="<?=(isset($_GET['search_string'])?$_GET['search_string']:'')?>">

                                        <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Enter Student ID"><i class="fa fa-question-circle"></i></span>
                                      </div>  
                                    </div>
                                </div>

                                <div class="row">
                                     <div class="col-lg-4 col-md-4 col-sm-12"> 
                                        <div class="input-daterange input-group">
                                          <input type="text" class="form-control-sm form-control datepicker" name="created" id="created" value="<?=$_GET['created']?>" placeholder="Date of Creation" autocomplete="off">
                                          
                                          <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select a date range"><i class="fa fa-question-circle"></i></span>
                                        </div>
                                     </div>

                                      <div class="col-lg-4 col-md-4 col-sm-12 pl-0 pr-4"> 

                                        <div class="input-daterange input-group">
                                          <input type="text" class="form-control-sm form-control datepicker" name="search_start" id="search_start" value="<?=$_GET['search_start']?>" placeholder="Starting Date" autocomplete="off">

                                          <span class="input-group-addon">to</span>

                                          <input type="text" class="form-control-sm form-control datepicker" name="search_end" id="search_end" value="<?=$_GET['search_end']?>" placeholder="Ending Date" autocomplete="off">

                                          <span class="cursor-pointer pl-1 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select a date range"><i class="fa fa-question-circle"></i></span>
                                            
                                        </div>
                                     </div>

                                     <div class="col-lg-4 col-md-4 col-sm-12 pl-0">
                                         <button class="btn btn-primary" type="submit" id="fetch_item_data" data-toggle="tooltip" data-placement="bottom" title="Fetch Student's Receipt Data"><i class="fa fa-search"></i> Fetch Student Data</button> 
                                     </div>   
                                </div>
                              </form>
                            </div>
                        </div>
                      </div>

                      <div class="col-lg-3 col-md-3">
                        <div class="ibox ">
                             <div class="ibox-title">
                                <h5>Export Records</h5>

                                <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Export student records in pdf or excel format based on search parameters on the search form"><i class="fa fa-question-circle"></i></span>

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
                                <a href="javascript:void(0);" class="btn btn-danger btn-md export_student_table_data" data-export="pdf">
                                    <i class="fa fa-file-pdf-o"> </i> Export in PDF Format
                                    <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Export student records in pdf format based on search parameters on the search form. This is a longer process and may take time upto 1 hour based on records number present in table."><i class="fa fa-question-circle"></i></span>
                                </a>
                                
                                 <a href="javascript:void(0);" class="btn btn-primary btn-md mt-2 export_student_table_data" data-export="excel">
                                    <i class="fa fa-file-excel-o"> </i> Export in CSV Format
                                    <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Export student records in excel format based on search parameters on the search form. This is a much faster process than pdf export and this is recomended."><i class="fa fa-question-circle"></i></span>
                                 </a>
        
                                 <a href="javascript:void(0)" id="export_record_href" style="display:none" download>
                                    <button type="button" id="hidden_export_button">Export</button>
                                 </a> 
                            </div>    
                        </div>
                      </div> 
                    </div>
                    
                    <?php if($showStatusDropdown){ ?> 
                        <div class="row">
                           <div class="col-lg-12">
                             <div class="ibox ">
                                <div class="ibox-title">
                                    <h5>Fetch Students based on their verification status </h5>
                                    <div class="ibox-tools">
                                        <a class="collapse-link">
                                            <i class="fa fa-chevron-up"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="ibox-content">
                                   <form id="fetch_verified_records" onsubmit="return false;">
                                     <div class="row">
                                        <div class="col-sm-10 m-b-xs">
                                          <select class="form-control-sm form-control input-s-sm inline" name="verified_status" id="verified_status">
                                            <option selected disabled value>Select a Verified Status to proceed</option>
                                            <option value="n" <?=(($verified_status =='n')?'selected':'')?>>Not Verified</option>  
                                            <option value="y" <?=($verified_status=='y'?'selected':'')?>>Verified</option>  
                                          </select>
                                        </div>
                                     
                                        <div class="col-sm-2">
                                          <button class="btn btn-primary" type="submit" id="fetch_item_data"><i class="fa fa-search"></i>&nbsp;Fetch Data</button>
                                        </div>
                                    </div>
                                  </form>
                                </div>
                            </div>
                          </div>
                        </div> 
                    <?php } ?>      

                    <div class="row">
                       <div class="col-lg-12 col-md-12 col-sm-12"> 
                          <div class="ibox">
                            <div class="ibox-title">
                                <h5>Student List with all details</h5>
                                <div class="ibox-tools">

                                     <?php if($createPermission){ ?> 
                                         <a href="<?=SITE_URL?>?route=add_student" class="table-action-primary" data-toggle="tooltip" data-placement="bottom" title="Add New Student"><i class="fa fa-plus-circle"></i></a>
                                     <?php }?>

                                      <a href="<?=SITE_URL?>?route=view_students" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Student Data"><i class="fa fa-refresh"></i></a>
                                  
                                     <?php if($record_status == 'active'){ ?>
                                       <?php if($showStatusDropdown){ ?>
                                          <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid = "all" data-type="student" data-ptype="Student" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
                                       <?php } ?>   

                                      <?php }else{ ?>   
                                     
                                          <?php if($showStatusDropdown){ ?>
                                            <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid = "all" data-type="student" data-ptype="Student" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
                                          <?php } ?>  

                                          <?php if($deletePermission){ ?>
                                             <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid = "all" data-type="student" data-ptype="Student" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
                                           <?php } ?>  

                                     <?php } ?>   
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
                                <div class="table-responsive project-list">

                                    <input type="text" class="form-control form-control-sm m-b-xs" id="student_tbl_filter" placeholder="Search in student table by student name, id, phone no or franchise...">

                                    <table class="table table-striped table-bordered table-hover text-center mt-3" id="student_list_tbl">
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
                                                <!--<th>SL No.</th>-->
                                                <th class="notexport">Image</th>
                                                <th class="sorting_desc_disabled">Name<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                                
                                                <th class="sorting_desc_disabled">Student Info</th>
                                                <th class="sorting_desc_disabled">Franchise/Course</th>
                                                
                                                <?php if($resultUpdatePermission == false?($showResultData == false ? true:false):true){ ?> 
                                                    <th class="sorting_desc_disabled">Result</th>
                                                <?php } ?>    
                                                
                                                <?php //if($updatePermission == false?($showStatusColumn == true ? true:false):true){ ?>
                                                    <th class="sorting_desc_disabled notexport">Status</th>
                                                <?php //} ?>    
                                                    
                                                <th class="sorting_desc_disabled notexport">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          <?php 
                                             if(count($studentListArr)>0){
                                               foreach($studentListArr as $index => $content){
                                                $student_image_url = USER_UPLOAD_DIR.'student/'.$content->image_file_name;

                                                if (!strlen($content->image_file_name)>0 || !file_exists($student_image_url)) {   
                                                  $student_image_url = RESOURCE_URL.'images/default-user-avatar.jpg'; 
                                                }else{
                                                  $student_image_url = USER_UPLOAD_URL.'student/'.$content->image_file_name;
                                                }

                                                if($content->student_status != 'course_complete'){
                                                    $student_status = ucfirst($content->student_status);
                                                }else{
                                                    $student_status = 'Course Complete';
                                                }
                                          ?> 
                                              <tr id="stu_tr_<?=$content->stu_id?>" style="background-color:<?=($content->verified_status == '0' ? '#f1d0d0;':'')?>">
                                                <td style="width: 6%;">
                                                  <div class="pretty p-image p-plain selectAllItem ml-2">
                                                       <input type="checkbox" class="singleCheck" id="<?=$content->id?>" value="<?=$content->id?>"/>
                                                       <div class="state">
                                                          <img class="image" src="<?=RESOURCE_URL?>images/checkbox.png">
                                                          <label class="cursor-pointer selectAllItem" for="<?=$content->id?>"></label>
                                                       </div>
                                                    </div> 
                                                </td>
                                                <!--<td><?=$index+1?></td>-->
                                                <td class="client-avatar" style="width:6%;">
                                                    <a href="<?=$student_image_url?>" data-fancybox="gallery" data-caption="<?=$content->stu_name?>">
                                                    <img alt="image" title="Neel Banerjee" src="<?=$student_image_url?>">
                                                    </a> 
                                                </td>

                                                <td class="project-title" style="width: 17%;">
                                                    <a href="javascript:void(0);" class="viewStudentDetail" data-sid="<?=$content->id?>" data-toggle="tooltip" data-placement="bottom" title="Student Name: <?=$content->stu_name?>"><?=(strlen($content->stu_name)>12?substr($content->stu_name,0,12)."...":$content->stu_name)?></a>
                                                    <br/>
                                                    <small>Created <?=date('jS F, Y',strtotime($content->created_at))?></small>
                                                </td>

                                                <td class="project-title" style="width: 17%;">
                                                    <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student ID: <?=$content->stu_id?>"><strong><?=$content->stu_id?></strong></span><br>
                                                    <small><strong>Student Contact: <?=$content->stu_phone?></strong></small>
                                                </td>

                                                <td class="project-title" style="width: 21%;">
                                                    <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Franchise Name: <?=$content->center_name?>"><?=(strlen($content->center_name)>0 ? (strlen($content->center_name)>14?substr($content->center_name,0,14)."...":$content->center_name):'<h5 style="color:red;">No Franshise available!</h5>')?></span><br>
                                                    <small class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Course Name: <?=$content->course_title?>"><strong>Course: <?=(strlen($content->course_title)>25?substr($content->course_title,0,25)."...":$content->course_title)?></strong></small>
                                                </td>
                                                
                                                <?php 
                                                    if($resultUpdatePermission){ 
                                                ?>    
                                                    <td class="project-title" style="width: 10%;">
                                                        <span class="dropdown">
                                                          <button type="button" class="btn btn-primary product-btn dropdown-toggle btn-xs" data-toggle="dropdown">
                                                            <?=(($content->stu_result == 'qualified'?'<i class="fa fa-check-circle"></i> ':'').ucfirst($content->stu_result))?>
                                                          </button>
                                                          <ul class="dropdown-menu">

                                                            <?php if($content->stu_result !== 'qualified'){ ?> 
                                                               <li>
                                                                 <a href="javascript:void(0)" class="changeStudentStatus" data-sid = "<?=$content->stu_id?>" data-stype="result" data-status= "qualified" data-type="student" data-ptype="Student" data-toggle="tooltip" data-placement="bottom" title="Change student result to qualified" style="font-size: inherit;"><i class="fa fa-check-circle"></i> Qualified</a>
                                                               </li>
                                                            <?php } ?>

                                                             <?php if($content->stu_result !== 'unqualified'){ ?> 
                                                               <li>
                                                                 <a href="javascript:void(0)" class="changeStudentStatus" data-sid = "<?=$content->stu_id?>" data-stype="result" data-status= "unqualified" data-type="student" data-ptype="Student" data-toggle="tooltip" data-placement="bottom" title="Change student result to unqualified" style="font-size: inherit;">Unqualified</a>
                                                               </li>
                                                            <?php } ?>
                                                        </ul>
                                                       </span> 
                                                    </td>
                                                <?php }elseif(!$showResultData){ ?> 
                                                    <td>
                                                        <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student Result: <?=ucfirst($content->stu_result)?>"><strong><?=ucfirst($content->stu_result)?></strong></span>
                                                    </td>
                                                <?php } ?>    
                                            
                                                <?php if($showStatusDropdown){ ?> 
                                                    <td class="project-title" style="width: 10%;">
                                                        <span class="dropdown">
                                                          <button type="button" class="btn btn-success product-btn dropdown-toggle btn-xs" data-toggle="dropdown">
                                                            <?=(strlen($content->student_status)>0? ($content->student_status != 'course_complete'?ucfirst($content->student_status):'Complete') : 'No status selected')?>
                                                          </button>
                                                          <ul class="dropdown-menu">

                                                            <?php if($content->student_status !== 'admitted'){ ?> 
                                                               <li>
                                                                 <a href="javascript:void(0)" class="changeStudentStatus" data-sid = "<?=$content->stu_id?>" data-stype="status" data-status= "admitted" data-type="student" data-ptype="Student" data-toggle="tooltip" data-placement="bottom" title="Change student status to Continue" style="font-size: inherit;">Admitted</a>
                                                               </li>
                                                            <?php } ?>

                                                             <?php if($content->student_status !== 'continue'){ ?> 
                                                               <li>
                                                                 <a href="javascript:void(0)" class="changeStudentStatus" data-sid = "<?=$content->stu_id?>" data-stype="status" data-status= "continue" data-type="student" data-ptype="Student" data-toggle="tooltip" data-placement="bottom" title="Change student status to Continue" style="font-size: inherit;">Continue</a>
                                                               </li>
                                                            <?php } ?>

                                                             <?php if($content->student_status !== 'course_complete'){ ?> 
                                                               <li>
                                                                 <a href="javascript:void(0)" class="changeStudentStatus" data-sid = "<?=$content->stu_id?>" data-stype="status" data-status= "course_complete" data-type="student" data-ptype="Student" data-toggle="tooltip" data-placement="bottom" title="Change student status to Course Complete" style="font-size: inherit;"> Course Complete</a>
                                                               </li>
                                                            <?php } ?>

                                                            <?php if($content->student_status !== 'dropout'){ ?> 
                                                               <li>
                                                                 <a href="javascript:void(0)" class="changeStudentStatus" data-sid = "<?=$content->stu_id?>" data-stype="status" data-status= "dropout" data-type="student" data-ptype="Student" data-toggle="tooltip" data-placement="bottom" title="Change student status to Dropout" style="font-size: inherit;">Dropout</a>
                                                               </li>
                                                            <?php } ?>
                                                           
                                                        </ul>
                                                       </span> 
                                                    </td>
                                                <?php }else{ ?> 
                                                    <td>
                                                        <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student Status: <?=$student_status?>"><strong><?=$student_status?></strong></span>
                                                        <?php if($showResultData){ ?>
                                                            <br><small><strong>Student Result: <?=ucfirst($content->stu_result)?></strong></small>
                                                        <?php } ?>
                                                    </td>  
                                                <?php } ?>   

                                                <td class="project-status" style="width: 10%;">
                                                     <span class="dropdown">
                                                      <button class="btn btn-primary product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                                     <ul class="dropdown-menu">
                                                        <?php if($updatePermission){ ?>
                                                           <li>
                                                             <a href="<?=SITE_URL?>?route=edit_student&id=<?=$content->id?>" data-toggle="tooltip" data-placement="bottom" title="Edit this student"><i class="fa fa-pencil"></i> Edit Student</a>
                                                           </li> 
                                                        <?php } ?>  

                                                        <?php if($showStatusDropdown){ ?> 
                                                           <li>
                                                             <a href="<?=SITE_URL?>?route=clone_student&id=<?=$content->id?>" data-toggle="tooltip" data-placement="bottom" title="Clone this student"><i class="fa fa-clone"></i> Clone Student</a>
                                                           </li> 
                                                        <?php } ?>  

                                                         <?php if($updatePermission && ($_SESSION['user_type'] == "admin" || $_SESSION['user_type'] == "developer")){ ?> 

                                                            <li>
                                                                <a href="javascript:void(0)" id="item_<?=$content->stu_id?>" class="verified_action" data-vstatus="<?=($content->verified_status=='1'?'0':'1')?>" data-sid="<?=$content->stu_id?>" data-toggle="tooltip" data-placement="bottom" title="Make this receipt's status <?=($content->verified_status=='1'?'not verified':'verified')?>"><i class="<?=($content->verified_status=='1'?'fa fa-check-circle':'fa fa-info-circle')?>"></i> <?=($content->verified_status=='1'?'Verified':'Not-Verified')?> 
                                                                </a>
                                                            </li>

                                                        <?php } ?>      

                                                       <?php if($content->record_status == 'active'){?>
                                                          
                                                           <?php if($showStatusDropdown){ ?>
                                                                <li>
                                                                   <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$content->id?>" data-type="student" data-ptype="Student" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Student"><i class="fa fa-trash"></i> Block Student</a>
                                                                </li>
                                                            <?php } ?>    

                                                             <?php if(!$updatePermission){ ?>
                                                               <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                                            <?php } ?>   
                                                        <?php }else{ ?>
                                                             <?php if($showStatusDropdown){ ?>
                                                               <li>
                                                                 <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$content->id?>" data-type="student" data-ptype="Student" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore this Student"><i class="fa fa-refresh"></i> Restore Student</a>
                                                               </li>
                                                            <?php } ?>    

                                                            <?php if($deletePermission){ ?>
                                                               <li>
                                                                 <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$content->id?>" data-type="student" data-ptype="Student" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete this Student"><i class="fa fa-times"></i> Delete Student</a>
                                                               </li>
                                                            <?php } ?> 

                                                            <?php if(!$updatePermission && !$deletePermission){ ?>
                                                               <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                                            <?php } ?> 

                                                         <?php } ?> 

                                                         <?php if($createReceiptPermission){ ?>
                                                           <li>
                                                             <a href="<?=SITE_URL.'?route=view_receipts&actionType=create&stu_id='.$content->stu_id?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Create receipt fot this student"><i class="fa fa-plus-circle"></i> Create Receipt</a>
                                                           </li> 
                                                        <?php } ?>   

                                                     </ul>
                                                   </span>  
                                                  </td>
                                                </tr>
                                           <?php } }else{ ?>
                                              <tr>
                                                <td colspan="8">No student data found...!</td>
                                              </tr>  
                                          <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <nav aria-label="Student Page navigation">
                                  <ul class="pagination">

                                    <?php 
                                       if($totalPageNo > 1){
                                           if($pageNo == 1){
                                              $pervious_link = "javascript:void(0);";
                                           }else{
                                              $pervious_link = SITE_URL.'?route=view_students'.$extra_query_str.'&pageNo='.($pageNo-1);
                                           }
                                           $next_link = SITE_URL.'?route=view_students'.$extra_query_str.'&pageNo='.($pageNo+1);
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
                                          <a class="page-link" href="<?=SITE_URL.'?route=view_students'.$extra_query_str.'&pageNo='.$page?>"><?=$page?></a>
                                        </li>

                                    <?php 
                                        }elseif($pageNo >= 5 && $page != $totalPageNo){ 
                                           if($page ==1){
                                    ?>

                                        <li class="page-item <?=($page==$pageNo?'active':'')?>">
                                          <a class="page-link" href="<?=SITE_URL.'?route=view_students'.$extra_query_str.'&pageNo=1'?>"><?=$page?></a>
                                        </li>
                                    
                                    <?php }elseif($page == $pageNo-2){ ?>   

                                        <li class="page-item">
                                          <a class="page-link" href="javascript:void(0);">...</a>
                                        </li>

                                    <?php }elseif($page == $pageNo-1 || $page == $pageNo || $page == $pageNo+1){ ?>     
                                        
                                         <li class="page-item <?=($page==$pageNo?'active':'')?>">
                                          <a class="page-link" href="<?=SITE_URL.'?route=view_students'.$extra_query_str.'&pageNo='.$page?>"><?=$page?></a>
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
                                          <a class="page-link" href="<?=SITE_URL.'?route=view_students'.$extra_query_str.'&pageNo=1'?>"><?=$page?></a>
                                        </li>

                                    <?php }elseif($page >= $totalPageNo-4){ ?>     

                                        <li class="page-item <?=($page==$pageNo?'active':'')?>">
                                          <a class="page-link" href="<?=SITE_URL.'?route=view_students'.$extra_query_str.'&pageNo=1'?>"><?=$page?></a>
                                        </li>  

                                    <?php 
                                        } }elseif($page == 6 && $pageNo < 5){ 
                                    ?>    

                                        <li class="page-item">
                                          <a class="page-link" href="javascript:void(0);">...</a>
                                        </li>

                                    <?php }elseif($page == $totalPageNo){ ?> 

                                        <li class="page-item <?=($page==$pageNo?'active':'')?>">
                                          <a class="page-link" href="<?=SITE_URL.'?route=view_students'.$extra_query_str.'&pageNo='.$page?>"><?=$page?></a>
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


            <!-- Modal window div-->
             <div class="modal fade show" id="showStudentDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                   <div class="modal-content animated fadeIn">
                        <div class="modal-header">
                             <h3 class="modal-title" id="result_modal_title">Student Details</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">×</span>
                            </button> 
                        </div>
                        <div class="modal-body">

                          <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                              <h5><i class="fa fa-clone"></i> General Information</h5>    
                              <div class="table-responsive pt-0">
                                <table class="table table-bordered">
                                  <tr>
                                    <th width="30%">Student's Name</th>
                                    <td width="2%">:</td>
                                    <td id="stu_name">Not Available</td>
                                  </tr>  
                                  <tr>
                                    <th width="30%">Father's Name</th>
                                    <td width="2%">:</td>
                                    <td id="stu_father_name">Not Available</td>
                                  </tr>
                                  <tr>
                                    <th width="30%">Student ID</th>
                                    <td width="2%">:</td>
                                    <td id="stu_id">Not Available</td>
                                  </tr>  
                                  <tr>
                                    <th width="30%">Contact No</th>
                                    <td width="2%">:</td>
                                    <td id="stu_phone">Not Available</td>
                                  </tr>  
                                   <tr>
                                    <th width="30%">Email</th>
                                    <td width="2%">:</td>
                                    <td id="stu_email">Not Available</td>
                                  </tr>
                                  <tr>
                                    <th width="30%">Date of Birth</th>
                                    <td width="2%">:</td>
                                    <td id="stu_dob">Not Available</td>
                                  </tr>
                                  <tr>
                                    <th width="30%">Student's Status</th>
                                    <td width="2%">:</td>
                                    <td id="modal_student_status">Not Available</td>
                                  </tr>
                                  <tr>
                                    <th width="30%">Franchise</th>
                                    <td width="2%">:</td>
                                    <td id="center_name">Not Available</td>
                                  </tr>
                                  <tr>
                                    <th width="30%">Student's Result</th>
                                    <td width="2%">:</td>
                                    <td id="stu_result">Not Available</td>
                                  </tr>
                                </table>
                              </div>
                            </div>  
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                              <h5><i class="fa fa-clone pr-1"></i>Other Information</h5>
                              <div class="table-responsive pt-0">
                                   <table class="table table-bordered">
                                      <tr>
                                        <th width="30%">Course Name</th>
                                        <td width="2%">:</td>
                                        <td id="course_title">Not Available</td>
                                      </tr>
                                     <tr>
                                        <th width="30%">Address</th>
                                        <td width="2%">:</td>
                                        <td id="stu_address">Not Available</td>
                                      </tr>
                                      <tr>
                                        <th width="30%">Qualification</th>
                                        <td width="2%">:</td>
                                        <td id="stu_qualification">Not Available</td>
                                      </tr>
                                      <tr>
                                        <th width="30%">Gender</th>
                                        <td width="2%">:</td>
                                        <td id="stu_gender">Not Available</td>
                                      </tr>

                                      <tr>
                                        <th width="30%">Marital Status</th>
                                        <td width="2%">:</td>
                                        <td id="stu_marital_status">Not Available</td>
                                      </tr>
                                    </table>
                               </div>
                            </div> 
                        </div>
                        
                        <?php if($showStatusDropdown){ ?> 
                          <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                              <h5><i class="fa fa-clone pr-1"></i>Receipt Information</h5>
                              <div class="table-responsive pt-0">
                                   <table class="table table-bordered">
                                      <tr>
                                        <th width="30%">Course Fees</th>
                                        <td width="2%">:</td>
                                        <td id="course_fees">Not Available</td>
                                      </tr>
                                      <tr>
                                        <th width="30%">Course Discount</th>
                                        <td width="2%">:</td>
                                        <td id="course_discount">Not Available</td>
                                      </tr>
                                      <tr>
                                        <th width="30%">Net Course Fees</th>
                                        <td width="2%">:</td>
                                        <td id="net_course_fees">Not Available</td>
                                      </tr>
                                      <tr>
                                        <th width="30%">Advance Fees (Included in Fees paid so far)</th>
                                        <td width="2%">:</td>
                                        <td id="stu_advanced_fees">Not Available</td>
                                      </tr>
                                      <tr>
                                        <th width="30%">Fees Paid Before DR</th>
                                        <td width="2%">:</td>
                                        <td id="stu_fees_paid_before_dr">Not Available</td>
                                      </tr>
                                     <tr>
                                        <th width="30%">Fees Paid So Far</th>
                                        <td width="2%">:</td>
                                        <td id="fees_paid">Not Available</td>
                                      </tr>
                                      <tr>
                                        <th width="30%">Fees Due</th>
                                        <td width="2%">:</td>
                                        <td id="fees_due">Not Available</td>
                                      </tr>
                                    </table>
                               </div>
                            </div> 
                          </div>
                        <?php } ?>  
                    </div>
                    <div class="modal-footer">
                          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                     </div>
                </div>
              </div> 
           </div>   
            <!-- Modal ends here -->
       <script>
         
        $(document).ready(function () {
          
          $('.record_status').select2({width: "91%",allowClear: true});
          $('.student_status').select2({width: "90%",allowClear: true});
          $('.result_status').select2({width: "92%",allowClear: true});
          $('.course').select2({width: "91%",allowClear: true});
          $('.franchise').select2({width: "90%",allowClear: true});

          var current_franchise_id = $("#franchise_id").val();
          var current_course_id = $("#course_id").val();

           /*$('#student_list_tbl').tableFilter({
              
              placeholder:'Search in student table by student name, id, phone no or franchise...',

              class: 'form-control form-control-sm',
              style: 'font-size:12px',
              min: 2,
 
              // include column 1 and 2
              inCols: '2,3,4',

              // exclude column 3 and 4
              excludeCols: '0,1'
              
           });*/
           $('#student_list_tbl').filterTable('#student_tbl_filter');

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

          //Search student params handling
          $(document).on('click','.serach_studen_option',function(){
             var stu_search_option = $(this).data('stusrchopt');
             //console.log(stu_search_option);
             if(stu_search_option == 'based_on_stu_id'){
               $('#fetch_single_student_record').removeClass('d-none');
               $('#fetch_all_student_records').addClass('d-none'); 
             }else{
               $('#fetch_all_student_records').removeClass('d-none'); 
               $('#fetch_single_student_record').addClass('d-none'); 
             }
             return false;
          });

         //handling student detail fetch form
         $(document).on('click','.viewStudentDetail', function(event){
           event.preventDefault();  
           
           var student_id = $(this).data('sid');
           var formData = {action:"fetchStudentDetailInModal",student_id:student_id}

           //Calling ajax request
           $.ajax({
              url:ajaxControllerHandler,
              method:'POST',
              data: formData,
              beforeSend: function() {
                 $('.content_div_loader').addClass('sk-loading');
              },
              success:function(responseData){
                 var data = JSON.parse(responseData);
                 //console.log(responseData);
                 if(data.check == 'success'){
                    //Populating student data in student detail div
                    var studentDetail = data.studentDetail;
                    //console.log(studentDetail); 
                    //populating student detail div
                    $('#student_dp').attr('src',studentDetail.student_dp);
                    $('#student_dp_fancybox').attr('href',studentDetail.student_dp);
                    //populating other fields
                    $('#stu_id').html('<b>'+studentDetail.stu_id+'</b>');
                    $('#stu_phone').text(studentDetail.stu_phone);
                    $('#stu_result').html('<b>'+studentDetail.stu_result+'</b>');
                    $('#focus_stu_name').text(studentDetail.stu_name);
                    $('#stu_name').text(studentDetail.stu_name);
                    $('#stu_father_name').text(studentDetail.stu_father_name);
                    $('#stu_address').text(studentDetail.stu_address);
                    $('#stu_dob').text(studentDetail.stu_dob);
                    $('#course_title').text(studentDetail.course_title);
                    $('#center_name').text(studentDetail.center_name);
                    $('#stu_email').text(studentDetail.stu_email);
                    $('#stu_qualification').text(studentDetail.stu_qualification);
                    $('#modal_student_status').html('<b>'+studentDetail.student_status+'</b>');
                    $('#stu_gender').text(studentDetail.stu_gender);
                    $('#stu_marital_status').text(studentDetail.stu_marital_status);

                    //Receipt Data display
                    if(studentDetail.stu_course_fees){
                       var course_fees = parseInt(studentDetail.stu_course_fees);
                    }else{
                       var course_fees = parseInt(studentDetail.course_default_fees);
                    }

                    $('#course_fees').text('Rs.'+course_fees);

                    if(studentDetail.stu_course_discount){
                       var stu_course_discount = parseInt(studentDetail.stu_course_discount); 
                    }else{
                       var stu_course_discount = parseInt('0'); 
                    }

                    $('#course_discount').text('Rs.'+stu_course_discount);

                    var net_course_fees =  course_fees - stu_course_discount;

                    $('#net_course_fees').html('Rs.'+net_course_fees); 

                    if(studentDetail.advanced_fees){
                        var advanced_fees = parseInt(studentDetail.advanced_fees);
                        $('#stu_advanced_fees').text('Rs.'+advanced_fees+' has been deposited on '+ studentDetail.advance_fees_date);
                    }else{
                        var advanced_fees = parseInt('0');
                        $('#stu_advanced_fees').text('Rs.0');
                    } 

                    if(studentDetail.course_fees_paid){
                        var stu_receipt_paid = parseInt(studentDetail.course_fees_paid);
                    }else{
                        var stu_receipt_paid = parseInt('0'); 
                    }

                    if(studentDetail.fees_paid_before_dr){
                        var fees_paid_before_dr = parseInt(studentDetail.fees_paid_before_dr);
                        $('#stu_fees_paid_before_dr').text('Rs.'+studentDetail.fees_paid_before_dr+' has been deposited before digital receipt.');
                    }else{
                        var fees_paid_before_dr = parseInt('0'); 
                        $('#stu_fees_paid_before_dr').text('Rs.0');
                    }   

                    var course_fees_paid = parseInt(stu_receipt_paid + advanced_fees + fees_paid_before_dr); 

                    if(course_fees_paid>0){
                        $('#fees_paid').text('Rs.'+course_fees_paid);
                    }else{
                        $('#fees_paid').text('No fees has been paid yet!');
                    }

                    var fees_due = parseInt(net_course_fees - course_fees_paid);

                    $('#fees_due').text('Rs.'+fees_due);

                    setTimeout(function() {
                        //disabling loader
                        $('.content_div_loader').removeClass('sk-loading');
                        //Display modal
                        $("#showStudentDetailModal").modal("show");
                        return true;
                    }, 500);     
                 }else{
                    //show sweetalert success
                     if(data.message.length>0){
                       var message = data.message;
                    }else{
                       var message = "Something went wrong";
                    }
                    return false;
                 }
              }
             });
          });
          //Handling student status change
          $(document).on('click', '.changeStudentStatus', function(e){
            e.preventDefault();
            
            var stu_id = $(this).data('sid');
            var status_type = $(this).data('stype');
            var status_data = $(this).data('status');
            var page_route = $('#page_route').val();
            var formData = {action:"changeStudentStatus",stu_id:stu_id,status_type:status_type,status_data:status_data};

            if(status_type == "status"){
               var alertText = "Are you sure to change this student status?"; 
               var successTxt = "Student status has been successfully updated!";
            }else{
               var alertText = "Are you sure to change this student result?";  
               var successTxt = "Student result has been successfully updated!";
            }

             swal({
                title: "Are you sure?",
                text: alertText,
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
                        beforeSend: function() {
                          $('.content_div_loader').addClass('sk-loading');
                        },
                        success:function(responseData){
                           var data = JSON.parse(responseData);
                           //console.log(responseData);
                           if(data.check == 'success'){
                              //show sweetalert success
                              setTimeout(function() {
                                  $('.content_div_loader').removeClass('sk-loading');
                                  swal({
                                    title: "Success!",
                                    text: successTxt,
                                    type: "success"
                                  }, function() {
                                      window.location = "<?=SITE_URL?>?route="+page_route;
                                  });
                              }, 1000);
                             return true; 
                           }else{
                              setTimeout(function() {
                                  $('.content_div_loader').removeClass('sk-loading');
                                      swal({
                                      title: "Oops!",
                                      text: data.message,
                                      type: "error"
                                  });
                              }, 1000);
                              return false;
                           }
                        }
                     });
                });   
           });

          //Configuring page records fetching params
          $(document).on('submit', '#fetch_verified_records', function(event){
              event.preventDefault();
              var verified_status = $('#verified_status').val();

              if(verified_status === null){
                  window.location = SITE_URL+"?route=view_students";
              }else{
                $('#fetch_item_data').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Fetching').attr('disabled',true);
                 setTimeout(function(){
                 $('#fetch_item_data').html('<i class="fa fa-search"></i>&nbsp;Fetch Data').attr('disabled',false);
                 //show sweetalert success
                 swal({
                  title: "Great!",
                  text: "Data has been successfully fetched!",
                  type: "success",
                  allowEscapeKey : false,
                  allowOutsideClick: false
                 },function(){
                   window.location = SITE_URL+"?route=view_students&verified_status="+verified_status;
                 });},500);
                 return true;  
             } 
          });

          //Configuring fetching all page records fetching params
          $(document).on('submit', '#fetch_all_student_records', function(event){
              event.preventDefault();

              var record_status = $('#record_status').val();
              var student_status = $('#student_status').val();
              var result_status = $('#result_status').val();
              var search_string = $('#search_string').val();
              var page_route = $('#page_route').val();

              var course_id = $('#course_id').val();
              var franchise_id = $('#franchise_id').val();

              var created = $('#created').val();

              var search_start = $('#search_start').val(); 
              var search_end = $('#search_end').val();

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
                   var redirect_url = SITE_URL+"?route="+page_route+"&record_status="+record_status;

                   if(student_status){
                      redirect_url += "&student_status="+student_status;
                   }

                   if(result_status){
                      redirect_url += "&result_status="+result_status;
                   }
                   
                   if(course_id>0){
                      redirect_url += "&course_id="+course_id;
                   }
                   
                   if(franchise_id>0){
                      redirect_url += "&franchise_id="+franchise_id;
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

                   if(created.length>0){
                      redirect_url += "&created="+created;
                   }   
                   
                   window.location = redirect_url;
                  
                 });},500);
                 return true;  
             } 
           });

          //Handling hard export for student table
          $(document).on('click', '.export_student_table_data', function(event){
              event.preventDefault();
              var record_status = $('#record_status').val();
              var record_limit = $('#record_limit').val();
              var pageNo = $('#pageNo').val();

              var course_id = $('#course_id').val();
              var franchise_id = $('#franchise_id').val();

              var search_string = $('#search_string').val();

              var search_start = $('#search_start').val(); 
              var search_end = $('#search_end').val();

              var created = $('#created').val();


              var export_method = $(this).data('export');

              var formData = {export_table:"student",record_status:record_status,record_limit:record_limit,pageNo:pageNo,
                                   course_id:course_id,franchise_id:franchise_id,search_start:search_start,search_end:search_end,created:created,search_string:search_string,export_method:export_method};

              //Clearing hyperlink href for fresh download
              $('#export_record_href').attr("href","javascript:void(0);");
              $('#export_record_href').attr("download","");

              if(record_limit>360 && export_method == "pdf"){
                 var recordLimitErrorTxt = "Exporting more than 350 records is restricted for server resource limitation! Please use the CSV format to export data efficeently.";
                 toastr.error(recordLimitErrorTxt, "Error!",{ timeOut: 10000 });
                 return false;
              }

              if(export_method == "excel"){
                var exportAlertText = "All table data will be exported as CSV file!";  
              }else{
                var exportAlertText = "All table data will be exported as PDF & this may take a while!"; 
              }

              //show sweetalert success
              swal({
                   title: "Are you sure?",
                   text: exportAlertText,
                   type: "warning",
                   showCancelButton: true,
                   confirmButtonColor: "#DD6B55",
                   confirmButtonText: "Yes, Go ahead...",
                   closeOnConfirm: true
                 },function(){
                   
                    $.ajax({
                      url:exportTableDataController,
                      method:'POST',
                      data: formData,
                      beforeSend: function() {
                         $('.content_div_loader').addClass('sk-loading');
                      },
                      success:function(responseData){
                          $('.content_div_loader').removeClass('sk-loading');
                          //console.log(responseData);
                          var result = JSON.parse(responseData);
                          $('#export_record_href').attr("href",result.file_url);
                          $( "#hidden_export_button").click();
                          //Removing file from server
                          setTimeout(function() {
                            removeFileFromServer(result.file_upload_dir);
                          }, 5000);
                          return true;     
                       }
                    });
                   
                 });
             }); 

             //handling import data div
               $(document).on('click','.handle_import_div',function(){
                   var handle_type = $(this).data('htype');
                   if(handle_type == "show"){
                     $('#import_data_div').removeClass('d-none');
                   }else{
                     $('#import_data_div').addClass('d-none');  
                   } 
                   return true; 
               });

               //Check file extension before uploading to import data
                $(document).on('change','#importDataCSV',function() {
                   var file = this.files[0];
                   var fileType = file["type"];
                   //console.log(fileType);return false;
                   var validFileTypes = ["application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
                   if($.inArray(fileType, validFileTypes) < 0) {
                     toastr.error("Only csv file allowed!", "Upload error!"); 
                     $(this).val('');
                     $('#import_data_submit').attr('disabled',true);
                     return false;
                   }else{
                     $('#import_data_submit').attr('disabled',false);
                     return false;
                   }
                });  

                //Check file extension before uploading to import data
                $(document).on('change','#import_data_file',function() {
                   var file = this.files[0];
                   var fileType = file["type"];
                   //console.log(fileType);return false;
                   var validFileTypes = ["application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
                   if ($.inArray(fileType, validFileTypes) < 0) {
                     swal("Error!",langaugeObj.file_type_err, "error"); 
                     $(this).val('');
                     $('#import_data_submit').attr('disabled',true);
                     return false;
                   }else{
                     $('#import_data_submit').attr('disabled',false);
                     return false;
                   }
                });  

                //ADMIN TABLE DATA IMPORT FORM SUBMIT HANDLER
                $(document).on('submit', '#import_table_data_form', function(e){
                    e.preventDefault();
                    
                    var formData = new FormData(this);

                    swal({
                     title: "Are you sure?",
                     text: "Are you sure to import these data?",
                     type: "warning",
                     showCancelButton: true,
                     confirmButtonColor: "#DD6B55",
                     confirmButtonText: "Yes, Go ahead!",
                     closeOnConfirm: true
                    },
                    function() {
                       $.ajax({
                          type: 'POST',
                          url: importTableDataController,
                          data: formData,
                          contentType: false,
                          processData: false,
                          beforeSend: function(){
                             $('.content_div_loader').addClass('sk-loading');
                          },
                          success: function(responseData){
                               setTimeout(function() {
                                  $('.content_div_loader').removeClass('sk-loading');
                                  var data = JSON.parse(responseData);
                                  //console.log(responseData);
                                  if(data.check == 'success'){
                                     var redirect_url = SITE_URL+"?route=view_students&record_status=blocked";
                                     //define toastr error
                                     toastr.options = {
                                       closeButton: true,
                                       progressBar: true,
                                       showMethod: 'slideDown',
                                       timeOut: 2000
                                     };
                                     toastr.options.onHidden = function() { window.location = redirect_url; }
                                     toastr.success(data.message, 'Success!');
                                   }else{
                                     if(data.message.length>0){
                                        var message = data.message;
                                     }else{
                                        var message = "Something went wrong";
                                     }
                                     toastr.error(message, "Upload error!"); 
                                     return false;
                                   }
                                }, 1000);
                            }
                        });
                    });  
                });


               /*Status change handler*/
               $(document).on('click','.verified_action',function(){
                  var action = "updateStudentVerifiedStatus";  
                  var student_id = $(this).data('sid'); 
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

                  var formData = {action:action,student_id:student_id,verified_status:verified_status};
                  
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
                              if(verified_Status == '1'){
                                $(thisItem).data('vstatus', '0');
                                $(thisItem).attr('data-original-title',"Make this receipt's status not verified!");
                                $(thisItem).html('<i class="fa fa-check-circle"></i> Verified');

                                //Chnage table tr background color
                                $("#stu_tr_"+student_id).css({'background-color':''});
                                //Show success toast
                                toastr.success(toastrText, 'Success!');

                              }else{
                                $(thisItem).data('vstatus', '1');
                                $(thisItem).attr('data-original-title',"Make this receipt's status verified!");
                                $(thisItem).html('<i class="fa fa-info-circle"></i> Not Verified');
                                //Chnage table tr background color
                                $("#stu_tr_"+student_id).css({'background-color':'#f1d0d0'});
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
</html>
