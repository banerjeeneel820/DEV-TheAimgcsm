<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" href="<?= RESOURCE_URL ?>images/fav.png" type="image/x-icon">
    <link rel="icon" href="<?= RESOURCE_URL ?>images/fav.png" type="image/x-icon">

    <title>The AimGcsm || Admin</title>

    <link href="<?= RESOURCE_URL ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= RESOURCE_URL ?>font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="<?= RESOURCE_URL ?>css/animate.css" rel="stylesheet">
    <link href="<?= RESOURCE_URL ?>css/style.css" rel="stylesheet">
    <link href="<?= RESOURCE_URL ?>css/custom.css" rel="stylesheet">

    <!-- Toastr style -->
    <link href="<?= RESOURCE_URL ?>css/plugins/toastr/toastr.min.css" rel="stylesheet">

    <style>
        /* .g-recaptcha {
            transform: scale(1.08);
            transform-origin: 0 0;
        } */
    </style>

</head>

<body class="gray-bg">

    <div class="loginColumns animated fadeInDown">
        <div class="row pb-2">
            <!--<h1 class="logo-name-custom">RS Travels Admin Panel</h1>-->
        </div>
        <div class="row">

            <div class="col-md-6">
                <h2 class="font-bold">Welcome to AimGcsm Admin</h2>

                <p>
                    Perfectly designed and precisely prepared admin theme with over 50 pages with extra new web app views.
                </p>

                <p>
                    Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s.
                </p>

                <p>
                    When an unknown printer took a galley of type and scrambled it to make a type specimen book.
                </p>
            </div>
            <div class="col-md-6">
                <div class="ibox-content">
                    <form class="m-t" role="form" name="user_signin_form" method="post" id="user_signin_form" onsubmit="return false;">

                        <input type="hidden" name="action" value="check_user_login">

                        <div class="form-group">
                            <input type="text" name="user_email" class="form-control" placeholder="Enter your id or email" value="<?php if (isset($_COOKIE['user_email'])) {
                                                                                                                                        echo $_COOKIE['user_email'];
                                                                                                                                    } ?>" required>
                        </div>

                        <div class="form-group">
                            <select class="form-control-sm form-control input-s-sm inline user_type" name="user_type" id="user_type" required>
                                <option selected disabled value>Select a User Type to Proceed</option>
                                <option value="developer" <?= ($_GET['login_type'] == "admin" ? 'selected' : '') ?>>Developer</option>
                                <option value="admin" <?= ($_GET['login_type'] == "admin" ? 'selected' : '') ?>>Admin</option>
                                <option value="franchise" <?= ($_GET['login_type'] == "franchise" ? 'selected' : '') ?>>Franchise</option>
                                <option value="exam" <?= ($_GET['login_type'] == "exam" ? 'selected' : '') ?>>Exam</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="input-group" id="show_hide_password">
                                <input class="form-control" type="password" name="user_pswd" id="user_pswd" placeholder="Enter your account password..." value="<?php if (isset($_COOKIE['user_pswd'])) {
                                                                                                                                                                    echo $_COOKIE['user_pswd'];
                                                                                                                                                                } ?>" required>
                                <div class="input-group-addon">
                                    <a href="javascript:void(0);"><i class="fa fa-eye-slash" aria-hidden="true"></i></a>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 pl-0">
                                <div class="g-recaptcha" id="form_recaptcha_div"></div>
                            </div>
                        </div>

                        <div class="checkbox pl-0">
                            <input id="remember_me" name="remember_me" type="checkbox">
                            <label for="remember_me">
                                Remember me
                            </label>
                        </div>
                        <button type="submit" name="submit" id="user_signin_submit" class="btn btn-primary block full-width m-b">Login</button>
                    </form>

                    <p class="m-t">
                        <small>Inspinia we app framework base on Bootstrap 4.5.1 &copy; 2019</small>
                    </p>
                </div>
            </div>
        </div>
        <hr />
        <div class="row">
            <div class="col-md-6">
                <strong>Copyright</strong> © Neel Banerjee <small>2019-23</small>
            </div>
            <div class="col-md-6 text-right">
                Software Version: <strong>2.5.2</strong>
            </div>
        </div>
    </div>

</body>

<footer>
    <script src="<?= RESOURCE_URL ?>js/jquery-3.1.1.min.js"></script>
    <!-- Toastr -->
    <script src="<?= RESOURCE_URL ?>js/plugins/toastr/toastr.min.js"></script>
    <!-- Google Recaptcha Script -->
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit"></script>

    <script>
        var adminUrl = '<?= SITE_URL ?>';
        var ajaxControllerHandler = "<?= SITE_URL ?>controller/callAjaxController.php";

        //Google recaptcha variables
        var loginCaptchaWidget;
        var countAddShowClassContact = 0;
        var onloadCallback = function() {
            // Renders the HTML element with id 'example1' as a reCAPTCHA widget.
            loginCaptchaWidget = grecaptcha.render('form_recaptcha_div', {
                'sitekey': '6LdJ398UAAAAALCcgKy69mXlTjI4sfz682uHR0_e',
                'theme': 'light'
            });
        }

        //Show/hide password handler in form
        $(document).on('click', '#show_hide_password a', function(event) {
            event.preventDefault();
            if ($('#show_hide_password input').attr("type") == "text") {
                $('#show_hide_password input').attr('type', 'password');
                $('#show_hide_password i').addClass("fa-eye-slash");
                $('#show_hide_password i').removeClass("fa-eye");
            } else if ($('#show_hide_password input').attr("type") == "password") {
                $('#show_hide_password input').attr('type', 'text');
                $('#show_hide_password i').removeClass("fa-eye-slash");
                $('#show_hide_password i').addClass("fa-eye");
            }
        });

        //User signin form handler
        $(document).on('submit', '#user_signin_form', function(event) {
            event.preventDefault();
            var formData = new FormData(this);

            var response = grecaptcha.getResponse(loginCaptchaWidget);

            if (response.length === 0) {
                // reCAPTCHA not checked
                toastr.error("Please verify that you are not a robot.", "Error!");
            } else {
                $.ajax({
                    url: ajaxControllerHandler,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#user_signin_submit').html('<i class="fa fa-spinner fa-spin"></i> Logging...').attr('disabled', true);
                    },
                    success: function(responseData) {
                        //console.log(responseData);return false;
                        var data = JSON.parse(responseData);
                        //reseting captcha
                        grecaptcha.reset(loginCaptchaWidget);

                        //define toastr error
                        toastr.options = {
                            closeButton: true,
                            progressBar: true,
                            showMethod: 'slideDown',
                            timeOut: 3000
                        };
                        $('#user_signin_submit').html('Log in').attr('disabled', false);
                        if (data.check == 'success') {
                            $('#user_signin_form')[0].reset();
                            toastr.options.onHidden = function() {
                                window.location = adminUrl;
                            }
                            toastr.success(data.msg, 'Success!');
                            return true;
                        } else {
                            toastr.error(data.msg, "Error!");
                            return false;
                        }
                    }
                });
            }

        });
    </script>
</footer>

</html>