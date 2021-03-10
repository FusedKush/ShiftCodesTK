<?php
  namespace ShiftCodesTK\Users;

  /** Represents a User of ShiftCodesTK */
  class User extends IUser {
    /**
     * Create a new `User` object
     * 
     * @param string|array|null $user If provided, a `string` representing the *User ID* of the User whose data is to be retrieved, or an `array` of *User Data*.
     * @throws TypeError Throws a TypeError if `$user` is not a `string`, `array`, or `null`.
     */
    public function __construct($user = null) {
      if ($user) {
        return $this->get_user_data($user);
      }
    }
  }
?>