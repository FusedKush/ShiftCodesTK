<?php
  require_once(dirname(__DIR__, 2) . '/initialize.php');

  use ShiftCodesTK\PageConfiguration;

  (new PageConfiguration('help/how-to-redeem/borderlands-website'))
    ->setTitle('How to Redeem: GOTY')
    ->setGeneralInfo(
      'How to redeem SHiFT Codes on the Borderlands website',
      'bl1/3'
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
    <link rel="stylesheet" href="/assets/css/local/help/how-to-redeem/instructions.css<?= \ShiftCodesTK\VERSION_QUERY_STR; ?>">
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
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
      <div class="step 1">Navigate to&nbsp;<a class="themed" href="https://borderlands.com" title="Official Borderlands website" aria-label="Official Borderlands website" rel="noopener external" target="_blank"><span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link">&nbsp;</span>borderlands.com</a>.</div>
      <div class="step 2">If you have not already registered and signed in to&nbsp;<strong>SHiFT</strong>, you will be prompted to do so.</div>
      <div class="step 3">Select&nbsp;<em>SHiFT</em>&nbsp;from the menu, then choose&nbsp;<em>Redeem Code</em>.</div>
      <div class="step 4">Once you have reached the&nbsp;<strong>Code Redemption</strong>&nbsp;page, select&nbsp;<em>SHiFT Codes</em>&nbsp;from the list of options.</div>
      <div class="step 5">Enter the 25-character SHiFT Code into the provided field. Once you are finished, click&nbsp;<em>Check</em>.</div>
      <div class="step 6">If the SHiFT Code is available for multiple platforms, you will be prompted to select which platform the SHiFT Code should be redeemed for. You can redeem the SHiFT Code for as many platforms as you would like.</div>
      <div class="step 7">If successful, you will receive a confirmation message. If the SHiFT Code is for&nbsp;<em>Borderlands: The Pre-Sequel</em>&nbsp;or&nbsp;<em>Borderlands 3</em>, additional steps are required to receive your rewards. Refer to the&nbsp;<a class="themed" href="tps#step_8" title="How to Redeem SHiFT Codes in Borderlands: The Pre-Sequel" aria-label="How to Redeem SHiFT Codes in Borderlands: The Pre-Sequel">Borderlands: The Pre-Sequel</a>&nbsp;and&nbsp;<a class="themed" href="bl3#step_6" title="How to Redeem SHiFT Codes in Borderlands 3" aria-label="How to Redeem SHiFT Codes in Borderlands 3">Borderlands 3</a>&nbsp;instructions respectively for more information.</div>
    </div>
    <!-- Support Footer -->
    <?php include_once('local/support-footer.php'); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- local Scripts -->
    <script async src="/assets/js/local/help/how-to-redeem/instructions.js<?= \ShiftCodesTK\VERSION_QUERY_STR; ?>"></script>
  </body>
</html>
