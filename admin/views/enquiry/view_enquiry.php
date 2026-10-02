<?php
if (isset($_GET['record_status'])) {
  if ($_GET['record_status'] == 'active') {
    $record_status = 'active';
  } else {
    $record_status = 'blocked';
  }
} else {
  $record_status = 'active';
}

$enquiryListArr = $pageContent['pageData']['enquiry_data']['data'];

$rowCount = $pageContent['pageData']['enquiry_data']['row_count'];
$limit = $pageContent['pageData']['enquiry_data']['limit'];
$totalPageNo = ceil($rowCount / $limit);
$pageNo = $pageContent['pageData']['enquiry_data']['pageNo'];
$offset = ($pageNo - 1) * $limit;

//Constructing student form back url
$queries = array();
parse_str($_SERVER['QUERY_STRING'], $queries);

$extra_query_str = '';

foreach ($queries as $key => $query_val) {
  if ($key != "route" && $key != 'actionType' && $key != 'rcpt_id') {
    $extra_query_str .= "&" . $key . "=" . $query_val;
  }
}

//Course data
$courseArr = $pageContent['pageData']['course_data'];

//Fetching page action permission
$deletePermission = $this->permissionService->checkUserRolePermission("delete_enquiry");

/*print'<pre>';
  print_r($enquiryListArr);
  print'</pre>';exit;*/
?>

<div class="wrapper wrapper-content fadeInRight">
  <div class="row">
    <div class="col-lg-12">
      <div class="ibox ">
        <div class="ibox-title">
          <h5>Fetch Enquiry Data based on different parameters </h5>
          <div class="ibox-tools">
            <a class="collapse-link">
              <i class="fa fa-chevron-up"></i>
            </a>
          </div>
        </div>
        <div class="ibox-content">
          <form id="fetch_all_enquiry_records" class="<?= ($_GET['fetch_type'] == 'all' || !isset($_GET['fetch_type']) ? '' : 'd-none') ?>" onsubmit="return false;">
            <div class="row">
              <div class="col-lg-4 m-b-xs">
                <select class="form-control-sm form-control input-s-sm inline record_status" name="record_status" id="record_status" required>
                  <option selected disabled value>Select a Status to proceed</option>
                  <option value="active" <?= (($record_status == 'active' || $record_status == '') ? 'selected' : '') ?>>Active</option>
                  <option value="blocked" <?= ($record_status == 'blocked' ? 'selected' : '') ?>>Blocked</option>
                </select>

                <span class="cursor-pointer pl-2" data-toggle="tooltip" data-placement="bottom" title="Choose Record Status"><i class="fa fa-question-circle"></i></span>
              </div>

              <div class="col-lg-3 m-b-xs pl-1">
                <select class="form-control-sm form-control input-s-sm inline record_limit" name="record_limit" id="record_limit">
                  <option selected disabled value>Select Data Limit Per Page</option>
                  <option value="10" <?= (($limit == '10') ? 'selected' : '') ?>>10</option>
                  <option value="20" <?= ($limit == '20' ? 'selected' : '') ?>>20</option>
                  <option value="30" <?= ($limit == '30' ? 'selected' : '') ?>>30</option>
                  <option value="<?= $rowCount ?>" <?= ($limit == $rowCount ? 'selected' : '') ?>>Fetch All Records</option>
                </select>

                <span class="cursor-pointer pl-2" data-toggle="tooltip" data-placement="bottom" title="Choose Record Limit Per Page"><i class="fa fa-question-circle"></i></span>
              </div>


              <div class="col-lg-3 m-b-xs">
                <select class="form-control-sm form-control input-s-sm inline pageNo" name="pageNo" id="pageNo">
                  <option selected disabled value>Select Page No</option>
                  <?php
                  if ($totalPageNo >= 1) {
                    for ($pi = 1; $pi <= $totalPageNo; $pi++) {
                  ?>
                      <option value="<?= $pi ?>" <?= ($pageNo == $pi ? 'selected' : '') ?>><?= $pi ?>
                      </option>
                    <?php }
                  } else { ?>
                    <option value="1" selected>1</option>
                  <?php } ?>
                </select>

                <span class="cursor-pointer pl-2" data-toggle="tooltip" data-placement="bottom" title="Choose Page No."><i class="fa fa-question-circle"></i></span>
              </div>

              <input type="hidden" name="page_route" id="page_route" value="<?= $_GET['route'] ?>">

            </div>

            <div class="row">
              <div class="col-lg-4 ml-0">
                <select class="course" name="course_id" id="course_id" data-placeholder="Choose a Course first..." tabindex="2">
                  <option></option>
                  <?php foreach ($courseArr as $course) {
                  ?>
                    <option value="<?= $course->id ?>" <?= ($_GET['course_id'] == $course->id ? 'selected' : '') ?>><?= $course->course_title ?></option>
                  <?php } ?>
                </select>

                <span class="cursor-pointer pl-2" data-toggle="tooltip" data-placement="bottom" title="Select a Course"><i class="fa fa-question-circle"></i></span>
              </div>

              <div class="col-lg-3 pl-1">
                <select class="form-control-sm form-control input-s-sm inline enquiry_type" name="enquiry_type" id="enquiry_type">
                  <option selected disabled value>Choose a Enquiry type first...</option>
                  <option value="course" <?= ($_GET['enquiry_type'] == 'course' ? 'selected' : '') ?>>Course</option>
                  <option value="others" <?= ($_GET['enquiry_type'] == 'others' ? 'selected' : '') ?>>Others</option>
                </select>

                <span class="cursor-pointer pl-2" data-toggle="tooltip" data-placement="bottom" title="Select a Franchise"><i class="fa fa-question-circle"></i></span>
              </div>

              <div class="col-lg-3">
                <button class="btn btn-primary" type="submit" id="fetch_item_data"><i class="fa fa-search"></i>&nbsp;Fetch Enquiry Record</button>
              </div>

              <div class="col-lg-2"></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="ibox">
    <div class="ibox-title">
      <h5>Student List with all details</h5>
      <div class="ibox-tools">

        <a href="<?= SITE_URL ?>?route=view_enquiry" class="table-action-info" data-toggle="tooltip" data-placement="bottom" title="Refresh Enquiry Data"><i class="fa fa-refresh"></i></a>

        <?php if ($record_status == 'active') { ?>
          <?php if ($deletePermission) { ?>
            <a href="javascript:void(0);" class="table-action-warning changeRecordStatus" data-rid="all" data-type="enquiry" data-ptype="Enquiry" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Trash single or multiple rows"><i class="fa fa-trash"></i></a>
          <?php } ?>
        <?php } else { ?>
          <?php if ($deletePermission) { ?>
            <a href="javascript:void(0);" class="table-action-success changeRecordStatus" data-rid="all" data-type="enquiry" data-ptype="Enquiry" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Restore single or multiple rows"><i class="fa fa-recycle"></i></a>
          <?php } ?>

          <?php if ($deletePermission) { ?>
            <a href="javascript:void(0);" class="table-action-danger changeRecordStatus" data-rid="all" data-type="enquiry" data-ptype="Enquiry" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Delete single or multiple rows"><i class="fa fa-times"></i></a>
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

        <input type="text" class="form-control form-control-sm m-b-xs" id="enquiry_tbl_filter" placeholder="Search anything in enquiry for current page...">

        <div class="mt-2">
          <?php if (count($enquiryListArr) > 0) { ?>
            <strong>Showing <?= $offset + 1 ?> to <?= (count($enquiryListArr) == $limit ? $limit * $pageNo : count($enquiryListArr)) ?> of <?= $rowCount ?> entries</strong>
          <?php } else { ?>
            <strong>No Data Found!</strong>
          <?php } ?>
        </div>

        <table class="table table-striped table-bordered table-hover dataTables-example text-center mt-3" id="enquiry_list_tbl">
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

              <!-- <th style="width:9%;">SL No.</th>-->
              <th class="sorting_desc_disabled" style="width:15%;">Name<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
              <th class="sorting_desc_disabled">Email<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
              <th class="sorting_desc_disabled" style="width:12%;">Contact No<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

              <th class="sorting_desc_disabled">City<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
              <th class="sorting_desc_disabled">Subject<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
              <th class="sorting_desc_disabled">Type<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>
              <th class="sorting_desc_disabled notexport">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($enquiryListArr as $index => $content) {
              if ($content->enquiry_type == 'course') {
                $subject  = $content->course_title;
              } else {
                $subject = $content->subject;
              }
            ?>
              <tr>
                <td>
                  <div class="pretty p-image p-plain selectAllItem ml-2">
                    <input type="checkbox" class="singleCheck" id="<?= $content->id ?>" value="<?= $content->id ?>" />
                    <div class="state">
                      <img class="image" src="<?= RESOURCE_URL ?>images/checkbox.png">
                      <label class="cursor-pointer selectAllItem" for="<?= $content->id ?>"></label>
                    </div>
                  </div>
                </td>

                <!--<td><?= $index + 1 ?></td>-->

                <td class="project-title">
                  <a href="<?= SITE_URL ?>?route=edit_student&id=<?= $content->id ?>" data-toggle="tooltip" data-placement="bottom" title="User Name: <?= $content->user_name ?>"><?= $content->user_name ?></a>
                  <br />
                  <small>Created <?= date('jS F, Y', strtotime($content->created_at)) ?></small>
                </td>

                <td class="project-title">
                  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="User Email: <?= $content->user_email ?>"><?= $content->user_email ?></span>
                </td>

                <td class="project-title">
                  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Contact No: <?= $content->user_phone ?>"><?= $content->user_phone ?>
                  </span>
                </td>

                <td class="project-title">
                  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="User City: <?= $content->user_city ?>"><?= $content->user_city ?></span>
                </td>

                <td class="project-title">
                  <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Subject: <?= $subject ?>"><?= $subject ?></span>
                </td>

                <td class="project-status">
                  <span class="label label-info cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Enquiry type: <?= ucfirst($content->enquiry_type) ?>"><?= ucfirst($content->enquiry_type) ?></span>
                </td>

                <td class="project-status">
                  <span class="dropdown">
                    <button class="btn btn-primary product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                    <ul class="dropdown-menu">

                      <li>
                        <a href="javascript:void(0)" id="view_user_message" data-type="student" data-message="<?= strip_tags(stripslashes($content->user_message)) ?>" data-toggle="tooltip" data-placement="bottom" title="View this Message"><i class="fa fa-eye"></i> View Message</a>
                      </li>

                      <?php if ($content->record_status == 'active') { ?>
                        <?php if ($deletePermission) { ?>
                          <li>
                            <a href="javascript:void(0)" class="changeRecordStatus" data-rid="<?= $content->id ?>" data-type="enquiry" data-ptype="Enquiry" data-rstatus="blocked" data-toggle="tooltip" data-placement="bottom" title="Block this Enquiry"><i class="fa fa-trash"></i> Block Enquiry</a>
                          </li>
                        <?php } ?>

                        <?php if (!$deletePermission) { ?>
                          <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                        <?php } ?>
                      <?php } else { ?>
                        <?php if ($deletePermission) { ?>
                          <li>
                            <a href="javascript:void(0)" class="changeRecordStatus" data-rid="<?= $content->id ?>" data-type="student" data-ptype="Student" data-rstatus="active" data-toggle="tooltip" data-placement="bottom" title="Block this Student"><i class="fa fa-refresh"></i> Restore Student</a>
                          </li>
                        <?php } ?>

                        <?php if ($deletePermission) { ?>
                          <li>
                            <a href="javascript:void(0)" class="changeRecordStatus" data-rid="<?= $content->id ?>" data-type="student" data-ptype="Student" data-rstatus="delete" data-toggle="tooltip" data-placement="bottom" title="Block this Student"><i class="fa fa-times"></i> Delete Student</a>
                          </li>
                        <?php } ?>

                        <?php if (!$updatePermission && !$deletePermission) { ?>
                          <li><a href="javascript:void(0)" title="You have no action to perform for this item!">You have no action to perform for this item! </a></li>
                        <?php } ?>

                      <?php } ?>
                    </ul>
                  </span>
                </td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>

      <nav aria-label="Enquiry Page navigation">
        <ul class="pagination">

          <?php
          if ($totalPageNo > 1) {
            if ($pageNo == 1) {
              $pervious_link = "javascript:void(0);";
            } else {
              $pervious_link = SITE_URL . '?route=view_enquiry' . $extra_query_str . '&pageNo=' . ($pageNo - 1);
            }

            if ($pageNo < $totalPageNo) {
              $next_link = SITE_URL . '?route=view_enquiry' . $extra_query_str . '&pageNo=' . ($pageNo + 1);
            } else {
              $next_link = "javascript:void(0);";
            }
          }
          ?>

          <li class="page-item <?php //echo ($pageNo == 1 ? 'disabled' : ''); 
                                ?>">
            <a class="page-link" href="<?= $pervious_link ?>" tabindex="-1">Previous</a>
          </li>

          <?php
          for ($page = 1; $page <= $totalPageNo; $page++) {
            if ($page < 6 && $pageNo < 5) {
          ?>

              <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                <a class="page-link" href="<?= SITE_URL . '?route=view_enquiry' . $extra_query_str . '&pageNo=' . $page ?>"><?= $page ?></a>
              </li>

              <?php
            } elseif ($pageNo >= 5 && $page != $totalPageNo) {
              if ($page == 1) {
              ?>

                <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                  <a class="page-link" href="<?= SITE_URL . '?route=view_enquiry' . $extra_query_str . '&pageNo=1' ?>"><?= $page ?></a>
                </li>

              <?php } elseif ($page == $pageNo - 2) { ?>

                <li class="page-item">
                  <a class="page-link" href="javascript:void(0);">...</a>
                </li>

              <?php } elseif ($page == $pageNo - 1 || $page == $pageNo || $page == $pageNo + 1) { ?>

                <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                  <a class="page-link" href="<?= SITE_URL . '?route=view_enquiry' . $extra_query_str . '&pageNo=' . $page ?>"><?= $page ?></a>
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
                  <a class="page-link" href="<?= SITE_URL . '?route=view_enquiry' . $extra_query_str . '&pageNo=1' ?>"><?= $page ?></a>
                </li>

              <?php } elseif ($page >= $totalPageNo - 4) { ?>

                <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                  <a class="page-link" href="<?= SITE_URL . '?route=view_enquiry' . $extra_query_str . '&pageNo=1' ?>"><?= $page ?></a>
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
                <a class="page-link" href="<?= SITE_URL . '?route=view_enquiry' . $extra_query_str . '&pageNo=' . $page ?>"><?= $page ?></a>
              </li>

          <?php }
          } ?>

          <li class="page-item">
            <a class="page-link" href="<?= $next_link ?>">Next</a>
          </li>
        </ul>
      </nav>

    </div>
  </div>
</div>

<!-- Modal window div-->
<div class="modal inmodal" id="viewUserMessageModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content animated flipInY">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="category_modal_title">View User Message</h4>
      </div>
      <div class="modal-body" id="user_message_div">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<!-- Modal ends here -->
<script>
  $(document).ready(function() {

    $('.record_status').select2({
      width: "88%",
      placeholder: "Select a status to proceed...",
      allowClear: true
    });

    $('.enquiry_type').select2({
      width: "88%",
      placeholder: "Select a enquiry type to proceed...",
      allowClear: true
    });

    $('.record_limit').select2({
      width: "88%",
      placeholder: "Select data limi per page...",
      allowClear: true
    });

    $('.pageNo').select2({
      width: "70%",
      placeholder: "Select a page no...",
      allowClear: true
    });

    $('.course').select2({
      width: "88%",
      placeholder: "Select a course to proceed...",
      allowClear: true
    });

    $('#enquiry_list_tbl').filterTable('#enquiry_tbl_filter');

    //Handling show user message 
    $(document).on('click', '#view_user_message', function() {
      var user_message = $(this).data('message');
      $('#user_message_div').html(user_message);
      $('#viewUserMessageModal').modal('show');
      return true;
    });

    //Configuring fetching all page records fetching params
    $(document).on('submit', '#fetch_all_enquiry_records', function(event) {
      event.preventDefault();

      const $fetchButton = $('#fetch_item_data');

      const pageRoute = $('#page_route').val();
      const recordStatus = $('#record_status').val();
      const pageNo = $('#pageNo').val();

      const recordLimit = $('#record_limit').val();
      const courseId = $('#course_id').val();
      const enquiryType = $('#enquiry_type').val();

      // -----------------------------------------
      // Required fields
      // -----------------------------------------

      if (!pageRoute) {
        return;
      }

      if (!recordStatus) {
        swal({
          title: 'Error!',
          text: 'Please select record status.',
          type: 'error'
        });
        return;
      }

      if (!pageNo || !/^[1-9]\d*$/.test(pageNo)) {
        swal({
          title: 'Invalid Page!',
          text: 'Please enter a valid page number.',
          type: 'warning'
        });
        return;
      }

      // -----------------------------------------
      // Build URL parameters
      // -----------------------------------------

      const params = new URLSearchParams();

      params.set('route', pageRoute);
      params.set('pageNo', pageNo);
      params.set('record_status', recordStatus);

      // Optional: record limit
      if (recordLimit && /^[1-9]\d*$/.test(recordLimit)) {
        params.set('limit', recordLimit);
      }

      // Optional: course
      if (courseId && Number(courseId) > 0) {
        params.set('course_id', courseId);
      }

      // Optional: enquiry type
      if (enquiryType && enquiryType.trim() !== '') {
        params.set('enquiry_type', enquiryType);
      }

      // -----------------------------------------
      // Fetch data
      // -----------------------------------------

      $fetchButton
        .html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Fetching')
        .prop('disabled', true);

      setTimeout(function() {

        $fetchButton
          .html('<i class="fa fa-search"></i>&nbsp;Fetch Data')
          .prop('disabled', false);

        swal({
          title: 'Great!',
          text: 'Data has been successfully fetched!',
          type: 'success',
          allowEscapeKey: false,
          allowOutsideClick: false
        }, function() {

          window.location.href = SITE_URL + '?' + params.toString();

        });

      }, 500);
    });
  });
</script>