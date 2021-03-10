<?php
  namespace ShiftCodesTK\Users;
  use const \ShiftCodesTK\DATE_FORMATS;
  use \ShiftCodesTK\Auth,
      \ShiftCodesTK\Strings,
      \ShiftCodesTK\Database,
      \ShiftCodesTK\Validations,
      \ShiftCodesTK\Users\Constraints;

  /** The `IUserIDManager` is responsible for managing a User's unique *User ID*. */
  abstract class IUserIDManager extends IUserRecord {
    /** @var string The *prefix* of all User IDs */
    public const USER_ID_PREFIX = '14';

    /** @var ShiftCodesTKDatabaseQuery The Query used to check a User ID. */
    private static $check_user_id_query = null;

    /**
     * Check if a given *User ID* exists
     * 
     * @param string $user_id The *User ID* to check.
     * @return bool Returns **true** if the given *User ID* exists, or **false** if it does not.
     */
    public static function check_user_id ($user_id) {
      if (!self::$check_user_id_query) {
        $checkQueryStr = "SELECT 
                          COUNT(`user_id`) 
                            AS 'count'
                          FROM `auth_users`
                          WHERE `user_id` = ?
                          LIMIT 1";
        $checkOptions = [
          'collapse_all' => true,
          'format_parameters' => [
            'count'              => [
              'change_type'         => 'bool'
            ]
          ]
        ];
        $checkParams = new \ShiftCodesTKDatabasePreparedVariables('s');
  
        self::$check_user_id_query = new \ShiftCodesTKDatabaseQuery($checkQueryStr, $checkOptions, $checkParams);
      }
  
      $query = &self::$check_user_id_query;
  
      return $query->prepared_variables->change_variables($query, [ $user_id ]);
    }

    /**
     * Add a User ID for the User
     * 
     * @param string|null $user_id The *User ID* of the user. If omitted, a new *User ID* will be generated.
     * @return string|false Returns the new or existing *User ID* on success, or **false** if an error occurred.
     * @throws UnexpectedValueException Throws an `UnexpectedValueException` if the provided *User ID* does not fit the validation constraints.
     * @throws Error Throws an error if a valid User ID could not be generated.
     */
    public function add_user_id ($user_id = null) {
      // Set Existing User ID
      if ($user_id) {
        $validationObj = new \ValidationProperties(Constraints::USER_ID);
        $validation = $validationObj->check_parameter($user_id, 'user_id');
  
        if (!$validation['valid']) {
          throw new \UnexpectedValueException("User ID Validation Failed: {$validation['errors'][0]['message']}");
        }
  
        $this->user_id = $user_id;
      }
      // Generate New User ID
      else {
        $newID = '';
        $maxAttempts = 15;
  
        for ($attempts = 0; $attempts <= $maxAttempts; $attempts++) {
          $newID = (string) Auth\random_unique_id(
            Constraints::USER_ID['validations']['range']['is'] - strlen(self::USER_ID_PREFIX), 
            self::USER_ID_PREFIX
          );
  
          $newIDResult = self::check_user_id($newID);
  
          if ($newIDResult === false) {
            $this->user_id = $newID;
            break;
          }
          if ($attempts == $maxAttempts) {
            throw new \Error("Could not generate a valid User ID");
          }
        }
      }
  
      return $this->user_id;
    }
  }
  /** The `IUsernameManager` is responsible for managing a User's unique *Username*.  */
  abstract class IUsernameManager extends IUserIDManager {
    /** @var array|null If `get_username_eligibility_data()` has been called at least once, contains the *User's Username Eligibility Data*. */
    private $username_eligibility_data = null;

    /**
     * Check the availability of a given username
     * 
     * @param string $username The username to check.
     * @return boolean|null Returns **true** if the username is available, and **false** if it is not. Returns **null** if an error occurs. 
     * @throws \UnexpectedValueException Throws an `\UnexpectedValueException` if the username does not fit the validation constraints.
     */
    public static function check_username_availability ($username) {
      $username = (function () use ($username) {
        $validationProperties = new \ValidationProperties(Constraints::USERNAME);
        $validatedUsername = $validationProperties->check_parameter($username, 'username');

        if (!$validatedUsername['valid']) {
          throw new \UnexpectedValueException("Username Validation Failed: {$validatedUsername['errors'][0]['message']}");
        }

        $username = \ShiftCodesTKDatabase::escape_string($validatedUsername['parameter']);

        return $username;
      })();
      $query_str = "SELECT COUNT(`username`) AS 'count'
                    FROM `auth_users`
                    WHERE `username` = '{$username}'
                    LIMIT 1";
      $query_options = [
        'collapse_all' => true,
        'format_parameters'      => [
          'count'                   => [
            'change_type'              => 'bool'
          ]
        ]
      ];
      $query = new \ShiftCodesTKDatabaseQuery($query_str, $query_options);
      $query_result = $query->query();

      if (is_bool($query_result)) {
        return !$query_result;
      }
      else {
        return null;
      }
    }

    /**
     * Retrieve username eligibility data for the user
     * 
     * @return array Returns an `array` made up of the user's username eligibility data:
     * 
     * | Key | Description |
     * | --- | --- |
     * | *timestamp* | The timestamp of the last time the user changed their username. |
     * | *count* | The number of times the user has recently changed their username. |
     * @throws Error Throws an Error if the user's eligibility information could not be retrieved.
     */
    private function get_username_eligibility_data () {
      if (!$this->username_eligibility_data) {
        if ($this->user_id) {
          $lastChangeQueryStr = "SELECT `last_username_change`
                                FROM `auth_user_records`
                                WHERE `user_id` = '{$this->user_id}'
                                LIMIT 1";
          $lastChangeQueryOptions = [
            'collapse_all' => true
          ];
          $lastChangeQuery = new \ShiftCodesTKDatabaseQuery($lastChangeQueryStr, $lastChangeQueryOptions);
          $lastChangeQueryResult = $lastChangeQuery->query();
    
          if ($lastChangeQueryResult === false) {
            throw new \Error("Could not retrieve username eligibility information.");
          }
  
          $this->username_eligibility_data = $lastChangeQueryResult;
        }
        else {
          $eligibilityData = [
            'count'     => 0,
            'timestamp' => getFormattedTimestamp()
          ];

          $this->username_eligibility_data = $eligibilityData;

          return $eligibilityData;
        }
      }

      return $this->username_eligibility_data;
    }

    /**
     * Check a user's eligibility to change their *Username*
     * 
     * @return bool Returns **true** if the user is eligible to change their username, or **false** if they are not.
     */
    public function check_username_eligibility () {
      $eligibilityData = $this->get_username_eligibility_data();
      $now = new \DateTime('now', new \DateTimeZone('utc'));
      $threshold = new \DateTime($eligibilityData['timestamp']);
      $threshold->add(new \DateInterval('PT24H'));

      $changedUsernameRecently = $now->getTimestamp() < $threshold->getTimestamp();
      $canChangeUsername = !$changedUsernameRecently || $eligibilityData['count'] < 2;

      // Reset Count
      if ($canChangeUsername && (!$changedUsernameRecently && $eligibilityData['count'] > 0)) {
        $this->update_username_eligibility(null, 0);
      }

      return $canChangeUsername;
    }
    /**
     * Update the User's Username Eligibility Data
     * 
     * @param string|int|null $timestamp If provided, the *timestamp* of the last time the user changed their username. 
     * - Must be in the `ISO8601` format (`Y-m-d\TH:i:sO`).
     * - Alternatively, the @see `UsernameDefinitions::UPDATE_ELIGIBILITY_UPDATE_TIMESTAMP` will update the timestamp to the current time.
     * @param int|null $count If provided, the number of times the user has recently changed their username.
     * @return array Returns the updated *Username Eligibility Data*.
     * @throws TypeError Throws a `TypeError` is an invalid value was provided for `$timestamp` or `$count`. 
     * @throws Error Throws an `Error` if the user's username eligibility could not be updated in the database.
     */
    public function update_username_eligibility ($timestamp = null, $count = null) {
      if ($timestamp !== null) {
        if (strtolower($timestamp) == 'now') {
          $timestamp = getFormattedTimestamp(\ShiftCodesTK\DATE_FORMATS['iso']);
        }

        if (!Validations\check_date($timestamp, \ShiftCodesTK\DATE_FORMATS['iso'])) {
          throw new \TypeError("\"{$timestamp}\" is not a valid timestamp.");
        }

        $this->username_eligibility_data['timestamp'] = $timestamp;
      }
      if ($count !== null) {
        if (!is_int($count)) {
          throw new \TypeError("\"{$timestamp}\" is not a valid value for the count.");
        }

        $this->username_eligibility_data['count'] = $count;
      }

      // Update Database
      (function () {
        $eligibilityData = \ShiftCodesTKDatabase::escape_string(json_encode($this->username_eligibility_data, JSON_UNESCAPED_UNICODE));
        $queryStr = "UPDATE `auth_user_records`
                    SET `last_username_change` = '{$eligibilityData}'
                    WHERE `user_id` = '{$this->user_id}'
                    LIMIT 1";
        $queryOptions = [
          'collapse_all'      => true,
          'format_parameters' => [
            'affected_rows'      => [
              'change_type'         => 'bool'
            ]
          ]
        ];
        $query = new \ShiftCodesTKDatabaseQuery($queryStr, $queryOptions);
        $queryResult = $query->query();

        if (!$queryResult) {
          throw new \Error("Username Eligibility Data was not updated successfully.");
        }
      })();

      return $this->username_eligibility_data;
    }
    /**
     * Update the username for the user.
     * 
     * @param string $username The username to be set.
     * @param bool $user_change Indicates if the user is performing the operation.
     * - If **true**:
     * - - The user must be eligible to change their username (see `check_username_eligibility()`). 
     * - - The User's *Username Eligibility Record* (see `update_username_eligibility()`) will automatically be updated. 
     * - - The User's *Account Activity Timestamp* (see `update_account_activity_timestamp()`) will automatically be updated. 
     * @return bool Returns **true** if the User's Username was updated successfully, or **false** if it was not.
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if the username does not fit the validation constraints.
     * @throws \Error Throws an `Error` if any of the following issues occur:
     * 
     * | Error Code | Error |
     * | --- | --- | 
     * | 1 | `$user_change` is **true** and the user is not currently eligible to change their username. |
     * | 2 | `$username` is already in use. |
     * | 4 | The username was not succesfully updated in the database. |
     */
    public function change_username ($username, $user_change = false) {
      $usernameAvailability = self::check_username_availability($username);

      if ($usernameAvailability) {
        if ($user_change && !$this->check_username_eligibility()) {
          throw new \Error("User \"{$this->user_id}\" is not eligible to change their username.", 1);
        }

        $queryUsername = \ShiftCodesTKDatabase::escape_string($username);
        $queryStr = "UPDATE `auth_users`
                    SET `username` = '{$queryUsername}'
                    WHERE `user_id` = '{$this->user_id}'
                    LIMIT 1";
        $queryOptions = [
          'collapse_all'      => true,
          'format_parameters' => [
            'affected_rows'      => [
              'change_type'         => 'bool'
            ]
          ]
        ];
        $query = new \ShiftCodesTKDatabaseQuery($queryStr, $queryOptions);
        $queryResult = $query->query();

        if (!$queryResult) {
          throw new \Error("Username was not updated successfully.", 4);
        }

        $this->username = $queryUsername;

        if ($this->is_current_user()) {
          $currentUser = CurrentUser::get_current_user();

          $currentUser->username = $queryUsername;
          $currentUser->sync_with_session();
        }

        if ($user_change) {
          $this->update_username_eligibility('now', $this->username_eligibility_data['count'] + 1);
        }

        return true;
      }
      else {
        throw new \Error("Username \"{$username}\" has already been claimed.", 2);
      }

      return false;
    }
  }
  /** The `IUserRoleManager` is responsible for managing a User's *User Roles*. */
  abstract class IUserRoleManager extends IUsernameManager {
    /**
     * Synchronize the User's Roles with the Database
     * 
     * @return boolean Returns **true** if the user's roles were successfully updated, and **false** if they were not. 
     * - This will return **false** if the user's roles were the same as the roles in the database.
     */
    public function sync_roles () {
      $roles = (int) $this->user_roles;
      $user_id = Strings\escape_sql($this->user_id);
      $query_str = "UPDATE `auth_users`
                      SET `user_roles` = '{$roles}'
                      WHERE `user_id` = '{$user_id}'
                      LIMIT 1;
                    UPDATE `auth_user_records`
                      SET `last_activity` = CURRENT_TIMESTAMP()
                      WHERE `user_id` = '{$user_id}'
                      LIMIT 1;";
      $query_options = [
        'allow_multiple_queries' => true,
        'collapse_all'           => true,
        'format_parameters'      => [
          'affected_rows'           => [
            'change_type'         => 'bool'
          ]
        ]
      ];
      $query = new Database\DatabaseQuery($query_str, $query_options);
      $query_result = $query->query();

      return $query_result[0];
    }
    /**
     * Grant a given role to the user
     * 
     * @param string $role The name of the *User Role* to grant.
     * @return bool Returns **true** if the user was granted the `$role`, or **false** if they were not. 
     * - Trying to add a role that the user already possesses will always return **false**.
     * @throws \UnexpectedValueException Throws an `UnexepectedValueException` if `$role` is not a valid User Role.
     * @throws \Error Throws an Error if the `$role` couldn't be granted to the user.
     */
    public function add_role ($role) {
      self::check_role($role, true);

      if ($this->has_role($role)) {
        trigger_error("User \"{$this->user_id}\" already has role \"{$role}\".", E_USER_WARNING);
        return false;
      }

      // Update Roles
      $this->user_roles = $this->user_roles | self::get_role_data($role)['value'];
 
      // Update Database
      $sync_result = $this->sync_roles();
  
      if (!$sync_result) {
        trigger_error("Role \"{$role}\" could not be granted for user \"{$this->user_id}\".", E_USER_WARNING);
      }
  
      return $this->has_role($role);
    }
    /**
     * Revoke a given role from the user
     * 
     * @param string $role The name of the *User Role* to revoke.
     * @return bool Returns **true** if the `$role` was revoked from the user, or **false** if it was not.
     * - Trying to remove a role that the user doesn't have will always return **false**.
     * @throws \UnexpectedValueException Throws an `UnexepectedValueException` if `$role` is not a valid User Role.
     * @throws \Error Throws an Error if the `$role` couldn't be revoked from the user.
     */
    public function remove_role ($role) {
      self::check_role($role, true);

      if (!$this->has_role($role)) {
        trigger_error("User \"{$this->user_id}\" does not have role \"{$role}\".", E_USER_WARNING);
        return false;
      }

      // Update Roles
      $this->user_roles = $this->user_roles &= ~self::get_role_data($role)['value'];
  
      // Update Database
      $sync_result = $this->sync_roles();
  
      if (!$sync_result) {
        trigger_error("Role \"{$role}\" could not be revoked from user \"{$this->user_id}\".", E_USER_WARNING);
      }
  
      return !$this->has_role($role);
    }
  }
  /** The `IUserAccountStateManager` is responsible for managing a User's *Account State* or *Account Status*. */
  abstract class IAccountStateManager extends IUserRoleManager {
    /**
     * Change the User's current *Account State*
     * 
     * @param ACCOUNT_STATE_ACTIVE|ACCOUNT_STATE_PENDING|ACCOUNT_STATE_SUSPENDED|ACCOUNT_STATE_BLOCKED|ACCOUNT_STATE_DELETED $account_state
     * Indicates the new *Account State* to be set. 
     * 
     * | State | Description |
     * | --- | --- |
     * | `ACCOUNT_STATE_ACTIVE` | The User's Account will be fully available. |
     * | `ACCOUNT_STATE_PENDING` | The User's Account will be set to *Pending*. It may be restricted, or not available at all, until the User confirms their account. |
     * | `ACCOUNT_STATE_SUSPENDED` | The User's Account will be set to *Suspended*. They will not be permitted to sign in to their account until their suspension is over. |
     * | `ACCOUNT_STATE_BLOCKED` | The User's Account will be *Blocked*. It will no longer be accessible. **All User Data (with the exception of the `user_id` & `email_address`) will be deleted.** |
     * | `ACCOUNT_STATE_DELETED` | The User's Account will be *Deleted*. It will no longer be accessible. **All User Data (with the exception of the `user_id`) will be deleted.** |
     * @return bool Returns **true** if the User's *Account State* was successfully updated, or **false** if it was not.
     */
    protected function change_account_state ($account_state): bool {
      try {
        // Parameter Validations
        (function () use ($account_state) {
          $matches = [
            self::ACCOUNT_STATE_ACTIVE,
            self::ACCOUNT_STATE_BLOCKED,
            self::ACCOUNT_STATE_DELETED,
            self::ACCOUNT_STATE_PENDING,
            self::ACCOUNT_STATE_SUSPENDED
          ];

          if (!check_match($account_state, $matches)) {
            throw new \Exception("\"{$account_state}\" is not a valid Account State.");
          }
          if (check_match($this->account_state, [ $matches[1], $matches[2] ])) {
            throw new \Exception("You cannot change the Account State of a {$this->account_state} user.");
          }
          if ($account_state == $this->account_state) {
            throw new \Exception("This user is already {$account_state}.");
          }
        })();
        
        $isDeletionState = array_search($account_state, [ self::ACCOUNT_STATE_BLOCKED, self::ACCOUNT_STATE_DELETED ]) !== false;
        $queryStr = (function () use ($account_state, $isDeletionState) {
          $queryStr = '';
          
          // Delete User Data Properties
          (function () use ($account_state, $isDeletionState) {
            if ($isDeletionState) {
              $reflection = new \ReflectionClass($this);
              $properties = $reflection->getProperties();
              $ignoredProperties = [
                'user_id'
              ];
  
              if ($account_state == self::ACCOUNT_STATE_BLOCKED) {
                $ignoredProperties[] = 'email_address';
              }
    
              foreach ($properties as $property) {
                $propertyName = $property->getName();
    
                if (array_search($propertyName, $ignoredProperties) === false) {
                  $this->$propertyName = null;
                }
              }
            }
          })();
          // Update `auth_users`
          (function () use ($account_state, $isDeletionState, &$queryStr) {
            $queryStr = "UPDATE `auth_users`
                            SET 
                                `account_state` = '{$account_state}',";
      
              if ($isDeletionState) {
                if ($account_state === self::ACCOUNT_STATE_DELETED) {
                  $queryStr .= "`email_address` = null,";
                }
      
                $queryStr .= "`username` = null,
                              `password` = null,
                              `user_roles` = null,
                              `redemption_id` = null";
              }
      
              // Remove trailing comma
              $queryStr = preg_replace('/, {0,1}$/', '', $queryStr);
      
              $queryStr .= " WHERE `user_id` = '{$this->user_id}'; ";
          })();
          // Update `auth_user_records`
          (function () use ($isDeletionState, &$queryStr) {
            if ($isDeletionState) {
              $queryStr .= "DELETE FROM `auth_user_records`
                            WHERE `user_id` = '{$this->user_id}'
                            LIMIT 1; ";
            }
          })();
          // Update `shift_codes_redeemed`
          (function () use ($isDeletionState, &$queryStr) {
            if ($isDeletionState && $this->redemption_id) {
              $queryStr .= "DELETE FROM `shift_codes_redeemed`
                            WHERE `redemption_id` = '{$this->redemption_id}';";
            }
          })();
  
          return $queryStr;
        })();
        $queryOptions = [
          'allow_multiple_queries' => true,
          'collapse_result'        => true,
          'collapse_row'           => true
        ];
  
        $transaction = \ShiftCodesTKDatabase::start_transaction();
        $query = new \ShiftCodesTKDatabaseQuery($queryStr, $queryOptions);
        $queryResult = $query->query();
  
        foreach ($queryResult as $resultIndex => $result) {
          if (!$result) {
            \ShiftCodesTKDatabase::close_transaction($transaction, false);
            throw new \Exception("Chunk {$resultIndex} of the User's Account Data was not updated successfully.");
          }
        }
  
        \ShiftCodesTKDatabase::close_transaction($transaction);
        $this->account_state = $account_state;
  
        return true;
      }
      catch (\Throwable $exception) {
        trigger_error("Failed to update the User's Account State: {$exception->getMessage()}");
        return false;
      }
    }

    /**
     * *(Re)Activates* the user, removing any restrictions placed on their account by `require_user_confirmation()` or `suspend_user()`.
     * - Sets the User's `account_state` to *active*.
     * - Not available if the User's *Account State* is currently *Active*, *Blocked*, or *Deleted*. 
     * 
     * @return bool Returns **true** if the User's Account was *Activated* successfully, or **false** if it was not.
     */
    public function activate_user (): bool {
      return $this->change_account_state(self::ACCOUNT_STATE_ACTIVE);
    }
    /**
     * Locks the User's Account until it has been *confirmed*.
     * - Sets the User's `account_state` to *pending*.
     * - Not available if the User's *Account State* is currently *Pending*, *Blocked*, or *Deleted*. 
     * 
     * @return bool Returns **true** if the User's Account was set to *Pending* successfully, or **false** if it was not.
     */
    public function require_user_confirmation (): bool {
      return $this->change_account_state(self::ACCOUNT_STATE_PENDING);
    }  
    public function block_user () {}
    public function delete_user () {}
  }
  /** The `ISuspensionsManager` is responsible for temporary *User Account Suspensions*. */
  abstract class ISuspensionsManager extends IAccountStateManager {
    /** @var string The User's Suspension will last for *6 hours*. */
    public const SUSPENSION_DURATION_SHORT = 'PT6H';
    /** @var string The User's Suspension will last for *3 days*. */
    public const SUSPENSION_DURATION_MEDIUM = 'P3D';
    /** @var string The User's Suspension will last for *1 week*. */
    public const SUSPENSION_DURATION_LONG = 'P1W';
    
    /**
     * Suspends the User for a set amount of time.
     * - Sets the User's `account_state` to *suspended*.
     * - Not available if the User's *Account State* is currently *Suspended*, *Blocked*, or *Deleted*. 
     * 
     * @param string $duration Indicates the *duration* of the suspension. A *Duration `Constant`*, *DateInterval Spec `String`*, or `null` can be provided.
     * 
     * | Value | Behavior |
     * | --- | --- |
     * | SUSPENSION_DURATION_SHORT | Suspends the user for *6 hours*. |
     * | SUSPENSION_DURATION_MEDIUM | Suspends the user for *3 days*. |
     * | SUSPENSION_DURATION_LONG | Suspends the user for *1 week*. | 
     * | *DateInterval Spec String* | Suspends the user for the provided amount of time. |
     * | **null** | Suspends the user until their account is manually reactivated with `activate_user()`. |
     * @return bool Returns **true** if the User's Account was *suspended* successfully, or **false** if it was not.
     */
    public function suspend_user ($duration = null) {
      $suspensionDate = (function () use ($duration) {
        try {
          $date = new \DateTime();
          $date->add(new \DateInterval($duration));
  
          return $date->format(\ShiftCodesTK\DATE_FORMATS['date_time']);
        }
        catch (\Throwable $exception) {
          trigger_error("\"{$duration}\" is not a valid DateInterval Spec String.");
          return false;
        }
        
      })();
  
      if (!$suspensionDate) {
        return false;
      }
  
      $transaction = \ShiftCodesTKDatabase::start_transaction();
      $stateResult = $this->change_account_state(self::ACCOUNT_STATE_SUSPENDED);
  
      if (!$stateResult) {
        \ShiftCodesTKDatabase::close_transaction($transaction, false);
        return false;
      }
  
      $dateQuery = new \ShiftCodesTKDatabaseQuery("
        UPDATE `auth_user_records`
        SET `suspension_date` = '{$suspensionDate}'
        WHERE `user_id` = '{$this->user_id}'
        LIMIT 1",
        [
          'collapse_all' => true
        ]
      );
      $dateResult = $dateQuery->query();
  
      if (!$dateResult) {
        \ShiftCodesTKDatabase::close_transaction($transaction, false);
        return false;
      }
      if (isset($this->suspension_date)) {
        $this->suspension_date = $suspensionDate;
      }
  
      return $dateResult == 1;
    }
  }
  
  /** 
   * The `IUserManager` is responsible for managing a `User` and their associated `UserRecord`. 
   **/
  abstract class IUserManager extends ISuspensionsManager {
    /**
     * Check if an *Email Address* is already registered to another user.
     * 
     * @param string $email_address The *Email Address* to check.
     * @return boolean|null Returns **true** if the Email Address is available, and not currently registered to another user, or **false** if it is not. Returns **null** if an error occurs. 
     * @throws \UnexpectedValueException Throws an `\UnexpectedValueException` if the Email Address does not fit the validation constraints.
     */
    public static function check_email_availability ($email_address) {
      $email_address = (function () use ($email_address) {
        $validationProperties = new \ValidationProperties(Constraints::EMAIL_ADDRESS);
        $validatedEmail = $validationProperties->check_parameter($email_address, 'email_address');

        if (!$validatedEmail['valid']) {
          throw new \UnexpectedValueException("Email Address Validation Failed: {$validatedEmail['errors'][0]['message']}");
        }

        $email_address = \ShiftCodesTKDatabase::escape_string($validatedEmail['parameter']);

        return $email_address;
      })();
      $query_str = "SELECT COUNT(`email_address`) AS 'count'
                    FROM `auth_users`
                    WHERE `email_address` = '{$email_address}'
                    LIMIT 1";
      $query_options = [
        'collapse_all' => true,
        'format_parameters'      => [
          'count'                   => [
            'change_type'              => 'bool'
          ]
        ]
      ];
      $query = new \ShiftCodesTKDatabaseQuery($query_str, $query_options);
      $query_result = $query->query();

      if (is_bool($query_result)) {
        return !$query_result;
      }
      else {
        return null;
      }
    }
    /** Update the *Profile Stats Privacy Preference* of the user.
     * 
     * @param string $privacy A `PROFILE_STATS_*` constant representing the new *Profile Stats Privacy Preference* to be set.
     *
     * | Preference | Description |
     * | --- | --- |
     * | `PROFILE_STATS_HIDDEN` | The user's profile stats are only visible to them. |
     * | `PROFILE_STATS_PRIVATE` | The user's profile stats are only visible to users who are currently logged-in. |
     * | `PROFILE_STATS_PUBLIC` | The user's profile stats are visible to everyone. |
     * @return bool Returns **true** on success or **false** if an error occurred. 
     * - Will always return **false** if `$privacy` is the same as the current value.
     */
    public function update_profile_stats_privacy (string $privacy) {
      if (array_search($privacy, [ self::PROFILE_STATS_HIDDEN, self::PROFILE_STATS_PRIVATE, self::PROFILE_STATS_PUBLIC ]) === false) {
        throw new \UnexpectedValueException("\"{$privacy}\" is not a valid Privacy Preference.");
      }
      if ($privacy !== $this->profile_stats_preference) {
        $queryStr = "UPDATE `auth_user_records`
                     SET `profile_stats_preference` = ?
                     WHERE `user_id` = ?
                     LIMIT 1";
        $queryParams = [
          $privacy,
          $this->user_id
        ];
        $query = new Database\DatabaseQuery(
          $queryStr, 
          [ 'collapse_all' => true ], 
          new Database\PreparedVariables('ss', $queryParams)
        );
  
        if ($query->query()) {
          $this->profile_stats_preference = $privacy;

          return true;
        }
      }

      return false;
    }
  }
?>