(function ($) {
  "use strict";
  // @credit taken refernce from "wpswings" plugin
  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
   *
   * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
   *
   * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the WordPress core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */
  jQuery(document).on("click", ".wcd-registerbtn", function (e) {
    const wcd_product_name = document.querySelector("#wcd_product_name").value;
    const wcd_product_description = document.querySelector(
      "#wcd_product_description"
    ).value;
    const wcd_product_price =
      document.querySelector("#wcd_product_price").value;
    const wcd_subscription_interval = document.querySelector(
      "#wcd_subscription_interval"
    ).value;
    const wcd_subscription_period = document.querySelector(
      "#wcd_woo_demo_sub_product_trial_number"
    ).value;
    const wcd_nonce_verify = document.querySelector("#wcd_nonce_subs").value;

    var data = {
      wcd_product_name: wcd_product_name,
      wcd_product_description: wcd_product_description,
      wcd_product_price: wcd_product_price,
      wcd_subscription_interval: wcd_subscription_interval,
      nonce: wcd_admin_param.wcd_nonce_verification,
      wcd_subscription_period: wcd_subscription_period,
      wcd_nonce_verification: wcd_nonce_verify,
      action: "subs_prod_creation_when_activated",
    };

    jQuery.ajax({
      type: "post",
      dataType: "json",
      url: wcd_admin_param.ajaxurl,
      data: data,
      success: function (data1) {
        if (data1 == "Subscription Product Created.") {
          var wcd_prod_creation_form_id =
            document.getElementById("wcd_create_prod_id");
          wcd_prod_creation_form_id.style.display = "none";
          var wcd_prod_creation_form_id = document.getElementById(
            "wcd_create_payment_id"
          );
          wcd_prod_creation_form_id.style.display = "block";
        }
      },
    });
  });

  // Run when payemnt gateway is install and activated on plugin activation.
  jQuery(document).on("click", ".wcd-payment-gate-install", function (e) {
    if ($(this).prop("id") == "woocommerce-paypal-payments") {
      var wcd_gateway_selected = $(this).prop("value");
    } else {
      var wcd_gateway_selected = $(this).prop("value");
    }

    var data = {
      wcd_payment_gateway: wcd_gateway_selected,
      // wcd_nonce_verification : wcd_nonce_verify,
      nonce: wcd_admin_param.wcd_nonce_verification,
      action: "subs_payment_creation_when_activated",
    };
    $.ajax({
      type: "post",
      dataType: "json",
      url: wcd_admin_param.ajaxurl,
      data: data,
      success: function (data1) {
        alert("Gateways is Install , click Finish to proceed");
      },
    });
  });

  jQuery(document).on("click", "#wcd_finish_setup", function (e) {
    location.reload();
  });

  const wcd_demo_subscription_created =
    wcd_admin_param.demo_subscription_product_created;
  if ("yes" != wcd_demo_subscription_created) {
    window.onload = function () {
      wcd_show_pop_up();
    };

    function wcd_show_pop_up() {
      $('[popup-name="' + "popup-1" + '"]').fadeIn(300);
    }

    // Open Popup.
    $(document).on("click", ".open-button", function (e) {
      var popup_name = $(this).attr("popup-open");
      $('[popup-name="' + popup_name + '"]').fadeIn(300);
    });

    // Close Popup.
    $(document).on("click", ".close-button", function (e) {
      var popup_name = $(this).attr("popup-close");
      $('[popup-name="' + popup_name + '"]').fadeOut(300);
    });

    // Close Popup When Click Outside
    $(".popup")
      .on("click", function () {
        var popup_name = $(this).find("[popup-close]").attr("popup-close");
        $('[popup-name="' + popup_name + '"]').fadeOut(300);
      })
      .children()
      .click(function () {
        return false;
      });
  } else {
    // $('.popup').css('display','none');
    var d = document.getElementById("wcd_main_popup_id");
    // if( d ) {
    // 	d.className += "wcd_subscription_product_class";
    // }
  }
})(jQuery);
