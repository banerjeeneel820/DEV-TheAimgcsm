 <!-- Main Container  -->
    <div class="main-container container">
        <div class="row">
            <div id="content" class="col-sm-12" >
                <div class="page-login">
                    <center>
                      <?php if($_SESSION["successParam"]['tnx_status'] == 'success'){ ?>   
                        <div class="well" style="background:#0c4371; padding-top:40px;">
                            <h2 style="color:#fff; font-size:20px;"><i class="fa fa-check" aria-hidden="true" style="font-size:36px;"></i> Payment Successful</h2>
                            <div style="margin-bottom:20px;"><i class="fa fa-smile-o" aria-hidden="true" style="color:#fff; font-size:50px;"></i></div>
                            <h3 style="color:#4ec1ed; font-size:26px;">Thank you for your contribution.</h3><br>
                            <h3 style="color:#fff; font-size:15px;"><span>Order Id : <?=$_SESSION['successParam']['transaction_return_id']?></span>&nbsp;&nbsp;|&nbsp; <span>Payment Amount : Rs. <?=$_SESSION['successParam']['payment_amount']?></span></h3>
                            <br><a href="<?=SITE_URL?>"><input type="button" name="" value="Explore More" style="width:120px; height:30px;
                            color:#000;font-size:16px; font-weight:600;"></a> 
                        </div>
                      <?php }else{ ?>
                         <div class="well" style="background:#0c4371; padding-top:40px;">
                            <h2 style="color:#fff; font-size:20px;"><i class="fa fa-check" aria-hidden="true" style="font-size:36px;"></i> Order Unsuccessfull!</h2>
                            <div style="margin-bottom:20px;"><i class="far fa-frown" aria-hidden="true" style="color:#fff; font-size:50px;"></i></div> 
                            <h3 style="color:#4ec1ed; font-size:26px;">Please try to process the transaction again.</h3><br>
                            <!--<h3 style="color:#fff; font-size:15px;"><span>Order Id : 0143d</span>&nbsp;&nbsp;|&nbsp; <span>Grand Total : $34</span></h3>-->
                            <br><a href="<?=SITE_URL?>"><input type="button" name="" value="Explore More" style="width:120px; height:30px;
                            color:#000;font-size:16px; font-weight:600;"></a> 
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