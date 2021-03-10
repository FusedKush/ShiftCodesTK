<?php
  namespace ShiftCodesTK\Users;

  /** 
     * Manages the registration of a new `User`. 
     * - **Note**: The following methods inherited from `UserRecord` shouldn't be used as they may cause unexpected behavior:
     * - - `get_record()`
     * - - `get_manager()`
     * - - `get_user_data()`
     * - - `require_user_id()`
     **/
  class UserRegistration extends UserRecord {
    /** @var string The user's hashed password. */
    public $password = null;

    /**
     * Register a new `User`
     * 
     * @param array $new_user An array of `UserRegistration` properties that are used to register the user. See `Users\Constraints` for details on how these properties are constrained.
     * 
     * | Property | Type | Description |
     * | --- | --- | --- |
     * | **Required Properties** |||
     * | _email_address_ | `string` | The *Email Address* of the new user. |
     * | _username_ | `string` | The *Username* of the new user. |
     * | _password_ | `string` | The user's new *Password*. `UserRegistration->password` returns the *hashed* version of this value. |
     * | **Optional Properties** |||
     * | _redemption_id_ | `string` | The *Redemption ID* of the user if they already have one. |
     * | _profile_stats_privacy_ | `PROFILE_STATS_HIDDEN\|PROFILE_STATS_PRIVATE\|PROFILE_STATS_PUBLIC` | The *Privacy Preference* of the user regarding their *Profile Statistics*. |
     * @throws \Error Throws an `Error` if a required property is omitted from the `$new_user` array.
     */
    public function __construct($new_user) {
      $currentTimestamp = getFormattedTimestamp();
      $properties = [
        'timestamps' => [
          'creation_date',
          'last_auth',
          'last_activity',
          'last_public_activity'
        ],
        'user_required' => [
          'email_address',
          'username',
          'password'
        ],
        'user_optional' => [
          'redemption_id',
          'profile_stats_preference'
        ]
      ];

      $this->account_state = self::ACCOUNT_STATE_ACTIVE;
      $this->user_roles = $this->get_roles(self::USER_ROLES_GET_INT);
      $this->shift_codes_submitted = 0;
      $this->profile_stats_preference = self::PROFILE_STATS_HIDDEN;
      $this->user_id = (function () {
        $manager = new UserManager();
        $manager->add_user_id();

        return $manager->user_id;
      })();

      foreach ($properties['user_required'] as $property) {
        $value = $new_user[$property] ?? null;

        if (!isset($value)) {
          throw new \Error("Required Property \"{$property}\" was not provided.");
        }

        $constraints = constant(Constraints::class."::" . strtoupper($property));

        if ($constraints) {
          $validations = new \ValidationProperties($constraints);
          $validated = $validations->check_parameter($value, $property);

          if (!$validated['valid']) {
            throw new \Error("Property Validation Failed: {$validated['errors'][0]['message']}");
          }
        }

        if ($property == 'email_address') {
          try {
            $availability = UserManager::check_email_availability($value);

            if (!$availability) {
              throw new \Error("This Email Address is already in use.");
            }
          }
          catch (\Throwable $exception) {
            throw new \Error("An invalid Email Address was provided: {$exception->getMessage()}");
          }
        }
        else if ($property == 'username') {
          try {
            $availability = UserManager::check_username_availability($value);

            if (!$availability) {
              throw new \Error("This username is already in use.");
            }
          }
          catch (\Throwable $exception) {
            throw new \Error("An invalid Username was provided: {$exception->getMessage()}");
          }
        }

        if ($property !== 'password') {
          $this->$property = $value;
        }
        else {
          $this->password = \ShiftCodesTK\Auth\hash_password($value);
        }
      }
      foreach ($properties['user_optional'] as $property) {
        $value = $new_user[$property] ?? null;

        if (isset($value)) {
          $this->$property = $value;
        }
      }
      foreach ($properties['timestamps'] as $property) {
        $this->$property = $currentTimestamp;
      }

      return $this;
    }
    /**
     * Register the user with ShiftCodesTK
     * 
     * @return UserRecord|false Returns a `UserRecord` instance of the *Registered User* on success. Returns **false** if an error occurred.
     * @throws Error Throws an `Error` if an error ocurred while updating the database.
     */
    public function register_user () {
      $parameters = [
        'auth_users' => [
          'user_id'                  => $this->user_id,
          'account_state'            => $this->account_state,
          'email_address'            => $this->email_address,
          'username'                 => $this->username,
          'password'                 => $this->password,
          'user_roles'               => json_encode($this->user_roles, JSON_UNESCAPED_UNICODE),
          'redemption_id'            => $this->redemption_id
        ],
        'auth_user_records' => [
          'user_id'                  => $this->user_id,
          'creation_date'            => $this->creation_date,
          'last_auth'                => $this->last_auth,
          'last_login'               => $this->last_login,
          'last_activity'            => $this->last_activity,
          'last_public_activity'     => $this->last_public_activity,
          // 'last_failed_login'        => $this->last_failed_login ? json_encode($this->last_failed_login, JSON_UNESCAPED_UNICODE) : null,
          // 'last_username_change'     => json_encode($this->last_username_change, JSON_UNESCAPED_UNICODE),
          'shift_codes_submitted'    => $this->shift_codes_submitted,
          'profile_stats_preference' => $this->profile_stats_preference
        ]
      ];
      $queryOptions = [
        'collapse_all'           => true,
        'format_parameters'      => [
          'affected_rows' => [
            'change_type'         => 'bool'
          ]
        ]
      ];
      $transaction = \ShiftCodesTKDatabase::start_transaction();

      foreach ($parameters as $table => $params) {
        $tableFields = implode(', ', array_keys($params));
        $tableValues = preg_replace('/, $/', '', str_repeat('?, ', count($params)));
        $tableTypeStr = str_repeat('s', count($params));
        $tableQueryStr = "INSERT INTO {$table}
                          ({$tableFields})
                          VALUES ({$tableValues})";
        $tableParams = new \ShiftCodesTKDatabasePreparedVariables($tableTypeStr, $params);
        $tableQuery = new \ShiftCodesTKDatabaseQuery($tableQueryStr, $queryOptions, $tableParams);
        $tableResult = $tableQuery->query();

        if (!$tableResult) {
          \ShiftCodesTKDatabase::close_transaction($transaction, false);
          throw new \Error("The user was not successfully registered.");
        }
      }
      
      \ShiftCodesTKDatabase::close_transaction($transaction, true);

      return new UserRecord($this);
    }
  }
?>