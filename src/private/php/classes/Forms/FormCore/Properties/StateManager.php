<?php
  namespace ShiftCodesTK\Forms\FormCore\Properties;
  use ShiftCodesTK\Strings;

  /** The `StateManager` is responsible for the `Disabled` and `Hidden` state of the element and its children. */
  trait StateManager {
    use IDManager;

    /** @var bool Indicates if the element is currently *Disabled*. */
    protected $disabled = false;
    /** @var bool Indicates if the element is currently *Hidden*. */
    protected $hidden = false;

    /** Set the `Disabled` or `Hidden` state of the Form or Form Child.
     * 
     * @param "disabled"|"hidden" $state_type The state being changed.
     * @param bool $new_state The new value of the state. 
     * @return $this Returns the object for further chaining.
     */
    public function set_state (string $state_type, bool $new_state) {
      if (array_search(Strings\transform($state_type, Strings\TRANSFORM_LOWERCASE), [ 'disabled', 'hidden' ]) !== false) {
        if ($this->$state_type !== $new_state) {
          $this->$state_type = $new_state;
        }
      }
      else {
        trigger_error("\"{$state_type}\" is not a valid Element State Type.");
      }

      return $this;
    }

    /** Initialize the `StateManager` */
    public function __construct () {
      parent::__construct();

      $this->add_attribute('disabled', '', '$disabled');
      $this->add_attribute('hidden', '', '$hidden');
    }
  }
?>