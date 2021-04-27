<?php
  namespace ShiftCodesTK\Router\Endpoint;
  
  use ShiftCodesTK\Router\Endpoint,
      ShiftCodesTK\Validations\VariableEvaluator;

  /** The *Response Properties* of the Endpoint represent the results of its invocation. */
  trait ResponseProperties {
    use EndpointSignature;
    
    /** Add a *Response Value* to the *Endpoint Signature*.
     *
     * @param string $property The *Response Property*.
     * A *Dot* (`.`) represents an *Array Key* or *Object Property*.
     * @param VariableEvaluator $evaluator The `VariableEvaluator` object responsible for validating the *Response Property*.
     * @return Endpoint Returns the `Endpoint` object on success.
     * @throws \Error if `$property` is already part of the Endpoint Signature.
     */
    public function addResponseProperty (string $property, VariableEvaluator $evaluator): Endpoint {
      return $this->addEndpointSignatureProperty('responseProperties', ...func_get_args());
    }
    /** Get the *Response Property Signature* for the Endpoint
     *
     * @return array Returns an `array` representing the *Response Property Signature* for the Endpoint.
     */
    public function getResponsePropertySignature (): array {
      $property_signature = [];
      $response_properties = $this->getEndpointPropertySignature('responseProperties');

      foreach ($response_properties as $property => $constraints) {
        $signature = &array_nested_value($property_signature, $property, true);

        $signature = $constraints;
      }

      return $property_signature;
    }
    /** Get the *Response Properties* from the Routed Request
     *
     * @param bool $validate_parameters Indicates if the Response Properties should be *Validated* during retrieval. Defaults to **true**.
     * @return array Returns an `array` of the *Response Values* from the Routed Request
     * If a value was not provided, its value will be **null**.
     */
    public function getResponseProperties (bool $validate_parameters = true): array {
      $response_properties = [];
      $payload = $this->routedRequest
        ->getResponseData('payload');

      /**
       * @var Validations\VariableEvaluator $evaluator
       */
      foreach ($this->endpointSignature['responseProperties'] as $property => $evaluator) {
        $response_property = &array_nested_value($response_properties, $property, true);
        $payload_property = &array_nested_value($payload, $property);
    
        if ($validate_parameters) {
          $evaluator->check_variable($payload_property, $property);
          $response_property = $evaluator->get_last_result('variable');
        }
        else {
          $response_property = $payload_property;
        }
      }
  
      return $response_properties;
    }
  }