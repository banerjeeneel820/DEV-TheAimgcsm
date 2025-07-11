<?php  
  $examDataArr = $pageContent['pageData']['exam_data']; 

  if(isset($_GET['record_status'])){
    if($_GET['record_status'] == 'active'){
       $record_status = 'active'; 
    }else{
       $record_status = 'blocked'; 
    }
  }else{
    $record_status = 'active'; 
  }

  //Fetching page action permission
  $createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_exam"); 
  $updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_exam"); 
  $deletePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("delete_exam"); 
  
  /*print"<pre>";
  print_r($examDataArr); 
  print"</pre>";*/
?>
 
     <div class="wrapper wrapper-content fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Fetch Exam based on their status </h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                       <form id="fetch_all_exam_records" onsubmit="return false;">
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
                    <h5>All Exam List</h5>
                    <div class="ibox-tools">
                         <?php if($createPermission){ ?> 
                             <a href="<?=SITE_URL?>?route=add_exam" class="table-action-primary" data-toggle="tooltip" data-placement="bottom" title="Add New Exam"><i class="fa fa-plus-circle"></i></a>
                         <?php }?>

                          <a href="<?=SITE_URL?>?route=view_exams" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Exam Data"><i class="fa fa-refresh"></i></a>
                      
                         <?php if($record_status == 'active'){ ?>
                           <?php if($updatePermission){ ?>
                              <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid = "all" data-type="exam" data-ptype="Exam" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
                           <?php } ?>   
                        <?php }else{ ?>   
                          <?php if($updatePermission){ ?>
                            <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid = "all" data-type="exam" data-ptype="Exam" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
                          <?php } ?>  

                          <?php if($deletePermission){ ?>
                             <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid = "all" data-type="exam" data-ptype="Exam" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
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
                                <th class="sorting_desc_disabled"> Exam Name <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>
                                <th class="sorting_desc_disabled">Franchise <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>  
                                <th class="sorting_desc_disabled">Course <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>
                                <th class="sorting_desc_disabled">Total Marks/Times</th>
                                 <th class="sorting_desc_disabled">Total Questions</th>      
                                 <th class="sorting_desc_disabled">Status <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>
                                <th class="sorting_desc_disabled notexport">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                                 foreach($examDataArr as $index => $exam){

                                  if(strlen($exam->optional_pdf)>0 && file_exists(USER_UPLOAD_DIR.'exam/'.$exam->optional_pdf)){
                                     $optional_pdf = USER_UPLOAD_URL.'exam/'.$exam->optional_pdf;
                                  }else{
                                     $optional_pdf = null;
                                  }

                                  $total_time = '';

                                  if(!empty($exam->hours)){
                                      $total_time .= $exam->hours.'h ';
                                  }

                                  if(!empty($exam->minutes)){
                                      $total_time .= $exam->minutes.'m';
                                  }

                              ?> 
                                    <tr>
                                        <td>
                                           <div class="pretty p-image p-plain selectAllItem ml-2">
                                               <input type="checkbox" class="singleCheck" id="<?=$exam->id?>" value="<?=$exam->id?>"/>
                                               <div class="state">
                                                  <img class="image" src="<?=RESOURCE_URL?>images/checkbox.png">
                                                  <label class="cursor-pointer selectAllItem" for="<?=$exam->id?>"></label>
                                               </div>
                                            </div>  
                                        </td>
                                                                               
                                        <td class="project-title" style="width:18%;">
                                            <a href="<?=SITE_URL.'?route=edit_exam&id='.$exam->id?>" data-toggle="tooltip" data-placement="bottom" title="Exam Title: <?=$exam->name?>"><?=ucfirst($exam->name)?></a>
                                            <br/>
                                            <small>Created <?=date('jS F, Y',strtotime($exam->created_at))?></small>
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Franchise taking exam: <?=$exam->center_name?>"><?=$exam->center_name?></span>
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Exam subject/course: <?=$exam->course_title?>"><?=$exam->course_title?></span>
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Total Exam Time: <?=$total_time?>"><?=$exam->total_marks?> / <?=$total_time?></span>
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Total Questions: <?=$exam->question_count?>"><?=$exam->question_count?></span>
                                        </td>

                                        <td class="project-status">
                                         <span class="label label-<?=($exam->record_status == 'active'?'primary':'danger')?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Exam Status: <?=ucfirst($exam->record_status)?>"><?=ucfirst($exam->record_status)?></span> 
                                        </td>

                                       <td>
                                         <span class="dropdown">
                                          <button class="btn btn-success product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                         <ul class="dropdown-menu">
                                            <?php if($updatePermission){ ?>
                                                <li>
                                                 <a href="<?=SITE_URL?>?route=edit_exam&id=<?=$exam->id?>" class="#" data-toggle="tooltip" data-placement="bottom" title="Edit this Exam"><i class="fa fa-pencil"></i> Edit Exam</a>
                                               </li> 
                                            <?php } ?>   

                                            <?php if($updatePermission){ ?>
                                                <li>
                                                 <a href="<?=SITE_URL?>?route=manage_questions&exm_id=<?=$exam->id?>" class="#" data-toggle="tooltip" data-placement="bottom" title="Manage questions for this exam"><i class="fa fa-keyboard-o"></i> Edit Questions</a>
                                               </li> 
                                            <?php } ?>   
                                           
                                             <?php if($exam->record_status == 'active'){?>
                                               <?php if($updatePermission){ ?>
                                                   <li>
                                                     <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$exam->id?>" data-type="exam" data-ptype="Exam" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Exam"><i class="fa fa-trash"></i> Block Exam</a>
                                                   </li>
                                                <?php } ?> 

                                                 <?php if(!$updatePermission){ ?>
                                                    <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                                 <?php } ?>  

                                             <?php }else{ ?>
                                               <?php if($updatePermission){ ?> 
                                                   <li>
                                                     <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$exam->id?>" data-type="exam" data-ptype="Exam" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore this Exam"><i class="fa fa-refresh"></i> Restore Exam</a>
                                                   </li>
                                                <?php } ?>
                                                
                                                <?php if($deletePermission){ ?>   
                                                   <li>
                                                     <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$exam->id?>" data-type="exam" data-ptype="Exam" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete this Exam"><i class="fa fa-times"></i> Delete Exam</a>
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

<script>
    $(document).on('submit', '#fetch_all_exam_records', function(event){
          event.preventDefault();
          var page_route = $('#page_route').val();
          var record_status = $('#record_status').val();
          var exam_type = $('#exam_type').val();

          if(record_status === null){
              window.location = SITE_URL+"?route="+page_route;
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
               var redirect_url = SITE_URL+"?route="+page_route+"&record_status="+record_status;

               if(exam_type){
                  redirect_url += "&exam_type="+exam_type;
               }
              
               window.location = redirect_url;
              
             });},500);
             return true;  
         } 
    });

</script>

