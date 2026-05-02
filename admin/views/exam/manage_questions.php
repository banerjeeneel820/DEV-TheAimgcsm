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
            <button type="button" class="btn btn-primary btn-xs" onclick=" loadQuestions({ page: currentPage,append: false });">
              <i class="fa fa-refresh"> </i> Referesh Questions
            </button>

            <button type="button" class="btn btn-xs btn-danger handle_import_div" data-htype="show">
              <i class="fa fa-file-excel-o"> </i> Import Data in CSV Format
            </button>

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
                  <button class="btn btn-danger btn-sm" id="delete_all_questions" disabled="<?= (!empty($questions) ? 'false' : 'true') ?>" data-toggle="tooltip" data-placement="bottom" title="Delete all Question">Delete</button>
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
            <ul class="unstyled utf_footer_social" id="question_child_list"></ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row" id="search_question_div" <?= (!empty($questions) ? '' : 'd-none') ?>>
    <div class="col-lg-12">
      <div class="ibox">
        <div class="ibox-title">
          <h5>Search Question</h5>
          <div class="ibox-tools">
            <a class="collapse-link">
              <i class="fa fa-chevron-up"></i>
            </a>
          </div>
        </div>
        <div class="ibox-content">
          <form id="search_question_form" onsubmit="return false;">
            <div class="row">
              <div class="col-lg-9 col-md-9 col-sm-12 m-b-xs">
                <input type="text" class="form-control" name="search_string" id="search_string" placeholder="Search question by text...">
              </div>

              <div class="col-lg-3 col-md-3 col-sm-12">
                <button class="btn btn-primary p-2" type="submit"><i class="fa fa-search"></i>&nbsp;Search Question</button>
                <button class="btn btn-danger p-2 d-none" type="button" id="remove_question_filter" data-toggle="tooltip" title="Remove current search filter">
                  <i class="fa fa-times"></i>&nbsp;Remove
                </button>
              </div>
            </div>
          </form>
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

        <div class="fab-outer" id="question_action_div">
          <div class="form-action-btns">

            <!-- LEFT -->
            <div class="fab-group fab-left">

              <input type="checkbox" id="select_all_questions" class="fab-checkbox">

              <a href="<?= SITE_URL ?>?route=view_exams" data-toggle="tooltip" title="Cancel">
                <button type="button" class="btn btn-warning">
                  <i class="fa fa-reply"></i>
                </button>
              </a>

              <button class="btn btn-primary btn-sm" id="saveQuestions" type="submit">
                <i class="fa fa-save"></i> Save Questions
              </button>

            </div>

            <!-- CENTER -->
            <div class="fab-group fab-center">
              <a href="javascript:void(0);" id="browse_question_list" data-toggle="tooltip" title="Browse Question List">
                <button type="button" class="btn btn-info btn-sm">
                  <i class="fa fa-question-circle"></i> Question List
                </button>
              </a>
            </div>

            <!-- RIGHT -->
            <div class="fab-group fab-right">

              <a href="javascript:void(0);" class="btn btn-primary d-none" id="show_question_list">
                <i class="fa fa-list"></i> Show Question List
              </a>

              <a href="javascript:void(0);" class="btn btn-danger d-none" id="delete_selected_question">
                <i class="fa fa-trash"></i> Delete Selected Questions
              </a>

              <button type="submit" class="btn btn-success" id="add_more">
                <i class="fa fa-plus-circle"></i> Add a New Question
              </button>

            </div>

          </div>
        </div>

        <div id="main_question_container"></div>

        <div id="latest_changes_container" class="mt-4 d-none">
          <h4>Latest Changes</h4>
          <div id="latest_changes_container"></div>
        </div>

        <div class="ibox <?= (!empty($questions) ? '' : 'd-none') ?>" id="empty_question_container">

          <div class="ibox-title">
            <h5>Empty Question Action</h5>
          </div>

          <div class="ibox-content">

            <div class="alert alert-danger text-center font-weight-bold mt-4 d-none" role="alert" id="empty_question_msg">
              <span><strong>Note!</strong>&nbsp;No question was found for your search criteria</span>
            </div>

            <div class="alert alert-warning mt-4 <?= (!empty($questions) ? 'd-none' : '') ?>" role="alert" id="add_first_question">
              <div class="d-flex justify-content-between">
                <span><strong>Warning!</strong>&nbsp;No question is added yet for this exam, please add questions by clicking on the add your first question button besides.</span>
                <button class="btn btn-success btn-sm"><i class="fa fa-plus-circle"> Add Your First Question</i></button>
              </div>
            </div>

          </div>
        </div>

      </form>

      <div id="pagination_container" class="text-center my-3"></div>

    </div>
  </div>
</div>

<!-- Custom JS -->
<script>
  let currentPage = 1;
  const questionLimit = 10;
  let totalQuestionCount = 0;

  let isLoadingQuestions = false;
  let hasMoreQuestions = true;

  let initialSnapshot = "";

  var exam_id = $("#exam_id").val();
  var div_top = $('.form-action-btns').offset().top;

  const badgeMap = {
    created: `<span class="badge badge-success ml-2 q-badge" data-type="created">New</span>`,
    updated: `<span class="badge badge-info ml-2 q-badge" data-type="updated">Updated</span>`,
    cloned: `<span class="badge badge-primary ml-2 q-badge" data-type="cloned">Cloned</span>`
  };

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

  function applyBadge($questionDiv, type) {

    const $header = $questionDiv.find(".question-header");

    // ✅ store state (IMPORTANT)
    $questionDiv.attr("data-badge", type);

    // Remove conflicting badges
    if (type === 'updated') {
      $header.find('.q-badge[data-type="created"]').remove();
    }

    // Avoid duplicate
    if ($header.find(`.q-badge[data-type="${type}"]`).length) {
      return;
    }

    // Append badge
    $header.find("h5").append(badgeMap[type]);
  }

  function getQuestionCount() {
    return $("#main_question_container .question_div").length;
  }

  function serializeQuestions(includeNew = false) {

    const data = {};

    const $activeContainer = !$("#latest_changes_container").hasClass("d-none") ?
      $("#latest_changes_container") :
      $("#main_question_container");

    $activeContainer.find(".question_div").each(function() {

      const $q = $(this);

      const rid = $q.attr("data-rid");
      const tempId = $q.attr("data-temp-id");

      // =========================
      // SKIP NEW (for snapshot)
      // =========================
      if (!includeNew && !rid) return;

      const key = rid || tempId; // unique identifier

      data[key] = {
        ques: ($q.find(".ques").val() || "").trim(),
        opt1: ($q.find(".opt1").val() || "").trim(),
        opt2: ($q.find(".opt2").val() || "").trim(),
        opt3: ($q.find(".opt3").val() || "").trim(),
        opt4: ($q.find(".opt4").val() || "").trim(),
        cor_ans: String($q.find(".cor_ans").val() || ""),
        marks: String($q.find(".marks").val() || ""),
        record_status: $q.find(".record_status").val() || ""
      };

    });

    return data;
  }

  function createSnapshot() {
    initialSnapshot = JSON.stringify(serializeQuestions(false));
  }

  function getQuestionDiff() {
    const current = serializeQuestions({
      includeNew: true
    });
    const initial = JSON.parse(initialSnapshot || "{}");

    const changes = [];

    // Detect removed + updated
    Object.keys(initial).forEach((key) => {

      // removed
      if (!current[key]) {
        changes.push({
          type: "removed",
          id: key,
          data: initial[key]
        });
        return;
      }

      const oldQ = initial[key];
      const newQ = current[key];

      // field-level compare
      Object.keys(newQ).forEach((field) => {
        if ((oldQ[field] || "") !== (newQ[field] || "")) {
          changes.push({
            type: "updated",
            id: key,
            field: field,
            before: oldQ[field],
            after: newQ[field]
          });
        }
      });
    });

    // Detect added (new + cloned)
    Object.keys(current).forEach((key) => {
      if (!initial[key]) {
        changes.push({
          type: "added",
          id: key,
          data: current[key]
        });
      }
    });

    return changes;
  }

  function hasUnsavedChanges() {
    const current = JSON.stringify(serializeQuestions(true));
    const initial = initialSnapshot || "{}";

    const diff = getQuestionDiff();
    console.log(diff);

    return current !== initial;
  }

  function getSnapshotArray() {
    try {
      return JSON.parse(initialSnapshot || "{}");
    } catch (e) {
      return {};
    }
  }

  function getQuestionData($questionDiv) {
    return {
      ques: $questionDiv.find(".ques").val()?.trim() || "",
      opt1: $questionDiv.find(".opt1").val()?.trim() || "",
      opt2: $questionDiv.find(".opt2").val()?.trim() || "",
      opt3: $questionDiv.find(".opt3").val()?.trim() || "",
      opt4: $questionDiv.find(".opt4").val()?.trim() || "",
      cor_ans: $questionDiv.find(".cor_ans").val() || "",
      marks: $questionDiv.find(".marks").val() || "",
      record_status: $questionDiv.find(".record_status").val() || ""
    };
  }

  function isQuestionChanged($questionDiv) {

    const currentData = getQuestionData($questionDiv);

    const rid = $questionDiv.attr("data-rid");
    const tempId = $questionDiv.attr("data-temp-id");

    const snapshot = getSnapshotArray();

    // =========================
    // NEW / CLONED
    // =========================
    if (!rid) {
      return true; // always treated as changed
    }

    const originalData = snapshot[rid];

    // Safety fallback
    if (!originalData) return true;

    return JSON.stringify(currentData) !== JSON.stringify(originalData);
  }

  function arrangeQuestionList() {
    let questionCount = $("#main_question_container .question_div").length;
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

  function arrangeQLatestuestionList() {
    let questionCount = $("#latest_changes_container .question_div").length;
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

  function buildQuestionHTML(question = {}, index = 0, collapsed = 'open', type = null) {

    const divIndex = index + 1;

    // Basic escape (important for safety)
    const esc = (str) => String(str || '').replace(/[&<>"']/g, m => ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#39;'
    } [m]));

    // Status selection
    const isActive = question.record_status === "active";
    const isBlocked = question.record_status === "blocked";

    // Badge
    const badgeMap = {
      created: `<span class="badge badge-success ml-2">New</span>`,
      updated: `<span class="badge badge-info ml-2">Updated</span>`
    };
    const badge = badgeMap[type] || '';

    // Actions (hide for created/updated preview)
    const showActions = !['created', 'updated'].includes(type);

    const actionButtons = showActions ? `
      <a href="javascript:void(0);" data-divid="${divIndex}" class="clone-question dynamicQuestion">
        <span class="badge badge-primary p-2"><i class="fa fa-clone"></i></span>
      </a>
      <a href="javascript:void(0);" data-divid="${divIndex}" data-rid="${question.id || ''}" class="remove-question dynamicQuestion">
        <span class="badge badge-danger p-2"><i class="fa fa-trash"></i></span>
      </a>
    ` : '';

    // Options generator
    const optionsHTML = ['opt1', 'opt2', 'opt3', 'opt4'].map((opt, i) => {
      const required = i < 4 ? 'required' : '';
      const star = i < 4 ? '<span class="text-danger">*</span>' : '';

      return `
    <div class="form-group row">
      <label class="col-sm-2 col-form-label text-right">
        Option ${i + 1} ${star}
      </label>
      <div class="col-sm-10">
        <textarea
          class="form-control ${opt}"
          name="questions[${index}][${opt}]"
          rows="2"
          placeholder="Enter option ${i + 1}..."
          ${required}>${esc(question[opt])}</textarea>
      </div>
    </div>
    <div class="hr-line-dashed"></div>
  `;
    }).join('');

    return `
      <div id="questions-${question.id || ''}">
        <div class="ibox question_div ${collapsed} mt-3 shadow-sm rounded"
          id="question_div_${divIndex}"
          data-dirty="false"
          data-rid="${question.id || ''}">

          <!-- HEADER -->
          <div class="ibox-title d-flex justify-content-between align-items-center">

          <div class="d-flex align-items-center gap-2">

            <input type="checkbox" 
              class="question-select-checkbox fab-checkbox mr-2 mb-1"
              data-divid="${divIndex}" data-rid="${question.id || ''}">
            
              <div id="question_header_${divIndex}" class="question-header">
                <h5 class="mb-0">
                  Question No ${divIndex}: ${esc(question.ques)} ${badge}
                </h5>
              </div>
            </div>  

            <div class="ibox-tools d-flex gap-2">
              ${actionButtons}
              <a class="collapse-question-div" data-cstatus="${collapsed}">
                <span class="badge badge-warning p-2">
                  <i class="fa fa-chevron-up"></i>
                </span>
              </a>
            </div>
          </div>

          <!-- BODY -->
          <div class="ibox-content content_div_loader">

            <!-- QUESTION -->
            <div class="form-group row">
              <label class="col-sm-2 col-form-label text-right font-weight-bold">Question</label>
              <div class="col-sm-10">
                <textarea class="form-control ques"
                  name="questions[${index}][ques]"
                  rows="2"
                  placeholder="Enter question..."
                  required>${esc(question.ques)}</textarea>
              </div>
            </div>

            <div class="hr-line-dashed"></div>

            <!-- OPTIONS -->
            ${optionsHTML}

            <!-- STATUS -->
            <div class="form-group row">
              <label class="col-sm-2 col-form-label text-right">Status</label>
              <div class="col-sm-10">
                <select class="form-control record_status" name="questions[${index}][record_status]">
                  <option disabled>Select question status</option>
                  <option value="active" ${isActive ? 'selected' : ''}>Active</option>
                  <option value="blocked" ${isBlocked ? 'selected' : ''}>Inactive</option>
                </select>
              </div>
            </div>

            <!-- ANSWER + MARKS -->
            <div class="form-group row">
              
              <label class="col-sm-2 col-form-label text-right">Correct</label>
              <div class="col-sm-4">
                <input type="number"
                  class="form-control cor_ans"
                  name="questions[${index}][cor_ans]"
                  placeholder="1-4"
                  value="${question.cor_ans || ''}"
                  required>
              </div>

              <label class="col-sm-2 col-form-label text-right">Marks</label>
              <div class="col-sm-4">
                <input type="number"
                  class="form-control marks"
                  name="questions[${index}][marks]"
                  placeholder="Marks"
                  value="${question.marks || ''}"
                  required>
              </div>

            </div>

          </div>
        </div>
      </div>
    `;
  }

  function reindexQuestions() {
    const $container = $("#main_question_container");

    $container.find(".question_div").each(function(i) {
      const divIndex = i + 1;
      const qIndex = i;

      const $el = $(this);

      // =========================
      // UPDATE MAIN CONTAINER ID
      // =========================
      $el.attr("id", "question_div_" + divIndex);

      // =========================
      // HEADER (CLEAN RESET)
      // =========================
      const $header = $el.find(".question-header");
      const $h5 = $header.find("h5");

      const $h5Clone = $h5.clone();

      // Remove badges completely before reading text
      $h5Clone.find(".q-badge").remove();

      let fullText = ($el.find(".ques").val() || "").trim();

      // Remove numbering
      fullText = fullText.replace(/^Question No\s+\d+:\s*/, '');

      // Reset header (NO badge here)
      $h5.html(`Question No ${divIndex}: ${fullText}`);

      // Update header ID
      $header.attr("id", "question_header_" + divIndex);

      // =========================
      // REAPPLY BADGE (STATE-DRIVEN)
      // =========================
      const badgeType = $el.attr("data-badge");
      if (badgeType) {
        applyBadge($el, badgeType);
      }

      // =========================
      // ACTION BUTTONS
      // =========================
      $el.find(".clone-question").data("divid", divIndex);
      $el.find(".remove-question").data("divid", divIndex);

      // =========================
      // CHECKBOX
      // =========================
      $el.find(".question-select-checkbox")
        .attr("data-divid", divIndex);

      // =========================
      // INPUT NAME UPDATE
      // =========================
      $el.find(".ques").attr("name", `questions[${qIndex}][ques]`);
      $el.find(".opt1").attr("name", `questions[${qIndex}][opt1]`);
      $el.find(".opt2").attr("name", `questions[${qIndex}][opt2]`);
      $el.find(".opt3").attr("name", `questions[${qIndex}][opt3]`);
      $el.find(".opt4").attr("name", `questions[${qIndex}][opt4]`);
      $el.find(".cor_ans").attr("name", `questions[${qIndex}][cor_ans]`);
      $el.find(".marks").attr("name", `questions[${qIndex}][marks]`);
      $el.find(".record_status").attr("name", `questions[${qIndex}][record_status]`);
    });

    // =========================
    // TOTAL COUNT
    // =========================
    $("#question_count").text(
      $container.find(".question_div").length
    );
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

  function pageItem(page) {
    return `
    <li class="page-item ${page === currentPage ? 'active' : ''}">
      <a class="page-link page-btn" href="javascript:void(0);" data-page="${page}">
        ${page}
      </a>
    </li>
  `;
  }

  function ellipsisItem() {
    return `
    <li class="page-item disabled">
      <span class="page-link">...</span>
    </li>
  `;
  }

  function updatePaginationUI(totalCount) {
    const totalPages = Math.ceil(totalCount / questionLimit);

    if (totalPages <= 1) {
      $("#pagination_container").html('');
      return;
    }

    let html = `<nav aria-label="Question pagination">
              <ul class="pagination justify-content-center flex-wrap">`;

    // =========================
    // PREVIOUS
    // =========================
    html += `
    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
      <a class="page-link page-btn" href="javascript:void(0);" data-page="${currentPage - 1}">
        &laquo; Prev
      </a>
    </li>`;

    const visibleRange = 2;
    let start = Math.max(1, currentPage - visibleRange);
    let end = Math.min(totalPages, currentPage + visibleRange);

    // =========================
    // FIRST PAGE
    // =========================
    if (start > 1) {
      html += pageItem(1);

      if (start > 2) {
        html += ellipsisItem();
      }
    }

    // =========================
    // MIDDLE
    // =========================
    for (let i = start; i <= end; i++) {
      html += pageItem(i);
    }

    // =========================
    // LAST PAGE
    // =========================
    if (end < totalPages) {

      if (end < totalPages - 1) {
        html += ellipsisItem();
      }

      html += pageItem(totalPages);
    }

    // =========================
    // NEXT
    // =========================
    html += `
      <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
        <a class="page-link page-btn" href="javascript:void(0);" data-page="${currentPage + 1}">
          Next &raquo;
        </a>
      </li>`;

    html += `</ul></nav>`;

    $("#pagination_container").html(html);
  }

  function initTinymce() {
    /*Summernote HTML5 Text Editor*/
    tinyMCE.init({
      selector: 'textarea.tinymce',
      height: 250,
      plugins: "link image media code",
      toolbar: 'undo redo | styleselect | forecolor | bold italic | alignleft aligncenter alignright alignjustify | ' +
        'outdent indent | media | link image | code',
      setup: function(ed) {
        ed.on('NodeChange', function(e) {
          tinyMCE.triggerSave();
          $("#" + ed.id).valid();
          //console.log('the event object ' + e);
          //console.log('the editor object ' + ed);
          //console.log('the content ' + ed.getContent());
        });
      }
    });
    /*------- Ends Here ---------*/
  }

  function handleQuestionLengthUI(count, search_string = null) {
    const emptyMsg = search_string ?
      "No results found for your search" :
      "No questions available";

    if (count === 0) {
      $("#delete_all_questions").attr('disabled', true);
      $("#question_list_div").addClass('d-none');
      $("#pagination_container").addClass('d-none');
      $("#empty_question_container").removeClass('d-none');

      if (search_string !== null) {
        $("#add_first_question").addClass('d-none');
        $("#empty_question_msg").removeClass('d-none');
      } else {
        $("#empty_question_msg").addClass('d-none');
        $("#search_question_div").addClass('d-none');
        //$("#question_action_div").addClass('d-none');
        $("#add_first_question").removeClass('d-none');
      }
    } else {
      $("#delete_all_questions").attr('disabled', false);
      $("#question_list_div").removeClass('d-none');
      $("#search_question_div").removeClass('d-none');
      //$("#question_action_div").removeClass('d-none');
      $("#pagination_container").removeClass('d-none');

      $("#empty_question_container").addClass('d-none');
      $("#empty_question_msg").addClass('d-none');
      $("#add_first_question").addClass('d-none');
    }
  }

  function loadQuestions({
    page = 1,
    search_string = null,
    append = false
  } = {}) {

    if (isLoadingQuestions) return;

    isLoadingQuestions = true;

    if (!append) {
      $("#main_question_container").html('');
    }

    ajaxRequest({
      action: "fetchQuestions",
      exam_id,
      search_string,
      page,
      limit: questionLimit
    }, ajaxControllerHandler, function(data) {

      isLoadingQuestions = false;

      if (data.check !== 'success') {
        toastr.error(data.message || "Failed to load questions");
        return;
      }

      const questions = data.questions || [];
      totalQuestionCount = data.total_count;
      const collapsed = question_div_collapse === 'true' ? 'collapsed' : 'open';

      // Handle ui based on question count
      handleQuestionLengthUI(questions.length, search_string);

      // Render
      questions.forEach((question, i) => {
        const index = append ? getQuestionCount() : i;
        const html = buildQuestionHTML(question, index, collapsed);
        $("#main_question_container").append(html);
      });

      currentPage = page;

      //initTinymce();
      arrangeQuestionList();
      reindexQuestions();
      updatePaginationUI(data.total_count);

      createSnapshot();
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

  function renderLatestChanges(data) {
    const $section = $("#latest_changes_container");
    const $container = $("#latest_changes_container");

    $container.empty();

    let index = 0;

    const nPayload = buildPayload();
    console.log(nPayload);

    // Created
    if (data.created?.length) {
      data.created.forEach(q => {
        const html = buildQuestionHTML(q, index++, 'collapsed', 'created');
        $container.append(html);
      });
    }

    // Updated
    if (data.updated?.length) {
      data.updated.forEach(q => {
        const html = buildQuestionHTML(q, index++, 'collapsed', 'updated');
        $container.append(html);
      });
    }

    if (index > 0) {

      //initTinymce();

      $section.removeClass("d-none");

      $("#show_question_list").removeClass("d-none");

      $("#main_question_container").empty();
      $("#add_more").addClass("d-none");

      // Scroll into view
      document.getElementById("latest_changes_container")?.scrollIntoView({
        behavior: "smooth"
      });
    }
  }

  function resetDirtyState() {
    $(".question_div").each(function() {
      $(this).attr("data-dirty", "false");
    });
  }

  function removeTemporaryQuestions() {
    $(".question_div").each(function() {
      const rid = $(this).data("rid");

      // If no DB id → it was a newly created temp question
      if (!rid || rid === 0) {
        $(this).remove();
      }
    });
  }

  function handlePostDeleteUI($questionDivs, dbDelete = false) {

    $questionDivs.remove();

    const questionCount = getQuestionCount();

    //$(".form-action-btns").toggleClass("d-none", totalQuestionCount === 0);
    $("#question_list_div").toggleClass("d-none", totalQuestionCount === 0);
    $("#add_first_question").toggleClass("d-none", totalQuestionCount !== 0);

    $("#pagination_container").removeClass("d-none");

    $("#select_all_questions").prop("checked", $(this).is(":checked")).trigger("change");

    // ---- GLOBAL SAVE BUTTON ----
    if (hasUnsavedChanges()) {
      $("#saveQuestions").addClass("btn-warning");
    } else {
      $("#saveQuestions").removeClass("btn-warning");
    }

    if (questionCount > 0) {

      reindexQuestions();
      arrangeQuestionList();
      // only if deleted from db
      if (dbDelete) {
        createSnapshot();
      }

      const targetId = questionCount > 1 ?
        `question_header_${questionCount}` :
        "main_question_container";

      document.getElementById(targetId)?.scrollIntoView({
        behavior: "smooth"
      });

    } else {

      loadQuestions({
        page: parseInt(currentPage),
        append: false
      });
    }
  }

  $(window).on('scroll', function() {
    const $outer = $('.fab-outer');
    const $bar = $('.form-action-btns');

    const offsetTop = $outer.offset().top;
    const scrollTop = $(window).scrollTop();

    if (scrollTop > offsetTop) {
      const width = $outer.outerWidth();
      const left = $outer.offset().left;

      $bar.addClass('fixed-active').css({
        width: width + 'px',
        left: left + 'px'
      });

    } else {
      $bar.removeClass('fixed-active').css({
        width: '',
        left: ''
      });
    }
  });

  $(document).ready(function() {

    loadQuestions({
      page: currentPage,
      append: false
    });

    //Bind tooltip on dynamic meta elements
    $('body').tooltip({
      selector: '.dynamicQuestion'
    });

    $(document).on("change", "#select_all_questions", function() {
      if ($(this).is(":checked")) {
        $("#delete_selected_question").removeClass("d-none");
      } else {
        $("#delete_selected_question").addClass("d-none");
      }
      $(".question-select-checkbox").prop("checked", $(this).is(":checked")).trigger("change");
    });

    $(document).on("change", ".question-select-checkbox", function() {
      const checkedCount = $('.question-select-checkbox:checked').length;

      if (checkedCount > 0) {
        $('#delete_selected_question').removeClass('d-none');
      } else {
        $('#delete_selected_question').addClass('d-none');
      }
    });

    $(document).on('click', '.clone-question', function(e) {
      e.preventDefault();

      const divId = $(this).data('divid');
      const qIndex = getQuestionCount();
      const divIndex = qIndex + 1;
      const targetDiv = 'question_header_' + parseInt(divIndex - 2);

      const $source = $("#question_div_" + divId);
      const $clone = $source.clone(true);

      // Hiden pagination div until clone is done
      $("#pagination_container").addClass("d-none");

      // Update main container ID
      $clone.attr('id', `question_div_${divIndex}`)
        .removeClass("collapsed").addClass("mt-3 open");

      $clone.find('.collapse-question-div')
        .attr('data-cstatus', 'open');

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

      $("#saveQuestions").addClass("btn-warning");
      $("#pagination_container").removeClass("d-none");

      // Scroll
      document.getElementById(targetDiv)?.scrollIntoView({
        behavior: "smooth"
      });

      // Apply CLONED badge
      applyBadge($clone, "cloned");

      arrangeQuestionList();

    });

    $(document).on('click', '#add_more, #add_first_question', function(e) {
      e.preventDefault();

      const qIndex = getQuestionCount();
      const divIndex = qIndex + 1;

      // First question UI toggle
      if (qIndex === 0) {
        $(".form-action-btns").removeClass("d-none");
        $("#question_list_div").removeClass("d-none");
        $("#add_first_question").addClass("d-none");
      }

      const collapsed = 'open'; //question_div_collapse === 'true' ? 'collapsed' : 'open';
      const targetDiv = 'question_header_' + parseInt(divIndex - 2);

      // Empty question object
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

      // Build HTML
      const html = buildQuestionHTML(emptyQuestion, qIndex, collapsed);

      // Append
      $("#main_question_container").append(html);

      const $newQuestion = $("#question_div_" + divIndex);

      // Mark as dirty (important)
      $newQuestion.attr('data-dirty', 'true');

      // Apply ADDED badge
      applyBadge($newQuestion, "created");

      // Trigger save state
      $("#saveQuestions").addClass("btn-warning");

      // Update list
      arrangeQuestionList();

      // Handle ui based on question count
      handleQuestionLengthUI(divIndex);

      // Scroll
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

        // Hiden pagination div until clone is done
        $("#pagination_container").addClass("d-none");

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
              handlePostDeleteUI($questionDiv, true);
              toastr.success(data.message || "Question deleted successfully", "Success!");
            } else {
              toastr.error(data.message || "Something went wrong", "Error!");
            }
          });

        }
        // CASE 2: New/Cloned → DOM only
        else {
          handlePostDeleteUI($questionDiv);
          $btn.prop("disabled", false);
        }

      });
    });

    $(document).on('click', '#delete_selected_question', function(e) {
      e.preventDefault();

      const $btn = $(this);
      const $checked = $('.question-select-checkbox:checked');

      if ($checked.length === 0) {
        alert('No items selected');
        return;
      }

      swal({
        title: "Are you sure?",
        text: "Are you sure to delete this question?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, Go ahead!",
        closeOnConfirm: true
      }, function() {

        $btn.prop("disabled", true);
        $("#pagination_container").addClass("d-none");

        let ids = [];
        let $toRemove = $(); // jQuery collection

        $checked.each(function() {
          const $q = $(this).closest('.question_div');
          const rid = $(this).data('rid');

          $toRemove = $toRemove.add($q);

          // Only push DB IDs (skip cloned / new)
          if (rid) {
            ids.push(rid);
          }
        });

        // CASE 1: If there are DB records
        if (ids.length > 0) {

          const payload = {
            action: "deleteGlobalData",
            type: "exam_questions",
            row_id: ids
          };

          ajaxRequest(payload, ajaxControllerHandler, function(data) {

            $btn.prop("disabled", false);

            if (data.check === 'success') {

              handlePostDeleteUI($toRemove, true);

              toastr.success(data.message || "Questions deleted successfully", "Success!");

            } else {
              toastr.error(data.message || "Something went wrong", "Error!");
            }
          });

        }
        // CASE 2: Only cloned/new (no DB call)
        else {

          handlePostDeleteUI($toRemove);

          $btn.prop("disabled", false);
        }

      });
    });

    $(document).on(
      'input change',
      '.question_div input:not(.question-select-checkbox), .question_div textarea, .question_div select',
      function() {

        const $questionDiv = $(this).closest('.question_div');

        const changed = isQuestionChanged($questionDiv);

        // =========================
        // DIRTY STATE
        // =========================
        $questionDiv.attr('data-dirty', changed ? 'true' : 'false');

        // =========================
        // GLOBAL SAVE BUTTON
        // =========================
        if (hasUnsavedChanges()) {
          $("#saveQuestions").addClass("btn-warning");
        } else {
          $("#saveQuestions").removeClass("btn-warning");
        }

        // =========================
        // BADGE LOGIC
        // =========================

        const currentBadge = $questionDiv.attr("data-badge");

        // If reverted → remove updated badge completely
        if (!changed) {
          if (currentBadge === "updated") {
            $questionDiv.removeAttr("data-badge");
            $questionDiv.find('.q-badge[data-type="updated"]').remove();
          }
          return;
        }

        // Never override cloned/new
        if (currentBadge === "cloned" || currentBadge === "created") {
          return;
        }

        // Apply updated badge
        if (currentBadge !== "updated") {
          applyBadge($questionDiv, "updated");
        }
      });

    $(document).on('click', '#show_question_list', function(e) {

      const $btn = $(this); // store reference

      swal({
          title: "Are you sure?",
          text: "This latest changes section will be removed until your next update",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "Yes, Go ahead!",
          closeOnConfirm: true
        },
        function() {

          $btn.addClass("d-none"); // correct reference

          $("#latest_changes_container").empty().addClass("d-none");

          // Load current page
          loadQuestions({
            page: currentPage,
            append: false
          });

          $("#main_question_container").removeClass("d-none");
          $("#add_more").removeClass("d-none");
          $("#pagination_container").removeClass("d-none");
        });

    });

    $(document).on("click", ".page-btn", function() {

      const page = parseInt($(this).data("page"));

      if (!page || page < 1) return;

      if (hasUnsavedChanges()) {
        toastr.warning("Please save changes before navigating.");
        return;
      }

      loadQuestions({
        page,
        append: false
      });
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
      var targetDiv = divId > 2 ? 'question_header_' + parseInt(divId - 2) : "main_question_container";

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

      // ---- GLOBAL SAVE BUTTON ----
      if (!hasUnsavedChanges()) {
        toastr.error("You haven made no changes to save.", "Error!");
        return;
      }

      // add extra required data
      payload.exam_id = exam_id;
      payload.action = "manageExamQuestions";

      $("#saveQuestions").addClass("btn-warning");
      $("#pagination_container").addClass("d-none");

      ajaxRequest(payload, ajaxControllerHandler, function(data) {
        //console.log(data);return;
        $("#saveQuestions").removeClass("btn-warning");

        $('body>.tooltip').remove();

        if (data.check === 'success') {
          //show sweetalert success
          swal({
            title: "Great!",
            text: "Questions are successfully saved",
            type: "success"
          }, function() {

            resetDirtyState();

            removeTemporaryQuestions();

            renderLatestChanges(data);

            arrangeQLatestuestionList();
            createSnapshot();
          });
        } else {
          //show sweetalert error
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
        }

        return;
      });

    });

    $(document).on('submit', '#search_question_form', function(event) {
      event.preventDefault();

      const search_string = $("#search_string").val();

      if (hasUnsavedChanges()) {
        toastr.warning("Please save changes before navigating.");
        return;
      }

      loadQuestions({
        page: 1,
        search_string,
        append: false
      });

      // Handling UI state
      $("#remove_question_filter").removeClass('d-none');

    });

    $(document).on('click', '#remove_question_filter', function(event) {
      // Handling UI state
      $(this).addClass('d-none');
      $("#search_string").val('');

      loadQuestions({
        page: currentPage,
        append: false
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