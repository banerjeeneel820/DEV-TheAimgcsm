/*
 * jQuery Theme.Custom
 * http://theaimgcsm.com/
 * Copyright (c) 2019 The AIMGCSM
 */

//Declaring Global Varibales and Objects

//Google recaptcha variables
var captchaWidgetId;
var countAddShowClassContact = 0;
var onloadCallback = function() {
   // Renders the HTML element with id 'example1' as a reCAPTCHA widget.
   // The id of the reCAPTCHA widget is assigned to 'widgetId1'.
   captchaWidgetId = grecaptcha.render('form_recaptcha_div', {
     'sitekey' : '6LdJ398UAAAAALCcgKy69mXlTjI4sfz682uHR0_e',
     'theme' : 'light'
   });
 } 

 /*Site cookie Handler*/
 //deleteCookie('cookieaccepted');

 function acceptCookie() {
   document.cookie = "cookieaccepted=1; expires=Thu, 18 Dec 2030 12:00:00 UTC; path=/", document.getElementById("cookie-notice").style.visibility = "hidden"
 }

 function deleteCookie(name) {
   document.cookie = name +'=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;';
 }

 document.cookie.indexOf("cookieaccepted") < 0 && (document.getElementById("cookie-notice").style.visibility = "visible");

 function myFunction() {
    document.getElementById("cookie-notice").style.visibility = "hidden";
 }
 /*End here*/  

//User city autofill variables
var searchInput = 'user_city';  

function getLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(serUserLocation);
  } else { 
    x.innerHTML = "Geolocation is not supported by this browser.";
  }
}

function serUserLocation(position){
  //Pouplating user location value on hidden fields  
  $('#user_latitude').val(position.coords.latitude);
  $('#user_longitude').val(position.coords.longitude);
}

function autocomplete(inp, arr) {
/*the autocomplete function takes two arguments,
the text field element and an array of possible autocompleted values:*/
var currentFocus;
/*execute a function when someone writes in the text field:*/
inp.addEventListener("input", function(e) {
    var a, b, i, val = this.value;
    /*close any already open lists of autocompleted values*/
    closeAllLists();
    if (!val) { return false;}
    currentFocus = -1;
    /*create a DIV element that will contain the items (values):*/
    a = document.createElement("DIV");
    a.setAttribute("id", this.id + "autocomplete-list");
    a.setAttribute("class", "autocomplete-items");
    /*append the DIV element as a child of the autocomplete container:*/
    this.parentNode.appendChild(a);
    /*for each item in the array...*/
    for (i = 0; i < arr.length; i++) {
      /*check if the item starts with the same letters as the text field value:*/
      if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
        /*create a DIV element for each matching element:*/
        b = document.createElement("DIV");
        /*make the matching letters bold:*/
        b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
        b.innerHTML += arr[i].substr(val.length);
        /*insert a input field that will hold the current array item's value:*/
        b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
        /*execute a function when someone clicks on the item value (DIV element):*/
        b.addEventListener("click", function(e) {
            /*insert the value for the autocomplete text field:*/
            inp.value = this.getElementsByTagName("input")[0].value;
            /*close the list of autocompleted values,
            (or any other open lists of autocompleted values:*/
            closeAllLists();
        });
        a.appendChild(b);
      }
    }
});
/*execute a function presses a key on the keyboard:*/
inp.addEventListener("keydown", function(e) {
    var x = document.getElementById(this.id + "autocomplete-list");
    if (x) x = x.getElementsByTagName("div");
    if (e.keyCode == 40) {
      /*If the arrow DOWN key is pressed,
      increase the currentFocus variable:*/
      currentFocus++;
      /*and and make the current item more visible:*/
      addActive(x);
    } else if (e.keyCode == 38) { //up
      /*If the arrow UP key is pressed,
      decrease the currentFocus variable:*/
      currentFocus--;
      /*and and make the current item more visible:*/
      addActive(x);
    } else if (e.keyCode == 13) {
      /*If the ENTER key is pressed, prevent the form from being submitted,*/
      e.preventDefault();
      if (currentFocus > -1) {
        /*and simulate a click on the "active" item:*/
        if (x) x[currentFocus].click();
      }
    }
});
function addActive(x) {
  /*a function to classify an item as "active":*/
  if (!x) return false;
  /*start by removing the "active" class on all items:*/
  removeActive(x);
  if (currentFocus >= x.length) currentFocus = 0;
  if (currentFocus < 0) currentFocus = (x.length - 1);
  /*add class "autocomplete-active":*/
  x[currentFocus].classList.add("autocomplete-active");
}
function removeActive(x) {
  /*a function to remove the "active" class from all autocomplete items:*/
  for (var i = 0; i < x.length; i++) {
    x[i].classList.remove("autocomplete-active");
  }
}
function closeAllLists(elmnt) {
  /*close all autocomplete lists in the document,
  except the one passed as an argument:*/
  var x = document.getElementsByClassName("autocomplete-items");
  for (var i = 0; i < x.length; i++) {
    if (elmnt != x[i] && elmnt != inp) {
      x[i].parentNode.removeChild(x[i]);
    }
  }
}
/*execute a function when someone clicks in the document:*/
document.addEventListener("click", function (e) {
    closeAllLists(e.target);
});
}

$(window).load(function() {
    $(".book_preload").delay(2000).fadeOut(200);
    $(".book").on('click', function() {
        $(".book_preload").fadeOut(200);
    })
});

$(document).on('ready',function(e){
  
  if($(".sticky_sidebar").length > 0){
      //STICKY SIDEBAR HANDLER
      $.stickysidebarscroll(".sticky_sidebar",{offset: {top: 70, bottom: 700}});  
  } 
  
  setTimeout(function(){
      if($("#page_content").length > 0){
          $('html,body').animate({
             scrollTop: $("#page_content").offset().top
          }, 'slow');   
      }    
  },500);

  //Calling geo location function to collect user location
  //getLocation();
  
  //Actiating tooltip for dom
  $('[data-toggle="tooltip"]').tooltip();  

  //applying select 2 js for course dropdown
  //$('.course').select2({'width': "100%",'border-radius':'50px'});

  setTimeout(function(){
     if($('.shimmer-preview').length>0 && $('.div-content').length>0){
       $('.shimmer-preview').addClass('d-none');
       $('.div-content').removeClass('d-none');
     }
    
     //Loading datatable specifications
     if($('.dataTables').length>0){
        $('.dataTables').DataTable({
          "pageLength": 10,
          "lengthChange": false,
          "paging":true,
          "responsive": true,
          "info":false,
          "ordering": false,
          language: {"searchPlaceholder": "Search by name...",},
          //sDom: 'lrtip'
        });
     }    
   },2000);

   if($('#user_enquiry_form').length){
        $('#user_enquiry_form').validate();
    }
        
});

//User enquiry form city autosuggestion
$(document).on('keyup','#user_city',function(e){
   e.preventDefault();
   
   var city = $(this).val();
   var formData = {action:"findUserCity",city:city}

   $.ajax({
        type: "POST",
        url: ajaxCallUrl,
        data: formData,
        beforeSend: function() {
            //$("#search-box").css("background", "#FFF url(LoaderIcon.gif) no-repeat 165px");
        },
        success: function(responseData) {
           var data = JSON.parse(responseData);

           if(data.check == 'success'){
              /*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
              autocomplete(document.getElementById("user_city"), data.cities);
              //$("#search-box").css("background", "#FFF"); 
           }else{
              return false; 
           }
        }
    });
});

//Clean cache files from server
$(document).on("click",".clearSiteCache",function(e){
    e.preventDefault();

    var currentCacheFile = $('#currentCacheFile').val();

    var warningTxt = "Are you sure to delete current page cache?"; 
    var formData = {action:"clearCacheFolder",currentCacheFile:currentCacheFile}; 

    $.ajax({
      url:ajaxCallUrl,
      method:'POST',
      data: formData,
      beforeSend: function() {
         //$('.tooltip').hide();
      },
      success:function(responseData){
          var result = JSON.parse(responseData);
          //console.log(result);
          swal("Success!",result.message, "success");
          return true; 
       }
    });
});

function check_user_email(user_email){
    if(user_email.length >0){
      var regularExp = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
      var chkEmail = regularExp.test(user_email);

    if(!chkEmail){
        swal({
           title: "Oops!",
           text: "Enter a proper email!",
           type: "error"
        });
        $('#create').attr('disabled',true);
        return false;
     }
    }else{
      swal({
         title: "Oops!",
         text: "This field is required!",
         type: "error"
      });
      $('#create').attr('disabled',true);
      return false;
    }
}

/*What'sapp Widget*/
// var url = 'https://wati-integration-service.clare.ai/ShopifyWidget/shopifyWidget.js?75265';
// var s = document.createElement('script');
// s.type = 'text/javascript';
// s.async = true;
// s.src = url;
// var options = {
//   "enabled":true,
//   "chatButtonSetting":{
//       "backgroundColor":"#4dc247",
//       "ctaText":"Contact For Franchise",
//       "borderRadius":"25",
//       "marginLeft":"30",
//       "marginBottom":"18",
//       "marginRight":"90",
//       "position":"right"
//   },
//   "brandSetting":{
//       "brandName":"The AIMGCSM",
//       "brandSubTitle":"Typically replies within a day",
//       "brandImg":"https://cdn.clare.ai/wati/images/WATI_logo_square_2.png",
//       "welcomeText":"Hi there!\nHow can I help you?",
//       "messageText":"Hello, I have a question about {{page_link}}",
//       "backgroundColor":"#0a5f54",
//       "ctaText":"Start Chat",
//       "borderRadius":"25",
//       "autoShow":false,
//       "phoneNumber":"9831649099"
//   }
// };
// s.onload = function() {
//     CreateWhatsappChatWidget(options);
// };
// var x = document.getElementsByTagName('script')[0];
// x.parentNode.insertBefore(s, x);
/*end here*/

/*$('.nb-form').on('mouseover',function(){
    if(countAddShowClassContact == 0){
        console.log(countAddShowClassContact);
        $('#minimize_contact_form').removeClass('d-none');
        $(this).addClass('show');
        countAddShowClassContact++;
    }    
});

$('#minimize_contact_form').on('click',function(){
    $(this).addClass('d-none');
    $('.nb-form').removeClass('show');
    countAddShowClassContact = 0;
});*/

var wa_btnSetting = {"btnColor":"#16BE45","ctaText":"Contact Us for Franchise","cornerRadius":40,"marginBottom":20,"marginLeft":20,"marginRight":20,"btnPosition":"left","whatsAppNumber":"919831649099","welcomeMessage":"Hi, how can we help you?\nWe normally reply reply within a day.","zIndex":999999,"btnColorScheme":"light"};
  window.onload = () => {
    _waEmbed(wa_btnSetting);
};

//Hndling enquiry type of the contact form
$(document).on('change','#enquiry_type',function(){
      var enquiry_type = $(this).val();
      if(enquiry_type == "course"){
         $('#course_div').removeClass('d-none').attr('disabled',false);
         $('#subject_div').addClass('d-none').attr('disabled',true);
         $('#course_id').attr('required',true);
         $('#subject').attr('required',false);
      }else{
         $('#subject_div').removeClass('d-none').attr('disabled',false);
         $('#course_div').addClass('d-none').attr('disabled',true);
         $('#course_id').attr('required',false);
         $('#subject').attr('required',true);
      }
      return true;
     });

     //handling user enquiry form
     $(document).on('submit', '#user_enquiry_form', function(event){
       event.preventDefault();  

       var enquiry_type = $("#enquiry_type").val();
     
       var course_id = $('#course_id').val();
       var course_name = $('#course_'+course_id).text();
       //putting course name for further use   
       $('#course_name').val(course_name);
           
       //var user_email = $('#user_email').val();
       //validate user email
       //check_user_email(user_email);

       if(enquiry_type == "course"){
           var course_id = $("#course_id").val();
           if(!course_id>0){
            swal("Error!","Please select a course to proceed!", "error");
            return false;
           }
       }else{
           var subject = $("#subject").val(); 
           if(!subject.length>0){
            swal("Error!","Please write a subject to proceed!", "error");
            return false;
           }
       }

       $.ajax({
          url:ajaxCallUrl,
          method:'POST',
          data: new FormData(this),
          contentType:false,
          processData:false,
          beforeSend: function() {
             $('#contact_submit').html('Connecting <i class="fa fa-spinner fa-spin"></i>').attr('disabled',true); 
          },
          success:function(responseData){
              var data = JSON.parse(responseData);
              $('#contact_submit').html('Send Now').attr('disabled',false); 
              //reseting captcha
              grecaptcha.reset(captchaWidgetId);
              //hiding subject div
              $('#subject_div').addClass('d-none').attr('disabled',true);
              $('#course_div').addClass('d-none').attr('disabled',true);
              //reseting form data
              $('#user_enquiry_form')[0].reset();
             //console.log(responseData);
             if(data.check == 'success'){
                //show sweetalert success
                swal("Success!", "Your enquiry is reached to us successfully! We shall contact you shortly.", "success");
                return true; 
             }else{
                //show sweetalert success
                 if(data.message.length>0){
                   var message = data.message;
                }else{
                   var message = "Something went wrong";
                }
                swal("Error!",message, "error");
                return false;
             }
          }
           });
     });  

     //UNSUBSCRIBE USER NEWSLETTER FORM HANDLER 
     $(document).on('submit', '#unsubscribe_newsletter_form', function(event){
          event.preventDefault();

           var user_email = $('#unsubscribe_email').val();
         //validate user email
         check_user_email(user_email);
           
           $.ajax({
            url:ajaxCallUrl,
            method:'POST',
            data: new FormData(this),
            contentType:false,
            processData:false,
            beforeSend: function() {
               //$('#unsubscribe_user_btn').html('<i class="fa fa-spinner fa-spin"></i>').attr('disabled',true); 
            },
            success:function(responseData){
                //console.log(responseData);return false;
                var data = JSON.parse(responseData);
                $('#unsubscribe_user_btn').html('Submit').attr('disabled',false); 
                if(data.check == 'success'){
                 $('#unsubscribe_newsletter_form')[0].reset(); 
                 swal("Success!", "An email is sent to your inbox containing link to unsubscribe.");
                 return true; 
               }else{
                  swal("Error!", "Something went wrong!", "error");
                  return false;
               }
            }
           });
       });  

    