<?php
  include_once('initialize.php');

  use ShiftCodesTK\PageConfiguration;

  (new PageConfiguration('bl2'))
    ->setTitle('Borderlands 2')
    ->setGeneralInfo(
      'SHiFT Codes for Borderlands 2',
      'bl2/1',
      'bl2'
    )
    ->setShiftConfiguration(
      new \ShiftCodesTK\PageConfiguration\ShiftConfiguration([
        'game' => 'bl2'
      ]))
    ->saveConfiguration();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/shared-styles.php'); ?>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/before-content.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <!-- SHiFT -->
    <?php include_once('local/shift.php'); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('global/after-content.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/shared-scripts.php'); ?>
  </body>
</html>
