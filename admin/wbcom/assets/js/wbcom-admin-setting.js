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
