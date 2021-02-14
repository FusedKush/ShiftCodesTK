<?php
  namespace ShiftCodesTK\Users;

  /** A list of `ValidationProperties` used for validating User Properties. */
  interface Constraints {
    public const USER_ID = [
      'type'        => 'string',
      'required'    => true,
      'validations' => [
        'range'        => [
          'is'            => 12
        ],
        'pattern'      => "%^[0-9]+$%"
      ]
    ]; 
    public const EMAIL_ADDRESS = [
      'type'        => 'string',
      'required'    => true,
      'validations' => [
        'range'          => [
          'max'             => 320
        ],
        'isEmailAddress' => true
      ]
    ];
    public const USERNAME = [
      'type'        => 'string',
      'required'    => true,
      'validations' => [
        'range'        => [
          'min'           => 4,
          'max'           => 32
        ],
        'pattern'      => '%^[a-zA-Z0-9_]+$%'
      ]
    ];  
    public const PASSWORD = [
      'type'        => 'string',
      'required'    => true,
      'validations' => [
        'range'          => [
          'min'             => 8,
          'max'             => 64
        ]
      ]
    ];
  }
?>