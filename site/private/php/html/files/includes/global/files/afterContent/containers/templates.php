<?php
  use ShiftCodesTK\Strings;
?>

<div id="templates" hidden>
  <!-- Global Templates -->
  <!-- Toasts -->
  <template id="toast_template">
    <div class="toast" id="toast" role="alert" hidden aria-hidden="true">
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
        <button class="dedicated styled action dismiss-toast layer-target" aria-controls="toast" aria-label="Dismiss the toast">
          <span class="fas fa-times box-icon" aria-hidden="true"></span>
        </button>
        <div class="layer tooltip">Dismiss the toast</div>
        <div class="actions">
        </div>
      </div>
    </div>
  </template>
  <!-- Modals -->
  <template id="modal_template">
    <div class="modal configured inactive" hidden>
      <div class="content-wrapper">
        <div class="panel">
          <div class="header">
            <strong class="title"></strong>
            <button class="close modal-toggle bubble-parent" data-modal-toggle="false" title="Close the modal" aria-label="Close the modal">
              <span class="bubble bubble-light"></span>
              <span class="fas fa-times box-icon" aria-hidden="true"></span>
            </button>
          </div>
          <div class="body">
            <div class="content-container"></div>
          </div>
        </div>
      </div>
    </div> 
  </template>
  <!-- Dropdown Panels -->
  <template id="dropdown_panel_template">
      <div class="dropdown-panel">
        <button class="header dropdown-panel-toggle">
          <div class="wrapper">
            <div class="title">
              <div class="icon"></div>
              <div class="string">
                <h3 class="primary"></h3>
                <span class="secondary"></span>
              </div>
            </div>
            <div class="indicator">
              <span class="fas fa-chevron-right"></span>
            </div>
          </div>
        </button>
        <div class="body content-container"></div>
      </div>
  </template>
  <!-- Pager -->
  <template id="pager_template">
    <div class="pager configured">
      <div class="content-wrapper">
        <button class="styled previous" title="Previous Page" aria-label="Previous Page">
          <span class="fas fa-chevron-left box-icon"></span>
        </button>
        <div class="jumps">
          <div class="content-container">
            <button class="styled jump" title="Jump to Page 1" aria-label="Jump to Page 1">
            <strong class="box-icon">1</strong>
            </button>
          </div>
        </div>
        <button class="styled next" title="Next Page" aria-label="Next Page">
          <span class="fas fa-chevron-right box-icon"></span>
        </button>
      </div>
    </div>
  </template>
  <!-- Form Alerts -->
  <template id="form_alert_template">
    <div class="alert">
      <span class="icon">
        <span class="box-icon"></span>
      </span>
      <span class="message"></span>
    </div>
  </template>

  <!-- Local Templates -->
</div>
