<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/php/html/min/imports/importPath.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/styles/css/min/local/help/index.min.css<?php echo $svQueryString; ?>" rel="stylesheet"></link>
    <!--// Page-Specific Metadata \\-->
    <!-- Page Title -->
    <title>Help Center - ShiftCodesTK</title>
    <meta name="title" content="Help Center - ShiftCodesTK">
    <meta property="og:title" content="Help Center - ShiftCodesTK">
    <meta property="twitter:title" content="Help Center - ShiftCodesTK">
    <!-- Page Description -->
    <meta name="description" content="ShiftCodesTK Help and Support Hub">
    <meta property="og:description" content="ShiftCodesTK Help and Support Hub">
    <meta property="twitter:description" content="ShiftCodesTK Help and Support Hub">
    <!-- Canonical Page Location -->
    <meta name="canonical" href="https://shiftcodestk.com/help">
    <meta property="og:url" content="https://shiftcodestk.com/help">
    <!-- Page Thumbnail Image -->
    <meta property="og:image" content="https://shiftcodestk.com/assets/img/metadata/help/index.png">
    <meta property="twitter:image" content="https://shiftcodestk.com/assets/img/metadata/help/index.png">
    <!-- Page-Specific Browser Properties -->
    <link rel="manifest" href="/assets/manifests/main.webmanifest">
    <meta name="theme-color-tm" id="theme_color_tm" content="#f00">
    <!--// Shared Head Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="main">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <main class="content-wrapper">
      <em class="intro">How can we help you?</em>
      <section class="articles">
        <h2>Help Articles</h2>
        <div class="wrapper" id="article_container">
        </div>
      </section>
      <section class="links">
        <h2>External Links</h2>
        <div class="contact wrapper">
          <h3>Contact us</h3>
          <a class="resource" href="https://m.me/ShiftCodesTK" target="_blank" rel="external noopener" title="Contact us on Facebook (External Link)" aria-label="Contact us on Facebook (External Link)">
            <div class="icon">
              <span class="fab fa-facebook-messenger"></span>
            </div>
            <div class="content">
              <div>Contact us</div>
              <div>on Facebook</div>
            </div>
          </a>
          <a class="resource" href="https://twitter.com/messages/compose?recipient_id=3830990053" target="_blank" rel="external noopener" title="Contact us on Twitter (External Link)" aria-label="Contact us on Twitter (External Link)">
            <div class="icon">
              <span class="fab fa-twitter"></span>
            </div>
            <div class="content">
              <div>Contact us</div>
              <div>on Twitter</div>
            </div>
          </a>
        </div>
        <div class="support wrapper">
          <h3>Additional Support</h3>
          <a class="resource" href="http://support.gearboxsoftware.com/" target="_blank" rel="external noopener" title="Visit the official Gearbox Support website (External Link)" aria-label="Visit the official Gearbox Support website (External Link)">
            <div class="icon">
              <span class="fas fa-external-link-square-alt"></span>
            </div>
            <div class="content">
              <div>Official Gearbox</div>
              <div>Support website</div>
            </div>
          </a>
          <a class="resource" href="https://support.2k.com/" target="_blank" rel="external noopener" title="Visit the official 2K Games Support website (External Link)" aria-label="Visit the official 2K Games Support website (External Link)">
            <div class="icon">
              <span class="fas fa-external-link-square-alt"></span>
            </div>
            <div class="content">
              <div>Official 2K Games</div>
              <div>Support website</div>
            </div>
          </a>
        </div>
      </section>
      <section class="notice">
        <h2><span class="fas fa-exclamation-triangle"></span>&nbsp;Notice</h2>
        <p>
          As ShiftCodesTK is not affiliated with Gearbox Software or 2K Games, help and support provided by ShiftCodesTK related to Borderlands & SHiFT is not guaranteed to be 100% accurate. For the most accurate and reliable support, visit the official&nbsp;
          <a class="themed" href="http://support.gearboxsoftware.com/" target="_blank" rel="external noopener" title="Official Gearbox Support website (External Link)" aria-label="Official Gearbox Support Support Website (External Link)">
            <span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link">&nbsp;</span>
            Gearbox Support
          </a>
          &nbsp;or&nbsp;
          <a class="themed" href="https://support.2k.com/" target="_blank" rel="external noopener" title="Official 2K Games Support website (External Link)" aria-label="Official 2K Games Support Website (External Link)">
            <span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link">&nbsp;</span>
            2K Games Support
          </a>
          &nbsp;websites.
        </p>
      </section>
    </main>
    <template id="article_template">
      <a class="resource">
        <div class="icon">
          <span></span>
        </div>
        <div class="content">
          <strong class="title"></strong>
          <div class="description"></div>
        </div>
      </a>
    </template>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/scripts/min/local/help/index.min.js<?php echo $svQueryString; ?>"></script>
  </body>
</html>
