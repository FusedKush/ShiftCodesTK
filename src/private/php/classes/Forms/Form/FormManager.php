<?php
  namespace ShiftCodesTK\Forms\Form;
  use ShiftCodesTK\Forms,
      ShiftCodesTK\Validations,
      ShiftCodesTK\Strings;

  /** The `FormManager` is responsible for the surface properties and methods of the `Form`. */
  abstract class FormManager extends Result {
    /** Initialize the `FormChildManager` */
    public function __construct () {
      parent::__construct();
    }
  }
?>