<?php
  if(isset($_GET['id'])){
    $examDetailArr = $pageContent['pageData']['exam_details'];

    //Configuring franchise file data
    if(strlen($examDetailArr->optional_pdf)>0 && file_exists(USER_UPLOAD_DIR.'exam/'.$examDetailArr->optional_pdf)){
       $exam_pdf = USER_UPLOAD_URL.'exam/'.$examDetailArr->optional_pdf;
    }else{
       $exam_pdf = null;
    }
  }

   //Franchise data
  $franchiseArr = $pageContent['pageData']['franchise_data'];
  //Course data
  $courseArr = $pageContent['pageData']['course_data']; 

  /*print"<pre>";
  print_r($examDetailArr);
  print"</pre>";*/
?>        
          <div class="wrapper wrapper-content fadeInRight">
            <div class="row">
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-title">
                            <h5>General Exam Details</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content" id="manage_exam_loader">
                           <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                            <form id="manage_exam_form" class="needs-validation" method="post" onsubmit="return false;">
                               
                              <input type="hidden" name="action" id="action" value="manageGlobalExam">
                              <input type="hidden" name="exam_id" id="exam_id" value="<?=(isset($examDetailArr)?$examDetailArr->id:'null')?>">
                              <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Exam Name"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="name" placeholder="Enter Exam Name..." value="<?=(($examDetailArr && $examDetailArr->name)?$examDetailArr->name:'')?>" required>
                                   </div>   
                                  </div>
                              </div>                               
                              <div class="hr-line-dashed"></div>

                               <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Select Franchise <span class=" cursor-pointer" data-toggle="tooltip" data-placement="left" title="Select a Franchise for student from Dropdown"><i class="fa fa-question-circle"></i></span></label>
                                      <div class="col-sm-10 pr-0" id="state_loader_div">
                                        <div class="input-group">
                                            <select class="franchise" name="franchise_id" id="franchise_id" data-placeholder="Choose a Franchise first..." tabindex="2" <?=($_SESSION['user_type'] == 'franchise'?'disabled':'')?> required>
                                              <option></option>
                                               <?php foreach($franchiseArr as $franchise){ 
                                              ?>
                                                <option value="<?=$franchise->id?>" <?=($_SESSION['user_type'] == 'franchise'?($_SESSION['user_id'] == $franchise->id?'selected':''):($franchise->id == $examDetailArr->franchise_id ?'selected':''))?>><?=$franchise->center_name?></option>
                                              <?php } ?>
                                           </select>
                                       </div>

                                    </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Select Course <span class=" cursor-pointer" data-toggle="tooltip" data-placement="left" title="Select a course for student from Dropdown"><i class="fa fa-question-circle"></i></span></label>
                                <div class="col-sm-4 pr-0" id="state_loader_div">
                                    <div class="input-group">
                                        <select class="course" name="course_id" id="course_id" data-placeholder="Choose a Course first..." tabindex="2"required>
                                          <option></option>
                                           <?php foreach($courseArr as $course){ 
                                          ?>
                                            <option value="<?=$course->id?>" <?=($course->id == $examDetailArr->course_id ?'selected':'')?>><?=$course->course_title?></option>
                                          <?php } ?>
                                       </select>
                                       <input type="hidden" name="course_name" id="course_name" value="">
                                   </div>
                                </div>

                                <label class="col-sm-2 col-form-label text-right">Exam Date <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Select Exam Date"><i class="fa fa-question-circle"></i></span></label>

                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                        <input type="text" class="form-control" name="exam_date" id="exam_date" value="<?=(isset($examDetailArr)?date('d-m-Y',strtotime($examDetailArr->exam_date)):'')?>" placeholder="Select a date of exam" autocomplete="off">
                                    </div>
                                </div>
                            </div>    
                            <div class="hr-line-dashed"></div>

                             <div class="form-group row">
                                <label class="col-sm-2 col-form-label text-right">Total Marks <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Exam Total Marks"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-4">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="total_marks" placeholder="Enter Total Marks..." value="<?=(($examDetailArr && $examDetailArr->total_marks)?$examDetailArr->total_marks:'')?>" required>
                                   </div>   
                                  </div>

                                   <label class="col-sm-2 col-form-label text-right">Total Hours Limit <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Exam Total Hours"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-4">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="hours" placeholder="Enter Hours Limit..." value="<?=(($examDetailArr && $examDetailArr->hours)?$examDetailArr->hours:'')?>" required>
                                   </div>   
                                  </div>
                            </div>                               
                            <div class="hr-line-dashed"></div>   

                            <div class="form-group row">

                                <label class="col-sm-2 col-form-label text-right">Total Minutes Limit <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Exam Minutes"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-4">
                                    <div class="input-group">
                                      <input type="text" class="form-control" name="minutes" placeholder="Enter Minutes Limit..." value="<?=(($examDetailArr && $examDetailArr->minutes)?$examDetailArr->minutes:'')?>">
                                   </div>   
                                </div>

                                <label class="col-sm-2 col-form-label text-right">Exam Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose a Status"><i class="fa fa-question-circle"></i></span></label>
                                <div class="col-sm-4 mt-2">
                                  <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="record_status" <?=($examDetailArr->record_status)=='active'?'checked':''?> required/> <i></i>Active </label>
                                   <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" name="record_status" <?=($examDetailArr->record_status)=='blocked'?'checked':''?> required> <i></i> Blocked </label>
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
                                          <a href="<?=$exam_pdf?>" data-fancybox="gallery" data-caption="<?=$examDetailArr->title?>">   
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

                                   <div class="btn-group" id="exam_pdf_local_div">
                                      <label title="Upload a file" for="optionalPdf" class="btn btn-primary">
                                          <input type="file" accept="application/pdf" name="local_exam_pdf" id="optionalPdf" class="hide" />
                                          Upload any optional Pdf if necessary...
                                      </label>    
                                  </div>

                                </div>
                                <input type="hidden" name="hidden_optional_pdf" value="<?=$examDetailArr->optional_pdf?>">
                                <div class="col-sm-1 pl-5">
                              </div>
                            </div>
                            
                            <div class="hr-line-dashed"></div>  
                      
                            <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Description <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter exam details in detail"><i class="fa fa-question-circle"></i></span></label>
                                  <div class="col-sm-10">
                                      <textarea class="tinymce" name="instructions" style="height:130px;border-radius:10px;" maxlength="700" id="instructions" required><?=(isset($examDetailArr)?$examDetailArr->instructions:'')?></textarea>
                                      <span id="textbox_charNumCount" style="color:red;"></sapn>
                                  </div>
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="form-group row">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <a href="<?=SITE_URL?>?route=view_exam" data-toggle="tooltip" title="" class="btn btn-default" data-original-title="Cancel"><i class="fa fa-reply"></i></a>
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

            //Multiple select course
            $('.course').select2({width: "98.5%",allowClear:true});
            $('.franchise').select2({width: "98.5%",allowClear:true});

            $('.course,.franchise').on('change',function(){
                $(this).valid();
            }); 

            var today = new Date();

            /*---Input date & time control--*/
            $('.input-group.date').datepicker({
               format: "dd/mm/yyyy",
               todayBtn: "linked",
               keyboardNavigation: true,
               todayHighlight: true,
               startDate: today,
               forceParse: false,
               calendarWeeks: true,
               autoclose: true
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
                   $('#exam_pdf_local_div').removeClass('d-none');
                   $('#exam_pdf_cdn_div').addClass('d-none');
                   $('.file_selection_warning').addClass('d-none');
                }
                else if(file_upload_type == 'cdn'){
                   //show & hide requied div 
                   $('#exam_pdf_cdn_div').removeClass('d-none');
                   $('#exam_pdf_local_div').addClass('d-none');
                   $('.file_selection_warning').addClass('d-none');
                }else{
                   //show & hide requied div 
                   $('#exam_pdf_cdn_div').addClass('d-none');
                   $('#exam_pdf_local_div').addClass('d-none');
                   $('.file_selection_warning').removeClass('d-none');
                }
             });

           //exam pdf url on blur handler 
            $(document).on('blur','#exam_pdf_cdn',function(){
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

            $(document).on('submit', '#manage_exam_form', function(event){
                event.preventDefault();
                
                var exam_id = $('#exam_id').val();
                
                $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: new FormData(this),
                  contentType:false,
                  processData:false,
                  beforeSend: function() {
                     //$('#manage_exam_loader').addClass('sk-loading');
                     //$('#create').attr('disabled',true);
                  },
                  success:function(responseData){
                      var data = JSON.parse(responseData);
                      $('#create').attr('disabled',false);
                     //console.log(responseData);
                     if(data.check == 'success'){
                        //reseting form data
                        $('#manage_exam_form')[0].reset();
                        //Clearing textarea, tagsinput & Dropdowns
                        $('.note-editable').html('');
                       
                        //Disabling loader
                        $('#manage_exam_loader').removeClass('sk-loading');
                        //show sweetalert success

                        if(data.last_insert_id>0){
                          var successText = "Exam has been successfully created!";
                          var redirect_url = SITE_URL+"?route=edit_exam&id="+data.last_insert_id;
                        }else{
                          var successText = "Exam has been successfully updated!";
                          var redirect_url = SITE_URL+"?route=edit_exam&id="+exam_id;
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
                        $('#manage_exam_loader').removeClass('sk-loading');
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