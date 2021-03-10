<?php
  namespace ShiftCodesTK\Forms;

  /** The `FormCore` is responsible for the Properties & Methods used by Forms and their children. */
  abstract class FormCore extends FormCore\FormCoreManager {
    public function __construct () {
      parent::__construct();
      $this->check_content_bitmasks();
    }
  }
?>