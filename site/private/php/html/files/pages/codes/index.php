<?php
  $page['auth'] = [
    'requireState'   => 'auth',
    // 'onFailRedirect' => '/'
    'onFailToast' => [
      'content' => [
        'body' => 'You must be logged in to view your submitted SHiFT Codes.'
      ]
    ]
  ];
  $page['meta'] = [
    'title'       => 'My SHiFT Codes - ShiftCodesTK',
    'description' => 'SHiFT Codes you have submitted to ShiftCodesTK',
    'canonical'   => '/codes/',
    'image'       => 'bl3/2',
    'theme'       => 'main'
  ];
  $page['shift'] = [
    'game'               => null,
    'owner'              => '$user',
    'order'              => 'newest',
    'status'             => [ 'active', 'expired', 'hidden' ],
    'readOnlyProperties' => [ 'limit' ]
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
  </body>
</html>
