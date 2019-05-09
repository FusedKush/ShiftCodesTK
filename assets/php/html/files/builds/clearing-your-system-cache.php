<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('./assets/php/html/min/imports/global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/styles/css/min/local/clearing-your-system-cache.min.css<?php echo $svQueryString; ?>" rel="stylesheet"></link>
    <!--// Page-Specific Metadata \\-->
    <!-- Page Title -->
    <title>Clearing your System Cache - ShiftCodesTK</title>
    <meta name="title" content="Clearing your System Cache - ShiftCodesTK">
    <meta property="og:title" content="Clearing your System Cache - ShiftCodesTK">
    <meta property="twitter:title" content="Clearing your System Cache - ShiftCodesTK">
    <!-- Page Description -->
    <meta name="description" content="How to clear your system cache on PC, Xbox, or Playstation">
    <meta property="og:description" content="How to clear your system cache on PC, Xbox, or Playstation">
    <meta property="twitter:description" content="How to clear your system cache on PC, Xbox, or Playstation">
    <!-- Canonical Page Location -->
    <meta name="canonical" href="https://shiftcodes.tk/clearing-your-system-cache">
    <meta property="og:url" content="https://shiftcodes.tk/clearing-your-system-cache">
    <!-- Page Thumbnail Image -->
    <meta property="og:image" content="https://shiftcodes.tk/assets/img/metadata/clearing-your-system-cache.png">
    <meta property="twitter:image" content="https://shiftcodes.tk/assets/img/metadata/clearing-your-system-cache.png">
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
    <main class="content-wrapper">
      <div class="intro">
        <p>If you are experiencing issues with redeeming SHiFT Codes, clearing your system cache is a good first step that can resolve many common errors.</p>
        <p>Choose your platform below, and follow the listed steps.</p>
        <div class="spacer"></div>
        <p><em><b>Note:</b>&nbsp;Clearing your cache will not affect any of your save data or game progress.</em></p>
      </div>
      <div class="dropdown-panel-group">
        <section class="dropdown-panel" id="steam">
          <button class="header">
            <div class="wrapper">
              <div class="title">
                <div class="string">
                  <h2 class="primary">Steam</h2>
                </div>
              </div>
              <div class="indicator">
                <span class="fas fa-chevron-right"></span>
              </div>
            </div>
          </button>
          <div class="body content-container">
            <p>Instead of clearing your cache on Steam, you can instead verify the integrity of the cache and automatically conduct the necessary repairs by performing the following steps:</p>
            <ol class="styled">
              <li>Right click on the game in the Steam library</li>
              <li>Select&nbsp;<strong>Properties</strong></li>
              <li>Click on the&nbsp;<strong>Local Files</strong>&nbsp;tab</li>
              <li>Select&nbsp;<strong>Verify Integrity of Game Cache</strong></li>
            </ol>
            <p><em><b>Note:</b>&nbsp;One or more files may fail to verify. This is normal for most Steam games, as the files that fail to verify are local configuration files that should not be replaced during the process. You can safely ignore this message.</em></p>
          </div>
        </section>
        <section class="dropdown-panel" id="xb1">
          <button class="header">
            <div class="wrapper">
              <div class="title">
                <div class="string">
                  <h2 class="primary">Xbox One</h2>
                </div>
              </div>
              <div class="indicator">
                <span class="fas fa-chevron-right"></span>
              </div>
            </div>
          </button>
          <div class="body content-container">
            <p>You can clear the temporary cache on Xbox One by performing the following steps:</p>
            <ol class="styled">
              <li>Turn off the console.</li>
              <li>Unplug the Power Brick from the back of the console.</li>
              <li>After waiting for approximately 30 seconds, reconnect the Power Brick to the console</li>
              <li>Wait until the light on the Power Brick has changed from white to orange</li>
              <li>Turn the console back on as normal</li>
            </ol>
          </div>
        </section>
        <section class="dropdown-panel" id="xb360">
          <button class="header">
            <div class="wrapper">
              <div class="title">
                <div class="string">
                  <h2 class="primary">Xbox 360</h2>
                </div>
              </div>
              <div class="indicator">
                <span class="fas fa-chevron-right"></span>
              </div>
            </div>
          </button>
          <div class="body content-container">
            <p>You can clear the system cache on Xbox 360 by performing the following steps:</p>
            <ol class="styled">
              <li>Press the&nbsp;<strong>Guide</strong>&nbsp;button on your controller, navigate to&nbsp;<strong>Settings</strong>, and select&nbsp;<strong>System Settings</strong>.</li>
              <li>Select&nbsp;<strong>Storage</strong>.</li>
              <li>Highlight any storage device, and then press the&nbsp;<strong>Y</strong>&nbsp;button on the controller for&nbsp;<strong>Device Options</strong>.</li>
              <li>Select&nbsp;<strong>Clear System Cache</strong>.</li>
              <li>Select&nbsp;<strong>Yes</strong>.</li>
            </ol>
          </div>
        </section>
        <section class="dropdown-panel" id="ps4">
          <button class="header">
            <div class="wrapper">
              <div class="title">
                <div class="string">
                  <h2 class="primary">Playstation 4</h2>
                </div>
              </div>
              <div class="indicator">
                <span class="fas fa-chevron-right"></span>
              </div>
            </div>
          </button>
          <div class="body content-container">
            <p>You can clear the temporary cache on Playstation 4 by performing the following steps:</p>
            <ol class="styled">
              <li>Turn off the console.&nbsp;<strong>Do not enter Rest Mode.</strong></li>
              <li>Once the indicator light has stopped blinking and is completely off, unplug the Power Cord from the console.</li>
              <li>Wait for approximately 30 seconds before plugging the Power Cord back into the console.</li>
              <li>Power the console back on as normal.</li>
            </ol>
          </div>
        </section>
        <section class="dropdown-panel" id="ps3">
          <button class="header">
            <div class="wrapper">
              <div class="title">
                <div class="string">
                  <h2 class="primary">Playstation 3</h2>
                </div>
              </div>
              <div class="indicator">
                <span class="fas fa-chevron-right"></span>
              </div>
            </div>
          </button>
          <div class="body content-container">
            <p>You can clear the system cache on Playstation 3 by performing the following steps:</p>
            <ol class="styled">
              <li>Scroll to the&nbsp;<strong>Game</strong>&nbsp;tab of the XMB and select&nbsp;<strong>Game Data Utility</strong>.</li>
              <li>Open the folder to populate the Game Data List.</li>
              <li>Select the&nbsp;<strong>Game Date List</strong>&nbsp;of the title you are having issues with, then press the&nbsp;<strong>Triangle</strong>&nbsp;button on the controller.</li>
              <li>Select&nbsp;<strong>Delete</strong>, then&nbsp;<strong>Confirm</strong>.</li>
            </ol>
            <p><em><b>Note:</b>&nbsp;This process will also remove some required game data, such as title updates, that you will be prompted to re-download the next time you launch the game.</em></p>
          </div>
        </section>
      </div>
      <p>Once you have cleared your system cache, relaunch the game and try redeeming the SHiFT Code again.</p>
    </main>
    <!-- Support Footer -->
    <?php include_once('./assets/php/html/min/imports/local/support-footer.php'); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('./assets/php/html/min/imports/global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('./assets/php/html/min/imports/global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
  </body>
</html>
