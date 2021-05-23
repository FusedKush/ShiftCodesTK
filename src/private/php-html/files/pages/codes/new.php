<?php
  require_once(dirname(__DIR__) . '/initialize.php');

  use ShiftCodesTK\PageConfiguration;

  (new PageConfiguration('codes/new'))
    ->setTitle('Submit a SHiFT Code')
    ->setGeneralInfo(
      'Submit a SHiFT Code to ShiftCodesTK',
      'tps/7'
    )
    ->setUserLoginCondition(true)
    ->saveConfiguration();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/shared-styles.php'); ?>
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
    <?php include_once('global/before-content.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <main class="content-wrapper">
      <?php
        include(\ShiftCodesTK\Paths\PHP_PATHS['forms'] . '/shift/shift-code.php');

        $form_shiftCode = getShiftCodeForm('add');
        $form_shiftCode->insertForm();
      ?>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('global/after-content.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/shared-scripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/js/local/codes/new.js<?php echo \ShiftCodesTK\VERSION_QUERY_STR; ?>"></script>
  </body>
</html>
