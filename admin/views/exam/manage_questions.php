<?php
if (isset($_GET['exm_id'])) {
  $exam_id = $_GET['exm_id'];
  $examDetails = $pageContent['pageData']['exam_details'];
  $questions = $pageContent['pageData']['questions'];

  $total_time = '';

  if (!empty($examDetails->hours)) {
    $total_time .= $examDetails->hours . 'h ';
  }

  if (!empty($examDetails->minutes)) {
    $total_time .= $examDetails->minutes . 'm';
  }
} else {
  $exam_id = 'null';
  $examDetails = array();
  $questions = array();
}

if (!empty($questions)) {
  $questionIndex = count($questions);
} else {
  $questionIndex = 0;
}

$question_div_collapse = $_COOKIE['question_div_collapse'] == "true" ? "collapsed" : "open";

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
              <button type="button" class="btn btn-xs btn-white <?= ($_COOKIE['question_div_collapse'] == "true" ? "active" : "") ?>" id="collapse_question_divs">Collapse Question's Section</button>
              <button type="button" class="btn btn-xs btn-white <?= ($_COOKIE['question_div_collapse'] == "false" ? "active" : "") ?>" id="open_question_divs">Open Question's Section</button>
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
                  <a href="<?= SITE_URL . '?route=edit_exam&id=' . $examDetails->id ?>" data-toggle="tooltip" data-placement="bottom" title="Exam Title: <?= $examDetails->name ?>"><?= ucfirst($examDetails->name) ?></a>
                  <br />
                  <small>Created <?= date('jS F, Y', strtotime($examDetails->created_at)) ?></small>
                </td>

                <td class="project-title vertical-align-middle">
                  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Franchise taking exam: <?= $examDetails->center_name ?>"><?= $examDetails->center_name ?></span>
                </td>

                <td class="project-title vertical-align-middle">
                  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Exam subject/course: <?= $examDetails->course_title ?>"><?= $examDetails->course_title ?></span>
                </td>

                <td class="project-title vertical-align-middle">
                  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Total Questions: <?= $examDetails->question_count ?>" id="question_count"><?= $examDetails->question_count ?></span>
                </td>

                <td class="project-title vertical-align-middle">
                  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Total Exam Time: <?= $total_time ?>"><?= $examDetails->total_marks ?> / <?= $total_time ?></span>
                </td>

                <td class="project-status vertical-align-middle">
                  <span class="label label-<?= ($examDetails->record_status == 'active' ? 'primary' : 'danger') ?> cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Exam Status: <?= ucfirst($examDetails->record_status) ?>"><?= ucfirst($examDetails->record_status) ?></span>
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
                  <input type="hidden" name="action" id="action" value="manageImportData">
                  <input type="hidden" name="import_table" value="exam_questions">

                  <div class="btn-group">
                    <label title="Upload a file" for="importDataCSV" class="btn btn-primary">
                      <input type="file" accept="application/vnd.openxmlformats-officedoc.sheet" id="importDataCSV" name="import_data_file" class="hide" />
                      --Upload questions by uploading a csv or xls file with proper data format--
                    </label>
                  </div>

                  <button type="submit" class="btn btn-lg btn-success ml-2 mb-2" name="import_data_submit" id="import_data_submit" class="button" value="Import Data" disabled><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Import Data</button>

                  <a href="<?= RESOURCE_URL . 'importSampleCSV/sample-questions.xlsx' ?>" class="btn btn-primary btn-lg ml-2 mb-2" download>
                    <i class="fa fa-download"> </i> Sample CSV
                    <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="top" title="Download sample CSV format and strickly follow it to import bulk data"><i class="fa fa-question-circle"></i></span>
                  </a>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="alert alert-warning <?= (!empty($questions) ? 'd-none' : '') ?>" role="alert" id="add_first_question">
        <div class="d-flex justify-content-between">
          <span><strong>Warning!</strong> No question is added yet for this exam, please add questions by clicking on the add your first question button besides.</span>
          <button class="btn btn-success btn-sm"><i class="fa fa-plus-circle"> Add Your First Question</i></button>
        </div>
      </div>
    </div>
  </div>

  <div class="row" id="question_list_div" <?= (!empty($questions) ? '' : 'd-none') ?>>
    <div class="col-lg-12">
      <div class="ibox <?= $question_div_collapse ?>">
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
          <div class="col-lg-12 col-md-12 col-sm-12">
            <ul class="unstyled utf_footer_social" id="question_child_list">

            </ul>
          </div>
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
        <input type="hidden" name="exam_id" id="exam_id" value="<?= $exam_id ?>">

        <div class="form-action-btns" <?= (!empty($questions) ? '' : 'd-none') ?>>
          <div class="text-left">
            <a href="<?= SITE_URL ?>?route=view_exams" data-toggle="tooltip" title="Cancel">
              <button type="button" class="btn btn-warning btn-sm"><i class="fa fa-reply"></i></button>
            </a>

            <button class="btn btn-primary btn-sm" id="saveQuestions" type="submit" class="btn btn-success" data-toggle="tooltip" title="Save" data-placement="bottom"><i class="fa fa-save"></i> Save Questions</button>
          </div>

          <div class="text-center">
            <a href="javascript:void(0);" id="browse_question_list" data-toggle="tooltip" title="Browse Question List">
              <button type="button" class="btn btn-info btn-sm"><i class="fa fa-question-circle"></i> Question List</button>
            </a>
          </div>

          <div class="text-right">
            <button class="btn btn-success" id="add_more">
              <i class="fa fa-plus-circle"></i> Add a New Question
            </button>
          </div>
        </div>

        <div id="main_question_container"></div>

      </form>

      <div class="text-center my-3">
        <button id="load_more_btn" class="btn btn-primary px-4">
          <span class="btn-text">Load More Questions</span>
          <span class="spinner-border spinner-border-sm ms-2 d-none" role="status"></span>
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Custom JS -->
<script>
  let questionOffset = 0;
  const questionLimit = 10;

  let isLoadingQuestions = false;
  let hasMoreQuestions = true;

  let initialSnapshot = "";

  var exam_id = $("#exam_id").val();
  var div_top = $('.form-action-btns').offset().top;

  window.addEventListener("beforeunload", function(e) {

    if (hasUnsavedChanges()) {
      e.preventDefault();
      const dialogText = "You have unsaved progressed, are you realy sure you want to leave?";
      e.returnValue = dialogText;
      return dialogText;
    }
  });

  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
  }

  var question_div_collapse = getCookie('question_div_collapse');

  function getQuestionCount() {
    return $("#main_question_container .question_div").length;
  }

  function createSnapshot() {
    initialSnapshot = JSON.stringify(serializeQuestions());
  }

  function serializeQuestions() {
    const data = [];

    $("#main_question_container .question_div").each(function() {
      const $q = $(this);

      data.push({
        ques: $q.find(".ques").val()?.trim() || "",
        opt1: $q.find(".opt1").val()?.trim() || "",
        opt2: $q.find(".opt2").val()?.trim() || "",
        opt3: $q.find(".opt3").val()?.trim() || "",
        opt4: $q.find(".opt4").val()?.trim() || "",
        cor_ans: $q.find(".cor_ans").val() || "",
        marks: $q.find(".marks").val() || "",
        record_status: $q.find(".record_status").val() || ""
      });
    });

    return data;
  }

  function hasUnsavedChanges() {
    const current = JSON.stringify(serializeQuestions());
    return current !== initialSnapshot;
  }

  function arrangeQuestionList() {
    let questionCount = $('.question_div').length;
    let html = '';

    for (let i = 1; i <= questionCount; i++) {
      html += `
            <li>
                <a href="javascript:void(0);" 
                   id="question_no_${i}"
                   class="browse-question attempted"
                   data-did="${i}">
                    <text>${i}</text>
                </a>
            </li>`;
    }

    $("#question_child_list").html(html);
  }

  function buildQuestionHTML(question, index, collapsed = 'open') {

    const divIndex = index + 1;

    const rstatus_active_selected = question.record_status === "active" ? "selected" : "";
    const rstatus_inactive_selected = question.record_status === "blocked" ? "selected" : "";

    return `
      <div id="questions-${question.id || ''}">
        <div class="ibox question_div ${collapsed}" id="question_div_${divIndex}" data-dirty="false" data-rid="${question.id}">
            <div class="ibox-title">
                <div id="question_header_${divIndex}" class="question-header">
                    <h5>Question No ${divIndex}</h5>
                </div>
                <div class="ibox-tools">
                    <a href="javascript:void(0);" data-divid="${divIndex}" class="clone-question dynamicQuestion">
                        <span class="badge badge-primary p-1"><i class="fa fa-clone"></i> Clone This Question</span>
                    </a>
                    <a href="javascript:void(0);" data-divid="${divIndex}" data-rid="${question.id}" class="remove-question dynamicQuestion">
                        <span class="badge badge-danger p-1"><i class="fa fa-minus-circle"></i> Remove Question</span>
                    </a>
                    <a class="collapse-question-div" data-cstatus="${collapsed}">
                        <span class="badge badge-warning p-1"><i class="fa fa-chevron-up"></i> Toggle Question</span>
                    </a>
                </div>
            </div>

            <div class="ibox-content content_div_loader">

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-right">Question</label>
                    <div class="col-sm-10">
                        <textarea class="form-control ques" name="questions[${index}][ques]" required>${question.ques || ''}</textarea>
                    </div>
                </div>

                <div class="hr-line-dashed"></div>

                ${['opt1','opt2','opt3','opt4'].map((opt, i) => `
                    <div class="form-group row">
                        <label class="col-sm-2 col-form-label text-right">Option ${i+1}</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control ${opt}" 
                                name="questions[${index}][${opt}]" 
                                value="${question[opt] || ''}" ${i < 2 ? 'required' : ''}>
                        </div>
                    </div>
                    <div class="hr-line-dashed"></div>
                `).join('')}

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-right">Question Status</label>
                    <div class="col-sm-10">
                        <select class="form-control record_status" name="questions[${index}][record_status]">
                            <option disabled>Select question status</option>
                            <option value="active" ${rstatus_active_selected}>Active</option>
                            <option value="blocked" ${rstatus_inactive_selected}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label text-right">Correct Answer</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control cor_ans" name="questions[${index}][cor_ans]" value="${question.cor_ans || ''}" required>
                    </div>

                    <label class="col-sm-2 col-form-label text-right">Question Marks</label>
                    <div class="col-sm-4">
                        <input type="number" class="form-control marks" name="questions[${index}][marks]" value="${question.marks || ''}" required>
                    </div>
                </div>

            </div>
        </div>
    </div>`;
  }

  function reindexQuestions() {
    const $container = $("#main_question_container");

    $container.find(".question_div").each(function(i) {
      const divIndex = i + 1; // UI numbering
      const qIndex = i; // form naming

      const $el = $(this);

      // Update main container ID
      $el.attr("id", "question_div_" + divIndex);

      // Update header
      $el.find(".question-header")
        .attr("id", "question_header_" + divIndex)
        .html("<h5>Question No " + divIndex + "</h5>");

      // Update action buttons
      $el.find(".clone-question").data("divid", divIndex);
      $el.find(".remove-question").data("divid", divIndex);

      // Update input names
      $el.find(".ques").attr("name", `questions[${qIndex}][ques]`);
      $el.find(".opt1").attr("name", `questions[${qIndex}][opt1]`);
      $el.find(".opt2").attr("name", `questions[${qIndex}][opt2]`);
      $el.find(".opt3").attr("name", `questions[${qIndex}][opt3]`);
      $el.find(".opt4").attr("name", `questions[${qIndex}][opt4]`);
      $el.find(".cor_ans").attr("name", `questions[${qIndex}][cor_ans]`);
      $el.find(".marks").attr("name", `questions[${qIndex}][marks]`);
      $el.find(".record_status").attr("name", `questions[${qIndex}][record_status]`);
    });

    // Update total count UI
    $("#question_count").text($container.find(".question_div").length);
  }

  function fetchAllQuestions() {
    var formData = {
      exam_id: exam_id,
      action: "fetchAllQuestions"
    }

    ajaxRequest(formData, ajaxControllerHandler, function(data) {
      //console.log(data);return false;

      if (data.check === 'success') {

        index = 0;
        const questions = data.questions || [];
        const collapsed = question_div_collapse === 'true' ? 'collapsed' : 'open';

        if (questions.length > 0) {

          const html = questions.map((q, i) => {
            return buildQuestionHTML(q, i, collapsed);
          }).join('');

          $("#main_question_container").html(html);

          arrangeQuestionList();
          $("#question_count").text(questions.length);

          toastr.success("All questions are successfully fetched", "Success");

          $(".form-action-btns").removeClass("d-none");
          $("#question_list_div").removeClass("d-none");
          $("#add_first_question").addClass("d-none");

          var qIndex = getQuestionCount();

        } else {

          $("#main_question_container").html('');
          $("#question_count").text('0');

          toastr.warning("No question was found", "Warning");

          arrangeQuestionList();

          $(".form-action-btns").addClass("d-none");
          $("#question_list_div").addClass("d-none");
          $("#add_first_question").removeClass("d-none");
        }

      } else {

        $('.content_div_loader').removeClass('sk-loading');

        const message = data.message?.length ? data.message : "Something went wrong";
        toastr.error(message, "Error!");
      }
    });

  }

  function loadQuestions({
    reset = false
  } = {}) {

    if (isLoadingQuestions || !hasMoreQuestions) return;

    isLoadingQuestions = true;

    if (reset) {
      questionOffset = 0;
      hasMoreQuestions = true;
      $("#main_question_container").html('');
      $("#load_more_btn").removeClass('d-none');
    }

    ajaxRequest({
      action: "fetchQuestions",
      exam_id: exam_id,
      offset: questionOffset,
      limit: questionLimit
    }, ajaxControllerHandler, function(data) {

      isLoadingQuestions = false;

      if (data.check !== 'success') {
        toastr.error(data.message || "Failed to load questions");
        return;
      }

      const questions = data.questions || [];
      const collapsed = question_div_collapse === 'true' ? 'collapsed' : 'open';

      // No more data
      if (questions.length < questionLimit) {
        hasMoreQuestions = false;
        //$("#load_more_btn").addClass('d-none');
      }

      // Render questions
      questions.forEach((question) => {

        const index = getQuestionCount();

        const html = buildQuestionHTML(question, index, collapsed);

        $("#main_question_container").append(html);
      });

      questionOffset = getQuestionCount();

      arrangeQuestionList();
      createSnapshot();

      // UI updates
      const total = getQuestionCount();

      if (total > 0) {
        $(".form-action-btns").removeClass("d-none");
        $("#question_list_div").removeClass("d-none");
        $("#add_first_question").addClass("d-none");
      } else {
        $("#add_first_question").removeClass("d-none");
      }

    });
  }

  function buildPayload() {
    const create = [];
    const update = [];

    $(".question_div").each(function(index) {
      const $q = $(this);

      const rid = $q.attr('data-rid');
      const isDirty = $q.attr('data-dirty') === 'true';

      const questionData = {
        ordering: index + 1,
        ques: $q.find('.ques').val(),
        opt1: $q.find('.opt1').val(),
        opt2: $q.find('.opt2').val(),
        opt3: $q.find('.opt3').val(),
        opt4: $q.find('.opt4').val(),
        cor_ans: $q.find('.cor_ans').val(),
        marks: $q.find('.marks').val(),
        record_status: $q.find('.record_status').val()
      };

      if (rid) {
        // existing question
        if (isDirty) {
          questionData.id = rid;
          update.push(questionData);
        }
      } else {
        // new or cloned
        create.push(questionData);
      }
    });

    return {
      create,
      update
    };
  }

  $(window).on('scroll', function() {
    $('.form-action-btns').toggleClass(
      'sticky',
      $(window).scrollTop() > div_top
    );
  });

  $(document).ready(function() {

    loadQuestions({
      reset: true
    });

    //Bind tooltip on dynamic meta elements
    $('body').tooltip({
      selector: '.dynamicQuestion'
    });

    $(document).on('click', '#load_more_btn', function(e) {

      if (hasUnsavedChanges()) {
        toastr.warning("Please save changes before loading more.");
        return;
      }

      loadQuestions({
        reset: false
      });
    });

    $(document).on('click', '.clone-question', function(e) {
      e.preventDefault();

      const divId = $(this).data('divid');
      const qIndex = getQuestionCount();
      const divIndex = qIndex + 1;
      const targetDiv = 'question_header_' + divIndex;

      const $source = $("#question_div_" + divId);
      const $clone = $source.clone(true);

      // Update main container ID
      $clone.attr('id', "question_div_" + divIndex).addClass("mt-3");

      // Update header
      $clone.find('.question-header')
        .attr('id', targetDiv)
        .data('divid', divIndex)
        .html(`<h5>Question No ${divIndex}</h5>`);

      // Update action buttons
      $clone.find('.clone-question, .remove-question')
        .data('divid', divIndex);

      // Update all input names dynamically
      $clone.find('[name]').each(function() {
        const name = $(this).attr('name');
        if (name) {
          const updatedName = name.replace(/questions\[\d+\]/, `questions[${qIndex}]`);
          $(this).attr('name', updatedName);
        }
      });

      $clone.removeAttr('data-rid');
      $clone.find('[data-rid]').removeAttr('data-rid');

      const tempId = 'temp_' + Date.now();
      $clone.attr('data-temp-id', tempId);

      // Append once
      $("#main_question_container").append($clone);

      // Scroll
      document.getElementById(targetDiv)?.scrollIntoView({
        behavior: "smooth"
      });

      arrangeQuestionList();
    });

    $(document).on('click', '#add_more, #add_first_question', function(e) {
      e.preventDefault();

      console.log(index);

      const qIndex = getQuestionCount();
      const divIndex = qIndex + 1;

      // First question UI toggle
      if (qIndex === 0) {
        $(".form-action-btns").removeClass("d-none");
        $("#question_list_div").removeClass("d-none");
        $("#add_first_question").addClass("d-none");
      }

      const collapsed = question_div_collapse === 'true' ? 'collapsed' : 'open';
      const targetDiv = 'question_header_' + divIndex;

      // Empty question object (important)
      const emptyQuestion = {
        id: '',
        ques: '',
        opt1: '',
        opt2: '',
        opt3: '',
        opt4: '',
        cor_ans: '',
        marks: '',
        record_status: ''
      };

      // Reuse builder function from fetchAllQuestions
      const html = buildQuestionHTML(emptyQuestion, qIndex, collapsed);

      $("#main_question_container").append(html);

      arrangeQuestionList();

      document.getElementById(targetDiv)?.scrollIntoView({
        behavior: "smooth"
      });

    });

    //handling import data div
    $(document).on('click', '.handle_import_div', function() {
      var handle_type = $(this).data('htype');
      if (handle_type == "show") {
        $('#import_data_div').removeClass('d-none');
      } else {
        $('#import_data_div').addClass('d-none');
      }
      return true;
    });

    $(document).on('blur', '.cor_ans', function(e) {
      var cor_ans = $(this).val();

      if (cor_ans < 1 || cor_ans > 4) {
        toastr.error("Correct option should be between 1 - 4.", 'Error!');
        $("#saveQuestions").prop('disabled', true);
        return false;
      } else {
        $("#saveQuestions").prop('disabled', false);
        return true;
      }
    });

    $(document).on('click', '.remove-question', function(e) {
      e.preventDefault();

      const $btn = $(this);
      const $questionDiv = $btn.closest(".question_div");
      const $container = $("#main_question_container");

      const rid = $btn.attr('data-rid');

      swal({
        title: "Are you sure?",
        text: "Are you sure to delete this question?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, Go ahead!",
        closeOnConfirm: true
      }, function() {

        // Prevent double click
        $btn.prop("disabled", true);

        // CASE 1: Existing DB record → DB delete
        if (rid) {

          const formData = {
            action: "deleteGlobalData",
            type: "exam_questions",
            row_id: rid
          };

          ajaxRequest(formData, ajaxControllerHandler, function(data) {

            $btn.prop("disabled", false);

            if (data.check === 'success') {
              handlePostDeleteUI();
              toastr.success(data.message || "Question deleted successfully", "Success!");
            } else {
              toastr.error(data.message || "Something went wrong", "Error!");
            }
          });

        }
        // CASE 2: New/Cloned → DOM only
        else {
          handlePostDeleteUI();
          $btn.prop("disabled", false);
        }

        // Common UI handler
        function handlePostDeleteUI() {
          $questionDiv.remove();

          const questionCount = getQuestionCount();

          // Unified UI toggle
          $(".form-action-btns").toggleClass("d-none", questionCount === 0);
          $("#question_list_div").toggleClass("d-none", questionCount === 0);
          $("#add_first_question").toggleClass("d-none", questionCount !== 0);

          if (questionCount > 0) {
            reindexQuestions();
            arrangeQuestionList();

            const targetId = questionCount > 1 ?
              `question_header_${questionCount}` :
              "main_question_container";

            document.getElementById(targetId)?.scrollIntoView({
              behavior: "smooth"
            });
          }
        }

      });
    });

    $(document).on('input change', '.question_div input, .question_div textarea, .question_div select', function() {
      if (hasUnsavedChanges()) {
        $(this).closest('.question_div').attr('data-dirty', 'true');
        $("#saveQuestions").addClass("btn-warning");
        $("#load_more_btn").prop("disabled", false);
      } else {
        $(this).closest('.question_div').attr('data-dirty', 'false');
        $("#saveQuestions").removeClass("btn-warning");
        $("#load_more_btn").prop("disabled", false);
      }
    });

    $(document).on('click', '#browse_question_list', function(e) {
      document.getElementById("question_list_div").scrollIntoView({
        behavior: "smooth"
      });
    });

    $(document).on('click', '#collapse_question_divs', function(e) {

      //Set User Preference in Cookie
      document.cookie = "question_div_collapse=true";
      question_div_collapse = 'true';

      $(this).addClass('active');

      //Show Loader
      $('.content_div_loader').addClass('sk-loading');

      setTimeout(function() {

        $(".question_div").each(function() {
          $(this).addClass('collapsed');
        });

        $(".collapse-question-div").each(function() {
          $(this).data('cstatus', 'collapsed');
        });

        $("#question_list_div").find('.ibox').addClass('collapsed');

        $("#open_question_divs").removeClass('active');

        $('.content_div_loader').removeClass('sk-loading');

      }, 1000);

    });

    $(document).on('click', '#open_question_divs', function(e) {

      //Set User Preference in Cookie
      document.cookie = "question_div_collapse=false";
      question_div_collapse = 'false';

      //Show Loader
      $('.content_div_loader').addClass('sk-loading');

      $(this).addClass('active');

      setTimeout(function() {

        $(".question_div").each(function() {
          $(this).removeClass('collapsed');
        });

        $(".collapse-question-div").each(function() {
          $(this).data('cstatus', 'open');
        });

        $("#question_list_div").find('.ibox').removeClass('collapsed');

        $("#collapse_question_divs").removeClass('active');

        $('.content_div_loader').removeClass('sk-loading');

      }, 1000);

    });

    $(document).on('click', '.collapse-question-div', function(e) {
      var collapse_status = $(this).data('cstatus');

      if (collapse_status == "open") {
        $(this).data('cstatus', 'collapsed');
        $(this).parent().parent().parent().addClass("collapsed");
      } else {
        $(this).data('cstatus', 'open');
        $(this).parent().parent().parent().removeClass("collapsed");
      }
    });

    $(document).on('click', '.browse-question', function(e) {
      var divId = $(this).data('did');
      var targetDiv = divId > 2 ? 'question_header_' + (divId - 2) : "main_question_container";

      console.log(targetDiv);

      document.getElementById(targetDiv).scrollIntoView({
        behavior: "smooth"
      });
    });

    $('#main_question_container').sortable({
      //handle: ".draggable",
      update: function(event, ui) {
        var formData = $(this).sortable('serialize');

        formData += '&exam_id=' + exam_id + '&action=sortExamQuestions';

        //POST to server using $.post or $.ajax
        $.ajax({
          data: formData,
          method: 'POST',
          url: ajaxControllerHandler,
          beforeSend: function() {
            //Display loader
            // $('.overlayer').fadeIn();
            // $('.content_div_loader').addClass('sk-loading');
          },
          success: function(responseData) {
            var data = JSON.parse(responseData);
            //console.log(data);

            $('.overlayer').fadeOut();
            $('.content_div_loader').removeClass('sk-loading');

            if (data.check == "success") {
              toastr.success("Oreding is successfully completed", 'Success!');

              reindexQuestions();
              //loadQuestions({ reset: true });

            } else {
              toastr.error(data.message, 'Error!');
            }
          }
        });
      }
    });

    $(document).on('submit', '#manage_exam_questions_form', function(event) {
      event.preventDefault();

      const payload = buildPayload();
      // add extra required data
      payload.exam_id = exam_id;

      console.log(payload);return;

      $.ajax({
        url: ajaxControllerHandler,
        method: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function() {
          $('.content_div_loader').addClass('sk-loading');
          $('.overlayer').fadeIn();
          $('#saveQuestions').attr('disabled', true);
        },
        success: function(responseData) {
          var data = JSON.parse(responseData);
          $('#saveQuestions').attr('disabled', false);
          $('body>.tooltip').remove();
          //console.log(responseData);
          if (data.check == 'success') {
            //Disabling loader
            $('.content_div_loader').removeClass('sk-loading');
            $('.overlayer').fadeOut();

            swal({
              title: "Great!",
              text: "Questions are successfully saved",
              type: "success"
            }, function() {
              loadQuestions();
              createSnapshot();
            });
            return true;
          } else {
            //Disabling loader
            $('.content_div_loader').removeClass('sk-loading');
            //show sweetalert success
            if (data.message.length > 0) {
              var message = data.message;
            } else {
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


    $(document).on('click', '#delete_all_questions', function(event) {
      event.preventDefault();

      var formData = {
        exam_id: exam_id,
        action: "deleteAllQuestions"
      }

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
            url: ajaxControllerHandler,
            method: 'POST',
            data: formData,
            beforeSend: function() {
              $('.content_div_loader').addClass('sk-loading');
              $('.overlayer').fadeIn();
              $(this).attr('disabled', true);
            },
            success: function(responseData) {
              var data = JSON.parse(responseData);
              $(this).attr('disabled', false);
              //console.log(responseData);
              if (data.check == 'success') {

                setTimeout(function() {
                  //Disabling loader
                  $('.content_div_loader').removeClass('sk-loading');
                  $('.overlayer').fadeOut();

                  $("#main_question_container").html('');

                  $(".form-action-btns").addClass("d-none");
                  $("#question_list_div").addClass("d-none");
                  $("#add_first_question").removeClass("d-none");

                  toastr.success("All questions are successfully deleted", "Success");

                  setTimeout(function() {
                    fetchAllQuestions();
                  }, 1000);

                  return true;
                }, 1000);

              } else {
                //Disabling loader
                $('.content_div_loader').removeClass('sk-loading');
                //show sweetalert success
                if (data.message.length > 0) {
                  var message = data.message;
                } else {
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
    $(document).on('change', '#importDataCSV', function() {
      var file = this.files[0];
      var fileType = file["type"];
      //console.log(fileType);return false;
      var validDocTypes = ["application/vnd.ms-excel", "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"];
      if ($.inArray(fileType, validDocTypes) < 0) {
        toastr.error("Only csv file allowed!", "Upload error!");
        $(this).val('');
        $('#import_data_submit').attr('disabled', true);
        return false;
      } else {
        $('#import_data_submit').attr('disabled', false);
        return false;
      }
    });

    //EXAM'S QUESTIONS IMPORT FORM SUBMIT HANDLER
    $(document).on('submit', '#import_table_data_form', function(e) {
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
            url: ajaxControllerHandler,
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
              //  $('.content_div_loader').addClass('sk-loading');
              //  $('.overlayer').fadeIn();
              //  $('#import_data_submit').prop('disabled',true);
            },
            success: function(responseData) {
              setTimeout(function() {
                $('.content_div_loader').removeClass('sk-loading');
                $('.overlayer').fadeOut();

                $('#import_table_data_form')[0].reset();

                var data = JSON.parse(responseData);
                //console.log(responseData);
                if (data.check == 'success') {

                  toastr.success(data.message, 'Success!');

                  setTimeout(function() {
                    fetchAllQuestions();
                  }, 1000);

                  return true;
                } else {
                  if (data.message.length > 0) {
                    var message = data.message;
                  } else {
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