<?php
  $page['meta'] = [
    'title'       => 'Borderlands 2 - ShiftCodesTK',
    'description' => 'SHiFT Codes for Borderlands 2',
    'canonical'   => '/bl2',
    'image'       => 'bl2/1',
    'theme'       => 'bl2'
  ];
  $page['shift'] = [
    'game'  => 'bl2'
  ];

  include_once('initialize.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="bl2">
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
