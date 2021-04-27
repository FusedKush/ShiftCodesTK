<?php
  namespace ShiftCodesTK\Router\RouterFramework\RoutedRequest;
  
  use ShiftCodesTK\Validations;
  
  /** Represents the *Response Payloads* of the Routed Request. */
  trait ResponsePayloads {
    /** @var array The primary *Response Payload*. */
    protected $payload = [];
    /** @var array A list of *Warnings* generated during the life of the request. */
    protected $warnings = [];
    /** @var array A list of *Errors* generated during the life of the request. */
    protected $errors = [];
    
    /** Add a value to one of the *Response Payloads*
     *
     * @param string $payload_name The *Name* of the Response Payload.
     * Options include `payload`, `warnings`, and `errors`.
     * @param mixed $data The *Data* being added to the Response Payload.
     * @param string|null $name The *Name* associated with the `$data`. If omitted, a numeric value will be assigned.
     * @param bool $replace_existing Indicates if the `$data` can overwrite any existing data currently recorded under `$name`.
     * Defaults to **false**.
     * - Has no effect if `$name` is omitted.
     * @return bool Returns **true** on success.
     * Returns **false** if `$name` is provided, already in use, and `$replace_existing` is **false**.
     */
    protected function addResponseData (
      string $payload_name,
      $data,
      string $name = null,
      bool $replace_existing = false
    ): bool {
      $payloads = [ 'payload', 'warnings', 'errors' ];
      
      if (!Validations\check_match($payload_name, $payloads)) {
        throw new \UnexpectedValueException("\"{$payload_name}\" is not a valid Payload Name.");
      }
      
      $payload = &$this->$payload_name;
      
      if (isset($name) && array_key_exists($name, $payload) && !$replace_existing) {
        return false;
      }
      
      if (isset($name)) {
        $payload[$name] = $data;
      }
      else {
        $payload[] = $data;
      }
      
      return true;
    }
    
    /** Add a value to the *Primary Response Payload*
     *
     * @param mixed $data The *Data* being added to the Response Payload.
     * @param string|null $name The *Name* associated with the `$data`. If omitted, a numeric value will be assigned.
     * @param bool $replace_existing Indicates if the `$data` can overwrite any existing data currently recorded under `$name`.
     * Defaults to **false**.
     * - Has no effect if `$name` is omitted.
     * @return bool Returns **true** on success.
     * Returns **false** if `$name` is provided, already in use, and `$replace_existing` is **false**.
     */
    public function addPayload (
      $data,
      string $name = null,
      bool $replace_existing = false
    ): bool {
      return $this->addResponseData('payload', ...func_get_args());
    }
    /** Add a value to the *Warning Payload*
     *
     * @param mixed $data The *Data* being added to the Warning Payload.
     * - See {@see \errorObject()} for a structured *Error Object*
     * @param string|null $name The *Name* associated with the `$data`. If omitted, a numeric value will be assigned.
     * @param bool $replace_existing Indicates if the `$data` can overwrite any existing data currently recorded under `$name`.
     * Defaults to **false**.
     * - Has no effect if `$name` is omitted.
     * @return bool Returns **true** on success.
     * Returns **false** if `$name` is provided, already in use, and `$replace_existing` is **false**.
     */
    public function addWarning (
      $data,
      string $name = null,
      bool $replace_existing = false
    ): bool {
      return $this->addResponseData('warnings', ...func_get_args());
    }
    /** Add a value to the *Error Payload*
     *
     * @param mixed $data The *Data* being added to the Error Payload.
     * - See {@see \errorObject()} for a structured *Error Object*
     * @param string|null $name The *Name* associated with the `$data`. If omitted, a numeric value will be assigned.
     * @param bool $replace_existing Indicates if the `$data` can overwrite any existing data currently recorded under `$name`.
     * Defaults to **false**.
     * - Has no effect if `$name` is omitted.
     * @return bool Returns **true** on success.
     * Returns **false** if `$name` is provided, already in use, and `$replace_existing` is **false**.
     */
    public function addError (
      $data,
      string $name = null,
      bool $replace_existing = false
    ): bool {
      return $this->addResponseData('errors', ...func_get_args());
    }
  }