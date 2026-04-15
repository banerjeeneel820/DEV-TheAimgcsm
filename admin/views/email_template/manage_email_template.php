<?php
 $templateDetailArr = $pageContent['pageData']['email_template_details'];

  /*print"<pre>";
  print_r($templateDetailArr);
  print"</pre>";*/
?>       
          <div class="wrapper wrapper-content fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>General Template Details</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content" id="manage_template_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <form id="manage_email_template_form" class="needs-validation" method="post" onsubmit="return false;">
                               
                              <input type="hidden" name="action" id="action" value="manageEmailTemplate">
                              <input type="hidden" name="template_id" id="template_id" value="<?=(isset($templateDetailArr)?$templateDetailArr->id:'null')?>">

                               
                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Subject <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Email Subject"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="subject" placeholder="Enter Email Subject..." value="<?=(($templateDetailArr && $templateDetailArr->subject)?$templateDetailArr->subject:'')?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Code <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Email Code"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" id="code" name="code" placeholder="Enter Email Code..." value="<?=(($templateDetailArr && $templateDetailArr->code)?$templateDetailArr->code:'')?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Email For <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select the type email"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-4 mt-2">
                                     <label class="checkbox-inline i-checks"> <input type="radio" value="admin" name="email_for" <?=(($templateDetailArr && $templateDetailArr->email_for)?($templateDetailArr->email_for=='admin'?'checked':''):'')?> required/> <i></i>Admin </label>
                                     <label class="checkbox-inline i-checks"> <input type="radio" value="user" name="email_for" <?=(($templateDetailArr && $templateDetailArr->email_for)?($templateDetailArr->email_for=='user'?'checked':''):'')?> required> <i></i> User </label>
                                  </div>   

                                  <label class="col-sm-2 col-form-label text-right">Record Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Status"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-2">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="record_status" <?=($templateDetailArr->record_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" name="record_status" <?=($templateDetailArr->record_status)=='blocked'?'checked':''?> required> <i></i> Blocked </label>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>
                              
                                <!--<div class="form-group row"><label class="col-sm-2 col-form-label text-right">Template Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select the status of this user"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-9 mt-2">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="template_status" <?=(($templateDetailArr && $templateDetailArr->template_status)?($templateDetailArr->template_status=='active'?'checked':''):'')?> required/> <i></i>Active </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="inactive" name="template_status" <?=(($templateDetailArr && $templateDetailArr->template_status)?($templateDetailArr->template_status=='inactive'?'checked':''):'')?> required> <i></i> In-active </label>
                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>-->
                      
                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Email Variable  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Email Template Variable Seperate by comma"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <textarea class="form-control" name="variables" placeholder="Enter Email Template Variable..."><?=(($templateDetailArr && $templateDetailArr->variables)?$templateDetailArr->variables:'')?></textarea>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">From Email  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Email From Name"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="email" class="form-control" name="from_email" id="from_email" placeholder="Enter Email From..." value="<?=(($templateDetailArr && $templateDetailArr->from_email)?$templateDetailArr->from_email:'')?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Form Name  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Email From Name"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="from_name" placeholder="Enter Email From Name..." value="<?=(($templateDetailArr && $templateDetailArr->from_name)?$templateDetailArr->from_name:'')?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Email CC  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Email CC"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="email" class="form-control" name="cc_email" id="cc_email" placeholder="Enter Email CC..." value="<?=(($templateDetailArr && $templateDetailArr->cc_email)?$templateDetailArr->cc_email:'')?>">
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>
                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Template Description <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter a event details"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                        <textarea class="tinymce" name="template" required>
                                           <?=(isset($templateDetailArr)?$templateDetailArr->template:'')?>
                                      </textarea>
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=view_email_templates" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
                                        <button class="btn btn-success btn-sm" id="create" type="submit" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="Save"><i class="fa fa-save"></i> Save</button>
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
         $(document).ready(function () {

              $('.i-checks').iCheck({
                 checkboxClass: 'icheckbox_square-green',
                 radioClass: 'iradio_square-green',
             });
             
             /*Summernote HTML5 Text Editor*/
             tinyMCE.init({
                selector: 'textarea.tinymce',
                height: 300,
                plugins: "link image media code",
                toolbar: 'undo redo | styleselect | forecolor | bold italic | alignleft aligncenter alignright alignjustify | '+
                         'outdent indent | media | link image | code',
                setup : function(ed){
                     ed.on('NodeChange', function(e){
                         tinyMCE.triggerSave();
                         $("#" + ed.id).valid();
                         //console.log('the event object ' + e);
                         //console.log('the editor object ' + ed);
                         //console.log('the content ' + ed.getContent());
                     });
                }
            });
            /*------- Ends Here ---------*/

            //function to check unique email id for user  
            function check_form_email(email){
             
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

            $(document).on('submit', '#manage_email_template_form', function(event){
                event.preventDefault();
                
                var template_id = $('#template_id').val();
                var from_email = $('#from_email').val();
                var cc_email = $('#cc_email').val();
                
               //Checking if the emails are valid
               check_form_email(from_email);
               check_form_email(cc_email);

               $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: new FormData(this),
                  contentType:false,
                  processData:false,
                  beforeSend: function() {
                     //$('#manage_template_loader').addClass('sk-loading');
                     //$('#create').attr('disabled',true);
                  },
                  success:function(responseData){
                      var data = JSON.parse(responseData);
                      $('#create').attr('disabled',false);
                     //console.log(responseData);
                     if(data.check == 'success'){
                        //reseting form data
                        $('#manage_email_template_form')[0].reset();
                        //Clearing textarea, tagsinput & Dropdowns
                        $('.note-editable').html('');
                       
                        //Disabling loader
                        $('#manage_template_loader').removeClass('sk-loading');
                        //show sweetalert success

                        if(data.last_insert_id>0){
                          var successText = "Template has been successfully created!";
                          var redirect_url = SITE_URL+"?route=edit_email_template&id="+data.last_insert_id;
                        }else{
                          var successText = "Template has been successfully updated!";
                          var redirect_url = SITE_URL+"?route=edit_email_template&id="+template_id;
                        } 

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
                        $('#manage_template_loader').removeClass('sk-loading');
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