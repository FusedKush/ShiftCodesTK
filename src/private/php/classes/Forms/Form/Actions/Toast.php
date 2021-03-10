<?php
  namespace ShiftCodesTK\Forms\Form\Actions;

  /** Represents a *Toast* that is displayed when the form is successfully submitted. */
  abstract class Toast extends FormActions {
    /** @var string Indicates that the Toast is to be displayed immediately, once the *Result Data* has been returned to the client. */
    const TOAST_METHOD_RESPONSE = 'response';
    /** @var string Indicates that the Toast is to be displayed on the next page load. */
    const TOAST_METHOD_REFRESH = 'refresh';

    /** Initialize the `Toast` subclass. */
    public function __construct() {
      parent::__construct();

      $this->form_actions['toast'] = [
        'enabled'        => false,
        'method'         => self::TOAST_METHOD_RESPONSE,
        'properties'     => []
      ];
    }

    /** Toggle the *Method* used for displaying the Toast.
     * 
     * @return $this Returns the object for further chaining.
     */
    public function toggle_toast_method () {
      $methods = [
        self::TOAST_METHOD_RESPONSE,
        self::TOAST_METHOD_REFRESH
      ];

      foreach ($methods as $method) {
        $currentValue = &$this->form_actions['toast']['method'];

        if ($method !== $currentValue) {
          $currentValue = $method;
          break;
        }
      }

      return $this;
    }
    /** Update the *Toast Properties* that define the Toast
     * 
     * @param array An `array` representing the *Toast Properties* to be updated.
     * - Primary properties include `settings`, `content`, & `actions`.
     * @return $this Returns the object for further chaining.
     */
    public function update_toast_properties (array $toast_properties) {
      $primaryProperties = [
        'settings',
        'content',
        'actions'
      ];
      $currentProperties = &$this->form_actions['toast'];

      foreach ($primaryProperties as $property) {
        if (array_key_exists($property, $toast_properties)) {
          if (!array_key_exists($property, $currentProperties['properties'])) {
            $this->toast['properties'][$property] = [];
          }

          $currentValue = &$currentProperties['properties'][$property];
          $newValue = $toast_properties[$property];
          
          if (is_array($newValue)) {
            if (isset($currentValue)) {
              $currentValue = array_replace_recursive($currentValue, $newValue);
            }
            else {
              $currentValue = $newValue;
            }
          }
          else {
            trigger_error("The provided value for \"{$property}\" is not an Array.");
            continue;
          }
        }
      }

      return $this;
    }
  }
?>