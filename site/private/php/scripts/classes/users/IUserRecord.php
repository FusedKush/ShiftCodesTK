<?php
  namespace ShiftCodesTK\Users;
  use ShiftCodesTK\Strings;
  
  /** Represents the User's *Profile Statistics Privacy Preference*. */
  abstract class IProfileStatsRecord extends IUser {
    /** @var string The user's profile stats are only visible to them. */
    public const PROFILE_STATS_HIDDEN = 'hidden';
    /** @var string The user's profile stats are only visible to users who are currently logged-in. */
    public const PROFILE_STATS_PRIVATE = 'private';
    /** @var string The user's profile stats are visible to everyone. */
    public const PROFILE_STATS_PUBLIC = 'public';

    /** 
     * @var PROFILE_STATS_HIDDEN|PROFILE_STATS_PRIVATE|PROFILE_STATS_PUBLIC Indicates the user's current Profile Stats Privacy Preference.
     * - @see **PROFILE_STATS_HIDDEN** The user's profile stats are only visible to them.
     * - @see **PROFILE_STATS_PRIVATE** The user's profile stats are only visible to users who are currently logged-in.
     * - @see **PROFILE_STATS_PUBLIC** The user's profile stats are only visible to everyone.
     **/
    public $profile_stats_preference = null;

    /** Checks the *Profile Stat Visibility* of the current user.
     * 
     * @return bool Returns **true** if the current user has permission to view this user's Profile Stats. Otherwise, returns **false**.
     */
    public function current_profile_stats_visibility () {
      switch ($this->profile_stats_preference) {
        case self::PROFILE_STATS_PUBLIC :
          return true;
        case self::PROFILE_STATS_PRIVATE :
          return CurrentUser::is_logged_in();
        case self::PROFILE_STATS_HIDDEN :
          return $this->is_current_user();
      }
    }
  }
  /** The `IUserAuth` is responsible for handling the *Last User Authentication State* for the user. */
  abstract class IUserAuth extends IProfileStatsRecord {
    /** @var string A *timestamp* of when the user's last account authorization check. */
    public $last_auth = null;

    /** Check the *Last Authentication Timestamp* for the *Current User* and determine if the account authentication is *Out of Date*.
     * 
     * @return bool Returns **true** if the Current User's Account Authentication is valid and is not out of date. Otherwise, returns **false**.
     * @throws Error If the User's Last Authentication Timestamp could not be retrieved from the server.
     */
    public function check_user_auth () {
      $lastSessionAuth = (new \DateTime($_SESSION['user']['last_auth']))
                         ->getTimestamp();
      $lastAuth = (function () {
        $queryStr = "SELECT `last_auth`
                     FROM `auth_user_records`
                     WHERE `user_id` = '{$this->user_id}'
                     LIMIT 1";
        $query = new \ShiftCodesTK\Database\DatabaseQuery($queryStr, [ 'collapse_all' => true ]);
        $queryResult = $query->query();

        if (!$queryResult) {
          throw new \Error("The user's Last Authentication Timestamp could not be retrieved.");
        }

        return (new \DateTime($queryResult))
                ->getTimestamp();
      })();
      $maxAge = (new \DateTime('@' . $lastAuth))
                ->add(new \DateInterval('P6M'))
                ->getTimestamp();

      // Authentication Data is out of date
      if ($lastSessionAuth < $lastAuth || time() > $maxAge) {
        return false;
      }

      return true;
    }
  }

  /** 
   * The `IUserRecord` is responsible for retrieving the *User Record Data* for a `UserRecord`. 
   **/
  abstract class IUserRecord extends IUserAuth {
    /** @var string A *timestamp* of when the user's account was created. */
    public $creation_date = null;
    /** @var null|string A *timestamp* of when the user last logged in. */
    public $last_login = null;
    /** @var string A *timestamp* of the user's last account activity. */
    public $last_activity = null;
    /** @var string A *timestamp* of the user's last *public* account activity. */
    public $last_public_activity = null;
    /** @var null|array The *Last Failed Login Details* for the user. */
    public $last_failed_login = null;
    /** @var null|array The *Username Eligibility Data* for the user. This is the same value as the *private* `Username->username_eligibility_data` property.  */
    public $last_username_change = null;
    /** @var null|string If the `User`'s `account_state` is **suspended**, this is the *timestamp* of when the suspension will end. */
    public $suspension_date = null;
    /** @var null|string If set, the path to the User's Profile Picture. */        
    public $profile_picture = null;
    /** @var int The number of SHiFT Codes the user has submitted. */
    public $shift_codes_submitted = null;

    /** Retrieve the *Profile Card Data* for the user.
     * 
     * Some properties may not be populated due to not having the appropriate permissions:
     * - `permissions['can_change_username']`
     * - `profile_stats['last_public_activity']`
     * - `profile_stats['creation_date']`
     * - `profile_stats['shift_codes_submitted']`
     * 
     * @return array|false Returns the *Profile Card Data `Array`* on success, or **false** if an error occurred.
     */
    public function get_profile_card_data () {
      $currentUser = CurrentUser::get_current_user();
      $isCurrentUser = $currentUser && $currentUser->user_id === $this->user_id;
      /** @var Users\UserRecord|Users\UserManager */
      $userData = (function () use ($isCurrentUser) {
        $userData = $this;

        if (!$userData->user_id) {
          return false;
        }
        if ($isCurrentUser) {
          $userData = $userData->get_manager();
        }

        return $userData;
      })();

      if ($userData) {
        $profileCardData = [
          'user_data'     => [],
          'permissions'   => [],
          'profile_stats' => []
        ];

        // User Data 
        $profileCardData['user_data'] = [
          'id'              => Strings\encode_html($userData->user_id),
          'username'        => Strings\encode_html($userData->username),
          'profile_picture' => $userData->profile_picture
                               ? Strings\encode_html($userData->profile_picture)
                               : null,
          'profile_stats_preference' => $userData->profile_stats_preference,
          'roles'           => $userData->get_roles()
        ];

        // Permissions
        (function () use (&$profileCardData, $currentUser, $userData, $isCurrentUser) {
          $canModerateUsers = $currentUser && $currentUser->has_permission("MODERATE_USERS");

          $profileCardData['permissions']['can_change_username'] = ($isCurrentUser
                                                  ? $userData->check_username_eligibility()
                                                  : null);
          $profileCardData['permissions']['can_edit'] = $isCurrentUser;
          $profileCardData['permissions']['can_enforce'] = (function () use ($currentUser, $userData, $isCurrentUser, $canModerateUsers) {
            // Not Current User
            if (!$isCurrentUser) {
              // Current User has Permissions
              if ($canModerateUsers) {
                // Current User has a higher role value than the Target User
                if ($currentUser->get_roles(self::USER_ROLES_GET_INT) > $userData->get_roles(self::USER_ROLES_GET_INT)) {
                  return true;
                }
              }
            }

            return false;
          })();
          $profileCardData['permissions']['can_report'] = !$canModerateUsers;
        })();

        // Profile Stats
        if ($userData->current_profile_stats_visibility()) {
          foreach ([ 'last_public_activity', 'creation_date', 'shift_codes_submitted' ] as $stat_name) {
            $profileCardData['profile_stats'][$stat_name] = $userData->$stat_name;
          }
        }

        return $profileCardData;
      }
      
      return false;
    }
  }
?>