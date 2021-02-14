<?php
  namespace ShiftCodesTK\Users;
  
  /** Represents a User's *Full Record* */
  class UserRecord extends IUserRecord {
    /**
     * Initialize a new `UserRecord`
     * 
     * @param string|array|null $user If provided, a `string` representing the *User ID* of the User whose data is to be retrieved, or an `array` of *User Data*.
     */
    public function __construct ($user = null) {
      if ($user) {
        return $this->get_user_data($user);
      }
    }
  }
?>