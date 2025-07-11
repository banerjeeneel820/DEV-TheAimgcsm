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

//Course data
$courseArr = $pageContent['pageData']['course_data'];
//Franchise data
$franchiseArr = $pageContent['pageData']['franchise_data'];
$studentPagedData = $pageContent['pageData']['student_data'];

$studentListArr = $studentPagedData['data'];
$pageNo = $studentPagedData['pageNo'];
$rowCount = $studentPagedData['row_count'];
$limit = $studentPagedData['limit'];

$offset = ($pageNo - 1) * $limit;
$totalPageNo = ceil($rowCount / $limit);

/*echo $pageNo."<br>";
  echo $rowCount."<br>";
  echo $limit."<br>";
  echo $totalPageNo."<br>";*/

//Fetching page action permission
$createPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_student");
$updatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_student");
$createReceiptPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("create_receipt");

$queries = array();
parse_str($_SERVER['QUERY_STRING'], $queries);

$extra_query_str = '';

foreach ($queries as $key => $query_val) {
    if ($key != "route" && $key != "pageNo") {
        $extra_query_str .= "&" . $key . "=" . $query_val;
    }
}

if(!empty($_GET['fetchType'])){
    $page_data_type = $_GET['fetchType'];
}else{
    $page_data_type = "dueList";
}

/*print'<pre>';
  print_r($studentListArr);
  print'</pre>';exit;*/
?>

<div class="wrapper wrapper-content fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Fetch Student based on their status </h5>
                    <div class="ibox-tools">

                        <div class="btn-group" bis_skin_checked="1">
                            <button type="button" class="btn btn-xs btn-white filter_table_data <?= $page_data_type == 'dueList' ? 'active': ''?>" data-ftype="dueList">View Due Students List</button>
                            <button type="button" class="btn btn-xs btn-white filter_table_data <?=$page_data_type == 'markupList' ? 'active': ''?>" data-ftype="markupList">Show Updated Markup List</button>
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
                    <form id="fetch_all_student_records" onsubmit="return false;">
                        <input type="hidden" id="pageNo" value="<?= $pageNo ?>">
                        <input type="hidden" name="page_route" id="page_route" value="<?= $_GET['route'] ?>">

                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-12 m-b-xs input-group pr-0">
                                <select class="record_status" name="record_status" id="record_status" required>
                                    <option selected disabled value>Select a Data type to proceed</option>
                                    <option value="active" <?= (($record_status == 'active' || $record_status == '') ? 'selected' : '') ?>>Active</option>
                                    <option value="blocked" <?= ($record_status == 'blocked' ? 'selected' : '') ?>>Blocked</option>
                                </select>

                                <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Select Receipt Status"><i class="fa fa-question-circle"></i></span>
                            </div>
                            <div class="col-lg-3 ml-0">
                                <select class="course" name="course_id" id="course_id" data-placeholder="Choose a Course..." tabindex="2">
                                    <option></option>
                                    <?php foreach ($courseArr as $course) {
                                    ?>
                                        <option value="<?= $course->id ?>" <?= ($_GET['course_id'] == $course->id ? 'selected' : '') ?>><?= $course->course_title ?></option>
                                    <?php } ?>
                                </select>

                                <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Select a Course"><i class="fa fa-question-circle"></i></span>
                            </div>

                            <div class="col-lg-3 col-md-4 col-sm-12 pl-0">
                                <select class="franchise" name="franchise_id" id="franchise_id" data-placeholder="Choose a Franchise..." tabindex="2" <?= ($_SESSION['user_type'] == 'franchise' ? 'disabled' : '') ?>>
                                    <option></option>
                                    <?php foreach ($franchiseArr as $franchise) {
                                    ?>
                                        <option value="<?= $franchise->id ?>" <?= ($_SESSION['user_type'] == 'franchise' ? ($_SESSION['user_id'] == $franchise->id ? 'selected' : '') : ($_GET['franchise_id'] == $franchise->id ? 'selected' : '')) ?>><?= $franchise->center_name ?></option>
                                    <?php } ?>
                                </select>

                                <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom" title="Select a Franchise"><i class="fa fa-question-circle"></i></span>
                            </div>

                            <div class="col-lg-3 col-md-3 col-sm-12 m-b-xs pl-0">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="student_id" id="student_id" placeholder="Search by Student's ID..." value="<?= (isset($_GET['stu_id']) ? $_GET['stu_id'] : '') ?>">
                                    <span class="cursor-pointer pl-2 pt-1" data-toggle="tooltip" data-placement="bottom" title="Enter Student ID"><i class="fa fa-question-circle"></i></span>
                                </div>
                            </div>

                            <div class="col-lg-1 col-md-1 col-sm-12 m-b-xs pl-0">
                                <button class="btn btn-primary ml-3" type="submit" id="fetch_item_data" data-toggle="tooltip" data-placement="bottom" title="Fetch Student's Receipt Data"><i class="fa fa-search"></i></button>
                            </div>

                        </div>

                    </form>
                </div>
            </div>
        </div>

    </div>

    <div class="row" id="import_data_div">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Import Student Monthly Course Fees in CSV Format</h5>
                    <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="bottom"
                        title="Update Student's Monthly Course Fees by uploading a csv or xls file with proper data format">
                        <i class="fa fa-question-circle"></i></span>
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
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <form method="post" id="import_table_data_form" class="wp-upload-form" onsubmit="return false;">
                            <input type="hidden" name="import_table" value="students_monthly_fees">

                            <div class="btn-group">
                                <label title="Upload a file" for="importDataCSV" class="btn btn-primary">
                                    <input type="file" accept="application/vnd.openxmlformats-officedoc.sheet" id="importDataCSV" name="import_data_file" class="hide" />
                                    -- Update Student's Monthly Course Fees by uploading a csv or xls file --
                                </label>
                            </div>

                            <button type="submit" class="btn btn-lg btn-success ml-2 mb-2" name="import_data_submit" id="import_data_submit" class="button" value="Import Data" disabled><i class="fa fa-upload" aria-hidden="true"></i>&nbsp;Import Data</button>

                            <a href="<?= RESOURCE_URL . 'importSampleCSV/sample-student-monthly-fees.xlsx' ?>" class="btn btn-primary btn-lg ml-2 mb-2" download>
                                <i class="fa fa-download"> </i> Sample CSV
                                <span class="cursor-pointer pl-1" data-toggle="tooltip" data-placement="top" title="Download sample CSV format and strickly follow it to import bulk data"><i class="fa fa-question-circle"></i></span>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Student List with all details</h5>
                    <div class="ibox-tools">

                        <?php if ($createPermission) { ?>
                            <a href="<?= SITE_URL ?>?route=add_student" class="table-action-primary" data-toggle="tooltip" data-placement="bottom" title="Add New Student"><i class="fa fa-plus-circle"></i></a>
                        <?php } ?>

                        <a href="<?= SITE_URL ?>?route=view_due_students" class="table-action-info" data-toggle="tooltip" data-placement="bottom" title="Refresh Student Data"><i class="fa fa-refresh"></i></a>

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

                        <input type="text" class="form-control form-control-sm m-b-xs" id="student_tbl_filter" placeholder="Search in student table by student name, id, phone no or franchise...">

                        <div class="mt-2">
                            <?php if (count($studentListArr) > 0) { ?>
                                <strong>Showing <?= $offset + 1 ?> to <?= (count($studentListArr) == $limit ? $limit * $pageNo : count($studentListArr)) ?> of <?= $rowCount ?> entries</strong>
                            <?php } else { ?>
                                <strong>No Data Found!</strong>
                            <?php } ?>
                        </div>

                        <table class="table table-striped table-bordered table-hover text-center mt-3" id="student_list_tbl">
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
                                    <!--<th>SL No.</th>-->
                                    <th class="notexport">Image</th>
                                    <th class="sorting_desc_disabled">Name<span class="footable-sort-indicator"><i class="fa fa-sort"></i></span></th>

                                    <th class="sorting_desc_disabled">Student Info</th>
                                    <th class="sorting_desc_disabled">Franchise/Course</th>

                                    <?php if ($resultUpdatePermission == false ? ($showResultData == false ? true : false) : true) { ?>
                                        <th class="sorting_desc_disabled">Result</th>
                                    <?php } ?>

                                    <?php //if($updatePermission == false?($showStatusColumn == true ? true:false):true){ 
                                    ?>
                                    <th class="sorting_desc_disabled notexport">Status</th>
                                    <?php //} 
                                    ?>

                                    <th class="sorting_desc_disabled notexport">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($studentListArr) > 0) {
                                    foreach ($studentListArr as $index => $content) {
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
                                ?>
                                        <tr id="stu_tr_<?= $content->stu_id ?>" style="background-color:<?= ($content->verified_status == '0' ? '#f1d0d0;' : '') ?>">
                                            <td style="width: 6%;">
                                                <div class="pretty p-image p-plain selectAllItem ml-2">
                                                    <input type="checkbox" class="singleCheck" id="<?= $content->id ?>" value="<?= $content->id ?>" />
                                                    <div class="state">
                                                        <img class="image" src="<?= RESOURCE_URL ?>images/checkbox.png">
                                                        <label class="cursor-pointer selectAllItem" for="<?= $content->id ?>"></label>
                                                    </div>
                                                </div>
                                            </td>
                                            <!--<td><?= $index + 1 ?></td>-->
                                            <td class="client-avatar" style="width:6%;">
                                                <a href="<?= $student_image_url ?>" data-fancybox="gallery" data-caption="<?= $content->stu_name ?>">
                                                    <img alt="image" title="Neel Banerjee" src="<?= $student_image_url ?>">
                                                </a>
                                            </td>

                                            <td class="project-title" style="width: 17%;">
                                                <a href="javascript:void(0);" class="viewStudentDetail" data-sid="<?= $content->id ?>" data-toggle="tooltip" data-placement="bottom" title="Student Name: <?= $content->stu_name ?>"><?= (strlen($content->stu_name) > 12 ? substr($content->stu_name, 0, 12) . "..." : $content->stu_name) ?></a>
                                                <br />
                                                <small>Created <?= date('jS F, Y', strtotime($content->created_at)) ?></small>
                                            </td>

                                            <td class="project-title" style="width: 17%;">
                                                <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student ID: <?= $content->stu_id ?>"><strong><?= $content->stu_id ?></strong></span><br>
                                                <small><strong>Student Contact: <?= $content->stu_phone ?></strong></small>
                                            </td>

                                            <td class="project-title" style="width: 21%;">
                                                <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Franchise Name: <?= $content->center_name ?>"><?= (strlen($content->center_name) > 0 ? (strlen($content->center_name) > 14 ? substr($content->center_name, 0, 14) . "..." : $content->center_name) : '<h5 style="color:red;">No Franshise available!</h5>') ?></span><br>
                                                <small class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Course Name: <?= $content->course_title ?>"><strong>Course: <?= (strlen($content->course_title) > 25 ? substr($content->course_title, 0, 25) . "..." : $content->course_title) ?></strong></small>
                                            </td>

                                            <td>
                                                <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student Result: <?= ucfirst($content->stu_result) ?>"><strong><?= ucfirst($content->stu_result) ?></strong></span>
                                            </td>

                                            <td>
                                                <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student Status: <?= $student_status ?>"><strong><?= $student_status ?></strong></span>
                                                <?php if ($showResultData) { ?>
                                                    <br><small><strong>Student Result: <?= ucfirst($content->stu_result) ?></strong></small>
                                                <?php } ?>
                                            </td>

                                            <td class="project-status" style="width: 10%;">
                                                <span class="dropdown">
                                                    <button class="btn btn-primary product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                                    <ul class="dropdown-menu">
                                                        <?php if ($updatePermission) { ?>
                                                            <li>
                                                                <a href="<?= SITE_URL ?>?route=edit_student&id=<?= $content->id ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Edit this student"><i class="fa fa-pencil"></i> Edit Student</a>
                                                            </li>
                                                        <?php } ?>

                                                        <?php if ($createReceiptPermission) { ?>
                                                            <li>
                                                                <a href="<?= SITE_URL . '?route=view_receipts&actionType=create&stu_id=' . $content->stu_id ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Create receipt fot this student"><i class="fa fa-plus-circle"></i> Create Receipt</a>
                                                            </li>
                                                        <?php } ?>

                                                    </ul>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="8">No student data found...!</td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <nav aria-label="Student Page navigation">
                        <ul class="pagination">

                            <?php
                            if ($totalPageNo > 1) {
                                if ($pageNo == 1) {
                                    $pervious_link = "javascript:void(0);";
                                } else {
                                    $pervious_link = SITE_URL . '?route=view_due_students' . $extra_query_str . '&pageNo=' . ($pageNo - 1);
                                }
                                $next_link = SITE_URL . '?route=view_due_students' . $extra_query_str . '&pageNo=' . ($pageNo + 1);
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
                                        <a class="page-link" href="<?= SITE_URL . '?route=view_due_students' . $extra_query_str . '&pageNo=' . $page ?>"><?= $page ?></a>
                                    </li>

                                    <?php
                                } elseif ($pageNo >= 5 && $page != $totalPageNo) {
                                    if ($page == 1) {
                                    ?>

                                        <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                                            <a class="page-link" href="<?= SITE_URL . '?route=view_due_students' . $extra_query_str . '&pageNo=1' ?>"><?= $page ?></a>
                                        </li>

                                    <?php } elseif ($page == $pageNo - 2) { ?>

                                        <li class="page-item">
                                            <a class="page-link" href="javascript:void(0);">...</a>
                                        </li>

                                    <?php } elseif ($page == $pageNo - 1 || $page == $pageNo || $page == $pageNo + 1) { ?>

                                        <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                                            <a class="page-link" href="<?= SITE_URL . '?route=view_due_students' . $extra_query_str . '&pageNo=' . $page ?>"><?= $page ?></a>
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
                                            <a class="page-link" href="<?= SITE_URL . '?route=view_due_students' . $extra_query_str . '&pageNo=1' ?>"><?= $page ?></a>
                                        </li>

                                    <?php } elseif ($page >= $totalPageNo - 4) { ?>

                                        <li class="page-item <?= ($page == $pageNo ? 'active' : '') ?>">
                                            <a class="page-link" href="<?= SITE_URL . '?route=view_due_students' . $extra_query_str . '&pageNo=1' ?>"><?= $page ?></a>
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
                                        <a class="page-link" href="<?= SITE_URL . '?route=view_due_students' . $extra_query_str . '&pageNo=' . $page ?>"><?= $page ?></a>
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
    </div>
</div>


<!-- Modal window div-->
<div class="modal fade show" id="showStudentDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content animated fadeIn">
            <div class="modal-header">
                <h3 class="modal-title" id="result_modal_title">Student Details</h3>
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
                                    <td id="modal_student_status">Not Available</td>
                                </tr>
                                <tr>
                                    <th width="30%">Franchise</th>
                                    <td width="2%">:</td>
                                    <td id="center_name">Not Available</td>
                                </tr>
                                <tr>
                                    <th width="30%">Student's Result</th>
                                    <td width="2%">:</td>
                                    <td id="stu_result">Not Available</td>
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

                <?php if ($showStatusDropdown) { ?>
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
                <?php } ?>
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
            width: "84%",
            allowClear: true
        });

        $('.course').select2({
            width: "91%",
            allowClear: true
        });
        $('.franchise').select2({
            width: "90%",
            allowClear: true
        });

        var current_franchise_id = $("#franchise_id").val();
        var current_course_id = $("#course_id").val();

        $('#student_list_tbl').filterTable('#student_tbl_filter');

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

        //Search student params handling
        $(document).on('click', '.serach_studen_option', function() {
            var stu_search_option = $(this).data('stusrchopt');
            //console.log(stu_search_option);
            if (stu_search_option == 'based_on_stu_id') {
                $('#fetch_single_student_record').removeClass('d-none');
                $('#fetch_all_student_records').addClass('d-none');
            } else {
                $('#fetch_all_student_records').removeClass('d-none');
                $('#fetch_single_student_record').addClass('d-none');
            }
            return false;
        });

        $(document).on("change", ".singleCheck,#checkAll", function(e) {

            var ids = 0;
            $('.content_div_loader').addClass('sk-loading');

            setTimeout(function() {

                $('.singleCheck').each(function(index, element) {

                    if ($(this).prop("checked") == true) {
                        ids++;
                    }

                });

                //console.log(ids);

                if (ids > 0) {
                    $("#student_bulk_status_update_div").removeClass('d-none');
                } else {
                    $("#student_bulk_status_update_div").addClass('d-none');
                }

                $('.content_div_loader').removeClass('sk-loading');
            }, 500);

        });

        /*------------- Handle Studen filter Data -----------*/
        $(document).on('click', '.filter_table_data', function() {
            var fetchType = $(this).data('ftype');
            window.location = `<?= SITE_URL ?>?route=view_due_students&fetchType=${fetchType}`;
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
                        $('#student_dp').attr('src', studentDetail.student_dp);
                        $('#student_dp_fancybox').attr('href', studentDetail.student_dp);
                        //populating other fields
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
                        $('#modal_student_status').html('<b>' + studentDetail.student_status + '</b>');
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

        //Configuring fetching all page records fetching params
        $(document).on('submit', '#fetch_all_student_records', function(event) {
            event.preventDefault();

            var record_status = $('#record_status').val();
            var student_id = $('#student_id').val();
            var page_route = $('#page_route').val();

            var course_id = $('#course_id').val();
            var franchise_id = $('#franchise_id').val();

            var pageNo = $('#pageNo').val();

            if (record_status === null) {
                window.location = SITE_URL + "?route=" + page_route;
            } else {
                $('#fetch_item_data').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;').attr('disabled', true);
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
                        var redirect_url = SITE_URL + "?route=" + page_route + "&record_status=" + record_status;

                        if (student_id.length > 0) {
                            redirect_url += "&stu_id=" + student_id;
                        }

                        if (course_id > 0) {
                            redirect_url += "&course_id=" + course_id;
                        }

                        if (franchise_id > 0) {
                            redirect_url += "&franchise_id=" + franchise_id;
                        }

                        if (pageNo.length > 0) {
                            redirect_url += "&pageNo=" + pageNo;
                        }

                        window.location = redirect_url;

                    });
                }, 500);
                return true;
            }
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
                        url: importTableDataController,
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $('.content_div_loader').addClass('sk-loading');
                            $('#import_data_submit').prop('disabled', true);
                        },
                        success: function(responseData) {
                            setTimeout(function() {
                                $('.content_div_loader').removeClass('sk-loading');
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
</body>

</html>