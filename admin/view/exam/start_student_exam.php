<?php
  if(isset($_GET['exm_id'])){
    $exam_id = $_GET['exm_id'];
    $examDetails = $pageContent['pageData']['exam_details'];
    $questions = $pageContent['pageData']['questions'];
    $answers = $pageContent['pageData']['answers'];
    $falgged_questions = $pageContent['pageData']['falgged_questions'];
    $viewed_questions = $pageContent['pageData']['viewed_questions'];
    
    //Formatting answers array
    $answerArr = array();

    foreach ($answers as $aindex => $answer) {
        $answerArr[$answer->ques_id]['ques_id'] = $answer->ques_id;
        $answerArr[$answer->ques_id]['selection'] = $answer->answer;
    }

    //Formatting flagged questions array
    $flagArr = array();

    foreach ($falgged_questions as $findex => $flag) {
        $flagArr[$findex] = $flag->ques_id;
    }

    //Formatting viewed questions array
    $viewArr = array();

    foreach ($viewed_questions as $vindex => $view) {
        $viewArr[$vindex] = $view->ques_id;
    }

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

  //Exam Start Handler
  $total_exam_time = ($examDetails->hours * 3600 + $examDetails->minutes * 60);

  //unset($_SESSION['exam_started']);

  if($_SESSION['exam_started'] == "true"){
     $today = time();
     $exam_start_at = date('jS F Y g:i A',time());
     $exam_end_at = date('jS F Y g:i A',time()+$total_exam_time);
  }else{
     $exam_start_at = null;
     $exam_end_at = null;
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

                              </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

              </div>
            </div>

            <div class="row">
              <div class="col-lg-8">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Exam Instructions </h5>
                        <div class="ibox-tools">
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
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="panel panel-primary">
                                    <div class="panel-heading">
                                        <i class="fa fa-info-circle"></i> OMR Legend 
                                    </div>
                                    <div class="panel-body">
                                         <div class="exam-legend-info">
                                            <span class="pl-list">
                                                <span class="view-btn default"></span>Not Viewd
                                            </span> 
                                            <span class="pl-list">
                                                <span class="view-btn viewed"></span>Viewd
                                            </span> 
                                            <span class="pl-list">
                                                <span class="view-btn saved"></span>Saved
                                            </span> 
                                            <span class="pl-list">
                                                <span class="view-btn flagged"></span>Flagged
                                            </span> 
                                            <span class="pl-list">
                                                <span class="view-btn saved-flagged"></span>Saved & Flagged
                                            </span> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <i class="fa fa-level-down"></i> Exam Instructions
                                    </div>
                                    <div class="panel-body">
                                        <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum tincidunt est vitae ultrices accumsan. Aliquam ornare lacus adipiscing, posuere lectus et, fringilla augue.</p>

                                    </div>
                                </div>
                            </div>    
                        </div>

                    </div>
                </div>

              </div>
              <div class="col-lg-4">
                <div class="ibox ">
                    <div class="ibox-title">
                        <h5>Exam Information</h5>
                        <div class="ibox-tools">
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
                        <div class="row">
                             <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="panel panel-info">
                                    <div class="panel-heading">
                                        <i class="fa fa-keyboard-o"></i> Exam Date & Time
                                    </div>
                                    <div class="panel-body text-justify">
                                       <p><strong>Exam Date:</strong> <?=date('jS F, Y',strtotime($examDetails->created_at))?></p>
                                       <p><strong>Exam Start:</strong> <?=($exam_start_at != null ? $exam_start_at: 'Exam not Started Yet.')?></p>
                                       <p><strong>Exam End:</strong> <?=($exam_end_at != null ? $exam_end_at: 'Exam not Started Yet.')?></p> 
                                    </div>
                                </div>
                            </div> 

                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="panel panel-success">
                                    <div class="panel-heading" id="exam_timer_header">
                                        <i class="fa fa-ravelry"></i> Start Exam
                                    </div>
                                    <div class="panel-body">
                                       <?php if($_SESSION['exam_started'] == "true"){ ?> 
                                           <div class="exam-timer text-center" id="countDown"></div>    
                                        <?php }else{ ?>
                                            <div class="text-center">
                                                <button type="button" id="initiateExam" class="btn btn-danger"><i class="fa fa-clock-o"></i> Start Your Exam</button>
                                           </div>
                                        <?php } ?>      
                                    </div>
                                </div>
                            </div>    
                        </div>    
                    </div>
                </div>

              </div>
            </div>

            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-12">
                  <form id="manage_exam_answer_form" class="needs-validation" method="post" onsubmit="return false;">

                     <div class="overlayer" style="display: none;">
                       <div class="spinner"></div>
                    </div>
                               
                     <input type="hidden" name="action" id="action" value="manageExamAnswer">
                     <input type="hidden" name="exam_id" id="exam_id" value="<?=$exam_id?>">
                               
                      <div id="main_question_container">
                          <?php 
                            foreach ($questions as $index => $question) { 
                               if(array_key_exists($question->id, $answerArr)){
                                   $selection = $answerArr[$question->id]['selection'];
                               }else{
                                   $selection = 0;
                               }  
                          ?>
                                <div class="ibox <?=($index > 0 ? 'd-none':'')?> question_div" id="question_div_<?=($index+1)?>">
                                    <div class="ibox-title">
                                        <h5>Question No <?=($index+1)?></h5>
                                        <div class="ibox-tools">
                                            <a class="collapse-link">
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

                                        <h3><?=$question->ques?></h3>
                                                                      
                                         <div class="hr-line-dashed"></div>

                                         <div class="form-group row">
                                            <input type="hidden" id="hidden_qid_<?=$index?>" name="answers[<?=$index?>][qid]" value="<?=$question->id?>">
                                            <input type="hidden" id="hidden_sid_<?=$question->id?>" data-sid="<?=$index+1?>" name="answers[<?=$index?>][qid]" value="<?=$question->id?>">
                                            
                                            <div class="col-lg-12 col-md-12 col-sm-12">
                                                
                                                <?php if(!empty($question->opt1)){ ?>
                                                    <div class="form-group row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <label class="checkbox-inline i-checks answer_selection cursor-pointer"> 
                                                                <input type="radio" id="opt1<?=$index?>" data-qid="<?=$question->id?>" name="answers[<?=$index?>][selection]" value="1" <?=($selection == 1 ? 'checked':'')?>> <i></i> 
                                                                <?=$question->opt1?> 
                                                            </label>
                                                        </div> 
                                                    </div>  

                                                    <div class="hr-line-dashed"></div>  
                                                <?php } ?>
                                                
                                                <?php if(!empty($question->opt2)){ ?>       
                                                
                                                    <div class="form-group row">    
                                                        <div class="col-lg-12 col-md-12 col-sm-12">  
                                                            <label class="checkbox-inline i-checks answer_selection cursor-pointer"> <input type="radio" id="opt2<?=$index?>" data-qid="<?=$question->id?>" name="answers[<?=$index?>][selection]" value="2" <?=($selection == 2 ? 'checked':'')?>> <i></i> 
                                                                <?=$question->opt2?> 
                                                            </label>
                                                        </div>    
                                                    </div>    

                                                    <div class="hr-line-dashed"></div>
                                                <?php } ?>   
                                                
                                                <?php if(!empty($question->opt3)){ ?>  

                                                    <div class="form-group row">
                                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                                            <label class="checkbox-inline i-checks answer_selection cursor-pointer"> 
                                                                <input type="radio" id="opt3<?=$index?>" data-qid="<?=$question->id?>" name="answers[<?=$index?>][selection]" value="3" <?=($selection == 3 ? 'checked':'')?>> <i></i> 
                                                                <?=$question->opt3?> 
                                                            </label>
                                                        </div>  
                                                    </div>

                                                    <div class="hr-line-dashed"></div>
                                                <?php } ?>
                                                
                                                <?php if(!empty($question->opt4)){ ?>    
                                                      
                                                    <div class="form-group row">    
                                                        <div class="col-lg-12 col-md-12 col-sm-12">  
                                                            <label class="checkbox-inline i-checks answer_selection cursor-pointer"> 
                                                                <input type="radio" id="opt4<?=$index?>" data-qid="<?=$question->id?>" name="answers[<?=$index?>][selection]" value="4" <?=($selection == 4 ? 'checked':'')?>> <i></i> 
                                                                <?=$question->opt4?> 
                                                            </label>
                                                        </div>    
                                                    </div>    

                                                    <div class="hr-line-dashed"></div>
                                                <?php } ?>    

                                                <div class="col-sm-12 d-flex justify-content-between px-0">
                                                    
                                                    <button type="button" id="previous_<?=$index+1?>" class="btn btn-success btn-sm nav_action" data-stype="previous" data-sid="<?=($index+1)?>" data-qid="<?=$question->id?>" data-toggle="tooltip" title="Browse Previous Question" <?=($index>0 ? '':'disabled')?>><i class="fa fa-arrow-left"></i> Previous</button>

                                                    <button type="button" id="save_review_<?=$question->id?>" class="btn <?=(in_array($question->id, $flagArr)? 'btn-orange':'btn-info')?> btn-sm review_later" data-qid="<?=$question->id?>" data-sid="<?=($index+1)?>" data-qid="<?=$question->id?>" data-toggle="tooltip" title="Save Answer & Review for Later" <?=( $_SESSION['exam_started'] == "true" ? "":"disabled" )?>>
                                                        <?php if(in_array($question->id, $flagArr)){ ?>
                                                            <i class="fa fa-flag-o"></i> Flagged for Review
                                                        <?php }else{ ?>   
                                                            <i class="fa fa-star"></i> Review for Later
                                                        <?php } ?>     
                                                    </button>

                                                    <button type="button" id="clear_<?=$question->id?>" class="btn btn-danger btn-sm clear_action" data-sid="<?=($index+1)?>" data-qid="<?=$question->id?>" data-toggle="tooltip" title="Clear Seection" <?=( $_SESSION['exam_started'] == "true" ? ( array_key_exists($question->id, $answerArr) ? '':'disabled' ) :"disabled" )?>><i class="fa fa-trash"></i> Clear Seection</button>

                                                    <button type="button" id="clear_<?=$question->id?>" class="btn btn-info btn-sm" data-toggle="tooltip" title="Exam Instructions"><i class="fa fa-info-circle"></i> Exam Instructions</button>

                                                    <button type="button" id="next_<?=$index+1?>" class="btn btn-warning btn-sm nav_action" data-stype="next" data-sid="<?=($index+1)?>" data-qid="<?=$question->id?>" data-qid="<?=$question->id?>" data-toggle="tooltip" title="Browse Next Question" <?=(($index+1 == count($questions) ? 'disabled' : ''))?>><i class="fa fa-arrow-right"></i> Next</button>
                                                </div>
                                            </div>
                                         </div>                               
                                    
                                   </div>
                               </div>   
                           <?php } ?>   
                             
                            <div class="form-action-btns">
                              <div class="text-left <?=(!empty($questions) ? '' : 'd-none')?>"> 
                                  <button class="btn btn-primary btn-sm" id="create" type="submit" class="btn btn-success" data-toggle="tooltip" title="Save" data-placement="bottom" <?=($_SESSION['exam_started'] !== "true" ? 'disabled':'' )?>><i class="fa fa-save"></i> Save Answer</button>
                              </div>

                              <div class="text-left"> 
                                  <button class="btn btn-warning btn-sm" type="button" class="btn btn-success" data-toggle="tooltip" title="View Your Profile" data-placement="bottom"><i class="fa fa-user"></i> Student Information</button>
                              </div>

                              <div class="text-left"> 
                                  <button class="btn btn-info btn-sm" type="button" class="btn btn-success" data-toggle="tooltip" title="View Exam Details" data-placement="bottom"><i class="fa fa-file-text-o"></i> Exam Details</button>
                              </div>

                              <div class="text-right">    
                                <button type="button" class="btn btn-danger" id="finish_exam" <?=($_SESSION['exam_started'] !== "true" ? 'disabled':'' )?>>
                                    <i class="fa fa-chain-broken"></i> Finish Exam
                                </button>
                              </div>   
                            </div>     
                             
                      </div>
                     
                  </form>    
               </div>
               <div class="col-lg-4 col-md-4 col-sm-12">
                  <div class="ibox" id="inner-scrollbar">
                    <div class="ibox-title">
                        <h5>Question List</h5>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-xs btn-warning collapse-link">
                                <i class="fa fa-chevron-up"></i> Toggle Section
                            </button>
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

                      <div class="scroll_content">  
                        <div class="col-lg-12 col-md-12 col-sm-12"> 
                            <ul class="unstyled utf_footer_social" id="question_child_list">

                                <?php 
                                  foreach($questions as $qindex => $question){ 
                                    if( array_key_exists($question->id, $answerArr) && in_array($question->id, $flagArr) ){
                                       $class = "attempted-review";
                                    }
                                    elseif(array_key_exists($question->id, $answerArr)){
                                       $class = "attempted";
                                    }
                                    elseif(in_array($question->id, $flagArr)){
                                       $class = "flagged";
                                    }
                                    elseif(in_array($question->id, $viewArr)){
                                       $class = "viewed";
                                    }else{
                                       $class = $qindex == 0 ? "viewed":"";
                                    }
                                ?>

                                    <li>
                                        <a href="javascript:void(0);" id="browse_question_<?=$question->id?>" class="browse-question <?=$class?>" data-sid="<?=$qindex+1?>" data-qid="<?=$question->id?>"><text><?=$qindex+1?></text></a>
                                    </li> 

                                <?php } ?> 
                           </ul>
                       </div>  
                      </div>
                     </div>
                  </div>
               </div>

            </div>
        </div>  

      <!-- Custom JS -->
       <script>
         var index = 0;
         var exam_id = $("#exam_id").val();

         /*window.onbeforeunload = function(e) {
            var dialogText = "New added question won't get saved, Are you realy sure you want to leave?";
            e.returnValue = dialogText;
            return dialogText;
         };*/

         function recordViewdQuestion(qId){
             
             var formData = {exam_id:exam_id,qId:qId,action:"recordViewdQuestions"}
                
             $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: formData,
                  success:function(responseData){
                     var data = JSON.parse(responseData);
                     $(this).attr('disabled',false);
                     //console.log(responseData);

                     if(data.check == 'success'){
                        var qId = data.qId;
                        var questionId = "browse_question_"+qId;
                        $("#"+questionId).addClass('viewed');
                        return true; 
                     }else{
                        return false;
                     }
                  }
              });
         }

         $(document).ready(function () {
            
            //Save initial question status as viewed 
            var initQId = $("#hidden_qid_"+index).val();
            recordViewdQuestion(initQId);

            //Start Exam Timer
            function getCookie(name) {
               const value = `; ${document.cookie}`;
               const parts = value.split(`; ${name}=`);
               if (parts.length === 2) return parts.pop().split(';').shift();
            }

            var exam_started = '<?=$_SESSION['exam_started']?>';

            function validateExamStarted(){
                if(exam_started != "true"){
                    toastr.error("This exam isn't started yet so you can't perforform this action", "Error!"); 
                    return false;
                }else{
                    return true;
                }
            }

            if(exam_started == "true"){

                $("#exam_timer_header").html('<i class="fa fa-clock-o"></i> Time Left');

                $("#countDown").countdownTimer({
                    time: '<?=$total_exam_time?>',
                    callback: function () {
                       var exma_over_message = "Your exam is over, You will be redirected to the result page." 
                       swal({title: "That's it!",text: exma_over_message,type: "warning"},function() {
                            location.reload();
                        });
                    },
                });
            }else{
               localStorage.clear("count_timer"); 
            }    

            //Bind tooltip on dynamic meta elements
            $('body').tooltip({
                selector: '.dynamicQuestion'
            });

            // Add slimscroll to element
            $('.scroll_content').slimscroll({
                height: '472px'
            });

            $(document).on('click', '#initiateExam', function(event){
                //Set Exam Started Validation Log
                var formData = {exam_id:exam_id,action:"setExamValidationLog"}

                 swal({
                   title: "Are you sure?",
                   text: "Are you sure to start this exam?",
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
                         //$(this).attr('disabled',true);
                      },
                      success:function(responseData){
                         var data = JSON.parse(responseData);
                         $(this).attr('disabled',false);
                         //console.log(responseData);
                         if(data.check == 'success'){

                            //Disabling loader
                            $('.content_div_loader').removeClass('sk-loading');
                            setTimeout(function(){
                                swal({
                                    title: "Great!",
                                    text: "Your exam is being started",
                                    type: "success"
                                },function() {
                                    location.reload();
                                });
                            },200);    
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

                });    
                
            });

            $(document).on('ifClicked', '.i-checks.answer_selection input', function (e) {
                 var qId = $(this).data('qid');

                 var examValidation = validateExamStarted();

                 if(!examValidation){
                     setTimeout(function(){
                        $('input[name="answers['+index+'][selection]"]').prop('checked', false);
                        $('input[name="answers['+index+'][selection]"]').parent().removeClass('checked');
                    },200);
                    return false;
                 }

                 //console.log(qId);

                 $("#clear_"+qId).prop('disabled',false);

                 //Saving answer form data 
                 setTimeout(function(){
                    //Saving answer form data
                    $("#create").trigger('click');
                 },200);
            });

            $(document).on('click', '.clear_action', function(event){
                 var sId = $(this).data('sid') -1;
                 var qId = $(this).data('qid');

                 var examValidation = validateExamStarted();

                 if(!examValidation){
                    return false;
                 }

                 //console.log(qId);

                 $('body>.tooltip').remove();

                 $('input[name="answers['+sId+'][selection]"]').prop('checked', false);
                 $('input[name="answers['+sId+'][selection]"]').parent().removeClass('checked');

                 $("#clear_"+qId).prop('disabled',true);
                 
                 setTimeout(function(){
                    //Saving answer form data
                    $("#create").trigger('click');
                 },200);

            });

            $(document).on('click', '.nav_action', function(event){
                 var selection_type = $(this).data('stype');
                 var sId = $(this).data('sid');
                 var qId = $(this).data('qid');

                 $('.content_div_loader').addClass('sk-loading');

                 if(selection_type == "next"){
                    var targetId = sId+1; 
                    var currentQId = $("#next_"+targetId).data('qid');
                    index = $("#next_"+targetId).data('sid')-1;
                 }else{
                    var targetId = sId-1;
                    var currentQId = $("#previous_"+targetId).data('qid');
                    index = $("#previous_"+targetId).data('sid')-1;
                 }

                 //console.log(targetId);

                 setTimeout(function(){
                    $(".question_div").each(function(){
                        $(this).addClass('d-none');
                    });

                    $("#question_div_"+targetId).removeClass('d-none');
                    $('.content_div_loader').removeClass('sk-loading');

                    recordViewdQuestion(currentQId);
                 },400);

            });

            $(document).on('click', '.browse-question', function(event){
                 var sId = $(this).data('sid');
                 var qId = $(this).data('qid');

                 $('.content_div_loader').addClass('sk-loading');

                 setTimeout(function(){
                    $(".question_div").each(function(){
                        $(this).addClass('d-none');
                    });
                    $("#question_div_"+sId).removeClass('d-none');
                    $('.content_div_loader').removeClass('sk-loading');

                    index = sId-1;

                    recordViewdQuestion(qId);
                 },400);   
            });

            $(document).on('click', '.review_later', function(event){
                event.preventDefault();

                var qId = $(this).data('qid');
                var sId = $(this).data('sid');

                var examValidation = validateExamStarted();

                if(!examValidation){
                   return false;
                }

                var formData = {exam_id:exam_id,qId:qId,action:"flagQuestionForReview"}
                
                $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: formData,
                  beforeSend: function() {
                     //$('.content_div_loader').addClass('sk-loading');
                     //$(this).attr('disabled',true);
                  },
                  success:function(responseData){
                     var data = JSON.parse(responseData);
                     $(this).attr('disabled',false);
                     //console.log(data);
                     if(data.check == 'success'){

                        setTimeout(function(){
                            //Disabling loader
                            $('.content_div_loader').removeClass('sk-loading');

                            var qId = data.qId;

                            var questionId = "browse_question_"+qId;

                            if(data.flag_status == "attempted_review"){
                               var btnHtml = '<i class="fa fa-flag-o"></i> Flagged for Review';
                               $("#"+questionId).addClass('attempted-review');
                               $("#save_review_"+qId).removeClass('btn-info').addClass('btn-orange').html(btnHtml);
                            }
                            else if(data.flag_status == "added_reveiw"){
                               var btnHtml = '<i class="fa fa-flag-o"></i> Flagged for Review';
                               $("#"+questionId).addClass('flagged');
                               $("#save_review_"+qId).removeClass('btn-info').addClass('btn-orange').html(btnHtml);
                            }
                            else if(data.flag_status == "attempted"){
                               var btnHtml = '<i class="fa fa-star"></i> Review for Later'; 
                               $("#"+questionId).removeClass('attempted-review flagged').addClass('attempted');
                               $("#save_review_"+qId).removeClass('btn-orange').addClass('btn-info').html(btnHtml);
                            }
                            else if(data.flag_status == "deleted"){
                               var btnHtml = '<i class="fa fa-star"></i> Review for Later'; 
                               $("#"+questionId).removeClass('attempted-review flagged attempted').addClass('viewed');
                               $("#save_review_"+qId).removeClass('btn-orange').addClass('btn-info').html(btnHtml);
                            }else{
                               var btnHtml = '<i class="fa fa-star"></i> Review for Later'; 
                               $("#"+questionId).removeClass('flagged').addClass('viewed');
                               $("#save_review_"+qId).removeClass('btn-orange').addClass('btn-info').html(btnHtml); 
                            }    

                            toastr.success("Question no "+sId+" was successfully flagged.", "Success"); 
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

            $(document).on('submit', '#manage_exam_answer_form', function(event){
                event.preventDefault();

                var examValidation = validateExamStarted();

                if(!examValidation){
                   return false;
                }
                                
                $.ajax({
                  url:ajaxControllerHandler,
                  method:'POST',
                  data: new FormData(this),
                  contentType:false,
                  processData:false,
                  beforeSend: function() {
                     //$('.content_div_loader').addClass('sk-loading');
                     //$('#create').attr('disabled',true);
                  },
                  success:function(responseData){
                     var data = JSON.parse(responseData);
                     $('#create').attr('disabled',false);
                     $('body>.tooltip').remove();
                     //console.log(responseData);
                     
                     if(data.check == 'success'){                        
                        //Disabling loader
                        $('.content_div_loader').removeClass('sk-loading');

                        var answeredQuestions = data.answeredQuestions;
                        var removedAnswers = data.removedAnswerArr;
                        var flaggedQuestions = data.flaggedQuestions;

                        //console.log(answeredQuestions);

                        for(var i=0; i<answeredQuestions.length; i++){
                            var questionId = answeredQuestions[i];
                            var questionHtmlId = "browse_question_"+questionId;

                            if($.inArray(questionId,flaggedQuestions) !== -1){
                                $("#"+questionHtmlId).addClass('attempted-review');
                            }else{
                                $("#"+questionHtmlId).addClass('attempted');
                            }
                        }

                        for(var i=0; i<removedAnswers.length; i++){
                            var questionId = removedAnswers[i];
                            var questionHtmlId = "browse_question_"+questionId;

                            var sId = $("#hidden_sid_"+questionId).data('sid');

                            if($.inArray(questionId,flaggedQuestions) !== -1){
                                $("#"+questionHtmlId).removeClass('attempted-review').addClass('flagged');
                            }else{
                                $("#"+questionHtmlId).removeClass('attempted').addClass('viewed');
                            }
                            
                            //Define toastr options
                            toastr.options = {closeButton: true,progressBar: true,showMethod: 'slideDown',timeOut: 4000};
                            toastr.error("You haven't selected any option for question no "+sId, "Error!"); 
                        }
                        
                        toastr.options = {closeButton: true,progressBar: true,showMethod: 'slideDown',timeOut: 1500};
                        toastr.success("All answers are successfully saved", "Success"); 
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

            
     });
     </script>