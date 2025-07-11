  <div class="middle-box text-center animated fadeInDown">
        <?php if($pageContent['pageData']['data']['page'] != "dashboard"){ ?>
        
          <h1>403</h1>

          <h3 class="font-bold">This Area is Forbidden</h3>

          <div class="error-desc">
              You don't have permission to enter this area. We apologize for your inconvenience.Please contact the admin.<br/>
              You can go back to dashboard: <br/><a href="<?=SITE_URL?>" class="btn btn-primary m-t">Dashboard</a>
          </div>

        <?php }else{ ?>
           
           <h2>Unavailable Data</h2>

           <h3 class="font-bold">No content is available at this moment! Please try again letter.</h3>

           <div class="error-desc">
                No Data is available at this moment for Dashboard page. We apologize for your inconvenience.Please try again letter.<br/>
                You can check other modules.
           </div>  

        <?php } ?>   
    </div>