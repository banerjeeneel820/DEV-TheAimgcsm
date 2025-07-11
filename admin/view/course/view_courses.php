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
  //Configiring course data
  $courseListArr = $pageContent['pageData']['data'];

  //Fetching page action permission
  $createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_course"); 
  $updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_course"); 
  $deletePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("delete_course"); 
?>
                    
            <div class="wrapper wrapper-content fadeInRight">  
                 <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h5>Fetch Franchise based on their status </h5>
                                <div class="ibox-tools">
                                    <a class="collapse-link">
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="ibox-content">
                               <form id="fetch_all_records" onsubmit="return false;">
                                 <div class="row">
                                    <div class="col-sm-10 m-b-xs">
                                      <select class="form-control-sm form-control input-s-sm inline record_status" name="record_status" id="record_status">
                                        <option selected disabled value>Select a Data type to proceed</option>
                                        <option value="active" <?=(($record_status =='active' || $record_status =='')?'selected':'')?>>Active</option>  
                                        <option value="blocked" <?=($record_status=='blocked'?'selected':'')?>>Blocked</option>  
                                      </select>
                                    </div>
                                    <input type="hidden" name="page_route" id="page_route" value="<?=$_GET['route']?>">
                                 
                                    <div class="col-sm-2">
                                      <button class="btn btn-primary" type="submit" id="fetch_item_data"><i class="fa fa-search"></i>&nbsp;Fetch Data</button>
                                    </div>
                                </div>
                              </form>
                            </div>
                        </div>
                      </div>
                   </div>

                   <div class="row">
                   <div class="col-lg-12">

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Course List with all details</h5>
                            <div class="ibox-tools">
                                <?php if($createPermission){ ?>  
                                     <a href="<?=SITE_URL?>?route=add_course" class="table-action-primary" data-toggle="tooltip" data-placement="bottom" title="Add New Course"><i class="fa fa-plus-circle"></i></a>
                                 <?php }?>

                                  <a href="<?=SITE_URL?>?route=view_courses" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Course Data"><i class="fa fa-refresh"></i></a>
                              
                                 <?php if($record_status == 'active'){ ?>
                                   <?php if($updatePermission){ ?>
                                      <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid = "all" data-type="course" data-ptype="Course" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
                                   <?php } ?>   
                                <?php }else{ ?>   
                                  <?php if($updatePermission){ ?>
                                    <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid = "all" data-type="course" data-ptype="Course" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
                                  <?php } ?>  

                                  <?php if($deletePermission){ ?>
                                     <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid = "all" data-type="course" data-ptype="Course" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
                                   <?php } ?>  
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

                                <table class="table table-striped table-bordered table-hover dataTables-example text-center">
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
                                            <!--<th class="sorting_desc_disabled">SL No.</th>-->
                                            <th class="notexport">Image</th>
                                            <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Course Title">Title<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                            <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Course Fees" style="width:12%;">Fees<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                            <!--<th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Course Duration">Duration<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>-->

                                            <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="No of student enrolled in this course">Students<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                           
                                            <th class="sorting_desc_disabled notexport" data-toggle="tooltip" data-placement="bottom" title="Course Status">Status<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                            <th class="sorting_desc_disabled notexport" data-toggle="tooltip" data-placement="bottom" title="Course PDF" style="width:10%;">PDF</th>
                                            
                                            <th class="sorting_desc_disabled notexport">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php 
                                        foreach($courseListArr as $index => $content){
                                            
                                            $course_thumbnail = USER_UPLOAD_DIR.'course/'.$content->course_thumbnail;

                                            if (!strlen($content->course_thumbnail)>0 || !file_exists($course_thumbnail)) {   
                                              $course_thumbnail = RESOURCE_URL.'images/preview.jpg'; 
                                            }else{
                                              $course_thumbnail = USER_UPLOAD_URL.'course/'.$content->course_thumbnail;
                                            }

                                            if(strlen($content->course_pdf)>0 && file_exists(USER_UPLOAD_DIR.'course/'.$content->course_pdf)){
                                               $course_pdf = USER_UPLOAD_URL.'course/'.$content->course_pdf;
                                            }else{
                                               $course_pdf = RESOURCE_URL.'images/COMPUTER-COURSE.pdf';
                                            }
                                           
                                      ?> 
                                          <tr>
                                            <td>
                                                <div class="pretty p-image p-plain selectAllItem ml-2">
                                                   <input type="checkbox" class="singleCheck" id="<?=$content->id?>" value="<?=$content->id?>"/>
                                                   <div class="state">
                                                      <img class="image" src="<?=RESOURCE_URL?>images/checkbox.png">
                                                      <label class="cursor-pointer selectAllItem" for="<?=$content->id?>"></label>
                                                   </div>
                                                </div>     
                                            </td>
                                            <!--<td><?=$index+1?></td>-->
                                            <td class="client-avatar">
                                                <a href="<?=$course_thumbnail?>" data-fancybox="gallery" data-caption="<?=$content->center_name?>">
                                                <img alt="image" src="<?=$course_thumbnail?>">
                                                </a> 
                                            </td>

                                            <td class="project-title">
                                                <a href="<?=SITE_URL?>?route=edit_course&id=<?=$content->id?>" data-toggle="tooltip" data-placement="bottom" title="Course Name:  <?=$content->course_title?>">
                                                    <?=$content->course_title?></a>
                                                <br/>
                                                <small>Created <?=date('jS F, Y',strtotime($content->created_at))?></small>
                                            </td>

                                             <td class="project-title">
                                               <h5>Course Fees:&nbsp;<i class="fa fa-inr" aria-hidden="true"></i>&nbsp;<?=$content->course_fees?></h5>
                                            </td>

                                            <!--<td class="project-title">
                                               <h5>Course Duration:&nbsp;<?=$content->course_duration?></h5>
                                            </td>-->

                                            <td>
                                               <h5>Total No of Student Enrolled:&nbsp;<?=$content->no_of_stu_enrld?></h5>
                                            </td>

                                            <td class="project-status">
                                                <span class="label label-<?=($content->record_status == 'active'?'primary':'danger')?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Course Status: <?=ucfirst($content->record_status)?>"><?=ucfirst($content->record_status)?></span>   
                                            </td>

                                            <td class="client-avatar">
                                                <img alt="image" src="<?=RESOURCE_URL.'images/pdf_preview.png'?>">
                                                <br/>
                                                <small><a href="<?=$course_pdf?>" data-fancybox="gallery" data-caption="<?=$content->course_title?>">View PDF</a></small>
                                            </td>

                                            <td class="project-status">
                                                 <span class="dropdown">
                                                  <button class="btn btn-success product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                                 <ul class="dropdown-menu">
                                                   <?php if($updatePermission){ ?>  
                                                        <li>
                                                         <a href="<?=SITE_URL?>?route=edit_course&id=<?=$content->id?>" class="#" data-toggle="tooltip" data-placement="bottom" title="Edit this Course"><i class="fa fa-pencil"></i> Edit Course</a>
                                                       </li>
                                                   <?php } ?> 

                                                   <?php if($updatePermission){ ?>
                                                      <li>
                                                        <a href="javascript:void(0)" id="item_<?=$content->id?>" class="featured_action" data-type="course" data-ptype="Course" data-ftype="<?=($content->featured_status=='active'?'inactive':'active')?>" data-rid="<?=$content->id?>" data-toggle="tooltip" data-placement="bottom" title="<?=($content->featured_status=='active'?'Non-Featured':'Featured')?> this Course"><i class="<?=($content->featured_status=='active'?'fa fa-star':'fa fa-star-o')?>"></i> <?=($content->featured_status=='active'?'Featured':'Non-Featured')?> 
                                                        </a>
                                                      </li>
                                                   <?php } ?>    
                                                   
                                                   <?php if($content->record_status == 'active'){?>
                                                     
                                                      <?php if($updatePermission){ ?> 
                                                        <li>
                                                           <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$content->id?>" data-type="course" data-ptype="Course" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Course"><i class="fa fa-trash"></i> Block Course</a>
                                                        </li>
                                                      <?php } ?>

                                                      <?php if(!$updatePermission){ ?>
                                                        <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                                      <?php } ?>  
                                                       
                                                    <?php }else{ ?>

                                                       <?php if($updatePermission){ ?>  
                                                           <li>
                                                             <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$content->id?>" data-type="course" data-ptype="Course" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore this Course"><i class="fa fa-refresh"></i> Restore Course</a>
                                                           </li>
                                                        <?php }?>
                                                        
                                                        <?php if($deletePermission){ ?>    
                                                            <li>
                                                              <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$content->id?>" data-type="course" data-ptype="Course" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete this Course"><i class="fa fa-times"></i> Delete Course</a>
                                                            </li>
                                                        <?php } ?>  

                                                         <?php if(!$updatePermission && !$deletePermission){ ?>
                                                           <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                                        <?php } ?>     
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
       <script>
         
        $(document).ready(function () {
             
          //Handling course status change
          $(document).on('click', '.changeCourseStatus', function(e){
            e.preventDefault();
            
            var row_id = $(this).data('rid');
            var student_status = $(this).data('status');
            var formData = {action:"changeCourseStatus",row_id:row_id,student_status:student_status};

             swal({
                title: "Are you sure?",
                text: "Are you sure to change this student status?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, Go ahead!",
                closeOnConfirm: false
                }, function () {

                    $.ajax({
                      url:ajaxControllerHandler,
                      method:'POST',
                      data: formData,
                        beforeSend: function() {
                          //$('#manageResultModal').close();
                        },
                        success:function(responseData){
                           var data = JSON.parse(responseData);
                           //console.log(responseData);
                           if(data.check == 'success'){
                              //show sweetalert success
                              setTimeout(function() {
                                  swal({
                                    title: "Success!",
                                    text: "Student status has been successfully updated!",
                                    type: "success"
                                  }, function() {
                                      window.location = "<?=SITE_URL?>?route=view_students";
                                  });
                              }, 1000);
                             return true; 
                           }else{
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

        });  
       </script>
    </body>
</html>
