<?php
  require_once(SCRIPTS_INCLUDES_PATH . 'shift_constants.php');

  $form_shiftSortFilter = new FormBase([
    'properties'     => [
      'name'            => 'shift_header_sort_filter_form'
    ],
    'content'        => [
      'title'           => 'Sort & Filter SHiFT Codes' 
    ],
    'inputProperties' => [
      'validations'      => [
        'required'          => true
      ]
    ],
    'formProperties' => [
      // 'action'          => '#',
      'spacing'         => 'vertical'
    ],
    'formFooter'     => [
      'actions'         => [
        'reset'            => [
          'enabled'           => true,
          'content'           => 'Clear',
          'title'             => 'Reset all options to their default values'
        ],
        'detailsToggle'    => [
          'hideByDefault'     => true
        ],
        'submit'           => [
          'content'           => 'Update',
          'title'             => 'Update the sorting & filtering options'
        ]
      ]
    ]
  ]);

  (function () use (&$form_shiftSortFilter) {
    // Sort by Release Date
    $form_shiftSortFilter->addChild('field', [
      'properties' => [
        'name'        => 'sort',
        'size'        => 'half'
      ],
      'content'    => [
        'title'       => 'Sort'
      ],
      'inputProperties' => [
        'type'             => 'radio',
        'value'            => 'default',
        'options'          => [
          'default'           => 'Default',
          'newest'            => 'Newest',
          'oldest'            => 'Oldest'
        ]
      ]
    ]);
    // Filter by Code Status
    $form_shiftSortFilter->addChild('field', [
      'properties' => [
        'name'        => 'status_filter',
        'size'        => 'half'
      ],
      'content'    => [
        'title'       => 'Filter by Code Status'
      ],
      'inputProperties' => [
        'type'             => 'checkbox',
        'value'            => 'active',
        'options'          => [
          'active'            => 'Active',
          'expired'           => 'Expired'
        ]
      ]
    ]);
    // Filter by Game
    $form_shiftSortFilter->addChild('field', [
      'properties' => [
        'name'        => 'game_filter',
        'size'        => 'half',
        'disabled'    => true,
        'hidden'      => true
      ],
      'content'    => [
        'title'       => 'Filter by Game'
      ],
      'inputProperties' => [
        'type'             => 'radio',
        'value'            => 'all',
        'options'          => (function () {
          $options = [
            'all' => 'Show All'
          ];

          foreach (SHIFT_GAMES as $gameID => $gameNames) {
            $options[$gameID] = $gameNames['name'];
          }

          return $options;
        })()
      ]
    ]);
  })();
?>