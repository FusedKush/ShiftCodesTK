<?php
  $page = [
    'auth' => [
      'requireState' => 'auth',
      'onFailToast' => [
        'content' => [
          'body' => 'You must be logged in to submit a SHiFT Code.'
        ]
      ]
    ],
    'meta' => [
      'title'       => 'Submit a SHiFT Code - ShiftCodesTK',
      'description' => 'Submit a SHiFT Code to ShiftCodesTK',
      'canonical'   => '/codes/new',
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
  <?php
    $bodyTheme = isset($_GET['game']) 
                   && array_search($_GET['game'], array_keys(SHIFT_GAMES)) !== false
                 ? $_GET['game']
                 : 'main';
  ?>
  <body data-theme="<?= $bodyTheme; ?>">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <main class="content-wrapper">
      <?php
        include(\ShiftCodesTK\PRIVATE_PATHS['forms'] . '/shift/shift-code.php');

        $form_shiftCode = getShiftCodeForm('add');
        $form_shiftCode->insertForm();
      ?>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/js/local/codes/new.js<?php echo TK_VERSION_STR; ?>"></script>
  </body>
</html>
