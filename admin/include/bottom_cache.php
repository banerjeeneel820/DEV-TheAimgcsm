<?php
    if(!empty($cachefilePath) && $this->site_setting_data->site_caching == "active"){
	   //Cache the contents to a cache file
	   $cached = fopen($cachefilePath, 'w');
	   fwrite($cached, ob_get_contents());
	   fclose($cached);
	   ob_end_flush(); // Send the output to the browser
    }
?>
