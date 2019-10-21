<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/php/html/min/imports/importPath.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link rel="stylesheet" href="/assets/styles/css/min/local/help/how-to-redeem/instructions.min.css">
    <!--// Page-Specific Metadata \\-->
    <!-- Page Title -->
    <title>How to Redeem: SHiFT Website - ShiftCodesTK</title>
    <meta name="title" content="How to Redeem: Online - ShiftCodesTK">
    <meta property="og:title" content="How to Redeem: SHiFT Website - ShiftCodesTK">
    <meta property="twitter:title" content="How to Redeem: SHiFT Website - ShiftCodesTK">
    <!-- Page Description -->
    <meta name="description" content="How to redeem SHiFT Codes on the SHiFT website">
    <meta property="og:description" content="How to redeem SHiFT Codes on the SHiFT website">
    <meta property="twitter:description" content="How to redeem SHiFT Codes on the SHiFT website">
    <!-- Canonical Page Location -->
    <meta name="canonical" href="https://shiftcodestk.com/help/how-to-redeem/shift-website">
    <meta property="og:url" content="https://shiftcodestk.com/how-to-redeem/shift-website">
    <!-- Page Images -->
    <meta name="header:image" content="bl1_4">
    <meta property="og:image" content="https://shiftcodestk.com/assets/img/metadata/bl1/4.png">
    <meta property="twitter:image" content="https://shiftcodestk.com/assets/img/metadata/bl1/4.png">
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
      <!-- How to Redeem Template -->
      <?php include_once('local/how-to-redeem-instructions.php'); ?>
      <div class="note"><strong>Note:</strong>&nbsp;While the appearance of the site may differ between devices, the steps should remain the same.</div>
    </main>
    <div class="setup">
      <div class="step 1">Navigate to&nbsp;<a class="themed" href="https://shift.gearboxsoftware.com" title="Official SHiFT website" aria-label="Official SHiFT website" rel="noopener external" target="_blank"><span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link">&nbsp;</span>shift.gearboxsoftware.com</a>. If you have not already registered and signed in, you will be prompted to do so.</div>
      <div class="step 2">Once you have reached your home page, select&nbsp;<em>Rewards</em>&nbsp;from the menu.</div>
      <div class="step 3">Enter the 25-character SHiFT Code into the provided field. Once you are finished, click&nbsp;<em>Check</em>.</div>
      <div class="step 4">If the SHiFT Code is available for multiple platforms, you will be prompted to select which platform the SHiFT Code should be redeemed for. You can redeem the SHiFT Code for as many platforms as you would like.</div>
      <div class="step 5">If successful, you will receive a confirmation message. If the SHiFT Code is for&nbsp;<em>Borderlands: The Pre-Sequel</em>&nbsp;or&nbsp;<em>Borderlands 3</em>, additional steps are required to receive your rewards. Refer to the&nbsp;<a class="themed" href="tps#step_8" title="How to Redeem SHiFT Codes in Borderlands: The Pre-Sequel" aria-label="How to Redeem SHiFT Codes in Borderlands: The Pre-Sequel">Borderlands: The Pre-Sequel</a>&nbsp;and&nbsp;<a class="themed" href="bl3#step_6" title="How to Redeem SHiFT Codes in Borderlands 3" aria-label="How to Redeem SHiFT Codes in Borderlands 3">Borderlands 3</a>&nbsp;instructions respectively for more information.</div>
    </div>
    <!-- Support Footer -->
    <?php include_once('local/support-footer.php'); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- local Scripts -->
    <script async src="/assets/scripts/min/local/help/how-to-redeem/instructions.min.js?v=<?php echo $svQueryString; ?>"></script>
  </body>
</html>
