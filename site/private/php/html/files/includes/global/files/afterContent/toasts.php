<div class="toast-list" id="toast_list" role="log" aria-live="polite"></div>

<?php if (count($_SESSION['toasts']) > 0) : ?>
  <div class="toast-session-toasts" id="toast_session_toasts" hidden>
    <?= json_encode($_SESSION['toasts'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK); ?>
  </div>

  <?php getSessionToast(true); ?>
<?php endif; ?>
