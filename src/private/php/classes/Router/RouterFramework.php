<?php
  namespace ShiftCodesTK\Router;
  
  use       ShiftCodesTK\Router,
            ShiftCodesTK\Strings;
  use const ShiftCodesTK\Paths\PHP_PATHS;

  /** The `RouterFramework` is responsible for the various components of the {@see \ShiftCodesTK\Router}. */
  class RouterFramework {
    use RouterFramework\Router\RequestData,
        RouterFramework\Router\RouterController,
        RouterFramework\Router\EndpointManager;
  
    /** @var string The domain the live site resides on. */
		public const SITE_DOMAIN = 'shiftcodestk.com';
    /** @var array A list of HTTP Status Codes that may be returned by the server.
     * 
     * The `Router` will refuse to send any Status Codes not included in this list.
     *
     * **HTTP Status Codes**
     * 
     * _Each Status Code contains the `statusText` & `statusMessage` properties._
     * 
     * | Status Code                  | Status Text                     |
     * | ---                          | ---                             |
     * | _Informational Status Codes_ |                                 |
     * | `100`                        | Continue                        |
     * | `101`                        | Switching Protocols             |
     * | _Successful Status Codes_    |                                 |
     * | `200`                        | Ok                              |
     * | `201`                        | Created                         |
     * | `202`                        | Accepted                        |
     * | `204`                        | No Content                      |
     * | `205`                        | Reset Content                   |
     * | `206`                        | Partial Content                 |
     * | _Redirect Status Codes_      |                                 |
     * | `300`                        | Multiple Choice                 |
     * | `301`                        | Moved Permanently               |
     * | `302`                        | Found                           |
     * | `303`                        | See Other                       |
     * | `304`                        | Not Modified                    |
     * | `307`                        | Temporary Redirect              |
     * | `308`                        | Permanent Redirect              |
     * | _Client Error Status Codes_  |                                 |
     * | `400`                        | Bad Request                     |
     * | `401`                        | Unauthorized                    |
     * | `403`                        | Forbidden                       |
     * | `404`                        | Not Found                       |
     * | `405`                        | Method Not Allowed              |
     * | `406`                        | Not Acceptable                  |
     * | `407`                        | Proxy Authentication Required   |
     * | `408`                        | Request Timeout                 |
     * | `409`                        | Conflict                        |
     * | `410`                        | Gone                            |
     * | `411`                        | Length Required                 |
     * | `412`                        | Precondition Failed             |
     * | `413`                        | Payload Too Large               |
     * | `414`                        | URI Too Long                    |
     * | `415`                        | Unsupported Media Type          |
     * | `416`                        | Range Not Satisfiable           |
     * | `417`                        | Expectation Failed              |
     * | `418`                        | I'm a Teapot                    |
     * | `419`                        | Misdirected Request             |
     * | `426`                        | Upgrade Required                |
     * | `428`                        | Precondition Required           |
     * | `429`                        | Too Many Requests               |
     * | `431`                        | Request Header Fields Too Large |
     * | _Server Error Status Codes_  |                                 |
     * | `500`                        | Internal Server Error           |
     * | `501`                        | Not Implemented                 |
     * | `502`                        | Bad Gateway                     |
     * | `503`                        | Service Unavailable             |
     * | `504`                        | Gateway Timeout                 |
     * | `505`                        | HTTP Version Not Supported      |
     * | `510`                        | Not Extended                    |
     * 
     * **Custom ShiftCodesTK Status Codes**
     * 
     * _Each Status Code contains the `statusText`, `statusMessage`, & `httpStatusCode` properties._
     * 
     * | Status Code                  | HTTP Status Code  | Status Text                     |
     * | ---                          | ---               | ---                             |
     * | _Successful Status Codes_    |                   |                                 |
     * | `1`                          | `200`             | Ok                              |
     * | `2`                          | `201`             | Resource Created                |
     * | `3`                          | `200`             | Resource Modified               |
     * | `4`                          | `200`             | Resource Deleted                |
     * | _Client Error Status Codes_  |                   |                                 |
     * | `-1`                         | `400`             | Validation Error                |
     * | `-2`                         | `400`             | Request Error                   |
     * | `-3`                         | `400`             | Invalid Request Token           |
     * | `-4`                         | `400`             | Javascript Disabled             |
     * | `-5`                         | `400`             | Cookies Disabled                |
     * | Server Error Status Codes_   |                   |                                 |
     * | `-100`                       | `500`             | Server Processing Error         |
     * | `-101`                       | `500`             | Server Error                    |
     * | `-102`                       | `503`             | Server Maintenance              |
     */
    public const STATUS_CODES = [
      // HTTP Status Codes
      // Informational Status Codes
      100 => [
        'statusText'      => 'Continue',
        'statusMessage'   => 'Everything so far is OK.'
      ],
      101 => [
        'statusText'      => 'Switching Protocols',
        'statusMessage'   => 'The server is switching to the protocol specified by the client.'
      ],
      // Successful Status Codes
      200 => [
        'statusText'      => 'Ok',
        'statusMessage'   => 'The request has succeeded.'
      ],
      201 => [
        'statusText'      => 'Created',
        'statusMessage'   => 'The new resource has been successfully created.'
      ],
      202 => [
        'statusText'      => 'Accepted',
        'statusMessage'   => 'The request has been accepted, but has not yet been acted upon.'
      ],
      204 => [
        'statusText'      => 'No Content',
        'statusMessage'   => 'The request has succeeded, but no content is being sent.'
      ],
      205 => [
        'statusText'      => 'Reset Content',
        'statusMessage'   => 'The server is requesting that the requesting document be reset.'
      ],
      206 => [
        'statusText'      => 'Partial Content',
        'statusMessage'   => 'The request has succeeded, and part of the response body is being sent.'
      ],
      // Redirection Status Codes
      300 => [
        'statusText'      => 'Multiple Choice',
        'statusMessage'   => 'The request has more than one possible response.'
      ],
      301 => [
        'statusText'      => 'Moved Permanently',
        'statusMessage'   => 'The requested resource has been permanently moved.'
      ],
      302 => [
        'statusText'      => 'Found',
        'statusMessage'   => 'The requested resource has been temporarily moved.'
      ],
      303 => [
        'statusText'      => 'See Other',
        'statusMessage'   => 'The requested resource is available at another location.'
      ],
      304 => [
        'statusText'      => 'Not Modified',
        'statusMessage'   => 'The response has not been modified.'
      ],
      307 => [
        'statusText'      => 'Temporary Redirect',
        'statusMessage'   => 'The requested resource has been temporarily moved and must be the same HTTP Method as the original request.'
      ],
      308 => [
        'statusText'      => 'Permanent Redirect',
        'statusMessage'   => 'The requested resource has been permanently moved and must be the same HTTP Method as the original request.'
      ],
      // Client Error Status Codes
      400 => [
        'statusText'      => "Bad Request",
        'statusMessage'   => "The request was of an invalid structure, format, or syntax."
      ],
      401 => [
        'statusText'      => "Unauthorized",
        'statusMessage'   => "The client must be authenticated to access the requested resource."
      ],
      403 => [
        'statusText'      => "Forbidden",
        'statusMessage'   => "The client does not have permission to access the requested resource."
      ],
      404 => [
        'statusText'      => "Not Found",
        'statusMessage'   => "The requested resource could not be found."
      ],
      405 => [
        'statusText'      => "Method Not Allowed",
        'statusMessage'   => "The request method of the request is not permitted for the requested resource."
      ],
      406 => [
        'statusText'      => "Not Acceptable",
        'statusMessage'   => "No content conforming to the user agent's criteria was found."
      ],
      407 => [
        'statusText'      => "Proxy Authentication Requred",
        'statusMessage'   => "The client must be authenticated via a proxy to access the requested resource."
      ],
      408 => [
        'statusText'      => "Request Timeout",
        'statusMessage'   => "The server has terminated the connection due to inactivity."
      ],
      409 => [
        'statusText'      => "Conflict",
        'statusMessage'   => "The request has a conflict with the current state of the server."
      ],
      410 => [
        'statusText'      => "Gone",
        'statusMessage'   => "The requested resource has been permanently deleted."
      ],
      411 => [
        'statusText'      => "Length Required",
        'statusMessage'   => "The request did not include the Content-Length header, which is required by the server for the requested resource."
      ],
      412 => [
        'statusText'      => "Precondition Failed",
        'statusMessage'   => "Access to the requested resource has been denied due to unfulfilled conditions required by the client."
      ],
      413 => [
        'statusText'      => "Payload Too Large",
        'statusMessage'   => "The request entity is larger than the limits defined by the server."
      ],
      414 => [
        'statusText'      => "URI Too Long",
        'statusMessage'   => "The requested URI exceeds the maximum length interpreted by the server."
      ],
      415 => [
        'statusText'      => "Unsupported Media Type",
        'statusMessage'   => "The media format of the requested data is not supported by the server."
      ],
      416 => [
        'statusText'      => "Range Not Satisfiable",
        'statusMessage'   => "The specified range of the request could not be fulfilled by the server."
      ],
      417 => [
        'statusText'      => "Expectation Failed",
        'statusMessage'   => "The expectation indicated in the request could not be met by the server."
      ],
      418 => [
        'statusText'      => "I\'m a Teapot",
        'statusMessage'   => "The server refused to attempt to brew coffee because it is, permanently, a teapot."
      ],
      419 => [
        'statusText'      => "Misdirected Request",
        'statusMessage'   => "The request was directed at a resource that is not able to produce a response."
      ],
      426 => [
        'statusText'      => "Upgrade Required",
        'statusMessage'   => "Access to the requested resource will be refused using the current protocol."
      ],
      428 => [
        'statusText'      => "Precondition Required",
        'statusMessage'   => "The server requires that requests to the requested resource be conditional."
      ],
      429 => [
        'statusText'      => "Too Many Requests",
        'statusMessage'   => "The server has received too many requests from the client in a short period of time."
      ],
      431 => [
        'statusText'      => "Request Header Fields Too Large",
        'statusMessage'   => "The size of the request header fields exceeds the maximum the server is willing to process."
      ],
      // Server Error Status Codes
      500 => [
        'statusText'      => "Internal Server Error",
        'statusMessage'   => "The server has encountered an unexpected error that has prevented it from fulfilling the request."
      ],
      501 => [
        'statusText'      => "Not Implemented",
        'statusMessage'   => "The request method is not supported by the server."
      ],
      502 => [
        'statusText'      => "Bad Gateway",
        'statusMessage'   => "The server, working as a gateway for the response, received an invalid response."
      ],
      503 => [
        'statusText'      => "Service Unavailable",
        'statusMessage'   => "The server is currently unable to handle the request."
      ],
      504 => [
        'statusText'      => "Gateway Timeout",
        'statusMessage'   => "The server, working as a gateway for the response, could not get a response in time and has timed out."
      ],
      505 => [
        'statusText'      => "HTTP Version Not Supported",
        'statusMessage'   => "The request uses an unsupported HTTP Version."
      ],
      510 => [
        'statusText'      => "Not Extended",
        'statusMessage'   => "The server refuses to process the request until further extensions are declared."
      ],
      // ShiftCodesTK Status Codes
      // Successful Status Codes
      1   => [
        'statusText'      => 'Ok',
        'statusMessage'   => 'The request has succeeded.',
        'httpStatusCode'  => 200
      ],
      2   => [
        'statusText'      => 'Resource Created',
        'statusMessage'   => 'The requested resource has been created successfully.',
        'httpStatusCode'  => 201
      ],
      3   => [
        'statusText'      => 'Resource Modified',
        'statusMessage'   => 'The requested resource has been modified successfully.',
        'httpStatusCode'  => 200
      ],
      4   => [
        'statusText'      => 'Resource Deleted',
        'statusMessage'   => 'The requested resource has been deleted successfully.',
        'httpStatusCode'  => 200
      ],
      // Client Error Status Codes
      -1  => [
        'statusText'      => 'Validation Error',
        'statusMessage'   => 'One or more of the request parameters for the requested resource are invalid.',
        'httpStatusCode'  => 400
      ],
      -2  => [
        'statusText'      => 'Request Error',
        'statusMessage'   => 'The request submitted for the requested resource is invalid.',
        'httpStatusCode'  => 400
      ],
      -3  => [
        'statusText'      => 'Invalid Request Token',
        'statusMessage'   => 'The request token provided with the request is missing or invalid.',
        'httpStatusCode'  => 401
      ],
      -4  => [
        'statusText'      => 'Javascript Disabled',
        'statusMessage'   => 'The server requires the use of Javascript but it is disabled on the client.',
        'httpStatusCode'  => 400
      ],
      -5  => [
        'statusText'      => 'Cookies Disabled',
        'statusMessage'   => 'The server requires the use of Cookies but they are blocked on the client.',
        'httpStatusCode'  => 400
      ],
      // Server Error Status Codes
      -100  => [
        'statusText'      => 'Server Processing Error',
        'statusMessage'   => 'The server encountered an error while processing the request.',
        'httpStatusCode'  => 500
      ],
      -101  => [
        'statusText'      => 'Server Error',
        'statusMessage'   => 'The server encountered an error and returned an invalid response.',
        'httpStatusCode'  => 500
      ],
      -102  => [
        'statusText'      => 'Server Maintenance',
        'statusMessage'   => 'The server is currently undergoing maintenance.',
        'httpStatusCode'  => 503
      ],
    ];
  
    /** @var bool Indicates if the *Router Framework* has been *Initialized* yet. */
    private static $__initialized = false;

    /** @var string A *Timestamp* representing when the request was initially received. */
    protected static $requestTime = null;
  
    /** Initialize the `RouterFramework`
     * 
     * This method can and should be invoked as soon as possible during startup.
     *
     * @return true Returns **true** on completion.
     * @throws \Error if the *RouterFramework* has already been initialized.
     */
    private static function init (): bool {
      if (self::$__initialized) {
        throw new \Error('The RouterFramework has already been initialized.');
      }
      
      self::$requestTime = \ShiftCodesTK\Timestamps\time();
            
      self::$__initialized = true;
      return true;
    }
    
    /** Create a new *Router*
     *
     * @return Router Returns the new *Router*.
     */
    public static function newRouter (): Router {
      $router = new Router(
        new RequestProperties(),
        new RoutedRequest()
      );
      $router->getRoutedRequest()
        ->syncRouterData($router);
      
      return $router;
    }

    /** Handle *Nonexistent* or *Inaccessible* Static Method Calls
     *
     * Allows {@see RouterFramework::init()} to be invoked during startup.
     */
    public static function __callStatic (string $method_name, array $method_args) {
      if ($method_name === 'init' && self::class === 'ShiftCodesTK\Router\RouterFramework') {
        return self::init();
      }
    }
    
    /** *Route* the response and complete the request
     * 
     * Any *Class Methods* that begin with `route` will be invoked during routing. 
     * If the method returns **false**, routing will be *halted*.
     *
     * @param bool $allow_exit Indicates if the script is permitted to **Exit** during routing. Defaults to **true**.
     * @return bool|exit Returns **true** on success and **false** on failure. 
     * The script may also `exit` during routing if `$allow_exit` is **true**.
     */
    public function route (bool $allow_exit = true): bool {
      self::checkHeaders(true);

      /** The routing methods to be invoked. */
      $methods = [
        'routeHeaders',
        'routeResponseStatus',
        'routeContentType',
        'routeLocation'
      ];

      foreach ($methods as $method) {
        $result = $this->$method($allow_exit);
        
        if ($result === false) {
          return false;
        }
      }

      return true;
    }
    /** Get the *Initial Request Timestamp*
     *
     * @return string Returns the *Request Timestamp* as a `string`.
     */
    public function getRequestTime (): string {
      return self::$requestTime;
    }
    
    /** Initialize a new *`Router` Route*.
     *
     * @param RequestProperties $request_properties The `RequestProperties` object representing the *Request Properties & Headers* of the Route.
     * @param RoutedRequest $routed_request The `RoutedRequest` object used to represent the *Fulfilled Request*.
     * @return Route Returns the new *`Router` Route*.
     */
    public function __construct (RequestProperties $request_properties, RoutedRequest $routed_request) {    
      $this->requestProperties = $request_properties;
      $this->routedRequest = $routed_request;
      
      return $this;
    }
  }