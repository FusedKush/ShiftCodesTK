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
    <title>How to Redeem: Borderlands 3 - ShiftCodesTK</title>
    <meta name="title" content="How to Redeem: BL3 - ShiftCodesTK">
    <meta property="og:title" content="How to Redeem: Borderlands 3 - ShiftCodesTK">
    <meta property="twitter:title" content="How to Redeem: Borderlands 3 - ShiftCodesTK">
    <!-- Page Description -->
    <meta name="description" content="How to redeem SHiFT Codes in Borderlands 3">
    <meta property="og:description" content="How to redeem SHiFT Codes in Borderlands 3">
    <meta property="twitter:description" content="How to redeem SHiFT Codes in Borderlands 3">
    <!-- Canonical Page Location -->
    <meta name="canonical" href="https://shiftcodestk.com/help/how-to-redeem/bl3">
    <meta property="og:url" content="https://shiftcodestk.com/how-to-redeem/bl3">
    <!-- Page Images -->
    <meta name="header:image" content="bl3_3">
    <meta property="og:image" content="https://shiftcodestk.com/assets/img/metadata/bl3/3.png">
    <meta property="twitter:image" content="https://shiftcodestk.com/assets/img/metadata/bl3/3.png">
    <!-- Page-Specific Browser Properties -->
    <link rel="manifest" href="/assets/manifests/bl3.webmanifest">
    <meta name="theme-color-tm" id="theme_color_tm" content="#ffb600">
    <!--// Shared Head Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="bl3">
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
      <div class="step 1">Either by heading to the&nbsp;<strong>Main Menu</strong>, or by opening the&nbsp;<strong>Start Menu</strong>&nbsp;in-game, navigate to the&nbsp;<em>Social</em>&nbsp;menu.</div>
      <div class="step 2">Switch to the&nbsp;<strong>SHiFT</strong>&nbsp;tab.</div>
      <div class="step 3">If you have not already registered and signed in to&nbsp;<strong>SHiFT</strong>, you will be prompted to do so.</div>
      <div class="step 4">Enter the 25-character SHiFT Code into the provided fields. Once you are finished, click&nbsp;<em>Submit</em>.</div>
      <div class="step 5">If successful, you will receive a confirmation message. The reward from the SHiFT Code will also appear in the&nbsp;<strong>Rewards History</strong>&nbsp;section.</div>
      <div class="step 6">If you are not already in-game, load into any character and return to the&nbsp;<strong>Social</strong>&nbsp;menu. Then, switch to the&nbsp;<strong>Mail</strong>&nbsp;tab. Select the&nbsp;<strong>rewards</strong>&nbsp;from your mailbox to claim them.</div>
      <div class="step 7">You can now make your way to the&nbsp;<strong>Golden Chest</strong>&nbsp;aboard&nbsp;<strong>Sanctuary</strong>&nbsp;to spend your Golden Keys. The Golden Chest can be found near the hallway leading to&nbsp;<strong>Marcus Munitions</strong>. Opening the chest will cost&nbsp;<strong>one Golden Key</strong>, and the rewards will be droppped at the&nbsp;<strong>level of your current character</strong>, regardless of playthrough.</div>
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
