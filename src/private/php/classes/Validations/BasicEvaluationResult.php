<?php
  namespace ShiftCodesTK\Validations;

  /** Represents the basic result of a `VariableEvaluator` or `GroupEvaluator` *Evaluation*. */
  abstract class BasicEvaluationResult {
    /** @var bool Indicates the result of the evaluation. **True** represents total success, while **false** represents one or more points of failure. */
    public $result = true;
    /** @var array A list of warnings generated during the evaluation. */
    public $warnings = [];
    /** @var array A list of errors generated during the evaluation. */
    public $errors = [];
  }
?>