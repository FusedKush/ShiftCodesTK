<?php
$shiftCodes = ShiftCodes::getInstance()->getCodes(array_replace_recursive($_GET, [ 'return_full_response' => true ]));

$shiftCodes->send();
exit;

// // require_once('../../../../initialize.php');
// require_once(PRIVATE_PATHS['scripts'] . 'includes/shift_constants.php');

// /**
//  * JSON response
//  */
// $response = new ResponseObject();
// /**
//  * Request parameters & parameter validation
//  */
// $params = (function () use (&$response) {
//   $validations = [
//     /**
//      * The game_id of the game to filter the codes by
//      * string [ all | bl3 | bl1 | bl2 | tps ]
//      */
//     'game' => new ValidationProperties([
//       // 'required'    => true,
//       'value'       => false,
//       'type'        => 'boolean|string',
//       'validations' => [
//         'match'        => array_merge(array_keys(SHIFT_GAMES), [ false ])
//       ]
//     ]),
//     /**
//      * The user_id of an owner to filter the codes by
//      * string [ 8 ] | false
//      */
//     'owner' => new ValidationProperties([
//       'type'        => 'boolean|string',
//       'value'       => false,
//       'validations' => [
//         'length' => [
//           'is' => 12
//         ]
//       ]
//     ]),
//     /**
//      * The code_id of an active SHiFT Code to include in the results
//      * string [ 8 ] | false
//      */
//     'code' => new ValidationProperties([
//       'type'        => 'boolean|string',
//       'value'       => false,
//       'validations' => [
//         'length' => [ 
//           'is' => 12
//         ]
//       ]
//     ]),
//     /**
//      * The order in which to sort the SHiFT Codes
//      * string [ default | new | old ]
//      */
//     'order' => new ValidationProperties([
//       'type'        => 'string',
//       'value'       => 'default',
//       'validations' => [
//         'match' => [
//           'default',
//           'newest', 
//           'oldest'
//         ]
//       ]
//     ]),
//     /**
//      * `array` The event(s) in which to filter the SHiFT Codes
//      * - Possible values include *''*, *"new"*, *"expiring"*
//      */
//     'status' => new ValidationProperties([
//       'type'         => 'array',
//       'value'        => [ 'active' ],
//       'validations'  => [
//         'match' => [
//           'active', 
//           'expired',
//           'hidden',
//           'new', 
//           'expiring'
//         ]
//       ]
//     ]),
//     /**
//      * `array` The platform in which to filter the SHiFT Codes
//      * - The provided value should be in the form of a **SHIFT_CODE_PLATFORMS** *Platform ID*
//      */
//     'platform' => new ValidationProperties([
//       'type'         => 'boolean|string',
//       'value'        => false,
//       'validations'  => [
//         'match' => (function () {
//           $matches = [
//             false
//           ];

//           foreach (SHIFT_CODE_PLATFORMS as $familyID => $familyData) {
//             foreach ($familyData['platforms'] as $platformID => $platformData) {
//               $matches[] = $platformID;
//             }
//           }
//           return $matches;
//         })()
//       ]
//     ]),
//     /**
//      * The maximum number of SHiFT Codes to be retrieved
//      * Int [ 0 - 100 ]
//      */
//     'limit' => new ValidationProperties([
//       'type'        => 'integer',
//       'value'       => 10,
//       'validations' => [
//         'length' => [
//           'min' => 1, 
//           'max' => 50
//         ]
//       ]
//     ]),
//     /**
//      * `int` The page number
//      * - Must be a minimum of 1
//      */
//     'page' => new ValidationProperties([
//       'type'        => 'integer',
//       'value'       => 1,
//       'validations' => [
//         'length' => [
//           'min' => 1
//         ]
//       ]
//     ])
//   ];

//   $checked = check_parameters($_GET, $validations);

//   foreach ($checked['warnings'] as $warning) {
//     $response->setWarning($warning);
//   }
//   foreach ($checked['errors'] as $error) {
//     $response->setError($error);
//   }

//   // Parameter errors are present
//   if (!$checked['valid']) {
//     $response->set(-1);
//     $response->send();
//     exit;
//   }

//   return $checked['parameters'];
// })();
// /**
//  * The SQL Query Statement
//  */
// $query = (function () use ($params) {
//   /**
//    * The Redemption ID of the user
//    */
//   $redemptionID = redemption_get_id();
//   $fields = [
//     "sc.code_id",
//     "sc.owner_id",
//     "sc.code_state",
//     "scd.code_hash",
//     "scd.game_id",
//     "au.username as 'owner_username'",
//     "scd.reward",
//     "scd.source",
//     "scd.release_date",
//     "scd.expiration_date",
//     "sc.creation_time",
//     "sc.update_time as 'last_update'",
//     "scd.timezone",
//     "scd.notes",
//     "scd.platforms",
//     "scd.shift_codes",
//     "CASE 
//       WHEN EXISTS(
//         SELECT id
//         FROM shift_codes_redeemed as scr
//         WHERE
//           scr.code_hash = scd.code_hash
//           AND scr.redemption_id = '{$redemptionID}'
//         LIMIT 1
//       )
//       THEN 1
//       ELSE 0
//     END as 'is_redeemed'"
//   ];
//   $select = "" . implode(", ", $fields) . "";
//   $where = (function () use ($params) {
//     $filter = (function () use ($params) {
//       $filter = $params['status'];
//       $eventFilterIsPresent = array_search('new', $filter) !== false || array_search('expiring', $filter) !== false;

//       $filter = array_filter($filter, function ($value, $key) use ($eventFilterIsPresent) {
//         if ($eventFilterIsPresent) {
//           if ($value == 'active' || $value == 'inactive') {
//             return false;
//           }
//         }

//         return true;
//       }, ARRAY_FILTER_USE_BOTH);

//       return $filter;
//     })();
//     $str = '';

//     // Filter by Code 
//     if ($params['code']) {
//       $str .= "(sc.code_id = '{$params['code']}'";

//       if (!auth_isLoggedIn() || !auth_user_roles()['admin']) {
//         $str .= " AND (sc.code_state != 'hidden'";

//         // Logged in but not Admin
//         if (auth_isLoggedIn()) {
//           $userID = auth_user_id();

//           $str .= " OR sc.owner_id = '{$userID}'";
//         }

//         $str .= ")";
//       }
//       $str .= ") OR";
//     }
//     // Filter by State
//     (function () use (&$str, $filter) {
//       $showActive = array_search('active', $filter) !== false || array_search('expired', $filter) !== false;
//       $showHidden = array_search('hidden', $filter) !== false;

//       if ($showActive || $showHidden) {
//         $str .= "(";

//         if ($showActive) {
//           $str .= "sc.code_state = 'active'";
//         }      
//         if ($showHidden) {
//           if ($showActive) {
//             $str .= " OR ";
//           }
//           if (auth_isLoggedIn()) {
//             $str .= "(sc.code_state = 'hidden'";
            
//             if (!auth_user_roles()['admin']) {
//               $userID = auth_user_id();
//               $str .= " AND owner_id = '{$userID}'";
//             }
  
//             $str .= ")";
//           }
//         }

//         $str .= ") AND";
//       }
//     })();
//     // Filter by Expiration State
//     if (count($filter) > 0) {
//       if (count($filter) > 1 || $filter[0] != 'hidden') {
//         $str .= "(";
  
//         foreach ($filter as $i => $key) {
//           if ($key == 'hidden') {
//             continue;
//           }
  
//           $str .= "(" . SHIFT_DATES[$key] . ") ";
  
//           if (count($filter) > 1 && $i != count($filter) - 1) {
//             $str .= "OR ";
  
//             // if ($key == 'active' && !array_search('inactive', $filter) || $key == 'inactive') {
//             //   $str .= "AND ";
//             // } 
//             // else {
//             //   $str .= "OR ";
//             // }
//           }
//         }
  
//         $str = preg_replace('/ (?:OR|AND) $/', '', $str);
//         $str .= ") AND ";
//       }
//     }
//     // Filter by Game ID
//     if ($params['game'] != false) {
//       $str .= "(scd.game_id = '{$params['game']}') AND ";
//     }
//     // Filter by Owner
//     if ($params['owner']) {
//       $str .= "(sc.owner_id = '{$params['owner']}') AND ";
//     }

//     if ($str) {
//       return preg_replace('/(AND|OR)( ){0,1}$/', '', $str);
//     } 
//     else {
//       return "1";
//     }
//   })();
//   $order = (function () use ($params) {
//     $cleanCodeID = clean_sql($params['code']);
//     // SHiFT Code ordering statements
//     $options = [
//       'default' =>
//          "CASE WHEN " . SHIFT_DATES['active'] . " THEN 1
//             ELSE 0
//           END DESC,
//           CASE WHEN " . SHIFT_DATES['expiring'] . " THEN
//               CASE WHEN " . SHIFT_DATES['new'] . " THEN 2
//                 ELSE 1
//               END
//               ELSE 0
//           END DESC,
//           CASE WHEN " . SHIFT_DATES['new'] . " THEN 1
//               ELSE 0
//               END DESC,
//           -(expiration_date IS NULL) DESC,
//           release_date DESC,
//           expiration_date ASC",
//       'newest' =>
//       'release_date DESC',
//       'oldest' =>
//       'release_date ASC'
//     ];
//     $str = '';

//     if ($cleanCodeID) {
//       $str .= "CASE sc.code_id
//                     WHEN '$cleanCodeID'
//                     THEN 0
//                     ELSE 1
//                  END,";
//     }

//     $str .= $options[$params['order']];

//     return $str;
//   })();
//   $limit = clean_sql($params['limit']);
//   $offset = clean_sql(($params['page'] - 1) * $params['limit']);
//   $count = (function () use ($params, $where, $limit) {
//     $str = '';

//     if ($limit > 1) {
//       $whereCondition = $where;
//       // $whereCondition = str_replace('(' . SHIFT_DATES['new'] . ')', '', $whereCondition);
//       // $whereCondition = str_replace('(' . SHIFT_DATES['expiring'] . ')', '', $whereCondition);
//       // $whereCondition = preg_replace('/(^|AND|OR)(\s+)(AND|OR|$)/', '', $whereCondition);
//       $codeIDCondition = $params['game'] != false
//                          ? " AND scd.game_id = '{$params['game']}'"
//                          : '';
      
//       $str .= ', ';
//       // Total Count
//       $str .= "(SELECT COUNT(sc.id)
//                 FROM shift_codes
//                   AS sc
//                 LEFT JOIN
//                   shift_code_data
//                     AS scd
//                     ON sc.code_id = scd.code_id
//                 WHERE {$whereCondition})
//                   AS 'count_total', ";
//       // New Count
//       $str .= "(SELECT COUNT(sc.id)
//                 FROM shift_codes
//                   AS sc
//                 LEFT JOIN
//                   shift_code_data
//                     AS scd
//                     ON sc.code_id = scd.code_id
//                 WHERE " . SHIFT_DATES['new'] . "
//                   {$codeIDCondition})
//                   AS 'count_new', ";
//       // Expiring Count
//       $str .= "(SELECT COUNT(sc.id)
//                 FROM shift_codes
//                   AS sc
//                 LEFT JOIN
//                   shift_code_data
//                     AS scd
//                     ON sc.code_id = scd.code_id
//                 WHERE " . SHIFT_DATES['expiring'] . "
//                   {$codeIDCondition})
//                   AS 'count_expiring'";
//     }
    
//     return $str;
//   })();
//   $search = (function () {

//   })();

//   return collapseWhitespace("SELECT {$select} {$count}
//                              FROM shift_codes
//                                 AS sc
//                              LEFT JOIN shift_code_data
//                                 AS scd
//                                 ON sc.code_id = scd.code_id
//                              LEFT JOIN auth_users
//                                 AS au
//                                 ON sc.owner_id = au.user_id
//                              WHERE {$where}
//                              GROUP BY sc.id
//                              ORDER BY {$order}
//                              LIMIT {$limit}
//                              OFFSET {$offset}");
// })();

// /**
//  * The retrieved list of SHiFT Codes
//  */
// $result = $_mysqli->query($query);

// if ($result === false) {
//   $response->fatalError(-3, [
//     'type'       => 'Server Query Error',
//     'statusCode' => $_mysqli->con->errno
//   ]);
//   exit;
// }

// // Build payload
// (function () use (&$response, $result, $params) {
//   $payload = [];

//   // Total SHiFT Code count
//   (function () use (&$payload, $result) {
//     if (isset($result[0]['count_total'])) {
//       $types = ['total', 'new', 'expiring'];
//       $counts = array_fill_keys($types, 0);
      
//       if (count($result) > 0) {
//         foreach ($types as $type) {
//           $counts[$type] = (int) $result[0]["count_{$type}"];
//         }
//       }
  
//       $payload['counts'] = $counts;
//     }
//   })();
//   // SHiFT Codes
//   (function () use (&$payload, $result, $params) {
//     // SHiFT Code payload
//     $shiftCodes = [];
//     /**
//      * SHiFT Code Response array 
//      * 
//      * - Passing a `string` as a value will use the string as the key for the value from the database.
//      * - Passing **true** will use the key as the key for the value from the database.
//      * - Passing **NULL** will prevent any values from being filled in.
//      **/
//     $shiftCodeTemplate = [
//       'properties' => [
//         'code'        => [
//           'id'           => 'code_id',
//           'state'        => 'code_state',
//           'hash'         => 'code_hash'
//         ],
//         'owner'       => [
//           'id'           => 'owner_id',
//           'username'     => 'owner_username'
//         ],
//         'game_id'     => true
//       ],
//       'info' => [
//         'reward'          => true,
//         'source'          => [
//           'type'             => NULL,
//           'value'            => 'source'
//         ],
//         'release_date'    => [
//           'type'             => NULL,
//           'value'            => 'release_date'
//         ],
//         'expiration_date' => [
//           'type'             => NULL,
//           'value'            => 'expiration_date'
//         ],
//         'last_update'     => true,
//         'timezone'        => true,
//         'notes'           => true
//       ],
//       'codes' => [
//         'platforms'   => NULL,
//         'shift_codes' => NULL
//       ],
//       'states' => [
//         'code'    => [
//           'isActive'             => NULL,
//           'isNew'                => NULL,
//           'isExpiring'           => NULL,
//           'wasRecentlySubmitted' => NULL,
//           'wasRecentlyUpdated'   => NULL
//         ],
//         'user'    => [
//           'isOwner'     => NULL,
//           'canEdit'     => NULL,
//           'hasRedeemed' => NULL
//         ]
//       ],
//     ];

//     if ($params['code'] != false) {
//       $payload['search_result'] = false;
//     }

//     foreach ($result as $code) {
//       $shiftCode = [];
  
//       // Create SHiFT Code Response Array
//       (function () use (&$shiftCode, $shiftCodeTemplate, $code) {
//         $processArray = function (&$parent, $key, $array) use (&$processArray, $code) {
//           $parent[$key] = [];

//           foreach ($array as $arrayKey => $arrayValue) {
//             if (is_string($arrayValue)) {
//               $parent[$key][$arrayKey] = $code[$arrayValue] ?? null;
//             }
//             else if ($arrayValue === true) {
//               $parent[$key][$arrayKey] = $code[$arrayKey] ?? null;
//             }
//             else if ($arrayValue === NULL) {
//               $parent[$key][$arrayKey] = NULL;
//             }
//             else if (is_array($arrayValue)) {
//               $processArray($parent[$key], $arrayKey, $arrayValue);
//             }
//           }
//         };
        
//         foreach ($shiftCodeTemplate as $section => $fields) {
//           $processArray($shiftCode, $section, $fields);
//         }
//       })();

//       // Source
//       (function () use (&$shiftCode, $code) {
//         $source = $code['source'];
//         $resultSource = &$shiftCode['info']['source'];

//         if ($source !== null) {
//           if (check_url($source)) {
//             $resultSource['type'] = 'online';
//           }
//           else {
//             $resultSource['type'] = 'physical';
//           }

//           $resultSource['value'] = $source;
//         }
//         else {
//           $resultSource = [
//             'type' => 'none',
//             'value' => NULL
//           ];
//         }
//       })(); 
//       // Release Date
//       (function () use (&$shiftCode, $code) {
//         $date = $code['release_date'];
//         $resultDate = &$shiftCode['info']['release_date'];

//         if ($date !== NULL) {
//           $resultDate['type'] = 'date';
//         }
//         else {
//           $resultDate['type'] = 'none';
//         }
//       })(); 
//       // Expiration Date
//       (function () use (&$shiftCode, $code) {
//         $date = $code['expiration_date'];
//         $resultDate = &$shiftCode['info']['expiration_date'];

//         if ($date !== NULL) {
//           if ($date == '9999-12-31T23:59:59+00:00') {
//             $resultDate['type'] = 'infinite';
//           }
//           else {
//             if (strpos($date, 'T23:59:59') !== false) {
//               $resultDate['type'] = 'through';
//             }
//             else {
//               $resultDate['type'] = 'until';
//             }
//           }
//         }
//         else {
//           $resultDate['type'] = 'none';
//         }
//       })(); 
//       // Platforms
//       (function () use (&$shiftCode, $code) {
//         $platforms = $code['platforms'];

//         if ($platforms) {
//           $shiftCode['codes']['platforms'] = json_decode($platforms);
//         }
//       })(); 
//       // Codes
//       (function () use (&$shiftCode, $code) {
//         $codes = $code['shift_codes'];

//         if ($codes) {
//           $shiftCode['codes']['shift_codes'] = json_decode($codes);
//         }
//       })(); 
//       // States
//       (function () use (&$shiftCode, $code) {
//         $states = &$shiftCode['states'];

//         // Code States
//         (function () use ($code, &$states) {
//           $codeStates = &$states['code'];
          
//           // Dates
//           (function () use ($code, &$codeStates) {
//             $interval = new DateInterval('PT' . SHIFT_EVENT_DURATION . 'H');
        
//             // New
//             (function () use ($code, $interval, &$codeStates) {
//               $release = $code['release_date'];
        
//               $codeStates['isNew'] = false;
    
//               if ($release) {
//                 $threshold = new DateTime($release);
//                 $threshold->add($interval);
//                 $threshold = $threshold->getTimestamp();
        
//                 $codeStates['isNew'] = $threshold > time();
//               }
//             })();
//             // Active, Expiring
//             (function () use ($code, $interval, &$codeStates) {
//               $expiration = $code['expiration_date'];
//               $timezone = new DateTimeZone($code['timezone'] ?? 'UTC');
    
//               $codeStates['isActive'] = true;
//               $codeStates['isExpiring'] = false;
              
//               if ($expiration) {
//                 // Adjusted expiration time
//                 $expirationTime = new DateTime($expiration);
//                 $expirationTime->setTimeZone($timezone);
//                 $expirationTime = $expirationTime->format('c');
//                 // codeIsExpiring threshold
//                 $expiringThreshold = new DateTime($expiration);
//                 $expiringThreshold->sub($interval);
//                 $expiringThreshold = $expiringThreshold->getTimestamp();
//                 // codeIsActive threshold
//                 $activeThreshold = new DateTime($expiration);
//                 $activeThreshold = $activeThreshold->getTimestamp();
    
//                 // $shiftCode['states']['expiration_date'] = $expirationTime;
//                 $codeStates['isActive'] = $activeThreshold > time();
//                 $codeStates['isExpiring'] = $expiringThreshold < time();
//               }
//             })();
//           })();
//           // Recent Events
//           (function () use ($code, &$codeStates) {
//             $threshold = new DateTime();
//             $threshold->sub(new DateInterval('PT15M'));
//             $threshold = $threshold->getTimestamp();
  
//             // RecentlySubmitted
//             (function () use ($code, $threshold, &$codeStates) {
//               $creation = new DateTime($code['creation_time']);
//               $creation = $creation->getTimestamp();
  
//               $codeStates['wasRecentlySubmitted'] = $creation > $threshold;
//             })();
//             // RecentlyUpdated
//             (function () use ($code, $threshold, &$codeStates) {
//               $updated = new DateTime($code['last_update']);
//               $updated = $updated->getTimestamp();
  
//               $codeStates['wasRecentlyUpdated'] = $updated > $threshold;
//             })();
//           })();
//         })();
//         // User States
//         (function () use ($code, &$states) {
//           $ownerID = $code['owner_id'];
//           $user = &$states['user'];
          
//           // Redemption state
//           $user['hasRedeemed'] = $code['is_redeemed'] == 1;
//           // Owner Permission
//           $user['isOwner'] = ($ownerID == auth_user_id());
//           // Editing Permission
//           $user['canEdit'] = ($ownerID == auth_user_id() || auth_user_roles()['admin']);
//         })(); 
//       })();

//       if ($params['code'] != false && $params['code'] == $shiftCode['properties']['code']['id']) {
//         $payload['search_result'] = true;
//       }

//       $shiftCodes[] = $shiftCode;
//     }

//     $payload['codes'] = $shiftCodes;
//   })();

//   $response->setPayload($payload, 'shift_codes');
// })();

// $response->send();
// ?>