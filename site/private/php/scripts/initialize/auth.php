<?php
  // Check Cookies
  if (!auth_isLoggedIn()) {
    $cookies = [
      'rmb'      => getCookie('rmb'),
      'redeemed' => getCookie('redeemed_codes')
    ];

    // "Remember Me" cookie
    if ($cookies['rmb']) {
      $userID = auth_rmb_check($cookies['rmb']);

      // Token exists and is valid
      if ($userID) {
        auth_login($userID);

        auth_rmb_update();
      }
    }
    // Redeemed SHiFT Codes cookie
    if ($cookies['redeemed']) {
      $redeemedCodes = json_decode($cookies['redeemed'], true);

      if ($redeemedCodes['isAccountBound']) {
        redeemed_codes_cookie();
      }
    }
  }
  if (auth_isLoggedIn()) {
    $userID = auth_user_id();
    $query = "SELECT last_auth
              FROM auth_records
              WHERE user_id='{$userID}'
              LIMIT 1";
    $sql = $_mysqli->query($query, [ 'collapseAll' => true ]);

    if ($sql) {
      $authTs = $_SESSION['user']['last_auth'];
      $lastAuth = new DateTime($sql);
      $lastAuth = $lastAuth->getTimestamp();
      $maxAge = new DateTime($sql);
      $maxAge->add(new DateInterval('P6M'));
      $maxAge = $maxAge->getTimestamp();
    
      if (time() > $maxAge) {
        // Update Last Auth
        (function () use (&$_mysqli, $userID) {
          $now = new DateTime();
          $now = $now->format(DATE_FORMATS['dateTime']);
          $query = "UPDATE auth_records
                    SET last_auth='{$now}'
                    WHERE user_id='{$userID}'
                    LIMIT 1";
          $sql = $_mysqli->query($query, [ 'collapseAll' => true ]);

          if (!$sql) {
            error_log("Authentication Error: Failed to update last_auth timestamp for user $userID.");
          } 
        })();
        // Update Remember Me Tokens
        (function () use (&$_mysqli, $userID) {
          $now = new DateTime();
          $now = $now->format(DATE_FORMATS['dateTime']);
          $query = "DELETE FROM auth_tokens
                    WHERE user_id='${userID}'
                      AND type='remember_me'";
          $sql = $_mysqli->query($query, [ 'collapseAll' => true ]);

          if (!$sql) {
            error_log("Authentication Error: Failed to remove Remember Me tokens for user $userID.");
          } 
        })();
      }
      if ($authTs < $lastAuth || time() > $maxAge) {
        auth_logout();
      }
    }
  }
?>