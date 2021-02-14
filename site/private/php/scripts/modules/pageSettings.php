<?php
  (function () use (&$page) {
    $defaults = [
      /**
       * Authentication settings
       */
      'auth' => [
        /**
         * The required auth state to view the page
         * | string [ none | auth | no-auth ]
         */
        'requireState' => 'none',
        /**
         * Where the user is to be redirected to if they do not have the required auth state to view the page.
         * | string | false [ A valid URL to redirect to | false: use the default redirect location ] 
         */
        'onFailRedirect' => false,
        /**
         * A toast to be displayed after the user is redirected if they do not have the required auth state to view the page.
         * | array [ The toast properties to generate the toast ]
         */
        'onFailToast' => false
      ],
      /**
       * Page metadata
       */
      'meta' => [
        'title'       => '',
        'description' => '',
        'canonical'   => '',
        'image'       => '',
        'theme'       => '',
        'path'        => preg_replace('/(.php|index)/', '', $_SERVER['SCRIPT_NAME'])
      ],
      /**
       * SHiFT Code retrieval settings
       */
      'shift' => [
        'game'               => null,
        'status'             => [ 'active' ],
        'platform'           => null,
        'owner'              => null,
        'code'               => null,
        'order'              => 'default',
        'limit'              => 10,
        'page'               => 1,
        'readOnlyProperties' => [ 'game', 'owner', 'limit' ]
      ]
    ];

    
    $settings = array_replace_recursive($defaults, $page ?? []);
    
    if (isset($page['shift']['readOnlyProperties'])) {
      $settings['shift']['readOnlyProperties'] = $page['shift']['readOnlyProperties'];
    }
    if (isset($settings['shift']['owner']) && $settings['shift']['owner'] == '$user') {
      $settings['shift']['owner'] = auth_user_id();
    }

    /**
     * Settings for the current page
     */
    define("PAGE_SETTINGS", $settings);
    
    // Page authentication
    (function () {
      $required = PAGE_SETTINGS['auth']['requireState'];
      $defaultFailRedirect = '';
      $onFailRedirect = PAGE_SETTINGS['auth']['onFailRedirect'];
      $defaultToastProps = [
        'settings' => [
          'id' => 'auth_state_mismatch_toast',
          'duration' => 'medium'
        ]
      ];
      $onFailToast = PAGE_SETTINGS['auth']['onFailToast'];
      $loggedIn = auth_isLoggedIn();

      // Authentication State does not match required state
      if ($required == 'auth' && !$loggedIn || $required == 'no-auth' && $loggedIn) {
        if ($required == 'auth') {
          $defaultFailRedirect = '/account/login';
          $defaultToastProps['content'] = [
            'title' => 'Currently Logged Out',
            'body' => 'You must be logged in to view this content.'
          ];
        }
        else if ($required == 'no-auth') {
          $defaultFailRedirect = '/';
          $defaultToastProps['content'] = [
            'title' => 'Currently Logged In',
            'body' => 'You must be logged out to view this content.'
          ];
        }

        if ($onFailToast) {
          $_SESSION['toasts'][] = array_replace_recursive($defaultToastProps, $onFailToast);
        }

        response_redirect($onFailRedirect !== false ? $onFailRedirect : $defaultFailRedirect . '?continue=' . clean_url($_SERVER['REQUEST_URI']));
      }
    })();

    // Session timestamp
    $_SESSION['timestamp'] = time();
  })();  
?>