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

  //Fetching page action permission
  $createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_category"); 
  $updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_category"); 
  $deletePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("delete_category"); 

  /* print"<pre>";
  print_r($pageContent['pageData']); 
  print"</pre>"; */
?>

     <div class="wrapper wrapper-content fadeInRight">  
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Fetch Category based on their status </h5>
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
                            <h5>All projects assigned to this account</h5>
                            <div class="ibox-tools">
                            
                                 <?php if($createPermission){ ?> 
                                     <a href="javascript:void(0)" data-excution="add" class="table-action-primary excution" data-toggle="tooltip" data-placement="bottom" title="Add New Category"><i class="fa fa-plus-circle"></i></a>
                                 <?php }?>

                                  <a href="<?=SITE_URL?>?route=view_category" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Category Data"><i class="fa fa-refresh"></i></a>
                              
                                 <?php if($record_status == 'active'){ ?>
                                   <?php if($updatePermission){ ?>
                                      <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid = "all" data-type="parent_category" data-ptype="Category" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
                                   <?php } ?>   
                                <?php }else{ ?>   
                                  <?php if($updatePermission){ ?>
                                    <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid = "all" data-type="parent_category" data-ptype="Category" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
                                  <?php } ?>  

                                  <?php if($deletePermission){ ?>
                                     <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid = "all" data-type="parent_category" data-ptype="Category" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
                                   <?php } ?>  
                                <?php } ?>   
                            </div>

                        </div>
                        <div class="ibox-content content_div_loader">
                            <!--<div class="row m-b-sm m-t-sm">
                                <div class="col-md-1">
                                    <button type="button" id="loading-example-btn" class="btn btn-white btn-xs" ><i class="fa fa-refresh"></i> Refresh</button>
                                </div>
                                <div class="col-md-11">
                                    <div class="input-group"><input type="text" placeholder="Search" class="form-control-sm form-control"> <span class="input-group-btn">
                                        <button type="button" class="btn btn-xs btn-primary"> Go!</button> </span></div>
                                </div>
                            </div>-->

                            <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>

                            <div class="project-list">

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
                                            <th class="sorting_desc_disabled" style="width:10%;">SL No.</th>
                                            <th class="sorting_desc_disabled">Category Name<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                            <th class="sorting_desc_disabled">Created at<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                            <th class="sorting_desc_disabled">Parent Section<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                            <th class="sorting_desc_disabled">Category Status<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                            <th class="sorting_desc_disabled notexport">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php foreach($pageContent['pageData']['category_data'] as $index => $content){?> 
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
                                                
                                                <td class="project-title"><?=$index+1?></td>
                                                
                                                <td class="project-title">
                                                    <a href="javascript:void(0);" data-toggle="tooltip" id="category_name" data-placement="bottom" title="Category Name: <?=$content->name?>"><?=$content->name?></a>
                                                    <br/>
                                                </td>

                                                <td>Created: <?=date('jS F, Y',strtotime($content->created_at))?></td>

                                                <td>
                                                   <a href="#" class="pb-1">
                                                        <span class="badge badge-pill badge-success" data-toggle="tooltip" data-placement="bottom" title="Parent Name: <?=ucfirst($content->parent_category)?>"><?=ucfirst($content->parent_category)?></span>
                                                    </a> 
                                                </td>

                                                <td class="project-status">
                                                   <span class="label label-<?=($content->record_status == 'active'?'primary':'danger')?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Category status: <?=ucfirst($content->record_status)?>"><?=ucfirst($content->record_status)?></span>   
                                                </td>

                                                <td style="width:25%;">
                                                   <?php if($updatePermission){ ?>  
                                                      <a href="javascript:void(0);" data-excution="update" data-status="<?=$content->record_status?>" data-rid="<?=$content->id?>" data-cname="<?=$content->name?>" data-pid="<?=$content->parent_category?>" class="btn btn-success btn-xs mt-1 excution" data-toggle="tooltip" data-placement="bottom" title="Edit this Category"><i class="fa fa-pencil"></i> Edit Category</a>
                                                    <?php } ?>  

                                                    <?php if($content->record_status == 'active'){?>

                                                       <?php if($updatePermission){ ?> 
                                                         <a href="javascript:void(0);" class="btn btn-warning btn-xs mt-1 changeRecordStatus" data-rid = "<?=$content->id?>" data-type="parent_category" data-ptype="Category" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Category"><i class="fa fa-trash"></i> Block Category</a>
                                                       <?php } ?> 

                                                        <?php if(!$updatePermission){ ?>
                                                           <span>No action found!</span>
                                                        <?php } ?> 

                                                    <?php }else{ ?> 

                                                       <?php if($updatePermission){ ?>  
                                                         <a href="javascript:void(0);" class="btn btn-info btn-xs mt-1 changeRecordStatus" data-rid = "<?=$content->id?>" data-type="parent_category" data-ptype="Category" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore this Category"><i class="fa fa-refresh"></i> Restore Category</a>
                                                       <?php } ?>  

                                                       <?php if($deletePermission){ ?>
                                                          <a href="javascript:void(0);" class="btn btn-danger btn-xs mt-1 changeRecordStatus" data-rid = "<?=$content->id?>" data-type="parent_category" data-ptype="Category" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete this Category"><i class="fa fa-times"></i> Delete Category</a>
                                                        <?php } ?>

                                                         <?php if(!$updatePermission && !$deletePermission){ ?>
                                                           <span>No action found!</span>
                                                        <?php } ?>

                                                    <?php } ?>    
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

         <!-- Modal window div-->
         <div class="modal inmodal" id="manageCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content animated flipInY">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="category_modal_title">Add New Category</h4>
                        <small class="font-bold">Enter New category for gallery & receipy.</small>
                    </div>
                    <div class="modal-body">
                      <form id="manage_category_form" class="needs-validation" method="post" onsubmit="return false;">
                        <input type="hidden" name="action" id="action" value="manageParentCategory">
                        <input type="hidden" name="row_id" id="row_id" value="">
                        <input type="hidden" name="parent_category" id="parent_hidden_id" value="">
                        
                        <div class="form-group">
                           <label>Category Name</label> <input type="text" name="category" id="category" placeholder="Enter Category Name" class="form-control" required>
                        </div>

                          <div class="form-group">
                             <label>Parent Section</label> 
                              <select class="form-control" name="parent_category" id="parent_category" required />
                                  <option selected disabled>Choose a Prent Section</option>
                                  <option id="gallery" value="gallery">Gallery</option>
                                  <option id="receipt" value="receipt">Receipt</option>
                             </select>
                         </div>

                         <div class="form-group">
                             <label>Category Status</label> 
                             <div class="col-sm-9 pl-0">
                               <label class="checkbox-inline i-checks"> <input type="radio" value="active" id="active" name="record_status" / required> <i></i>Active </label>
                               <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" id="blocked" name="record_status" required> <i></i> Blocked </label>
                             </div>
                         </div>

                          <div class="modal-footer">
                           <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                           <button type="submit" id="manage" class="btn btn-primary">Save</button>
                         </div>
                       </form>   
                      </div>
                  </div>
               </div>
            </div>
            <!-- Modal ends here -->
        <script>
           
           $(document).ready(function () {

               $(document).on('click', '.excution', function(event){

                   var excution = $(this).data('excution');
                   
                   $('#active').parent().removeClass('checked');
                   $('#blocked').parent().removeClass('checked');

                   if(excution == 'update'){
                      $('#category_modal_title').text('Update Category');
                      $('#action').val('manageParentCategory');
                    
                      var parent_category = $(this).data('pid');
                      var row_id = $(this).data('rid');
                      var status = $(this).data('status');
                      var category_name = $(this).data('cname');
                    
                      $('#parent_hidden_id').val(parent_category);
                      $('#row_id').val(row_id);
                      $('#category').val(category_name);
                      $('#parent_category').val(parent_category);

                      if(status == 'active'){
                        $('#active').parent().addClass('checked');
                        $("#active").prop("checked", true);
                      }else{
                        $('#blocked').parent().addClass('checked');
                        $("#blocked").prop("checked", true);
                      }
                   }else{
                     $('#category_modal_title').text('Add new Category');
                     $('#manage_category_form')[0].reset();
                   }
                   $('#manageCategoryModal').modal('show'); 
             });

             $(document).on('submit', '#manage_category_form', function(event){
                   event.preventDefault();

                   $.ajax({
                    url:ajaxControllerHandler,
                    method:'POST',
                    data: new FormData(this),
                    contentType:false,
                    processData:false,
                    beforeSend: function() {
                      //$('#manageCategoryModal').close();
                    },
                    success:function(responseData){
                       var data = JSON.parse(responseData);
                       $('#manage').attr('disabled',false);
                       //console.log(responseData);
                       if(data.check == 'success'){
                          //reseting form data
                          $('#manage_category_form')[0].reset();
                          
                          if(data.last_insert_id>0){
                            var successText = "Category has been successfully created!";
                          }else{
                            var successText = "Category has been successfully updated!";
                          } 

                          //show sweetalert success
                          setTimeout(function() {
                              swal({
                                title: "Success!",
                                text: successText,
                                type: "success"
                              }, function() {
                                  window.location = "<?=SITE_URL?>?route=view_category";
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


            
            
         