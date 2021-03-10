<?php
  namespace ShiftCodesTK\Forms\Form\Actions;
  use ShiftCodesTK\Forms\Form;

  /** Represents the behavior of the form on a successful *Form Submission*. */
  abstract class FormActions extends Form\Footer {
    /** @var array Represents the behavior of the form on a successful *Form Submission*. */
    protected $form_actions = [];

    /** Initialize a new `FormActions` object. */
    public function __construct() {
      parent::__construct();
    }

    /** Toggle the *Active State* of a Form Action
     * 
     * @param string $form_action The name of the Form Action to toggle. Valid actions include:
     * - `toast`
     * - `redirect`
     * - `modal`
     * @param bool $action_state The new *Active State* of the Form Action. `true` indicates that the action is to be *Enabled*, while `false` indicates that the action is to be *Disabled*.
     * @return $this Returns the object for further chaining.
     */
    public function toggle_form_action (string $form_action, bool $action_state) {
      if (array_key_exists($form_action, $this->form_actions)) {
        $currentValue = &$this->form_actions[$form_action]['enabled'];

        if ($action_state !== $currentValue) {
          $currentValue = $action_state;
        }
      }
      else {
        trigger_error("\"{$form_action}\" is not a valid Form Action name.");
      }

      return $this;
    }
  }
?>