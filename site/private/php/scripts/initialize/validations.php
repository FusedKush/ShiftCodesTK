<?php
  // Validation Functions
  /**
   * Check if a parameter is *present* and *not empty*
   * - To be considered *present*, the parameter must not be **null** and it must not be an blank `string` or empty `array`.
   * 
   * @param mixed $parameter The parameter to be checked.
   * @return boolean Returns **true** if the parameter is considered to be *present*, or **false** if it is not.
   */
  function check_isPresent ($parameter = null) {
    if (!isset($parameter)) {
      return false;
    }
    if (is_string($parameter) && strlen(collapseWhitespace($parameter)) == 0) {
      return false;
    } 
    if (is_array($parameter) && count($parameter) == 0) {
      return false;
    }

    return true;
  }
  /**
   * Check if a parameter matches one of the provided values.
   * 
   * @param mixed $parameter The parameter to check. 
   * - If the parameter is an `array`, each value of the provided array must match a value in the `$matches` array.
   * @param array $matches An array of values that the parameter is allowed to match.
   * @param array $properties An array of additional matching options.
   * - `boolean $strict` — Indicates if strict (`===`) comparison is to be used. This has no effect if `$isHash` is also set to **true**.
   * - `boolean $caseSensitive` — Indicates if the comparison is to be *case sensitive*. This has no effect if `$isHash` is also set to **true**.
   * - `boolean $isHash` — Indicates if the `$parameter` and `$matches` are to be compared as *hashes*.
   * @return boolean Returns **true** if the parameter matches one of the provided values, or **false** if it does not.
   */
  function check_match ($parameter, array $matches, array $properties = []) {
    $isMatch = true;
    $matchingProperties = (function () use ($properties) {
      $defaultProperties = [
        'strict'        => true,
        'caseSensitive' => true,
        'isHash'        => false
      ];
      
      return array_replace_recursive($defaultProperties, $properties);
    })();

    $checkMatch = function ($value) use ($matches, &$isMatch, $matchingProperties) {
      $cValue = $matchingProperties['caseSensitive']
                ? $value
                : strtolower($value);
                
      foreach ($matches as $match) {
        if ($matchingProperties['isHash']) {
          if (!is_string($value)) {
            trigger_error("check_match Error: Only strings can be compared as hashes.");
            $isMatch = false;
            return false;
          }

          if (hash_equals($match, $value)) {
            return true;
          }
        }
        else {
          $cMatch = $matchingProperties['caseSensitive']
                    ? $match
                    : strtolower($match);

          if ($matchingProperties['strict']) {
            if ($cValue === $cMatch) {
              return true;
            }
          }
          else {
            if ($cValue == $cMatch) {
              return true;
            }
          }
        }
      }

      $isMatch = false;
      return false;
    };


    if (is_array($parameter)) {
      array_walk_recursive($parameter, function ($value, $key) use (&$checkMatch) {
        $checkMatch($value);
      });
    }
    else {
      $checkMatch($parameter);
    }

    return $isMatch;
  }
  /**
   * Check if a parameter lies within a specified range
   * 
   * @param mixed $parameter The parameter to check. How the parameter is validated is determined by the *type*.
   * @param "string"|"number"|"array"|"date" $type Indicates how the parameter and range options are to be compared.
   * - **string** — The minimum/exact/maximum length of the string.
   * - **number** — The minimum/exact/maximum number.
   * - **array** — The minimum/exact/maximum number of array items.
   * - **date** — The minimum/exact/maximum date.
   * @param mixed $range An array of options that specify the permitted range.
   * - `int $min` — The minimum required range.
   * - `int $is` — The exact required range.
   * - `int $max` — The maximum required range.
   * @return boolean Returns **true** if the parameter lies within the specified range, or **false** if it does not.
   */
  function check_range ($parameter, string $type, array $range) {
    if (array_search($type, [ 'string', 'number', 'array', 'date' ]) !== false) {
      $invalidParameterError = function ($paramType) use ($type) {
        trigger_error("check_range Error: A {$paramType} must be provided for type \"{$type}\".");
      };

      if (!is_string($parameter) && ($type == 'string' || $type == 'date')) {
        $invalidParameterError('string');
        return false;
      }
      if (!is_numeric($parameter) && $type == 'number') {
        $invalidParameterError('int or float');
        return false;
      }
      if (!is_array($parameter) && $type == 'array') {
        $invalidParameterError('array');
        return false;
      }
    }
    else {
      trigger_error("check_range Error: \"{$type}\" is not a valid value for the type argument.");
    }

    $values = (function () use ($parameter, $type, $range) {
      $values = [];

      foreach ($range as $rangeType => $threshold) {
        if ($type == 'date') {
          $values[$rangeType] = new DateTime($threshold);
        }
        else {
          $values[$rangeType] = $threshold;
        }
      }
      
      if ($type == 'string') { $values['parameter'] = strlen($parameter); }
      if ($type == 'number') { $values['parameter'] = (int) $parameter; }
      if ($type == 'array')  { $values['parameter'] = count($parameter); }
      if ($type == 'date')   { $values['parameter'] = new DateTime($parameter); }

      return $values;
    })();

    if ($range === false) {
      trigger_error('check_range Error: Provided type is not a valid value.');
      return false;
    }

    if (isset($values['min']) && $values['min'] > $values['parameter']) {
      return false;
    }
    if (isset($values['is']) && $values['is'] != $values['parameter']) {
      return false;
    }
    if (isset($values['max']) && $values['max'] < $values['parameter']) {
      return false;
    }

    return true;
  }
  /**
   * Check if a parameter matches a Regular Expression pattern
   * 
   * @param string $parameter The parameter to check. 
   * @param string $pattern The Regular Expression pattern to match.
   * @return boolean Returns **true** if the parameter matches the given expression, or **false** if it does not.
   */
  function check_pattern (string $parameter, string $pattern) {
    return preg_match($pattern, $parameter) == 1;
  }
  /**
   * Check if a parameter is a valid date
   * 
   * @param string $parameter The parameter to check.
   * @param array $formats A list of formats that the date must match to be considered valid. 
   * @return boolean Returns **true** if the parameter is a valid date, or **false** if it is not.
   */
  function check_date (string $parameter, array $formats = []) {
    /** @var array The pieces of the date. */
    $pieces = [];

    if (count($formats) > 0) {
      foreach ($formats as $format) {
        $date = DateTime::createFromFormat($format, $parameter);

        if ($date && $date->format($format) == $parameter) {
          $pieces = date_parse_from_format($format, $parameter);
          break;
        }
      }

      if (count($pieces) == 0) {
        return false;
      }
    }
    else {
      $pieces = date_parse($parameter);
    }

    return checkdate($pieces['month'], $pieces['day'], $pieces['year']);
  }
  /**
   * Check if a parameter is a valid URL
   * 
   * @param string $parameter The parameter to check.
   * @param "any"|"relative"|"absolute" $type Indicates what type of URL to search for. 
   * - **any** — Matches both *relative* and *absolute URLs*.
   * - **absolute** — Matches only *absolute URLs*.
   * - **relative** — Matches only *relative URLs*.
   * @return boolean Returns **true** if the parameter is considered to be a valid string, or **false** if it is not.
   */
  function check_url (string $parameter, string $type = 'any') {
    /** @var string The trimmed parameter */
    // $trimmedParam = collapseWhitespace($parameter);
    $parsedParam = collapseWhitespace($parameter);
    $patterns = [
      'full'     => '%^(?:(?:(?:https?|ftp):)?\/\/)?(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}]\.)+(?:[a-z\x{00a1}-\x{ffff}]{2,}\.?))(?::\d{2,5})?(?:[\/?#]\S*)?$%iuS',
      'relative' => "%^(?:(?:\.|\/|#)+(?:[\w\d\-._~!$&'()*+,;=]+|$))+%i"
    ];

    if ($type != 'relative' && check_pattern($parsedParam, $patterns['full'])) {
      return true;
    }
    else if ($type != 'absolute' && check_pattern($parsedParam, $patterns['relative'])) {
      return true;
    }
 
    return false;
  }

  // Bulk Validation
  /**
   * Validation Properties for a parameter. Use `check_parameter()` or `check_parameters()` to validate a parameter using these options.
   */
  class ValidationProperties {
    /**
     * @var boolean Indicates if the parameter is *required* and cannot be empty.
     */
    public $required = false;
    /**
     * @var boolean Indicates if the parameter is *readonly* and cannot be modified. 
     */
    public $readonly = false;
    /**
     * @var string The allowed type(s) of the parameter. Multiple types can be separated by a `|`.
     * - *PHP Types* — `boolean`, `integer`, `float`, `string`, `array`, `object`, `callable`, `iterable`, `resource`, & `null`
     * - *Other Types* — `any`, `hash`, `date`, & `url`
     */
    public $type = 'any';
    /**
     * @var mixed A default, inherited, or required value of the parameter.
     */
    public $value = null;
    /** 
     * @var boolean Indicates if the input and output parameters are to be *sanitzed*. 
     */
    public $sanitizeParameter = true;
    /**
     * @var boolean Generate a warning if the parameter is *missing* or *empty* and `$required` is set to **false**.
     */
    public $emptyWarning = false;
    /**
     * @var array A list of validations to run against the value of the parameter. 
     * - The **Key** refers to the *type of validation* to run and the **Value** refers to the *validation arguments*.
     * - - **match** — `array $properties` An array of additional matching options.
     * - - - `boolean $strict` — Indicates if strict (`===`) comparison is to be used. This has no effect if `$isHash` is also set to **true**.
     * - - - `boolean $caseSensitive` — Indicates if the comparison is to be *case sensitive*. This has no effect if `$isHash` is also set to **true**.
     * - - - `boolean $isHash` — Indicates if the `$parameter` and `$matches` are to be compared as *hashes*.
     * - - **range** — `array $range` An array of options that specify the permitted range.
     * - - - `int $min` — The minimum required range.
     * - - - `int $is` — The exact required range.
     * - - - `int $max` — The maximum required range.
     * - - **pattern** — `string $pattern` The Regular Expression pattern to match.
     * - - **url** — `"any"|"relative"|"absolute" $type` Indicates what type of URL to search for. 
     * - - - **any** — Matches both *relative* and *absolute URLs*.
     * - - - **absolute** — Matches only *absolute URLs*.
     * - - - **relative** — Matches only *relative URLs*.
     */
    public $validations = [];
    /**
     * @var array
     * Default validation messages for warnings and errors
     * - Each *value* can be either a `string` with the validation message, or an `array` of messages for each parameter type.
     * - Validation Messages specified in `$customValidationMessages` will take prescendence over those found here.
     * - Valid issue types include: 
     * - - `invalidType`
     * - - `valueMissing`, `valueMismatch`
     * - - `rangeUnderflow`, `rangeMismatch`, `rangeUnderflow`
     * - - `patternMismatch`
     */
    protected $validationMessages = [];
    /**
     * @var array
     * Custom validation messages for warnings and errors
     * - Each *value* can be either a `string` with the validation message, or an `array` of messages for each parameter type.
     * - These messages will take prescendence over those specified by `$validationMessages`.
     * - Valid issue types include: 
     * - - `invalidType`
     * - - `valueMissing`, `valueMismatch`
     * - - `rangeUnderflow`, `rangeMismatch`, `rangeUnderflow`
     * - - `patternMismatch`
     */
    public $customValidationMessages = [];
    

    /**
     * Instantiate a new parameter
     * 
     * @param array $properties A list of properties to pass to the ValidationProperties object.
     * @return void 
     */
    public function __construct($properties = []) {
      $this->validationMessages = [
        'invalidType'     => 'Parameter is not of a valid type.',
        'valueMissing'    => 'Parameter cannot be left empty.',
        'valueMismatch'   => 'Parameter is not one of the permitted values.',
        'rangeUnderflow'  => array_merge(
          array_fill_keys([ 'string', 'hash', 'url' ], 'Parameter must be longer than ${threshold} character(s).'),
          array_fill_keys([ 'int', 'float', 'date' ], 'Parameter must be greater than ${threshold}.'),
          array_fill_keys([ 'array', 'object' ], 'Parameter must contain at least ${threshold} item(s).')
        ), 
        'rangeMismatch'   => array_merge(
          array_fill_keys([ 'string', 'hash', 'url' ], 'Parameter must be exactly ${threshold} character(s).'),
          array_fill_keys([ 'int', 'float', 'date' ], 'Parameter must be exactly ${threshold}.'),
          array_fill_keys([ 'array', 'object' ], 'Parameter must contain exactly ${threshold} item(s).')
        ), 
        'rangeUnderflow'  => array_merge(
          array_fill_keys([ 'string', 'hash', 'url' ], 'Parameter must be shorter than ${threshold} characters.'),
          array_fill_keys([ 'int', 'float', 'date' ], 'Parameter must be less than ${threshold}.'),
          array_fill_keys([ 'array', 'object' ], 'Parameter must contain less than ${threshold} items.')
        ),
        'patternMismatch' => 'Parameter must match the requested format'
      ];

      foreach ($properties as $key => $value) {
        $this->$key = $value;
      }
    }
    /**
     * Validate a parameter against multiple constraints.
     * 
     * @param mixed $parameter The parameter to check.
     * @param string|false $key An optional key to be output in warnings and errors.
     * @return array Returns the an array with the results of the validation.
     * - `boolean $valid` — Indicates if the parameter is to be considered valid or not.
     * - `mixed|null $parameter` — The validated parameter. This will be **null** if the parameter is invalid.
     * - `array $warnings` — An array of warnings generated while validating the parameter.
     * - `array $errors` — An array of errors generated while validating the parameter.
     */
    function check_parameter ($parameter, $key = false) {
      /** @var mixed The updated parameter. */
      $param = $parameter;
      /** @var array The results of the validation. */
      $result = [
        'valid'     => true,
        'parameter' => &$param,
        'warnings'  => [],
        'errors'    => []
      ];

      $validationIssue = function ($validationIssue, $includeProvidedParameter = true) use (&$param, $key, &$result) {
        $providedParameter = $includeProvidedParameter
                             ? $param
                             : null;

        $validationMessage = (function () use ($validationIssue, $providedParameter) {
          $message = $this->customValidationMessages[$validationIssue]
                    ?? $this->validationMessages[$validationIssue];

          if (is_array($message)) {
            if ($this->type != 'any') {
              $message = $message[$this->type] ?? "";
            }
            else {
              $message - $message[gettype($providedParameter)] ?? "";
            }
          }

          return $message;
        })();

        if (!$this->required) {
          $result['warnings'][] = errorObject($validationIssue, $key, $validationMessage, $providedParameter, $this->value);

          if ($this->value !== null) {
            $param = $this->value;
          }
        }
        else {
          $result['valid'] = false;
          $param = null;
          $result['errors'][] = errorObject($validationIssue, $key, $validationMessage, $providedParameter);
        }
      };

      if ($this->sanitizeParameter && is_string($param)) {
        $param = clean_all_html($param);
      }

      if (check_isPresent($param)) {
        $type = (function () use (&$param) {
          if ($this->type != 'any') {
            $typeChecks = [
              'boolean'  => is_bool($param) || $param == 'true' || $param == 'false',
              'integer'  => is_numeric($param) && is_int((int) $param),
              'float'    => is_numeric($param) && is_float((float) $param),
              'string'   => is_string($param),
              'array'    => is_array($param),
              'object'   => is_object($param),
              'callable' => is_callable($param),
              'iterable' => is_iterable($param),
              'resource' => is_resource($param),
              'null'     => is_null($param),
              'hash'     => is_string($param),
              'date'     => is_string($param) && check_date($param),
              'url'      => is_string($param) && check_url($param)
            ];
            $validTypes = explode('|', $this->type);
    
            foreach ($validTypes as $type) {
              if ($typeChecks[$type] ?? false) {
                if ($type == 'boolean') {
                  if ($param == 'true') {
                    $param = true;
                  }
                  else if ($param == 'false') {
                    $param = false;
                  }
                }
                else if ($type == 'integer') {
                  $param = (int) $param;
                }
                else if ($type == 'float') {
                  $param = (float) $param;
                }

                return $type;
              }
            }

            return false;
          }
          else {
            return gettype($param);
          }

        })();

        if ($type !== false) {
          // Check Match
          (function () use ($param, $validationIssue, $type) {
            $match = $this->validations['match'] ?? false;

            if ($param !== null && $match) {
              $matchResult = false;

              if (is_array_associative($match)) {
                $matchResult = check_match($param, $match['matches'], $match['properties']);
              }
              else {
                $matchResult = check_match($param, $match);
              }

              if (!$matchResult) {
                $validationIssue('valueMismatch');
              }
            }
          })();
          // Check Range
          (function () use ($param, $validationIssue, $type) {
            $range = $this->validations['range'] ?? false;
    
            if ($param !== null && $range) {
              foreach ($range as $rangeType => $threshold) {
                $rangeCategory = (function () use ($type) {
                  if ($type == 'string' || $type == 'hash' || $type == 'url') {
                    return "string";
                  }
                  else if ($type == 'int' || $type == 'float') {
                    return "number";
                  }
                  else if ($type == 'array') {
                    return "array";
                  }
                  else if ($type == 'date') {
                    return "date";
                  }
                  else {
                    return false;
                  }
                })();
                
                if ($rangeCategory && !check_range($param, $rangeCategory, [ $rangeType => $threshold ])) {
                  $issues = [
                    'min' => 'rangeUnderflow',
                    'is'  => 'rangeMismatch',
                    'max' => 'rangeOverflow'
                  ];

                  $validationIssue($issues[$rangeType], $param);
                }
              }
            }
          })();
          // Check Pattern
          (function () use ($param, $validationIssue, $type) {
            $pattern = $this->validations['pattern'] ?? false;
    
            if ($param !== null && $pattern) {
              if (!check_pattern($param, $pattern)) {
                $validationIssue('patternMismatch');
              }
            }
          })();
        }
        // Invalid Type
        else {
          $validationIssue('invalidType');
        }
      }
      else if ($this->required || $this->emptyWarning) {
        $validationIssue('valueMissing', false);
      }

      return $result;
    }
  };
  /**
   * Validate a list of parameters against a matching list of constraints
   * - For a parameter to be validated, it must be present in both the `$parameterList` and `$propertiesList` with the *same key*.
   * 
   * @param array $parameterList An associative array of properties to be validated. 
   * @param array $propertiesList An indexed array of `ValidationProperties` objects.  
   * @return array Returns the an array with the results of the validation.
     * - `boolean $valid` — Indicates if the provided list of parameters are to be considered valid or not.
     * - `array $parameters` — An array of validated parameters. Each parameter can be accessed by its provided *key*.
     * - `array $warnings` — An array of warnings generated while validating the parameter list.
     * - `array $errors` — An array of errors generated while validating the parameter list.
   */
  function check_parameters (array $parameterList, array $propertiesList) {
    $result = [
      'valid'      => true,
      'parameters' => [],
      'warnings'   => [],
      'errors'     => []
    ];

    foreach ($propertiesList as $key => &$properties) {
      if (get_class($properties) != 'ValidationProperties') {
        $result['errors'][] = errorObject('invalidValidationProperties', null, "The ValidationProperties provided for \"{$key}\" is not a valid ValidationProperties object.");
        continue;
      }

      $validation = $properties->check_parameter($parameterList[$key] ?? null, $key);

      $result['parameters'][$key] = $validation['parameter'];

      if ($validation['warnings']) {
        $result['warnings'] = array_merge_recursive($result['warnings'], $validation['warnings']);
      }
      if ($validation['errors']) {
        $result['errors'] = array_merge_recursive($result['errors'], $validation['errors']);
        $result['valid'] = false;
      }
    }

    return $result;
  }
?>