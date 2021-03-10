<?php
  namespace ShiftCodesTK\Forms\Form\Result;

  /** The `ResponseObject` representing the Form Submission Result. */
  abstract class Response extends Parameters {
    /** Initialize the `Response` subclass */
    public function __construct() {
      parent::__construct();

      $this->form_result['response'] = new \ResponseObject();
    }

    /** Retrieve the `ResponseObject` representing the Form Submission Result.
     * 
     * @return \ResponseObject Returns the `ResponseObject` representing the form.
     */
    public function &get_response_object () {
      return $this->form_result['response'];
    }
  }
?>