<?php
  namespace ShiftCodesTK\Forms\Form;
  use ShiftCodesTK\Validations,
      ShiftCodesTK\Integers,
      ShiftCodesTK\Strings,
      ShiftCodesTK\Forms;

  /** Represents the *Form Action*, indicating where and how the *Form Submission* is to be sent. */
  abstract class Action extends Forms\FormCore {
    /** @var array `VariableEvaluator` *Validation Constriants* for the *Form Action Properties*. */
    const FORM_ACTION_CONSTRAINTS = [
      'path' => [
        'type'        => 'string',
        'required'    => true,
        'validations' => [
          'check_url'    => true
        ]
      ],
      'type'  => [
        'type'        => 'string',
        'required'    => true,
        'validations' => [
          'check_match'  => [
            self::ACTION_TYPE_STANDARD,
            self::ACTION_TYPE_AJAX,
            self::ACTION_TYPE_JS,
          ]
        ]
      ],
      'method'  => [
        'type'        => 'string',
        'required'    => true,
        'validations' => [
          'check_match'  => [
            self::ACTION_METHOD_POST,
            self::ACTION_METHOD_GET
          ]
        ]
      ]
    ];

    /** @var string Indicates that the Form is to be submitted synchronously. */
    const ACTION_TYPE_STANDARD = 'standard';
    /** @var string Indicates that the Form is to be submitted asychronously. */
    const ACTION_TYPE_AJAX = 'ajax';
    /** @var string Indicates that the Form is to be submitted using Javascript. */
    const ACTION_TYPE_JS = 'js';

    /** @var string Represents the `POST` Request Method. */
    const ACTION_METHOD_POST = 'POST';
    /** @var string Represents the `GET` Request Method. */
    const ACTION_METHOD_GET = 'GET';

    /** @var array Properties that control the *Form Submissions*.
     * 
     * | Property | Description |
     * | --- | --- |
     * | *path* | A *Relative* or *Absolute URL* representing the path to the desired destination of the *Form Submission*. |
     * | *type* | A `ACTION_TYPE_*` class constant indicating how the form is to be submitted. |
     * | *method* | A `ACTION_METHOD_*` class constant representing the *Request Method* used to submit the form. |
     */
    protected $action = [
      'path'            => '#',
      'type'            => self::ACTION_TYPE_AJAX,
      'method'          => self::ACTION_METHOD_POST
    ];

    /** Update the *Form Action Configuration*.
     * 
     * @param string|null $path A *Relative* or *Absolute URL* representing the path to the desired destination of the *Form Submission*.
     * @param string|null $type A `ACTION_TYPE_*` class constant indicating how the form is to be submitted.
     * @param string|null $method A `ACTION_METHOD_*` class constant representing the *Request Method* used to submit the form.
     * @return $this Returns the object for further chaining. 
     */
    public function update_action (string $path = null, string $type = null, string $method = null) {
      foreach ($this->action as $property => $currentValue) {
        
        if (isset($$property)) {
          $validator = new Validations\VariableEvaluator(self::FORM_ACTION_CONSTRAINTS[$property]);
          
          if ($validator->check_variable($$property)) {
            if ($this->action[$property] !== $$property) {
              $this->action[$property] = $$property;
            }
          }
          else {
            trigger_error($validator->get_last_result('errors')[0]['message'], E_USER_WARNING);
            continue; 
          }
        }
      }

      return $this;
    }
  }
  /** Represents the *Form Preferences* that can be used to customize the appearance and behavior of the Form. */
  abstract class Preferences extends Action {
    /** @var array `VariableEvaluator` *Validation Constriants* for each of the *Form Preferences*. */
    const FORM_PREF_CONSTRAINTS = [
      'submit_on_change'        => [
        'type'                     => 'bool',
        'required'                 => true
      ],
      'confirm_unsaved_changes' => [
        'type'                     => 'bool',
        'required'                 => true
      ],
      'show_progress'           => [
        'type'                     => 'bool',
        'required'                 => true
      ],
      'show_alerts'             => [
        'type'                     => 'bool',
        'required'                 => true
      ],
      'show_background'         => [
        'type'                     => 'bool',
        'required'                 => true
      ],
      'spacing'                 => [
        'type'                     => 'string',
        'required'                 => true,
        'validations'              => [
          'check_match'               => [
            self::FORM_PREFS_SPACING_STANDARD,
            self::FORM_PREFS_SPACING_VERTICAL,
            self::FORM_PREFS_SPACING_DOUBLE,
            self::FORM_PREFS_SPACING_NONE
          ]
        ]
      ]
    ];

    /** @var string Indicates that the Form should use *Standard Spacing*. The default amount of *Form Spacing* will be used. */
    const FORM_PREFS_SPACING_STANDARD = 'standard';
    /** @var string Indicates that the Form should use *Vertical Spacing*. Only the *Top* and *Bottom* of Form Elements will receive *Form Spacing*. */
    const FORM_PREFS_SPACING_VERTICAL = 'vertical';
    /** @var string Indicates that the Form should use *Double Spacing*. Twice as much *Form Spacing* will be used. */
    const FORM_PREFS_SPACING_DOUBLE = 'double';
    /** @var string Indicates that the Form should use *No Spacing*. No Form Elements will receive any *Form Spacing*. */
    const FORM_PREFS_SPACING_NONE = 'none';

    /** @var array Represents the *Form Preferences* controlling the appearance and behavior of the form. 
     * - See `update_form_preferences()` for more information.
     * - **Available Properties**:
     * - - `submit_on_change`
     * - - `confirm_unsaved_changes`
     * - - `show_progress`
     * - - `show_alerts`
     * - - `show_background`
     * - - `spacing`
     **/
    protected $form_preferences = [
      'submit_on_change'        => false,
      'confirm_unsaved_changes' => false,
      'show_progress'           => true,
      'show_alerts'             => true,
      'show_background'         => false,
      'spacing'                 => self::FORM_PREFS_SPACING_VERTICAL
    ];

    /** Update the *Form Preferences* of the Form
     * 
     * @param array $preferences An `array` of *Form Preferences* to be configured.
     * 
     * | Preference | Type |Description | Default Value |
     * | --- | --- | --- | --- |
     * | *submit_on_change* | `bool` | Automatically submits the form when any change is commited to a field in the form. | `false` |
     * | *confirm_unsaved_changes* | `bool` | Displays a *Browser Dialog* confirming the user's action if they attempt to navigate away from the page with unsaved changes made to the form. | `false` |
     * | *show_progress* | `bool` | The *Form Submission Process* will be displayed using the *Navbar Progress Bar*. | `true` |
     * | *show_alerts* | `bool` | *Form Alerts* will be displayed to the user inside of the form. | `true` |
     * | *show_background* | `bool` | Renders a Background for the Form and its Form Fields. | `false` |
     * | *spacing* | `string` | A `FORM_PREFS_SPACING_*` class constant representing the *Form Spacing* to use. | `FORM_PREFS_SPACING_VERTICAL` |
     * @return $this Returns the object for further chaining.
     */
    public function update_form_preferences (array $preferences) {
      foreach ($this->form_preferences as $preference => &$currentValue) {
        $newValue = $preferences[$preference] ?? null;

        if (isset($newValue)) {
          $validator = new Validations\VariableEvaluator(self::FORM_PREF_CONSTRAINTS[$preference]);
  
          if ($validator->check_variable($newValue, $preference)) {
            if ($currentValue !== $newValue) {
              $currentValue = $newValue;
            }
          }
          else {
            trigger_error($validator->get_last_result('errors')[0]['message'], E_USER_WARNING);
            continue;
          }
        }
      }

      return $this;
    }
    
    /** Initialize the `Preferences` subclass */
    public function __construct () {
      parent::__construct();
    }
  }

  /** Represents the *Form Properties* that control the behavior of the form. */
  abstract class Properties extends Preferences {
    /** Initialize the `Properties` subclass */
    public function __construct () {
      parent::__construct();
    }
  }
?>