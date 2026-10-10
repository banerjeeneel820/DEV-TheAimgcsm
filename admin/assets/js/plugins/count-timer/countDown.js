(function($) {
   if(localStorage.getItem("count_timer")){
      var count_timer = localStorage.getItem("count_timer");
   } else {
      var count_timer = 0;
   }

   //console.log(count_timer);

   function secondsToHms(d) {
      d = Number(d);
      var h = Math.floor(d / 3600);
      var m = Math.floor(d % 3600 / 60);
      var s = Math.floor(d % 3600 % 60);

      var hDisplay = h > 0 ? h+':' : "";
      var mDisplay = m > 0 ? (m>=10 ? m: '0'+m) +':' : "00";
      var sDisplay = s > 0 ? s>=10 ? s: '0'+s : "00";
      //console.log(hDisplay+mDisplay+sDisplay);
      return hDisplay+mDisplay+sDisplay; 
  }

  $.fn.countdownTimer = function(options) {
    var settings = $.extend({
      time: 60,
      callback: function() {}
    }, options);

    return this.each(function() {
      var $countdown = $(this);
      var intervalId;

      function updateTimer() {
        //settings.time--;

        if(count_timer <= 0){
            localStorage.clear("count_timer");
            var timeSplit = secondsToHms(settings.time);
            count_timer = settings.time;
        } else {
          var timeSplit = secondsToHms(count_timer);
            localStorage.setItem("count_timer",count_timer);
        }

        count_timer--;

        $countdown.text(timeSplit);

        //console.log(count_timer);

        if (settings.time === 0) {
          clearInterval(intervalId);
          settings.callback();
        }
      }

      intervalId = setInterval(updateTimer, 1000);
    });
  };
})(jQuery);




