<?php
  namespace ShiftCodesTK\Router\RouterFramework\RequestProperties;

  use ShiftCodesTK\Strings;

  trait CustomRequestProperties {
    use RequestInfo,
        RequestData;

    /** @var array A list of *Request Properties* that have had custom values provided. */
    protected $customRequestProperties = [];

    /** Add custom values to a *Request Property*
     * 
     * @param string $property The *Request Info Property* to add the custom values to. 
     * @param array $data The data being added to the *Request Info Property*. Existing values are *overwritten*.
     * @return bool Returns **true** on success.
     * @throws \UnexpectedValueException if `$property` is not a valid *Request Info Property Name*. 
     */
    public function addCustomRequestInfo(string $property, $data): bool {
      if (!array_key_exists($property, $this->getRequestInfo())) {
        throw new \UnexpectedValueException("\"{$property}\" is not a valid Request Info Property Name.");
      }

      $this->$property = $data;

      // Update `$customRequestProperties`
      if (!in_array($property, $this->customRequestProperties)) {
        $this->customRequestProperties[] = $property;
      }

      return true;
    }
    /** Add custom request data to a *Request Data Field*
     * 
     * @param string $field The *Request Data Field* to add the custom data to. 
     * @param array $data The data being added to the *Request Data Field* as an `array`. Existing values are *overwritten*.
     * @return bool Returns **true** on success.
     * @throws \UnexpectedValueException if `$field` is not a valid *Request Data Field Name*. 
     */
    public function addCustomRequestData(string $field, array $data): bool {
      $field_name = Strings\transform($field, Strings\TRANSFORM_UPPERCASE);

      if (!array_key_exists($field_name, $this->requestData)) {
        throw new \UnexpectedValueException("\"{$field}\" is not a valid Request Data Field Name.");
      }

      $request_data = &$this->requestData[$field_name];
      $request_data = array_replace_recursive($request_data, $data);

      // Update `$customRequestProperties`
      (function () use ($field, $data) {
        if (!array_key_exists('requestData', $this->customRequestProperties)) {
          $this->customRequestProperties['requestData'] = [];
        }

        $custom_request_data = &$this->customRequestProperties['requestData'];

        if (!array_key_exists($field, $custom_request_data)) {
          $custom_request_data[$field] = [];
        }

        $custom_request_data[$field] = array_merge_recursive($custom_request_data[$field], array_keys($data));
      })();

      return true;
    }
  }