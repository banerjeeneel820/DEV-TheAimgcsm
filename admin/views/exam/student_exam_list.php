<?php  
  $examDataArr = $pageContent['pageData']['exam_data']; 

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
  $examStartPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_exam"); 
  
  /*print"<pre>";
  print_r($examDataArr); 
  print"</pre>";*/
?>
 
     <div class="wrapper wrapper-content fadeInRight">
                     
         <div class="row">
             <div class="col-lg-12">  
              <div class="ibox">
                <div class="ibox-title">
                    <h5>All Exam List</h5>
                    <div class="ibox-tools">
                        <a href="<?=SITE_URL?>?route=view_exams" class="table-action-info"  data-toggle="tooltip" data-placement="bottom" title="Refresh Exam Data"><i class="fa fa-refresh"></i></a>

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
                    <div class="table-responsive project-list">
                      <table class="table table-striped table-bordered table-hover dataTables-example text-center">
                            <thead class="cursor-pointer">
                              <tr>
                                <th class="notexport">SL No</th>
                                <th class="sorting_desc_disabled"> Exam Name <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>
                                <th class="sorting_desc_disabled">Franchise <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>  
                                <th class="sorting_desc_disabled">Course <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>
                                <th class="sorting_desc_disabled">Total Marks/Times</th>
                                 <th class="sorting_desc_disabled">Total Questions</th>      
                                 <th class="sorting_desc_disabled">Status <span class="footable-sort-indicator"><i class="fa fa-sort"></i></th>
                                <th class="sorting_desc_disabled notexport">Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                                 foreach($examDataArr as $index => $exam){

                                  if(strlen($exam->optional_pdf)>0 && file_exists(USER_UPLOAD_DIR.'exam/'.$exam->optional_pdf)){
                                     $optional_pdf = USER_UPLOAD_URL.'exam/'.$exam->optional_pdf;
                                  }else{
                                     $optional_pdf = null;
                                  }

                                  $total_time = '';

                                  if(!empty($exam->hours)){
                                      $total_time .= $exam->hours.'h ';
                                  }

                                  if(!empty($exam->minutes)){
                                      $total_time .= $exam->minutes.'m';
                                  }

                              ?> 
                                    <tr>
                                        <td><?=$index+1?></td>
                                                                               
                                        <td class="project-title" style="width:18%;">
                                            <strong><?=ucfirst($exam->name)?></strong>
                                            <br/>
                                            <small>Created <?=date('jS F, Y',strtotime($exam->created_at))?></small>
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Franchise taking exam: <?=$exam->center_name?>"><?=$exam->center_name?></span>
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Exam subject/course: <?=$exam->course_title?>"><?=$exam->course_title?></span>
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Total Exam Time: <?=$total_time?>"><?=$exam->total_marks?> / <?=$total_time?></span>
                                        </td>

                                        <td class="project-title">
                                            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Total Questions: <?=$exam->question_count?>"><?=$exam->question_count?></span>
                                        </td>

                                        <td class="project-status">
                                         <span class="label label-<?=($exam->record_status == 'active'?'primary':'danger')?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Exam Status: <?=ucfirst($exam->record_status)?>"><?=ucfirst($exam->record_status)?></span> 
                                        </td>

                                       <td>
                                          <a href="<?=SITE_URL.'?route=start_exam&exm_id='.$exam->id?>"><button class="btn btn-success btn-sm" id="start_exam" data-toggle="tooltip" data-placement="bottom" title="Start this Exam">Start</button></a>  
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


