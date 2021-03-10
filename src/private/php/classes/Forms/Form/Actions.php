<?php
  namespace ShiftCodesTK\Forms\Form;

  /** Represents the behavior of the form on a successful *Form Submission*. */
  abstract class Actions extends Actions\FormState {
    /** Initialize the `Actions` subclass. */
    public function __construct() {
      parent::__construct();
    }
  }
?>