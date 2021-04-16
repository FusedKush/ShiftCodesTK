<?php
  require_once('initialize.php');

  use ShiftCodesTK\PageConfiguration;

  (new PageConfiguration('updates'))
    ->setTitle('Updates')
    ->setGeneralInfo(
      'Recent changes and updates to ShiftCodesTK',
      'bl2/5'
    )
    ->saveConfiguration();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/css/local/updates.css<?php echo \ShiftCodesTK\VERSION_QUERY_STR; ?>" rel="stylesheet"></link>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <header class="updates-header" id="updates_header">
      <div class="content-wrapper">
        <div class="section current">
          <span><span class="title">Current Version:</span>&nbsp;<strong class="currentver"><?php echo $serverVersion; ?></strong></span>
        </div>
        <div class="section jump">
          <button id="updates_header_jump" title="Jump to Changelog" aria-label="Jump to Changelog" data-pressed="false" aria-pressed="false" aria-haspopup="true" autocomplete="off" disabled aria-disabled="true">
            <span>Jump to&nbsp;<span class="fas fa-caret-down"></span></span>
          </button>
          <div class="dropdown-menu no-auto-config no-refocus o-toggle" id='updates_header_jump_dropdown' data-target="updates_header_jump" data-pos="bottom" data-align="right">
            <div class="panel">
              <div class="title">Jump to:</div>
              <ul class="choice-list scrollable"></ul>
            </div>
          </div>
        </div>
      </div>
    </header>
    <main class="content-wrapper">
      <div class="full-changelog-link">
        This is a simplified list of changes related to ShiftCodesTK's service. For the complete list of changes, visit the&nbsp;<strong>Releases</strong>&nbsp;page on&nbsp;
        <a class="themed" href="https://github.com/FusedKush/ShiftCodesTK/releases" rel="external noopener" target="_blank" title="ShiftCodesTK Releases on Github" aria-label="ShiftCodesTK Releases on Github"><span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link">&nbsp;</span>GitHub</a>
      </div>
      <div id="changelog_list"></div>
      <div class="pager no-auto-config" id="changelog_pager" data-offset="10" data-subtractoffset="true" data-onclick="updates_header_jump"></div>
      <div class="overlay" id="changelog_overlay">
        <!-- Loading spinner -->
        <?php include_once('local/spinner.php'); ?>
      </div>
    </main>
    <template id="changelog_template">
      <section class="dropdown-panel changelog">
        <button class="header dropdown-panel-toggle" data-custom-labels='{"false": "Expand Changelog", "true": "Collapse Changelog"}'>
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
    <template id="changelog_jump_template">
      <li role="menuitem">
        <a class="choice" href="#" title="Jump to Version 1.0.0 Changelog" aria-label="Jump to Version 1.0.0 Changelog"><span></span></a>
      </li>
    </template>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/js/local/updates.js<?php echo \ShiftCodesTK\VERSION_QUERY_STR; ?>"></script>
  </body>
</html>
