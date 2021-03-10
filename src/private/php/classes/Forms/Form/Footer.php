<?php
  namespace ShiftCodesTK\Forms\Form;
  use ShiftCodesTK\Validations,
      ShiftCodesTK\Integers,
      ShiftCodesTK\Strings,
      ShiftCodesTK\Forms;

  /** Represents the *Form Footer*, housing the details and actions regarding the Form. */
  abstract class Footer extends Footer\FooterActions {
    /** Initialize the `Footer` subclass */
    public function __construct () {
      parent::__construct();
    }
  }
?>