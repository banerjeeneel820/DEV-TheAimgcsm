<?php
  
  $franDetailArr = $pageContent['pageData']['frnachise_data'];

  /*print"<pre>";
  print_r($franDetailArr);
  print"</pre>";*/

?>
         
        <div class="wrapper wrapper-content animated fadeInRight"> 
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>General Franchise Details</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content" id="create_franchise_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <form id="create_franchise_form" method="post" onsubmit="return false;">
                              <input type="hidden" name="action" id="action" value="manageFranchiseProfile">
                              
                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Franchise Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="center_name" placeholder="Enter Franchise Name..." value="<?=(isset($franDetailArr)?$franDetailArr->center_name:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>
                               
                               <div class="form-group row text-right">
                                <label class="col-sm-2 col-form-label">Owner Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Owner Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="owner_name" placeholder="Enter Franchise Owner Name..." value="<?=(isset($franDetailArr)?$franDetailArr->owner_name:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right">
                                    <label class="col-sm-2 col-form-label">Franchise Contact No <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Contact No"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="number" class="form-control" name="fran_phone" placeholder="Enter Franchise Contact No..." value="<?=(isset($franDetailArr)?$franDetailArr->fran_phone:'')?>" required>
                                     </div>   
                                    </div>

                                    <label class="col-sm-2 col-form-label">Franchise ID <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Franchise ID"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4">
                                      <div class="input-group">
                                        <input type="text" class="form-control" value="<?=(isset($franDetailArr)?$franDetailArr->fran_id:'')?>" readonly>
                                      </div>   
                                    </div>

                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Franchise Email <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Email"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="email" class="form-control" id="fran_email" name="fran_email" placeholder="Enter Franchise Email..." value="<?=(isset($franDetailArr)?$franDetailArr->fran_email:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Franchise Image <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                    <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                        <div class="col-sm-2 pb-3 pl-0">
                                           <h4>Current image</h4>
                                           <a href="<?=USER_UPLOAD_URL.'franchise/'.$franDetailArr->fran_image?>" data-fancybox="gallery" data-caption="<?=$franDetailArr->center_name?>">  
                                              <img id="current_image_review" src="<?=USER_UPLOAD_URL.'franchise/'.$franDetailArr->fran_image?>" alt="franchise image" style="height: 100px;width: 100px;" />
                                            </a>  
                                         </div>

                                        <div class="col-sm-10 pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="fran_preview_image" data-fancybox="gallery" data-caption="<?=$franDetailArr->center_name?>"> 
                                           <img id="image_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                      
                                      <div class="btn-group">
                                          <label title="Upload a file" for="franchiseImage" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="fran_image" id="franchiseImage" class="hide" />
                                              Upload a featured image of the franchise...
                                          </label>    
                                      </div>
                                    </div>
                                    <input type="hidden" name="hidden_fran_image" value="<?=$franDetailArr->fran_image?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Franchise Address <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Address"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="fran_address" placeholder="Enter Franchise Address..." value="<?=(isset($franDetailArr)?$franDetailArr->fran_address:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Franchise Password <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Franchise Password"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="password" class="form-control" id="fran_pass" name="fran_pass" placeholder="Enter Franchise Password..." autocomplete="off">

                                        <input type="hidden" name="fran_hidden_password" value="<?=$franDetailArr->fran_pass?>"> 
                                        <input type="hidden" name="fran_hidden_og_password" value="<?=$franDetailArr->fran_og_pass?>">
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Repeat Password <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Repeat Franchise Password"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_fran_pass" name="confirm_fran_pass" autocomplete="off" placeholder="Repeat Franchise Password...">
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                            
                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Franchise Detail Pdf <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a pdf file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-9">
                                       <div class="row pl-3">
                                         <img id="image_upload_preview" src="<?=RESOURCE_URL.'images/pdf_preview.png'?>" alt="your image" style="height: 100px;width: 100px;" />
                                       </div>

                                       <div class="row pl-1 pt-1 pb-2">
                                         <div class="col-sm-12">
                                            <a href="<?=USER_UPLOAD_URL.'franchise/'.$franDetailArr->fran_pdf_name?>" data-fancybox="gallery" data-caption="<?=$franDetailArr->center_name?>">   
                                              View Current PDF
                                            </a> 
                                           <span class='d-none' data-toggle="modal" data-target="#viewPdfModal" id='preview_fran_pdf'>
                                              <a style="color:green;" class='pl-2' data-toggle="tooltip" data-placement="bottom" title="View PDF Preview Before Upload">   
                                                <i class="fa fa-eye" aria-hidden="true"></i> PDF Preview
                                              </a>
                                           </span>   
                                         </div>  
                                       </div>  

                                       <div class="btn-group">
                                          <label title="Upload a file" for="franchisePdf" class="btn btn-primary">
                                              <input type="file" accept="application/pdf" name="fran_pdf_name" id="franchisePdf" class="hide" />
                                              Upload a Pdf containg detail of the franchise...
                                          </label>    
                                      </div>
                                    </div>
                                    <input type="hidden" name="hidden_fran_pdf" value="<?=$franDetailArr->fran_pdf_name?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="forum-title pr-38 border-bottom mb-4">
                                   <h5>Franchise Description</h5> 
                                </div>  

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Franchise Description <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter a event details"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-10">
                                            <textarea class="summernote" name="fran_description">
                                               <?=(isset($franDetailArr)?$franDetailArr->fran_description:'')?>
                                          </textarea>
                                      </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=view_franchises" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
                                        <button class="btn btn-success btn-sm" id="create" type="submit" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="Save"><i class="fa fa-save"></i> Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Popup -->
        <div id="viewPdfModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>PDF Preview</h3>
                        <button type="button" class="close" data-dismiss="modal">
                            &times;</button>
                        <h4 class="modal-title">
                        </h4>
                    </div>
                    <div class="modal-body">
                       <iframe src="" title="W3Schools Free Online Web Tutorials" id='pdfViewer' width="750" height="460"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-dismiss="modal">
                            Close</button>
                    </div>
                </div>
            </div>
        </div>
    

        <!-- Custom JS -->
       <script>
          function readURL(input,type) {
            if (input.files && input.files[0]) {
                var reader = new FileReader(); 

                reader.onload = function (e) {
                    if(type == 'fran_image'){
                      $('#image_upload_preview').attr('src', e.target.result);
                      $('#fran_preview_image').attr('href', e.target.result);
                    }else{
                      $('#pdfViewer').attr('src',  e.target.result);
                    } 
                }
                reader.readAsDataURL(input.files[0]);
            }
         } 

         $(document).ready(function () {

              $('.i-checks').iCheck({
                 checkboxClass: 'icheckbox_square-green',
                 radioClass: 'iradio_square-green',
             });
             
             /*Summernote HTML5 Text Editor*/
             $('.summernote').summernote();
            /*------- Ends Here ---------*/

            //function to check unique email id for user  
            function check_fran_email(fran_email){
             
              if(fran_email.length >0){
                var regularExp = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
                var chkEmail = regularExp.test(fran_email);

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

            //verify franchise image file type before uploading into server
            $("#franchiseImage").change(function () {
                var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only formats are allowed : "+fileExtension.join(', '));
                }else{
                   readURL(this,'fran_image'); 
                }
            });

            //verify franchise pdf file type before uploading into server
            $("#franchisePdf").change(function (event) {
                var fileExtension = ['pdf'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only formats are allowed : "+fileExtension.join(', '));
                }else{
                   readURL(this,'fran_pdf');
                   $('#preview_fran_pdf').removeClass('d-none');
                }
            });

            $(document).on('submit', '#create_franchise_form', function(event){
                event.preventDefault();
                
                var fran_row_id = $('#fran_row_id').val();
                var fran_email = $('#fran_email').val();
                var password = $('#fran_pass').val();
                var confirm_password = $('#confirm_fran_pass').val();

                if( (password.length>0) && (password != confirm_password) ){
                swal({
                   title: "Oops!",
                   text: "Password and Confirm Password does not match!",
                   type: "error"
                });
                return false;
               }

               //Checking if the email is valid
               check_fran_email(fran_email);

               $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: new FormData(this),
                  contentType:false,
                  processData:false,
                  beforeSend: function() {
                     //$('#create_franchise_loader').addClass('sk-loading');
                     //$('#create').attr('disabled',true);
                  },
                  success:function(responseData){
                      var data = JSON.parse(responseData);
                      $('#create').attr('disabled',false);
                     //console.log(responseData);
                     if(data.check == 'success'){
                        //reseting form data
                        $('#create_franchise_form')[0].reset();
                        //Clearing textarea, tagsinput & Dropdowns
                        $('.note-editable').html('');
                        //Clearing image preview data
                        $('#image_upload_preview').attr('src', '<?=RESOURCE_URL.'images/preview.jpg'?>');
                        //Disabling loader
                        $('#create_franchise_loader').removeClass('sk-loading');
                        //show sweetalert success

                        var successText = "Profile has been successfully updated!";
                        var redirect_url = SITE_URL+"?route=edit_profile";

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
                        $('#create_franchise_loader').removeClass('sk-loading');
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

