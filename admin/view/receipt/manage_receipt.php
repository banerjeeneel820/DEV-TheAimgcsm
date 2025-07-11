<?php
if (isset($_GET['stu_id'])) {
  $stu_id = $_GET['stu_id'];
} else {
  $stu_id = null;
}

//Constructing receipt cancel url
$queries = array();
parse_str($_SERVER['QUERY_STRING'], $queries);

$extra_query_str = '';

foreach ($queries as $key => $query_val) {
  if ($key != "route" && $key != 'actionType' && $key != 'rcpt_id') {
    $extra_query_str .= "&" . $key . "=" . $query_val;
  }
}

$verified_status = $_GET['verified_status'];

if (!empty($_GET['actionType'])) {

  //Category data
  $categoryList = $pageContent['pageData']['category_data'];

  if ($_GET['actionType'] == "create") {

    //Student data
    $studentDetails = $pageContent['pageData']['student_data'];
    //Receipt data
    $receiptDetails = array();
  } elseif ($_GET['actionType'] == "edit") {

    $receipt_row_id = $_GET['rcpt_id'];

    //Receipt data
    $receiptDetails = $pageContent['pageData']['receipt_data'];

    //Student data
    $studentDetails = $pageContent['pageData']['student_data'];
    //Getting student id
    $stu_id = $studentDetails->stu_id;
  }

  if (empty($studentDetails->stu_id)) {
    $receiptErrUrl = SITE_URL . '?route=view_receipts&stu_id=' . $stu_id . '&stu_err=1';
    exit(header('Location: ' . $receiptErrUrl));
  }
} else {

  if (isset($_GET['record_status'])) {
    if ($_GET['record_status'] == 'active') {
      $record_status = 'active';
    } else {
      $record_status = 'blocked';
    }
  } else {
    $record_status = 'active';
  }

  //Student data
  $studentDetails = $pageContent['pageData']['student_data'];
  //Franchise data
  $franchiseArr = $pageContent['pageData']['franchise_data'];
  //Course data
  $courseArr = $pageContent['pageData']['course_data'];
  //Receipt data
  $receiptPagedData = $pageContent['pageData']['receipt_data'];

  $receiptListArr = $receiptPagedData['data'];

  if (!empty($receiptListArr)) {
    $pageNo = $receiptPagedData['pageNo'];
    $rowCount = $receiptPagedData['row_count'];
    $limit = $receiptPagedData['limit'];
    $offset = ($pageNo - 1) * $limit;
    $totalPageNo = ceil($rowCount / $limit);
  }
}

if (!empty($stu_id)) {
  $site_refresh_url = SITE_URL . '?route=view_receipts&stu_id=' . $stu_id;
  $create_receipt_url = SITE_URL . '?route=view_receipts&actionType=create'.$extra_query_str;
} else {
  $site_refresh_url = SITE_URL . '?route=view_receipts';
  $create_receipt_url = SITE_URL . '?route=view_receipts' . $extra_query_str . '&stu_err=1';
}

if (!empty($studentDetails->stu_course_fees)) {
  $course_due_fees = (int)$studentDetails->stu_course_fees - (int)$studentDetails->stu_course_discount - (int)$studentDetails->course_fees_paid - (int)$studentDetails->advanced_fees - (int)$studentDetails->fees_paid_before_dr;
} else {

  $course_fees = (int)0; //(int)$studentDetails->course_default_fees;

  $course_due_fees = $course_fees - (int)$studentDetails->stu_course_discount - (int)$studentDetails->course_fees_paid - (int)$studentDetails->advanced_fees - (int)$studentDetails->fees_paid_before_dr;
}

$createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_receipt");
$updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_receipt");
$deletePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("delete_receipt");

$updateStuPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_student");

if (!empty($_GET['rcpt_id'])) {
  $modifyPermission = $updatePermission;
} else {
  $modifyPermission = $createPermission;
}

/*print"<pre>";
  print_r($studentDetails);
  print"</pre>";*/

?>

<div class="wrapper wrapper-content fadeInRight">

  <?php if (!empty($_GET['actionType'])) { ?>

    <div class="row" id="manage_receipt_form_div">
      <div class="col-lg-12">
        <?php if ($course_due_fees > 0) { ?>
          <div class="alert alert-warning text-center" role="alert">
            Course due fees of this student : <i class="fa fa-inr"></i> <?= $course_due_fees ?>
          </div>
        <?php } else { ?>
          <div class="alert alert-primary text-center" role="alert">
            All course fees have been cleared by this student!
          </div>
        <?php } ?>

        <div class="ibox">
          <div class="ibox-title">
            <h5>Student Receipt Form</h5>
            <div class="ibox-tools">
              <span><strong>Receipt Creation Date: <?= date('jS F, Y', time()) ?></strong></span>
              <a class="collapse-link ml-2">
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
            <form id="manage_receipt_form" class="needs-validation" method="post" onsubmit="return false;" novalidate>
              <input type="hidden" name="action" id="action" value="manageStudentReceipt">
              <input type="hidden" name="receipt_row_id" id="receipt_row_id" value="<?= (!empty($receipt_row_id) ? $receipt_row_id : null) ?>">
              <input type="hidden" name="stu_id" id="stu_id" value="<?= (!empty($stu_id) ? $stu_id : null) ?>">

              <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Student Name <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student Name"><i class="fa fa-question-circle"></i></span></label>
                <div class="col-sm-10">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Student Name..." value="<?= (!empty($studentDetails) ? $studentDetails->stu_name : '') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="hr-line-dashed"></div>

              <div class="form-group row">

                <label class="col-sm-2 col-form-label text-right">Student ID <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student's ID"><i class="fa fa-question-circle"></i></span></label>
                <div class="col-sm-4">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Student's ID" value="<?= (!empty($studentDetails) ? $studentDetails->stu_id : '') ?>" readonly>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-right">Receipt ID <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Receipt's ID"><i class="fa fa-question-circle"></i></span></label>
                <div class="col-sm-4">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Receipt's ID" value="<?= (!empty($receiptDetails) ? $receiptDetails->receipt_id : 'Not created yet!') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="hr-line-dashed"></div>


              <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Course Enrolled <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student's enrolled course"><i class="fa fa-question-circle"></i></span></label>
                <div class="col-sm-10">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Student's enrolled course" value="<?= (!empty($studentDetails) ? $studentDetails->course_title : '') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="hr-line-dashed"></div>

              <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Student's Franchise <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student's Franchise"><i class="fa fa-question-circle"></i></span></label>
                <div class="col-sm-10">
                  <div class="input-group">
                    <input type="text" class="form-control" placeholder="Student's Franchise" value="<?= (!empty($studentDetails) ? $studentDetails->center_name : '') ?>" readonly>
                  </div>
                </div>
              </div>
              <div class="hr-line-dashed"></div>

              <div class="form-group row">
                <label class="col-sm-2 col-form-label text-right">Record Status <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose Student Record Status"><i class="fa fa-question-circle"></i></span></label>
                <div class="col-sm-4 pt-1">
                  <label class="checkbox-inline i-checks"> <input type="radio" value="active" name="record_status" <?= (!empty($receiptDetails) ? ($receiptDetails->receipt_status == 'active' ? 'checked' : '') : 'checked') ?> /> <i></i>Active </label>
                  <label class="checkbox-inline i-checks"> <input type="radio" value="blocked" name="record_status" <?= ($receiptDetails->receipt_status) == 'blocked' ? 'checked' : '' ?>> <i></i> Blocked </label>
                </div>

                <label class="col-sm-2 col-form-label text-right">Send Mail <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Choose Student Record Status"><i class="fa fa-question-circle"></i></span></label>
                <div class="col-sm-4 pt-1">
                  <label class="checkbox-inline i-checks"> <input type="radio" value="yes" name="send_mail" /> <i></i>Yes </label>
                  <label class="checkbox-inline i-checks"> <input type="radio" value="no" name="send_mail" checked> <i></i> No </label>
                </div>
              </div>
              <div class="hr-line-dashed"></div>

              <div class="form-group row">

                <label class="col-sm-2 col-form-label text-right">Receipt Category <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student's Receipt Category"><i class="fa fa-question-circle"></i></span></label>

                <div class="col-sm-4">

                  <div class="input-group">
                    <select class="category_id" name="category_id" id="category_id" data-placeholder="Choose a Receipt Category..." tabindex="2" required>
                      <option></option>
                      <?php
                      foreach ($categoryList as $category) {
                        if (stripos($category->name, 'admiss') == false) {
                      ?>
                          <option value="<?= $category->id ?>" <?= ($receiptDetails->category_id == $category->id ? 'selected' : '') ?>><?= $category->name ?></option>
                      <?php }
                      } ?>
                    </select>
                  </div>

                </div>

                <label class="col-sm-2 col-form-label text-right"><span id="receipt_amount_txt"><?= ($receiptDetails->category == "Admission Fees" ? "Admission Fees" : "Receipt Amount") ?></span> <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Student's Receipt Amount"><i class="fa fa-question-circle"></i></span></label>

                <div class="col-sm-4">
                  <div class="input-group">
                    <input type="text" class="form-control" name="receipt_amount" id="og_receipt_amount" placeholder="Enter Student's Receipt Amount..." value="<?= $receiptDetails->receipt_amount ?>" <?= ($receiptDetails->category == "Other Fees" ? 'readonly' : '') ?>>
                  </div>
                </div>

              </div>
              <div class="hr-line-dashed"></div>

              <div class="form-group row">
                <label class="col-sm-2 col-form-label text-right">Late Fine <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter late fine if any"><i class="fa fa-question-circle"></i></span></label>
                <div class="col-sm-4">
                  <div class="input-group">
                    <input type="text" class="form-control" name="late_fine" id="late_fine" placeholder="Enter late fine if any..." value="<?= (!empty($receiptDetails) ? $receiptDetails->late_fine : '') ?>" <?= ($receiptDetails->category == "Other Fees" ? 'readonly' : '') ?>>
                  </div>
                </div>

                <label class="col-sm-2 col-form-label text-right"><span id="additional_fees_text"><?= ($receiptDetails->category == "Admission Fees" ? "Registration Fees" : "Additional Fees") ?></span> <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter any additional if any"><i class="fa fa-question-circle"></i></span></label>
                <div class="col-sm-4">
                  <div class="input-group">
                    <input type="text" class="form-control" name="extra_fees" id="extra_fees" placeholder="Enter any additional if any..." value="<?= (!empty($receiptDetails) ? $receiptDetails->extra_fees : '') ?>">
                  </div>
                </div>
              </div>
              <div class="hr-line-dashed"></div>

              <div class="<?= (!empty($receiptDetails->extra_fees_description) ? '' : 'd-none') ?>" id="additional_fees_desc_div">

                <div class="form-group row text-right"><label class="col-sm-2 col-form-label">Additional Fees Description <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enter Additional Fees Description"><i class="fa fa-question-circle"></i></span></label>
                  <div class="col-sm-10">
                    <div class="input-group">
                      <textarea class="form-control" name="extra_fees_description"><?= (!empty($receiptDetails) ? $receiptDetails->extra_fees_description : '') ?></textarea>
                    </div>
                  </div>
                </div>
                <div class="hr-line-dashed"></div>

              </div>

              <div class="form-group row">
                <div class="col-sm-4 col-sm-offset-2">
                  <a href="<?= SITE_URL . '?route=view_receipts' . $extra_query_str ?>"><button type="button" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Close Receipt Form"><i class="fa fa-reply"></i></button></a>

                  <button type="button" class="btn btn-warning btn-sm" id="preview_receipt" data-toggle="tooltip" title="Preview Receipt Data"><i class="fa fa-eye"></i> Preview Receipt</button>

                  <?php if ($modifyPermission) { ?>
                    <button class="btn btn-primary btn-sm" id="manage_receipt" type="submit" data-toggle="tooltip" title="<?= (!empty($_GET['rcpt_id']) ? 'Update Receipt' : 'Create Receipt') ?>" class="btn btn-success" title="Save"><i class="fa fa-save"></i> <?= (!empty($_GET['rcpt_id']) ? 'Update Receipt' : 'Create Receipt') ?></button>
                  <?php } ?>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  <?php } else { ?>

    <div class="row">
      <div class="col-lg-9">
        <div class="ibox ">
          <div class="ibox-title">
            <h5>Fetch Student's Receipts Based on Selected Parameters</h5>
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
            <form id="fetch_student_receipt_records" onsubmit="return false;">
              <input type="hidden" name="page_route" id="page_route" value="<?= $_GET['route'] ?>">
              <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 m-b-xs input-group pr-0">
                  <select class="record_status" name="record_status" id="record_status" data-placeholder="Select Record Status" required>
                    <option selected disabled value>Select a Data type to proceed</option>
                    <option value="active" <?= (($record_status == 'active' || $record_status == '') ? 'selected' : '') ?>>Active</option>
                    <option value="blocked" <?= ($record_status == 'blocked' ? 'selected' : '') ?>>Blocked</option>
                  </select>

                  <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select Receipt Status"><i class="fa fa-question-circle"></i></span>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-12 pl-0">
                  <div class="input-daterange input-group">
                    <input type="text" class="form-control-sm form-control datepicker" name="created" id="created" value="<?= $_GET['created'] ?>" placeholder="Date of Creation" autocomplete="off">

                    <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select a date range"><i class="fa fa-question-circle"></i></span>
                  </div>
                </div>

                <div class="col-lg-5 col-md-5 col-sm-12 m-b-xs pl-0">
                  <div class="input-group">
                    <input type="text" class="form-control" name="student_id" id="student_id" placeholder="Search by Student's ID..." value="<?= (isset($_GET['stu_id']) ? $_GET['stu_id'] : '') ?>">

                    <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Enter Student ID"><i class="fa fa-question-circle"></i></span>

                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-lg-3 col-md-3 col-sm-12 ml-0 pr-0">
                  <select class="course" name="course_id" id="course_id" data-placeholder="Search by a Course..." tabindex="2">
                    <option></option>
                    <?php foreach ($courseArr as $course) {
                    ?>
                      <option value="<?= $course->id ?>" <?= ($_GET['course_id'] == $course->id ? 'selected' : '') ?>><?= $course->course_title ?></option>
                    <?php } ?>
                  </select>

                  <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Search by a Course"><i class="fa fa-question-circle"></i></span>
                </div>

                <div class="col-lg-3 col-md-3 col-sm-12 pl-0">
                  <select class="franchise" name="franchise_id" id="franchise_id" data-placeholder="Search by a Franchise..." tabindex="2" <?= ($_SESSION['user_type'] == 'franchise' ? 'disabled' : '') ?>>
                    <option></option>
                    <?php foreach ($franchiseArr as $franchise) {
                    ?>
                      <option value="<?= $franchise->id ?>" <?= ($_SESSION['user_type'] == 'franchise' ? ($_SESSION['user_id'] == $franchise->id ? 'selected' : '') : ($_GET['franchise_id'] == $franchise->id ? 'selected' : '')) ?>><?= $franchise->center_name ?></option>
                    <?php } ?>
                  </select>

                  <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Search by a Franchise"><i class="fa fa-question-circle"></i></span>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 pl-0 pr-4">

                  <div class="input-daterange input-group">
                    <input type="text" class="form-control-sm form-control datepicker" name="receipt_search_start" id="receipt_search_start" value="<?= $_GET['receipt_season_start'] ?>" placeholder="Starting Date" autocomplete="off">

                    <span class="input-group-addon">to</span>

                    <input type="text" class="form-control-sm form-control datepicker" name="receipt_search_end" id="receipt_search_end" value="<?= $_GET['receipt_season_end'] ?>" placeholder="Ending Date" autocomplete="off">

                    <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select a date range"><i class="fa fa-question-circle"></i></span>

                    <button class="btn btn-primary ml-3" type="submit" id="fetch_item_data" data-toggle="tooltip" data-placement="bottom" title="Fetch Student's Receipt Data"><i class="fa fa-search"></i></button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>

      <div class="col-lg-3">
        <div class="ibox ">
          <div class="ibox-title">
            <h5>Export Records</h5>

            <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Export student records in pdf or excel format based on search parameters on the search form"><i class="fa fa-question-circle"></i></span>

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
            <a href="javascript:void(0);" class="btn btn-danger btn-md export_student_receipt_data" data-export="pdf">
              <i class="fa fa-file-pdf-o"> </i> Export in PDF Format
              <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Export student records in pdf format based on search parameters on the search form. This is a longer process and may take time upto 1 hour based on records number present in table."><i class="fa fa-question-circle"></i></span>
            </a>

            <a href="javascript:void(0);" class="btn btn-primary btn-md mt-2 export_student_receipt_data" data-export="excel">
              <i class="fa fa-file-excel-o"> </i> Export in CSV Format
              <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Export student records in excel format based on search parameters on the search form. This is a much faster process than pdf export and this is recomended."><i class="fa fa-question-circle"></i></span>
            </a>

            <a href="javascript:void(0)" id="export_record_href" style="display:none" download>
              <button type="button" id="hidden_export_button">Export</button>
            </a>
          </div>
        </div>
      </div>
    </div>

    <?php if ($_GET['stu_err'] == 1) { ?>
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
          <div class="alert alert-danger" role="alert">
            Invalid Student ID! Please Enter an Appropriate Student ID.
          </div>
        </div>
      </div>
    <?php } ?>

    <?php if (!empty($studentDetails)) { ?>
      <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">

          <?php if ($course_due_fees > 0) { ?>
            <div class="alert alert-warning text-center" role="alert">
              Course due fees of this student : <i class="fa fa-inr"></i> <?= $course_due_fees ?>
            </div>
          <?php } else { ?>
            <div class="alert alert-primary text-center" role="alert">
              All course fees have been cleared by this student!
            </div>
          <?php } ?>

          <div class="ibox ">
            <div class="ibox-title">
              <h5>Student Overview</h5>
              <div class="ibox-tools">
                <?php if ($createPermission && !empty($studentDetails)) { ?>
                  <a href="<?= $create_receipt_url ?>" class="table-action-primary" data-mtype="create_receipt" data-toggle="tooltip" data-placement="bottom" title="Create Receipt for current student"><i class="fa fa-plus-circle"></i></a>
                <?php } ?>

                <a href="<?= $site_refresh_url ?>" class="table-action-info" data-toggle="tooltip" data-placement="bottom" title="Refresh Receipt Data"><i class="fa fa-refresh"></i></a>
                <a class="collapse-link ml-2">
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
                <table class="table table-striped table-bordered table-hover text-center">
                  <thead class="cursor-pointer">
                    <tr>
                      <th class="notexport">Image</th>
                      <th class="sorting_desc_disabled">Name</th>
                      <th class="sorting_desc_disabled">Student ID<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
                      <th class="sorting_desc_disabled">Contact No</th>
                      <th class="sorting_desc_disabled">Course/Franchise</th>
                      <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="No. of Receipt created for student">Count</th>
                      <th class="sorting_desc_disabled notexport">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $student_image_url = USER_UPLOAD_DIR . 'student/' . $studentDetails->image_file_name;

                    if (!strlen($studentDetails->image_file_name) > 0 || !file_exists($student_image_url)) {
                      $student_image_url = RESOURCE_URL . 'images/default-user-avatar.jpg';
                    } else {
                      $student_image_url = USER_UPLOAD_URL . 'student/' . $studentDetails->image_file_name;
                    }
                    ?>
                    <tr>
                      <td class="client-avatar">
                        <a href="<?= $student_image_url ?>" data-fancybox="gallery" data-caption="<?= $studentDetails->stu_name ?>">
                          <img alt="image" src="<?= $student_image_url ?>">
                        </a>
                      </td>

                      <td class="project-title" style="width:20%;">
                        <a href="javascript:void(0);" class="viewStudentDetail" data-sid="<?= $studentDetails->id ?>"><?= $studentDetails->stu_name ?></a>
                        <br />
                        <small>Created <?= date('jS F, Y', strtotime($studentDetails->created_at)) ?></small>
                      </td>

                      <td class="project-title" style="width:10%;">
                        <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student ID: <?= $studentDetails->stu_id ?>"><?= $studentDetails->stu_id ?></span>
                      </td>

                      <td class="project-title">
                        <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student Contact No: <?= $studentDetails->stu_phone ?>"><?= $studentDetails->stu_phone ?></span>
                      </td>

                      <td class="project-title">
                        <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Course Name: <?= $studentDetails->course_title ?>"><?= $studentDetails->course_title ?></span><br>
                        <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Franchise Name: <?= $studentDetails->center_name ?>"><strong>Franchise: <?= (strlen($studentDetails->center_name) > 0 ? $studentDetails->center_name : '<h5 style="color:red;">No Franchise available!</h5>') ?></strong></span>
                      </td>

                      <td class="project-title">
                        <span class="badge badge-pill badge-primary cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="No of Receipt created for this Student: <?= $studentDetails->receipt_count ?>"><?= $studentDetails->receipt_count ?></span>
                      </td>

                      <td class="project-status">
                        <span class="dropdown">
                          <button class="btn btn-primary product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                          <ul class="dropdown-menu">
                            <?php if ($updateStuPermission) { ?>
                              <li>
                                <a href="<?= SITE_URL ?>?route=edit_student&id=<?= $studentDetails->id ?>" data-toggle="tooltip" data-placement="bottom" title="Edit this Student"><i class="fa fa-pencil"></i> Edit Student</a>
                              </li>
                            <?php } ?>

                            <?php if ($createPermission && !empty($studentDetails) && $course_due_fees > 0) { ?>
                              <li>
                                <a href="<?= $create_receipt_url ?>"><i class="fa fa-plus-circle"></i> Create Receipt</a>
                              </li>
                            <?php } ?>
                          </ul>
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>


    <?php if (empty($studentDetails)) { ?>
      <div class="row">
        <div class="col-lg-12">
          <div class="ibox ">
            <div class="ibox-title">
              <h5>Fetch Receipt based on their verification status </h5>
              <div class="ibox-tools">
                <a class="collapse-link">
                  <i class="fa fa-chevron-up"></i>
                </a>
              </div>
            </div>
            <div class="ibox-content">
              <form id="fetch_verified_records" onsubmit="return false;">
                <div class="row">
                  <div class="col-sm-10 m-b-xs">
                    <select class="form-control-sm form-control input-s-sm inline" name="verified_status" id="verified_status">
                      <option selected disabled value>Select a Verified Status to proceed</option>
                      <option value="n" <?= (($verified_status == 'n') ? 'selected' : '') ?>>Not Verified</option>
                      <option value="y" <?= ($verified_status == 'y' ? 'selected' : '') ?>>Verified</option>
                    </select>
                  </div>

                  <div class="col-sm-2">
                    <button class="btn btn-primary" type="submit" id="fetch_item_data"><i class="fa fa-search"></i>&nbsp;Fetch Data</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php } ?>

    <div class="row">
      <div class="col-lg-12 col-md-12 col-sm-12">
        <?php if (!empty($studentDetails->advanced_fees)) { ?>
          <div class="alert alert-primary text-center" role="alert">
            This student submitted an advanced fees of : <i class="fa fa-inr"></i> <?= $studentDetails->advanced_fees ?> on <?= date('jS F, Y', strtotime($studentDetails->advance_fees_date)) ?> which has been deducted from net course fees.
          </div>
        <?php } ?>

        <?php if (!empty($studentDetails->fees_paid_before_dr)) { ?>
          <div class="alert alert-primary text-center" role="alert">
            This student submitted total fees of : <i class="fa fa-inr"></i> <?= $studentDetails->fees_paid_before_dr ?> before the start of digital receipt.
          </div>
        <?php } ?>

        <div class="ibox">
          <div class="ibox-title">
            <h5>All Receipt Details</h5>
            <div class="ibox-tools">
              <?php if ($createPermission && !empty($studentDetails)) { ?>
                <a href="<?= $create_receipt_url ?>" class="table-action-primary" data-mtype="create_receipt" data-toggle="tooltip" data-placement="bottom" title="Create Receipt for current student"><i class="fa fa-plus-circle"></i></a>
              <?php } ?>

              <a href="<?= $site_refresh_url ?>" class="table-action-info" data-toggle="tooltip" data-placement="bottom" title="Refresh Receipt Data"><i class="fa fa-refresh"></i></a>

              <?php if ($record_status == 'active') { ?>
                <?php if ($updatePermission) { ?>
                  <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid="all" data-type="student_receipts" data-ptype="Receipt" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>

                  <a href="javascript:void(0);" class="table-action-danger sendMailToUser" data-rid="all" data-type="student_receipts" data-ptype="Receipt" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-envelope-o"></i></a>
                <?php } ?>
              <?php } else { ?>
                <?php if ($updatePermission) { ?>
                  <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid="all" data-type="student_receipts" data-ptype="Receipt" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
                <?php } ?>

                <?php if ($deletePermission) { ?>
                  <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid="all" data-type="student_receipts" data-ptype="Receipt" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
                <?php } ?>
              <?php } ?>
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

              <input type="text" class="form-control form-control-sm m-b-xs" id="receipt_tbl_filter" placeholder="Search in student table by student name, id, phone no or franchise...">

              <div class="mt-2 mb-2">
                <?php if (!empty($receiptListArr)) { ?>
                  <strong>Showing <?= $offset + 1 ?> to <?= (count($receiptListArr) == $limit ? $limit * $pageNo : count($receiptListArr)) ?> of <?= $rowCount ?> entries</strong>
                <?php } else { ?>
                  <strong>No Data Found!</strong>
                <?php } ?>
              </div>

              <table class="table table-striped table-bordered table-hover text-center" id="receipt_list_tbl">
                <thead class="cursor-pointer">
                  <tr>
                    <th class="notexport">
                      <div class="pretty p-image p-plain checkAll ml-2">
                        <input type="checkbox" id="checkAll" />
                        <div class="state">
                          <img class="image" src="<?= RESOURCE_URL ?>images/checkbox.png">
                          <label></label>
                        </div>
                      </div>
                    </th>

                    <th class="notexport">Image</th>
                    <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Student name">Name<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                    <th class="sorting_desc_disabled">Student Info</th>
                    <th class="sorting_desc_disabled">Franchise/Course</th>

                    <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Receipt ID">Receipt ID</th>

                    <th class="sorting_desc_disabled" data-toggle="tooltip" data-placement="bottom" title="Receipt amount in Rupees">Amount(Rs.)</th>

                    <th class="sorting_desc_disabled notexport">Action</th>
                  </tr>
                </thead>
                <tbody id="receiptTbody">
                  <?php
                  if (!empty($receiptListArr)) {
                    //$total_collection = 0;
                    foreach ($receiptListArr as $index => $content) {

                      $student_image_url = USER_UPLOAD_DIR . 'student/' . $content->image_file_name;

                      if (!strlen($content->image_file_name) > 0 || !file_exists($student_image_url)) {
                        $student_image_url = RESOURCE_URL . 'images/default-user-avatar.jpg';
                      } else {
                        $student_image_url = USER_UPLOAD_URL . 'student/' . $content->image_file_name;
                      }

                      if ($content->student_status != 'course_complete') {
                        $student_status = ucfirst($content->student_status);
                      } else {
                        $student_status = 'Course Complete';
                      }

                      $total_receipt_amount = round( (int)$content->receipt_amount + (int)$content->late_fine + (int)$content->extra_fees);

                      if(!empty($studentDetails)){
                        $total_collection = round((int)$total_collection + (int)$content->receipt_amount);
                      }else{
                        $total_collection = round((int)$total_collection + (int)$total_receipt_amount); 
                      }

                      if (!empty($content->edit_description)) {
                        $edit_description_arr = unserialize($content->edit_description);
                        $edit_desc_str = "<p>";

                        foreach ($edit_description_arr as $edIndex => $desc) {
                          $edit_desc_str .= ($edIndex + 1) . ". " . $desc . "<br>";
                        }
                        $edit_desc_str .= "</p>";
                      }
                  ?>
                      <tr id="rcpt_tr_<?= $content->receipt_id ?>" style="background-color:<?= (($_SESSION['user_type'] != 'franchise' && $content->verified_status == '0') ? '#f1d0d0;' : '') ?>">

                        <?php if (!empty($edit_description_arr) && ($_SESSION['user_type'] == "admin" || $_SESSION['user_type'] == "developer")) { ?>
                          <input type="hidden" id="rcpt_edt_desc_<?= $content->id ?>" value="<?= $edit_desc_str ?>">
                        <?php } ?>
                        <td>
                          <div class="pretty p-image p-plain selectAllItem ml-2">
                            <input type="checkbox" class="singleCheck" id="<?= $content->id ?>" value="<?= $content->id ?>" />
                            <div class="state">
                              <img class="image" src="<?= RESOURCE_URL ?>images/checkbox.png">
                              <label class="cursor-pointer selectAllItem" for="<?= $content->id ?>"></label>
                            </div>
                          </div>
                        </td>

                        <td class="client-avatar">
                          <a href="<?= $student_image_url ?>" data-fancybox="gallery" data-caption="<?= $content->stu_name ?>">
                            <img alt="image" src="<?= $student_image_url ?>">
                          </a>
                        </td>

                        <td class="project-title" style="width: 17%;">
                          <a href="javascript:void(0);" class="viewStudentDetail" data-sid="<?= $content->student_record_id ?>" data-toggle="tooltip" data-placement="bottom" title="Student Name: <?= $content->stu_name ?>"><?= (strlen($content->stu_name) > 20 ? substr($content->stu_name, 0, 20) . "..." : $content->stu_name) ?></a><br>
                          <small class="notexport">Created <?= date('jS F, Y', strtotime($content->created_at)) ?></small>
                        </td>

                        <td class="project-title" style="width: 17%;">
                          <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student ID: <?= $content->stu_id ?>"><strong><?= $content->stu_id ?></strong></span><br>
                          <small><strong>Student Contact: <?= $content->stu_phone ?></strong></small>
                          <?php //if(!$updatePermission){ 
                          ?>
                          <br><small data-toggle="tooltip" data-placement="bottom" title="Student Status: <?= $student_status ?>" class="cursor-pointer"><strong>Status: <?= $student_status ?></strong></small>
                          <?php //} 
                          ?>
                        </td>

                        <td class="project-title" style="width: 21%;">
                          <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Franchise Name: <?= $content->center_name ?>"><?= (strlen($content->center_name) > 0 ? (strlen($content->center_name) > 14 ? substr($content->center_name, 0, 14) . "..." : $content->center_name) : '<h5 style="color:red;">No Franshise available!</h5>') ?></span><br>
                          <small class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Course Name: <?= $content->course_title ?>"><strong>Course: <?= (strlen($content->course_title) > 25 ? substr($content->course_title, 0, 25) . "..." : $content->course_title) ?></strong></small>
                          <?php //if(!$resultUpdatePermission){ 
                          ?>
                          <br><small><strong>Student Result: <?= ucfirst($content->stu_result) ?></strong></small>
                          <?php //} 
                          ?>
                        </td>

                        <td class="project-title">
                          <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Receipt ID: <?= $content->receipt_id ?>"><strong><?= (strlen($content->receipt_id) > 20 ? substr($content->receipt_id, 0, 20) . "..." : $content->receipt_id) ?></strong></span><br />
                          <small><strong>Receipt Type: <?= ucfirst($content->category) ?></strong></small>
                        </td>

                        <td class="project-title">
                          <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Receipt Amount: <?= $total_receipt_amount ?>"><i class="fa fa-inr" aria-hidden="true"></i>&nbsp;<?= $total_receipt_amount ?></span>
                        </td>

                        <!--<td class="project-title">
                                                  <span class="label label-success cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Receipt Status: <?= ucfirst($content->receipt_status) ?>"><?= ucfirst($content->receipt_status) ?></span> 
                                              </td>-->

                        <td class="project-status">
                          <span class="dropdown">
                            <button class="btn btn-primary product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                            <ul class="dropdown-menu">
                              <?php if ($updatePermission) { ?>
                                <li>
                                  <a href="<?= SITE_URL . '?route=view_receipts' . $extra_query_str . '&pageNo=' . $pageNo . '&actionType=edit&rcpt_id=' . $content->id ?>" class="manageStudentReceipt" data-toggle="tooltip" data-placement="bottom" title="Edit this Receipt for this student"><i class="fa fa-pencil"></i> Edit Receipt</a>
                                </li>
                              <?php } ?>

                              <?php if ($updatePermission && ($_SESSION['user_type'] == "admin" || $_SESSION['user_type'] == "developer")) { ?>

                                <li>
                                  <a href="javascript:void(0)" id="item_<?= $content->receipt_id ?>" class="verified_action" data-vstatus="<?= ($content->verified_status == '1' ? '0' : '1') ?>" data-rid="<?= $content->receipt_id ?>" data-toggle="tooltip" data-placement="bottom" title="Make this receipt's status <?= ($content->verified_status == '1' ? 'not verified' : 'verified') ?>"><i class="<?= ($content->verified_status == '1' ? 'fa fa-check-circle' : 'fa fa-info-circle') ?>"></i> <?= ($content->verified_status == '1' ? 'Verified' : 'Not-Verified') ?>
                                  </a>
                                </li>

                              <?php } ?>

                              <?php if ($content->receipt_status == 'active') { ?>
                                <?php if ($updatePermission) { ?>
                                  <li>
                                    <a href="javascript:void(0)" class="changeRecordStatus" data-rid="<?= $content->id ?>" data-type="student_receipts" data-ptype="Receipt" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Receipt"><i class="fa fa-trash"></i> Block Receipt</a>
                                  </li>

                                  <li>
                                    <a href="javascript:void(0);" class="sendMailToUser" data-rid="<?= $content->id ?>" data-type="student_receipts" data-ptype="Receipt" data-toggle="tooltip" data-placement="bottom" title="Send this receipt to this Student"><i class="fa fa-envelope-o"></i> Send Email</a>
                                  </li>
                                <?php } ?>

                                <?php if (!$updatePermission) { ?>
                                  <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                <?php } ?>
                              <?php } else { ?>
                                <?php if ($updatePermission) { ?>
                                  <li>
                                    <a href="javascript:void(0)" class="changeRecordStatus" data-rid="<?= $content->id ?>" data-type="student_receipts" data-ptype="Receipt" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore this Receipt"><i class="fa fa-refresh"></i> Restore Receipt</a>
                                  </li>
                                <?php } ?>

                                <?php if ($deletePermission) { ?>

                                  <li>
                                    <a href="javascript:void(0)" class="changeRecordStatus" data-rid="<?= $content->id ?>" data-type="student_receipts" data-ptype="Receipt" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete this Receipt"><i class="fa fa-times"></i> Delete Receipt</a>
                                  </li>
                                <?php } ?>

                                <?php if (!$updatePermission && !$deletePermission) { ?>
                                  <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                                <?php } ?>
                              <?php } ?>

                              <?php if (!empty($edit_description_arr) && ($_SESSION['user_type'] == "admin" || $_SESSION['user_type'] == "developer")) { ?>
                                <li>
                                  <a href="javascript:void(0)" class="showReceiptEditDesc" data-rcptid="<?= $content->id ?>" data-toggle="tooltip" data-placement="bottom" title="Show Edit Receipt Details"><i class="fa fa-eye"></i> Show Edit Details</a>
                                </li>
                              <?php } ?>

                              <li>
                                <a href="javascript:void(0);" class="exportReceiptData" data-toggle="tooltip" data-placement="bottom" title="Print PDF file for this receipt" data-rid="<?= $content->id ?>" data-rcptid="<?= $content->receipt_id ?>" data-extype="print" data-toggle="tooltip" data-placement="bottom" title="Print PDF file for this receipt"><i class="fa fa-print"></i>&nbsp;Print Receipt</a>

                                <a href="javascript:void(0);" class="exportReceiptData" data-toggle="tooltip" data-placement="bottom" title="Download PDF file for this receipt" data-rid="<?= $content->id ?>" data-rcptid="<?= $content->receipt_id ?>" data-extype="download" data-toggle="tooltip" data-placement="bottom" title="Download PDF file for this receipt"><i class="fa fa-download"></i>&nbsp;Download</a>
                                <a href="javascript:void(0)" id="export_receipt_href" style="display:none" download>
                                  <button type="button" id="hidden_export_receipt_button">Export</button>
                                </a>
                              </li>
                            </ul>
                          </span>
                        </td>
                      </tr>
                    <?php }
                  } else { ?>
                    <tr>
                      <td colspan="8">No receipt data found...!</td>
                    </tr>
                  <?php } ?>
                </tbody>
                <?php if (!empty($receiptListArr)) { ?>
                  <div class="alert alert-success text-center" role="alert">
                    Total Collection of fees deposited by the students on <?= $rowCount ?>&nbsp;occasions : <a href="javascript:void(0);"
                      id="fetchTotalCollectionReceipt" data-toggle="tooltip" data-placement="top" data-fstatus="pending"
                      data-toggle="tooltip" data-placement="top" title="Fetch Total Collection Based on Selected Criteria"><i class="fa fa-eye-slash"></i>&nbsp;Reveal</a>
                  </div>
                <?php } ?>
              </table>
            </div>

            <?php if (!empty($receiptListArr)) { ?>
              <nav aria-label="Student Page navigation">
                <ul class="pagination">

                  <?php
                  if ($totalPageNo > 1) {
                    if ($pageNo == 1) {
                      $pervious_link = "javascript:void(0);";
                    } else {
                      $pervious_link = SITE_URL . '?route=view_receipts' . $extra_query_str . '&pageNo=' . ($pageNo - 1);
                    }
                    $next_link = SITE_URL . '?route=view_receipts' . $extra_query_str . '&pageNo=' . ($pageNo + 1);
                  }
                  ?>

                  <li class="page-item <?= ($pageNo == 1 ? 'disabled' : '') ?>">
                    <a class="page-link" href="<?= $pervious_link ?>" tabindex="-1">Previous</a>
                  </li>

                  <?php
                  for ($page = 1; $page <= $totalPageNo; $page++) {
                    if ($page < 6 && $pageNo < 5) {
                  ?>

                      <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                        <a class="page-link" href="<?= SITE_URL . '?route=view_receipts' . $extra_query_str . '&pageNo=' . $page ?>"><?= $page ?></a>
                      </li>

                      <?php
                    } elseif ($pageNo >= 5 && $page != $totalPageNo) {
                      if ($page == 1) {
                      ?>

                        <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                          <a class="page-link" href="<?= SITE_URL . '?route=view_receipts' . $extra_query_str . '&pageNo=1' ?>"><?= $page ?></a>
                        </li>

                      <?php } elseif ($page == $pageNo - 2) { ?>

                        <li class="page-item">
                          <a class="page-link" href="javascript:void(0);">...</a>
                        </li>

                      <?php } elseif ($page == $pageNo - 1 || $page == $pageNo || $page == $pageNo + 1) { ?>

                        <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                          <a class="page-link" href="<?= SITE_URL . '?route=view_receipts' . $extra_query_str . '&pageNo=' . $page ?>"><?= $page ?></a>
                        </li>

                      <?php } elseif ($page == $pageNo + 2) { ?>

                        <li class="page-item">
                          <a class="page-link" href="javascript:void(0);">...</a>
                        </li>

                      <?php
                      }
                    } elseif ($pageNo == $totalPageNo) {
                      if ($page == 1) {
                      ?>

                        <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                          <a class="page-link" href="<?= SITE_URL . '?route=view_receipts' . $extra_query_str . '&pageNo=1' ?>"><?= $page ?></a>
                        </li>

                      <?php } elseif ($page >= $totalPageNo - 4) { ?>

                        <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                          <a class="page-link" href="<?= SITE_URL . '?route=view_receipts' . $extra_query_str . '&pageNo=1' ?>"><?= $page ?></a>
                        </li>

                      <?php
                      }
                    } elseif ($page == 6 && $pageNo < 5) {
                      ?>

                      <li class="page-item">
                        <a class="page-link" href="javascript:void(0);">...</a>
                      </li>

                    <?php } elseif ($page == $totalPageNo) { ?>

                      <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                        <a class="page-link" href="<?= SITE_URL . '?route=view_receipts' . $extra_query_str . '&pageNo=' . $page ?>"><?= $page ?></a>
                      </li>

                  <?php }
                  } ?>

                  <li class="page-item">
                    <a class="page-link" href="<?= $next_link ?>">Next</a>
                  </li>
                </ul>
              </nav>
            <?php } ?>
          </div>
        </div>

      </div>
    </div>
</div>

<?php } ?>


<!-- Modal window div-->
<div class="modal fade show" id="showStudentDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content animated flipInY">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Student Details</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body">

        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <h5><i class="fa fa-clone"></i> General Information</h5>
            <div class="table-responsive pt-0">
              <table class="table table-bordered">
                <tr>
                  <th width="30%">Student's Name</th>
                  <td width="2%">:</td>
                  <td id="stu_name">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Father's Name</th>
                  <td width="2%">:</td>
                  <td id="stu_father_name">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Student ID</th>
                  <td width="2%">:</td>
                  <td id="stu_id">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Contact No</th>
                  <td width="2%">:</td>
                  <td id="stu_phone">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Email</th>
                  <td width="2%">:</td>
                  <td id="stu_email">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Date of Birth</th>
                  <td width="2%">:</td>
                  <td id="stu_dob">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Student's Status</th>
                  <td width="2%">:</td>
                  <td id="student_status">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Franchise</th>
                  <td width="2%">:</td>
                  <td id="center_name">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Student's Result</th>
                  <td width="2%">:</td>
                  <td id="stu_result">125</td>
                </tr>
              </table>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <h5><i class="fa fa-clone pr-1"></i>Other Information</h5>
            <div class="table-responsive pt-0">
              <table class="table table-bordered">
                <tr>
                  <th width="30%">Course Name</th>
                  <td width="2%">:</td>
                  <td id="course_title">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Address</th>
                  <td width="2%">:</td>
                  <td id="stu_address">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Qualification</th>
                  <td width="2%">:</td>
                  <td id="stu_qualification">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Gender</th>
                  <td width="2%">:</td>
                  <td id="stu_gender">Not Available</td>
                </tr>

                <tr>
                  <th width="30%">Marital Status</th>
                  <td width="2%">:</td>
                  <td id="stu_marital_status">Not Available</td>
                </tr>
              </table>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-12 col-md-12 col-sm-12">
            <h5><i class="fa fa-clone pr-1"></i>Receipt Information</h5>
            <div class="table-responsive pt-0">
              <table class="table table-bordered">
                <tr>
                  <th width="30%">Course Fees</th>
                  <td width="2%">:</td>
                  <td id="course_fees">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Course Discount</th>
                  <td width="2%">:</td>
                  <td id="course_discount">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Net Course Fees</th>
                  <td width="2%">:</td>
                  <td id="net_course_fees">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Advance Fees (Included in Fees paid so far)</th>
                  <td width="2%">:</td>
                  <td id="stu_advanced_fees">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Fees Paid Before DR</th>
                  <td width="2%">:</td>
                  <td id="stu_fees_paid_before_dr">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Fees Paid So Far</th>
                  <td width="2%">:</td>
                  <td id="fees_paid">Not Available</td>
                </tr>
                <tr>
                  <th width="30%">Fees Due</th>
                  <td width="2%">:</td>
                  <td id="fees_due">Not Available</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal ends here -->

<!-- Modal window div-->
<div class="modal fade show" id="receiptPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content animated fadeIn">
      <div class="modal-header">
        <h3 class="modal-title" id="receipt_modal_title">Receipt Preview Window</h3>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>

      <div class="modal-body">
        <div class="row">
          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label>Receipt Category:</label>
              <input type="text" id="preview_receipt_category" class="form-control" readonly>
            </div>
          </div>

          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label>Receipt Amount:</label>
              <input type="text" id="preview_receipt_amount" class="form-control" readonly>
            </div>
          </div>

          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label>Late Fine:</label>
              <input type="text" id="preview_late_fine" class="form-control" readonly>
            </div>
          </div>

          <div class="col-lg-6 col-md-6 col-sm-12">
            <div class="form-group">
              <label>Additional Fees:</label>
              <input type="text" id="preview_extra_fees" class="form-control" readonly>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal ends here -->

<!-- Modal window div-->
<div class="modal inmodal" id="viewUserReceiptChangesModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated flipInY">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="category_modal_title">View Receipt Changes</h4>
      </div>
      <div class="modal-body" id="receipt_changes_desc">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal ends here -->

<!-- Custom JS -->
<script>
  $(document).ready(function() {
    //student id for this page
    var stu_row_id = "<?= $content->stu_id ?>";

    //Multiple select dropdown 
    $('.course').select2({
      width: "84.5%",
      allowClear: true
    });
    $('.franchise').select2({
      width: "89%",
      allowClear: true
    });
    $('.record_status').select2({
      width: "84%",
      allowClear: true
    });
    $('.category_id').select2({
      width: "98.5%"
    });

    $('#receipt_list_tbl').filterTable('#receipt_tbl_filter');

    $('.category_id').on('change', function() {
      $(this).valid();

      var categoryData = $(this).select2('data');
      var category_name = categoryData[0].text;

      if (category_name == "Admission Fees") {
        $("#receipt_amount_txt").text("Admission Fees");
        $("#additional_fees_text").text("Registration Fees");
      } else {
        $("#receipt_amount_txt").text("Receipt Amount");
        $("#additional_fees_text").text("Additional Fees");
      }

      if (category_name == "Other Fees") {
        $("#og_receipt_amount").attr('readonly', true);
        $("#late_fine").attr('readonly', true);
      } else {
        $("#og_receipt_amount").attr('readonly', false);
        $("#late_fine").attr('readonly', false);
      }
    });

    function removeFileFromServer(file_upload_dir) {
      var formData = {
        action: "removeFileFromServer",
        file_upload_dir: file_upload_dir
      };

      $.ajax({
        url: ajaxControllerHandler,
        method: 'POST',
        data: formData,
        beforeSend: function() {
          //$('.tooltip').hide();
          //$('.content_div_loader').addClass('sk-loading');
        },
        success: function(responseData) {
          var result = JSON.parse(responseData);
          //console.log(result);
          return true;
        }
      });
    }

    /*---Input date & time control--*/
    var date = new Date();
    var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

    $('.datepicker').datepicker({
      format: "dd/mm/yyyy",
      todayBtn: "linked",
      keyboardNavigation: true,
      todayHighlight: true,
      //startDate: today,
      forceParse: false,
      calendarWeeks: true,
      autoclose: true
    });
    /*------- Ends Here ---------*/

    //handling receipt session duration 
    $('.datepicker').datepicker()
      .on("input change", function(e) {

        e.preventDefault();
        $(this).valid();

        var d1 = $('#receipt_season_start').val();
        var d2 = $('#receipt_season_end').val();

        var startDate = new Date(d1);
        var endDate = new Date(d2);

        if (startDate.getTime() > endDate.getTime()) {
          toastr.error("Receipt season start date can't be greater than end date.", "Error!");
          $('#manage_receipt').attr('disabled', true);
          $(this).val('');
          return false;
        } else {
          $('#manage_receipt').attr('disabled', false);
          return true;
        }
      });

    $(document).on('keyup', '#extra_fees', function(event) {

      var additional_fees = $("#extra_fees").val();

      if (additional_fees) {
        $("#additional_fees_desc_div").removeClass('d-none');
      } else {
        $("#additional_fees_desc_div").addClass('d-none');
      }

    });

    //Handling show recipt edit details 
    $(document).on('click', '.showReceiptEditDesc', function() {
      var receipt_id = $(this).data('rcptid');
      var edit_desc = $("#rcpt_edt_desc_" + receipt_id).val();

      $('#receipt_changes_desc').html(edit_desc);
      $('#viewUserReceiptChangesModal').modal('show');
      return true;
    });

    $(document).on('click', '#preview_receipt', function(event) {

      var receipt_category_data = $('#category_id').select2('data');
      var receipt_amount = $('#og_receipt_amount').val();
      var late_fine = $('#late_fine').val();
      var extra_fees = $('#extra_fees').val();

      if (receipt_category_data[0].id != '') {
        $('#preview_receipt_category').val(receipt_category_data[0].text);
      } else {
        $('#preview_receipt_category').val('No input provided!');
      }

      if (receipt_amount) {
        $('#preview_receipt_amount').val('Rs. ' + receipt_amount);
      } else {
        $('#preview_receipt_amount').val('No input provided!');
      }

      if (late_fine) {
        $('#preview_late_fine').val('Rs. ' + late_fine);
      } else {
        $('#preview_late_fine').val('No input provided!');
      }

      if (extra_fees) {
        $('#preview_extra_fees').val('Rs. ' + extra_fees);
      } else {
        $('#preview_extra_fees').val('No input provided!');
      }

      $('#receiptPreviewModal').modal('show');
    });

    //Handling export receipt pdf
    $(document).on('click', '.exportReceiptData', function(event) {
      event.preventDefault();

      var receipt_row_id = $(this).data('rid');
      var receipt_id = $(this).data('rcptid');
      var export_type = $(this).data('extype');

      var formData = {
        action: "exportStudentReceiptPdf",
        receipt_row_id: receipt_row_id
      };

      $.ajax({
        url: ajaxControllerHandler,
        method: 'POST',
        data: formData,
        beforeSend: function() {
          $('.tooltip').hide();
          $('.content_div_loader').addClass('sk-loading');
        },
        success: function(responseData) {
          var result = JSON.parse(responseData);
          //console.log(result);
          setTimeout(function() {
            //Disabling loader
            $('.content_div_loader').removeClass('sk-loading');

            if (export_type == "download") {
              $('#export_receipt_href').attr("href", result.file_url);
              $("#hidden_export_receipt_button").click();
            } else {
              //Generating dynamic button for print pdf
              var printPdfBtn = '<button id="print_receipt_btn" style="display:none;" onclick="printJS(\'' + result.file_url + '\')">' +
                '<i class="fa fa-print"></i> Print</button>';

              if ($('#print_receipt_btn')[0]) {
                $('#print_receipt_btn').remove();
              }
              $("body").append(printPdfBtn);
              $("#print_receipt_btn").click();
            }
          }, 50);
          //Removing file from server
          setTimeout(function() {
            removeFileFromServer(result.file_upload_dir);
          }, 5000);
          return true;
        }
      });
    });

    //handling manage receipt form             
    $(document).on('submit', '#manage_receipt_form', function(event) {
      event.preventDefault();

      var stu_id = $("#stu_id").val();
      var formData = new FormData(this);

      swal({
        title: "Are you sure?",
        text: "You may ne be able to update the receipt later?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, Go ahead!",
        closeOnConfirm: true
      }, function() {
        $.ajax({
          url: ajaxControllerHandler,
          method: 'POST',
          data: formData,
          contentType: false,
          processData: false,
          beforeSend: function() {
            //$('.content_div_loader').addClass('sk-loading');
            //$('#manage_receipt').attr('disabled',true);
          },
          success: function(responseData) {
            var data = JSON.parse(responseData);
            $('#manage_receipt').attr('disabled', false);
            //console.log(responseData);
            if (data.check == 'success') {
              //reseting form data
              $('#manage_receipt_form')[0].reset();
              //Disabling loader
              $('.content_div_loader').removeClass('sk-loading');
              if (data.last_insert_id > 0) {
                var successText = "Receipt has been successfully created! <br><strong>Your Receipt ID: <span style='color:#f50101;'>" + data.receipt_id + "</span></strong>";
              } else {
                var successText = "Receipt has been successfully updated! <br><strong>Your Receipt ID: <span style='color:#f50101;'>" + data.receipt_id + "</span></strong>";
              }

              var redirect_url = SITE_URL + "?route=view_receipts&stu_id=" + stu_id;

              setTimeout(function() {
                swal({
                  title: "Great!",
                  html: true,
                  text: successText,
                  type: "success"
                }, function() {
                  window.location = redirect_url;
                });
              }, 500);
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
              setTimeout(function() {
                swal({
                  title: "Oops!",
                  text: message,
                  type: "error"
                });
              }, 500);
              return false;
            }
          }
        });
      });
    });

    //Handling hard export for student receipt table
    $(document).on('click', '.export_student_receipt_data', function(event) {
      event.preventDefault();
      var record_status = $('#record_status').val();

      var course_id = $('#course_id').val();
      var franchise_id = $('#franchise_id').val();
      var student_id = $('#student_id').val();

      var receipt_season_start = $('#receipt_search_start').val();
      var receipt_season_end = $('#receipt_search_end').val();

      var created = $('#created').val();
      var dataType = "receipt";
      var export_table = "receipt";
      var export_method = $(this).data('export');

      var formData = {
        export_table: export_table,
        record_status: record_status,
        course_id: course_id,
        franchise_id: franchise_id,
        receipt_season_start: receipt_season_start,
        receipt_season_end: receipt_season_end,
        created: created,
        student_id: student_id,
        export_method: export_method
      };

      //Clearing hyperlink href for fresh download
      $('#export_record_href').attr("href", "javascript:void(0);");
      $('#export_record_href').attr("download", "");

      if (export_method == "excel") {
        var exportAlertText = "All table data will be exported as CSV file!";
      } else {
        var exportAlertText = "All table data will be exported as PDF & this may take a while!";
      }

      //show sweetalert success
      swal({
        title: "Are you sure?",
        text: exportAlertText,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, Go ahead...",
        closeOnConfirm: true
      }, function() {

        $.ajax({
          url: exportTableDataController,
          method: 'POST',
          data: formData,
          beforeSend: function() {
            $('.content_div_loader').addClass('sk-loading');
          },
          success: function(responseData) {
            $('.content_div_loader').removeClass('sk-loading');
            //console.log(responseData);
            var result = JSON.parse(responseData);
            $('#export_record_href').attr("href", result.file_url);
            $("#hidden_export_button").click();
            //Removing file from server
            setTimeout(function() {
              removeFileFromServer(result.file_upload_dir);
            }, 5000);
            return true;
          }
        });
      });
    });

    //Handling fetching total collection of receipt
    $(document).on('click', '#fetchTotalCollectionReceipt', function(event) {
      event.preventDefault();
      
      var fetch_status = $(this).data('fstatus');

      if(fetch_status == "pending"){
        var record_status = $('#record_status').val();

        var course_id = $('#course_id').val();
        var franchise_id = $('#franchise_id').val();
        var student_id = $('#student_id').val();

        var receipt_season_start = $('#receipt_search_start').val();
        var receipt_season_end = $('#receipt_search_end').val();
        var created = $('#created').val();
        
        var formData = {
          action: "fetchReceiptTotal",
          record_status: record_status,
          course_id: course_id,
          franchise_id: franchise_id,
          receipt_season_start: receipt_season_start,
          receipt_season_end: receipt_season_end,
          created: created,
          student_id: student_id,
        };

        //set toastr option
        toastr.options = {
          closeButton: true,
          progressBar: true,
          showMethod: 'slideDown',
          timeOut: 2000,
        };
      
        $.ajax({
          url: ajaxControllerHandler,
          method: 'POST',
          data: formData,
          beforeSend: function() {
            $('.content_div_loader').addClass('sk-loading');
          },
          success: function(responseData) {
            $('.content_div_loader').removeClass('sk-loading');
            //console.log(responseData);
            var result = JSON.parse(responseData);
            var receiptData = result.receiptData;
            var toastrText = result.message;

            if (result.check == 'success') {

              if(student_id.length > 0){
                var total_collection = parseFloat(receiptData.receipt_amount);
              }else{
                var total_collection = (parseFloat(receiptData.receipt_amount) || 0) + (parseFloat(receiptData.late_fine) || 0) + (parseFloat(receiptData.extra_fees) || 0);
              }
              //Show success toast
              toastr.success(toastrText, 'Success!');
              $("#fetchTotalCollectionReceipt").data('fstatus', 'completed');
              $("#fetchTotalCollectionReceipt").html(`<i class="fa fa-inr"></i> ${total_collection}`);
            }else{
              toastr.error(toastrText, 'Error!');
              $("#fetchTotalCollectionReceipt").data('fstatus', 'pending');
              $("#fetchTotalCollectionReceipt").html(`<i class="fa fa-eye-slash"></i>&nbsp;Reveal`);
            }
            return;
          }
        });
      }  
    });
  });

  //Configuring page records fetching params
  $(document).on('submit', '#fetch_verified_records', function(event) {
    event.preventDefault();
    var verified_status = $('#verified_status').val();

    if (verified_status === null) {
      window.location = SITE_URL + "?route=view_receipts";
    } else {
      $('#fetch_item_data').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Fetching').attr('disabled', true);
      setTimeout(function() {
        $('#fetch_item_data').html('<i class="fa fa-search"></i>&nbsp;Fetch Data').attr('disabled', false);
        //show sweetalert success
        swal({
          title: "Great!",
          text: "Data has been successfully fetched!",
          type: "success",
          allowEscapeKey: false,
          allowOutsideClick: false
        }, function() {
          window.location = SITE_URL + "?route=view_receipts&verified_status=" + verified_status;
        });
      }, 500);
      return true;
    }
  });

  //Configuring fetching all page records fetching params
  $(document).on('submit', '#fetch_student_receipt_records', function(event) {
    event.preventDefault();
    var student_id = $('#student_id').val();
    var record_status = $('#record_status').val();
    var page_route = $('#page_route').val();

    var course_id = $('#course_id').val();
    var franchise_id = $('#franchise_id').val();
    var created = $('#created').val();

    var receipt_season_start = $('#receipt_search_start').val();
    var receipt_season_end = $('#receipt_search_end').val();

    if (record_status === null) {
      window.location = SITE_URL + "?route=" + page_route;
    } else {
      $('#fetch_item_data').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Fetching').attr('disabled', true);
      setTimeout(function() {
        $('#fetch_item_data').html('<i class="fa fa-search"></i>').attr('disabled', false);
        //show sweetalert success
        swal({
          title: "Great!",
          text: "Data has been successfully fetched!",
          type: "success",
          allowEscapeKey: false,
          allowOutsideClick: false
        }, function() {

          var redirect_url = SITE_URL + "?route=" + page_route;

          if (student_id.length > 0) {
            redirect_url += "&stu_id=" + student_id;
          }

          if (receipt_season_start.length > 0) {
            redirect_url += "&receipt_season_start=" + receipt_season_start;
          }

          if (receipt_season_end.length > 0) {
            redirect_url += "&receipt_season_end=" + receipt_season_end;
          }

          if (course_id > 0) {
            redirect_url += "&course_id=" + course_id;
          }

          if (franchise_id > 0) {
            redirect_url += "&franchise_id=" + franchise_id;
          }

          if (created.length > 0) {
            redirect_url += "&created=" + created;
          }

          redirect_url += "&record_status=" + record_status;

          window.location = redirect_url;

        });
      }, 500);
      return true;
    }
  });

  /*Status change handler*/
  $(document).on('click', '.verified_action', function() {
    var action = "updateReceiptVerifiedStatus";
    var receipt_id = $(this).data('rid');
    var verified_status = $(this).data('vstatus');

    var thisItem = $(this);

    if (verified_status == '1') {
      var toastrText = 'This receipt has been marked as verified successfully!';
    } else {
      var toastrText = 'This receipt has been marked as not verified successfully!';
    }
    //set toastr option
    toastr.options = {
      closeButton: true,
      progressBar: true,
      showMethod: 'slideDown',
      timeOut: 2000,
    };

    var formData = {
      action: action,
      receipt_id: receipt_id,
      verified_status: verified_status
    };

    $.ajax({
      url: ajaxControllerHandler,
      method: 'POST',
      data: formData,
      beforeSend: function() {
        $('.content_div_loader').addClass('sk-loading');
      },
      success: function(responseData) {
        //console.log(responseData); 
        var data = JSON.parse(responseData);
        //Disabling loader
        $('.content_div_loader').removeClass('sk-loading');

        //Check response
        if (data.check == 'success') {
          if (verified_status == '1') {
            $(thisItem).data('vstatus', '0');
            $(thisItem).attr('title', "Make this receipt's status not verified!");
            $(thisItem).html('<i class="fa fa-check-circle"></i> Verified');

            //Chnage table tr background color
            $("#rcpt_tr_" + receipt_id).css({
              'background-color': ''
            });
            //Show success toast
            toastr.success(toastrText, 'Success!');

          } else {
            $(thisItem).data('vstatus', '1');
            $(thisItem).attr('title', "Make this receipt's status verified!");
            $(thisItem).html('<i class="fa fa-info-circle"></i> Not Verified');
            //Chnage table tr background color
            $("#rcpt_tr_" + receipt_id).css({
              'background-color': '#f1d0d0'
            });
            //Show warning toast
            toastr.warning(toastrText, 'Success!');
          }
          return true;
        } else {
          if (data.message.length > 0) {
            var toastrErrorText = data.message;
          } else {
            var toastrErrorText = 'Something went wrong! Please try again.'
          }
          //show toastr error
          toastr.options.onHidden = function() {
            window.location.reload();
          }
          toastr.error(toastrErrorText, 'Error!');
          return false;
        }

      }
    });
  });

  //handling student detail fetch form
  $(document).on('click', '.viewStudentDetail', function(event) {
    event.preventDefault();

    var student_id = $(this).data('sid');
    var formData = {
      action: "fetchStudentDetailInModal",
      student_id: student_id
    }

    //Calling ajax request
    $.ajax({
      url: ajaxControllerHandler,
      method: 'POST',
      data: formData,
      beforeSend: function() {
        $('.content_div_loader').addClass('sk-loading');
      },
      success: function(responseData) {
        var data = JSON.parse(responseData);
        //console.log(responseData);
        if (data.check == 'success') {
          //Populating student data in student detail div
          var studentDetail = data.studentDetail;
          //console.log(studentDetail); 
          //populating student detail div
          $('#stu_id').html('<b>' + studentDetail.stu_id + '</b>');
          $('#stu_phone').text(studentDetail.stu_phone);
          $('#stu_result').html('<b>' + studentDetail.stu_result + '</b>');
          $('#focus_stu_name').text(studentDetail.stu_name);
          $('#stu_name').text(studentDetail.stu_name);
          $('#stu_father_name').text(studentDetail.stu_father_name);
          $('#stu_address').text(studentDetail.stu_address);
          $('#stu_dob').text(studentDetail.stu_dob);
          $('#course_title').text(studentDetail.course_title);
          $('#center_name').text(studentDetail.center_name);
          $('#stu_email').text(studentDetail.stu_email);
          $('#stu_qualification').text(studentDetail.stu_qualification);
          $('#student_status').html('<b>' + studentDetail.student_status + '</b>');
          $('#stu_gender').text(studentDetail.stu_gender);
          $('#stu_marital_status').text(studentDetail.stu_marital_status);

          //Receipt Data display
          if (studentDetail.stu_course_fees) {
            var course_fees = parseInt(studentDetail.stu_course_fees);
          } else {
            var course_fees = parseInt(studentDetail.course_default_fees);
          }

          $('#course_fees').text('Rs.' + course_fees);

          if (studentDetail.stu_course_discount) {
            var stu_course_discount = parseInt(studentDetail.stu_course_discount);
          } else {
            var stu_course_discount = parseInt('0');
          }

          $('#course_discount').text('Rs.' + stu_course_discount);

          var net_course_fees = course_fees - stu_course_discount;

          $('#net_course_fees').html('Rs.' + net_course_fees);

          if (studentDetail.advanced_fees) {
            var advanced_fees = parseInt(studentDetail.advanced_fees);
            $('#stu_advanced_fees').text('Rs.' + advanced_fees + ' has been deposited on ' + studentDetail.advance_fees_date);
          } else {
            var advanced_fees = parseInt('0');
            $('#stu_advanced_fees').text('Rs.0');
          }

          if (studentDetail.course_fees_paid) {
            var stu_receipt_paid = parseInt(studentDetail.course_fees_paid);
          } else {
            var stu_receipt_paid = parseInt('0');
          }

          if (studentDetail.fees_paid_before_dr) {
            var fees_paid_before_dr = parseInt(studentDetail.fees_paid_before_dr);
            $('#stu_fees_paid_before_dr').text('Rs.' + studentDetail.fees_paid_before_dr + ' has been deposited before digital receipt.');
          } else {
            var fees_paid_before_dr = parseInt('0');
            $('#stu_fees_paid_before_dr').text('Rs.0');
          }

          var course_fees_paid = parseInt(stu_receipt_paid + advanced_fees + fees_paid_before_dr);

          if (course_fees_paid > 0) {
            $('#fees_paid').text('Rs.' + course_fees_paid);
          } else {
            $('#fees_paid').text('No fees has been paid yet!');
          }

          var fees_due = parseInt(net_course_fees - course_fees_paid);

          $('#fees_due').text('Rs.' + fees_due);

          setTimeout(function() {
            //disabling loader
            $('.content_div_loader').removeClass('sk-loading');
            //Display modal
            $("#showStudentDetailModal").modal("show");
            return true;
          }, 500);
        } else {
          //show sweetalert success
          if (data.message.length > 0) {
            var message = data.message;
          } else {
            var message = "Something went wrong";
          }
          return false;
        }
      }
    });
  });
</script>
</body>

</html>