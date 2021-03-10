<?php
  namespace ShiftCodesTK;

  /** Represents a *Tooltip Configuration* for use with the `layers` JS Module. */
  class TooltipLayer {
    /** @var array `VariableEvaluator` *Validation Constriants* for each of the *Layer Position Properties*. */
    const POSITION_CONSTRAINTS = [
      'pos'           => [
        'type'           => 'null|string',
        'required'       => true,
        'validations'    => [
          'check_match'     => [
            null,
            self::SIDE_TOP,
            self::SIDE_RIGHT,
            self::SIDE_BOTTOM,
            self::SIDE_LEFT
          ]
        ]
      ],
      'align'         => [
        'type'           => 'null|string',
        'required'       => true,
        'validations'    => [
          'check_match'     => [
            null,
            self::SIDE_TOP,
            self::SIDE_RIGHT,
            self::SIDE_BOTTOM,
            self::SIDE_LEFT
          ]
        ]
      ],
      'sticky'        => [
        'type'           => 'bool',
        'required'       => true
      ],
      'use_cursor'    => [
        'type'           => 'bool',
        'required'       => true
      ],
      'follow_cursor' => [
        'type'           => 'bool',
        'required'       => true
      ],
      'follow_lazily' => [
        'type'           => 'bool',
        'required'       => true
      ]
    ];

    /** @var string Indicates that the Tooltip should be displayed *Immediately* (`~50ms`). */
    const DELAY_NONE = 'none';
    /** @var string Indicates that the Tooltip should be displayed after a *Short Delay* (`~250ms`). */
    const DELAY_SHORT = 'short';
    /** @var string Indicates that the Tooltip should be displayed after a *Medium Delay* (`~500ms`). */
    const DELAY_MEDIUM = 'medium';
    /** @var string Indicates that the Tooltip should be displayed after a *Long Delay* (`~1000ms`). */
    const DELAY_LONG = 'long';

    /** @var string Indicates that the Tooltip should be positioned or aligned to the *Top* of the *Layer Target*. */
    const SIDE_TOP = 'top';
    /** @var string Indicates that the Tooltip should be positioned or aligned to the *Right* of the *Layer Target*. */
    const SIDE_RIGHT = 'right';
    /** @var string Indicates that the Tooltip should be positioned or aligned to the *Bottom* of the *Layer Target*. */
    const SIDE_BOTTOM = 'bottom';
    /** @var string Indicates that the Tooltip should be positioned or aligned to the *Left* of the *Layer Target*. */
    const SIDE_LEFT = 'left';

    /** @var string Indicates that the Tooltip will be triggered via *Hovering over* the Layer Target. */
    const TRIGGER_FOCUS = 'focus';
    /** @var string Indicates that the Tooltip will be triggered via the *Primary Click* on the Layer Target. */
    const TRIGGER_PRIMARY_CLICK = 'primary-click';
    /** @var string Indicates that the Tooltip will be triggered via the *Secondary Click* on the Layer Target. */  
    const TRIGGER_SECONDARY_CLICK = 'secondary-click';

    /** @var string The content of the Tooltip. */
    protected $content = "";
    /** @var null|string The *Layer Name* if available. Useful for identifying or grouping related Tooltips together. */
    protected $name = null;
    /** @var string|int A `DELAY_*` class constant or an `int` representing the delay between triggering the Tooltip and the Tooltip being displayed. 
     * - Only valid for the `focus` *trigger*. 
     * - Defaults to `DELAY_SHORT` for `button` and `a` *Layer Targets*. All other targets default to `DELAY_MEDIUM`.
     **/
    protected $delay = null;
    /** @var array A list of *Layer Triggers* that will trigger the Tooltip. */
    protected $triggers = [
      'focus'
    ];
    /** @var array Properties related to the *Tooltip Positioning* in relation to the *Layer Target*.
     * - See `update_position()` for more information.
     * - **Available Properties**
     * - - `pos`
     * - - `align`
     * - - `sticky`
     * - - `use_cursor`
     * - - `follow_cursor`
     * - - `follow_lazily`
     */
    protected $position = [
      'pos'           => self::SIDE_TOP,
      'align'         => null,
      'sticky'        => false,
      'use_cursor'    => false,
      'follow_cursor' => false,
      'follow_lazily' => false
    ];

    /** Initialize a new `TooltipLayer` 
     * 
     * @param array $properties An array of `TooltipLayer` Properties to pass to the object. Refer to each's respective setter method for more information.
     * - **Available Properties**:
     * - - `name`
     * - - `delay`
     * - - `pos`
     * - - `align`
     * - - `sticky`
     * - - `use_cursor`
     * - - `follow_cursor`
     * - - `follow_lazily`
     * - - `triggers`
     * @return TooltipLayer Returns the new `TooltipLayer` representing the provided `$properties`
     */
    public function __construct ($properties = []) {
      if (isset($properties['content'])) {
        $this->update_content($properties['content']);
      }
      if (isset($properties['name'])) {
        $this->set_name($properties['name']);
      }
      if (isset($properties['delay'])) {
        $this->change_delay($properties['delay']);
      }
      if (isset($properties['triggers'])) {
        $this->set_triggers($properties['triggers']);
      }

      $this->update_position($properties);
    }

    /** Update the *Tooltip Content*
     * 
     * @param string $content The new content of the Tooltip. 
     * @return TooltipLayer Returns the `TooltipLayer` object for further chaining.
     */
    public function update_content (string $content) {
      $this->content = $content;

      return $this;
    }
    /** Set or Clear the *Layer Name* of the Tooltip.
     * 
     * @param string|null $name The *Layer Name* of the Tooltip. Passing **null** will clear the current name.
     * @return TooltipLayer Returns the `TooltipLayer` for further chaining.
     */
    public function set_name ($name) {
      $isValidName = (function () use ($name) {
        if (isset($name)) {
          if (is_string($name)) {
            return true;
          }
          else {
            trigger_error("The Layer Name must be a String or NULL.");
          }
        }
        else {
          return true;
        }

        return false;
      })();

      if ($isValidName) {
        if ($this->name !== $name) {
          $this->name = $name;
        }
      }

      return $this;
    }
    /** Change the *Layer Delay* of the Tooltip.
     * 
     * @param string|int|null $delay The new *Layer Delay* of the Tooltip. Can be the value of a `DELAY_*` class constant, a *Delay `int`* in Milliseconds, or **null**.
     * @return TooltipLayer Returns the `TooltipLayer` for further chaining.
     */
    public function change_delay ($delay) {
      $isValidDelay = (function () use ($delay) {
        if (!isset($delay)) {
          return true;
        }
        else if (is_string($delay) && !is_numeric($delay)) {
          $isValidMatch = Validations\check_match(
            $delay, 
            [
              self::DELAY_NONE,
              self::DELAY_SHORT,
              self::DELAY_MEDIUM,
              self::DELAY_LONG
            ]
          );
  
          if ($isValidMatch) {
            return true;
          }
          else {
            trigger_error("\"{$delay}\" is not a valid Tooltip Delay Preset.", E_USER_WARNING);
          }
        }
        else if (is_numeric($delay)) {
          return true;
        }

        return false;
      })();

      if ($isValidDelay) {
        if ($this->delay !== $delay) {
          $this->delay = $delay;
        }
      }

      return $this;
    }
    /** Update the *Layer Position Properties* of the Tooltip
     * 
     * @param array $position_properties An `array` representing the *Layer Position Properties* to be set:
     * 
     * | Property | Type | Description | Default Value |
     * | --- | --- | --- | --- |
     * | *pos* | `null\|string` | A `SIDE_*` class constant value indicating how the Tooltip is *positioned* relative to the *Layer Target*. | `SIDE_TOP` | 
     * | *align* | `null\|string` | A `SIDE_*` class constant value indicating how the Tooltip is *aligned* relative to the *Layer Target*. | `null` |
     * | *sticky* | `bool` | Indicates if the Tooltip should be *fixed* to the screen. | `false` |
     * | *use_cursor* | `bool` | Indicates if the Tooltip should use the *Mouse Cursor* as the *Layer Target*. | `false` |
     * | *follow_cursor* | `bool` | Indicates if the Tooltip should follow the *Mouse Cursor* while active. Requires `use_cursor` to be **true**. | `false` |
     * | *follow_lazily* | `bool` | Indicates if the Tooltip should only follow the Mouse Cursor *on the axis it's positioned on*. Requires `use_cursor` and `follow_cursor` to both be **true**. | `false` |
     * @return TooltipLayer Returns the `TooltipLayer` for further chaining.
     */
    public function update_position ($position_properties = []) {
      foreach ($this->position as $property => &$currentValue) {
        if (array_key_exists($property, $position_properties)) {
          $newValue = $position_properties[$property];
          $validator = new Validations\VariableEvaluator(self::POSITION_CONSTRAINTS);
  
          if ($validator->check_variable($newValue, $property)) {
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
    /** Set the *Layer Triggers* for the trigger.
     * 
     * @param array $triggers An array of values representing `TRIGGER_*` class constants, indicating which trigger(s) will invoke the Tooltip.
     * @return TooltipLayer Returns the `TooltipLayer` for further chaining.
     */
    public function set_triggers (array $triggers) {
      $hasValidTriggers = Validations\check_match(
        $triggers, 
        [
          self::TRIGGER_FOCUS,
          self::TRIGGER_PRIMARY_CLICK,
          self::TRIGGER_SECONDARY_CLICK
        ]
      );

      if ($hasValidTriggers) {
        if (count(array_diff($this->triggers, $triggers)) > 0) {
          $this->triggers = $triggers;
        }

        return $this;
      }
      else {
        trigger_error("The provided Triggers contain one or more invalid triggers.", E_USER_WARNING);
      }

      return $this;
    }
  }
?>