<?php
  namespace ShiftCodesTK\Users;

  /** The `UserManager` is responsible for managing a `User`. */
  final class UserManager extends IUserManager {
    /**
     * Initialize a new `UserManager` instance
     * 
     * @param string|array|null $user If provided, a `string` representing the *User ID* of the User whose data is to be retrieved, or an `array` of *User Data*.
     * @return UserManager Returns the new `UserManager` instance.
     */
    public function __construct($user = null) {
      if ($user) {
        return $this->get_user_data($user);
      }
    }
  }
?>