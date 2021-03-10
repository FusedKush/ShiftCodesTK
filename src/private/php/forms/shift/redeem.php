<?php
  /** Update SHiFT Code Redemption Status Form */
  $form_redeemShiftCode = new FormBase([
    'properties'     => [
      'name'            => 'redeem_shift_code_form',
      'templates'       => [ 'SINGLE_BUTTON' ],
      // 'disabled'        => true
    ]
  ]);
  // Code Hash ID
  $form_redeemShiftCode->addChild('field', [
    'properties' => [
      'name'        => 'code',
      'hidden'      => true
    ],
    'inputProperties' => [
      'validations'      => [
        'required'          => true,
        'readonly'          => true,
        'validations'       => [
          'range'                   => [
            'is'                       => 12
          ],
          'pattern'                 => '/^12\d{10}$/',
          'customValidationMesages' => [
            'patternMismatch'          => 'Invalid SHiFT Code Hash ID. They start with 12...',
            'rangeMismatch'            => 'SHiFT Code Hash IDs are 12 characters long',
          ]
        ]
      ]
    ]
  ]);
  // Update Type
  $form_redeemShiftCode->addChild('field', [
    'properties'      => [
      'name'             => 'action',
      'hidden'           => true
    ],
    'inputProperties' => [
      'type'              => 'radio',
      'value'             => 'redeem',
      'options'           => [
        'redeem'            => 'Redeem',
        'remove'             => 'Remove'
      ],
      'validations'      => [
        'type'              => 'string',
        'required'          => true
      ]
    ]
  ]);
  // Redeem Button
  $form_redeemShiftCode->addChild('button', [
    'properties' => [
      'name'        => 'redeem_button',
      'customHTML'  => [
        'classes'      => [
          'styled',
          // 'action',
          'redeem',
          'layer-target',
          'button-effect',
          'text',
          'info',
          'auto-press',
        ]
      ]
    ],
    'inputProperties' => [
      'type'             => 'submit',
      'content'          => '<span class="icon"><span class="box-icon fas fa-bookmark" aria-hidden="true"></span></span>',
      'title'            => 'Mark this SHiFT Code as redeemed',
      'tooltip'          => [
        // 'content'           => 'SHiFT Code Redemption is coming soon.'
        'content'           => 'Mark this SHiFT Code as&nbsp;<em>redeemed</em>
                                <br>
                                <button class="link no-color modal-toggle auto-toggle" data-modal="shift_code_redeeming_codes_info_modal"><strong>Learn More</strong></button>'
      ]
    ]
  ]);
?>