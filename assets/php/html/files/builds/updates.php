<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('./assets/php/html/min/imports/global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/styles/css/min/local/updates.min.css<?php echo $svQueryString; ?>" rel="stylesheet"></link>
    <!--// Page-Specific Metadata \\-->
    <!-- Page Title -->
    <title>Updates - ShiftCodesTK</title>
    <meta name="title" content="Updates - ShiftCodesTK">
    <meta property="og:title" content="Updates - ShiftCodesTK">
    <meta property="twitter:title" content="Updates - ShiftCodesTK">
    <!-- Page Description -->
    <meta name="description" content="Recent changes and updates to ShiftCodesTK">
    <meta property="og:description" content="Recent changes and updates to ShiftCodesTK">
    <meta property="twitter:description" content="Recent changes and updates to ShiftCodesTK">
    <!-- Canonical Page Location -->
    <meta name="canonical" href="https://shiftcodes.tk/updates">
    <meta property="og:url" content="https://shiftcodes.tk/updates">
    <!-- Page Thumbnail Image -->
    <meta property="og:image" content="https://shiftcodes.tk/assets/img/metadata/updates.png">
    <meta property="twitter:image" content="https://shiftcodes.tk/assets/img/metadata/updates.png">
    <!-- Page-Specific Browser Properties -->
    <link rel="manifest" href="/assets/manifests/main.webmanifest">
    <meta name="theme-color-tm" id="theme_color_tm" content="#f00">
    <!--// Shared Head Markup \\-->
    <?php include_once('./assets/php/html/min/imports/global/head.php'); ?>
  </head>
  <body data-theme="main">
    <!--// Before-Content Imports \\-->
    <?php include_once('./assets/php/html/min/imports/global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('./assets/php/html/min/imports/global/main-header.php'); ?>
    <!-- Main Content -->
    <header class="updates-header" id="updates_header">
      <div class="content-wrapper">
        <div class="section current" hidden aria-hidden="true" data-hidden="true">
          <span><span class="title">Current Version:</span>&nbsp;<a class="currentver tr-underline interal" id="updates_header_current"><strong></strong></a></span>
        </div>
        <div class="section jump">
          <button id="updates_header_jump" title="Jump to Changelog" aria-label="Jump to Changelog" data-pressed="false" aria-pressed="false" aria-haspopup="true" autocomplete="off" disabled aria-disabled="true">
            <span>Jump to&nbsp;<span class="fas fa-caret-down"></span></span>
          </button>
          <div class="dropdown-menu no-refocus" id='updates_header_jump_dropdown' data-target="updates_header_jump" data-pos="bottom">
            <div class="panel">
              <div class="title">Jump to:</div>
              <ul class="choice-list scrollable"></ul>
            </div>
          </div>
        </div>
      </div>
    </header>
    <main class="content-wrapper">
      <template id="panel_template">
        <section class="dropdown-panel changelog">
          <button class="header" data-custom-labels='{"false": "Expand Changelog", "true": "Collapse Changelog"}'>
            <div class="wrapper">
              <div class="title">
                <div class="icon">
                  <span class="fas"></span>
                </div>
                <div class="string">
                  <h2 class="primary version"></h2>
                  <div class="secondary info">
                    <span class="date"></span>
                    <span class="separator">&bull;</span>
                    <span class="type"></span>
                  </div>
                </div>
              </div>
              <div class="indicator">
                <span class="fas fa-chevron-right"></span>
              </div>
            </div>
          </button>
          <div class="body content-container"></div>
        </section>
      </template>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('./assets/php/html/min/imports/global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('./assets/php/html/min/imports/global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/scripts/min/local/updates.min.js<?php echo $svQueryString; ?>"></script>
  </body>
</html>
