<?php
  require_once(SCRIPTS_INCLUDES_PATH . 'shift_constants.php');

  $form_shiftDelete = new FormBase([
    'properties' => [
      'name'        => 'delete_shift_code_form'
    ],
    'content'    => [
      // 'title'       => 'SHiFT Code',
      // 'subtitle'    => 'Are you sure you want to delete this SHiFT Code?',
      // 'description' => [
      //   'This SHiFT Code will no longer be available to view or share by anyone on ShiftCodesTK.',
      //   'You will permanently lose access to this SHiFT Code.',
      //   'This action <strong>cannot be reversed</strong>.'
      // ]
    ],
    'formProperties' => [
      'action'         => '/assets/requests/post/shift/delete',
      'spacing'        => 'vertical'
    ],
    'formFooter'    => [
      'actions'        => [
        'reset'           => [
          'enabled'          => true,
          'content'          => 'Cancel',
          'title'            => 'Abandon this action and leave the SHiFT Code as-is',
          'classes'          => [
            'styled',
            'modal-toggle'
          ]
        ],
        'submit'          => [
          'content'          => 'Delete SHiFT Code',
          'title'            => 'Permanently delete the SHiFT Code',
          'classes'          => [
            'styled',
            'color',
            'danger'
          ]
        ]
      ]
    ]
  ]);

  // SHiFT Code ID
  $form_shiftDelete->addChild('field', [
    'properties' => [
      'name'        => 'code_id'
    ],
    'content'         => [
      // 'title'            => 'SHiFT Code',
      'subtitle'         => 'Are you sure you want to delete this SHiFT Code?',
      'description'      => [
        'This SHiFT Code will no longer be available to view or share by anyone on ShiftCodesTK.',
        'You will permanently lose access to this SHiFT Code.',
        'This action <strong>cannot be reversed</strong>.'
      ]
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
?>