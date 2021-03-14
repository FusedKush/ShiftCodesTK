<?php
  /**
   * Update and send a formatted response object
   */
  class ResponseObject {
    /**
     * The Status Code to return
     */
    public $status_code;
    /**
     * The Status Message to return
     */
    public $status_message;
    /**
     * The response payload
     */
    public $payload = [];
    /**
     * Updates an array in the response
     *
     * @param string $type The array to be updated
     * @param any $value The value of the object
     * @param string $key The key of the object
     */
    private function updateArray($type, $value, $key) {
      $arr = &$this->$type;

      if (!isset($arr)) {
        $arr = [];
      }

      if ($key) { $arr[$key] = $value; }
      else      { $arr[count($arr)] = $value; }
    }
    /**
     * The `ResponseObject` Constructor
     * 
     * @param false|int $code The *Status Code* to set the Response Object to.
     * - Must be a valid integer from `\ShiftCodesTK\STATUS_CODES`
     * - Defaults to **1**.
     * @return void 
     */
    public function __construct ($code = false) {
      if ($code !== false) {
        $this->set($code);
      }
      else {
        $this->set(1);
      }
    }
    /**
     * Set the Status Code of the response
     * 
     * @param int $code The new Status Code
     * @return void
     */
    public function set($code) {
      $status_code = \ShiftCodesTK\STATUS_CODES[$code] ?? false;
      $httpCode = $status_code && isset($status_code['httpCode']) ? $status_code['httpCode'] : $code;

      if (!$status_code) {
        error_log("\"$code\" is not a valid Status Code.");
        return;
      }

      $this->status_code = $httpCode;
      $this->status_message = $status_code['name'];
    }
    /**
     * Add or update a payload
     *
     * @param any $value The payload value
     * @param string $key The payload key
     * @return void
     */
    public function setPayload($value, $key = null) {
      $this->updateArray('payload', $value, $key);
    }
    /**
     * Add or update a warning
     *
     * @param any $value The warning value
     * @param string $key The warning key
     */
    public function setWarning($value, $key = null) {
      $this->updateArray('warnings', $value, $key);
    }
    /**
     * Add or update an error
     *
     * @param any $value The error value
     * @param string $key The error key
     */
    public function setError($value, $key = null) {
      $this->updateArray('errors', $value, $key);
    }
    /**
     * Adds an error and sends the response
     *
     * @param [type] $code
     * @param [type] $value
     */
    public function fatalError($code, $value) {
      $this->set($code);
      $this->setError($value);
      $this->send();
    }
    /**
     * Sends the response
     */
    public function send() {
      response_type('application/json');
      response_http($this->status_code);
      echo json_encode($this);
    }
    /**
     * Retrieve the response payloads from the `ResponseObject`
     * 
     * @return array Returns the response payloads from the `ResponseObject`. 
     */
    public function getPayloads() {
      $payloads = (array) clone $this;

      unset($payloads['status_code']);
      unset($payloads['status_message']);

      return $payloads;
    }
  }
?>