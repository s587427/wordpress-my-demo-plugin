<?php

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
            if (isset($_POST["submit"])) {
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
    }

    public function renderHtml() {
        wp_enqueue_style("tailwindcss", plugin_dir_url(__FILE__) . "./css/bundle.css");
        ?>
        <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script> -->
        <form action="" method="post">
            <div class="custom-card-group md:container p-3">
                <?php foreach ($this->payments as $gateway) {
            require plugin_dir_path(__FILE__) . "views/payment_card_html.php";}?>
            </div>
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
    $key         = $formFieldKey;
    $type        = $formFields["type"];
    $default     = $formFields["default"] ?? "";
    $title       = $formFields["title"] ?? "";
    $class       = $formFields["class"];
    $style       = $formFields["css"];
    $options     = $formFields["options"] ?? array(); // only select
    $disabled    = $formFields["disabled"] ?? false;
    $description = $formFields["description"] ?? "";
    $value       = $settings[$key] !== '' ? $settings[$key] : $default;
    $id          = $name          = $pluginId . $instanceId . "_" . $key;
    $display     = ($formFieldKey === "notice_selector") ? "none" : ""; // ! 目前知道WC_Payment_Gateway_Stripe_CC欄位有這個key
    $labelHtml   = "<label for='" . esc_attr($id) . "' class='text-base font-semibold block' >" . esc_html($title) . " </label>";
    $fieldsHtml  = "";

    switch ($type) {
    case "text":
        $fieldsHtml =
        $labelHtml .
        "<input type='text' class='w-full' id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' placeholder='' value='" . esc_attr($value) . "'>";
        break;
    case "safe_text":
        $fieldsHtml =
        $labelHtml .
        "<input type='text' class='w-full' id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' placeholder='' value='" . esc_attr($value) . "'>";
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
            name="<?php echo $id ?>[]"
            class="w-full <?php echo $class ?>"
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
                    <option value="<?php echo esc_attr($optionKey); ?>" <?php selected(in_array((string) $optionKey, $value, true), true);?>>
                        <?php echo esc_html($optionValue); ?>
                    </option>
                <?php endif;?>
            <?php endforeach;?>
        </select>
        <p class="mt-2"><?php echo $description ?></p>

        <?php $fieldsHtml = $labelHtml . ob_get_clean();
        break;
    case "multiselect":
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
                    <option value="<?php echo esc_attr($optionKey); ?>" <?php selected(in_array((string) $optionKey, $value, true), true);?>>
                        <?php echo esc_html($optionValue); ?>
                    </option>
                <?php endif;?>
            <?php endforeach;?>
        </select>
        <?php $fieldsHtml = $labelHtml . ob_get_clean();
        break;
    case "title":
        $fieldsHtml = "<p class='text-lg font-semibold'>$title</p>";
        break;
    case "description":
        $fieldsHtml = "<p>$description</p>";
        break;
    default:
        $fieldsHtml = "<label class='d-block fs-6' for='" . esc_attr($id) . "'></label>" .
        $fieldsHtml .= $formFieldKey . "<p>還沒有設定</p>";
        break;
    }

    echo "<div class='mb-6" . ($formFieldKey === "notice_selector" ? "d-none" : "") . "'>" .
        $fieldsHtml .
        '</div>';
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