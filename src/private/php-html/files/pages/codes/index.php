<?php
  include_once(dirname(__DIR__) . '/initialize.php');

  use ShiftCodesTK\PageConfiguration,
      ShiftCodesTK\PageConfiguration\ShiftConfiguration,
      ShiftCodesTK\Users\CurrentUser;
  

  (function () {
    $page_configuration = (new PageConfiguration('codes/index'))
      ->setTitle('My SHiFT Codes')
      ->setGeneralInfo(
        'SHiFT Codes you have submitted to ShiftCodesTK',
        'bl3/2'
      )
      ->setShiftConfiguration(
        new ShiftConfiguration([
          'game'  => null,
          'owner' =>  CurrentUser::is_logged_in()
                      ? CurrentUser::get_current_user()->user_id
                      : null,
          'order' => ShiftConfiguration::ORDER_NEWEST,
          'status' => [
            ShiftConfiguration::STATUS_ACTIVE,
            ShiftConfiguration::STATUS_EXPIRED,
            ShiftConfiguration::STATUS_HIDDEN,
          ]
        ], [
          'limit'
        ])
      );
    $page_configuration->setUserLoginCondition(true, true)
      ->setFailureToast([
        'content' => [
          'body' => 'You must be logged in to view your submitted SHiFT Codes.'
        ]
      ]);
    $page_configuration->saveConfiguration();
  })();
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
  <body data-theme="main">
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
