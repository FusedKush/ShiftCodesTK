<?php
  $updatePasswordForm = new FormBase([
    'name'               => 'update_password',
    // 'title'              => 'Update Password',
    'action'             => '/assets/requests/post/account/change-password',
    // 'showFieldWalls'     => true,
    'footer'             => [
      'reset' => [
        'use'   => true,
        'name'  => 'Cancel',
        'label' => 'Leave your current password unchanged',
        'class' => [ 'view-toggle' ],
        'attr'  => [ 
          [ 'data-view' => 'update_password_view' ] 
        ] 
      ],
      'submit' => [
        'name'  => 'Change Password',
        'label' => 'Save and update your account password'
      ]
    ]
  ]);
  (function () use (&$updatePasswordForm) {
    $updatePasswordForm->addChild('field', [
      'name'        => 'current_password',
      'label'       => 'Current Password',
      'input'       => 'password',
      'required'    => true
    ]);
    $updatePasswordForm->addChild('field', [
      'name'        => 'new_password',
      'label'       => 'New Password',
      'description' => [
        'Your new password must be at least six characters long.'
      ],
      'input'       => 'password',
      'required'    => true
    ]);
    $updatePasswordForm->addChild('field', [
      'name'        => 'new_password_confirm',
      'label'       => 'Confirm New Password',
      'input'       => 'password',
      'required'    => true
    ]);
  })();
?>