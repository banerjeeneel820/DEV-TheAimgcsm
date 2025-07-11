<?php
  $dataArr['action'] = 'gallery';
  $dataArr['table'] = 'gallery';
  $dataArr['protocol'] = 'main_page';
  $dataArr['limit'] = 18;  // Number of entries to show in a page.

  // Look for a GET variable page if not found default is 1. 
  if($urlSegmentArr['params'][1] == 'page'){
    $dataArr['pageNo'] = $urlSegmentArr['params'][2];
  }else{
    $dataArr['pageNo'] = 1;
  }

  $returnArr = global_Pagination_Handler($dataArr);

  $mediaDataArr = $returnArr['listingArr'];
  $pageLink = $returnArr['pageLink'];  

  //Fetch all gallery category for gallery
  $type = 'gallery';
  $mediaCategoryArr = fetchSingleParentCategory($type);
  
  /*print"<pre>";
  print_r($returnArr);
  print"</pre>";*/
?>
	
	<!--Page Title-->
    <section class="page-title" style="background-image:url(<?=RESOURCE_URL?>images/background/12.jpg);">
    	<div class="auto-container">
        	<div class="row clearfix">
            	<!--Title -->
            	<div class="title-column col-lg-6 col-md-12 col-sm-12">
                	<h1>Gallery</h1>
                </div>
                <!--Bread Crumb -->
                <div class="breadcrumb-column col-lg-6 col-md-12 col-sm-12">
                    <ul class="bread-crumb clearfix">
                        <li><a href="<?=SITE_URL?>"><span class="icon fas fa-home"></span> Home</a></li>
                        <li class="active"><span class="icon fas fa-arrow-alt-circle-right"></span> Gallery</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <!--End Page Title-->
	
	<!-- Gallery Section -->
	<section class="gallery-section gallery-page-section" id="page_content">
		<div class="auto-container">
			
			<!--MixitUp Galery-->
            <div class="mixitup-gallery">
                
                <!--Filter
                <div class="filters clearfix">
                	
                	<ul class="filter-tabs filter-btns text-center clearfix">
                	    <li class="active filter" data-role="button" data-filter="all">All</li>	
                	<?php foreach($mediaCategoryArr as $key => $category){ ?>	
                        <li class="active filter" data-role="button" data-filter=".<?=$category->id?>"><?=$category->name?></li>
                    <?php } ?>    
                    </ul>
                    
                </div>-->
                
                <div class="filter-list row clearfix">
                     <div class="col-md-12 col-sm-12 col-xs-12 mb-3">  
                       <span style="text-align:center;"><strong><h2>Our Gallery</h2></strong></span>
                       <!--<div class="alert alert-success" role="alert">
                         Please click in the content bellow to watch the media.&nbsp;Sort media by clicking the categories above.&nbsp;Don't forget to subscribe;&nbsp;Thank you for visting <a href="<?=SITE_URL?>" class="alert-link"> THE AIMGCSM</a>. 
                       </div>-->
                     </div>
				<?php 
				  foreach($mediaDataArr as $key => $media){ 
				  	$mediaCategoryString = implode(' ',explode(',',$media->category_id)); 

                        if($media->content_type == 'image'){
                                   
                           if($media->file_upload_type == "local"){
                             $media_path = USER_UPLOAD_DIR.'gallery/'.$media->content; 

                             if (!strlen($media->content)>0 && !file_exists($media_path)) {   
                                $media_url = RESOURCE_URL.'images/preview.jpg';
                             }else{
                                $media_url = USER_UPLOAD_URL.'gallery/'.$media->content;
                             }      
                          }else{
                             $media_url = $media->content;   
                          }

                          $content = '<a href="'.$media_url.'" data-fancybox="gallery" 
                                      data-caption="'.$media->title.'">
                                        <img alt="image" src="'.$media_url.'">
                                      </a> ';                                                             
                      }else{
                         $media_url = $media->content;  
                         //$video_thumbnail = RESOURCE_URL.'images/watch_video.gif';
                         if(strpos($media_url, 'be/')>0){
                           $video_id = explode("be/", $media_url)[1];
                           $video_thumbnail = 'https://img.youtube.com/vi/'.$video_id.'/hqdefault.jpg';
                         }

                         if(strpos($media_url, 'embed/')>0){
                           $video_id = explode("embed/", $media_url)[1];
                           $video_thumbnail = 'https://img.youtube.com/vi/'.$video_id.'/hqdefault.jpg';
                         } 
                          
                         $content = '<a href="'.$media_url.'" data-fancybox="gallery" 
                                      data-caption="'.$media->title.'">
                                      <img alt="image" src="'.$video_thumbnail.'">
                                     </a> ';
                      }             
				?>
					<!-- Gallery Block -->
					<div class="gallery-block mix <?=$mediaCategoryString?> col-lg-4 col-md-6 col-sm-12">
						<div class="inner-box">
							<div class="image">
								<?=$content?>
							</div>
						</div>
					</div>
			     <?php } ?>		
				</div>
			
				<!--Post Share Options-->
				<div class="styled-pagination text-center">
					<ul class="clearfix">
					 <?=$pageLink?>	
					</ul>
				</div>
				
			</div>
			
		</div>
	</section>
	<!-- End Gallery Section -->
	
	
	