<?php
  $page['meta'] = [
    'title'       => 'Borderlands: TPS - ShiftCodesTK',
    'description' => 'SHiFT Codes for Borderlands: The Pre-Sequel',
    'canonical'   => '/tps',
    'image'       => 'tps/1',
    'theme'       => 'tps'
  ];
  $page['shift'] = [
    'game'  => 'tps'
  ];

  include_once('initialize.php');
?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><?php include_once('global/head.php'); ?><body data-theme=tps><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><?php include_once('local/shift.php'); ?><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?>