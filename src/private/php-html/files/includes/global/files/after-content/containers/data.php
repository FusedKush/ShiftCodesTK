<?php (function () { ?>
  <div id="data">
    <!-- SHiFT Information -->
    <div id="shift_data">
      <!-- SHiFT Platform Information -->
      <div class="platforms">
        <?= json_encode(SHIFT_CODE_PLATFORMS); ?>
      </div>
      <!-- SHiFT Game Information -->
      <div class="games">
        <?= json_encode(SHIFT_GAMES); ?>
      </div>
    </div>
    <!-- Default Form Alert Messages -->
    <div class="form-default-alert-messages">
      <?= json_encode(FORM_VALIDATION_MESSAGES); ?>
    </div>
  </div>
<?php })(); ?>
