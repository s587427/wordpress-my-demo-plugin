<?php

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

function renderpaymentGateWays($paymentGateWayId = "bacs") {
    $gateway = new WC_Gateway_BACS();
    require plugin_dir_path(__FILE__) . "views/payment_bracs_form_html.php";
}

function renderFields($formFieldKey, $formFields, $settings) {
    // print_r($formFields);
    $pluginId   = "woocommerce_";
    $gatewayId  = "bacs_";
    $key        = $formFieldKey;
    $id         = $name         = $pluginId . $gatewayId . $key;
    $type       = $formFields["type"];
    $default    = $formFields["default"] ?? "";
    $title      = $formFields["title"] ?? "";
    $value      = $settings[$key] !== '' ? $settings[$key] : $default;
    $fieldsHtml = "";

    switch ($type) {
    case "safe_text":
        $fieldsHtml =
        "<label for='" . esc_attr($id) . "' class='form-label'>" . esc_html($title) . "</label>" .
        "<input type='text' class='form-control' id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' placeholder='' value='" . esc_attr($value) . "'>";
        break;
    case "textarea":
        $fieldsHtml =
        "<label for='" . esc_attr($id) . "' class='form-label'>" . esc_html($title) . "</label>" .
        "<textarea class='form-control' id='" . esc_attr($id) . "' name='" . esc_attr($name) . "' rows='3'>" . esc_html($value) . "</textarea>";
        break;
    case "checkbox":
        $checked    = ($value === "yes") ? " checked" : ""; // Check if the checkbox is checked
        $fieldsHtml = "
                <label class='d-block fs-6' for='" . esc_attr($id) . "'>" .
        "<span class='me-3 d-inline-block'>" . esc_html($title) . "</span>" .
        "<input type='checkbox' id='" . esc_attr($id) . "' name='" . esc_attr($name) . "'" . $checked . ">" .
            "</label>";
        break;
    case "account_details":
        $gatewayBacs        = new WC_Gateway_BACS();
        $accountDetailsHtml = $gatewayBacs->generate_account_details_html();
        $fieldsHtml         = "<div id='bacs_accounts'>" . $accountDetailsHtml . "</div>";
        break;
    default:
        $fieldsHtml = "<label class='d-block fs-6' for='" . esc_attr($id) . "'></label>" .
        $fieldsHtml .= $formFieldKey . "<p>還沒有設定</p>";
        break;
    }

    echo "<div class='mb-3'>" .
        $fieldsHtml .
        '</div>';
}
