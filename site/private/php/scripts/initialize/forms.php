<?php
  /** Form Configuration */

  /** Properties and Methods used by forms and their children */
  class FormCore {
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
          "'properties->size'" => [
            [ 'name' => 'inheritProperty' ],
            [ 'name' => 'inheritValue' ],
          ],
          "'inputProperties->type'" => [
            'name' => 'inheritValue'
          ]
        ],
        'attributes' => [
          "'internalProperties->id'" => [
            'name'  => 'inherit',
            'value' => 'inherit'
          ],
          "'properties->hidden'" => [
            'name'  => 'inherit',
            'value' => 'boolean'
          ],
          "'properties->disabled'" => [
            'name'  => 'inherit',
            'value' => 'boolean'
          ]
        ]
      ],
      'htmlDefaults'        => [
        'classes'    => [],
        'attributes' => []
      ], 
      'propertyValidations' => [],
      'alertMessages'       => [],
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
     * - `string $size` — Indicates how much space the element should take up on wider devices. Available options are **full**, **half**, **third**, & **two-thirds**. This option doesn't have an effect on the form itself.
     * - `boolean $disabled` — Indicates if the element can be interacted with.
     * - `boolean $hidden` — Indicates if the element is to be visually rendered.
     */
    protected $properties = [
      'name'       => '',
      'customHTML' => [
        'classes'    => [],
        'attributes' => []
      ],
      'size'       => 'full',
      'disabled'   => false,
      'hidden'     => false
    ];
    /**
     * @var array Visual content that is added to the markup.
     * - `string|false $title` — The title of the element. This property behaves differently depending on its usage:
     * - - **Form**: Used as the *title* of the form.
     * - - **Section**: Used as the *title* of the section.
     * - - **Field**, **Button**: Used as the *label* of the field or button.
     * - - This property has no effect on any other elements.
     * - `string|false $subtitle` — An additional sentence or two used to describe the *form*, *section*, or *field*. To provide more information about the field, consider using the `content->description` property. 
     * - `string|array|false $description` — A long description of the *form*, *section*, or *field*. A `string` can be provided, or an `array` to display individual line-items.
     */
    protected $content = [
      'title'               => false,
      'subtitle'            => false,
      'description'         => false,
      'customAlertMessages' => []
    ];
    /**
     * @var array Properties that define and control form fields or buttons.
     * - `string $type` — The type of input element to be used.
     * - - Input Fields: *color, date, datetime, datetimetz, email, file, number, month, password, range, search, tel, text, textarea, time, tz, url, week*
     * - - Multiple Choice Fields: *checkbox, radio, select*
     * - - Buttons: *button, reset, submit*
     * - `mixed $value` — The default or required value of the input element.
     * - `array $options` — An associative array of options for *Multiple Choice Fields*. Has no effect on *Input Fields* or *Buttons*.
     * - - `string $key` — The *name* or *key* of the option as it is to be sent when submitting the form.
     * - - `string $value` — The *display name* of the option as it is to be presented to the user.
     * - `object $validations` — A ValidationObject of validation settings for the inpt value.
     */
    protected $inputProperties = [
      'type'        => 'text',
      'value'       => '',
      'options'     => [],
      'validations' => null
    ];

    /**
     * Update a property of the form or form child
     * 
     * @param mixed $propertyString The *property string* of the property to update.
     * @param mixed $propertyValue The new *value* of the property.
     * - **NULL** *cannot* be provided as the value of a property.
     * - If both the new and existing value are an `array`, they will be recursively merged.
     * @param boolean $internalUpdate Indicates if the property is being updated internally. This should **not** be set to *true* when updating properties manually.
     * @return boolean Returns **true** if the property was successfully updated, or **false** if an error occurred.
     */
    public function updateProperty ($propertyString, $propertyValue, $internalUpdate = false) {
      $property = &$this->findReferencedProperty($propertyString);

      if ($property === null) {
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
          if (is_array($property) && is_array($propertyValue)) {
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
     * @param string $childType The type of child that is being added. Valid options are **section** and **field**.
     * @param mixed $properties An array of properties that are to be passed to the element.
     * @return object|false Returns the *child object* if the child was successfully added, or **false** if an error occurred.
     */
    public function addChild ($childType, $properties) {
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
        // ID & Name
        (function () use (&$child, $properties) {
          $name = get_class($this) != 'FormBase'
                  ? "{$this->properties['name']}_{$child->properties['name']}"
                  : $child->properties['name'];
          $id = "{$this->internalProperties['id']}_{$child->internalProperties['id']}";

          $child->updateProperty("properties->name", $name, true);
          $child->updateProperty("internalProperties->id", clean_id($id), true);
        })();
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
        'provided' => $providedProperties
      ];

      $processNode = function ($node, $basePropertyString = '', $usingCustomProperties = false) use (&$processNode, $properties) {
        foreach ($node as $propertyName => $propertyValue) {
          $propertyString = $basePropertyString
                            ? "{$basePropertyString}->{$propertyName}"
                            : $propertyName;
          $parentProperty = strpos($propertyString, '_') === 0 && !$usingCustomProperties ? substr($propertyString, 1) : false;
          
          if ($parentProperty) {
            $this->updateProperty($parentProperty, $propertyValue, true);
          }
          else if ($this->findReferencedProperty($propertyString) && is_array($propertyValue) || is_object($propertyValue)) {
            $processNode($propertyValue, $propertyString, $usingCustomProperties);
          }
          else {
            $internalUpdate = (function () use ($propertyString, $usingCustomProperties) {
              if ($propertyString == 'properties->name') { return true; }
              else                                       { return !$usingCustomProperties; }
            })();

            $this->updateProperty($propertyString, $propertyValue, $internalUpdate);

            if ($propertyString == "properties->name" && $usingCustomProperties) {
              $this->updateProperty('internalProperties->id', clean_id($propertyValue), true);
            }
          }
        }
      };

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
        "'properties->size'" => new ValidationProperties([
          'type'        => 'string',
          'validations' => [
            'match' => [ 'full', 'half', 'third', 'two-thirds' ]
          ]
        ]),
        "'properties->disabled'" => new ValidationProperties([
          'type' => 'boolean'
        ]),
        "'properties->hidden'" => new ValidationProperties([
          'type' => 'boolean'
        ]),
        "'content->title'" => new ValidationProperties([
          'type'              => 'boolean|string',
          'sanitizeParameter' => false,
          'validations'       => [
            'range' => [
              'max' => 64
            ]
          ]
        ]),
        "'content->subtitle'" => new ValidationProperties([
          'type'              => 'boolean|string',
          'sanitizeParameter' => false,
          'validations'       => [
            'range' => [
              'max' => 256
            ]
          ]
        ]),
        "'content->description'" => new ValidationProperties([
          'type'              => 'boolean|string|array',
          'sanitizeParameter' => false,
          'validations'       => [
            'range' => [
              'max' => 1024
            ]
          ]
        ])
      ], true);
      $this->updateProperty('internalProperties->alertMessages', [
        'invalidType'     => '${field} is not of a valid type.',
        'valueMissing'    => '${field} cannot be left empty.',
        'valueMismatch'   => '${field} is not one of the permitted values.',
        'rangeUnderflow'  => array_merge(
          [ '$input_fields' => '${field} must be longer than ${threshold} character(s).' ],
          array_fill_keys([ 'date', 'datetime', 'datetimetz', 'month', 'time', 'week' ], '${field} must be after ${threshold}.'),
          array_fill_keys([ 'number', 'range' ], '${field} must be greater than ${threshold}.'),
          array_fill_keys([ 'checkbox', 'radio', 'select' ], 'At least ${threshold} options have to be selected  ${field} must contain at least ${threshold} item(s).')
        ), 
        'rangeMismatch'   => array_merge(
          array_fill_keys([ 'string', 'hash', 'url' ], '${field} must be exactly ${threshold} character(s).'),
          array_fill_keys([ 'int', 'float', 'date' ], '${field} must be exactly ${threshold}.'),
          array_fill_keys([ 'array', 'object' ], '${field} must contain exactly ${threshold} item(s).')
        ), 
        'rangeUnderflow'  => array_merge(
          array_fill_keys([ 'string', 'hash', 'url' ], '${field} must be shorter than ${threshold} characters.'),
          array_fill_keys([ 'int', 'float', 'date' ], '${field} must be less than ${threshold}.'),
          array_fill_keys([ 'array', 'object' ], '${field} must contain less than ${threshold} items.')
        ),
        'patternMismatch' => '${field} must match the requested format'
      ], true);
      $this->updateProperty('inputProperties->validations', new ValidationProperties([]), true);
      
      $processNode($properties['class'], '', false);

      // foreach ($this->internalProperties['propertyValidations'] as $propertyString => &$validations) {
      //   if ($validations->value === null) {
      //     $validations->value = $this->findReferencedProperty(str_replace("'", '', $propertyString));
      //   }
      // }

      $processNode($properties['provided'], '', true);
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
                  $classList[] = preg_replace('/(.+->)/', '', $parsedPropertyString);
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

          if ($property !== null) {
            if (!is_array($attributes[array_key_first($attributes)])) {
              $attributes = [ $attributes ];
            }

            foreach ($attributes as $propertySettings) {
              if (isset($propertySettings['condition']) && $propertySettings['condition'] || $property) {
                $attributeName = '';
                $attributeValue = null;
  
                // Name
                if (!isset($propertySettings['name']) || $propertySettings['name'] == 'inherit') {
                  $attributeName = preg_replace('/(.+->)/', '', $parsedPropertyString);
                }
                else {
                  $attributeName[] = $propertySettings['name'];
                }
  
                // Value
                if (!isset($propertySettings['value']) || $propertySettings['value'] == 'inherit') {
                  $attributeValue = $property;
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
            array_merge($attributeList, $attributes);
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
          if (!$isField) {
            $markup .= '<div class="title">';
              $markup .= clean_all_html($content['title']);
            $markup .= '</div>'; // Closing title tag
          }
          else {
            $labelType = (function () {
              $type = $this->inputProperties['type'];
              $useLegend = $type == 'checkbox'
                           || $type == 'radio'
                           || $type == 'toggle-button'
                           || $type == 'toggle-box'
                           || $type == 'datetime'
                           || $type == 'datetimetz';

              return $useLegend ? 'legend' : 'label';
            })();

            // Opening Label tags
            $markup .= "<{$labelType} 
                          id=\"{$this->internalProperties['id']}_label\" 
                          for=\"{$this->internalProperties['id']}_input\">";

              // Required Field indicator tags
              if ($this->inputProperties['validations']->required) {
                $markup .= '<span class="required" title="Required" aria-label="(Required) ">
                              <span class="fas fa-asterisk" aria-hidden="true"></span>
                            </span>';
              }

              // Label Value
              $markup .= clean_all_html($content['title']);

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

              if (is_array($description) && count($description) > 1) {
                $markup .= '<ul class="styled">';
                
                foreach ($description as $item) {
                  $markup .= "<li>";
                    $markup .= clean_html($item, '<span><a><b><strong><i><em>');
                  $markup .= '</li>';
                }

                $markup .= '</ul>';
              }
              else {
                $markup .= clean_html($description, '<span><a><b><strong><i><em>');
              }
            })();

          $markup .= '</div>'; // Closing description tag
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
     * @return string Returns an HTML Markup string made up of the child elements.
     */
    protected function getChildrenMarkup () {
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

      return collapseWhitespace($markupString);
    }
  }

  /** The base form */
  class FormBase extends FormCore {
    protected $_internalProperties = [
      'htmlBindings' => [
        'classes'    => [
          "'formProperties->showFieldWalls'" => [
            'name'  => 'show-field-walls'
          ],
          "'formProperties->ajax->useAjax'" => [
            'name'  => 'ajax-submit'
          ],
          "'formProperties->ajax->useProgressBar'" => [
            'name'  => 'use-ajax-progress-bar'
          ],
          "'formFooter->isSticky'" => [
            'name'  => 'sticky-footer'
          ],
          "'formFooter->actions->detailsToggle->hideByDefault'" => [
            'name'  => 'hide-details',
          ]
        ],
        'attributes' => [
          "'formProperties->action'" => [
            'name'  => 'inherit',
            'value' => 'inherit'
          ],
          "'formProperties->method'" => [
            'name'  => 'inherit',
            'value' => 'inherit'
          ],
          "'formProperties->autocomplete'" => [
            'name'  => 'inherit',
            'value' => 'inherit'
          ]
        ]
      ]
    ];
    /**
     * @var array Properties that define and control the form
     * - `string $action` — The URL of the form's action script.
     * - `'GET'|'POST' $method` — The HTTP method to submit the form with.
     * - `'on'|'off' $autocomplete` — Indicates whether form fields can be automatically completed by the browser.
     * - `boolean $showAlerts` — Indicates if form warnings and errors are to be displayed.
     * - `'none'|'vertical'|'standard'|'double' $spacing` — Indicates how much spacing to generate with the form.
     * - `array $ajax` — Settings to control the use of asynchronous form submissions.
     * - - `boolean $useAjax` — Indicates if the form should by submitted asynchronously.
     * - - `boolean $useProgressBar` — Indicates if the *Loader Progress Bar* should be used when submitting the request asynchronously. 
     */
    protected $formProperties = [
      'action'         => '#',
      'method'         => 'POST',
      'autocomplete'   => 'off',
      'showAlerts'     => true,
      'showFieldWalls' => false,
      'spacing'        => 'standard',
      'ajax'           => [
        'useAjax'        => true,
        'useProgressBar' => true
      ]
    ];
    /**
     * @var array Configuration options for the Form Footer
     * - `boolean $enabled` — Indicates if the footer should be used in the form or not.
     * - `boolean $isSticky` — Indicates if the footer should stick to the bottom of the screen when scrolling through the form.
     * - `null|object $object` — The FormSection object of the form.
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
     * - - - - `'top'|'right'|'bottom'|'left'|false $pos` — The position of the tooltip relative to the button. 
     * - - - - `'top'|'right'|'bottom'|'left'|false $align` — The alignment of the tooltip relative to the button. 
     * - - - - `false|'short'|'medium'|'long' $delay` — Indicates the delay that *focus* layers have before appearing.
     * - - - - `false|string $name` — The *layer name* used for callbacks or styling.
     * - - - `null|object $object` — The FormField object of the button.
     * - - - `array $classes` — An indexed array of classes to be passed to the button.
     * - - - `array $attributes` — An associative array of attributes to be passed to the button.
     * - - - - The **key** is the *name of the attribute* that is being passed to the element.
     * - - - - The **value** is the *value of the attribute* that is being passed to the element.
     * - - *The `$reset` and `$submit` actions contain the following properties:*
     * - - - `boolean $confirm` — Indicates if a confirmation button is to be displayed when the button is clicked.
     */
    protected $formFooter = [
      'enabled'  => true,
      'isSticky' => false,
      'object'   => null,
      'actions'  => [
        'reset'         => [
          'enabled'    => false,
          'content'    => 'Reset',
          'title'      => false,
          'tooltip' => [
            'content'  => 'Reset the form and start over',
            'pos'      => 'top',
            'align'    => false,
            'delay'    => 'medium',
            'name'     => false
          ],
          'confirm'    => true,
          'object'     => null,
          'classes'    => [
            'form-reset',
            'styled',
            'color',
            'warning'
          ],
          'attributes' => []
        ],
        'detailsToggle' => [
          'enabled'       => false,
          'content'       => 'Show Details',
          'title'         => false,
          'tooltip' => [
            'content'  => 'Toggle the visibility of the form details',
            'pos'      => 'top',
            'align'    => false,
            'delay'    => 'medium',
            'name'     => false
          ],
          'hideByDefault' => false,
          'object'        => null,
          'classes'       => [
            'form-details-toggle',
            'styled',
          ],
          'attributes'    => []
        ],
        'submit' => [
          'enabled'    => true,
          'content'    => 'Submit',
          'title'      => false,
          'tooltip' => [
            'content'  => 'Complete and submit the form',
            'pos'      => 'top',
            'align'    => false,
            'delay'    => 'medium',
            'name'     => false
          ],
          'confirm'    => true,
          'object'     => null,
          'classes'    => [
            'form-submit',
            'styled',
            'color',
            'light'
          ],
          'attributes' => []
        ]
      ]
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
     */
    protected $formResult = [
      'toast'    => [
        'enabled'    => false,
        'method'     => 'response',
        'properties' => []
      ],
      'redirect' => [
        'enabled'          => false,
        'delay'            => 0,
        'location'         => '',
        'useQueryParam' => false
      ],
      'modal'    => [
        'enabled' => false
      ]
    ];
    /**
     * @var array Results of the Form Submission
     * - *These values are populated by calling the `validateForm()` method.*
     * - `boolean $success` — Indicates if the form was successfully submitted and processed.
     * - `array $parameters` — An array of provided parameters generated by the form submission.
     * - `array $warnings` — An array of warnings generated by the form submission.
     * - `array $errors` — An array of errors generated by the form submission.
     * - `object $response` — The form's response object.
     */
    protected $formSubmit = [
      'success'      => true,
      'parameters'   => [],
      'warnings'     => [],
      'errors'       => [],
      'response'     => null
    ];
    
    /**
     * Initialize the form class 
     * 
     * @param array $providedProperties An array of properties that are to be passed to the element.
     * @return void
     */
    public function __construct ($properties) {
      $this->updateProperty('internalProperties->propertyValidations', [
        "'formProperties->action'" => new ValidationProperties([
          'type'        => 'string',
          'validations' => [
            'isURL'   => [
              'protocol' => true,
              'port'     => true
            ]
          ]
        ]),
        "'formProperties->method'" => new ValidationProperties([
          'type'        => 'string',
          'validations' => [
            'match' => [ 'GET', 'POST', 'dialog' ]
          ]
        ]),
        "'formProperties->autocomplete'" => new ValidationProperties([
          'type' => 'string',
          'validations' => [
            'match' => [ 'on', 'off' ]
          ]
        ]),
        "'formProperties->showFieldWalls'" => new ValidationProperties([
          'type' => 'boolean'
        ]),
        "'formProperties->spacing'" => new ValidationProperties([
          'type' => 'string',
          'validations' => [
            'match' => [ 'none', 'vertical', 'default', 'double' ]
          ]
        ]),
        "'formProperties->ajax->useAjax'" => new ValidationProperties([
          'type' => 'boolean'
        ]),
        "'formProperties->ajax->useProgressBar'" => new ValidationProperties([
          'type' => 'boolean'
        ]),
        "'formResult->toast->useToast'" => new ValidationProperties([
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
        "'formResult->redirect->useRedirect'" => new ValidationProperties([
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
        "'formResult->modal->useModal'" => new ValidationProperties([
          'type' => 'boolean',
        ])
      ], true);
      // Footer Property Validations
      (function () {
        $validations = [
          'enabled' => new ValidationProperties([
            'type' => 'boolean'
          ]),
          'name' => new ValidationProperties([
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
          'confirm' => new ValidationProperties([
            'type' => 'boolean'
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

      $this->internalProperties['htmlBindings']['classes']["'formFooter->actions->detailsToggle->hideByDefault'"]['condition'] = (function () {
        $enabled = &$this->formFooter['actions']['detailsToggle']['enabled'];
        $hideByDefault = &$this->formFooter['actions']['detailsToggle']['hideByDefault'];

        return $hideByDefault;
      })();
      $this->internalProperties['htmlBindings']['classes']["'formProperties->showAlerts'"] = [
        'name'      => 'hide-alerts',
        'condition' => (function () {
          $value = &$this->formProperties['showAlerts'];

          return $value === false;
        })()
      ];
      $this->internalProperties['htmlBindings']['classes']["'formProperties->spacing'"] = (function () {
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
            'type'            => 'password',
            'validations'     => [
              'required'         => false
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

        $this->addChild('field', [
          'properties'      => [
            'name'     => '_redirect',
            'hidden'   => true,
            'disabled' => (function () {
              $enabled = &$this->formResult['redirect']['enabled'];
              $useQueryParam = &$this->formResult['redirect']['useQueryParam'];
    
              return !($enabled && $useQueryParam);
            })(),
          ],
          'inputProperties' => [
            'type'        => 'text',
            'value'       => $_GET['redirect'] ?? $this->formResult['redirect']['location'],
            'validations' => [
              'validations' => [
                'isURL' => true
              ]
            ]
          ]
        ]);
      })();
      // Footer
      (function () {
        $object = &$this->formFooter['object'];
        $footerState = (function () {
          $state = &$this->formFooter['enabled'];

          return !$state;
        })();
        $actions = &$this->formFooter['actions'];
        
        // Section
        $object = $this->addChild('section', [
          'properties'   => [
            'name'       => '_footer',
            'disabled'   => &$footerState,
            'hidden'     => &$footerState,
            'customHTML' => [
              'classes' => [
                'footer'
              ]
            ]
          ]
        ]);

        foreach ($actions as $button => $configuration) {
          $state = (function () use (&$actions, $button) {
            $state = &$actions[$button]['enabled'];

            return !$state;
          })();
          $child = $object->addChild('button', [
            'properties'   => [
              'name'          => $button,
              'customHTML'    => [
                'classes'        => &$actions[$button]['classes'],
                'attributes'     => &$actions[$button]['attributes']
              ],
              'disabled'      => &$state,
              'hidden'        => &$state,
            ],
            'inputProperties' => [
              'type'             => $button == 'detailsToggle'
                                    ? 'button'
                                    : $button,
              'content'          => &$actions[$button]['content'],
              'title'            => &$actions[$button]['title'],
              'tooltip'          => &$actions[$button]['tooltip']
            ]
          ]);
          $child->updateProperty('internalProperties->htmlDefaults->classes', [ $button ], true);
        }
      })();

      unset($this->_internalProperties);
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
        $method = strtoupper($this->formProperties['method']);

        if ($method == 'GET')  { return $_GET; }
        if ($method == 'POST') { return $_POST; }
      })();
      $validations = (function () {
        $properties = [];

        $processChildren = function ($children) use (&$processChildren, &$properties) {
          foreach ($children as $name => $childClass) {
            $childInputProps = $childClass->findReferencedProperty('inputProperties');
            $childProps = &$childInputProps['validations'];

            if (get_class($childClass) == 'FormField' && $childProps) {
              // Value
              (function () use ($childInputProps, &$childProps) {
                $validationValue = &$childProps->value;

                if (!$validationValue) {
                  $validationValue = $childInputProps['value'];
                }
              })();
              if ($childProps->readonly === true) {
                if (preg_match('/^_auth_(?:token|timestamp|bypass)$/', $name) == 0) {
                  $childValue = $childInputProps['value'];

                  if ($childValue !== "") {
                    $childProps->validations['match'] = [ $childValue ];
                  }
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
      $checkedParams = check_parameters($requestParams, $validations);

      // Check auth parameters
      (function () use (&$_mysqli, $checkedParams) {
        $authValidationError = function ($error, $serverParam, $providedParam) use (&$_mysqli) {
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

          $this->formSubmit['success'] = false;
          $this->formSubmit['errors'][] = errorObject('invalidRequest', null, 'Something was wrong with your request. Please <a class="themed" href=" " title="Refresh the current page" aria-label="Refresh the current page">refresh</a> the page and try again.');
          $this->formSubmit['response']->set(-2);

          if (!$result) {
            error_log("Form Validation Error: Failed to record invalid form request for \"{$_SERVER['REMOTE_ADDR']}\".");
          }
        };

        // Auth Token
        (function () use ($checkedParams, $authValidationError) {
          $server = $_SESSION['token'] ?? false;
          $provided = $checkedParams['parameters']['_auth_token'] ?? false;
          $isValid = $server
                        && $provided
                        && auth_strHashCheck($server, $provided);

          if (!$isValid) {
            $authValidationError('invalidToken', $server, $provided);
          }
        })();
        // Auth Honeypot
        (function () use ($checkedParams, $authValidationError) {
          $field = $checkedParams['parameters']['_auth_debug'] ?? null;
          $isValid = !isset($field);

          if (!$isValid) {
            $authValidationError('invalidBypass', null, $field);
          }
        })();
        // Auth Timestamp
        (function () use ($checkedParams, $authValidationError) {
          $server = $_SESSION['timestamp'] ?? false;
          $provided = $checkedParams['parameters']['_auth_timestamp'] ?? false;
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

      $this->formSubmit['parameters'] = $checkedParams['parameters'];

      return $this->formSubmit['success'];
    }
    /**
     * Generate the form's response object
     * 
     * @return object Returns the form's response object. 
     */
    public function buildResponse () {
      $props = &$this->formSubmit;
      $response = &$props['response'];

      $response->setPayload($props['success'], 'form_result');
      
      if ($props['success']) {
        $result = $this->formResult;
        /** @var array The form result action payload */
        $payload = array_fill_keys(['toast', 'redirect', 'modal'], false);
        
        // Toast
        (function () use ($result, &$payload) {
          $toast = $result['toast'];

          if ($toast['enabled']) {
            $defaultProps = [
              'settings' => [
                'id'       => "{$this->internalProperties['id']}_response_toast",
                'template' => 'formResponse'
              ]
            ];
            $toast = array_replace_recursive($defaultProps, $toast['properties']);
            $method = strtolower($toast['method']);

            if ($method == 'response') {
              $payload['toast'] = $toast;
            }
            else if ($method == 'session') {
              $_SESSION['toasts'][] = $toast;
            }
          }
        })();
        // Redirect
        (function () use ($result, &$payload) {
          $redirect = $result['redirect'];

          if ($redirect['enabled']) {
            $payload['redirect'] = [
              'delay'    => $redirect['delay'],
              'location' => (function () use ($redirect) {
                $url = (function () use ($redirect) {
                  $useRedirectField = $redirect['useQueryParam'];

                  if ($useRedirectField && isset($_GET['_redirect'])) {
                    return $_GET['_redirect'];
                  }
                  else if ($useRedirectField && isset($_POST['_redirect'])) {
                    return $_POST['_redirect'];
                  }
                  else {
                    return $redirect['location'];
                  }
                })();

                return clean_url($url);
              })()
            ];
          }
        })();
        // Modal
        (function () use ($result, &$payload) {
          $modal = $result['modal'];

          if ($modal['enabled']) {
            $payload['modal'] = true;
          }
        })();

        if ($response->statusCode === null) {
          $response->set(1);
        }

        $response->setPayload($payload, 'form_result_actions');
      }

      foreach ($props['warnings'] as &$warning) {
        $replacement = 'Field';

        if (isset($warning['parameter'])) {
          $parameter = $this->getChild($warning['parameter']);

          if ($parameter) {
            $label = $parameter->findReferencedProperty('content->title');

            if ($label) {
              $replacement = $label;
            }
          }
        }

        $warning['message'] = str_replace('Parameter', $replacement, $warning['message']);
        $response->setWarning($warning);
      }
      foreach ($props['errors'] as &$error) {
        $replacement = 'Field';

        if (isset($error['parameter'])) {
          $parameter = $this->getChild($error['parameter']);

          if ($parameter) {
            $label = $parameter->findReferencedProperty('content->title');

            if ($label) {
              $replacement = $label;
            }
          }
        }

        $error['message'] = str_replace('Parameter', $replacement, $error['message']);

        $response->setError($error);
      }

      return $response;
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
      $markup .= $this->getChildrenMarkup();
      // Closing Form Base
      (function () use (&$markup) {
        $markup .= '</div>'; // Closing form content wrapper tag
        $markup .= '</form>'; // Closing form tag
      })();
      // Markup cleanup
      (function () use (&$markup) {
        $allowedTags = '<div><span><p><ul><ol><li><strong><em><b><i><a><code><pre><form><fieldset><label><legend><input><select><option><button><textarea>';

        $markup = clean_html($markup, $allowedTags);
        $markup = collapseWhitespace($markup);
      })();

      return $markup;
    }
  }
  /** A section in a form */
  class FormSection extends FormCore {
    /**
     * @var array Internal form section properties
     */
    protected $_internalProperties = [
      'htmlBindings' => [
        'classes'    => [
          "'properties->size'" => [
            [ 'name'  => 'inheritProperty' ],
            [ 'name'  => 'inheritValue' ]
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

      unset($this->_internalProperties);
    }
    /**
     * Retrieve the HTML Markup for the form section
     * 
     * @return string|false Returns the form section's *HTML Markup string* on success, or **false** if an error occurred.
     */
    protected function getElementMarkup () {
      /** @var string The form section's *HTML Markup string* */
      $markup = "";

      // Section Base
      $markup .= "<div {$this->getAttributeMarkup()}>";
      // Section Header
      $markup .= $this->getHeaderMarkup();
      // Children
      $markup .= $this->getChildrenMarkup();
      // Closing Section Base
      $markup .= '</div>';
      // Markup cleanup
      (function () use (&$markup) {
        $allowedTags = '<div><span><p><ul><ol><li><strong><em><b><i><a><code><pre><fieldset><label><legend><input><select><option><button><textarea>';

        $markup = clean_html($markup, $allowedTags);
        $markup = collapseWhitespace($markup);
      })();

      return $markup;
    }
  }
  /** A field in a form */
  class FormField extends FormCore {
    protected $_internalProperties = [
      'htmlBindings' => [
        'classes' => [
          "'inputProperties->type'" => [
            'name' => 'inheritValue'
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
     * @var array Properties that define and control the form field.
     * - `false|string $placeholder` — The placeholder or toggle value to be displayed in the field.
     * - - For some *Input Fields*, this value is used as the *placeholder* in the field.
     * - - For *Toggle Fields*, this value is used as the *label* of the toggle button.
     */
    protected $_inputProperties = [
      'placeholder' => false
    ];

    /**
     * Initialize the form field class 
     * 
     * @param array $properties An array of properties that are to be passed to the field.
     * @return void
     */
    public function __construct ($properties) {
      $this->updateProperty('internalProperties->propertyValidations', [
        "'inputProperties->type'"        => new ValidationProperties([
          'type'        => 'string',
          'validations' => [
            'match' => [
              'checkbox',
              'color',
              'date',
              'datetime',
              'datetimetz',
              'email',
              'file',
              'number',
              'month',
              'password',
              'radio',
              'range',
              'search',
              'select',
              'toggle-button',
              'toggle-box',
              'tel',
              'text',
              'textarea',
              'time',
              'tz',
              'url',
              'week'
             ]
          ]
        ]),
        "'inputProperties->placeholder'" => new ValidationProperties([
          'type'        => 'boolean|string'
        ])
      ], true);
      
      $this->construct($properties);

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
            'date' => "(?:\d{2,4}[\-\/\\\]\d{2,4}[\-\/\\\]\d{2,4})+",
            'time' => "(?:\d{2}\:\d{2,4}\:\d{2,4}(?:\.\d{1,5}){0,1})+",
            'tz'   => "[a-zA-Z_\/]{3,}"    
          ];
          $dateValue = [];
          $datePlaceholder = [];

          foreach ($datePieces as $piece => $regex) {
            if (strpos($type, $piece) !== false) {
              $value = (function () use ($regex) {
                $value = $this->inputProperties['value'] ?? "";
                $matches = [];

                if ($value != "" && preg_match("%{$regex}%", $value, $matches)) {
                  return $matches[0];
                }
                else {
                  return false;
                }
              })();
              $placeholder = (function () use ($regex) {
                $placeholder = $this->inputProperties['placeholder'] ?? false;
                $matches = [];

                if ($placeholder !== false && preg_match("%{$regex}%", $placeholder, $matches)) {
                  return $matches[0];
                }
                else {
                  return false;
                }
              })();
              $range = (function () use ($regex) {
                $range = $this->inputProperties['validations']->validations['range'] ?? false;
                $ranges = [];
                
                if ($range !== false) {
                  foreach ($range as $type => $threshold) {
                    $matches = [];

                    if (preg_match("%{$regex}%", $threshold, $matches)) {
                      $ranges[$type] = $matches[0];
                    }
                    else {
                      $ranges[$type] = false;
                    }
                  }

                  return $ranges;
                }
                else {
                  return false;
                }
              })();
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

              $this->addChild('field', [
                'properties' => [
                  'name'        => $piece,
                  'size'        => $piece == 'tz'
                                   ? 'full'
                                   : 'half'
                ],
                'inputProperties' => [
                  'type'             => $piece == 'tz'
                                        ? 'select'
                                        : $piece,
                  'value'            => $value,
                  'placeholder'      => $placeholder,
                  'options'          => $options,
                  'validations'      => [
                    'validations'       => [
                      'range'             => $range
                    ]
                  ]
                ]
              ]);
            }
          }
        }
      })();

      unset($this->_internalProperties);
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
        'input'    => array_search($type, [ 'textarea', 'select' ]) === false,
        'multi'    => array_search($type, [ 'checkbox', 'radio', 'select', 'toggle-button', 'toggle-box' ]) !== false,
        'datetime' => array_search($type, [ 'datetime', 'datetimetz', 'tz' ]) !== false
      ];

      // Field Base
      (function () use (&$markup, $types) {
        $markup .= "<fieldset {$this->getAttributeMarkup($types['input'] && $types['multi'])}>";
        $markup .= '<div class="fieldset-wrapper">';
      })();
      // Field wrapper
      (function () use (&$markup, $types) {
        // if (!$types['multi']) {
          $classes = implode(' ', [
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
              : ''
          ]);
  
          $markup .= "<div class=\"{$classes}\">";
        // }
      })();
      // Title & Subtitle
      $markup .= $this->getHeaderMarkup(['title', 'subtitle']);
      // Input markup
      (function () use (&$markup, $type, $types) {
        if ($type != 'group') {
          $tagName = $types['input']
                     ? 'input'
                     : $type;

          // Input & Select Fields
          if ((!$types['multi'] || $type == 'select') && !$types['datetime']) {
            $markup .= "<div class=\"input-container {$type}\">";
              // Input element opening tag
              (function () use (&$markup, $type, $types, $tagName) {
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
                  'placeholder' => (function () use ($types) {
                    $placeholder = $this->inputProperties['placeholder'];
  
                    if ($placeholder !== false && !$types['multi']) { return 'placeholder="' . clean_all_html($placeholder) . '" '; }
                    else                                            { return ''; }
                  })(),
                  'value'       => (function () use ($types) {
                    $value = $this->inputProperties['value'];
  
                    if ($value !== null && $types['input']) { return 'value="' . clean_all_html($value) . '" '; }
                    else                                    { return ''; }
                  })(),
                  'id'          => (function () {
                    $id = clean_all_html($this->internalProperties['id']);
  
                    return "{$id}_input";
                  })(),
                  'type'        => $type != 'select'
                                   ? "type=\"{$type}\" "
                                   : "",
                  'disabled'    => $this->properties['disabled']
                                   ? 'disabled '
                                   : '',
                  'hidden'      => $this->properties['hidden']
                                   ? 'hidden '
                                   : '',
                  'validations' => (function () use ($type, $types) {
                    $str = "";
                      $settings = $this->inputProperties['validations'];
                      $validations = $settings->validations;
  
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
                              'date',
                              'month',
                              'week',
                              'time',
                              'number',
                              'range'
                            ],
                            'length' => [
                              'email',
                              'password',
                              'search',
                              'tel',
                              'text',
                              'url'
                            ]
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
                          if (isset($range['min']) || isset($range['is'])) {
                            $key = 'min';
                            $value = null;
    
                            if ($rangeType == 'length') {
                              $key .= 'Length';
                            }
    
                            if (isset($range['min'])) {
                              $value = $range['min'];
                            }
                            else {
                              $value = $range['is'];
                            }
    
                            $str .= "{$key}=\"{$value}\" ";
                          }
                          if (isset($range['max']) || isset($range['is'])) {
                            $key = 'max';
                            $value = null;
    
                            if ($rangeType == 'length') {
                              $key .= 'Length';
                            }
    
                            if (isset($range['max'])) {
                              $value = $range['max'];
                            }
                            else {
                              $value = $range['is'];
                            }
    
                            $str .= "{$key}=\"{$value}\" ";
                          }
                        }
                      }
                      if (isset($validations['pattern'])) {
                        $delimiters = "[\/~@;%`]";
                        $pattern = clean_all_html(
                                     preg_replace(
                                       "/^{$delimiters}|{$delimiters}[a-zA-Z]{0,}$/", 
                                       '', 
                                       $validations['pattern']
                                     )
                                   );
    
                        $str .= "pattern=\"{$pattern}\" ";
                      }
  
                      return $str;
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
                              {$pieces['validations']} 
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
          }
          // Checkbox, Radio, & Toggle Fields
          else if ($types['multi']) {
            $options = $this->findReferencedProperty('inputProperties->options');
            $optionCount = 1;

            $addOption = function ($name, $value, $label) use (&$markup, $type, &$optionCount) {
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
                  if ($value !== '') { return clean_all_html($value); }
                  else               { return ""; }
                })(),
                'id'          => (function () use ($optionCount) {
                  $id = clean_all_html($this->internalProperties['id']);

                  return "{$id}_option_{$optionCount}";
                })(),
                'label'         => clean_all_html($label),
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
                })()
              ];
              $attributeMarkup = (function () use ($pieces) {
                $markup = $this->getAttributeMarkup();

                $markup = preg_replace('/id\=\"[\w\d_]+\"/', "id=\"{$pieces['id']}\"", $markup);

                return $markup;
              })();

              $markup .= "<div class=\"field {$pieces['validations']}\" {$pieces['hidden']}>
                            <div class=\"input-container {$type}\">
                              <input
                                {$attributeMarkup}
                                name=\"{$pieces['name']}\" 
                                value=\"{$pieces['value']}\" 
                                type=\"{$pieces['type']}\" 
                                {$pieces['validations']}
                                {$pieces['defaultOption']}>
                            </div>
                            <label
                              id=\"{$pieces['id']}_label\" 
                              for=\"{$pieces['id']}\">
                              {$pieces['label']}
                          </div>";

              $optionCount++;
            };

            $markup .= '<div class="children">';

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
      // Children elements
      $markup .= (function () {
        $children = $this->getChildrenMarkup();

        if ($children != "") {
          return "<div class=\"children\">
                    {$children}
                  </div>";
        }
        else {
          return "";
        }
      })();
      // Description & Alerts
      $markup .= $this->getHeaderMarkup(['description', 'alerts']);
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
        $allowedTags = '<div><span><p><ul><ol><li><strong><em><b><i><a><code><pre><fieldset><label><legend><input><select><option><button><textarea>';

        $markup = clean_html($markup, $allowedTags);
        $markup = collapseWhitespace($markup);
      })();

      return $markup;
    }
  }
  /** A button in a form */
  class FormButton extends FormCore {
    /**
     * @var array Properties that define and control the form button.
     * - `false|string $label` — The content of the button. 
     * - - `<span>` tags are permitted inside of the button.
     * - `false|string $title` — The assistive text/alternative label of the button. 
     * - `array $tooltip` — Properties for adding a *tooltip* to the button.
     * - - `false|string $content` — The content of the tooltip. If **false**, no tooltip will be added.
     * - - `'top'|'right'|'bottom'|'left'|false $pos` — The position of the tooltip relative to the target. 
     * - - `'top'|'right'|'bottom'|'left'|false $align` — The alignment of the tooltip relative to the target. 
     * - - `false|'short'|'medium'|'long' $delay` — Indicates the delay that *focus* layers have before appearing.
     * - - `false|string $name` — The *layer name* used for callbacks or styling.
     */
    protected $_inputProperties = [
      'content' => false,
      'title'   => false,
      'tooltip' => [
        'content'  => false,
        'pos'      => 'top',
        'align'    => false,
        'delay'    => 'medium',
        'name'     => false
      ]
    ];

    /**
     * Initialize the form field class 
     * 
     * @param array $properties An array of properties that are to be passed to the field.
     * @return void
     */
    public function __construct ($properties) {
      $this->updateProperty('internalProperties->propertyValidations', [
        "'inputProperties->type'"    => new ValidationProperties([
          'type'        => 'string',
          'validations' => [
            'match' => [
              'button',
              'reset',
              'submit'
             ]
          ]
        ]),
        "'inputProperties->content'" => new ValidationProperties([
          'type'              => 'string',
          'sanitizeParameter' => false,
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
          'type'        => 'string',
          'validations' => [
            'range' => [
              'max' => 256
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
              'max' => 128
            ]
          ]
        ]),
      ], true);
      $this->updateProperty("internalProperties->htmlBindings->classes->'inputProperties->tooltip->content'", [
        'name' => 'layer-target',
        'condition' => (function () {
          $content = &$this->inputProperties['tooltip']['content'];

          return $content !== false;
        })()
      ], true);
      
      $this->construct($properties);

      unset($this->_internalProperties);
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

        $markup .= "<div class=\"{$classes}\">";
      })();
      // Title & Subtitle
      $markup .= $this->getHeaderMarkup(['title', 'subtitle']);
      // Input markup
      (function () use (&$markup, $type) {
        if ($type != 'group') {
          // $markup .= "<div class=\"input-container {$type}\">";
            // Input element opening tag
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

                    return "title=\"{$title}\" aria-label=\"{$title}\" ";
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
              if ($type == 'submit') {
                $markup .= '<div class="spinner" title="Loading Indicator" aria-label="Loading Indicator">
                              <span class="dot"></span>
                              <span class="dot"></span>
                              <span class="dot"></span>
                              <span class="dot"></span>
                            </div>';
              }

              $markup .= '</span>';
            })();
            $markup .= "</button>";

            // Tooltip
            (function () use (&$markup) {
              $tooltip = $this->inputProperties['tooltip'];

              if ($tooltip['content'] !== false) {
                $markup .= "<div class=\"layer tooltip\"";

                foreach ($tooltip as $attr => $value) {
                  if ($attr == 'content') {
                    continue;
                  }

                  if ($value !== false) {
                    $markup .= " data-layer-{$attr}=\"{$value}\"";
                  }
                }

                $markup .= ">";
                $markup .= clean_html($tooltip['content']);
                $markup .= "</div>";
              }
            })();
          // $markup .= "</div>"; // Input container closing tag
        }
      })();
      // Children elements
      $markup .= (function () {
        $children = $this->getChildrenMarkup();

        if ($children != "") {
          return "<div class=\"children\">
                    {$children}
                  </div>";
        }
        else {
          return "";
        }
      })();
      // Description & Alerts
      $markup .= $this->getHeaderMarkup(['description']);
      // Closing field wrapper
      (function () use (&$markup) {
        $markup .= '</div>';
      })();
      // Closing Field Base
      (function () use (&$markup) {
        $markup .= '</div>'; // Closing fieldset wrapper tag
        $markup .= '</fieldset>'; // Closing fieldset tag
      })();
      // Markup cleanup
      (function () use (&$markup) {
        $allowedTags = '<div><span><p><ul><ol><li><strong><em><b><i><a><code><pre><fieldset><label><legend><input><select><option><button><textarea>';

        $markup = clean_html($markup, $allowedTags);
        $markup = collapseWhitespace($markup);
      })();

      return $markup;
    }
  }
?>