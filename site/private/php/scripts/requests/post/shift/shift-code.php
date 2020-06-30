<?php
  require_once(SCRIPTS_INCLUDES_PATH . 'shift_constants.php');
  require_once(FORMS_PATH . 'shift/shift-code.php');

  /** The SHiFT Code Form */
  $form = &$shiftCodeForm['base'];
  /** The form Response Object */
  $response = &$form_shiftCode->findReferencedProperty('formSubmit->response');
  /** The form validation result */
  $formValidation = $form_shiftCode->validateForm();
  
  $form_shiftCode->buildResponse();
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