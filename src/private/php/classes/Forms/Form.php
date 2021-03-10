<?php
  namespace ShiftCodesTK\Forms;

  /** The `FormCore` is responsible for the Properties & Methods used by Forms and their children. */
  class Form extends Form\FormManager {
    public function __construct (string $form_name = null) {
      parent::__construct();

      if (isset($form_name)) {
        $this->set_name($form_name);
      }
    }

    public function get_element_markup() {
    }
  }
?>