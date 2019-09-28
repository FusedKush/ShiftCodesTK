<!-- Dropdown Panels -->
<template id="dropdown_panel_template">
  <div class="dropdown-panel">
    <button class="header">
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
      <div class="actions">
      </div>
    </div>
  </div>
</template>
