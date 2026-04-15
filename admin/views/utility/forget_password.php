<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>THE AIMGCSM || Admin</title>

    <link href="<?=INSPINIA_RESOURCE_URL?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?=INSPINIA_RESOURCE_URL?>font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="<?=INSPINIA_RESOURCE_URL?>css/animate.css" rel="stylesheet">
    <link href="<?=INSPINIA_RESOURCE_URL?>css/style.css" rel="stylesheet">
    <link href="<?=INSPINIA_RESOURCE_URL?>css/custom.css" rel="stylesheet">
     <!-- Toastr style -->
    <link href="<?=INSPINIA_RESOURCE_URL?>css/plugins/toastr/toastr.min.css" rel="stylesheet">
</head>
<?php
  $hash = $_GET['hash'];
  $hash_decode_array = json_decode(base64_decode($hash),true);
  $user_type = $hash_decode_array['user_type'];
  $user_id = $hash_decode_array['user_id'];
  $rand_auth_factor = $hash_decode_array['rand_auth_factor'];
  //Creating object for global library
  $GlobalLibraryHandlerObj = new GlobalLibraryHandler();
  $verify_user_status = $GlobalLibraryHandlerObj->verify_User_Status($user_id,$user_type,$rand_auth_factor);
?>
<body class="gray-bg">

    <div class="passwordBox animated fadeInDown">
        <div class="row">

            <div class="col-md-12">
                <div class="ibox-content">
                 <?php if($verify_user_status){ ?>
                    <h2 class="font-bold">Forgot password</h2>
                    <p>Resrt your password by fill up the form properly below.</p>

                    <div class="row">
                        <div class="col-lg-12">
                            <form class="mt-0" role="form" id="reset_password_form" onsubmit="return false">
                                <input type="hidden" name="action" value="reset_user_password">
                                <input type="hidden" name="user_type" value="<?=$user_type?>">
                                <input type="hidden" name="user_id" value="<?=$user_id?>">
                                <div class="form-group">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter new password" required autofocus="on">
                                </div>

                                <div class="form-group">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Reenter new password" required="" autofocus="">
                                </div>

                                <button type="submit" class="btn btn-primary block full-width m-b" id="reset_password_submit">Reset password</button>
                                <a href="<?=SITE_URL?>">
                                    <small>Sign in</small>
                                </a> 
                            </form>
                        </div>
                    </div>
                 <?php }else{ ?>
                    <h2 class="font-bold">Forgot password</h2>
                    <p>Enter Your email to get a fresh reset password link.</p>

                    <div class="row">
                       <div class="col-lg-12">
                          <form class="mt-0" role="form" id="user_forget_password_form" onsubmit="return false;">
                            <input type="hidden" name="action" value="user_forget_password">
                            <div class="form-group">
                                <input type="email" class="form-control" name="user_email" placeholder="Email address" required="">
                            </div>

                            <div class="form-group">
                               <select class="form-control-sm form-control input-s-sm inline user_type" name="user_type" id="user_type" required>
                                <option selected disabled value>Select a User Type to Proceed</option>
                                <option value="developer">Developer</option> 
                                <option value="admin">Admin</option>  
                                <option value="franchise">Franchise</option>  
                                <option value="student">Student</option>  
                              </select> 
                            </div>

                            <button type="submit" class="btn btn-primary block full-width m-b" id="forget_password_submit">Send reset password link</button>
                             <a href="<?=SITE_URL?>" id="show_signin_form">
                                <small>Sign in</small>
                            </a>
                        </form>

                      </div>
                    </div>  
                 <?php } ?>    
                </div>
            </div>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-6">
                Copyright</strong> Neel Banerjee(Freelancer)
            </div>
            <div class="col-md-6 text-right">
               <small>© 2019-2020</small>
            </div>
        </div>
    </div>

</body>
<footer>
    <script src="<?=INSPINIA_RESOURCE_URL?>js/jquery-3.1.1.min.js"></script>
     <!-- Toastr -->
    <script src="<?=INSPINIA_RESOURCE_URL?>js/plugins/toastr/toastr.min.js"></script>
    <script>
        var ajaxControllerHandler = "<?=SITE_URL?>controller/callAjaxController.php";
        $(document).ready(function () {
           $(document).on('submit', '#reset_password_form', function(event){
               event.preventDefault();
               var formData = new FormData(this);
               var password = $('#password').val();
               var confirm_password = $('#confirm_password').val();
                //define toastr options
                toastr.options = {
                  closeButton: true,
                  progressBar: true,
                  showMethod: 'slideDown',
                  timeOut: 3000
                };
               
               if(password != confirm_password){
                  toastr.error("Password and Confirm Password does not match!", "Error!");
                  return false;
               }
          
               $.ajax({
                url:ajaxControllerHandler,
                method:'POST',
                data: formData,
                contentType:false,
                processData:false,
                beforeSend: function() {
                   //$('#reset_password_submit').html('<i class="fa fa-spinner fa-spin"></i> Please wait').attr('disabled',true); 
                },
                success:function(responseData){
                    //console.log(responseData);return false;
                    var data = JSON.parse(responseData);
                    $('#reset_password_submit').html('Reset Password').attr('disabled',false); 
                    if(data.check == 'success'){
                     $('#reset_password_form')[0].reset(); 
                     toastr.success(data.msg, 'Success!');
                     return true;
                   }else{
                      toastr.error(data.msg, "Error!");
                      return false;
                   }
                }
               });
            });

           $(document).on('submit', '#user_forget_password_form', function(event){
               event.preventDefault();
               var formData = new FormData(this);
                   
               $.ajax({
                url:ajaxControllerHandler,
                method:'POST',
                data: formData,
                contentType:false,
                processData:false,
                beforeSend: function() {
                   $('#forget_password_submit').html('<i class="fa fa-spinner fa-spin"></i> Please wait').attr('disabled',true); 
                },
                success:function(responseData){
                    //console.log(responseData);return false;
                    var data = JSON.parse(responseData);
                    //define toastr options
                    toastr.options = {
                      closeButton: true,
                      progressBar: true,
                      showMethod: 'slideDown',
                      timeOut: 3000
                    };
                    $('#forget_password_submit').html('Send reset password link').attr('disabled',false); 
                    if(data.check == 'success'){
                     $('#user_forget_password_form')[0].reset(); 
                     toastr.success(data.msg, 'Success!');
                     return true; 
                   }else{
                      toastr.error(data.msg, 'Error!');
                      return false;
                   }
                 }
               });
           });
        });
    </script>
</footer>

</html>
