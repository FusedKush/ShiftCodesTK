<?php
  $form_authLogin = new FormBase([
    'properties'       => [
      'name'              => 'auth_login_form'
    ],
    'content'          => [
      'title'             => 'Login',
      'subtitle'          => 'If you have an account for ShiftCodesTK, you can log in here.'
    ],
    'formProperties'   => [
      'action'            => [
        'path'               => '/assets/requests/post/auth/login'
      ],
      'spacing'           => 'standard'
    ],
    'formFooter'       => [
      'actions'           => [
        'submit'             => [
          'content'             => 'Login',
          'tooltip'               => [
            'content'                => 'Login to your ShiftCodesTK Account'
          ]
        ]
      ]
    ],
    'formResult'       => [
      'redirect'          => [
        'enabled'            => true,
        'location'           => '/',
        'useQueryParam'      => true
      ]
    ]
  ]);
  // Email Address
  $form_authLogin->addChild('field', [
    'properties'     => [
      'name'            => 'email'
    ],
    'content'        => [
      'title'           => 'Email Address',
      'innerTitle'      => true
    ],
    'inputProperties' => [
      'type'            => 'email',
      'autocomplete'    => 'email',
      'validations'     => [
        'required'         => true
      ]
    ]
  ]);
  // Password
  $form_authLogin->addChild('field', [
    'properties'     => [
      'name'            => 'password'
    ],
    'content'        => [
      'title'           => 'Password',
      'innerTitle'      => true
    ],
    'inputProperties' => [
      'type'            => 'password',
      'autocomplete'    => 'current-password',
      'toolbar'         => [
        'passwordVisibilityToggle' => true
      ],
      'validations'     => [
        'required'         => true
      ]
    ]
  ]);
  // Remember Me
  $form_authLogin->addChild('field', [
    'properties'     => [
      'name'            => 'remember_me'
    ],
    'content'        => [
      'title'           => 'Stay Logged In',
      'subtitle'        => 'Stayed signed in on <em>this device</em>.'
    ],
    'inputProperties' => [
      'type'            => 'toggle-button',
      'value'           => 'Remember Me'
    ]
  ]);
?>