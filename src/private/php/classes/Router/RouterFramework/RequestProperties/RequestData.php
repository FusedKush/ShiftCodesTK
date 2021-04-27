<?php
  namespace ShiftCodesTK\Router\RouterFramework\RequestProperties;
  
  use ShiftCodesTK\Strings;
  
  /** Represents the *Request Data* sent with the Request. */
  trait RequestData {
    /**
     * @var array[] Represents the *Request Data* sent with the request.
     *
     * Contains the following *Request Data `Array`s*:
     * - `GET`
     * - `POST`
     * - `COOKIE`
     */
    protected $requestData = [
      'GET'     => [],
      'POST'    => [],
      'COOKIE'  => []
    ];
    
    /** Retrieve the *Request Data* from the Request
     *
     * @return bool Returns **true** on success.
     */
    protected function retrieveRequestData (): bool {
      $this->requestData = array_replace($this->requestData, [
        'GET'    => $_GET,
        'POST'   => $_POST,
        'COOKIE' => $_COOKIE,
      ]);
      
      return true;
    }
    
    /** Get the *Request Data* of the request
     *
     * @param string|null $property If provided, this is the *Request Data Property* to be returned instead of the full `array`.
     * @return array If `$property` is omitted, an `array` representing the *Request Data* will be returned.
     * If a `$property` is provided, its value is returned.
     * @throws \UnexpectedValueException if `$property` is not a valid *Request Data Property*.
     */
    public function getRequestData (string $property = null): array {
      $data = $this->requestData;
      
      if (isset($property)) {
        $capitalized_property = Strings\transform($property, Strings\TRANSFORM_UPPERCASE);
        
        if (!array_key_exists($capitalized_property, $data)) {
          throw new \UnexpectedValueException("\"{$property}\" is not a valid Request Data Property.");
        }
        
        return $data[$capitalized_property];
      }
      
      return $data;
    }
  }