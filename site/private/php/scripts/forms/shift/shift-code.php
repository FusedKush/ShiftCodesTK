<?php
  require_once(SCRIPTS_INCLUDES_PATH . 'shift_constants.php');

  $form_shiftCode = new FormBase([
    'properties'     => [
      'name'            => 'shift_code_form'
    ],
    'formProperties' => [
      'action'          => '/assets/requests/post/shift/shift-code',
      'spacing'         => 'vertical'
    ],
    'formFooter'     => [
      'actions'         => [
        'reset'            => [
          'enabled'           => true,
          'content'           => 'Discard',
          'title'             => 'Discard any changes made to this SHiFT Code',
          'classes'           => [ 
            'modal-toggle' 
          ]
        ],
        'detailsToggle'    => [
          'enabled'           => true,
          'hideByDefault'     => true
        ],
        'submit'           => [
          'content'           => 'Save',
          'title'             => 'Save and submit this SHiFT Code'
        ]
      ]
    ]
  ]);

  (function () use (&$form_shiftCode) {
    // General Information
    $general = $form_shiftCode->addChild('section', [
      'properties' => [
        'name'        => 'general'
      ],
      'content'    => [
        'title'       => 'General Information',
        'subtitle'    => 'Standard information about the SHiFT Code'
      ]
    ]);
    // SHiFT Code ID
    $general->addChild('field', [
      'properties'      => [
        'name'             => 'code_id',
        'hidden'           => true
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
            'pattern'            => '/^\d+$/'
          ]
        ]
      ]
    ]);
    // Reward
    $general->addChild('field', [
      'properties'      => [
        'name'             => 'reward'
      ],
      'content'         => [
        'title'            => 'Reward',
        'subtitle'         => 'The reward(s) granted by the SHiFT Code'
      ],
      'inputProperties' => [
        'type'             => 'text',
        'validations'      => [
          'type'              => 'string',
          'required'          => true,
          'validations'       => [
            'range'              => [
              'min'                 => 3,
              'max'                 => 64
            ],
            'pattern'            => "/^[a-zA-Z0-9'\"\-\/.& ]+$/"
          ]
        ]
      ]
    ]);
  })();
?>