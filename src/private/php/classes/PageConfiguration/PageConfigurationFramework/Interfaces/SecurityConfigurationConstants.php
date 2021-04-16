<?php
  namespace ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Interfaces;

  /** Constant values defined for the `SecurityConfiguration`. */
  interface SecurityConfigurationConstants {
    /** @var string[] The *Relative URL*s where the Current User will be *Redirected* by default if they do not have permission to view the page. 
     * Values are grouped by the *Desired Login Status*.
    */
    const DEFAULT_FAILURE_REDIRECT = [
      'require_login'   => '/account/login',
      'require_logout'  => '/'
    ];
    /** @var array The *Default Toast* displayed if the Current User does not have permission to view the page. 
     * Values are grouped into `common` settings, as well as by the *Desired Login Status*.
    */
    const DEFAULT_FAILURE_TOAST = [
      'common'        => [
        'settings'       => [
          'id'              => 'page_configuration_authentication_failure_toast',
          'duration'        => 'medium'
        ]
      ],
      'require_login' => [
        'content'        => [
          'title'           => 'Login Required',
          'body'            => 'You must be Logged In to access this page.'
        ]
      ],
      'require_logout' => [
        'content'         => [
          'title'            => 'Logout Required',
          'body'             => 'You cannot access this page while you are Logged In.'
        ]
      ]
    ];
  }
?>