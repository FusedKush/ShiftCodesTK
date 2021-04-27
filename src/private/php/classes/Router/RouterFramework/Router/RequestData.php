<?php
  namespace ShiftCodesTK\Router\RouterFramework\Router;
  
  use ShiftCodesTK\Router\RequestProperties,
      ShiftCodesTK\Router\RoutedRequest;

  /** Represents the *Response & Request Data* for the Request */
  trait RequestData {
    /** @var RequestProperties The *Request Properties & Headers* of the Request. */
    protected $requestProperties = null;
    /** @var RoutedRequest The `RoutedRequest` object representing the *Fulfilled Request*. */
    protected $routedRequest = null;
  
    /** Get the `RequestProperties` object of the *Request*
     *
     * @return RequestProperties Returns a `RequestProperties` object representing the *Request*.
     */
    public function &getRequestProperties (): RequestProperties {
      return $this->requestProperties;
    }
    /** Get the `RoutedRequest` object of the *Router*
     *
     * @return RoutedRequest Returns the `RoutedRequest` object responsible for the `Router`.
     */
    public function &getRoutedRequest (): RoutedRequest {
      return $this->routedRequest;
    }
  }