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
  //Configiring franchise data
  $franchiseListArr = $pageContent['pageData']['data'];

  //Fetching page action permission
  $createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_franchise"); 
  $updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_franchise"); 
  $deletePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("delete_franchise"); 

?>
                    
            <div class="wrapper wrapper-content fadeInRight">  
                 <div class="row">
                    <div class="col-lg-7">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h5>Export Receipts based on export method </h5>
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
                               <form id="fetch_all_records" onsubmit="return false;">
                                 <div class="row">
                                    <div class="col-sm-6 m-b-xs">
                                      <select class="form-control-sm form-control input-s-sm inline record_status" name="record_status" id="record_status">
                                        <option selected disabled value>Select a Data type to proceed</option>
                                        <option value="active" <?=(($record_status =='active' || $record_status =='')?'selected':'')?>>Active</option>  
                                        <option value="blocked" <?=($record_status=='blocked'?'selected':'')?>>Blocked</option>  
                                      </select>
                                    </div>
                                    <input type="hidden" name="page_route" id="page_route" value="<?=$_GET['route']."&stu_id=".$_GET['stu_id']?>">

                                    <div class="col-sm-6">
                                      <button class="btn btn-primary" type="submit" id="fetch_item_data"><i class="fa fa-search"></i>&nbsp;Fetch Data</button>

                                      <a href="<?=SITE_URL?>?route=view_franchises" data-toggle="tooltip" title="Back to Receipt List Page" class="btn btn-info ml-1" data-original-title="Cancel"><i class="fa fa-refresh"></i> Refresh Table Data</a>
                                    </div>
                                </div>
                              </form>
                            </div>
                        </div>
                      </div>

                      <div class="col-lg-5">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h5>Fetch Francise based on their status </h5>
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
                              <div class="row">   
                                  <div class="col-lg-7 col-md-7 col-sm-12">
                                      <select class="form-control-sm form-control input-s-sm inline record_status" name="export_method" id="export_method">
                                        <option selected disabled value>Select an exoprt type</option>
                                        <option value="pdf">PDF</option>  
                                        <option value="excel">CSV</option>  
                                      </select>
                                    </div>

                                    <a href="javascript:void(0)" id="export_record_href" style="display:none" download>
                                       <button type="button" id="hidden_export_button">Export</button>
                                    </a> 

                                    <div class="col-lg-5 col-md-5 col-sm-12">
                                         <button class="btn btn-success" type="submit" id="export_data_submit"><i class="fa fa-download"></i>&nbsp;Export Data</button> 
                                    </div>
                                 </div>   
                             </div>
                           </div>       
                      </div>  
                   </div>  

                  <div class="row">
                    <div class="col-lg-12">

                    <div class="ibox">
                        <div class="ibox-title">
                            <h5>Franchise List with all details</h5>
                            <div class="ibox-tools">
                                <?php if($createPermission){ ?> 
                                     <a href="<?=SITE_URL?>?route=add_franchise" class="table-action-primary" data-toggle="tooltip" data-placement="bottom" title="Add New Franchise"><i class="fa fa-plus-circle"></i></a>
                                 <?php }?>

                                  <a href="<?=SITE_URL?>?route=view_franchises" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Franchise Data"><i class="fa fa-refresh"></i></a>
                              
                                 <?php if($record_status == 'active'){ ?>
                                   <?php if($updatePermission){ ?>
                                      <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid = "all" data-type="franchise" data-ptype="Franchise" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
                                   <?php } ?>   
                                  <?php }else{ ?>   
                                  <?php if($updatePermission){ ?>
                                    <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid = "all" data-type="franchise" data-ptype="Franchise" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
                                  <?php } ?>  

                                  <?php if($deletePermission){ ?>
                                     <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid = "all" data-type="franchise" data-ptype="Franchise" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
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
                                            <!--<th>SL No.</th>-->
                                            <th class="notexport">Image</th>

                                            <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Franchise Name" style="width:20%;">Center Name<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                            <th class="sorting_desc_disabled">Owner<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                            <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Franchise Contact No">Contact<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                            <!--<th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Franchise Email">Email<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>-->

                                            <!--<th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Franchise Address">Address<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>-->

                                            <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Total no of student enreoled for this franchise">Students<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                            <th class="sorting_desc_disabled notexport" data-toggle="tooltip" data-placement="bottom" title="Franchise Detail PDF" style="width:10%;">PDF<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                            <th class="sorting_desc_disabled notexport">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php 
                                        foreach($franchiseListArr as $index => $content){

                                          $franchise_thumbnail = USER_UPLOAD_DIR.'franchise/'.$content->fran_image;

                                          if(!strlen($content->fran_image)>0 || !file_exists($franchise_thumbnail)) {   
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
                                                <a href="<?=$franchise_thumbnail?>" data-fancybox="gallery" data-caption="<?=$content->center_name?>">
                                                <img alt="image" src="<?=$franchise_thumbnail?>">
                                                </a> 
                                            </td>

                                            <td class="project-title cursor-pointer">
                                                <a href="<?=SITE_URL?>?route=edit_franchise&id=<?=$content->id?>" data-toggle="tooltip" data-placement="bottom" title="Franchise Name:  <?=$content->center_name?>"><?=$content->center_name?></a>
                                                <br/>
                                                <small>Created <?=date('jS F, Y',strtotime($content->created_at))?></small>
                                            </td>
                                            
                                            <td class="project-title cursor-pointer">
                                                <span data-toggle="tooltip" data-placement="bottom" title="Franchise Owner Name: <?=$content->owner_name?>"><?=(strlen($content->owner_name)>20?substr($content->owner_name,0,20)."...":$content->owner_name)?></span>
                                                <br/>
                                                <small><strong>Franchise ID: <?=$content->fran_id?></strong></small>
                                            </td>

                                            <td class="project-title">
                                                <span data-toggle="tooltip" data-placement="bottom" title="Franchise Contact No: <?=$content->fran_phone?>"><?=(strlen($content->fran_phone)>20?substr($content->fran_phone,0,20)."...":$content->fran_phone)?></span>
                                            </td>

                                            <!--<td class="project-title">
                                                <span data-toggle="tooltip" data-placement="bottom" title="Franchise Email: <?=$content->fran_email?>"><?=$content->fran_email?></span>
                                            </td>-->

                                            <!--<td class="project-title">
                                                <span data-toggle="tooltip" data-placement="bottom" title="Franchise Address: <?=$content->fran_address?>"><?=$content->fran_address?></span>
                                            </td>-->

                                            <td style="width:18%;">
                                               <h5>Total No of Student Enrolled:&nbsp;<?=$content->enrolled_student_count?></h5>
                                            </td>

                                            <td class="client-avatar">
                                                <img alt="image" src="<?=RESOURCE_URL.'images/pdf_preview.png'?>">
                                                <br/>
                                                <small><a href="<?=$franchise_pdf?>" data-fancybox="gallery" data-caption="<?=$content->center_name?>">View PDF</a></small>
                                            </td>

                                            <td class="project-status">
                                                 <span class="dropdown">
                                                  <button class="btn btn-success product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                                 <ul class="dropdown-menu">
                                                   <?php if($updatePermission){ ?> 
                                                        <li>
                                                         <a href="<?=SITE_URL?>?route=edit_franchise&id=<?=$content->id?>" class="#" data-toggle="tooltip" data-placement="bottom" title="Edit this Franchise"><i class="fa fa-pencil"></i> Edit Franchise</a>
                                                       </li>
                                                   <?php } ?>     

                                                    <?php if($updatePermission){ ?>
                                                          <li>
                                                            <a href="javascript:void(0)" id="item_<?=$content->id?>" class="featured_action" data-type="franchise" data-ptype="Franchise" data-ftype="<?=($content->featured_status=='active'?'inactive':'active')?>" data-rid="<?=$content->id?>" data-toggle="tooltip" data-placement="bottom" title="<?=($content->featured_status=='active'?'Non-Featured':'Featured')?> this Franchise"><i class="<?=($content->featured_status=='active'?'fa fa-star':'fa fa-star-o')?>"></i> <?=($content->featured_status=='active'?'Featured':'Non-Featured')?> 
                                                            </a>
                                                          </li>
                                                    <?php } ?>

                                                   <?php if($content->record_status == 'active'){?>
                                                     
                                                     <?php if($updatePermission){ ?> 
                                                       <li>
                                                         <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$content->id?>" data-type="franchise" data-ptype="Franchise" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Franchise"><i class="fa fa-trash"></i> Block Franchise</a>
                                                       </li>
                                                     <?php } ?>

                                                     <?php if(!$updatePermission){ ?>
                                                       <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                                     <?php } ?>   
                                                       
                                                    <?php }else{ ?>

                                                       <?php if($updatePermission){ ?>  
                                                           <li>
                                                             <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$content->id?>" data-type="franchise" data-ptype="Franchise" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore this Franchise"><i class="fa fa-refresh"></i> Restore Franchise</a>
                                                           </li>
                                                        <?php } ?>
                                                        
                                                       <?php if($deletePermission){ ?>     
                                                           <li>
                                                             <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$content->id?>" data-type="franchise" data-ptype="Franchise" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete this Franchise"><i class="fa fa-times"></i> Delete Franchise</a>
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
         
        //Handling hard export for student receipt table
          $(document).on('click', '#export_data_submit', function(event){
              event.preventDefault();
              var record_status = $('#record_status').val();

              var course_id = $('#course_id').val();
              var franchise_id = $('#franchise_id').val();

              var student_id = "<?=$_GET['stu_id']?>";

              var export_method = $("#export_method").val();

              //console.log(typeof(export_method));

              var formData = {export_table:"franchise",record_status:record_status,export_method:export_method};

              //Clearing hyperlink href for fresh download
              $('#export_record_href').attr("href","javascript:void(0);");
              $('#export_record_href').attr("download","");

              if(!export_method){
                 toastr.error("Please select a export method to proceed!", "Error!",{ timeOut: 10000 });
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

            //Send re-verification email to student
              $(document).on('click', '#sendUserVerificationMail', function(e){
                e.preventDefault();
                
                var row_id = $(this).data('rid');
                var user_type = $(this).data('utype');
                var user_email = $(this).data('uemail');
                var page_route = $('#page_route').val();

                var formData = {action:"resendUserVerificationLink",row_id:row_id,user_type:user_type,user_email:user_email};

                 swal({
                    title: "Are you sure?",
                    text: "Are you sure to send a verification mail to this student?",
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
                              $('#student_div_loader').addClass('sk-loading');
                            },
                            success:function(responseData){
                               var data = JSON.parse(responseData);
                               //console.log(responseData);
                               if(data.check == 'success'){
                                  $('#student_div_loader').removeClass('sk-loading');
                                  //show sweetalert success
                                  setTimeout(function() {
                                      swal({
                                        title: "Success!",
                                        text: data.msg,
                                        type: "success"
                                      }, function() {
                                          //window.location = "<?=SITE_URL?>?route="+page_route;
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
       </script>
    </body>
</html>
