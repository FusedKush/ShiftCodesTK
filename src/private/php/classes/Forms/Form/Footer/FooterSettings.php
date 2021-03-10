<?php
  namespace ShiftCodesTK\Forms\Form\Footer;
  use ShiftCodesTK\Forms,
      ShiftCodesTK\Forms\Form;

  /** Represents the *Form Footer Settings*, controlling the behavior of the Form Footer. */
  abstract class FooterSettings extends Form\Properties {
    /** @var bool[] An `array` of `bool` values representing the primary settings of the Footer. */
    protected $footer_settings = [
      'enabled'       => true,
      'sticky'        => false,
      'show_progress' => false,
      'show_changes'  => false
    ];

    /** Initialize the `Footer` subclass */
    public function __construct () {
      parent::__construct();
    }

    /** Toggle the *Form Footer* on or off.
     * 
     * @param bool $footer_state The new state of the Form Footer. **True** enables the Footer while **false** disables it.
     * @return Forms\Form Returns the `Form` object for further chaining.
     */
    public function toggle_footer (bool $footer_state) {
      $currentValue = &$this->footer_settings['enabled'];

      if ($currentValue !== $footer_state) {
        $currentValue = $footer_state;
      }

      return $this;
    }
    /** Update the *Form Footer Settings*
     * 
     * @param array $footer_settings An `Associative Array` of Form Footer Settings to be updated. 
     * - All values must be `bool`'s and all values default to **false**.
     * - - `sticky`: Indicates if the Footer should stick to the bottom of the screen while scrolling through the Form.
     * - - `show_progress`: Indicates if the Footer should display a *Progress Bar* indicating the Form's Completion.
     * - - - For a field to be counted as progress, the field must have the canSubmit and willSubmit states as true.
     * - - - For a field to be counted towards the total, the field must meet one of the following requirements:
     * - - - The field must have the canSubmit state as true, or the field must be readonly or disabled.
     * - - - The field must not be hidden.
     * - - `show_changes`: Indicates if the *Number of Unsaved Changes* should be displayed by the Footer. 
     * - - - Requires that the submit button is enabled.
     * - - - For a field to be considered to have an unsaved change, it must be modified and not readonly, disabled, or hidden.
     * @return Forms\Form Returns the `Form` object for further chaining.
     */
    public function update_footer_settings (array $footer_settings) {
      foreach ($this->footer_settings as $setting => &$currentValue) {
        if ($setting === 'enabled') {
          continue;
        }

        if (array_key_exists($setting, $footer_settings)) {
          $newValue = $footer_settings[$setting];

          if (is_bool($newValue)) {
            if ($currentValue !== $newValue) {
              $currentValue = $newValue;
            }
          }
          else {
            trigger_error("The value provided for Setting \"{$setting}\" is not a bool.");
            continue;
          }
        }
      }

      return $this;
    }
  }
?>