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
      // 'shift' => [
      //   'game'   => 'all',
      //   'owner'  => false,
      //   'code'   => false,
      //   'filter' => [ 'active' ],
      //   'order'  => 'default',
      //   'limit'  => 10,
      //   'offset' => 0
      // ]
    ];
    $settings = array_replace_recursive($defaults, $page ?? []);

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
          $defaultFailRedirect = '/login';
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

        response_redirect($onFailRedirect !== false ? $onFailRedirect : $defaultFailRedirect . '?redirect=' . clean_url(PAGE_SETTINGS['meta']['path']));
      }
    })();

    // Session timestamp
    $_SESSION['timestamp'] = time();
  })();  
?>