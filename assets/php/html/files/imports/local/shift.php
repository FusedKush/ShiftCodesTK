<header class="shift-header" id="shift_header">
  <div class="content-wrapper">
    <div class="section counters">
      <div class="badge total inactive" id="shift_header_count_total" title="No SHiFT Codes Available" aria-label="No SHiFT Codes Available">
        <span class="count">0</span>
        <span class="fas fa-key"></span>
      </div>
      <div class="badge new inactive" id="shift_header_count_new" title="No New SHiFT Codes" aria-label="No New SHiFT Codes">
        <span class="count">0</span>
        <span class="fas fa-star"></span>
      </div>
      <div class="badge exp inactive" id="shift_header_count_exp" title="No Expiring SHiFT Codes" aria-label="No Expiring SHiFT Codes">
        <span class="count">0</span>
        <span class="fas fa-exclamation-triangle"></span>
      </div>
    </div>
    <div class="section sort">
      <button id="shift_header_sort" title="Sorted by Newest Codes First. Click to change sort." aria-label="Sorted by Newest Codes First. Click to change sort." disabled aria-disabled>
        <span class="fas fa-sort-amount-down"></span>
      </button>
    </div>
  </div>
</header>
<div class="overlay" id="shift_overlay">
  <!-- Spinner -->
  <?php include("./assets/php/html/min/imports/spinner.php"); ?>
  <div class="error" hidden aria-hidden="true">
    <div>No SHiFT Codes are currently available</div>
    <span class="fas fa-heart-broken"></span>
    <div>Please try again later</div>
  </div>
</div>
<section class="feed content-wrapper" id="panel_feed" data-sort="default">
</section>
<template id="panel_template">
  <div class="panel" role="button" data-expanded="false" aria-expanded="false" tabindex="0">
    <div class="flag new" title="New SHiFT Code" aria-label="New SHiFT Code">
      <span class="fas fa-star"></span>
    </div>
    <div class="flag exp" title="Expiring SHiFT Code" aria-label="Expiring SHiFT Code">
      <span class="fas fa-exclamation-triangle"></span>
    </div>
    <div class="header">
      <div class="top">
        <span class="fas fa-key" title="SHiFT Code" aria-label="SHiFT Code"></span>
        <div class="title">
          <div class="reward">5 Golden Keys</div>
          <div class="description">SHiFT Code</div>
        </div>
        <button class="toggle bubble-parent" title="Expand SHiFT Code" aria-label="Expand SHiFT Code">
          <span class="bubble bubble-light"></span>
          <span class="fas fa-chevron-circle-down"></span>
        </button>
      </div>
      <div class="bottom">
        <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
          <span class="progress"></span>
        </div>
      </div>
    </div>
    <div class="body">
      <span class="background fas fa-key"></span>
      <div class="section rel">
        <div class="title">Release Date:</div>
        <div class="content"></div>
      </div>
      <div class="section exp">
        <div class="title">Expiration Date:</div>
        <div class="content"></div>
      </div>
      <div class="section src">
        <div class="title">Source:</div>
        <div class="content">
          <a target="_blank" rel="external noopener" tabindex="-1">
            <span class="fas fa-external-link-square-alt"></span>
            <span class="text"></span>
          </a>
        </div>
      </div>
      <div class="separator"></div>
      <div class="section pc">
        <div class="title"></div>
          <div class="content">
            <span class="display"></span>
            <input class="value" hidden aria-hidden="true" tabindex="-1">
            <button class="copy" title="Copy to Clipboard" aria-label="Copy to Clipboard" disabled aria-disabled="true">
              <span class="fas fa-clipboard"></span>
            </button>
          </div>
        </div>
      <div class="section xbox">
        <div class="title"></div>
        <div class="content">
          <span class="display"></span>
          <input class="value" hidden aria-hidden="true" tabindex="-1">
          <button class="copy" title="Copy to Clipboard" aria-label="Copy to Clipboard" disabled aria-disabled="true">
            <span class="fas fa-clipboard"></span>
          </button>
        </div>
      </div>
      <div class="section ps">
        <div class="title"></div>
        <div class="content">
          <span class="display"></span>
          <input class="value" hidden aria-hidden="true" tabindex="-1">
          <button class="copy" title="Copy to Clipboard" aria-label="Copy to Clipboard" disabled aria-disabled="true">
            <span class="fas fa-clipboard"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
