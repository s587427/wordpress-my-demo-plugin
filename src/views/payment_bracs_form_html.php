<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-wEmeIV1mKuiNpC+IOBjI7aAzPcEZeedi5yW5f2yOq55WWLwNGmvvx4Um1vskeMj0" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>


<?php
if (isset($_POST["save"])) {
    do_action('woocommerce_update_options_payment_gateways_' . $gateway->id);
}
// echo "<p>" . json_encode(getPaymentGateWaysSettings("bacs", "settings")) . "</p>";

// $gateway->admin_options();
?>


<!--
 * $paymentGate->id // current class Payment Gateway id
 * $paymentGate->enabled 啟用
 * $paymentGate->method_title 方法本身標題
 * $paymentGate->method_description 方法本身描述
 * $paymentGate->set$gateway->method_descriptiontings current settings
 * $paymentGate->form_fields default options settings
 * $paymentGate->enabled
-->

<div class="mt-5"></div>

<form id="myForm" method="POST">

  <div class="card">

    <div class="card-header">
      <?php echo $gateway->method_title ?>
    </div>

    <div class="card-body">

      <h5 class="card-title fw-bold"><?php echo $gateway->method_title ?></h5>
      <p class="card-text mb-4"><?php echo $gateway->method_description ?></p>

      <?php foreach ($gateway->form_fields as $key => $value): ?>
        <?php renderFields($key, $value, $gateway->settings)?>
      <?php endforeach;?>
    </div>

  </div>

  <input type="hidden" name="save" value="xd"/>
  <?php submit_button()?>

</form>







