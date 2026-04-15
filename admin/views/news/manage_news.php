<?php
  if(isset($_GET['id'])){
    $newsDetailArr = $pageContent['pageData']['news_details'];

    //Configuring franchise file data
    if(strlen($newsDetailArr->optional_pdf)>0 && file_exists(USER_UPLOAD_DIR.'news/'.$newsDetailArr->optional_pdf)){
       $news_pdf = USER_UPLOAD_URL.'news/'.$newsDetailArr->optional_pdf;
    }else{
       $news_pdf = null;
    }
  }

  /*print"<pre>";
  print_r($newsDetailArr);
  print"</pre>";*/
?>        
          <div class="wrapper wrapper-content fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>General News Details</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content" id="manage_news_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <form id="manage_news_form" class="needs-validation" method="post" onsubmit="return false;">
                               
                              <input type="hidden" name="action" id="action" value="manageGlobalNews">
                              <input type="hidden" name="news_id" id="feedback_id" value="<?=(isset($newsDetailArr)?$newsDetailArr->id:'null')?>">
                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Title <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter News Subject"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="title" placeholder="Enter News Subject..." value="<?=(($newsDetailArr && $newsDetailArr->title)?$newsDetailArr->title:'')?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                            <div class="form-group row">
                                 <label class="col-sm-2 col-form-label text-right">News Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Status"><i class="fa fa-question-circle"></i></span></label>
                                <div class="col-sm-4 mt-2">
                                  <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="record_status" <?=($newsDetailArr->record_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                   <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" name="record_status" <?=($newsDetailArr->record_status)=='blocked'?'checked':''?> required> <i></i> Blocked </label>
                                </div>

                                 <label class="col-sm-2 col-form-label text-right">Featured Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Featured Status"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-4 pt-2">
                                   <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="featured_status" <?=($newsDetailArr->featured_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                   <label class="checkbox-inline i-checks"> <input type="radio" value="inactive" name="featured_status" <?=($newsDetailArr->featured_status)=='inactive'?'checked':''?> required> <i></i> Inactive </label>
                                  </div>
                             </div>    
                             <div class="hr-line-dashed"></div>    

                            <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Optional Attachment <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a pdf file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>
                                <div class="col-sm-9">
                                   <div class="row pl-3">
                                     <img id="image_upload_preview" src="<?=RESOURCE_URL.'images/pdf_preview.png'?>" alt="your image" style="height: 100px;width: 100px;" />
                                   </div>

                                   <div class="row pl-1 pt-1 pb-2">
                                     <div class="col-sm-12">
                                       <?php if(isset($_GET['id'])){ ?>
                                          <a href="<?=$news_pdf?>" data-fancybox="gallery" data-caption="<?=$newsDetailArr->title?>">   
                                          View Current PDF
                                          </a> 
                                       <?php } ?>   
                                       <span class='d-none' data-toggle="modal" data-target="#viewPdfModal" id='preview_optional_pdf'>
                                          <a style="color:green;" class='pl-2' data-toggle="tooltip" data-placement="bottom" title="View PDF Preview Before Upload">   
                                            <i class="fa fa-eye" aria-hidden="true"></i> PDF Preview
                                          </a>
                                       </span>   
                                     </div>  
                                   </div>  

                                   <div class="btn-group" id="news_pdf_local_div">
                                      <label title="Upload a file" for="optionalPdf" class="btn btn-primary">
                                          <input type="file" accept="application/pdf" name="local_news_pdf" id="optionalPdf" class="hide" />
                                          Upload any optional Pdf if necessary...
                                      </label>    
                                  </div>

                                </div>
                                <input type="hidden" name="hidden_optional_pdf" value="<?=$newsDetailArr->optional_pdf?>">
                                <div class="col-sm-1 pl-5">
                              </div>
                            </div>
                            
                            <div class="hr-line-dashed"></div>  
                      
                            <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Description <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter news details in detail"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                      <textarea class="tinymce" name="description" style="height:130px;border-radius:10px;" maxlength="700" id="description" required><?=(isset($newsDetailArr)?$newsDetailArr->description:'')?></textarea>
                                      <span id="textbox_charNumCount" style="color:red;"></sapn>
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=view_news" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
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
         $(document).ready(function () {

              $('.i-checks').iCheck({
                 checkboxClass: 'icheckbox_square-green',
                 radioClass: 'iradio_square-green',
             });
             
             /*Summernote HTML5 Text Editor*/
             //$('.summernote').summernote();
            /*------- Ends Here ---------*/

            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader(); 

                    reader.onload = function (e) {
                       $('#pdfViewer').attr('src',  e.target.result);
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            //function media url validation
             function checkMediaURL(url) {
                if(url.match(/\.(jpeg|jpg|gif|png|pdf)$/) != null){
                    return true;
                }
                else if(url.includes('drive.google.com')) {
                    return true;
                }else{
                    toastr.info("Please use a url ends with jpeg,jpg,gif,png or pdf or host the resource at Google Drive and use the sharable link direcly here.", "Suggestion!",{timeOut: 10000,closeButton:true,progressBar:true}); 
                    return false;
                }
             }

             function formatGoogleImg(url){
                var urlId = url.match(/[-\w]{25,}/);
                return "https://drive.google.com/uc?export=view&id="+urlId;
             }

             /*Tinymce HTML5 Text Editor*/
             tinyMCE.init({
                selector: 'textarea.tinymce',
                height: 250,
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

            //Count maximum characters in textarea
            $(document).on('keyup','#description',function(){
              var len = $(this).val().length;
              var char_left = (700 - len);
              if (len >= 700) {
                toastr.warning("Max length reached;content can not exceed more than 700 characters.", "Warning!",{ timeOut: 4000 }); 
                $('#textbox_charNumCount').html("Maximum length reached;content can not exceed more than 700 characters.");
              } else {
                $('#textbox_charNumCount').html("Characters left "+char_left+'!');
              }
            });

            //verify franchise pdf file type before uploading into server
            $("#optionalPdf").change(function (event) {
                var fileExtension = ['pdf'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only formats are allowed : "+fileExtension.join(', '));
                }else{
                   readURL(this);
                   $('#preview_optional_pdf').removeClass('d-none');
                }
            });

            //Handling file upload type checkbox
            $(document).on('ifChanged', '.i-checks.file_upload_type input', function (e) {
                var file_upload_type = $(this).val();
                 
                if(file_upload_type == 'local'){
                   //show & hide requied div  
                   $('#news_pdf_local_div').removeClass('d-none');
                   $('#news_pdf_cdn_div').addClass('d-none');
                   $('.file_selection_warning').addClass('d-none');
                }
                else if(file_upload_type == 'cdn'){
                   //show & hide requied div 
                   $('#news_pdf_cdn_div').removeClass('d-none');
                   $('#news_pdf_local_div').addClass('d-none');
                   $('.file_selection_warning').addClass('d-none');
                }else{
                   //show & hide requied div 
                   $('#news_pdf_cdn_div').addClass('d-none');
                   $('#news_pdf_local_div').addClass('d-none');
                   $('.file_selection_warning').removeClass('d-none');
                }
             });

           //news pdf url on blur handler 
            $(document).on('blur','#news_pdf_cdn',function(){
                 var media_url = $(this).val();

                 //console.log(checkMediaURL(media_url));

                 if(!checkMediaURL(media_url)){
                    toastr.error("Please add a valid url of the pdf", "Error!"); 
                    $('#preview_optional_pdf').addClass('d-none');    
                    return false;  
                 }else{
                    toastr.success("PDF file data is successfully fetched.", "Success!"); 
                 }

                 if(media_url.includes('drive.google.com')) {
                   var formattedUrl = formatGoogleImg(media_url);  
                 }else{
                   var formattedUrl = media_url; 
                 }

                 $('#pdfViewer').attr('src', formattedUrl);
                 $('#preview_optional_pdf').removeClass('d-none');
            }); 

            $(document).on('submit', '#manage_news_form', function(event){
                event.preventDefault();
                
                var feedback_id = $('#feedback_id').val();
                
                $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: new FormData(this),
                  contentType:false,
                  processData:false,
                  beforeSend: function() {
                     //$('#manage_news_loader').addClass('sk-loading');
                     //$('#create').attr('disabled',true);
                  },
                  success:function(responseData){
                      var data = JSON.parse(responseData);
                      $('#create').attr('disabled',false);
                     //console.log(responseData);
                     if(data.check == 'success'){
                        //reseting form data
                        $('#manage_news_form')[0].reset();
                        //Clearing textarea, tagsinput & Dropdowns
                        $('.note-editable').html('');
                       
                        //Disabling loader
                        $('#manage_news_loader').removeClass('sk-loading');
                        //show sweetalert success

                        if(data.last_insert_id>0){
                          var successText = "News has been successfully created!";
                          var redirect_url = SITE_URL+"?route=edit_news&id="+data.last_insert_id;
                        }else{
                          var successText = "News has been successfully updated!";
                          var redirect_url = SITE_URL+"?route=edit_news&id="+feedback_id;
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
                        $('#manage_news_loader').removeClass('sk-loading');
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