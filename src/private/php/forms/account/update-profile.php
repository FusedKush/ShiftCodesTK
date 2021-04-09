<?php
  use ShiftCodesTK\Users,
      ShiftCodesTK\Users\UserRecord;

  $primaryViewID = 'profile_card_template_card_view_primary';

  // Change Username
  $form_changeUsername = new FormBase([
    'properties'     => [
      'name'            => 'change_username'
    ],
    'formProperties' => [
      'action'          => [
        'path'             => '/api/post/account/change-username'
      ],
      'spacing'         => 'vertical'
    ],
    'formResult'     => [
      'toast'     => [
        'enabled'    => true,
        'method'     => 'response',
        'properties' => [
          'content'     => [
            'title'        => 'Username Changed',
            'body'         => 'Your username has been changed successfully.'
          ]
        ]
      ],
      'formState' => 'enabled'
    ],
    'formFooter'     => [
      'actions'         => [
        'reset'            => [
          'enabled'           => true,
          'content'           => 'Cancel',
          'requiresModify'    => false,
          'confirmation'      => [
            'required'           => false
          ],
          'classes'           => [ 'styled', 'view-toggle' ],
          'attributes'        => [
            'data-view'          => $primaryViewID
          ]
        ],
        'detailsToggle'    => [
          'enabled'           => false,
          // 'hideByDefault'     => true
        ],
        'submit'           => [
          'content'           => 'Change Username',
          'confirmation'      => [
            'required'           => false
          ],
        ]
      ]
    ]
  ]);

  (function () use (&$form_changeUsername, $primaryViewID) {
    // Username Field
    $form_changeUsername->addChild('field', [
      'properties'      => [
        'name'             => 'username'
      ],
      'content'         => [
        'title'            => 'Username',
        'subtitle'         => 'Your display name on the site',
        'description'      => [
          'Your Username can only contain Alphanumeric (<code>A-Z 0-9</code>) Characters and Underscores (<code>_</code>).',
          'Your Username is displayed on your Profile and on any SHiFT Codes you have submitted.',
          'Your Username must be unique.',
          'You can change your username <em>twice</em> every <em>24 hours</em>.'
        ]
      ],
      'inputProperties' => [
        'type'             => 'text',
        'value'            => Users\CurrentUser::is_logged_in()
                              ? Users\CurrentUser::get_current_user()->username
                              : '',
        'validations'      => Users\Constraints::USERNAME
      ]
    ]);
  })();

  // Role Details
  $form_roleDetails = new FormBase([
    'properties'     => [
      'name'            => 'role_details'
    ],
    'formProperties' => [
      'action'          => [
        'path'             => '/api/post/account/role-details',
        'type'             => 'js'
      ],
      'spacing'         => 'vertical'
    ],
    'formFooter'     => [
      'actions'         => [
        'reset'            => [
          'enabled'           => true,
          'content'           => 'Close',
          'requiresModify'    => false,
          'confirmation'      => [
            'required'           => false
          ],
          'classes'           => [ 'styled', 'view-toggle' ],
          'attributes'        => [
            'data-view'          => $primaryViewID
          ]
        ],
        'detailsToggle'    => [
          'enabled'           => false,
          // 'hideByDefault'     => true
        ],
        'submit'           => [
          'enabled'           => false
        ]
      ]
    ]
  ]);

  (function () use (&$form_roleDetails) {
    $roleDetails = [
      'badass' => [
        'name'        => 'Badass',
        'description' => 'As a site <em>Badass</em>, you are granted the following permissions:
                            <ul class="styled">
                            <li>You have access to User Moderation tools and options.</li>
                            <li>You can submit up to <em>six</em> SHiFT Codes per day.</li>
                          </ul>'
      ],
      'admin' => [
        'name'        => 'Admin',
        'description' => 'As a site <em>Admin</em>, you are granted the following permissions:
                          <ul class="styled">
                            <li>You have no limit on the number of SHiFT Codes you can submit per day.</li>
                            <li>You have access to SHiFT Code Moderation tools and options.</li>
                          </ul>'
      ],
      'developer' => [
        'name'        => 'Developer',
        'description' => 'As a site <em>Developer</em>, you are granted the following permissions:
                          <ul class="styled">
                            <li>You have access to Developer Tools, Insights, & Features.</li>
                          </ul>'
      ]
    ];
    $roleList = array_reverse(\ShiftCodesTK\Users\User::USER_ROLES, true);

    $form_roleDetails->addChild('field', [
      'properties'      => [
        'name'             => "roles"
      ],
      'content'         => [
        'title'            => 'Your Roles',
        'subtitle'         => 'The <em>User Roles</em> that have been assigned to you.',
        'description'      => (function () use ($roleList, $roleDetails) {
          $description = [];

          foreach ($roleList as $role => $hasRole) {
            if ($hasRole) {
              $description[$role] = $roleDetails[$role]['description'];
            }
          }

          return $description;
        })()
      ],
      'inputProperties' => [
        'type'             => 'checkbox',
        // 'value'            => (function () use ($roleList, $roleDetails) {
        //   $value = [];

        //   foreach ($roleList as $role => $hasRole) {
        //     if ($hasRole) {
        //       $value[] = $role;
        //     }
        //   }

        //   return implode(', ', $value);
        // })(),
        'options'          => (function () use ($roleList, $roleDetails) {
          $options = [];

          foreach ($roleList as $role => $roleInfo) {
            $options[$role] = $roleDetails[$role]['name'];
          }

          return $options;
        })(),
        'validations'      => [
          'required'          => true,
          'readonly'          => true
        ]
      ]
    ]);
    foreach (auth_user_roles() as $role => $hasRole) {
    }
  })();

  // Profile Stat Privacy
  $form_statPrivacy = new FormBase([
    'properties'     => [
      'name'            => 'profile_stats_privacy'
    ],
    'formProperties' => [
      'action'          => [
        'path'             => '/api/post/account/update-stats-privacy'
      ],
      'spacing'         => 'vertical'
    ],
    'formResult'     => [
      'toast'     => [
        'enabled'    => true,
        'method'     => 'response',
        'properties' => [
          'settings'    => [
            'duration'     => 'medium'
          ],
          'content'     => [
            'title'        => 'Privacy Settings Updated',
            'body'         => 'Your Profile Stats Privacy Settings have been saved.'
          ]
        ]
      ],
      'formState' => 'enabled'
    ],
    'formFooter'     => [
      'actions'         => [
        'reset'            => [
          'enabled'           => true,
          'content'           => 'Cancel',
          'requiresModify'    => false,
          'confirmation'      => [
            'required'           => false
          ],
          'classes'           => [ 'styled', 'view-toggle' ],
          'attributes'        => [
            'data-view'          => $primaryViewID
          ]
        ],
        'detailsToggle'    => [
          'enabled'           => false,
          // 'hideByDefault'     => true
        ],
        'submit'           => [
          'content'           => 'Update Privacy Settings',
          'confirmation'      => [
            'required'           => false
          ],
        ]
      ]
    ]
  ]);

  (function () use (&$form_statPrivacy) {
    $form_statPrivacy->addChild('field', [
      'properties' => [
        'name'        => 'privacy_preference'
      ],
      'content'    => [
        'title'       => 'Profile Stats Privacy',
        'subtitle'    => 'Determines who can see the Profile Statistics on your profile.',
        'description' => [
          UserRecord::PROFILE_STATS_HIDDEN  => 'Only you will be able to see your Profile Statistics.',
          UserRecord::PROFILE_STATS_PRIVATE => 'Only other users that are currently signed-in will be able to see your Profile Statistics.',
          UserRecord::PROFILE_STATS_PUBLIC  => 'Everyone will be able to see your Profile Statistics.'
        ]
      ],
      'inputProperties' => [
        'type'             => 'radio',
        'value'            => UserRecord::PROFILE_STATS_HIDDEN,
        'options'          => [
          UserRecord::PROFILE_STATS_HIDDEN  => 'Just Me',
          UserRecord::PROFILE_STATS_PRIVATE => 'Other Members',
          UserRecord::PROFILE_STATS_PUBLIC  => 'Everyone'
        ],
        'validations'      => [
          'required'          => true
        ]
      ]
    ]);
  })();
?>
<?php
  // $form_updateProfile = new FormBase([
  //   'properties'     => [
  //     'name'            => 'update_profile'
  //   ],
  //   'formProperties' => [
  //     'action'          => [
  //       'path'             => '/api/post/account/update-profile'
  //     ],
  //     'spacing'         => 'vertical'
  //   ],
  //   'formFooter'     => [
  //     'actions'         => [
  //       'reset'            => [
  //         'enabled'           => true,
  //         'content'           => 'Discard Changes',
  //         'requiresModify'    => false,
  //         'tooltip'           => [
  //           'content'            => 'Discard any changes made to your profile'
  //         ],
  //         'confirmation'      => [
  //           'required'           => false
  //         ],
  //         'classes'           => [ 'view-toggle' ]
  //       ],
  //       'detailsToggle'    => [
  //         'enabled'           => true,
  //         'hideByDefault'     => true
  //       ],
  //       'submit'           => [
  //         'content'           => 'Update Profile',
  //         'tooltip'           => [
  //           'content'            => 'Save all changes made to your profile'
  //         ],
  //         'confirmation'      => [
  //           'required'           => false
  //         ],
  //       ]
  //     ]
  //   ]
  // ]);

  // (function () use (&$form_updateProfile) {
  //   // Info Section
  //   $info = $form_updateProfile->addChild('section', [
  //     'properties' => [
  //       'name'        => 'info'
  //     ],
  //     'content'    => [
  //       'title'       => 'Profile Details',
  //       'subtitle'    => 'Public information about your profile'
  //     ]
  //   ]);
  //   // Username Field
  //   $info->addChild('field', [
  //     'properties'      => [
  //       'name'             => 'username',
  //       'size'             => 'two-thirds'
  //     ],
  //     'content'         => [
  //       'title'            => 'Username',
  //       'subtitle'         => 'Your display name on the site',
  //       'description'      => [
  //         'Your Username is displayed on your Profile and on any SHiFT Codes you have submitted.',
  //         'Your Username must be unique.'
  //       ]
  //     ],
  //     'inputProperties' => [
  //       'type'             => 'text',
  //       'value'            => auth_user_name(),
  //       'validations'      => [
  //         'required'          => true,
  //         'validations'       => [
  //           'range'              => [
  //             'min'                 => 3,
  //             'max'                 => 20,
  //           ]
  //         ]
  //       ]
  //     ]
  //   ]);
  //   // User ID Field
  //   $info->addChild('field', [
  //     'properties' => [
  //       'name'        => 'user_id',
  //       'size'        => 'third'
  //     ],
  //     'content'    => [
  //       'title'       => 'User ID',
  //       'subtitle'    => 'Your unique User ID',
  //       'description' => [
  //         'Your User ID is used to identify your account, even when you change your username.',
  //         'Your User ID cannot be changed.'
  //       ]
  //     ],
  //     'inputProperties' => [
  //       'type'             => 'text',
  //       'value'            => auth_user_id(),
  //       'validations'      => [
  //         'required'          => true,
  //         'readonly'          => true,
  //         'validations'       => [
  //           'range'              => [
  //             'is'                  => 12,
  //           ]
  //         ]
  //       ]
  //     ]
  //   ]);

  //   // Roles Section
  //   $roles = $form_updateProfile->addChild('section', [
  //     'properties' => [
  //       'name'        => 'roles',
  //       'customHTML'  => [
  //         'classes'      => [
  //           'details'
  //         ]
  //       ]
  //     ],
  //     'content'    => [
  //       'title'       => 'Roles',
  //       'subtitle'    => 'Roles are additional permissions granted to your account. They are visible to all users.'
  //     ]
  //   ]);
  //   // Roles 
  //   $roles->addChild('field', [
  //     'properties' => [
  //       'name'        => 'roles'
  //     ],
  //     'content'    => [
  //       'title'       => 'Available Roles',
  //       // 'subtitle'    => 'Roles are additional permissions granted to your account. They are visible to all users.'
  //     ],
  //     'inputProperties' => [
  //       'type'             => 'toggle-box',
  //       'value'            => (function () {
  //         $value = [];
  //         $userRoles = auth_user_roles();

  //         foreach ($userRoles as $role => $hasRole) {
  //           if ($hasRole) {
  //             $value[] = $role;
  //           }
  //         }

  //         return implode(", ", $value);
  //       })(),
  //       'options'          => (function () {
  //         $options = [];
          
  //         foreach (AUTH_ROLES['props'] as $role => $properties) {
  //           $options[$role] = $properties['name'];
  //         }

  //         return $options;
  //       })(),
  //       'validations'      => [
  //         'readonly'          => true
  //       ]
  //     ]
  //   ]);
  //   // Profile Stats
  //   $stats = $form_updateProfile->addChild('section', [
  //     'properties' => [
  //       'name'        => 'profile_stats'
  //     ],
  //     'content'    => [
  //       'title'       => 'Profile Stats',
  //       'subtitle'    => 'Statistics related to your account activity'
  //     ]
  //   ]);
  //   // Privacy Settings
  //   $stats->addChild('field', [
  //     'properties' => [
  //       'name'        => 'privacy'
  //     ],
  //     'content'    => [
  //       'title'       => 'Show on Public Profile',
  //       'subtitle'    => 'Indicates if your Profile Stats should be visible to other users, or just you.'
  //     ],
  //     'inputProperties' => [
  //       'type'             => 'toggle-button',
  //       'placeholder'      => 'Show Stats',
  //       'value'            => 'on',
  //       'validations'      => [
  //         'required'          => true
  //       ]
  //     ]
  //   ]);
  // })();
?>