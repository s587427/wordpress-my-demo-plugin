<!-- card -->
<div class="card-box mb-6 bg-slate-200 font-sans rounded-md shadow-lg shadow-slate-400">
  <!-- card-header -->
  <div class="card-header bg-slate-400 p-3 rounded-t-md">
    <p class="text-lg font-black"><?php echo $gateway->method_title ?></p>
  </div>
  <!-- card-body -->
  <div class="p-3">

    <p class="text-2xl mb-1 font-semibold"><?php echo $gateway->method_title ?></p>
    <p class="text-base mb-8 font-medium"><?php echo $gateway->method_description ?></p>

      <?php foreach ($gateway->form_fields as $key => $value): ?>
        <?php //print_r($gateway->settings)?>
        <?php renderFields($key, $value, $gateway->settings, $gateway->id)?>
      <?php endforeach;?>
  </div>

  <?php
// echo "<pre>";
// echo json_encode($gateway->form_fields, JSON_PRETTY_PRINT);
// echo "</pre>";
?>
</div>
