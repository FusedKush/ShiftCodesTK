<?php
  namespace ShiftCodesTK\Validations;
  use ShiftCodesTK\Strings;

  /** Represents a group of variables being evaluated. */
  class GroupEvaluator {
    /** @var null|GroupEvaluationResult Represents the *Last Validation Result*, if one has been previously performed.
     * 
     * You can use the `get_last_result()` to retrieve these values.
     */
    protected $last_result = null;

    /** @var VariableEvaluator[] An `Associative Array` of `VariableEvaluator` objects representing the checks to be performed on each of the variables.
     */
    protected $validations = [];
    
    /**
     * Initialize a new `GroupEvaluator` representing a group of variables to be evaluated
     * 
     * @param VariableEvaluator[] $validations An `array` of `VariableEvaluator` objects representing a group of *Variable Constriants*.
     * @return GroupEvaluator Returns the new `GroupEvaluator` object.
     * @throws \UnexpectedValueException If a `$validations` value is not an instance of `VariableEvaluator`.
     */
    public function __construct(array $validations = []) {
      foreach ($validations as $variableName => $evaluator) {
        if (!($evaluator instanceof VariableEvaluator)) {
          throw new \UnexpectedValueException("An Instance of the VariableEvaluator was not provided for \"{$variableName}\".");
        }

        $this->validations[$variableName] = $evaluator;
      }
    }
    /**
     * Validate a list of variables against the configured *Constraints*.
     * 
     * @param array $vars The list of parameters to be validated against the current Constraints.
     * @return bool Returns **true** if the `$vars` are all considered to be *Valid*. Returns **false** if one or more `$vars` are considered *Invalid*.
     * - You can use `get_last_result()` for more information about the validation result.
     */
    public function check_variables (array $vars): bool {
      $result = new GroupEvaluationResult();

      foreach ($this->validations as $variableName => $variableEvaluator) {
        $evaluationResult = $variableEvaluator->check_variable($vars[$variableName] ?? null, $variableName);
        /** @var VariableEvaluationResult */
        $fullEvaluationResult = $variableEvaluator->get_last_result(false);

        $result->variables[$variableName] = $fullEvaluationResult->variable;

        if ($fullEvaluationResult->warnings) {
          $result->warnings = array_merge($result->warnings, $fullEvaluationResult->warnings);
        }
        if (!$evaluationResult) {
          if ($result->result) {
            $result->result = false;
          }
          if ($fullEvaluationResult->errors) {
            $result->errors = array_merge($result->errors, $fullEvaluationResult->errors);
          }
        }
      }

      $this->last_result = $result;

      return $result->result;
    }
    /** Get the last result of a *Variable Evaluation*.
     * 
     * @param string|false $property The name of a specific `EvaluationResult` Property to be retrieved. Defaults to **"result"**.
     * - Valid properties include `variables`, `key`, `result`, `warnings`, and `errors`.
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
    /** Retrieve the last *Warning* generated during a Variable Evaluation.
     * 
     * @return string|false If a warning was generated during the last Variable Evaluation, returns the *Warning Message*. Returns **false** if no warning was generated.
     */
    public function get_last_warning () {
      $lastWarning = $this->get_last_result('warnings');

      if (isset($lastWarning)) {
        return $lastWarning[0]['message'];
      }

      return false;
    }
    /** Retrieve the last *Error* generated during a Variable Evaluation.
     * 
     * @return string|false If an error occurred during the last Variable Evaluation, returns the *Error Message*. Returns **false** if no error occurred.
     */
    public function get_last_error () {
      $lastError = $this->get_last_result('errors');

      if (isset($lastError)) {
        return $lastError[0]['message'];
      }

      return false;
    }
  };
?>