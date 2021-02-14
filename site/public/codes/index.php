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
?><!doctype html><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><?php include_once('global/head.php'); ?><body data-theme=main><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><?php include_once('local/shift.php'); ?><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?>