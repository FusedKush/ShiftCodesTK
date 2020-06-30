<?php
  /* Header Functions */
  /**
   * Redirect to a specified page
   * 
   * @param string $url The url to redirect to.
   * - *Note: The URL is **not** automatically URL Encoded*
   * @return exit
   */
  function response_redirect($url) {
    header("Location: $url");
    exit();
  }
  /**
   * Return an HTTP Status Code
   * 
   * @param int $code The Status Code
   * @param boolean $stopScript If true, an Error Notice or ResponseObject will be returned, and script execution will immediately halt.
   * @return void
   */
  function response_http($code, $stopScript = false) {
    $statusCode = STATUS_CODES[$code] ?? false;
    $httpCode = $statusCode && isset($statusCode['httpCode']) ? $statusCode['httpCode'] : $code;
    $httpStatusCode = STATUS_CODES[$httpCode];

    if (!$statusCode) {
      error_log("\"$code\" is not a valid Status Code.");
      return false;
    }

    header("{$_SERVER['SERVER_PROTOCOL']} {$httpCode} {$httpStatusCode['name']}");

    if ($stopScript) {
      if (SCRIPT_TYPE == 1) {
        errorNotice($code);
      }
      else {
        $response = new ResponseObject();
        $response->set($code);
        $response->send();
      }
      exit();
    }
  }
  /**
   * Set the Content-Type for a resource
   * 
   * @param string $contentType The MIME media type to set
   * @return void
   */
  function response_type($contentType) {
    header("Content-Type: $contentType");
  }
  // Cookies
  /**
   * Retrieve the value of a particular cookie
   * 
   * @param string $name The name of the cookie.
   * @return string|null Returns the string value of the cookie if set. Otherwise, **NULL**.
   */
  function getCookie ($name) {
    return $_COOKIE[$name] ?? null;
  }
  /**
   * Create or update a cookie
   * 
   * @param string $name The name of the cookie. Do not use control characters, spaces, tabs, or separator characters.
   * @param string $value The value of the cookie. Arrays and Objects will automatically be converted to **JSON**.
   * - *Note: Using **False** will try to delete the cookie. Use **0** and **1** for booleans instead.*
   * @param array &$options Additional options to pass to the cookie
   * - *@param* `string $[expires] = 'P1M'` The max-age of the cookie. This can be a *DateIntervalSpec string* to set the max-age relative to the current time, or the keywords **now** or **session** to expire the cookie immediately or at the end of the session, respectively.
   * - *@param* `string $[path] = '/'` The path on the server the cookie will be available on. This option will likely not be modified.
   * - *@param* `string $[domain] = '.shiftcodestk.com'` The domain the cookie is available to. This option will likely not be modified.
   * - *@param* `boolean $[secure] = true` Indicates that the cookie should only be transmitted over a secure connection from the client. This option will likely only be modified for local development purposes.
   * - *@param* `boolean $[httponly] = true` Indicates that the cookie should be accessible only through the HTTP protocol, and not through scripting languages. 
   * - *@param* `string $[samesite] = 'Lax'` Determines how the cookie functions with cross-origin requests using the keywords **None**, **Lax**, or **Strict**.
   * - - *Note: The keyword **None** requires the `Secure` option to be set to **True**.*
   * @return boolean Returns **True** if the cookie was successfully generated, or **false** on error. This *does not* indicate whether or not the user accepted the cookie.
   */
  function updateCookie ($name, $value, $options = []) {
    // Production Options
    $defaultOptions = [
      'expires'  => 'P1M',
      'path'     => '/',
      'domain'   => 'shiftcodestk.com',
      'secure'   => true,
      'httponly' => true,
      'samesite' => 'Lax'
    ];
    // Development Options
    $defaultOptions = [
      'expires'  => 'P1M',
      'path'     => '/',
      'domain'   => '',
      'secure'   => false,
      'httponly' => true,
      'samesite' => 'Lax'
    ];
    $cookieOptions = array_replace_recursive($defaultOptions, $options);

    // Update Arrays & Object values
    if (is_array($value) || is_object($value)) {
      $value = json_encode($value);
    }
    // Update Expiration Option
    (function () use (&$cookieOptions) {
      $timestamp = new DateTime();
      $option = &$cookieOptions['expires'];

      if ($option == 'now') {
        $timestamp->sub(new DateInterval('P1Y'));
        $timestamp = $timestamp->getTimestamp();
      }
      else if ($option == 'session') {
        $timestamp = 0;
      }
      else {
        $timestamp->add(new DateInterval($option));
        $timestamp = $timestamp->getTimestamp();
      }

      $option = $timestamp;
    })();

    return setcookie($name, $value, $cookieOptions);
  }
  /**
   * Delete a cookie
   * 
   * @param string $name The name of the cookie to be deleted
   * @param boolean $deleteImmediately If the cookie should be immediately deleted or removed at the end of the browsing session.
   * @return boolean Returns true if the cookie was updated. Otherwise, false
   */
  function deleteCookie ($name, $deleteImmediately = true) {
    $options = [ 
      'expires' => $deleteImmediately ? 'now' : 'session' 
    ];

    return updateCookie($name, false, [ 'expires' => $deleteImmediately ? 'now' : 'session' ]);
  }

  /* Auth Functions */
  // Randomly generated identifiers
  /**
   * Generate a random integer ID
   * 
   * @param int $length The length of the ID
   * @return string The generated ID
   */
  function auth_randomID($length = 12) {
    $getID = function ($idLength) {
      $min = (int) '1' . str_repeat('0', $idLength - 1);
      $max = (int) ('1' . str_repeat('0', $idLength)) - 1;
      
      return (string) random_int($min, $max);
    };

    if ($length <= 18) {
      return $getID($length);
    }
    else {
      $chunkedID = '';

      while (true) {
        $chunkLength = min($length - strlen($chunkedID), 18);

        if ($chunkLength != 0) {
          $chunkedID .= $getID($chunkLength);
        }
        else {
          return $chunkedID;
        }
      }  
    }
  }
  /**
   * Generate a random integer ID containing metadata information
   * 
   * @param int $length The length of the random integer ID.
   * - This value determines the length of the *random integer ID*, and does *not* include or affect the length of the `customMetaID`.
   * - If this value is _less than **14**_, the *timestamp* will be truncated from the left, until it is omitted entirely. 
   * - This value cannot be less than **4**.
   * @param string $customMetaID A custom integer ID to be included in the returned ID.
   * @param string $customMetaIDPosition Indicates where the custom integer ID is included in the returned ID. Possible values are **start**, **middle**, and **end**.
   * @return string|false Returns the generated integer ID on success, or **false** if an error occurred.
   */
  function auth_randomMetaID($length = 12, $customMetaID = '', $customMetaIDPosition = 'start') {
    $pieces = [
      'meta'      => $customMetaID,
      'timestamp' => substr((string) time(), $length >= 14 ? 0 : 0 - ($length - 4)),
      'random'    => auth_randomID($length >= 14 ? $length - 10 : 4)
    ];
    $orderedPieces = [];

    // Parameter Issues
    if ($length < 4) {
      trigger_error('The length of the random integer ID must be greater than 4.');
      return false;
    }
    else if (!is_numeric($customMetaID)) {
      trigger_error('The customMetaID must be a numeric integer ID.');
      return false;
    }
    else if (!check_match($customMetaIDPosition, [ 'start', 'middle', 'end' ])) {
      trigger_error('The value of customMetaIDPosition must be "start", "middle", or "end".');
      return false;
    }

    if ($customMetaIDPosition == 'start') {
      $orderedPieces = $pieces;
    }
    else if ($customMetaIDPosition == 'middle') {
      $orderedPieces = [
        $pieces['timestamp'],
        $pieces['meta'],
        $pieces['random']
      ];
    }
    else {
      $orderedPieces = [
        $pieces['timestamp'],
        $pieces['random'],
        $pieces['meta']
      ];
    }

    return implode('', $orderedPieces);
  }
  /**
   * Generate a random string key
   * 
   * @param int $length The length of the key
   * @return string The generated string
   */
  function auth_randomKey($length = 64) {
    $getKey = function ($keyLength) {
      if (function_exists('random_bytes')) {
        return bin2hex(random_bytes($keyLength / 2));
      }
      else if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes($keyLength / 2));
      }
    };

    return str_pad($getKey($length), $length, $getKey(2));
  }
  // Hashing
  /**
   * Generate a hash for a string
   * 
   * @param string $string The string to be hashed
   * @return string Returns the generated hash
   */
  function auth_strHash($string) {
    return hash_hmac('sha256', $string, TK_SECRETS['hash']);
  }
  /**
   * Determine if two string hashes match
   * 
   * @param string $firstHash The first hash to compare
   * @param string $secondHash The second hash to compare
   * @return boolean Returns true if hashes match. Otherwise, false
   */
  function auth_strHashCheck($firstHash, $secondHash) {
    return hash_equals($firstHash, $secondHash);
  }
  /**
   * Generate a hash for a password
   * 
   * @param string $password The password to be hashed. Maximum length is 72 characters
   * @return string|false On success, returns the hashed password. On failure, returns false 
   */
  function auth_pwHash($password) {
    if (strlen($password) > 72) {
      return false;
    }

    return password_hash($password, PASSWORD_BCRYPT);
  }
  /**
   * Determine a given password matches the hash
   * 
   * @param string $pwStr The password being checked
   * @param string $pwHash The hashed password
   * @return boolean Returns true if the password matches the hash. Otherwise, false
   */
  function auth_pwHashCheck($pwStr, $pwHash) {
    return password_verify($pwStr, $pwHash);
  }
  // Joined validation tokens
  /**
   * Generate a joined validation token
   * 
   * @param string|int $key If an integer is passed, it will be treated as the length of the generated key. If a key is passed, a new token will be generated using the existing key.
   * @param int $tokenLength The length of the token
   * @return array Returns an array made up of the key, original token, hashed token, and joined validation token.
   */
  function auth_valGetToken($key = 32, $tokenLength = 64) {
    $result = [];
    
    $result['key'] = (function () use ($key) {
      if (is_numeric($key))     { return auth_randomKey($key); }
      else if (is_string($key)) { return $key; }
      else                      { return auth_randomKey(16); }
    })();
    $result['token'] = auth_randomKey($tokenLength);
    $result['tokenHash'] = auth_strHash($result['token']);
    $result['fullToken'] = $result['key'] . ':' . $result['token'];

    return $result;
  }
  /**
   * Parse a joined validation token
   * 
   * @param string $fullToken The joined validation token to be parsed
   * @return array Returns an array made up of the key, original token, hashed token, and joined validation token.
   */
  function auth_valParseToken($fullToken) {
    $separatorPos = strpos($fullToken, ':');
    $result = [];

    $result['key'] = preg_replace('/\:[\w\d]+/', '', $fullToken);
    $result['token'] = preg_replace('/[\w\d]+\:/', '', $fullToken);
    $result['tokenHash'] = auth_strHash($result['token']);
    $result['fullToken'] = $fullToken;

    return $result;
  }

  // Authentication & User Management
  /**
   * Retrieve and update the user's information
   * 
   * @param string $id The user_id of the user
   * @return boolean Returns true if the user's information was successfully retrieved and updated. Otherwise, false. 
   */
  function auth_update_user_data ($id) {
    GLOBAL $_mysqli;

    $query = "SELECT
                au.username 
                  as username,
                ar.last_auth 
                  as last_auth,
                ar.last_activity
                as last_activity,
                au.role_badass 
                  as role_badass,
                au.role_admin
                  as role_admin,
                au.redemption_id
                  as redemption_id
              FROM auth_users as au
              LEFT JOIN auth_records as ar
                ON au.user_id = ar.user_id
              WHERE au.user_id = '{$id}'";
    $result = $_mysqli->query($query);
    $userData = $result[0];

    if (!$userData) {
      $_SESSION['toasts'][] = [
        'settings' => [
          'id' => 'login_form_response_toast',
          'duration' => 'medium',
          'template' => 'exception'
        ],
        'content' => [
          'title' => 'Login Error',
          'body' => 'We were unable to log you in to ShiftCodesTK due to an error retrieving your account information. Please try again later.'
        ]
      ];
      return false;
    }

    $lastAuth = new DateTime($userData['last_auth']);
    $lastAuth = $lastAuth->getTimestamp();
    $lastActivity = new DateTime($userData['last_activity']);
    $lastActivity = $lastActivity->getTimestamp();

    $_SESSION['user'] = [];
    $_SESSION['user']['id'] = $id;
    $_SESSION['user']['username'] = $userData['username'];
    $_SESSION['user']['last_auth'] = $lastAuth;
    $_SESSION['user']['last_activity'] = $lastActivity;
    $_SESSION['user']['last_check'] = time();
    $_SESSION['user']['roles'] = (function () use ($userData) {
      $roles = [];

      foreach (AUTH_ROLES['roles'] as $role) {
        $roles[$role] = $userData["role_{$role}"] ?? false;
      }
      
      return $roles;
    })();

    // Redemption ID cookie
    (function () use (&$_mysqli, $userData) {
      $cookie = getCookie(REDEMPTION_ID_COOKIE);
      $oldRedemptionID = redemption_get_id();
      $userRedemptionID = $userData['redemption_id'];

      if ($userRedemptionID) {
        redemption_update_cookie($userRedemptionID);
      }
      // Generate new Redemption ID
      else {
        $userRedemptionID = redemption_new_id();
      }
      // Add previously-redeemed SHiFT Codes to user's account 
      if ($cookie && !json_decode($cookie, true)['isAccountBound']) {
        (function () use (&$_mysqli, $oldRedemptionID, $userRedemptionID) {
          $query = "UPDATE shift_codes_redeemed
                    SET redemption_id = '{$userRedemptionID}'
                    WHERE redemption_id = '{$oldRedemptionID}'";

          $result = $_mysqli->query($query, [ 'collapseRow' => true ]);

          if ($result === false) {
            error_log("Failed to update redeemed SHiFT Codes for \"{$oldRedemptionID}\" and \"{$userRedemptionID}\"");
            return false;
          }
        })();
      }
    })();

    return true;
  }
  /** 
   * Login a user
   * 
   * @param string $id The id of the user
   * @param boolean $addSessionToast Whether or not to add a notification via session toast.
   * @return void
   */
  function auth_login($id, $addSessionToast = false) {
    GLOBAL $_mysqli;

    refreshSession();
    auth_update_user_data($id);

    if ($addSessionToast) {
      $_SESSION['toasts'][] = [
        'settings' => [
          'id'       => 'login_form_response_toast',
          'duration' => 'medium',
          'template' => 'formSuccess'
        ],
        'content' => [
          'title' => 'Welcome, <b>' . clean_all_html(auth_user_name()) . '</b>!',
          'body'  => 'You are now logged in to ShiftCodesTK.'
        ]
      ];
    }

    // Update last_login record
    (function () use (&$_mysqli) {
      $now = new DateTime();
      $sql = "UPDATE auth_records
              SET last_login = ?
              WHERE user_id = ?
              LIMIT 1";
      $params = [
        $now->format('Y-m-d H:i:s'),
        auth_user_id()
      ];

      $result = $_mysqli->prepared_query($sql, 'ss', $params, [ 'collapseAll' => true ]);

      if ($result === false) {
        error_log("auth_login Error: Failed to record last_login timestamp.");
      }
    })();
  }
  /**
   * Logout the current user
   *
   * @param boolean $addSessionToast Whether or not to add a notification via session toast
   * @return void
   */
  function auth_logout($addSessionToast = false) {
    if ($addSessionToast) {
      $_SESSION['toasts'][] = [
        'settings' => [
          'id'       => 'login_form_response_toast',
          'duration' => 'medium',
          'template' => 'formSuccess'
        ],
        'content' => [
          'title'    => 'Goodbye, <b>' . clean_all_html(auth_user_name()) . '</b>',
          'body'     => 'You are no longer logged in to ShiftCodesTK.'
        ],
        'actions' => [
          [
            'content' => 'Log back in',
            'title'   => 'Log back in to ShiftCodesTK',
            'link'    => '/login'
          ]
        ]
      ];
    }
    if (getCookie('rmb')) {
      auth_rmb_delete();
    }
    if (redemption_get_id()) {
      redemption_update_cookie(false);
    }

    unset($_SESSION['user']);
    refreshSession();
  }
  /**
   * Returns if the user is logged in or not.
   * 
   * @return boolean Returns true if the user is logged in. Otherwise, false
   */
  function auth_isLoggedIn () {
    return isset($_SESSION['user']);
  }
  /**
   * Returns the username of the user.
   * 
   * @return string|false The *Username* of the user if logged in, or **false** if the user is not currently logged in.
   */
  function auth_user_name () {
    return $_SESSION['user']['username'] ?? false;
  }
  /**
   * Returns the ID of the user.
   * 
   * @return string|false The *User ID* of the user if logged in, or **false** if the user is not currently logged in.
   */
  function auth_user_id () {
    return $_SESSION['user']['id'] ?? false;
  }
  /**
   * Returns the User Roles Array of the user
   * @return array The User's Roles Array 
   */
  function auth_user_roles () {
    return $_SESSION['user']['roles'] ?? array_fill_keys(AUTH_ROLES['roles'], false);
  }
  
  // "Remember me" token management
  /**
   * Check the database for a given "remember me" token
   * 
   * @param string $fullToken The full validation token
   * @return array Returns the user_id of the user who is to be granted access if the token is valid. If the token is invalid or does not exist, returns false.
   */
  function auth_rmb_check ($fullToken) {
    GLOBAL $_mysqli;

    $token = auth_valParseToken($fullToken);
    $sql = "SELECT val_token, user_id, issue_date
            FROM auth_tokens
            WHERE val_key = ?
              AND type = ?
            LIMIT 1";
    $params = [
      $token['key'],
      'remember_me'
    ];
    $result = $_mysqli->prepared_query($sql, 'ss', $params, [ 
      'preserveDate'        => ['user_id'], 
      'collapseResult'      => true, 
      'collapseQueryResult' => true 
    ]);

    if ($result) {
      $expiration = new DateTime($result['issue_date']);
      $expiration->add(new DateInterval('P1M'));

      // Token has expired or does not match
      if ($expiration->getTimestamp() < time() || !hash_equals($token['tokenHash'], $result['val_token'])) {
        auth_rmb_delete();

        return false;
      }
      // Token is valid
      else {
        return $result['user_id'];
      }
    }
    else {
      auth_rmb_delete();

      return false;
    }
  }
  /**
   * Update the "remember me" token
   * 
   * @return boolean Returns true on success, and false on failure
   */
  function auth_rmb_update () {
    GLOBAL $_mysqli;
    $keyLength = 16;
    $tokenLength = 32;
    $token = [];
    $cookie = getCookie('rmb');

    // Existing token
    if ($cookie) {
      $token = auth_valParseToken($cookie);
      $token = auth_valGetToken($token['key'], $tokenLength);
      $sql = "UPDATE auth_tokens
              SET val_token = ?
              WHERE val_key = ?
                AND type = ?
              LIMIT 1";
      $params = [
        $token['tokenHash'],
        $token['key'],
        'remember_me'
      ];
      $result = $_mysqli->prepared_query($sql, 'sss', $params);

      if ($result) {
        updateCookie('rmb', $token['fullToken'], [ 'expires' => 'P1M' ]);
      }
      
      return $result;
    }
    // New token
    else {
      $token = auth_valGetToken($keyLength, $tokenLength);

      while (true) {
        $uniqueKey = auth_rmb_check($token['fullToken']) === false;

        if (!$uniqueKey) {
          $token = auth_valGetToken($keyLength, $tokenLength);
          continue;
        }

        break;
      }

      $sql = "INSERT INTO auth_tokens
              (val_key, val_token, user_id, type)
              VALUES (?, ?, ?, ?)";
      $params = [
        $token['key'],
        $token['tokenHash'],
        auth_user_id(),
        'remember_me'
      ];
      $result = $_mysqli->prepared_query($sql, 'ssss', $params);

      if ($result) {
        updateCookie('rmb', $token['fullToken'], [ 'expires' => 'P1M' ]);
      }

      return $result;
    }
  }
  /**
   * Delete the stored "remember me" token
   * 
   * @return boolean Returns true on success, or false on failure.
   */
  function auth_rmb_delete () {
    GLOBAL $_mysqli;
    $cookie = getCookie('rmb');

    if ($cookie) {
      $token = auth_valParseToken($cookie);
      $sql = "DELETE FROM auth_tokens
              WHERE val_key = ?
                AND type = ?
              LIMIT 1";
      $params = [
        $token['key'],
        'remember_me'
      ];
      $result = $_mysqli->prepared_query($sql, 'ss', $params);
  
      if ($result) {
        deleteCookie('rmb');
      }
      else {
        trigger_error("auth_rmb_delete Error: Could not delete token from the database.");
      }
  
      return $result;
    }
    else {
      return false;
    }
  }

  /* Session Functions */
  /** 
   * Initialize the session
   * 
   * @return void
   */
  function startSession() {
    GLOBAL $_mysqli;

    // Session Properties
    if (!isset($_SESSION['session'])) {
      $_SESSION['session'] = [
        'active' => true,
        'start'  => time()
      ];
    }
    // Ensure user data is up to date
    if (auth_isLoggedIn()) {
      $threshold = new DateTime('@' . $_SESSION['user']['last_check']);
      $threshold = $threshold->add(new DateInterval('PT5M'));
      $threshold = $threshold->getTimestamp();

      if (time() > $threshold) {
        $query = "SELECT last_activity
                  FROM auth_records
                  WHERE user_id = ?
                  LIMIT 1";
        $result = $_mysqli->prepared_query($query, 's', [ auth_user_id() ], [ 'collapseAll' => true ]);

        if ($result) {
          $lastActivity = new DateTime($result);
          $lastActivity = $lastActivity->getTimestamp();

          if ($_SESSION['user']['last_activity'] < $lastActivity) {
            auth_update_user_data(auth_user_id());
          }
        }

        $_SESSION['user']['last_check'] = time();
      }
    }
    // Session toasts array
    if (!isset($_SESSION['toasts'])) {
      $_SESSION['toasts'] = [];
    }
    // Global Session Variables
    if (!isset($_SESSION['token'])) {
      $_SESSION['token'] = auth_randomKey(128);
    }
    // Session access timestamp
    if (!isset($_SESSION['timestamp'])) {
      $_SESSION['timestamp'] = time();
    }
  }
  /**
   * Refresh the session ID and archive the old session
   * 
   * @return void
   */
  function refreshSession() {
    $_SESSION['session']['active'] = false;
    $_SESSION['timestamp'] = time();
    session_regenerate_id(); // Refreshed Session
    unset($_SESSION['session'], $_SESSION['token'], $_SESSION['timestamp']);
    startSession();
  }

  /* Misc Functions */
  /**
   * Display an Error Notice to the user
   * 
   * @param int $code The Status Code of the associated error.
   * @return void
   */
  function errorNotice ($code) {
    define('ERROR_NOTICE_CODE', $code);
    include_once(HTML_INCLUDES_PATH . 'local/error-notice.php');
  }
  function errorObject ($type, $parameter = null, $message = null, $providedValue = null, $inheritedValue = null) {
    $keys = [
      'type',
      'parameter',
      'message',
      'provided_value',
      'inherited_value'
    ];
    $params = func_get_args();
    $object = [];

    foreach ($keys as $i => $key) {
      $param = $params[$i] ?? null;

      if ($param!== null) {
        $object[$key] = $param;
      }
    }

    return $object;
  }
  // Redeemed SHiFT Codes ID management
  /**
   * @var string The name of the Redemption ID Cookie
   */
  define("REDEMPTION_ID_COOKIE", 'redemption_id');
  /**
   * Generate and set a new Redemption ID
   * @return string|false Returns the new Redemption ID on success, or false on failure
   */
  function redemption_new_id () {
    GLOBAL $_mysqli;
    $newID = '';
    $success = true;

    // Generate and verify new ID
    (function () use (&$_mysqli, &$newID, &$success) {
      $generationAttempts = 0;

      while (true) {
        $newID = auth_randomKey(24);
        $query = "SELECT redemption_id
                  FROM shift_codes_redeemed
                  WHERE redemption_id = '{$newID}'
                  LIMIT 1";
        
        $generationAttempts++;

        if ($_mysqli->query($query, [ 'collapseAll' => true ]) == [] || $generationAttempts > 10) {
          if ($generationAttempts > 10) {
            error_log("Failed to generate a new Redemption ID.");
            $success = false;
          }
          
          break;
        }
      }
    })();
    // Update Cookie & Database if Logged In 
    (function () use (&$_mysqli, $newID, &$success) {
      if (auth_isLoggedIn()) {
        $userID = auth_user_id();
        $query = "UPDATE auth_users
                  SET redemption_id = '{$newID}'
                  WHERE user_id = '{$userID}'
                  LIMIT 1";
        
        if (!$_mysqli->query($query, [ 'collapseAll' => true ])) {
          error_log("Failed to update RedemptionID for user {$userID}.");
          $success = false;
        }
      }

      redemption_update_cookie($newID);
    })();

    return $newID;
  }
  /**
   * Update the user's Redemption ID cookie
   * 
   * @param string|false $redemptionID The Redemption ID to set, or false to clear the cookie
   * @return boolean|string Returns true on success and false on failure.
   */
  function redemption_update_cookie ($redemptionID = false) {
    if ($redemptionID) {
      $value = [
        'id'             => $redemptionID,
        'isAccountBound' => auth_isLoggedIn()
      ];
      $options = [
        'expires' => 'P6M'
      ];
  
      return updateCookie(REDEMPTION_ID_COOKIE, $value, $options);
    }
    else {
      return deleteCookie(REDEMPTION_ID_COOKIE);
    }
  }
  /**
   * Retrieve the user's Redemption ID
   * 
   * @return string|false Returns the user's Redemption ID if present, or false if the cookie has not been set
   */
  function redemption_get_id () {
    $cookie = getCookie(REDEMPTION_ID_COOKIE);

    return $cookie !== null ? json_decode($cookie, true)['id'] : false;
  }
  /* Misc helper functions */
  /**
   * Remove extraneous whitespace from a string
   *
   * @param string $str The string to clean
   * @return string The cleaned string
   */
  function collapseWhitespace($str) {
    return preg_replace('/\s+/', ' ', trim($str));
  }
  /**
   * Determines if a plural letter is needed based on a value
   *
   * @param number val The value to be evaluated
   * @param string letter='s' The letter to be returned if a plural is needed
   * @returns string Returns the specified letter if val is 1 or an empty string if number is any other value
   */
  function checkPlural ($val, $letter = 's') {
    if ($val != 1) { return 's'; }
    else           { return ''; }
  }
  /**
   * Determine if the request method is of a certain type
   * 
   * @param string $method The desired request method | [ GET | POST ]
   * @return boolean Returns true if the request method matches. Otherwise, false
   */
  function is_method($method) {
    $request = $_SERVER['REQUEST_METHOD'];

    if (strtoupper($method) === $request) {
      return true;
    }
    else {
      return false;
    }
  }
  /**
   * Get a formatted DateTime timestamp
   * 
   * @param string $format A valid date/time format
   * @param string $time A valid date/time string. 'now' returns the current date/time
   * @param object $timezone A valid DateTimezone object
   * @return string Returns the formatted timestamp
   */
  function getFormattedTimestamp ($format = DATE_FORMATS['dateTime'], $time = 'now', $timezone = null) {
    $date = new DateTime($time, $timezone);

    return $date->format($format);
  }
  /**
   * Determine if a string is a valid date.
   * 
   * @param string $date The string to validate.
   * @param array $formats An array of whitelisted formats. If omitted, the string will just have to be a valid date, regardless of format.
   * @return boolean Returns true if the string is a valid date and matches one of the provided formats. Otherwise, return false.
   */
  function validateDate ($date, $formats = []) {
    if (!is_string($date)) {
      return false;
    }

    if (count($formats) > 0) {
      foreach ($formats as $format) {
        $formattedDate = DateTime::createFromFormat($format, $date);
  
        if ($formattedDate && $formattedDate->format($format) == $date) {
          $parsedDate = date_parse_from_format($format, $date);

          return checkdate($parsedDate['month'], $parsedDate['day'], $parsedDate['year']);
        }
      }

      return false;
    }
    else {
      $parsedDate = date_parse($date);

      return checkdate($parsedDate['month'], $parsedDate['day'], $parsedDate['year']);
    }
  }
  /**
   * Determine if an array has string keys
   * 
   * @param array $array The array to check
   * @return boolean Returns true if the array has at least one string key. Otherwise, returns false
   */
  function is_array_associative (array $array) {
    return count(array_filter(array_keys($array), 'is_string')) > 0;
  }
  // array_key_first Polyfill
  if (!function_exists('array_key_first')) {
    /**
     * Get the first key from an array
     * 
     * @param array $arr The array 
     * @return string The first key from the array
     */
    function array_key_first(array $arr) {
      foreach($arr as $key => $unused) {
        return $key;
      }
      
      return NULL;
    }
  }
?>