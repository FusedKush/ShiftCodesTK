<?php
  namespace ShiftCodesTK\Forms\Form\Result;
  use ShiftCodesTK\Forms\Form;

  /** Represents the *Result* of a *Form Submission*. */
  abstract class FormResult extends Form\Actions {
    /** @var array Represents the *Result* of a *Form Submission*. */
    protected $form_result = [];

    /** Initialize a new `FormActions` object. */
    public function __construct() {
      parent::__construct();
    }
  }
?>