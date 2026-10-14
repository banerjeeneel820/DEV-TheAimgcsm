<?php
  if(isset($_GET['exm_id'])){
    $exam_id = $_GET['exm_id'];
    $examDetails = $pageContent['pageData']['exam_details'];
    $questions = $pageContent['pageData']['questions'];

    $total_time = '';

    if(!empty($examDetails->hours)){
      $total_time .= $examDetails->hours.'h ';
    }

    if(!empty($examDetails->minutes)){
      $total_time .= $examDetails->minutes.'m';
    }
  }else{
    $exam_id = 'null';
    $examDetails = array();
    $questions = array(); 
  }

  if(!empty($questions)){
    $questionIndex = count($questions);
  }else{
    $questionIndex = 0;
  }

  /*print"<pre>";
  print_r($examDetails);
  print"</pre>";*/
?>        
          <div class="wrapper wrapper-content fadeInRight">
            <div class="row">
              <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Exam Details </h5>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-primary btn-xs" onclick="fetchAllQuestions()"><i class="fa fa-refresh"> </i> Referesh Questions</button>

                            <button type="button" class="btn btn-xs btn-danger handle_import_div" data-htype="show"><i class="fa fa-file-excel-o"> </i> Import Data in CSV Format</button>

                            <div class="btn-group">
                                <button type="button" class="btn btn-xs btn-white <?=( $_COOKIE['question_div_collapse'] == "true" ? "active" : "" )?>" id="collapse_question_divs">Collapse Question's Section</button>
                                <button type="button" class="btn btn-xs btn-white <?=( $_COOKIE['question_div_collapse'] == "false" ? "active" : "" )?>" id="open_question_divs">Open Question's Section</button>
                            </div>
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
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th class="text-center">Exam Name</th>
                                <th class="text-center">Franchise</th>
                                <th class="text-center">Course</th>
                                <th class="text-center">Total Questions</th>
                                <th class="text-center">Total Marks/Times</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                            </thead>
                            <tbody class="text-center">
                            <tr>
                               <td class="project-title vertical-align-middle">
                                    <a href="<?=SITE_URL.'?route=edit_exam&id='.$examDetails->id?>" data-toggle="tooltip" data-placement="bottom" title="Exam Title: <?=$examDetails->name?>"><?=ucfirst($examDetails->name)?></a>
                                    <br/>
                                    <small>Created <?=date('jS F, Y',strtotime($examDetails->created_at))?></small>
                                </td>

                                <td class="project-title vertical-align-middle">
                                    <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Franchise taking exam: <?=$examDetails->center_name?>"><?=$examDetails->center_name?></span>
                                </td>

                                <td class="project-title vertical-align-middle">
                                    <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Exam subject/course: <?=$examDetails->course_title?>"><?=$examDetails->course_title?></span>
                                </td>

                                <td class="project-title vertical-align-middle">
                                    <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Total Questions: <?=$examDetails->question_count?>"><?=$examDetails->question_count?></span>
                                </td>

                                <td class="project-title vertical-align-middle">
                                    <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Total Exam Time: <?=$total_time?>"><?=$examDetails->total_marks?> / <?=$total_time?></span>
                                </td>

                                <td class="project-status vertical-align-middle">
                                 <span class="label label-<?=($examDetails->record_status == 'active'?'primary':'danger')?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Exam Status: <?=ucfirst($examDetails->record_status)?>"><?=ucfirst($examDetails->record_status)?></span> 
                                </td>
                                <td class="project-status vertical-align-middle">
                                 <button class="btn btn-danger btn-sm" id="delete_all_questions" data-toggle="tooltip" data-placement="bottom" title="Delete all Question">Delete</button> 
                                </td>
                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row d-none" id="import_data_div">
                    <div class="col-lg-12">
                      <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Import Questions in CSV Format</h5>
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
                                <input type="hidden" name="import_table" value="exam_questions"> 

                                <div class="btn-group">
                                  <label title="Upload a file" for="importDataCSV" class="btn btn-primary">
                                      <input type="file" accept="application/vnd.openxmlformats-officedoc.sheet" id="importDataCSV" name="import_data_file" class="hide" />
                                      --Upload questions by uploading a csv or xls file with proper table structure--
                                  </label>    
                                </div>

                                <button type="submit" class="btn btn-lg btn-success ml-2 mb-2" name="import_data_submit" id="import_data_submit" class="button" value="Import Data" disabled><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Import Data</button> 

                                <a href="<?=RESOURCE_URL.'importSampleCSV/sample-student.xlsx'?>" class="btn btn-primary btn-lg ml-2 mb-2" download>
                                    <i class="fa fa-download"> </i> Sample CSV
                                    <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="top" title="Download sample CSV format and strickly follow it to import bulk data"><i class="fa fa-question-circle"></i></span>
                                 </a> 
                             </form>
                           </div>  
                         </div>
                      </div>
                   </div>
                </div>
                
                <div class="alert alert-warning <?=(!empty($questions) ? 'd-none' : '')?>" role="alert" id="add_first_question">
                  <div class="d-flex justify-content-between">  
                      <span><strong>Warning!</strong> No question is added yet for this exam, please add questions by clicking on the add your first question button besides.</span> 
                      <button class="btn btn-success btn-sm"><i class="fa fa-plus-circle"> Add Your First Question</i></button>
                   </div>     
                </div>
              </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                  <form id="manage_exam_questions_form" class="needs-validation" method="post" onsubmit="return false;">

                     <div class="overlayer" style="display: none;">
                       <div class="spinner"></div>
                    </div>
                               
                     <input type="hidden" name="action" id="action" value="manageExamQuestions">
                     <input type="hidden" name="exam_id" id="exam_id" value="<?=$exam_id?>">
                               
                      <div id="main_question_container">
                          <?php foreach ($questions as $index => $question) { ?>
                                <div id='questions-<?=$question->id?>'> 
                                   <div class="ibox question_div <?=( $_COOKIE['question_div_collapse'] == "true" ? "collapsed" : "open" )?>" id="question_div_<?=($index+1)?>">
                                    <div class="ibox-title">
                                        <div id="question_header_<?=($index+1)?>" class="question-header">
                                           <h5>Question No <?=($index+1)?></h5>
                                        </div>
                                        <div class="ibox-tools">
                                            <a href="javascript:void(0);" data-divid="<?=($index+1)?>" class="clone-question dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Clone this question"><span class="badge badge-primary p-1"><i class="fa fa-clone"></i> Clone This Question</span></a>

                                            <a href="javascript:void(0);" data-divid="<?=($index+1)?>" class="remove-question dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Remove this question"><span class="badge badge-danger p-1"><i class="fa fa-minus-circle"></i> Remove Question</span></a>

                                            <a class="collapse-question-div" data-cstatus="<?=( $_COOKIE['question_div_collapse'] == "true" ? "collapsed" : "open" )?>">
                                                <span class="badge badge-success p-1"><i class="fa fa-chevron-up"></i> Toogle Question</span> 
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
                                        
                                        <div class="form-group row"><label class="col-sm-2 col-form-label text-right">Question <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Question"><i class="fa fa-question-circle"></i></span></label>
                                              <div class="col-sm-10">
                                                <div class="input-group">
                                                  <textarea class="form-control ques" name="questions[<?=$index?>][ques]" rows="2" placeholder="Enter Question..." required><?=$question->ques?></textarea>
                                               </div>   
                                              </div>
                                          </div>                               
                                         <div class="hr-line-dashed"></div>

                                         <div class="form-group row">
                                            <label class="col-sm-2 col-form-label text-right">Option One <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option One"><i class="fa fa-question-circle"></i></span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                  <input type="text" class="form-control opt1" name="questions[<?=$index?>][opt1]" placeholder="Enter Option One..." value="<?=$question->opt1?>" required>
                                               </div>   
                                           </div>
                                         </div>                               
                                         <div class="hr-line-dashed"></div>  

                                         <div class="form-group row">
                                            <label class="col-sm-2 col-form-label text-right">Option Two <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option Two"><i class="fa fa-question-circle"></i></span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                  <input type="text" class="form-control opt2" name="questions[<?=$index?>][opt2]" placeholder="Enter Option Two..." value="<?=$question->opt2?>" required>
                                               </div>   
                                           </div>
                                         </div>                               
                                         <div class="hr-line-dashed"></div>  

                                         <div class="form-group row">
                                            <label class="col-sm-2 col-form-label text-right">Option Three <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option One"><i class="fa fa-question-circle"></i></span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                  <input type="text" class="form-control opt3" name="questions[<?=$index?>][opt3]" placeholder="Enter Option Three..." value="<?=$question->opt3?>" required>
                                               </div>   
                                           </div>
                                         </div>                               
                                         <div class="hr-line-dashed"></div>  

                                         <div class="form-group row">
                                            <label class="col-sm-2 col-form-label text-right">Option Four <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option One"><i class="fa fa-question-circle"></i></span></label>
                                            <div class="col-sm-10">
                                                <div class="input-group">
                                                  <input type="text" class="form-control opt4" name="questions[<?=$index?>][opt4]" placeholder="Enter Option Four..." value="<?=$question->opt4?>" required>
                                               </div>   
                                           </div>
                                         </div>                               
                                         <div class="hr-line-dashed"></div> 

                                         <div class="form-group row">
                                            <label class="col-sm-2 col-form-label text-right">Correct Answer <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Correct Answer"><i class="fa fa-question-circle"></i></span></label>
                                            <div class="col-sm-4">
                                                <div class="input-group">
                                                  <input type="number" class="form-control cor_ans" name="questions[<?=$index?>][cor_ans]" min="1" max="4" placeholder="Enter Correct Answer..." value="<?=$question->cor_ans?>" required>
                                               </div>   
                                           </div>

                                           <label class="col-sm-2 col-form-label text-right">Question Status <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Choose Question Status"><i class="fa fa-question-circle"></i></span></label>
                                           <div class="col-sm-4">
                                              <div class="input-group">
                                                <select class="form-control record_status" name="questions[<?=$index?>][record_status]">
                                                   <option selected disabled>Select question status</option>
                                                   <option value="active" <?=($question->record_status == 'active'?'selected':'')?>>Active</option>
                                                   <option value="blocked" <?=($question->record_status == 'blocked'?'selected':'')?>>Blocked</option>
                                                </select>
                                              </div>     
                                            </div> 
                                         </div>                               
                                   </div>
                                </div>   
                            </div>
                        <?php } ?>        
                             
                      </div>

                      <div class="d-flex justify-content-between mb-3">
                          <div class="text-left <?=(!empty($questions) ? '' : 'd-none')?>" id="form_submit_actions"> 
                              <a href="<?=SITE_URL?>?route=view_exams" data-toggle="tooltip" data-original-title="Cancel">
                                <button class="btn btn-warning btn-sm"><i class="fa fa-reply"></i></button></a>
                              
                              <button class="btn btn-primary btn-sm" id="create" type="submit" data-toggle="tooltip" title="" class="btn btn-success" data-original-title="Save"><i class="fa fa-save"></i> Save Questions</button>
                          </div>
                          
                          <div class="text-right <?=(!empty($questions) ? '' : 'd-none')?>" id="add_question_div">    
                            <button class="btn btn-success" id="add_more">
                                <i class="fa fa-plus-circle"></i> Add a New Question
                            </button>
                          </div>   
                      </div>
                     
                  </form>    
               </div>
            </div>
        </div>  

      <!-- Custom JS -->
       <script>
         var index = parseInt('<?=$questionIndex?>');
         var exam_id = $("#exam_id").val();

         function fetchAllQuestions(){
            var formData = {exam_id:exam_id, action:"fetchAllQuestions"}

            $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: formData,
                  beforeSend: function() {
                     $('.content_div_loader').addClass('sk-loading');
                     $('.overlayer').fadeIn();
                     $(this).attr('disabled',true);
                  },
                  success:function(responseData){
                      var data = JSON.parse(responseData);
                      $(this).attr('disabled',false);
                     console.log(responseData);return false;
                     if(data.check == 'success'){

                        //Disabling loader
                        $('.content_div_loader').removeClass('sk-loading');
                        $('.overlayer').fadeOut();

                        index = 0;
                        var questions = data.questions;

                        var collapsed = question_div_collapse == 'true' ? 'collapsed' : 'open';

                        if(questions.length > 0){

                          for(var i=0; i<questions.length; i++){

                            var divIndex = index+1;
                            var question = questions[i];

                            var rstatus_active_selected = question['record_status'] == "active" ? "selected":""; 
                            var rstatus_inactive_selected = question['record_status'] == "blocked" ? "selected":""; 

                            let questionContainer = "";
                            questionContainer += '<div class="ibox question_div '+collapsed+'" id="question_div_'+divIndex+'">\n';
                            questionContainer += '\t\t\t\t\t\t\t<div class="ibox-title">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t<div id="question_header_'+divIndex+'" class="question-header">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t   <h5>Question No '+divIndex+'</h5>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t</div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t<div class="ibox-tools">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<a href="javascript:void(0);" data-divid="'+divIndex+'" class="clone-question dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Clone this question"><span class="badge badge-primary p-1"><i class="fa fa-clone"></i> Clone This Question</span></a>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<a href="javascript:void(0);" data-divid="'+divIndex+'" class="remove-question dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Remove this question"><span class="badge badge-danger p-1"><i class="fa fa-minus-circle"></i> Remove Question</span></a>\n';
                            questionContainer += '\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<a class="collapse-question-div" data-cstatus="'+collapsed+'">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t<span class="badge badge-success p-1"><i class="fa fa-chevron-up"></i> Toogle Question</span>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t</a> \n';
                            questionContainer += '\t\t\t\t\t\t\t\t</div>\n';
                            questionContainer += '\t\t\t\t\t\t\t</div>\n';
                            questionContainer += '\t\t\t\t\t\t\t<div class="ibox-content content_div_loader">\n';
                            questionContainer += '\t\t\t\t\t\t\t   <div class="sk-spinner sk-spinner-wave">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="sk-rect1"></div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="sk-rect2"></div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="sk-rect3"></div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="sk-rect4"></div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="sk-rect5"></div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t</div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\n';
                            questionContainer += '\t\t\t\t\t\t\t\t<div class="form-group row"><label class="col-sm-2 col-form-label text-right">Question <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Question"><i class="fa fa-question-circle"></i></span></label>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t  <div class="col-sm-10">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t  <textarea class="form-control ques" name="questions['+index+'][ques]" rows="2" placeholder="Enter Question..." value="" required>'+question['ques']+'</textarea>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t  </div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t  </div>\t\t\t\t\t\t\t   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t <div class="hr-line-dashed"></div>\n';
                            questionContainer += '\n';
                            questionContainer += '\t\t\t\t\t\t\t\t <div class="form-group row">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Option One <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option One"><i class="fa fa-question-circle"></i></span></label>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-10">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t  <input type="text" class="form-control opt1" name="questions['+index+'][opt1]" placeholder="Enter Option One..." value="'+question['opt1']+'" required>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t </div>\t\t\t\t\t\t\t   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t <div class="hr-line-dashed"></div>  \n';
                            questionContainer += '\n';
                            questionContainer += '\t\t\t\t\t\t\t\t <div class="form-group row">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Option Two <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option Two"><i class="fa fa-question-circle"></i></span></label>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-10">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t  <input type="text" class="form-control opt2" name="questions['+index+'][opt2]" placeholder="Enter Option Two..." value="'+question['opt2']+'" required>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t </div>\t\t\t\t\t\t\t   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t <div class="hr-line-dashed"></div>  \n';
                            questionContainer += '\n';
                            questionContainer += '\t\t\t\t\t\t\t\t <div class="form-group row">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Option Three <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option One"><i class="fa fa-question-circle"></i></span></label>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-10">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t  <input type="text" class="form-control opt3" name="questions['+index+'][opt3]" placeholder="Enter Option Three..." value="'+question['opt3']+'" required>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t </div>\t\t\t\t\t\t\t   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t <div class="hr-line-dashed"></div>  \n';
                            questionContainer += '\n';
                            questionContainer += '\t\t\t\t\t\t\t\t <div class="form-group row">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Option Four <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option One"><i class="fa fa-question-circle"></i></span></label>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-10">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t  <input type="text" class="form-control opt4" name="questions['+index+'][opt4]" placeholder="Enter Option Four..." value="'+question['opt4']+'" required>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t </div>\t\t\t\t\t\t\t   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t <div class="hr-line-dashed"></div> \n';
                            questionContainer += '\n';
                            questionContainer += '\t\t\t\t\t\t\t\t <div class="form-group row">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Correct Answer <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Correct Answer"><i class="fa fa-question-circle"></i></span></label>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-4">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t  <input type="number" class="form-control cor_ans" name="questions['+index+'][cor_ans]" placeholder="Enter Correct Answer..." value="'+question['cor_ans']+'" required>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Question Status <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Select Question Status"><i class="fa fa-question-circle"></i></span></label>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-4">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t  <select class="form-control record_status" name="questions['+index+'][record_status]">\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t\t<option selected disabled value>Select question status</option>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t\t<option value="active" '+rstatus_active_selected+' >Active</option>  \n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t\t<option value="blocked" '+rstatus_inactive_selected+'>Inactive</option>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t\t  </select>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                            questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                            questionContainer += '\t\t\t\t\t\t\t\t </div>\t\t\t\t\t\t\t   \n';
                            questionContainer += '\t\t\t\t\t\t  </div>\n';
                            questionContainer += '\t\t\t\t\t  </div>\n';

                            $("#main_question_container").append(questionContainer);

                            index++;
                         }   
                        }else{

                        } 
                        
                        //Form button actions
                        $("#form_submit_actions").removeClass("d-none");
                        $("#add_question_div").removeClass("d-none");
                        $("#add_first_question").addClass("d-none");

                        toastr.options.onHidden = function() { location.reload(); }
                        toastr.success("All questions are successfully deleted", "Success"); 
                        return true; 
                       
                     }else{
                       //Disabling loader
                        $('.content_div_loader').removeClass('sk-loading');
                        //show sweetalert success
                         if(data.message.length>0){
                           var message = data.message;
                        }else{
                           var message = "Something went wrong";
                        }
                        
                        toastr.error(message, "Error!"); 
                        return false;
                     }
                  }
            });
         }

         $(document).ready(function () {

            fetchAllQuestions();

            var question_div_collapse = getCookie('question_div_collapse');

            //Bind tooltip on dynamic meta elements
            $('body').tooltip({
                selector: '.dynamicQuestion'
            });

            $(document).on('click','.clone-question',function(e){
               e.preventDefault();
               divIndex = index + 1;

               var divId = $(this).data('divid');

               var questionContainer =  $("#question_div_"+divId).clone(true);
               questionContainer.attr('id',"question_div_"+divIndex);
               questionContainer.addClass("mt-3");
               questionContainer.find( '.question-header').attr( 'id', 'question_header_'+ divIndex ).data( 'divid', divIndex );
               questionContainer.find( '.clone-question').data( 'divid', divIndex );
               questionContainer.find( '.remove-question').data( 'divid', divIndex );
               questionContainer.find( '.ques' ).attr( 'name', 'questions[' + index + '][ques]' );
               questionContainer.find( '.opt1' ).attr( 'name', 'questions[' + index + '][opt1]' );
               questionContainer.find( '.opt2' ).attr( 'name', 'questions[' + index + '][opt2]' );
               questionContainer.find( '.opt3' ).attr( 'name', 'questions[' + index + '][opt3]' );
               questionContainer.find( '.opt4' ).attr( 'name', 'questions[' + index + '][opt4]' );
               questionContainer.find( '.cor_ans' ).attr( 'name', 'questions[' + index + '][cor_ans]' );
               questionContainer.find( '.record_status' ).attr( 'name', 'questions[' + index + '][record_status]' );

               $("#main_question_container").append(questionContainer);
               $("#question_header_"+divIndex).html("<h5>Question No "+divIndex+"</h5>");

               index++;

           });

           $(document).on('click','#add_more,#add_first_question',function(e){
               e.preventDefault();

                divIndex = index + 1;

                if(index == 0){
                   $("#form_submit_actions").removeClass("d-none");
                   $("#add_question_div").removeClass("d-none");
                   $("#add_first_question").addClass("d-none");
                }

                var collapsed = question_div_collapse == 'true' ? 'collapsed' : 'open';

                let questionContainer = "";
                questionContainer += '<div class="ibox question_div '+collapsed+'" id="question_div_'+divIndex+'">\n';
                questionContainer += '\t\t\t\t\t\t\t<div class="ibox-title">\n';
                questionContainer += '\t\t\t\t\t\t\t\t<div id="question_header_'+divIndex+'" class="question-header">\n';
                questionContainer += '\t\t\t\t\t\t\t\t   <h5>Question No '+divIndex+'</h5>\n';
                questionContainer += '\t\t\t\t\t\t\t\t</div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t<div class="ibox-tools">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<a href="javascript:void(0);" data-divid="'+divIndex+'" class="clone-question dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Clone this question"><span class="badge badge-primary p-1"><i class="fa fa-clone"></i> Clone This Question</span></a>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<a href="javascript:void(0);" data-divid="'+divIndex+'" class="remove-question dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Remove this question"><span class="badge badge-danger p-1"><i class="fa fa-minus-circle"></i> Remove Question</span></a>\n';
                questionContainer += '\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<a class="collapse-question-div" data-cstatus="'+collapsed+'">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t<span class="badge badge-success p-1"><i class="fa fa-chevron-up"></i> Toogle Question</span>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t</a> \n';
                questionContainer += '\t\t\t\t\t\t\t\t</div>\n';
                questionContainer += '\t\t\t\t\t\t\t</div>\n';
                questionContainer += '\t\t\t\t\t\t\t<div class="ibox-content content_div_loader">\n';
                questionContainer += '\t\t\t\t\t\t\t   <div class="sk-spinner sk-spinner-wave">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="sk-rect1"></div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="sk-rect2"></div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="sk-rect3"></div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="sk-rect4"></div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="sk-rect5"></div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t</div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\n';
                questionContainer += '\t\t\t\t\t\t\t\t<div class="form-group row"><label class="col-sm-2 col-form-label text-right">Question <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Question"><i class="fa fa-question-circle"></i></span></label>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t  <div class="col-sm-10">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t  <textarea class="form-control ques" name="questions['+index+'][ques]" rows="2" placeholder="Enter Question..." value="" required></textarea>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                questionContainer += '\t\t\t\t\t\t\t\t\t  </div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t  </div>\t\t\t\t\t\t\t   \n';
                questionContainer += '\t\t\t\t\t\t\t\t <div class="hr-line-dashed"></div>\n';
                questionContainer += '\n';
                questionContainer += '\t\t\t\t\t\t\t\t <div class="form-group row">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Option One <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option One"><i class="fa fa-question-circle"></i></span></label>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-10">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t  <input type="text" class="form-control opt1" name="questions['+index+'][opt1]" placeholder="Enter Option One..." value="" required>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t </div>\t\t\t\t\t\t\t   \n';
                questionContainer += '\t\t\t\t\t\t\t\t <div class="hr-line-dashed"></div>  \n';
                questionContainer += '\n';
                questionContainer += '\t\t\t\t\t\t\t\t <div class="form-group row">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Option Two <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option Two"><i class="fa fa-question-circle"></i></span></label>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-10">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t  <input type="text" class="form-control opt2" name="questions['+index+'][opt2]" placeholder="Enter Option Two..." value="" required>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t </div>\t\t\t\t\t\t\t   \n';
                questionContainer += '\t\t\t\t\t\t\t\t <div class="hr-line-dashed"></div>  \n';
                questionContainer += '\n';
                questionContainer += '\t\t\t\t\t\t\t\t <div class="form-group row">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Option Three <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option One"><i class="fa fa-question-circle"></i></span></label>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-10">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t  <input type="text" class="form-control opt3" name="questions['+index+'][opt3]" placeholder="Enter Option Three..." value="" required>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t </div>\t\t\t\t\t\t\t   \n';
                questionContainer += '\t\t\t\t\t\t\t\t <div class="hr-line-dashed"></div>  \n';
                questionContainer += '\n';
                questionContainer += '\t\t\t\t\t\t\t\t <div class="form-group row">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Option Four <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Option One"><i class="fa fa-question-circle"></i></span></label>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-10">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t  <input type="text" class="form-control opt4" name="questions['+index+'][opt4]" placeholder="Enter Option Four..." value="" required>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t </div>\t\t\t\t\t\t\t   \n';
                questionContainer += '\t\t\t\t\t\t\t\t <div class="hr-line-dashed"></div> \n';
                questionContainer += '\n';
                questionContainer += '\t\t\t\t\t\t\t\t <div class="form-group row">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Correct Answer <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Enter Correct Answer"><i class="fa fa-question-circle"></i></span></label>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-4">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t  <input type="number" class="form-control cor_ans" name="questions['+index+'][cor_ans]" placeholder="Enter Correct Answer..." value="" required>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<label class="col-sm-2 col-form-label text-right">Question Status <span class="cursor-pointer dynamicQuestion" data-toggle="tooltip" data-placement="bottom" title="Select Question Status"><i class="fa fa-question-circle"></i></span></label>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t<div class="col-sm-4">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t<div class="input-group">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t  <select class="form-control record_status" name="questions['+index+'][record_status]">\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t\t<option selected disabled value>Select question status</option>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t\t<option value="active">Active</option>  \n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t\t<option value="blocked">Inactive</option>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t\t  </select>\n';
                questionContainer += '\t\t\t\t\t\t\t\t\t   </div>   \n';
                questionContainer += '\t\t\t\t\t\t\t\t   </div>\n';
                questionContainer += '\t\t\t\t\t\t\t\t </div>\t\t\t\t\t\t\t   \n';
                questionContainer += '\t\t\t\t\t\t  </div>\n';
                questionContainer += '\t\t\t\t\t  </div>\n';

                $("#main_question_container").append(questionContainer);

                index++;

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

           $(document).on('blur','.cor_ans',function(e){
               var cor_ans = $(this).val();

               if(cor_ans<1 || cor_ans>4){
                  toastr.error("Correct option should be between 1 - 4.", 'Error!');
                  $("#create").prop('disabled',true);
                  return false;
               }else{
                  $("#create").prop('disabled',false);
                  return true;
               }
           });

           $(document).on('click','.remove-question',function(e){
               var divId = $(this).data('divid');
               index = index - 1;

               swal({
                    title: "Are you sure?",
                    text: "Are you sure to delete this question?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, Go ahead!",
                    closeOnConfirm: true
               }, function () {

                   $('.overlayer').fadeIn();
               
                   if(divId > 0){
                       //Removing question div from div
                      $("#question_div_"+divId).remove();

                      if(index == 0){
                         $("#form_submit_actions").addClass("d-none");
                         $("#add_question_div").addClass("d-none");
                         $("#add_first_question").removeClass("d-none");
                      }else{
                         $("#form_submit_actions").removeClass("d-none");
                         $("#add_question_div").removeClass("d-none");
                         $("#add_first_question").addClass("d-none");

                         $(".question_div").each(function(index,el){
                             var divIndex = index+1;
                             var newDivIndex = index+1;

                             if(divIndex >= divId){
                                divIndex = divIndex+1;
                             }else{
                                divIndex = divIndex;
                             }

                             var questionContainer =  $("#question_div_"+divIndex).clone(true);
                             $("#question_div_"+divIndex).remove();

                             questionContainer.attr('id',"question_div_"+newDivIndex);
                             questionContainer.addClass("mt-3");
                             questionContainer.find( '.question-header').attr( 'id', 'question_header_'+ newDivIndex ).data( 'divid',divIndex );
                             questionContainer.find( '.clone-question').data( 'divid', newDivIndex );
                             questionContainer.find( '.remove-question').data( 'divid', newDivIndex );
                             questionContainer.find( '.ques' ).attr( 'name', 'questions[' + index + '][ques]' );
                             questionContainer.find( '.opt1' ).attr( 'name', 'questions[' + index + '][opt1]' );
                             questionContainer.find( '.opt2' ).attr( 'name', 'questions[' + index + '][opt2]' );
                             questionContainer.find( '.opt3' ).attr( 'name', 'questions[' + index + '][opt3]' );
                             questionContainer.find( '.opt4' ).attr( 'name', 'questions[' + index + '][opt4]' );
                             questionContainer.find( '.cor_ans' ).attr( 'name', 'questions[' + index + '][cor_ans]' );
                             questionContainer.find( '.record_status' ).attr( 'name', 'questions[' + index + '][record_status]' );

                             $("#main_question_container").append(questionContainer);
                             $("#question_header_"+newDivIndex).html("<h5>Question No "+newDivIndex+"</h5>");
                         });

                      }

                      $('.overlayer').fadeOut();

                      return true;
                   }else{
                      toastr.error("Question set must contains minimum one question.", 'Error!');
                      return false;
                   }
               });    
              
           }); 

           $(document).on('click','#collapse_question_divs',function(e){

               //Set User Preference in Cookie
               document.cookie = "question_div_collapse=true";
               question_div_collapse = 'true';

               $(this).addClass('active');  

               //Show Loader
               $('.content_div_loader').addClass('sk-loading');

               setTimeout(function(){

                  $(".question_div").each(function(){
                     $(this).addClass('collapsed');
                  });

                  $(".collapse-question-div").each(function(){
                     $(this).data('cstatus','collapsed');
                  });
                   
                  $("#open_question_divs").removeClass('active');

                  $('.content_div_loader').removeClass('sk-loading');

               },1000);
                             
           }); 

           $(document).on('click','#open_question_divs',function(e){

               //Set User Preference in Cookie
               document.cookie = "question_div_collapse=false";
               question_div_collapse = 'false';

               //Show Loader
               $('.content_div_loader').addClass('sk-loading');

               $(this).addClass('active'); 

               setTimeout(function(){
               
                   $(".question_div").each(function(){
                       $(this).removeClass('collapsed');
                   });

                   $(".collapse-question-div").each(function(){
                      $(this).data('cstatus','open');
                   });

                   $("#collapse_question_divs").removeClass('active');

                   $('.content_div_loader').removeClass('sk-loading');

                },1000);   
              
           }); 

            $(document).on('click','.collapse-question-div',function(e){
               var collapse_status = $(this).data('cstatus');

               if(collapse_status == "open"){
                  $(this).data('cstatus','collapsed');
                  $(this).parent().parent().parent().addClass("collapsed");
               }else{
                  $(this).data('cstatus','open');
                  $(this).parent().parent().parent().removeClass("collapsed");
               }
           }); 

           $('#main_question_container').sortable({
                 //handle: ".draggable",
                 update: function (event, ui) {
                    var formData = $(this).sortable('serialize');

                    formData += '&exam_id=' + exam_id + '&action=sortExamQuestions';

                    //POST to server using $.post or $.ajax
                    $.ajax({
                        data: formData,
                        method: 'POST',
                        url: ajaxControllerHandler,
                         beforeSend: function() {
                            //Display loader
                            $('.content_div_loader').addClass('sk-loading');
                         },
                         success:function(responseData){
                            var data = JSON.parse(responseData);
                            //console.log(data);

                            $('.content_div_loader').removeClass('sk-loading');

                            if(data.check == "success"){
                                toastr.success("Oreding is successfully completed", 'Success!');

                                $(".question_div").each(function(index,el){
                                     var divIndex = index+1;

                                     var questionContainer =  $("#question_div_"+divIndex).clone(true);
                                     $("#question_div_"+divIndex).remove();

                                     questionContainer.attr('id',"question_div_"+divIndex);
                                     questionContainer.addClass("mt-3");
                                     questionContainer.find( '.question-header').attr( 'id', 'question_header_'+ divIndex ).data( 'divid',divIndex );
                                     questionContainer.find( '.clone-question').data( 'divid', divIndex );
                                     questionContainer.find( '.remove-question').data( 'divid', divIndex );
                                     questionContainer.find( '.ques' ).attr( 'name', 'questions[' + index + '][ques]' );
                                     questionContainer.find( '.opt1' ).attr( 'name', 'questions[' + index + '][opt1]' );
                                     questionContainer.find( '.opt2' ).attr( 'name', 'questions[' + index + '][opt2]' );
                                     questionContainer.find( '.opt3' ).attr( 'name', 'questions[' + index + '][opt3]' );
                                     questionContainer.find( '.opt4' ).attr( 'name', 'questions[' + index + '][opt4]' );
                                     questionContainer.find( '.cor_ans' ).attr( 'name', 'questions[' + index + '][cor_ans]' );
                                     questionContainer.find( '.record_status' ).attr( 'name', 'questions[' + index + '][record_status]' );

                                     $("#main_question_container").append(questionContainer);
                                     $("#question_header_"+divIndex).html("<h5>Question No "+divIndex+"</h5>");
                                });

                            }else{
                                toastr.error(data.message, 'Error!');
                            }
                         }
                    });
                  }
            }); 

            $(document).on('submit', '#manage_exam_questions_form', function(event){
                event.preventDefault();
                                
                $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: new FormData(this),
                  contentType:false,
                  processData:false,
                  beforeSend: function() {
                     $('.content_div_loader').addClass('sk-loading');
                     $('.overlayer').fadeIn();
                     $('#create').attr('disabled',true);
                  },
                  success:function(responseData){
                     var data = JSON.parse(responseData);
                     $('#create').attr('disabled',false);
                     //console.log(responseData);
                     if(data.check == 'success'){                        
                        //Disabling loader
                        $('.content_div_loader').removeClass('sk-loading');
                        $('.overlayer').fadeOut();
                        
                        swal({
                            title: "Great!",
                            text: "Questions are successfully created",
                            type: "success"
                        },function() {
                            // window.location = redirect_url;
                            location.reload();
                        });
                        return true; 
                     }else{
                       //Disabling loader
                        $('.content_div_loader').removeClass('sk-loading');
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


            $(document).on('click', '#delete_all_questions', function(event){
                event.preventDefault();
                
                var formData = {exam_id:exam_id, action:"deleteAllQuestions"}

                swal({
                   title: "Are you sure?",
                   text: "Are you sure to delete all questions?",
                   type: "warning",
                   showCancelButton: true,
                   confirmButtonColor: "#DD6B55",
                   confirmButtonText: "Yes, Go ahead!",
                   closeOnConfirm: true
                },
                function() {
                
                  $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: formData,
                  beforeSend: function() {
                     $('.content_div_loader').addClass('sk-loading');
                     $('.overlayer').fadeIn();
                     $(this).attr('disabled',true);
                  },
                  success:function(responseData){
                      var data = JSON.parse(responseData);
                      $(this).attr('disabled',false);
                     //console.log(responseData);
                     if(data.check == 'success'){

                        setTimeout(function(){
                            //Disabling loader
                            $('.content_div_loader').removeClass('sk-loading');
                            $('.overlayer').fadeOut();

                            $("#main_question_container").html('');
                            $("#form_submit_actions").addClass("d-none");
                            $("#add_question_div").addClass("d-none");
                            $("#add_first_question").removeClass("d-none");

                            toastr.options.onHidden = function() { location.reload(); }
                            toastr.success("All questions are successfully deleted", "Success"); 
                            return true; 
                        },1000);
                       
                     }else{
                       //Disabling loader
                        $('.content_div_loader').removeClass('sk-loading');
                        //show sweetalert success
                         if(data.message.length>0){
                           var message = data.message;
                        }else{
                           var message = "Something went wrong";
                        }
                        
                        toastr.error(message, "Error!"); 
                        return false;
                     }
                  }
                 });

                });  
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

            //EXAM'S QUESTIONS IMPORT FORM SUBMIT HANDLER
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
                         $('.overlayer').fadeIn();
                      },
                      success: function(responseData){
                           setTimeout(function() {
                              $('.content_div_loader').removeClass('sk-loading');
                              $('.overlayer').fadeOut();

                              $('#import_table_data_form')[0].reset();

                              var data = JSON.parse(responseData);
                              //console.log(responseData);
                              if(data.check == 'success'){
                                 //define toastr error
                                 toastr.options = {
                                   closeButton: true,
                                   progressBar: true,
                                   showMethod: 'slideDown',
                                   timeOut: 2000
                                 };
                                 toastr.options.onHidden = function() { location.reload(); }
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
                            }, 1000);
                        }
                    });
                });  
            });
     });
     </script>