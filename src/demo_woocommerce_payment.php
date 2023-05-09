<?php

!class_exists("WC_Stripe_Manager") && exit("please install woo-striple-payment");

!class_exists("WC_Misha_Gateway") && exit("class WC_Misha_Gateway 不存在");
class Olt_WC_PaymentWays extends Olt_WC_Settings {

    static $instance    = null;
    protected $payments = array();

    public function __construct() {
        $this->initPayments();
        $this->handleSubmits();
    }

    static function getInstanec() {
        if (self::$instance === null) {
            self::$instance = new Olt_WC_PaymentWays();
        }
        return self::$instance;
    }
    public function handleSubmits() {

        foreach ($this->payments as $payment) {
            // ! 這邊之後要用nonce做驗證
            if (isset($_POST["updateWCPayNonce"]) && wp_verify_nonce($_POST["updateWCPayNonce"], "updateWCPayAction")) {
                do_action('woocommerce_update_options_payment_gateways_' . $payment->id);
            }
        }

    }

    public function initPayments() {

        //  * $paymentGate->id // current class Payment Gateway id
        //  * $paymentGate->enabled 啟用
        //  * $paymentGate->method_title 方法本身標題
        //  * $paymentGate->method_description 方法本身描述
        //  * $paymentGate->settings  current settings
        //  * $paymentGate->form_fields default options settings
        //  * $paymentGate->enabled

        // stripe
        //   * WC_Payment_Gateway (原本woocommerce class)
        //     -  WC_Payment_Gateway_Stripe (stripe base class extends WC_Payment_Gateway)
        //       -  WC_Payment_Gateway_Stripe_CC stripe_cc
        //       -  WC_Payment_Gateway_Stripe_GooglePay stripe_googlepay
        //       -  WC_Payment_Gateway_Stripe_ApplePay stripe_applepay

        $this->payments["bacs"]             = new WC_Gateway_BACS();
        $this->payments["stripe_cc"]        = new WC_Payment_Gateway_Stripe_CC();
        $this->payments["stripe_googlepay"] = new WC_Payment_Gateway_Stripe_GooglePay();
        $this->payments["stripe_applepay"]  = new WC_Payment_Gateway_Stripe_ApplePay();
        // below is olt payment
        // ! 這邊是透過其他plugin擴充的但是要注意優先權不然就要用他提供的hook(woocommerce_payment_gateways)得到全部的支付網關
        $this->payments["misha"] = new WC_Misha_Gateway();
    }

    public function renderHtml() {
        // ? 之後可根據要不要只在當前plguin頁面載入這些script
        if (true) {

            // ! load woo-stripe-payment plugin dependecies
            wp_enqueue_script("googlePay", "https://pay.google.com/gp/p/js/pay.js"); // ? load goolge pay api script for js/admin/googlepay.js
            wp_enqueue_script("wooStripePaymentGoogle", WC_STRIPE_ASSETS . "js/admin/googlepay.js");
            // ?  reference plugins\woo-stripe-payment\includes\admin\class-wc-stripe-admin-assets.php
            wp_enqueue_script("wc-stripe-admin-settings", WC_STRIPE_ASSETS . "js/admin/admin-settings.js");
            wp_enqueue_style('wc-stripe-admin-style');
            wp_style_add_data('wc-stripe-admin-style', 'rtl', 'replace');
            wp_localize_script(
                'wc-stripe-admin-settings',
                'wc_stripe_setting_params',
                array(
                    'routes'     => array(
                        'apple_domain'      => WC_Stripe_Rest_API::get_admin_endpoint(stripe_wc()->rest_api->settings->rest_uri('apple-domain')),
                        'create_webhook'    => WC_Stripe_Rest_API::get_admin_endpoint(stripe_wc()->rest_api->settings->rest_uri('create-webhook')),
                        'delete_webhook'    => WC_Stripe_Rest_API::get_admin_endpoint(stripe_wc()->rest_api->settings->rest_uri('delete-webhook')),
                        'connection_test'   => WC_Stripe_Rest_API::get_admin_endpoint(stripe_wc()->rest_api->settings->rest_uri('connection-test')),
                        'delete_connection' => WC_Stripe_Rest_API::get_admin_endpoint(stripe_wc()->rest_api->settings->rest_uri('delete-connection')),
                    ),
                    'rest_nonce' => wp_create_nonce('wp_rest'),
                    'messages'   => array(
                        'delete_connection' => __('Are you sure you want to delete your connection data?', 'woo-stripe-payment'),
                    ),
                )
            );

            wp_enqueue_style("bundle", plugin_dir_url(__FILE__) . "../dist/bundle.css");
            wp_enqueue_script("bundle", plugin_dir_url(__FILE__) . "../dist/bundle.js", array("jquery"), "1.0", true);
            wp_localize_script("bundle", "localize", array(
                "ajaxurl" => admin_url("admin-ajax.php"),
                // "otherdata" => "....",
                // ....
            ));
        }
        ?>
        <form action="" method="post">
            <!-- // ! wc-stripe-settings-container 是 woo-striple plugin定義的class 有些相關js代碼抓取此元素 -->
            <div class="wc-stripe-settings-container md:container p-3">
                <?php foreach ($this->payments as $gateway) {
            require plugin_dir_path(__FILE__) . "views/payment_card_html.php";}?>
            </div>
            <?php wp_nonce_field("updateWCPayAction", "updateWCPayNonce")?>
            <?php submit_button()?>
        </form>
        <?php
}
}

class Olt_WC_Settings extends WC_Settings_API {

    function generateSafeTextHtml($htmlId, $fieldValue, $setting) {

        $defaults = array(
            'title'             => '',
            'disabled'          => false,
            'class'             => '',
            'css'               => '',
            'placeholder'       => '',
            'type'              => 'text',
            'desc_tip'          => false,
            'description'       => '',
            'custom_attributes' => array(),
        );

        $data  = wp_parse_args($fieldValue, $defaults);
        $value = $setting !== '' ? $setting : $data["value"];

        $html = "<label for='" . esc_attr($htmlId) . "' class='form-label'>" . esc_html($data["title"]) . "</label>" .
        "<input
            type='" . esc_attr($data['type']) . "'
            style='" . $data['css'] . "'
            class='input-text regular-input w-full " . esc_attr($data['class']) . "'
            id='" . esc_attr($htmlId) . "'
            name='" . esc_attr($htmlId) . "'
            placeholder='" . esc_attr($data['placeholder']) . "'
            value='" . esc_attr($value) . "'>" .
        $this->get_description_html($data);

        return $html;

    }

    function renderFieldsHtml($fieldKey, $fieldValue, $instance) {
        $type       = $fieldValue["type"];
        $htmlId     = $this->plugin_id . $instance->id . "_" . $fieldKey;
        $fieldsHtml = "";
        $setting    = $instance->settings[$fieldKey];
        // echo $type;
        switch ($type) {
        case "safe_text":
            $fieldsHtml = $this->generateSafeTextHtml($htmlId, $fieldValue, $setting);
            break;
        case "textarea":
            break;
        case "account_details":
            break;
        case "textarea":
            break;
        case "select":
            break;
        case "checkbox":
            break;
        }

        echo "<div class='mb-3'>" .
            $fieldsHtml .
            "</div>";
    }
}

function renderFields($formFieldKey, $formFields, $settings, $instanceId = "") {
    // print_r($formFields);
    $pluginId = "woocommerce_";
    // $instanceId = "bacs_"; // ! can shipping methods or payment way or ...

    $key              = $formFieldKey;
    $type             = $formFields["type"];
    $default          = $formFields["default"] ?? "";
    $title            = $formFields["title"] ?? "";
    $class            = $formFields["class"] ?? "";
    $style            = $formFields["css"] ?? "";
    $options          = $formFields["options"] ?? array(); // only select
    $disabled         = $formFields["disabled"] ?? false;
    $description      = $formFields["description"] ?? "";
    $customAttributes = getCustomAttributeHtml($formFields);
    $value            = $settings[$key] !== '' ? $settings[$key] : $default;
    $id               = $name               = $pluginId . $instanceId . "_" . $key;
    $labelHtml        = "<label for='" . esc_attr($id) . "' class='text-base font-semibold block' >" . esc_html($title) . " </label>";
    $fieldsHtml       = "";
    // echo "<p>id: $id, type: $type, value:$value"; // !debug

    switch ($type) {
    case "text":
        ob_start();
        ?>
        <input
            type="text"
            class="w-full"
            id="<?php echo esc_attr($id) ?>"
            name="<?php echo esc_attr($name) ?>"
            placeholder=""
            value="<?php echo esc_attr($value) ?>"
            <?php echo $customAttributes ?>
        >
        <?php $fieldsHtml = $labelHtml . ob_get_clean();
        break;
    case "safe_text":
        ob_start();
        ?>
        <input
            type="text"
            class="w-full"
            id="<?php echo esc_attr($id) ?>"
            name="<?php echo esc_attr($name) ?>"
            placeholder=""
            value="<?php echo esc_attr($value) ?>"
            <?php echo $customAttributes ?>
        >
        <?php $fieldsHtml = $labelHtml . ob_get_clean();
        break;
    case "textarea":
        $fieldsHtml =
        $labelHtml .
        "<textarea class='input-text wide-input w-full p-1' id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' rows='3'>" . esc_html($value) . "</textarea>";
        break;
    case "checkbox":
        $checked    = ($value === "yes") ? " checked" : ""; // Check if the checkbox is checked
        $labelHtml  = "<label for='" . esc_attr($id) . "' class='text-base font-semibold mr-3 inline-block' >" . esc_html($title) . "</label>";
        $fieldsHtml = $labelHtml .
        "<input type='checkbox' id='" . esc_attr($id) . "' name='" . esc_attr($name) . "'" . $checked . ">" .
            "</label>";
        break;
    case "account_details":
        $gatewayBacs        = new WC_Gateway_BACS();
        $accountDetailsHtml = $gatewayBacs->generate_account_details_html();
        $fieldsHtml         = "<div id='bacs_accounts'>" . $accountDetailsHtml . "</div>";
        break;
    case "select":
        ob_start();
        ?>
        <select
            id="<?php echo $id ?>"
            name="<?php echo $id ?>"
            class="w-full <?php echo $class ?>"
            style="<?php echo $style ?>"
            <?php disabled($disabled, true)?>
            <?php echo $customAttributes ?>
        >
            <?php foreach ((array) $options as $optionKey => $optionValue): ?>
                <?php if (is_array($optionValue)): ?>
                    <optgroup label="<?php echo esc_attr($optionKey); ?>">
                        <?php foreach ($optionValue as $optionKeyInner => $optionValueInner): ?>
                            <option value="<?php echo esc_attr($optionKeyInner); ?>"
                                <?php selected(in_array((string) $optionKeyInner, $value, true), true);?>
                            >
                                <?php echo esc_html($optionValueInner); ?>
                            </option>
                        <?php endforeach;?>
                    </optgroup>
                <?php else: ?>
                    <option
                        value="<?php echo esc_attr($optionKey); ?>"
                        <?php selected($value, $optionKey);?>
                        >
                        <?php echo esc_html($optionValue); ?>
                    </option>
                <?php endif;?>
            <?php endforeach;?>
        </select>
        <p class="mt-2"><?php echo $description ?></p>

        <?php $fieldsHtml = $labelHtml . ob_get_clean();
        break;
    case "multiselect":
        // ! multiselect value is array
        ob_start();
        ?>
        <select
            multiple="multiple"
            id="<?php echo $id ?>"
            name="<?php echo $id ?>[]"
            class="w-full multiselect <?php echo $class ?>"
            style="<?php echo $style ?>"
            <?php disabled($disabled, true)?>

        >
            <?php foreach ((array) $options as $optionKey => $optionValue): ?>
                <?php if (is_array($optionValue)): ?>
                    <optgroup label="<?php echo esc_attr($optionKey); ?>">
                        <?php foreach ($optionValue as $optionKeyInner => $optionValueInner): ?>
                            <option value="<?php echo esc_attr($optionKeyInner); ?>"
                                <?php selected(in_array((string) $optionKeyInner, $value, true), true);?>
                            >
                                <?php echo esc_html($optionValueInner); ?>
                            </option>
                        <?php endforeach;?>
                    </optgroup>
                <?php else: ?>
                    <option value="<?php echo esc_attr($optionKey); ?>"
                        <?php selected(in_array($optionKey, $value), true);?>
                    >
                        <?php echo esc_html($optionValue); ?>
                    </option>
                <?php endif;?>
            <?php endforeach;?>
        </select>
        <p class="mt-2"><?php echo $description ?></p>
        <?php $fieldsHtml = $labelHtml . ob_get_clean();
        break;
    case "title":
        $fieldsHtml = "<p class='text-lg font-semibold'>$title</p>";
        break;
    case "description":
        $fieldsHtml = "<p>$description</p>";
        break;
    case "button_demo": // * striple google pay
        // * reference plugins\woo-stripe-payment\includes\admin\views\html-button-demo.php
        ob_start();
        ?>
        <div id="<?php echo $formFields["id"] ?>"></div>
        <p class="mt-2"><?php echo $description ?></p>
        <?php $fieldsHtml = $labelHtml . ob_get_clean();
        break;
    default:
        $fieldsHtml = "<label class='d-block fs-6' for='" . esc_attr($id) . "'></label>" .
        $fieldsHtml .= $formFieldKey . "<p>還沒有設定</p>";
        break;
    }

    echo "<div class='mb-4'" . $customAttributes . ">" .
        $fieldsHtml .
        '</div>';
}

function getCustomAttributeHtml($data) {

    $custom_attributes = array();

    if (!empty($data['custom_attributes']) && is_array($data['custom_attributes'])) {
        foreach ($data['custom_attributes'] as $attribute => $attribute_value) {
            $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr(json_encode($attribute_value)) . '"';
        }
    }

    return implode(' ', $custom_attributes);
}

function getPaymentGateWays($print = false) {

    $installed_payment_methods = WC()->payment_gateways()->payment_gateways();
    if ($print) {
        echo '<pre>';
        print_r($installed_payment_methods);
        echo '</pre>';
    } else {
        return $installed_payment_methods;
    }
}

function getPaymentGateWaysSettings($paymentGateWayId, $key = "") {

    $gateWay = null;

    if ($paymentGateWayId === "bacs") {
        $gateWay = new WC_Gateway_BACS();

        return (!$key) ? $gateWay : $gateWay->$key;
    }

}