<?php
  require_once(dirname(__DIR__) . '/initialize.php');
  
  use \ShiftCodesTK\Strings,
			\ShiftCodesTK\PageConfiguration;
  use const \ShiftCodesTK\VERSION_QUERY_STR;
  
	(new PageConfiguration('account/login'))
    ->setTitle('Account Login')
		->setGeneralInfo(
			'Login to your ShiftCodesTK Account',
			'bl2/6',
      (function () {
        $continue = $_GET[\ShiftCodesTK\ROUTER_REDIRECT_BACKLINK_PARAMETER] ?? null;
        
        if (isset($continue)) {
          $game_string = Strings\escape_reg(
            implode(
              '|',
              array_keys(\ShiftCodes::GAME_SUPPORT)
            ),
            '/'
          );
          
          if ($theme_color = Strings\preg_match($continue, "/{$game_string}/", Strings\PREG_RETURN_FULL_MATCH)) {
            return $theme_color;
          }
        }
        
        return 'main';
      })()
		)
		->setUserLoginCondition(false)
		->saveConfiguration();

  // Remove duplicate toasts
  if (getSessionToast('logout_toast')) {
    removeSessionToast('auth_state_mismatch_toast');
  }
  // Page theme color
//  (function () {
//    $theme = 'main';
//    $param = $_GET['continue'] ?? false;
//
//    if ($param) {
//      $gamesString = implode('|', array_keys(SHIFT_GAMES));
//      $match = [];
//
//      preg_match("/{$gamesString}/", $param, $match);
//
//      if ($match) {
//        $theme = $match[0];
//      }
//
//    }
//
//    define('PAGE_THEME_COLOR', $theme);
//  })();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/shared-styles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/css/local/login.css<?= VERSION_QUERY_STR; ?>" rel="stylesheet"></link>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/before-content.php'); ?>
    <!-- Main Content -->
    <main class="no-header" data-webp='{"path": "/assets/img/banners/bl2/6", "alt": ".jpg", "type": "bg"}'>
      <?php
        require_once(\ShiftCodesTK\Paths\PHP_PATHS['forms'] . '/auth/login.php');
        $form_authLogin->insertForm();
      ?>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('global/after-content.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/shared-scripts.php'); ?>
  </body>
</html>
