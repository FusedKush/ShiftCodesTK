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
    <?php include_once('global/sharedStyles.php'); ?>
    <!--// Metadata \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <!-- SHiFT -->
    <?php include_once('local/shift.php'); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
  </body>
</html>
