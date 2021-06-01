<?php
  require_once('initialize.php');
  
  use ShiftCodesTK\PageConfiguration;
  
  (new PageConfiguration('bl3'))
    ->setTitle('Borderlands 3')
    ->setGeneralInfo(
      'SHiFT Codes for Borderlands 3',
      'bl3/1',
      'bl3'
    )
    ->setShiftConfiguration(
      new \ShiftCodesTK\PageConfiguration\ShiftConfiguration([
        'game' => 'bl3'
      ])
    )
    ->saveConfiguration();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include('global/shared-styles.php'); ?>
    <!--// Metadata \\-->
    <?php include('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
    <!--// Before-Content Imports \\-->
    <?php include('global/before-content.php'); ?>
    <!-- Main Header -->
    <?php include('global/main-header.php'); ?>
    <!-- Main Content -->
    <!-- SHiFT -->
    <?php include('local/shift.php'); ?>
    <!--// After-Content Imports \\-->
    <?php include('global/after-content.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include('global/shared-scripts.php'); ?>
  </body>
</html>
