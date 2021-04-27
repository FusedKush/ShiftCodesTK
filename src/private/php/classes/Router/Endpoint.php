<?php
  namespace ShiftCodesTK\Router;

  use ShiftCodesTK\Strings,
      ShiftCodesTK\Validations,
      ShiftCodesTK\Validations\VariableEvaluator;

  class Endpoint extends \ShiftCodesTK\Router {
    use Endpoint\EndpointSignature,
        Endpoint\RequestMethod,
        Endpoint\RequestParameters,
        Endpoint\ResponseProperties,
        Endpoint\EndpointHandler;
  
    /** Create a new `Endpoint`
     *
     * @param string $endpoint The *Endpoint Identifier*.
     * @return Endpoint Returns the new `Endpoint`.
     */
    public static function newEndpoint (string $endpoint): Endpoint {
      $endpoint = new Endpoint(
        $endpoint,
        new RequestProperties(),
        new RoutedRequest()
      );
      
      $endpoint->getRoutedRequest()
               ->syncRouterData($endpoint);
               
      return $endpoint;
    }

    /** Route the Endpoint and complete the request
     * 
     * @param bool $allow_exit Indicates if the script is permitted to **Exit** during routing. 
     * Defaults to **true**.
     * Has no effect on the routing performed by the {@see \ShiftCodesTK\Router}, which will never exit.
     * @return bool|exit Outputs the *Endpoint Response* and returns **true** on success. 
     * **Exits** the script if `$allow_exit` is **true**.
     * Returns **false** if routing failed.
     */
    public function route (bool $allow_exit = true): bool {
      $routed = parent::route(false);

      if (!$routed) {
        return false;
      }

      echo $this->routedRequest
        ->getResponseJSON();

      if ($allow_exit) {
        exit();
      }

      return true;
    }
    
    /** Initialize a new `Endpoint`
     *
     * @param string $endpoint The *Endpoint Identifier* as it is invoked.
     */
    public function __construct (string $endpoint, RequestProperties $request_properties, RoutedRequest $routed_request) {
      parent::__construct($request_properties, $routed_request);
    
      $this->endpointSignature['endpoint'] = Strings\preg_replace($endpoint, '%^\/|\.[\w\d]+$%', '');
      $this->contentType('application/json', 'UTF-8');
      
      return $this;
    }
  }