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
    /* Mobile Toggle Menu */
    jQuery(".wb-responsive-menu").on("click", function (e) {
      e.preventDefault();
      if (
        jQuery(".wbcom-admin-settings-page .nav-tab-wrapper ul").hasClass(
          "wbcom-show-mobile-menu"
        )
      ) {
        jQuery(".wbcom-admin-settings-page .nav-tab-wrapper ul").removeClass(
          "wbcom-show-mobile-menu"
        );
      } else {
        jQuery(".wbcom-admin-settings-page .nav-tab-wrapper ul").addClass(
          "wbcom-show-mobile-menu"
        );
      }
    });
    jQuery(document).on(
      "click",
      "ul.wbcom-addons-plugins-links li a",
      function (e) {
        e.preventDefault();
        var getextension = $(this).data("link");
        $(".wbcom-addons-link-active").removeClass("wbcom-addons-link-active");
        $(this)
          .attr("class", "wbcom-addons-link-active")
          .siblings()
          .removeClass("wbcom-addons-link-active");
        var data = {
          action: "wbcom_addons_cards",
          display_extension: getextension,
          nonce: wbcom_plugin_installer_params.nonce,
        };
        $.post(ajaxurl, data, function (response) {
          if ("paid_extension" == response) {
            $("#wbcom-learndash-extension").hide();
            $("#wbcom-themes-list").hide();
            $("#wbcom-free-extension").hide();
            $("#wbcom_paid_extention").show();
          }
          if ("free_extension" == response) {
            $("#wbcom-free-extension").show();
            $("#wbcom-learndash-extension").hide();
            $("#wbcom-themes-list").hide();
            $("#wbcom_paid_extention").hide();
          }
          if ("learndash_extension" == response) {
            $("#wbcom-learndash-extension").show();
            $("#wbcom-free-extension").hide();
            $("#wbcom-themes-list").hide();
            $("#wbcom_paid_extention").hide();
          }
          if ("our_themes" == response) {
            $("#wbcom-themes-list").show();
            $("#wbcom-free-extension").hide();
            $("#wbcom-learndash-extension").hide();
            $("#wbcom_paid_extention").hide();
          }
        });
      }
    );
  });
})(jQuery);
