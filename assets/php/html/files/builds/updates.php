<!DOCTYPE html>
<html lang="en">
  <head>
    <!--// Page-Specific Metadata \\-->
    <meta charset="utf-8">
    <!-- Page Title -->
    <title>Updates - ShiftCodesTK</title>
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
    <!-- Local Dependencies -->
    <meta class="loader-localFile" content="updates.min.css">
    <meta class="loader-localFile" content="updates.min.js">
    <!--// Head Imports \\-->
    <?php include_once('./assets/php/html/min/imports/global/head.php'); ?>
  </head>
  <body data-theme="main">
    <!--// Before-Content Imports \\-->
    <?php include_once('./assets/php/html/min/imports/global/beforeContent.php'); ?>
    <!-- Main Content -->
    <header class="main" data-webp='{"path": "/assets/img/banners/updates/", "name": "updates", "alt": ".jpg", "type": "bg"}'>
      <div class="content-container">
        <div class="content-wrapper">
          <div class="content short">
            <h1 class="title">Updates</h1>
            <div class="description">Recent changes and updates to ShiftCodesTK</div>
          </div>
        </div>
      </div>
    </header>
    <header class="updates-header" id="updates_header">
      <div class="content-wrapper">
        <div class="section current" hidden aria-hidden="true" data-hidden="true">
          <span><span>Current Version:</span><a id="updates_header_current" data-internalLink="true"><strong></strong></a></span>
        </div>
        <div class="section jump">
          <button id="updates_header_jump" title="Jump to Changelog" aria-label="Jump to Changelog" data-pressed="false" aria-pressed="false" aria-haspopup="true" autocomplete="off" disabled aria-disabled="true">
            <span>Jump to <span class="fas fa-caret-down"></span></span>
          </button>
          <div class="dropdown" id="updates_header_jump_dropdown" data-expanded="false" aria-expanded="false" hidden aria-hidden="true" data-hidden="true">
            <span class="arrow"></span>
            <ul class="panel" role="menu">
              <span class="description">Jump to:</span>
            </ul>
          </div>
        </div>
      </div>
    </header>
    <main class="content-wrapper">
      <template id="panel_template">
        <section class="panel" data-expanded="false" aria-expanded="false">
          <div class="hashTargetOverlay"></div>
          <div class="header">
            <span class="icon fas"></span>
            <div class="title">
              <h2 class="version"></h2>
              <div class="info">
                <span class="date"></span>
                <span class="separator">&bull;</span>
                <span class="type"></span>
              </div>
            </div>
            <button class="toggle bubble-parent" title="Expand Changelog" aria-label="Expand Changelog">
              <span class="fas fa-chevron-circle-down"></span>
              <span class="bubble bubble-light"></span>
            </button>
          </div>
          <div class="body">
          </div>
        </section>
      </template>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('./assets/php/html/min/imports/global/afterContent.php'); ?>
  </body>
</html>
