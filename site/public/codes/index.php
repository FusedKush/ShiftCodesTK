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
?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><?php include_once('global/head.php'); ?><body data-theme=main><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><?php include_once('local/shift.php'); ?><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?>