<?php
  /**
   * Update and send a formatted response object
   */
  class ResponseObject {
    /**
     * The Status Code to return
     */
    public $statusCode;
    /**
     * The Status Message to return
     */
    public $statusMessage;
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
     * Set the Status Code of the response
     * 
     * @param int $code The new Status Code
     * @return void
     */
    public function set($code) {
      $statusCode = STATUS_CODES[$code] ?? false;
      $httpCode = $statusCode && isset($statusCode['httpCode']) ? $statusCode['httpCode'] : $code;

      if (!$statusCode) {
        error_log("\"$code\" is not a valid Status Code.");
        return;
      }

      response_http($httpCode);
      $this->statusCode = $httpCode;
      $this->statusMessage = $statusCode['name'];
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
      if ($this->statusCode === null) {
        $this->set(1);
      }

      response_type('application/json');
      echo json_encode($this);
    }
  }
  /**
   * Updates and sends a formatted response object
   */
  // $response = new ResponseObject;
?>