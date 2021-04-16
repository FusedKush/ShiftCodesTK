<?php
  include_once('initialize.php');
  
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

    <?php include_once('global/sharedStyles.php'); ?>
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
    <?php include_once('global/beforeContent.php'); ?>
    <?php include_once('global/main-header.php'); ?>

    <?php include_once('local/shift.php'); ?>

    <?php include_once('global/afterContent.php'); ?>
    <?php include_once('global/sharedScripts.php'); ?>
  </body>
</html>
