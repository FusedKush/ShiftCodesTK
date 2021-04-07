<?php
  // Settings
  ini_set('session.name', 'session');
  ini_set('session.use_cookies', 'On');
  ini_set('session.use_only_cookies', 'On');
  ini_set('session.use_strict_mode', 'On');
  ini_set('session.gc_maxlifetime', 60 * 60 * 2);
  ini_set('session.sid_length', '64');
  ini_set('session.hash_function', 'sha256');
  ini_set('session.save_path', \ShiftCodesTK\PRIVATE_PATHS['temp'] . '/sessions');

  if (!ShiftCodesTK\BUILD_INFORMATION['is_dev_branch']) {
    session_set_cookie_params([
      'lifetime' => 0,
      'domain'   => ShiftCodesTK\SITE_DOMAIN,
      'secure'   => true,
      'httponly' => true,
      'samesite' => 'Lax'
    ]);
  }
  else {
    session_set_cookie_params([
      'lifetime' => 0,
      'domain'   => '',
      'secure'   => false,
      'httponly' => true,
      'samesite' => 'Lax'
    ]);
  }

  // Startup
  session_start();

  // Session Timestamp Management
  (function () {
    /**
     * @var array Session expiration thresholds, defined in *Hours*
     * - *@var* `int $[expiration]` How long each session should be valid since it was last accessed.
     * - *@var* `int $[maxLife]` The maximum length of time a session should be valid for since it was first created.
     */
    $settings = [
      'expiration' => 2,
      'maxLife'    => 12
    ];

    if (isset($_SESSION['session']) && isset($_SESSION['timestamp'])) {
      $now = time();

      $expiration = new DateTime("@{$_SESSION['timestamp']}");
      $expiration->add(new DateInterval("PT{$settings['expiration']}H"));
      $expiration = $expiration->getTimestamp();

      $maxLife = new DateTime("@{$_SESSION['session']['start']}");
      $maxLife->add(new DateInterval("PT{$settings['maxLife']}H"));
      $maxLife = $maxLife->getTimestamp();
  
      // Session has expired
      if (!$_SESSION['session']['active'] || $now > $expiration || $now > $maxLife) {
        refreshSession();
        return;
      }
    }

    startSession();
  })();
?>