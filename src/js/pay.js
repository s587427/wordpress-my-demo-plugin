export function initWoocommercePayment() {
  jQuery(document).ready(function ($) {
    // ?*hide or show custom form
    $("#woocommerce_stripe_cc_form_type")
      .on("change", toggleCustomForm)
      .trigger("change")

    // ? hide or show notices selector
    $("#woocommerce_stripe_cc_notice_location")
      .on("change", toggleNoticeLocation)
      .trigger("change")

    // ? toogle enaebled to show or hide area
    // ! woocommerce_stripe_cc_form_type, woocommerce_stripe_cc_notice_location
    // ! 執行順序會影響hide/show, 必須放在它們之下
    $(
      `#woocommerce_bacs_enabled,
       #woocommerce_stripe_cc_enabled, 
       #woocommerce_stripe_googlepay_enabled,
       #woocommerce_stripe_applepay_enabled`
    )
      .on("init", toggleEnabled)
      .on("click", toggleEnabled)
      .trigger("init")

    function toggleEnabled() {
      if ($(this).is(":checked")) {
        $(this).parent().nextAll().show()
      } else {
        $(this).parent().nextAll().hide()
      }
    }

    function toggleCustomForm() {
      const conditonValue = "custom"
      if ($(this).val() === conditonValue) {
        $(`div[data-show-if='{"form_type":"${conditonValue}"}'], 
            select[data-show-if='{"form_type":"${conditonValue}"}']`).show()
      } else {
        $(`div[data-show-if='{"form_type":"${conditonValue}"}'], 
            select[data-show-if='{"form_type":"${conditonValue}"}']`).hide()
      }
    }

    function toggleNoticeLocation() {
      const conditonValue = "custom"
      if ($(this).val() === conditonValue) {
        $(`div[data-show-if='{"notice_location":"${conditonValue}"}'], 
              input[data-show-if='{"notice_location":"${conditonValue}"}']`).show()
      } else {
        $(`div[data-show-if='{"notice_location":"${conditonValue}"}'], 
              input[data-show-if='{"notice_location":"${conditonValue}"}']`).hide()
      }
    }
  })
}
