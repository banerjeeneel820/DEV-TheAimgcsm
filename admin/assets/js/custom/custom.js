function initDtTbl(){
   $('.dataTables-example').DataTable({
     "pageLength": 30,
     "lengthChange": true,
     "paging":true,
     "responsive": true,
     "info":true,
     "ordering": true,
     language: {"searchPlaceholder": "Search here...",},
     dom: '<"html5buttons"B>lTfgitp',
     buttons: [
        //{extend:'copy'},
        {
           extend: 'csv',
           "fnClick": function ( nButton, oConfig, oFlash ) {
                alert( 'Mouse click' );
            },
           text: '<i class="fa fa-file-excel-o"></i> Export Csv',
           className: 'btn btn-primary',
           exportOptions: {
             columns: 'th:not(.notexport)'
           }
        },

        {
         extend: 'print',
         text: '<i class="fa fa-print"></i> Print',
         customize: function (win){
            $(win.document.body).addClass('white-bg');
            $(win.document.body).css('font-size', '10px');

            $(win.document.body).find('table')
                    .addClass('compact')
                    .css('font-size', 'inherit');
         },
         exportOptions: {
             columns: 'th:not(.notexport)'
         }
       },
     ],

     aoColumnDefs: [
      {
         bSortable: false,
         aTargets: [ 0 ]
      }
    ],

  });

   /**Table Design is Fixed*/
    $('#DataTables_Table_0').removeClass('dataTable no-footer');
    $('#DataTables_Table_0_wrapper').attr('style','padding-left:0px;padding-right:0');
    /*-------------Ends Here ---------------*/
}

function destroyDTbl(){
    $('.dataTables-example').DataTable().clear().destroy();
}

var btn = $('#button_to_top');

$(window).scroll(function() {
    var window_top = $(window).scrollTop() - 0;  
  
    //Calculating scroll top
    if ($(window).scrollTop() > 300) {
      btn.addClass('show');
    } else {
      btn.removeClass('show');
    }
});

$(document).ready(function(){

    //Initializing smooth scrollbar
    //Scrollbar.initAll({alwaysShowTracks:true});

    //Initializing datatable
    if ($(".dataTables-example")[0]){
        initDtTbl();

        /**Table Design is Fixed*/
        $('#DataTables_Table_0').removeClass('dataTable no-footer');
        $('#DataTables_Table_0_wrapper').attr('style','padding-left:0px;padding-right:0');
        /*-------------Ends Here ---------------*/
    }    

    setTimeout(function(){
       $('#preloader').fadeOut();
    },1000);

    $('.buttons-csv,.buttons-excel,.buttons-pdf').on('click',function(){
       // $('.content_div_loader').addClass('sk-loading');
    }) 

    $('.dataTables-example').on('draw.dt', function () {
        //turn_on_icheck();
        //alert('hi');
    });

     /**----------------- ICHECK STYLING ------------------*/
    if ($(".i-checks")[0]){
        $('.i-checks').iCheck({
             checkboxClass: 'icheckbox_square-green',
             radioClass: 'iradio_square-green',
        });

        $(".i-checks.checkAll input").on('ifChecked', function (e) {
             var checked = $(this).is(':checked');
             alert(checked); 
             if(checked != false){
                $(this).data('check','checked');
                $('.selectAllItem input').iCheck('check');
            }else{
                $('.selectAllItem input').iCheck('uncheck');
            }
        });
    }
        
    $(document).on('change','.checkAll input',function (e) {
           var checked = $(this).prop('checked');

           if(checked == true){
              $('.selectAllItem input').prop('checked',true);
          }else{
              $('.selectAllItem input').prop('checked',false);
          }
     });
     /*---------------- Ends here -----------------------*/

    $('#loading-example-btn').click(function () {
        btn = $(this);
        simpleLoad(btn, true)

        // Ajax example
        //$.ajax().always(function () {
        //  simpleLoad($(this), false)
       //});

        simpleLoad(btn, false)
    });

    /**----ICheck Validation----*/
    $(document).on('ifChanged', '.i-checks input', function (e) {
        $(this).valid();
    });
});
/*--------------- End Here------------------*/

var validator = $('.needs-validation').validate({
    errorPlacement: function(label, element) {
        label.addClass('arrow');
        label.insertAfter(element.parent());
    },
    wrapper: 'span',

    /*rules: {
     'record_status':{ required:true }
    }*/
});

//Setting tooltip class for dom
$('[data-toggle="tooltip"]').tooltip({
    trigger : 'hover'
})  

btn.on('click', function(e) {
  e.preventDefault();
  $('html, body').animate({scrollTop:0}, '300');
});  

/** Toastr option during page load */
setTimeout(function() {
    toastr.options = {
        closeButton: true,
        progressBar: true,
        showMethod: 'slideDown',
        timeOut: 3000
    };
    //toastr.info('You can edit any listing by click on edit button.', 'Quick Info!');

}, 1300);

/*----------------- End Here ---------------*/

 //Clean dynamic content storing folder
 $(document).on("click",".cleanRuntimeUpload",function(e){
    e.preventDefault();
    var formData = {action:"cleanDynamicContentFolder"};

    swal({
        title: "Are you sure?",
        text: "Are you sure to clean the folder right now?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, Go ahead!",
        closeOnConfirm: true
    }, function () {
        $.ajax({
          url:ajaxControllerHandler,
          method:'POST',
          data: formData,
          beforeSend: function() {
             //$('.tooltip').hide();
             //$('#clean_dynamic_content_store_folder').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Processing').attr('disabled',true);
             $('.overlay').fadeIn();
          },
          success:function(responseData){
              var result = JSON.parse(responseData);
              //console.log(result);
              if(result.check == "success"){
                 toastr.success(result.message, 'Success!');
              }else{
                 toastr.error(result.message, 'Error!');
              }
              
              $('.overlay').fadeOut();
              //$('#clean_dynamic_content_store_folder').html('<i class="fa fa-recycle"></i>&nbsp; Clean Dynamic PDF Store Folder').attr('disabled',false);
              return true; 
           }
        });
    });    
 });

//Clean cache files from server
$(document).on("click",".clearSiteCache",function(e){
    e.preventDefault();
    
    var warningTxt = "Are you sure to clear the cache folder right now?"; 
    var formData = {action:"clearCacheFolder"};  

    swal({
        title: "Are you sure?",
        text: warningTxt,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Yes, Go ahead!",
        closeOnConfirm: true
    }, function () {
        $.ajax({
          url:ajaxControllerHandler,
          method:'POST',
          data: formData,
          beforeSend: function() {
             //$('.tooltip').hide();
             //$('#clean_dynamic_content_store_folder').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Processing').attr('disabled',true);
             $('.overlay').fadeIn();
          },
          success:function(responseData){
              var result = JSON.parse(responseData);
              //console.log(result);
              toastr.options.onHidden = function() { location.reload(); }
              toastr.success(result.message, 'Success!');
              $('.overlay').fadeOut();
              //$('#clean_dynamic_content_store_folder').html('<i class="fa fa-recycle"></i>&nbsp; Clean Dynamic PDF Store Folder').attr('disabled',false);
              return true; 
           }
        });
    });    
});

 //Configuring page records fetching params
 $(document).on('submit', '#fetch_all_records', function(event){
      event.preventDefault();
      var record_status = $('#record_status').val();
      var page_route = $('#page_route').val();

      if(record_status === null){
          window.location = SITE_URL+"?route="+page_route;
      }else{
        $('#fetch_item_data').html('<i class="fa fa-spinner fa-spin"></i>&nbsp;Fetching').attr('disabled',true);
         setTimeout(function(){
         $('#fetch_item_data').html('<i class="fa fa-search"></i>&nbsp;Fetch Data').attr('disabled',false);
         //show sweetalert success
         swal({
          title: "Great!",
          text: "Data has been successfully fetched!",
          type: "success",
          allowEscapeKey : false,
          allowOutsideClick: false
         },function(){
           window.location = SITE_URL+"?route="+page_route+"&record_status="+record_status;
         });},500);
         return true;  
     } 
 });

 /*Status change handler*/
 $(document).on('click','.featured_action',function(){
  var action = "globalFeaturedStatusUpdate";  
  var row_id = $(this).data('rid'); 
  var type = $(this).data('type');
  var featured_status = $(this).data('ftype');
  var page_type = $(this).data('ptype');

  var thisItem = $(this);

  if(featured_status == 'active'){
    var toastrText = 'This '+page_type+' has been marked as featured successfully!';
  }else{
    var toastrText = 'This '+page_type+' has been marked as non-featured successfully!';
  }
  //show toastr success
  toastr.options = {
     closeButton: true,
     progressBar: true,
     showMethod: 'slideDown',
     timeOut: 5000,
  };

  var formData = {action:action,type:type,row_id:row_id,featured_status:featured_status};
  
  $.ajax({
      url:ajaxControllerHandler,
      method:'POST',
      data: formData,
      beforeSend: function() {
         $('.content_div_loader').addClass('sk-loading');
      },
      success:function(responseData){
          //console.log(responseData); 
          var data = JSON.parse(responseData);
          //Disabling loader
          $('.content_div_loader').removeClass('sk-loading');
          //Check response
          if(data.check == 'success'){
              //toggle switcher
              if(featured_status == 'active'){
                $(thisItem).data('ftype', 'inactive');
                $(thisItem).attr('data-original-title','Non-Featured this '+page_type);
                $(thisItem).html('<i class="fa fa-star"></i> Featured');
                toastr.success(toastrText, 'Success!');

              }else{
                $(thisItem).data('ftype', 'active');
                $(thisItem).attr('data-original-title','Featured this '+page_type);
                $(thisItem).html('<i class="fa fa-star-o"></i> Non-Featured');
                toastr.warning(toastrText, 'Success!');
              }  
              return true; 
          }else{
              if(data.message.length>0){
                var toastrErrorText = data.message;
              }else{
                var toastrErrorText = 'Something went wrong! Please try again.'
              }
              //show toastr error
              toastr.options.onHidden = function() { window.location.reload(); }
              toastr.error(toastrErrorText, 'Error!');
              return false;
          } 

       }
     });
 });

 //Handle change record status record
 $(document).on("click",".changeRecordStatus",function (e) {
   e.preventDefault();
   
   var ids = '';
   var row_id = $(this).data('rid');
   var type = $(this).data('type');
   var record_status = $(this).data('rstatus');
   var page_type = $(this).data('ptype');

   if(row_id == 'all'){
     $('.singleCheck').each(function(index,element){
       if($(this).prop("checked") == true){
         ids += $(this).val()+',';
       } 
     });
     ids = ids.replace(/,\s*$/, "");
     var singular_text = 'These';
   }else{
     ids = row_id;
     var singular_text = 'This';
   }

   if(record_status == 'active'){
     var action = 'updateGlobalStatusRecord';
     var errorText = "Restoration failed!";
     var alertText = singular_text+" "+page_type+" will be restored into active record list!";
     var confirmText = "Yes, Restore it!";
     var successText = page_type+" has been successfully restored into active records!";
   }
   else if(record_status == 'blocked'){
     var action = 'updateGlobalStatusRecord';
     var errorText = "Block failed!";
     var alertText = singular_text+" "+page_type+" will be removed to trash;You can recover this item from trash anytime!";
     var confirmText = "Yes, Trash it!";
     var successText = page_type+" has been successfully moved into trash!";
   }else{
     var action = 'deleteGlobalData';
     var errorText = "Deletion failed!";
     var alertText = singular_text+" "+page_type+" will be parmanently deleted and not recoverable!";
     var confirmText = "Yes, Delete it!";
     var successText = page_type+" has been successfully deleted!"; 
   }

   var formData = {action:action,type:type,row_id:ids,record_status:record_status};

   //console.log(formData);return false;
   
   if(ids){
       swal({
            title: "Are you sure?",
            text: alertText,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: confirmText,
            closeOnConfirm: true
            }, function () {
            //dataTable.destroy();
              $.ajax({
              url:ajaxControllerHandler,
              method:'POST',
              data: formData,
              beforeSend: function() {
                 $('.content_div_loader').addClass('sk-loading');
              },
              success:function(responseData){
                 //console.log(responseData); 
                 var data = JSON.parse(responseData);
                 if(data.check == 'success'){
                    //show sweetalert success
                    setTimeout(function() {
                          //Disabling loader
                          $('.content_div_loader').removeClass('sk-loading');
                          //showing success message
                          swal({
                              title: "Success!",
                              text: successText,
                              type: "success"
                          }, function() {
                              location.reload();
                          });
                      }, 1000);

                      return true; 
                 }else{
                     //Disabling loader
                     $('.content_div_loader').removeClass('sk-loading');
                     //show sweetalert success
                     toastr.error(data.message,'Error!', {timeOut: 2000,closeButton:true,progressBar:true});
                     return false;
                 }
               }
             });
          });
       }else{
          toastr.error('Please select at least one data!',errorText, {timeOut: 2000,closeButton:true,progressBar:true});  
          return false;   
       } 
 });

 //Handle change record status record
 $(document).on("click",".sendMailToUser",function (e) {
   e.preventDefault();
   
   var ids = '';
   var user_type = null;
   var row_id = $(this).data('rid');
   var type = $(this).data('type');
   var page_type = $(this).data('ptype');

   if(row_id == 'all'){
     $('.singleCheck').each(function(index,element){
       if($(this).prop("checked") == true){
         ids += $(this).val()+',';
       } 
     });
     ids = ids.replace(/,\s*$/, "");
     var singular_text = 'These';
   }else{
     ids = row_id;
     var singular_text = 'This';
   }

   var errorText = "Mail Sent Failed!";
   var confirmText = "Yes, Go ahead!";
   var successText = page_type+(row_id=='all'?'s have':' has')+"  been successfully sent over mail!";

   if(type == 'student_receipts'){
     var action = 'sendStudentReceipt';
     var alertText = singular_text+" "+page_type+" will be sent to "+singular_text.toLowerCase()+" student!";
   }

   var formData = {action:action,type:type,row_id:ids,user_type:user_type};

   //console.log(formData);return false;
   
   if(ids){
       swal({
            title: "Are you sure?",
            text: alertText,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: confirmText,
            closeOnConfirm: true
            }, function () {
            //dataTable.destroy();
              $.ajax({
              url:ajaxControllerHandler,
              method:'POST',
              data: formData,
              beforeSend: function() {
                 $('.content_div_loader').addClass('sk-loading');
              },
              success:function(responseData){
                 //console.log(responseData); 
                 var data = JSON.parse(responseData);
                 if(data.check == 'success'){
                    //show sweetalert success
                    setTimeout(function() {
                          //Disabling loader
                          $('.content_div_loader').removeClass('sk-loading');
                          //showing success message
                          swal({
                              title: "Success!",
                              text: successText,
                              type: "success"
                          }, function() {
                              location.reload();
                          });
                      }, 1000);

                      return true; 
                 }else{
                     //Disabling loader
                     $('.content_div_loader').removeClass('sk-loading');
                     //show sweetalert success
                     toastr.error(data.message,'Error!', {timeOut: 2000,closeButton:true,progressBar:true});
                     return false;
                 }
               }
             });
          });
       }else{
          toastr.error('Please select at least one data!',errorText, {timeOut: 2000,closeButton:true,progressBar:true});  
          return false;   
       } 
 });
 



       
       