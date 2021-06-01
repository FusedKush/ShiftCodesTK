<?php
  require_once('initialize.php');
  
  use ShiftCodesTK\PageConfiguration;

  (new PageConfiguration('bl1'))
    ->setTitle('Borderlands: GOTY')
    ->setGeneralInfo(
      'SHiFT Codes for Borderlands: Game of the Year Edition',
      'bl1/1',
      'bl1'
    )
		->setShiftConfiguration(
      new \ShiftCodesTK\PageConfiguration\ShiftConfiguration([
        'game' => 'bl1'
      ]))
		->saveConfiguration();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">

    <?php include('global/shared-styles.php'); ?>
    <?php include('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
    <?php include('global/before-content.php'); ?>
    <?php include('global/main-header.php'); ?>

    <?php include('local/shift.php'); ?>

    <?php include('global/after-content.php'); ?>
    <?php include('global/shared-scripts.php'); ?>
  </body>
</html>
