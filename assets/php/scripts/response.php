<?php
  class Response {
    public $statusCode = 0;

    public function set($statusCode) {
      $this->statusCode = $statusCode;
    }
    public function addPayload($content) {
      $this->payload[] = $content;
    }
    public function addWarning($content) {
      $this->warnings[] = $content;
    }
    public function addError($content) {
      $this->errors[] = $content;
    }
    public function send() {
      echo json_encode($this);
    }
  }
?>
