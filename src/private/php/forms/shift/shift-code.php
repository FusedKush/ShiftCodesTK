<?php
  require_once('shift_constants.php');

  use ShiftCodesTK\Strings;

  /**
   * Retrieves an Add SHiFT Code or Update SHiFT Code Form
   * 
   * @param "add"|"update" $type The type of form to retrieve.
   * @return object Returns the SHiFT Code Form Object
   */
  function getShiftCodeForm ($type) {
    $timestamp = new DateTime();
    $timestamp->setTime(0, 0, 0, 0);
    $formProps = [
      'formProperties' => [
        'action'          => [
          'type'             => 'ajax',
          'path'             => '/api/post/shift/shift-code'
        ]
      ],
      'formFooter'     => [
        'actions'         => [
          'reset'            => [
            'enabled'           => true,
            'confirmation'      => [
              'required'           => true
            ]
          ],
          'detailsToggle'    => [
            'enabled'           => true
          ],
          'submit'           => [
            'content'           => 'Save'
          ]
        ]
      ],
      'formResult'   => [
        'toast'           => [
          'enabled'    => true,
          'method'     => 'response',
          'properties' => []
        ]
      ]
    ];

    if ($type == 'add') {
      $formProps = array_replace_recursive($formProps, [
        'properties'     => [
          'name'            => 'new_shift_code_form'
        ],
        'formProperties' => [
          'action'          => [
            'confirmUnsavedChanges' => true,
          ],
          'spacing'         => 'vertical',
          // 'showBackground'  => true
        ],
        'formFooter'     => [
          'isSticky'        => true,
          'showProgress'    => true,
          'actions'         => [
            'reset'            => [
              'enabled'           => true,
              'content'           => 'Start Over',
              'tooltip' => [
                'content'  => 'Start Over with a new SHiFT Code'
              ],
              'confirmation'      => [
                'title'              => 'Start Over',
                'body'               => 'Are you sure you want to discard this SHiFT Code? Any unsaved updates will be lost.',
                'actions'            => [
                  'deny'           => [
                    'name'            => 'Keep Working',
                    'tooltip'         => 'Continue working on the current SHiFT Code'
                  ],
                  'approve'        => [
                    'name'            => 'Start Over',
                    'tooltip'         => 'Discard any unsaved changes and start over'
                  ]
                ]
              ]
            ],
            'detailsToggle'    => [
              'hideByDefault'     => false
            ],
            'submit'           => [
              'content'           => 'Submit SHiFT Code',
              'tooltip' => [
                'content'         => 'Submit the SHiFT Code'
              ],
              'confirmation'      => [
                'required'           => true,
                'title'              => 'Submit this SHiFT Code?',
                'body'               => Strings\encode_html('Are you sure you want to submit this SHiFT Code? You will be able to edit it later from <a class="styled" href="/codes/" aria-label="My SHiFT Codes">My Codes</a>.'),
                'requireResponseData' => true,
                'actions'            => [
                  'deny'           => [
                    'name'            => 'Keep Working',
                    'tooltip'         => 'Continue working on the current SHiFT Code'
                  ],
                  'approve'        => [
                    'name'            => 'Submit SHiFT Code',
                    'tooltip'         => 'Submit the SHiFT Code to ShiftCodesTK'
                  ]
                ]
              ],
              'classes'          => [
                'styled',
                'info',
                'form-submit'
              ]
            ]
          ]
        ],
        'formResult'     => [
          'formState'       => 'reset'
        ],
        'content'        => [
          'subtitle'        => "You can use this form to submit a SHiFT Code to ShiftCodesTK. Once you've submitted a code, you can view it on the code's respective page, or in <a class=\"styled\" href=\"/codes/\">My SHiFT Codes</a>."
        ]
      ]);
    }
    else {
      $formProps = array_replace_recursive($formProps, [
        'properties'     => [
          'name'            => 'update_shift_code_form',
          'customHTML'      => [
            'classes'          => [
              'no-auto-setup'
            ]
          ]
        ],
        'formFooter'     => [
          'showChangeCount' => true,
          'actions'         => [
            'reset'            => [
              'enabled'           => true,
              'content'           => 'Discard',
              'tooltip' => [
                'content'  => 'Discard any changes made to the SHiFT Code'
              ],
              'requiresModify'    => false,
              'confirmation'      => [
                'title'              => 'Discard your Changes',
                'body'               => 'Are you sure you want to discard your changes? Any unsaved updates will be lost.',
                'actions'            => [
                  'deny'           => [
                    'name'            => 'Keep Working',
                    'tooltip'         => 'Continue working on the current SHiFT Code'
                  ],
                  'approve'        => [
                    'name'            => 'Discard Changes',
                    'tooltip'         => 'Discard any unsaved changes made to this SHiFT Code'
                  ]
                ]
              ]
            ],
            'submit'           => [
              'content'           => 'Update',
              'tooltip' => [
                'content'  => 'Submit the SHiFT Code'
              ],
              'confirmation'      => [
                'required'           => false
              ]
            ]
          ]
        ]
      ]);
    }

    $form_shiftCode = new FormBase($formProps);

    // Form Type
    (function () use (&$form_shiftCode, $type) {
      $form_shiftCode->addChild('field', [
        'properties'      => [
          'name'             => 'form_type',
          'hidden'           => true
        ],
        'inputProperties' => [
          'type'             => 'text',
          'value'            => $type,
          'validations'      => [
            'required'         => true,
            'readonly'          => true,
            'validations'       => [
              'match'              => [
                'add',
                'update'
              ]
            ]
          ]
        ]
      ]);
    })();
    // SHiFT Code ID
    (function () use (&$form_shiftCode, $type) {
      if ($type == 'update') {
        $props = [
          'properties'      => [
            'name'             => 'code_id',
            'hidden'           => true
          ],
          'content'         => [
            'title'            => 'SHiFT Code ID'
          ],
          'inputProperties' => [
            'type'             => 'text',
            'validations'      => [
              'type'              => 'string',
              'readonly'          => true,
              'validations'       => [
                'range'              => [
                  'is'                  => 12
                ],
                'pattern'            => '/^11\d{10}/'
              ],
              'customValidationMessages' => [
                'rangeMismatch'     => 'SHiFT Code IDs are 12 numbers long.',
                'patternMismatch'   => 'Not a valid SHiFT Code ID.'
              ]
            ]
          ]
        ];
  
        $form_shiftCode->addChild('field', $props);
      }
    })();
    // Reward
    (function () use (&$form_shiftCode) {
      $allowedChars = [
        '/',
        '\\',
        '\'',
        "\"",
        '&',
        '.',
        "\-",
      ];
      $props = [
        'properties'      => [
          'name'             => 'reward'
        ],
        'content'         => [
          'title'            => 'Reward',
          'subtitle'         => 'The reward(s) granted by the SHiFT Code'
        ],
        'inputProperties' => [
          'type'             => 'text',
          'placeholder'      => '5 Golden Keys',
          'validations'      => [
            'type'              => 'string',
            'required'          => true,
            'validations'       => [
              'range'              => [
                'min'                 => 3,
                'max'                 => 64
              ],
              'pattern'            => "%^[a-zA-Z0-9 " . implode('', $allowedChars) . "]+$%",
              'customValidationMessages' => [
                'patternMismatch' => 'Reward can contain alphanumerical characters and the following special characters: ' . implode(' ', $allowedChars)
              ]
            ]
          ]
        ]
      ];

      $form_shiftCode->addChild('field', $props);
    })();
    // Game
    (function () use (&$form_shiftCode) {
      $props = [
        'properties'      => [
          'name'             => 'game_id',
          'size'             => 'half'
        ],
        'content'         => [
          'title'            => 'Supported Game',
          'subtitle'         => 'The game that the SHiFT Code can be redeemed for.'
        ],
        'inputProperties' => [
          'type'             => 'radio',
          'value'            => isset($_GET['game']) 
                                  && array_search($_GET['game'], array_keys(SHIFT_GAMES)) !== false
                                ? $_GET['game']
                                : '',
          'options'          => (function () {
            $games = [];
    
            foreach (SHIFT_GAMES as $game => $names) {
              $games[$game] = $names['long_name'];
            }
    
            return $games;
          })(),
          'validations'      => [
            'required'          => true
          ],
          'hasControl'  => (function () {
            $controlList = [];
            $fieldList = (function () {
              $fieldList = [
                'universal' => 'codes_universal'
              ];
  
              foreach (SHIFT_CODE_PLATFORMS as $familyID => $familyInfo) {
                $fieldList[$familyID] = "codes_individual_{$familyID}";
              }
  
              return $fieldList;
            })();
  
            foreach (SHIFT_GAMES as $gameID => $gameInfo) {
              $unsupportedList = [
                'families' => implode(", ", $gameInfo['support']['unsupported']['families']),
                'platforms' => implode(", ", $gameInfo['support']['unsupported']['platforms'])
              ];
              $controlList[] = [
                'condition' => "hasValue: {$gameID}",
                'controls'  => (function () use ($fieldList, $gameInfo, $unsupportedList) {
                  $controllerList = [];
  
                  foreach ($fieldList as $fieldFamilyID => $fieldName) {
                    // $controllerList["{$fieldName}[{$platformList['supported']}]"] = [
                    //   'disabled' => false
                    // ];

                    if (strpos($unsupportedList['families'], $fieldFamilyID) !== false) {
                      $controllerList["{$fieldName}_code"] = [
                        'disabled' => true
                      ];
                    }

                    $controllerList["{$fieldName}_platforms[][{$unsupportedList['platforms']}]"] = [
                      'disabled' => true
                    ];
                    $controllerList["{$fieldName}_platforms[]"] = [
                      'value' => $gameInfo['support']['supported']['platforms']
                    ];
                  }
  
                  return $controllerList;
                })()
              ];
            }

            return $controlList;
          })()
        ],
      ];

      $form_shiftCode->addChild('field', $props);
    })();
    // Source
    (function () use (&$form_shiftCode) {
      // Group
      $group = (function () use (&$form_shiftCode) {
        $props = [
          'properties' => [
            'name'        => 'source',
            'size'        => 'half'
          ],
          'content'         => [
            'title'            => 'Code Source',
            'subtitle'         => 'The original source of the SHiFT Code.'
          ],
          'inputProperties' => [
            'type'             => 'group',
            'validations'      => [
              'required'          => true
            ]
          ]
        ];

        return $form_shiftCode->addChild('field', $props);
      })();
      
      // URL Source
      (function () use (&$group) {
        $props = [
          'properties'      => [
            'name'             => 'url'
          ],
          'inputProperties' => [
            'type'             => 'url',
            'placeholder'      => 'https://twitter.com/DuvalMagic/status/1119388343832199168',
            'validations'      => [
              'type'              => 'string',
              'required'          => true,
              'validations'       => [
                'range'              => [
                  'max'                 => 512
                ]
              ]
            ]
          ]
        ];

        $group->addChild('field', $props);
      })();
      // String Source
      (function () use (&$group) {
        $props = [
          'properties'      => [
            'name'             => 'string',
            'hidden'           => 'true',
            'disabled'         => 'true'
          ],
          'inputProperties' => [
            'type'             => 'text',
            'placeholder'      => "Diamond Loot Chest Collector's Edition",
            'validations'      => [
              'type'              => 'string',
              'required'          => true,
              'validations'       => [
                'range'              => [
                  'max'                 => 64
                ]
              ]
            ]
          ]
        ];

        $group->addChild('field', $props);
      })();
      // Source Type
      (function () use (&$group) {
        $props = [
          'properties'      => [
            'name'              => 'type',
            'size'              => 'half'
          ],
          'content'         => [
            'title'             => 'Source Type',
            // 'subtitle'          => 'Indicates the type of source the SHiFT Code originated from.',
            'description'       => [
              'online'   => 'The SHiFT Code originated from an online source, such as a Tweet from Gearbox.',
              'physical' => 'The SHiFT Code originated from a physical source, such as in a bonus edition of the game.',
              'none'     => 'The SHiFT Code does not have a confirmed source. ',
            ],
            'hideTitle'         => true
          ],
          'inputProperties' => [
            'type'              => 'radio',
            'value'             => 'online',
            'options'           => [
              'online'             => 'Online',
              'physical'           => 'Physical',
              'none'               => 'No Source Available'
            ],
            'hasControl'  => [
              [
                'condition' => 'hasValue: physical',
                'controls'  => [
                  'source_url' => [
                    'disabled'        => true,
                    'hidden'          => true
                  ],
                  'source_string' => [
                    'disabled'        => false,
                    'hidden'          => false
                  ]
                ]
              ],
              [
                'condition' => 'hasValue: none',
                'controls'  => [
                  'source_url' => [
                    'disabled'        => true,
                    'hidden'          => true
                  ],
                  'source_string' => [
                    'disabled'        => false,
                    'hidden'          => false,
                    'readonly'        => true,
                    'value'           => 'N/A'
                  ]
                ]
              ]
            ],
            'validations'      => [
              'type'              => 'string',
              'required'          => true
            ]
          ]
        ];

        $group->addChild('field', $props);
      })();
    })();
    // Release Date
    (function () use (&$form_shiftCode, $timestamp) {
      $minExpiration = clone($timestamp);
      $minExpiration->sub(new DateInterval('P6M'));

      $props = [
        'properties'      => [
          'name'             => 'release_date',
          'size'             => 'half'
        ],
        'content'         => [
          'title'            => 'Release Date',
          'subtitle'         => 'The date the <em>SHiFT Code</em> was released.',
        ],
        'inputProperties' => [
          'placeholder'      => $timestamp->format(\ShiftCodesTK\DATE_FORMATS['date']),
          'type'             => 'date',
          'value'            => $timestamp->format(\ShiftCodesTK\DATE_FORMATS['date']),
          'validations'      => [
            'required'          => true,
            'type'              => 'date',
            'validations'       => [
              'range' => [
                // 'max'              => '${expiration_date_value_date}'
                'min'              => $minExpiration->format(\ShiftCodesTK\DATE_FORMATS['date']),
                'max'              => "\${expiration_date_value_date}|{$timestamp->format(\ShiftCodesTK\DATE_FORMATS['date'])}"
              ]
            ]
            // 'required'          => true,
          ]
        ]
      ];

      $form_shiftCode->addChild('field', $props);
    })();
    // Expiration Date
    (function () use (&$form_shiftCode, $timestamp) {
      // Group
      $group = (function () use (&$form_shiftCode) {
        $props = [
          'properties' => [
            'name'        => 'expiration_date',
            'size'        => 'half'
          ],
          'content'         => [
            'title'            => 'Expiration Date',
            'subtitle'         => 'When the SHiFT Code is set to <em>expire</em>.'
          ],
          'inputProperties' => [
            'type'             => 'group',
            'validations'      => [
              'required'          => true
            ]
          ]
        ];

        return $form_shiftCode->addChild('field', $props);
      })();
      
      // Field
      (function () use (&$group, $timestamp) {
        $defaultExpiration = clone($timestamp);
        $defaultExpiration->add(new DateInterval('P17D'));
        $maxExpiration = clone($timestamp);
        $maxExpiration->add(new DateInterval('P6M'));

        $props = [
          'properties'      => [
            'name'             => 'value'
          ],
          'content'         => [
            'description'      => 'The <em>timezone</em> will sometimes be provided alongside the SHiFT Code\'s <em>Expiration Date</em>. Most codes released by 
            <a class="styled" href="https://twitter.com/borderlands" target="_blank" rel="external noopener">Gearbox</a> 
            are released for <b>PST</b>/<b>PDT</b>. Those released by people such as 
            <a class="styled" href="https://twitter.com/DuvalMagic" target="_blank" rel="external noopener">Randy Pitchford</a>, 
            are released in <b>CST</b>/<b>CDT</b>.' 
          ],
          'inputProperties' => [
            'type'             => 'datetimetz',
            // 'value'            => $timestamp->add(new DateInterval('P13D'))->format(DateTime::ISO8601) . ' America/Los_Angeles',
            'value'            => [
              // 'date'              => $defaultExpiration->format(\ShiftCodesTK\DATE_FORMATS['date']),
              'time'              => "23:59:59",
              'tz'                => 'America/Los_Angeles'
            ],
            'options'          => [
              'America/Chicago',
              'America/Los_Angeles'
            ],
            'validations'      => [
              // 'type'              => 'date',
              // 'required'          => true,
              'required' => true,
              'validations'       => [
                'range' => [
                  'min'               => [
                    'date'               => "\${release_date}|{$timestamp->format(\ShiftCodesTK\DATE_FORMATS['date'])}"
                  ],
                  'max'               => [
                    'date'               => $maxExpiration->format(\ShiftCodesTK\DATE_FORMATS['date'])
                  ]
                ]
              ]
            ]
          ]
        ];
  
        $group->addChild('field', $props);
      })();
      // Date Type
      (function () use (&$group, $timestamp) {
        $props = [
          'properties'      => [
            'name'              => 'type'
          ],
          'content'         => [
            'title'             => 'Expiration Type',
            'description'       => [
              'through'            => 'This SHiFT Code is active <em>through</em> the provided date.',
              'until'              => 'This SHiFT Code is active <em>until</em> the provided date and time.',
              'infinite'           => 'This SHiFT Code is set to never expire.',
              'none'               => 'This SHiFT Code does not have a confirmed Expiration Date.'
            ],
            'hideTitle'         => true
          ],
          'inputProperties' => [
            'type'              => 'radio',
            'value'             => 'through',
            'options'           => [
              'through'            => 'Active through Expiration Date',
              'until'              => 'Active until Expiration Date',
              'infinite'           => 'Never Expires',
              'none'               => 'No Expiration Date Available'
            ],
            'hasControl'  => [
              [
                'condition' => 'hasValue: through',
                'controls'  => [
                  'expiration_date_value_time' => [
                    'readonly'        => true,
                    'value'           => "23:59:59"
                  ]
                ]
              ],
              [
                'condition' => 'hasValue: until',
                'controls'  => [
                  'expiration_date_value_date' => [
                    'value'           => (function () use ($timestamp) {
                      $expiration = clone($timestamp);
                      $expiration->add(new DateInterval('P3D'));

                      return $expiration->format(\ShiftCodesTK\DATE_FORMATS['date']);
                    })()
                  ],
                  'expiration_date_value_time' => [
                    // 'readonly'        => false,
                    'value'           => "10:00:00"
                  ]
                ]
              ],
              [
                'condition' => 'hasValue: infinite',
                'controls'  => [
                  'expiration_date_value_date' => [
                    'disabled'        => true,
                    'value'           => ""
                  ],
                  'expiration_date_value_time' => [
                    'disabled'        => true,
                    'value'           => ""
                  ],
                  'expiration_date_value_tz' => [
                    'disabled'        => true,
                    'value'           => ""
                  ]
                ]
              ],
              [
                'condition' => 'hasValue: none',
                'controls'  => [
                  'expiration_date_value_date' => [
                    'disabled'        => true,
                    'value'           => ""
                  ],
                  'expiration_date_value_time' => [
                    'disabled'        => true,
                    'value'           => ""
                  ],
                  'expiration_date_value_tz' => [
                    'disabled'        => true,
                    'value'           => ""
                  ]
                ]
              ],
            ],
            'validations'      => [
              'type'              => 'string',
              'required'          => true
            ]
          ]
        ];

        $group->addChild('field', $props);
      })();
    })();
    // Notes
    (function () use (&$form_shiftCode) {
      $allowedChars = [
        '/',
        '\\',
        '\'',
        "\"",
        '&',
        '-',
        '.'
      ];
      $props = [
        'properties'      => [
          'name'             => 'notes'
        ],
        'content'         => [
          'title'            => 'Notes',
          'subtitle'         => 'Additional information regarding the SHiFT Code'
        ],
        'inputProperties' => [
          'type'             => 'textarea',
          'placeholder'      => "This SHiFT Code was part of last year's event...",
          'validations'      => [
            'type'              => 'string',
            'validations'       => [
              'range'              => [
                'max'                 => 512
              ],
              // 'pattern'            => "%^[a-zA-Z0-9 " . implode('', $allowedChars) . "]+$%",
              // 'customValidationMessages' => [
              //   'patternMismatch' => 'Notes contain invalid characters. Only the following special characters are allowed: ' . implode(' ', $allowedChars)
              // ]
            ]
          ]
        ]
      ];

      $form_shiftCode->addChild('field', $props);
    })();
    // SHiFT Codes
    (function () use (&$form_shiftCode) {
      // Generate the group, supported platforms, and shift code fields for a given platform
      $getPlatformFields = function ($platform, &$parent) {
        $platformDisplayName = $platform == 'universal'
                               ? 'Universal'
                               : SHIFT_CODE_PLATFORMS[$platform]['display_name'];

        // Group
        $group = $parent->addChild('field', [
          'properties'      => [
            'name'             => $platform,
            'hidden'           => $platform != 'universal',
            'disabled'         => $platform != 'universal',
          ],
          'inputProperties' => [
            'type'              => 'group',
            'validations'      => [
              'required'          => true
            ]
          ]
        ]);

        // SHiFT Code
        $group->addChild('field', [
          'properties'      => [
            'name'             => 'code',
            'size'             => 'half'
          ],
          'content'         => [
            'title'            => "{$platformDisplayName} SHiFT Code",
            'innerTitle'       => true,
            // 'subtitle'         => $platform == 'universal'
            //                       ? "The Universal SHiFT Code."
            //                       : "The {$platformDisplayName} SHiFT Code."
          ],
          'inputProperties' => [
            'type'             => 'text',
            'placeholder'      => 'A12B3-C4D5E-6F7G8-H9I0J-K1L2M',
            'toolbar'          => [
              'clearFieldButton'  => true,
              'textTransform'     => 'uppercase',
              'dynamicFill'       => [
                'match'              => '([\w\d]{5})',
                'fill'               => '$1-',
              ]
            ],
            'validations'      => [
              'validations'       => [
                'range'              => [
                  'is'                  => 29
                ],
                'pattern'            => '%([a-zA-Z0-9]{5}-{0,1}){5}%'
              ],
              'customValidationMessages' => [
                'rangeMismatch'             => 'SHiFT Codes must be 29 characters long, in the following format: XXXXX-XXXXX-XXXXX-XXXXX-XXXXX',
                'patternMismatch'           => 'SHiFT Codes must be in the following format: XXXXX-XXXXX-XXXXX-XXXXX-XXXXX'
              ]
            ]
          ]
        ]);
        // Supported Platforms
        $group->addChild('field', [
          'properties'      => [
            'name'             => 'platforms',
            'size'             => 'half'
          ],
          'content'         => [
            'title'            => 'Supported Platforms',
            'subtitle'         => $platform == 'universal'
                                  ? "Platforms supported by the Universal SHiFT Code."
                                  : "Supported {$platformDisplayName} platforms.",
            'description'      => 'Available options are determined by the <button class="link" type="button" data-target-field="game_id"><code>Supported Game</code></button>.'
          ],
          'inputProperties' => [
            'type'             => 'checkbox',
            'options'          => (function () use ($platform) {
              $options = [];

              $addPlatformOptions = function ($platformList) use (&$options) {
                foreach ($platformList as $platformName => $platformInfo) {
                  $options[$platformName] = $platformInfo['display_name'];
                }
              };
              
              if ($platform == 'universal') {
                foreach (SHIFT_CODE_PLATFORMS as $platformFamily => $platformFamilyInfo) {
                  $addPlatformOptions($platformFamilyInfo['platforms']);
                }
              }
              else {
                $addPlatformOptions(SHIFT_CODE_PLATFORMS[$platform]['platforms']);
              }

              return $options;
            })(),
            'wrapOptions'      => 'half'
          ]
        ]);

        return $group;
      };

      // Section
      $section = $form_shiftCode->addChild('section', [
        'properties' => [
          'name'        => 'codes'
        ],
        'content'    => [
          'title'       => 'SHiFT Codes',
          'subtitle'    => 'The redeemable SHiFT Codes'
        ]
      ]);

      // Code Type
      $section->addChild('field', [
        'properties'      => [
          'name'             => 'code_type'
        ],
        'content'         => [
          'title'            => 'SHiFT Code Type',
          'hideTitle'        => true,
          // 'subtitle'         => 'The type of SHiFT Code that is being provided.',
          'description'      => [
            'universal'         => 'A single SHiFT Code can be redeemed for all supported platforms.',
            'individual'        => 'An individual SHiFT Code is available for each of the supported platforms.'
          ]
        ],
        'inputProperties' => [
          'type'              => 'radio',
          'value'             => 'universal',
          'options'           => [
            'universal'          => 'Universal',
            'individual'         => 'Individual'
          ],
          'wrapOptions'       => 'half',
          'hasControl'  => [
            [
              'condition' => 'hasValue: individual',
              'controls'  => [
                'codes_universal' => [
                  'disabled'        => true,
                  'hidden'          => true
                ],
                'codes_individual' => [
                  'disabled'        => false,
                  'hidden'          => false
                ],
              ]
            ]
          ],
          'validations'      => [
            'type'              => 'string',
            'required'          => true
          ]
        ]
      ]);

      // Universal SHiFT Code
      $getPlatformFields('universal', $section);
      // Individual SHiFT Codes
      $individualCodesGroup = $section->addChild('field', [
        'properties'      => [
          'name'             => 'individual'
        ],
        'inputProperties' => [
          'type'              => 'group'
        ]
      ]);

      foreach (SHIFT_CODE_PLATFORMS as $platformCategory => $platformList) {
        $getPlatformFields($platformCategory, $individualCodesGroup);
      }
    })();

    return $form_shiftCode;
  }
?>