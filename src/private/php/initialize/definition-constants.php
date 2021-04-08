<?php
  /**
   * The Global Definition Constants
   */

  // Path Constants
  (function () {
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
      $paths['initialize'] = $paths['php'] . '/initialize';
      $paths['classes'] = $paths['php'] . '/classes';
      $paths['vendor'] = dirname($_SERVER["DOCUMENT_ROOT"], 2) . '/vendor';
      $paths['modules'] = $paths['php'] . '/modules';
      $paths['php_includes'] = $paths['php'] . '/includes';
      $paths['requests'] = $paths['php'] . '/requests';
      $paths['forms'] = $paths['php'] . '/forms';
      $paths['html'] = $paths['root'] . '/php-html/';
      $paths['html_includes'] = $paths['html'] . '/.min/includes';
      $paths['temp'] = dirname($_SERVER['DOCUMENT_ROOT'], 4) . '/tmp';
      
      /**
       * A list of private directory paths
       * 
       * | Key | Path | Description |
       * | --- | --- | -- |
       * | *root* | `/private` | The root private directory where all private resources are stored. |
       * | *resources* | `/private/resources` | The private Resources directory where backend resources are stored. |
       * | *php* | `/private/php` | The main private PHP directory where all PHP Scripting resources are stored. |
       * | *initialize* | `/private/php/initialize` | The private PHP Initialization directory where PHP Scripts required for initialization are stored. |
       * | *modules* | `/private/php/modules` | The private PHP Scripts directory where all ShiftCodesTK modules are stored. |
       * | *classes* | `/private/php/classes` | The private PHP Classes directory where all ShiftCodesTK PHP Class Definitions are stored. |
       * | *vendor* | `/private/php/vendor` | The private PHP directory where all **Composer** package information is stored. |
       * | *php_includes* | `/private/php/includes` | The private PHP Script Includes directory where PHP script snippets are stored. This is also used in the `set_include_path()`. |
       * | *requests* | `/private/php/scripts/requests` | The private PHP Requests directory where PHP request scripts are stored. |
       * | *forms* | `/private/php/scripts/forms` | The private PHP Forms directory where PHP form configurations are stored. |
       * | *html* | `/private/php/php-html` | The private PHP-HTML directory where all HTML Pages are stored. |
       * | *html_includes* | `/private/php/php-html/.min/includes` | The private PHP-HTML includes directory where HTML Page includes are stored. This is also used in the `set_include_path()`. |
       * | *temp* | `../../../temp` | The temporary files directory. |
       */
      define("ShiftCodesTK\PRIVATE_PATHS", $paths);
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
  // `ShiftCodesTK\BUILD_INFORMATION`
  (function () {
    $build_info = (function () {
      $build_info = [
        'branch'        => null,
        'is_prod_branch' => null,
        'last_commit'   => [
          'hash'           => null,
          'time'           => null,
          'message'        => null,
        ]
      ];
      $gitPath = (dirname($_SERVER["DOCUMENT_ROOT"], 2)) . "/.git";

      if (file_exists($gitPath)) {
        $head = file_get_contents("{$gitPath}/HEAD");
        $branch = trim(preg_replace("%(.*?\/){2}%", "", $head));
        $branch_path = "{$gitPath}/refs/heads/{$branch}";
  
        $build_info = array_replace_recursive($build_info, [
          'branch'        => $branch,
          'is_prod_branch' => $branch === 'master',
          'last_commit'   => [
            'hash'    => trim(file_get_contents($branch_path)),
            'time'    => date(DATE_ISO8601, filemtime($branch_path)),
            'message' => trim(file_get_contents("{$gitPath}/COMMIT_EDITMSG"))
          ]
        ]);
      }

      return $build_info;
    })();

    /** @var array Information about the *Current Build* of ShiftCodesTK.
     * 
     * Property Values will be **null** if they could not be retrieved.
     * 
     * | Property | Type | Description |
     * | --- | --- | --- |
     * | *branch* | `string` | The name of the current *Build Branch*. |
     * | *is_prod_branch* | `bool` | Indicates if the current `branch` is a *Production Branch* (**true**), or a *Development Branch* (**false**). |
     * | *last_commit* | `array` | Information related to the last *Branch Commit*. |
     */
    define('ShiftCodesTK\BUILD_INFORMATION', $build_info);
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