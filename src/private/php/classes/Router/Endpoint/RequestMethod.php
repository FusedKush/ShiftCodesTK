<?php
  namespace ShiftCodesTK\Router\Endpoint;
  
  use ShiftCodesTK\Router\Endpoint,
      ShiftCodesTK\Router\RequestProperties,
      ShiftCodesTK\Validations;

  /** The *Request Method* of the Endpoint determines the different ways it can be invoked. */
  trait RequestMethod {
    use EndpointSignature;
  
    /** Set the *Supported Request Methods* for the Endpoint
     *
     * @param array $request_methods A list of *HTTP Request Methods* supported by the Endpoint.
     * - {@see RequestProperties::REQUEST_METHOD_LIST}
     * @return \ShiftCodesTK\Router\Endpoint Returns the `Endpoint` object on success.
     * @throws \UnexpectedValueException if one or more of the `$request_methods` are invalid.
     */
    public function setRequestMethods (array $request_methods): Endpoint {
      if (!Validations\check_match($request_methods, RequestProperties::REQUEST_METHOD_LIST)) {
        throw new \UnexpectedValueException('One or more of the provided HTTP Request Methods are invalid.');
      }
      $this->endpointSignature['requestMethods'] = $request_methods;
      return $this;
    }
    /** Validate the *Request Method* used to perform the request
     *
     * @param bool $handle_invalid_requests Indicates if invalid requests should automatically be handled when encountered.
     * @return bool|exit Returns **true** if the *Request Method* used to perform the request is supported by the Endpoint.
     * Otherwise, returns **false**.
     * **Exits** the script if an invalid Request Method is used and `$handle_invalid_requests` is **true**.
     */
    public function checkRequestMethod (bool $handle_invalid_requests = false): bool {
      $request_method = $this->requestProperties
        ->getRequestInfo('requestMethod');
      $supported_methods = $this->endpointSignature['requestMethods'];
      if (!Validations\check_match($request_method, $supported_methods)) {
        if ($handle_invalid_requests) {
          $this->setResponseStatus(405, 'The requested endpoint does not support the request method used.');
          $this->setHeader('Allow', implode(', ', $supported_methods));
          $this->route();
        }
        return false;
      }
      return true;
    }
  }