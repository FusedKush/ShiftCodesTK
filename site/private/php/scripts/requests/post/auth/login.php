<?php
  require_once(FORMS_PATH . 'auth/login.php');

  /** The form's response object */
  $response = &$form_authLogin->findReferencedProperty('formSubmit->response');

  // Valid Form Submission
  if ($form_authLogin->validateForm()) {
    /** Provided Parameters */
    $formParams = $form_authLogin->findReferencedProperty('formSubmit->parameters');
    $response->setPayload($formParams, '_formData');
    /** User Account Data */
    $userData = (function () use (&$_mysqli, $formParams) {
      $userData = [];

      // Account Lookup
      (function () use (&$_mysqli, &$userData, $formParams) {
        $query = "SELECT 
                    user_id,
                    username,
                    password
                  FROM auth_users
                  WHERE email_address = ?
                  LIMIT 1";
        $params = [
          $formParams['email']
        ];
        $result = $_mysqli->prepared_query($query, 's', $params, [ 'collapseAll' => true ]);

        if ($result) {
          $userData['account'] = $result;
        }
      })();

      if ($userData) {
        $userData['attempts'] = [];

        // User Failed Attempts Lookup
        (function () use (&$_mysqli, &$userData) {
          $query = "SELECT
                      MAX(last_attempt) AS last_attempt,
                      SUM(failed_attempts) AS failed_attempts
                    FROM auth_failed_logins
                    WHERE user_id = ?
                    LIMIT 1";
          $params = [
            $userData['account']['user_id']
          ];
          $result = $_mysqli->prepared_query($query, 's', $params, [ 'collapseAll' => true ]);

          if ($result !== false) {
            $userData['attempts'] = array_merge_recursive($userData['attempts'], [
              'last'  => [ 'user' => $result['last_attempt'] ],
              'count' => [ 'user' => $result['failed_attempts'] ]
            ]);
          }
        })();
        // IP Failed Attempts Lookup
        (function () use (&$_mysqli, &$userData, $formParams) {
          $query = "SELECT
                      MAX(last_attempt) AS last_attempt,
                      SUM(failed_attempts) AS failed_attempts
                    FROM auth_failed_logins
                    WHERE ip = ?
                    LIMIT 1";
          $params = [
            inet_pton($_SERVER['REMOTE_ADDR'])
          ];
          $result = $_mysqli->prepared_query($query, 's', $params, [ 'collapseAll' => true ]);

          if ($result !== false) {
            $userData['attempts'] = array_merge_recursive($userData['attempts'], [
              'last'  => [ 'ip' => $result['last_attempt'] ],
              'count' => [ 'ip' => $result['failed_attempts'] ]
            ]);
          }
        })();
        // User + IP Failed Attempts Lookup
        (function () use (&$_mysqli, &$userData, $formParams) {
          $query = "SELECT
                      last_attempt AS last_attempt,
                      failed_attempts AS failed_attempts
                    FROM auth_failed_logins
                    WHERE user_id = ?
                      AND ip = ?
                    LIMIT 1";
          $params = [
            $userData['account']['user_id'],
            inet_pton($_SERVER['REMOTE_ADDR'])
          ];
          $result = $_mysqli->prepared_query($query, 'ss', $params, [ 'collapseAll' => true ]);

          if ($result !== false) {
            $userData['attempts'] = array_merge_recursive($userData['attempts'], [
              'last'  => [ 'user_ip' => $result['last_attempt'] ],
              'count' => [ 'user_ip' => $result['failed_attempts'] ]
            ]);
          }
        })();
      }

      return $userData;
    })();
    /** Login Error Handling */
    $loginError = function ($error) use (&$form_authLogin, &$response) {
      $defaultParams = array_fill_keys(['error', 'param', 'message', 'provided', 'inherited'], null);

      $form_authLogin->updateProperty('formSubmit->success', false);
      $response->set(-1);
      $response->setError(errorObject(...array_values(array_replace_recursive($defaultParams, $error))));
    };
    $credentialError = function () use (&$loginError) {
      $loginError([
        'error' => 'invalidCredentials',
        'message' => 'Your Email Address or Password is incorrect. Please try again.'
      ]);
    };
    $logFailedAttempt = function () use (&$_mysqli, $userData) {
      $failedAttemptsUpdate = (function () {
        $updateThreshold = (function () {
          $time = new DateTime();
  
          $time->add(new DateInterval('PT12H'));
  
          return $time->getTimestamp();
        })();

        if (time() < $updateThreshold) {
          return "failed_attempts + 1";
        }
        else {
          return "1";
        }
      })();
      $query = "UPDATE auth_failed_logins
                SET last_attempt = ?,
                  failed_attempts = {$failedAttemptsUpdate}
                WHERE ip = ?
                  AND user_id = ?
                LIMIT 1";
      $params = [
        getFormattedTimestamp(),
        inet_pton($_SERVER['REMOTE_ADDR']),
        $userData['account']['user_id'] ?? null
      ];

      if ($params[2] === null) {
        $query = str_replace('user_id = ?', 'user_id IS NULL', $query);
        array_pop($params);
      }

      $result = $_mysqli->prepared_query($query, str_repeat('s', count($params)), $params, [ 'collapseAll' => true ]);

      if ($result == 0) {
        $query = "INSERT INTO auth_failed_logins
                  (ip, user_id, last_attempt)
                  VALUES (?, ?, ?)";
        $params = [
          $params[1],
          $params[2] ?? null,
          $params[0]
        ];
        $result = $_mysqli->prepared_query($query, 'sss', $params, [ 'collapseAll' => true ]);

        if ($result === false) {
          error_log("Form login Error: Failed to update auth_login_attempts for user \"{$params[1]}\" with an ip of \"{$params[0]}\"");
        }
      }
      else if ($result === false) {
        error_log("Form login Error: Failed to update auth_login_attempts for user \"{$params[3]}\" with an ip of \"{$params[2]}\"");
      }
    };

    if ($userData) {
      /** @var boolean Indicates if the login is currently throttled */
      $isThrottled = (function () use (&$_mysqli, $userData) {
        /** 
         * @var array Settings that control throttle threshold and timeout durations 
         * - *Values are supplied for each type of throttle: `user`, `ip`, and `user_ip`.*
         * - `array $throttleSettings` — Indicates how many failed attempts can be made before throttling the login.
         * - `array $timeouts` — Indicates the duration, in seconds, of the login throttle.
         */
        $throttleSettings = [
          'thresholds' => [
            'user'      => 30,
            'user_ip'   => 15,
            'ip'        => 60
          ],
          'timeouts' => [
            'user'      => 30,
            'user_ip'   => 15,
            'ip'        => 60
          ]
        ];
        
        foreach (array_keys($throttleSettings['thresholds']) as $throttleType) {
          $attempts = [
            'last'  => $userData['attempts']['last'][$throttleType] ?? "2020-01-01",
            'count' => $userData['attempts']['count'][$throttleType] ?? 0
          ];
          $throttle = [
            'threshold' => $throttleSettings['thresholds'][$throttleType],
            'duration'  => $throttleSettings['timeouts'][$throttleType]
          ];

          if ($attempts['count'] > $throttle['threshold']) {
            $throttleExpiration = (function () use ($attempts, $throttle) {
              $interval = $throttle['duration'] + (($attempts['count'] - $throttle['threshold']) * 2);
              $expiration = new DateTime($attempts['last']);

              $expiration->add(new DateInterval("PT{$interval}S"));

              return $expiration->getTimestamp();
            })();

            if (time() < $throttleExpiration) {
              return true;
            }
          }
          else if ($attempts['count'] == $throttle['threshold']) {
            // Log Throttle Event
            (function () use (&$_mysqli, $userData, $throttleType) {
              $query = "INSERT INTO logs_auth_throttles
                        (ip, user_id, type)
                        VALUES (?, ?, ?)";
              $params = [
                inet_pton($_SERVER['REMOTE_ADDR']),
                $userData['account']['user_id'],
                $throttleType
              ];
              $result = $_mysqli->prepared_query($query, 'sss', $params, [ 'collapseAll' => true ]);

              if ($result === false) {
                error_log("login form Error: Failed to record throttle event for \"{$params[0]}\" with an ip of \"${$params[1]}\"");
              }
            })();
          }
        }

        return false;
      })();

      if (!$isThrottled) {
        /** @var boolean Indicates if the provided password matches the one on file */
        $validPassword = auth_pwHashCheck($formParams['password'], $userData['account']['password']);

        if ($validPassword) {
          $userID = $userData['account']['user_id'];

          // Authorize the User
          auth_login($userID, true);
          // Update Logs
          (function () use (&$_mysqli, $userID) {
            // Update User Record
            (function () use (&$_mysqli, $userID) {
              $query = "UPDATE auth_records
                        SET last_login = ?
                        WHERE user_id = ?
                        LIMIT 1";
              $params = [
                getFormattedTimestamp(),
                $userID
              ];
              $result = $_mysqli->prepared_query($query, 'ss', $params, [ 'collapseAll' => true ]);

              if (!$result) {
                error_log("Form login Error: Failed to update auth_records for user \"{$userID}\"");
              }
            })();
            // Clear Failed Attempts
            (function () use (&$_mysqli, $userID) {
              $query = "DELETE FROM auth_failed_logins
                        WHERE ip = ?
                          AND user_id = ?
                        LIMIT 1";
              $params = [
                inet_pton($_SERVER['REMOTE_ADDR']),
                $userID
              ];
              $result = $_mysqli->prepared_query($query, 'ss', $params, [ 'collapseAll' => true ]);

              if ($result === false) {
                error_log("Form login Error: Failed to update auth_records for user \"{$userID}\"");
              }
            })();
          })();
          // Set "Remember Me" Token
          (function () use ($formParams) {
            if ($formParams['remember_me'] == 'true') {
              auth_rmb_update();
            }
          })();
        }
        // Password is Incorrect
        else {
          $logFailedAttempt();
          $credentialError();
        }
      }
      // Login is Throttled
      else {
        $loginError([
          'error' => 'throttledLogin',
          'message' => 'We could not log you in at this time. Please wait a few seconds and try again.'
        ]);
      }
    }
    // Invalid Username
    else {
      $logFailedAttempt();
      $credentialError();
    }
  }

  $form_authLogin->buildResponse();
  $response->send();
?>