<?php
  namespace ShiftCodesTK\Forms\Form;

  /** Represents the *Result* of a *Form Submission*. */
  abstract class Result extends Result\Response {

    /** Initialize the `Result` subclass. */
    public function __construct() {
      parent::__construct();
    }
  }
?>