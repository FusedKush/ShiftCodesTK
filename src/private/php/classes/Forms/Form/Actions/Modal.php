<?php
  namespace ShiftCodesTK\Forms\Form\Actions;

  /** Represents a *Modal* that is displayed when the form is successfully submitted. */
  abstract class Modal extends Redirect {
    public function __construct() {
      parent::__construct();

      $this->form_actions['modal'] = [
        'enabled' => false
      ];
    }
  }
?>