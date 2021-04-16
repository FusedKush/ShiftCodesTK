<?php
  /** The namespace for variable validations */
  namespace ShiftCodesTK\Validations {
    use ShiftCodesTK\Strings;

    /** Constants */
    /** @var array[] An `Associative Array` of `array`s representing the various *Variable Types* that can be evaluated. */
    define("ShiftCodesTK\Validations\VARIABLE_TYPES", [
      'mixed'    => [ 'mixed', 'any' ],
      'bool'     => [ 'bool', 'boolean' ],
      'int'      => [ 'int', 'integer' ],
      'float'    => [ 'float', 'double' ],
      'string'   => [ 'string' ],
      'array'    => [ 'array' ],
      'object'   => [ 'object' ],
      'function' => [ 'function', 'callable' ],
      'iterable' => [ 'interable' ],
      'resource' => [ 'resource' ],
      'null'     => [ 'null', 'void' ]
    ]);
    /** @var array An `Array` representing all of the various *Variable Types* that can be evaluated. */
    define("ShiftCodesTK\Validations\ALL_VARIABLE_TYPES", array_merge(...array_values(VARIABLE_TYPES)));
    /**
     * @var array[]
     * Default validation messages for warnings and errors
     * - Each *value* can be either a `string` with the validation message, or an `array` of messages for each parameter type.
     * - Possible Types include: 
     * - - `typeMismatch`
     * - - `valueMissing`, `valueMismatch`
     * - - `rangeUnderflow`, `rangeMismatch`, `rangeUnderflow`
     * - - `patternMismatch`
     * - - `invalidDate`, `invalidURL`
     */
    define('ShiftCodesTK\Validations\VALIDATION_MESSAGES', [
      'typeMismatch'  => '${param} must be a ${requiredType}.',
      'valueMissing'  => '${param} must be provided.', 
      'valueMismatch' => '${param} is not of a permitted value.',
      'rangeUnderflow' => array_merge(
        array_fill_keys(array_merge( VARIABLE_TYPES['int'], VARIABLE_TYPES['float'] ), '${param} must be greater than ${threshold}.'), 
        array_fill_keys(array_merge( VARIABLE_TYPES['array'], VARIABLE_TYPES['object'] ), 'At least ${threshold} option${plural} are required for ${param}.'), 
        array_fill_keys(VARIABLE_TYPES['string'], '${param} must be longer than ${threshold} character${plural}.')
      ),
      'rangeMismatch' => array_merge(
        array_fill_keys(array_merge( VARIABLE_TYPES['int'], VARIABLE_TYPES['float'] ), '${param} must be exactly ${threshold}.'), 
        array_fill_keys(array_merge( VARIABLE_TYPES['array'], VARIABLE_TYPES['object'] ), 'Exactly ${threshold} option${plural} are required for ${param}.'), 
        array_fill_keys(VARIABLE_TYPES['string'], '${param} must be exactly ${threshold} character${plural}.')
      ),
      'rangeOverflow' => array_merge(
        array_fill_keys(array_merge( VARIABLE_TYPES['int'], VARIABLE_TYPES['float'] ), '${param} must be less than ${threshold}.'), 
        array_fill_keys(array_merge( VARIABLE_TYPES['array'], VARIABLE_TYPES['object'] ), 'No more than ${threshold} option${plural} are allowed for ${param}.'), 
        array_fill_keys(VARIABLE_TYPES['string'], '${param} must be shorter than ${threshold} character${plural}.')
      ),
      'patternMismatch' => '${param} does not match the required format.',
      'invalidDate' => '${param} is not a valid Date.',
      'invalidURL' => '${param} is not a valid URL.',
      'invalidPath' => '${param} is not a valid File Path.'
    ]);

    /** @var int Matches are to be compared using *Loose Comparison* (`==`). The default behavior is to use *Strict Comparison* (`===`).
     * - Has no effect if `MATCH_HASH` is also provided.
     * - Valid for the `check_match()` function.
     * - - @see Validations\check_match()
     */
    const MATCH_LOOSELY = 1;
    /** @var int Matches are to be compared *Case Sensitively*. The default behavior is to match *Case Insensitively*.
     * - Has no effect if `MATCH_HASH` is also provided.
     * - Valid for the `check_match()` function.
     * - - @see Validations\check_match()
     */
    const MATCH_CASE_SENSITIVE = 2;
    /** @var int Matches are to be compared as *Hashes*. The default behavior is to match as normal *Strings*.
     * - Valid for the `check_match()` function.
     * - - @see Validations\check_match()
     */
    const MATCH_HASH = 4;
    /** @var int Matches are to be compared against a *Blacklist*. The default behavior is to match against a *Whitelist*.
     * - Valid for the `check_match()` function.
     * - - @see Validations\check_match()
     */
    const MATCH_BLACKLIST = 8;

    /** @var int Matches both *Absolute* and *Relative* URLs and Paths. */
    const PATH_ANY = -1;
    /** @var int Matches only *Absolute* URLs and Paths. */
    const PATH_ABSOLUTE = 1;
    /** @var int Matches only *Relative* URLs and Paths. */
    const PATH_RELATIVE = 2;

    /** Check if a variable has been defined and is present.
     * 
     * For a variable to be considered *defined* and *present*, it must not be **null**, an *empty string*, or an *empty array*.
     * 
     * @param mixed $var The variable to be evaluated.
     * @return bool Returns **true** if the `$var` is defined and present, or **false** if it is not.
     */
    function check_var ($var = null) {
      if (!isset($var)) {
        return false;
      }
      else if (is_string($var)) {
        $str = trim($var);

        if (mb_strlen($str) === 0) {
          return false;
        }
      }
      else if (is_array($var) && count($var) == 0) {
        return false;
      }

      return true;
    }
    /** Check the *Variable Type* of a variable
     * 
     * @param mixed $var The variable being tested.
     * @param string $allowed_types A `string` representing the types the `$var` is permitted to be of. 
     * - Multiple types can be specified using a pipe (`|`). 
     * - You can specify the name of a *Class* to test if the `$var` is an instance of the designated class.
     * @return string|false Returns the *Variable Type* if it matches of the `$allowed_types`, or **false** if the `$var` is not of a permitted type.
     */
    function check_type ($var = null, string $allowed_types) {
      if (in_array($allowed_types, [ 'mixed', 'any' ]) === false) {
        $typeChecks = array_merge(
          array_fill_keys([ 'bool', 'boolean' ], function () use ($var) { 
            return is_bool($var); 
          }),
          array_fill_keys([ 'int', 'integer' ], function () use ($var) { 
            return is_int($var) || (is_numeric($var) && !Strings\substr_check($var, '.')); 
          }),
          array_fill_keys([ 'float', 'double' ], function () use ($var) { 
            return is_float($var) || (is_numeric($var) && Strings\substr_check($var, '.')); 
          }),
          array_fill_keys([ 'function', 'callable' ], function () use ($var) { 
            return is_callable($var);
          }),
          array_fill_keys([ 'null', 'void' ], function () use ($var) { 
            return is_null($var);
          }),
          [
            'string' => function () use ($var) { 
              return is_string($var);
            },
            'array' => function () use ($var) { 
              return is_array($var);
            },
            'object' => function () use ($var) { 
              return is_object($var);
            },
            'iterable' => function () use ($var) { 
              return is_iterable($var);
            },
            'resource' => function () use ($var) { 
              return is_resource($var);
            },
          ]
        );
        $validTypes = explode('|', $allowed_types);

        foreach ($validTypes as $type) {
          $typeFunc = $typeChecks[$type] ?? null;

          if (isset($typeFunc)) {
            if ($typeFunc()) {
              return $type;
            }
          }
          else {
            if (class_exists($type)) {
              if (is_object($var) && is_a($var, $type)) {
                return true;
              }
            }
            else {
              trigger_error("\"{$type}\" is not a valid Variable Type or Class Name.");
              continue;
            }
          }
        }

        return false;
      }
      else {
        return gettype($var);
      }
    }
    /** Check if a variable matches a set of values
     * 
     * For the `$var` to be considered a *match*, it must or must not match one of the provided `$matches`, depending on the presence of the `MATCH_BLACKLIST` flag. 
     * If `$var` is an `array`, all of the array values must or must not match one of the provided `$matches` to be considered a *match*.
     * 
     * @param mixed $var The variable to be evaluated.
     * @param array $matches A list of values the `$var` should (whitelist) or should not (blacklist) match.
     * @param int $flags A *Bitmask* representing option flags:
     * 
     * | Flag | Description |
     * | --- | --- |
     * | `MATCH_LOOSELY` | Matches are to be compared using *Loose Comparison* (`==`). The default behavior is to use *Strict Comparison* (`===`). |
     * | `MATCH_CASE_SENSITIVE` | Matches are to be compared *Case Sensitively*. The default behavior is to match *Case Insensitively*. |
     * | `MATCH_HASH` | Matches are to be compared as *Hashes*. The default behavior is to match as normal *Strings*. |
     * | `MATCH_BLACKLIST` | Matches are to be compared against a *Blacklist*. The default behavior is to match against a *Whitelist*. |
     * @return bool Depending on the presence of the `MATCH_BLACKLIST` flag, returns **true** if the `$var` does or does not match one or more of the `$matches`. Otherwise, returns **false**.
     */
    function check_match ($var, array $matches, int $flags = 0) {
      try {
        $result = true;
        $caseInsensitive = ($flags & MATCH_CASE_SENSITIVE) == 0 || ($flags & MATCH_HASH > 0);
        $checkMatch = function ($var_to_check) use ($matches, $flags, $caseInsensitive) {
          $comparisonVar = is_string($var_to_check) && $caseInsensitive
                           ? Strings\transform($var_to_check, Strings\TRANSFORM_LOWERCASE)
                           : $var_to_check;

          foreach ($matches as $originalMatch) {
            $match = is_string($originalMatch) && $caseInsensitive
                     ? Strings\transform($originalMatch, Strings\TRANSFORM_LOWERCASE)
                     : $originalMatch;
            $isMatch = false;

            // Compare as Strings
            if (($flags & MATCH_HASH) == 0) {
              $isMatch = ($flags & MATCH_LOOSELY) == 0
                         ? $comparisonVar === $match
                         : $comparisonVar == $match;
            }
            // Compare as Hashes
            else {
              $isMatch = \ShiftCodesTK\Auth\check_hash_string($match, $comparisonVar);
            }
            
            if ($isMatch) {
              if (($flags & MATCH_BLACKLIST) == 0) {
                return true;
              }
              else if (($flags & MATCH_BLACKLIST) > 0) {
                return false;
              }
            } 
          }

          return ($flags & MATCH_BLACKLIST) > 0;
        };

        if (is_array($var)) {
          foreach ($var as $element) {
            $result = $checkMatch($element);

            if ($result != (($flags & MATCH_BLACKLIST) == 0)) {
              return $result;
            }
          }
        }
        else {
          return $checkMatch($var);
        }
        
        return true;
      }
      catch (\Throwable $exception) {
        return false;
      }
    }
    /** Check if a variable lies within a specified range
     * 
     * @param string|int|float|array $var The variable being evaluated.
     * - The variable can be one of the following types, determining how the variable is evaluated:
     * - - A *Date* as a `string` recognized by `DateTime`.
     * - - An `array`.
     * - - An `int` or `float`.
     * - - A `string`.
     * @param array $range An `array` that specifies the range thresholds the `$var` has to abide by.
     * - There are three range thresholds that you can define. Each threshold value should be an `int` or `float`, unless `$var` is a `date`, in which case the threshold value should be a `string`.
     * | Threshold | Description |
     * | --- | --- |
     * | *min* | The minimum date, count, value, or length of `$var`. |
     * | *is* | The required date, count, value, or length of `$var`. The same as defining both `min` and `max` with the same value. |
     * | *max* | The maximum date, count, value, or length of `$var`. |
     * - These values constrain the `$var` differently depending on the `$var`'s type:
     * | Type | Description |
     * | --- | --- |
     * | *date* | The `$var`'s date must be less than, equal to, or greater than the respective *threshold value*. |
     * | *array* | The `$var` must contain less than, equal to, or greater than the respective *threshold value* number of elements. |
     * | *numeric* | The `$var` must be less than, equal to, or greater than the respective *threshold value*. |
     * | *string* | The `$var` must have less than, equal to, or greater than the respective *threshold value* number of characters. |
     * @return bool Returns **true** if the `$var` lies within the specified `$range`, or **false** if it does not.
     * @throws UnexpectedValueException Throws an `UnexpectedValueException` if `$var` is an invalid type, or if `$range` contains invalid threshold values.
     */
    function check_range ($var, array $range) {
      $comparisonType = (function () use ($var, $range) {
        $checkType = function ($var_to_check) {
          if (validateDate($var_to_check)) {
            return 'date';
          }
          else if (is_array($var_to_check)) {
            return 'array';
          }
          else if (is_numeric($var_to_check)) {
            return 'numeric';
          }
          else if (is_string($var_to_check)) {
            return 'string';
          }
          
          return false;
        };

        $comparisonType = $checkType($var);
        
        if (!$comparisonType) {
          throw new \UnexpectedValueException("The variable being evaluated must be a Parsable Date, Array, Int/Float, or String.");
        }

        foreach ($range as $type => $threshold) {
          $valueType = $checkType($threshold);

          if (is_numeric($threshold)) {
            if ($comparisonType == 'date') {
              throw new \UnexpectedValueException("The \"{$type}\" threshold value must be a valid Date String.");
            }
          }
          else if (is_string($threshold)) {
            if ($comparisonType != 'date') {
              throw new \UnexpectedValueException("The \"{$type}\" threshold value must be an Integer or Float when working with {$comparisonType}s.");
            }
          }
          else {
            throw new \UnexpectedValueException("\"{$threshold}\" is not a valid threshold value.");
          }
        }

        return $comparisonType;
      })();
      $values = (function () use ($var, $range, $comparisonType) {
        $valueList = array_merge($range, [ 'var' => $var ]);
        $values = [];

        foreach ($valueList as $name => $value) {
          switch ($comparisonType) {
            case 'date':
              $values[$name] = (new \DateTime($value))->getTimestamp();
              break;
            case 'array':
              $compValue = $name == 'var'
                            ? count($value)
                            : $value;

              $values[$name] = $compValue;
              break;
            case 'numeric':
              $compValue = $name == 'var'
                            ? (int) $value
                            : $value;

              $values[$name] = $compValue;
              break;
            case 'string':
              $compValue = $name == 'var'
                            ? Strings\strlen($value)
                            : $value;

              $values[$name] = $compValue;
              break;
          }
        }

        return $values;
      })();
      $isInRange = (
        (!isset($values['is']) || $values['is'] === $values['var'])
        && (!isset($values['min']) || $values['min'] <= $values['var'])
        && (!isset($values['max']) || $values['max'] >= $values['var'])
      );

      // if (isset($values['min']) && $values['min'] > $values['var']) {
      //   return false;
      // }
      // else if (isset($values['is']) && $values['is'] != $values['var']) {
      //   return false;
      // }
      // else if (isset($values['max']) && $values['max'] < $values['var']) {
      //   return false;
      // }

      return $isInRange;
    }
    /** An *alias* of `Strings\preg_test()`
     * 
     * > Test if a string matches a *Regular Expression*
     * @see Strings\preg_test()
     * 
     * @param string $var The variable to be evaluated.
     * @param string $pattern The *Regular Expression Pattern*, as a `string`.
     * @return bool Returns **true** if the `$var` matches the provided `$pattern`, or **false** if it does not.
     */
    function check_pattern (string $var, string $pattern) {
      return Strings\preg_test($var, $pattern);
    }
    /** Check if a variable represents a valid *Date*.
     * 
     * @param string $var The variable to be evaluated.
     * @param string|array $formats A format `string` or an indexed `array` of valid *DateTime Formats* accepted by {@see `DateTime::createFromFormat()`}.
     * - Some predefined formats are available via the {@see `ShiftCodesTK\DATE_FORMATS`} constant.
     * @return bool Returns **true** if `$var` represents a valid *date* and, if provided, matches one of the `$formats`. Otherwise, returns **false**.
     */
    function check_date (string $var, $formats = []) {  
      if (is_string($formats)) {
        $formats = [ $formats ];
      }

      if (count($formats) > 0) {
        foreach ($formats as $format) {
          $formattedDate = \DateTime::createFromFormat($format, $var);
    
          if ($formattedDate && $formattedDate->format($format) == $var) {
            $parsedDate = date_parse_from_format($format, $var);
  
            if ($parsedDate && !$parsedDate['error_count']) {
              return checkdate($parsedDate['month'], $parsedDate['day'], $parsedDate['year']);
            }
          }
        }
  
        return false;
      }
      else {
        $parsedDate = date_parse($var);

        if ($parsedDate && !$parsedDate['error_count']) {
          return checkdate($parsedDate['month'], $parsedDate['day'], $parsedDate['year']);
        }

        return false;
      }
    }
    /** Check if a variable represents a valid URL.
     * 
     * This does not validate if the remote resource exists or not.
     * 
     * @param string $var The variable being evaluated.
     * @param PATH_ANY|PATH_ABSOLUTE|PATH_RELATIVE $type Indicates the *URL Type* that the `$var` must match: *Relative*, *Absolute*, or either. Defaults to **PATH_ANY**.
     * @return bool Returns **true** if the `$var` represents a valid URL and matches the `$type`. Otherwise, returns **false**.
     */
    function check_url (string $var, int $type = PATH_ANY) {
      $trimmedVar = Strings\trim($var);
      $patterns = [
        'full'     => '%^(?:(?:(?:https?|ftp):)?\/\/)?(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z0-9\x{00a1}-\x{ffff}][a-z0-9\x{00a1}-\x{ffff}_-]{0,62})?[a-z0-9\x{00a1}-\x{ffff}]\.)+(?:[a-z\x{00a1}-\x{ffff}]{2,}\.?))(?::\d{2,5})?(?:[\/?#]\S*)?$%iuS',
        'relative' => "%^(?:(?:\.|\/|#)+(?:[\w\d\-._~!$&'()*+,;=]+|$))+%i"
      ];

      if ($type & PATH_ABSOLUTE) {
        if (check_pattern($trimmedVar, $patterns['full'])) {
          return true;
        }
      }
      if ($type & PATH_RELATIVE) {
        if (check_pattern($trimmedVar, $patterns['relative'])) {
          return true;
        }
      }

      return false;
    }
    /** Check if a variable represents a valid *File* or *Directory Path*.
     * 
     * This does not validate if the specified file or directory exists.
     * 
     * @param string $var The variable being evaluated.
     * @param PATH_ANY|PATH_ABSOLUTE|PATH_RELATIVE $type Indicates the *URL Type* that the `$var` must match: *Relative*, *Absolute*, or either. Defaults to **PATH_ANY**.
     * @return bool Returns **true** if the `$var` represents a valid File or Directory Path and matches the `$type`. Otherwise, returns **false**.
     */
    function check_path (string $var, int $type = PATH_ANY) {
      $trimmedVar = Strings\trim($var);
      $blacklistedCharacters = "
        \/
        \\\
        \1-\31
        \<
        \>
        \:
        \\\"
        \|
        \?
        \*
        \r
        \n";
      $patterns = [
        // 'full'     => "~ ^ ( (?: (?: \/|\|\.|[a-zA-Z]\:|file\:\/\/ ) (?: \/|\ ){0,2} ) (?: (?: [^\/\-\<\>\:\"\|\?\*\.]+ (?: \/|\ ){1,2} )+? ) ) ( [^\/\-\<\>\:\"\|\?\*]+? [^\/\-\<\>\:\"\|\?\*\ ] (?: \.[\w\d]+ ){0,1} ) $ ~i",
        'full'     => <<<EOT
          ~                                         # Opening Delimiter
            ^                                       # Start of Line
              (                                     # [1] Start of File Path Capture Group
                (?:                                 # Start of Prefix Non-Capture Group
                  (?:                               # Start of Prefixes Non-Capture Group
                    \/|\\\|\.|[a-zA-Z]\:|file\:\/\/ # Supported Prefixes
                  )                                 # End of Prefixes Non-Capture Group
                  (?:                               # Start of Directory Separator Non-Capture Group
                    \/|\\\                          # Directory Separators
                  ){0,2}                            # End of Directory Separator Non-Capture Group
                )                                   # End of Prefix Non-Capture Group
                (?:                                 # Start of File Path Non-Capture Group
                  (?:                               # Start of Directory-Separator Non-Capture Group
                    [^{$blacklistedCharacters}\.]+  # One or More non-blacklisted Characters, including *Periods* (`.`)
                    (?:                             # Start of Directory Separator Non-Capture Group
                      \/|\\\                        # Directory Separators
                    ){1,2}                          # End of Directory Separator Non-Capture Group
                  )+?                               # End of Directory-Separator Non-Capture Group
                )                                   # End of File Path Non-Capture Group
              ){0,1}                                # [1] End of File Path Capture Group
              (                                     # [2] Start of File Name Capture Group
                [^{$blacklistedCharacters}]+?       # One or More non-blacklisted Characters, Lazy
                [^{$blacklistedCharacters}\ ]       # One non-blacklisted Characters, including spaces.
                (?:                                 # Start of File Extension Non-Capture Group
                  \.[\w\d]+                         # File Extension
                ){0,1}                              # End of File Extension Non-Capture Group
              )                                     # [2] End of File Name Capture Group
            $                                       # End of Line
                                                    # Closing Delimiter
                                                    # Pattern Flags
          ~
          ix
        EOT,
        'relative' => "%^(?:(?:\.|\/|#)+(?:[\w\d\-._~!$&'()*+,;=]+|$))+%i"
      ];

      // var_dump(\ShiftCodesTK\Strings\preg_replace($patterns['full'], [ '/#(.+)[\r\n]/', '/(?<!\\\)\s/' ], ''));

      if ($type & PATH_ABSOLUTE) {
        if (check_pattern($trimmedVar, $patterns['full'])) {
          return true;
        }
      }
      if ($type & PATH_RELATIVE) {
        if (check_pattern($trimmedVar, $patterns['relative'])) {
          return true;
        }
      }

      return false;
    }
    /**
     * Validate a list of variables against a corresponding list of constraints.
     * 
     * Variables from `$variables` are only checked and returned if they have a corresponding entry in the `$constraints` array. All other `$variables` are ignored.
     * 
     * @param array $variables An `Associative Array` representing the variables being evaluated.
     * @param array $constraints An indexed array of `ValidationProperties` objects.  
     * @return array Returns the an array with the results of the validation.
       * - `boolean $valid` — Indicates if the provided list of parameters are to be considered valid or not.
       * - `array $parameters` — An array of validated parameters. Each parameter can be accessed by its provided *key*.
       * - `array $warnings` — An array of warnings generated while validating the parameter list.
       * - `array $errors` — An array of errors generated while validating the parameter list.
     */
    function check_variables (array $variables, array $constraints) {
      $result = [
        'valid'      => true,
        'parameters' => [],
        'warnings'   => [],
        'errors'     => []
      ];

      foreach ($constraints as $key => &$properties) {
        if (get_class($properties) != 'ValidationProperties') {
          $result['errors'][] = errorObject('invalidValidationProperties', null, "The ValidationProperties provided for \"{$key}\" is not a valid ValidationProperties object.");
          continue;
        }

        $validation = $properties->check_parameter($variables[$key] ?? null, $key);

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
  }
  namespace {

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
  define('VALIDATION_MESSAGES', [
    'typeMismatch' => array_merge(
      [
        'boolean'  => '${param} cannot be a boolean.',
        'string'   => '${param} cannot be a string.',
        'callable' => '${param} cannot be callable.',
        'iterable' => '${param} cannot be iterable.',
        'array'    => '${param} cannot be an array.',
        'object'   => '${param} cannot be an object.',
        'resource' => '${param} cannot be a resource.',
        'NULL'     => '${param} cannot be NULL.',
        'hash'     => '${param} cannot be a hash.',
        'date'     => '${param} cannot be a date.',
        'url'      => '${param} cannot be a URL.'
      ],
        array_fill_keys([ 'integer', 'float' ], '${param} cannot be a number.'),
    ),
    'valueMissing'    => array_merge(
        [ 'date' => 'You must provide a date for ${param}.' ],
        array_fill_keys([ 'boolean', 'string', 'callable', 'iterable', 'resource', 'NULL', 'hash', 'url' ], '${param} is required.'),
        array_fill_keys([ 'integer', 'float' ], 'You must provide a value for ${param}.'),
        array_fill_keys([ 'array', 'object' ], 'You must provide an option for ${param}')
    ), 
    'valueMismatch'   => '${param} is not one of the permitted values.',
    'rangeUnderflow'  => array_merge(
      [
        'string' => '${param} must be longer than ${threshold} character${plural}.',
        'date'   => '${param} must be after ${threshold}.'
      ],
      array_fill_keys([ 'integer', 'float' ], '${param} must be greater than ${threshold}.'),
      array_fill_keys([ 'array', 'object' ], 'At least ${threshold} option${plural} are required for ${param}.')
    ), 
    'rangeMismatch'   => array_merge(
      [ 'string' => '${param} must be exactly ${threshold} character${plural}.' ],
      array_fill_keys([ 'date', 'integer', 'float' ], '${param} must be exactly ${threshold}.'),
      array_fill_keys([ 'array', 'object' ], 'Exactly ${threshold} option${plural} are required for ${param}.')
    ), 
    'rangeOverflow'  => array_merge(
      [
        'string' => '${param} must be shorter than ${threshold} character${plural}.',
        'date'   => '${param} must be before ${threshold}.'
      ],
      array_fill_keys([ 'integer', 'float' ], '${param} must be less than ${threshold}.'),
      array_fill_keys([ 'array', 'object' ], 'No more than ${threshold} option${plural} are required for ${param}.')
    ),
    'patternMismatch' => '${param} contains invalid characters.'
  ]);

  // Validation Functions
  /**
   * Check if a parameter is *present* and *not empty*
   * - To be considered *present*, the parameter must not be **null** and it must not be an blank `string` or empty `array`.
   * 
   * @deprecated `1.5.0` Use {@see ShiftCodesTK\Validations\check_var()} instead.
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
   * @deprecated `1.5.0` Use {@see ShiftCodesTK\Validations\check_match()} instead.
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
        'caseSensitive' => false,
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
   * @deprecated `1.5.0` Use {@see ShiftCodesTK\Validations\check_range()} instead.
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
          $values[$rangeType] = (function () use ($threshold) {
            $datetime = new DateTime($threshold);

            return $datetime->getTimestamp();
          })();
        }
        else {
          $values[$rangeType] = $threshold;
        }
      }
      
      if ($type == 'string') { $values['parameter'] = strlen($parameter); }
      if ($type == 'number') { $values['parameter'] = (int) $parameter; }
      if ($type == 'array')  { $values['parameter'] = count($parameter); }
      if ($type == 'date')   { $values['parameter'] = (function () use ($parameter) {
        $datetime = new DateTime($parameter);

        return $datetime->getTimestamp();
      })(); }

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
   * @deprecated `1.5.0` Use {@see ShiftCodesTK\Validations\check_pattern()} instead.
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
   * @deprecated `1.5.0` Use {@see ShiftCodesTK\Validations\check_date()} instead.
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
   * @deprecated `1.5.0` Use {@see ShiftCodesTK\Validations\check_url()} instead.
   * 
   * @param string $parameter The parameter to check.
   * @param "any"|"relative"|"absolute" $type Indicates what type of URL to search for. 
   * - **any** — Matches both *relative* and *absolute URLs*.
   * - **absolute** — Matches only *absolute URLs*.
   * - **relative** — Matches only *relative URLs*.
   * @return boolean Returns **true** if the parameter is considered to be a valid URL, or **false** if it is not.
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
  /**
   * Check if a parameter is a valid *Email Address*.
   * > _In general, this validates e-mail addresses against the syntax in RFC 822, with the exceptions that comments and whitespace folding and dotless domain names are not supported._
   * 
   * @deprecated `1.5.0` Use {@see ShiftCodesTK\Validations\check_email()} instead.
   * 
   * @param string $parameter The parameter to check.
   * @return bool Returns **true** if the parameter is considered to be a valid *Email Address*, or **false** if it is not.
   */
  function check_email (string $parameter) {
    return filter_var($parameter, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE) !== false;
  }

  // Bulk Validation
  /**
   * Validation Properties for a parameter. Use `check_parameter()` or `check_parameters()` to validate a parameter using these options.
   * 
   * @deprecated `1.5.0` Use {@see ShiftCodesTK\Validations\VariableEvaluator} instead.
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
     * - *PHP Types* — `boolean`, `integer`, `double` (`float`), `string`, `array`, `object`, `callable`, `iterable`, `resource`, & `null`
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
     * Custom validation messages for warnings and errors
     * - Each *value* can be either a `string` with the validation message, or an `array` of messages for each parameter type.
     * - Valid issue types include: 
     * - - `typeMismatch`
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
      // $this->validationMessages = [
      //   'invalidType'     => 'Parameter is not of a valid type.',
      //   'valueMissing'    => 'Parameter cannot be left empty.',
      //   'valueMismatch'   => 'Parameter is not one of the permitted values.',
      //   'rangeUnderflow'  => array_merge(
      //     array_fill_keys([ 'string', 'hash', 'url' ], 'Parameter must be longer than ${threshold} character(s).'),
      //     array_fill_keys([ 'int', 'float', 'date' ], 'Parameter must be greater than ${threshold}.'),
      //     array_fill_keys([ 'array', 'object' ], 'Parameter must contain at least ${threshold} item(s).')
      //   ), 
      //   'rangeMismatch'   => array_merge(
      //     array_fill_keys([ 'string', 'hash', 'url' ], 'Parameter must be exactly ${threshold} character(s).'),
      //     array_fill_keys([ 'int', 'float', 'date' ], 'Parameter must be exactly ${threshold}.'),
      //     array_fill_keys([ 'array', 'object' ], 'Parameter must contain exactly ${threshold} item(s).')
      //   ), 
      //   'rangeUnderflow'  => array_merge(
      //     array_fill_keys([ 'string', 'hash', 'url' ], 'Parameter must be shorter than ${threshold} characters.'),
      //     array_fill_keys([ 'int', 'float', 'date' ], 'Parameter must be less than ${threshold}.'),
      //     array_fill_keys([ 'array', 'object' ], 'Parameter must contain less than ${threshold} items.')
      //   ),
      //   'patternMismatch' => 'Parameter must match the requested format'
      // ];

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
      // The parameter type as it's being validated.
      $validationType = isset($param) 
                        ? gettype($param) 
                        : (
                          is_array($this->type) 
                          ? explode('|', $this->type, 1)[0] 
                          : $this->type
                        );

      $validationIssue = function ($validationIssue, $includeProvidedParameter = true, $threshold = null) use (&$param, $validationType, $key, &$result) {
        $providedParameter = $includeProvidedParameter
                             ? $param
                             : NULL;

        $validationMessage = (function () use ($key, $validationIssue, $threshold, $providedParameter, $validationType) {
          $message = $this->customValidationMessages[$validationIssue]
                    ?? VALIDATION_MESSAGES[$validationIssue];

          if (is_array($message)) {
            if ($validationType != 'any') {
              $message = $message[$validationType] ?? "";
            }
            else {
              $message = $message[gettype($providedParameter)] ?? "";
            }
          }

          // Variable replacements
          (function () use ($key, $threshold, &$message) {
            $message = str_replace('${param}', $key !== false ? $key : 'Parameter', $message);

            if ($threshold !== null) {
              $plural = checkPlural($threshold);

              $message = str_replace([ '${threshold}', '${plural}' ], [ $threshold, $plural ], $message);
            }
          })();

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
        // $param = clean_all_html($param);
      }
      if (!$this->sanitizeParameter) {
        trigger_error("Property \"sanitizeParameter\" of \"{$key}\"has been deprecated. Please manually sanitize your variables where you depend on this property having an effect.", E_USER_DEPRECATED);
      }

      if (check_isPresent($param)) {
        $type = (function () use (&$param) {
          if ($this->type != 'any') {
            $typeChecks = [
              'boolean'  => function () use ($param) { return is_bool($param) || $param == 'true' || $param == 'false'; },
              'integer'  => function () use ($param) { return is_numeric($param) && is_int((int) $param); },
              'float'    => function () use ($param) { return is_numeric($param) && is_float((float) $param); },
              'string'   => function () use ($param) { return is_string($param); },
              'array'    => function () use ($param) { return is_array($param); },
              'object'   => function () use ($param) { return is_object($param); },
              'callable' => function () use ($param) { return is_callable($param); },
              'iterable' => function () use ($param) { return is_iterable($param); },
              'resource' => function () use ($param) { return is_resource($param); },
              'null'     => function () use ($param) { return is_null($param); },
              'hash'     => function () use ($param) { return is_string($param); },
              'date'     => function () use ($param) { return is_string($param) && check_date($param); },
              'url'      => function () use ($param) { return is_string($param) && check_url($param); },
              'email'    => function () use ($param) { return is_string($param) && check_email($param); }
            ];
            $validTypes = explode('|', $this->type);
    
            foreach ($validTypes as $type) {
              if (isset($typeChecks[$type]) && $typeChecks[$type]()) {
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
          $validationType = $type;

          // Check Match
          (function () use ($param, $validationIssue) {
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

                  $validationIssue($issues[$rangeType], $param, $threshold);
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
          $validationIssue('typeMismatch');
        }
      }
      else {
        if ($this->required || $this->emptyWarning) {
          $validationIssue('valueMissing', false);
        }
        else if ($this->value !== null) {
          $param = $this->value;
        }
      }

      return $result;
    }
  };
  /**
   * Validate a list of parameters against a matching list of constraints
   * - For a parameter to be validated, it must be present in both the `$parameterList` and `$propertiesList` with the *same key*.
   * 
   * @deprecated `1.5.0` Use {@see ShiftCodesTK\Validations\check_variables()} instead.
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
}
?>