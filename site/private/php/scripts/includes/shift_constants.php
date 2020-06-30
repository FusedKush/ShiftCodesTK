<?php
/**
 * SHiFT Games
 * - Each game is an associative array of the following format:
 * - - `string key` — The GameID of the Game
 * - - - `string $name` — The Display Name of the Game
 * - - - `string $long_name` — The Expanded Display Name of the Game
 */
  define("SHIFT_GAMES", [
    'bl2' => [
      'name'      => 'Borderlands 2',
      'long_name' => 'Borderlands 2'
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
      'long_name' => 'Borderlands 3'
    ]
  ]);
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
    $dates['active'] = "expiration_date >= '{$now}' OR expiration_date IS NULL";
    $dates['expired'] = "expiration_date < '{$now}'";
    $dates['new'] = "({$dates['active']}) AND '{$releaseThreshold}' <= release_date";
    $dates['expiring'] = "(expiration_date >= '{$now}') AND '{$expirationThreshold}' >= expiration_date";

    /**
     * SHiFT Expiration Date Conditionals for use in SQL Queries
     */
    define("SHIFT_DATES", $dates);
  })();
  /**
   * Supported SHiFT Code Platforms
   */
  define("SHIFT_CODE_PLATFORMS", (function () {
    $shiftCodePlatforms = [];
    $defs = [
      'pc'   => [
        'pc'      => 'PC',
        'pc_vr'   => 'PC VR',
        'mac'     => 'Mac',
        'linux'   => 'Linux'
      ],
      'xbox' => [
        'xb360'   => 'Xbox 360',
        'xb1'     => 'Xbox One'
      ],
      'ps'   => [
        'ps3'     => 'PS3',
        'ps_vita' => 'PS Vita',
        'ps4'     => 'PS4',
        'ps_vr'   => 'PS VR'
      ]
    ];
    $games = [
      'pc'    => [
        'pc'      => [ 'bl3', 'bl1', 'tps', 'bl2' ],
        'pc_vr'   => [ 'bl2' ],
        'mac'     => [ 'bl3', 'tps', 'bl2' ],
        'linux'   => [ 'tps' ]
      ],
      'xbox'  => [
        'xb360'   => [ 'tps', 'bl2' ],
        'xb1'     => [ 'bl3', 'bl1', 'tps', 'bl2' ],
      ],
      'ps'    => [
        'ps3'     => [ 'tps', 'bl2' ],
        'ps_vita' => [ 'bl2' ],
        'ps4'     => [ 'bl3', 'bl1', 'tps', 'bl2' ],
        'ps_vr'   => [ 'bl2' ]
      ]
    ];

    foreach ($defs as $category => $platforms) {
      foreach ($platforms as $name => $displayName) {
        $shiftCodePlatforms[$category][$name] = [
          'display_name' => $displayName,
          'supported_games' => $games[$category][$name]
        ];
      }
    }

    return $shiftCodePlatforms;
  })());
?>