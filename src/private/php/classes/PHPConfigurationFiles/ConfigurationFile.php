<?php
  namespace ShiftCodesTK\PHPConfigurationFiles;

  use ShiftCodesTK\Validations;

  /** Represents a *PHP Configuration File*. */
  class ConfigurationFile implements Interfaces\ConfigurationInterface {
    use LocalInterfaceHelpers;

    /** @var string A *Semver*-compatible Version Number representing the *Current Version* of the PHP Configuration File class declaration. */
    public const CONFIGURATION_FILE_VERSION = "1.0.0";

    /** @var string|null If applicable, the *Alias* of the Configuration File. */
    protected $alias = self::DEFAULT_CONFIGURATION_OPTIONS['alias'];
    /** @var string A `CONFIGURATION_TYPE_*` interface constant representing the *Configuration Type* of the PHP Configuration File. */
    protected $type = self::DEFAULT_CONFIGURATION_OPTIONS['type'];
    /** @var string The *PHP Configuration File Version* of the Configuration File. */
    protected $version = self::DEFAULT_CONFIGURATION_OPTIONS['version'];
    /** @var null|string If applicable, the *PHP Comment* of the Configuration File. */
    protected $comment = self::DEFAULT_CONFIGURATION_OPTIONS['comment'];

    /** @var array|object|ConfigurationProperty The *File Contents* of the Configuration File. */
    protected $contents = self::DEFAULT_CONFIGURATION_OPTIONS['contents'];
    /** @var array Represents an *Index* of the *Configuration File Values*. */
    protected $configurationValueIndex = [];

    /** Get the *Base Property* of a *Property Name `String`* 
     * 
     * @param string $property The *Property Name `string`* being evaluated.
     * @return string Returns the *Base Property* of the `$property`.
     */
    protected static function getBasePropertyName (string $property): string {
      return explode(self::CONFIGURATION_PROPERTY_DELIMITER, $property, 2)[0];
    }

    /** Initialize a new `ConfigurationFile` object using the exported properties.
     * 
     * @param array $properties The exported properties.
     * @return ConfigurationFile|null Returns the `ConfigurationFile` object representing the `$properties` on success, or **null** if the `ConfigurationFile` could not be imported.
     */
    public static function __set_state($properties) {
      try {
        $options = [];
        $default_options = (function () {
          $default_options = self::DEFAULT_CONFIGURATION_OPTIONS;
          $default_options['configurationValueIndex'] = [];
  
          return $default_options;
        })();

        foreach ($default_options as $option => $default_value) {
          $options[$option] = $properties[$option] ?? $default_value;
        }

        return new ConfigurationFile($options);
      }
      catch (\Throwable $exception) {
        \trigger_error("ConfigurationFile could not be imported: {$exception->getMessage()}");
        return null;
      }
    }

    /** Validate the current *Configuration File Type* of the Configuration File
     * 
     * @param string $required_type The expected *Configuration File Type*.
     * @param bool $is_required Indicates if the `$required_type` *must match* (**true**) or *must not match* (**false**) the current Configuration File Type.
     * @param bool $throw_errors Indicates if a `BadMethodCallException` should be thrown if validation fails.
     * @return bool Returns **true** if the current *Configuration File Type* does or does not match the `$required_type`, depending on the value of `$is_required`.
     * @throws \UnexpectedValueException if `$required_type` is not a valid Configuration File Type.
     * @throws \BadMethodCallException if $throw_errors` is **true** and the current *Configuration File Type* does or does not match the `$required_type`.
     */
    protected function validateConfigurationType (string $required_type, bool $is_required = true, bool $throw_errors = false) {
      $current_type = $this->type;
      $is_valid_type = (function () use ($required_type, $is_required, $current_type) {
        if (!Validations\check_match($required_type, self::CONFIGURATION_TYPES_LIST)) {
          throw new \UnexpectedValueException("\"{$required_type}\" is not a valid Configuration File Type.");
        }

        if ($is_required) {
          return $current_type === $required_type;
        }
        else {
          return $current_type !== $required_type;
        }
      })();

      if ($throw_errors) {
        if (!$is_valid_type) {
          if ($is_required) {
            throw new \BadMethodCallException("This method expects Configuration File Type \"{$required_type}\": \"{$current_type}\" found.");
          }
          else {
            throw new \BadMethodCallException("This method does not support Configuration File Type \"{$current_type}\".");
          }
        }

        return true;
      }
      else {
        return $is_valid_type;
      }
    }

    /** Initialize a new `ConfigurationFile`
     * 
     * @param array $options An `Associative Array` of options to pass to the object:
     * 
     * | Key | Default Value | Description |
     * | --- | --- | --- |
     * | *alias* | `null` | The *Alias* of the Configuration File, if available. |
     * | *type* | `::CONFIGURATION_TYPE_ARRAY` | A `CONFIGURATION_TYPE_*` class constant representing the *Variable Type* that the Configuration File should hold. |
     * | *version* | `null` | The *PHP Configuration File Version* of the Configuration File. |
     * | *comment* | `null` | If applicable, the *PHP Comment* of the Configuration File. |
     * | *contents* | `null` | The *File Contents* of the Configuration File. |
     * | *configurationValueIndex* | `[]` | Represents an *Index* of the *Configuration File Values*. |
     */
    public function __construct (array $options = []) {
      $this->changeConfigurationProperties($options);

      if (\array_key_exists('contents', $options)) {
        $this->changeConfigurationContents($options['contents']);
      }

      // Check Index
      $this->listConfigurationValues();
    }

    /** Regenerate the *Configuration File* using the latest `::CONFIGURATION_FILE_VERSION`.
     * 
     * @return \ShiftCodesTK\PHPConfigurationFiles\ConfigurationFile|false 
     * On success, returns the regenerated `Configuration File`. Returns **false** if the Configuration File could not be regenerated.
     */
    public function regenerateConfigurationFile () {
      try {
        $default_options = (function () {
          $default_options = self::DEFAULT_CONFIGURATION_OPTIONS;

          unset($default_options['version']);

          return $default_options;
        })();
        $options = [];

        foreach ($default_options as $option => $default_value) {
          if (\property_exists($this, $option)) {
            $options[$option] = $this->$option;
          }
        }

        return new ConfigurationFile($options);
      }
      catch (\Throwable $exception) {
        return false;
      }
    }

    /** Get the *Configuration Properties* of the Configuration File.
     * 
     * @return array Returns an `Associative Array` representing the *Configuration Properties* of the Configuration File:
     * 
     * | Property | Type | Description |
     * | --- | --- | --- |
     * | *alias* | `string\|null` | If applicable, the *Alias* of the Configuration File. |
     * | *type* | `string` | A `CONFIGURATION_TYPE_*` interface constant representing the *Configuration Type* of the PHP Configuration File. |
     * | *version* | `string` | The *PHP Configuration File Version* of the Configuration File. |
     * | *comment* | `string\|null` | If applicable, the *Alias* of the Configuration File. |
     */
    public function getConfigurationProperties (): array {
      $property_list = (function () {
        $default_options = self::DEFAULT_CONFIGURATION_OPTIONS;

        unset($default_options['contents']);

        return $default_options;
      })();

      return $this->getLocalConfigurationProperties($property_list);
    }
    /** Update the *Configuration Properties* of the Configuration File
     * 
     * @param array $properties An `Associative Array` of Configuration Properties to update.
     * - Providing a value of **null** for a Configuration Property will reset its value to the **Default Value**. 
     * 
     * | Property | Type | Default Value | Description |
     * | --- | --- | --- | --- |
     * | *alias* | `string\|null` | `null` | If applicable, the *Alias* of the Configuration File. |
     * | *type* | `string` | `::CONFIGURATION_TYPE_ARRAY` | A `CONFIGURATION_TYPE_*` interface constant representing the *Configuration Type* of the PHP Configuration File. **Note**: Changing this value will **remove** the current *File Contents*, as they will no longer be compatible. |
     * | *version* | `string` | `::CONFIGURATION_FILE_VERSION` | The *PHP Configuration File Version* of the Configuration File. |
     * | *comment* | `string\|null` | `null` | If applicable, the *Alias* of the Configuration File. |
     * @return true Returns **true** on success.
     * @throws \UnexpectedValueException if the provided *Configuration File Type* or *Configuration File Version* is invalid.
     * @throws \RuntimeException if a *Configuration Property* was not updated successfully.
     */
    public function changeConfigurationProperties (array $properties): bool {
      $property_setters = [
        'type'   => function ($type) {
          if (!Validations\check_match($type, self::CONFIGURATION_TYPES_LIST)) {
            throw new \UnexpectedValueException("\"{$type}\" is not a valid Configuration Type.");
          }

          $this->type = $type;
          $this->changeConfigurationContents();
        },
        'version' => function ($version) {
          if (isset($version)) {
            if (!Validations\check_pattern($version, \ShiftCodesTK\COMMON_REGEXES['semver_version_number'])) {
              throw new \UnexpectedValueException("\"{$version}\" is not a Semver-compatible Version Number.");
            }
    
            $this->version = $version;
          }
          else {
            $this->version = self::CONFIGURATION_FILE_VERSION;
          }
        }
      ];
      $property_list = (function () {
        $default_options = self::DEFAULT_CONFIGURATION_OPTIONS;
        $default_options['configurationValueIndex'] = [];

        unset($default_options['contents']);

        return $default_options;
      })();

      return $this->changeLocalConfigurationProperties($property_setters, $property_list, $properties);
    }
    /** Get the *File Contents* of the Configuration File
     * 
     * @return array|object|ConfigurationProperty Returns the *File Contents* of the Configuration File on success.
     */
    public function &getConfigurationContents () {
      return $this->contents;
    }
    /** Change the *File Contents* of a Configuration File
     * 
     * @param mixed $contents The *New Contents* of the Configuration File. 
     * - Must match the current *Configuration File Type*.
     * - If omitted, _all of the current contents will be **removed**_.
     * @return bool Returns **true** on success and **false** on failure.
     * @throws \TypeError if the `$contents` do not match the current *Configuration File Type*.
     */
    public function changeConfigurationContents ($contents = null): bool {
      $required_type = $this->type;

      if (isset($contents)) {
        $contents_type = gettype($contents);
        $has_valid_contents = ($required_type !== self::CONFIGURATION_TYPE_PROPERTY
                                && $contents_type === $this->type)
                              || ($required_type === self::CONFIGURATION_TYPE_PROPERTY
                                && $contents instanceof ConfigurationProperty);

        if (!$has_valid_contents) {
          if ($required_type !== self::CONFIGURATION_TYPE_PROPERTY) {
            throw new \TypeError("An \"{$required_type}\" was expected: \"{$contents_type}\" provided.");
          }
          else {
            $classname = $contents_type === 'object'
                    ? get_class($contents) . ' '
                    : '';

            throw new \TypeError("An instance of \"{$required_type}\" was expected: \"{$classname}{$contents_type}\" provided.");
          }
        }

        $this->contents = $contents;
      }
      else {
        $this->contents = null;
        
        if ($required_type !== self::CONFIGURATION_TYPE_PROPERTY) {
          \settype($this->contents, $required_type);
        }
        else {
          $this->contents = new ConfigurationProperty([
            'name' => $this->alias
          ]);
        }
      }

      return true;
    }
    
    /** List all of the stored *Configuration Values*
     * 
     * @param bool $flush_index Indicates if the *Configuration Value Index* should be flushed and reconstructed. 
     * @return array Returns an `Array` representing all of the stored *Configuration Values*.
     */
    public function listConfigurationValues (bool $flush_index = false): array {
      if ($flush_index || !isset($this->configurationValueIndex) || empty($this->configurationValueIndex)) {
        $this->configurationValueIndex = [];

        if ($this->contents instanceof ConfigurationProperty) {
          $this->addConfigurationValueToIndex($this->contents, $this->alias);
        }
        else if (is_array($this->contents) || is_object($this->contents)) {
          foreach ($this->contents as $property => $value) {
            $this->addConfigurationValueToIndex($value, $property);
          }
        }
  
        // $get_var_values = function ($var, $parent = '') use (&$get_var_values, &$values) {
        //   foreach ($var as $property => $value) {
        //     $full_property_name = $parent . self::CONFIGURATION_PROPERTY_DELIMITER . $property;
  
        //     if ($value instanceof ConfigurationProperty) {
        //       $values[] = $full_property_name;
        //     }
        //     else if (is_array($value) || is_object($value)) {
        //       $get_var_values($value, $full_property_name);
        //     }
        //   }
        // };
  
        // $get_var_values($this->contents);
        // $this->configurationValueIndex = $values;
      }

      return $this->configurationValueIndex;
    }
    /** Add a *Configuration Value* to the *Configuration Value Index*.
     * 
     * @param ConfigurationProperty $configuration_value The *Configuration Value* being indexed, as a `ConfigurationProperty` object.
     * @param string|null $property_name The *Full Configuration Value Property Name* to be added. 
     * @return bool Returns **true** on success and **false** on failure.
     * @throws \Error if `$property_name` already exists in the Index.
     */
    public function addConfigurationValueToIndex (ConfigurationProperty $configuration_value, string $property_name = null): bool {
      $raw_value = $configuration_value->getProperties()['value'];
      $base_property = isset($property_name)
                       ? self::getBasePropertyName($property_name)
                       : '';
      $index = $this->configurationValueIndex;

      $check_traversable = function ($traversable, string $parent_property) use (&$check_traversable, &$index) {
        foreach ($traversable as $property => $value) {
          $full_property_name = $parent_property . self::CONFIGURATION_PROPERTY_DELIMITER . $property;
  
          if ($value instanceof ConfigurationProperty) {
            $values[] = $full_property_name;
          }
          if (is_array($value) || is_object($value)) {
            $check_traversable($value, $full_property_name);
          }
          else {
            $index[] = $full_property_name;
          }
        }
      };
        
      if (in_array($base_property, $this->configurationValueIndex)) {
        throw new \Error("Configuration Value \"{$property_name}\" has already been indexed.");
      }

      if (Validations\check_var($base_property)) {
        $index[] = $base_property;
      }

      if (is_array($raw_value) || is_object($raw_value)) {
        $check_traversable($raw_value, $base_property);
      }

      $this->configurationValueIndex = $index;

      return in_array($base_property, $this->configurationValueIndex);
    }
    /** Remove a *Configuration Value* from the *Configuration Value Index*.
     * 
     * @param string $property_name The *Full Configuration Value Property Name* to be removed. 
     * @return bool Returns **true** on success and **false** on failure.
     * @throws \Error if `$property_name` does not exist in the Index.
     */
    public function removeConfigurationValueFromIndex (string $property_name): bool {
      $index = $this->configurationValueIndex;
      $base_property = self::getBasePropertyName($property_name);
      $raw_value = $this->getConfigurationValue($base_property);

      $check_traversable = function ($traversable, string $parent_property) use (&$check_traversable, &$index) {
        foreach ($traversable as $property => $value) {
          $full_property_name = $parent_property . self::CONFIGURATION_PROPERTY_DELIMITER . $property;
  
          if ($value instanceof ConfigurationProperty) {
            $values[] = $full_property_name;
          }
          if (is_array($value) || is_object($value)) {
            $check_traversable($value, $full_property_name);
          }
          else {
            $pos = \array_search($full_property_name, $index);

            if ($pos !== false) {
              \array_splice(
                $index,
                $pos,
                1
              );
            }

          }
        }
      };

      if (!in_array($base_property, $index)) {
        throw new \Error("Configuration Value \"{$property_name}\" has not been indexed.");
      }

      \array_splice(
        $index,
        \array_search($base_property, $index),
        1
      );

      if (\is_array($raw_value) || \is_object($raw_value)) {
        $check_traversable($raw_value, $base_property);
      }

      $this->configurationValueIndex = $index;

      return !in_array($base_property, $this->configurationValueIndex);
    }

    /** Check if a *Configuration Value* has been defined
     * 
     * @param string $property The *Property Name* of the Configuration Value to check for.
     * @return bool Returns **true** if `$property` has been defined, or **false** if it has not. 
     */
    public function configurationValueExists (string $property): bool {
      $config_values = $this->listConfigurationValues();

      return in_array($property, $config_values);
    }
    /** Get a *Configuration Value* from the Configuration File
     * 
     * @param string|null $property The *Property Name* of the Configuration Value to retrieve.
     * 
     * You can use the `::CONFIGURATION_PROPERTY_DELIMITER` to signify an *`Array` Key* or *Public `Object` Property* to access.
     * - For example, `foo.bar.baz` can refer to the following `foo[bar]->baz`.
     * 
     * If omitted, the *Full Configuration File Contents* will be returned.
     * @param string $secret_key If the `$property` is *Encrypted*, this is the *Secret Key* needed to Decrypt the property.
     * @return mixed Returns the *Configuration Value* represented by `$property` on success. 
     * @throws \TypeError if a `$property` is provided, and the File Contents are not a traversable `Array` or `Object`.
     * A `TypeError` will also be thrown if a *Subkey* of `$property` specifies an Array Key or Public Object Property that is not a traversable `Array` or `Object`.
     * @throws \Error if the `$property` was not found.
     */
    public function getConfigurationValue (string $property = null, string $secret_key = null) {
      $file_contents = $this->getConfigurationContents();
      
      if (isset($property)) {
        if (!$this->configurationValueExists($property)) {
          throw new \Error("Configuration Value \"{$property}\" does not exist.");
        }
        else if (!is_array($file_contents) && !is_object($file_contents)) {
          throw new \TypeError("A Configuration Value Property was provided, but the File Contents are not a traversable Array or Object.");
        }
  
        $subkeys = explode(self::CONFIGURATION_PROPERTY_DELIMITER, $property);
        $subcontents = $file_contents;
  
        foreach ($subkeys as $subkey) {
          if (is_array($subcontents)) {
            $subcontents = $subcontents[$subkey];
          }
          else {
            $subcontents = $subcontents->$subkey;
          }
  
          if ($subcontents instanceof ConfigurationProperty) {
            $subcontents = $subcontents->getValue($secret_key);
          }
        }

        return $subcontents;
      }
      else {
        if ($file_contents instanceof ConfigurationProperty) {
          return $file_contents->getValue($secret_key);
        }
        else {
          return $file_contents;
        }
      }
    }
    /** Add a *Configuration Value* to the Configuration File
     * 
     * Requires the *Configuration File Type* to be `{@see ::CONFIGURATION_TYPE_ARRAY}` or `{@see ::CONFIGURATION_TYPE_OBJECT}`.
     * 
     * @param string $property_name The *Property Name* of the Configuration Value. 
     * - Cannot already exist within the *Configuration File*.
     * @param mixed $property_value The *Property Value* of the Configuration Value.  If the `$secret_key` is provided, this **must** be a `string`, `array`, or `object`.
     * @param string|null $secret_key If provided, a *Secret Key* used to *Encrypt* the `$property_value` when storing it.
     * - If you need a Secret Key, you can use `{@see ShiftCodesTK\Auth\Crypto\SecretKeyCrypto::generateSecretKey()}` to generate one.
     * @return bool Returns **true** on success and **false** on failure.
     * @throws \Error if `$property_name` already exists.
     */
    public function addConfigurationValue (string $property_name, $property_value, string $secret_key = null): bool {
      $base_property = self::getBasePropertyName($property_name);
      
      $this->validateConfigurationType(self::CONFIGURATION_TYPE_PROPERTY, false, true);

      if ($this->configurationValueExists($base_property)) {
        throw new \Error("Configuration Value \"{$base_property}\" already exists.");
      }

      $property = new ConfigurationProperty([
        'name'        => $base_property,
        'isEncrypted' => isset($secret_key)
      ]);
      $property->setValue($property_value, $secret_key);
      $contents = &$this->getConfigurationContents();

      if ($this->type === self::CONFIGURATION_TYPE_ARRAY) {
        $contents[$base_property] = $property;
      }
      else {
        $contents->$base_property = $property;
      }

      $updated_contents = $this->changeConfigurationContents($contents);

      if ($updated_contents) {
        $updated_index = $this->addConfigurationValueToIndex($property, $base_property);

        if ($updated_index) {
          return $this->configurationValueExists($base_property);
        }
      }

      return false;
    }
    /** Remove a *Configuration Value* from the Configuration File.
     * 
     * Requires the *Configuration File Type* to be `{@see ::CONFIGURATION_TYPE_ARRAY}` or `{@see ::CONFIGURATION_TYPE_OBJECT}`.
     * 
     * @param string $property_name The *Property Name* of the Configuration Value being removed.
     * @return bool Returns **true** on success and **false** on failure.
     * @throws Error if the `$property_name` does not exist.
     */
    public function removeConfigurationValue (string $property_name): bool {
      $this->validateConfigurationType(self::CONFIGURATION_TYPE_PROPERTY, false, true);

      $base_property = self::getBasePropertyName($property_name);
      $contents = $this->getConfigurationContents();

      if (!$this->configurationValueExists($base_property)) {
        throw new \Error("Property Value \"{$base_property}\" does not exist.");
      }

      if (is_array($contents)) {
        unset($contents[$base_property]);
      }
      else {
        unset($contents->$base_property);
      }

      $updated_contents = $this->changeConfigurationContents($contents);
      
      if ($updated_contents) {
        $updated_index = $this->removeConfigurationValueFromIndex($base_property);

        if ($updated_index) {
          return !$this->configurationValueExists($base_property);
        }
      }

      return false;
    }
    /** Update the *Configuration Property* of the Configuration File
     * 
     * @param string|null $property_name The *Property Name* of the Configuration Value being updated. 
     * - If the *Configuration File Type* is `{@see ::CONFIGURATION_TYPE_PROPERTY}`, this argument is ignored, and can be omitted.
     * - If the *Configuration File Type* is `{@see ::CONFIGURATION_TYPE_ARRAY}` or `{@see ::CONFIGURATION_TYPE_ARRAY}`, this argument **must** be provided.
     * @param mixed $property_value The new value of the property.
     * - If a `$secret_key` is provided, this value **must** be a `string`, `array`, or `object`.
     * @param string|null $secret_key If provided, a *Secret Key* used to *Encrypt* the `$property_value` when storing it.
     * - If you need a Secret Key, you can use `{@see ShiftCodesTK\Auth\Crypto\SecretKeyCrypto::generateSecretKey()}` to generate one.
     * @return bool Returns **true** on success and **false** on failure.
     * @throws \Error if `$property_name` does not exist.
     */
    public function updateConfigurationValue ($property_name = null, $property_value, string $secret_key = null): bool {
      if (!isset($property_name)) {
        if (!$this->validateConfigurationType(self::CONFIGURATION_TYPE_PROPERTY, true)) {
          if (!isset($property_name)) {
            throw new \ArgumentCountError("The \"Property Name\" argument must be provided when the Configuration File Type is \"{$this->type}\".");
          }
        }
      }

      $contents = &$this->getConfigurationContents();
      
      if (!isset($contents)) {
        $this->changeConfigurationContents();
      }
      
      if ($this->type === self::CONFIGURATION_TYPE_PROPERTY) {

        $last_modified = $contents->getProperties()['lastModified'];
        $contents->setValue($property_value, $secret_key);
        $this->listConfigurationValues(true);

        return $contents->getProperties()['lastModified'] !== $last_modified;
      }
      else {
        $base_property = self::getBasePropertyName($property_name);

        if (!$this->configurationValueExists($base_property)) {
          throw new \Error("Property Value \"{$base_property}\" does not exist.");
        }

        /** @var ConfigurationProperty */
        $property = &$contents[$base_property];
        $last_modified = $property->getProperties()['lastModified'];

        $property->setValue($property_value, $secret_key);

        return $property->getProperties()['lastModified'] !== $last_modified;
      }
    }
  }
?>