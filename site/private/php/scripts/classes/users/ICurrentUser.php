<?php
  namespace ShiftCodesTK\Users;

  use ShiftCodesTK\Strings,
      ShiftCodesTK\Auth,
      ShiftCodesTK\Database;

  /** Sync the `CurrentUser` to the `Session`.
   * - Valid for the `sync_with_session()` method/
   * - - @see ICurrentUserInstance::sync_with_session()
   */
  const SYNC_TO_SESSION = 1;
  /** Sync the `Session` to the `Current User`.
   * - Valid for the `sync_with_session()` method/
   * - - @see ICurrentUserInstance::sync_with_session()
   */
  const SYNC_TO_OBJECT = 2;

  /** The `ICurrentUserInstanceManager` is responsible for maintaining the current instance of the `CurrentUser`. */
  abstract class ICurrentUserInstance extends IUserManager {
    /** @var CurrentUser|false If the User is currently *Logged In*, this is the `CurrentUser`. Otherwise, **false**. */
    protected static $current_user = null;

    /** Refresh the `CurrentUser` data. 
     * 
     * @param null|string $user_id The `user_id` of the *User* that is being logged in. Can be omitted if no user is being logged in.
     * @param bool $fetch_user_data Indicates if the full `UserRecord` is to be retrieved for the user.
     * - If **false**, only the `SYNCED_SESSION_PROPERTIES` will be populated and available for use.
     * @return CurrentUser Returns the new `CurrentUser` object.
     */
    protected static function refresh_current_user (string $user_id = null, bool $fetch_user_data = false) {
      if ($user_id) {
        self::$current_user = new CurrentUser($user_id, $fetch_user_data);
      }
      else {
        self::$current_user = false;
      }

      return self::$current_user;
    }
    /** Retrieve the *Current User*
      *
      * **Note**: If the User is not currently logged in and an invalid *Persistent Login Token* if detected, it will automatically be removed.
      * 
      * @param bool $fetch_user_data Indicates if the full `UserRecord` is to be retrieved for the user.
      * - If **false**, only the `SYNCED_SESSION_PROPERTIES` are guaranteed to be populated and available for use.
      * @return CurrentUser Returns the active instance of `CurrentUser`.
      */
    public static function get_current_user (bool $fetch_user_data = false) {
      if (self::$current_user === null) {
        // Check Session
        if (isset($_SESSION['user']) && isset($_SESSION['user']['user_id'])) {
          self::refresh_current_user($_SESSION['user']['user_id'], $fetch_user_data);
        }
        else {
          self::refresh_current_user();
        }
      }
      else if (self::$current_user && $fetch_user_data && !isset(self::$current_user->email_address)) {
        self::refresh_current_user(self::$current_user->user_id, $fetch_user_data);
      }

      return self::$current_user;
    }
  } 
  /** The `IPersistentTokensManager` is responsible for managing *Persistent Login Tokens*. */
  abstract class IPersistentTokens extends ICurrentUserInstance {
    /** @var string A `DateIntervalSpec String` representing the total lifetime of *Persistent Tokens*.  */
    public const PERSISTENT_TOKEN_LIFETIME = 'P1M';
    /** @var string The `auth_tokens.val_type` database name. */
    public const PERSISTENT_TOKEN_TYPE_NAME = 'persistent_login';
    /** @var string The name of the *Persistent Login Token Cookie*. */
    public const PERSISTENT_TOKEN_COOKIE_NAME = 'persistent_login';

    /** @var null|JoinedValidationToken If set, this is the User's `JoinedValidationToken` repesenting the *Persistent Login Token* used to automatically sign in. */
    public $persistent_token = null;

    /** Create a new *Persistent Login Token* for the User.
     * 
     * **Note**: This method must be called *before* any other page output or the cookie will not updated.
     * 
     * @return string|false On success, returns the new *Persistent Login Token* for the User. Returns **false** if an error occurred.
     */
    public function create_persistent_token () {
      while (true) {
        $this->persistent_token = Auth\JoinedValidationToken::create_token();
        $isValidToken = self::validate_persistent_token($this->persistent_token->get_token()) === false;

        if ($isValidToken) {
          break;
        }
      }

      $queryStr = "INSERT INTO `auth_tokens`
                   (`val_key`, `val_token`, `val_type`, `user_id`)
                   VALUES (?, ?, ?, ?)";
      $queryParams = [
        $this->persistent_token->key,
        $this->persistent_token->token_hash,
        self::PERSISTENT_TOKEN_TYPE_NAME,
        $this->user_id
      ];
      $query = new \ShiftCodesTKDatabaseQuery(
        $queryStr, 
        [ 'collapse_all' => true ], 
        new \ShiftCodesTKDatabasePreparedVariables('ssss', $queryParams)
      );
      $queryResult = $query->query();

      if ($queryResult) {
        updateCookie(self::PERSISTENT_TOKEN_COOKIE_NAME, $this->persistent_token->get_token(), [ 'expires' => self::PERSISTENT_TOKEN_LIFETIME ]);

        return $this->persistent_token->get_token();
      }

      return false;
    }
    /** Refresh the *Persistent Login Token* for the User
     * 
     * This will generate a new *Validation Token* and reset the *Expiration Date*.
     * 
     * **Note**: This method must be called *before* any other page output or the cookie will not updated.
     * 
     * **Note**: The user must already have a *Persistent Login Token* for this method to work.
     * 
     * @return string|false On success, returns the updated *Persistent Login Token* for the User. Returns **false** if an error occurred.
     */
    public function refresh_persistent_token () {
      if (!$this->persistent_token) {
        return false;
      }

      // Refresh Token
      $this->persistent_token->new_token();

      $queryStr = "UPDATE `auth_tokens`
                   SET 
                      `val_token` = ?,
                      `issue_date` = ?
                   WHERE 
                      `val_key` = ?
                      AND `val_type` = ?
                   LIMIT 1";
      $queryParams = [
        $this->persistent_token->token_hash,
        (new \DateTime())->format(\ShiftCodesTK\DATE_FORMATS['date_time']),
        $this->persistent_token->key,
        self::PERSISTENT_TOKEN_TYPE_NAME
      ];
      $query = new \ShiftCodesTKDatabaseQuery(
        $queryStr, 
        [ 'collapse_all' => true ], 
        new \ShiftCodesTKDatabasePreparedVariables('ssss', $queryParams)
      );
      $queryResult = $query->query();

      if ($queryResult) {
        updateCookie(self::PERSISTENT_TOKEN_COOKIE_NAME, $this->persistent_token->get_token(), [ 'expires' => self::PERSISTENT_TOKEN_LIFETIME ]);

        return $this->persistent_token->get_token();
      }
    }
    /** Delete all of the User's *Persistent Login Tokens*.
     * 
     * **Note**: This method must be called *before* any other page output or the cookie will not updated.
     * 
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function delete_all_persistent_tokens () {
      $token = self::check_persistent_token();

      if ($token) {
        $tokenType = self::PERSISTENT_TOKEN_TYPE_NAME;
        $queryStr = "DELETE FROM `auth_tokens`
                     WHERE 
                        `user_id` = '{$this->user_id}'
                        AND `val_type` = '{$tokenType}'";
        $query = new \ShiftCodesTKDatabaseQuery($queryStr, [ 'collapse_all' => true ]);
        $queryResult = $query->query();

        deleteCookie(self::PERSISTENT_TOKEN_COOKIE_NAME);

        if (self::$current_user) {
          self::$current_user->persistent_token = null;
        }

        return true;
      }

      return false;
    }

    /** Check and validate a *Persistent Login Token*.
     * 
     * @param string $token The *Joined Validation Token* to be validated.
     * @param bool $log_errors Indicates if issues with tokens are to be logged. Used for debugging.
     * @return bool Returns **true** if the the `$token` is a valid *Persistent Login Token*, or **false** if it is not.
     */
    public static function validate_persistent_token (string $token, bool $log_errors = false) {
      $logError = function ($error) use ($log_errors) {
        if ($log_errors) {
          trigger_error("Persistent Login Token Validation Failed: {$error}");
        }
      };

      try {
        $parsedToken = new Auth\JoinedValidationToken($token);

        if ($parsedToken === null) {
          $logError('The Token is not a valid Joined Validation Token.');
          return false;
        }

        $queryStr = (function () use ($parsedToken) {
          $tokenKey = Strings\escape_sql($parsedToken->key);
          $tokenType = self::PERSISTENT_TOKEN_TYPE_NAME;
          $queryStr = "SELECT 
                        `val_token`, 
                        `issue_date`
                       FROM `auth_tokens`
                       WHERE 
                        `val_key` = '{$tokenKey}' 
                        AND `val_type` = '{$tokenType}'
                       LIMIT 1";
          
          return $queryStr;
        })();
        $query = new \ShiftCodesTKDatabaseQuery($queryStr, [ 'collapse_all' => true ]);
        $queryResult = $query->query();

        if ($queryResult) {
          $expirationThreshold = (new \DateTime($queryResult['issue_date']))
                                  ->add(new \DateInterval(self::PERSISTENT_TOKEN_LIFETIME))
                                  ->getTimestamp();

          if (!Auth\check_hash_string($queryResult['val_token'], $parsedToken->token_hash)) {
            $logError("The Validation Token is Invalid.");
            return false;
          }
          else if ($expirationThreshold < time()) {
            $logError("The Token has Expired.");
            return false;
          }
          
          // Token matches and has not expired
          return true;
        }
      }
      catch (\Throwable $exception) {
        $logError($exception->getMessage());
      }

      return false;
    }
    /** Checks for the User's *Persistent Login Token Cookie*.
     * 
     * @return JoinedValidationToken|false Returns a `JoinedValidationToken` representing the User's *Persistent Login Token* if they have one. Otherwise, returns **false**.
     */
    public static function check_persistent_token_cookie () {
      $token = false;
      $cookie = getCookie(self::PERSISTENT_TOKEN_COOKIE_NAME);

      if ($cookie !== null) {
        try {
          $token = new Auth\JoinedValidationToken($cookie);
        }
        catch (\Throwable $exception) {
          return false;
        }
      }

      return $token;
    }
    /** Checks for the User's *Persistent Login Token*.
     * 
     * On success, this function will also set the `persistent_token` property if it has not already been.
     * 
     * @return JoinedValidationToken|false Returns a `JoinedValidationToken` representing the User's *Persistent Login Token* if they have one. Otherwise, returns **false**.
     */
    public static function check_persistent_token () {
      $token = false;

      if (self::$current_user && self::$current_user->persistent_token) {
        $token = self::$current_user->persistent_token;
      }
      else {
        $cookie = self::check_persistent_token_cookie();

        if ($cookie) {
          $token = $cookie;
        }
      }

      if (self::$current_user && !self::$current_user->persistent_token) {
        self::$current_user->persistent_token = $token;
      }

      return $token;
    }
    /** Delete the User's *Persistent Login Token*.
     * 
     * **Note**: This method must be called *before* any other page output or the cookie will not updated.
     * 
     * @return bool Returns **true** on success and **false** on failure.
     */
    public static function delete_persistent_token () {
      $token = self::check_persistent_token();

      if ($token) {
        $tokenKey = $token->key;
        $tokenType = self::PERSISTENT_TOKEN_TYPE_NAME;
        $queryStr = "DELETE FROM `auth_tokens`
                     WHERE 
                        `val_key` = '{$tokenKey}'
                        AND `val_type` = '{$tokenType}'
                     LIMIT 1";
        $query = new \ShiftCodesTKDatabaseQuery($queryStr, [ 'collapse_all' => true ]);
        $queryResult = $query->query();

        deleteCookie(self::PERSISTENT_TOKEN_COOKIE_NAME);

        if (self::$current_user) {
          self::$current_user->persistent_token = null;
        }

        return true;
      }

      return false;
    }
  }
  /** `IUserSessionManager` is responsible for syncing the `CurrentUser` with the current `Session`. */
  abstract class IUserSessionManager extends IPersistentTokens {
    /** @var array A list of properties that are synced with the `Session`. */
    private const SYNCED_SESSION_PROPERTIES = [
      'user_id',
      'username',
      'account_state',
      'user_roles',
      'redemption_id',
      'last_auth',
      'last_activity'
    ];

    /** Sync the `CurrentUser` and the `Session`
     * 
     * @param SYNC_TO_SESSION|SYNC_TO_OBJECT $sync_direction Indicates in which direction the *User Data* should be synced to. Defaults to **SYNC_TO_SESSION**.
     * 
     * | Direction | Description |
     * | --- | --- |
     * | `SYNC_TO_SESSION` | Sync the `CurrentUser` with the `Session` |
     * | `SYNC_TO_OBJECT` | Sync the `Session` with the `CurrentUser` |
     * @return bool Returns **true** on success and **false** on failure.
     * @throws \UnexpectedValueException if an invalid `$sync_direction` is provided.
     */
    public function sync_with_session (int $sync_direction = SYNC_TO_SESSION) {
      if ($sync_direction == SYNC_TO_SESSION) {
        $_SESSION['user'] = [];
  
        foreach (self::SYNCED_SESSION_PROPERTIES as $property) {
          $_SESSION['user'][$property] = $this->$property;
        }
  
        $_SESSION['user']['last_check'] = time();
        return true;
      } 
      else if ($sync_direction == SYNC_TO_OBJECT) {
        if (isset($_SESSION['user'])) {
          foreach (self::SYNCED_SESSION_PROPERTIES as $property) {
            if (isset($_SESSION['user'][$property])) {
              $this->$property = $_SESSION['user'][$property];
            }
          }

          return true;
        }
      }
      else {
        throw new \UnexpectedValueException("\"{$sync_direction}\" is not a valid Syncing Direction Constant.");
      }
      
      return false;
    }
    /** Clear the `CurrentUser` *User Data* from the `Session`.
     * 
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function clear_from_session () {
      unset($_SESSION['user']);
      
      return isset($_SESSION['user']);
    }
  }
  /** The `ILoginManager` is responsible for managing the login of a user. */
  abstract class ILoginManager extends IUserSessionManager {
    /** @var array An array of *Login Throttle Thresholds* that determine how many failed attempts of each type are permitted before a throttle event should occur. */
    protected const LOGIN_THROTTLE_THRESHOLDS = [
      'user'        => 30,
      'user_ip'     => 15,
      'user_and_ip' => 60
    ];
    /** @var array An array of *Login Throttle Timeouts* that determine how long in *seconds* a basic *Login Throttle* should last. 
     * - This value is multiplied by each additional failed attempt after the *threshold*. `30, 60, 90, etc...`
     **/
    protected const LOGIN_THROTTLE_TIMEOUTS = [
      'user'        => 30,
      'user_ip'     => 15,
      'user_and_ip' => 60
    ];
    protected const LOGIN_ERROR_INVALID_CREDENTIALS = 1;
    /** 
     * @var array A list of *Error Codes* and *Error Messages* representing errors that may be returned by `get_last_login_error()`. 
     **/
    protected const LOGIN_ERRORS = [
      1 => 'Provided Credentials are Invalid',
      2 => 'Login is currently being Throttled',
      4 => 'Persistent Login Token could not be validated'
    ];

    /**
     * @var false|\Error If available, the last error that occurred when attempting to login.
     * - Can be retrieved using `get_last_login_error()`.
     */
    protected static $last_login_error = false;
    /** 
     * @var bool Indicates if the Current User is *Logged In* or not. 
     * - You can use `is_logged_in()` to check if the Current User is current *Logged In*.
     **/
    public $login_state = false;

    /** Login to a ShiftCodesTK Account
     * 
     * This method performs the procedure to log the user in to their account. Use {@see `login_with_credentials()`} or {@see `login_with_token()`} to perform an authenticated login.
     * 
     * @param string $user_id The `user_id` of the *User* that is being logged in.
     * @return bool Returns **true** if the User was Logged In successfully, or **false** if an error occurred.
     * @throws \Error if a user is already logged in or if the `$user_id` is invalid.
     */
    protected static function login (string $user_id) {
      if (self::is_logged_in()) {
        throw new \Error("A User is already Logged In.");
      }

      // Update User Record & Clear Failed Logins
      (function () use ($user_id) {
        $user_ip = inet_pton($_SERVER['REMOTE_ADDR']);
        $user_id_val = Strings\escape_sql($user_id);
        $query_str = "UPDATE `auth_user_records`
                        SET `last_login` = CURRENT_TIMESTAMP()
                        WHERE `user_id` = '{$user_id_val}'
                        LIMIT 1;
                      DELETE FROM `auth_failed_logins`
                        WHERE `ip` = '{$user_ip}'
                          AND `user_id` = '{$user_id_val}'
                        LIMIT 1;";
        $query = new \ShiftCodesTK\Database\DatabaseQuery($query_str, [ 'allow_multiple_queries' => true, 'collapse_all' => true ]);
        $query_result = $query->query();

        if (!$query_result[0] && !$query_result[1]) {
          trigger_error("The User Record & Failed Logins were not successfully updated for user \"{$user_id_val}\".");
        }
      })();

      refreshSession();
      self::refresh_current_user($user_id, true)
        ->sync_with_session();
    }
    /** Log a *Failed Login Attempt* to the database
     * 
     * @param string|null $user_id If a valid User was specified, the `user_id` of the User.
     * @return bool Returns **true** on success and **false** on failure.
     */
    protected static function log_failed_login_attempt (string $user_id = null) {
      $user_ip = inet_pton($_SERVER['REMOTE_ADDR']);
      $userIDStmt = $user_id
                    ? "`user_id` = '{$user_id}'"
                    : "`user_id` IS NULL"; 
      $recordExists = (function () use ($user_ip, $userIDStmt) {
        $queryStr = "SELECT COUNT(*)
                    FROM `auth_failed_logins`
                    WHERE 
                      `ip` = '{$user_ip}'
                      AND {$userIDStmt}
                    LIMIT 1";
        $query = new \ShiftCodesTKDatabaseQuery($queryStr, [ 'collapse_all' => true ]);
        $queryResult = $query->query();

        return $queryResult;
      })();
      $query = null;

      if (!$recordExists) {
        $queryStr = "INSERT INTO `auth_failed_logins`
                      (`ip`, `user_id`, `last_attempt`)
                      VALUES (?, ?, ?)";
        $queryParams = [
          $user_ip,
          $user_id,
          getFormattedTimestamp()
        ];
        $query = new \ShiftCodesTKDatabaseQuery(
          $queryStr, 
          [ 'collapse_all' => true ], 
          new \ShiftCodesTKDatabasePreparedVariables('sss', $queryParams)
        );
      }
      else {
        $queryStr = "UPDATE `auth_failed_logins`
                    SET 
                      `last_attempt` = CURRENT_TIMESTAMP(),
                      `failed_attempts` = `failed_attempts` + 1
                    WHERE
                      `ip` = '{$user_ip}'
                      AND {$userIDStmt}
                    LIMIT 1";
        $query = new \ShiftCodesTKDatabaseQuery(
          $queryStr, 
          [ 'collapse_all' => true ] 
        );
      }
      $queryResult = $query->query();

      if (!$queryResult) {
        error_log("Failed to record failed login attempt for user \"{$user_id}\" with an ip of \"{$user_ip}\".");
        return false;
      }

      return true;
    }
    /** Determine if a login from the current user is being *throttled*.
     * 
     * @param string|null $user_id If a matching user was provided, the `user_id` of the user.
     * @return bool Returns **true** if the user is currently being *throttled* and prevented from logging in, and **false** if they are permitted to login.
     */
    protected static function is_login_throttled (string $user_id = null) {
      $user_ip = inet_pton($_SERVER['REMOTE_ADDR']);
      $current_user = self::get_current_user();
      $failed_attempts = (function () use (&$user_id, $user_ip) {
        $attempts = [];
        $user_id = is_string($user_id)
                   ? Strings\escape_sql($user_id)
                   : $user_id;
        $query_str = "SELECT
                        MAX(`last_attempt`) AS 'last_user_attempt',
                        SUM(`failed_attempts`) AS 'failed_user_attempts'
                      FROM `auth_failed_logins`
                      WHERE `user_id` = '{$user_id}'
                      LIMIT 1;
                    SELECT
                        MAX(`last_attempt`) AS 'last_user_ip_attempt',
                        SUM(`failed_attempts`) AS 'failed_user_ip_attempts'
                      FROM `auth_failed_logins`
                      WHERE `ip` = '{$user_ip}'
                      LIMIT 1;
                    SELECT
                        `last_attempt` AS 'last_user_and_ip_attempt',
                        `failed_attempts` AS 'failed_user_and_ip_attempts'
                      FROM `auth_failed_logins`
                      WHERE 
                        `user_id` = '{$user_id}'
                        AND `ip` = '{$user_ip}'
                      LIMIT 1;";
        $query = new \ShiftCodesTKDatabaseQuery($query_str, [ 'allow_multiple_queries' => true, 'collapse_all' => true ]);
        $query_result = $query->query();

        if ($query_result) {
          $sub_query_names = [
            'user',
            'user_ip',
            'user_and_ip'
          ];

          $attempts = [];

          foreach ($query_result as $subQuery => $subQueryResult) {
            $subQueryType = $sub_query_names[$subQuery];
            $attempts[$subQueryType] = [
              'last_attempt'    => $subQueryResult["last_{$subQueryType}_attempt"] ?? null,
              'failed_attempts' => $subQueryResult["failed_{$subQueryType}_attempts"] ?? 0
            ];
          }

          // $userData['attempts'] = $queryResult;
        }

        return $attempts;
      })();
      
      foreach (array_keys(self::LOGIN_THROTTLE_THRESHOLDS) as $throttle_type) {
        $attempts = $failed_attempts[$throttle_type];
        $threshold = self::LOGIN_THROTTLE_THRESHOLDS[$throttle_type];
        $timeout = self::LOGIN_THROTTLE_TIMEOUTS[$throttle_type];

        if ($attempts['failed_attempts'] > $threshold) {
          $throttleExpiration = (function () use ($attempts, $threshold, $timeout) {
            $interval = $timeout + (($attempts['failed_attempts'] - $threshold) * 2);
            $expiration = (new \DateTime($attempts['last_attempt'], new \DateTimeZone('UTC')))
                          ->add(new \DateInterval("PT{$interval}S"))
                          ->getTimestamp();
            return $expiration;
          })();

          if (time() < $throttleExpiration) {
            return true;
          }
        }
        else if ($attempts['failed_attempts'] == $threshold) {
          $query_str = "INSERT INTO `logs_auth_throttles`
                      (`ip`, `user_id`, `type`)
                      VALUES (?, ?, ?)";
          $query_params = [
            $user_ip,
            $user_id,
            $throttle_type
          ];
          $query = new \ShiftCodesTKDatabaseQuery($query_str, [ 'collapse_all' => true ], new \ShiftCodesTKDatabasePreparedVariables('sss', $query_params));
          $query_result = $query->query();

          if (!$query_result) {
            error_log("login form Error: Failed to record throttle event for \"{$user_id}\" with an ip of \"{$user_ip}\"");
          }
        }
      }

      return false;
    }
    /** Update the *last Login Error*
     * 
     * @param int|null $error_code An *Error Code* corresponding to the `LOGIN_ERRORS` error to be set. 
     * 
     * | Error Code | Error Message |
     * | --- | --- |
     * | 1 | *Provided Credentials are Invalid* |
     * | 2 | *Login is currently being Throttled* |
     * | 4 | *Persistent Login Token could not be validated* |
     * 
     * - If omitted, the last login error will be cleared.
     * @return void 
     */
    protected static function update_last_login_error (int $error_code = null) {
      if (isset($error_code) && !isset(self::LOGIN_ERRORS[$error_code])) {
        throw new \UnexpectedValueException("\"{$error_code}\" is not a valid Error Code.");
      }

      self::$last_login_error = isset($error_code)
                                ? new \Error(self::LOGIN_ERRORS[$error_code], $error_code) 
                                : false;
    }

    /** Determines if the user is currently *Logged In*
     * 
     * @return bool Returns **true** if the user is currently logged in to their ShiftCodesTK Account, or **false** if they are not.
     */
    public static function is_logged_in () {
      $currentUser = self::get_current_user();

      return $currentUser && $currentUser->login_state;
    }
    /** Login a user using their *Login Credentials*
     * 
     * @param string $email_address The *Email Address* of the use to login.
     * @param string $password The User's *Password*.
     * @param bool $add_toast Indicates if a *Session Toast* should be created when the user is successfully logged in.
     * @return CurrentUser|false Returns the `CurrentUser` on success aor **false** if the User could not be logged in.
     * - Use `get_last_login_error()` to retrieve the error that caused this method to fail.
     * @throws \Error if a user is already logged in.
     */
    public static function login_with_credentials (string $email_address, string $password, bool $add_toast = false) {
      self::update_last_login_error();

      $userData = (function () use ($email_address) {
        $userData = false;
        $emailParam = Strings\escape_sql($email_address);
        $queryStr = "SELECT
                      `user_id`,
                      `username`,
                      `password`
                    FROM `auth_users`
                    WHERE `email_address` = '{$emailParam}'
                    LIMIT 1";
        $query = new \ShiftCodesTKDatabaseQuery($queryStr, [ 'collapse_all' => true ]);
        $queryResult = $query->query();

        if ($queryResult) {
          $userData = $queryResult;
        }
        
        return $userData;
      })();
      $userID = $userData['user_id'] ?? null
                ? Strings\escape_sql($userData['user_id'])
                : null;

      if ($userData) {
        if (!self::is_login_throttled($userID)) {
          if (Auth\check_hash_password($password, $userData['password'])) {
            self::login($userID);

            if ($add_toast) {
              $_SESSION['toasts'][] = [
                'settings' => [
                  'id'       => 'login_toast',
                  'duration' => 'medium',
                  'template' => 'formSuccess'
                ],
                'content' => [
                  'title' => 'Welcome, <b>' . clean_all_html(self::get_current_user()->username) . '</b>!',
                  'body'  => 'You are now logged in to ShiftCodesTK.'
                ]
              ];
            }
          }
          // Incorrect Password
          else {
            self::update_last_login_error(1);
          }
        }
        // Login is throttled
        else {
          self::update_last_login_error(2);
        }
      }
      // Account was not found
      else {
       self::update_last_login_error(1);
      }

      return self::get_current_user();
    }
    /** Login a user using a *Persistent Login Token*
     * 
     * @param string $token The *Persistent Login Token* to authenticate with.
     * @return CurrentUser|false Returns the new `CurrentUser` object on success, or **false** if the user could not be logged in.
     * @throws \Error if a user is already logged in.
     */
    public static function login_with_token (string $token) {
      self::update_last_login_error();

      if (!self::validate_persistent_token($token, true)) {
        $last_login_error = new \Error("The Persistent Login Token is Invalid.");
        return false;
      }

      $parsedToken = new Auth\JoinedValidationToken($token);
      $queryStr = (function () use ($parsedToken) {
        $tokenKey = Strings\escape_sql($parsedToken->key);
        $tokenType = self::PERSISTENT_TOKEN_TYPE_NAME;
        $queryStr = "SELECT `user_id`
                     FROM `auth_tokens`
                     WHERE 
                       `val_key` = '{$tokenKey}' 
                       AND `val_type` = '{$tokenType}'
                     LIMIT 1";
        
        return $queryStr;
      })();
      $query = new \ShiftCodesTKDatabaseQuery($queryStr, [ 'collapse_all' => true ]);
      $queryResult = $query->query();

      if ($queryResult) {
        self::login($queryResult);

        return self::is_logged_in();
      }

      self::update_last_login_error(4);
      return false;
    }
    /** Retrieve the last error that was thrown while attempting to login.
     * 
     * This returns the last `Throwable` error that caused a call to `login_with_credentials()` or `login_with_token()` to fail.
     * 
     * @return \Throwable|false Returns the `Throwable` error or exception that caused the login to fail. If no error has occurred, returns **false**.
     */
    public static function get_last_login_error () {
      return self::$last_login_error;
    }

    /** Logout of a ShiftCodesTK Account
     * 
     * @param bool $add_toast Indicates if a *Session Toast* should be created when the user is successfully logged out.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function logout (bool $add_toast = false) {
      $current_user = self::get_current_user();

      if ($add_toast) {
        $currentURL = (function () {
          $url = '';
          
          if (isset($_SERVER["HTTP_REFERER"])) {
            $url = Strings\slice($_SERVER['HTTP_REFERER'], Strings\strlen($_SERVER['HTTP_ORIGIN']));
          }
          else if (!Strings\substr_check($_SERVER['REQUEST_URI'], '/assets/requests/')) {
            $url = $_SERVER['REQUEST_URI'];
          }

          return $url;
        })();

        $_SESSION['toasts'][] = [
          'settings' => [
            'id'       => 'logout_toast',
            'duration' => 'medium',
            'template' => 'formSuccess'
          ],
          'content' => [
            'title'    => 'Goodbye, <b>' . Strings\encode_html($current_user->username) . '</b>',
            'body'     => 'You are no longer logged in to ShiftCodesTK.'
          ],
          'actions' => [
            [
              'content' => 'Log back in',
              'title'   => 'Return to the Login Page',
              'link'    => $currentURL ? "/account/login?continue={$currentURL}" : "/account/login"
            ]
          ]
        ];
      }

      $current_user->delete_persistent_token();
      $current_user->clear_from_session();
      refreshSession();
      self::refresh_current_user();
    }
  }

  /** The `ICurrentUser` is responsible for managing the *Current User* of the session. */
  abstract class ICurrentUser extends ILoginManager {
    /** Refresh the *Last Authentication Timestamp* for the user.
     * 
     * This will invalidate all *Persistent Login Tokens*, and cause the user to be logged out everywhere.
     * - If the user is currently logged in, they will be issued a new *Persistent Login Token* and will not be logged out here.
     * 
     * @return bool Returns **true** on success and **false** on failure.
     * - This will return **false** on success if the updated timestamp is the same as the one stored in the database.
     */
    public function refresh_user_auth () {
      $this->last_auth = getFormattedTimestamp(\ShiftCodesTK\DATE_FORMATS['date_time']);

      $queryStr = "UPDATE `auth_user_records`
                   SET `last_auth` = '{$this->last_auth}'
                   WHERE `user_id` = {$this->user_id}
                   LIMIT 1;";
      $query = new Database\DatabaseQuery($queryStr, [ 'collapse_all' => true ]);
      $queryResult = $query->query();

      if ($queryResult == 1) {
        $hasPersistentToken = $this->check_persistent_token();

        $this->delete_all_persistent_tokens();

        if ($hasPersistentToken) {
          $this->create_persistent_token();
        }

        return true;
      }

      return false;
    }
  }
?>