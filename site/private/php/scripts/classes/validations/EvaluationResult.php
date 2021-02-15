<?php
  namespace ShiftCodesTK\Validations;

  /** Represents the result of a `VariableEvaluator` *Evaluation*. */
  class EvaluationResult {
    /** @var mixed The evaluated variable. 
     * - If `$result` is **true**, this value may be of a different type after being *casted* to the designated type.
     * - If `$result` is **false**, this may be the `$default_value` of the `VariableEvaluation`, or **null**.
     */
    public $variable = null;
    /** @var null|string If provided, the name or key of the `$variable`. */
    public $key = null;
    /** @var bool Indicates the result of the evaluation. **True** represents success, while **false** represents failure. */
    public $result = true;
    /** @var array A list of warnings generated during the evaluation. */
    public $warnings = [];
    /** @var array A list of errors generated during the evaluation. */
    public $errors = [];
  }
?>