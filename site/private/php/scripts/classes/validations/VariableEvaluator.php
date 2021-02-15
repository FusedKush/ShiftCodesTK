<?php
  namespace ShiftCodesTK\Validations;
  use ShiftCodesTK\Strings;

  /** Represents a variable being tested */
  class VariableEvaluator {
    /** @var mixed The variable being evaluated. */
    protected $variable = null;
    /** @var null|EvaluationResult Represents the *Last Validation Result*, if one has been previously performed.
     * 
     * You can use the `get_last_result()` to retrieve these values.
     */
    protected $last_result = null;

    /**
     * @var string The allowed type(s) of the parameter. Multiple types can be separated by a pipe (`|`).
     * - See `VARIABLE_TYPES` for the list of supported types.
     */
    public $type = 'mixed';
    /**
     * @var boolean Indicates if the variable is *required* and cannot be empty. 
     * - Variables marked as `required` will throw an *Error* instead of a *Warning* if a validation fails.
     */
    public $required = false;
    /**
     * @var boolean Indicates if the variable is *readonly* and cannot be modified. 
     */
    public $readonly = false;
    /**
     * @var mixed The default, inherited, or required value of the parameter.
     * - If `required` if **false**, this value is inherited is the variable fails validation.
     * - If `readonly` is **true**, the variable value *must* match this value to be considered valid.
     */
    public $default_value = null;
    /**
     * @var boolean If **true** and `required` is set to **false**, logs a *Warning* if the variable is *empty*.
     */
    public $optional_empty_warning = false;
    /** @var array An `Associative Array` representing the checks to be performed on the variable.
     * - Checks are provided in the following format:
     * > `string` *Check Name* => `array` *Check Arguments*
     * - - *Check Name* refers to the *Validation Check* being performed. In other words, one of the `Validations\check_*` functions.
     * - - - `check_var` is not a valid option because it is always checked.
     * - - - Valid Checks include:
     * - - - - `check_match`
     * - - - - `check_range`
     * - - - - `check_pattern`
     * - - - - `check_date`
     * - - - - `check_url`
     * - - *Check Arguments* is an `array` made up of the *Arguments* of the *Validation Check* being performed.
     * - - - The first argument of each *Validation Check* (`$var`) does not need to be provided.
     */
    public $validations = [];
    
    /**
     * Start a new evaluation on a variable
     * 
     * @param array $configuration An `array` of properties to pass to the `VariableEvaluator`.
     * @param mixed $var The variable being evaluated. Can be omitted to use the evaluator for multiple variables.
     * @return VariableEvaluator Returns the new `VariableEvaluator` on success.
     */
    public function __construct(array $configuration = [], $var = null) {
      // Configuration Validations
      (function () use ($configuration) {
        // Type
        $type = $configuration['type'];

        if (!check_match($type, array_merge(array_keys(VARIABLE_TYPES), array_values(VARIABLE_TYPES)))) {
          throw new \UnexpectedValueException("\"{$type}\" is not a valid Variable Type.", 1);
        }
      })();

      foreach (\get_object_vars($this) as $property => $currentValue) {
        $configValue = $configuration[$property] ?? null;

        if (isset($configValue)) {
          $this->$property = $configValue;
        }
      }

      if (isset($var)) {
        $this->variable = $var;
      }
    }
    /**
     * Validate a parameter against multiple constraints.
     * 
     * @param mixed $var The parameter to check. Can be omitted to use the object `$variable`.
     * @param string|false $key An optional key to be output in the results.
     * @return bool Returns **true** if the `$var` is considered *Valid*, or **false** if it is not.
     * - You can use `get_last_result()` for more information about the validation result.
     * @throws \UnexpectedValueException if `$var` is omitted and no variable was previous set.
     */
    public function check_variable ($var = null, string $key = null): bool {
      /** @var mixed The updated parameter. */
      $param = (function () use ($var) {
        if (isset($var)) {
          return $var;
        }
        else {
          if (isset($this->variable)) {
            return $this->variable;
          }
          else {
            throw new \UnexpectedValueException("The variable was omitted, but none was set.");
          }
        }
      })();
      /** @var EvaluationResult */
      $result = (function () use (&$param, $key) {
        $result = new EvaluationResult;

        $result->variable = &$param;

        if (isset($key)) {
          $result->key = $key;
        }

        return $result;
      })();
      // The parameter type as it's being validated.
      $validationType = isset($param) 
                        ? gettype($param) 
                        : (
                          !is_array($this->type) 
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
            $message = Strings\str_replace($message, '${param}', isset($key) ? $key : 'Parameter');

            if ($threshold !== null) {
              $plural = checkPlural($threshold);

              $message = Strings\str_replace($message, [ '${threshold}', '${plural}' ], [ $threshold, $plural ]);
            }
          })();

          return $message;
        })();

        if (!$this->required) {
          $result->warnings[] = errorObject($validationIssue, $key, $validationMessage, $providedParameter, $this->default_value);

          if ($this->default_value !== null) {
            $param = $this->default_value;
          }
        }
        else {
          $result->result = false;
          $param = null;
          $result->errors[] = errorObject($validationIssue, $key, $validationMessage, $providedParameter);
        }
      };

      if (check_isPresent($param)) {
        $type = check_type($param, $this->type);

        if ($type !== false) {
          if ($type === 'string') {
            $param = (string) $param;
          }
          else if (array_search($type, [ 'bool', 'boolean' ]) !== false) {
            $param = (bool) $param;
          }
          else if (array_search($type, [ 'int', 'integer' ]) !== false) {
            $param = \ShiftCodesTK\Integers\TypeConv::to_int($param);
          }
          else if (array_search($type, [ 'float', 'double' ]) !== false) {
            $param = (float) $param;
          }

          $validationType = $type;

          // Check Match
          (function () use ($param, $validationIssue) {
            $match = $this->validations['check_match'] ?? null;

            if (isset($param) && isset($match)) {
              $matchResult = false;

              if (is_array_associative($match)) {
                $matchResult = check_match($param, $match['matches'], $match['flags']);
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
          (function () use ($param, $validationIssue) {
            $range = $this->validations['check_range'] ?? null;
    
            if (isset($param) && isset($range)) {
              foreach ($range as $rangeType => $threshold) {
                if (!check_range($param, [ $rangeType => $threshold ])) {
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
          (function () use ($param, $validationIssue) {
            $pattern = $this->validations['check_pattern'] ?? null;
    
            if (isset($param) && isset($pattern)) {
              if (!check_pattern($param, $pattern)) {
                $validationIssue('patternMismatch');
              }
            }
          })();
          // Check Date
          (function () use ($param, $validationIssue) {
            $date = $this->validations['check_date'] ?? null;
    
            if (isset($param) && isset($date)) {
              if (!check_date($param, is_array($date) ? $date : [])) {
                $validationIssue('invalidDate');
              }
            }
          })();
          // Check URL
          (function () use ($param, $validationIssue) {
            $url = $this->validations['check_url'] ?? null;
    
            if (isset($param) && isset($url)) {
              if (!check_url($param, is_int($url) ? $url : null)) {
                $validationIssue('invalidURL');
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
        if ($this->required || $this->optional_empty_warning) {
          $validationIssue('valueMissing', false);
        }
        else if ($this->default_value !== null) {
          $param = $this->default_value;
        }
      }

      $this->last_result = $result;

      return $result->result;
    }
    /** Get the last result of a *Variable Evaluation*.
     * 
     * @param string|false $property The name of a specific `EvaluationResult` Property to be retrieved. Defaults to **"result"**.
     * - Valid properties include `variable`, `key`, `result`, `warnings`, and `errors`.
     * - If **false**, the full `EvaluationResult` object will be returned.
     * @return mixed Returns the previous `EvaluationResult`, or a property from it, on success. Returns **null** if an Evaluation has not been performed yet, or if the provided `$property` is invalid.
     */
    public function get_last_result ($property = "result") {
      if (!isset($this->last_result)) {
        trigger_error("An Evaluation has not been performed yet.");
        return null;
      }

      if ($property !== false) {
        $propertyValue = $this->last_result->$property ?? null;

        if (isset($propertyValue)) {
          return $propertyValue;
        }
        else {
          trigger_error("\"{$property}\" is not a valid Result Property.");
          return null;
        }
      }
      else {
        return $this->last_result;
      }
    }
  };
?>