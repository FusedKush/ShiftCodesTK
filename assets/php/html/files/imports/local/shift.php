<header class="shift-header" id="shift_header">
  <div class="content-wrapper">
    <div class="section counters">
      <div class="badge total inactive" id="shift_header_count_total" title="No SHiFT Codes Available" aria-label="No SHiFT Codes Available">
        <strong class="count">0</strong>
        <span class="fas fa-key"></span>
      </div>
      <button class="badge new inactive" id="shift_header_count_new" title="No New SHiFT Codes" aria-label="No New SHiFT Codes" data-pressed="false" aria-pressed="false" disabled aria-disabled="true">
        <strong class="count">0</strong>
        <span class="fas fa-star"></span>
      </button>
      <button class="badge exp inactive" id="shift_header_count_exp" title="No Expiring SHiFT Codes" aria-label="No Expiring SHiFT Codes" data-pressed="false" aria-pressed="false" disabled aria-disabled="true">
        <strong class="count">0</strong>
        <span class="fas fa-exclamation-triangle"></span>
      </button>
    </div>
    <div class="section sort">
      <button id="shift_header_sort" title="Change Sort" aria-label="Change Sort" disabled aria-disabled="true">
        <span class="fas fa-sort-amount-down"></span>
      </button>
      <div class="dropdown-menu" id="shift_header_sort_dropdown" data-target="shift_header_sort" data-pos="bottom" data-align="right">
        <div class="panel">
          <div class="title">Sort by:</div>
          <ul class="choice-list">
            <li><button class="choice" data-value="default" data-pressed="true" aria-pressed="true">Default</button></li>
            <li><button class="choice" data-value="newest">Newest</button></li>
            <li><button class="choice" data-value="oldest">Oldest</button></li>
          </ul>
        </div>
      </div>
      <!--
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
      -->
    </div>
  </div>
</header>
<main class="feed content-wrapper" id="shift_code_feed" data-filter="none" data-sort="default">
  <div class="overlay" id="shift_overlay">
    <!-- Spinner -->
    <?php include("./assets/php/html/min/imports/local/spinner.php"); ?>
    <div class="error" hidden aria-hidden="true">
      <strong>
        <div>No SHiFT Codes are currently available</div>
        <span class="fas fa-heart-broken"></span>
        <div>Please try again later</div>
      </strong>
    </div>
  </div>
</main>
<div id="shift_code_cache" hidden aria-hidden="true"></div>
<template id="shift_code_template">
  <div class="dropdown-panel shift-code">
    <button class="header" data-custom-labels='{"false": "Expand SHiFT Code", "true": "Collapse SHiFT Code"}'>
      <div class="wrapper">
        <div class="title">
          <div class="icon" title="SHiFT Code" aria-label="SHiFT Code">
            <span class="fas fa-key"></span>
          </div>
          <div class="string">
            <h2 class="primary reward">5 Golden Keys</h2>
            <span class="secondary labels">
              <span class="label description" title="SHiFT Code" aria-label="SHiFT Code">
                <span>SHiFT Code</span>
              </span>
              <span class="label new" title="New SHiFT Code" aria-label="New SHiFT Code">
                <span>New!</span>
              </span>
              <span class="label exp" title="Expiring SHiFT Code" aria-label="Expiring SHiFT Code">
                <span>Expiring!</span>
              </span>
            </span>
          </div>
        </div>
        <div class="indicator">
          <span class="fas fa-chevron-right"></span>
        </div>
        <div class="full-width">
          <div class="progress-bar" aria-valuemin="0" aria-valuemax="100">
            <div class="progress"></div>
          </div>
        </div>
      </div>
    </button>
    <div class="body">
      <div class="background">
        <span class="fas fa-key"></span>
      </div>
      <div class="section rel can-split">
        <strong class="title">Release Date:</strong>
        <div class="content"></div>
      </div>
      <div class="section exp can-split">
        <strong class="title">Expiration Date:</strong>
        <div class="content"></div>
      </div>
      <div class="section src">
        <strong class="title">Source:</strong>
        <div class="content">
          <a class="tr-underline" target="_blank" rel="external noopener">
            <span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link">&nbsp;</span>
          </a>
        </div>
      </div>
      <div class="section notes">
        <strong class="title">Notes:</strong>
        <div class="content">
          <ul class="styled">
          </ul>
        </div>
      </div>
      <div class="separator"></div>
      <div class="section pc">
        <strong class="title"></strong>
        <div class="content code">
          <input class="value" hidden aria-hidden="true" readonly tabindex="-1">
          <div class="display"></div>
          <button class="copy" title="Copy to Clipboard" aria-label="Copy to Clipboard">
            <span class="fas fa-clipboard"></span>
          </button>
        </div>
      </div>
      <div class="section xbox">
        <strong class="title"></strong>
        <div class="content code">
          <input class="value" hidden aria-hidden="true" readonly tabindex="-1">
          <div class="display"></div>
          <button class="copy" title="Copy to Clipboard" aria-label="Copy to Clipboard">
            <span class="fas fa-clipboard"></span>
          </button>
        </div>
      </div>
      <div class="section ps">
        <strong class="title"></strong>
        <div class="content code">
          <input class="value" hidden aria-hidden="true" readonly tabindex="-1">
          <div class="display"></div>
          <button class="copy" title="Copy to Clipboard" aria-label="Copy to Clipboard">
            <span class="fas fa-clipboard"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
