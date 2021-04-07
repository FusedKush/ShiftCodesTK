<?php
  namespace ShiftCodesTK\PHPConfigurationFiles;

  use ShiftCodesTK\Auth\Crypto,
      ShiftCodesTK\Validations;

  /** Represents a *Configuration Property* of a *PHP Configuration File*. */
  class ConfigurationProperty {
    /** @var string|null The *Name* of the Configuration Property, if available. */
    protected $name = null;
    /** @var mixed The *Value* of the Configuration Property. */
    protected $value = null;
    /** @var bool Indicates if the `$value` has been *Encrypted* or not. */
    protected $isEncrypted = false;
    /** @var string A *Timestamp String* indicating when the Configuration Property was last modified. */
    protected $lastModified = null;

    /** Initialize a new `ConfigurationProperty` object
     * 
     * @param array $options Options to be passed to the `ConfigurationProperty`:
     * 
     * | Property | Type | Default Value | Description |
     * | --- | --- | --- | --- |
     * | *name* | `string\|null` | `null` | The *Name* of the Configuration Property, if available. |
     * | *value* | `mixed` | `null` | The *Value* of the Configuration Property. |
     * | *isEncrypted* | `bool` | `false` | Indicates if the `$value` has been *Encrypted* or not. |
     * | *lastModified* | `string` | `null` | A *Timestamp String* indicating when the Configuration Property was last modified. |
     * @throws \TypeError if a Configuration Property is of an incorrect type.
     */
    public function __construct (array $options = []) {
      $required_types = [
        'name'         => 'string|null',
        'isEncrypted'  => 'bool',
        'lastModified' => 'string'
      ];

      foreach ($options as $property => $value) {
        if (property_exists(get_class($this), $property)) {
          if (\array_key_exists($property, $required_types)) {
            if (!Validations\check_type($value, $required_types[$property])) {
              $required_type = \ShiftCodesTK\Strings\str_replace($required_types[$property], '|', ', ');
              $provided_type = gettype($value);
    
              throw new \TypeError("\"{$property}\" must be a {$required_type}: {$provided_type} provided.");
            }
          }

          $this->$property = $value;
        }
      }

      if (!isset($this->lastModified)) {
        $this->updateTimestamp();
      }
    }
    /** Initialize a new `ConfigurationProperty` object using the exported properties.
     * 
     * @param mixed $properties The exported properties.
     * @return ConfigurationProperty Returns the `ConfigurationProperty` object representing the `$properties`.
     */
    public static function __set_state($properties) {
      return new ConfigurationProperty($properties);
    }

    /** Get an `array` representing the properties of the Configuration Property.
     * 
     * Note that the `::$value` will still be encrypted if `::$isEncrypted` is true. You can use `::getValue()` to retrieve the decrypted value.
     * 
     * @return array Returns an `array` representing the Configuration Property.
     * - `name`
     * - `value` *(Returns the raw value. Use `getValue()` instead if you need to *Decrypt* the value before retrieval.)*
     * - `isEncrypted`
     * - `lastModified`
     */
    public function getProperties () {
      $property_list = [];

      foreach ($this as $property => $value) {
        $property_list[$property] = $value;
      }

      return $property_list;
    }
    /** Retrive the *Value* of the Configuration Property
     * 
     * If you need the raw value, you can use `::getProperties()['value']` instead.
     * 
     * @param string|null $secret_key If the Configuration Property is *Encrypted*, the *Secret Key* used to Encrypt the Configuration Property.
     * - Without the Secret Key, you will be unable to decrypt the Configuration Property.
     * - Emits a *Notice* if the `$secret_key` is provided for a Configuration Property that is not Encrypted.
     * @return mixed Returns the Value of the Configuration Property on success.
     * @throws ArgumentCountError if the Configuration Property is *Encrypted* and the `$secret_key` was not provided.
     * @throws Error if Verification of the Encrypted Configuration Property failed.
     */
    public function getValue (string $secret_key = null) {
      $current_value = $this->value;

      if ($this->isEncrypted) {
        if (!isset($secret_key)) {
          throw new \ArgumentCountError("The Secret Key must be provided to decrypt the Encrypted Value.");
        }

        if (is_string($current_value)) {
          $decrypted_value = \ShiftCodesTK\Auth\Crypto\SecretKeyCrypto::decryptMessage($current_value, $secret_key);

          if (!$decrypted_value) {
            throw new \Error("Verification of the Configuration Property failed. The Secret Key is incorrect or the integrity of the Configuration Property Value has been compromised.");
          }

          return $decrypted_value;
        }
        else if (is_array($current_value) || is_object($current_value) && !($current_value instanceof ConfigurationProperty)) {
          $walk_traversable = function (&$traversable) use (&$walk_traversable, $secret_key) {
            foreach ($traversable as $arr_key => &$arr_value) {
              if (is_string($arr_value)) {
                try {
                  $decrypted_value = Crypto\SecretKeyCrypto::decryptMessage($arr_value, $secret_key);
  
                  if ($decrypted_value === false) {
                    throw new \Error("Verification of Array Key or Public Object Property \"{$arr_key}\" failed. The Secret Key is incorrect or the integrity of the value has been compromised.");
                  }
  
                  $arr_value = $decrypted_value;
                }
                catch (\Throwable $exception) {
                  throw new \Error("Array Key or Public Object Property \"{$arr_key}\" could not be decrypted: {$exception->getMessage()}");
                }
              }
              else if (is_array($arr_value) || is_object($arr_value) && !($arr_value instanceof ConfigurationProperty)) {
                $walk_traversable($arr_value);
              }
            }
          };

          $walk_traversable($current_value);

          return $current_value;
        }
      }
      else {
        if (isset($secret_key)) {
          trigger_error("A Secret Key was provided, but the property is not Encrypted.", E_USER_NOTICE);
        }

        return $current_value;
      }
    }
    /** Add or Change the *Value* of the Configuration Property
     * 
     * Remember to call `ConfigManager::writeConfigFile()` to commit the changes.
     * 
     * @param mixed $new_value The *New Value* of the Configuration Property.
     * - If a `$secret_key` is provided, this value **must** be a `string`, `array`, or `object`.
     * - - If an `array` or `object` is provided, `string` *Array Keys* or *Public Object Properties* will be Encrypted using the `$secret_key`. 
     * @param string|null $secret_key If provided, the `$new_value` will be *Encrypted* using this Secret Key. If you need to generate a Secret Key, see `ShiftCodesTK\Auth\Crypto\SecretKeyCrypto::generate_secret_key()`.
     * @return mixed Returns the *New Value* of the Configuration Property on success. If a `$secret_key` was provided, this value will be *Encrypted*. Otherwise, it should be identical to `$new_value`.
     * @throws \TypeError if a `$secret_key` is provided and `$new_value` is not a `string`.
     * @throws \Error if the `$new_value` is the same as the *Current Value**, or if the `$new_value` is an `Array` or an `Object` and an Array Key or Public Object Property could not be encrypted.
     */
    public function setValue ($new_value, string $secret_key = null) {
      $old_value = $this->getProperties()['value'];

      if (isset($secret_key)) {
        if (is_string($new_value)) {
          $encrypted_value = Crypto\SecretKeyCrypto::encryptMessage($new_value, $secret_key);
        }
        else if (is_array($new_value) || is_object($new_value)) {
          $walk_traversable = function (&$traversable) use (&$walk_traversable, $secret_key) {
            foreach ($traversable as $key => &$value) {
              if (is_string($value)) {
                try {
                  $value = Crypto\SecretKeyCrypto::encryptMessage($value, $secret_key);
                }
                catch (\Throwable $exception) {
                  throw new \Error("Array Key or Public Object Property \"{$key}\" could not be encrypted: {$exception->getMessage()}");
                }
              }
              else if (is_array($value) || is_object($value)) {
                $walk_traversable($value);
              }
            }
          };

          $walk_traversable($new_value);
          $encrypted_value = $new_value;
        }
        else {
          $current_type = gettype($new_value);

          throw new \TypeError("A \"String\", \"Array\", or \"Object\" must be provided if the value is being Encrypted. A \"{$current_type}\" was provided.");
        }

        if ($encrypted_value === $old_value) {
          throw new \Error("The New Value is the same as the Current Value.");
        }

        $this->value = $encrypted_value;
        $this->isEncrypted = true;
      }
      else {
        if ($new_value === $old_value) {
          throw new \Error("The New Value is the same as the Current Value.");
        }

        $this->value = $new_value;
        $this->isEncrypted = false;
      }

      $this->updateTimestamp();
      return $this->value;
    }
    
    /** Update the `$last_modified` timestamp of the Configuration Property
     * 
     * @param int|null $timestamp If provided, this is a *Unix Timestamp* generated by `ShiftCodesTK\Timestamps\tktime()`. If omitted, the *Current Timestamp* will be used. 
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function updateTimestamp (string $timestamp = null): bool {
      $original_timestamp = $this->lastModified;

      if (isset($timestamp)) {
        $this->lastModified = $timestamp;
      }
      else {
        $this->lastModified = \ShiftCodesTK\Timestamps\tktime();
      }

      return $this->lastModified !== $original_timestamp;
    }
  }
?>