<?php
  namespace ShiftCodesTK\Router\RouterFramework\RequestProperties;
  
  /** Represents the available *HTTP Request Methods*. */
  interface RequestInfoConstants {
    /** @var string Requests a representation of the specified resource. Requests using `GET` should only retrieve data.
     * @link https://developer.mozilla.org/docs/Web/HTTP/Methods/GET
     */
    public const REQUEST_METHOD_GET = 'GET';
    /** @var string Asks for a response identical to that of a `GET` request, but without the response body.
     * @link https://developer.mozilla.org/docs/Web/HTTP/Methods/HEAD
     */
    public const REQUEST_METHOD_HEAD = 'HEAD';
    /** @var string Used to submit an entity to the specified resource, often causing a change in state or side effects on the server.
     * @link https://developer.mozilla.org/docs/Web/HTTP/Methods/POST
     */
    public const REQUEST_METHOD_POST = 'POST';
    /** @var string Replaces all current representations of the target resource with the request payload.
     * @link https://developer.mozilla.org/docs/Web/HTTP/Methods/PUT
     */
    public const REQUEST_METHOD_PUT = 'PUT';
    /** @var string Deletes the specified resource.
     * @link https://developer.mozilla.org/docs/Web/HTTP/Methods/DELETE
     */
    public const REQUEST_METHOD_DELETE = 'DELETE';
    /** @var string Establishes a tunnel to the server identified by the target resource.
     * @link https://developer.mozilla.org/docs/Web/HTTP/Methods/CONNECT
     */
    public const REQUEST_METHOD_CONNECT = 'CONNECT';
    /** @var string Used to describe the communication options for the target resource.
     * @link https://developer.mozilla.org/docs/Web/HTTP/Methods/OPTIONS
     */
    public const REQUEST_METHOD_OPTIONS = 'OPTIONS';
    /** @var string Performs a message loop-back test along the path to the target resource.
     * @link https://developer.mozilla.org/docs/Web/HTTP/Methods/TRACE
     */
    public const REQUEST_METHOD_TRACE = 'TRACE';
    /** @var string Used to apply partial modifications to a resource.
     * @link https://developer.mozilla.org/docs/Web/HTTP/Methods/PATCH
     */
    public const REQUEST_METHOD_PATCH = 'PATCH';
    /** @var array A list of valid *HTTP Request Methods*. */
    public const REQUEST_METHOD_LIST = [
      self::REQUEST_METHOD_GET,
      self::REQUEST_METHOD_HEAD,
      self::REQUEST_METHOD_POST,
      self::REQUEST_METHOD_PUT,
      self::REQUEST_METHOD_DELETE,
      self::REQUEST_METHOD_CONNECT,
      self::REQUEST_METHOD_OPTIONS,
      self::REQUEST_METHOD_TRACE,
      self::REQUEST_METHOD_PATCH
    ];
    
    /** @var int Represents a standard *PHP Script*. */
    public const RESOURCE_TYPE_SCRIPT = 0;
    /** @var int Represents a Front-end *PHP-HTML Page*. */
    public const RESOURCE_TYPE_PAGE = 1;
    /** @var int Represents a *Front-end Request* for a *Backend Script*. */
    public const RESOURCE_TYPE_REQUEST = 2;
    /** @var array A list of valid *Requested Resource Types*. */
    public const RESOURCE_TYPE_LIST = [
      self::RESOURCE_TYPE_SCRIPT,
      self::RESOURCE_TYPE_PAGE,
      self::RESOURCE_TYPE_REQUEST
    ];
    
    /** @var string[] A list of *Identifiers* for the *Request Token*.
     *
     * Identifiers include:
     * - `HEADER`
     * - `GET`
     * - `POST`
     */
    public const REQUEST_TOKEN_IDENTIFIERS = [
      'HEADER' => 'X-Request-Token',
      'GET'    => '_request_token',
      'POST'   => '_auth_token'
    ];
  }