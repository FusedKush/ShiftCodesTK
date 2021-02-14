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
?><!doctype html><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><style>main,main *{opacity:1}</style><?php include_once('global/head.php'); ?><body data-theme=main><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><main class=content-wrapper><?php
        require_once(PRIVATE_PATHS['forms'] . 'shift-code.php');

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
      ?></main><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?>