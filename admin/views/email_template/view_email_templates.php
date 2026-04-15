<?php  
  $templateArr = $pageContent['pageData']['email_template_data']; 

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
  $createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_template"); 
  $updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_template"); 
  $deletePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("delete_template"); 
  /*print"<pre>";
  print_r($templateArr); 
  print"</pre>";*/ 
?>
 
                 <div class="wrapper wrapper-content fadeInRight">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="ibox ">
                                <div class="ibox-title">
                                    <h5>Fetch Template based on their status </h5>
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
                                     <a href="<?=SITE_URL?>?route=add_email_template" class="table-action-primary" data-toggle="tooltip" data-placement="bottom" title="Add New Template"><i class="fa fa-plus-circle"></i></a>
                                 <?php }?>

                                  <a href="<?=SITE_URL?>?route=view_Template" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Template Data"><i class="fa fa-refresh"></i></a>
                              
                                 <?php if($record_status == 'active'){ ?>
                                   <?php if($updatePermission){ ?>
                                      <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid = "all" data-type="email_template" data-ptype="Template" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
                                   <?php } ?>   
                                <?php }else{ ?>   
                                  <?php if($updatePermission){ ?>
                                    <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid = "all" data-type="email_template" data-ptype="Template" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
                                  <?php } ?>  

                                  <?php if($deletePermission){ ?>
                                     <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid = "all" data-type="email_template" data-ptype="Template" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
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
                                        <!--<th class="sorting_desc_disabled">SL No.</th>-->
                                        <th class="sorting_desc_disabled">Email Subject <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>
                                        <!--<th class="sorting_desc_disabled">Email Code <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>-->
                                        <th class="sorting_desc_disabled" style="width:12%;">Email For <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>
                                        <th class="sorting_desc_disabled">Email From <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>
                                        <th class="sorting_desc_disabled">Email CC <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>    
                                        <th class="sorting_desc_disabled">Status <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>
                                        <th class="sorting_desc_disabled notexport">Action</i></th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <?php foreach($templateArr as $index => $template){?> 
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
                                                
                                                <!--<td class="project-title"><?=$index+1?></td>-->
                                               
                                                <td class="project-title">
                                                    <a href="<?=SITE_URL.'?route=add_email_template&id='.$template->id?>" data-toggle="tooltip" data-placement="bottom" title="Email Subject:  <?=$template->subject?>"><?=ucfirst($template->subject)?></a>
                                                    <br/>
                                                    <small>Created <?=date('jS F, Y',strtotime($template->created_at))?></small>
                                                </td>

                                                <!--<td class="project-title">
                                                  <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="Email Code: <?=$template->code?>"><?=$template->code?></a>-->
                                                </td>

                                                <td class="project-title">
                                                   <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="Email For:  <?=$template->email_for?>"><?=ucfirst($template->email_for)?></a>
                                                </td>

                                                <td class="project-title">
                                                   <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="Email From:  <?=$template->from_email?>"><?=$template->from_email?></a>
                                                </td>

                                                <td class="project-title">
                                                   <a href="javascript:void(0)" data-toggle="tooltip" data-placement="bottom" title="Email CC:  <?=$template->cc_email?>"><?=$template->cc_email?></a>
                                                </td>

                                               <td class="project-status">
                                                 <span class="label label-<?=($template->record_status == 'active'?'primary':'danger')?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Template Status: <?=ucfirst($template->record_status)?>"><?=ucfirst($template->record_status)?></span> 
                                                </td>

                                               <td>

                                                 <span class="dropdown">
                                                  <button class="btn btn-success product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                                 <ul class="dropdown-menu">
                                                    <?php if($updatePermission){ ?>
                                                        <li>
                                                         <a href="<?=SITE_URL?>?route=edit_email_template&id=<?=$template->id?>" class="#" data-toggle="tooltip" data-placement="bottom" title="Edit this Template"><i class="fa fa-pencil"></i> Edit Template</a>
                                                       </li> 
                                                    <?php } ?>   
                                                   
                                                   <?php if($template->record_status == 'active'){?>
                                                       <?php if($updatePermission){ ?>
                                                           <li>
                                                             <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$template->id?>" data-type="email_template" data-ptype="Template" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Template"><i class="fa fa-trash"></i> Block Template</a>
                                                           </li>
                                                        <?php } ?> 

                                                         <?php if(!$updatePermission){ ?>
                                                            <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                                         <?php } ?>  
                                                    <?php }else{ ?>
                                                       <?php if($updatePermission){ ?> 
                                                           <li>
                                                             <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$template->id?>" data-type="email_template" data-ptype="Template" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore this Template"><i class="fa fa-refresh"></i> Restore Template</a>
                                                           </li>
                                                        <?php } ?>
                                                        
                                                        <?php if($deletePermission){ ?>   
                                                           <li>
                                                             <a href="javascript:void(0)" class="changeRecordStatus" data-rid = "<?=$template->id?>" data-type="email_template" data-ptype="Template" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete this Template"><i class="fa fa-times"></i> Delete Template</a>
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
       
