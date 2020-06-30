<div id="toasts">
  <!-- Active Toast List -->
  <div class="active-toasts"></div>
  <!-- Queued Toast List -->
  <div class="queued-toasts" hidden></div>
  <!-- Server-Side Toasts -->
  <div class="server-side-toasts" hidden>
    <?php foreach ($_SESSION['toasts'] as $i => $toast) : ?>
      <div class="server-side-toast">
        <?= json_encode($toast, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK); ?>
      </div>
    <?php endforeach; ?>
    <?php
      $_SESSION['toasts'] = [];
    ?>
  </div>
</div>