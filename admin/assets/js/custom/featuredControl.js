
                /* 1. Visualizing things on Hover - See next part for action on click */
                  //console.log(review_star);
                  $('#featured li').on('mouseover', function(){
                      var value = $(this).data('value');
                      //console.log(review_star);
                      $(this).addClass('hover');

                      if(value == '1'){
                        $(this).removeClass('selected');
                      }else{
                        return true;
                      }

                  }).on('mouseout', function(){
                      var value = $(this).data('value');
                      $(this).removeClass('hover');

                      if(value == '1'){
                        $(this).addClass('selected');
                      }else{
                        return true;
                      }
                  });
                  
                  
                  /* 2. Action to perform on click */
                  $('#featured li').on('click', function(){
                    
                    var row_id = $(this).data('rid'); 
                    var type = $(this).data('type');
                    var value = $(this).data('value');

                    var thisItem = $(this);

                    if(value == 1){
                      var featured = '0';
                      var toastrText = 'This '+type+' has been unmarked as featured successfully!';
                      //$(this).addClass('selected');
                    }else{
                      var featured = '1';
                      var toastrText = 'This '+type+' has been marked as featured successfully!';
                      //$(this).removeClass('selected');
                    }

                    var formData = {action:"globalFeaturedUpdate",type:type,row_id:row_id,featured:featured};

                    //console.log(formData);return false;
                                        
                       $.ajax({
                                url:ajaxControllerHandler,
                                method:'POST',
                                data: formData,
                                beforeSend: function() {
                                   //$('#create_hotel_loader').addClass('sk-loading');
                                },
                                success:function(responseData){
                                  //console.log(responseData);
                                   var data = JSON.parse(responseData);
                                   //console.log(data);return false;
                                   if(data.responseArr.check == 'success'){
                                          //add or remove star
                                          if(featured == 1){ 
                                             $(thisItem).data('value', 1);
                                             $(thisItem).addClass('selected');
                                          }else{
                                             $(thisItem).data('value', 0);
                                             $(thisItem).removeClass('selected');
                                          }  
                                          //show toastr success
                                          toastr.options = {
                                             closeButton: true,
                                             progressBar: true,
                                             showMethod: 'slideDown',
                                             timeOut: 5000
                                          };
                                          toastr.success(toastrText, 'Success!');

                                         return true; 
                                   }else{
                                      //show toastr success
                                      toastr.erro('Something went wrong!', 'Error!');
                                        return false;
                                   }
                                }
                          });
                    
                  });
                  
