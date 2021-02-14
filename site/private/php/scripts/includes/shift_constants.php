<?php
  /**
   * @var array Supported SHiFT Code Platforms
   * - Each platform is an `associative array` in the following format:
   * - - `array $familyID` - The ID of the Platform, containing all of the information about the platform family.
   * - - - `string $display_name` - The _display name_ of the platform family.
   * - - - `array $platforms` - The platforms that belong to the platform family, and their associated information.
   * - - - - `string $display_name` - The _display name_ of the platform.
   * - - - - `array $supported_games` - The games that are supported by the platform.
   */
  define("SHIFT_CODE_PLATFORMS", [
    'pc'       => [
      'display_name' => 'PC',
      'platforms'    => [
        'pc'            => [
          'display_name'    => 'PC',
          'supported_games' => [ 'bl3', 'bl1', 'tps', 'bl2' ]
        ],
        'pc_vr'         => [
          'display_name'    => 'PC VR',
          'supported_games' => [ 'bl2' ]
        ],
        'mac'           => [
          'display_name'    => 'Mac',
          'supported_games' => [ 'bl3', 'tps', 'bl2' ]
        ],
        'linux'         => [
          'display_name'    => 'Linux',
          'supported_games' => [ 'tps' ]
        ]
      ]
    ],
    'xbox'     => [
      'display_name' => 'Xbox',
      'platforms'    => [
        'xb360'         => [
          'display_name'    => 'Xbox 360',
          'supported_games' => [ 'tps', 'bl2' ]
        ],
        'xb1_xbxs'      => [
          'display_name'    => 'Xbox One / Series X|S',
          'supported_games' => [ 'bl3', 'bl1', 'tps', 'bl2' ]
        ],
      ]
    ],
    'ps'       => [
      'display_name' => 'PlayStation',
      'platforms'    => [
        'ps3'           => [
          'display_name'    => 'PS3',
          'supported_games' => [ 'tps', 'bl2' ]
        ],
        'ps_vita'       => [
          'display_name'    => 'PS Vita',
          'supported_games' => [ 'bl2' ]
        ],
        'ps4_ps5'       => [
          'display_name'    => 'PS4 / PS5',
          'supported_games' => [ 'bl3', 'bl1', 'tps', 'bl2' ]
        ],
        'ps_vr'         => [
          'display_name'    => 'PS VR',
          'supported_games' => [ 'bl2' ]
        ]
      ]
    ],
    'nintendo' => [
      'display_name' => 'Nintendo',
      'platforms'    => [
        'switch'        => [
          'display_name'    => 'Nintendo Switch',
          'supported_games' => [ 'bl1', 'tps', 'bl2' ]
        ]
      ]
    ]
  ]);
  // SHIFT_GAMES
  (function () {
    // The SHiFT Code Game List
    $gameList = [
      'bl2' => [
        'name'      => 'Borderlands 2',
      ],
      'tps' => [
        'name'      => 'Borderlands: TPS',
        'long_name' => 'Borderlands: The Pre-Sequel'
      ],
      'bl1' => [
        'name'      => 'Borderlands: GOTY',
        'long_name' => 'Borderlands: Game of the Year Edition'
      ],
      'bl3' => [
        'name'      => 'Borderlands 3',
      ]
    ];

    // Update Game List
    foreach ($gameList as $gameID => &$gameInfo) {
      if (!isset($gameInfo['long_name'])) {
        $gameInfo['long_name'] = $gameInfo['name'];
      }

      // Get Supported Platforms
      (function () use ($gameID, &$gameInfo) {
        $gameInfo['support'] = array_fill_keys(
          [ 'supported', 'unsupported' ], 
          array_fill_keys(
            [ 'families', 'platforms' ], 
            []
          )
        );
        $support = &$gameInfo['support'];

        foreach (SHIFT_CODE_PLATFORMS as $familyID => $familyInfo) {
          $isSupported = false;
          
          foreach ($familyInfo['platforms'] as $familyPlatformID => $familyPlatformInfo) {
            if (array_search($gameID, $familyPlatformInfo['supported_games']) !== false) {
              $isSupported = true;
              $support['supported']['platforms'][] = $familyPlatformID;
            }
            else {
              $support['unsupported']['platforms'][] = $familyPlatformID;
            }
          }

          if ($isSupported) {
            $support['supported']['families'][] = $familyID;
          }
          else {
            $support['unsupported']['families'][] = $familyID;
          }
        }
      })();
    }

    /**
     * @var array SHiFT Code Supported Games
     * - Each game is an `associative array` in the following format:
     * - - `string key` — The GameID of the Game
     * - - - `string $name` — The Display Name of the Game
     * - - - `string $long_name` — The Expanded Display Name of the Game
     * - - - `array $support` - A list of **SHIFT_CODE_PLATFORM** support.
     * - - - - `array $supported` - A list of supported families & platforms.
     * - - - - - `array $families` - Platform families that support the game.
     * - - - - - `array $platforms` - Platforms that support the game.
     * - - - - `array $unsupported` - A list of unsupported families & platforms.
     * - - - - - `array $families` - Platform families that don't support the game.
     * - - - - - `array $platforms` - Platforms that don't support the game.
     */
    define("SHIFT_GAMES", $gameList);
  })();

  /**
   * The duration of New and Expiring SHiFT Events in Hours
   */
  define("SHIFT_EVENT_DURATION", 36);
  // SQL Expiration Date conditionals
  (function () use (&$_mysqli) {
    /**
     * The DateTime format to use
     */
    $format = $_mysqli->dateFormats['dateTime'];

    $current_time = new DateTime();

    $now = clone $current_time;
    $now = $now->format($format);

    $releaseThreshold = clone $current_time;
    $releaseThreshold->sub(new DateInterval("PT" . SHIFT_EVENT_DURATION . "H"));
    $releaseThreshold = $releaseThreshold->format($format);

    $expirationThreshold = clone $current_time;
    $expirationThreshold->add(new DateInterval("PT" . SHIFT_EVENT_DURATION . "H"));
    $expirationThreshold = $expirationThreshold->format($format);

    $userID = auth_user_id();

    $dates = [];
    $dates['active'] = "expiration_date >= '{$now}' OR (expiration_date IS NULL OR expiration_date = '9999-12-31 23:59:59')";
    $dates['expired'] = "expiration_date < '{$now}'";
    $dates['new'] = "({$dates['active']}) AND '{$releaseThreshold}' <= release_date";
    $dates['expiring'] = "(expiration_date >= '{$now}') AND '{$expirationThreshold}' >= expiration_date";

    /**
     * SHiFT Expiration Date Conditionals for use in SQL Queries
     */
    define("SHIFT_DATES", $dates);
  })();
?>