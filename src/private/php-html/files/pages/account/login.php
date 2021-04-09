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
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/css/local/login.css<?= \ShiftCodesTK\VERSION_QUERY_STR; ?>" rel="stylesheet"></link>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="<?= PAGE_THEME_COLOR; ?>">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Content -->
    <main class="no-header" data-webp='{"path": "/assets/img/banners/bl2/6", "alt": ".jpg", "type": "bg"}'>
      <?php
        require_once(\ShiftCodesTK\Paths\PHP_PATHS['forms'] . '/auth/login.php');
        $form_authLogin->insertForm();
      ?>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
  </body>
</html>
