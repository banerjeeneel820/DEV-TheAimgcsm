<?php
  $slider_id = $_GET['id'];
  if($_GET['type'] == 'edit'){
    $sliderDetailArr = $pageContent['pageData']['slider_data']; 
    
     //Configuring franchise file data
     if($sliderDetailArr->file_upload_type == "local"){
        if(strlen($sliderDetailArr->banner_image)>0 && file_exists(USER_UPLOAD_DIR.'home_sliders/'.$sliderDetailArr->banner_image)){
           $banner_image = USER_UPLOAD_URL.'home_sliders/'.$sliderDetailArr->banner_image;
        }else{
           $banner_image = RESOURCE_URL.'images/preview.jpg';
        }
     }else{
         $banner_image = $sliderDetailArr->banner_image; 
     }     
  }
  if(empty($_GET['type'])){
    $sliderArr = $pageContent['pageData']['slider_data'];   
  }

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
  $pagePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("manage_home_slider"); 

  /*print"<pre>";
  print_r($sliderArr);
  print"</pre>";*/
?>       


        <div class="wrapper wrapper-content">
          <?php if(!isset($_GET['type'])){ ?>   
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
                           <form id="fetch_all_slider_records" onsubmit="return false;">
                             <div class="row">
                                <div class="col-sm-5 m-b-xs">
                                  <select class="form-control-sm form-control input-s-sm inline record_status" name="record_status" id="record_status">
                                    <option selected disabled value>Select a Data type to proceed</option>
                                    <option value="active" <?=(($record_status =='active' || $record_status =='')?'selected':'')?>>Active</option>  
                                    <option value="blocked" <?=($record_status=='blocked'?'selected':'')?>>Blocked</option>  
                                  </select>
                                </div>
                                <input type="hidden" name="page_route" id="page_route" value="<?=$_GET['route']?>">

                                <div class="col-sm-5 m-b-xs">
                                  <select class="form-control-sm form-control input-s-sm inline slider_type" name="slider_type" id="slider_type">
                                    <option selected disabled>Select a Slider type</option>
                                    <option value="header" <?=(!empty($_GET['slider_type'])?($_GET['slider_type']=='header'?'selected':''):'selected')?>>Header</option>  
                                    <option value="footer" <?=(!empty($_GET['slider_type'])?($_GET['slider_type']=='footer'?'selected':''):'')?>>Footer</option>  
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
          
          <?php if($_GET['type'] == 'add' || $_GET['type'] == 'edit'){ ?>   
             <div class="row">
                <div class="col-lg-12">
                    <div id="responseMsg"></div>
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5><?=($_GET['type'] == 'add'?'Add':'Modify')?> Banner Into Home Slider </h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content" id="manage_media_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <form id="manage_gallery_form" class="needs-validation" method="post" onsubmit="return false;">
                              <input type="hidden" name="action" id="action" value="manageHomeSlider">
                              <input type="hidden" name="type" id="type" value="home_sliders">
                              <input type="hidden" name="slider_id" id="slider_id" value="<?=$slider_id?>">
                                
                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Banner Title <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter a title of this banner (Use '-'' as seperator for alternate text)"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="banner_title" placeholder="Enter a title of this banner..." value="<?=$sliderDetailArr->banner_title?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Banner Text <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter a text of this banner (Use '-' as seperator for alternate text)"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="banner_text" placeholder="Enter a text of this banner..." value="<?=$sliderDetailArr->banner_text?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Banner Link <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter a link of this banner (Just use the url slug. Example:- 'about-us' for frontend)"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="banner_link" placeholder="Enter a link of this banner..." value="<?=$sliderDetailArr->banner_link?>" required>
                                     </div>   
                                    </div>

                                    <label class="col-sm-2 col-form-label text-right pt-2">Slider Position <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Position of the Slider"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-2">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="header" name="slider_type" required <?=($sliderDetailArr->slider_type == 'header'?'checked':'')?>/> <i></i>Header </label>
                                       <label class="checkbox-inline i-checks pl-1"> <input type="radio" value="footer" name="slider_type" required <?=($sliderDetailArr->slider_type == 'footer'?'checked':'')?>/> <i></i> Footer </label>
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>
                               
                               <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right pt-2">File Upload Type <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Status"><i class="fa fa-question-circle"></i></span></label>
                                     <div class="col-sm-4 pt-2 pl-0">
                                       <label class="checkbox-inline i-checks file_upload_type"> <input type="radio" value="local" name="file_upload_type" <?=($sliderDetailArr->file_upload_type=='local'?'checked':'')?> required/> <i></i>Local </label>&nbsp;
                                       <label class="checkbox-inline i-checks file_upload_type pl-1"> <input type="radio" value="cdn" name="file_upload_type" <?=($sliderDetailArr->file_upload_type=='cdn'?'checked':'')?> required/> <i></i> CDN </label>
                                     </div>

                                     <label class="col-sm-2 col-form-label text-right pt-2">Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Status"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-2">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="record_status" required <?=($sliderDetailArr->record_status == 'active'?'checked':'')?> required/> <i></i>Active </label>
                                       <label class="checkbox-inline i-checks pl-1"> <input type="radio" value="blocked" name="record_status" required <?=($sliderDetailArr->record_status == 'blocked'?'checked':'')?> required/> <i></i> Blocked </label>
                                    </div>
                                 </div>       
                                <div class="hr-line-dashed"></div>
                                
                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Banner Image <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                    <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                      <?php if(isset($_GET['id'])){ ?>  
                                        <div class="col-sm-2 pb-3 pl-0">
                                           <h4>Current image</h4>
                                           <a href="<?=$banner_image?>" data-fancybox="gallery" data-caption="<?=$sliderDetailArr->banner_title?>">  
                                              <img id="current_image_review" src="<?=$banner_image?>" alt="Banner image" style="height: 100px;width: 100px;" />
                                            </a>
                                         </div>
                                        <?php } ?> 

                                        <div class="<?php (isset($_GET['id']) ? 'col-sm-10' : 'col-sm-12')?> pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="image_upload_preview" data-fancybox="gallery" data-caption="<?=$sliderDetailArr->title?>"> 
                                           <img id="banner_image_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                      
                                      <div class="btn-group <?=($sliderDetailArr->file_upload_type=='local'?'':'d-none')?>" id="banner_image_local_div">
                                          <label title="Upload a file" for="banner_image_local" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="banner_image_local" id="banner_image_local" class="hide" />
                                              Upload an image of the banner...
                                          </label>    
                                      </div>

                                      <div class="input-group <?=($sliderDetailArr->file_upload_type=='cdn'?'':'d-none')?>" id="banner_image_cdn_div">
                                        <input type="text" class="form-control" name="banner_image_cdn" id="banner_image_cdn" placeholder="Enter Banner Image URL..." value="<?=($sliderDetailArr->file_upload_type == 'cdn'?$sliderDetailArr->banner_image:'')?>">
                                      </div>  

                                    </div>
                                    <input type="hidden" name="hidden_banner_image" value="<?=$sliderDetailArr->banner_image?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                 </div>

                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=home_sliders" data-toggle="tooltip" title="" class="btn btn-default selectAction" data-type="hideAddItemDiv" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
                                        <button class="btn btn-primary btn-sm" type="submit" id="create">Save changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>  
            <?php } ?> 

            <?php if(!isset($_GET['type'])){ ?>    
                <div class="row">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>All projects assigned to this account</h5>
                                <div class="ibox-tools">
                                         <?php if($pagePermission){ ?> 
                                         <a href="<?=SITE_URL?>?route=home_sliders&type=add" class="table-action-primary" data-toggle="tooltip" data-placement="bottom" title="Add New Slider Item"><i class="fa fa-plus-circle"></i></a>
                                     <?php }?>

                                      <a href="<?=SITE_URL?>?route=home_sliders" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Slider Item Data"><i class="fa fa-refresh"></i></a>
                                  
                                     <?php if($record_status == 'active'){ ?>
                                       <?php if($pagePermission){ ?>
                                          <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid = "all" data-type="home_sliders" data-ptype="Slider" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
                                       <?php } ?>   
                                    <?php }else{ ?>   
                                      <?php if($pagePermission){ ?>
                                        <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid = "all" data-type="home_sliders" data-ptype="Slider" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
                                      <?php } ?>  

                                      <?php if($pagePermission){ ?>
                                         <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid = "all" data-type="home_sliders" data-ptype="Slider" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
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
                                <div class="project-list">
                                    <table class="table table-striped dataTables-example text-center">
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
                                                <th class="notexport">Slider<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                                <th class="sorting_desc_disabled">Title<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                                <th class="sorting_desc_disabled" style="width: 30%;" data-toggle="tooltip" data-placement="bottom" title="Banner Text">Banner Text</th>

                                                <th class="sorting_desc_disabled">Slider Type</th>

                                                <th class="sorting_desc_disabled" style="width: 10%;">Banner Link</th>

                                                <th class="sorting_desc_disabled">Status<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                                <th class="sorting_desc_disabled notexport">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          <?php foreach($sliderArr as $index => $slider){
                                             
                                               if($slider->file_upload_type == "local"){
                                                   $slider_path = USER_UPLOAD_DIR.'home_sliders/'.$slider->banner_image; 

                                                   if ($slider->file_upload_type == "local" && !file_exists($slider_path)) {   
                                                      $banner_image = '<a href="'.RESOURCE_URL.'images/preview.jpg" data-fancybox="gallery" 
                                                                      data-caption="'.$slider->banner_title.'">
                                                                      <img alt="image" src="'.RESOURCE_URL.'images/preview.jpg">
                                                                     </a> ';
                                                   }else{
                                                      $slider_url = USER_UPLOAD_URL.'home_sliders/'.$slider->banner_image;
                                                   }      
                                                }else{
                                                   $slider_url = $slider->banner_image;   
                                                }

                                                $banner_image = '<a href="'.$slider_url.'" data-fancybox="gallery" 
                                                            data-caption="'.$slider->title.'">
                                                              <img alt="image" src="'.$slider_url.'">
                                                            </a> ';                                                             
                                              
                                           ?> 
                                                <tr>
                                                    <td>
                                                        <div class="pretty p-image p-plain selectAllItem ml-2">
                                                           <input type="checkbox" class="singleCheck" id="<?=$slider->id?>" value="<?=$slider->id?>"/>
                                                           <div class="state">
                                                              <img class="image" src="<?=RESOURCE_URL?>images/checkbox.png">
                                                              <label class="cursor-pointer selectAllItem" for="<?=$slider->id?>"></label>
                                                           </div>
                                                        </div> 
                                                    </td>
                                                    <!--<td class="project-title"><?=$index+1?></td>-->
                                                    
                                                    <td class="client-avatar">
                                                        <?=$banner_image?>
                                                    </td>
                                                    <td class="project-title">
                                                        <a href="<?=SITE_URL?>?route=home_sliders&type=edit&id=<?=$slider->id?>" data-toggle="tooltip" data-placement="bottom" title="Slider Title:  <?=$slider->banner_title?>"><?=str_replace('-',' ',$slider->banner_title)?>   </a>
                                                        <br/>
                                                        <small>Created <?=date('jS F, Y',strtotime($slider->created_at))?></small>
                                                    </td>

                                                    <td class="project-status" style="width: 30%;">
                                                       <?=str_replace('-',' ',$slider->banner_text)?>   
                                                    </td>

                                                    <td class="project-status">
                                                       <span class="label label-<?=($slider->slider_type=='header'?'primary':'success')?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Slider Position: <?=ucfirst($slider->slider_type)?>"><?=ucfirst($slider->slider_type)?></span>   
                                                    </td>

                                                     <td class="project-status" style="width: 10%;">
                                                        <a href="<?=FRONT_SITE_URL.$slider->banner_link?>" data-toggle="tooltip" data-placement="bottom" title="Banner Link:  <?=$slider->banner_link?>"><?=ucfirst($this->globalLibraryHandlerObj->seoUrlStructure($slider->banner_link,'r_seo'))?></a>
                                                    </td>

                                                    <td class="project-status">
                                                       <span class="label label-<?=($slider->record_status=='active'?'primary':($slider->record_status=='blocked'?'warning':'danger'))?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Slider status: <?=ucfirst($slider->record_status)?>"><?=ucfirst($slider->record_status)?></span>   
                                                    </td>

                                                    <td>
                                                      <span class="dropdown">
                                                          <button class="btn btn-success product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                                         <ul class="dropdown-menu">
                                                            <?php if($pagePermission){ ?>
                                                                <li>
                                                                 <a href="<?=SITE_URL?>?route=home_sliders&type=edit&id=<?=$slider->id?>" class="#" data-toggle="tooltip" data-placement="bottom" title="Edit this Slider"><i class="fa fa-pencil"></i> Edit Slider</a>
                                                               </li> 
                                                            <?php } ?>  
                                                           
                                                            <?php if($slider->record_status == 'active'){?>
                                                                <?php if($pagePermission){ ?>
                                                                   <li>
                                                                     <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$slider->id?>" data-type="home_sliders" data-ptype="Slider" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Slider"><i class="fa fa-trash"></i> Block Slider</a>
                                                                   </li>
                                                                <?php } ?>   

                                                                <?php if(!$pagePermission){ ?>
                                                                    <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                                                <?php } ?>   

                                                            <?php }else{ ?>
                                                                <?php if($pagePermission){ ?> 
                                                                   <li>
                                                                     <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$slider->id?>" data-type="home_sliders" data-ptype="Slider" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Block this Slider"><i class="fa fa-refresh"></i> Restore Slider</a>
                                                                   </li>
                                                                <?php } ?>   

                                                                <?php if($pagePermission){ ?> 
                                                                   <li>
                                                                     <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$slider->id?>" data-type="home_sliders" data-ptype="Slider" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Block this Slider"><i class="fa fa-times"></i> Delete Slider</a>
                                                                   </li>
                                                                <?php } ?>  

                                                                <?php if(!$pagePermission){ ?>
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
            <?php } ?>    
        </div>

       <!-- jQuery library -->
       <!-- Latest compiled JavaScript -->
   
        <script type="text/javascript">
            function readURL(input,id) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader(); 

                    reader.onload = function (e) {
                        $('#'+id).attr('src', e.target.result);
                        $('#image_upload_preview').attr('href', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function checkMediaURL(url) {
              return(url.match(/\.(jpeg|jpg|gif|png|pdf)$/) != null);
            }

             //Configuring fetching all page records fetching params
              $(document).on('submit', '#fetch_all_slider_records', function(event){
                  event.preventDefault();
                  var record_status = $('#record_status').val();
                  var page_route = $('#page_route').val();

                  var slider_type = $('#slider_type').val();

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
                       if(slider_type){
                          redirect_url += "&slider_type="+slider_type;
                       }
                       window.location = redirect_url;
                      
                     });},500);
                     return true;  
                 } 
               });

            $(document).ready(function(){
                 var checkbox_counter = 0;

                 $('.i-checks').iCheck({
                     checkboxClass: 'icheckbox_square-green',
                     radioClass: 'iradio_square-green',
                 });

                  //Handling file upload type checkbox
                 $(document).on('ifChanged', '.i-checks.file_upload_type input', function (e) {
                    var file_upload_type = $(this).val();
                     
                    if(file_upload_type == 'local'){
                       //show & hide requied div  
                       $('#banner_image_local_div').removeClass('d-none');
                       $('#banner_image_cdn_div').addClass('d-none');
                       $("#banner_image_local").prop("required",true);
                       $("#banner_image_cdn").prop("required",false);
                    }else{
                       //show & hide requied div 
                       $('#banner_image_cdn_div').removeClass('d-none');
                       $('#banner_image_local_div').addClass('d-none');
                       $("#banner_image_local").prop("required",false);
                       $("#banner_image_cdn").prop("required",true);
                    }
                 });

                 //franchise image url on blur handler 
                 $(document).on('blur','#banner_image_cdn',function(){
                     var media_url = $(this).val();

                     //console.log(checkMediaURL(media_url));

                     if(!checkMediaURL(media_url)){
                        toastr.error("Please add a valid url of the image", "Error!"); 
                        media_url = RESOURCE_URL+'images/preview.jpg';
                     }else{
                        toastr.success("Media image is successfully fetched.", "Success!"); 
                     }
                     $('#banner_image_preview').attr('src', media_url);
                     $('#image_upload_preview').attr('href', media_url);
                 }); 

                 /*Banner image preview*/ 
                 $("#banner_image_local").change(function () {
                    readURL(this,'banner_image_preview');
                 });

                 //verify franchise image file type before uploading into server
                 $("#banner_image_local").change(function () {
                    var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        alert("Only formats are allowed : "+fileExtension.join(', '));
                    }else{
                       readURL(this,'image_upload_preview'); 
                    }
                 });

                 //Multiple select category
                 $(".category").select2();
                 $('.category').select2({width: "94%"});

                 $(document).on('ifChanged', '.i-checks.content_type input', function (e) {
                     var checked = $(this).val();
                     
                     if(checked == 'image'){
                       $('#content_type').val('image');  
                       $('#content_image').removeClass('d-none');
                       $('#content_video').addClass('d-none');
                       $('#file_upload_type_selector').removeClass('d-none');
                       //$('#content_video').find('input[type="file"]').prop("required",true);
                    }else{
                       $('#content_type').val('video');   
                       $('#content_video').removeClass('d-none');
                       $('#content_image').addClass('d-none');
                       $('#file_upload_type_selector').addClass('d-none');
                       //$('#content_image').find('input[type="text"').prop("required",true);
                    }
                });
               
                //Gallery modifiy form handler
                $(document).on('submit', '#manage_gallery_form', function(event){
                    event.preventDefault();
                     
                     $.ajax({
                      url:ajaxControllerHandler,
                      method:'POST',
                      data: new FormData(this),
                      contentType:false,
                      processData:false,
                      beforeSend: function() {
                         //$('#manage_media_loader').addClass('sk-loading');
                         //$('#create').attr('disabled',true);
                      },
                      success:function(responseData){
                          var data = JSON.parse(responseData);
                          $('#create').attr('disabled',false);
                         //console.log(responseData);
                         if(data.check == 'success'){
                            //reseting form data
                            $('#manage_gallery_form')[0].reset();                    
                            //Clearing image preview data
                            $('#image_upload_preview').attr('src', 'http://placehold.it/100x100');
                            //Disabling loader
                            $('#manage_media_loader').removeClass('sk-loading');

                            if(data.last_insert_id>0){
                                var successTxt = "Slider has been successfully created!";
                            }else{
                                var successTxt = "Slider has been successfully updated!";
                            }
                            //show sweetalert success
                            swal({
                                title: "Great!",
                                text: successTxt,
                                type: "success"
                            },function() {
                                location.reload();
                            });
                            return true; 
                         }else{
                           //Disabling loader
                            $('#manage_media_loader').removeClass('sk-loading');
                            //show sweetalert success
                            swal({
                                title: "Error!",
                                text: data.message,
                                type: "error"
                            });
                            return false;
                         }
                      }
                  });
             
               });

            })
        </script>