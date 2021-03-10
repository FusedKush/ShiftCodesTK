<?php
  namespace ShiftCodesTK\Forms\FormChild;
  use ShiftCodesTK\Forms,
      ShiftCodesTK\Validations,
      ShiftCodesTK\Strings;

  /** The `SizeManager` is responsible for the sizing of the Form Element within its parent container. */
  abstract class SizeManager extends Forms\FormCore {
    /** @var string Indicates that the Form Element should take up the *Full Width* of the container. */
    const SIZE_FULL = 'full';
    /** @var string Indicates that the Form Element should take up the *Half of the Width* of the container. */
    const SIZE_HALF = 'half';
    /** @var string Indicates that the Form Element should take up *A Third of the Width* of the container. */
    const SIZE_THIRD = 'third';
    /** @var string Indicates that the Form Element should take up *Two-Thirds of the Width* of the container. */
    const SIZE_TWO_THIRDS = 'two-thirds';

    /** @var string Indicates how much of the container's width the Form Element should occupy. */
    protected $size = self::SIZE_FULL;

    /** Initialize the `SizeManager` */
    public function __construct () {
      parent::__construct();

      $this->add_class('size', function () { return $this->size !== self::SIZE_FULL; });
      $this->add_class('$size', function () { return $this->size !== self::SIZE_FULL; });
    }
    /** Set the size of the Form or Form Child
     * 
     * @param string $size A `SIZE_*` class constant representing the new size of the element.
     * @return $this Returns the object for further chaining.
     */
    public function set_size (string $size) {
      $sizes = [
        self::SIZE_FULL,
        self::SIZE_HALF,
        self::SIZE_THIRD,
        self::SIZE_TWO_THIRDS,
      ];

      if (array_search($size, $sizes) !== false) {
        if ($this->size !== $size) {
          $this->size = $size;
        }
      }
      else {
        trigger_error("\"{$size}\" is not a valid Form Element Size.");
      }

      return $this;
    }
  }
  /** Represents the `InputProperties` shared by all Form Children. */
  abstract class InputProperties extends SizeManager {
    /** @var Validations\VariableEvaluator The `VariableEvaluator` used to validate the Form Input Value. */
    protected $evaluator = null;

    /** Initialize the `InputProperties` */
    public function __construct () {
      parent::__construct();

      $this->evaluator = new Validations\VariableEvaluator();
    }
  }

  /** The `FormChildManager` is responsible for the surface properties and methods of the `FormChild`. */
  abstract class FormChildManager extends InputProperties {
    /** Initialize the `FormChildManager` */
    public function __construct () {
      parent::__construct();
    }

    public function check_dynamic_validations ($get_values = false) {
      $validations = [];

      $checkValidation = function ($validationName, $validationConstraint) use (&$checkValidation, &$dynamicValidations, $get_values) {
        if ($matches = Strings\preg_match($validationConstraint, '/^(?:\$\{([\w\d_]+)\}){1}(?:\|([^\s\r\n]+)){0,1}$/', Strings\PREG_RETURN_SUB_MATCHES)) {
          $validations[$validationName] = [
            'field'   => $matches[0],
            'default' => $matches[1] ?? false
          ];

          if ($get_values) {
            $form = $this->get_form();

            if ($form) {
              $field = $form->get_child($matches[0]);

              if ($field) {
                $value = $field->value ?? null;

                if (!isset($value)) {
                  $value = '';
                }
              }
              
              $validations[$validationName]['value'] = $value;
            }
          }

          return true;
        }

        return false;
      };

      foreach ($this->evaluator->validations as $check => $constraints) {
        $checkValidation($check, $constraints);
      }

      return $validations;
    }
  }
?>