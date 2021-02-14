<?php
  namespace ShiftCodesTK\Users;
  use ShiftCodesTK\Strings;

  /** Represents the *Current User* of the session. */
  final class CurrentUser extends ICurrentUser {
    /**
     * Initialize a new `UserManager` instance
     * 
     * @param null|string $user_id The `user_id` of the *User* that is being logged in. Can be omitted if no user is being logged in.
     * @param bool $fetch_user_data Indicates if the full `UserRecord` is to be retrieved for the user.
     * - If **false**, only the `SYNCED_SESSION_PROPERTIES` will be populated and available for use. 
     * @return CurrentUser Returns the new `CurrentUser` instance.
     */
    protected function __construct(string $user_id = null, bool $fetch_user_data = false) {
      if ($user_id) {
        $this->sync_with_session(SYNC_TO_OBJECT);

        if ($fetch_user_data) {
          $this->get_user_data($user_id);
        }
        if ($token = $this->check_persistent_token()) {
          $this->persistent_token = $token;
        }

        $this->login_state = true;
        $this->check_permissions();
      }
    }
  }
?>
