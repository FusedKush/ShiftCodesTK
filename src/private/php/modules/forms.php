<?php
  // New Module
  namespace ShiftCodesTK\Forms {
    use ShiftCodesTK\Strings;

    // `FIELD_TYPES`
    (function () {
      $typeList = (function () {
        $typeList = [];
        $categories = [
          'text' => [
            'password',
            'search',
            'tel',
            'text',
            'textarea',
          ],
          'addresses' => [
            'email',
            'url'
          ],
          'numbers' => [
            'number', 
            'range'
          ],
          'dates' => [
            'date', 
            'datetime', 
            'datetimetz', 
            'month',
            'time', 
            'week'
          ],
          'multi' => [
            'checkbox', 
            'radio', 
            'toggle-box',
            'toggle-button',
            'select',
            'tz'
          ],
          'button' => [
            'button',
            'reset',
            'submit'
          ],
          'other' => [
            'group',
            'color',
            'file'
          ]
        ];
  
        $typeList['all'] = array_merge(...array_values($categories));
        $typeList['fields'] = array_merge(
          $categories['text'],
          $categories['addresses'],
          $categories['numbers'],
          $categories['dates'],
          $categories['multi'],
          $categories['other']
        );  
        $typeList['buttons'] = $categories['button'];
        $typeList['inputs'] = array_merge(
          array_slice($categories['text'], 0, -1),
          $categories['addresses'],
          $categories['numbers'],
          $categories['dates'],
          array_slice($categories['multi'], 0, -2),
          $categories['other']
        );
        $typeList['toolbar'] = array_merge(
          $categories['text'],
          $categories['addresses'],
          $categories['numbers']
        );
        $typeList['categories'] = $categories;
  
        return $typeList;
      })();

      /** @var array A `Multi-Dimensional Array` representing the various groups and categories of *Form Field Types*.
       * 
       * | Key | Description |
       * | --- | --- |
       * | *all* | Represents all of the available *Form Field Types*, including *Buttons*. |
       * | *fields* | Represents the *Form Field Types* used a *Form Fields*. |
       * | *buttons* | Represents the *Form Field Types* used for *Form Buttons*. |
       * | *inputs* | Represents the *Form Field Types* that utilize the `input` HTML Element. |
       * | *toolbar* | Represents the *Form Field Types* that utilize the *Toolbar*. |
       * | *categories* | An `Associative Array` representing all of the *Form Field Types*, grouped together by their *Categories*. |
       */
      define("ShiftCodesTK\Forms\FIELD_TYPES", $typeList);
    })();
    // `VALIDATION_MESSAGES
    (function () {
      $messages = [
        'typeMismatch' => array_merge(
          [
            'email'  => '${field} is not a valid Email Address.',
            'url'    => '${field} is not a valid URL.'
          ],
          array_fill_keys(FIELD_TYPES['categories']['text'], 'An invalid value was provided for ${field}.'),
          array_fill_keys(FIELD_TYPES['categories']['dates'], '${field} is not a valid date.'),
          array_fill_keys(FIELD_TYPES['categories']['numbers'], '${field} is not a valid number.'),
          array_fill_keys(FIELD_TYPES['categories']['multi'], 'An invalid option was provided for ${field}.')
        ),
        'valueMissing' => array_merge(
          [
            'date' => 'You must select a date for ${field}.',
            'time' => 'You must select a time for ${field}.',
            'tz'   => 'You must select a timezone for ${field}.',
          ],
          array_fill_keys(FIELD_TYPES['categories']['text'], '${field} cannot be left empty.'),
          array_fill_keys(FIELD_TYPES['categories']['numbers'], 'You must select a value for ${field}.'),
          array_fill_keys(FIELD_TYPES['categories']['multi'], 'You must select an option for ${field}')
        ),
        'valueMismatch' => '${field} is not of a permitted value.',
        'rangeUnderflow' => array_merge(
          array_fill_keys(FIELD_TYPES['categories']['text'], '${field} must be longer than ${threshold} character${plural}.'),
          array_fill_keys(FIELD_TYPES['categories']['dates'], '${field} must be after ${threshold}.'),
          array_fill_keys(FIELD_TYPES['categories']['numbers'], '${field} must be greater than ${threshold}.'),
          array_fill_keys(FIELD_TYPES['categories']['multi'], 'At least ${threshold} option${plural} have to be selected for ${field}.')
        ),
        'rangeMismatch' => array_merge(
          array_fill_keys(FIELD_TYPES['categories']['text'], '${field} must be exactly ${threshold} character${plural}.'),
          array_fill_keys(
            array_merge(FIELD_TYPES['categories']['dates'], FIELD_TYPES['categories']['numbers']), 
            '${field} must be exactly ${threshold}.'
          ),
          array_fill_keys(FIELD_TYPES['categories']['multi'], 'Exactly ${threshold} option${plural} have to be selected for ${field}.')
        ),
        'rangeOverflow' => array_merge(
          array_fill_keys(FIELD_TYPES['categories']['text'], '${field} must be shorter than ${threshold} character${plural}.'),
          array_fill_keys(FIELD_TYPES['categories']['dates'], '${field} must be before ${threshold}.'),
          array_fill_keys(FIELD_TYPES['categories']['numbers'], '${field} must be less than ${threshold}.'),
          array_fill_keys(FIELD_TYPES['categories']['multi'], 'No more than ${threshold} option${plural} can be selected for ${field}.')
        ),
        'patternMismatch' => array_merge(
          [
            'date' => '${field} is not a valid date. Please enter the date in the following format: YYYY-MM-DD',
            'time' => '${field} is not a valid time. Please enter the time in the following format: 24H:MM:SS.MS'
          ],
          array_fill_keys(FIELD_TYPES['fields'], '${field} contains invalid characters.')
        ),
      ];

      /** @var array An `Associative Array` representing the default *Validation Messages* for invalid field values. */
      define("ShiftCodesTK\Forms\VALIDATION_MESSAGES", $messages);
    })();
    // `ALERT_TEMPLATE`
    (function () {
      $template = <<<EOT
        <div class="alert" role="alert">
          <span class="icon">
            <span class="fas" aria-hidden="true"></span>
          </span>
          <span class="message"></span>
        </div>
      EOT;

      $template = Strings\collapse($template);

      /** @var string Represents a *Form Alert Element*, used for communicating issues with the form. */
      define("ShiftCodesTK\Forms\ALERT_TEMPLATE", $template);
    })();
  }
  // Old Module
  namespace {
    use const ShiftCodesTK\DATE_TIMEZONES;

    /** Form Configuration */
  
    /** @var array A categorized array of form field types */
    define('FORM_FIELD_TYPES', (function () {
      $types = [
        'text' => [
          'password',
          'search',
          'tel',
          'text',
          'textarea',
        ],
        'addresses' => [
          'email',
          'url'
        ],
        'numbers' => [
          'number', 
          'range'
        ],
        'dates' => [
          'date', 
          'datetime', 
          'datetimetz', 
          'month',
          'time', 
          'week'
        ],
        'multi' => [
          'checkbox', 
          'radio', 
          'toggle-box',
          'toggle-button',
          'select',
          'tz'
        ],
        'button' => [
          'button',
          'reset',
          'submit'
        ],
        'other' => [
          'group',
          'color',
          'file'
        ]
      ];
      
      $types['all'] = [
        'inputs' => array_merge(
          array_slice($types['text'], 0, count($types['text']) - 1), 
          $types['addresses'], 
          $types['numbers'], 
          $types['dates'], 
          array_slice($types['multi'], 0, count($types['text']) - 2),
          $types['other']
        ),
        'fields' => array_merge(...array_values($types))
      ];
      $types['toolbar'] = array_merge(
        $types['text'], 
        $types['addresses'], 
        $types['numbers']
      );
  
      return $types;
    })());
    /**
     * @var array
     * Default validation messages for warnings and errors
     * - Each *value* can be either a `string` with the validation message, or an `array` of messages for each parameter type.
     * - Validation Messages specified in `$customValidationMessages` will take prescendence over those found here.
     * - Valid issue types include: 
     * - - `typeMismatch`
     * - - `valueMissing`, `valueMismatch`
     * - - `rangeUnderflow`, `rangeMismatch`, `rangeUnderflow`
     * - - `patternMismatch`
     */
    define('FORM_VALIDATION_MESSAGES', [
      'typeMismatch'    => array_merge(
        [ 'email'  => '${field} is not a valid Email Address.',
          'url'    => '${field} is not a valid URL.'],
          array_fill_keys(FORM_FIELD_TYPES['text'], 'An invalid value was provided for ${field}.'),
          array_fill_keys(FORM_FIELD_TYPES['dates'], '${field} is not a valid date.'),
          array_fill_keys(FORM_FIELD_TYPES['numbers'], '${field} is not a valid number.'),
          array_fill_keys(FORM_FIELD_TYPES['multi'], 'An invalid option was provided for ${field}.')
      ),
      'valueMissing'    => array_merge(
          [ 'date' => 'You must select a date for ${field}.',
            'time' => 'You must select a time for ${field}.',
            'tz'   => 'You must select a timezone for ${field}.'],
          array_fill_keys(FORM_FIELD_TYPES['text'], '${field} cannot be left empty.'),
          // array_fill_keys(FORM_FIELD_TYPES['dates'], 'You must select a date for ${field}.'),
          array_fill_keys(FORM_FIELD_TYPES['numbers'], 'You must select a value for ${field}.'),
          array_fill_keys(FORM_FIELD_TYPES['multi'], 'You must select an option for ${field}')
      ), 
      'valueMismatch'   => '${field} is not one of the permitted values.',
      'rangeUnderflow'  => array_merge(
        array_fill_keys(FORM_FIELD_TYPES['text'], '${field} must be longer than ${threshold} character${plural}.'),
        array_fill_keys(FORM_FIELD_TYPES['dates'], '${field} must be after ${threshold}.'),
        array_fill_keys(FORM_FIELD_TYPES['numbers'], '${field} must be greater than ${threshold}.'),
        array_fill_keys(FORM_FIELD_TYPES['multi'], 'At least ${threshold} option${plural} have to be selected for ${field}.')
      ), 
      'rangeMismatch'   => array_merge(
        array_fill_keys(FORM_FIELD_TYPES['text'], '${field} must be exactly ${threshold} character${plural}.'),
        array_fill_keys(array_merge(FORM_FIELD_TYPES['dates'], FORM_FIELD_TYPES['numbers']), '${field} must be exactly ${threshold}.'),
        array_fill_keys(FORM_FIELD_TYPES['multi'], 'Exactly ${threshold} option${plural} have to be selected for ${field}.')
      ), 
      'rangeOverflow'  => array_merge(
        array_fill_keys(FORM_FIELD_TYPES['text'], '${field} must be shorter than ${threshold} character${plural}.'),
        array_fill_keys(FORM_FIELD_TYPES['dates'], '${field} must be before ${threshold}.'),
        array_fill_keys(FORM_FIELD_TYPES['numbers'], '${field} must be less than ${threshold}.'),
        array_fill_keys(FORM_FIELD_TYPES['multi'], 'No more than ${threshold} option${plural} can be selected for ${field}.')
      ),
      'patternMismatch' => array_replace(
        array_fill_keys(FORM_FIELD_TYPES['all']['fields'], '${field} contains invalid characters.'),
        [ 'date'   => '${field} is not a valid date. Please enter the date in one of the following format: YYYY-MM-DD',
          'time'   => '${field} is not a valid time. Please enter the time in one of the following format: 24H:MM:SS' ]
      )
    ]);
  
    /** Properties and Methods used by forms and their children */
    class FormCore {
      const ALERT_TEMPLATE = <<<EOT
        <div class="alert" role="alert">
          <span class="icon">
            <span class="fas" aria-hidden="true"></span>
          </span>
          <span class="message"></span>
        </div>
      EOT;
      /** 
       * @var array Properties & Settings used by the internal methods. 
       * - `string $id` — The unique ID of the element. This is automatically generated from the `properties->name` property and inherited from parent elements.
       * - `array $htmlBindings` — Bindings that indicate how various properties are transformed into HTML Classes & Attributes.
       * - - *Note: These bindings are inherited by all children, unless explicitly overridden.*
       * - - `array $classes` — Bindings that indicate how various properties are transformed into HTML Classes.
       * - - - The **key** is the *property string* of the property that is being binded.
       * - - - The **value** is an array of configuration properties. Multiple classes can be added for a single property by providing multiple arrays of configuration properties.
       * - - - - `string $name` — The name of the class, the keyword **inheritProperty** to inherit the *name* of the property, or **inheritValue** to inherit the *value* of the property. If omitted, the behavior of **inheritProperty** is used.
       * - - - - `string $condition` — The condition that must evaluate to **true** for the class to be added to the markup. If omitted, the property value must simply be truthy.
       * - - `array $attributes` — Bindings that indicate how various properties are transformed into HTML Attributes.
       * - - - The **key** is the *property string* of the property that is being binded.
       * - - - The **value** is an array of configuration properties. Multiple attributes can be added for a single property by providing multiple arrays of configuration properties.
       * - - - - `string $name` — The name of the attribute, or the keyword **inherit** to inherit the name of the property. If omitted, the behavior of **inherit** is used.
       * - - - - `string $value` — The value of the attribute, the keyword **inherit** to inherit the value of the property, or the keyword **boolean** to be treated as a *boolean attribute*. If omitted, the behavior of **inherit** is used.
       * - - - - `string $condition` — The condition that must evaluate to **true** for the attribute to be added to the markup. If omitted, the property value must simply be truthy.
       * - `array $htmlDefaults` — Classes & Attributes that are passed to every instance of the element.
       * - - *Note: These Classes & Attributes are inherited by all children, unless explicitly overridden.*
       * - - `array $classes` — An indexed array of classes that are to be passed to every instance of the element.
       * - - `array $attributes` — An associative array of attributes that are to be passed to every instance of the element.
       * - - - The **key** is the *name of the attribute* that is being passed to the element.
       * - - - The **value** is the *value of the attribute* that is being passed to the element.
       * - `array $propertyValidations` — Validation Property settings for the Form Properties.
       * - - Each **key** is the *property name* of the property that possesses those validation settings.
       * - - Each **value** is the ValidationSettings object for the property.
       * - `array $children` — The child classes of the element.
       */
      protected $internalProperties = [
        'id'                  => '',
        'htmlBindings'        => [
          'classes'    => [
            "'inputProperties->type'" => [
              'name' => 'inheritValue'
            ]
          ],
          'attributes' => [
            "'internalProperties->id'" => [
              'name'  => 'inheritProperty',
              'value' => 'inheritValue'
            ],
            "'properties->hidden'" => [
              'name'  => 'inheritProperty',
              'value' => 'boolean'
            ],
            "'properties->disabled'" => [
              'name'  => 'inheritProperty',
              'value' => 'boolean'
            ]
          ]
        ],
        'htmlDefaults'        => [
          'classes'    => [],
          'attributes' => []
        ], 
        'propertyValidations' => [],
        'children'            => []
      ];
      /**
       * @var array Properties that define the element.
       * - `string $name` — The name of the element. This is sent as the **key** of form fields and buttons, and is automatically converted into the **ID**.
       * - `array $customHTML` — Custom HTML Classes & Attributes that are passed to the element.
       * - - `array $classes` — An indexed array of classes that are to be passed to the element.
       * - - `array $attributes` — An associative array of attributes that are to be passed to the element.
       * - - - The **key** is the *name of the attribute* that is being passed to the element.
       * - - - The **value** is the *value of the attribute* that is being passed to the element.
       * - `boolean $disabled` — Indicates if the element can be interacted with.
       * - `boolean $hidden` — Indicates if the element is to be visually rendered.
       * - `boolean $showChildrenFirst` — Indicates if the element's children are to be displayed before the element contents itself. This option only applies to `FormField`'s and `FormButton`'s.
       * - `false|array $template` - An `array` of *templates* to inherit default properties from. Templates are applied in the order they are provided, and provided properties overwrite all template properties.
       * - - *Template List:*
       * - - - **FormBase**: `SINGLE_BUTTON`,
       * - - - **FormField**: `SHIFT_CODE`
       */
      protected $properties = [
        'name'              => '',
        'customHTML'        => [
          'classes'            => [],
          'attributes'         => []
        ],
        'disabled'          => false,
        'hidden'            => false,
        'showChildrenFirst' => false,
        'templates'         => false
      ];
      /**
       * @var array Visual content that is added to the markup.
       * - `string|false $title` — The title of the element. This property behaves differently depending on its usage:
       * - - **Form**: Used as the *title* of the form. 
       * - - **Section**: Used as the *title* of the section.
       * - - **Field**, **Button**: Used as the *label* of the field or button.
       * - - _Can be up to **64** characters long._
       * - `boolean $hideTitle` — Indicates if the title should be visibility rendered on screen or not. Does not affect how labels are associated with inputs to assistive technologies.
       * - `string|false $subtitle` — An additional sentence or two used to describe the element. To provide more information about the field, consider using the `content->description` property.
       * - - _Can be up to **256** characters long._ 
       * - `string|array|false $description` — A long description of the element. A `string` can be provided, or an `array` to display individual line-items.
       * - - _Can be up to **1024** characters long._
       */
      protected $content = [
        'title'               => false,
        'hideTitle'           => false,
        'subtitle'            => false,
        'description'         => false
      ];
  
      /**
       * Update a property of the form or form child
       * - _**Note:** Not all properties can be properly updated, and can only be defined once during initial construction._
       * 
       * @param mixed $propertyString The *property string* of the property to update.
       * @param mixed $propertyValue The new *value* of the property.
       * - **NULL** *cannot* be provided as the value of a property.
       * - If both the new and existing value are an `array`, they will be recursively merged. The following properties are exceptions to this rule:
       * - - `formFooter->actions->reset->classes`
       * - - `formFooter->actions->reset->attributes`
       * - - `formFooter->actions->detailsToggle->classes`
       * - - `formFooter->actions->detailsToggle->attributes`
       * - - `formFooter->actions->submit->classes`
       * - - `formFooter->actions->submit->attributes`
       * - - `properties->customHTML->classes`
       * - - `properties->customHTML->attributes`
       * - - `content->description`
       * - - `inputProperties->options`
       * @param boolean $internalUpdate Indicates if the property is being updated internally. Allow for more lenient error handling during construction.
       * @return boolean Returns **true** if the property was successfully updated, or **false** if an error occurred.
       */
      public function updateProperty ($propertyString, $propertyValue, $internalUpdate = false) {
        $property = &$this->findReferencedProperty($propertyString);
  
        // Ignore unset, numerical array indexes
        if ($property === null && !is_numeric(preg_replace('/(.+)(->)/', '', $propertyString))) {
          if (!$internalUpdate) {
            trigger_error("Failed to update form property: \"{$propertyString}\" does not exist. ");
            return null;
          }
          else {
            $property = &$this->findReferencedProperty($propertyString, true);
          }
        }
  
        if (!$internalUpdate) {
          // else if ($propertyValue === null) {
          //   trigger_error("Failed to update form property: properties cannot be set to NULL.");
          //   return false;
          // }
          if (strpos($propertyString, 'internalProperties') !== false) {
            trigger_error("Failed to update form property: internalProperties cannot be modified. ");
            return null;
          }
          else if ($propertyString == 'properties->name') {
            trigger_error("Failed to update form property: 'properties->name' can only be defined during construction.");
            return null;
          }
        }
  
        // Update the property
        return (function () use (&$property, $propertyString, $propertyValue, $internalUpdate) {
          $validations = (function () use ($property, $propertyString) {
            $properties = $this->internalProperties['propertyValidations']["'{$propertyString}'"] ?? false;
  
            if ($properties) {
              $properties->value = $property;
  
              return $properties;
            }
            else {
              return false;
            }
          })();
  
          $updateProperty = function ($propertyValue) use (&$property, $internalUpdate, $propertyString) {
            /** Property Strings that should not have their values recursively replaced. */
            $arrayReplacementExceptions = [
              'formFooter->actions->reset->classes',
              'formFooter->actions->reset->attributes',
              'formFooter->actions->detailsToggle->classes',
              'formFooter->actions->detailsToggle->attributes',
              'formFooter->actions->submit->classes',
              'formFooter->actions->submit->attributes',
              'properties->customHTML->classes',
              'properties->customHTML->attributes',
              'content->description',
              'inputProperties->options'
            ];
            
            if (is_array($property) && is_array($propertyValue) && array_search($propertyString, $arrayReplacementExceptions) === false) {
              $property = array_replace_recursive($property, $propertyValue);
            }
            else {
              $property = $propertyValue;
            }
          };
  
          if ($validations) {
            $validationResult = $validations->check_parameter($propertyValue, $propertyString);
    
            if (!$internalUpdate) {
              if ($validationResult['warnings']) {
                trigger_error('Form property validation failed. ' . print_r($validationResult['warnings'], true));
              }
              if ($validationResult['errors']) {
                trigger_error('Form property validation failed. ' . print_r($validationResult['errors'], true));
                return false;
              }
            }
            if ($validationResult['valid']) {
              $updateProperty($validationResult['parameter']);
            }
          }
          else {
            $updateProperty($propertyValue);
          }
          
          return true;
        })();
      }
      /**
       * Add a child element to the form
       * 
       * @param string $childType The type of child that is being added. Valid options are **section**, **field**, and **button**.
       * @param mixed $properties An array of properties that are to be passed to the element.
       * @return object|false Returns the *child object* if the child was successfully added, or **false** if an error occurred.
       */
      public function addChild ($childType, $properties) {
        // Name
        if (get_class($this) != 'FormBase') {
          $properties['properties']['name'] = "{$this->properties['name']}_{$properties['properties']['name']}";
  
          $properties['internalProperties']['form'] = &$this->internalProperties['form'];
        }
        else {
          $properties['internalProperties']['form'] = &$this;
        }
  
        /** The child class */
        $child = (function () use ($childType, $properties) {
          switch ($childType) {
            case 'section' :
              return new FormSection($properties);
              break;
            case 'field' :
              return new FormField($properties);
              break;
            case 'button' :
              return new FormButton($properties);
              break;
            default: 
              trigger_error("Failed to add form child: \"$childType\" is not a valid option.");
              return false;
              break;
          }
        })();
  
        if (!$child) {
          return false;
        }
  
        // Update the child properties
        (function () use (&$child, $properties) {
          $id = "{$this->internalProperties['id']}_{$child->internalProperties['id']}";
  
          $child->updateProperty("internalProperties->id", clean_id($id), true);
  
          // Inherited Props
          (function () use (&$child, $properties) {
            $inherited = [
              'properties->hidden',
              'properties->disabled',
              'inputProperties->validations->required',
              'inputProperties->validations->readonly',
              'inputProperties->validations->validations',
            ];
  
            foreach ($inherited as $propertyString) {
              $parentProperty = $this->findReferencedProperty($propertyString);
              $providedProperty = $this->findReferencedProperty($propertyString, false, $properties);
  
              if ($parentProperty !== null && $providedProperty === null) {
                $child->updateProperty($propertyString, $parentProperty, true);
              }
            }
          })();
        })();
  
        return $this->internalProperties['children'][$child->findReferencedProperty('properties->name')] = $child;
      }
      /**
       * Initialize the form or form child class 
       * 
       * @param array $providedProperties An array of properties that are to be passed to the element.
       * @return void
       */
      protected function construct ($providedProperties) {
        $properties = [
          'parent'   => get_parent_class($this)
                        ? get_class_vars(get_parent_class($this))
                        : [],
          'class'    => $this,
          'templates' => (function () use ($providedProperties) {
            $templates = $providedProperties['properties']['templates'] ?? false;
            $templateList = [
              'FormBase'    => [
                'SINGLE_BUTTON' => [
                  'formProperties' => [
                    'action'          => [
                      'path'             => '/api/post/shift/redeem'
                    ],
                    'showAlerts'      => false,
                    'spacing'         => 'none'
                  ],
                  'formResult'     => [
                    'formState'       => 'enabled'
                  ],
                  'formFooter'     => [
                    'enabled'         => false,
                    'actions'         => [
                      'reset'            => [
                        'requiresModify'    => false
                      ],
                      'submit'           => [
                        'requiresModify'    => false
                      ]
                    ]
                  ]
                ]
              ],
              'FormField'   => [
                'SHIFT_CODE'   => [
                  'properties'      => [
                    'name'             => 'shift_code',
                  ],
                  'content'         => [
                    'title'            => "SHiFT Code",
                    'innerTitle'       => true
                  ],
                  'inputProperties' => [
                    'type'             => 'text',
                    'placeholder'      => 'A12B3-C4D5E-6F7G8-H9I0J-K1L2M',
                    'toolbar'          => [
                      'clearFieldButton'  => true,
                      'textTransform'     => 'uppercase',
                      'dynamicFill'       => [
                        'match'              => '([\w\d]{5})',
                        'fill'               => '$1-',
                      ]
                    ],
                    'validations'      => [
                      'validations'       => [
                        'range'              => [
                          'is'                  => 29
                        ],
                        'pattern'            => '%([a-zA-Z0-9]{5}-{0,1}){5}%'
                      ],
                      'customValidationMessages' => [
                        'rangeMismatch'             => 'SHiFT Codes must be 29 characters long, in the following format: XXXXX-XXXXX-XXXXX-XXXXX-XXXXX',
                        'patternMismatch'           => 'SHiFT Codes must be in the following format: XXXXX-XXXXX-XXXXX-XXXXX-XXXXX'
                      ]
                    ]
                  ]
                ]
              ]
            ];
            $templatePropertyList = [];
  
            if ($templates) {
              foreach ($templates as $template) {
                if (isset($templateList[get_class($this)][$template])) {
                  $templatePropertyList = array_replace_recursive($templatePropertyList, $templateList[get_class($this)][$template]);
                }
              }
            }
  
            return $templatePropertyList;
          })(),
          'provided' => $providedProperties
        ];
        /** Properties that should not have their nested properties processed separately. */
        $nestingBlacklist = [
          'internalProperties->form',
          'internalProperties->children',
          'internalProperties->defaultHTML',
          'properties->customHTML',
          'formFooter->actions->reset->classes',
          'formFooter->actions->reset->attributes',
          'formFooter->actions->detailsToggle->classes',
          'formFooter->actions->detailsToggle->attributes',
          'formFooter->actions->submit->classes',
          'formFooter->actions->submit->attributes',
        ];
  
        $processNode = function ($node, $basePropertyString = '', $usingCustomProperties = false) use (&$processNode, $properties, $nestingBlacklist) {
          foreach ($node as $propertyName => $propertyValue) {
            $propertyString = $basePropertyString
                              ? "{$basePropertyString}->{$propertyName}"
                              : $propertyName;
            $parentProperty = preg_match('/^_+/', $propertyString) === 1 && !$usingCustomProperties ? preg_replace('/^_+/', '', $propertyString) : false;
            $isNestedProperty = (is_array($propertyValue) 
                                  || is_object($propertyValue))
                                && $this->findReferencedProperty($propertyString)
                                && array_search($propertyString, $nestingBlacklist) === false;
  
            if ($parentProperty) {
              $this->updateProperty($parentProperty, $propertyValue, true);
            }
            else if ($isNestedProperty) {
              $processNode($propertyValue, $propertyString, $usingCustomProperties);
            }
            else {
              $internalUpdate = (function () use ($propertyString, $usingCustomProperties) {
                $internalProperties = [
                  'properties->name',
                  'internalProperties->form'
                ];
  
                if (array_search($propertyString, $internalProperties) !== false) {
                  return true;
                }
                else {
                  return !$usingCustomProperties;
                }
              })();
  
              $this->updateProperty($propertyString, $propertyValue, $internalUpdate);
  
              if ($propertyString == "properties->name" && $usingCustomProperties) {
  
                $this->updateProperty('internalProperties->id', clean_id($propertyValue), true);
              }
            }
          }
        };
        $invokeTraitConstructors = function ($position) {
          if (($usedTraits = class_uses($this))) {
            foreach ($usedTraits as $trait) {
              $constructor = "{$trait}__construct";
    
              if (method_exists($this, $constructor)) {
                $this->{$constructor}($position);
              }
            }
          }
        };
  
        // FormCore Property Validations
        $this->updateProperty('internalProperties->propertyValidations', [
          "'properties->name'" => new ValidationProperties([
            'type'        => 'string',
            'required'    => true,
            'validations' => [
              'pattern' => '/^[\d\w ]+$/',
              'range'   => [
                'min' => 1,
                'max' => 100
              ]
            ]
          ]),
          "'properties->customHTML'" => new ValidationProperties([
            'type' => 'array'
          ]),
          "'properties->customHTML->classes'" => new ValidationProperties([
            'type' => 'array'
          ]),
          "'properties->customHTML->attributes'" => new ValidationProperties([
            'type' => 'array'
          ]),
          "'properties->disabled'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'properties->showChildrenFirst'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'properties->hidden'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'content->title'" => new ValidationProperties([
            'type'              => 'boolean|string',
            'validations'       => [
              'range' => [
                'max' => 64
              ]
            ]
          ]),
          "'content->hideTitle'" => new ValidationProperties([
            'type'              => 'boolean'
          ]),
          "'content->subtitle'" => new ValidationProperties([
            'type'              => 'boolean|string',
            'validations'       => [
              'range' => [
                'max' => 256
              ]
            ]
          ]),
          "'content->description'" => new ValidationProperties([
            'type'              => 'boolean|string|array',
            'validations'       => [
              'range' => [
                'max' => 1024
              ]
            ]
          ])
        ], true);
        
        $invokeTraitConstructors('before');
  
        $processNode($properties['class'], '', false);
        $processNode($properties['templates'], '', false);
        $processNode($properties['provided'], '', true);
  
        $invokeTraitConstructors('after');
  
        // Unset appended properties
        foreach (get_object_vars($this) as $propertyName => $propertyValue) {
          if (preg_match('/^_+/', $propertyName) === 1) {
            unset($this->{$propertyName});
          }
        }
      }
      /**
       * Retrieve a property using a *property string*
       * 
       * @param string $propertyString A string used to reference form class properties. Use an *arrow* (**->**) to access nested properties, including both *classes* and *arrays*.
       * - *Note: Properties with a `propertyString` as a value **must** be surrounded by a pair of single quotes to be properly parsed.*
       * @param boolean $defineUnsetProperties Indicates if unset properties are to be created on-the-fly when encountered.
       * @param object|array|null $propertyList The base of the search. This can be a *Form `Object`* or an `array` of properties. The current class will be returned if omitted.
       * @return mixed 
       * - Returns the requested *property* on success. The property is *passed by reference*.
       * - If `$defineUnsetProperties` is set to **false**, **NULL** will be returned if an unset property is encountered.
       * - If `$defineUnsetProperties` is set to **true**, *unset* properties will be *created* with a value of **NULL** before being returned.
       * - If an error occurs, **NULL** will be returned.
       */
      public function &findReferencedProperty ($propertyString, $defineUnsetProperties = false, &$propertyList = null) {
        /** Pass null by reference */
        $nullResult = null;
        $levels = (function () use ($propertyString) {
          $matches = [];
  
          preg_match_all("/[\'\"][\w\d\-\>]+[\'\"]|[\w\d]+/", $propertyString, $matches);
  
          if (!$matches) {
            return false;
          }
  
          return $matches[0];
        })();
  
        if ($levels) {
          $property = &$propertyList;
          
          if ($property === null) {
            $property = &$this;
          }
  
          foreach ($levels as $i => $level) {
            $parentType = gettype($property);
  
            if ($parentType == 'object') {
              if (!isset($property->$level)) {
                if ($defineUnsetProperties) {
                  $property->$level = null;
                }
                else {
                  return $nullResult;
                }
              }
  
              $property = &$property->$level;
            }
            else if ($parentType == 'array') {
              if (!isset($property[$level])) {
                if ($defineUnsetProperties) {
                  $property[$level] = null;
                }
                else {
                  return $nullResult;
                }
              }
  
              $property = &$property[$level];
            }
            else {
              return $nullResult;
            }
          }
  
          return $property;
        }
        else {
          return $nullResult;
        }
      }
      /**
       * Retrieve a child object of the form
       * 
       * @param string $childName The name of the child element to be retreived.
       * @return object|false If the child object is found, it is returned. The object is *passed by reference*. If the child could not be found, **false** is returned.
       */
      public function &getChild ($childName) {
        /** @var false|object */
        $child = false;
        
        $checkChildren = function (&$children) use (&$checkChildren, &$child, $childName) {
          foreach ($children as $name => &$object) {
            $childChildren = &$object->findReferencedProperty('internalProperties->children');
  
            if ($child !== false) {
              return true;
            }
            if ($name == $childName) {
              $child = $object;
              return true;
            }
            else if ($childChildren) {
              $checkChildren($childChildren);
            }
          }
        };
  
        $checkChildren($this->internalProperties['children']);
  
        return $child;
      }
      /**
       * Get the markup for HTML Classes & Attributes
       * 
       * @param boolean $includeCustomAttributes Indicates if the classes & attributes passed by `$properties->customHTML` should be included in the markup.
       * @return string Returns an HTML Markup string made up of the valid classes & attributes.
       */
      public function getAttributeMarkup ($includeCustomAttributes = true) {
        /** The full attribute markup string */
        $markupString = '';
        /** The class markup string */
        $classMarkup = (function () use ($includeCustomAttributes) {
          $classList = [];
  
          // Get Property Bindings
          foreach ($this->internalProperties['htmlBindings']['classes'] as $propertyString => $classes) {
            $parsedPropertyString = preg_replace('/[\'\"]/', '', $propertyString);
            $property = $this->findReferencedProperty($parsedPropertyString);
  
            if ($property !== null && is_array($classes)) {
              if (!is_array($classes[array_key_first($classes)])) {
                $classes = [ $classes ];
              }
  
              foreach ($classes as $propertySettings) {
                $useClass = isset($propertySettings['condition']) 
                    && $propertySettings['condition'] == true 
                  || !isset($propertySettings['condition']) 
                    && $property;
  
                if ($useClass) {
                  if (!isset($propertySettings['name']) || $propertySettings['name'] == 'inheritProperty') {
                    $propertyName = (function () use ($parsedPropertyString) {
                      $name = $parsedPropertyString;
  
                      $name = preg_replace('/(.+->)/', '', $name);
                      $name = preg_replace('/([A-Z])/', "-\${1}", $name);
                      $name = strtolower($name);
  
                      return $name;
                    })();
  
                    $classList[] = $propertyName;
                  }
                  else if ($propertySettings['name'] == 'inheritValue') {
                    $classList[] = $property;
                  }
                  else {
                    $classList[] = $propertySettings['name'];
                  }
                }
              }
  
            }
          }
          // Get Default Classes
          (function () use (&$classList) {
            $classes = $this->internalProperties['htmlDefaults']['classes'];
  
            if ($classes) {
              $classList = array_merge($classList, $classes);
            }
          })();
          // Custom Classes
          (function () use (&$classList, $includeCustomAttributes) {
            $classes = $this->properties['customHTML']['classes'];
  
            if ($includeCustomAttributes && $classes) {
              $classList = array_merge($classList, $classes);
            }
          })();
  
          if ($classList) {
            return 'class="' . clean_all_html(implode(' ', $classList)) . '"';
          }
          else {
            return '';
          }
        })();
        /** The attribute markup string */
        $attributeMarkup = (function () use ($includeCustomAttributes) {
          $attributeList = [];
  
          // Get Property Bindings
          foreach ($this->internalProperties['htmlBindings']['attributes'] as $propertyString => $attributes) {
            $parsedPropertyString = preg_replace('/[\'\"]/', '', $propertyString);
            $property = $this->findReferencedProperty($parsedPropertyString);
  
            if ($property !== null && is_array($attributes)) {
              if (!is_array($attributes[array_key_first($attributes)])) {
                $attributes = [ $attributes ];
              }
  
              foreach ($attributes as $propertySettings) {
                $useAttribute = isset($propertySettings['condition']) 
                                  && $propertySettings['condition'] === true 
                                || !isset($propertySettings['condition']) 
                                  && $property;
  
                if ($useAttribute) {
                  $attributeName = '';
                  $attributeValue = null;
    
                  // Name
                  if (!isset($propertySettings['name']) || $propertySettings['name'] == 'inheritProperty') {
                    $attributeName = preg_replace('/(.+->)/', '', $parsedPropertyString);
                  }
                  else if ($propertySettings['name'] == 'inheritValue') {
                    $attributeName = $property;
                  }
                  else {
                    $attributeName = $propertySettings['name'];
                  }
    
                  // Value
                  if (!isset($propertySettings['value']) || $propertySettings['value'] == 'inheritValue') {
                    $attributeValue = $property;
                  }
                  else if ($propertySettings['value'] == 'inheritValue') {
                    $attributeValue = preg_replace('/(.+->)/', '', $parsedPropertyString);
                  }
                  else if ($propertySettings['value'] == 'boolean') {
                    $attributeValue = "";
                  }
                  else {
                    $attributeValue = $propertySettings['value'];
                  }
    
                  $attributeList[$attributeName] = $attributeValue;
                }
              }
            }
          }
          // Get Default Attributes
          (function () use (&$attributeList) {
            $attributes = $this->internalProperties['htmlDefaults']['attributes'];
  
            if ($attributes) {
              $attributeList = array_merge($attributeList, $attributes);
            }
          })();
          // Custom Attributes
          (function () use (&$attributeList, $includeCustomAttributes) {
            $attributes = $this->properties['customHTML']['attributes'];
  
            if ($includeCustomAttributes && $attributes) {
              $attributeList = array_merge($attributeList, $attributes);
            }
          })();
  
          if ($attributeList) {
            $str = '';
  
            foreach ($attributeList as $attributeName => $attributeValue) {
              $str .= clean_all_html($attributeName) . '="' . clean_all_html($attributeValue) . '" ';
            }
  
            return $str;
          }
          else {
            return '';
          }
        })();
  
        if ($classMarkup) {
          $markupString .= "$classMarkup ";
        }
        if ($attributeMarkup) {
          $markupString .= "$attributeMarkup ";
        }
  
        return collapseWhitespace($markupString);
      }
      /**
       * Retrieve the title, subtitle, description, & alerts markup for a Form element.
       * 
       * @param false|array $pieces Indicates which pieces of the header are to be returned.
       * - *array*: Indicates which pieces are to be returned. Available options include **title**, **subtitle**, **description**, & **alerts**.
       * - **False**: Indicates that the full header should be returned.
       * @return string Returns the HTML header markup for the form element.
       */
      protected function getHeaderMarkup ($pieces = false) {
        $markup = '';
        $content = $this->content;
        $isField = get_class($this) == 'FormField';
  
        $isValidPiece = function ($piece) use ($pieces, $content) {
          $valid = ($pieces === false
                    || array_search($piece, $pieces) !== false)
                   && (($piece != 'alerts'
                    && $content[$piece])
                   || $piece == 'alerts');
  
          return $valid;
        };
  
        if ($content['title'] || $content['subtitle'] || $content['description']) {
          if ($pieces === false) {
            $markup .= '<div class="title-container">';
          }
  
          if ($isValidPiece('title')) {
            $hiddenTitle = $content['hideTitle']
                           ? ' hidden aria-hidden="false"'
                           : '';
  
            if (!$isField) {
              $markup .= "<div class=\"title{$hiddenTitle}\">";
                $markup .= "<span class=\"content\">";
                  $markup .= clean_all_html($content['title']);
                $markup .= '</span>';
              $markup .= '</div>'; // Closing title tag
            }
            else {
              $labelType = (function () {
                $type = $this->inputProperties['type'];
                $legendFields = [
                  'checkbox',
                  'radio',
                  'toggle-button',
                  'toggle-box',
                  'datetime',
                  'datetimetz'
                ];
                
                return array_search($type, $legendFields) !== false ? 'legend' : 'label';
              })();
  
              // Opening Label tags
              $markup .= "<{$labelType} 
                            id=\"{$this->internalProperties['id']}_label\" 
                            for=\"{$this->internalProperties['id']}_input\"
                            {$hiddenTitle}>";
  
                // Invalid Icon
                $markup .= "<span class=\"invalid-icon layer-target\" aria-hidden=\"true\">
                              <span class=\"fas fa-exclamation-triangle\"></span>
                            </span>
                            <div class=\"layer tooltip\" data-layer-delay=\"medium\">
                              There is an issue with this field
                            </div>";
                // Required Field indicator tags
                if ($this->inputProperties['validations']->required) {
                  $requiredIndicatorID = "{$this->internalProperties['id']}_label_required";
                  $markup .= "<span 
                                class=\"required layer-target\" 
                                aria-label=\"(Required) \" 
                                id=\"{$requiredIndicatorID}\">
                                <span class=\"fas fa-asterisk\" aria-hidden=\"true\"></span>
                              </span>
                              <span 
                                class=\"layer tooltip\" 
                                data-layer-target=\"{$requiredIndicatorID}\">
                                Required Field
                              </span>";
                }
  
                // Label Value
                $markup .= "<span class=\"content\">";
                  $markup .= clean_all_html($content['title']);
                $markup .= '</span>';
  
              // Closing label tags
              $markup .= "</{$labelType}>";
            }
          }
          if ($isValidPiece('subtitle')) {
            $markup .= '<div class="subtitle">';
              $markup .= clean_html($content['subtitle'], '<span><a><b><strong><i><em>');
            $markup .= '</div>'; // Closing subtitle tag
          }
          if ($isValidPiece('description')) {
            $markup .= '<div class="description">';
  
            (function () use ($content, &$markup) {
              $description = $content['description'];
  
              if (is_array($description)) {
                $markup .= '<ul class="styled">';
                
                foreach ($description as $key => $item) {
                  if (!is_array_associative($description) || !isset($this->inputProperties['options'][$key])) {
                    $markup .= "<li>";
                      $markup .= clean_html($item, '<span><a><b><strong><i><em><ul><ol><li><code><button><a>');
                    $markup .= '</li>';
                  }
                }
  
                $markup .= '</ul>';
              }
              else {
                $markup .= clean_html($description, '<span><a><b><strong><i><em><br><code><pre><ul><ol><li><button><a>');
              }
            })();
  
            $markup .= '</div>'; // Closing description tag
            $markup = preg_replace('/<div class="description"><ul class="styled"><\/ul><\/div>$/', '', $markup); // Remove empty description
          }
  
          if ($pieces === false) {
            $markup .= '</div>'; // Closing title section tag
          }
        }
  
        if ($isValidPiece('alerts')) {
          $markup .= '<div class="alerts" aria-live="polite"></div>';
        }
        
        return $markup;
      }
      /**
       * Get the markup for all children of the element
       * 
       * @param boolean $includeWrapper — Indicates if the `children` element wrapper should be included in the markup.
       * @return string Returns an HTML Markup string made up of the child elements.
       */
      protected function getChildrenMarkup ($includeWrapper = true) {
        /** @var string The full children markup string */
        $markupString = '';
        /** @var array Internal children that are added to the end of the form. */
        $internalChildren = [];
        /** @var null|FormSection The footer section to be added to the end of the form. */
        $footer = null;
        /** @var boolean Indicates if the parent object is the Form Base. */
        $isFormBase = get_class($this) == 'FormBase';
  
        foreach ($this->internalProperties['children'] as $name => $child) {
          if ($isFormBase && $name == '_footer') {
            $footer = $child;
            continue;
          }
          // if ($isFormBase && check_pattern($name, '/^_/')) {
          //   if ($name == '_footer') {
          //     array_unshift($internalChildren, $child);
          //   }
          //   else {
          //     $internalChildren[] = $child;
          //   }
          // }
          else if (method_exists($child, 'getElementMarkup')) {
            $markupString .= $child->getElementMarkup() . ' ';
          }
        }
  
        // if (count($internalChildren) > 0) {
        //   foreach ($internalChildren as $child) {
        //     $markupString .= $child->getElementMarkup();
        //   }
        // }
        if ($footer) {
          $markupString .= $footer->getElementMarkup();
        }
  
        if ($markupString != "") {
          if ($includeWrapper) {
            $wrapper = '';
  
            if ($this->inputProperties['type'] == 'group' ?? false) {
              $wrapper .= "<div class=\"children group\" data-nested=\"{$this->properties['name']}\">";
            }
            else {
              $wrapper .= '<div class="children">';
            }
  
            $wrapper .= $markupString;
            $wrapper .= "</div>";
  
            return collapseWhitespace($wrapper);
          }
          else {
            return collapseWhitespace($markupString);
          }
        }
        else {
          return "";
        }
      }
    }
    /** Properties and Methods used by children of the form. */
    trait FormChild {
      /**
       * @var array Properties & Settings used by the internal methods. 
       * 
       * - `boolean|object $form` - The parent form. 
       */
      protected $__internalProperties = [
        'form' => false
      ];
      /**
       * @var array Properties that define the element.
       * 
       * - `"full"|"half"|"third"|"two-thirds" $size` — Indicates how much space the element should take up on wider devices.
       */
      public $__properties = [
        'size'               => 'full',
      ];
      /**
       * @var array Properties that define and control form fields and buttons.
       * - `string $type` — The type of input element to be used.
       * - - Organization: *group*
       * - - Input Fields: *color, date, datetime, datetimetz, email, file, number, month, password, range, search, tel, text, textarea, time, tz, url, week*
       * - - Multiple Choice Fields: *checkbox, radio, select*
       * - - Buttons: *button, reset, submit*
       * - `mixed $value` — The default or required value of the input element.
       * - `object $validations` — A ValidationObject of validation settings for the input value.
       * - - **Dynamic Validations**
       * - - - _Dynamic Validations_ are validation constraints that use the value of another field to enforce the field.
       * - - - Supported validations include: `match`, `range`, `pattern`, and `url`.
       * - - - The following format is used to indicate a _Dynamic Validation_:
       * - - - - `${fieldName} content|defaultValue`
       * - - - - **${fieldName}** is the field that is used for validation. 
       * - - - - - Multiple fields can be included. 
       * - - - - - If the referring field value is _empty_ or the referring field is _disabled_ or _readonly_, the value is not included in the validation. See **defaultValue** below.
       * - - - - **content** is a validation value that is included alongside the validations as long as _one of the referring fields is included in the validation_.
       * - - - - **|defaultValue** is a validation value that is used _only if none of the referring fields are included in the validation_. 
       * 
       */
      protected $inputProperties = [
        'type'        => 'text',
        'value'       => '',
        'validations' => null
      ];
  
      /** 
       * The FormChild Constructor 
       * 
       * @param "before"|"after" $position Indicates where the contructor was invoked from.
       * - `before` - The constructor was invoked before class properties have been processed.
       * - `after` - The constructor was invoked after class properties have been processed.
       **/
      public function FormChild__construct($position = 'before') {
        if ($position == 'before') {
          // FormChild Property Validations
          $this->updateProperty('internalProperties->propertyValidations', [
            "'properties->size'" => new ValidationProperties([
              'type'        => 'string',
              'validations' => [
                'match' => [ 'full', 'half', 'third', 'two-thirds' ]
              ]
            ]),
            "'inputProperties->type'" => new ValidationProperties([
              'type'        => 'string',
              'validations' => [
                'match' => FORM_FIELD_TYPES['all']['fields']
              ]
            ]),
            "'inputProperties->value'" => new ValidationProperties([
              'type'        => 'string|integer|double|array'
            ])
          ], true);
          $this->updateProperty('inputProperties->validations', new ValidationProperties([]), true);
        }
        else if ($position == 'after') {
          // FormChild htmlBindings
          $this->updateProperty('internalProperties->htmlBindings->classes', [
            "'properties->size'" => [
              [ 
                'name'      => 'size',
                'condition' => $this->findReferencedProperty('properties->size') != 'full'
              ],
              [ 
                'name'      => 'inheritValue',
                'condition' => $this->findReferencedProperty('properties->size') != 'full'
              ]
            ]
          ], true);
        }
      }
      /**
       * Check the element for dynamic validation properties
       * 
       * @param boolean $retrieveValues Indicates if the constraint values for the dynamic validations should be retrieved.
       * @return array Returns an associative array of dynamic validation properties.
       */
      public function checkDynamicValidations ($retrieveValues = false) {
        $dynamicValidations = [];
  
        $checkValidation = function ($validationName, $validationConstraint) use (&$checkValidation, &$dynamicValidations, $retrieveValues) {
          $matches = [];
  
          if (!is_array($validationConstraint)) {
            if (preg_match('/^(?:\$\{([\w\d_]+)\}){1}(?:\|([^\s\r\n]+)){0,1}$/', $validationConstraint, $matches) === 1) {
              $dynamicValidations[$validationName] = [
                'field'   => $matches[1],
                'default' => $matches[2] ?? false
              ];
  
              if ($retrieveValues) {
                $field = $this->internalProperties['form']->getChild($matches[1]);
  
                if ($field) {
                  $value = $field->inputProperties['value'] ?? '';
  
                  if ($value == '') {
                    $value = '';
                  }
                }
                
                $dynamicValidations[$validationName]['value'] = $value;
              }
    
              return true;
            }
          }
          else {
            foreach ($validationConstraint as $subValidation => $subConstraint) {
              $checkValidation("{$validationName}->{$subValidation}", $subConstraint);
            }
          }
  
          return false;
        };
  
        foreach ($this->inputProperties['validations']->validations as $validation => $constraints) {
          $checkValidation($validation, $constraints);
        }
  
        return $dynamicValidations;
      }
    }
    /** Properties and Methods used by Fields and Buttons. */
    trait FormInput {
      /**
       * @var array Properties that define the element.
       * 
       * - `boolean $showChildrenFirst` — Indicates if the element's children are to be displayed before the element contents itself.
       */
      protected $___properties = [
        'showChildrenFirst' => false,
      ];
  
      /** The FormInput Constructor */
      public function FormInput__construct() {
        // FormInput Property Validations
        $this->updateProperty('internalProperties->propertyValidations', [
          "'properties->showChildrenFirst'" => new ValidationProperties([
            'type'        => 'boolean'
          ]),
        ], true);
      }
    }
  
    /** The base form */
    class FormBase extends FormCore {
      protected $_internalProperties = [
        'htmlBindings' => [
          'classes'    => [
            "'formProperties->action->submitOnChange'" => [
              'name'   => 'inheritProperty'
            ],
            "'formProperties->action->confirmUnsavedChanges'" => [
              'name'   => 'inheritProperty'
            ],
            "'formProperties->action->showProgress'" => [
              'name'   => 'inheritProperty'
            ],
            "'formProperties->showAlerts'" => [
              'name'   => 'hide-alerts'
            ],
            "'formProperties->showBackground'" => [
              'name'   => 'inheritProperty'
            ],
            "'formFooter->isSticky'" => [
              'name'  => 'sticky-footer'
            ],
            "'formFooter->showProgress'" => [
              'name'  => 'inheritProperty'
            ],
            "'formFooter->showChangeCount'" => [
              'name'  => 'inheritProperty'
            ],
            "'formFooter->actions->detailsToggle->hideByDefault'" => [
              'name'  => 'hide-details',
            ],
          ],
          'attributes' => [
            "'properties->name'" => [
              'name'  => 'data-form-name',
              'value' => 'inheritValue'
            ],
            "'formProperties->action->type'" => [
              'name'  => 'data-action-type',
              'value' => 'inheritValue'
            ],
            "'formProperties->action->path'" => [
              'name'  => 'data-action-path',
              'value' => 'inheritValue'
            ],
            "'formProperties->action->method'" => [
              'name'  => 'inheritProperty',
              'value' => 'inheritValue'
            ],
            "'formResult->formState'" => [
              'name'  => 'data-state-after-submit',
              'value' => 'inheritValue'
            ]
          ]
        ],
        'htmlDefaults' => [
          'classes'       => [],
          'attributes'    => [
            'action'               => '#',
            'autocomplete'         => 'off',
            'data-modified-fields' => 0
          ]
        ]
      ];
      /**
       * @var array Properties that define and control the form
       * - `array $action` — Properties that control the submission behavior of the form:
       * - - `"standard"|"ajax"|"js" $type` — Indicates how the form behaves when submitted:
       * - - - _standard_: The form is submitted normally, without the use of AJAX.
       * - - - _ajax_: The form is submitted asynchronously via AJAX.
       * - - - _js_: The form is not to be submitted, but instead used in Javascript.
       * - - `string $path` — The path to the form action script. 
       * - - `"GET"|"POST" $method` — Indicates which Request Method to use to submit the form. Has no effect if `$action` is set to _"js"_.
       * - - `boolean $submitOnChange` — Indicates if committed changes to any field will automatically submit the form as long as it is *valid*. 
       * - - `boolean $confirmUnsavedChanges` — Indicates if a confirmation dialog should be displayed before navigating the page if the form has unsaved changes.
       * - - `boolean $showProgress` — Indicates if form submission progress should be displayed using the _Loader Progress Bar_.
       * - `boolean $showAlerts` — Indicates if form warnings and errors are to be displayed.
       * - `boolean $showBackground` — Indicates if background layers are to be rendered behind field sections & fields.
       * - `'none'|'vertical'|'standard'|'double' $spacing` — Indicates how much spacing to generate with the form.
       */
      protected $formProperties = [
        'action'         => [
          'type'                  => 'ajax',
          'path'                  => '#',
          'method'                => 'POST',
          'submitOnChange'        => false,
          'confirmUnsavedChanges' => false,
          'showProgress'          => true,
        ],
        'showAlerts'     => true,
        'showBackground' => false,
        'spacing'        => 'vertical'
      ];
      /**
       * @var array Configuration options for the Form Footer
       * - `boolean $enabled` — Indicates if the footer should be used in the form or not.
       * - `boolean $isSticky` — Indicates if the footer should stick to the bottom of the screen when scrolling through the form.
       * - `boolean $showProgress` — Indicates if a progress bar should be displayed to indicate the progress made through the form.
       * - - For a field to be counted as progress, the field must have the `canSubmit` and `willSubmit` states as **true**.
       * - - For a field to be counted towards the total, the field must meet one of the following requirements:
       * - - - The field must have the `canSubmit` state as **true**, or the field must be *readonly* or *disabled*.
       * - - - The field must not be *hidden*.
       * - `boolean $showChangeCount` — Indicates if the number of unsaved changes to fields should be displayed.
       * - - Requires that the *submit* button is enabled.
       * - - For a field to be considered to have an unsaved change, it must be *modified* and not *readonly*, *disabled*, or *hidden*.
       * - `array $actions` — The various actions that can be used in the footer.
       * - - `array $reset` — A button that resets the form to its default state when clicked.
       * - - `array $detailsToggle` — A button that toggles the visibility of additional form information when clicked.
       * - - `array $submit` — A button that submits the form when clicked.
       * - - *Each action contains the following properties:*
       * - - - `boolean $enabled` — Indicates if the button should be included in the footer or not.
       * - - - `string $content` — The visual name of the button, as it is to be displayed to the user.
       * - - - `false|string $title` — The alternative text label of the button to be displayed to assistive technologies.
       * - - - `array $tooltip` — Properties for adding a *tooltip* to the button.
       * - - - - `false|string $content` — The content of the tooltip. If **false**, no tooltip will be added.
       * - - - - - _Can be up to **256** characters in length._
       * - - - - `'top'|'right'|'bottom'|'left'|false $pos` — The position of the tooltip relative to the button. 
       * - - - - `'top'|'right'|'bottom'|'left'|false $align` — The alignment of the tooltip relative to the button. 
       * - - - - `false|'short'|'medium'|'long' $delay` — Indicates the delay that *focus* layers have before appearing.
       * - - - - `false|string $name` — The *layer name* used for callbacks or styling.
       * - - - - - _Can be up to **128** characters in length._
       * - - - `null|object $object` — The FormField object of the button.
       * - - - `array $classes` — An indexed array of classes to be passed to the button.
       * - - - `array $attributes` — An associative array of attributes to be passed to the button.
       * - - - - The **key** is the *name of the attribute* that is being passed to the element.
       * - - - - The **value** is the *value of the attribute* that is being passed to the element.
       * - - *The `$reset` and `$submit` actions contain the following properties:*
       * - - - `array $confirmation` — Properties that govern how form event confirmations behave:
       * - - - - `boolean $required` — Indicates if the action displays a confirmation modal when clicked.
       * - - - - `false|string $title` — The title of the confirmation modal.
       * - - - - `false|string $body` — The body of the confirmation modal.
       * - - - - `boolean $requireResponseData` - If true, the `_confirmation` fields will be added to the form, and their values must be populated for the form to validate on the server. 
       * - - - - `array $actions` — Properties related to the confirmation response actions:
       * - - - - - `array $deny` — Properties that goven how the *deny* action button is displayed:
       * - - - - - - `false|string $name` — The name of the action button.
       * - - - - - - `false|string $tooltip` — The tooltip displayed when hovering over the action button.
       * - - - - - - `false|false|"theme"|"light"|"dark"|"info"|"warning"|"danger" $color` — The color of the action button.
       * - - - - - `array $approve` — Properties that goven how the *approve* action button is displayed:
       * - - - - - - `false|string $name` — The name of the action button.
       * - - - - - - `false|string $tooltip` — The tooltip displayed when hovering over the action button.
       * - - - - - - `false|false|"theme"|"light"|"dark"|"info"|"warning"|"danger" $color` — The color of the action button.
       * - - - `requiresModify` — Indicates if the action requires the form to have been modified to be enabled.
       * - `null|object $object` — The FormSection object of the form.
       */
      protected $formFooter = [
        'enabled'         => true,
        'isSticky'        => false,
        'showProgress'    => false,
        'showChangeCount' => false,
        'actions'         => [
          'reset'            => [
            'enabled'           => false,
            'content'           => 'Reset',
            'title'             => false,
            'tooltip'           => [
              'content'            => false,
              'pos'                => 'top',
              'align'              => false,
              'delay'              => 'medium',
              'name'               => false
            ],    
            'confirmation'      => [
              'required'            => true,
              'title'               => false,
              'body'                => false,
              'actions'             => [
                'deny'                => [
                  'name'                 => false,
                  'tooltip'              => false,
                  'color'                => false
                ],
                'approve'             => [
                  'name'                 => false,
                  'tooltip'              => false,
                  'color'                => false
                ]
              ]
            ],
            'requiresModify'    => true,
            'object'            => null,
            'classes'           => [
              'form-reset',
              'styled',
              'warning'
            ],
            'attributes'        => []
          ],
          'detailsToggle'    => [
            'enabled'           => false,
            'content'           => 'Show Details',
            'title'             => false,
            'tooltip'           => [
              'content'            => 'Show additional form details',
              'pos'                => 'top',
              'align'              => false,
              'delay'              => 'medium',
              'name'               => false
            ],
            'hideByDefault'     => false,
            'object'            => null,
            'classes'           => [
              'form-details-toggle',
              'styled',
              'button-effect',
              'outline'
            ],
            'attributes'        => [
              'aria-label'         => 'Show additional form information'
            ]
          ],
          'submit'           => [
            'enabled'           => true,
            'content'           => 'Submit',
            'title'             => false,
            'tooltip'           => [
              'content'            => false,
              'pos'                => 'top',
              'align'              => false,
              'delay'              => 'medium',
              'name'               => false
            ],
            'confirmation'      => [
              'required'            => false,
              'title'               => false,
              'body'                => false,
              'requireResponseData' => true,
              'actions'             => [
                'deny'                => [
                  'name'                 => false,
                  'tooltip'              => false,
                  'color'                => false
                ],
                'approve'             => [
                  'name'                 => false,
                  'tooltip'              => false,
                  'color'                => false
                ]
              ]
            ],
            'requiresModify'    => true,
            'object'            => null,
            'classes'           => [
              'form-submit',
              'styled',
              'info'
            ],
            'attributes'        => []
          ]
        ],
        'object'   => null,
      ];
      /**
       * @var array Properties that separately, or combined, determine how the form response should be formatted *on success*.
       * - `array $toast` — Display a toast on success.
       * - - `boolean $enabled` — Indicates if a toast should be displayed or not.
       * - - `'response'|'session' $method` — Indicates when the toast should be displayed:
       * - - `array $properties` — The toast configuration properties.
       * - `array $redirect` — Redirects the user on success.
       * - - `boolean $enabled` — Indicates if the user should be redirected or not.
       * - - `int $delay` — How long to wait before redirecting the user, in *miliseconds*.
       * - - `string $location` — Where the user should be redirected to. This can be of any URL scheme supported by browsers.
       * - - `boolean $useQueryParam` — Indicates if the *redirect* query parameter can be used.
       * - - - If no value is provided for the *redirect* parameter, the value of `$location` is used instead.
       * - `array $modal` — Displays a result modal on success.
       * - - `boolean $enabled` — Indicates if the result modal should be used or not.
       * - `"disabled"|"enabled"|"reset"` $formState — Indicates the *Active State* of the form after a successful submission. Forms are always re-enabled if an error occurred.
       * - - *disabled*: The form will remain disabled. 
       * - - *enabled*: The form will be re-enabled.
       * - - *reset*: The form will remain disabled, but the *Reset* button can be used to start over and re-enable the form.
       */
      protected $formResult = [
        'toast'     => [
          'enabled'    => false,
          'method'     => 'response',
          'properties' => []
        ],
        'redirect'  => [
          'enabled'       => false,
          'delay'         => 0,
          'location'      => '',
          'useQueryParam' => false
        ],
        'modal'     => [
          'enabled'    => false
        ],
        'formState' => 'disabled'
      ];
      /**
       * @var array Results of the Form Submission
       * - *Most of these values are populated by calling the `validateForm()` method.*
       * - `boolean $success` — Indicates if the form was successfully submitted and processed.
       * - `array $parameterList` — The *original* array of provided parameters generated by the form submission. 
       * - `array $parameters` — A *formatted* array of provided parameters generated by the form submission. 
       * - - Fields that are the child of a *Section* or *Group* are nested underneath the parent.
       * - `array $warnings` — An array of warnings generated by the form submission.
       * - `array $errors` — An array of errors generated by the form submission.
       * - `object $response` — The form's response object.
       */
      protected $formSubmit = [
        'success'       => true,
        'parameterList' => [],
        'parameters'    => [],
        'warnings'      => [],
        'errors'        => [],
        'response'      => null
      ];
  
      /**
       * Initialize the form class 
       * 
       * @param array $providedProperties An array of properties that are to be passed to the element.
       * @return void
       */
      public function __construct ($properties) {
        // FormBase Property Validations
        $this->updateProperty('internalProperties->propertyValidations', [
          "'formProperties->action'" => new ValidationProperties([
            'type'        => 'array',
            'customValidationMessages' => [
              'typeMismatch' => 'Action has been changed to an array. Please update this form configuration to follow the new specification.'
            ]
          ]),
          "'formProperties->action->type'" => new ValidationProperties([
            'type'        => 'string',
            'validations' => [
              'match' => [ 'standard', 'ajax', 'js' ]
            ]
          ]),
          "'formProperties->action->path'" => new ValidationProperties([
            'type'        => 'string',
            'validations' => [
              'isURL'   => [
                'protocol' => true,
                'port'     => true
              ]
            ]
          ]),
          "'formProperties->action->method'" => new ValidationProperties([
            'type'        => 'string',
            'validations' => [
              'match' => [ 'GET', 'POST' ]
            ]
          ]),
          "'formProperties->action->submitOnChange'" => new ValidationProperties([
            'type'        => 'boolean'
          ]),
          "'formProperties->action->confirmUnsavedChanges'" => new ValidationProperties([
            'type'        => 'boolean'
          ]),
          "'formProperties->action->showProgress'" => new ValidationProperties([
            'type'        => 'boolean'
          ]),
          "'formProperties->showAlerts'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'formProperties->showBackground'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'formProperties->spacing'" => new ValidationProperties([
            'type' => 'string',
            'validations' => [
              'match' => [ 
                'none', 
                'vertical', 
                'standard', 
                'double' 
              ]
            ]
          ]),
          "'formFooter->enabled'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'formFooter->isSticky'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'formFooter->showProgress'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'formFooter->showChangeCount'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'formFooter->object'" => new ValidationProperties([
            'type' => 'object'
          ]),
          // See "Footer Action Property Validations" below for action validations
          "'formResult->toast->enabled'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'formResult->toast->method'" => new ValidationProperties([
            'type'        => 'string',
            'validations' => [
              'match' => [ 'response', 'session' ]
            ]
          ]),
          "'formResult->toast->properties'" => new ValidationProperties([
            'type' => 'array'
          ]),
          "'formResult->redirect->enabled'" => new ValidationProperties([
            'type' => 'boolean'
          ]),
          "'formResult->redirect->delay'" => new ValidationProperties([
            'type'        => 'int',
            'validations' => [
              'range' => [
                'min' => 0,
                'max' => 10000
              ]
            ]
          ]),
          "'formResult->redirect->location'" => new ValidationProperties([
            'type'        => 'string',
            'validations' => [
              'isURL' => true
            ]
          ]),
          "'formResult->redirect->useQueryParam'" => new ValidationProperties([
            'type'        => 'boolean'
          ]),
          "'formResult->modal->enabled'" => new ValidationProperties([
            'type' => 'boolean',
          ]),
          "'formResult->formState'" => new ValidationProperties([
            'type'        => 'string',
            'validations' => [
              'match'        => [
                'disabled', 
                'enabled', 
                'reset' 
              ]
            ]
          ])
        ], true);
        // Footer Action Property Validations
        (function () {
          $validations = [
            'enabled' => new ValidationProperties([
              'type' => 'boolean'
            ]),
            'content' => new ValidationProperties([
              'type'        => 'string',
              'validations' => [
                'range' => [
                  'min' => 1,
                  'max' => 32
                ]
              ]
            ]),
            'title' => new ValidationProperties([
              'type'        => 'boolean|string',
              'validations' => [
                'range' => [
                  'min' => 1,
                  'max' => 128
                ]
              ]
            ]),
            'tooltip' => new ValidationProperties([
              'type' => 'array'
            ]),
            'tooltip->content' => new ValidationProperties([
              'type' => 'boolean|string'
            ]),
            'tooltip->pos' => new ValidationProperties([
              'type' => 'string'
            ]),
            'tooltip->align' => new ValidationProperties([
              'type' => 'boolean|string'
            ]),
            'tooltip->delay' => new ValidationProperties([
              'type' => 'string|number'
            ]),
            'tooltip->name' => new ValidationProperties([
              'type' => 'boolean|string'
            ]),
            'confirmation' => new ValidationProperties([
              'type' => 'array'
            ]),
            'confirmation->required' => new ValidationProperties([
              'type' => 'boolean'
            ]),
            'confirmation->title' => new ValidationProperties([
              'type' => 'boolean|string'
            ]),
            'confirmation->body' => new ValidationProperties([
              'type'              => 'boolean|string'
            ]),
            'confirmation->requireResponseData' => new ValidationProperties([
              'type'              => 'boolean',
              'value'             => false
            ]),
            'confirmation->actions' => new ValidationProperties([
              'type' => 'array'
            ]),
            'confirmation->actions->deny' => new ValidationProperties([
              'type' => 'array'
            ]),
            'confirmation->actions->deny->name' => new ValidationProperties([
              'type' => 'boolean|string'
            ]),
            'confirmation->actions->deny->tooltip' => new ValidationProperties([
              'type' => 'boolean|string'
            ]),
            'confirmation->actions->deny->color' => new ValidationProperties([
              'type' => 'boolean|string'
            ]),
            'confirmation->actions->approve' => new ValidationProperties([
              'type' => 'array'
            ]),
            'confirmation->actions->approve->name' => new ValidationProperties([
              'type' => 'boolean|string'
            ]),
            'confirmation->actions->approve->tooltip' => new ValidationProperties([
              'type' => 'boolean|string'
            ]),
            'confirmation->actions->approve->color' => new ValidationProperties([
              'type' => 'boolean|string'
            ]),
            'requiresModify' => new ValidationProperties([
              'type' => 'boolean'
            ]),
            'object' => new ValidationProperties([
              'type' => 'object'
            ]),
            'classes' => new ValidationProperties([
              'type' => 'array'
            ]),
            'attributes' => new ValidationProperties([
              'type' => 'array'
            ]),
          ];
  
          foreach ($this->formFooter['actions'] as $action => $properties) {
            $actionValidations = [];
  
            foreach ($validations as $property => $validationProperties) {
              $actionValidations["'formFooter->actions->{$action}->{$property}'"] = $validationProperties;
            }
  
            $this->updateProperty('internalProperties->propertyValidations', $actionValidations, true);
          }
        })();
        
        $this->construct($properties);
  
        // FormBase htmlBindings
        $this->updateProperty('internalProperties->htmlBindings', [
          'classes' => [
            "'formProperties->showAlerts'" => [
              'condition' => (function () {
                $value = &$this->formProperties['showAlerts'];
        
                return $value === false;
              })()
            ],
            "'formProperties->spacing'" => (function () {
              $classes = [];
              $spacing = [
                'none'     => 'no-spacing',
                'vertical' => 'vertical-spacing',
                'double'   => 'double-spacing'
              ];
      
              foreach ($spacing as $value => $class) {
                $classes[] = [
                  'name'      => $class,
                  'condition' => (function () use (&$value) {
                    $property = &$this->formProperties['spacing'];
      
                    return $property == $value;
                  })()
                ];
              }
      
              return $classes;
            })(),
            "'formFooter->actions->detailsToggle->hideByDefault'" => [
              'condition' => &$this->formFooter['actions']['detailsToggle']['hideByDefault']
            ]
          ]
        ], true);
        // Footer Actions that require modify
        (function () {
          $actionRequirements = [
            'confirmation' => [
              'attribute'     => 'data-require-confirmation',
              'property'      => 'confirmation->required'
            ],
            'modify' => [
              'attribute'     => 'data-require-modify',
              'property'      => 'requiresModify'
            ]
          ];
          $actions = $this->formFooter['actions'];
  
          foreach ($actions as $action => $props) {
            if ($action != 'detailsToggle') {
              foreach ($actionRequirements as $requirements) {
                $this->internalProperties['htmlBindings']['attributes']["'formFooter->actions->{$action}->{$requirements['property']}'"] = [
                  'name'  => $requirements['attribute'],
                  'value' => (function () use ($actions, $requirements) {
                    $matchingActions = [];
        
                    foreach ($actions as $subAction => $subProps) {
                      if ($subAction != 'detailsToggle') {
                        if ($this->findReferencedProperty("formFooter->actions->{$subAction}->{$requirements['property']}") === true) {
                          $matchingActions[] = $subAction;
                        }
                      }
                    }
    
                    return implode(', ', $matchingActions);
                  })()
                ];
              }
            }
          }
        })();
  
        $this->formSubmit['response'] = new ResponseObject();
  
        // Auth Fields
        (function () {
          // Section
          $auth = $this->addChild('section', [
            'properties'   => [
              'name'          => '_auth',
              'hidden'        => true,
              'customHTML'    => [
                'classes'        => [ 'auth' ]
              ]
            ],
            'inputProperties' => [
              'validations'      => [
                'required'         => true,
                'readonly'         => true
              ]
            ]
          ]);
          // Token
          $auth->addChild('field', [
            'properties'     => [
              'name'            => 'token'
            ],
            'inputProperties' => [
              'type'            => 'text',
              'value'           => $_SESSION['token']
            ]
          ]);
          // Timestamp
          $auth->addChild('field', [
            'properties'     => [
              'name'            => 'timestamp'
            ],
            'inputProperties' => [
              'type'            => 'number',
              'value'           => $_SESSION['timestamp']
            ]
          ]);
          // Honeypot
          $auth->addChild('field', [
            'properties'     => [
              'name'            => 'debug'
            ],
            'inputProperties' => [
              'type'            => 'text',
              'validations'     => [
                'required'         => false,
                'validations'      => [
                  'range'             => [
                    'is'                 => 24
                  ]
                ]
              ]
            ]
          ]);
        })();
        // Redirect Query Parameter
        (function () {
          $state = (function () {
            $enabled = &$this->formResult['redirect']['enabled'];
            $useQueryParam = &$this->formResult['redirect']['useQueryParam'];
  
            return $enabled && $useQueryParam;
          })();
  
          if ($state) {
            $this->addChild('field', [
              'properties'      => [
                'name'     => '_continue',
                'hidden'   => true,
                'disabled' => (function () {
                  $enabled = &$this->formResult['redirect']['enabled'];
                  $useQueryParam = &$this->formResult['redirect']['useQueryParam'];
        
                  return !($enabled && $useQueryParam);
                })(),
              ],
              'inputProperties' => [
                'type'        => 'text',
                'value'       => decode_url($_GET['continue'] ?? $this->formResult['redirect']['location']),
                'validations' => [
                  'validations' => [
                    'isURL' => true
                  ]
                ]
              ]
            ]);
          }
        })();
        // Submit Confirmation Response Data Fields
        (function () {
          $state = (function () {
            $enabled = &$this->formFooter['actions']['submit']['confirmation']['required'];
            $requireResponseData = &$this->formFooter['actions']['submit']['confirmation']['requireResponseData'];
  
            return $enabled && $requireResponseData;
          })();
  
          if ($state) {
            $section = $this->addChild('section', [
              'properties'      => [
                'name'     => '_confirmation_response',
                'hidden'   => true,
                'disabled' => (function () {
                  $enabled = &$this->formFooter['actions']['submit']['confirmation']['required'];
                  $requireResponseData = &$this->formFooter['actions']['submit']['confirmation']['requireResponseData'];
        
                  return !($enabled && $requireResponseData);
                })()
              ],
              'inputProperties' => [
                'validations'      => [
                  'required'          => true,
                  'readonly'          => true
                ]
              ]
            ]);
  
            $section->addChild('field', [
              'properties'      => [
                'name'     => 'response'
              ],
              'inputProperties' => [
                'type'             => 'toggle-box',
                'validations'      => [
                  // 'readonly'          => true
                  'type'              => 'boolean'
                ]
              ]
            ]);
  
            $section->addChild('field', [
              'properties'      => [
                'name'     => 'explicit_response'
              ],
              'inputProperties' => [
                'type'             => 'text',
                'validations'      => [
                  'type'              => 'string',
                  'validations'       => [
                    'match'              => [
                      "focus_lost",
                      "dismissed",
                      "denied",
                      "approved",
                      "other"
                    ]
                  ]
                  // 'readonly'          => true
                ]
              ]
            ]);
            
            $section->addChild('field', [
              'properties'      => [
                'name'     => 'timestamp'
              ],
              'inputProperties' => [
                'type'             => 'text',
                'validations'      => [
                  'type'              => 'string',
                  'validations'       => [
                    'isDate'             => true
                  ]
                  // 'readonly'          => true
                ]
              ]
            ]);
          }
        })();
        // Footer
        (function () {
          $object = &$this->formFooter['object'];
          $footerState = &$this->formFooter['enabled'];
  
          if ($footerState) {
            $actions = &$this->formFooter['actions'];
            
            // Section
            $object = $this->addChild('section', [
              'properties'   => [
                'name'       => '_footer',
                'disabled'   => !$footerState,
                'hidden'     => !$footerState,
                'customHTML' => [
                  'classes' => [
                    'footer'
                  ]
                ]
              ]
            ]);
    
            foreach ($actions as $button => &$configuration) {
              $state = &$configuration['enabled'];
  
              if ($state) {
                $actions[$button]['object'] = $object->addChild('button', [
                  'properties'   => [
                    'name'          => $button,
                    'customHTML'    => [
                      'classes'        => &$configuration['classes'],
                      'attributes'     => &$configuration['attributes']
                    ],
                    'disabled'      => !$state,
                    'hidden'        => !$state,
                  ],
                  'inputProperties' => [
                    'type'             => $button == 'detailsToggle'
                                          ? 'button'
                                          : $button,
                    'value'            => $button,
                    'content'          => &$configuration['content'],
                    'title'            => &$configuration['title'],
                    'tooltip'          => &$configuration['tooltip']
                  ]
                ]);
                $actions[$button]['object']->updateProperty('internalProperties->htmlDefaults->classes', [ $button ], true);
              }
            }
          }
        })();
      }
      /**
       * Retrieve and insert the form into the page HTML
       * 
       * @return void 
       */
      public function insertForm () {
        echo $this->getElementMarkup();
      }
      /**
       * Validate the submitted form
       * 
       * @return boolean Returns **true** if the form is *valid*, or **false** if it is not. 
       */
      public function validateForm () {
        GLOBAL $_mysqli;
        
        $requestParams = (function () {
          $method = strtoupper($this->formProperties['action']['method']);
          $props = (function () use ($method) {
            if ($method == 'GET')  { return $_GET; }
            if ($method == 'POST') { return $_POST; }
          })();
  
          array_walk_recursive($props, function (&$propValue, $propName) {
            $propValue = decode_url($propValue);
          });
  
          return $props;
        })();
        $validations = (function () use ($requestParams) {
          $properties = [];
  
          $processChildren = function ($children) use (&$processChildren, $requestParams, &$properties) {
            // Update Controlled Properties & Dynamic Validations
            foreach ($children as $name => $childClass) {
              $childInputProps = $childClass->findReferencedProperty('inputProperties');
              $childProps = &$childInputProps['validations'];
              $childValue = $requestParams[$name] ?? $childInputProps['value'];
              
              if (get_class($childClass) == 'FormField' && $childProps) {
                // Update properties modified by controller
                (function () use ($childInputProps, $childValue) {
                  $hasControl = $childInputProps['hasControl'];
                  
                  if ($hasControl) {
                    $controls = is_array($hasControl) ? $hasControl : [ $hasControl ];
  
                    $updateProperties = function (&$controlledField, $controllerUpdates) {
                      foreach ($controllerUpdates as $update => $newValue) {
                        if ($update == 'value') {
                          $controlledField->updateProperty('inputProperties->value', $newValue);
                        }
                        else if ($update == 'hidden' || $update == 'disabled') {
                          $controlledField->updateProperty("properties->{$update}", $newValue);
                        }
                        else if ($update == 'required' || $update == 'readonly') {
                          $controlledField->updateProperty("inputProperties->validations->{$update}", $newValue);
                        }
                      }
                    };
      
                    foreach ($controls as $control) {
                      $condition = (function () use ($control, $childValue) {
                        $condition = $control['condition'];
      
                        if ($condition == 'hasAnyValue' && $childValue || $condition == 'notAnyValue' && $childValue === NULL) {
                          return true;
                        }
                        else {
                          $conditionType = strpos($condition, 'has') !== false
                                            ? 'has'
                                            : 'not';
                          $conditionValue = str_replace("{$conditionType}Value: ", '', $condition);
    
                          if ($conditionType == 'has' && $conditionValue === $childValue || $conditionType == 'not' && $conditionValue !== $childValue) {
                            return true;
                          }
                        }
                        
                        return false;
                      })();
      
                      if ($condition) {
                        foreach ($control['controls'] as $controlledFieldName => $controllerUpdates) {
                          $controlledField = $this->getChild($controlledFieldName);
      
                          if ($controlledField) {
                            if ($controlledField->findReferencedProperty('inputProperties->type') == 'group') {
                              $processFieldChildren = function ($parent) use (&$processFieldChildren, &$updateProperties, $controllerUpdates) {
                                foreach ($parent->findReferencedProperty('internalProperties->children') as $childName => $childObject) {
                                  $childType = $childObject->findReferencedProperty('inputProperties->type');
  
                                  $updateProperties($childObject, $controllerUpdates);
  
                                  if ($childType == 'group') {
                                    $processFieldChildren($childObject);
                                  }
                                }
                              };
  
                              $processFieldChildren($controlledField);
                            }
                            else {
                              $updateProperties($controlledField, $controllerUpdates);
                            }
                          }
                        }
                      }
                    }
                  }
                })();
                // Update Dynamic Validations
                (function () use (&$childClass, &$childProps) {
                  $dynamicValidations = $childClass->checkDynamicValidations(true);
  
                  if ($dynamicValidations) {
                    foreach ($dynamicValidations as $validation => $validationProps) {
                      $dynamicValue = $validationProps['value'];
  
                      if ($dynamicValue !== false) {
                        if (strpos($validation, 'range') !== 0) {
                          $childProps->validations[$validation] = $dynamicValue;
                        }
                        else {
                          $rangeType = str_replace('range->', '', $validation);
          
                          $childProps->validations['range'][$rangeType] = $dynamicValue;
                        }
                      }
                      else {
                        if (strpos($validation, 'range') !== 0) {
                          $childProps->validations[$validation] = NULL;
                        }
                        else {
                          $rangeType = str_replace('range->', '', $validation);
          
                          $childProps->validations['range'][$rangeType] = NULL;
                        }
                      }
                    }
                  }
                })();
              }
            }
            // Update & Retrieve Validation Properties
            foreach ($children as $name => &$childClass) {
              $childInputProps = $childClass->findReferencedProperty('inputProperties');
              $childProps = &$childInputProps['validations'];
  
              if (get_class($childClass) == 'FormField' && $childProps && $childInputProps['type'] != 'group') {
                // Value
                (function () use ($childInputProps, &$childProps) {
                  $validationValue = &$childProps->value;
  
                  if (!$validationValue) {
                    $validationValue = $childInputProps['value'];
                  }
                  if ($validationValue == '') {
                    $validationValue = null;
                  }
                })();
                
                // Remove `Required` Validation if `Disabled`
                if ($childClass->properties['disabled'] === true && $childProps->required === true) {
                  $childClass->updateProperty('inputProperties->validations->required', false);
                }
                // Add `Match` Validation if `Readonly`
                if ($childProps->readonly === true) {
                  if (preg_match('/^_auth_(?:token|timestamp|bypass)$/', $name) == 0) {
                    $childValue = $childInputProps['value'];
  
                    if ($childValue !== "") {
                      $childProps->validations['match'] = [ $childValue ];
                    }
                  }
                }
                // Remove `Pattern` Fallback Validation from DateTime Fields 
                if ($childInputProps['type'] == 'date' || $childInputProps['type'] == 'time') {
                  if ($childProps->validations['pattern']) {
                    unset($childProps->validations['pattern']);
                  }
                }
  
                // Multiple Choice Selection
                (function () use ($childInputProps, &$childProps) {
                  $allowedTypes = [ 
                    'checkbox', 
                    'radio', 
                    'select', 
                    'toggle-button', 
                    'toggle-box' 
                  ];
                  $type = $childInputProps['type'];
  
                  if (array_search($type, $allowedTypes) !== false) {
                    $options = $childInputProps['options'];
          
                    if (!empty($options)) {
                      $childProps->validations['match'] = array_keys($options);
                    }
                  }
                })();
                
                if ($childInputProps['type'] == 'toggle-box') {
                  $childProps->type = 'boolean';
                }
  
                $properties[$name] = $childProps;
              }
  
              $processChildren($childClass->internalProperties['children']);  
            }
          };
          
          $processChildren($this->internalProperties['children']);
  
          return $properties;
        })();
        $this->formSubmit['validations'] = $validations;
        $checkedParams = check_parameters($requestParams, $validations);
        $parsedParams = (function () use ($checkedParams) {
          $parameters = [];
  
          $parseFields = function ($fields, &$parameterList, $parentName = false) use (&$parseFields, $checkedParams) {
            foreach ($fields as $name => $childClass) {
              $className = get_class($childClass);
              $parsedName = $parentName !== false
                            ? preg_replace("/({$parentName})(_){0,1}/", '', $name, 1)
                            : $name;
  
              if ($className == 'FormField' && $childClass->findReferencedProperty('inputProperties->type') != 'group') {
                $parameterList[$parsedName] = $checkedParams['parameters'][$name];
              }
              else if ($className == 'FormSection' || $childClass->findReferencedProperty('inputProperties->type') == 'group') {
                $parameterList[$parsedName] = [];
  
                $parseFields($childClass->findReferencedProperty('internalProperties->children'), $parameterList[$parsedName], $name);
  
                if ($parameterList[$parsedName] == []) {
                  unset($parameterList[$parsedName]);
                }
              }
            }
          };
          
          $parseFields($this->internalProperties['children'], $parameters);
  
          return $parameters;
        })(); 
  
        // Check auth parameters
        (function () use (&$_mysqli, $parsedParams) {
          $hasReportedError = false;
          $authValidationError = function ($error, $serverParam, $providedParam) use (&$_mysqli, &$hasReportedError) {
            if (!$hasReportedError) {
              $queryParams = [
                'ip'             => inet_pton($_SERVER['REMOTE_ADDR']),
                'timestamp'      => getFormattedTimestamp(),
                'type'           => $error,
                'value_required' => $serverParam,
                'value_provided' => $providedParam
              ];
              $query = "INSERT INTO logs_invalid_form_requests
                       (" . implode(', ', array_keys($queryParams)) . ")
                       VALUES(" . substr(str_repeat("?, ", count(array_values($queryParams))), 0, -2) . ")";
              $result = $_mysqli->prepared_query($query, str_repeat("s", count(array_values($queryParams))), array_values($queryParams));
  
              $hasReportedError = true;
  
              $this->formSubmit['success'] = false;
              $this->formSubmit['errors'][] = errorObject('invalidRequest', null, 'Something was wrong with your request. Please <a class="styled" href=" " title="Refresh the current page" aria-label="Refresh the current page">refresh</a> the page and try again.');
              $this->formSubmit['response']->set(-2);
           
              if (!$result) {
                error_log("Form Validation Error: Failed to record invalid form request for \"{$_SERVER['REMOTE_ADDR']}\".");
              }
            }
          };
  
          // Auth Token
          (function () use ($parsedParams, $authValidationError) {
            $server = $_SESSION['token'] ?? false;
            $provided = $parsedParams['_auth']['token'] ?? false;
            $isValid = $server
                          && $provided
                          && auth_strHashCheck($server, $provided);
  
            if (!$isValid) {
              $authValidationError('invalidToken', $server, $provided);
            }
          })();
          // Auth Honeypot
          (function () use ($parsedParams, $authValidationError) {
            $field = $parsedParams['_auth']['debug'] ?? null;
            $isValid = !isset($field);
  
            if (!$isValid) {
              $authValidationError('invalidBypass', null, $field);
            }
          })();
          // Auth Timestamp
          (function () use ($parsedParams, $authValidationError) {
            $server = $_SESSION['timestamp'] ?? false;
            $provided = $parsedParams['_auth']['timestamp'] ?? false;
            $threshold = (function () {
              $now = new DateTime();
              $now->sub(new DateInterval('PT3S'));
    
              return $now->getTimestamp();
            })();
            $isValid = $server
                          && $provided
                          && $provided <= $threshold;
  
            if (!$isValid) {
              if (!($server && $provided)) {
                $authValidationError('invalidTimestamp', $server, $provided);
              }
              else if ($provided > $threshold) {
                $authValidationError('tooQuickSubmit', $threshold, $provided);
              }
            }
          })();
        })();
        // Check provided parameters
        (function () use ($checkedParams) {
          if ($this->formSubmit['success']) {
            if (!$checkedParams['valid']) {
              $this->formSubmit['success'] = false;
              $this->formSubmit['response']->set(-1);
            }
  
            foreach ($checkedParams['warnings'] as $warning) {
              $this->formSubmit['warnings'][] = $warning;
            }
            foreach ($checkedParams['errors'] as $error) {
              $this->formSubmit['errors'][] = $error;
            }
          } 
        })();
  
        $this->formSubmit['parameterList'] = $checkedParams['parameters'];
        $this->formSubmit['parameters'] = $parsedParams;
  
        return $this->formSubmit['success'];
      }
      /**
       * Invalidate the current form request
       * 
       * @param int $error_code Indicates an `ERROR_CODES` error number to be sent as the HTTP Status Code for the request.
       * @param string $error If provided, this is an error that is added to the `errors` payload of the response.
       * @return boolean Returns **true** on success, or **false** on failure.
       */
      public function invalidateRequest ($error_code = -1, $error = null) {
        $response = &$this->formSubmit['response'];
        $success = &$this->formSubmit['success'];
  
        $response->set($error_code);
        $success = false;
  
        if (isset($error)) {
          $response->setError($error);
        }
      }
      /**
       * Generate the form's response object
       * 
       * @return object Returns the form's response object. 
       */
      public function buildResponse () {
        $props = &$this->formSubmit;
        $response = &$props['response'];
  
        // Form Payload
        (function () use (&$response, $props) {
          $formPayload = [
            'result'  => $props['success'],
            'actions' => array_fill_keys(['toast', 'redirect', 'modal'], false)
          ];
          
          if ($props['success']) {
            $result = $this->formResult;
  
            // Toast
            (function () use ($result, &$formPayload) {
              $toast = $result['toast'];
    
              if ($toast['enabled']) {
                $defaultProps = [
                  'settings' => [
                    'id'       => "{$this->internalProperties['id']}_response_toast",
                    'template' => 'formSuccess'
                  ]
                ];
                $method = strtolower($toast['method']);
                $toast = array_replace_recursive($defaultProps, $toast['properties']);
    
                if ($method == 'response') {
                  $formPayload['actions']['toast'] = $toast;
                }
                else if ($method == 'session') {
                  $_SESSION['toasts'][] = $toast;
                }
              }
            })();
            // Redirect
            (function () use ($result, &$formPayload) {
              $redirect = $result['redirect'];
    
              if ($redirect['enabled']) {
                $formPayload['actions']['redirect'] = [
                  'delay'    => $redirect['delay'],
                  'location' => (function () use ($redirect) {
                    $url = (function () use ($redirect) {
                      $useRedirectField = $redirect['useQueryParam'];
    
                      if ($useRedirectField && isset($_POST['_continue'])) {
                        return $_POST['_continue'];
                      }
                      else if ($useRedirectField && isset($_GET['continue'])) {
                        return $_GET['continue'];
                      }
                      else {
                        return $redirect['location'];
                      }
                    })();
  
                    return decode_url($url);
                  })()
                ];
              }
            })();
            // Modal
            (function () use ($result, &$formPayload) {
              $modal = $result['modal'];
    
              if ($modal['enabled']) {
                $formPayload['actions']['modal'] = true;
              }
            })();
          }
  
          $response->setPayload($formPayload, 'form');
        })();
  
        if ($props['success']) {
          if ($response->status_code === null) {
            $response->set(1);
          }
        }
  
        // Process Warnings & Updates
        foreach ([ 'warnings', 'errors' ] as $messageType) {
          foreach ($props[$messageType] as $messageProps) {
            $message = (function () use ($messageType, $messageProps) {
              $message = FORM_VALIDATION_MESSAGES[$messageProps['type']] ?? false;
  
              if ($message) {
                if (is_array($message)) {
                  $parameter = $this->getChild($messageProps['parameter']);
                  $message = $message[$parameter->findReferencedProperty('inputProperties->type')] ?? false;
                  
                  if ($message) {
                    // Variable replacements
                    (function () use (&$message, $messageProps, $parameter) {
                      $label = (function () use ($messageProps, $parameter) {
                        if (isset($messageProps['parameter'])) {
                          // $parameter = $this->getChild($messageProps['parameter']);
                
                          if ($parameter) {
                            $label = $parameter->findReferencedProperty('content->title');
                
                            if ($label) {
                              return $label;
                            }
                          }
                        }
    
                        return false;
                      })();
  
                      $message = str_replace('${field}', $label !== false ? $label : 'Field', $message);
    
                      if (strpos($message, '${threshold}') !== false) {
                        $threshold = (function () use ($parameter, $messageProps) {
                          $range = $parameter->findReferencedProperty('inputProperties->validations->validations->range');
    
                          if ($messageProps['type'] == 'rangeUnderflow') { return $range['min']; }
                          if ($messageProps['type'] == 'rangeMismatch')  { return $range['is']; }
                          if ($messageProps['type'] == 'rangeOverflow')  { return $range['max']; }
                        })();
    
                        $message = str_replace('${threshold}', $threshold, $message);
    
                        if (strpos($message, '${plural}') !== false) {
                          $plural = checkPlural($threshold);
      
                          $message = str_replace('${plural}', $plural, $message);
                        }
                      }
                    })();
                    
                    return $message;
                  }
                }
              }
  
              return $messageProps['message'];
            })();
  
            $messageProps['message'] = $message;
  
            if ($messageType == 'warnings') {
              $response->setWarning($messageProps);
            }
            else if ($messageType == 'errors') {
              $response->setError($messageProps);
            }
          }
        }
        
        return $response;
      }
      /**
       * Retrieve the HTML Markup for a Form Alert
       * 
       * @param string $message The message body of the alert. 
       * - The `strong`, `b`, `em`, `i`, & `a` HTML tags are permitted.
       * @param "info"|"warning"|"error" $type The type of alert to be generated.
       * @return string|false Returns the HTML Markup of the Form Alert, or **false** if an error occurred.
       */
      static function buildAlert ($message = "", $type = 'warning') {
        if (array_search($type, ['info', 'warning', 'error']) === false) {
          trigger_error("formBase::buildAlert Error: \"{$type}\" is not a valid option for \"type\".");
          return false;
        }
  
        $icons = [
          'info'    => "fa-info-circle",
          'warning' => 'fa-exclamation-triangle',
          'error'   => 'fa-exclamation-circle'
        ];
  
        /** @var string The alert markup string */
        $markup = "";
  
        $markup .= "<div class=\"alert {$type}\">
                      <span class=\"icon\">
                        <span class=\"fas {$icons[$type]}\"></span>
                      </span>
                      <span class=\"message\">";
        $markup .=      clean_html($message, '<strong><b><em><i><a><br><ul><ol><li>');
        $markup .= "  </span>
                    </div>";
  
        return $markup;
      }
      /**
       * Retrieve the HTML Markup for the form
       * 
       * @return string|false Returns the form's *HTML Markup string* on success, or **false** if an error occurred.
       */
      protected function getElementMarkup () {
        /** @var string The form's *HTML Markup string* */
        $markup = "";
  
        // Form Base
        (function () use (&$markup) {
          $markup .= "<form {$this->getAttributeMarkup()}>";
          $markup .= '<div class="content-container">';
        })();
        // Form Header
        $markup .= $this->getHeaderMarkup();
        // Children
        $markup .= $this->getChildrenMarkup(false);
        // Confirmation Modal Properties
        (function () use (&$markup) {
          $getConfirmationProps = function ($eventType) {
            $parent = &$this->formFooter['actions'][$eventType]['confirmation'];
            $eventProps = [
              'title'           => &$parent['title'],
              'body'            => &$parent['body'],
              'deny_name'       => &$parent['actions']['deny']['name'],
              'deny_tooltip'    => &$parent['actions']['deny']['tooltip'],
              'deny_color'      => &$parent['actions']['deny']['color'],
              'approve_name'    => &$parent['actions']['approve']['name'],
              'approve_tooltip' => &$parent['actions']['approve']['tooltip'],
              'approve_color'   => &$parent['actions']['approve']['color']
            ];
            $props = [];
    
            foreach ($eventProps as $eventProp => $eventPropVal) {
              if ($eventPropVal !== false) {
                if (strpos($eventProp, '_') !== false) {
                  $action = (function () use ($eventProp) {
                    $matches = [];
    
                    preg_match('/^[^_]+/', $eventProp, $matches);
    
                    return $matches[0];
                  })();
                  $actionProp = (function () use ($eventProp) {
                    $matches = [];
    
                    preg_match('/[^_]+$/', $eventProp, $matches);
    
                    return $matches[0];
                  })();
    
                  if (!isset($props['actions'])) {
                    $props['actions'] = [];
                  }
                  if (!isset($props['actions'][$action])) {
                    $props['actions'][$action] = [];
                  }
    
                  $props['actions'][$action][$actionProp] = $eventPropVal;
                }
                else {
                  $props[$eventProp] = $eventPropVal;
                }
              }
            }
    
            return $props;
          };
          $properties = [
            'reset'  => $getConfirmationProps('reset'),
            'submit' => $getConfirmationProps('submit')
          ];
  
          foreach ($properties as $eventType => $props) {
            if (count($props) > 0) {
              $encodedProps = clean_all_html(json_encode($props));
              // $encodedProps = json_encode($props);
  
              $markup .= "<div class=\"{$eventType} confirmation-properties\" hidden>{$encodedProps}</div>";
            }
          }
        })();
        // Closing Form Base
        (function () use (&$markup) {
          $markup .= '</div>'; // Closing form content wrapper tag
          $markup .= '</form>'; // Closing form tag
        })();
        // Markup cleanup
        (function () use (&$markup) {
          $allowedTags = '<div><span><p><ul><ol><li><strong><em><b><i><a><code><pre><br><form><fieldset><label><legend><input><select><option><button><textarea><dl><dt><dd>';
  
          $markup = clean_html($markup, $allowedTags);
          $markup = collapseWhitespace($markup);
        })();
  
        return $markup;
      }
    }
    /** A section in a form */
    class FormSection extends FormCore {
      use FormChild;
  
      /**
       * @var array Internal form section properties
       */
      protected $_internalProperties = [
        'htmlBindings' => [
          'classes'    => [
            "'properties->size'" => [
              [ 'name'  => 'inheritProperty' ],
              [ 'name'  => 'inheritValue' ]
            ],
            "'inputProperties->type'" => [
              'condition'                => false
            ]
          ],
          'attributes' => [
            "'properties->name'" => [
              'name'                => 'data-nested',
              'value'               => 'inheritValue'
            ]
          ]
        ],
        'htmlDefaults'  => [
          'classes' => [
            'section'
          ]
        ],
        'success'      => true,
        'response'     => null,
        'warnings'     => [],
        'errors'       => []
      ];
  
      /**
       * Initialize the form section class 
       * 
       * @param array $properties An array of properties that are to be passed to the section.
       * @return void
       */
      public function __construct ($properties) {
        $this->construct($properties);
      }
      /**
       * Retrieve the HTML Markup for the form section
       * 
       * @return string|false Returns the form section's *HTML Markup string* on success, or **false** if an error occurred.
       */
      protected function getElementMarkup () {
        /** @var string The form section's *HTML Markup string* */
        $markup = "";
        $formFooter = $this->properties['name'] == '_footer'
                      ? $this->internalProperties['form']->findReferencedProperty('formFooter')
                      : false;
  
        // Section Base
        $markup .= "<div {$this->getAttributeMarkup()}>";
        // Section Header
        $markup .= $this->getHeaderMarkup($formFooter ? [] : false);
  
        if ($formFooter['showProgress'] ?? false) {
          $markup .= '<div class="progress-bar full-animation show-cursor" role="progressbar">
                        <span class="progress disable-theme-transitions"></span>
                        <span class="cursor">0%</span>
                      </div>';
        }
  
        // Children
        if (!$formFooter) {
          $markup .= $this->getChildrenMarkup(false);
        }
        else {
          $markup .= "<div class=\"actions\">
                        {$this->getChildrenMarkup(false)}
                      </div>";
        }
        if ($formFooter['showChangeCount'] ?? false && $formFooter['actions']['submit']['enabled']) {
          $markup .= '<i class="change-count" hidden>0 Unsaved Changes</i>';
        }
        // Closing Section Base
        $markup .= '</div>';
        // Markup cleanup
        (function () use (&$markup) {
          $allowedTags = '<div><span><p><ul><ol><li><strong><em><b><i><a><code><pre><br><fieldset><label><legend><input><select><option><button><textarea>';
  
          $markup = clean_html($markup, $allowedTags);
          $markup = collapseWhitespace($markup);
        })();
  
        return $markup;
      }
    }
    /** A field in a form */
    class FormField extends FormCore {
      use FormChild, FormInput;
  
      const CHARACTER_COUNTER_TEMPLATE = <<<EOT
        <div class="tool character-counter layer-target">
          <span class="now">{now}</span>
          <span class="separator">&nbsp;/&nbsp;</span>
          <span class="threshold">{threshold}</span>
        </div>
        <div class="layer tooltip">
          <strong>{now}</strong> characters used out of <strong>{threshold}</strong> {descriptor}
        </div>
      EOT;
      protected $_internalProperties = [
        'htmlBindings' => [
          'classes' => [
            "'inputProperties->type'" => [
              'name' => 'inheritValue'
            ],
            "'inputProperties->validations->validations->range'" => [
              'name' => 'has-character-counter'
            ]
          ],
          'attributes' => [
            "'inputProperties->validations->type'" => [
              'name' => 'data-value-type'
            ],
            "inputProperties->toolbar->textTransform" => [
              'name'  => 'data-text-transform'
            ]
          ]
        ],
        'htmlDefaults' => [
          'classes' => [
            'input'
          ]
        ]    
      ];
      /**
       * @var array Visual content that is added to the markup.
       * - `boolean $innerTitle` - Indicates if the title should be displayed inside of the field, moved up to the border when focused. Only applies to text-based fields.
       */
      protected $_content = [
        'innerTitle' => false
      ];
      /**
       * @var array Properties that define and control the form field.
       * - `false|string $placeholder` — The placeholder or toggle value to be displayed in the field.
       * - - For some *Input Fields*, this value is used as the *placeholder* in the field.
       * - - For *Toggle Fields*, this value is used as the *label* of the toggle button.
       * - `string $autocomplete` — Indicates if and how form fields can be filled in by the browser. [Available Values](https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes/autocomplete#Values)
       * - `false|"none"|"text"|"url"|"email"|"numeric"|"search" $inputMode` — Hints to the browser which virtual keyboard configuration to use.
       * - - Only applies to _Character-based Input Fields_.
       * - - If **false** is provided, a preset value may be used based on the value of `type`.
       * - `array $options` — An associative array of options for *Multiple Choice Fields*. 
       * - - _Has no effect on **Input Fields**_.
       * - - `string $key` — The *name* or *key* of the option as it is to be sent when submitting the form.
       * - - `string $value` — The *display name* of the option as it is to be presented to the user.
       * - `false|"half"|"third"|"quarter" $wrapOptions` — Indicates if and how multiple-choice options should wrap on wider devices.
       * - `array $toolbar` — Additional functionality applicable to certain fields.
       * - - `boolean $characterCounter` — Indicates if the Character Counter is to be displayed for the field.
       * - - - Only works on text-based fields with set character requirements or limits.
       * - - `boolean $passwordVisibilityToggle` — Indicates if the *password visibiltiy toggle* is to be rendered.
       * - - - Only applies to `password` fields. 
       * - - `boolean $markdown` — Indicates if and how Markdown is supported for the field. 
       * - - - Only applies to _textarea_ fields.
       * - - - Currently, this only enables the _Markdown Helper_ toolbar tool.
       * - - `false|"lowercase"|"uppercase"|"words" — Indicates if the value of text fields should be automatically transformed:
       * - - - **false**: No transformation will occur.
       * - - - *lowercase*: The value will be entirely lowercase.
       * - - - *uppercase*: The value will be entirely uppercase.
       * - - - *words*: Each word in the value will be capitalized.
       * - - `false|array $dynamicFill` — Indicates how the value of text fields can be automatically filled in with certain characters.
       * - - - *match*: The RegExp pattern to match for the fill.
       * - - - *fill*: The replacement pattern for the fill. Variables ($1, $2, etc...) refer to captured groups within the *matches*. 
       * - `false|array $hasControl` — Identifies how a field can control other form elements. Each array entry indicates a *control scheme* made up of the following properties:
       * - - `string $condition` — Indicates the condition in which the field can control the following form element(s).
       * - - - `hasAnyValue`: The field _must_ have _any_ value or input.
       * - - - `notAnyValue`: The field _must not_ have _any_ value or input.
       * - - - `hasValue: *value*`: The field _must_ have the _provided_ value or input.
       * - - - `notValue: *value*`: The field _must not_ have the _provided_ value or input.
       * - - `array $controls`: A list of form elements that are controlled by the field. Each array entry is made up of the following properties"
       * - - - The **key** refers to the _Section_ or _Field_ name of the element being controlled.
       * - - - The **value** is an `array` of updates that are to be performed on the form element. Each update is structured as follows:
       * - - - - The **key** refers to the *option* to set. Valid options include `disabled`, `hidden`, `readonly`, and `required`.
       * - - - - The **value** is a `boolean` that indicates the state of the option.
       */
      protected $_inputProperties = [
        'placeholder'  => false,
        'autocomplete' => 'off',
        'inputMode'    => false,
        'options'      => [],
        'wrapOptions'  => false,
        'toolbar'      => [
          'characterCounter'         => true,
          'passwordVisibilityToggle' => false, 
          'clearFieldButton'         => false,
          'markdown'                 => false,
          'textTransform'            => false,
          'dynamicFill'              => false
        ],
        'hasControl'   => false
      ];
  
      /**
       * Initialize the form field class 
       * 
       * @param array $properties An array of properties that are to be passed to the field.
       * @return void
       */
      public function __construct ($properties) {
        // FormField Property Validations
        $this->updateProperty('internalProperties->propertyValidations', [
          "'content->innerTitle'" => new ValidationProperties([
            'type'        => 'boolean'
          ]),
          "'inputProperties->placeholder'" => new ValidationProperties([
            'type'              => 'boolean|string'
          ]),
          "'inputProperties->autocomplete'" => new ValidationProperties([
            'type'        => 'boolean|string'
          ]),
          "'inputProperties->inputMode'" => new ValidationProperties([
            'type'        => 'boolean|string',
            'validations' => [
              'match'        => [
                false,
                "none",
                "text",
                "url",
                "email",
                "numeric",
                "search"
              ]
            ]
          ]),
          "'inputProperties->options'" => new ValidationProperties([
            'type'        => 'array'
          ]),
          "'inputProperties->wrapOptions'" => new ValidationProperties([
            'type'        => 'boolean|string',
            'validations' => [
              'match'        => [
                false,
                'half',
                'third',
                'quarter'
              ]
            ]
          ]),
          "'inputProperties->toolbar'" => new ValidationProperties([
            'type'        => 'array'
          ]),
          "'inputProperties->toolbar->characterCounter'" => new ValidationProperties([
            'type'        => 'boolean'
          ]),
          "'inputProperties->toolbar->passwordVisibilityToggle'" => new ValidationProperties([
            'type'        => 'boolean'
          ]),
          "'inputProperties->toolbar->clearFieldButton'" => new ValidationProperties([
            'type'        => 'boolean'
          ]),
          "'inputProperties->toolbar->markdown'" => new ValidationProperties([
            'type'        => 'boolean|string'
          ]),
          "'inputProperties->toolbar->textTransform'" => new ValidationProperties([
            'type'        => 'boolean|string',
            'validations' => [
              'match'        => [
                false,
                'lowercase',
                'uppercase',
                'words'
              ]
            ]
          ]),
          "'inputProperties->toolbar->dynamicFill'" => new ValidationProperties([
            'type'        => 'boolean|array'
          ]),
          "'inputProperties->toolbar->dynamicFill->match'" => new ValidationProperties([
            'type'        => 'string'
          ]),
          "'inputProperties->toolbar->dynamicFill->fill'" => new ValidationProperties([
            'type'        => 'string'
          ]),
        ], true);
        
        $this->construct($properties);
  
        // FormField htmlBindings
        $this->updateProperty("internalProperties->htmlBindings", [
          'classes'    => [
            "'inputProperties->validations->validations->range'" => [
              'condition' => (function () {
                $supportsCounter = array_search($this->inputProperties['type'], FORM_FIELD_TYPES['toolbar']) !== false;
                $hasRange = isset($this->inputProperties['validations']->validations['range']);
        
                return $supportsCounter && $hasRange;
              })(),
            ]
          ],
          'attributes' => [
            // "'inputProperties->hasControl'" => [
            //   'name'  => 'data-has-control',
            //   'value' => json_encode($this->findReferencedProperty('inputProperties->hasControl'))
            // ],
            "'inputProperties->toolbar->dynamicFill'" => [
              'name'  => 'data-dynamic-fill',
              'value' => json_encode($this->findReferencedProperty('inputProperties->toolbar->dynamicFill'))
            ]
          ]
        ], true);
        if (array_search($this->inputProperties['type'], FORM_FIELD_TYPES['toolbar']) !== false) {
          $this->updateProperty("internalProperties->htmlDefaults->attributes", [
            'data-previous-length' => strlen($this->inputProperties['value']) 
          ], true);
        }
  
        // Type-Specific Updates
        (function () {
          $type = $this->inputProperties['type'];
  
          if ($type == 'checkbox' && $this->inputProperties['validations']->type == 'any') {
            $this->inputProperties['validations']->type = 'array';
          }
          if ($type == 'toggle-button' || $type == 'toggle-box') {
            $options = (function () use ($type) {
              $defaultLabel = "";
  
              if ($this->inputProperties['value'] !== "") {
                $defaultLabel = &$this->inputProperties['value'];
              }
              else if ($this->content['title'] !== false) {
                $defaultLabel = &$this->content['title'];
              }
  
              if ($type == 'toggle-button') {
                return [
                  'off' => '',
                  'on'  => &$defaultLabel
                ];
              }
              else {
                return [
                  'false' => '',
                  'true'  => &$defaultLabel
                ];
              }
            })();
  
            // $this->inputProperties['options'] = $options;
          }
          if ($type == 'datetime' || $type == 'datetimetz') {
            $datePieces = [
              'date' => [
                'display' => 'Date',
              ],
              'time' => [
                'display' => 'Time',
              ],
              'tz' => [
                'display' => 'Timezone',
              ]
            ];
  
            $this->inputProperties['type'] = 'group';
  
            // Individual Piece Properties
            (function () use (&$datePieces) {
              $properties = [
                'inputProperties->value',
                'inputProperties->placeholder',
                'inputProperties->validations'
              ];
  
              $checkProperty = function ($property) use (&$checkProperty, &$datePieces) {
                $propertyValue = $this->findReferencedProperty($property);
  
                if ($propertyValue !== NULL) {
                  if (is_array($propertyValue) && is_array_associative($propertyValue) || is_object($propertyValue)) {
                    $hasValidPieces = false;
  
                    if (is_array($propertyValue)) {
                      foreach ($datePieces as $piece => &$pieceProperties) {
                        $pieceProperty = $propertyValue[$piece] ?? false;
                        
                        if ($pieceProperty !== false) {
                          $hasValidPieces = true;
                          $pieceProperties[$property] = $pieceProperty;
                        }
                      } 
                    }
  
                    if ($hasValidPieces) {
                      $this->updateProperty($property, NULL, true);
                    }
                    else {
                      foreach ($propertyValue as $nestedKey => $nestedValue) {
                        $checkProperty("${property}->{$nestedKey}");
                      }
                    }
                  }
                }
              };
  
              foreach ($properties as $propertyString) {
                $checkProperty($propertyString);
              }
            })();
  
            foreach ($datePieces as $piece => $properties) {
              if (strpos($type, $piece) !== false) {
                $child = $this->addChild('field', [
                  'properties' => [
                    'name'        => $piece,
                    'size'        => $piece == 'tz'
                                     ? 'full'
                                     : 'half'
                  ],
                  'content'    => [
                    'title'       => "{$this->content['title']} ({$properties['display']})",
                    'hideTitle'   => true
                  ],
                  'inputProperties' => [
                    'type'             => $piece == 'tz'
                                          ? 'select'
                                          : $piece, 
                    'options'          => $piece == 'tz'
                                       ? (function () use ($piece) {
                                         if ($piece == 'tz') {
                                           $providedOptions = $this->inputProperties['options'];
                                           
                                           if ($providedOptions) {
                                             $options = [];
                         
                                             foreach ($providedOptions as $providedTimezone) {
                                               $timezone = DATE_TIMEZONES[$providedTimezone] ?? false;
                         
                                               if ($timezone) {
                                                 $options[$providedTimezone] = $timezone;
                                               }
                                             }
                         
                                             return $options;
                                           }
                                           else {
                                             return DATE_TIMEZONES;
                                           }
                                         }
                                         else {
                                           return [];
                                         }
                                        })()
                                       : [],
                    'validations'      => [
                      'type'              => $piece == 'date' ? 'date' : 'string'
                    ]
                  ]
                ]);
  
                if ($piece == 'tz') {
                  $options = (function () use ($piece) {
                    if ($piece == 'tz') {
                      $providedOptions = $this->inputProperties['options'];
                      
                      if ($providedOptions) {
                        $options = [];
    
                        foreach ($providedOptions as $providedTimezone) {
                          $timezone = DATE_TIMEZONES[$providedTimezone] ?? false;
    
                          if ($timezone) {
                            $options[$providedTimezone] = $timezone;
                          }
                        }
    
                        return $options;
                      }
                      else {
                        return DATE_TIMEZONES;
                      }
                    }
                    else {
                      return [];
                    }
                  })();
  
                  $child->updateProperty('inputProperties->options', $options, true);
                }
  
                foreach ($properties as $piecePropName => $piecePropValue) {
                  if ($piecePropName != 'display') {
                    $child->updateProperty($piecePropName, $piecePropValue, true);
                  }
                }
  
                if (!isset($datePieces['required'])) {
                  $child->updateProperty('inputProperties->validations->required', $this->findReferencedProperty('inputProperties->validations->required'));
                }
              }
            }
          }
          if ($type == 'date') {
            $this->inputProperties['validations']->validations['pattern'] = '%[2-3]{1}[0-9]{3}-([0-9]{1}|1[0-2]{1})-([0-9]{1}|[0-3]{1}[0-9]{1})%';
          }
          if ($type == 'time') {
            $this->inputProperties['validations']->validations['pattern'] = '%([0-9]{1}|[0-2]{1}[0-9]{1}):[0-5]{1}[0-9]{1}:([0-9]{1}|[0-5]{1}[0-9]{1}){0,1}%';
          }
        })();
      }
      /**
       * Retrieve the HTML Markup for the form field
       * 
       * @return string|false Returns the form field's *HTML Markup string* on success, or **false** if an error occurred.
       */
      protected function getElementMarkup () {
        /** @var string The form field's *HTML Markup string* */
        $markup = "";
        /** @var string The form field's type */
        $type = $this->inputProperties['type'];
        /** 
         * @var array An associative array of categories the form field's type is a part of. 
         * - `boolean $input` — Indicates if the field uses the `input` element.
         * - `boolean $multi` — Indicates if the field is a *Multiple Choice Field*.
         * - `boolean $datetime` — Indicates if the field works with dates, times, or timezones.
         **/
        $types = [
          'input'    => array_search($type, FORM_FIELD_TYPES['all']['inputs']) !== false,
          'text'     => array_search($type, array_merge(FORM_FIELD_TYPES['text'], FORM_FIELD_TYPES['addresses'], FORM_FIELD_TYPES['numbers'], FORM_FIELD_TYPES['dates'])) !== false,
          'multi'    => array_search($type, FORM_FIELD_TYPES['multi']) !== false,
          'datetime' => array_search($type, FORM_FIELD_TYPES['dates']) !== false,
        ];
  
        // Field Base
        (function () use (&$markup, $types) {
          $fieldsetInnerMarkup = trim("{$this->getAttributeMarkup($types['input'] && $types['multi'])}");
          $fieldsetClasses = trim(implode(' ', [
            // Size
            (function () {
              $size = $this->findReferencedProperty('properties->size');
  
              if ($size != 'full') {
                return "size {$size}";
              }
  
              return '';
            })(),
            // Type
            $this->findReferencedProperty('inputProperties->type'),
            // Hidden
            $this->findReferencedProperty('properties->hidden')
              ? 'hidden'
              : '',
            // Disabled
            $this->findReferencedProperty('properties->disabled')
              ? 'disabled'
              : '',
            // Readonly
            $this->findReferencedProperty('inputProperties->validations->disable->readonly')
            ? 'readonly'
            : '',
          ]));
  
          $markup .= "<fieldset class=\"{$fieldsetClasses}>\">";
          $markup .= '<div class="fieldset-wrapper">';
        })();
        // Field wrapper
        (function () use (&$markup, $types) {
          $classes = trim(implode(' ', [
            'field', 
            $this->properties['disabled'] 
              ? 'disabled' 
              : '',
            $this->properties['hidden'] 
              ? 'hidden' 
              : '',
            $this->inputProperties['validations']->readonly
              ? 'readonly' 
              : '',
            $this->inputProperties['validations']->required 
              ? 'required' 
              : '',
            $this->content['innerTitle'] && $types['text']
              ? 'inner-title'
              : ''
          ]));
  
          $markup .= "<div class=\"{$classes}\">";
        })();
        // Children Before Content
        (function () use (&$markup) {
          if ($this->internalProperties['children'] && $this->properties['showChildrenFirst']) {
            $markup .= $this->getChildrenMarkup();
          }
        })();
        // Standard Title & Subtitle
        if (!$this->content['innerTitle'] || !$types['text']) {
          $markup .= $this->getHeaderMarkup(['title', 'subtitle']);
        }
        // Input markup
        (function () use (&$markup, $type, $types) {
          if ($type != 'group') {
            $tagName = $types['input']
                       ? 'input'
                       : $type;
  
            // Input & Select Fields
            if ((!$types['multi'] || $type == 'select')) {
              $markup .= "<div class=\"input-container {$type}\">";
                // Input element opening tag
                (function () use (&$markup, $type, $types, $tagName) {
                  $pieces = [
                    'name'         => (function () {
                      $name = clean_all_html($this->properties['name']);
                      $type = $this->inputProperties['validations']->type;
    
                      if ($type != 'array') {
                        return $name;
                      }
                      else {
                        return "{$name}[]";
                      }
                    })(),
                    'placeholder'  => (function () use ($types) {
                      $placeholder = $this->inputProperties['placeholder'];
    
                      if ($placeholder !== false && !$types['multi']) { return 'placeholder="' . clean_html($placeholder, '') . '" '; }
                      else                                            { return ''; }
                    })(),
                    'autocomplete' => (function () use ($types) {
                      $autocomplete = $this->inputProperties['autocomplete'];
    
                      if (!$types['multi']) { return "autocomplete=\"{$autocomplete}\" "; }
                      else                  { return ""; }
                    })(),
                    'inputmode'    => (function () use ($type, $types) {
                      $inputmode = $this->inputProperties['inputMode'];
                      
                      if (!$types['multi']) {
                        $value = false;
  
                        if ($inputmode === false) {
                          $directMatches = [
                            'text',
                            'tel',
                            'url',
                            'email',
                            'search'
                          ];
                          
                          if (array_search($type, $directMatches) !== false) {
                            $value = $type;
                          }
                          else if ($type == 'number') {
                            $value = 'numeric';
                          }
                        }
                        else {
                          $value = $inputmode;
                        }
  
                        if ($value != false) {
                          return "inputmode=\"{$value}\" ";
                        }
                      }
  
                      return "";
                    })(),
                    'value'        => (function () use ($types) {
                      $value = $this->inputProperties['value'];
    
                      if ($value !== '' && $types['input']) { return 'value="' . clean_all_html($value) . '" '; }
                      else                                    { return ''; }
                    })(),
                    'id'           => (function () {
                      $id = clean_all_html($this->internalProperties['id']);
    
                      return "{$id}_input";
                    })(),
                    'type'         => (function () use ($type) {
                      if ($type != 'select') {
                        if ($type != 'textarea') { return "type=\"{$type}\" "; }
                        else                     { return "type=\"{$type}\" rows=\"2\""; }
                      }
  
                      return "";
                    })(),
                    'disabled'     => $this->properties['disabled']
                                      ? 'disabled '
                                      : '',
                    'hidden'       => $this->properties['hidden']
                                      ? 'hidden '
                                      : '',
  
                    'step'         => $this->inputProperties['type'] == 'time'
                                      ? 'step="1" '
                                      : '',
                    'validations'  => (function () use ($type, $types) {
                      $str = "";
                        $settings = $this->inputProperties['validations'];
                        $validations = $settings->validations;
                        $dynamicValidations = $this->checkDynamicValidations(true);
    
                        if ($settings->required) {
                          $str .= "required ";
                        }
                        if ($settings->readonly) {
                          $str .= "readonly ";
                        }
                        if (isset($validations['range'])) {
                          $rangeType = (function () use ($type) {
                            $fields = [
                              'count' => [
                                'number',
                                'range',
                                'date',
                                'month',
                                'time',
                                'week'
                              ],
                              'length' => array_merge(FORM_FIELD_TYPES['text'], FORM_FIELD_TYPES['addresses'])
                            ];
      
                            if (array_search($type, $fields['count']) !== false) {
                              return 'count';
                            }
                            if (array_search($type, $fields['length']) !== false) {
                              return 'length';
                            }
                          })();
                          $range = $validations['range'];
      
                          
                          if ($rangeType) {
                            $getRangeValue = function ($rangeThreshold) use ($range, $dynamicValidations) {
                              $dynamicValue = $dynamicValidations["range->{$rangeThreshold}"] ?? false;
    
                              if ($dynamicValue !== false) {
                                if ($dynamicValue['value'] ?? false !== false) {
                                  return $dynamicValue['value'];
                                }
                                else if ($dynamicValue['default'] ?? false !== false) {
                                  return $dynamicValue['default'];
                                }
                                else {
                                  return false;
                                }
                              }
                              else {
                                return $range[$rangeThreshold] ?? false;
                              }
                            };
  
                            foreach ([ 'min', 'max' ] as $rangeThresholdType) {
                              $key = '';
                              $value = null;
                              
                              if (isset($range[$rangeThresholdType]) || isset($range['is'])) {
                                $key = $rangeThresholdType;
        
                                if (isset($range[$rangeThresholdType]) && $getRangeValue($rangeThresholdType) !== false) {
                                  $value = $getRangeValue($rangeThresholdType);
                                }
                                else if ($getRangeValue('is') !== false) {
                                  $value = $getRangeValue('is');
                                }
                              }
  
                              if ($rangeType == 'length') {
                                $key .= 'Length';
                              }
    
                              if ($value !== NULL) {
                                $str .= "{$key}=\"{$value}\" ";
                              }
                            }
                          }
                        }
                        if (isset($validations['pattern'])) {
                          $validationPattern = (function () use ($validations, $dynamicValidations) {
                            $dynamicValue = $dynamicValidations['pattern'] ?? false;
    
                            if ($dynamicValue !== false) {
                              if ($dynamicValue['value'] ?? false !== false) {
                                return $dynamicValue['value'];
                              }
                              else if ($dynamicValue['default'] ?? false !== false) {
                                return $dynamicValue['default'];
                              }
                            }
                            else {
                              return $validations['pattern'];
                            }
  
                            return false;
                          })();
                          
                          if ($validationPattern) {
                            $delimiters = "[\/~@;%`]";
                            $pattern = clean_all_html(
                                        preg_replace(
                                          "/\\\/",
                                          "\\\\\\",
                                          preg_replace(
                                            "/^{$delimiters}|{$delimiters}[a-zA-Z]{0,}$/", 
                                            '', 
                                            $validationPattern
                                          )
                                        )
                                       );
        
                            $str .= "pattern=\"{$pattern}\" ";
                          }
                        }
  
                        if ($dynamicValidations) {
                          $encodedValue = json_encode($dynamicValidations);
  
                          $str .= "data-dynamic-validations={$encodedValue}";
                        }
    
                        return $str;
                    })(),
                    "controller"   => (function () {
                      $controller = $this->findReferencedProperty('inputProperties->hasControl');
  
                      if ($controller) {
                        $encodedSchemes = clean_all_html(json_encode($controller));
  
                        return "data-has-control=\"{$encodedSchemes}\" ";
                      }
                      
                      return "";
                    })()
                  ];
                  $attributeMarkup = (function () {
                    $markup = $this->getAttributeMarkup();
    
                    $markup = preg_replace('/id\=\"([\w\d_]+)\"/', 'id="$1_input"', $markup);
    
                    return $markup;
                  })();
  
                  $markup .= "<{$tagName}
                                {$attributeMarkup}
                                name=\"{$pieces['name']}\" 
                                {$pieces['type']} 
                                {$pieces['value']} 
                                {$pieces['placeholder']} 
                                {$pieces['autocomplete']} 
                                {$pieces['inputmode']} 
                                {$pieces['step']} 
                                {$pieces['validations']} 
                                {$pieces['controller']}
                              >";
                })();
                // InnerHTML
                (function () use (&$markup, $type) {
                  $defaultValue = $this->findReferencedProperty('inputProperties->value');
  
                  if ($type == 'textarea' && $defaultValue) {
                    $markup .= clean_all_html($defaultValue);
                  }
                  else if ($type == 'select') {
                    $options = $this->inputProperties['options'];
  
                    if ($options) {
                      $options = array_merge([ '' => '-- Choose an Option --' ], $options);
  
                      foreach ($options as $value => $label) {
                        $pieces = [
                          'value'    => clean_all_html($value),
                          'selected' => $value == $defaultValue
                                        ? 'selected '
                                        : '',
                          'content'  => clean_all_html($label) 
                        ];
  
                        $markup .= "<option
                                      value=\"{$pieces['value']}\" 
                                      {$pieces['selected']}>
                                      {$pieces['content']}
                                    </option>";
                      }
                    }
                  }
                })();
                // Input element closing tag
                if (!$types['input']) {
                  $markup .= "</{$tagName}>";
                }
                // Toolbar
                (function () use (&$markup, $type, $types) {
                  $markup .= "<div class=\"toolbar\">";
  
                  // Markdown Helper
                  if ($type == 'textarea' && $this->inputProperties['toolbar']['markdown'] !== false) {
                    $markup .= <<<EOT
                      <button class="tool markdown-helper" type="button">
                        <span>Some Markdown is Supported</span>
                      </button>
                    EOT;
                  }
                  // Character Counter
                  if (($types['input'] || $type == 'textarea') && !$types['multi'] && $this->inputProperties['toolbar']['characterCounter']) {
                    if (isset($this->inputProperties['validations']->validations['range'])) {
                      $characterCounter = (function () {
                        $str = $this::CHARACTER_COUNTER_TEMPLATE;
                        $range = $this->inputProperties['validations']->validations['range'];
                        $pieces = (function () use ($range) {
                          $pieces = [];
  
                          $pieces['now'] = strlen($this->inputProperties['value']);
                          $pieces['threshold'] = (function () use ($range, $pieces) {
                            if (isset($range['now'])) {
                              return $range['now'];
                            }
                            else {
                              if (isset($range['min']) && $pieces['now'] < $range['min']) {
                                return $range['min'];
                              }
                              else if (isset($range['max'])) {
                                return $range['max'];
                              }
                            }
                          })();
                          $pieces['descriptor'] = $pieces['now'] <= $pieces['threshold']
                                                  ? 'required'
                                                  : 'available';
  
                          return $pieces;
                        })();
  
                        foreach ($pieces as $piece => $pieceValue) {
                          $str = preg_replace("/\{{$piece}\}/", $pieceValue, $str);
                        }
  
                        
                        return $str;
                      })();
  
                      $markup .= $characterCounter;
                    } 
                  }
                  // Toggle Password Visibility
                  if ($type == 'password' && $this->inputProperties['toolbar']['passwordVisibilityToggle']) {      
                    $markup .= <<<EOT
                      <button class="tool toggle-password-visibility layer-target icon" type="button">
                        <span class="show box-icon fas fa-eye" aria-hidden="true"></span>
                        <span class="hide box-icon fas fa-eye-slash" aria-hidden="true"></span>
                      </button>
                      <div class="layer tooltip" data-layer-delay="medium">Peek at the password</div>
                    EOT;            
                  }
                  // Clear Field Button
                  if (($types['input'] || $type == 'textarea') && !$types['multi'] && $this->inputProperties['toolbar']['clearFieldButton']) {      
                    $markup .= <<<EOT
                      <button class="tool clear-field layer-target icon" type="button">
                        <span class="show box-icon fas fa-times" aria-hidden="true"></span>
                      </button>
                      <div class="layer tooltip" data-layer-delay="medium">Clear the field</div>
                    EOT;            
                  }
  
                  $markup .= "</div>";
                })();
                // Inner Title
                if ($this->content['innerTitle'] && $types['text']) {
                  $markup .= $this->getHeaderMarkup(['title']);
                }
  
                $markup .= '</div>';
            }
            // Checkbox, Radio, & Toggle Fields
            else if ($types['multi']) {
              $options = $this->findReferencedProperty('inputProperties->options');
              $optionWrapperClasses = trim(implode(' ', [
                'children',
                'options',
                (function () {
                  $wrapOptions = $this->findReferencedProperty('inputProperties->wrapOptions');
  
                  if ($wrapOptions !== false) {
                    return "wrap {$wrapOptions}";
                  }
                  else {
                    return "";
                  }
                })()
              ]));
              $controller = $this->findReferencedProperty('inputProperties->hasControl');
              $optionCount = 1;
  
              $addOption = function ($name, $value, $label) use (&$markup, $type, &$optionCount, $controller) {
                $pieces = [
                  'name'        => (function () use ($name) {
                    $cleanName = (function () use ($name) {
                      $inputName = $this->properties['name'];
  
                      if (strpos($inputName, $name) === false) {
                        return clean_all_html("{$inputName}_{$name}");
                      }
                      else {
                        return clean_all_html($inputName);
                      }
                    })();
                    $type = $this->inputProperties['validations']->type;
  
                    if (strpos($type, 'array') !== false) {
                      return "{$cleanName}[]";
                    }
                    else {
                      return $cleanName;
                    }
                  })(),
                  'value'       => (function () use ($value) {
                    if ($value !== false && $value !== '') { return 'value="' . clean_all_html($value) . '" '; }
                    else                                   { return ""; }
                  })(),
                  'id'          => (function () use ($optionCount) {
                    $id = clean_all_html($this->internalProperties['id']);
  
                    return "{$id}_option_{$optionCount}";
                  })(),
                  'type'          => $type == 'toggle-button' || $type == 'toggle-box'
                                     ? "checkbox"
                                     : $type,
                  'disabled'      => $this->properties['disabled']
                                     ? 'disabled '
                                     : '',
                  'hidden'        => $type == 'toggle-button'
                                        && $value == 'off'
                                     || $type == 'toggle-box'
                                        && $value == 'false'
                                     || $this->properties['hidden']
                                     ? 'hidden '
                                     : '',
                  'validations'   => (function () use ($optionCount) {
                    $str = "";
                      $settings = $this->inputProperties['validations'];
                      $validations = $settings->validations;
  
                      if ($settings->required && $optionCount == 1) {
                        $str .= "required ";
                      }
                      if ($settings->readonly) {
                        $str .= "readonly ";
                      }
  
                      return $str;
                  })(),
                  'defaultOption' => (function () use ($name, $type, $value) {
                    $defaultValue = $this->inputProperties['value'];
                    $isDefault = $defaultValue == $value
                                 || $type != 'select'
                                    && is_string($defaultValue)
                                    && is_string($value)
                                    && strpos($defaultValue, $value) !== false
                                 || ($type == 'toggle-button'
                                      || $type == 'toggle-box')
                                    && is_string($defaultValue)
                                    && is_string($name)
                                    && strpos($defaultValue, $name) !== false
                                 || $type == 'toggle-button'
                                    && $value == 'off'
                                 || $type == 'toggle-box'
                                    && $value == 'false';
  
                    if ($isDefault) {
                      if ($type == 'select') { return 'selected '; }
                      else                   { return 'checked '; }
                    }
                    else                     { return ''; }
                  })(),
                  "controller"   => (function () use ($value, $controller) {
                    if ($controller) {
                      $matchingSchemes = [];
                      
                      foreach ($controller as $controlScheme) {
                        if (strpos($controlScheme['condition'], $value) !== false) {
                          $matchingSchemes[] = $controlScheme;
                        }
                      }
                      
                      $encodedSchemes = clean_all_html(json_encode($matchingSchemes));
  
                      return "data-has-control=\"{$encodedSchemes}\" ";
                    }
                    
                    return "";
                  })()
                ];
                $pieces['label'] = (function () use ($optionCount, &$pieces, $value, $label) {
                  $labelMarkup = "";
                  $title = clean_all_html($label);
                  $description = (function () use ($optionCount, $value, $label) {
                    $description = $this->content['description'];
                    $matches = [];
  
                    if (is_array($description) && is_array_associative($description) && isset($description[$value])) {
                      return clean_html($description[$value], '<div><span><p><strong><b><em><i><a><br><code><pre><ul><ol><li><button><a>');
                    }
  
                    return false;
                  })();
  
                  if ($description) {
                    $labelMarkup .= '<div class="title-container">';
                  }
  
                  $labelMarkup .= "<label
                                      id=\"{$pieces['id']}_label\" 
                                      for=\"{$pieces['id']}\">
                                      {$title}
                                   </label>";
  
                  if ($description) {
                    $labelMarkup .= "   <div class=\"subtitle\">
                                           ${description}
                                        </div>
                                     </div>";
                  }
  
                  return $labelMarkup;
                })();
                $fieldWrapperClasses = trim("field {$pieces['validations']}{$pieces['hidden']}{$pieces['disabled']}");
                $attributeMarkup = (function () use ($pieces) {
                  $markup = $this->getAttributeMarkup();
  
                  $markup = preg_replace('/id\=\"[\w\d_]+\"/', "id=\"{$pieces['id']}\"", $markup);
  
                  return $markup;
                })();
  
                $markup .= "<div class=\"{$fieldWrapperClasses}\">
                              <div class=\"input-container {$type}\">
                                <input
                                  {$attributeMarkup}
                                  name=\"{$pieces['name']}\" 
                                  {$pieces['value']} 
                                  type=\"{$pieces['type']}\" 
                                  {$pieces['hidden']}
                                  {$pieces['disabled']}
                                  {$pieces['validations']}
                                  {$pieces['defaultOption']}
                                  {$pieces['controller']}
                                >
                              </div>
                              {$pieces['label']}
                            </div>";
  
                $optionCount++;
              };
  
              $markup .= "<div class=\"{$optionWrapperClasses}\">";
  
              if ($options) {
                foreach ($options as $value => $label) {
                  if ($type == 'toggle-button' || $type == 'toggle-box') {
                    $optionValues = (function () use ($type, $label) {
                      if ($type == 'toggle-button') {
                        return [
                          'off' => '',
                          'on'  => &$label
                        ];
                      }
                      else {
                        return [
                          'false' => '',
                          'true'  => &$label
                        ];
                      }
                    })();
  
                    foreach ($optionValues as $optionValue => $optionLabel) {
                      $addOption($value, $optionValue, $optionLabel);
                    }
                  }
                  else {
                    $addOption($this->properties['name'], $value, $label);
                  }
                }
              }
              else if ($type == 'toggle-button' || $type == 'toggle-box') {
                $optionValues = (function () use ($type) {
                  $defaultLabel = "";
      
                  if ($this->inputProperties['placeholder'] !== false) {
                    $defaultLabel = &$this->inputProperties['placeholder'];
                  }
                  else if ($this->content['title'] !== false) {
                    $defaultLabel = &$this->content['title'];
                  }
      
                  if ($type == 'toggle-button') {
                    return [
                      'off' => '',
                      'on'  => &$defaultLabel
                    ];
                  }
                  else {
                    return [
                      'false' => '',
                      'true'  => &$defaultLabel
                    ];
                  }
                })();
  
                foreach ($optionValues as $optionValue => $optionLabel) {
                  $addOption($this->properties['name'], $optionValue, $optionLabel);
                }
              }
  
              $markup .= '</div>';
            }
          }
        })();
        // Inner Text Subtitle
        if ($this->content['innerTitle'] && $types['text']) {
          $markup .= $this->getHeaderMarkup(['subtitle']);
        }
        // Children After Content
        (function () use (&$markup) {
          if ($this->internalProperties['children'] && !$this->properties['showChildrenFirst']) {
            $markup .= $this->getChildrenMarkup();
          }
        })();
        // Description & Alerts
        (function () use (&$markup) {        
          $markup .= $this->getHeaderMarkup(['description', 'alerts']);
  
          // Alert Messages
          (function () use (&$markup) {
            // $messages = $this->findReferencedProperty('content->alertMessages') ?? [];
            $messages = $this->findReferencedProperty('inputProperties->validations->customValidationMessages') ?? [];
  
            $markup .= '<div class="alert-messages">';
            $markup .=    json_encode($messages);
            $markup .= '</div>';
          })();
        })();
        // Closing field wrapper
        (function () use (&$markup, $types) {
          // if (!$types['multi']) {
            $markup .= '</div>';
          // }
        })();
        // Closing Field Base
        (function () use (&$markup) {
          $markup .= '</div>'; // Closing fieldset wrapper tag
          $markup .= '</fieldset>'; // Closing fieldset tag
        })();
        // Markup cleanup
        (function () use (&$markup) {
          $allowedTags = '<div><span><p><ul><ol><li><strong><em><b><i><a><code><pre><br><fieldset><label><legend><input><select><option><button><textarea>';
  
          $markup = clean_html($markup, $allowedTags);
          $markup = collapseWhitespace($markup);
        })();
  
        return $markup;
      }
    }
    /** A button in a form */
    class FormButton extends FormCore {
      use FormChild, FormInput;
  
      protected $_internalProperties = [
        'htmlDefaults' => [
          'attributes' => [
            'autocomplete' => 'off'
          ]
        ]  
      ];
      /**
       * @var array Properties that define and control the form button.
       * - `false|string $label` — The content of the button. 
       * - - `<span>` tags are permitted inside of the button.
       * - `false|string $title` — The assistive text/alternative label of the button. 
       * - - _Can be up to **256** characters long._
       * - `array $tooltip` — Properties for adding a *tooltip* to the button.
       * - - `false|string $content` — The content of the tooltip. If **false**, no tooltip will be added.
       * - - - _Can be up to **1024** characters in length._
       * - - `'top'|'right'|'bottom'|'left'|false $pos` — The position of the tooltip relative to the target. 
       * - - `'top'|'right'|'bottom'|'left'|false $align` — The alignment of the tooltip relative to the target. 
       * - - `false|'short'|'medium'|'long' $delay` — Indicates the delay that *focus* layers have before appearing.
       * - - `boolean $sticky
       * - - `boolean $isSticky` — Indicates if the layer position is fixed or not.
       * - - `boolean $useCursor` — Indicates if the layer is to be positioned based on the *cursor's current position* or not.
       * - - `boolean $followCursor` — Indicates if the layer is to always follow the *cursor* while active.
       * - - `boolean $lazyFollow` - Indicates if the layer "lazily" follows the cursor, sticking to the axis specified by `pos`.
       * - - `false|string $name` — The *layer name* used for callbacks or styling.
       * - - - _Can be up to **256** characters in length._
       */
      protected $_inputProperties = [
        'content' => false,
        'title'   => false,
        'tooltip' => [
          'content'      => false,
          'pos'          => 'top',
          'align'        => false,
          'delay'        => 'medium',
          'sticky'       => false,
          'useCursor'    => false,
          'followCursor' => false,
          'lazyFollow'   => false,
          'name'         => false
        ]
      ];
  
      /**
       * Initialize the form field class 
       * 
       * @param array $properties An array of properties that are to be passed to the field.
       * @return void
       */
      public function __construct ($properties) {
        // FormButton Property Validations
        $this->updateProperty('internalProperties->propertyValidations', [
          "'inputProperties->content'" => new ValidationProperties([
            'type'              => 'string',
            'validations'       => [
              'range' => [
                'max' => 256
              ]
            ]
          ]),
          "'inputProperties->title'" => new ValidationProperties([
            'type'        => 'boolean|string',
            'validations' => [
              'range' => [
                'max' => 256
              ]
            ]
          ]),
          "'inputProperties->tooltip'" => new ValidationProperties([
            'type'        => 'array',
          ]),
          "'inputProperties->tooltip->content'" => new ValidationProperties([
            'type'              => 'boolean|string',
            'validations'       => [
              'range' => [
                'max' => 1024
              ]
            ]
          ]),
          "'inputProperties->tooltip->pos'" => new ValidationProperties([
            'type'        => 'boolean|string',
            'match' => [
              false,
              'top',
              'right',
              'bottom',
              'left'
            ]
          ]),
          "'inputProperties->tooltip->align'" => new ValidationProperties([
            'type'        => 'boolean|string',
            'match' => [
              false,
              'top',
              'right',
              'bottom',
              'left'
            ]
          ]),
          "'inputProperties->tooltip->delay'" => new ValidationProperties([
            'type'        => 'boolean|string',
            'match' => [
              false,
              'short',
              'medium',
              'long'
            ]
          ]),
          "'inputProperties->tooltip->name'" => new ValidationProperties([
            'type'        => 'boolean|string',
            'validations' => [
              'range' => [
                'max' => 256
              ]
            ]
          ]),
        ], true);
  
        // FormButton htmlBindings
        $this->updateProperty("internalProperties->htmlBindings", [
          'classes' => [
            "'inputProperties->tooltip->content'" => [
              'name' => 'layer-target',
              'condition' => (function () {
                $content = &$this->inputProperties['tooltip']['content'];
  
                return $content !== false;
              })()
            ]
          ]
        ], true);
        
        $this->construct($properties);
      }
      /**
       * Retrieve the HTML Markup for the form field
       * 
       * @return string|false Returns the form field's *HTML Markup string* on success, or **false** if an error occurred.
       */
      protected function getElementMarkup () {
        /** @var string The form field's *HTML Markup string* */
        $markup = "";
        /** @var string The form field's type */
        $type = $this->inputProperties['type'];
  
        // Field Base
        (function () use (&$markup) {
          $markup .= "<fieldset {$this->getAttributeMarkup(false)}>";
          $markup .= '<div class="fieldset-wrapper">';
        })();
        // Field wrapper
        (function () use (&$markup) {
          $classes = implode(' ', [
            'field', 
            $this->properties['disabled'] 
              ? 'disabled' 
              : ''
          ]);
  
          // $markup .= "<div class=\"{$classes}\">";
        })();
        // Children Before Content
        (function () use (&$markup) {
          if ($this->internalProperties['children'] && $this->properties['showChildrenFirst']) {
            $markup .= $this->getChildrenMarkup();
          }
        })();
        // Title & Subtitle
        $markup .= $this->getHeaderMarkup(['title', 'subtitle']);
        // Button markup
        (function () use (&$markup, $type) {
          // Attributes
          (function () use (&$markup, $type) {
            $pieces = [
              'name'        => (function () {
                $name = clean_all_html($this->properties['name']);
                $type = $this->inputProperties['validations']->type;
  
                if ($type != 'array') {
                  return $name;
                }
                else {
                  return "{$name}[]";
                }
              })(),
              'value'       => (function () {
                $value = $this->inputProperties['value'];
  
                if ($value !== "") { return 'value="' . clean_all_html($value) . '" '; }
                else               { return ''; }
              })(),
              'id'          => (function () {
                $id = clean_all_html($this->internalProperties['id']);
  
                return "{$id}_button";
              })(),
              'type'        => $type,
              'content'     => clean_html($this->inputProperties['content'], '<div><span>'),
              'title'       => (function () {
                $title = $this->inputProperties['title'];
  
                if ($title) { 
                  $title = clean_all_html($title);
  
                  return "aria-label=\"{$title}\" ";
                }
                else { 
                  return "";
                }
              })(),
              'disabled'    => $this->properties['disabled']
                               ? 'disabled '
                               : '',
              'hidden'      => $this->properties['hidden']
                               ? 'hidden '
                               : ''
            ];
            $attributeMarkup = (function () {
              $markup = $this->getAttributeMarkup();
  
              $markup = preg_replace('/id\=\"([\w\d_]+)\"/', 'id="$1_button"', $markup);
  
              return $markup;
            })();
  
            $markup .= "<button
                          {$attributeMarkup}
                          name=\"{$pieces['name']}\" 
                          type=\"{$pieces['type']}\"
                          {$pieces['title']} 
                          {$pieces['value']} 
                          >";
          })();
          // InnerHTML
          (function () use (&$markup, $type) {
            $content = $this->inputProperties['content'];
            $title = $this->content['title'];
  
            $markup .= '<span>';
  
            if ($content) {
              $markup .= clean_html($content, '<div><span>');
            }
            else if ($title) {
              $markup .= clean_all_html($title);
            }
  
            $markup .= '</span>';
  
            if ($type == 'reset' || $type == 'submit') {
              // $markup .= '<div class="spinner" title="Loading Indicator" aria-label="Loading Indicator">
              //               <span class="dot"></span>
              //               <span class="dot"></span>
              //               <span class="dot"></span>
              //               <span class="dot"></span>
              //             </div>';
              $markup .= file_get_contents('local/spinner.php', true);
            }
          })();
          $markup .= "</button>";
  
          // Tooltip
          (function () use (&$markup) {
            $propertyTypes = [
              'attr' => [
                'pos',
                'align',
                'delay',
                'name'
              ],
              'class' => [
                'sticky',
                'useCursor',
                'followCursor',
                'lazyFollow'
              ]
            ];
            $tooltip = $this->inputProperties['tooltip'];
  
            if ($tooltip['content'] !== false) {
              $className = (function () use ($tooltip, $propertyTypes) {
                $classList = [
                  'layer',
                  'tooltip'
                ];
                $name = '';
  
                foreach ($propertyTypes['class'] as $class) {
                  $value = $tooltip[$class];
  
                  if ($value !== false) {
                    $classList[] = $class;
                  }
                }
  
                $name = implode(' ', $classList);
                $name = preg_replace('/(.+->)/', '', $name);
                $name = preg_replace('/([A-Z])/', "-\${1}", $name);
                $name = strtolower($name);
  
                return $name;
              })();
  
              $markup .= "<div class=\"{$className}\"";
  
              foreach ($propertyTypes['attr'] as $attr) {
                $value = $tooltip[$attr];
  
                if ($value !== false) {
                  $markup .= " data-layer-{$attr}=\"{$value}\"";
                }
              }
  
              $markup .= ">";
              $markup .= clean_html($tooltip['content'], '<div><span><p><ul><ol><li><strong><em><b><i><a><button><code><pre><br>');
              $markup .= "</div>";
            }
          })();
        })();
        // Children After Content
        (function () use (&$markup) {
          if ($this->internalProperties['children'] && !$this->properties['showChildrenFirst']) {
            $markup .= $this->getChildrenMarkup();
          }
        })();
        // Description & Alerts
        $markup .= $this->getHeaderMarkup(['description']);
        // Closing field wrapper
        (function () use (&$markup) {
          // $markup .= '</div>';
        })();
        // Closing Field Base
        (function () use (&$markup) {
          $markup .= '</div>'; // Closing fieldset wrapper tag
          $markup .= '</fieldset>'; // Closing fieldset tag
        })();
        // Markup cleanup
        (function () use (&$markup) {
          $allowedTags = '<div><span><p><ul><ol><li><strong><em><b><i><a><code><pre><br><fieldset><label><legend><input><select><option><button><textarea>';
  
          $markup = clean_html($markup, $allowedTags);
          $markup = collapseWhitespace($markup);
        })();
  
        return $markup;
      }
    }
  }
?>