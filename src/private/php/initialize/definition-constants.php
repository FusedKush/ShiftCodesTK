<?php
  /**
   * The Global Definition Constants
   */

  use ShiftCodesTK\Paths;

  // Path Constants
  (function () {
    // Base Paths
    (function () {
      $base_paths = [];

      $base_paths['root'] =     dirname($_SERVER["DOCUMENT_ROOT"], 2);
      $base_paths['private'] =  "{$base_paths['root']}/src/private";
      $base_paths['public'] =   "{$base_paths['root']}/src/public";

      /** @var array Represents paths to root directories of ShiftCodesTK.
       * 
       * | Name       | Description                             |
       * | ---        | ---                                     |
       * | `root`     | The *Server Root*.                      |
       * | `private`  | The *Private Site Resources* directory. |
       * | `public`   | The *Public Site Resources* directory.  |
       */
      define("ShiftCodesTK\Paths\BASE_PATHS", $base_paths);
    })();
    // General Paths
    (function () {
      $base_paths = Paths\BASE_PATHS;
      $general_paths = [];

      $general_paths['git'] =       "{$base_paths['root']}/.git";
      $general_paths['cache'] =     "{$base_paths['root']}/cache";
      $general_paths['logs'] =      "{$base_paths['root']}/logs";
      $general_paths['temp'] =      "{$base_paths['root']}/tmp";
      $general_paths['resources'] = "{$base_paths['private']}/resources";

      /** @var array Represents paths to general directories of ShiftCodesTK.
       * 
       * | Name         | Description                                                 |
       * | ---          | ---                                                         |
       * | `git`        | The directory where *Git Repository Information* is stored. |
       * | `cache`      | The directory where *Cached Resources* are stored.          |
       * | `logs`       | The directory where various *Site Logs* are stored.         |
       * | `temp`       | The directory where *Temporary Files* are stored.           |
       * | `resources`  | The directory where general *Site Resources* are stored.    |
       */
      define("ShiftCodesTK\Paths\GENERAL_PATHS", $general_paths);
    })();
    // PHP Paths
    (function () {
      $base_paths = Paths\BASE_PATHS;
      $php_paths = [];

      $php_paths['main'] =              "{$base_paths['private']}/php";
      $php_paths['initialize'] =        "{$php_paths['main']}/initialize";
      $php_paths['classes'] =           "{$php_paths['main']}/classes";
      $php_paths['modules'] =           "{$php_paths['main']}/modules";
      $php_paths['includes'] =          "{$php_paths['main']}/includes";
      $php_paths['frontend_handlers'] = "{$php_paths['main']}/frontend-handlers";
      $php_paths['endpoints'] =         "{$php_paths['main']}/frontend-endpoints";
      $php_paths['server_endpoint'] =   "{$base_paths['public']}/api";
      $php_paths['forms'] =             "{$php_paths['main']}/forms";
      $php_paths['composer'] =          "{$base_paths['root']}/vendor";
      $php_paths['phive'] =             "{$base_paths['root']}/tools";

      /** @var array Represents paths to *PHP* resources.
       * 
       * | Name | Description |
       * | --- | --- |
       * | `main`               | The *Primary PHP Directory*.                                                    |
       * | `initialize`         | The directory where *PHP Initialization Files* are stored.                      |
       * | `classes`            | The directory where internal *PHP Class Definition Files* are stored.           |
       * | `modules`            | The directory where internal *Module Definition Files* are stored.              |
       * | `includes`           | The directory where *PHP Scripts* meant to be individually included are stored. |
       * |                      | Part of the `PHP Include Path`.                                                 |
       * | `frontend_handlers`  | The directory where *Frontend Handler Scripts* are stored.                      |
       * | `endpoints`          | The directory where the individual *Backend Request Endpoints* are stored.      |
       * | `server_endpoint`    | The *Primary Server Endpoint* that exposes the individual `endpoints`.          |
       * | `forms`              | The directory where *Form `Objects`* from the `Forms` module are stored.        |
       * | `composer`           | The `vendors` directory where *Composer Packages* are stored.                   |
       * | `phive`              | The `tools` directory where *Phive PHARs* are stored.                           |
       */
      define("ShiftCodesTK\Paths\PHP_PATHS", $php_paths);
    })();
    // PHP-HTML Paths
    (function () {
      $base_paths = Paths\BASE_PATHS;
      $html_paths = [];

      $html_paths['main'] =     "{$base_paths['private']}/php-html";
      $html_paths['original'] = "{$html_paths['main']}/files";
      $html_paths['minified'] = "{$html_paths['main']}/.min";
      $html_paths['final'] =    $base_paths['public'];
      $html_paths['includes'] = "{$html_paths['minified']}/includes";

      /** @var array Represents paths to *PHP-HTML* resources
       * 
       * | Name       | Description                                                               |
       * | ---        | ---                                                                       |
       * | `main`     | The *Primary PHP-HTML Directory*.                                         |
       * | `original` | The directory where the *Original Files* are stored.                      |
       * | `minified` | The directory where the *Minified Files* are stored.                      |
       * | `final`    | The directory where the *Built Files* are deployed.                       |
       * | `includes` | The directory where *PHP-HTML Files* intended to be included are stored.  |
       * |            | Part of the `PHP Include Path`.                                               |
       */
      define("ShiftCodesTK\Paths\HTML_PATHS", $html_paths);
    })();
    // Asset Paths
    (function () {
      $base_paths = Paths\BASE_PATHS;
      $asset_paths = [];
      
      $asset_paths['public'] = [];
      $asset_paths['public']['main'] =      "{$base_paths['public']}/assets";
      $asset_paths['public']['fonts'] =     "{$base_paths['public']}/fonts";
      $asset_paths['public']['manifests'] = "{$base_paths['public']}/manifests";
      $asset_paths['public']['requests'] =  "{$base_paths['public']}/requests";
      
      $asset_paths['private'] = [];
      $asset_paths['private']['main'] = $base_paths['private'];
      $asset_paths['private']['npm'] =  "{$base_paths['root']}/node-modules";

      foreach ([ 'css', 'js', 'img' ] as $path) {
        foreach ([ 'public', 'private' ] as $visibility) {
          $asset_paths[$visibility][$path] = "{$asset_paths[$visibility]['main']}/{$path}";
        }
      }

      /** @var array Represents paths to additional *Site Assets*.
       * 
       * | Name                                             | Description                                                   |
       * | ---                                              | ---                                                           |
       * | **`public`**: Represents *Public Asset Paths*    |                                                               |
       * | `main`                                           | The *Primary Public Assets Directory*.                        |
       * | `fonts`                                          | The directory where *Web Fonts* are stored.                   |
       * | `manifests`                                      | The directory where *Web Manifests* are stored.               |
       * | `requests`                                       | The directory where *Backend Requests* are sent.              |
       * | `css`                                            | The directory where *Public CSS Files* are stored.            |
       * | `js`                                             | The directory where *Public Javascript Files* are stored.     |
       * | `img`                                            | The directory where *Public Images* are stored.               |
       * | **`private`**: Represents *Private Asset Paths*  |                                                               |
       * | `main`                                           | The *Primary Private Assets Directory*.                       |
       * | `npm`                                            | The `node-modules` directory where *NPM Packages* are stored. |
       * | `css`                                            | The directory where *Private CSS Files* are stored.           |
       * | `js`                                             | The directory where *Private Javascript Files* are stored.    |
       * | `img`                                            | The directory where *Private Images* are stored.              |
       */
      define("ShiftCodesTK\Paths\ASSET_PATHS", $asset_paths);
    })();
  })();
  // Request Constants
  (function () {
    GLOBAL $__script_type;
    
    // Script Type
    (function () use (&$__script_type) {
      /** @var int Represents a standard *PHP Script*, likely executing independently. */
      define("ShiftCodesTK\SCRIPT_TYPE_SCRIPT", 0);
      /** @var int Represents a Front-end *PHP-HTML Page*. */
      define("ShiftCodesTK\SCRIPT_TYPE_PAGE", 1);
      /** @var int Represents a *Front-end Request* for a *Backend Script*. */
      define("ShiftCodesTK\SCRIPT_TYPE_REQUEST", 2);
  
      /** @var int The type of script that is currently executing
       * @var int The type of script [ 0 = script | 1 = page ]
       */
      define('SCRIPT_TYPE', strpos($_SERVER['REQUEST_URI'], '/assets/') === 0 ? 0 : 1);
      /**
       * @var int A `SCRIPT_TYPE_*` constant indicating the *Script Type* of the currently executing PHP Script.
       */
      define('ShiftCodesTK\SCRIPT_TYPE', $__script_type ?? \ShiftCodesTK\SCRIPT_TYPE_SCRIPT);

      unset($__script_type);
    })();
    // Token Constants
    (function () {
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
    })();
  })();
  // Date Constants
  (function () {
    /** @var array A list of preset date formats for use with `DateTimeInterface->format()`.
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
  })();

  /** @var array ShiftCodesTK User Roles */
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
  /** @var array A list of HTTP & custom Status Codes.
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
  /** @var array Common *Regular Expressions* that can be used for matching or validation.
   * 
   * | Name | Description | Notes |
   * | --- | --- | --- |
   * | `semver_version_number` | Matches a [*Semver*](https://semver.org/)-compatible Version Number. | [Regex Source](https://semver.org/#is-there-a-suggested-regular-expression-regex-to-check-a-semver-string). Supports Named Capture Groups: `major`, `minor`, `patch`, `prerelease`, & `buildmetadata`. |
   */
  define("ShiftCodesTK\COMMON_REGEXES", [
    "semver_version_number" => <<<EOT
      /                                               # Opening Delimiter             
        ^                                             # Start of Line
          (?P<major>                                  # [0|major] Major Version Capture Group
            0|[1-9]\d*
          )
          \.                                          # Separator Character
          (?P<minor>                                  # [1|minor] Minor Version Capture Group
            0|[1-9]\d*
          )
          \.                                          # Separator Character
          (?P<patch>                                  # [2|patch] Start of Patch Version Capture Group
            0|[1-9]\d*
          )
          (?:
            -                                         # Separator Character
            (?P<prerelease>                           # [3|prerelease] Pre-Release Version Capture Group
              (?:
                0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]*
              )
              (?:
                \.                                    # Separator Character
                (?:
                  0|[1-9]\d*|\d*[a-zA-Z-][0-9a-zA-Z-]* 
                )
              )*
            )
          )?
          (?:
            \+                                        # Separator Character
            (?P<buildmetadata>                        # [4|buildmetadata] Build Metadata Capture Group
              [0-9a-zA-Z-]+
              (?:
                \.[0-9a-zA-Z-]+
              )*
            )
          )?
        $                                             # End of Line
                                                      # Ending Delimiter
      /xx
    EOT
  ]);
  /** @var string The domain the live site resides on */
  define("ShiftCodesTK\SITE_DOMAIN", 'shiftcodestk.com');
?>