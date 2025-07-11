<?php
	$hash = $urlSegmentArr['params'][1];
	$hash_decode_array = json_decode(base64_decode($hash),true);
	$dataArr['user_id'] = $hash_decode_array['user_id'];
    $dataArr['user_type'] = $hash_decode_array['user_type'];
    $dataArr['action_type'] = $hash_decode_array['action_type'];
	$dataArr['rand_auth_factor'] = $hash_decode_array['rand_auth_factor'];
	$verify_user_status = verifyUserStatus($dataArr);
?>

    <!-- Main Container  -->
    <div class="main-container container">
        <div class="row">
            <div id="content" class="col-sm-12">
                <div class="page-login">
                    <center>
                      <?php if($verify_user_status['check'] == 'success' && $dataArr['action_type'] == "user_sign_up_verification"){ ?>   
                        <div class="well" style="background:#0c4371; padding-top:40px;">
                            <p>
                              <h2 style="color:#fff; font-size:20px;"><i class="fa fa-check" aria-hidden="true" style="font-size:36px;"></i> Verification Successfull!</h2>  
                            </p>  
                            <h3 style="color:#4ec1ed; font-size:26px;">Thank you for sign up with us.</h3><br>
                            <a href="<?=SITE_URL?>"><input type="button" name="" value="Explore More" style="width:120px; height:30px;color:#000;font-size:16px; font-weight:600; margin-bottom: 30px;"></a> 
                        </div>
                        <?php }elseif($verify_user_status['check'] == 'success' && $dataArr['action_type'] == "unsubscribe_newsletter"){ ?> 
                        <div class="well" style="background:#0c4371; padding-top:40px;">
                            <p>
                              <h2 style="color:#fff; font-size:20px;"><i class="far fa-frown" aria-hidden="true" style="font-size:36px;"></i> You are unsubscribed Successfully!</h2>  
                            </p>  
                            <h3 style="color:#4ec1ed; font-size:26px;">We will be sad to see you go.</h3><br>
                            <a href="<?=SITE_URL?>"><input type="button" name="" value="Explore More" style="width:120px; height:30px;color:#000;font-size:16px; font-weight:600; margin-bottom: 30px;"></a> 
                        </div>
                       <?php }else{ ?>  
                         <div class="well" style="background:#0c4371; padding-top:40px;">
                            <p>
                              <h2 style="color:#fff; font-size:20px;"><i class="far fa-frown" aria-hidden="true" style="font-size:36px;"></i> Verification Unsuccessfull!</h2>  
                            </p>                            
                            <p style="color:#fff; font-size:15px;">This link is expired. Please get another verification link by providing your email which was given during the time of signup.</p>
                            
                            <div class="emailed-form">  
                              <form id="resend_user_verification_link_form" method="post" onsubmit="return false;">
                                 <div class="form-group pb-5">
                                  <input type="hidden" name="action" value="resend_user_verification_link">
                                  <input type="hidden" name="user_type" value="<?=$dataArr['user_type']?>">
                                  <input type="hidden" name="user_id" value="<?=$dataArr['user_id']?>">
                                  <input type="email" name="user_email" placeholder="Enter Email" required>
                                  <button type="submit" id="resend_user_verification_link_submit" class="theme-btn">submit</button>
                                </div>
                              </form>
                            </div>  
                        </div>
                      <?php } ?>  
                       <div class="bottom-form" class="row">
                            <div class="col-md-12">
                                <br>
                            </div>
                        </div>
                        <br>
                        <br>
                    </center>
                </div>
            </div>
        </div>
    </div>
    <!-- //Main Container -->

    <script type="text/javascript">
      $(document).ready(function () {
           //NEWSLETTER FORM HANDLER  
           $(document).on('submit', '#resend_user_verification_link_form', function(event){
                event.preventDefault();
                 
                 $.ajax({
                  url:ajaxCallUrl,
                  method:'POST',
                  data: new FormData(this),
                  contentType:false,
                  processData:false,
                  beforeSend: function() {
                     $('#resend_user_verification_link_submit').html('<i class="fa fa-spinner fa-spin"></i> Please wait').attr('disabled',true); 
                  },
                  success:function(responseData){
                      //console.log(responseData);return false;
                      var data = JSON.parse(responseData);
                      $('#resend_user_verification_link_submit').html('submit').attr('disabled',false); 
                      if(data.check == 'success'){
                       $('#resend_user_verification_link_form')[0].reset(); 
                       swal("Success!",data.msg, "success");
                       return true; 
                     }else{
                        swal("Error!", "Something went wrong!", "error");
                        return false;
                     }
                  }
                 });
             
             });
        });   
   </script>  