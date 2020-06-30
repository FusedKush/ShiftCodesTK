<?php
// require_once('../../../../initialize.php');
require_once(SCRIPTS_PATH . 'includes/shift_constants.php');

/**
 * JSON response
 */
$response = new ResponseObject();
/**
 * Request parameters & parameter validation
 */
$params = (function () use (&$response) {
  $validations = [
    /**
     * The game_id of the game to filter the codes by
     * string [ all | bl3 | bl1 | bl2 | tps ]
     */
    'game' => new ValidationProperties([
      'required'    => true,
      'type'        => 'string',
      'validations' => [
        'match' => array_merge(array_keys(SHIFT_GAMES), ['all'])
      ]
    ]),
    /**
     * The user_id of an owner to filter the codes by
     * string [ 8 ] | false
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
     * The code_id of an active SHiFT Code to include in the results
     * string [ 8 ] | false
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
     * string [ default | new | old ]
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
     * The event(s) in which to filter the SHiFT Codes
     * Array [ '' | new | expiring ]
     */
    'filter' => new ValidationProperties([
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
     * The maximum number of SHiFT Codes to be retrieved
     * Int [ 0 - 100 ]
     */
    'limit' => new ValidationProperties([
      'type'        => 'integer',
      'value'       => 10,
      'validations' => [
        'length' => [
          'min' => 1, 
          'max' => 50
        ]
      ]
    ]),
    /**
     * The offset number
     * Int [ 0 - any ]
     */
    'offset' => new ValidationProperties([
      'type'        => 'integer',
      'value'       => 0,
      'validations' => [
        'length' => [
          'min' => 0
        ]
      ],
      'emptyWarning' => true
    ])
  ];

  $checked = check_parameters($_GET, $validations);

  foreach ($checked['warnings'] as $warning) {
    $response->setWarning($warning);
  }
  foreach ($checked['errors'] as $error) {
    $response->setError($error);
  }

  // Parameter errors are present
  if (!$checked['valid']) {
    $response->set(-1);
    $response->send();
    exit;
  }
  
  return $checked['parameters'];
})();
/**
 * The SQL Query Statement
 */
$query = (function () use ($params) {
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
    "sc.update_time as 'last_update'",
    "scd.timezone",
    "scd.notes",
    "scd.platforms_pc",
    "scd.code_pc",
    "scd.platforms_xbox",
    "scd.code_xbox",
    "scd.platforms_ps",
    "scd.code_ps",
    "CASE 
      WHEN EXISTS(
        SELECT id
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
  $where = (function () use ($params) {
    $filter = (function () use ($params) {
      $filter = $params['filter'];
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
    if ($params['code']) {
      $str .= "(sc.code_id = '{$params['code']}'";

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
  
          $str .= "(" . SHIFT_DATES[$key] . ") ";
  
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
    if ($params['game'] != 'all') {
      $str .= "(scd.game_id = '{$params['game']}') AND ";
    }
    // Filter by Owner
    if ($params['owner']) {
      $str .= "(sc.owner_id = '{$params['owner']}') AND ";
    }

    if ($str) {
      return preg_replace('/(AND|OR)( ){0,1}$/', '', $str);
    } 
    else {
      return "1";
    }
  })();
  $order = (function () use ($params) {
    $cleanCodeID = clean_sql($params['code']);
    // SHiFT Code ordering statements
    $options = [
      'default' =>
         "CASE WHEN " . SHIFT_DATES['active'] . " THEN 1
            ELSE 0
          END DESC,
          CASE WHEN " . SHIFT_DATES['expiring'] . " THEN
              CASE WHEN " . SHIFT_DATES['new'] . " THEN 2
                ELSE 1
              END
              ELSE 0
          END DESC,
          CASE WHEN " . SHIFT_DATES['new'] . " THEN 1
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

    $str .= $options[$params['order']];

    return $str;
  })();
  $limit = clean_sql($params['limit']);
  $offset = clean_sql($params['offset']);
  $count = (function () use ($params, $where, $limit) {
    $str = '';

    if ($limit > 1) {
      $whereCondition = $where;
      // $whereCondition = str_replace('(' . SHIFT_DATES['new'] . ')', '', $whereCondition);
      // $whereCondition = str_replace('(' . SHIFT_DATES['expiring'] . ')', '', $whereCondition);
      // $whereCondition = preg_replace('/(^|AND|OR)(\s+)(AND|OR|$)/', '', $whereCondition);
      $codeIDCondition = $params['game'] != 'all'
                         ? " AND scd.game_id = '{$params['game']}'"
                         : '';
      
      $str .= ', ';
      // Total Count
      $str .= "(SELECT COUNT(sc.id)
                FROM shift_codes
                  AS sc
                LEFT JOIN
                  shift_code_data
                    AS scd
                    ON sc.code_id = scd.code_id
                WHERE {$whereCondition})
                  AS 'count_total', ";
      // New Count
      $str .= "(SELECT COUNT(sc.id)
                FROM shift_codes
                  AS sc
                LEFT JOIN
                  shift_code_data
                    AS scd
                    ON sc.code_id = scd.code_id
                WHERE " . SHIFT_DATES['new'] . "
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
                WHERE " . SHIFT_DATES['expiring'] . "
                  {$codeIDCondition})
                  AS 'count_expiring'";
    }
    
    return $str;
  })();
  $search = (function () {

  })();

  return collapseWhitespace("SELECT {$select} {$count}
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
                             OFFSET {$offset}");
})();

$response->setPayload($query, '_query');
/**
 * The retrieved list of SHiFT Codes
 */
$result = $_mysqli->query($query);

if ($result === false) {
  $response->fatalError(-3, [
    'type'       => 'Server Query Error',
    'statusCode' => $_mysqli->con->errno
  ]);
  exit;
}

// Build payload
(function () use (&$response, $result, $params) {
  // Total SHiFT Code count
  (function () use (&$response, $result) {
    if (isset($result[0]['count_total'])) {
      $types = ['total', 'new', 'expiring'];
      $counts = array_fill_keys($types, 0);
      
      if (count($result) > 0) {
        foreach ($types as $type) {
          $counts[$type] = (int) $result[0]["count_{$type}"];
        }
      }
  
      $response->setPayload($counts, 'counts');
    }
  })();
  // SHiFT Codes
  (function () use (&$response, $result, $params) {
    // SHiFT Code payload
    $shiftCodes = [];
    // SHiFT Code array
    $shiftCodeTemplate = [
      'properties' => [
        'code_id',
        'code_state',
        'code_hash',
        'game_id',
        'owner_id',
        'owner_username'
      ],
      'info' => [
        'reward',
        'source',
        'release_date',
        'expiration_date',
        'last_update',
        'timezone',
        'notes'
      ],
      'codes' => [
        'platforms_pc',
        'code_pc',
        'platforms_xbox',
        'code_xbox',
        'platforms_ps',
        'code_ps'
      ],
      'states' => [
        'codeIsActive',
        'codeIsNew',
        'codeIsExpiring',
        'codeWasRecentlyAdded',
        'codeWasRecentlyUpdated',
        'userIsOwner',
        'userHasRedeemed',
        'userCanEdit'
      ]
    ];

    if ($params['code'] != false) {
      $response->setPayload(false, 'shift_code_search');
    }

    foreach ($result as $code) {
      $shiftCode = [];
  
      // States
      (function () use (&$code) {
        // Dates
        (function () use (&$code) {
          $interval = new DateInterval('PT' . SHIFT_EVENT_DURATION . 'H');
      
          // codeIsNew
          (function () use (&$code, $interval) {
            $release = $code['release_date'];
      
            $code['codeIsNew'] = false;
  
            if ($release) {
              $threshold = new DateTime($release);
              $threshold->add($interval);
              $threshold = $threshold->getTimestamp();
      
              $code['codeIsNew'] = $threshold > time();
            }
          })();
          // codeIsActive, codeIsExpiring
          (function () use (&$code, $interval) {
            $expiration = $code['expiration_date'];
            $timezone = new DateTimeZone($code['timezone'] ?? 'UTC');
  
            $code['codeIsExpiring'] = false;
            $code['codeIsActive'] = true;
            
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
  
              $code['expiration_date'] = $expirationTime;
              $code['codeIsExpiring'] = $expiringThreshold < time();
              $code['codeIsActive'] = $activeThreshold > time();
            }
          })();
        })();
        // Recent Events
        (function () use (&$code) {
          $threshold = new DateTime();
          $threshold->sub(new DateInterval('PT15M'));
          $threshold = $threshold->getTimestamp();

          // codeWasRecentlyAdded
          (function () use (&$code, $threshold) {
            $creation = new DateTime($code['creation_time']);
            $creation = $creation->getTimestamp();

            $code['codeWasRecentlyAdded'] = $creation > $threshold;
          })();
          // codeWasRecentlyUpdated
          (function () use (&$code, $threshold) {
            $updated = new DateTime($code['last_update']);
            $updated = $updated->getTimestamp();

            $code['codeWasRecentlyUpdated'] = $updated > $threshold;
          })();
        })();
        // User Permissions
        (function () use (&$code) {
          $owner = $code['owner_id'];
  
          $code['userIsOwner'] = ($owner == auth_user_id());
          $code['userCanEdit'] = ($owner == auth_user_id() || auth_user_roles()['admin']);
        })(); 
        // Redemption state
        (function () use (&$code) {
          $code['userHasRedeemed'] = $code['is_redeemed'] == 1;
        })(); 
      })();

      foreach ($shiftCodeTemplate as $section => $fields) {
        $shiftCode[$section] = [];

        foreach ($fields as $field) {
          $shiftCode[$section][$field] = $code[$field] ?? null;

          if (strpos($field, 'platforms') !== false) {
            (function () use (&$shiftCode, $section, $field) {
              $platforms = &$shiftCode[$section][$field];
              $platformList = explode('/', $platforms);
              
              $shiftCode[$section][$field] = (function () use ($field, $platforms, $platformList) {
                $newPlatformList = [];
                $platformCategory = str_replace('platforms_', '', $field);
                
                foreach ($platformList as $platform) {
                  $newValue = $platform;
                  $platformDef = SHIFT_CODE_PLATFORMS[$platformCategory][$platform] ?? false;

                  if ($platformDef) {
                    $newValue = $platformDef['display_name'];
                  }

                  $newPlatformList[$platform] = $newValue;
                }

                return $newPlatformList;
              })();
            })();
          }
        }
      }

      if ($params['code'] != false && $params['code'] == $shiftCode['properties']['code_id']) {
        $response->setPayload(true, 'shift_code_search');
      }

      $shiftCodes[] = $shiftCode;
    }

    $response->setPayload($shiftCodes, 'shift_codes');
  })();
})();

$response->send();
?>