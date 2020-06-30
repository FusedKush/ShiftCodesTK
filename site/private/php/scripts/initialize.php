<?php
  /** Initialization Script */

  // Path Constants
  /**
   * Path to the private folder
   */
  define('PRIVATE_PATH', dirname($_SERVER["DOCUMENT_ROOT"]) . '/private/');
  /**
   * Path to the primary PHP folder
   */
  define('PHP_PATH', PRIVATE_PATH . 'php/');
  /**
   * Path to the PHP Scripts folder
   */
  define('SCRIPTS_PATH', PHP_PATH . 'scripts/');
  /**
   * Path to the PHP Scripts Includes folder
   */
  define('SCRIPTS_INCLUDES_PATH', SCRIPTS_PATH . 'includes/');
  /**
   * Path to the PHP Requests folder
   */
  define('REQUESTS_PATH', SCRIPTS_PATH . 'requests/');
  /**
   * Path to the PHP Forms folder
   */
  define('FORMS_PATH', SCRIPTS_PATH . 'forms/');
  /**
   * Path to the PHP HTML Includes folder
   */
  define('HTML_INCLUDES_PATH', PHP_PATH . 'html/min/includes/');
  /**
   * The type of script that is currently executing
   * @var int The type of script [ 0 = script | 1 = page ]
   */
  define('SCRIPT_TYPE', strpos($_SERVER['REQUEST_URI'], '/assets/requests/') === false ? 1 : 0);

  set_include_path(HTML_INCLUDES_PATH . PATH_SEPARATOR . get_include_path());
  date_default_timezone_set('UTC');

  // Definition Constants
  /**
   * Preset date formats
   * | [ date ] => 'Y-m-d'
   * | [ time ] => 'H:i:s'
   * | [ dateTime ] => 'Y-m-d H:i:s'
   * | [ fullDateTime ] => 'Y-m-d H:i:s.u'
   */
  define('DATE_FORMATS', [
    'date' => 'Y-m-d',
    'time' => 'H:i:s',
    'dateTime' => 'Y-m-d H:i:s',
    'fullDateTime' => "Y-m-d H:i:s.u"
  ]);
  /**
   * An associative array of timezones and their display-friendly names
   * @var array [ timezone_identifier => timezone_name]
   */
  define('DATE_TIMEZONES', (function () {
    $names = DateTimeZone::listIdentifiers();
    $utc = new DateTime('now', new DateTimeZone('UTC'));
    $timezones = [];
    $tzList = [];

    foreach ($names as $name) {
      $timezone = new DateTimeZone($name);

      $timezones[] = [
        'offset' => $timezone->getOffset($utc),
        'name'   => $name
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

      $tzList[$timezone['name']] = "(UTC {$sign}{$offset}) {$prettyName}";
    } 

    /**
     * An associative array of timezones
     */
    return $tzList;
  })());
  /**
   * ShiftCodesTK & HTTP Status Code definitions
   */
  define('STATUS_CODES', [
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

  // Startup Scripts
  foreach (['serverConfig', 'functions', 'response'] as $file) {
    require_once(SCRIPTS_PATH . "initialize/$file.php");
  }
  
  /**
   * The query string to be used when loading cached resources.
   * @var string The version number query string.
  */
  define("TK_VERSION_STR", "?v=" . TK_VERSION);

  // Maintenance Updates
  if (TK_MAINTENANCE) {
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

  // PHP Script Loading
  $scripts = [];
  $scripts['required'] = [
    '.secrets', 
    'db', 
    'sanitizer', 
    'validations', 
    'session', 
    'auth',
    'forms'
  ];
  $scripts['page'] = [
    'pageSettings', 
    'shiftStats'
  ];
  $scripts['session'] = SCRIPT_TYPE == 1 ? array_merge($scripts['required'], $scripts['page']) : $scripts['required'];
                      
  foreach ($scripts['session'] as $file) {
    require_once(SCRIPTS_PATH . "initialize/$file.php");
  }

  unset($scripts);

  // Request script processing
  if (SCRIPT_TYPE == 0) { 
    $response = new ResponseObject();
    $missingToken = !isset($_GET['_token']) 
                      && !isset($_POST['_token']) 
                      && !isset($_POST['_auth_token'])
                    || isset($_GET['_token']) 
                      && $_GET['_token'] != $_SESSION['token']
                    || isset($_POST['_token']) 
                      && $_POST['_token'] != $_SESSION['token']
                    || isset($_POST['_auth_token'])
                      && $_POST['_auth_token'] != $_SESSION['token'];
    
    // Requested file 
    $request = (function () {
      $request = $_GET['_request'] ?? false;

      if ($request) {
        $request = REQUESTS_PATH . $request;
        $request = strpos($request, '.php') === false ? "$request.php" : $request;
      }

      return $request;
    })();

    // Nonexistent File
    if (!$request) {
      $response->set(404);
      $response->statusMessage = 'No resource was specified';
      $response->send();
      exit;
    }
    else if (!file_exists($request)) {
      $response->set(404);
      $response->statusMessage = 'The specified resource could not be found';
      $response->send();
      exit;
    }
    // Invalid request type
    if (strpos($request, '/get/') !== false && !is_method('GET') || strpos($request, '/post/') !== false && !is_method('POST')) {
      $response->set(-1);
      $response->statusMessage = 'An incorrect request method was used';
      $response->send();
      exit;
    } 
    // Missing Token
    if ($missingToken && $_GET['_request'] != 'get/token') {
      $response->set(401);
      $response->statusMessage = 'Missing or Invalid Request Token';
      $response->send();
      exit;
    }

    include($request);
  }
?>