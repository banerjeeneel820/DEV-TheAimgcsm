<?php
  
  $siteSettingArr = $pageContent['pageData']['site_settings'];
  $page_type = $pageContent['pageData']['page_type'];
  
  //post thubnail array
  $pageMediaArr = json_decode($siteSettingArr->logo,true);
  /*print"<pre>";
  print_r($siteSettingArr);
  print"</pre>";*/

?>
        <div class="wrapper wrapper-content fadeInRight">  
           
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Update Site Settings</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content" id="update_site_setting_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <form id="update_site_setting_form" class="needs-validation" method="post" onsubmit="return false;">
                              <input type="hidden" name="action" id="action" value="updateSiteSettings">
                              <input type="hidden" name="type" id="type" value="<?=$page_type?>">

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Site Title  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Site Title"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="title" placeholder="Enter Site Title..." value="<?=$siteSettingArr->title?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Contact Email  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Contact Email"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="email" class="form-control" name="contact_email" id="contact_email" placeholder="Enter Contact Email..." value="<?=$siteSettingArr->contact_email?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Career Email  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Career Email"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="email" class="form-control" name="career_email" id="career_email" placeholder="Enter Career Email..." value="<?=$siteSettingArr->career_email?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                             <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Buiness Email  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Buiness Email"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="email" class="form-control" id="business_email" name="business_email" placeholder="Enter Buiness Email..." value="<?=$siteSettingArr->business_email?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>
                              
                              <div class="form-group row">
                                <label class="col-sm-2 col-form-label text-right">Contact No  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Contact No"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-4">
                                    <div class="input-group">
                                      <input type="number" class="form-control" name="phone" placeholder="Enter Contact No..." value="<?=$siteSettingArr->phone?>" minlength="10" maxlength="10" required>
                                   </div>   
                                  </div>
                                  
                                  <label class="col-sm-2 col-form-label text-right">Site Caching  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Contact No"><i class="fa fa-question-circle"></i></span></label>
                                   <div class="col-sm-4 pt-2">
                                     <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="site_caching" <?=($siteSettingArr->site_caching)=='active'?'checked':''?> required/> <i></i>Active </label>
                                     <label class="checkbox-inline i-checks"> <input type="radio" value="inactive" name="site_caching" <?=($siteSettingArr->site_caching)=='inactive'?'checked':''?> required> <i></i> Inactive </label>
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Comapany Signature <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                   <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                        <div class="col-sm-2 pb-3 pl-0">
                                           <h4>Current image</h4>
                                           <a href="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->signature?>" data-fancybox="gallery" data-caption="<?=$siteSettingArr->signature?>">  
                                              <img id="current_signature_review" src="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->signature?>" alt="Company Signature" style="height: 100px;width: 100px;" />
                                            </a>  
                                         </div>

                                        <div class="col-sm-10 pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="signature" data-fancybox="gallery" data-caption="<?=$siteSettingArr->title?>"> 
                                           <img id="signature_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                      
                                      <div class="btn-group">
                                          <label title="Upload a file" for="companySignature" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="signature" id="companySignature" class="hide" />
                                              Upload a Signature of Institution...
                                          </label>    
                                      </div>
                                    </div>
                                    <input type="hidden" name="hidden_signature" value="<?=$siteSettingArr->signature?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                               </div>
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Facebook Link  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Facebook Link"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="facebook_link" placeholder="Enter Facebook Link..." value="<?=$siteSettingArr->facebook_link?>">
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                               <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Youtube Link  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Youtube Link"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="youtube_link" placeholder="Enter Youtube Link..." value="<?=$siteSettingArr->youtube_link?>">
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                               <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Twitter Link  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Twitter Link"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="twitter_link" placeholder="Enter Twitter Link..." value="<?=$siteSettingArr->twitter_link?>">
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                               <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Skype Link  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Skype Link"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="skype_link" placeholder="Enter Skype Link..." value="<?=$siteSettingArr->skype_link?>">
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Instagram Link  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Instagram Link"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="instagram_link" placeholder="Enter Instagram Link..." value="<?=$siteSettingArr->instagram_link?>">
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                               <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Telegram Link  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Telegram Link"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="telegram_link" placeholder="Enter Telegram Link..." value="<?=$siteSettingArr->telegram_link?>">
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Linkdin Link  <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Linkdun Link"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="linkdin_link" placeholder="Enter Linkdin Link..." value="<?=$siteSettingArr->linkdin_link?>">
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row">
                                 <label class="col-sm-2 col-form-label text-right">Feedback Availibility&nbsp;<span class="cursor-pointer" data-toggle="tooltip" data-placement="right" title="Select the availibility of feedback section"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-4 pt-2">
                                     <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="feedback_status" <?=($siteSettingArr->feedback_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                     <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" name="feedback_status" <?=($siteSettingArr->feedback_status)=='blocked'?'checked':''?> required> <i></i> Blocked </label>
                                  </div>

                                 <label class="col-sm-2 col-form-label text-right">Site Availibility&nbsp;<span class="cursor-pointer" data-toggle="tooltip" data-placement="right" title="Select the availibility of site"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-4 pt-2">
                                     <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="maintenance_status" <?=($siteSettingArr->maintenance_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                     <label class="checkbox-inline i-checks"> <input type="radio" value="inactive" name="maintenance_status" <?=($siteSettingArr->maintenance_status)=='inactive'?'checked':''?> required> <i></i> Under Maintenance </label>
                                  </div>   
                                       
                                </div>
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Comapany Logo <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                   <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                        <div class="col-sm-2 pb-3 pl-0">
                                           <h4>Current image</h4>
                                           <a href="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->logo?>" data-fancybox="gallery" data-caption="<?=$siteSettingArr->title?>">  
                                              <img id="current_image_review" src="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->logo?>" alt="Company Logo" style="height: 100px;width: 100px;" />
                                            </a>  
                                         </div>

                                        <div class="col-sm-10 pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="logo" data-fancybox="gallery" data-caption="<?=$siteSettingArr->title?>"> 
                                           <img id="image_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                      
                                      <div class="btn-group">
                                          <label title="Upload a file" for="companyLogo" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="logo" id="companyLogo" class="hide" />
                                              Upload a logo of Institution...
                                          </label>    
                                      </div>
                                    </div>
                                    <input type="hidden" name="hidden_logo" value="<?=$siteSettingArr->logo?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>


                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Header Logo <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                   <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                        <div class="col-sm-2 pb-3 pl-0">
                                           <h4>Current image</h4>
                                           <a href="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->header_logo?>" data-fancybox="gallery" data-caption="<?=$siteSettingArr->header_logo?>">  
                                              <img id="current_hlogo_review" src="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->header_logo?>" alt="Company Signature" style="height: 100px;width: 100px;" />
                                            </a>  
                                         </div>

                                        <div class="col-sm-10 pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="header_logo" data-fancybox="gallery" data-caption="<?=$siteSettingArr->title?>"> 
                                           <img id="hlogo_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                      
                                      <div class="btn-group">
                                          <label title="Upload a file" for="headerLogo" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="header_logo" id="headerLogo" class="hide" />
                                              Upload header logo of Institution...
                                          </label>    
                                      </div>
                                    </div>
                                    <input type="hidden" name="hidden_header_logo" value="<?=$siteSettingArr->header_logo?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                               </div>
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Sticky Header Logo <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                   <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                        <div class="col-sm-2 pb-3 pl-0">
                                           <h4>Current image</h4>
                                           <a href="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->sticky_logo?>" data-fancybox="gallery" data-caption="<?=$siteSettingArr->title?>">  
                                              <img id="current_sticky_review" src="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->sticky_logo?>" alt="Company Logo" style="height: 100px;width: 100px;" />
                                            </a>  
                                         </div>

                                        <div class="col-sm-10 pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="sticky_logo" data-fancybox="gallery" data-caption="<?=$siteSettingArr->title?>"> 
                                           <img id="sticky_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                      
                                      <div class="btn-group">
                                          <label title="Upload a file" for="stickyLogo" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="sticky_logo" id="stickyLogo" class="hide" />
                                              Upload a logo for sticky header of Institution...
                                          </label>    
                                      </div>
                                    </div>
                                    <input type="hidden" name="hidden_sticky_logo" value="<?=$siteSettingArr->sticky_logo?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Footer Logo <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                   <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                        <div class="col-sm-2 pb-3 pl-0">
                                           <h4>Current image</h4>
                                           <a href="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->footer_logo?>" data-fancybox="gallery" data-caption="<?=$siteSettingArr->title?>">  
                                              <img id="current_footer_review" src="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->footer_logo?>" alt="Company Logo" style="height: 100px;width: 100px;" />
                                            </a>  
                                         </div>

                                        <div class="col-sm-10 pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="footer_logo" data-fancybox="gallery" data-caption="<?=$siteSettingArr->title?>"> 
                                           <img id="footer_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                      
                                      <div class="btn-group">
                                          <label title="Upload a file" for="footerLogo" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="footer_logo" id="footerLogo" class="hide" />
                                              Upload a logo for footer of Institution...
                                          </label>    
                                      </div>
                                    </div>
                                    <input type="hidden" name="hidden_footer_logo" value="<?=$siteSettingArr->footer_logo?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Favicon Images <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                   <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                        <div class="col-sm-2 pb-3 pl-0">
                                           <h4>Current image</h4>
                                           <a href="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->favicon?>" data-fancybox="gallery" data-caption="<?=$siteSettingArr->title?>">  
                                              <img id="current_fav_review" src="<?=USER_UPLOAD_URL.'others/'.$siteSettingArr->favicon?>" alt="Company Logo" style="height: 100px;width: 100px;" />
                                            </a>  
                                         </div>

                                        <div class="col-sm-10 pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="favicon" data-fancybox="gallery" data-caption="<?=$siteSettingArr->title?>"> 
                                           <img id="fav_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                      
                                      <div class="btn-group">
                                          <label title="Upload a file" for="favIcon" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="favicon" id="favIcon" class="hide" />
                                              Upload a logo for footer of Institution...
                                          </label>    
                                      </div>
                                    </div>
                                    <input type="hidden" name="hidden_favicon" value="<?=$siteSettingArr->favicon?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Copyright <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Copyright Text"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="copyright" placeholder="Enter Copyright Text..." value="<?=$siteSettingArr->copyright?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Address <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Company Address"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="address" placeholder="Enter Company Address..." value="<?=$siteSettingArr->address?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="forum-title pr-38 border-bottom mb-4">
                                <h3>Site Decription</h3>
                              </div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Site Description <span class="cursor-pointer" data-toggle="tooltip" datdata-placement="bottom" title="Enter Company Description"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <textarea class="form-control" name="description" style="height:100px;border-radius:10px;" maxlength="300" id="description" required><?=$siteSettingArr->description?></textarea>
                                    <span id="textbox_charNumCount" style="color:red;"></span>
                                </div>
                              </div>
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row">
                                  <div class="col-sm-4 col-sm-offset-2">
                                      <a href="<?=SITE_URL.'?route=edit_site_setting'?>" data-toggle="tooltip" title="" class="btn btn-default btn-sm" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
                                      <button class="btn btn-success btn-sm" id="submit" type="submit" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="Save"><i class="fa fa-save"></i> Save</button>
                                  </div>
                              </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>


         <!-- Custom JS -->
         <script>
            function readURL(input,upload_id) {
               if (input.files && input.files[0]) {
                  var reader = new FileReader(); 

                  reader.onload = function (e) {
                     if(upload_id == "image_upload_preview"){
                       $('#'+upload_id).attr('src', e.target.result);
                       $('#logo').attr('href', e.target.result);
                     }

                     else if(upload_id == "signature_upload_preview"){
                       $('#'+upload_id).attr('src', e.target.result);
                       $('#signature').attr('href', e.target.result);
                     }

                     else if(upload_id == "hlogo_upload_preview"){
                       $('#'+upload_id).attr('src', e.target.result);
                       $('#header_logo').attr('href', e.target.result);
                     }

                     else if(upload_id == "sticky_upload_preview"){
                       $('#'+upload_id).attr('src', e.target.result);
                       $('#sticky_logo').attr('href', e.target.result);
                     }

                     else if(upload_id == "footer_upload_preview"){
                       $('#'+upload_id).attr('src', e.target.result);
                       $('#footer_logo').attr('href', e.target.result);
                     }

                     else if(upload_id == "fav_upload_preview"){
                       $('#'+upload_id).attr('src', e.target.result);
                       $('#favicon').attr('href', e.target.result);
                     }
                  }
                  reader.readAsDataURL(input.files[0]);
               }
           }

           //function to check unique email id for user  
           function check_site_email(email){
             
              if(email.length >0){
                var regularExp = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
                var chkEmail = regularExp.test(email);

              if(!chkEmail){
                  swal({
                     title: "Oops!",
                     text: "Enter a proper email!",
                     type: "error"
                  });
                  $('#create').attr('disabled',true);
                  return false;
               }
              }else{
                swal({
                   title: "Oops!",
                   text: "This field is required!",
                   type: "error"
                });
                $('#create').attr('disabled',true);
                return false;
              }
            } 

           $(document).ready(function () {
               //Declaring datepicker triiger validation variable
               var trigger = false;

                //Handling file upload type checkbox
                $(document).on('ifChanged', '.i-checks.domain_modify_type input', function (e) {
                    var domain_modify_type = $(this).val();
                     
                    if(domain_modify_type == 'concat'){
                       //show & requied div  
                       $('#delete_resources_lbl_id').removeClass('d-none');
                       $('#delete_resources_div_id').removeClass('d-none');
                    }else{
                       //hide requied div 
                       $('#delete_resources_lbl_id').addClass('d-none');
                       $('#delete_resources_div_id').addClass('d-none');
                    }
                 });

               
               /*Summernote HTML5 Text Editor*/
               //$('.summernote').summernote();
              /*------- Ends Here ---------*/

              //Count maximum characters in textarea
              $(document).on('keyup','#description',function(){
                var len = $(this).val().length;
                var char_left = (300 - len);
                if (len >= 300) {
                  toastr.warning("Max length reached;content can not exceed more than 300 characters.", "Warning!",{ timeOut: 4000 }); 
                  $('#textbox_charNumCount').html("Maximum length reached;content can not exceed more than 300 characters.");
                } else {
                  $('#textbox_charNumCount').html("Characters left "+char_left+'!');
                }
              });

              //verify franchise image file type before uploading into server
              $("#companySignature").change(function () {
                  var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                  if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                     alert("Only formats are allowed : "+fileExtension.join(', '));
                  }else{
                     readURL(this,"signature_upload_preview"); 
                  }
              });

               //verify franchise image file type before uploading into server
              $("#companyLogo").change(function () {
                  var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                  if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                     alert("Only formats are allowed : "+fileExtension.join(', '));
                  }else{
                     readURL(this,"image_upload_preview"); 
                  }
              });

               //verify franchise image file type before uploading into server
              $("#headerLogo").change(function () {
                  var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                  if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                     alert("Only formats are allowed : "+fileExtension.join(', '));
                  }else{
                     readURL(this,"hlogo_upload_preview"); 
                  }
              });

               //verify franchise image file type before uploading into server
              $("#stickyLogo").change(function () {
                  var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                  if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                     alert("Only formats are allowed : "+fileExtension.join(', '));
                  }else{
                     readURL(this,"sticky_upload_preview"); 
                  }
              });

               //verify franchise image file type before uploading into server
              $("#footerLogo").change(function () {
                  var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                  if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                     alert("Only formats are allowed : "+fileExtension.join(', '));
                  }else{
                     readURL(this,"footer_upload_preview"); 
                  }
              });

              //verify franchise image file type before uploading into server
              $("#favIcon").change(function () {
                  var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                  if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                     alert("Only formats are allowed : "+fileExtension.join(', '));
                  }else{
                     readURL(this,"fav_upload_preview"); 
                  }
              });

              //Handle update site setting form
              $(document).on('submit', '#update_site_setting_form', function(event){
                 event.preventDefault();

                 var contact_email = $('#contact_email').val();
                 var career_email = $('#career_email').val();
                 var business_email = $('#business_email').val();
                  
                 //Checking if the emails are valid
                 check_site_email(contact_email);
                 check_site_email(career_email);
                 check_site_email(business_email);

                 $.ajax({
                    url:ajaxControllerHandler,
                    method:'POST',
                    data: new FormData(this),
                    contentType:false,
                    processData:false,
                    beforeSend: function() {
                       //$('#update_site_setting_loader').addClass('sk-loading');
                       //$('#submit').attr('disabled',true);
                    },
                    success:function(responseData){
                        var data = JSON.parse(responseData);
                        $('#submit').attr('disabled',false);
                       //console.log(responseData);
                       if(data.check == 'success'){
                          //reseting form data
                          $('#update_site_setting_form')[0].reset();
                          //Clearing textarea, tagsinput & Dropdowns
                          $('.note-editable').html('');
                          //Clearing image preview data
                          $('#image_upload_preview').attr('src', '<?=RESOURCE_URL.'images/preview.jpg'?>');
                          //Disabling loader
                          $('#update_site_setting_loader').removeClass('sk-loading');
                          //show sweetalert success
                          var successText = "Site settings have been successfully updated!";
                          var redirect_url = SITE_URL+"?route=edit_site_setting";

                          swal({
                              title: "Great!",
                              text: successText,
                              type: "success"
                          },function() {
                              window.location = redirect_url;
                          });
                          return true; 
                       }else{
                         //Disabling loader
                          $('#create_student_loader').removeClass('sk-loading');
                          //show sweetalert success
                          if(data.message.length>0){
                            var message = data.message;
                          }else{
                            var message = data.message;
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

      