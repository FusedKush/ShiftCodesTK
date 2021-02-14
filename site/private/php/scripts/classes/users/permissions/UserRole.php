<?php
  namespace ShiftCodesTK\Users\Permissions;

  /** Represents a *User Role* that can be granted to users to provide certain permissions. */
  class UserRole {
    /** @var array An `Associative Array` of *Role Categories* in the following format: 
     * > `Bitmask Value => Category Name`
     **/
    const ROLE_CATEGORIES = [
      1 => 'Auth',
      2 => 'General'
    ];

    /** @var array A compiled `Associatve Array` of *Role Property Constraints* for use with a `ValidationProperties` object in the following format:
     * > `Property Name => Constraints Array` 
     **/
    private static $ROLE_CONSTRAINTS = null;

    /** @var int An `int` representing the Role. */
    public $role_id = null;
    /** @var string The Display Name of the Role. */
    public $role_name = null;
    /** @var string The Icon that represents the Role, if applicable. */
    public $role_icon = null;
    /** @var string The Category that the Role belongs to. */
    public $role_category = null;
    /** @var string An `int` serialized as a `string`, representing the *Permissions* implicitly granted by the Role. */
    public $permissions = null;

    /** Initialize a new *User Role `Object`*.
     * 
     * @param array|null $role_data The *User Role Data* to be passed to the object. 
     * @return UserRole Returns the new `UserRole` on success.
     */
    public function __construct ($role_data = null) {
      // Compile Role Constraints
      if (!isset(self::$ROLE_CONSTRAINTS)) {
        self::$ROLE_CONSTRAINTS = [
          'role_id'   => \ShiftCodesTK\BITMASK_CONSTRAINTS,
          'role_name' => [
            'type'        => 'string',
            'required'    => true,
            'validations' => [
              'range'        => [
                'min'           => 1,
                'max'           => 32
              ]
            ]
          ],
          'role_icon' => [
            'type'       => 'string',
            'required'   => true,
            'validations' => [
              'range'        => [
                'min'           => 3,
                'max'           => 64
              ],
              'pattern'      => '%^(fas|far|fab) (fa|fas|far|fab|fal)\-[\w\-]+$%'
            ]
          ],
          'role_category' => [
            'type'       => 'integer',
            'required'   => true,
            'validations' => [
              'match'        => array_keys(self::ROLE_CATEGORIES)
            ]
          ],
          'permissions' => [
            'type'       => 'string',
            'required'   => true,
            'validations' => [
              'range'        => [
                'min'           => 3,
                'max'           => 128
              ],
              'pattern'      => '%^[\d]+$%'
            ]
          ],
        ];
      }

      if ($role_data) {
        $validations = (function () {
          $validations = [];

          foreach (self::$ROLE_CONSTRAINTS as $property => $constraints) {
            $validations[$property] = new \ValidationProperties($constraints);
          } 

          return $validations;
        })();
        $validatedParams = \check_parameters($role_data, $validations);

        if ($validatedParams['warnings']) {
          foreach ($validatedParams['warnings'] as $warning) {
            \trigger_error($warning['message'], \E_USER_WARNING);
          }
        }

        if ($validatedParams['valid']) {
          foreach (\get_class_vars(get_class($this)) as $property => $defaultValue) {
            $value = $validatedParams['parameters'][$property] ?? null;
  
            if (isset($value)) {
              $this->$property = $value;
            }
          }
        }
        else {
          throw new \Error($validatedParams['errors'][0]['message'], 1);
        }
      }
    }
  }
?>