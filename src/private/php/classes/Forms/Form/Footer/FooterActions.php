<?php
  namespace ShiftCodesTK\Forms\Form\Footer;
  use ShiftCodesTK\Forms\FormCore\Properties,
      ShiftCodesTK\Validations;

  /** Represents an *Action Button* from the *Confirmation Modal*. */
  class FooterConfirmationAction {
    /** @var string Indicates that the Confirmation Action should utilize the *Theme Color Scheme*. */
    const COLOR_THEME = 'theme';
    /** @var string Indicates that the Confirmation Action should utilize the *Light Color Scheme*. */
    const COLOR_LIGHT = 'light';
    /** @var string Indicates that the Confirmation Action should utilize the *Dark Color Scheme*. */
    const COLOR_DARK = 'dark';
    /** @var string Indicates that the Confirmation Action should utilize the *Info Color Scheme*. */
    const COLOR_INFO = 'info';
    /** @var string Indicates that the Confirmation Action should utilize the *Success Color Scheme*. */
    const COLOR_SUCCESS = 'success';
    /** @var string Indicates that the Confirmation Action should utilize the *Warning Color Scheme*. */
    const COLOR_WARNING = 'warning';
    /** @var string Indicates that the Confirmation Action should utilize the *Danger Color Scheme*. */
    const COLOR_DANGER = 'danger';

    /** @var array `VariableEvaluator` *Validation Constriants* for each of the *Confirmation Action Properties*. */
    const CONFIRMATION_ACTION_CONSTRAINTS = [
      'content' => [
        'type'        => 'null|string',
        'required'    => true
      ],
      'tooltip' => [
        'type'        => 'null|string',
        'required'    => true
      ],
      'color'   => [
        'type'        => 'null|string',
        'required'    => true,
        'validations' => [
          'check_match'  => [
            self::COLOR_THEME,
            self::COLOR_LIGHT,
            self::COLOR_DARK,
            self::COLOR_INFO,
            self::COLOR_SUCCESS,
            self::COLOR_WARNING,
            self::COLOR_DANGER
          ]
        ]
      ]
    ];

    /** @var null|string Represents the *Content* of the Confirmation Action. */
    protected $content = null;
    /** @var null|string Represents the *Tooltip Content* of a Tooltip. */
    protected $tooltip = null;
    /** @var null|string Represents the *Button Color* of the Confirmation Action. */
    protected $color = null;

    /** Update the *Confirmation Action Properties*.
     * 
     * @param array $action_properties An `Associative Array` representing the Confirmation Action Properties to update:
     * 
     * | Property | Description |
     * | --- | --- |
     * | *content* | The *Content* of the Confirmation Action. |
     * | *tooltip* | The *Tooltip Content* of the Tooltip, or **null** if the Confirmation Action shouldn't have a Tooltip. |
     * | *color* | A `COLOR_*` class constant value representing a *Button Color* to be applied to the Confirmation Action, or **null** if the Confirmation Action shouldn't have a color. |
     * @return FooterConfirmationAction Returns the `FooterConfirmationAction` on success. 
     */
    public function update_action(array $action_properties) {
      foreach (self::CONFIRMATION_ACTION_CONSTRAINTS as $propertyName => $propertyConstraints) {
        if (array_key_exists($propertyName, $action_properties)) {
          $newValue = $action_properties[$propertyName];
          $validator = new Validations\VariableEvaluator($propertyConstraints);

          if ($validator->check_variable($newValue, $propertyName)) {
            $currentValue = &$this->$propertyName;

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
  }
  /** Represents a *Footer Action Button*. */
  class FooterAction {
    use Properties\HTMLManager;

    /** @var array `VariableEvaluator` *Validation Constriants* for the *Footer Action Confirmation Properties*. */
    const CONFIRMATION_CONSTRAINTS = [
      'title' => [
        'type'        => 'null|string',
        'required'    => true
      ],
      'body' => [
        'type'        => 'null|string',
        'required'    => true
      ],
      'require_response_data' => [
        'type'        => 'bool',
        'required'    => true
      ]
    ];

    /** @var bool Indicates if the Footer Action is currently *Enabled* (`true`) or *Disabled* (`false`). */
    public $enabled = true;
    /** @var string|null The *Content* or *Body* of the Footer Action. */
    public $content = null;
    /** @var string|null An alternative *Title* or *Label* for the Footer Action. */
    public $title = null;
    /** @var \ShiftCodesTK\TooltipLayer|null A *Tooltip* used to describe the Footer Action. */
    public $tooltip = null;
    /** @var array An `Associative Array` of properties related to the *Footer Action Confirmation Modal*. 
     * - See `update_confirmation_properties()` for more information.
     * - **Available Properties**:
     * - - `enabled`
     * - - `title` 
     * - - `body`
     * - - `require_response_data`
     * - - `actions`
    */
    public $confirmation = [
      'enabled'               => false,
      'title'                 => null,
      'body'                  => null,
      'require_response_data' => false,
      'actions'               => [
        'deny'                   => null,
        'approve'                => null
      ]
    ];
    /** @var bool Indicates if the Form must be *modified* for the action to be available. Has no effect on the `FOOTER_ACTION_DETAILS_TOGGLE`. */
    public $requires_modify = false;

    /** Initialize a new `FooterAction` */
    public function __construct (array $properties = []) {
      $this->confirmation['actions']['deny'] = new FooterConfirmationAction();
      $this->confirmation['actions']['approve'] = new FooterConfirmationAction();

      if (isset($properties['enabled'])) {
        $this->toggle_action($properties['enabled']);
      }
      if (isset($properties['content']) || isset($properties['title'])) {
        $this->update_content($properties['content'] ?? null, $properties['title'] ?? null);
      }
      if (isset($properties['tooltip'])) {
        $this->toogle_tooltip(true, $properties['tooltip']);
      }
    }

    /** Toggle the state of the Footer Action.
     * 
     * @param bool $action_state Indicates if the Footer Action is to be *Enabled* (`true`) or *Disabled* (`false`).
     * @return FooterAction Returns the `FooterAction` object for further chaining.
     */
    public function toggle_action (bool $action_state) {
      if ($this->enabled !== $action_state) {
        $this->enabled = $action_state;
      }

      return $this;
    }
    /** Update the *Button Content* of the Footer Action
     * 
     * Providing **false** for the `$content` or `$title` will clear its *Current Value*.
     * 
     * @param string|false|null $content The *Content* or *Body* of the Footer Action.
     * @param string|false|null $title An alternative *Title* or *Label* for the Footer Action.
     * @return FooterAction Returns the `FooterAction` object for further chaining.
     */
    public function update_content ($content = null, $title = null) {
      foreach ([ 'content', 'title' ] as $property) {
        if (isset($$property)) {
          if ($$property === false) {
            $$property = null;
          }
          else if (!is_string($$property)) {
            trigger_error("Property \"{$property}\" is not a String or FALSE.");
            continue;
          }
  
          if ($this->$property !== $$property) {
            $this->$property = $$property;
          }
        }
      }

      return $this;
    }
    /** Toggle the *Active State* of the Footer Action Tooltip.
     * 
     * @param bool $tooltip_state Indicates if the Tooltip is *Enabled* (`true`) or *Disabled* (`false`).
     * @param array $tooltip_configuration If `$tooltip_state` is **true**, you can provide an `array` to be passed to the `TooltipLayer::__construct()`.
     * @return FooterAction Returns the `FooterAction` object for further chaining.
     */
    public function toogle_tooltip (bool $tooltip_state, array $tooltip_configuration = null) {
      if (isset($this->tooltip) !== $tooltip_state) {
        if ($tooltip_state) {
          $this->tooltip = new \ShiftCodesTK\TooltipLayer($tooltip_configuration);
        }
        else {
          $this->tooltip = null;
        }
      }

      return $this;
    }
    /** Retrieve the *Form Footer Tooltip* to update
     * 
     * @return \ShiftCodesTK\TooltipLayer|null Returns the `TooltipLayer` object representing the *Footer Action Tooltip*, or **null** if no tooltip has been set. 
     */
    public function &update_tooltip () {
      return $this->tooltip;
    }
    /** Toggle the *Footer Action Confirmation* on or off.
     * 
     * @param bool $confirmation_state Indicates if the Footer Action Confirmation should be *Enabled* (`true`) or *Disabled* (`false`).
     * @return FooterAction Returns the `FooterAction` object for further chaining. 
     */
    public function toggle_confirmation (bool $confirmation_state) {
      if ($this->confirmation['enabled'] !== $confirmation_state) {
        $this->confirmation['enabled'] = $confirmation_state;
      }

      return $this;
    }
    /** Update one or more of the *Footer Action Confirmation Properties*.
     * 
     * @param array $confirmation_properties An `Associative Array` representing the *Confirmation Properties* to be updated:
     * 
     * * | Property | Type | Description | Default Value |
     * | --- | --- | --- | --- |
     * | *title* | `string\|null` | The *Modal Title* of the Confirmation Modal. | `null` |
     * | *body* | `string\|null` | The *Modal Content* of the Confirmation Modal. | `null` | 
     * | *require_response_data* | `bool` | Requires the Form to provide the *Confirmation Result* from the Confirmation Modal to successfully validate. | `false` |
     * @return FooterAction Returns the `FooterAction` object for further chaining.
     */
    public function update_confirmation_properties (array $confirmation_properties) {
      foreach ($this->confirmation as $propertyName => &$currentValue) {
        if (!array_key_exists($propertyName, self::CONFIRMATION_CONSTRAINTS) || !isset($confirmation_properties[$propertyName])) {
          continue;
        }

        $newValue = $confirmation_properties[$propertyName];
        $validator = new Validations\VariableEvaluator(self::CONFIRMATION_CONSTRAINTS[$propertyName]);

        if ($validator->check_variable($newValue, $propertyName)) {
          if ($currentValue !== $newValue) {
            $currentValue = $newValue;
          }
        }
        else {
          trigger_error($validator->get_last_result('errors')[0]['message'], E_USER_WARNING);
          continue;
        }
      }

      return $this;
    }
    /** Retrieve a *Confirmation Action* to update.
     * 
     * @param string The *Confirmation Action* to be updated. Can be `deny` or `approve`.
     * @return FooterConfirmationAction Returns the `FooterActionConfirmation` object representing the *Confirmation Action*.
     * @throws \UnexpectedValueException if an invalid `$confirmation_action` is provided. 
     */
    public function &update_confirmation_action (string $confirmation_action) {
      $validActions = [ 'deny', 'approve' ];

      if (in_array($confirmation_action, $validActions)) {
        return $this->confirmation['actions'][$confirmation_action];
      }
      else {
        throw new \UnexpectedValueException("\"{$confirmation_action}\" is not a valid Confirmation Action Name.");
      }
    }
  }

  /** Represents the *Actions* of the Form Footer. */
  abstract class FooterActions extends FooterSettings {
    /** @var string Represents the *Reset* Button of the Form Footer. */
    const FOOTER_ACTION_RESET = 'reset';
    /** @var string Represents the *Details Toggle* Button of the Form Footer. */
    const FOOTER_ACTION_DETAILS_TOGGLE = 'details_toggle';
    /** @var string Represents the *Submit* Button of the Form Footer. */
    const FOOTER_ACTION_SUBMIT = 'submit';

    /** @var FooterAction[] An `array` of `FooterAction` objects representing the actions of the Footer. */
    protected $footer_actions = [
      self::FOOTER_ACTION_RESET          => null,
      self::FOOTER_ACTION_DETAILS_TOGGLE => null,
      self::FOOTER_ACTION_SUBMIT         => null
    ];

    /** Initialize the `Footer` subclass */
    public function __construct () {
      $this->footer_actions[self::FOOTER_ACTION_RESET] = new FooterAction([
        'enabled' => true,
        'content' => 'Reset'
      ]);
      $this->footer_actions[self::FOOTER_ACTION_DETAILS_TOGGLE] = new FooterAction([
        'enabled' => false,
        'content' => 'Toggle Details'
      ]);
      $this->footer_actions[self::FOOTER_ACTION_SUBMIT] = new FooterAction([
        'enabled' => true,
        'content' => 'Submit'
      ]);

      (function () {
        $actions = (function () {
          $actions = [];

          foreach ($this->footer_actions as $actionName => $actionObj) {
            if ($actionName != self::FOOTER_ACTION_DETAILS_TOGGLE && $actionObj->requires_modify) {
              $actions[] = $actionName;
            }
          }

          return implode(", ", $actions);
        })();

        $this->add_attribute('data-require-modify', $actions, function () { return !empty($actions); });
      })();
      (function () {
        $actions = (function () {
          $actions = [];

          foreach ($this->footer_actions as $actionName => $actionObj) {
            if ($actionName != self::FOOTER_ACTION_DETAILS_TOGGLE && $actionObj->confirmation['enabled']) {
              $actions[] = $actionName;
            }
          }

          return implode(", ", $actions);
        })();

        $this->add_attribute('data-require-confirmation', $actions, function () { return !empty($actions); });
      })();
    }

    /** Retrieve a *Form Footer Action* to update
     * 
     * @param string $footer_action A `FOOTER_ACTION_*` class constant value indicating which Form Footer Action to be update.
     * @return FooterAction Returns the `FooterAction` to be updated on success.
     * @throws UnexpectedValueException if `$footer_action` is not a valid Form Footer Action.
     */
    public function &update_footer_action (string $footer_action) {
      if (array_key_exists($footer_action, $this->footer_actions)) {
        return $this->footer_actions[$footer_action];
      }
      else {
        throw new \UnexpectedValueException("\"{$footer_action}\" is not a valid Form Footer Action.");
      }
    }
  }
?>