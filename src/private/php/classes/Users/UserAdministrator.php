<?php
  namespace ShiftCodesTK\Users;
  use ShiftCodesTK\Strings;

  /** Represents a *User Administrator*, capable of adding, updating, & modifying users, roles, and permissions. */
  final class UserAdministrator extends IUserAdmininistrator {
    /**
     * Initialize a new `UserAdmin` instance
     * 
     * @param CurrentUser $current_user The *Current User*. To be able to use the `UserAdmin`, the user must be *Logged In* and have the `USER_ADMIN` permission.
     * @return UserAdministrator Returns the new `UserAdmin` instance on success.
     * @throws \Error if any of the following occur:
     * 
     * | Error Code | Description |
     * | --- | --- |
     * | 1 | The `$current_user` is not *Logged In*. |
     * | 2 | The `$current_user` does not have the `USER_ADMIN` permission.
     */
    public function __construct (CurrentUser $current_user) {
      // Current User Validations
      (function () use ($current_user) {
        if (!$current_user::is_logged_in()) {
          throw new \Error("The Current User is not currently Logged In.", 1);
        }
        // else if (!$current_user->has_permission('USER_ADMIN')) {
        //   throw new \Error("The Current User does not have permission to use the User Administrator.", 2);
        // }
      })();
    }
  }
?>
