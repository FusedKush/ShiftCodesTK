<?php
  $page['auth'] = [
    'requireState'   => 'auth',
    'onFailRedirect' => '/'
  ];
  $page['meta'] = [
    'title'       => 'My SHiFT Codes - ShiftCodesTK',
    'description' => 'SHiFT Codes you have submitted to ShiftCodesTK',
    'canonical'   => '/codes/',
    'image'       => 'bl3/2',
    'theme'       => 'main'
  ];
  $page['shift'] = [
    'game'   => 'all',
    'owner'  => '$user',
    'order'  => 'newest',
    'filter' => [ 'active', 'expired', 'hidden' ]
  ];

  include_once('../initialize.php');
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
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="main">
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
    <script async src="/assets/js/global/libs/moment.js<?= TK_VERSION_STR; ?>"></script>
    <script async src="/assets/js/local/shift.js<?= TK_VERSION_STR; ?>"></script>
  </body>
</html>
