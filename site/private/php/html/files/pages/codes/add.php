<?php
  $page = [
    'auth' => [
      'requireState' => 'auth',
      'onFailToast' => [
        'content' => [
          'body' => 'You must be logged in to add a SHiFT Code.'
        ]
      ]
    ],
    'meta' => [
      'title'       => 'Add a SHiFT Code - ShiftCodesTK',
      'description' => 'Add a SHiFT Code to ShiftCodesTK',
      'canonical'   => '/codes/add',
      'image'       => 'tps/7',
      'theme'       => 'main'
    ]
  ];

  require_once('../initialize.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <style>
      main, main * { opacity: 1; }
    </style>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="main">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <main class="content-wrapper">
      <?php
        require_once(FORMS_PATH . 'shift/shift-code.php');

        if (isset($_GET['game'])) {
          $cleanParam = check_parameter('game', $_GET['game'], $shiftCodeForm['game_id']);

          if ($cleanParam['valid']) {
            $shiftCodeForm['game_id']->updateProperty('value', $cleanParam['value']);
          }
        }

        $shiftCodeForm['base']->insertForm();
      ?>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
  </body>
</html>
