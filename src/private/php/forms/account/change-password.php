<?php
  $form_changePassword = new FormBase([
    'properties'     => [
      'name'            => 'auth_change_password_form'
    ],
    // 'content'        => [
    //   'title'           => 'Change your Password',
    //   'subtitle'        => 'Change the password you use to login to ShiftCodesTK',
    //   'description'     => '<em>You will be signed out of your account on all devices, and will need to login again.</em>'
    // ],
    'formProperties' => [
      'action'          => [
        'path'             => '/api/post/account/change-password',
      ],
      'spacing'         => 'vertical'
    ],
    'formFooter'   => [
      'actions'       => [
        'reset' => [
          'enabled'        => true,
          'content'        => 'Cancel',
          'requiresModify' => false,
          'tooltip'        => [
            'content'         => false
          ],
          'classes'        => [ 'view-toggle' ],
          'attributes'     => [ 
            'data-view' => 'update_password_view' 
          ] 
        ],
        'submit' => [
          'content'        => 'Change Password',
          'tooltip'        => [
            'content'         => 'Save and update your account password'
          ],
          'confirmation'   => [
            'required'        => true,
            'title'           => 'Change your Password',
            'body'            => "<p>Are you sure you want to change your password</p>
                                  <br>
                                  <div><em>You will be signed out of your account on all devices, and will need to login again.</em></div>",
            'actions'         => [
              'deny'             => [
                'name'              => 'Cancel',
                'tooltip'           => false
              ],
              'approve'          => [
                'name'              => 'Change your Password',
                'tooltip'           => 'Save and update your account password',
                'color'             => 'warning'
              ]
            ]
          ]
        ]
      ]
    ]
  ]);
  (function () use (&$form_changePassword) {
    $form_changePassword->addChild('field', [
      'properties'      => [
        'name'             => 'current_pw'
      ],
      'content'         => [
        'title'            => 'Current Password',
        'subtitle'         => 'The password you <em>currently</em> use to login.'
      ],
      'inputProperties' => [
        'type'             => 'password',
        'autocomplete'     => 'current-password',
        'validations'      => [
          'required'          => true
        ]
      ]
    ]);   
    $form_changePassword->addChild('field', [
      'properties'      => [
        'name'             => 'new_pw'
      ],
      'content'         => [
        'title'            => 'New Password',
        'subtitle'         => "The new password you will use to login",
        'description'      => [
          'Your new password must be at least 8 characters long.'
        ]
      ],
      'inputProperties' => [
        'type'             => 'password',
        'autocomplete'     => 'new-password',
        'validations'      => [
          'required'          => true,
          'validations'       => [
            'range'              => [
              'min'                 => 8,
              'max'                 => 64
            ]
          ]
        ]
      ]
    ]);   
    $form_changePassword->addChild('field', [
      'properties'      => [
        'name'             => 'confirm_pw',
      ],
      'content'         => [
        'title'            => 'Confirm Password'
      ],
      'inputProperties' => [
        'type'             => 'password',
        'autocomplete'     => 'new-password',
        'validations'      => [
          'required'          => true
        ]
      ]
    ]); 
  })();
?>