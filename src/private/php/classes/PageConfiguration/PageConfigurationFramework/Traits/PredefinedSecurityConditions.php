<?php 
  namespace ShiftCodesTK\PageConfiguration\PageConfigurationFramework\Traits;

  use ShiftCodesTK\PageConfiguration,
      ShiftCodesTK\PageConfiguration\SecurityCondition,
      ShiftCodesTK\Validations,
      ShiftCodesTK\Strings,
      ShiftCodesTK\Users\CurrentUser;

  /** Exposes additional methods that can add predefined `SecurityCondition`s. */
  trait PredefinedSecurityConditions {
    use SecurityConfiguration;

    /** Set if the Page requires the Current User to be *Logged In* or not
     * 
     * A *Redirect* is configured by default.
     * 
     * @param bool $login_policy The *Login Policy* for the page:
     * 
     * | Value    | Description                                              |
     * | ---      | ---                                                      |
     * | `true`   | The user **must** be *Logged In* to access the page.     |
     * | `false`  | The user **must not** be *Logged In* to access the page. |
     * @param bool $return_condition Indicates if the new `SecurityCondition` should be returned, instead of the `PageConfiguration`. Defaults to **false**.
     * @return PageConfiguration|SecurityCondition Returns the updated `PageConfiguration` or `SecurityCondition` object, depending on the value of `$return_condition`.
     */
    public function setUserLoginCondition (bool $login_policy, bool $return_condition = false): object {
      $default_redirect = $login_policy
                          ? '/account/login'
                          : '/';
      $security_condition = (
        (new SecurityCondition(
          function (PageConfiguration $page_configuration) use ($login_policy) {
            return CurrentUser::is_logged_in() === $login_policy;
          },
          'require_user_login'
        ))
        ->setFailureRedirect($default_redirect, $login_policy)
      );

      $this->addSecurityCondition($security_condition);

      if (!$return_condition) {
        return $this;
      }
      else {
        return $security_condition;
      }
    }
    /** Set if the Page requires the Current User to have a specific *User Role*
     * 
     * Both a *Redirect* and *Toast* are configured by default. 
     * 
     * @param string $user_role The *User Role* to check for.
     * @param bool $requires_role Indicates if the user is required to *have* or *not have* the `$user_role`:
     * 
     * | Value    | Description                                                     |
     * | ---      | ---                                                             |
     * | `true`   | The user **must** have the `$user_role` to access the page.     |
     * | `false`  | The user **must not** have the `$user_role` to access the page. |
     * @param bool $return_condition Indicates if the new `SecurityCondition` should be returned, instead of the `PageConfiguration`. Defaults to **false**.
     * @return PageConfiguration|SecurityCondition Returns the updated `PageConfiguration` or `SecurityCondition` object, depending on the value of `$return_condition`.
     */
    public function setUserRoleCondition (string $user_role, bool $requires_role = true, bool $return_condition = false): object {
      $default_redirect = '/';
      $default_toast = [ 
        'content' => [ 
          'body' => 'You do not have permission to access this resource: ' .
            ($requires_role
            ? 'Role ' . Strings\encode_html($user_role) . ' is Required.'
            : 'Role ' . Strings\encode_html($user_role) . ' is Forbidden.') 
        ] 
      ];
      $security_condition = (
        (new SecurityCondition(
          function (PageConfiguration $page_configuration) use ($user_role, $requires_role) {
            if (CurrentUser::is_logged_in()) {
              if (CurrentUser::get_current_user()->has_role($user_role) === $requires_role) {
                return true;
              }
            }
  
            return false;
          },
          'require_user_role'
        ))
          ->setFailureRedirect($default_redirect, false)
          ->setFailureToast($default_toast)
      );

      $this->addSecurityCondition($security_condition);

      if (!$return_condition) {
        return $this;
      }
      else {
        return $security_condition;
      }

      return $this;
    }
    /** Set if the Page requires the Current User to have a specific *User Permission*
     * 
     * Both a *Redirect* and *Toast* are configured by default.
     * 
     * @param string $user_permission The *User Permission* to check for.
     * @param bool $requires_permission Indicates if the user is required to *have* or *not have* the `$user_permission`:
     * 
     * | Value    | Description                                                     |
     * | ---      | ---                                                             |
     * | `true`   | The user **must** have the `$user_role` to access the page.     |
     * | `false`  | The user **must not** have the `$user_role` to access the page. |
     * @param bool $return_condition Indicates if the new `SecurityCondition` should be returned, instead of the `PageConfiguration`. Defaults to **false**.
     * @return PageConfiguration|SecurityCondition Returns the updated `PageConfiguration` or `SecurityCondition` object, depending on the value of `$return_condition`.
     */
    public function setUserPermissionCondition (string $user_permission, bool $requires_permission = true, bool $return_condition = false): object {
      $default_redirect = '/';
      $default_toast = [ 
        'content' => [ 
          'body' => 'You do not have permission to access this resource: ' .
            ($requires_permission
            ? 'Permission ' . Strings\encode_html($user_permission) . ' is Required.'
            : 'Permission ' . Strings\encode_html($user_permission) . ' is Denied.') 
        ] 
      ];
      $security_condition = (
        (new SecurityCondition(
          function (PageConfiguration $page_configuration) use ($user_permission, $requires_permission) {
            if (CurrentUser::is_logged_in()) {
              if (CurrentUser::get_current_user()->has_permission($user_permission) === $requires_permission) {
                return true;
              }
            }
  
            return false;
          },
          'require_user_role'
        ))
          ->setFailureRedirect($default_redirect, false)
          ->setFailureToast($default_toast)
      );

      $this->addSecurityCondition($security_condition);

      if (!$return_condition) {
        return $this;
      }
      else {
        return $security_condition;
      }
    }
  }
?>