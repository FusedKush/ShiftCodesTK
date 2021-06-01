<div id="toasts">
  <!-- Active Toast List -->
  <div class="active-toasts" role="log" aria-live="polite"></div>
  <!-- Queued Toast List -->
  <div class="queued-toasts" hidden></div>
  <!-- Templates -->
  <template id="toast_template">
    <div class="toast" id="toast_template" role="alert" hidden aria-hidden="true">
      <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100%" aria-hidden="true">
        <div class="progress"></div>
      </div>
      <div class="content-container">
        <div class="content">
          <div class="icon" aria-hidden="true">
            <span></span>
          </div>
          <div class="message">
            <div class="title"></div>
            <p class="body"></p>
          </div>
        </div>
        <button class="dedicated styled action dismiss-toast layer-target" id="toast_template_dismiss_toast" aria-controls="toast_template" aria-label="Dismiss the toast" data-layer-targets="toast_template_dismiss_toast_tooltip">
          <span class="fas fa-times box-icon" aria-hidden="true"></span>
        </button>
        <div class="layer tooltip" id="toast_template_dismiss_toast_tooltip" data-layer-target="toast_template_dismiss_toast">Dismiss the toast</div>
        <div class="actions">
        </div>
      </div>
    </div>
  </template>
</div>