<?php
  class Response {
    public $statusCode = 0;
    public $payload = [];
    public $warnings = [];
    public $errors = [];

    private function addArray($array, $content, $name) {
      $p = &$this->$array;

      if ($name !== null) {
        $p[$name] = $content;
        return $p[$name];
      }
      else {
        $len = count($p);

        $p[$len] = $content;
        return $p[$len];
      }
    }
    public function set($statusCode) {
      return $this->statusCode = $statusCode;
    }
    public function addPayload($content, $name = null) {
      return $this->addArray('payload', $content, $name);
    }
    public function addWarning($content, $name = null) {
      return $this->addArray('warnings', $content, $name);
    }
    public function addError($content, $name = null) {
      return $this->addArray('errors', $content, $name);
    }
    public function fatalError($statusCode, $content) {
      $this->set($statusCode);
      $this->addError($content);
      $this->send();
    }
    public function push() {
      echo json_encode($this);
    }
    public function send() {
      $this->push();
      exit();
    }
  }
?>
