<?php
  $page = [
    'auth' => [
      'requireState' => 'no-auth',
    ],
    'meta' => [
      'title'       => 'Login - ShiftCodesTK',
      'description' => 'Login to ShiftCodesTK',
      'canonical'   => '/account/login',
      'image'       => 'bl2/6',
      'theme'       => 'main'
    ]
  ];
  
  require_once('../initialize.php');

  // Remove duplicate toasts
  if (getSessionToast('logout_toast')) {
    removeSessionToast('auth_state_mismatch_toast');
  }
  // Page theme color
  (function () {
    $theme = 'main';
    $param = $_GET['continue'] ?? false;
    
    if ($param) {
      $gamesString = implode('|', array_keys(SHIFT_GAMES));
      $match = [];

      preg_match("/{$gamesString}/", $param, $match);

      if ($match) {
        $theme = $match[0];
      }
      
    }
    
    define('PAGE_THEME_COLOR', $theme);
  })();
?><!doctype html><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/css/local/login.css<?= TK_VERSION_STR; ?>" rel=stylesheet><?php include_once('global/head.php'); ?><body data-theme="<?= PAGE_THEME_COLOR; ?>"><?php include_once('global/beforeContent.php'); ?><main class=no-header data-webp='{"path": "/assets/img/banners/bl2/6", "alt": ".jpg", "type": "bg"}'><?php
        require_once(PRIVATE_PATHS['forms'] . 'auth/login.php');
        $form_authLogin->insertForm();
      ?></main><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?>