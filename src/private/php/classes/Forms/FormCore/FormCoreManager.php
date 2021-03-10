<?php
  namespace ShiftCodesTK\Forms\FormCore;
  use ShiftCodesTK\Forms,
      ShiftCodesTK\Strings;

  /** The `FormCoreManager` is responsible for the surface properties and methods of the `FormCore`. */
  abstract class FormCoreManager extends Content implements Forms\FormElementInterface {
    public function __construct () {
      parent::__construct();
    }
  }
?>