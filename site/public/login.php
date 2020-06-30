<?php
  $page = [
    'auth' => [
      'requireState' => 'no-auth',
    ],
    'meta' => [
      'title'       => 'Login - ShiftCodesTK',
      'description' => 'Login to ShiftCodesTK',
      'canonical'   => '/login',
      'image'       => 'bl1/3',
      'theme'       => 'main'
    ]
  ];

  
  require_once('initialize.php');
?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/css/local/login.css<?= TK_VERSION_STR; ?>"rel=stylesheet><?php include_once('global/head.php'); ?><body data-theme=main><?php include_once('global/beforeContent.php'); ?><main class=no-header data-webp='{"path": "/assets/img/banners/bl2/6", "alt": ".jpg", "type": "bg"}'><?php
        require_once(FORMS_PATH . 'auth/login.php');
        $form_authLogin->insertForm();
      ?></main><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?>