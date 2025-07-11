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
  $pagePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("manage_city_db"); 

  /* print"<pre>";
  print_r($pageContent['pageData']); 
  print"</pre>"; */
?>

     <div class="wrapper wrapper-content fadeInRight">  
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Fetch City based on their status </h5>
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

            <div class="row d-none" id="import_data_div">
                <div class="col-lg-12">
                  <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Import City Data in CSV Format</h5>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-xs btn-warning handle_import_div" data-htype="hide"><i class="fa fa-chevron-up"></i> Close Import Section</button>
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
                        <div class="col-lg-12 col-md-12 col-sm-12"> 
                          <form method="post" id="import_table_data_form" class="wp-upload-form" onsubmit="return false;">
                            <input type="hidden" name="import_table" value="city"> 

                            <div class="btn-group">
                              <label title="Upload a file" for="importDataCSV" class="btn btn-primary">
                                  <input type="file" accept="application/vnd.openxmlformats-officedoc.sheet" id="importDataCSV" name="import_data_file" class="hide" />
                                  Upload table data by uploading a csv or xls file with proper table structure...
                              </label>    
                            </div>

                            <button type="submit" class="btn btn-lg ml-2 btn-success mb-2" name="import_data_submit" id="import_data_submit" class="button" value="Import Data" disabled><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Import Data</button>  

                            <a href="<?=RESOURCE_URL.'importSampleCSV/sample-city.xlsx'?>" class="btn btn-primary btn-lg ml-2 mb-2" download>
                                <i class="fa fa-download"> </i> Sample CSV
                                <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Download sample CSV format and strickly follow it to import bulk data"><i class="fa fa-question-circle"></i></span>
                            </a>
                         </form>
                       </div>  
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
                                <button type="button" class="btn btn-xs btn-danger handle_import_div" data-htype="show"><i class="fa fa-file-excel-o"> </i> Import Data in CSV Format</button>

                                 <?php if($pagePermission){ ?> 
                                     <a href="javascript:void(0)" data-excution="add" class="table-action-primary excution" data-toggle="tooltip" data-placement="bottom" title="Add New City"><i class="fa fa-plus-circle"></i></a>
                                 <?php }?>

                                  <a href="<?=SITE_URL?>?route=manage_cities" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh City Data"><i class="fa fa-refresh"></i></a>
                              
                                 <?php if($record_status == 'active'){ ?>
                                   <?php if($pagePermission){ ?>
                                      <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid = "all" data-type="cities" data-ptype="City" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
                                   <?php } ?>   
                                <?php }else{ ?>   
                                  <?php if($pagePermission){ ?>
                                    <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid = "all" data-type="cities" data-ptype="City" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
                                  <?php } ?>  

                                  <?php if($pagePermission){ ?>
                                     <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid = "all" data-type="cities" data-ptype="City" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
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
                                            <th class="sorting_desc_disabled">City Name<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                            <th class="sorting_desc_disabled">Created at<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                            <th class="sorting_desc_disabled">City Status<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                            <th class="sorting_desc_disabled notexport">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php foreach($pageContent['pageData']['city_data'] as $index => $content){?> 
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
                                                    <a href="javascript:void(0);" data-toggle="tooltip" id="category_name" data-placement="bottom" title="City Name: <?=$content->name?>"><?=$content->name?></a>
                                                    <br/>
                                                </td>

                                                <td>Created: <?=date('jS F, Y',strtotime($content->created_at))?></td>

                                               
                                                <td class="project-status">
                                                   <span class="label label-<?=($content->record_status == 'active'?'primary':'danger')?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="City status: <?=ucfirst($content->record_status)?>"><?=ucfirst($content->record_status)?></span>   
                                                </td>

                                                <td >
                                                   <?php if($pagePermission){ ?>  
                                                      <a href="javascript:void(0);" data-excution="update" data-status="<?=$content->record_status?>" data-rid="<?=$content->id?>" data-cname="<?=$content->name?>" class="btn btn-success btn-xs mt-1 excution" data-toggle="tooltip" data-placement="bottom" title="Edit this City"><i class="fa fa-pencil"></i> Edit City</a>
                                                    <?php } ?>  

                                                    <?php if($content->record_status == 'active'){?>

                                                       <?php if($pagePermission){ ?> 
                                                         <a href="javascript:void(0);" class="btn btn-warning btn-xs mt-1 changeRecordStatus" data-rid = "<?=$content->id?>" data-type="cities" data-ptype="City" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this City"><i class="fa fa-trash"></i> Block City</a>
                                                       <?php } ?> 

                                                        <?php if(!$pagePermission){ ?>
                                                           <span>No action found!</span>
                                                        <?php } ?> 

                                                    <?php }else{ ?> 

                                                       <?php if($pagePermission){ ?>  
                                                         <a href="javascript:void(0);" class="btn btn-info btn-xs mt-1 changeRecordStatus" data-rid = "<?=$content->id?>" data-type="cities" data-ptype="City" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore this City"><i class="fa fa-refresh"></i> Restore City</a>
                                                       <?php } ?>  

                                                       <?php if($pagePermission){ ?>
                                                          <a href="javascript:void(0);" class="btn btn-danger btn-xs mt-1 changeRecordStatus" data-rid = "<?=$content->id?>" data-type="cities" data-ptype="City" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete this City"><i class="fa fa-times"></i> Delete City</a>
                                                        <?php } ?>

                                                         <?php if(!$pagePermission && !$pagePermission){ ?>
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
         <div class="modal inmodal" id="manageCityModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
               <div class="modal-content animated flipInY">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        <h4 class="modal-title" id="city_modal_title">Add New City</h4>
                        <small class="font-bold">Enter New city under contact form in frontend.</small>
                    </div>
                    <div class="modal-body">
                      <form id="manage_city_form" class="needs-validation" method="post" onsubmit="return false;">
                        <input type="hidden" name="action" id="action" value="manageGlobalCity">
                        <input type="hidden" name="row_id" id="row_id" value="">
                        
                        <div class="form-group">
                           <label>City Name</label> <input type="text" name="city" id="city" placeholder="Enter City Name" class="form-control" required>
                        </div>


                         <div class="form-group">
                             <label>City Status</label> 
                             <div class="col-sm-9 pl-0">
                               <label class="checkbox-inline i-checks"> <input type="radio" value="active" id="active" name="record_status" / required=""> <i></i>Active </label>
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
                      $('#city_modal_title').text('Update City');
                      $('#action').val('manageGlobalCity');
                    
                      var row_id = $(this).data('rid');
                      var status = $(this).data('status');
                      var category_name = $(this).data('cname');
                    
                      $('#row_id').val(row_id);
                      $('#city').val(category_name);

                      if(status == 'active'){
                        $('#active').parent().addClass('checked');
                        $("#active").prop("checked", true);
                      }else{
                        $('#blocked').parent().addClass('checked');
                        $("#blocked").prop("checked", true);
                      }
                   }else{
                     $('#city_modal_title').text('Add new City');
                     $('#manage_city_form')[0].reset();
                   }
                   $('#manageCityModal').modal('show'); 
             });

             $(document).on('submit', '#manage_city_form', function(event){
                   event.preventDefault();

                   $.ajax({
                    url:ajaxControllerHandler,
                    method:'POST',
                    data: new FormData(this),
                    contentType:false,
                    processData:false,
                    beforeSend: function() {
                      //$('#manageCityModal').close();
                    },
                    success:function(responseData){
                       var data = JSON.parse(responseData);
                       $('#manage').attr('disabled',false);
                       //console.log(responseData);
                       if(data.check == 'success'){
                          //reseting form data
                          $('#manage_city_form')[0].reset();
                          
                          if(data.last_insert_id>0){
                            var successText = "City has been successfully created!";
                          }else{
                            var successText = "City has been successfully updated!";
                          } 

                          //show sweetalert success
                          setTimeout(function() {
                              swal({
                                title: "Success!",
                                text: successText,
                                type: "success"
                              }, function() {
                                  window.location = "<?=SITE_URL?>?route=manage_cities";
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
                   var validDocTypes = ["application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
                   if($.inArray(fileType, validDocTypes) < 0) {
                     toastr.error("Only csv file allowed!", "Upload error!"); 
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
                                     var redirect_url = SITE_URL+"?route=manage_cities&record_status=blocked";
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
                                }, 2000);
                            }
                        });
                    });  
                });  

            }); 
         </script>
    </body>
</html>


            
            
         