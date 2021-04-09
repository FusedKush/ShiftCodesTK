<?php
  $page = [
    'auth' => [
      'requireState' => 'auth',
      'onFailToast' => [
        'content' => [
          'body' => 'You must be logged in to edit a SHiFT Code.'
        ]
      ]
    ],
    'meta' => [
      'title'       => 'Edit a SHiFT Code - ShiftCodesTK',
      'description' => 'Edit a SHiFT Code to ShiftCodesTK',
      'canonical'   => '/codes/add',
      'image'       => 'tps/7',
      'theme'       => 'main'
    ]
  ];

  require_once('../initialize.php');

  if (!isset($_GET['code'])) {
    response_redirect('/codes/');
  }
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
        require_once(\ShiftCodesTK\Paths\PHP_PATHS['forms'] . '/shift-code.php');

        $cleanParam = check_parameter('code', $_GET['code'], $shiftCodeForm['code_id']);

        if ($cleanParam['valid']) {
          $cols = ['reward', 'game_id', 'source', 'release_date', 'expiration_date', 'timezone', 'code_pc', 'code_xbox', 'code_ps'];
          $colStr = preg_replace('/, $/', '', implode(', ', $cols));
          $query = "SELECT $colStr
                    FROM shift_codes
                    WHERE code_id = '{$cleanParam['value']}'
                    LIMIT 1";

          $shiftCodeForm['code_id']->updateProperty('hidden', false);
          $shiftCodeForm['code_id']->updateProperty('required', true);
          $shiftCodeForm['code_id']->updateProperty('value', $cleanParam['value']);
          $sql = $_mysqli->query($query, [ 'collapseAll' => true ]);

          if ($sql) {
            foreach ($cols as $col) {
              if ($col != 'expiration_date' && $col != 'timezone') {
                $shiftCodeForm[$col]->updateProperty('value', $sql[$col]);
              }
            }

            $shiftCodeForm['expiration_date']->updateProperty('value', $sql['expiration_date'] . ' ' . $sql['timezone']);
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
