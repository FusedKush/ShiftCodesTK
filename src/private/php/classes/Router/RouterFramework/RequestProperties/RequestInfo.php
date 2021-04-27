<?php
  namespace ShiftCodesTK\Router\RouterFramework\RequestProperties;
  
  use ShiftCodesTK\Router\RouterFramework,
      ShiftCodesTK\Router\RouterFramework\Router\RouteController\HeaderConstants,
      ShiftCodesTK\Strings;

  /** Represents all of the *Request Information*. */
  trait RequestInfo {
    /** @var int A *Unix Timestamp* representing the *Beginning of the Request*. */
    protected $requestTime = null;
    /** @var string The *HTTP Request Method* of the request.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTTP/Methods
     * @see RequestInfoConstants
     */
    protected $requestMethod = null;
    /** @var string The *HTTP Request Scheme* of the request. E.g. `HTTPS` */
    protected $requestScheme = null;
    /** @var string The *HTTP Request Protocol* of the request. E.g. `HTTPS/2.0` */
    protected $requestProtocol = null;
    /** @var array A list of *Request Headers* sent with the request.
     * - **Note**: These values should *not* be trusted, as they are provided by the client.
     */
    protected $requestHeaders = null;
    /**
     * @var array[] Represents the *Request Data* sent with the request.
     *
     * Contains the following *Request Data `Array`s*:
     * - `GET`
     * - `POST`
     * - `COOKIE`
     */
    /** @var string|false If sent, this is the *Request Token* used to conduct the request. */
    protected $requestToken = null;
    /** @var int A `RESOURCE_TYPE_*` class constant representing the *Resource Type* of the Requested Resource.
     *
     * @see RESOURCE_TYPE_SCRIPT
     * @see RESOURCE_TYPE_PAGE
     * @see RESOURCE_TYPE_REQUEST
     */
    protected $resourceType = null;
    /** @var string The *Uniform Resource Identifier* of the Requested Resource. */
    protected $resourceURI = null;
    /** @var string The *Name* of the Requested Resource. */
    protected $resourceName = null;
    /** @var string The *Filename* of the Requested Resource. */
    protected $resourceFilename = null;
    /** @var string The *Full Path* to the Requested Resource. */
    protected $resourcePath = null;
    /** @var string The *Full Filepath* of the Requested Resource. */
    protected $resourceFilepath = null;
    
    /** Retrieve the *Request Info* from the Request.
     *
     * @return bool Returns **true** on success.
     */
    protected function retrieveRequestInfo (): bool {
      $basic_properties = [
        'requestProtocol'       => 'SERVER_PROTOCOL',
        'requestMethod'         => 'REQUEST_METHOD',
        'resourceURI'           => 'REQUEST_URI',
        'resourcePath'          => 'SCRIPT_NAME',
        'resourceFilepath'      => 'SCRIPT_FILENAME',
        'requestTime'           => 'REQUEST_TIME_FLOAT'
      ];
      
      foreach ($basic_properties as $property => $server_property) {
        if (array_key_exists($server_property, $_SERVER)) {
          $this->$property = $_SERVER[$server_property];
        }
      }
      foreach ($_SERVER as $property => $value) {
        if (Strings\substr_pos($property, 'HTTP_') === 0) {
          $property_name = RouterFramework::getStandardizedHeaderName(
            Strings\slice($property, 5),
            HeaderConstants::HEADER_FORMAT_STORAGE
          );
          $this->requestHeaders[$property_name] = $value;
        }
      }
      
      $this->requestScheme = $_SERVER['REQUEST_SCHEME']
        ?? strtolower(
          preg_replace(
            $_SERVER['SERVER_PROTOCOL'],
            '%\/[\d\.]+$%',
            '',
            1
          )
        );
      $this->requestToken = (function () {
        $identifiers = [
          'HEADER' => $this->requestHeaders,
          'GET'    => $this->requestData['GET'],
          'POST'   => $this->requestData['POST']
        ];
        
        foreach ($identifiers as $type => $source) {
          $request_token = $source[RequestInfoConstants::REQUEST_TOKEN_IDENTIFIERS[$type]] ?? null;
          if (isset($request_token)) {
            return $request_token;
          }
        }
        return false;
      })();
      $this->resourceType = defined('ShiftCodesTK\Router\RESOURCE_TYPE')
        ? \ShiftCodesTK\Router\RESOURCE_TYPE
        : RequestInfoConstants::RESOURCE_TYPE_SCRIPT;
      $this->resourceName = Strings\preg_replace($this->resourceURI, '%^.+?\/(.+$)$%', '$1');
      $this->resourceFilename = Strings\preg_replace($this->resourcePath, '%^.+?\/(.+$)$%', '$1');
      
      return true;
    }
  
    /** Get the *Request Info* of the request
     *
     * @param string|null $property If provided, this is the *Request Info Property* to be returned instead of the full `array`.
     * @return mixed If `$property` is omitted, an `array` representing the *Request Info* will be returned.
     * If a `$property` is provided, its value is returned.
     * @throws \UnexpectedValueException if `$property` is not a valid *Request Info Property*.
     */
    public function getRequestInfo (string $property = null) {
      $info = [];
      
      foreach ($this as $property_name => $value) {
        if ($property_name === 'requestData') {
          continue;
        }
      
        $info[$property_name] = $value;
      }
      
      if (isset($property)) {
        if (!array_key_exists($property, $info)) {
          throw new \UnexpectedValueException("\"{$property}\" is not a valid Request Info Property.");
        }
        
        return $info[$property];
      }
      
      return $info;
    }
  }