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
    <!-- Server-Side Toasts -->
    <div class="server-side-toasts" hidden>
      <?php 
        $session_toasts = getSessionToast(true);

        if (!is_array($session_toasts)) {
          $session_toasts = [];
        }
        var_dump($session_toasts);
      ?>
      <?php foreach ($session_toasts as $toast) : ?>
        <div class="server-side-toast">
          <?= json_encode($toast, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK); ?>
        </div>
      <?php endforeach; ?>
      <?php removeSessionToast(true); ?>
    </div>
  </div>
<?php })(); ?>
