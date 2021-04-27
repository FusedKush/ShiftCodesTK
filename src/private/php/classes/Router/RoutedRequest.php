<?php
  namespace ShiftCodesTK\Router;
  
  use ShiftCodesTK\Auth,
      ShiftCodesTK\Timestamps\TimestampInt;
  use ShiftCodesTK\Router\RouterFramework\ResponseStatus;

  /** Represents a *Request* handled by the `Router`. */
  class RoutedRequest {
    use ResponseStatus,
        RouterFramework\RoutedRequest\ResponseStatus,
        RouterFramework\RoutedRequest\ResponsePayloads;
    
    /** @var string The *Request ID* of the Routed Request. */
    protected $requestID = null;
    /** @var string A *Timestamp* representing the *beginning of the request*. */
    protected $requestTime = null;
    
    /** Get the *Response Data* of the Routed Request
     *
     * @param string|null $property If provided, only this *Response Data Property* will be returned.
     * @return string|string[] If `$property` is provided, its value is returned.
     * Otherwise, Returns an `array` representing the *Request Info* of the *Routed Request*, including the following properties:
     * - `statusCode`
     * - `statusText`
     * - `statusMessage`
     * - `requestID`
     * - `requestTime`
     * - `payload`
     * - `warnings`
     * - `errors`
     * @throws \UnexpectedValueException if `$property` is not a valid *Response Data Property*.
     */
    public function getResponseData (string $property = null): array {
      $response_data = [
        'statusCode'    => null,
        'statusText'    => null,
        'statusMessage' => null,
        'requestID'     => null,
        'requestTime'   => null,
        'payload'       => null,
        'warnings'      => null,
        'errors'        => null
      ];
      
      foreach ($response_data as $info_property => &$info_value) {
        $info_value = $this->$info_property;
      }
      
      if (isset($property)) {
        if (!array_key_exists($property, $response_data)) {
          throw new \UnexpectedValueException("\"{$property}\" is not a valid Response Data Property.");
        }
        
        return $response_data[$property];
      }
      
      return $response_data;
    }
    /** Get the *Response Data* as a `JSON` string
     *
     * @return string Returns the `JSON` representation of the *Response Data*.
     * @see getResponseData()
     * @see \json_encode()
     */
    public function getResponseJSON (): string {
      $response_data = $this->getResponseData();
      
      return json_encode($response_data);
    }
    
    /** Sync information from the current *Route* to the `RoutedRequest`
     *
     * The following properties are *Synced* between the `Router` and `RoutedRequest`:
     * - `requestTime`
     * - `statusCode`
     * - `statusText`
     * - `statusMessage` (only if unset)
     *
     * @param \ShiftCodesTK\Router $router The *Router* to be synced.
     * @return bool Returns **true** on success.
     */
    public function syncRouterData (\ShiftCodesTK\Router $router): bool {
      $response_status = $router->getResponseStatus();

      $this->requestTime = (
        (new TimestampInt(
          $router->getRequestTime()
        ))
          ->get_as(TimestampInt::TS_TYPE_DATETIME)
          ->format(\ShiftCodesTK\DATE_FORMATS['iso'])
      );
      $this->setResponseStatus($response_status['statusCode']);
      
      if (!isset($this->statusMessage)) {
        $this->setResponseStatus($this->statusCode, RouterFramework::STATUS_CODES[$response_status['statusCode']]['statusMessage']);
      }
      
      return true;
    }
  
    /** Initialize a new `RoutedRequest`
     *
     * @param \ShiftCodesTK\Router|null $router If available, the `Router` object to be synced to the `RoutedRequest`
     * @return RoutedRequest Returns the new `RoutedRequest` on success
     */
    public function __construct (\ShiftCodesTK\Router $router = null) {
      $this->requestID = \ShiftCodesTK\Strings\chunk(
        Auth\hash_string(
          (new Auth\UniqueID())
            ->unique_id
            ->get_int()
        ),
        16,
        '-'
      );
      
      if (isset($router)) {
        $this->syncRouterData($router);
      }
      
      return $this;
    }
  }