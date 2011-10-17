(function ($) {
  $(document).ready(function() {
		
    
    $('article.flexible-slideshow .flexslider').flexslider({
      animation: "slide",
    });

    // absurd work-around for webkit, because safari and chrome are pices of shit.
    $("article.nivo-slideshow .article-content img").load(function() {
        var imageHeight = $(this).height();
        $(this).parent().css("height",imageHeight);
    });

    
  }); //end ready
}(jQuery));