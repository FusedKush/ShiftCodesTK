<?php
  $form_authLogout = new FormBase([
    'properties'     => [
      'name'            => 'auth_logout_form'
    ],
    'formProperties' => [
      'action'          => '/assets/requests/post/auth/logout',
      'showAlerts'      => false,
      'spacing'         => 'none'
    ],
    'formResult'     => [
      'redirect'        => [
        'enabled'          => true,
        'location'         => ''
      ]
    ],
    'formFooter'     => [
      'enabled'         => false
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
      'value'            => auth_user_id(),
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
      'tooltip'          => [
        'content'           => 'Logout of your ShiftCodesTK Account',
        'pos'               => 'left'
      ]
    ]
  ]);
?>