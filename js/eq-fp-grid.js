(function ($) {
  Drupal.behaviors.atcontentdisplay_fpg = {
    attach: function(context) {
      $('body.front #block-system-main .article-inner').equalHeight();
      $("body.front #block-system-main").addClass("front-page-grid content-display-grid");
    }
  };
})(jQuery);