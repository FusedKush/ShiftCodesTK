<?php
  /** 
   * The primary PHP Initialization File. All PHP Scripts *must* include this file at least once. 
   **/

  /** The global namespace for ShiftCodesTK */
  namespace ShiftCodesTK;

  /**
   * A test variable used for testing things.
   */
  define("ShiftCodesTK\Foo", 42);

  // Public Paths
  (function () {
    /**
     * A list of public directory paths
     * - *root* - The root `public` directory
     * - *assets* - The public `assets` directory
     * - *requests* - The public `requests` directory
     */
    $paths = [];

    $paths['root'] = dirname($_SERVER["DOCUMENT_ROOT"]) . '/public';
    $paths['assets'] = $paths['root'] . 'assets';
    $paths['requests'] = $paths['assets'] . 'requests';

    /**
     * @var array ShiftCodesTK\PUBLIC_PATHS A list of public directory paths
     * 
     * | Key | Path | Description |
     * | --- | --- | -- |
     * | *root* | `/public` | The root public directory where all public-facing resources are stored. |
     * | *assets* | `/public/assets` | The public assets directory where site resources are stored. |
     * | *requests* | `/public/assets/requests` | The public requests directory where client requests are sent. |
     */
    define("ShiftCodesTK\PUBLIC_PATHS", $paths);
  })();
  // Private Paths
  (function () {
    $paths = [];

    $paths['root'] = dirname($_SERVER["DOCUMENT_ROOT"]) . '/private';
    $paths['resources'] = $paths['root'] . '/resources';
    $paths['php'] = $paths['root'] . '/php';
    $paths['classes'] = $paths['php'] . '/classes';
    $paths['vendor'] = dirname($_SERVER["DOCUMENT_ROOT"], 2) . '/vendor';
    $paths['modules'] = $paths['php'] . '/modules';
    $paths['php_includes'] = $paths['php'] . '/includes';
    $paths['requests'] = $paths['php'] . '/requests';
    $paths['forms'] = $paths['php'] . '/forms';
    $paths['html'] = $paths['root'] . '/php-html/';
    $paths['html_includes'] = $paths['html'] . '/.min/includes';

    /**
     * A list of private directory paths
     * 
     * | Key | Path | Description |
     * | --- | --- | -- |
     * | *root* | `/private` | The root private directory where all private resources are stored. |
     * | *resources* | `/private/resources` | The private Resources directory where backend resources are stored. |
     * | *php* | `/private/php` | The main private PHP directory where all PHP Scripting resources are stored. |
     * | *modules* | `/private/php/modules` | The private PHP Scripts directory where all ShiftCodesTK modules are stored. |
     * | *classes* | `/private/php/classes` | The private PHP Classes directory where all ShiftCodesTK PHP Class Definitions are stored. |
     * | *vendor* | `/private/php/vendor` | The private PHP directory where all **Composer** package information is stored. |
     * | *php_includes* | `/private/php/includes` | The private PHP Script Includes directory where PHP script snippets are stored. This is also used in the `set_include_path()`. |
     * | *requests* | `/private/php/scripts/requests` | The private PHP Requests directory where PHP request scripts are stored. |
     * | *forms* | `/private/php/scripts/forms` | The private PHP Forms directory where PHP form configurations are stored. |
     * | *html* | `/private/php/php-html` | The private PHP-HTML directory where all HTML Pages are stored. |
     * | *html_includes* | `/private/php/php-html/.min/includes` | The private PHP-HTML includes directory where HTML Page includes are stored. This is also used in the `set_include_path()`. |
     */
    define("ShiftCodesTK\PRIVATE_PATHS", $paths);
  })();

  // Startup Modules
  foreach (['serverConfig', 'functions', 'strings', 'response'] as $file) {
    require_once(PRIVATE_PATHS['php'] . "/modules/$file.php");
  }

  // Request Constants
  (function () {
    /**
     * The type of script that is currently executing
     * @var int The type of script [ 0 = script | 1 = page ]
     */
    define('SCRIPT_TYPE', strpos($_SERVER['REQUEST_URI'], '/assets/') === 0 ? 0 : 1);
    /** @var string|false The Request Token Header if sent */
    define('TOKEN_HEADER', $_SERVER[ 'HTTP_X_REQUEST_TOKEN'] ?? false);
    // `ShiftCodesTK\REQUEST_TOKEN`
    (function () {
      $requestToken = (function () {
        if ($token = $_SERVER['HTTP_X_REQUEST_TOKEN'] ?? null) {
          return $token;
        }
        else if ($token = $_POST['_auth_token'] ?? null) {
          return $token;
        }
        else if ($token = $_GET['_request_token'] ?? null) {
          return $token;
        }
  
        return false;
      })();

      /** @var string|false If sent with the request, this is the *Request Token* used to conduct the request. */
      define('ShiftCodesTK\REQUEST_TOKEN', $requestToken);
    })();
    /**
     * @var "SCRIPT"|"PAGE" Indicates if the current script that is executing is a *page* or a *remote script*.
     */
    define('ShiftCodesTK\SCRIPT_TYPE', strpos($_SERVER['REQUEST_URI'], '/assets/') === 0 ? "SCRIPT" : "PAGE");
    // `ShiftCodesTK\BUILD_INFORMATION`
    (function () {
      $buildInfo = (function () {
        $buildInfo = [];
        $gitPath = (dirname($_SERVER["DOCUMENT_ROOT"], 2)) . "/.git";
        $head = file_get_contents("{$gitPath}/HEAD");

        $buildInfo['branch'] = trim(preg_replace("%(.*?\/){2}%", "", $head));
        $buildInfo['is_dev_branch'] = $buildInfo['branch'] !== 'master';
  
        $branchPath = "{$gitPath}/refs/heads/{$buildInfo['branch']}";
  
        $buildInfo['last_commit'] = [
          'hash'    => trim(file_get_contents($branchPath)),
          'time'    => date(DATE_ISO8601, filemtime($branchPath)),
          'message' => trim(file_get_contents("{$gitPath}/COMMIT_EDITMSG"))
        ];
  
        return $buildInfo;
      })();

      /** Information regarding the current *Build* of ShiftCodesTK.
       * 
       * | Property | Type | Description |
       * | --- | --- | --- |
       * | *branch* | `string` | The name of the current *Build Branch*. |
       * | *is_dev_branch* | `bool` | Indicates if the current `branch` is a *Development Branch* (**true**), or a *Production Branch* (**false**). |
       * | *last_commit* | `array` | Information related to the last *Branch Commit*. |
       */
      define('ShiftCodesTK\BUILD_INFORMATION', $buildInfo);
    })();
  })();
  // Set Path & Timezone Defaults
  (function () {
    set_include_path(
      \ShiftCodesTK\PRIVATE_PATHS['php_includes']
      . PATH_SEPARATOR
      . \ShiftCodesTK\PRIVATE_PATHS['html_includes']
      . PATH_SEPARATOR
      . get_include_path()
    );
    date_default_timezone_set('UTC');
  })();
  // Definition Constants
  (function () {
    /**
     * A list of present date formats for use with `DateTimeInterface->format()`.
     * 
     * | Key | Format |
     * | --- | --- |
     * | *date* | `Y-m-d` |
     * | *time* | `H:i:s` |
     * | *date_time* | `Y-m-d H:i:s` |
     * | *full_date_time* | `Y-m-d H:i:s.u` |
     * | *iso* | `Y-m-d\TH:i:sO` |
     */
    define('ShiftCodesTK\DATE_FORMATS', [
      'date'           => 'Y-m-d',
      'time'           => 'H:i:s',
      'date_time'      => 'Y-m-d H:i:s',
      'full_date_time' => "Y-m-d H:i:s.u",
      'iso'            => \DateTimeInterface::ISO8601
    ]);
    // `ShiftCodesTK\DATE_TIMEZONES`
    (function () {
      $names = \DateTimeZone::listIdentifiers();
      $utc = new \DateTime('now', new \DateTimeZone('UTC'));
      $timezones = [];
      $tzList = [];
  
      foreach ($names as $name) {
        $timezone = new \DateTimeZone($name);
        $offset = $timezone->getOffset($utc);
  
        $timezones[] = [
          'offset' => $offset,
          'name'   => $name,
          'abbr'   => (function () use ($timezone, $name, $offset) {
            $abbrs = $timezone->getTransitions();
  
            foreach ($abbrs as $abbr => $abbrData) {
              if ($abbrData['offset'] == $offset) {
                return $abbrData['abbr'];
              }
            }
  
            return false;
          })()
        ];
      }
  
      usort($timezones, function ($a, $b) {
        if ($a['offset'] == $b['offset']) {
          return strcmp($a['name'], $b['name']);
        }
        else {
          return $a['offset'] - $b['offset'];
        }
      });
  
      foreach ($timezones as $timezone) {
        $prettyName = str_replace('_', ' ', $timezone['name']);
        $sign = ($timezone['offset'] > 0) ? '+' : '-';
        $offset = gmdate('H:i', abs($timezone['offset']));
  
        $tzList[$timezone['name']] = "(UTC {$sign}{$offset}) {$prettyName}" . ($timezone['abbr'] ? " ({$timezone['abbr']})" : "");
      } 
  
      /**
       * A sorted list of *Timezones* and their respective *Display Names*.
       */
      define('ShiftCodesTK\DATE_TIMEZONES', $tzList);
    })();
    /**
     * A list of HTTP & custom Status Codes.
     * 
     * | Code | Message | HTTP Code |
     * | --- | --- | --- |
     * | *HTTP Status Codes* |||
     * | 200 | Ok ||
     * | 201 | Created ||
     * | 204 | No Content ||
     * | 400 | Bad Request ||
     * | 401 | Unauthorized ||
     * | 403 | Forbidden ||
     * | 404 | Not Found ||
     * | 408 | Request Timeout ||
     * | 500 | Internal Server Error ||
     * | 503 | Service Unavailable ||
     * | *Custom Status Codes* |||
     * | 1 | Ok | 200 |
     * | 2 | Ok | 201 |
     * | 3 | Ok | 200 |
     * | -1 | Validation Error | 400 |
     * | -2 | Request Error | 400 |
     * | -3 | Server Error | 500 |
     * | -4 | Server Maintenance | 503 |
     * | -5 | Javascript Disabled | 400 |
     * | -6 | Cookies Disabled | 400 |
     */
    define('ShiftCodesTK\STATUS_CODES', [
      // HTTP Status Codes
      200 => [
        'name'        => 'Ok',           
        'description' => "The request has been successfully processed."
      ],
      201 => [
        'name'        => 'Created',           
        'description' => "The creation request has been successfully processed."
      ],
      204 => [
        'name'        => 'No Content',           
        'description' => "The deletion request has been successfully processed."
      ],
      400 => [
        'name'        => 'Bad Request',           
        'description' => "We cannot seem to process your request. Please try again later."
      ],
      401 => [
        'name'        => 'Unauthorized',          
        'description' => "You don't seem to be authorized to be here. Don't worry, we won't tell anybody."
      ],
      403 => [
        'name'        => 'Forbidden',             
        'description' => "You don't seem to be allowed in here. Don't worry, we'll keep it between us."
      ],
      404 => [
        'name'        => 'Not Found',             
        'description' => "We can't seem to find what you're looking for. Check the url and try again."
      ],
      408 => [
        'name'        => 'Request Timeout',       
        'description' => 'Your request seems to have timed out. Please try again later.'
      ],
      500 => [
        'name'        => 'Internal Server Error', 
        'description' => 'Our server seems to have encountered an error while processing your request. Please try again later.'
      ],
      503 => [
        'name'        => 'Service Unavailable',   
        'description' => 'Our service seems to be currently unavailable. Sorry about that.'
      ],
      // ShiftCodesTK Status Codes
      1 => [
        'name'        => 'Ok',           
        'description' => "The request has been successfully processed.",
        'httpCode'    => 200
      ],
      2 => [
        'name'        => 'Ok',           
        'description' => "The creation request has been successfully processed.",
        'httpCode'    => 201
      ],
      3 => [
        'name'        => 'Ok',           
        'description' => "The deletion request has been successfully processed.",
        'httpCode'    => 200
      ],
      -1 => [
        'name'        => 'Validation Error',           
        'description' => "Validation has failed for the request.",
        'httpCode'    => 400
      ],
      -2 => [
        'name'        => 'Request Error',           
        'description' => "An error occurred while validating the request",
        'httpCode'    => 400
      ],
      -3 => [
        'name'        => 'Server Error',           
        'description' => "An error occurred while the server was processing the request.",
        'httpCode'    => 500
      ],
      -4 => [
        'name'        => 'Server Maintenance',           
        'description' => "The server is currently unavailable while we perform maintenance.",
        'httpCode'    => 503
      ],
      -5 => [
        'name'        => 'Javascript Disabled',           
        'description' => 
          'We apologize for the inconvience, but ShiftCodesTK requires the use of Javascript to function properly. <br>
          Please <a class="themed" 
                    href="https://www.enable-javascript.com/" 
                    rel="external noopener" 
                    target="_blank" 
                    title="How to Enable Javascript (External Link)" 
                    aria-label="How to Enable Javascript (External Link)">
                    Enable or Allow Javascript
                 </a>
          for ShiftCodesTK and try again.',
        'httpCode'    => 400
      ],
      -6 => [
        'name'        => 'Cookies Disabled',           
        'description' => 
          "We apologize for the inconvience, but ShiftCodesTK requires the use of cookies to function properly. <br>
          Please enable or allow cookies for ShiftCodesTK and try again.",
        'httpCode'    => 400
      ],
    ]);
    /**
     * ShiftCodesTK User Roles
     */
    define("AUTH_ROLES", (function () {
      $roles = [
        'props' => [
          'admin' => [
            'name'  => 'Admin',
            'label' => 'This user is an Administrator of ShiftCodesTK'
          ],
          'badass' => [
            'name' => 'Badass',
            'label' => 'This user is a ShiftCodesTK Badass'
          ]
        ]
      ];
      $roles['roles'] = array_keys($roles['props']);
  
      return $roles;
    })());
    /**
     * @var string The domain the live site resides on
     * @see \ShiftCodesTK\STATUS_CODES
     */
    define("ShiftCodesTK\SITE_DOMAIN", 'shiftcodestk.com');
  })();

  // Class Autoloading
  require(PRIVATE_PATHS['vendor'] . '/autoload.php');
  
  /**
   * The query string to be used when loading cached resources.
   * @var string The version number query string to be used when loading cached resources.
  */
  define("TK_VERSION_STR", "?v=" . TK_VERSION);

  // Server Config & Startup Checks
  (function () {
    // Get Server Config
    (function () {
      try {
        $configData = getJSONFile(PRIVATE_PATHS['resources'] . '/server_config.json', true);
  
        if (!$configData) {
          throw new \Error();
        }

        $configData['version_str'] = "?v={$configData['version']}";

        /**
         * @var array Server Configuration Information
         * 
         * | Key | Type | Description |
         * | --- | --- | --- |
         * | *version* | `string` | The current version of ShiftCodesTK. |
         * | *version_str* | `string` | A *query string* for use with cached resources.
         * | *in_maintenance* | `bool` | Indicates if the site is currently in *Maintenance Mode*. |
         */
        define('ShiftCodesTK\SERVER_CONFIGURATION', $configData);
      }
      catch (\Throwable $exception) {
        throw new \Error("The server configuration could not be retrieved.");
      }
    })();

    // Maintenance Updates
    if (SERVER_CONFIGURATION['in_maintenance']) {
      response_http(-4, true);
    }
    // Check that cookies are enabled
    (function () {
      /**
       * The names of the testing cookies and query string parameters
       */
      $vars = [
        'test'    => 'first_visit',
        'failed'  => 'cookies_disabled',
        'checked' => 'verified',
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

  // Module Loading                      
  $scriptsToLoad = (function () {
    $scripts = [
      'required' => [
        '.secrets', 
        'secrets',
        'integers',
        'timestamps',
        'db', 
        'sanitizer', 
        'validations', 
        'forms',
        'session', 
        'auth',
        'users',
        'shift'
      ],
      'page'     => [
        'pageSettings', 
        'shiftStats'
      ]
    ];

    if (SCRIPT_TYPE == 'PAGE') {
      return array_merge(...array_values($scripts));
    }
    else {
      return $scripts['required'];
    }
  })();

  foreach ($scriptsToLoad as $file) {
    require_once(PRIVATE_PATHS['php'] . "/modules/$file.php");
  }

  unset($scriptsToLoad);

  // Request script processing
  if (SCRIPT_TYPE == "SCRIPT") { 
    $response = new \ResponseObject();
    // Requested file 
    $request = (function () {
      $request = $_GET['_request'] ?? false;
      
      if ($request) {
        $request = PRIVATE_PATHS['requests'] . "/{$request}";
        $request = strpos($request, '.php') === false ? "$request.php" : $request;
      }

      return $request;
    })();

    // Nonexistent File
    if (!$request) {
      $response->set(404);
      $response->status_message = 'No resource was specified';
      $response->send();
      exit;
    }
    else if (!file_exists($request)) {
      $response->set(404);
      $response->status_message = 'The specified resource could not be found';
      $response->send();
      exit;
    }
    // Invalid request type
    if (strpos($request, '/get/') !== false && !is_method('GET') || strpos($request, '/post/') !== false && !is_method('POST')) {
      $response->set(-1);
      $response->status_message = 'An incorrect request method was used';
      $response->send();
      exit;
    } 
    // Missing Token
    if (!REQUEST_TOKEN || REQUEST_TOKEN != $_SESSION['token'] && $_GET['_request'] != 'get/token') {
      $response->set(401);
      $response->status_message = 'Missing or Invalid Request Token';
      $response->send();
      exit;
    }

    include($request);
  } 
?>