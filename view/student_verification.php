<?php

//Fetching news data
$latestNewsArr = array_slice($newsArr, 0, 5);

/*print"<pre>";
  print_r($courseArr);
  print"</pre>";*/
?>

<style>
  .input-group-addon {
    padding: 0.5rem 0.75rem;
    margin-bottom: 0;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.25;
    color: #495057;
    text-align: center;
    background-color: #e9ecef;
    border: 1px solid rgba(0, 0, 0, .15);
    border-radius: 0.25rem;
  }
</style>

<!--Page Title-->
<section class="page-title" style="background-image:url(<?= RESOURCE_URL ?>images/background/12.jpg);">
  <div class="auto-container">
    <div class="row clearfix">
      <!--Title -->
      <div class="title-column col-lg-6 col-md-12 col-sm-12">
        <h1>Student Verification Page</h1>
      </div>
      <!--Bread Crumb -->
      <div class="breadcrumb-column col-lg-6 col-md-12 col-sm-12">
        <ul class="bread-crumb clearfix">
          <li><a href="index-2.html"><span class="icon fas fa-home"></span> Home</a></li>
          <li class="active"><span class="icon fas fa-arrow-alt-circle-right"></span> Latest News</li>
        </ul>
      </div>
    </div>
  </div>
</section>
<!--End Page Title-->


<!--Sidebar Page Container-->
<div class="sidebar-page-container" id="page_content">
  <div class="auto-container">
    <div class="alert alert-success text-center" role="alert">
      Our all course details have been given below.&nbsp;If you have any question please don’t hesitate to contact us @ <?= $siteSettingArr->phone ?>
    </div>
    <div class="row clearfix">

      <!--Content Side-->
      <div class="student-verification content-side col-lg-8 col-md-12 col-sm-12">
        <div class="overlay" style="display: none;">
          <div class="spinner"></div>
          <br />
          <h4>Processing...</h4>
        </div>

        <div class="title-box">
          <div class="text">To check student details please enter your student id below to see your details. If you are not an active student then you may not be able to see your detail. In that case please contact your franchise to restore your information.You can also read <br> our <a href="#">Terms of Service</a> and <a href="#">Support Policy</a> here.</div>
          <hr>
        </div>
        <div class="row pt-0" id="student_search_div">
          <div class="col-lg-12">
            <form id="fetch_student_detail_form" method="post" onsubmit="return false;">
              <div class="form-group">
                <input type="hidden" name="action" id="action" value="fetchStudentDetail">
                <label for="studentID"><b>Student ID</b></label>
                <input type="text" class="form-control" name="studentID" id="studentID" aria-describedby="emailHelp" placeholder="Enter your student id..." required autocomplete="off">
                <small id="emailHelp" class="form-text text-muted">We'll never share your details with anyone service.</small>
              </div>

              <div class="form-group">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0">
                  <div class="g-recaptcha" id="stu_verification_recaptcha_div"></div>
                </div>
              </div>

              <div class="form-group">
                <span class="d-none pt-2" id="student_creds_err_msg" style="color:red;"></span>
              </div>
              <button type="submit" id="student_detail_submit" class="btn btn-primary">Submit</button>
            </form>
          </div>

          <div class="col-lg-12 mt-5">
            <!-- Sec Title -->
            <div class="sec-title">
              <h2><span class="theme_color">some</span> news</h2>
            </div>
            <ul class="accordion-box">
              <?php
              foreach ($latestNewsArr as $key => $news) {

                if (strlen($news->optional_pdf) > 0 && file_exists(USER_UPLOAD_DIR . 'news/' . $news->optional_pdf)) {
                  $optional_pdf = USER_UPLOAD_URL . 'news/' . $news->optional_pdf;
                } else {
                  $optional_pdf = null;
                }
              ?>
                <!--Block-->
                <li class="accordion block">
                  <div class="acc-btn">
                    <div class="icon-outer"><span class="icon icon-plus fa fa-plus"></span> <span class="icon icon-minus fa fa-minus"></span></div><?= $news->title ?>
                  </div>
                  <div class="acc-content">
                    <div class="content">
                      <div class="text">
                        <?= strip_tags(stripslashes($news->description)) ?>
                        <?php if ($optional_pdf !== null) { ?>
                          <br><span style="color:blue;">Download the pdf for more details : <a href="<?= $optional_pdf ?>" download><i class="fa fa-download"></i>&nbsp;Download</a></span>
                        <?php } ?>
                      </div>
                    </div>
                  </div>
                </li>
              <?php } ?>
            </ul>
          </div>
        </div>

        <div class="d-none" id="student_detail_div">
          <div class="row">
            <div class="col-lg-4">
              <div class="card shadow-sm">
                <div class="card-header bg-transparent text-center">
                  <a href="https://source.unsplash.com/600x300/?student" id="student_dp_fancybox" data-fancybox="gallery" target="_blank">
                    <img class="profile_img" id="student_dp" src="https://source.unsplash.com/600x300/?student" alt="student dp" style="width:195px;height:140px;">
                  </a>
                  <h6><span id="focus_stu_name">Deep Chaterjee</span></h6>
                </div>
                <div class="card-body">
                  <p class="mb-0"><strong class="pr-1">ID:</strong><span id="stu_id">321000001</span></p>
                  <p class="mb-0"><strong class="pr-1">Contact No:</strong><span id="stu_phone">A</span></p>
                  <p class="mb-0"><strong class="pr-1">Status:</strong><span id="student_status">A</span></p>
                  <p class="mb-0"><strong class="pr-1">Result:</strong><span id="stu_result">A</span></p>
                </div>
              </div>
            </div>
            <div class="col-lg-8">
              <div class="card shadow-sm">
                <div class="card-header bg-transparent border-0">
                  <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-12">
                      <h5 class="mb-0"><i class="far fa-clone pr-1"></i>General Information</h5>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12">
                      <button type="button" id="close_student_detail_div" class="btn btn-danger btn-sm float-right">
                        <i class="fa fa-times"></i> Close</button>
                    </div>
                  </div>
                </div>
                <div class="table-responsive card-body pt-0">
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
                      <th width="30%">Franchise</th>
                      <td width="2%">:</td>
                      <td id="center_name">2020</td>
                    </tr>
                  </table>
                </div>
              </div>
              <div style="height: 26px"></div>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="card shadow-sm">
                <div class="card-header bg-transparent border-0">
                  <h5 class="mb-0"><i class="far fa-clone pr-1"></i>Others Information</h5>
                </div>
                <div class="card-body pt-0">
                  <table class="table table-bordered">
                    <tr>
                      <th width="30%">Couse Name</th>
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
                      <td id="stu_qualification">2020</td>
                    </tr>

                    <tr>
                      <th width="30%">Gender</th>
                      <td width="2%">:</td>
                      <td id="stu_gender">2020</td>
                    </tr>

                    <tr>
                      <th width="30%">Marital Status</th>
                      <td width="2%">:</td>
                      <td id="stu_marital_status">2020</td>
                    </tr>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!--Sidebar Side-->
      <div class="sidebar-side col-lg-4 col-md-4 col-sm-12">
        <aside class="sidebar sticky_sidebar">

          <!--Category Blog-->
          <div class="sidebar-widget categories-blog">
            <div class="sidebar-title">
              <h2>Course Enquiry</h2>
            </div>
            <form method="post" id="user_enquiry_form" onsubmit="return false;">
              <input type="hidden" name="action" id="action" value="createGlobalEnquiry">
              <input type="hidden" name="enquiry_type" id="enquiry_type" value="course">
              <input type="hidden" name="course_name" id="course_name" value="">
              <!-- Form Group -->
              <div class="form-group">
                <input type="text" class="form-control" name="user_name" value="" placeholder="Your name" required>
              </div>

              <div class="form-group">
                <input type="email" class="form-control" name="user_email" id="user_email" value="" placeholder="Your email" required>
              </div>

              <div class="form-group">
                <input type="number" class="form-control" name="user_phone" value="" placeholder="Your Contact No" required>
              </div>

              <div class="form-group">
                <input type="text" class="form-control" name="user_city" id="user_city" value="" placeholder="Your City" required>
              </div>

              <div class="form-group">
                <select class="form-control course" name="course_id" id="course_id" data-placeholder="Choose a Course..." required>
                  <option selected disabled>Choose a Course</option>
                  <?php foreach ($courseArr as $course) {
                  ?>
                    <option value="<?= $course->id ?>" id="course_<?= $course->id ?>"><?= $course->course_title ?></option>
                  <?php } ?>
                </select>
              </div>

              <div class="form-group mt-4">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0">
                  <div class="g-recaptcha" id="form_recaptcha_div"></div>
                </div>
              </div>

              <div class="form-group">
                <textarea name="user_message" class="form-control" style="height:80px;" placeholder="Add a quick note..."></textarea>
              </div>

              <div class="form-group">
                <button type="submit" id="contact_submit" class="btn btn-success" style="width:100%">Submit</button>
              </div>
            </form>
          </div>

          <!-- Help Widget -->
          <div class="sidebar-widget help-widget">
            <div class="sidebar-title">
              <h2>Need Help?</h2>
            </div>
            <div class="widget-content">
              <div class="text">if you have any question please don’t hesitate to contact us</div>
              <ul class="list">
                <li><span class="icon fas fa-phone-volume"></span>Call us @ <?= $siteSettingArr->phone ?></li>
                <li><span class="icon fas fa-envelope"></span>Reach us @ <?= strtolower($siteSettingArr->contact_email) ?></li>
              </ul>
            </div>
          </div>

        </aside>
      </div>

    </div>
  </div>
</div>

<script>
  //Show/hide password handler in form
  $(document).on('click', '#show_hide_password a', function(event) {
    event.preventDefault();
    if ($('#show_hide_password input').attr("type") == "text") {
      $('#show_hide_password input').attr('type', 'password');
      $('#show_hide_password i').addClass("fa-eye-slash");
      $('#show_hide_password i').removeClass("fa-eye");
    } 
    else if ($('#show_hide_password input').attr("type") == "password") {
      $('#show_hide_password input').attr('type', 'text');
      $('#show_hide_password i').removeClass("fa-eye-slash");
      $('#show_hide_password i').addClass("fa-eye");
    }
  });

  //Show/hide password input in form
  $(document).on('click', '#password_forgot', function(event) {
    var checked = $(this).is(":checked");

    if (checked) {
      $("#student_password_div").addClass('d-none');
    } else {
      $("#student_password_div").removeClass('d-none');
    }
  });

  $(document).on('click', '#close_student_detail_div', function(event) {
    //Hide and show required div 
    $('.overlay').fadeIn();
    // Reset reCaptcha
    grecaptcha.reset();

    setTimeout(function() {
      $('.overlay').fadeOut();
      $("#student_search_div").removeClass("d-none");
      $("#student_detail_div").addClass("d-none");
    }, 1000);
  });

  //handling student detail fetch form
  $(document).on('submit', '#fetch_student_detail_form', function(event) {
    event.preventDefault();

    var response = grecaptcha.getResponse(stuVerCaptchaWidget);

    // if (response.length === 0) {
    //   // reCAPTCHA not checked
    //   swal("Error!", "Please verify that you are not a robot.", "error");
    // } else {
      //Removing error message
      $('#student_creds_err_msg').removeClass('d-none');
      //Calling ajax request
      $.ajax({
        url: ajaxCallUrl,
        method: 'POST',
        data: new FormData(this),
        contentType: false,
        processData: false,
        beforeSend: function() {
          $('.overlay').fadeIn();
          $('#student_detail_submit').html('Processing <i class="fa fa-spinner fa-spin"></i>').attr('disabled', true);
        },
        success: function(responseData) {
          var data = JSON.parse(responseData);
          //console.log(data);

          //reseting captcha
          grecaptcha.reset(stuVerCaptchaWidget);

          //disabling loader
          $('.overlay').fadeOut();
          $('#student_detail_submit').html('Submit').attr('disabled', false);
          
          //console.log(responseData);
          if (data.check == 'success') {
            //reseting form data
            $('#fetch_student_detail_form')[0].reset();
            $("#student_password_div").removeClass('d-none');

            //Populating student data in student detail div
            var studentDetail = data.studentDetail;
            //console.log(studentDetail); 

            //populating student detail div
            $('#student_dp').attr('src', studentDetail.student_dp);
            $('#student_dp_fancybox').attr('href', studentDetail.student_dp);
            //populating other fields
            $('#stu_id').text(studentDetail.stu_id);
            $('#stu_phone').text(studentDetail.stu_phone);
            $('#stu_result').text(studentDetail.stu_result);
            $('#focus_stu_name').text(studentDetail.stu_name);
            $('#stu_name').text(studentDetail.stu_name);
            $('#stu_father_name').text(studentDetail.stu_father_name);
            $('#stu_address').text(studentDetail.stu_address);
            $('#stu_dob').text(studentDetail.stu_dob);
            $('#course_title').text(studentDetail.course_title);
            $('#center_name').text(studentDetail.center_name);
            $('#stu_email').text(studentDetail.stu_email);
            $('#stu_qualification').text(studentDetail.stu_qualification);
            $('#student_status').text(studentDetail.student_status);
            $('#stu_gender').text(studentDetail.stu_gender);
            $('#stu_marital_status').text(studentDetail.stu_marital_status);
            //Hide and show required div 
            $("#student_search_div").addClass("d-none");
            $('#student_creds_err_msg').addClass('d-none');
            $("#student_detail_div").removeClass("d-none");
            return true;
          } else {
            //show sweetalert success
            if (data.message.length > 0) {
              var message = data.message;
            } else {
              var message = "Something went wrong";
            }
            $('#student_creds_err_msg').html(message).removeClass('d-none');
            return false;
          }
        }
      });
    //}
  });
</script>