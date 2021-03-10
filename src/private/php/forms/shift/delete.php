<?php
  /** Update SHiFT Code Redemption Status Form */
  $form_deleteShiftCode = new FormBase([
    'properties'     => [
      'name'            => 'delete_shift_code_form',
      'templates'       => [ 'SINGLE_BUTTON' ]
    ],
    'formProperties' => [
      'action'          => [
        'path'             => '/assets/requests/post/shift/delete'
      ] 
    ],
    'formFooter'     => [
      'actions'         => [
        'submit'           => [
          'confirmation'      => [
            'required'           => true,
            'title'              => 'Delete this SHiFT Code?',
            'body'               => <<<EOT
              <div class="container">
                <div class="message">Are you sure you want to delete this SHiFT Code? This action&nbsp;<em>cannot be reversed</em>.</div>
                <div class="dropdown-panel c">
                  <div class="primary">SHiFT Code Details</div>
                  <div class="body">
                    <dl class="info">
                      <div class="section id">
                        <dt>
                          <span class="icon fas fa-key box-icon" aria-hidden="true"></span>
                          <span>ID</span>
                        </dt>
                        <dd>
                          <i class="secondary"></i>
                          <div class="primary"></div>
                        </dd>
                      </div>
                      <div class="section owner">
                        <dt>
                          <span class="icon fas fa-user box-icon" aria-hidden="true"></span>
                          <span>Owner</span>
                        </dt>
                        <dd>
                          <div class="primary"></div>
                          <i class="secondary"></i>
                        </dd>
                      </div>
                      <div class="section created">
                        <dt>
                          <span class="icon fas fa-clock box-icon" aria-hidden="true"></span>
                          <span>Created</span>
                        </dt>
                        <dd>
                          <div class="primary"></div>
                          <i class="secondary"></i>
                        </dd>
                      </div>
                      <div class="section expiration">
                        <dt>
                          <span class="icon far fa-clock box-icon" aria-hidden="true"></span>
                          <span>Expiration</span>
                        </dt>
                        <dd>
                          <div class="primary"></div>
                          <i class="secondary"></i>
                        </dd>
                      </div>
                    </dl>
                  </div>
                </div>
              </div>
            EOT,
            'actions'            => [
              'approve'             => [
                'name'                 => 'Delete SHiFT Code',
                'tooltip'              => 'Permanently remove this SHiFT Code',
                'color'                => 'danger'
              ]
            ]
          ]
        ]
      ]
    ]
  ]);
  // Code ID
  $form_deleteShiftCode->addChild('field', [
    'properties' => [
      'name'        => 'code_id',
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
          'pattern'                 => '/^11\d{10}$/',
        ],
        'customValidationMessages' => [
          'patternMismatch'          => 'Invalid SHiFT Code ID. They start with 11...',
          'rangeMismatch'            => 'SHiFT Code IDs are 12 characters long',
        ]
      ]
    ]
  ]);
  // Delete Button
  $form_deleteShiftCode->addChild('button', [
    'properties' => [
      'name'        => 'delete_button',
      'customHTML'  => [
        'classes'      => [
          'action',
          'choice',
          'styled',
          'danger',
          'button-effect',
          'text',
          'auto-toggle'
        ]
      ]
    ],
    'inputProperties' => [
      'type'             => 'submit',
      'content'          => '<span class="icon inline-box-icon fas fa-trash-alt" aria-hidden="true"></span>&nbsp;&nbsp;&nbsp;<span>Delete</span>',
      'title'            => 'Permanently delete this SHiFT Code',
      'tooltip'          => [
        'content'           => 'Permanently delete this SHiFT Code.
                                <br>
                                <br>
                                <em>You will be prompted to confirm this action.</em>',
        'pos'               => 'left'
        // 'content'           => 'Mark this SHiFT Code as&nbsp;<em>redeemed</em>
        //                         <br>
        //                         <a class="styled success" href="#">Learn More</a>'
      ]
    ]
  ]);
?>