<header class="shift-header" id="shift_header">
  <div class="content-wrapper">
    <div class="section counters">
      <div class="badge total inactive" id="shift_header_count_total" title="No SHiFT Codes Available" aria-label="No SHiFT Codes Available">
        <span class="count">0</span>
        <span class="fas fa-key"></span>
      </div>
      <button class="badge new inactive" id="shift_header_count_new" title="No New SHiFT Codes" aria-label="No New SHiFT Codes" data-pressed="false" aria-pressed="false" disabled aria-disabled="true">
        <span class="count">0</span>
        <span class="fas fa-star"></span>
      </button>
      <button class="badge exp inactive" id="shift_header_count_exp" title="No Expiring SHiFT Codes" aria-label="No Expiring SHiFT Codes" data-pressed="false" aria-pressed="false" disabled aria-disabled="true">
        <span class="count">0</span>
        <span class="fas fa-exclamation-triangle"></span>
      </button>
    </div>
    <div class="section sort">
      <button id="shift_header_sort" title="Change Sort" aria-label="Change Sort" data-pressed="false" aria-pressed="false" aria-haspopup="true" autocomplete="off" disabled aria-disabled="true">
        <span class="fas fa-sort-amount-down"></span>
      </button>
      <div class="dropdown" id="shift_header_sort_dropdown" data-expanded="false" aria-expanded="false" hidden data-hidden="true">
        <span class="arrow"></span>
        <ul class="panel" role="menu">
          <span class="description">Sort codes by:</span>
          <li role="menuitem">
            <button data-value="default" data-pressed="true" aria-pressed="true" disabled aria-disabled="true"><span>Default</span></button>
          </li>
          <li role="menuitem">
            <button data-value="newest" data-pressed="false" aria-pressed="false" disabled aria-disabled="true"><span>Newest</span></button>
          </li>
          <li role="menuitem">
            <button data-value="oldest" data-pressed="false" aria-pressed="false" disabled aria-disabled="true"><span>Oldest</span></button>
          </li>
        </ul>
      </div>
    </div>
  </div>
</header>
<main class="feed content-wrapper" id="panel_feed" data-filter="none" data-sort="default">
  <div class="overlay" id="shift_overlay">
    <!-- Spinner -->
    <?php include("./assets/php/html/min/imports/spinner.php"); ?>
    <div class="error" hidden aria-hidden="true">
      <div>No SHiFT Codes are currently available</div>
      <span class="fas fa-heart-broken"></span>
      <div>Please try again later</div>
    </div>
  </div>
</main>
<div id="panel_feed_template" hidden aria-hidden="true"></div>
<template id="panel_template">
  <div class="panel" data-extraInfo="false" data-expanded="false" aria-expanded="false">
    <div class="flag new" title="New SHiFT Code" aria-label="New SHiFT Code">
      <span class="fas fa-star"></span>
    </div>
    <div class="flag exp" title="Expiring SHiFT Code" aria-label="Expiring SHiFT Code">
      <span class="fas fa-exclamation-triangle"></span>
    </div>
    <div class="hashTargetOverlay"></div>
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
          <a target="_blank" rel="external noopener">
            <span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link"></span>
            <span class="text"></span>
          </a>
        </div>
      </div>
      <div class="section notes inactive">
        <div class="title">Notes:</div>
        <ul class="content"></ul>
      </div>
      <div class="separator"></div>
      <div class="section pc">
        <div class="title"></div>
          <div class="content">
            <span class="display"></span>
            <input class="value" hidden aria-hidden="true" tabindex="-1">
            <button class="copy" title="Copy to Clipboard" aria-label="Copy to Clipboard">
              <span class="fas fa-clipboard"></span>
            </button>
          </div>
        </div>
      <div class="section xbox">
        <div class="title"></div>
        <div class="content">
          <span class="display"></span>
          <input class="value" hidden aria-hidden="true" tabindex="-1">
          <button class="copy" title="Copy to Clipboard" aria-label="Copy to Clipboard">
            <span class="fas fa-clipboard"></span>
          </button>
        </div>
      </div>
      <div class="section ps">
        <div class="title"></div>
        <div class="content">
          <span class="display"></span>
          <input class="value" hidden aria-hidden="true" tabindex="-1">
          <button class="copy" title="Copy to Clipboard" aria-label="Copy to Clipboard">
            <span class="fas fa-clipboard"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<template id="panel_filter_overlay_template">
  <div class="filter-overlay" data-visible="hover-hide" hidden aria-hidden="true">
    <div class="content-container">
      <div class="title">
        <span class="fas fa-filter"></span>
        <span>Filter Active</span>
      </div>
      <button class="clear" title="Remove active filter" aria-label="Remove active filter">Click to Remove</button>
    </div>
  </div>
</template>
