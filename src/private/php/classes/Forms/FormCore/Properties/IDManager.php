<?php
  namespace ShiftCodesTK\Forms\FormCore\Properties;
  use ShiftCodesTK\Validations,
      ShiftCodesTK\Strings,
      ShiftCodesTK\Forms\FormCore;

  /** @var array Validation Constraints for the `IDManager` properties. */
  const ID_MANAGER_CONSTRAINTS = [
    'id' => [
      'type'     => 'string',
      'required' => true
    ],
    'name' => [
      'type'     => 'string',
      'required' => true,
      'validations' => [
        'check_pattern' => '/^[\d\w _-]+$/',
        'check_range'   => [
          'min'           => 1,
          'max'           => 128
        ]
      ]
    ]
  ];

  /** The `IDManager` is responsible for the *Unique ID & Name* of the Form or Form Child. */
  trait IDManager {
    use HTMLManager,
        FormCore\RelationshipManager;
  
    /** @var string The *Unique Identifier* of the Form or Form Child. Generated from the `$name` of the element and its parents. */
    protected $id = null;
    /** @var string The *Unique Name* of the Form or Form Child. 
     * - Should be unique among the Form and its children. 
     * - Can only contain alphanumeric characters, spaces, and underscores (`_`).
     * - Must be between **1** and **128** characters.
    */
    protected $name = null;
  
    /** Change the *Unique Name* of the Form or Form Child.
     * 
     * @param string $name The new *Unique Name* of the element.
     * - Should be unique among the Form and its children. 
     * - Can only contain alphanumeric characters, spaces, and underscores (`_`).
     * - Must be between **1** and **128** characters.
     * @return $this Returns the object for further chaining.
     */
    public function set_name (string $name) {
      $evaluator = new Validations\VariableEvaluator(ID_MANAGER_CONSTRAINTS['name']);
      $isValidName = $evaluator->check_variable($name, 'name');

      if ($isValidName) {
        $prefix = (function () {
          $parent = $this->get_parent();

          if ($parent) {
            return "{$parent->id}_";
          }

          return '';
        })();

        $this->name = $name;
        $this->id = $prefix . Strings\encode_id($name, Strings\ENCODE_ID_SNAKE_CASE);
      }
      else {
        trigger_error($evaluator->get_last_error(), E_USER_WARNING);
      }

      return $this;
    }

    /** Initialize the `PropertiesIDManager` */
    public function __construct () {
      $this->add_attribute('id', '$id');
      $this->add_attribute('name', '$name', function () {
        return in_array(FormInput::class, class_uses($this));
      });
    }
  }
?>