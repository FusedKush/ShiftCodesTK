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
?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/css/local/shift.css<?php echo TK_VERSION_STR; ?>"rel=stylesheet><?php include_once('global/head.php'); ?><body data-theme=bl2><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><?php include_once('local/shift.php'); ?><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?><script async src="/assets/js/local/shift.js<?php echo TK_VERSION_STR; ?>"></script>