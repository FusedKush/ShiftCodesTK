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
  ?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><?php include_once('global/head.php'); ?><body data-theme=bl1><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><?php include_once('local/shift.php'); ?><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?>