<?php
  namespace ShiftCodesTK\PHPConfigurationFiles;

  use ShiftCodesTK\Strings,
      ShiftCodesTK\Validations;

  /** The `ConfigurationManager` is responsible for managing custom PHP Configuration Files. */
  class ConfigurationManager implements Interfaces\ManagerInterface {
    use LocalInterfaceHelpers;

    /** @var array The *Default Configuration Options* of the `ConfigurationManager`. */
    public const DEFAULT_MANAGER_OPTIONS = [
      'fileinfo' => [
        'dirPath'  => null,
        'fileName' => null,
        'filePath' => null
      ]
    ];

    /** @var string[]|null[] Represents the location of the *Configuration File* 
     * 
     * | Key | Description |
     * | --- | --- |
     * | *dirPath* | The path to the *File Directory* containing the PHP Configuration File. |
     * | *fileName* | The *Filename* of the PHP Configuration File. |
     * | *filePath* | The *Full Path* to the PHP Configuration File; A combination of `dir_path` and `file_path`. |
     */
    protected $fileinfo = self::DEFAULT_MANAGER_OPTIONS['fileinfo'];
    /** @var ConfigurationFile|null The *Contents* of the PHP Configuration File. */
    protected $contents = null;

    /** Initialize a new `Configuration Manager`
     * 
     * @param string $file_path The *Full Path* to the *PHP Configuration File*.
     * @param ConfigurationFile|null $file_contents The *Contents* of the PHP Configuration File as a `ConfigurationFile` object.
     * - If no *File Contents* are provided and the Configuration File has already been created, automatically retrieves the File Contents using `::readConfigurationFile()`.
     * @throws UnexpectedValueException if the `$configuration_file` is not a valid *Relative* or *Absolute URL*.
     */
    public function __construct (string $file_path, ConfigurationFile $file_contents = null) {
      // `::$fileinfo`
      (function () use ($file_path) {
        if (!Validations\check_path($file_path)) {
          throw new \UnexpectedValueException("The Configuration File is not a valid File Path.");
        }
  
        $config_file_pieces = Strings\preg_match($file_path, '%^(.+?)(?:\/|\\\){1,2}([^\/\\\]+)$%', Strings\PREG_RETURN_SUB_MATCHES);

        $this->fileinfo['dirPath'] = $config_file_pieces[0];
        $this->fileinfo['fileName'] = $config_file_pieces[1];
        $this->fileinfo['filePath'] = $file_path;
      })();

      if (isset($file_contents)) {
        $this->contents = $file_contents;
      }

      if (!$this->contents->getConfigurationValue() && file_exists($this->fileinfo['filePath'])) {
        $this->readConfigurationFile();
      }
      else {
        $this->writeConfigurationFile();
      }
    }

    /** Read and retrieve the contents of the PHP Configuration File
     * 
     * @return ConfigurationFile|false Returns the *Contents* of the PHP Configuration File on success, represented as a `ConfigurationFile` object. 
     * Returns **false** if the contents could not be retrieved.
     * @throws \ParseError if the PHP Configuration File doesn't return a valid PHP value.
     */
    public function readConfigurationFile () {
      if (file_exists($this->fileinfo['filePath'])) {
        /** @var ConfigurationFile */
        $file_contents = include($this->fileinfo['filePath']);
  
        if ($file_contents !== false && $file_contents !== null) {
          if ($file_contents === 1 || $file_contents === null) {
            throw new \ParseError("\"{$this->fileinfo['fileName']}\" does not return a PHP value.");
          }
          else if (!($file_contents instanceof ConfigurationFile) || !method_exists($file_contents, 'getConfigurationProperties')) {
            throw new \Error("\"{$this->fileinfo['fileName']}\" is using an out-of-date Configuration. The Configuration File needs to be manually removed and re-created.");
          }
    
          $file_version = $file_contents->getConfigurationProperties()['version'];
          $current_version = ConfigurationFile::CONFIGURATION_FILE_VERSION;
    
          if ($file_version !== $current_version) {
            $updated_file = $file_contents->regenerateConfigurationFile();
    
            if ($updated_file) {
              $this->contents = $updated_file;
              $this->writeConfigurationFile();
              $file_contents = $updated_file;
            }
            else {
              if ($file_version < $current_version) {
                throw new \Error("\"{$this->fileinfo['fileName']}\" is using an out-of-date Configuration Version: \"{$file_version}\" and could not be automatically regenerated. 
                The Configuration File will need to be manually regenerated.");
              }
              else {
                throw new \Error("\"{$this->fileinfo['fileName']}\" is using an a newer Configuration Version: \"{$file_version}\" and could not be automatically regenerated. 
                The Configuration File will need to be manually regenerated.");
              }
            }
          }
    
          $this->contents = $file_contents;
          return $file_contents;
        }
      }

      return false;
    }
    /** Write the *Configuration File Contents* to the *Configuration File*.
     * 
     * @return int On success, returns an `int` representing the *Number of Bytes* written to the Configuration File.
     * @throws RuntimeException if the file contents could not be written to a file.
     */
    public function writeConfigurationFile (): int {
      $file_size = 0;
      $file_contents = (function () {
        $comment = (function () {
          $comment = $this->getConfigurationProperties()['comment'];

          if (isset($comment)) {
            $comment = Strings\preg_replace($comment, "/([\r\n]+)\ *([^\r\n]+?)/", "$1 * $2");

            return <<<EOT
            /**
             * {$comment}
             **/

            EOT;
          }

          return "";
        })();
        $contents = var_export($this->contents, true);

        return <<<EOT
        <?php
        {$comment}
        return {$contents}
        ?>
        EOT;
      })();
      $temp_filename = (function () use ($file_contents, &$file_size) {
        $temp_filename = tempnam(\ShiftCodesTK\Paths\GENERAL_PATHS['temp'], $this->fileinfo['fileName']);
        
        if ($temp_filename !== false) {
          $file_size = file_put_contents($temp_filename, $file_contents);
  
          if ($file_size !== false) {
            return $temp_filename;
          }
        }

        throw new \RuntimeException("\"{$this->fileinfo['fileName']}\" could not be written to a temporary file.");
      })();

      $new_file = rename($temp_filename, "{$this->fileinfo['dirPath']}/{$this->fileinfo['fileName']}");

      if (!$new_file) {
        throw new \RuntimeException("The \"{$this->fileinfo['fileName']}\" could not be written to \"{$this->fileinfo['dirPath']}\".");
      }

      return $file_size;
    }
    /** Get the `ConfigurationFile` object used by the `ConfigurationManager`.
     * 
     * @return ConfigurationFile|null Returns the `ConfigurationFile` used by the `ConfigurationManager`, or **null** if one has not been set.
     */
    public function &getConfigurationFile () {
      return $this->contents;
    }
    /** Get the last time the Configuration File was *Modified*.
     * 
     * @return int Returns a *Unix Timestamp* representing the *Last Modification Time* of the Configuration File.
     * @throws \RuntimeException if the *Last Modification Time* could not be retrieved.
     */
    public function getConfigurationFileModificationTime (): int {
      $filetime = filemtime($this->fileinfo['filePath']);

      if ($filetime === false) {
        throw new \RuntimeException("The Last Modification Time for the Configuration File could not be retrieved.");
      }

      return $filetime;
    }
    /** Regenerate the *Configuration File* using the latest `ConfigurationFile::CONFIGURATION_FILE_VERSION`.
     * 
     * @return bool Returns **true** on success and **false** on failure. 
     * @throws \RuntimeException in the following cases:
     * 
     * | Error Code | Description |
     * | --- | --- |
     * | 1 | The *Configuration File* could not be regenerated. |
     * | 2 | The *Configuration Manager Contents* could not be updated to the *Regenerated Configuration File*. | 
     * | 3 | The *Regenerated Configuration File* could not be written. |
     */
    public function regenerateConfigurationFile (): bool {
      $regenerated_file = (function () {
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
        $regenerated_file = $this->contents
                                 ->regenerateConfigurationFile();
  
        if ($regenerated_file) {
          $new_contents = $this->changeConfigurationContents($regenerated_file);
  
          if ($new_contents) {
            $new_file = $this->writeConfigurationFile();
  
            if ($new_file > 0) {
              return true;
            }
            else {
              throw new \RuntimeException("The Regenerated Configuration File could not be written.", 2);
            }
          }
          else {
            throw new \RuntimeException("The Configuration Manager Contents could not be updated to the Regenerated Configuration File.", 1);
          }
        }
  
        return false;
      })();

      if ($regenerated_file) {
        $new_contents = $this->changeConfigurationContents($regenerated_file);

        if ($new_contents) {
          $new_file = $this->writeConfigurationFile();

          if ($new_file > 0) {
            return true;
          }
          else {
            throw new \RuntimeException("The Regenerated Configuration File could not be written.", 3);
          }
        }
        else {
          throw new \RuntimeException("The Configuration Manager Contents could not be updated to the Regenerated Configuration File.", 2);
        }
      }
      else {
        throw new \RuntimeException("The Configuration File could not be regenerated.", 1);
      }
    }

    /** Get the *Configuration Properties* of the Configuration File.
     * 
     * @return array Returns an `Associative Array` representing the *Configuration Properties* of the Configuration File:
     * 
     * | Property | Type | Description |
     * | --- | --- | --- |
     * | *fileinfo* | `array` | Information about the *File Path* and *File Name* of the Configuration File. |
     * | *alias* | `string\|null` | If applicable, the *Alias* of the Configuration File. |
     * | *type* | `string` | A `CONFIGURATION_TYPE_*` interface constant representing the *Configuration Type* of the PHP Configuration File. |
     * | *version* | `string` | The *PHP Configuration File Version* of the Configuration File. |
     * | *comment* | `string\|null` | If applicable, the *Alias* of the Configuration File. |
     */
    public function getConfigurationProperties (): array {
      $properties = [
        'configuration_manager' => $this->getLocalConfigurationProperties(self::DEFAULT_MANAGER_OPTIONS),
        'configuration_file'    => $this->contents
                                        ->getConfigurationProperties(),
      ];

      return array_merge(...array_values($properties));
    }
    /** Update the *Configuration Properties* of the Configuration File
     * 
     * @param array $properties An `Associative Array` of Configuration Properties to update.
     * - Providing a value of **null** for a Configuration Property will reset its value to the **Default Value**. 
     * 
     * | Property | Type | Default Value | Description |
     * | --- | --- | --- | --- |
     * | *fileinfo* | `array` | `Array` | Information about the *File Path* and *File Name* of the Configuration File. Permitted keys include `dirPath`, `fileName`, and `filePath`. |
     * | *alias* | `string\|null` | `null` | If applicable, the *Alias* of the Configuration File. |
     * | *type* | `string` | `::CONFIGURATION_TYPE_ARRAY` | A `CONFIGURATION_TYPE_*` interface constant representing the *Configuration Type* of the PHP Configuration File. **Note**: Changing this value will **remove** the current *File Contents*, as they will no longer be compatible. |
     * | *version* | `string` | `::CONFIGURATION_FILE_VERSION` | The *PHP Configuration File Version* of the Configuration File. |
     * | *comment* | `string\|null` | `null` | If applicable, the *Alias* of the Configuration File. |
     * @return true Returns **true** on success.
     * @throws \UnexpectedValueException if the provided *Configuration File Type* or *Configuration File Version* is invalid.
     * @throws \RuntimeException if a *Configuration Property* was not updated successfully.
     */
    public function changeConfigurationProperties (array $properties): bool {
      // `ConfigurationManager` Properties
      (function () use ($properties) {
        $property_setters = [
          'fileinfo' => function ($fileinfo) {
            $updated_fileinfo = [];
  
            foreach ($this->fileinfo as $property => $current_value) {
              if (\array_key_exists($property, $fileinfo)) {
                $provided_value = $fileinfo[$property];
  
                if (Validations\check_type($provided_value, 'string|null')) {
                  $provided_type = gettype($provided_value);
  
                  throw new \UnexpectedValueException("The value of \"Fileinfo {$property}\" must be a String or NULL: {$provided_type} provided.");
                }
  
                $updated_fileinfo[$property] = $fileinfo[$property];
              }
            }

            $this->fileinfo = $updated_fileinfo;
          }
        ];
        $property_list = [ 'fileinfo' ];
        
        $this->changeLocalConfigurationProperties($property_setters, $property_list, $properties);
      })();

      // `ConfigurationFile` Properties
      $this->contents
           ->changeConfigurationProperties($properties);

      return true;
    }
    /** Get the *File Contents* of the Configuration File
     * 
     * @return array|object|ConfigurationProperty Returns the *File Contents* of the Configuration File.
     */
    public function &getConfigurationContents () {
      return $this->contents
                  ->getConfigurationContents();
    }
    /** Change the *File Contents* of a Configuration File
     * 
     * @param mixed $contents The *New Contents* of the Configuration File. 
     * - Must match the current *Configuration File Type*.
     * - If omitted, _all of the current contents will be **removed**_.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function changeConfigurationContents ($contents = null): bool {
      $updated_contents = $this->contents
                               ->changeConfigurationContents(...\func_get_args());

      if ($updated_contents) {
        return $this->writeConfigurationFile() > 0;
      }

      return false;
    }
    
    /** List all of the stored *Configuration Values*
     * 
     * @param bool $flush_index Indicates if the *Configuration Value Index* should be flushed and reconstructed. 
     * @return array Returns an `Array` representing all of the stored *Configuration Values*.
     */
    public function listConfigurationValues (bool $flush_index = false): array {
      return $this->contents
                  ->listConfigurationValues(...\func_get_args());
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
     */
    public function getConfigurationValue (string $property = null, string $secret_key = null) {
      return $this->contents
                  ->getConfigurationValue(...\func_get_args());
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
     */
    public function addConfigurationValue (string $property_name, $property_value, string $secret_key = null): bool {
      $result = $this->contents
                     ->addConfigurationValue(...\func_get_args());

      if ($result) {
        return $this->writeConfigurationFile() > 0;
      }

      return false;
    }
    /** Remove a *Configuration Value* from the Configuration File.
     * 
     * Requires the *Configuration File Type* to be `{@see ::CONFIGURATION_TYPE_ARRAY}` or `{@see ::CONFIGURATION_TYPE_OBJECT}`.
     * 
     * @param string $property_name The *Property Name* of the Configuration Value being removed.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function removeConfigurationValue (string $property_name): bool {
      $result = $this->contents
                     ->removeConfigurationValue(...\func_get_args());

      if ($result) {
        return $this->writeConfigurationFile() > 0;
      }

      return false;
    }
    /** Update the *Configuration Property* of the Configuration File
     * 
     * Requires the *Configuration File Type* to be `{@see ::CONFIGURATION_TYPE_PROPERTY}`
     * 
     * @param mixed $property_value The new value of the property.
     * - If a `$secret_key` is provided, this value **must** be a `string`, `array`, or `object`.
     * @param string|null $secret_key If provided, a *Secret Key* used to *Encrypt* the `$property_value` when storing it.
     * - If you need a Secret Key, you can use `{@see ShiftCodesTK\Auth\Crypto\SecretKeyCrypto::generateSecretKey()}` to generate one.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function updateConfigurationValue ($property_value, string $secret_key = null): bool {
      $result = $this->contents
                     ->updateConfigurationValue(...\func_get_args());

      if ($result) {
        return $this->writeConfigurationFile() > 0;
      }

      return false;
    }
  }
?>