<?php
  namespace ShiftCodesTK\Router\RouterFramework\RoutedRequest;
  
  use ShiftCodesTK\Router\RouterFramework,
      ShiftCodesTK\Router;
  
  /** Represents the *HTTP Status* of the Routed Request. */
  trait ResponseStatus {
    use RouterFramework\ResponseStatus;
  
    /** @var string|null $status The optional *Status Message*. */
    protected $statusMessage = null;
    
    /** @see RouterFramework\ResponseStatus::getResponseStatus() */
    public function getResponseStatus (): array {
      $status_info = [
        'statusCode'    => null,
        'statusText'    => null,
        'statusMessage' => null
      ];
  
      foreach ($status_info as $property => &$value) {
        $value = $this->$property;
      }
  
      return $status_info;
    }
    /** @see RouterFramework\ResponseStatus::setResponseStatus() */
    public function setResponseStatus (
      int $status_code = null,
      string $status_message = null,
      bool $throw_errors = true
    ): bool {
      if (!array_key_exists($status_code, RouterFramework::STATUS_CODES)) {
        if ($throw_errors) {
          throw new \OutOfRangeException("\"{$status_code}\" is not a valid Status Code.");
        }
      }

      $code_info = RouterFramework::STATUS_CODES[$status_code];

      $this->statusCode = $status_code;
      $this->statusText = $code_info['statusText'];
      $this->statusMessage = $status_message ?? $code_info['statusMessage'];
      
      return true;
    }
  }