<?php
  namespace ShiftCodesTK\Router\Endpoint;
  
  use ShiftCodesTK\Router\Endpoint,
      ShiftCodesTK\Validations\VariableEvaluator;

  /** The *Endpoint Signature* represents the model and schema of the Endpoint. */
  trait EndpointSignature {
    /** @var array Represents the *Endpoint Signature* of the Endpoint
     *
     * | Property             | Type      | Description                                             |
     * | ---                  | ---       | ---                                                     |
     * | *endpoint*           | `string`  | The *Endpoint Identifier*.                              |
     * | *requestParameters*  | `array`   | The recognized *Request Parameters* for the Endpoint.   |
     * | *responseProperties* | `array`   | The expected *Response Values* of the Endpoint.         |
     */
    protected $endpointSignature = [
      'endpoint'            => null,
      'requestMethods'      => [],
      'requestParameters'   => [],
      'responseProperties'  => []
    ];
    
    /** Add a property to the *Endpoint Signature*.
     *
     * @param string $signature_property The property of the *Endpoint Signature* being modified.
     * Options include `requestParameters` & `responseValues`.
     * @param string $property_name The *Name* of the new property.
     * @param VariableEvaluator $property_evaluator The *`VariableEvaluator` object responsible for validating the property.
     * @return Endpoint Returns the `Endpoint` object on success.
     * @throws \Error if `$property_name` is already part of the Endpoint Signature.
     */
    protected function addEndpointSignatureProperty (string $signature_property, string $property_name, VariableEvaluator $property_evaluator): Endpoint {
      $signature_values = &$this->endpointSignature[$signature_property];

      if (array_key_exists($property_name, $signature_values)) {
        throw new \Error("\"{$property_name}\" is already part of the Endpoint Signature.");
      }
    
      $signature_values[$property_name] = $property_evaluator;
      return $this;
    }
    /** Get the *Endpoint Property Signature* for the Endpoint
     *
     * The *Property Signature* indicates what type of value is expected for each of the given properties.
     *
     * @param string $signature_property The property of the *Endpoint Signature* being evaluated.
     * Options include `requestParameters` & `responseValues`.
     * @return array Returns an `array` representing the *Endpoint Property Signature* for the Endpoint.
     */
    protected function getEndpointPropertySignature (string $signature_property): array {
      $parameter_signature = [];
      $validator_properties = [
        'type',
        'default_value',
        'required',
        'readonly',
        'validations'
      ];
      $signature_values = &$this->endpointSignature[$signature_property];

      /** @var VariableEvaluator $parameter_evaluator */
      foreach ($signature_values as $parameter_name => $parameter_evaluator) {
        $parameter_signature[$parameter_name] = [];

        foreach ($validator_properties as $property) {
          $parameter_signature[$parameter_name][$property] = $parameter_evaluator->$property;
        }
        
        // Update Validations
        (function () use (&$parameter_signature, $parameter_name) {
          $validations = &$parameter_signature[$parameter_name]['validations'];
  
          $validations = array_combine(
            (new Strings\StringArrayObj(array_keys($validations)))
              ->str_replace('check_', '')
              ->get_array(),
            array_values($validations)
          );
  
          if (array_key_exists('match', $validations)) {
            if (array_key_exists('matches', $validations['match'])) {
              $validations['match']['matches'] = [];
            }
            else {
              $validations['match'] = [];
            }
          }
        })();
      }

      return $parameter_signature;
    }
    
    /** Get the *Endpoint Signature* of the Endpoint
     *
     * @return array Returns an `array` representing the *Endpoint Signature* of the Endpoint.
     */
    public function getEndpointSignature (): array {
      $endpoint_signature = $this->endpointSignature;

      $endpoint_signature['requestParameters'] = $this->getRequestParameterSignature();
      $endpoint_signature['responseProperties'] = $this->getResponsePropertySignature();

      return $endpoint_signature;
    }
  }