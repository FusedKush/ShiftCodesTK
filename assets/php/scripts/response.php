<?php
  class Response {
    /** @var int */
    public $statusCode = 0;
    /** @var array */
    public $payload = [];
    /** @var array */
    public $warnings = [];
    /** @var array */
    public $errors = [];

    private function addArray(array $array, $content, string $name = null) {
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

    public function set(int $statusCode): int {
      return $this->statusCode = $statusCode;
    }
    public function addPayload($content, string $name = null) {
      return $this->addArray('payload', $content, $name);
    }
    public function addWarning($content, string $name = null) {
      return $this->addArray('warnings', $content, $name);
    }
    public function addError($content, string $name = null) {
      return $this->addArray('errors', $content, $name);
    }
    public function fatalError(int $statusCode, $content): void {
      $this->set($statusCode);
      $this->addError($content);
      $this->send();
    }
    
    public function push(): void {
      echo json_encode($this);
    }
    /** 
     * @return exit
     */
    public function send(): void {
      $this->push();
      exit();
    }
  }
?>
