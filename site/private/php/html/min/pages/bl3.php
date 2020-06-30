<?php
  $page['meta'] = [
    'title'       => 'Borderlands 3 - ShiftCodesTK',
    'description' => 'SHiFT Codes for Borderlands 3',
    'canonical'   => '/bl3',
    'image'       => 'bl3/1',
    'theme'       => 'bl3'
  ];
  $page['shift'] = [
    'game'  => 'bl3'
  ];

  require_once('initialize.php');
?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/css/local/shift.css<?php echo TK_VERSION_STR; ?>"rel=stylesheet><?php include_once('global/head.php'); ?><body data-theme=bl3><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><?php include_once('local/shift.php'); ?><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?><script async src="/assets/js/global/libs/moment.js<?php echo TK_VERSION_STR; ?>"></script><script async src="/assets/js/local/shift.js<?php echo TK_VERSION_STR; ?>"></script>