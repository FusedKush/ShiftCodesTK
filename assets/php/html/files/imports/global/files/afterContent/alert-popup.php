<div class="alert-popup-list" id="alert_popup_feed" data-popup-count="0"></div>
<template id="alert_popup_template">
  <div class="alert-popup" id="alert_popup" role="alert" hidden aria-hidden="true" data-expanded="false" aria-expanded="false">
    <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100%">
      <div class="progress"></div>
    </div>
    <div class="content-container">
      <div class="content">
        <div class="icon">
          <span></span>
        </div>
        <div class="message">
          <div class="title"></div>
          <p class="description"></p>
        </div>
      </div>
      <div class="actions">
        <a class="button action"></a>
        <button class="styled close" title="Close the popup" aria-label="Close the popup">Close</button>
      </div>
    </div>
  </div>
</template>
