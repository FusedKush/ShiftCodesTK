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
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/css/local/login.css<?= TK_VERSION_STR; ?>" rel="stylesheet"></link>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="main">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Content -->
    <main class="no-header" data-webp='{"path": "/assets/img/banners/bl2/6", "alt": ".jpg", "type": "bg"}'>
      <?php
        require_once(FORMS_PATH . 'auth/login.php');
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
