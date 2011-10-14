(function ($) {
  Drupal.behaviors.atcontentdisplay_tpg = {
    attach: function(context) {
      $('body.page-taxonomy #block-system-main .article-inner').equalHeight();
      $("body.page-taxonomy #block-system-main").addClass("page-taxonomy-page-grid content-display-grid");
    }
  };
})(jQuery);