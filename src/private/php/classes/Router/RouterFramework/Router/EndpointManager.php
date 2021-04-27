<?php
  namespace ShiftCodesTK\Router\RouterFramework\Router;
  
  use       ShiftCodesTK\Router\Endpoint,
            ShiftCodesTK\Strings;
  use const ShiftCodesTK\Paths\PHP_PATHS;

  /** The `EndpointManager` is responsible for retrieving and invoking *Frontend Endpoints*. */
  trait EndpointManager {
    /** Get the `Endpoint` object of a *Frontend Endpoint*
     *
     * @param string $endpoint The *Endpoint* being retrieved. Any *Leading Slashes* and *File Extensions* can be omitted.
     * @return Endpoint Returns the `Endpoint` object on success.
     * @throws \UnexpectedValueException if the `$endpoint` does not exist.
     * @throws \ParseError if the `$endpoint` does not return a valid {@see \ShiftCodesTK\Router\Endpoint}.
     */
    public static function getEndpoint (string $endpoint): Endpoint {
      $endpoint_name = Strings\preg_replace($endpoint, '(^\/|\.[\w\d]+$)', '');
      $full_path = PHP_PATHS['endpoints'] . "/{$endpoint_name}.php";
      
      if (!file_exists($full_path)) {
        throw new \UnexpectedValueException("Endpoint \"{$endpoint}\" does not exist.");
      }
      
      $endpoint_obj = (function () use ($full_path) {
        return include($full_path);
      })();
      
      if ($endpoint_obj === false || $endpoint_obj === 1 || !is_a($endpoint_obj, Endpoint::class)) {
        throw new \ParseError("Endpoint \"{$endpoint}\" does not return a valid Endpoint Object.");
      }

      return $endpoint_obj;
    }
    /** Call a *Frontend Endpoint*
     *
     * @param string $endpoint The *Endpoint* being called. Any *Leading Slashes* and *File Extensions* can be omitted.
     * @return array Returns an `array` representing the *Routed Request* on success.
     */
    public static function callEndpoint (
      string $endpoint,
      string $request_method = RequestProperties::REQUEST_METHOD_GET,
      array $custom_properties = null
    ): array {
      $endpoint_obj = self::getEndpoint($endpoint);
      $request_properties = &$endpoint_obj->getRequestProperties();

      $request_properties->addCustomRequestInfo('requestMethod', $request_method);

      if (isset($custom_properties)) {
        foreach ($custom_properties as $property => $property_data) {
          if (array_key_exists($property, $request_properties->getRequestData())) {
            $request_properties->addCustomRequestData($property, $property_data);
          }
          else {
            $request_properties->addCustomRequestInfo($property, $property_data);
          }
        }
      }
      
      $endpoint_obj->invokeEndpointHandler();

      return $endpoint_obj->getRoutedRequest()
        ->getResponseData();
    }
  }