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
        'relative' => "%^(?:(?:\.|\/|#){0,1}(?:[\w\d\-._~!$&'()*+,;=]+|\?.+$|$))+$%i"
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