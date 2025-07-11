<?php
  if(isset($_GET['id'])){
    $course_id = $_GET['id'];
    $courseDetailArr = $pageContent['pageData']['course_data'];
  }else{
    $course_id = 'null';
  }

  //Configuring course file data
  if(strlen($courseDetailArr->course_thumbnail)>0 && file_exists(USER_UPLOAD_DIR.'course/'.$courseDetailArr->course_thumbnail)){
     $course_thumbnail = USER_UPLOAD_URL.'course/'.$courseDetailArr->course_thumbnail;
  }else{
     $course_thumbnail = RESOURCE_URL.'images/preview.jpg';
  }

  if(strlen($courseDetailArr->course_pdf)>0 && file_exists(USER_UPLOAD_DIR.'course/'.$courseDetailArr->course_pdf)){
     $course_pdf = USER_UPLOAD_URL.'course/'.$courseDetailArr->course_pdf;
  }

  /*print"<pre>";
  print_r($courseDetailArr);
  print"</pre>";*/
?>
         
         <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>General Course Details</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>

                        <div class="ibox-content" id="create_course_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <form id="create_course_form" class="needs-validation" method="post" onsubmit="return false;">
                              <input type="hidden" name="action" id="action" value="manageGlobalCourse">
                              <input type="hidden" name="course_id" id="course_id" value="<?=(isset($courseDetailArr)?$courseDetailArr->id:'')?>">

                                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Course Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Course Name"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="course_title" placeholder="Enter Course Name..." value="<?=(isset($courseDetailArr)?$courseDetailArr->course_title:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Course Fees <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Course Fees"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="number" class="form-control" name="course_fees" placeholder="Enter Course Fees..." value="<?=(isset($courseDetailArr)?$courseDetailArr->course_fees:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Course Thumbnail <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a file if you wish to upload one."><i class="fa fa-question-circle"></i></span></label>

                                    <div class="col-sm-9">
                                     <div class="row pl-3"> 
                                      <?php if(isset($_GET['id'])){ ?>  
                                        <div class="col-sm-2 pb-3 pl-0">
                                           <h4>Current image</h4>
                                           <a href="<?=$course_thumbnail?>" data-fancybox="gallery" data-caption="<?=$courseDetailArr->course_title?>">  
                                              <img id="current_image_review" src="<?=$course_thumbnail?>" alt="Course Thumbnail" style="height: 100px;width: 100px;" />
                                            </a>
                                         </div>
                                        <?php } ?> 

                                        <div class="<?php (isset($_GET['id']) ? 'col-sm-10' : 'col-sm-12')?> pb-3 pl-0">
                                         <h4>Preview image</h4>
                                         <a href="<?=RESOURCE_URL.'images/preview.jpg'?>" id="course_preview_image" data-fancybox="gallery" data-caption="<?=$courseDetailArr->course_title?>"> 
                                           <img id="image_upload_preview" src="<?=RESOURCE_URL.'images/preview.jpg'?>" alt="your image" style="height: 100px;width: 100px;" />
                                         </a>  
                                        </div>
                                      </div>  
                                
                                      <div class="btn-group" id="course_thumbnail_local_div">
                                          <label title="Upload a file" for="courseThumbnail" class="btn btn-primary">
                                              <input type="file" accept="image/*" name="course_thumbnail_local" id="courseThumbnail" class="hide" />
                                              Upload a featured image of the course...
                                          </label>    
                                      </div>

                                    </div>
                                    <input type="hidden" name="hidden_course_thumbnail" value="<?=$courseDetailArr->course_thumbnail?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Course Duration <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Course Duration"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-10">
                                      <div class="input-group">
                                        <input type="text" class="form-control" name="course_duration" placeholder="Enter Course Duration..." value="<?=(isset($courseDetailArr)?$courseDetailArr->course_duration:'')?>" required>
                                     </div>   
                                    </div>
                                </div>                               
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Course Detail Pdf <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a pdf file if you wish to upload one.">
                                    <i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-9">
                                       <div class="row pl-3">
                                         <img id="image_upload_preview" src="<?=RESOURCE_URL.'images/pdf_preview.png'?>" alt="your image" style="height: 100px;width: 100px;" />
                                       </div>

                                       <div class="row pl-1 pt-1 pb-2">
                                         <div class="col-sm-12">
                                           <?php if(isset($_GET['id'])){ ?>
                                                <a href="<?=$course_pdf?>" data-fancybox="gallery" data-caption="<?=$courseDetailArr->center_name?>">
                                                   View Current PDF
                                                </a> 
                                           <?php } ?>   
                                           <span class='d-none' data-toggle="modal" data-target="#viewPdfModal" id='preview_course_pdf'>
                                              <a style="color:green;" class='pl-2' data-toggle="tooltip" data-placement="bottom" title="View PDF Preview Before Upload">   
                                                <i class="fa fa-eye" aria-hidden="true"></i> PDF Preview
                                              </a>
                                           </span>   
                                         </div>  
                                       </div>  

                                       <div class="btn-group" id="course_pdf_local_div">
                                          <label title="Upload a file" for="coursePdf" class="btn btn-primary">
                                              <input type="file" accept="application/pdf" name="local_course_pdf" id="coursePdf" class="hide" />
                                              Upload a Pdf containg detail of the course...
                                          </label>    
                                      </div>

                                    </div>
                                    <input type="hidden" name="hidden_course_pdf" value="<?=$courseDetailArr->course_pdf?>">
                                    <div class="col-sm-1 pl-5">
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <label class="col-sm-2 col-form-label text-right">Course Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Status"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-1">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="record_status" <?=($courseDetailArr->record_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" name="record_status" <?=($courseDetailArr->record_status)=='blocked'?'checked':''?> required> <i></i> Blocked </label>
                                    </div>

                                    <label class="col-sm-2 col-form-label text-right">Featured Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Featured Status"><i class="fa fa-question-circle"></i></span></label>
                                    <div class="col-sm-4 pt-1">
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="featured_status" <?=($courseDetailArr->featured_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                       <label class="checkbox-inline i-checks"> <input type="radio" value="inactive" name="featured_status" <?=($courseDetailArr->featured_status)=='inactive'?'checked':''?> required> <i></i> Inactive </label>
                                    </div>
                                </div> 
                                <div class="hr-line-dashed"></div>


                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Course Description <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter course details"><i class="fa fa-question-circle"></i></span></label>
                                        <div class="col-sm-10">
                                            <textarea class="tinymce" name="course_description">
                                               <?=(isset($courseDetailArr)?$courseDetailArr->course_description:'')?>
                                          </textarea>
                                      </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=view_courses" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
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
                       <iframe src="<?=($courseDetailArr->pdf_upload_type=='cdn'? (strpos($courseDetailArr->course_pdf,'drive.google.com')?$course_pdf_cdn:''):'')?>" title="W3Schools Free Online Web Tutorials" id='pdfViewer' width="750" height="460"></iframe>
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
                    if(type == 'course_thumbnail'){
                      $('#image_upload_preview').attr('src', e.target.result);
                      $('#course_preview_image').attr('href', e.target.result);
                    }else{
                      $('#pdfViewer').attr('src',  e.target.result);
                    } 
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

         $(document).ready(function () {
             
             /*Summernote HTML5 Text Editor*/
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

            //verify course image file type before uploading into server
            $("#courseThumbnail").change(function () {
                var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only formats are allowed : "+fileExtension.join(', '));
                }else{
                   readURL(this,'course_thumbnail'); 
                }
            });

            //verify course pdf file type before uploading into server
            $("#coursePdf").change(function (event) {
                var fileExtension = ['pdf'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only formats are allowed : "+fileExtension.join(', '));
                }else{
                   readURL(this);
                   $('#preview_course_pdf').removeClass('d-none');
                }
            });

            $(document).on('submit', '#create_course_form', function(event){
               event.preventDefault();  
               var course_id = $('#course_id').val();

               $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: new FormData(this),
                  contentType:false,
                  processData:false,
                  beforeSend: function() {
                     $('#create_course_loader').addClass('sk-loading');
                     $('#create').attr('disabled',true);
                  },
                  success:function(responseData){
                      var data = JSON.parse(responseData);
                      $('#create').attr('disabled',false);
                     //console.log(responseData);
                     if(data.check == 'success'){
                        //reseting form data
                        $('#create_course_form')[0].reset();
                        //Clearing textarea, tagsinput & Dropdowns
                        $('.note-editable').html('');
                        //Disabling loader
                        $('#create_course_loader').removeClass('sk-loading');
                        //show sweetalert success

                        if(data.last_insert_id>0){
                          var successText = "Course has been successfully created!";
                          var redirect_url = SITE_URL+"?route=edit_course&id="+data.last_insert_id;
                        }else{
                          var successText = "Course has been successfully updated!";
                          var redirect_url = SITE_URL+"?route=edit_course&id="+course_id;
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
                        $('#create_course_loader').removeClass('sk-loading');
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
