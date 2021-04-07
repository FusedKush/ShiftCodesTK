<?php
  // SHiFT Code Management
  (function () {
    /**
     * The primary SHiFT Code Management Class
     */
    class ShiftCodes {
      /**
       * @var array A list of supported SHiFT Code Platform Families and their platforms
       * - See `$GAME_SUPPORT` for a full list of supported games and their platform support.
       * - Each platform family is an `associative array` in the following format:
       * - - `array $familyID` - The ID of the Platform, containing all of the information about the platform family.
       * - - - `string $display_name` - The _display name_ of the platform family.
       * - - - `array $platforms` - The platforms that belong to the platform family, and their associated information.
       * - - - - `string $display_name` - The _display name_ of the platform.
       * - - - - `array $supported_games` - The games that are supported by the platform.
       */
      public const PLATFORM_SUPPORT = [
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
      ];
      /**
       * @var array A list of supported SHiFT Code games
       * - See `$GAME_SUPPORT` for the full list of supported games and their platform support.
       * - Each supported game is an `associative array` in the following format:
       * - - `string key` — The GameID of the Game
       * - - - `string $name` — The Display Name of the Game.
       * - - - `string $long_name` — The Expanded Display Name of the Game. If omitted, the value of `$name` will be inherited when compiled to `$GAME_SUPPORT`
       */
      public const GAME_SUPPORT = [
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
      /**
       * @var int Indicates how long, in _hours_, that **New** and **Expiring** SHiFT Code Flags should be active for relative to their respective events.
       * - This is the number of hours since the *Release Date* of the SHiFT Code for the **New** Flag to be active.
       * - This is the number of hours until the *Expiration Date* of the SHiFT Code for the **Expiring** Flag to be active.
       */
      public const FLAG_DURATION = 36;

      /**
       * @var array A compiled list of supported SHiFT Code games and their associated platform support
       * - The values of this list are compiled from `PLATFORM_SUPPORT` and `GAME_SUPPORT`.
       * - Each game is an `associative array` in the following format:
       * - - `string key` — The GameID of the Game
       * - - - `string $name` — The Display Name of the Game
       * - - - `string $long_name` — The Expanded Display Name of the Game
       * - - - `array $support` - A list of `$PLATFORMS` support.
       * - - - - `array $supported` - A list of supported families & platforms.
       * - - - - - `array $families` - Platform families that support the game.
       * - - - - - `array $platforms` - Platforms that support the game.
       * - - - - `array $unsupported` - A list of unsupported families & platforms.
       * - - - - - `array $families` - Platform families that don't support the game.
       * - - - - - `array $platforms` - Platforms that don't support the game.
       */
      public static $GAME_SUPPORT = [];
      /**
       * @var array SHiFT Code date conditionals for filtering.
       * - *active* - Filters SHiFT Codes that are currently *active*.
       * - *expired* - Filters SHiFT Codes that are currently *expired*.
       * - *new* - Filters SHiFT Codes that currently have the *new* flag.
       * - *expiring* - Filters SHiFT Codes that currently have the *expiring* flag.
       */
      public static $DATE_CONDITIONS = [];

      /**
       * @var null|object The active instance of `ShiftCodes`, or **null** if one has not been initialized yet.
       */
      private static $instance = null;

      public $search_settings = [

      ];
      public $lastCodeResult = [];
      public $lastCodes = [];

  
      /**
       * Retrieves the instance of the `ShiftCodes` class
       * @return object Returns the `ShiftCodes` class instance
       */
      public static function getInstance () {
        if (self::$instance === null) {
          self::$instance = new ShiftCodes();
        }
  
        return self::$instance;
      }
      public function getCodes ($options) {
        GLOBAL $_mysqli;

        /** The response object */
        $response = new ResponseObject();
        /** The compiled and validated option list */
        $optionsList = (function () use (&$response, $options) {
          $validations = [
            /**
             * The `game_id` of the game to filter the codes by
             */
            'game' => new ValidationProperties([
              'value'       => false,
              'type'        => 'boolean|string',
              'validations' => [
                'match'        => array_merge(array_keys(self::$GAME_SUPPORT), [ false ])
              ]
            ]),
            /**
             * The `user_id` of the owner to filter the codes by
             */
            'owner' => new ValidationProperties([
              'type'        => 'boolean|string',
              'value'       => false,
              'validations' => [
                'length' => [
                  'is' => 12
                ]
              ]
            ]),
            /**
             * The `code_id` of an active SHiFT Code to search for.
             * - If provided, overwrites the properties of `order`, `limit`, & `page`.
             */
            'code' => new ValidationProperties([
              'type'        => 'boolean|string',
              'value'       => false,
              'validations' => [
                'length' => [ 
                  'is' => 12
                ]
              ]
            ]),
            /**
             * The order in which to sort the SHiFT Codes
             */
            'order' => new ValidationProperties([
              'type'        => 'string',
              'value'       => 'default',
              'validations' => [
                'match' => [
                  'default',
                  'newest', 
                  'oldest'
                ]
              ]
            ]),
            /**
             * The flags(s) in which to filter the SHiFT Codes
             */
            'status' => new ValidationProperties([
              'type'         => 'array',
              'value'        => [ 'active' ],
              'validations'  => [
                'match' => [
                  'active', 
                  'expired',
                  'hidden',
                  'new', 
                  'expiring'
                ]
              ]
            ]),
            /**
            * The **PLATFORM_SUPPORT** *Platform ID* of a platform in which to filter the SHiFT Codes
            */
            'platform' => new ValidationProperties([
              'type'         => 'boolean|string',
              'value'        => false,
              'validations'  => [
                'match' => (function () {
                  $matches = [
                    false
                  ];

                  foreach (self::PLATFORM_SUPPORT as $familyID => $familyData) {
                    foreach ($familyData['platforms'] as $platformID => $platformData) {
                      $matches[] = $platformID;
                    }
                  }

                  return $matches;
                })()
              ]
            ]),
            /**
            * The maximum number of SHiFT Codes to be retrieved
            */
            'limit' => new ValidationProperties([
              'type'        => 'integer',
              'value'       => 1,
              'validations' => [
                'length' => [
                  'min' => 1, 
                  'max' => 100
                ]
              ]
            ]),
            /**
            * The current page number
            */
            'page' => new ValidationProperties([
              'type'        => 'integer',
              'value'       => 1,
              'validations' => [
                'length' => [
                  'min' => 1
                ]
              ]
            ]),
            /** 
             * Indicates if the `Result Set Data` for the result set should be retrieved
             * - Can by retrieved via the `result_set` property of the response payload.
             **/
            'get_result_set_data' => new ValidationProperties([
              'type'        => 'boolean',
              'value'       => false
            ]),
            /** 
             * Indicates if the `Flag Counts` for the SHiFT Codes should be retrieved
             * - Can by retrieved via the `flag_counts` property of the response payload.
             **/
            'get_flag_counts' => new ValidationProperties([
              'type'        => 'boolean',
              'value'       => false
            ]),
            /** Indicates if the full `Response Object` should be returned, instead of just the response payloads. */
            'return_full_response' => new ValidationProperties([
              'type'        => 'boolean',
              'value'       => false
            ])
          ];

          $checked = check_parameters($options, $validations);

          foreach ($checked['warnings'] as $warning) {
            $response->setWarning($warning);
          }
          foreach ($checked['errors'] as $error) {
            $response->setError($error);
          }

          if (!$checked['valid']) {
            return false;
          }

          return $checked['parameters'];
        })();

        if ($optionsList) {
        /**
         * The SQL Query Statement
         */
        $query = (function () use ($optionsList) {
          /**
          * The Redemption ID of the user
          */
          $redemptionID = redemption_get_id();
          $fields = [
            "sc.code_id",
            "sc.owner_id",
            "sc.code_state",
            "scd.code_hash",
            "scd.game_id",
            "au.username as 'owner_username'",
            "scd.reward",
            "scd.source",
            "scd.release_date",
            "scd.expiration_date",
            "sc.creation_time",
            "sc.creation_time as 'creation_date'",
            "sc.update_time as 'last_update'",
            "scd.timezone",
            "scd.notes",
            "scd.platforms",
            "scd.shift_codes",
            "CASE 
              WHEN EXISTS(
                SELECT scr.id
                FROM shift_codes_redeemed as scr

                WHERE
                  scr.code_hash = scd.code_hash
                  AND scr.redemption_id = '{$redemptionID}'
                LIMIT 1
              )
              THEN 1
              ELSE 0
            END as 'is_redeemed'"
          ];
          $select = "" . implode(", ", $fields) . "";
          $where = (function () use ($optionsList) {
            $filter = (function () use ($optionsList) {
              $filter = $optionsList['status'];
              $eventFilterIsPresent = array_search('new', $filter) !== false || array_search('expiring', $filter) !== false;

              $filter = array_filter($filter, function ($value, $key) use ($eventFilterIsPresent) {
                if ($eventFilterIsPresent) {
                  if ($value == 'active' || $value == 'inactive') {
                    return false;
                  }
                }

                return true;
              }, ARRAY_FILTER_USE_BOTH);

              return $filter;
            })();
            $str = '';

            // Filter by Code 
            if ($optionsList['code']) {
              $str .= "(sc.code_id = '{$optionsList['code']}'";

              if (!auth_isLoggedIn() || !auth_user_roles()['admin']) {
                $str .= " AND (sc.code_state != 'hidden'";

                // Logged in but not Admin
                if (auth_isLoggedIn()) {
                  $userID = auth_user_id();

                  $str .= " OR sc.owner_id = '{$userID}'";
                }

                $str .= ")";
              }
              $str .= ") OR";
            }
            // Filter by State
            (function () use (&$str, $filter) {
              $showActive = array_search('active', $filter) !== false || array_search('expired', $filter) !== false;
              $showHidden = array_search('hidden', $filter) !== false;

              if ($showActive || $showHidden) {
                $str .= "(";

                if ($showActive) {
                  $str .= "sc.code_state = 'active'";
                }      
                if ($showHidden) {
                  if ($showActive) {
                    $str .= " OR ";
                  }
                  if (auth_isLoggedIn()) {
                    $str .= "(sc.code_state = 'hidden'";
                    
                    if (!auth_user_roles()['admin']) {
                      $userID = auth_user_id();
                      $str .= " AND owner_id = '{$userID}'";
                    }
          
                    $str .= ")";
                  }
                }

                $str .= ") AND";
              }
            })();
            // Filter by Expiration State
            if (count($filter) > 0) {
              if (count($filter) > 1 || $filter[0] != 'hidden') {
                $str .= "(";
          
                foreach ($filter as $i => $key) {
                  if ($key == 'hidden') {
                    continue;
                  }
          
                  $str .= "(" . self::$DATE_CONDITIONS[$key] . ") ";
          
                  if (count($filter) > 1 && $i != count($filter) - 1) {
                    $str .= "OR ";
          
                    // if ($key == 'active' && !array_search('inactive', $filter) || $key == 'inactive') {
                    //   $str .= "AND ";
                    // } 
                    // else {
                    //   $str .= "OR ";
                    // }
                  }
                }
          
                $str = preg_replace('/ (?:OR|AND) $/', '', $str);
                $str .= ") AND ";
              }
            }
            // Filter by Game ID
            if ($optionsList['game'] != false) {
              $str .= "(scd.game_id = '{$optionsList['game']}') AND ";
            }
            // Filter by Owner
            if ($optionsList['owner']) {
              $str .= "(sc.owner_id = '{$optionsList['owner']}') AND ";
            }

            if ($str) {
              return preg_replace('/(AND|OR)( ){0,1}$/', '', $str);
            } 
            else {
              return "1";
            }
          })();
          $order = (function () use ($optionsList) {
            $cleanCodeID = clean_sql($optionsList['code']);
            // SHiFT Code ordering statements
            $options = [
              'default' =>
                "CASE WHEN " . self::$DATE_CONDITIONS['active'] . " THEN 1
                    ELSE 0
                  END DESC,
                  CASE WHEN " . self::$DATE_CONDITIONS['expiring'] . " THEN
                      CASE WHEN " . self::$DATE_CONDITIONS['new'] . " THEN 2
                        ELSE 1
                      END
                      ELSE 0
                  END DESC,
                  CASE WHEN " . self::$DATE_CONDITIONS['new'] . " THEN 1
                      ELSE 0
                      END DESC,
                  -(expiration_date IS NULL) DESC,
                  release_date DESC,
                  expiration_date ASC",
              'newest' =>
              'release_date DESC',
              'oldest' =>
              'release_date ASC'
            ];
            $str = '';

            if ($cleanCodeID) {
              $str .= "CASE sc.code_id
                            WHEN '$cleanCodeID'
                            THEN 0
                            ELSE 1
                        END,";
            }

            $str .= $options[$optionsList['order']];

            return $str;
          })();
          $limit = clean_sql($optionsList['limit']);
          $offset = clean_sql(($optionsList['page'] - 1) * $optionsList['limit']);
          $count = (function () use ($optionsList, $where, $limit) {
            $str = '';

            if ($optionsList['get_flag_counts']) {
              $whereCondition = $where;
              // $whereCondition = str_replace('(' . self::$DATE_CONDITIONS['new'] . ')', '', $whereCondition);
              // $whereCondition = str_replace('(' . self::$DATE_CONDITIONS['expiring'] . ')', '', $whereCondition);
              // $whereCondition = preg_replace('/(^|AND|OR)(\s+)(AND|OR|$)/', '', $whereCondition);
              $codeIDCondition = $optionsList['game'] != false
                                ? " AND scd.game_id = '{$optionsList['game']}'"
                                : '';
              
              $str .= ', ';
              // New Count
              $str .= "(SELECT COUNT(sc.id)
                        FROM shift_codes
                          AS sc
                        LEFT JOIN
                          shift_code_data
                            AS scd
                            ON sc.code_id = scd.code_id
                        WHERE " . self::$DATE_CONDITIONS['new'] . "
                          {$codeIDCondition})
                          AS 'count_new', ";
              // Expiring Count
              $str .= "(SELECT COUNT(sc.id)
                        FROM shift_codes
                          AS sc
                        LEFT JOIN
                          shift_code_data
                            AS scd
                            ON sc.code_id = scd.code_id
                        WHERE " . self::$DATE_CONDITIONS['expiring'] . "
                          {$codeIDCondition})
                          AS 'count_expiring'";
            }
            
            return $str;
          })();
          $search = (function () {

          })();

          return "SELECT {$select} {$count}
                    FROM shift_codes
                        AS sc
                    LEFT JOIN shift_code_data
                        AS scd
                        ON sc.code_id = scd.code_id
                    LEFT JOIN auth_users
                        AS au
                        ON sc.owner_id = au.user_id
                    WHERE {$where}
                    GROUP BY sc.id
                    ORDER BY {$order}
                    LIMIT {$limit}
                    OFFSET {$offset}";
        })();

        /**
        * The retrieved list of SHiFT Codes
        */
        $result = $_mysqli->query($query, [ 'getResultSetData' => $optionsList['get_result_set_data'] ]);

        if ($result === false) {
          $response->fatalError(-3, [
            'type'       => 'Server Query Error',
            'status_code' => $_mysqli->con->errno
          ]);
          exit;
        }

        // Build payload
        (function () use (&$response, $result, $optionsList) {
          // Result Set Data
          if ($optionsList['get_result_set_data'] && isset($result['result_set'])) {
            $response->setPayload($result['result_set'], 'result_set');
          }
          // Total SHiFT Code count
          (function () use ($result, &$response, $optionsList) {
            if ($optionsList['get_flag_counts']) {
              $types = ['new', 'expiring'];
              $counts = array_fill_keys($types, 0);
              $firstResult = $optionsList['get_result_set_data']
                               ? $result['result'][0] ?? false
                               : $result[0] ?? false;

              if ($firstResult) {
                foreach ($types as $type) {
                  $counts[$type] = (int) $firstResult["count_{$type}"];
                }
              }
          
              $response->setPayload($counts, 'flag_counts');
            }
          })();
          // SHiFT Codes
          (function () use ($result, $optionsList, &$response) {
            // SHiFT Code payload
            $shiftCodes = [];
            /**
            * SHiFT Code Response array 
            * 
            * - Passing a `string` as a value will use the string as the key for the value from the database.
            * - Passing **true** will use the key as the key for the value from the database.
            * - Passing **NULL** will prevent any values from being filled in.
            **/
            $shiftCodeTemplate = [
              'properties' => [
                'code'        => [
                  'id'           => 'code_id',
                  'state'        => 'code_state',
                  'hash'         => 'code_hash'
                ],
                'owner'       => [
                  'id'           => 'owner_id',
                  'username'     => 'owner_username'
                ],
                'game_id'     => true
              ],
              'info' => [
                'reward'          => true,
                'source'          => [
                  'type'             => NULL,
                  'value'            => 'source'
                ],
                'release_date'    => [
                  'type'             => NULL,
                  'value'            => 'release_date'
                ],
                'expiration_date' => [
                  'type'             => NULL,
                  'value'            => 'expiration_date'
                ],
                'creation_date'   => true,
                'last_update'     => true,
                'timezone'        => true,
                'notes'           => true
              ],
              'codes' => [
                'platforms'   => NULL,
                'shift_codes' => NULL
              ],
              'states' => [
                'code'    => [
                  'isActive'             => NULL,
                  'isNew'                => NULL,
                  'isExpiring'           => NULL,
                  'wasRecentlySubmitted' => NULL,
                  'wasRecentlyUpdated'   => NULL
                ],
                'user'    => [
                  'isOwner'     => NULL,
                  'canEdit'     => NULL,
                  'hasRedeemed' => NULL
                ]
              ],
            ];
            $resultList = $optionsList['get_result_set_data']
                          ? $result['result'] ?? []
                          : $result ?? [];

            foreach ($resultList as $code) {
              $shiftCode = [];
          
              // Create SHiFT Code Response Array
              (function () use (&$shiftCode, $shiftCodeTemplate, $code) {
                $processArray = function (&$parent, $key, $array) use (&$processArray, $code) {
                  $parent[$key] = [];

                  foreach ($array as $arrayKey => $arrayValue) {
                    if (is_string($arrayValue)) {
                      $parent[$key][$arrayKey] = $code[$arrayValue] ?? null;
                    }
                    else if ($arrayValue === true) {
                      $parent[$key][$arrayKey] = $code[$arrayKey] ?? null;
                    }
                    else if ($arrayValue === NULL) {
                      $parent[$key][$arrayKey] = NULL;
                    }
                    else if (is_array($arrayValue)) {
                      $processArray($parent[$key], $arrayKey, $arrayValue);
                    }
                  }
                };
                
                foreach ($shiftCodeTemplate as $section => $fields) {
                  $processArray($shiftCode, $section, $fields);
                }
              })();

              // Source
              (function () use (&$shiftCode, $code) {
                $source = $code['source'];
                $resultSource = &$shiftCode['info']['source'];

                if ($source !== null) {
                  if (check_url($source)) {
                    $resultSource['type'] = 'online';
                  }
                  else {
                    $resultSource['type'] = 'physical';
                  }

                  $resultSource['value'] = $source;
                }
                else {
                  $resultSource = [
                    'type' => 'none',
                    'value' => NULL
                  ];
                }
              })(); 
              // Release Date
              (function () use (&$shiftCode, $code) {
                $date = $code['release_date'];
                $resultDate = &$shiftCode['info']['release_date'];

                if ($date !== NULL) {
                  $resultDate['type'] = 'date';
                }
                else {
                  $resultDate['type'] = 'none';
                }
              })(); 
              // Expiration Date
              (function () use (&$shiftCode, $code) {
                $date = $code['expiration_date'];
                $resultDate = &$shiftCode['info']['expiration_date'];

                if ($date !== NULL) {
                  if ($date == '9999-12-31T23:59:59+00:00') {
                    $resultDate['type'] = 'infinite';
                  }
                  else {
                    if (strpos($date, 'T07:59:59') !== false) {
                      $resultDate['type'] = 'through';
                    }
                    else {
                      $resultDate['type'] = 'until';
                    }
                  }
                }
                else {
                  $resultDate['type'] = 'none';
                }
              })(); 
              // Platforms
              (function () use (&$shiftCode, $code) {
                $platforms = $code['platforms'];

                if ($platforms) {
                  $shiftCode['codes']['platforms'] = json_decode($platforms);
                }
              })(); 
              // Codes
              (function () use (&$shiftCode, $code) {
                $codes = $code['shift_codes'];

                if ($codes) {
                  $shiftCode['codes']['shift_codes'] = json_decode($codes, true);
                }
              })(); 
              // States
              (function () use (&$shiftCode, $code) {
                $states = &$shiftCode['states'];

                // Code States
                (function () use ($code, &$states) {
                  $codeStates = &$states['code'];
                  
                  // Dates
                  (function () use ($code, &$codeStates) {
                    $interval = new DateInterval('PT' . self::FLAG_DURATION . 'H');
                
                    // New
                    (function () use ($code, $interval, &$codeStates) {
                      $release = $code['release_date'];
                
                      $codeStates['isNew'] = false;
            
                      if ($release) {
                        $threshold = new DateTime($release);
                        $threshold->add($interval);
                        $threshold = $threshold->getTimestamp();
                
                        $codeStates['isNew'] = $threshold > time();
                      }
                    })();
                    // Active, Expiring
                    (function () use ($code, $interval, &$codeStates) {
                      $expiration = $code['expiration_date'];
                      $timezone = new DateTimeZone($code['timezone'] ?? 'UTC');
            
                      $codeStates['isActive'] = true;
                      $codeStates['isExpiring'] = false;
                      
                      if ($expiration) {
                        // Adjusted expiration time
                        $expirationTime = new DateTime($expiration);
                        $expirationTime->setTimeZone($timezone);
                        $expirationTime = $expirationTime->format('c');
                        // codeIsExpiring threshold
                        $expiringThreshold = new DateTime($expiration);
                        $expiringThreshold->sub($interval);
                        $expiringThreshold = $expiringThreshold->getTimestamp();
                        // codeIsActive threshold
                        $activeThreshold = new DateTime($expiration);
                        $activeThreshold = $activeThreshold->getTimestamp();
            
                        // $shiftCode['states']['expiration_date'] = $expirationTime;
                        $codeStates['isActive'] = $activeThreshold > time();
                        $codeStates['isExpiring'] = $expiringThreshold < time();
                      }
                    })();
                  })();
                  // Recent Events
                  (function () use ($code, &$codeStates) {
                    $threshold = new DateTime();
                    $threshold->sub(new DateInterval('PT15M'));
                    $threshold = $threshold->getTimestamp();
          
                    // RecentlySubmitted
                    (function () use ($code, $threshold, &$codeStates) {
                      $creation = new DateTime($code['creation_time']);
                      $creation = $creation->getTimestamp();
          
                      $codeStates['wasRecentlySubmitted'] = $creation > $threshold;
                    })();
                    // RecentlyUpdated
                    (function () use ($code, $threshold, &$codeStates) {
                      $updated = new DateTime($code['last_update']);
                      $updated = $updated->getTimestamp();
          
                      $codeStates['wasRecentlyUpdated'] = $updated > $threshold;
                    })();
                  })();
                })();
                // User States
                (function () use ($code, &$states) {
                  $ownerID = $code['owner_id'];
                  $user = &$states['user'];
                  
                  // Redemption state
                  $user['hasRedeemed'] = ($code['is_redeemed'] ?? -1) == 1;
                  // Owner Permission
                  $user['isOwner'] = ($ownerID == auth_user_id());
                  // Editing Permission
                  $user['canEdit'] = ($ownerID == auth_user_id() || auth_user_roles()['admin']);
                })(); 
              })();

              $shiftCodes[] = $shiftCode;
            }

            $response->setPayload($shiftCodes, 'shift_codes');
          })();
        })();
        }
        // Parameter errors are present
        else {
          $response->set(-1);
        }

        $this->lastCodeResult = $response;
        $this->lastCodes = &$this->lastCodeResult->payload['shift_codes'];
        
        if (!$optionsList['return_full_response']) {
          return $response->getPayloads();
        }
        else {
          return $response;
        }
      }
      public function getSocialMediaPosts ($shiftCode = false) {
        if ($shiftCode === false) {
          if ($this->lastCodes && count($this->lastCodes) > 0) {
            $shiftCode = $this->lastCodes[0];
          }
        }

        $posts = [];
        $bindings = [
          '${game}' => (function () use ($shiftCode) {
            $gameID = $shiftCode['properties']['game_id'];

            return self::$GAME_SUPPORT[$gameID]['name'];
          })(),
          '${expiration}' => (function () use ($shiftCode) {
            $expiration = $shiftCode['info']['expiration_date'];
            $timezone = $shiftCode['info']['timezone'];
            $type = $expiration['type'];

            if ($type == 'through' || $type == 'until') {
              $date = new DateTime($expiration['value']);

              $date->setTimezone(new DateTimeZone($timezone));

              if ($type == 'through') {
                return $date->format('M d, Y');
              }
              else if ($type == 'until') {
                return $date->format('M d, Y h:i A T');
              }
            }
            else if ($type == 'infinite') {
              return 'Never Expires';
            }
            else if ($type == 'none') {
              return 'Unknown';
            }
          })(),
          '${reward}' => $shiftCode['info']['reward'],
          '${source}' => (function () use ($shiftCode) {
            $source = $shiftCode['info']['source'];
            $type = $source['type'];

            if ($type != 'none') {
              return $source['value'];
            }
            else {
              return 'None Provided';
            }
          })(),
          '${redemption_info}' => (function () use ($shiftCode) {
            $codeList = $shiftCode['codes']['shift_codes'];
            $redemptionInfo = '';
           
            if (isset($codeList['universal'])) {
              $redemptionInfo .= "All Platforms: {$codeList['universal']}";
            }
            else {
              $platformList = $shiftCode['codes']['platforms'];

              foreach ($platformList as $platformFamily => $familyPlatformList) {
                $familyPlatformName = '';

                foreach ($familyPlatformList as $platformID) {
                  $platformDisplayName = self::PLATFORM_SUPPORT[$platformFamily]['platforms'][$platformID]['display_name'];

                  $familyPlatformName .= "{$platformDisplayName} / ";
                }

                $familyPlatformName = preg_replace('/ \/ $/', '', $familyPlatformName);

                $redemptionInfo .= "{$familyPlatformName}: {$codeList[$platformFamily]}\r\n";
              }

              $redemptionInfo = trim($redemptionInfo);
            }

            return $redemptionInfo;
          })(),
          '${notes}' => (function () use ($shiftCode) {
            $source = $shiftCode['info']['notes'];

            if ($source) {
              return $source;
            }
            else {
              return '';
            }
          })(),
          '${share_link}' => (function () use ($shiftCode) {
            $gameID = $shiftCode['properties']['game_id'];
            $domain = \ShiftCodesTK\SITE_DOMAIN;

            return "{$domain}/{$gameID}";
          })()
        ];
        $templatePath = \ShiftCodesTK\PRIVATE_PATHS['php'] . '/shift_social_media_templates/';
        $templateList = [
          'twitter_retweet',
          'twitter_tweet',
          'facebook'
        ];

        var_dump($bindings);

        foreach ($templateList as $template) {
          $post = file_get_contents("{$templatePath}{$template}.txt");

          if ($bindings['${notes}'] === '') {
            $post = preg_replace('/\r\n\$\{notes\}\r\n/', '', $post);
            // $post = preg_replace("/\r\n\$\{notes\}\r\n/", '', $post);
          }

          $post = str_replace(
            array_keys($bindings),
            array_values($bindings),
            $post
          );

          $posts[$template] = $post;
        }

        $posts['image_link'] = "/assets/img/rewards/{$shiftCode['properties']['game_id']}.jpg";

        return $posts;
      }

      /**
       * Initialize the `ShiftCodes` class.
       * @return void 
       */
      private function __construct () {
        GLOBAL $_mysqli;

        // Compile game support list
        $this::$GAME_SUPPORT = (function () {
          $gameList = $this::GAME_SUPPORT;

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

              foreach ($this::PLATFORM_SUPPORT as $familyID => $familyInfo) {
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

          return $gameList;
        })();
        // Compile Conditionals List
        $this::$DATE_CONDITIONS = (function () use (&$_mysqli) {
          /** The date conditionals list */
          $dates = [];

          /** The DateTime format to use for the queries */
          $format = $_mysqli->dateFormats['dateTime'];

          /** The current `DateTime` */
          $current_time = new DateTime();

          /** The current formatted timestamp */
          $now = clone $current_time;
          $now = $now->format($format);

          /** The threshold for SHiFT Codes to have the **New** Flag */
          $newThreshold = clone $current_time;
          $newThreshold->sub(new DateInterval("PT" . $this::FLAG_DURATION . "H"));
          $newThreshold = $newThreshold->format($format);

          /** The threshold for SHiFT Codes to have the **Expiring** Flag */
          $expiringThreshold = clone $current_time;
          $expiringThreshold->add(new DateInterval("PT" . $this::FLAG_DURATION . "H"));
          $expiringThreshold = $expiringThreshold->format($format);

          /** The `User ID` of the current user, if available */
          $userID = auth_user_id();
          

          $dates['active'] = "expiration_date >= '{$now}' OR (expiration_date IS NULL OR expiration_date = '9999-12-31 23:59:59')";
          $dates['expired'] = "expiration_date < '{$now}'";
          $dates['new'] = "({$dates['active']}) AND '{$newThreshold}' <= release_date";
          $dates['expiring'] = "(expiration_date >= '{$now}') AND '{$expiringThreshold}' >= expiration_date";

          return $dates;
        })();
      }
    }
  })();
?>