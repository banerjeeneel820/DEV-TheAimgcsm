
                /* 1. Visualizing things on Hover - See next part for action on click */
                  var clicked = false;
                  //console.log(review_star);
                  $('#stars li').on('mouseover', function(){
                    var onStar = parseInt($(this).data('value'), 10); // The star currently mouse on
                    var state = 'mouseover';
                    var stars = $(this).parent().children('li.star');
                    //console.log(review_star);
                    if(review_star!=null){
                        for (i = 0; i < stars.length; i++) {
                          $(stars[i]).removeClass('selected');
                        }
                     }
                     // JUST RESPONSE (Not needed)
                    var msg = review_count(onStar); 
                    // Now highlight all the stars that's not after the current hovered star
                    $(this).parent().children('li.star').each(function(e){
                      if (e < onStar) {
                        $(this).addClass('hover');
                        responseMessage(clicked,state,msg);
                      }
                      else {
                        $(this).removeClass('hover');
                      }
                    });
                    
                  }).on('mouseout', function(){
                    var stars = $(this).parent().children('li.star');
                    //console.log(review_star);
                    if(review_star!=null){
                        for (i = 0; i < review_star; i++) {
                          $(stars[i]).addClass('selected');
                        }
                    }
                    $(this).parent().children('li.star').each(function(e){
                      var state = 'mouseout';
                      if(review_star!=null){
                        var msg = review_count(review_star);   
                      }else{
                        var msg = 'none';
                      }
                      $(this).removeClass('hover');
                      responseMessage(clicked,state,msg);
                    });
                  });
                  
                  
                  /* 2. Action to perform on click */
                  $('#stars li').on('click', function(){
                    clicked = true;
                    var onStar = parseInt($(this).data('value'), 10); // The star currently selected
                    review_star = onStar;
                    var stars = $(this).parent().children('li.star');
                    var state = 'clicked';
                    
                    for (i = 0; i < stars.length; i++) {
                      $(stars[i]).removeClass('selected');
                    }
                    
                    for (i = 0; i < onStar; i++) {
                      $(stars[i]).addClass('selected');
                    }
                    
                    // JUST RESPONSE (Not needed)
                    var ratingValue = parseInt($('#stars li.selected').last().data('value'), 10);
                    
                    var msg = review_count(ratingValue); 

                    $('#review').val(ratingValue);

                    responseMessage(clicked,state,msg);
                    
                  });
                  
                function review_count(starCount){
                    var msg = "";

                    if (starCount == 1) {
                        msg = "Poor!";
                    }
                    else if(starCount == 2) {
                        msg = "Bellow Avarage!";
                    }
                     else if(starCount == 3) {
                        msg = "Avarage!";
                    }
                     else if(starCount == 4) {
                        msg = "Above Avarage!";
                    }else {
                        msg = "Excellent!";
                    }
                    return msg;
                }  

                function responseMessage(clicked,state,msg) {
                  if(state == 'mouseover' || state == 'clicked'){
                     $('#review-star').fadeIn(200);  
                     $('#review-star span').html(msg);
                   }else{
                     if(clicked == true){
                       $('#review-star').fadeIn(200);  
                       $('#review-star span').html(msg);
                     }else{
                        $('#review-star span').html('');
                     }
                   } 
                }