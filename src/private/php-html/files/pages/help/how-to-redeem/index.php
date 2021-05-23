<?php
  require_once(dirname(__DIR__, 2) . '/initialize.php');

  use ShiftCodesTK\PageConfiguration;

  (new PageConfiguration('help/how-to-redeem/index'))
    ->setTitle('How to Redeem SHiFT Codes')
    ->setGeneralInfo(
      'How to redeem SHiFT Codes in Borderlands',
      'tps/4'
    )
    ->saveConfiguration();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/shared-styles.php'); ?>
    <!-- Local Styles -->
    <link rel="stylesheet" href="/assets/css/local/help/how-to-redeem/index.css<?= \ShiftCodesTK\VERSION_QUERY_STR; ?>">
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/before-content.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <main class="content-wrapper">
      <p class="intro">Unsure of what to do with the SHiFT Codes once you've found them? No problem! Choose one of the guides below for step-by-step instructions on redeeming SHiFT Codes and getting at that sweet loot!</p>
      <div class="links">
        <div class="title">Redeem In-Game</div>
        <a class="button color-light" href="bl3" title="How to Redeem SHiFT Codes in Borderlands 3" aria-label="How to Redeem SHiFT Codes in Borderlands 3">Borderlands 3</a>
        <a class="button color-light" href="bl1" title="How to Redeem SHiFT Codes in Borderlands: Game of the Year Edition" aria-label="How to Redeem SHiFT Codes in Borderlands: Game of the Year Edition">Borderlands: GOTY</a>
        <a class="button color-light" href="bl2" title="How to Redeem SHiFT Codes in Borderlands 2" aria-label="How to Redeem SHiFT Codes in Borderlands 2">Borderlands 2</a>
        <a class="button color-light" href="tps" title="How to Redeem SHiFT Codes in Borderlands: The Pre-Sequel" aria-label="How to Redeem SHiFT Codes in Borderlands: The Pre-Sequel">Borderlands: TPS</a>
      </div>
      <div class="links">
        <div class="title">Redeem Online</div>
        <a class="button color-light" href="borderlands-website" title="How to Redeem SHiFT Codes on the Borderlands website" aria-label="How to Redeem SHiFT Codes on the Borderlands website">Borderlands Website</a>
        <a class="button color-light" href="shift-website" title="How to Redeem SHiFT Codes on the SHiFT website" aria-label="How to Redeem SHiFT Codes on the SHiFT website">SHiFT Website</a>
      </div>
    </main>
    <!-- Support Footer -->
    <?php include_once('local/support-footer.php'); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('global/after-content.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/shared-scripts.php'); ?>
  </body>
</html>
