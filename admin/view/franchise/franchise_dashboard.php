<?php
//Configiring site backup files data
$siteBakFilesArr = $pageContent['pageData']['site_bak_files'];

//Check site backup job is created or not
$siteBackupQueue = $pageContent['pageData']['site_backup_queue'];

//Configiring course data
$courseListArr = $pageContent['pageData']['course_data'];
$latestCourseArr = array_slice($courseListArr, 0, 5);

//Configiring student data
$studentListArr = $pageContent['pageData']['student_data']['data'];
$studentCount = $pageContent['pageData']['student_data']['row_count'];

//Configiring receipt data
$receiptListArr = $pageContent['pageData']['receipt_data']['data'];
$receiptCount = $pageContent['pageData']['receipt_data']['row_count'];

//Configiring gallery data
$galleryListArr = $pageContent['pageData']['gallery_data'];

//Configiring news data
$newsListArr = $pageContent['pageData']['news_data'];
$latestNewsArr = array_slice($newsListArr, 0, 5);

if ($_GET['dataType'] == "student") {
  if (isset($_GET['fetchType'])) {
    $stuFetchType =  $_GET['fetchType'];
  } else {
    $stuFetchType = "weekly";
  }
} else {
  $stuFetchType = "weekly";
}

if ($stuFetchType == "today") {
  $stu_data_page_limit = "20";
} else {
  $stu_data_page_limit = "50";
}

if ($_GET['dataType'] == "receipt") {
  if (isset($_GET['fetchType'])) {
    $rcptFetchType =  $_GET['fetchType'];
  } else {
    $rcptFetchType = "weekly";
  }
} else {
  $rcptFetchType = "weekly";
}

if ($rcptFetchType == "today") {
  $rcpt_data_page_limit = "20";
} else {
  $rcpt_data_page_limit = "50";
}

if($siteBackupQueue){
  $backupAlertDivClass = "info";
  $backupAlertDivMsg = "A job to create site backup is already in the queue; No further action is required.";
}else{
  $backupAlertDivClass = "warning";
  $backupAlertDivMsg = "No job is queued to create site backup; Please create a latest backup now.";
}

//Fetching page action permission
$viewStuPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("view_student");
$viewReceiptPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("view_receipt");
$viewCoursePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("view_course");

$viewNewsPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("view_news");
$viewGalleryPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("view_gallery");

$stuUpdatePermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_student");
$updateReceiptPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("update_receipt");

if ($viewCoursePermission) {
  $view_course_url = SITE_URL . "?route=view_course";
} else {
  $view_course_url = FRONT_SITE_URL . "course";
}

if ($viewGalleryPermission) {
  $view_gallery_url = SITE_URL . "?route=gallery";
} else {
  $view_gallery_url = FRONT_SITE_URL . "gallery";
}

if ($viewNewsPermission) {
  $view_news_url = SITE_URL . "?route=view_news";
} else {
  $view_news_url = FRONT_SITE_URL . "news";
}

if ($_SESSION['user_type'] == 'franchise') {
  if ($_SESSION['owned_status'] == "yes" && $updateReceiptPermission == true) {
    $showReceiptPermission = true;
  } else {
    $showReceiptPermission = false;
  }
} elseif ($_SESSION['user_type'] == 'admin' || $_SESSION['user_type'] == 'developer') {
  if ($updateReceiptPermission) {
    $showReceiptPermission = true;
  } else {
    $showReceiptPermission = false;
  }
}

$siteBakupPermission = $this->globalLibraryHandlerObj->checkUserRolePermission("manage_site_backup");
$backupLimit = $_SESSION['user_type'] == "developer" ? true : ($_COOKIE["backupCount"] < 2 ? true : false);

/*print"<pre>";
 print_r($countProfileField);
 print"</pre>";*/

?>
<div class="wrapper wrapper-content">
  <div class="row mb-3">
    <div class="col-lg-12 col-md-12 col-sm-12">
      <div class="ticker-wrapper-h">
        <div class="heading">Latest News</div>

        <ul class="news-ticker-h">
          <?php
          if (count($latestNewsArr) > 0) {
            foreach ($latestNewsArr as $index => $news) {
          ?>
              <li><a href="<?= $view_news_url ?>"><img src="<?= RESOURCE_URL ?>images/new_blink.gif" style="height:30px;width:30px;display:inline-block;">&nbsp;<?= $news->title ?></a></li>
            <?php }
          } else { ?>
            <li>...No news is available at this moment...</li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </div>

  <?php if ($_SESSION['owned_status'] == "yes") { ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="alert alert-primary" role="alert">
          <strong>*Special student admission module is here for faster student registration. This module will create student and admission receipt in one click*</strong> <a href="<?= SITE_URL ?>?route=student_admission"><button type="button" class="btn btn-primary btn-sm ml-3"><i class="fa fa-universal-access"></i> Let's Go</button></a>
        </div>
      </div>
    </div>
  <?php } ?>

  <div class="row">
    <div class="col-lg-3">
      <div class="ibox ">
        <div class="ibox-title">
          <div class="ibox-tools">
            <a href="<?= SITE_URL ?>?route=view_students" target="_blank">
              <span class="label label-success float-right">View Students</span>
            </a>
          </div>
          <h5><i class="fa fa-mortar-board"></i> Students</h5>
        </div>
        <div class="ibox-content">
          <h1 class="no-margins"><?= $studentCount ?></h1>
          <div class="stat-percent font-bold text-navy"><?= count($studentListArr) ?> <i class="fa fa-level-up"></i></div>
          <small>New visits</small>
        </div>
      </div>
    </div>

    <?php if ($viewReceiptPermission) { ?>
      <div class="col-lg-3">
        <div class="ibox ">
          <div class="ibox-title">
            <div class="ibox-tools">
              <a href="<?= SITE_URL ?>?route=view_receipts&dataType=receipt" target="_blank">
                <span class="label label-success float-right">View Receipts</span>
              </a>
            </div>
            <h5><i class="fa fa-inr"></i> Receipts</h5>
          </div>
          <div class="ibox-content">
            <h1 class="no-margins"><?= (count($receiptListArr) > 0 ? count($receiptListArr) : 0) ?></h1>
            <div class="stat-percent font-bold text-danger"><?= count($receiptListArr) ?> <i class="fa fa-money"></i></div>
            <small>In first month</small>
          </div>
        </div>
      </div>
    <?php } else { ?>
      <div class="col-lg-3">
        <div class="ibox ">
          <div class="ibox-title">
            <div class="ibox-tools">
              <a href="<?= $view_gallery_url ?>" target="_blank">
                <span class="label label-success float-right">View Gallery</span>
              </a>
            </div>
            <h5><i class="fa fa-picture-o"></i> Gallery</h5>
          </div>
          <div class="ibox-content">
            <h1 class="no-margins"><?= count($galleryListArr) ?></h1>
            <div class="stat-percent font-bold text-danger"><?= count($galleryListArr) ?> <i class="fa fa-picture-o"></i></div>
            <small>Total Pictures</small>
          </div>
        </div>
      </div>
    <?php } ?>

    <div class="col-lg-3">
      <div class="ibox ">
        <div class="ibox-title">
          <div class="ibox-tools">
            <a href="<?= $view_course_url ?>" target="_blank">
              <span class="label label-success float-right">View Course</span>
            </a>
          </div>
          <h5><i class="fa fa-laptop"></i> Course</h5>
        </div>
        <div class="ibox-content">
          <h1 class="no-margins"><?= count($courseListArr) ?></h1>
          <div class="stat-percent font-bold text-info"><?= count($courseListArr) ?>&nbsp;<i class="fa fa-laptop"></i></div>
          <small>Total Course</small>
        </div>
      </div>
    </div>

    <div class="col-lg-3">
      <div class="ibox ">
        <div class="ibox-title">
          <div class="ibox-tools">
            <a href="<?= $view_news_url ?>" target="_blank">
              <span class="label label-success float-right">View News</span>
            </a>
          </div>
          <h5><i class="fa fa-newspaper-o"></i> News</h5>
        </div>
        <div class="ibox-content">
          <h1 class="no-margins"><?= count($newsListArr) ?></h1>
          <div class="stat-percent font-bold text-info"><?= count($newsListArr) ?>&nbsp;<i class="fa fa-newspaper-o"></i></div>
          <small>Total News</small>
        </div>
      </div>
    </div>
  </div>

  <?php if ($siteBakupPermission) { ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="ibox ">
          <div class="ibox-title">
            <h5>Site Backup Files</h5>
            <div class="ibox-tools">
              <?php if ($backupLimit && !$siteBackupQueue) { ?>
                <button type="button" id="createServerBackup" class="btn btn-xs btn-primary">
                  <i class="fa fa-recycle"> </i> Create Latest Backup
                </button>
              <?php } ?>
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
              <div class="col-lg-12">

                <div class="table-responsive project-list">
                  <table class="table table-stripped table-bordered toggle-arrow-tiny text-center">
                    <thead class="cursor-pointer">
                      <tr>
                        <th>Sl No.</th>
                        <th>File Name</th>
                        <th>Created At</th>
                        <th>File Type</th>
                        <th>File Size</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      if (!empty($siteBakFilesArr)) {
                        foreach ($siteBakFilesArr as $index => $file) {

                          $file_url = SITE_BACKUP_URL . $file->name;
                      ?>
                          <tr>
                            <td><?= $index + 1 ?></td>

                            <td class="project-title">
                              <span class="cursor-pointer" data-toggle="tooltip" data-placement="top" title="File Name: <?= $file->name ?>"><?= $file->name ?></span>
                            </td>

                            <td><?= date('jS F, Y', strtotime($file->creation_date)) ?></td>

                            <td class="project-title">
                              <span class="cursor-pointer" data-toggle="tooltip" data-placement="top" title="File Type: <?= $file->file_type ?>"><?= $file->file_type ?></span>
                            </td>

                            <td class="project-title">
                              <span class="cursor-pointer" data-toggle="tooltip" data-placement="top" title="File Size: <?= $file->size ?> MB"><?= $file->size ?> MB</span>
                            </td>

                            <td>
                              <a href="<?= $file_url ?>" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="Download this File">
                                <i class="fa fa-download"></i>&nbsp;Download
                              </a>
                            </td>
                          </tr>
                        <?php }
                      } else { ?>
                        <tr>
                          <td colspan="7">No backup file is avialable at this time...</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                      <tfoot>
                        <tr>
                          <div class="alert alert-<?=$backupAlertDivClass?> text-center" role="alert">
                            <?php if($_SESSION['user_type'] != "developer"){ ?>
                              <b>Total Backup Attempt remains: <?=(2 - $_COOKIE["backupCount"])?>&nbsp;||&nbsp;</b>
                            <?php } ?>
                              <b>*<?=$backupAlertDivMsg?>*</b>  
                          </div>
                        </tr>
                      </tfoot>
                  </table>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

  <?php if ($viewStuPermission) { ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="ibox ">
          <div class="ibox-title">
            <h5>Some Recent Student Data </h5>
            <div class="ibox-tools">
              <button type="button" class="btn btn-xs btn-primary export_student_receipt_data" data-tbl="student" data-export="excel"><i class="fa fa-file-excel-o"> </i> Export Data in CSV Format</button>

              <a href="javascript:void(0)" id="student_export_record_href" style="display:none" download>
                <button type="button" id="student_hidden_export_button">Export</button>
              </a>

              <div class="btn-group">
                <button type="button" class="btn btn-xs btn-white filter_table_data <?= ($stuFetchType == 'today' ? 'active' : '') ?>" data-dtype="student" data-ftype="today">Today</button>

                <button type="button" class="btn btn-xs btn-white filter_table_data <?= ($stuFetchType == 'weekly' ? 'active' : '') ?>" data-dtype="student" data-ftype="weekly">Weekly</button>

                <button type="button" class="btn btn-xs btn-white filter_table_data <?= ($stuFetchType == 'monthly' ? 'active' : '') ?>" data-dtype="student" data-ftype="monthly">Monthly</button>

                <button type="button" class="btn btn-xs btn-white filter_table_data <?= ($stuFetchType == 'annual' ? 'active' : '') ?>" data-dtype="student" data-ftype="annual">Annual</button>
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
            <div class="table-responsive">
              <input type="text" class="form-control form-control-sm m-b-xs" id="student_filter" placeholder="Search in student table...">

              <table class="table table-stripped table-bordered toggle-arrow-tiny text-center footable" data-page-size="50" data-filter=#student_filter>
                <thead>
                  <tr>
                    <th style="width:8%;">Image</th>
                    <th>Name</th>
                    <th>Student ID</th>
                    <th>Course</th>
                    <th>Franchise</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  if (count($studentListArr)) {
                    foreach ($studentListArr as $index => $content) {

                      $student_image_path = USER_UPLOAD_DIR . 'student/' . $content->image_file_name;

                      if (!strlen($content->image_file_name) > 0 || !file_exists($student_image_path)) {
                        $student_image_url = RESOURCE_URL . 'images/default-user-avatar.jpg';
                      } else {
                        $student_image_url = USER_UPLOAD_URL . 'student/' . $content->image_file_name;
                      }
                  ?>
                      <tr>

                        <td class="client-avatar">
                          <a href="<?= $student_image_url ?>" data-fancybox="gallery" data-caption="<?= $content->stu_name ?>">
                            <img alt="image" src="<?= $student_image_url ?>">
                          </a>
                        </td>

                        <td class="project-title" style="width:18%;">
                          <a href="javascript:void(0);" class="viewStudentDetail" data-sid="<?= $content->id ?>" data-toggle="tooltip" data-placement="bottom" title="Student Name: <?= $content->stu_name ?>"><?= $content->stu_name ?></a>
                          <br />
                          <small>Created <?= date('jS F, Y', strtotime($content->created_at)) ?></small>
                        </td>

                        <td class="project-title">
                          <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student ID: <?= $content->stu_id ?>"><?= $content->stu_id ?></span>
                          <br />
                          <small><strong>Contact: <?= $content->stu_phone ?></strong></small>
                        </td>

                        <!--<td class="project-title">
                                        <span data-toggle="tooltip" data-placement="bottom" title="Student Email: <?= $content->stu_email ?>"><?= (strlen($content->stu_email) > 20 ? substr($content->stu_email, 0, 20) . "..." : $content->stu_email) ?></span>
                                    </td>-->

                        <td class="project-title" style="width:22%;">
                          <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Course Name: <?= $content->course_title ?>"><?= $content->course_title ?></span><br>
                        </td>

                        <td class="project-title" style="width:18%;">
                          <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student Name: <?= $content->center_name ?>"><?= (strlen($content->center_name) > 0 ? $content->center_name : '<h5 style="color:red;">No Student available!</h5>') ?></span>
                        </td>

                        <td class="project-status">
                          <span class="dropdown">
                            <button class="btn btn-primary product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                            <ul class="dropdown-menu">
                              <?php if ($stuUpdatePermission) { ?>
                                <li>
                                  <a href="<?= SITE_URL ?>?route=edit_student&id=<?= $content->id ?>" target="_blank" data-toggle="tooltip" data-placement="bottom" title="Edit this student"><i class="fa fa-edit"></i> Edit Student</a>
                                </li>

                                <li>
                                  <a href="<?= SITE_URL ?>?route=clone_student&id=<?= $content->id ?>" data-toggle="tooltip" data-placement="bottom" title="Clone this student"><i class="fa fa-clone"></i> Clone Student</a>
                                </li>
                              <?php } ?>
                              <li>
                                <a href="javascript:void(0);" class="viewStudentDetail" data-sid="<?= $content->id ?>" data-toggle="tooltip" data-placement="bottom" title="View student info"><i class="fa fa-eye"></i> View Student</a>
                              </li>
                            </ul>
                          </span>
                        </td>
                      </tr>
                    <?php }
                  } else { ?>
                    <tr>
                      <td colspan="7">No Data is avialable at this time...</td>
                    </tr>
                  <?php } ?>
                </tbody>
                <tfoot>
                  <tr>
                    <td colspan="9">
                      <ul class="pagination float-right"></ul>
                    </td>
                  </tr>
                </tfoot>
              </table>
            </div>

          </div>
        </div>
      </div>
    </div>
  <?php } ?>

  <?php if ($viewReceiptPermission) { ?>
    <div class="row">
      <div class="col-lg-12">
        <div class="ibox ">
          <div class="ibox-title">
            <h5>Student Recent Receipt Data</h5>
            <div class="ibox-tools">
              <button type="button" class="btn btn-xs btn-primary export_student_receipt_data" data-tbl="receipt" data-export="excel"><i class="fa fa-file-excel-o"> </i> Export Data in CSV Format</button>

              <a href="javascript:void(0)" id="receipt_export_record_href" style="display:none" download>
                <button type="button" id="receipt_hidden_export_button">Export</button>
              </a>

              <div class="btn-group">
                <button type="button" class="btn btn-xs btn-white filter_table_data <?= ($rcptFetchType == 'today' ? 'active' : '') ?>" data-dtype="receipt" data-ftype="today">Today</button>

                <button type="button" class="btn btn-xs btn-white filter_table_data <?= ($rcptFetchType == 'weekly' ? 'active' : '') ?>" data-dtype="receipt" data-ftype="weekly">Weekly</button>

                <button type="button" class="btn btn-xs btn-white filter_table_data <?= ($rcptFetchType == 'monthly' ? 'active' : '') ?>" data-dtype="receipt" data-ftype="monthly">Monthly</button>

                <button type="button" class="btn btn-xs btn-white filter_table_data <?= ($rcptFetchType == 'annual' ? 'active' : '') ?>" data-dtype="receipt" data-ftype="annual">Annual</button>
              </div>
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
              <div class="col-lg-12">

                <div class="table-responsive project-list">
                  <input type="text" class="form-control form-control-sm m-b-xs" id="receipt_filter" placeholder="Search in receipt table...">

                  <table class="table table-stripped table-bordered toggle-arrow-tiny text-center footable" data-page-size="50" data-filter=#receipt_filter>
                    <thead class="cursor-pointer">
                      <tr>
                        <!--<th>ID.</th>-->
                        <th class="notexport">Image</th>
                        <th>Name</th>
                        <th>Student ID</th>

                        <th>Receipt ID</th>

                        <th>Amount (Rs.)</th>

                        <th>Course / Franchise</th>

                        <th class="notexport">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                      $total_collection = 0;
                      if (count($receiptListArr)) {
                        foreach ($receiptListArr as $index => $content) {

                          $student_image_path = USER_UPLOAD_DIR . 'student/' . $content->image_file_name;

                          if (!strlen($content->image_file_name) > 0 || !file_exists($student_image_path)) {
                            $student_image_url = RESOURCE_URL . 'images/default-user-avatar.jpg';
                          } else {
                            $student_image_url = USER_UPLOAD_URL . 'student/' . $content->image_file_name;
                          }

                          $total_receipt_amount = round((int)$content->receipt_amount + (int)$content->late_fine + (int)$content->extra_fees);
                          $total_collection = round((int)$total_collection + (int)$content->receipt_amount);
                      ?>
                          <tr>
                            <!--<td><?= $index + 1 ?></td>-->

                            <td class="client-avatar">
                              <a href="<?= $student_image_url ?>" data-fancybox="gallery" data-caption="<?= $content->stu_name ?>">
                                <img alt="image" src="<?= $student_image_url ?>">
                              </a>
                            </td>

                            <td class="project-title">
                              <a href="javascript:void(0);" class="viewStudentDetail" data-sid="<?= $content->id ?>" data-toggle="tooltip" data-placement="bottom" title="Student Name: <?= $content->stu_name ?>"><?= (strlen($content->stu_name) > 20 ? substr($content->stu_name, 0, 20) . "..." : $content->stu_name) ?></a>
                              <br />
                              <small>Created <?= date('jS F, Y', strtotime($content->created_at)) ?></small>
                            </td>

                            <td class="project-title" style="width:10%;">
                              <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Student ID: <?= $content->stu_id ?>"><?= $content->stu_id ?></span>
                            </td>

                            <td class="project-title" style="width:10%;">
                              <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Receipt ID: <?= $content->receipt_id ?>"><?= $content->receipt_id ?></span><br />
                              <small>Receipt Type: <?= ucfirst($content->category) ?></small>
                            </td>

                            <td class="project-title">
                              <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Receipt Amount: <?= $total_receipt_amount ?>"><i class="fa fa-inr" aria-hidden="true"></i>&nbsp;<?= $total_receipt_amount ?></span>
                            </td>

                            <td class="project-title" style="width:22%;">
                              <span class="cursor-pointer" data-toggle="tooltip" data-placement="bottom" title="Course Name: <?= $content->course_title ?>"><?= $content->course_title ?></span><br>
                              <small><strong>Franchise: <?= $content->center_name ?></strong></small>
                            </td>

                            <td class="project-status">
                              <span class="dropdown">
                                <button class="btn btn-primary product-btn dropdown-toggle btn-xs" type="button" data-toggle="dropdown">Action</button>
                                <ul class="dropdown-menu">

                                  <li>
                                    <a href="javascript:void(0);" class="viewStudentDetail" data-sid="<?= $content->id ?>" data-toggle="tooltip" data-placement="bottom" title="View student info"><i class="fa fa-eye"></i> View Student</a>
                                  </li>

                                  <?php if ($updateReceiptPermission) { ?>
                                    <li>
                                      <a href="<?= SITE_URL . '?route=view_receipts&actionType=edit&rcpt_id=' . $content->id ?>" data-toggle="tooltip" data-placement="bottom" title="Edit this Receipt for this student"><i class="fa fa-pencil"></i> Edit Receipt</a>
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
                          <td colspan="7">No Data is avialable at this time...</td>
                        </tr>
                      <?php } ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <td colspan="9">
                          <ul class="pagination float-right"></ul>
                        </td>
                      </tr>
                      <?php if (count($receiptListArr) > 0) { ?>
                        <div class="alert alert-success text-center" role="alert">
                          Total Collection of fees deposited by the students on <?= count($receiptListArr) ?>&nbsp;occasions : <i class="fa fa-inr"></i> <?= $total_collection ?>
                        </div>
                      <?php } ?>
                    </tfoot>
                  </table>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>
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
                  <td id="stu_name">125</td>
                </tr>
                <tr>
                  <th width="30%">Father's Name</th>
                  <td width="2%">:</td>
                  <td id="stu_father_name">125</td>
                </tr>
                <tr>
                  <th width="30%">Student ID</th>
                  <td width="2%">:</td>
                  <td id="stu_id">125</td>
                </tr>
                <tr>
                  <th width="30%">Contact No</th>
                  <td width="2%">:</td>
                  <td id="stu_phone">125</td>
                </tr>
                <tr>
                  <th width="30%">Email</th>
                  <td width="2%">:</td>
                  <td id="stu_email">125</td>
                </tr>
                <tr>
                  <th width="30%">Date of Birth</th>
                  <td width="2%">:</td>
                  <td id="stu_dob">Male</td>
                </tr>
                <tr>
                  <th width="30%">Student's Status</th>
                  <td width="2%">:</td>
                  <td id="student_status">125</td>
                </tr>
                <tr>
                  <th width="30%">Franchise</th>
                  <td width="2%">:</td>
                  <td id="center_name">2020</td>
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
                  <td id="course_title">125</td>
                </tr>
                <tr>
                  <th width="30%">Address</th>
                  <td width="2%">:</td>
                  <td id="stu_address">2020</td>
                </tr>
                <tr>
                  <th width="30%">Qualification</th>
                  <td width="2%">:</td>
                  <td id="stu_qualification">125</td>
                </tr>
                <tr>
                  <th width="30%">Gender</th>
                  <td width="2%">:</td>
                  <td id="stu_gender">Male</td>
                </tr>

                <tr>
                  <th width="30%">Marital Status</th>
                  <td width="2%">:</td>
                  <td id="stu_marital_status">Male</td>
                </tr>
              </table>
            </div>
          </div>
        </div>

        <?php if ($showReceiptPermission) { ?>
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
                    <td id="stu_advanced_fees">2020</td>
                  </tr>
                  <tr>
                    <th width="30%">Fees Paid Before DR</th>
                    <td width="2%">:</td>
                    <td id="stu_fees_paid_before_dr">Not Available</td>
                  </tr>
                  <tr>
                    <th width="30%">Fees Paid So Far</th>
                    <td width="2%">:</td>
                    <td id="fees_paid">2020</td>
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
    //Removing dynamically generated file from server
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

    $('.footable').footable();

    /*------------- Handle Studen filter Data -----------*/
    $(document).on('click', '.filter_table_data', function() {
      var dataType = $(this).data('dtype');
      var fetchType = $(this).data('ftype');
      window.location = "<?= SITE_URL ?>?dataType=" + dataType + "&fetchType=" + fetchType;
    });

    //Create latest backup on server
    $(document).on('click', '#createServerBackup', function() {
      var formData = {
        action: "createSiteBackupQueueJob",
      };

      swal({
        title: "Are you sure?",
        text: `Current backup files will be removed and new backup files will be created and this will take some time to run on background`,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, Go ahead...",
        closeOnConfirm: true
      }, function() {
        $.ajax({
          url: ajaxControllerHandler,
          method: 'POST',
          data: formData,
          beforeSend: function() {
            //$('.tooltip').hide();
            $("#createServerBackup").prop("disabled", true);
            $('.content_div_loader').addClass('sk-loading');
          },
          success: function(responseData) {
            var result = JSON.parse(responseData);
            //console.log(result);

            $('.content_div_loader').removeClass('sk-loading');

            if (result.check == "success") {
              toastr.options.onHidden = function() { location.reload(); }
              toastr.success(result.message, "Success!", {
                timeOut: 2000
              });
            } else {
              toastr.error(result.message, "Error!", {
                timeOut: 2000
              });
            }
            return true;
          }
        });
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

    //Handling hard export for student receipt table
    $(document).on('click', '.export_student_receipt_data', function(event) {
      event.preventDefault();

      var export_table = $(this).data('tbl');

      if (export_table == "student") {
        var fetchType = "<?= $stuFetchType ?>";
        var export_record_href = "student_export_record_href";
        var hidden_export_button = "student_hidden_export_button";
        var export_file_name = "Student_Export_Data.csv";
      } else {
        var fetchType = "<?= $rcptFetchType ?>";
        var export_record_href = "receipt_export_record_href";
        var hidden_export_button = "receipt_hidden_export_button";
        var export_record_href = "receipt_export_record_href";
        var export_file_name = "Student_Receipt_Export_Data.csv";
      }

      var export_method = $(this).data('export');

      var formData = {
        export_table: export_table,
        export_method: export_method,
        fetchType: fetchType,
        protocol: "dashboard"
      };

      //Clearing hyperlink href for fresh download
      $('#' + hidden_export_button).attr("href", "javascript:void(0);");
      $('#' + export_record_href).attr("download", "");

      //show sweetalert success
      swal({
        title: "Are you sure?",
        text: "All table data will be exported as PDF & this may take a while!",
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
            $('#' + export_record_href).attr("href", result.file_url);
            $("#" + hidden_export_button).click();
            //Removing file from server
            setTimeout(function() {
              removeFileFromServer(result.file_upload_dir);
            }, 5000);
            return true;
          }
        });

      });
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
          //$('.tooltip').hide();
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
          /*setTimeout(function() {
            removeFileFromServer(result.file_upload_dir);
          }, 5000);*/
          return true;
        }
      });
    });
  });
</script>