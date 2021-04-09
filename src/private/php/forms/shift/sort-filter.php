<?php
  require_once('shift_constants.php');

  /**
   * Generate the SHiFT Sort/Filter Form
   * 
   * @param string $game - The *Game ID* of the game, or **"all"** for all games. 
   * @param bool $showOwnedCodes - Indicates if owned SHiFT Codes are being displayed.
   */
  function getForm_shiftSortFilter ($game = 'all', $showOwnedCodes = true) {
    $form_shiftSortFilter = new FormBase([
      'properties'     => [
        'name'            => 'shift_header_sort_filter_form'
      ],
      'content'        => [
        // 'title'           => 'Sort & Filter'
      ],
      'formProperties' => [
        // 'action'          => '#',
        'action'          => [
          'type'             => 'js',
          'path'             => '/assets/requests/post/js/shift-sort-filter'
        ],
        'spacing'         => 'vertical'
      ],
      'formFooter'     => [
        'actions'         => [
          'reset'            => [
            'enabled'           => true,
            'content'           => 'Clear',
            'requiresModify'    => false,
            'tooltip'           => [
              'content'            => 'Reset the Sorting and Filtering options'
            ],
            'confirmation'      => [
              'required'           => false
            ]
          ],
          'detailsToggle'    => [
            'hideByDefault'     => true
          ],
          'submit'           => [
            'content'           => 'Apply',
            'requiresModify'    => false,
            'tooltip'           => [
              'content'            => 'Apply the Sorting and Filtering options'
            ]
          ]
        ]
      ],
      'formResult'     => [
        'formState'       => 'enabled'
      ]
    ]);

    // Sort by Release Date
    $form_shiftSortFilter->addChild('field', [
      'properties' => [
        'name'        => 'order',
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
        ],
        'validations' => [
          'required'     => true
        ]
      ]
    ]);
    // Filter by Code Status
    $form_shiftSortFilter->addChild('field', [
      'properties' => [
        'name'        => 'status',
        'size'        => 'half'
      ],
      'content'    => [
        'title'       => 'Filter by Code Status'
      ],
      'inputProperties' => [
        'type'             => 'checkbox',
        'value'            => 'active',
        'options'          => (function () use ($showOwnedCodes) {
          $options = [
            'active'            => 'Active',
            'expired'           => 'Expired'
          ];

          if ($showOwnedCodes) {
            $options['hidden'] = 'Hidden';
          }

          return $options;
        })(),
        'validations' => [
          'required'     => true
        ]
      ]
    ]);
    // Filter by Platform
    $form_shiftSortFilter->addChild('field', [
      'properties' => [
        'name'        => 'platform',
        'size'        => 'half',
        // 'disabled'    => true,
        // 'hidden'      => true
      ],
      'content'    => [
        'title'       => 'Filter by Platform'
      ],
      'inputProperties' => [
        'type'             => 'select',
        'value'            => '',
        'options'          => (function () use ($game) {
          $options = [
            '' => 'Show All'
          ];

          foreach (SHIFT_CODE_PLATFORMS as $familyID => $familyProps) {
            foreach ($familyProps['platforms'] as $platformID => $platformProps) {
              if ($game === false || array_search($game, $platformProps['supported_games']) !== false) {
                $options[$platformID] = $platformProps['display_name'];
              }
            }
          }
  
          return $options;
        })()
      ]
    ]);
    // Filter by Game
    $form_shiftSortFilter->addChild('field', [
      'properties' => [
        'name'        => 'game',
        'size'        => 'half',
        'disabled'    => $game != false,
        'hidden'      => $game != false
      ],
      'content'    => [
        'title'       => 'Filter by Game'
      ],
      'inputProperties' => [
        'type'             => 'select',
        'value'            => '',
        'options'          => (function () {
          $options = [
            '' => 'Show All'
          ];
  
          foreach (SHIFT_GAMES as $gameID => $gameNames) {
            $options[$gameID] = $gameNames['name'];
          }
  
          return $options;
        })()
      ]
    ]);

    return $form_shiftSortFilter;
  }
?>