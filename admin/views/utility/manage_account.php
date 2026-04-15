<?php

  //print_r($pageContent);

?>
<div class="row wrapper border-bottom white-bg page-heading">
                <div class="col-lg-10">
                    <h2>Manage Account Form</h2>
                   
                </div>
                <div class="col-lg-2">

                </div>
            </div>
        <div class="wrapper wrapper-content animated fadeInRight">
            <div class="row">
                  <div class="col-lg-6">
                        <div class="ibox ">
                            <div class="ibox-title">
                                <h5>Change Paswword</h5>
                            </div>
                            <div class="ibox-content">
                                <form method="post" id="managePassword" onsubmit="return false;">
                                    <input type="hidden" name="action" id="action" value="manageAccount" />
                                    <input type="hidden" name="type" id="type" value="managePassword" />
                                    <input type="hidden" name="hidden_username" id="hidden_username" value="<?=$_SESSION['username']?>" />
                                    
                                    <div class="form-group row">
                                        <div class="col-md-8"><input placeholder="Old Password..." name="oldpswd" id="oldpswd" class="form-control" type="password" required> 
                                        </div>
                                    </div>
                                   
                                    <div class="form-group row">
                                        <div class="col-md-8"><input placeholder="New Password..." class="form-control" name="nwpswd" id="nwpswd" type="password" required> 
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <div class="col-md-8"><input placeholder="Confirm Password..." class="form-control" name="cnfpswd" type="password" id="cnfpswd" required></div>
                                    </div>
                                    
                                   <div class="form-group row">
                                        <div class="col-md-8">
                                            <button class="btn btn-sm btn-primary" type="submit" name="submit" id="password_submit"><strong>Change Password</strong></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="ibox ">
                        <div class="ibox-title">
                            <h5>Change Username</h5>
                            
                        </div>
                        <div class="ibox-content">
                            <form method="post" id="manageUsername" onsubmit="return false;">
                                 <input type="hidden" name="action" id="action" value="manageAccount" />
                                 <input type="hidden" name="type" id="type" value="manageUsername" />

                                <div class="form-group row">
                                    <div class="col-md-8"><input name="oldUsername" id="oldUsername" value="<?=$_SESSION['username']?>" class="form-control" type="text" readonly> 
                                    </div>
                                </div>
                               
                                <div class="form-group row">
                                    <div class="col-md-8"><input placeholder="New Username..." class="form-control" name="nwUsername" id="nwUsername" type="text" required> 
                                    </div>
                                </div>
                                
                               <div class="form-group row">
                                    <div class="col-md-8">
                                        <button class="btn btn-sm btn-primary" type="submit" name="submit" id="username_submit"><strong>Change Username</strong></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
     </div>
        
        <script>
            $(document).ready(function () {

                $(document).on('submit', '#manageUsername', function(event){
                      event.preventDefault();

                      var nwUsername = $('#nwUsername').val();
                      //alert(oldUsername);

                       $.ajax({
                        url:ajaxControllerHandler,
                        method:'POST',
                        data: new FormData(this),
                        contentType:false,
                        processData:false,
                        beforeSend: function() {
                           //$('#create_guide_loader').addClass('sk-loading');
                           //$('#create').attr('disabled',true);
                        },
                        success:function(responseData){
                           var data = JSON.parse(responseData);
                           //console.log(responseData);
                           if(data.responseArr.check == 'success'){
                              //reseting form data
                              $('#nwUsername').val('');
                              $('#oldUsername').val(nwUsername);
                              //show sweetalert success
                              swal({
                                  title: "Great!",
                                  text: "Username has been successfully updated!",
                                  type: "success"
                              });
                             return true; 
                           }else{
                             //Disabling loader
                              $('#create_guide_loader').removeClass('sk-loading');
                              //show sweetalert success
                              swal({
                                  title: "Oops!",
                                  text: "Something went wrong!",
                                  type: "error"
                              });
                              return false;
                           }
                        }
                       });
             
                  });

                 $(document).on('submit', '#managePassword', function(event){
                      event.preventDefault();

                      var nwpswd = $('#nwpswd').val();
                      var cnfpswd = $('#cnfpswd').val();
                      //alert(type);return false;

                      if(nwpswd != cnfpswd){

                        swal({
                            title: "Oops!",
                            text: "Password & Confirm password don't match!",
                            type: "error"
                        });

                        return false;

                      }else{

                           $.ajax({
                            url:ajaxControllerHandler,
                            method:'POST',
                            data: new FormData(this),
                            contentType:false,
                            processData:false,
                            beforeSend: function() {
                               //$('#create_guide_loader').addClass('sk-loading');
                               //$('#create').attr('disabled',true);
                            },
                            success:function(responseData){
                               var data = JSON.parse(responseData);
                               //console.log(responseData);
                               if(data.responseArr.check == 'success'){
                                  $('#oldpswd').val('');
                                  $('#nwpswd').val('');
                                  $('#cnfpswd').val(''); 
                                  swal({
                                      title: "Great!",
                                      text: "Password has been successfully changed!",
                                      type: "success"
                                  });
                                 return true; 
                               }else{
                                 var errorMsg = data.responseArr;
                                 //Disabling loader
                                  $('#create_guide_loader').removeClass('sk-loading');
                                  //show sweetalert success
                                  swal({
                                      title: "Oops!",
                                      text: errorMsg,
                                      type: "error"
                                  });
                                  return false;
                               }
                            }
                           });

                        }
                  });
                
            });
        </script>

