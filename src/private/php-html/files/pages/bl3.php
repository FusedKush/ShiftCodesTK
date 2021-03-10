<?php
  $page['meta'] = [
    'title'       => 'Borderlands 3 - ShiftCodesTK',
    'description' => 'SHiFT Codes for Borderlands 3',
    'canonical'   => '/bl3',
    'image'       => 'bl3/1',
    'theme'       => 'bl3'
  ];
  $page['shift'] = [
    'game'  => 'bl3'
  ];

  require_once('initialize.php');
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
  <body data-theme="bl3">
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
