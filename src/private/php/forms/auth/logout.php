<?php
  use ShiftCodesTK\Users\CurrentUser;

  $form_authLogout = new \FormBase([
    'properties'     => [
      'name'            => 'auth_logout_form',
      'templates'       => [ 'SINGLE_BUTTON' ]
    ],
    'formProperties' => [
      'action'          => [
        'path'             => '/assets/requests/post/auth/logout'
      ]
    ],
    'formResult'     => [
      'redirect'        => [
        'enabled'          => true,
        'location'         => ''
      ]
    ]
  ]);
  // User ID
  $form_authLogout->addChild('field', [
    'properties' => [
      'name'        => 'user_id',
      'hidden'      => true
    ],
    'inputProperties' => [
      'type'             => 'text',
      'value'            => CurrentUser::get_current_user()->user_id,
      'validations'      => [
        'required'          => true,
        'readonly'          => true
      ]
    ]
  ]);
  // Logout Button
  $form_authLogout->addChild('button', [
    'properties' => [
      'name'        => 'logout',
      'customHTML'  => [
        'classes'      => [
          'choice'
        ]
      ]
    ],
    'inputProperties' => [
      'type'             => 'submit',
      'content'          => '<span class="box-icon fas fa-sign-out-alt" aria-hidden="true"></span><span class="label">Logout</span>',
      'title'            => 'Logout of your ShiftCodesTK Account',
      'tooltip'          => [
        'content'           => 'Logout of your ShiftCodesTK Account',
        'pos'               => 'left'
      ]
    ]
  ]);
?>