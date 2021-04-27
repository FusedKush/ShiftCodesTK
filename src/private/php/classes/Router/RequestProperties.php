<?php
  namespace ShiftCodesTK\Router;
  
  /** Represents the *Request Properties* of a *Routed Request*. */
  class RequestProperties
    implements RouterFramework\RequestProperties\RequestInfoConstants
  {
    use RouterFramework\RequestProperties\RequestInfo,
        RouterFramework\RequestProperties\RequestData,
        RouterFramework\RequestProperties\CustomRequestProperties;
    
    /** Initialize the `RequestProperties` */
    public function __construct () {
      $this->retrieveRequestInfo();
      $this->retrieveRequestData();
      
      return $this;
    }
  }