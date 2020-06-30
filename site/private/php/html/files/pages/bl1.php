<?php
  $page['meta'] = [
    'title'       => 'Borderlands: GOTY - ShiftCodesTK',
    'description' => 'SHiFT Codes for Borderlands: Game of the Year Edition',
    'canonical'   => '/bl1',
    'image'       => 'bl1/1',
    'theme'       => 'bl1'
  ];
  $page['shift'] = [
    'game'  => 'bl1'
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
    <!-- Local Styles -->
    <link href="/assets/css/local/shift.css<?php echo TK_VERSION_STR; ?>" rel="stylesheet"></link>
    <!--// Metadata \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="bl1">
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
    <!-- Local Scripts -->
    <script async src="/assets/js/global/libs/moment.js<?php echo TK_VERSION_STR; ?>"></script>
    <script async src="/assets/js/local/shift.js<?php echo TK_VERSION_STR; ?>"></script>
  </body>
</html>
