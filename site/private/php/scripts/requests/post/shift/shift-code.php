<?php
  require_once(PRIVATE_PATHS['script_includes'] . 'shift_constants.php');
  require_once(PRIVATE_PATHS['forms'] . 'shift/shift-code.php');

  /** The SHiFT Code Form */
  $form = (function () {
    $formType = $_POST['form_type'] ?? false;

    if ($formType) {
      if (array_search($formType, [ 'add', 'update' ]) !== false) {
        return getShiftCodeForm($formType);
      }
    }

    return getShiftCodeForm('add');
  })();
  /** The form Response Object */
  $response = &$form->findReferencedProperty('formSubmit->response');
  /** The form validation result */
  $formValidation = $form->validateForm();

  // $response->setPayload($form->findReferencedProperty('formSubmit->validations'), 'validations');
  $response->setPayload($form->findReferencedProperty('formSubmit->parameters'), 'parameters');
  $response->setPayload($form->findReferencedProperty('formSubmit->parameterList'), 'parameterList');

  // Form is Valid
  if ($formValidation) {
    /** The parsed form request paramters */
    $requestParameters = $form->findReferencedProperty('formSubmit->parameters');
    /** The form type */
    $formType = $requestParameters['form_type'];

    /** Check if the request is authenticated */
    $authenticatedRequest = (function () use (&$_mysqli, &$response, $requestParameters, $formType) {
      if (auth_isLoggedIn()) {
        if ($formType == 'add') {
          return true;
        }
        // Update SHiFT Code
        else if ($formType == 'update') {
          /** Existing SHiFT Code Properties */
          $existingCodeOwner = (function () use (&$_mysqli, &$response, $requestParameters, $formType) {
            if ($formType == 'update') {
              $existingCodeID = $requestParameters['code_id'];
              $query = "SELECT owner_id
                        FROM shift_codes
                        WHERE code_id = '${existingCodeID}'
                        LIMIT 1";
              
              $result = $_mysqli->query($query, [ 'collapseAll' => true ]);
    
              if ($result !== false) {
                return $result;
              }
              // Query Error
              else {
                trigger_error("An error occurred while validating SHiFT Code. Code ID: ${existingCodeID}.");
                $response->fatalError(-3);
              }
            }
    
            return false;
          })();
    
          // Permission to update
          if ($existingCodeOwner  == auth_user_id() || auth_user_roles()['admin']) {
            return true;
          }
          // No permission to update
          else {
            $response->fatalError(401, 'You do not have permission to edit this SHiFT Code.');
          }
        }
      }
      // Not Logged In
      else {
        $response->fatalError(401, 'You must be logged in to perform this action.');
      }

      return false;
    })();

    // Request is authenticated
    if ($authenticatedRequest) {
      /** The existing SHiFT Code Properties, if applicable */
      $existingCode = (function () use (&$_mysqli, &$response, $requestParameters, $formType) {
        if ($formType == 'update') {
          $existingCodeID = $requestParameters['code_id'];
          $query = "SELECT *
                    FROM shift_codes AS sc
                    INNER JOIN shift_code_data AS scd
                      ON sc.code_id = scd.code_id
                    WHERE sc.code_id = '${existingCodeID}'
                    LIMIT 1";
          
          $result = $_mysqli->query($query, [ 'collapseAll' => true ]);

          if ($result !== false) {
            return $result;
          }
          // Query Error
          else {
            trigger_error("An error occurred while validating the existing SHiFT Code. Code ID: ${existingCodeID}.");
            $response->fatalError(-3);
          }
        }

        return false;
      })();
      /** The SQL Query Request Paramters */
      $queryParameters = (function () use (&$_mysqli, &$response, $requestParameters, $formType, $existingCode) {
        $params = [];

        // New or Existing SHiFT Code ID
        $params['code_id'] = (function () use (&$_mysqli, $response, $requestParameters, $formType) {
          if ($formType == 'add') {
            $newCodeQuery = "SELECT code_id
                             FROM shift_codes
                             WHERE code_id = ?
                             LIMIT 1";
            $newCodeAttempts = 0;
            
            // Generate new SHiFT Code ID
            while (true) {
              $newCodeID = auth_randomMetaID(10, '11');
              $newCodeAttempts++;
              $newCodeValidation = $_mysqli->prepared_query($newCodeQuery, 's', [ $newCodeID ], [ 'collapseAll' => true ]);

              // Couldn't generate SHiFT Code ID
              if ($newCodeID === false || $newCodeValidation === false || $newCodeAttempts > 10) {
                trigger_error("An error occurred while attempting to generate a new SHiFT Code ID. Code ID: {$newCodeID}.");
                $response->fatalError(-3);
              }
              else if (!$newCodeValidation) {
                return $newCodeID;
              }
            }

          }
          else if ($formType == 'update') {
            return $requestParameters['code_id'] ?? false;
          }
        })();

        // shift_codes Table
        $params['shift_codes'] = [ 
          'code_id' => $params['code_id']
        ];
        // SHiFT Code Owner ID
        $params['shift_codes']['owner_id'] = $formType == 'add'
                                 ? auth_user_id()
                                 : $existingCode['owner_id'];
        // SHiFT Code State
        $params['shift_codes']['code_state'] = $formType == 'add'
                                   ? 'active'
                                   : $existingCode['code_state'];

        // shift_code_data Table
        $params['shift_code_data'] = [ 
          'code_id' => $params['code_id']
        ];
        // SHiFT Code Hash ID
        $params['shift_code_data']['code_hash'] = (function () use (&$_mysqli, $response, $requestParameters, $formType) {
          $shiftCodeString = (function () use (&$_mysqli, $response, $requestParameters, $formType) {
            $shiftCodes = (function () use (&$_mysqli, $response, $requestParameters, $formType) {
              $codeParams = $requestParameters['codes'];
              
              if (isset($codeParams['universal']['code'])) {
                return [ $codeParams['universal']['code'] ];
              }
              else {
                $codeList = [];

                foreach (SHIFT_CODE_PLATFORMS as $familyID => $familyInfo) {
                  $familyCode = $codeParams['individual'][$familyID]['code'] ?? false;
  
                  if ($familyCode) {
                    $codeList[] = $familyCode;
                  }
                }

                return $codeList;
              }
            })();
            
            return implode(" ", $shiftCodes);
          })();
          $codeHash = auth_strHash($shiftCodeString);
          $hashID = '';

          // Check for existing Code Hash
          (function () use (&$_mysqli, &$response, &$hashID, $codeHash) {
            $query = "SELECT hash_id
                      FROM shift_code_hashes
                      WHERE code_hash = '{$codeHash}'
                      LIMIT 1";

            $existingHashID = $_mysqli->query($query, [ 'collapseAll' => true ]);

            // Hash retrieval error
            if ($existingHashID === false) {
              trigger_error("An error occurred while attempting to retrieve an existing SHiFT Code Hash. Code Hash: ${codeHash}.");
              $response->fatalError(-3);
            }

            if ($existingHashID) {
              $hashID = $existingHashID;
              return true;
            }

            return false;
          })();

          // Generate Code Hash
          if (!$hashID) {
            // Generate ID
            (function () use (&$_mysqli, &$response, &$hashID, $codeHash) {
              $query = "SELECT hash_id
                        FROM shift_code_hashes
                        WHERE hash_id = ?
                        LIMIT 1";
              $newHashIDAttempts = 0;

              while (true) {
                $hashID = auth_randomMetaID(10, '12');
                $validateNewHashID = $_mysqli->prepared_query($query, 's', [ $hashID ], [ 'collapseAll' => true ]);
                $newHashIDAttempts++;

                if ($validateNewHashID === false || $newHashIDAttempts > 10) {
                  trigger_error("An error occurred while attempting to generate a new Hash ID. Hash ID: ${hashID}, Code Hash: ${codeHash}.");
                  $response->fatalError(-3);
                }
                else if (!$validateNewHashID) {
                  return $hashID;
                }
              }
            })();
            // Update Record
            (function () use (&$_mysqli, &$response, &$hashID, $codeHash) {
              $query = "INSERT INTO shift_code_hashes
                        (hash_id, code_hash)
                        VALUES (?, ?)";
              
              $result = $_mysqli->prepared_query($query, 'ss', [ $hashID, $codeHash ], [ 'collapseAll' => true ]);

              if (!$result) {
                trigger_error("An error occurred while attempting to set a new Hash ID. Hash ID: ${hashID}, Code Hash: ${codeHash}.");
                $response->fatalError(-3);
              }
            })();
          }

          return $hashID;
        })();
        // SHiFT Code Game ID
        $params['shift_code_data']['game_id'] = $requestParameters['game_id'];
        // SHiFT Code Reward
        $params['shift_code_data']['reward'] = $requestParameters['reward'];
        // SHiFT Code Source
        $params['shift_code_data']['source'] = (function () use ($requestParameters) {
          $sourceParams = $requestParameters['source'];

          if ($sourceParams['type'] == 'online') {
            return $sourceParams['url'];
          }
          else if ($sourceParams['type'] == 'physical') {
            return $sourceParams['string'];
          }
          else if ($sourceParams['type'] == 'none') {
            return NULL;
          }
        })();
        // SHiFT Code Release Date
        $params['shift_code_data']['release_date'] = (function () use ($requestParameters) {
          $date = $requestParameters['release_date'] ?? false;

          if ($date) {
            $datetime = new DateTime("{$date} 12:00:00", new DateTimeZone('UTC'));

            if ($datetime) {
              return $datetime->format(DATE_FORMATS['date']);
            }
          }
          else {
            return NULL;
          }
        })();
        // SHiFT Code Expiration Date
        $params['shift_code_data']['expiration_date'] = (function () use ($requestParameters) {
          $expirationParams = $requestParameters['expiration_date'] ?? false;

          if (array_search($expirationParams['type'], [ 'through', 'until']) !== false) {
            $datetime = new DateTime("{$expirationParams['value']['date']} {$expirationParams['value']['time']}", new DateTimeZone($expirationParams['value']['tz']));

            if ($datetime) {
              $datetime->setTimezone(new DateTimezone('UTC'));
              return $datetime->format(DATE_FORMATS['dateTime']);
            }
          }
          else if ($expirationParams['type'] == 'never') {
            return '9999-12-31 23:59:59';
          }
          else if ($expirationParams['type'] == 'none') {
            return NULL;
          }
        })();
        // SHiFT Code Timezone
        $params['shift_code_data']['timezone'] = (function () use ($requestParameters) {
          $tz = $requestParameters['expiration_date']['value']['tz'] ?? false;

          if ($tz) {
            $timezone = new DateTimeZone($tz);

            if ($timezone) {
              return $tz;
            }
          }

          return 'America/Los_Angeles';
        })();
        // SHiFT Code Notes
        $params['shift_code_data']['notes'] = $requestParameters['notes'] ?? NULL;
        // SHiFT Code Platforms
        $params['shift_code_data']['platforms'] = (function () use ($requestParameters) {
          $codeParams = $requestParameters['codes'];
          $platformList = [
            'universal' => $codeParams['universal']['platforms']
          ];

          foreach (SHIFT_CODE_PLATFORMS as $familyID => $familyInfo) {
            $platforms = $codeParams['individual'][$familyID]['platforms'] ?? [];

            if ($platforms) {
              $platformList[$familyID] = $platforms;
            }
          }

          return json_encode($platformList);
        })();
        // SHiFT Codes
        $params['shift_code_data']['shift_codes'] = (function () use ($requestParameters) {
          $codeParams = $requestParameters['codes'];
          $codeList = [
            'universal' => $codeParams['universal']['code']
          ];

          foreach (SHIFT_CODE_PLATFORMS as $familyID => $familyInfo) {
            $code = $codeParams['individual'][$familyID]['code'] ?? [];

            if ($code) {
              $codeList[$familyID] = $code;
            }
          }

          return json_encode($codeList);
        })();

        return $params;
      })();
      $response->setPayload($queryParameters, 'queryParameters');

      // Add SHiFT Code to Database
      if ($formType == 'add') {
        // Add to Database
        (function () use (&$_mysqli, &$response, $queryParameters) {
          // Update `shift_codes` & `shift_code_data`
          (function () use (&$_mysqli, &$response, $queryParameters) {
            foreach ([ 'shift_codes', 'shift_code_data' ] as $table) {
              $tableParams = $queryParameters[$table];
              $columns = implode(', ', array_keys($tableParams));
              $values = array_values($tableParams);
              $variables = preg_replace('/, $/', '', str_repeat('?, ', count($values)));
              $types = str_repeat('s', count($values));
  
              $query = "INSERT INTO {$table}
                        ({$columns})
                        VALUES ({$variables})";
              $result = $_mysqli->prepared_query($query, $types, $values, [ 'collapseAll' => true ]);
  
              if (!$result) {
                trigger_error("An error occurred while attempting to create the SHiFT Code.");
                $response->fatalError(-3);
              }
            }
          })();
        })();
        // Update User Records
        (function () use (&$_mysqli, &$response) {
          $timestamp = new DateTime();
          $timestamp = $timestamp->format(DATE_FORMATS['fullDateTime']);
          $params = [
            $timestamp,
            auth_user_id()
          ];
          $query = "UPDATE auth_user_records
                    SET last_public_activity = ?,
                        shift_codes_submitted = shift_codes_submitted + 1
                    WHERE user_id = ?
                    LIMIT 1";
          
          $result = $_mysqli->prepared_query($query, 'ss', $params, [ 'collapseAll' => true ]);

          if ($result === false) {
            trigger_error("An error occurred while updating the user's record after adding the SHiFT Code. User ID: {$params[2]}.");
            $response->fatalError(-3);
          }
        })();

        // Success
        (function () use (&$form, &$response, $queryParameters) {
          $gameName = SHIFT_GAMES[$queryParameters['shift_code_data']['game_id']]['long_name'] ?? '';
          $codeID = $queryParameters['code_id'];
          $gameID = $queryParameters['shift_code_data']['game_id'];

          $response->set(2);
          $form->updateProperty('formResult->toast->properties', [
            'settings'    => [
              'template'     => 'formSuccess',
              'duration'     => 'infinite'
            ],
            'content'        => [
              'title'           => 'SHiFT Code Submitted!',
              'body'            => "Your SHiFT Code has been submitted! You can view and edit it on the <em>${gameName}</em> page, or in <em>My SHiFT Codes</em>."
            ],
            'actions'        => [
              [
                'content'       => 'View Code',
                'title'         => 'View your newly submitted SHiFT Code',
                'link'          => "/{$gameID}#shift_code_{$codeID}"
              ],
              [
                'content'       => 'My SHiFT Codes',
                'title'         => 'View all of your previously submitted SHiFT Codes',
                'link'          => "/codes/#shift_code_{$codeID}"
              ]
            ]
          ]);
        })();
      }
      else if ($formType == 'update') {
        // Update `shift_codes` & `shift_code_data`
        (function () use (&$_mysqli, &$response, $queryParameters) {
          $tableParams = $queryParameters['shift_code_data'];
          $columns = array_keys($tableParams);
          // $columns = implode(', ', array_keys($tableParams));
          $values = array_merge(array_values($tableParams), [ $tableParams['code_id'] ]);
          $variables = preg_replace('/, $/', '', str_repeat('?, ', count($values)));
          $types = str_repeat('s', count($values));
          $updateString = (function () use ($tableParams, $columns, $values) {
            $strings = [];

            foreach ($columns as $column) {
              $strings[] = "scd.{$column} = ?";
            }

            return implode(', ', $strings);
          })();
          $query = "UPDATE
                      shift_codes 
                      AS sc
                    INNER JOIN
                      shift_code_data 
                      AS scd 
                      ON sc.code_id = scd.code_id
                    SET 
                      sc.update_time = CURRENT_TIMESTAMP(),
                      {$updateString}
                    WHERE sc.code_id = ?";
          $result = $_mysqli->prepared_query($query, $types, $values, [ 'collapseAll' => true ]);

          if (!$result) {
            trigger_error("An error occurred while attempting to update the SHiFT Code.");
            $response->fatalError(-3, errorObject('UpdateShiftCodeError', null, 'An error occurred while attempting to update the SHiFT Code.'));
          }
        })();
        // Get Updated SHiFT Code Data
        (function () use (&$_mysqli, &$response) {
          $response->setPayload($_SERVER, 'SERVER');
          $filepath = (function () {
            $str = '';

            $str .= "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['SERVER_ADDR']}";

            if (isset($_SERVER['SERVER_PORT'])) {
              $str .= ":{$_SERVER['SERVER_PORT']}";
            }

            $str .= '/assets/requests/get/shift/codes';

            return $str;
          })();
          $curl = curl_init($filepath);
          curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
              'X_REQUEST_TOKEN' => $_SERVER['HTTP_X_REQUEST_TOKEN']
            ]
          ]);
          $response->setPayload(curl_exec($curl), '_contents');
          // $timestamp = new DateTime();
          // $timestamp = $timestamp->format(DATE_FORMATS['fullDateTime']);
          // $params = [
          //   $timestamp,
          //   auth_user_id()
          // ];
          // $query = "UPDATE auth_user_records
          //           SET last_activity = ?
          //           WHERE user_id = ?
          //           LIMIT 1";
          
          // $result = $_mysqli->prepared_query($query, 'ss', $params, [ 'collapseAll' => true ]);

          // if ($result === false) {
          //   trigger_error("An error occurred while updating the user's record after updating the SHiFT Code. User ID: {$params[1]}.");
          //   $response->fatalError(-3);
          // }
        })();

        // Success
        (function () use (&$form, &$response, $queryParameters) {
          $gameName = SHIFT_GAMES[$queryParameters['shift_code_data']['game_id']]['long_name'] ?? '';
          $codeID = $queryParameters['code_id'];
          $gameID = $queryParameters['shift_code_data']['game_id'];

          $response->set(2);
          $form->updateProperty('formResult->toast->properties', [
            'settings'    => [
              'template'     => 'formSuccess',
              'duration'     => 'infinite'
            ],
            'content'        => [
              'title'           => 'SHiFT Code Updated!',
              'body'            => "Your SHiFT Code has been updated. You can view and edit it on the <em>${gameName}</em> page, or in <em>My SHiFT Codes</em>."
            ],
            // 'actions'        => [
            //   [
            //     'content'       => 'View Code',
            //     'title'         => 'View your newly submitted SHiFT Code',
            //     'link'          => "/{$gameID}#shift_code_{$codeID}"
            //   ],
            //   [
            //     'content'       => 'My SHiFT Codes',
            //     'title'         => 'View all of your previously submitted SHiFT Codes',
            //     'link'          => "/codes/#shift_code_{$codeID}"
            //   ]
            // ]
          ]);
        })();
      }
    }
  }

  $form->buildResponse();
  $response->send();
  exit;

  if ($formValidation['valid']) {
    /** The request parameters */
    $params = $formValidation['parameters'];
    /** The type of SHiFT Code event that is taking place */
    $eventType = $params['general_code_id'] ? 'update' : 'add';
    /** Properties of the existing SHiFT Code, if available */
    $existingCodeProps = (function () use (&$_mysqli, &$response, $params, $eventType)  {
      if ($eventType == 'update') {
        $codeID = $params['general_code_id'];
        $query = "SELECT *
                  FROM shift_codes
                  WHERE code_id = '${codeID}'
                  LIMIT 1";
        $result = $_mysqli->query($query, [ 'collapseAll' => true ]);
  
        if ($result === false) {
          trigger_error("An error occurred while attempting to retrieve the existing SHiFT Code properties. Code ID: ${codeID}.");
          $response->set(-3);
          $response->send();
          exit;
        }
        else if ($result) {
          return $result;
        }
      }

      return false;
    })();
    /** Indicates if the request is authenticated */
    $isAuthenticatedRequest = (function () use (&$_mysqli, &$response, $params, $eventType, $existingCodeProps) {
      if (!auth_isLoggedIn()) {
        $response->fatalError(401, 'You must be logged in to perform this action.');
        return false;
      }
      else if ($eventType == 'update') {
        if ($existingCodeProps['owner_id'] != auth_user_id() && !auth_user_roles()['admin']) {
          $response->fatalError(401, 'You do not have permission to edit this SHiFT Code.');
          return false;
        }
      }

      return true;
    })();

    if ($isAuthenticatedRequest) {
      /** The SQL Query Paramters */
      $queryParams = [];
      $queryParams['code_id'] = (function () use (&$_mysqli, &$response, $params) {
        $codeID = $params['general_code_id'] ?? false;

        if (!$codeID) {
          $query = "SELECT code_id
                    FROM shift_codes
                    WHERE code_id = ?
                    LIMIT 1";
          $generationAttempts = 0;

          while (true) {
            $newCodeID = auth_randomMetaID(10, '11');
            $testResult = $_mysqli->prepared_query($query, 's', [ $newCodeID ], [ 'collapseAll' => true ]);
            $generationAttempts++;

            if ($testResult === false || $generationAttempts > 10) {
              trigger_error("An error occurred while attempting to generate a new Code ID. Code ID: {$newCodeID}.");
              $response->set(-3);
              $response->send();
              exit;
            }
            else if (!$testResult) {
              $codeID = $newCodeID;
              break;
            }
          }
        }

        return $codeID;
      })();
      $queryParams['code_hash'] = (function () use (&$_mysqli, &$response, $params) {
        $fullHash = auth_strHash("{$params['codes_pc']} {$params['codes_xbox']} {$params['codes_ps']}");
        $hashID = '';
        
        // Check for existing Hash ID
        (function () use (&$_mysqli, &$response, $fullHash, &$hashID) {
          $query = "SELECT hash_id
                    FROM shift_code_hashes
                    WHERE code_hash = '{$fullHash}'
                    LIMIT 1";
          $existingID = $_mysqli->query($query, [ 'collapseAll' => true ]);

          if ($existingID === false) {
            trigger_error("An error occurred while attempting to retrieve an existing Hash ID. Code Hash: ${fullHash}.");
            $response->set(-3);
            $response->send();
            exit;
          }
          else if ($existingID) {
            $hashID = $existingID;
          }
        })();
        // Generate new Hash ID
        if (!$hashID) {
          (function () use (&$_mysqli, &$response, $fullHash, &$hashID) {
            // Generate the new ID
            (function () use (&$_mysqli, &$response, $fullHash, &$hashID) {
              $query = "SELECT hash_id
                        FROM shift_code_hashes
                        WHERE hash_id = ?
                        LIMIT 1";
              $generationAttempts = 0;
  
              while (true) {
                $newHashID = auth_randomMetaID(10, '12');
                $testResult = $_mysqli->prepared_query($query, 's', [ $newHashID ], [ 'collapseAll' => true ]);
                $generationAttempts++;
  
                if ($testResult === false || $generationAttempts > 10) {
                  trigger_error("An error occurred while attempting to generate a new Hash ID. Hash ID: ${newHashID}, Code Hash: ${fullHash}.");
                  $response->set(-3);
                  $response->send();
                  exit;
                }
                else if (!$testResult) {
                  $hashID = $newHashID;
                  break;
                }
              }
            })();
            // Update the database
            (function () use (&$_mysqli, &$response, $fullHash, &$hashID) {
              $query = "INSERT INTO shift_code_hashes
                        (hash_id, code_hash)
                        VALUES ('$hashID', '$fullHash')";
              $result = $_mysqli->query($query, [ 'collapseAll' => true ]);

              if (!$result) {
                trigger_error("An error occurred while attempting to set a new Hash ID. Hash ID: ${hashID}, Code Hash: ${fullHash}.");
                $response->set(-3);
                $response->send();
                exit;
              }
            })();

          })();
        }

        return $hashID;
      })();
      $queryParams['game_id'] = $params['general_game_id'];
      $queryParams['owner_id'] = $eventType == 'add' ? auth_user_id() : $existingCodeProps['owner_id'];
      $queryParams['reward'] = $params['general_reward'];
      $queryParams['source'] = $params['general_source'] ?? null;
      $queryParams['release_date'] = $params['general_release_date'];
      $queryParams['expiration_date'] = (function () use (&$_mysqli, $params) {
        $pieces = [
          'date' => $params['general_expiration_date_date'],
          'time' => $params['general_expiration_date_time'],
          'tz' => $params['general_expiration_date_tz'],
        ];
        $value = null;

        if ($pieces['date']) {
          $datetime = new DateTime($pieces['time'] ? "${pieces['date']} ${pieces['time']}" : "${pieces['date']} 23:59:59", new DateTimeZone($pieces['tz']));
          $datetime->setTimezone(new DateTimeZone('UTC'));
          $value = $datetime->format($_mysqli->dateFormats['dateTime']);
        }

        return $value;
      })(); 
      $queryParams['notes'] = $params['general_notes'] ?? null;
      $queryParams['platforms_pc'] = implode('/', $params['platforms_pc']);
      $queryParams['code_pc'] = $params['codes_pc'];
      $queryParams['platforms_xbox'] = implode('/', $params['platforms_xbox']);
      $queryParams['code_xbox'] = $params['codes_xbox'];
      $queryParams['platforms_ps'] = implode('/', $params['platforms_ps']);
      $queryParams['code_ps'] = $params['codes_ps'];
      /** The primary SQL query arguments */
      $queryArgs = (function () use ($eventType, $queryParams) {
        $query = '';
        $types = '';
        $params = [];
        
        if ($eventType == 'add') {
          $columns = implode(', ', array_keys($queryParams));
          $values = array_values($queryParams);
          $variables = substr(str_repeat('?, ', count($values)), 0, -2);

          $query = "INSERT INTO shift_codes
                    ($columns)
                    VALUES ($variables)";
          $types = str_repeat('s', count($values));
          $params = $values;
        }
        else {
          $updates = (function () use ($queryParams) {
            $str = '';

            foreach ($queryParams as $column => $value) {
              $str .= "{$column} = ?, ";
            }

            return substr($str, 0, -2);
          })();
          $values = array_values($queryParams);

          $query = "UPDATE shift_codes
                    SET $updates
                    WHERE code_id = ?
                    LIMIT 1";
          $types = str_repeat('s', (count($values) + 1));
          $params = array_merge($values, [ $queryParams['code_id'] ]);
        }

        return [
          'query'  => $query,
          'types'  => $types,
          'params' => $params
        ];
      })();
      
      // Debugging
      $response->setPayload($formValidation, '_validation');
      $response->setPayload($params, '_params');
      $response->setPayload($queryParams, '_query_params');
    
      $primaryResult = $_mysqli->prepared_query($queryArgs['query'], $queryArgs['types'], $queryArgs['params'], [ 'collapseAll' => true ]);

      if ($primaryResult === false) {
        trigger_error("An error occurred while attempting to create or update a SHiFT Code.");
        $response->set(-3);
        $response->send();
        exit;
      }
      else if ($primaryResult) {
        $toastProperties = [
          'settings' => [
            'duration' => 'long'
          ],
          'content' => [
            'title' => '',
            'body'  => ''
          ],
          'action' => [
            'use' => true,
            'type' => 'link',
            'link' => "/{$queryParams['game_id']}#shift_code_{$queryParams['code_id']}",
            'name' => 'View SHiFT Code',
            'label' => 'View the submitted SHiFT Code'
          ]
        ];

        if ($eventType == 'add') {
          $response->set(2);
          $toastProperties['content']['title'] = 'SHiFT Code Submitted';
          $toastProperties['content']['body'] = 'Your SHiFT Code has been successfully submitted to ShiftCodesTK!';
        }
        else {
          $response->set(1);
          $toastProperties['content']['title'] = 'SHiFT Code Update';
          $toastProperties['content']['body'] = 'Your SHiFT Code has been successfully updated.';
        }

        $form->updateProperty('result', [
          'toasts' => [
            'enabled' => true,
            'method' => 'response',
            'properties' => $toastProperties
          ],
        ]);
        $form->constructResponse();
      }

      $response->send();
    }
  }
  // Invalid request
  else {
    $form->constructResponse();
    exit;
  }

?>