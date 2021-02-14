<?php
  // $page['auth'] = [
  //   'requireState'   => 'auth',
  //   'onFailRedirect' => '/'
  // ];
  $page['meta'] = [
    'title'       => 'Development Hub - ShiftCodesTK',
    'description' => 'Resources related to the development of ShiftCodesTK',
    'canonical'   => '/dev/',
    'image'       => 'bl3/2',
    'theme'       => 'main'
  ];

  include_once('../initialize.php');
?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><?php include_once('global/head.php'); ?><body data-theme=main><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?>