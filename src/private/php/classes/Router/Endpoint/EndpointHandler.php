<?php
  namespace ShiftCodesTK\Router\Endpoint;
  
  use ShiftCodesTK\Router\Endpoint;

  /** The *Endpoint Handler* is responsible for processing requests to the Endpoint. */
  trait EndpointHandler {
    /** @var callable The *Endpoint Handler* responsible for processing requests to the Endpoint. */
    protected $endpointHandler = null;
    
    /** Set the *Endpoint Handler* responsible for the Endpoint
     *
     * @param callable $handler A `callable` function representing the *Endpoint Handler*.
     * - One argument is provided to the handler: The `Endpoint` object.
     * - The handler should return a `bool` on completion: **true** on success or **false** on failure.
     * @return \ShiftCodesTK\Router\Endpoint Returns the `Endpoint` object on success.
     */
    public function setEndpointHandler (callable $handler): Endpoint {
      $this->endpointHandler = $handler;

      return $this;
    }
    /** Invoke the *Endpoint Handler* responsible for the Endpoint.
     *
     * @return bool Returns the result of the *Endpoint Handler*: A `bool` representing **true** on success and **false** on failure.
     * @throws \Error if no *Endpoint Handler* has been registered for the Endpoint.
     * @throws \TypeError if the registered *Endpoint Handler* for the Endpoint does not return a `bool` value.
     */
    public function invokeEndpointHandler (): bool {
      $endpoint_name = $this->endpointSignature['endpoint'];

      if (!is_callable($this->endpointHandler)) {
        throw new \Error("No Endpoint Handler has been registered for Endpoint \"{$endpoint_name}\".");
      }

      $handler_result = ($this->endpointHandler)($this);

      if (!is_bool($handler_result)) {
        throw new \TypeError("The Endpoint Handler registered for Endpoint \"{$endpoint_name}\" does not return a Bool value.");
      }

      return $handler_result;
    }
  }