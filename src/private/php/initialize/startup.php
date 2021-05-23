<?php
  /**
   * The Startup Tasks & Checks
   */

  use ShiftCodesTK\Config,
      ShiftCodesTK\PHPConfigurationFiles,
      ShiftCodesTK\Paths,
      ShiftCodesTK\Strings;

  // Check for `php-defs`
  (function () {
    $files = [
      'define-secrets',
      'define-config'
    ];

    foreach ($files as $file) {
      $filepath = Paths\GENERAL_PATHS['utils'] . "/php-defs/{$file}";
  
      if (file_exists("{$filepath}.php")) {
        include("{$filepath}.php");
    
        rename("{$filepath}.php", "{$filepath}.used");
      }
    }
  })();

  // Define Runtime Constants
  (function () {
    // `ShiftCodesTK\BUILD_INFORMATION`
    (function () {
      $build_info = (function () {
        $build_info = [];
        $cache_file = new PHPConfigurationFiles\ConfigurationManager(
          Paths\GENERAL_PATHS['cache'] . '/build-information.php',
          new PHPConfigurationFiles\ConfigurationFile([
            'type'      => PHPConfigurationFiles\ConfigurationFile::CONFIGURATION_TYPE_ARRAY,
            'comment'   => "Represents a cached version of the `BUILD_INFORMATION` constant."
          ])
        );
        $git_path = Paths\GENERAL_PATHS['git'];

        if (file_exists($git_path)) {
          $head = file_get_contents("{$git_path}/HEAD");
          $current_branch = trim(preg_replace("%(.*?\/){2}%", "", $head));
          $head_commit = \ShiftCodesTK\Strings\trim(file_get_contents("{$git_path}/refs/heads/{$current_branch}"));

          if ($cache_file->configurationValueExists('head_commit') && $cache_file->getConfigurationValue('head_commit') === $head_commit) {
            $build_info = $cache_file->getConfigurationValue('build_information');
          }
          else {
            // General Information
            (function () use (&$build_info) {
              $build_info = array_merge($build_info, [
                'repository'  => 'https://github.com/FusedKush/ShiftCodesTK'
              ]);
            })();
            // Branch Information
            (function () use (&$build_info, $git_path, $current_branch) {
              $branch_info = [
                'current_branch'  => null,
                'prod_branch'     => 'master',
                'is_prod_branch'  => null
              ];
    
              if (file_exists($git_path)) {
                $branch_info = array_replace($branch_info, [
                  'current_branch'  => $current_branch,
                  'is_prod_branch'  => $current_branch === $branch_info['prod_branch']
                ]);
              }
    
              $build_info = array_merge($build_info, [
                'branch' => $branch_info
              ]);
            })();
            // Last Commit Information
            (function () use (&$build_info, $git_path) {
              $last_commit = [];
              $current_branch = $build_info['branch']['current_branch'];
              $branch_logs = (function () use ($git_path, $current_branch) {
                $branch_logs = [];
                $branch_paths = [
                  'build'   => "heads/{$current_branch}",
                  'remote'  => "remotes/origin/{$current_branch}"
                ];
                $log_section_names = [
                  'parent',
                  'commit',
                  'first',
                  'last',
                  'address',
                  'ts',
                  'tz',
                  'event'
                ];
                
                foreach ($branch_paths as $branch_type => $branch_path) {
                  $log_path = "{$git_path}/logs/refs/{$branch_path}";
    
                  if (file_exists($log_path)) {
                    $file_contents = (function () use ($log_path) {
                      $line = '';
                      $file = fopen($log_path, 'r');
                      $cursor = -1;
                      $char = "";
                      $char_list = [
                        "\n",
                        "\r"
                      ];
          
                      $seek = function ($decrement = true) use (&$file, &$cursor, &$char) {
                        if ($decrement) {
                          $cursor--;
                        }
          
                        fseek($file, $cursor, SEEK_END);
                        $char = fgetc($file);
                      };
          
                      $seek(false);
          
                      // Trim trailing newlines
                      while (in_array($char, $char_list, true)) {
                        $seek();
                      }
          
                      // Find File Start / First Newline
                      $char_list[] = false;
          
                      while (!in_array($char, $char_list, true)) {
                        $line = "{$char}{$line}";
                        $seek();
                      }
        
                      fclose($file);
                      
                      return $line;
                    })();
                    $log_sections = (function () use ($file_contents) {
                      $log_sections = [];
                      $pattern =
                        "/" .                         // Opening Delimiter
                          "([\w\d]+)\ " .             // [1] Parent Commit         
                          "([\w\d]+)\ " .             // [2] Commit Hash
                          "([^\s]+)\ "  .             // [3] Author First Name
                          "(?:([^\s]+)\ ){0,1}" .     // [4] Author Last Name (Optional)
                          "(\<[^\s]+\>)\ " .          // [5] Author Address
                          "(\d+)\ " .                 // [6] Timestamp
                          "((?:\-|\+){0,1}\d{4})" .   // [7] Timezone
                          "\s*(.+)$" .                // [8] Event Details
                        "/u";                         // Closing Delimiter
                      // $pattern = <<<EOT
                      //   /                       # Opening Delimiter
                      //     (?:([\w\d]+)\ )       # [1] Parent Commit
                      //     (?:([\w\d]+)\ )       # [2] Commit
                      //     (?:([^\s]+)\ )        # [3] Author First Name
                      //     (?:([^\s]+)\ ){0,1}   # [4] Author Last Name
                      //     (?:(\<[^\s]+\>)\ )    # [5] Author Address
                      //     (?:(\d+)\ )           # [6] Timestamp
                      //     (?:((?:-){0,1}\d{4})) # [7] Timezone 
                      //     (.+)$                 # [8] Event
                      //                           # Closing Delimiter
                      //   /ux
                      // EOT;
        
                      preg_match($pattern, $file_contents, $log_sections, PREG_UNMATCHED_AS_NULL);
                      array_splice($log_sections, 0, 1);
        
                      return $log_sections;
                    })();
        
                    $branch_logs[$branch_type] = array_combine($log_section_names, $log_sections);
                  }
                  else {
                    $branch_logs[$branch_type] = null;
                  }
                }
    
                return $branch_logs;
              })();
    
              // Common Properties
              (function () use (&$last_commit, $branch_logs) {
                foreach ($branch_logs as $branch_type => $branch_log) {
                  $common_properties = [
                    'commit'      => null,
                    'parent'      => null,
                    'author'      => [
                      'full_name'   => null,
                      'first_name'  => null,
                      'last_name'   => null,
                      'address'     => null
                    ],
                    'timestamp'   => null,
                    'description' => null
                  ];
    
                  if (isset($branch_log)) {
                    $common_properties = array_replace_recursive($common_properties, [
                      'commit'      => $branch_log['commit'],
                      'parent'      => !Strings\preg_test($branch_log['parent'], '/^0+$/')
                                       ? $branch_log['parent']
                                       : null,
                      'author'      => [
                        'full_name'   => (function () use ($branch_log) {
                          $last_name = isset($branch_log['last'])
                                      ? "{$branch_log['last']} "
                                      : "";
        
                          return "{$branch_log['first']} {$last_name}{$branch_log['address']}";
                        })(),
                        'first_name'  => $branch_log['first'],
                        'last_name'   => $branch_log['last'],
                        'address'     => Strings\slice($branch_log['address'], 1, -1)
                      ],
                      'timestamp'   => (
                        (new DateTime(
                          "@{$branch_log['ts']}", 
                          new DateTimeZone($branch_log['tz'])
                        ))
                        ->format('c')
                      ),
                      'description' => $branch_log['event']
                    ]);
                  }
    
                  $last_commit[$branch_type] = $common_properties;
                }
              })();
              // Build Properties 
              (function () use (&$last_commit, $branch_logs, $git_path) {
                $build_props = [
                  'commit_message'  => null,
                  'status'          => null
                ];
                $build_log = $branch_logs['build'] ?? null;
                $remote_log = $branch_logs['remote'] ?? null;
    
                if (isset($build_log)) {
                  // Commit Message
                  (function () use (&$build_props, $git_path) {
                    $commit_message_path = "{$git_path}/COMMIT_EDITMSG";

                    if (file_exists($commit_message_path)) {
                      $build_props['commit_message'] = \ShiftCodesTK\Strings\trim(
                        file_get_contents($commit_message_path)
                      );
                    }
                  })();
    
                  if (isset($remote_log)) {
                    $build_props['status'] = (function () use ($branch_logs, $build_log, $remote_log) {
                      if ($build_log['commit'] === $remote_log['commit']) {
                        return "up-to-date";
                      }
                      else if ($build_log['commit'] === $remote_log['parent']) {
                        return "behind-remote";
                      }
                      else if ($build_log['parent'] === $remote_log['commit']) {
                        return "ahead-of-remote";
                      }
                      else {
                        $timestamps = (function () use ($branch_logs) {
                          $timestamps = [];
        
                          foreach ([ 'build', 'remote' ] as $log_type) {
                            $branch_log = $branch_logs[$log_type];
        
                            $timestamps[$log_type] = (new DateTime("@{$branch_log['ts']}"))->getTimestamp();
                          }
        
                          return $timestamps;
                        })();
          
                        if ($timestamps['build'] > $timestamps['remote']) { 
                          return "ahead-of-remote"; 
                        }
                        else if ($timestamps['build'] < $timestamps['remote']) { 
                          return "behind-remote"; 
                        }
                        else { 
                          return "up-to-date"; 
                        }
                      }
    
                    })();
                  }
                }
    
                $last_commit['build'] = array_merge($last_commit['build'], $build_props);
              })();
    
              $build_info = array_replace_recursive($build_info, [
                'last_commit' => $last_commit
              ]);
            })();

            if (!$cache_file->configurationValueExists('head_commit')) {
              $cache_file->addConfigurationValue('head_commit', $head_commit);
              $cache_file->addConfigurationValue('build_information', $build_info);
            }
            else {
              $cache_file->updateConfigurationValue('head_commit', $head_commit);
              $cache_file->updateConfigurationValue('build_information', $build_info);
            }
          }
        }

        return $build_info;
      })();

      /** @var array Information about the *Current Build* of ShiftCodesTK.
       * 
       * Most Property Values will have a value of **null** if they could not be retrieved.
       * 
       * - *repository* `string`: The URL of the *Remote Repository*.
       * - *branch* `array`: Information about the *Current Branch*.
       * - - *current_branch* `string`: The name of the *Current Branch*.
       * - - *prod_branch* `string`: The name of the *Production Branch*.
       * - - *is_prod_branch* `bool`: Indicates if the `$current_branch` is the `$prod_branch`.
       * - *last_commit* `array`: Information about the most recent *Commit*.
       * - - *build* `array`: Commit details for the *Current Build*.
       * - - - *commit* `string`: The *Commit Hash* of the last commit.
       * - - - *parent* `string|null`: The Commit Hash of the *Parent Commit*, if available.
       * - - - *author* `array`: Information about the *Author* of the last commit.
       * - - - - *full_name* `string`: The *Full Display Name* of the Author.
       * - - - - *first_name* `string`: The Author's *First Name*.
       * - - - - *last_name* `string|null`: The Author's *Last Name*, if provided.
       * - - - - *address* `string`: The Author's *Email Address*.
       * - - - *timestamp* `string`: The *Timestamp* of when the changes were *Committed*.
       * - - - *description* `string`: The description of the event on the Build.
       * - - - *commit_message* `string`: The *Commit Message* of the last commit.
       * - - - *status* `"up-to-date"|"behind-remote"|"ahead-of-remote"`: Indicates the current *Status* of the Build in relation to the *Remote*.
       * - - *remote* `array`: Commit details for the *Remote Repository*.
       * - - - *See `build`* for more information on the shared properties.
       * - - - *commit* 
       * - - - *parent*
       * - - - *author* 
       * - - - - *full_name* 
       * - - - - *first_name* 
       * - - - - *last_name*
       * - - - - *address* 
       * - - - *timestamp* 
       * - - - *description* `string`: The description of the event on the Remote.
       */
      define('ShiftCodesTK\BUILD_INFORMATION', $build_info);
    })();
    /**
   * @var string The *Version Number Query String* to be used when loading resources.
   */
    define("ShiftCodesTK\VERSION_QUERY_STR", "?v=" . Config::getConfigurationValue('site_version'));
  })();

  // Set Configuration Values & Defaults
  (function () {
    set_include_path(
      Paths\PHP_PATHS['includes']
      . PATH_SEPARATOR
      . Paths\HTML_PATHS['includes']
      . PATH_SEPARATOR
      . get_include_path()
    );
    
    ini_set("log_errors", 1);
    ini_set("error_log", Paths\GENERAL_PATHS['logs'] . '/php_error.log');

    date_default_timezone_set('UTC');
  })();

    \ShiftCodesTK\Router\RouterFramework::init();
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

    session_init();
    
    // Database Initialization
    ShiftCodesTKDatabase::get_instance();
    /**
     * The ShiftCodesTK Database and all associated functionality
     */
    $_mysqli = new ShiftCodesTKDatabase_Old();
    
    ShiftCodes::getInstance();
    \ShiftCodesTK\Users\init();
    \ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Framework::init();
    
    if (ShiftCodesTK\SCRIPT_TYPE === ShiftCodesTK\SCRIPT_TYPE_PAGE) {
      $_SESSION['timestamp'] = time();
      get_shift_stats();
    }
  })();
?>