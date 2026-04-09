<!-- Print load modal -->
<div id="print_loader_modal" style="
  display:none;
  position:fixed;
  top:0;
  left:0;
  width:100%;
  height:100%;
  background:rgba(0,0,0,0.5);
  z-index:9999;
  align-items:center;
  justify-content:center;
">
  <div style="
    background:#fff;
    padding:20px 30px;
    border-radius:8px;
    font-size:16px;
    font-weight:500;
  ">
    <i class="fa fa-spinner fa-spin"></i>
    &nbsp;Retrieving document, please wait...
  </div>
</div>
<!-- End here -->

<div class="footer">
  <div class="float-right">
    Software Version: <strong>2.5.2</strong>
  </div>
  <div>
    <strong>Copyright</strong> Neel Banerjee &copy; 2019-2023
  </div>
</div>
</div>
</div>
</body>

</html>

<!-- Mainly scripts -->

<!-- jquery UI -->
<?php if ($_GET['route'] == "manage_questions") { ?>
  <script src="<?= RESOURCE_URL ?>js/plugins/jquery-ui/jquery-ui.min.js"></script>
<?php } ?>
<!--End Here-->

<script src="<?= RESOURCE_URL ?>js/popper.min.js"></script>
<script src="<?= RESOURCE_URL ?>js/bootstrap.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?= RESOURCE_URL ?>js/inspinia.js"></script>
<script src="<?= RESOURCE_URL ?>js/plugins/pace/pace.min.js"></script>
<script src="<?= RESOURCE_URL ?>js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- MENU -->
<script src="<?= RESOURCE_URL ?>js/plugins/metisMenu/jquery.metisMenu.js"></script>

<!-- Jquery Validate -->
<script src="<?= RESOURCE_URL ?>js/plugins/validate/jquery.validate.min.js"></script>

<?php if ($isTinyAllowed) { ?>
  <!-- Tinymce CDN -->
  <script src="https://cdn.tiny.cloud/1/0d7r1f1uhg1q4wjwwv9y7hyu5qjiuloe3qqbqdpcduyby3tz/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<?php } ?>

<?php
if (!empty($jsPluginArr)) {
  foreach ($jsPluginArr as $key => $jsFile) { ?>
    <script type="text/javascript" src="<?= RESOURCE_URL ?>js/plugins/<?= $jsFile ?>.js"></script>
<?php }
} ?>

<?php if ($_GET['route'] == "start_exam") { ?>
  <!-- <script src="<?= RESOURCE_URL ?>js/plugins/smooth-scrollbar/smooth-scrollbar.js"></script> -->
  <script src="<?= RESOURCE_URL ?>js/plugins/count-timer/countDown.js"></script>
<?php } ?>

<!-- Theme Custom js -->
<script src="<?= RESOURCE_URL ?>js/custom/custom.js"></script>

<script>
  $(document).ready(function() {

    if (navigator.userAgent.indexOf('MSIE') !== -1 || !!document.documentMode) {
      alert("This application does not support Internet Explorer. Please use Chrome or Firefox.");
    }
    //Admin sticky sidebar
    /*$("#sidebar-wrapper").niceScroll({
       scrollspeed: 40
      //A smaller value increases the scroll speed. A larger value makes the scroll speed slower.
    });*/
  });
</script>