jQuery(document).ready(function (event) {
  jQuery("#toplevel_page_wbcomplugins .wp-submenu li").each(function () {
    var link = jQuery(this).find("a").attr("href");
    if (
      link == "admin.php?page=wbcom-plugins-page" ||
      link == "admin.php?page=wbcom-themes-page" ||
      link == "admin.php?page=wbcom-support-page"
    ) {
      jQuery(this).addClass("hidden");
    }
  });

  //Admin Header Animation Effect
  var ink, d, x, y;
  jQuery("#wb_admin_header #wb_admin_nav ul li").on("click", function (e) {
    var $this = jQuery(this);

    jQuery(this).addClass("wbcom_btn_material");
    setTimeout(function () {
      $this.removeClass("wbcom_btn_material");
    }, 650);

    if (jQuery(this).find(".wbcom_material").length === 0) {
      jQuery(this).prepend('<span class="wbcom_material"></span>');
    }
    ink = jQuery(this).find(".wbcom_material");
    ink.removeClass("is-animated");
    if (!ink.height() && !ink.width()) {
      d = Math.max(jQuery(this).outerWidth(), jQuery(this).outerHeight());
      ink.css({ height: d, width: d });
    }
    x = e.pageX - jQuery(this).offset().left - ink.width() / 2;
    y = e.pageY - jQuery(this).offset().top - ink.height() / 2;
    ink.css({ top: y + "px", left: x + "px" }).addClass("is-animated");
  });
});

(function ($) {
  "use strict";

  $(document).ready(function () {
    /**
     * Responsive Navbar Menu
     */
    var wb_panel_tabs = $(".nav-tab-wrapper > ul");

    $(".wb-toggle-btn").change(function (e) {
      $.initResponsivePanel();
    });

    ($.initResponsivePanel = function () {
      if ($(".wb-toggle-btn").is(":checked")) {
        wb_panel_tabs.slideDown();
      } else {
        wb_panel_tabs.slideUp();
      }
    }),
      $(window).on("resize", function (e) {
        e.preventDefault();
        if ($(window).width() > 768) {
          wb_panel_tabs.fadeIn(1000);
        } else {
          $.initResponsivePanel();
        }
      });
  });
})(jQuery);
