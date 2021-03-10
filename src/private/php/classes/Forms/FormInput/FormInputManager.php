<?php
  namespace ShiftCodesTK\Forms\FormInput;
  use ShiftCodesTK\Forms,
      ShiftCodesTK\Validations,
      ShiftCodesTK\Strings;

  /** Represents the `InputProperties` shared by all Form Inputs. */
  abstract class InputProperties extends Forms\FormChild {
    /** @var string The *Type* of Form Field or Button. */
    protected $input_type = 'text';
    /** @var null|string The *Value*, *Default Value*, or *Required Value* of the Form Input. */
    protected $value = null;

    /** Initialize the `InputProperties` */
    public function __construct () {
      parent::__construct();

      $this->add_attribute('type', '$input_type');
    }

    /** Set the *Input Type* for the Form Field or Button
     * 
     * @param string $input_type The *Input Type* to set the Form Field or Button to.
     * 
     * | Category | Available Input Types |
     * | --- | --- |
     * | *Text* | `password`, `search`, `tel`, `text`, `textarea` |
     * | *Addresses* | `email`, `url` |
     * | *Numbers* | `number`, `range` |
     * | *Dates* | `date`, `datetime`, `datetimetz`, `month`, `time`, `week` |
     * | *Multiple-Choice* | `checkbox`, `radio`, `toggle-box`, `toggle-button`, `select`, `tz` |
     * | *Misc.* | `group`, `color`, `file` |
     * | *Buttons* | `button`, `reset`, `submit` | 
     * @return $this Returns the object for chaining.
     */
    public function set_input_type (string $input_type) {
      $validTypes = (function () {
        if (is_a($this, Forms\FormField::class)) {
          return Forms\FIELD_TYPES['fields'];
        }
        else if (is_a($this, Forms\FormButton::class)) {
          return Forms\FIELD_TYPES['buttons'];
        }

        return [];
      })();

      if (in_array($input_type, $validTypes)) {
        if ($this->input_type !== $input_type) {
          $this->input_type = $input_type;
        }
      }
      else {
        trigger_error("\"{$input_type}\" is not a valid Form Input Type for this element.");
      }

      return $this;
    }
    /** Change the *Value* of the Form Input.
     * 
     * @param string|null $value The new value of the Form Input. If omitted, the *Current Value* will be **removed**.
     * @return $this Returns the object for further chaining.
     */
    public function change_value (string $value = null) {
      if ($this->value !== $value) {
        $this->value = $value;
      }

      return $this;
    }
  }

  /** The `FormInputManager` is responsible for the surface properties and methods of the `FormInput` class. */
  abstract class FormInputManager extends InputProperties {
    /** Initialize the `FormInputManager` */
    public function __construct () {
      parent::__construct();
    }
  }
?>