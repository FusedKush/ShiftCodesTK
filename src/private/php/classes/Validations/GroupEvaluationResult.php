<?php
  namespace ShiftCodesTK\Validations;

  /** Represents the result of a `GroupEvaluator` *Evaluation*. */
  class GroupEvaluationResult extends BasicEvaluationResult {
    /** @var array The evaluated variables. 
     * - If `$result` is **true**, the values may be of a different type after being *casted* to the designated type.
     * - If `$result` is **false**, the values may be the `$default_value` of the `VariableEvaluation`, or **null**.
     */
    public $variables = [];
  }
?>