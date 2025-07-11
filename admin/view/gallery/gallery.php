<?php
  $media_id = $_GET['id'];
  if($_GET['type'] == 'edit'){
    $itemDetailArr = $pageContent['pageData']['gallery_data']; 
    
    if($itemDetailArr->content_type == "image"){
      //Configuring franchise file data
         if($itemDetailArr->file_upload_type == "local"){
            if(strlen($itemDetailArr->content)>0 && file_exists(USER_UPLOAD_DIR.'gallery/'.$itemDetailArr->content)){
               $content = USER_UPLOAD_URL.'gallery/'.$itemDetailArr->content;
            }else{
               $content = RESOURCE_URL.'images/preview.jpg';
            }
         }else{
             $content = $itemDetailArr->content; 
         }     
    }else{
        $content = $itemDetailArr->content; 
    }
  }
  if(empty($_GET['type'])){
    $itemArr = $pageContent['pageData']['gallery_data'];   
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
  
  $galleryCategoryArr = explode(',',$pageContent['pageData']['gallery_data']->category_string);

  //Fetching page action permission
  $createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_gallery"); 
  $updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_gallery"); 
  $deletePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("delete_gallery"); 

  /*print"<pre>";
  print_r($itemArr);
  print"</pre>";*/
?>       


        <div class="wrapper wrapper-content">
          <?php if(!isset($_GET['type'])){ ?>   
              <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Fetch Gallery item based on their status </h5>
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
          <?php } ?>    

          <?php if($_GET['type'] == 'add'){ ?>   
                <div class="row " id="local_media_insert">
                    <div class="col-lg-12">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5>Upload files here</h5>
                            </div>
                            <div class="ibox-content">

                                <p>
                                    <strong>Upload Guideline: </strong> Please upload image file only with jpeg,jpg,png extension. Try to upload minumum size image file.
                                </p>

                                <div class="postDropzone" id="postDropzone">
                                    <div class="dropzone-file-area">
                                        <div class="dz-message" data-dz-message><span><h3 class='title'>Drop files here or click to upload files</h3><p class='text'> You have to upload the minumum size image file for keep the main site server clean and light. </p></span></div>
                                    </div>
                                </div>
                                
                                <div class="col-lg-12 col-md-12 col-sm-12 mt-3 pl-0">
                                    <a href="<?=SITE_URL?>?route=gallery" class="btn btn-danger"><i class="fa fa-reply"></i> Back to List Page</a> 
                                </div>    
                            </div>
                        </div>
                    </div>
                </div>
          <?php } ?> 
          
          <?php if($_GET['type'] == 'edit'){ ?>   
             <div class="row">
                <div class="col-lg-12">
                    <div id="responseMsg"></div>
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5><?=($_GET['type'] == 'add'?'Add':'Modify')?> Item Into Gallery </h5>
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
                              <input type="hidden" name="action" id="action" value="manageGalleryItem">
                              <input type="hidden" name="type" id="type" value="gallery">
                              <input type="hidden" name="content_type" id="content_type" value="">
                              <input type="hidden" name="media_id" id="media_id" value="<?=$media_id?>">
                                
                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Item Title <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter a title of this item"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="title" placeholder="Enter a title of this item..." value="<?=$itemDetailArr->title?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>
                               
                               <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Item Type <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a type of this item"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-2">
                                       <label class="checkbox-inline i-checks content_type"> <input type="radio" value="image" name="content_type" required <?=($itemDetailArr->content_type == 'image'?'checked':'')?> required/> <i></i>Image </label>
                                       <label class="checkbox-inline i-checks content_type"> <input type="radio" value="video" name="content_type" required <?=($itemDetailArr->content_type == 'video'?'checked':'')?> required> <i></i> Video </label>
                                    </div>

                                    <input type="hidden" name="hidden_content_type" value="<?=$itemDetailArr->content_type?>">
                                    
                                    <div class="col-lg-6 col-md-6 <?=($itemDetailArr->content_type == 'image'?'':'d-none')?>" id="file_upload_type_selector">
                                        <div class="row">
                                            <label class="col-sm-4 col-form-label text-right pt-2">File Upload Type <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Status"><i class="fa fa-question-circle"></i></span></label>
                                             <div class="col-sm-6 pt-2 pl-0">
                                               <label class="checkbox-inline i-checks file_upload_type"> <input type="radio" value="local" name="file_upload_type" <?=($itemDetailArr->file_upload_type=='local'?'checked':'')?>/> <i></i>Local </label>&nbsp;
                                               <label class="checkbox-inline i-checks file_upload_type"> <input type="radio" value="cdn" name="file_upload_type" <?=($itemDetailArr->file_upload_type=='cdn'?'checked':'')?>/> <i></i> CDN </label>
                                             </div>

                                             <input type="hidden" name="hidden_file_upload_type" value="<?=$itemDetailArr->file_upload_type?>">
                                        </div>     
                                    </div> 
                                 </div>       
                                <div class="hr-line-dashed"></div>

                                 <div class="<?=($itemDetailArr->content_type == 'video'?'':'d-none')?>" id="content_video">
                                      <div class="form-group row text-right" id="content_video"><label class="col-sm-2 col-form-label">Video URL <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter a Video URL"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-10">
                                          <div class="input-group">
                                            <input type="text" class="form-control" name="video_url" id="video_url" placeholder="Enter a Video URL..." value="<?=($itemDetailArr->content_type == 'video'?$itemDetailArr->content:'')?>">
                                         </div>   
                                        </div>
                                    </div>    
                                    <div class="hr-line-dashed"></div>                           
                                </div>
                                
                               <div class="<?=($itemDetailArr->content_type == 'image'?'':'d-none')?>" id="content_image">
                                    <div class="form-group row">
                                        <label class="col-sm-2 col-form-label text-right">Gallery Image <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                        <div class="col-sm-9">
                                         <div class="row pl-3"> 
                                          <?php if(isset($_GET['id'])){ ?>  
                                            <div class="col-sm-2 pb-3 pl-0">
                                               <h4>Current image</h4>
                                               <a href="<?=$content?>" data-fancybox="gallery" data-caption="<?=$itemDetailArr->title?>">  
                                                  <img id="current_image_review" src="<?=$content?>" alt="Gallery image" style="height: 100px;width: 100px;" />
                                                </a>
                                             </div>
                                            <?php } ?> 

                                            <div class="<?php (isset($_GET['id']) ? 'col-sm-10' : 'col-sm-12')?> pb-3 pl-0">
                                             <h4>Preview image</h4>
                                             <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="media_preview_image" data-fancybox="gallery" data-caption="<?=$itemDetailArr->title?>"> 
                                               <img id="image_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                             </a>  
                                            </div>
                                          </div>  
                                          
                                          <div class="btn-group <?=($itemDetailArr->file_upload_type=='local'?'':'d-none')?>" id="media_image_local_div">
                                              <label title="Upload a file" for="local_media_image" class="btn btn-primary">
                                                  <input type="file" accept="image/*" name="local_media_image" id="local_media_image" class="hide" />
                                                  Upload a featured image of the media...
                                              </label>    
                                          </div>

                                          <div class="input-group <?=($itemDetailArr->file_upload_type=='cdn'?'':'d-none')?>" id="media_image_cdn_div">
                                            <input type="text" class="form-control" name="media_image_cdn" id="media_image_cdn" placeholder="Enter Media Image URL..." value="<?=($itemDetailArr->file_upload_type == 'cdn'?$itemDetailArr->content:'')?>">
                                          </div>  

                                        </div>
                                        <input type="hidden" name="hidden_media_content" value="<?=$itemDetailArr->content?>">
                                        <div class="col-sm-1 pl-5">
                                      </div>
                                     </div>

                                     <div class="hr-line-dashed"></div>
                                 </div>       

                                 <div class="form-group row"><label class="col-sm-2 col-form-label text-right pt-2">Select Category <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Category from Dropdown"><i class="fa fa-question-circle"></i></span></label>
                                      <div class="col-sm-10 pr-0" id="state_loader_div">
                                        <div class="input-group">
                                            <select class="category" name="category_id[]" id="category_id" data-placeholder="Choose a Category first..." tabindex="2" multiple required>
                                              <option></option>
                                                <?php 
                                                  foreach($pageContent['pageData']['category_data'] as $parent_category){ 
                                                    if(in_array($parent_category->id, $galleryCategoryArr)){
                                                ?>
                                                      <option value="<?=$parent_category->id?>" selected><?=$parent_category->name?></option>
                                                <?php
                                                 }else{ 
                                                 ?>
                                                      <option value="<?=$parent_category->id?>"><?=$parent_category->name?></option>
                                                 <?php
                                                   }
                                                  }
                                                ?>
                                           </select>
                                       </div>

                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Status"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-2">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="record_status" required <?=($itemDetailArr->record_status == 'active'?'checked':'')?> required/> <i></i>Active </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" name="record_status" required <?=($itemDetailArr->record_status == 'blocked'?'checked':'')?> required/> <i></i> Blocked </label>
                                    </div>

                                    <label class="col-sm-2 col-form-label text-right">Featured Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Featured Status"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-2">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="featured_status" <?=($itemDetailArr->featured_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="inactive" name="featured_status" <?=($itemDetailArr->featured_status)=='inactive'?'checked':''?> required> <i></i> Inactive </label>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=gallery" data-toggle="tooltip" title="" class="btn btn-default selectAction" data-type="hideAddItemDiv" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
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
                                <h5>All Gallery Item List</h5>
                                <div class="ibox-tools">
                                         <?php if($createPermission){ ?> 
                                         <a href="<?=SITE_URL?>?route=gallery&type=add" class="table-action-primary" data-toggle="tooltip" data-placement="bottom" title="Add New Gallery Item"><i class="fa fa-plus-circle"></i></a>
                                     <?php }?>

                                      <a href="<?=SITE_URL?>?route=gallery" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Gallery Item Data"><i class="fa fa-refresh"></i></a>
                                  
                                     <?php if($record_status == 'active'){ ?>
                                       <?php if($updatePermission){ ?>
                                          <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid = "all" data-type="gallery" data-ptype="Gallery" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
                                       <?php } ?>   
                                    <?php }else{ ?>   
                                      <?php if($updatePermission){ ?>
                                        <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid = "all" data-type="gallery" data-ptype="Gallery" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
                                      <?php } ?>  

                                      <?php if($deletePermission){ ?>
                                         <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid = "all" data-type="gallery" data-ptype="Gallery" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
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
                                                <th class="notexport">Content<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                                <th class="sorting_desc_disabled">Title<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                                <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Media Type">Type<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                                <th class="sorting_desc_disabled" style="width: 20%;">Category<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                                <th class="sorting_desc_disabled">Status<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                                                <th class="sorting_desc_disabled notexport">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                          <?php foreach($itemArr as $index => $media){
                                             
                                                if($media->content_type == 'image'){
                                                   
                                                   if($media->file_upload_type == "local"){
                                                       $media_path = USER_UPLOAD_DIR.'gallery/'.$media->content; 

                                                       if ($media->content_type == "image" && !file_exists($media_path)) {   
                                                          $content = '<a href="'.RESOURCE_URL.'images/preview.jpg" data-fancybox="gallery" 
                                                                          data-caption="'.$media->title.'">
                                                                          <img alt="image" src="'.RESOURCE_URL.'images/preview.jpg">
                                                                         </a> ';
                                                       }else{
                                                          $media_url = USER_UPLOAD_URL.'gallery/'.$media->content;
                                                       }      
                                                    }else{
                                                       $media_url = $media->content;   
                                                    }

                                                    $content = '<a href="'.$media_url.'" data-fancybox="gallery" 
                                                                data-caption="'.$media->title.'">
                                                                  <img alt="image" src="'.$media_url.'">
                                                                </a> ';                                                             
                                                }else{
                                                   $media_url = $media->content;  
                                                   //$video_thumbnail = RESOURCE_URL.'images/watch_video.gif';
                                                   if(strpos($media_url, 'be/')>0){
                                                     $video_id = explode("be/", $media_url)[1];
                                                     $video_thumbnail = 'https://img.youtube.com/vi/'.$video_id.'/hqdefault.jpg';
                                                   }

                                                   if(strpos($media_url, 'embed/')>0){
                                                     $video_id = explode("embed/", $media_url)[1];
                                                     $video_thumbnail = 'https://img.youtube.com/vi/'.$video_id.'/hqdefault.jpg';
                                                   } 
                                                    
                                                   $content = '<a href="'.$media_url.'" data-fancybox="gallery" 
                                                                data-caption="'.$media->title.'">
                                                                <img alt="image" src="'.$video_thumbnail.'">
                                                               </a> ';
                                                }  
                                           ?> 
                                                <tr>
                                                    <td>
                                                        <div class="pretty p-image p-plain selectAllItem ml-2">
                                                           <input type="checkbox" class="singleCheck" id="<?=$media->id?>" value="<?=$media->id?>"/>
                                                           <div class="state">
                                                              <img class="image" src="<?=RESOURCE_URL?>images/checkbox.png">
                                                              <label class="cursor-pointer selectAllItem" for="<?=$media->id?>"></label>
                                                           </div>
                                                        </div> 
                                                    </td>
                                                    <!--<td class="project-title"><?=$index+1?></td>-->
                                                    
                                                    <td class="client-avatar">
                                                        <?=$content?>
                                                    </td>
                                                    <td class="project-title">
                                                        <a href="<?=SITE_URL?>?route=gallery&type=edit&id=<?=$media->id?>" data-toggle="tooltip" data-placement="bottom" title="Media Title:  <?=$media->title?>"><?=$media->title?></a>
                                                        <br/>
                                                        <small>Created <?=date('jS F, Y',strtotime($media->created_at))?></small>
                                                    </td>

                                                     <td class="project-status">
                                                       <span class="label label-info cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Change status by clicking status button"><?=ucfirst($media->content_type)?></span>   
                                                    </td>

                                                    <td class="project-title">
                                                        <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Gallery Category: <?=$media->category_string?>"><?=$media->category_string?></span>
                                                    </td>

                                                    <td class="project-status">
                                                       <span class="label label-<?=($media->record_status=='active'?'primary':($media->record_status=='blocked'?'warning':'danger'))?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Media status: <?=ucfirst($media->record_status)?>"><?=ucfirst($media->record_status)?></span>   
                                                    </td>

                                                    <td>
                                                      <span class="dropdown">
                                                          <button class="btn btn-success product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                                         <ul class="dropdown-menu">
                                                            <?php if($updatePermission){ ?>
                                                                <li>
                                                                 <a href="<?=SITE_URL?>?route=gallery&type=edit&id=<?=$media->id?>" class="#" data-toggle="tooltip" data-placement="bottom" title="Edit this Media"><i class="fa fa-pencil"></i> Edit Media</a>
                                                               </li> 
                                                            <?php } ?>  

                                                            <?php if($updatePermission){ ?>
                                                               <li>
                                                                 <a href="javascript:void(0)" id="item_<?=$media->id?>" class="featured_action" data-type="gallery" data-ptype="Gallery" data-ftype="<?=($media->featured_status=='active'?'inactive':'active')?>" data-rid="<?=$media->id?>" data-toggle="tooltip" data-placement="bottom" title="<?=($media->featured_status=='active'?'Non-Featured':'Featured')?> this Media"><i class="<?=($media->featured_status=='active'?'fa fa-star':'fa fa-star-o')?>"></i> <?=($media->featured_status=='active'?'Featured':'Non-Featured')?> 
                                                                 </a>
                                                               </li>
                                                            <?php } ?>
                                                           
                                                            <?php if($media->record_status == 'active'){?>
                                                                <?php if($updatePermission){ ?>
                                                                   <li>
                                                                     <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$media->id?>" data-type="gallery" data-ptype="Media" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Media"><i class="fa fa-trash"></i> Block Media</a>
                                                                   </li>
                                                                <?php } ?>   

                                                                <?php if(!$updatePermission){ ?>
                                                                    <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                                                <?php } ?>   

                                                            <?php }else{ ?>
                                                                <?php if($updatePermission){ ?> 
                                                                   <li>
                                                                     <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$media->id?>" data-type="gallery" data-ptype="Media" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Block this Media"><i class="fa fa-refresh"></i> Restore Media</a>
                                                                   </li>
                                                                <?php } ?>   

                                                                <?php if($deletePermission){ ?> 
                                                                   <li>
                                                                     <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$media->id?>" data-type="gallery" data-ptype="Media" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Block this Media"><i class="fa fa-times"></i> Delete Media</a>
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
            <?php } ?>    
        </div>

       <!-- jQuery library -->
       <!-- Latest compiled JavaScript -->
   
        <script type="text/javascript">
            var media_id = $("#media_id").val();

            function readURL(input,id) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader(); 

                    reader.onload = function (e) {
                        $('#'+id).attr('src', e.target.result);
                        $('#media_preview_image').attr('href', e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            function checkMediaURL(url) {
              return(url.match(/\.(jpeg|jpg|gif|png|pdf)$/) != null);
            }

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
                       $('#media_image_local_div').removeClass('d-none');
                       $('#media_image_cdn_div').addClass('d-none');

                       if(!media_id){
                          $("#local_media_image").prop("required",true);
                          $("#media_image_cdn").prop("required",false);
                       }    
                    }else{
                       //show & hide requied div 
                       $('#media_image_cdn_div').removeClass('d-none');
                       $('#media_image_local_div').addClass('d-none');

                       if(!media_id){
                          $("#local_media_image").prop("required",false);
                          $("#media_image_cdn").prop("required",true);
                       }  
                    }
                 });

                 //franchise image url on blur handler 
                 $(document).on('blur','#media_image_cdn',function(){
                     var media_url = $(this).val();

                     //console.log(checkMediaURL(media_url));

                     if(!checkMediaURL(media_url)){
                        toastr.error("Please add a valid url of the image", "Error!"); 
                        media_url = RESOURCE_URL+'images/preview.jpg';
                     }else{
                        toastr.success("Media image is successfully fetched.", "Success!"); 
                     }
                     $('#image_upload_preview').attr('src', media_url);
                     $('#media_preview_image').attr('href', media_url);
                 }); 

            
                 //verify galley image file type before uploading into server
                 $("#local_media_image").change(function () {
                    var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        alert("Only formats are allowed : "+fileExtension.join(', '));
                    }else{
                       $(this).valid(); 
                       readURL(this,'image_upload_preview'); 
                    }
                 });

                 //Multiple select category
                 $(".category").select2();
                 $('.category').select2({width: "94%"});

                 $(document).on('change','.category',function(event){
                    $(this).valid();
                 });

                 $(document).on('ifChanged', '.i-checks.content_type input', function (e) {
                     var checked = $(this).val();
                     
                     if(checked == 'image'){
                       $('#content_type').val('image');  
                       $('#content_image').removeClass('d-none');
                       $('#content_video').addClass('d-none');
                       $('#file_upload_type_selector').removeClass('d-none');
                       $('input[name="file_upload_type"]').prop("required",true);
                       if(!media_id){ 
                         $('#video_url').prop("required",false);
                       }  
                    }else{
                       $('#content_type').val('video');   
                       $('#content_video').removeClass('d-none');
                       $('#content_image').addClass('d-none');
                       $('#file_upload_type_selector').addClass('d-none');
                       if(!media_id){
                         $('#video_url').prop("required",true);
                       }  
                       $('input[name="file_upload_type"]').prop("required",false);
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
                            //show sweetalert success
                            swal({
                                title: "Great!",
                                text: "Item has been successfully modified!",
                                type: "success"
                            },function() {
                                window.location = "<?=SITE_URL?>?route=gallery"
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

               //Dropzone gallery bulk upload handler
               $('#postDropzone').dropzone({
                    url: ajaxControllerHandler,
                    params: {action: 'galleryBulkUploader'},
                    addRemoveLinks: true,
                    autoProcessQueue: true,
                    dictResponseError: 'Error uploading file!',
                    dictDefaultMessage: "<h3 class='title'>Drop files here or click to upload screenshot</h3><p class='text'> You have to upload the screenshot for your software to satisfy the customers </p>",
                    acceptedFiles: ".jpeg,.jpg,.png,.pdf",
                    success: function(file, responseData) {
                        var data = JSON.parse(responseData);
                        //console.log(responseData);
                        if (data.check == 'success') {
                            //file.id = data.file_id;
                            //show successful toastr notificaton
                            toastr.success(data.message, 'File uploaded!', {
                              timeOut: 2000,
                              closeButton: true,
                              progressBar: true
                            });
                              
                            return true;
                        } else {
                            toastr.error(data.message, 'File upload error!', {
                                timeOut: 4000,
                                closeButton: true,
                                progressBar: true
                            });
                            return false;
                        }
                    },
                    init: function() {
                        thisDropzone = this;

                        this.on('queuecomplete', function() {
                            this.removeAllFiles();
                        })
                      }
                 });
            })
        </script>