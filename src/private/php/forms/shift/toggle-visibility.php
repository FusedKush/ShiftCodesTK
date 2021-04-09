<?php
  require_once('shift_constants.php');

  $form_shiftToggleVisibility = new FormBase([
    'properties' => [
      'name'        => 'toggle_shift_code_visibility_form'
    ],
    'formProperties' => [
      'action'         => [
        'path'            => '/assets/requests/post/shift/toggle-visibilty'
      ],
      'spacing'        => 'none'
    ],
    'formFooter'     => [
      'enabled'         => false,
      'actions'      => [
        'submit'        => [
          'requiresModify' => false
        ]
      ]
    ]
  ]);

  (function () use (&$form_shiftToggleVisibility) {
    // SHiFT Code ID
    $form_shiftToggleVisibility->addChild('field', [
      'properties' => [
        'name'        => 'code_id',
        'hidden'      => true
      ],
      'inputProperties' => [
        'validations'      => [
          'type'              => 'string',
          'required'          => true,
          'readonly'          => true,
          'validations'     => [
            'range'            => [
              'is'                => 12
            ],
            'pattern'          => '/[\d]+/'
          ]
        ]
      ]
    ]);

    // Make Public
    $form_shiftToggleVisibility->addChild('button', [
      'properties'      => [
        'name'             => 'make_public',
        'customHTML'       => [
          'classes'           => [
            'action',
            'choice',
            'styled',
            'button-effect',
            'text'
          ],
          'attributes'        => [
            'data-value'         => 'make_public'
          ]
        ]
      ],
      'inputProperties' => [
        'type'             => 'submit',
        'content'          => '<span class="icon inline-box-icon fas fa-eye" aria-hidden="true"></span><span>&nbsp;&nbsp; Make Public</span>',
        'tooltip'          => [
          'content'           => 'Mark this SHiFT Code as <em>Public</em>, visible to everyone.',
          'pos'               => 'left'
        ]
      ]
    ]);
    // Make Private
    $form_shiftToggleVisibility->addChild('button', [
      'properties'      => [
        'name'             => 'make_private',
        'customHTML'       => [
          'classes'           => [
            'action',
            'choice',
            'styled',
            'button-effect',
            'text'
          ],
          'attributes'        => [
            'data-value'         => 'make_private'
          ]
        ]
      ],
      'inputProperties' => [
        'type'             => 'submit',
        'content'          => '<span class="icon inline-box-icon fas fa-eye-slash" aria-hidden="true"></span><span>&nbsp;&nbsp; Make Private</span>',
        'tooltip'          => [
          'content'           => 'Mark this SHiFT Code as <em>Private</em>, hidden from the public.',
          'pos'               => 'left'
        ]
      ]
    ]);
  })();
?>