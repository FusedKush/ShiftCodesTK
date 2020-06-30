<?php
  $form_updateProfile = new FormBase([
    'properties'     => [
      'name'            => 'update_profile'
    ],
    'formProperties' => [
      'action'          => '/assets/requests/post/account/update-profile',
      'spacing'         => 'vertical'
    ],
    'formFooter'     => [
      'actions'         => [
        'reset'            => [
          'enabled'           => true,
          'content'           => 'Discard',
          'title'             => 'Discard any changes made to your profile',
          'classes'           => [ 'view-toggle' ]
        ],
        'detailsToggle'    => [
          'enabled'           => true,
          'hideByDefault'     => true,
        ],
        'submit'            => [
          'enabled'           => true,
          'content'           => 'Update Profile',
          'title'             => 'Save all changes made to your profile'
        ]
      ]
    ]
  ]);

  (function () use (&$form_updateProfile) {
    // Info Section
    $info = $form_updateProfile->addChild('section', [
      'properties' => [
        'name'        => 'info'
      ],
      'content'    => [
        'title'       => 'Profile Details',
        'subtitle'    => 'Public information about your profile'
      ]
    ]);
    // Username Field
    $info->addChild('field', [
      'properties'      => [
        'name'             => 'username',
        // 'size'             => 'half'
      ],
      'content'         => [
        'title'            => 'Username',
        'subtitle'         => 'Your display name on the site',
        'description'      => [
          'Your Username is displayed on your Profile and on any SHiFT Codes you have submitted.',
          'Your Username must be unique.'
        ]
      ],
      'inputProperties' => [
        'type'             => 'text',
        'value'            => auth_user_name(),
        'validations'      => [
          'required'          => true,
          'validations'       => [
            'range'              => [
              'min'                 => 3,
              'max'                 => 20,
            ]
          ]
        ]
      ]
    ]);
    // User ID Field
    $info->addChild('field', [
      'properties' => [
        'name'        => 'user_id',
        // 'size'        => 'half'
      ],
      'content'    => [
        'title'       => 'User ID',
        'subtitle'    => 'Your unique User ID',
        'description' => [
          'Your User ID is used to identify your account, even when you change your username.',
          'Your User ID cannot be changed.'
        ]
      ],
      'inputProperties' => [
        'type'             => 'text',
        'value'            => auth_user_id(),
        'validations'      => [
          'required'          => true,
          'readonly'          => true,
          'validations'       => [
            'range'              => [
              'is'                  => 12,
            ]
          ]
        ]
      ]
    ]);

    // Roles Section
    $roles = $form_updateProfile->addChild('section', [
      'properties' => [
        'name'        => 'roles',
        'customHTML'  => [
          'classes'      => [
            'details'
          ]
        ]
      ],
      'content'    => [
        'title'       => 'Roles',
        'subtitle'    => 'Roles are additional permissions granted to your account. They are visible to all users.'
      ]
    ]);
    // Roles 
    $roles->addChild('field', [
      'properties' => [
        'name'        => 'roles'
      ],
      'content'    => [
        'title'       => 'Available Roles',
        // 'subtitle'    => 'Roles are additional permissions granted to your account. They are visible to all users.'
      ],
      'inputProperties' => [
        'type'             => 'toggle-box',
        'value'            => (function () {
          $value = [];
          $userRoles = auth_user_roles();

          foreach ($userRoles as $role => $hasRole) {
            if ($hasRole) {
              $value[] = $role;
            }
          }

          return implode(", ", $value);
        })(),
        'options'          => (function () {
          $options = [];
          
          foreach (AUTH_ROLES['props'] as $role => $properties) {
            $options[$role] = $properties['name'];
          }

          return $options;
        })(),
        'validations'      => [
          'readonly'          => true
        ]
      ]
    ]);
    // Profile Stats
    $stats = $form_updateProfile->addChild('section', [
      'properties' => [
        'name'        => 'profile_stats'
      ],
      'content'    => [
        'title'       => 'Profile Stats',
        'subtitle'    => 'Statistics related to your account activity'
      ]
    ]);
    // Privacy Settings
    $stats->addChild('field', [
      'properties' => [
        'name'        => 'privacy'
      ],
      'content'    => [
        'title'       => 'Show on Public Profile',
        'subtitle'    => 'Indicates if your Profile Stats should be visible to other users, or just you.'
      ],
      'inputProperties' => [
        'type'             => 'toggle-button',
        'placeholder'      => 'Show Stats',
        'value'            => 'on',
        'validations'      => [
          'required'          => true
        ]
      ]
    ]);
  })();
?>