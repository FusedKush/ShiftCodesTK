<?php
  namespace ShiftCodesTK\Router\RouterFramework\Router;
  
  use ShiftCodesTK\Router\RouterFramework,
      ShiftCodesTK\Router\RequestProperties;

  /** The `RouterController` is responsible for modifying the *Router* */
  trait RouterController {
    use RequestData,
        RouteController\Headers,
        RouterFramework\ResponseStatus,
        RouteController\ContentType,
        RouteController\Location;
  
    /** Route the *Status Code* to the Response
     *
     * @param bool $allow_exit Indicates if the script is permitted to **Exit** during routing. Defaults to **true**.
     * @return bool|exit Returns **true** on success and **false** on failure.
     * If the *Response Status Code* is greater than **399** and less than **600**
     */
    protected function routeResponseStatus (bool $allow_exit = true): bool {
      $protocol = $this->getRequestProperties()
        ->getRequestInfo('requestProtocol');
    
      $result = self::sendHeader($protocol, "{$this->statusCode} {$this->statusText}");

      if (!$result) {
        return false;
      }
      else if ($allow_exit) {
        $exit_script = $allow_exit 
          && $this->statusCode > 399
          && $this->statusCode < 600
          && !$this->location
          && $this->requestProperties
            ->getRequestInfo('resourceType') !== RequestProperties::RESOURCE_TYPE_REQUEST;

        if ($exit_script) {
          errorNotice($this->statusCode);
          exit();
        }
      }

      return true;
    }
  
    /** @see RouterFramework\ResponseStatus::getResponseStatus() */
    public function getResponseStatus (): array {
      return $this->routedRequest
                  ->getResponseStatus();
    }
    /** @see RouterFramework\ResponseStatus::setResponseStatus() */
    public function setResponseStatus (
      int $status_code = null,
      string $status_message = null,
      bool $throw_errors = true
    ): bool {
      $status_code_list = RouterFramework::STATUS_CODES;

      if (!array_key_exists($status_code, $status_code_list)) {
        if ($throw_errors) {
          throw new \OutOfRangeException("\"{$status_code}\" is not a valid Status Code.");
        }
      }

      $http_status_code = $status_code_list[$status_code]['httpStatusCode'] ?? $status_code;
      $code_info = $status_code_list[$http_status_code];

      $this->statusCode = $http_status_code;
      $this->statusText = $code_info['statusText'];
      $this->getRoutedRequest()
           ->setResponseStatus(...func_get_args());
      
      return true;
    }
  }