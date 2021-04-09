<?php
  $dangerZoneForm = new FormBase([
    'name'               => 'logout',
    'action'             => '/api/post/account/danger-zone',
    'title'              => 'Danger Zone',
    'result'             => [
      'redirect' => [
        'enabled'  => true,
        'location' => 'reload'
      ]
    ]
  ]);
  $dangerZoneForm->addChild('field', [
    'name'     => 'user_id',
    'input'    => 'text',
    'required' => true,
    'readonly' => true,
    'hidden'   => true,
    'value'    => auth_user_id()
  ]);
  $triggers = $dangerZoneForm->addChild('section', [
    'name' => 'triggers'
  ]);
  $triggers->addChild('field', [
    'name'     => 'delete_account',
    'input'    => 'button',
    'label'    => 'Delete Account',
    'subtitle' => 'Delete your ShiftCodesTK account. This cannot be reversed.',
    'title'    => 'Delete your ShiftCodesTK account',
    // 'content'  => '<span class="box-icon fas fa-sign-out-alt"></span><span>Logout</span>',
    'content' => 'Delete Account',
    'customClasses' => [ 'color', 'danger' ]
  ]);
  $triggers->addChild('field', [
    'name'     => 'delete_everything',
    'input'    => 'button',
    'label'    => 'Delete Everything',
    'subtitle' => 'Delete your ShiftCodesTK account and all of your submitted SHiFT Codes. This cannot be reversed.',
    'title'    => 'Delete your ShiftCodesTK account and all of your submitted SHiFT Codes',
    // 'content'  => '<span class="box-icon fas fa-sign-out-alt"></span><span>Logout</span>',
    'content' => 'Delete Everything',
    'customClasses' => [ 'color', 'danger' ]
  ]);

  // $confirmation = $dangerZoneForm->addChild('section', [
  //   'name' => 'confirmation'
  // ]);
  // $confirmation->addChild('field', [
  //   'name'     => 'password',
  //   'input'    => 'password',
  //   'label'    => 'Confirm Password',
  // ]);
?>