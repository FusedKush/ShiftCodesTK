<?php
  $page['meta'] = [
    'title'       => 'How to Redeem: GOTY - ShiftCodesTK',
    'description' => 'How to redeem SHiFT Codes in Borderlands: Game of the Year Edition',
    'canonical'   => '/help/how-to-redeem/bl1',
    'image'       => 'bl1/2',
    'theme'       => 'bl1'
  ];

  require_once('../../initialize.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link rel="stylesheet" href="/assets/css/local/help/how-to-redeem/instructions.css<?= TK_VERSION_STR; ?>">
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="bl1">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <main class="content-wrapper">
      <!-- How to Redeem Template -->
      <?php include_once('local/how-to-redeem-instructions.php'); ?>
    </main>
    <div class="setup">
      <div class="step 1">Navigate to the Main Menu. Then, select&nbsp;<em>Extras</em>.</div>
      <div class="step 2">Select&nbsp;<em>SHiFT</em>.</div>
      <div class="step 3">If you have not already registered and signed in to&nbsp;<strong>SHiFT</strong>, you will be prompted to do so.</div>
      <div class="step 4">If you are not automatically redirected, press the indicated key to reach the&nbsp;<strong>SHiFT Code</strong>&nbsp;page.</div>
      <div class="step 5">Enter the 25-character SHiFT Code into the provided fields. Once you are finished, click&nbsp;<em>Submit</em>.</div>
      <div class="step 6">If successful, you will receive a confirmation message. The reward from the SHiFT Code will also appear on the&nbsp;<strong>My Rewards</strong>&nbsp;page.</div>
      <div class="step 7">You can now make your way to the&nbsp;<strong>Golden Chest</strong>&nbsp;in&nbsp;<strong>Fyrestone</strong>&nbsp;or&nbsp;<strong>New Haven</strong>&nbsp;to spend your Golden Keys. Both Golden Chests can be found in close proximity to the&nbsp;<strong>Fast Travel Station</strong>. Opening the chest will cost&nbsp;<strong>one Golden Key</strong>, and the rewards will be droppped at the&nbsp;<strong>level of your current character</strong>, regardless of playthrough.</div>
    </div>
    <!-- Support Footer -->
    <?php include_once('local/support-footer.php'); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- local Scripts -->
    <script async src="/assets/js/local/help/how-to-redeem/instructions.js<?= TK_VERSION_STR; ?>"></script>
  </body>
</html>