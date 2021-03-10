<?php
  namespace ShiftCodesTK\Forms\Form\Actions;

  /** Represents the *Active State* of the Form once it has been successfully submitted. */
  abstract class FormState extends Modal {
    /** @var string Indicates that the Form should be *Re-Enabled* once it has been successfully submitted. */
    const FORM_STATE_ENABLED = 'enabled';
    /** @var string Indicates that the Form should remain *Disabled* once it has been successfully submitted. */
    const FORM_STATE_DISABLED = 'disabled';
    /** @var string Indicates that the Form should remain *Disabled* once it has been successfully submitted, but can be *Reset* to Re-Enable it. */
    const FORM_STATE_RESET = 'reset';

    /** Change the *Form State* of the Form.
     * 
     * @param string $form_state A `FORM_STATE_*` class constant value representing the new *Form State* of the Form.
     * 
     * | Form State | Description |
     * | --- | --- |
     * | `FORM_STATE_ENABLED` | The Form should be *Re-Enabled* once it has been successfully submitted. |
     * | `FORM_STATE_DISABLED` | The Form should remain *Disabled* once it has been successfully submitted. |
     * | `FORM_STATE_RESET` | The Form should remain *Disabled* once it has been successfully submitted, but can be *Reset* to Re-Enable it. |
     * @return $this Returns the object for further chaining.
     */
    public function change_form_state (string $form_state) {
      $validStates = [
        self::FORM_STATE_ENABLED,
        self::FORM_STATE_DISABLED,
        self::FORM_STATE_RESET
      ];

      if (in_array($form_state, $validStates)) {
        $currentValue = &$this->form_actions['form_state'];

        if ($currentValue !== $form_state) {
          $currentValue = $form_state;
        }
      }
      else {
        trigger_error("\"{$form_state}\" is not a valid Form State.");
      }

      return $this;
    }

    /** Initialize the `FormState` subclass. */
    public function __construct() {
      parent::__construct();

      $this->form_actions['form_state'] = self::FORM_STATE_DISABLED;
    }
  }
?>