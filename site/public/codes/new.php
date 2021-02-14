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
?><!doctype html><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><style>main,main *{opacity:1}</style><?php include_once('global/head.php'); ?><?php
    $bodyTheme = isset($_GET['game']) 
                   && array_search($_GET['game'], array_keys(SHIFT_GAMES)) !== false
                 ? $_GET['game']
                 : 'main';
  ?><body data-theme="<?= $bodyTheme; ?>"><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><main class=content-wrapper><?php
        include(PRIVATE_PATHS['forms'] . 'shift/shift-code.php');

        $form_shiftCode = getShiftCodeForm('add');
        $form_shiftCode->insertForm();
      ?></main><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?><script async src="/assets/js/local/codes/new.js<?php echo TK_VERSION_STR; ?>"></script>