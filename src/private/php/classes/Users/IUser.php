<?php
  namespace ShiftCodesTK\Users;
  use ShiftCodesTK\Strings;

  /** Represents the unique *User ID* of the User. */
  abstract class IUserID {
    /** @var string The unique *User ID* of the User. */
    public $user_id = null;

    /**
     * Requires that the `user_id` be set before proceeding.
     * 
     * @throws \Exception If the `user_id` has not been set, throws an `Exception`
     */
    protected function require_user_id () {
      if (!$this->user_id) {
        throw new \Exception("The User ID is required but has not been set yet.");
      }
    }
  }
  /** Represents the *Account State* or *Account Status* of the User. */
  abstract class IUserAccountState extends IUserID {
    /** @var string The User's Account is currently *Active* and fully available. */
    public const ACCOUNT_STATE_ACTIVE = 'active';
    /** @var string The User's Account is currently *Pending*. It may be restricted, or not available at all, until the User confirms their account. */
    public const ACCOUNT_STATE_PENDING = 'pending';
    /** @var string The User's Account is currently *Suspended*. They are not permitted to sign in to their account until their suspension is over. */
    public const ACCOUNT_STATE_SUSPENDED = 'suspended';
    /** @var string The User's Account has been *Blocked*. It cannot be accessed anymore. */
    public const ACCOUNT_STATE_BLOCKED = 'blocked';
    /** @var string The User's Account has been *Deleted*. It cannot be accessed anymore. */
    public const ACCOUNT_STATE_DELETED = 'deleted';

    /** @var ACCOUNT_STATE_ACTIVE|ACCOUNT_STATE_PENDING|ACCOUNT_STATE_SUSPENDED|ACCOUNT_STATE_BLOCKED|ACCOUNT_STATE_DELETED Represents the current *Account State* of the User. */
    public $account_state = null;
  }
  /** Represents the *User Roles* a User can possess. */
  abstract class IUserRoles extends IUserAccountState {
     /** 
     * @var array A list of *User Roles* and their associated details.
     * - Each role has an `array` made up of the following details:
     * | Key | Description |
     * | --- | --- |
     * | *name* | The display name of the role. |
     * | *label* | The label or description of the role. |
     * | *value* | An `integer` representing the value of the role. |
     */
    public const USER_ROLES = [
      'badass'    => [
        'name'       => 'Badass',
        'label'      => 'This user is a ShiftCodesTK Badass',
        'value'      => 1
      ],
      'admin'     => [
        'name'       => 'Admin',
        'label'      => 'This user is an Administrator of ShiftCodesTK',
        'value'      => 2
      ],    
      'developer' => [
        'name'       => 'Developer',
        'label'      => 'This user is a Developer of ShiftCodesTK',
        'value'      => 4
      ]
    ];
    /** @var int The User's Roles will be returned as an `integer`. */
    public const USER_ROLES_GET_INT = 1;
    /** @var int The User's Roles will be returned as an `array` made up of the *Role Names*. */
    public const USER_ROLES_GET_ARRAY = 2;
    /** @var int The User's Roles will be returned as an `associative array` made up of the *Role Data*. */
    public const USER_ROLES_GET_FULL_ARRAY = 4;

    /** @var int An `integer` representing the *roles* that the user possesses. 
     * - Use `check_role()`, `get_roles()`, and `has_role()` to test against the user's roles.
     * - For the full list of available roles, see `USER_ROLES` 
     **/
    public $user_roles = 0;

    /**
     * Retrieve the information about a *User Role*
     * 
     * @param string|int $role The role to retrieve. Can be a `string` representing the *Role Name*, or an `integer` representing the *Role Value*.
     * @return array|bool Returns an `array` made up of the `USER_ROLES` data for the *Role*, or **false** if the provided `$role` is invalid.
     */
    public static function get_role_data ($role) {
      if (is_string($role)) {
        $role_name = Strings\transform($role, Strings\TRANSFORM_LOWERCASE);
        $role_data = self::USER_ROLES[$role_name] ?? null;

        if ($role_data) {
          return $role_data;
        }
      }
      else if (is_int($role)) {
        foreach (self::USER_ROLES as $roleName => $roleData) {
          if ($role == $roleData['value']) {
            return $roleData;
          }
        }
      }

      return false;
    }
    /**
     * Checks if a value is a valid *User Role*
     * 
     * @param string|int $value The value to check. Can be a `string` representing the *Role Name*, or an `integer` representing the *Role Value*.
     * @param bool $throw_exception Indicates if an `UnexpectedValueException` should be thrown if `$value` is not a valid User Role.
     * @return bool Returns **true** if the value matches a *User Role*, or **false** if it does not.
     * @throws \UnexpectedValueException If `$throw_exception` is **true**, throws an `UnexepectedValueException` if `$value` is not a valid User Role.
     */
    public static function check_role ($value, $throw_exception = false) {
      if (self::get_role_data($value) !== false) {
        return true;
      }
      
      if ($throw_exception) {
        throw new \UnexpectedValueException("\"{$value}\" is not a valid User Role.");
      }

      return false;
    }

    /**
     * Retrieve a list of the User's Roles
     * 
     * @param USER_ROLES_GET_INT|USER_ROLES_GET_ARRAY|USER_ROLES_GET_FULL_ARRAY $format Indicates how the User's Roles will be returned:
     * 
     * | Format | Description |
     * | --- | --- |
     * | `USER_ROLES_GET_INT` | The User's Roles will be returned as an `integer`. |
     * | `USER_ROLES_GET_ARRAY` | The User's Roles will be returned as an `array` made up of the *Role Names*. |
     * | `USER_ROLES_GET_FULL_ARRAY` | The User's Roles will be returned as an `associative array` made up of the *Role Data*. |
     * @return array Returns an `indexed array` of the User's Roles or an `associative array` of all *User Roles* depending on the value of `$get_full_array`.
     */
    public function get_roles (int $format = self::USER_ROLES_GET_ARRAY) {
      $roles = $format == self::USER_ROLES_GET_INT
               ? 0
               : [];

      foreach (array_reverse(self::USER_ROLES, true) as $role => $roleInfo) {
        // $hasRole = $this->user_roles[$role] ?? false;
        $hasRole = ($this->user_roles & $roleInfo['value']) > 0;

        if ($hasRole) {
          if ($format == self::USER_ROLES_GET_INT) {
            $roles = $roles | $roleInfo['value'];
          }
          else if ($format == self::USER_ROLES_GET_ARRAY) {
            $roles[] = $role;
          }
          else if ($format == self::USER_ROLES_GET_FULL_ARRAY) {
            $roles[$role] = $roleInfo;
          }
          else {
            throw new \TypeError("\"{$format}\" is not a valid value for the Format.");
          }
        }
      }

      return $roles;
    }
    /**
     * Checks if the user has a given role
     * 
     * @param string|int $role The *Role Name* or *Role Integer Value* to check the user for.
     * @return boolean Returns **true** if the user has the given role or **false** if they do not. 
     * - This function will still return **false** if no roles have been defined for the user.
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$role` is not a valid *User Role*.
     */
    public function has_role ($role) {
      self::check_role($role, true);
      $userRoles = $this->get_roles(is_int($role) ? self::USER_ROLES_GET_INT : self::USER_ROLES_GET_ARRAY);

      if (is_string($role)) {
        $role = Strings\transform($role, Strings\TRANSFORM_LOWERCASE);
        
        return array_search($role, $userRoles) !== false;
      }
      else if (is_int($role)) {
        return $userRoles & $role > 0;
      }
    }
  }
  /** Represents the *User Permissions* a User can possess. */
  abstract class IUserPermissions extends IUserRoles {
    /**
     * @var array A list of *Permission Constraints* used to determine which permissions a user should have.
     * 
     * | Constraint | Description |
     * | --- | --- |
     * 
     * | `HAS_ROLE_DEVELOPER` | Requires the user to have the *Developer* Role. |
     * | `HAS_ROLE_ADMIN` | Requires the user to have the *Admin* Role. |
     * | `HAS_ROLE_BADASS` | Requires the user to have the *Badass* Role. |
     * | `HAS_ROLE_ADMIN_OR_BADASS` | Requires the user to have either the *Admin* or *Badass* Roles. |
     * | `NOT_RESTRICTED` | Requires the user's `account_state` to be **active**. |
     */
    private const PERMISSION_CONSTRAINTS = [
      'HAS_ROLE_DEVELOPER'       => 1,
      'HAS_ROLE_ADMIN'           => 2,
      'HAS_ROLE_BADASS'          => 4,
      'HAS_ROLE_ADMIN_OR_BADASS' => 8,
      'NOT_RESTRICTED'           => 16
    ];
    /** 
     * @var array A list of *Permissions* and their *Permission Data*.
     * 
     * | Key | Description |
     * | --- | --- |
     * | `name` | A `string` representing the *Display Name* of the Permission |
     * | `description` | A `string` representing the *Description* of the Permission |
     * | `value` | An `integer` representing the Permission |
     * | `constraints` | An `integer` representing the *Permission Constraints* | 
     **/
    private const PERMISSIONS = [
      'MANAGE_USERS'       => [
        'name'                => 'Manage Users',
        'description'         => 'Add, Update, & Remove Users.',
        'value'               => 1,
        'constraints'         => self::PERMISSION_CONSTRAINTS['HAS_ROLE_ADMIN']
      ],
      'MODERATE_USERS'     => [
        'name'                => 'Moderate Users',
        'description'         => 'Suspend and Block Users.',
        'value'               => 2,
        'constraints'         => self::PERMISSION_CONSTRAINTS['HAS_ROLE_ADMIN_OR_BADASS']
      ],
      'MODERATE_SHIFT_CODES' => [
        'name'                => 'Moderate SHiFT Codes',
        'description'         => 'Edit & Delete submitted SHiFT Codes.',
        'value'               => 4,
        'constraints'         => self::PERMISSION_CONSTRAINTS['HAS_ROLE_ADMIN_OR_BADASS']
      ],
      'SUBMIT_SHIFT_CODES' => [
        'name'                => 'Submit SHiFT Codes',
        'description'         => 'Submit new SHiFT Codes to ShiftCodesTK.',
        'value'               => 8,
        'constraints'         => self::PERMISSION_CONSTRAINTS['NOT_RESTRICTED']
      ],
      'DEVELOPER_TOOLS'    => [
        'name'                => 'Developer Tools',
        'description'         => 'Access to Developer Tools, Insights, & Features.',
        'value'               => 16,
        'constraints'         => self::PERMISSION_CONSTRAINTS['HAS_ROLE_DEVELOPER']
      ]
    ];

    /** @var int An integer representing the User's *Permissions*. */
    public $permissions = 0;

    /**
     * Checks all of the *Permissions* for the User. 
     * 
     * @return int Returns an updated `integer` representing the *User's Permissions*. 
     */
    public function check_permissions () {
      $permissions = 0;

      foreach (self::PERMISSIONS as $permission => $permissionData) {
        $constraints = $permissionData['constraints'];
        $permissionValue = $permissionData['value'];
        
        if ($constraints & self::PERMISSION_CONSTRAINTS['HAS_ROLE_DEVELOPER']) {
          if (!$this->has_role('developer')) {
            continue;
          }
        }
        if ($constraints & self::PERMISSION_CONSTRAINTS['HAS_ROLE_ADMIN']) {
          if (!$this->has_role('admin')) {
            continue;
          }
        }
        if ($constraints & self::PERMISSION_CONSTRAINTS['HAS_ROLE_BADASS']) {
          if (!$this->has_role('badass')) {
            continue;
          }
        }
        if ($constraints & self::PERMISSION_CONSTRAINTS['HAS_ROLE_ADMIN_OR_BADASS']) {
          if (!$this->has_role('ADMIN') && !$this->has_role('BADASS')) {
            continue;
          }
        }
        if ($constraints & self::PERMISSION_CONSTRAINTS['NOT_RESTRICTED']) {
          if ($this->account_state != self::ACCOUNT_STATE_ACTIVE) {
            continue;
          }
        }

        $permissions = $permissions|$permissionValue;
      }
      
      $this->permissions = $permissions;

      return $this->permissions;
    }
    /**
     * Retrieve a list of the User's *Granted Permissions*.
     * 
     * @param bool $get_full_array Indicates if the full `PermissionData` should be returned for all granted permissions.
     * @return array Returns a list of the User's *Granted Permissions* on success.
     */
    public function get_permissions ($get_full_array = false) {
      $permissions = [];

      $this->check_permissions();

      foreach (self::PERMISSIONS as $permission => $permissionData) {
        if ($this->permissions & $permissionData['value']) {
          if ($get_full_array) {
            $permissions[$permission] = $permissionData;
          }
          else {
            $permissions[] = $permission;
          }
        }
      }

      return $permissions;
    }
    /**
     * Check if the `User` has a given *Permission*
     * 
     * @param string $permission The name of the *Permission* to check.
     * 
     * | Permission | Description |
     * | --- | --- |
     * | `MANAGE_USERS` | Add, Update, & Remove Users. |
     * | `MODERATE_USERS` | Suspend & Block Users. |
     * | `MANAGE_SHIFT_CODES` | Edit & Delete submitted SHiFT Codes. |
     * | `SUBMIT_SHIFT_CODES` | Submit new SHiFT Codes to ShiftCodesTK. |
     * | `DEVELOPER_INSIGHTS` | Access to Developer Tools, Insights, & Features. |
     * @return bool Returns **true** if the `User` has the given `$permission`, or **false** if they do not.
     * @throws \TypeError Throws a `TypeError` if `$permission` is not a valid *Permission Name*.
     */
    public function has_permission ($permission) {
      $permission_name = strtoupper($permission);
      $permissions = $this->get_permissions();

      if (!isset(self::PERMISSIONS[$permission_name])) {
        throw new \TypeError("\"{$permission}\" is not the name of a valid Permission.");
      }

      return array_search($permission_name, $permissions) !== false;
    }
  }

  /** 
   * The `IUser` class is responsible for retrieving the *Basic User Data* for a `User`. 
   **/
  abstract class IUser extends IUserPermissions {
    /** @var string The *Email Address* of the User. */
    public $email_address = null;
    /** @var string The *Username* of the User. */
    public $username = null;
    /** @var null|string If available, the user's *Redemption ID*. */
    public $redemption_id = null;

    /**
     * Initializes a new `User`
     * 
     * @param string|object|array $user A `string` representing the *User ID* of the User whose data is to be retrieved, or an `object` or `array` representing the *User Data*.
     * @return User|UserRecord|UserManager|false Returns the new `User`, `UserRecord`, or `UserManager` object on success. If the User ID provided by `$user` does not much an existing user, or an error occurs, returns **false**.
     * @throws TypeError Throws a TypeError if `$user` is not a `string` or `array`.
     */
    public function get_user_data ($user) {
      $userRecordClass = 'ShiftCodesTK\Users\IUserRecord';
      $hasUserRecordClass = get_class($this) == $userRecordClass || array_search($userRecordClass, class_parents($this)) !== false;

      if ($user) {
        $queryParams = [
          "`au`.`user_id`",
          "`au`.`email_address`",
          "`au`.`account_state`",
          "`au`.`username`",
          "`au`.`user_roles`",
          "`au`.`redemption_id`"
        ];
        $getMissingData = function ($user_id) use (&$queryParams, $hasUserRecordClass) {
          $user_id = \ShiftCodesTKDatabase::escape_string($user_id);
          $queryStr = (function () use ($user_id, &$queryParams, $hasUserRecordClass) {
            $queryParamsStr = implode(', ', $queryParams);

            if ($hasUserRecordClass) {
              return "SELECT 
                        {$queryParamsStr}
                      FROM 
                        `auth_users` 
                          AS `au`
                      INNER JOIN
                        `auth_user_records` 
                          AS `aur` 
                          ON `au`.`user_id` = `aur`.`user_id`
                      WHERE 
                        `au`.`user_id` = '{$user_id}'
                      LIMIT 1";
            }
            else {
              return "SELECT 
                        {$queryParamsStr}
                      FROM
                        `auth_users` AS `au`
                      WHERE
                        `au`.`user_id` = '{$user_id}'
                      LIMIT 1";
            }
          })();
          $query = new \ShiftCodesTKDatabaseQuery($queryStr, [ 'collapse_query_result' => true, 'collapse_result_data' => true, 'collapse_result' => true ]);
          $query_result = $query->query();

          if (!$query_result) {
            trigger_error("User \"{$user_id}\" could not be found.");
            return false;
          }

          foreach ($query_result as $field => $value) {
            $this->$field = $value;
          }

          $this->check_permissions();
        };

        if ($hasUserRecordClass) {
          $queryParams = array_merge($queryParams, [
            "`aur`.`creation_date`",
            "`aur`.`last_auth`",
            "`aur`.`last_login`",
            "`aur`.`last_activity`",
            "`aur`.`last_public_activity`",
            "`aur`.`suspension_date`",
            "`aur`.`last_failed_login`",
            "`aur`.`last_username_change`",
            "`aur`.`shift_codes_submitted`",
            "`aur`.`profile_picture`",
            "`aur`.`profile_stats_preference`"
            ]);
        }

        if (is_string($user)) {
          $getMissingData($user);

          return $this;
        }
        else if (is_object($user) || is_array_associative($user)) {
          foreach (get_class_vars(get_class($this)) as $property => $defaultValue) {
            $value = is_object($user)
                     ? ($user->$property ?? null)
                     : ($user[$property] ?? null);

            if ($value !== null) {
              $constraintConstantName = Users\Constraints::class."::" . strtoupper($property);

              if (defined($constraintConstantName)) {
                $validations = new \ValidationProperties(constant($constraintConstantName));
                $validated = $validations->check_parameter($value, $property);

                if (!$validated['valid']) {
                  throw new \Error("Property Validation Failed: {$validated['errors'][0]['message']}");
                }
              }

              if (is_string($value)) {
                $value = Strings\encode_html($value);
                $value = \ShiftCodesTKDatabase::escape_string($value);
              }
              else if (is_array($value)) {
                array_walk_recursive($value, function (&$arrValue, $arrKey) {
                  if (is_string($arrValue)) {
                    $arrValue = Strings\encode_html($arrValue);
                    $arrValue = \ShiftCodesTKDatabase::escape_string($arrValue);
                  }
                });
              }

              $this->$property = $value;

              foreach ($queryParams as $index => &$field) {
                if (strpos($field, $property) !== false) {
                  array_splice($queryParams, $index, 1);
                  break;
                }
              }
            }
          }

          $getMissingData($this->user_id);
          return $this;
        }

        throw new \TypeError("The \"\$user\" must be a String representing the User ID, or an Object or Array representing the User Data.");
      }
    }
    /**
     * Retrieves a `UserRecord` for the current `User`.
     * 
     * @return object Returns a `UserRecord` instance representing the current `User`. If the current `object` is a or is a child of `UserRecord`, returns the current object. 
     * @throws \Exception Throws an `Exception` if the `user_id` has not been set yet. 
     */
    public function get_record () {
      $this->require_user_id();

      return new UserRecord($this);
    }
    /**
     * Retrieves a `UserManager` for the current `User`.
     * 
     * @param bool $check_permissions If **true**, the `CurrentUser` must have the appropriate permissions to use the `UserManager` class. 
     * @return UserRecord|false Returns a `UserManager` instance for the `User`. 
     * - If `$check_permissions` is **true** and the `CurrentUser` does not have the appropriate permissions, returns **false**.
     * - If the current `object` is a or is a child of `UserManager`, returns the current object.
     * @throws \Exception Throws an `Exception` if the `user_id` has not been set yet. 
     */
    public function get_manager () {
      $this->require_user_id();

      return new UserManager($this);
    }
    /** Determine if the user is the *Current User*.
     * 
     * @return bool Returns **true** if the `User` is the currently logged-in `CurrentUser`, or **false** if they are not.
     */
    public function is_current_user () {
      $currentUser = CurrentUser::get_current_user();

      if ($currentUser) {
        return $this->user_id === $currentUser->user_id;
      }

      return false;
    }
  };
?>