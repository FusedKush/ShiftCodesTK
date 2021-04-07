<?php
  /**
   * The Startup Tasks & Checks
   */

  use ShiftCodesTK\Config;
  use const ShiftCodesTK\PRIVATE_PATHS;

  // Check for `define-secrets.php` & `define-config.php` definition files
  (function () {
    $files = [
      'define-secrets',
      'define-config'
    ];

    foreach ($files as $file) {
      $filepath = PRIVATE_PATHS['resources'] . "/build-tools/{$file}";
  
      if (file_exists("{$filepath}.php")) {
        include("{$filepath}.php");
    
        rename("{$filepath}.php", "{$filepath}.used");
      }
    }
  })();

  // Define Runtime Constants
  (function () {
    /**
   * @var string The *Version Number Query String* to be used when loading resources.
   */
   define("ShiftCodesTK\VERSION_QUERY_STR", "?v=" . Config::getConfigurationValue('site_version'));
  })();

  // Set Path & Timezone Defaults
  (function () {
    set_include_path(
      PRIVATE_PATHS['php_includes']
      . PATH_SEPARATOR
      . PRIVATE_PATHS['html_includes']
      . PATH_SEPARATOR
      . get_include_path()
    );
    date_default_timezone_set('UTC');
  })();

  // Perform Startup Checks
  (function () {
    // Check Maintenance Status
    if (Config::getConfigurationValue('site_maintenance')) {
      response_http(-4, true);
    }

    // Check that cookies are enabled
    (function () {
      /**
       * The names of the testing cookies and query string parameters
       */
      $vars = [
        'test'    => 'co_t',
        'failed'  => 'co_f',
        'checked' => 'co_c',
      ];
  
      /**
       * Retrieve the URL of the current page for redirects
       * 
       * @param boolean $queryParam The query string parameter to append to the URL
       * @return string Returns the updated URL of the current page
       */
      $getURL = function ($queryParam) use ($vars) {
        $url = $_SERVER['SCRIPT_NAME'];
  
        // Remove filename
        $url = str_replace('.php', '', $url);
  
        // Add query string parameter
        if (strpos($url, "{$queryParam}=1") === false) {
          foreach ($vars as $type => $var) {
            $url = preg_replace("/(\?|\&)$var=1/", "", $url);
          } 
  
          $url .= strpos($url, '?') === false ? '?' : '&';
          $url .= "{$queryParam}=1";
        }
  
        return $url;
      };
  
      // Perform cookie test
      if (!isset($_GET[$vars['checked']])) {
        if (!$_COOKIE || isset($_GET[$vars['test']]) && $_GET[$vars['test']] == 1) {
          updateCookie($vars['test'], 1, [ 'expires' => 'PT10M' ]);
  
          // Redirect required before testing cookie
          if (!isset($_GET[$vars['test']]) && !isset($_GET[$vars['failed']])) {
            response_redirect($getURL($vars['test']));
          }
          // Cookies are disabled
          else if (isset($_GET[$vars['failed']]) && $_GET[$vars['failed']] == 1) {
            response_http(-6, true);
          }
          // Test is cookie was set
          else {
            if (getCookie($vars['test'])) {
              deleteCookie($vars['test']);
              response_redirect($getURL($vars['checked']));
            }
            // Cookies are disabled
            else {
              response_redirect($getURL($vars['failed']));
            }
          }
        }
        else if ($_COOKIE && isset($_GET[$vars['failed']]) && $_GET[$vars['failed']] == 1) {
          response_redirect($getURL($vars['checked']));
        }
      }
    })();
  })();

  // Initialization Tasks
  (function () {
    GLOBAL $_mysqli;
    
    // Database Initialization
    ShiftCodesTKDatabase::get_instance();
    /**
     * The ShiftCodesTK Database and all associated functionality
     */
    $_mysqli = new ShiftCodesTKDatabase_Old();
    
    ShiftCodes::getInstance();
    
    if (ShiftCodesTK\SCRIPT_TYPE === ShiftCodesTK\SCRIPT_TYPE_PAGE) {
      check_page_settings();
      get_shift_stats();
    }
  })();
?>