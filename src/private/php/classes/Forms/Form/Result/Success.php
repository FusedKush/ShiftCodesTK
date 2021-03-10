<?php
  namespace ShiftCodesTK\Forms\Form\Result;

  /** Represents the *Success* or *Failure* of the Form Submission. */
  abstract class Success extends FormResult {
    /** Record a *Warning* or *Error* generated while processing the Form Submission.
     * 
     * @param string $issue_type The *Issue Type* of the `$issue`: **warning** or **error**.
     * @param array $issue The *Issue* being recorded.
     * @return $this Returns the object for further chaining.
     */
    private function record_issue (string $issue_type, array $issue) {
      $successful = &$this->form_result['successful'];

      if ($issue_type == 'error' && $successful) {
        $successful = false;
        trigger_error("Form Submission cannot be marked as Successful if any errors have occurred.", E_USER_NOTICE);
      }

      $property = "{$issue_type}s";

      $this->form_result[$property] = $issue;

      return $this;
    }

    /** Initialize the `Success` subclass. */
    public function __construct () {
      parent::__construct();

      $this->form_result['successful'] = false;
      $this->form_result['warnings'] = [];
      $this->form_result['errors'] = [];
    }

    /** Indicate that the Form Submission was *Successful*.
     * 
     * @return $this Returns the object for further chaining.
     */
    public function success () {
      $successful = &$this->form_result['successful'];

      if ($successful === false) {
        if (empty($this->form_result['errors'])) {
          $successful = true;
        }
        else {
          trigger_error("Form Submission cannot be marked as Successful if any errors have occurred.", E_USER_NOTICE);
        }
      }

      return $this;
    }
    /** Record a *Warning* generated while processing the Form Submission.
     * 
     * @param array $warning The *Warning* being recorded.
     * @return $this Returns the object for further chaining.
     */
    public function record_warning (array $warning) {
      return $this->record_issue('warning', $warning);
    }
    /** Record an *Error* thrown while processing the Form Submission.
     * 
     * @param array $error The *Error* being recorded.
     * @return $this Returns the object for further chaining.
     */
    public function record_error (array $error) {
      return $this->record_issue('error', $error);
    }
  }
?>