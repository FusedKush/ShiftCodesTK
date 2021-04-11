<?php
  namespace ShiftCodesTK;
  use ShiftCodesTK\Paths,
      ShiftCodesTK\Strings,
      ShiftCodesTK\PHPConfigurationFiles;

  /** 
   * The `Config` class is responsible for managing *Project Configuration Variables*.
   **/
  class Config implements 
    PHPConfigurationFiles\Interfaces\ConstantsInterface,
    PHPConfigurationFiles\Interfaces\StaticConfigurationValuesInterface {
    /** @var string The *Path* to the directory where Configuration Files are stored. */
    public const CONFIG_FILES_PATH = Paths\GENERAL_PATHS['resources'] . '/config';
    /** @var string The *File Extension* of the Configuration Files. */
    public const CONFIG_FILES_EXT = '.php';

    /** @var Config|null The current instance of the `Config` class. */
    protected static $config_instance = null;

    /** @var PHPConfigurationFiles\ConfigurationManager|null The `ConfigurationManager` responsible for the *Config File Index*. */
    protected $configFileIndex = null;
    /** @var PHPConfigurationFiles\ConfigurationManager[] An `array` of `ConfigurationManager` objects representing the individual *Config Files*. */
    protected $configFiles = [];

    /** Get an `Associative Array` representing the individuals pieces of a delimitter-separated *Config Property Name*
     * 
     * @param string $property The *Config Property Name* being evaluated. 
     * Pieces should be separated using the `{@see ::CONFIGURATION_PROPERTY_DELIMITER}`.
     * @param bool $throw_errors Indicates if an `Error` should be thrown if a `$property` namespace is not found, instead of filling it with **null**.
     * @return array Returns an `array` representing the pieces of the `$property`. If `$throw_errors` is **false**, missing pieces will return **null**.
     * @throws \Error if `$property` does not specify the proper namespaces and `$throw_errors` is **true**.
     * 
     * | Key | Piece | Description |
     * | --- | --- | --- |
     * | *file* | `[foo].bar.baz.blah` | The *Config File* the `config_property` belongs to. |
     * | *property* | `foo.[bar.baz.blah]` | The *Full Config Property* being evaluated. |
     * | *base_property* | `foo.[bar].baz.blah` | The *Base Config Property* being evaluated. |
     * | *property_children* | `foo.bar.[baz.blah]` | Any additional *Children* of the `config_property`. |
     */
    protected static function getConfigurationPropertyNamePieces (string $property, bool $throw_errors = false) {
      $piece_names = [
        'file',
        'property',
        'base_property',
        'property_children'
      ];
      $pieces = (function () use ($property, $piece_names) {
        $pieces = explode(self::CONFIGURATION_PROPERTY_DELIMITER, $property, 2);

        if (isset($pieces[1])) {
          $pieces = array_merge($pieces, explode(self::CONFIGURATION_PROPERTY_DELIMITER, $pieces[1], 2));
        }
        else {
          $pieces = array_pad($pieces, count($piece_names), null);
        }

        return $pieces;
      })();
      $pieces = array_pad($pieces, count($piece_names), null);

      if ($throw_errors) {
        $expected_count = count($piece_names) - 1;
        $provided_count = count($pieces);
  
        if ($provided_count < $expected_count) {
          throw new \Error("Only {$provided_count} property namespaces were found, at least {$expected_count} expected.");
        }
      }
      
      return array_combine($piece_names, $pieces);
    }

    /** Get the *Current Instance* of the `Config` Manager.
     * 
     * @return Config Returns the Current Instance of the `Config` class.
     */
    public static function &getInstance () {
      if (!isset(self::$config_instance)) {
        self::$config_instance = new Config(
          new PHPConfigurationFiles\ConfigurationManager(
            Paths\GENERAL_PATHS['cache'] . '/config-index.php', 
            new PHPConfigurationFiles\ConfigurationFile([
              'type'     => self::CONFIGURATION_TYPE_PROPERTY,
              'comment'  => "Represents an index of registered *Config Files*.",
              'contents' => new PHPConfigurationFiles\ConfigurationProperty(
                []
              )
            ])
          )
        );

        $instance = &self::$config_instance;

        /** @var \ShiftCodesTK\PHPConfigurationFiles\ConfigurationProperty */
        $config_file_index = $instance->configFileIndex
                                      ->getConfigurationValue();
        /** @var bool Indicates if the *Config Files* have been re-indexed already. Used to prevent an infinite loop while indexing. */
        $has_indexed_config_files = false;

        $index_config_files = function () use (&$config_file_index, &$instance) {
          $instance->indexConfigFiles();
          $config_file_index = $instance->configFileIndex;
        };
        $get_config_files = function () use (&$get_config_files, &$has_indexed_config_files, &$config_file_index, &$index_config_files) {
          $config_files = [];
          
          $reindex_config_files = function () use (&$get_config_files, &$has_indexed_config_files, &$index_config_files) {
            if (!$has_indexed_config_files) {
              $index_config_files();
              $has_indexed_config_files = true;
    
              return $get_config_files();
            }
            else {
              throw new \RuntimeException("Config Files could not be indexed.");
            }
          };

          foreach ($config_file_index as $file_alias => $file_properties) {
            $file_path = $file_properties['fileinfo']['filePath'];

            if (!\file_exists($file_path)) {
              return $reindex_config_files();
            }

            $file_contents = new PHPConfigurationFiles\ConfigurationManager(
              $file_path,
              new PHPConfigurationFiles\ConfigurationFile($file_properties)
            );

            if ($file_contents !== false && $file_contents !== 1) {
              $config_files[$file_alias] = $file_contents;
            }
            else {
              return $reindex_config_files();
            }
          } 

          return $config_files;
        };

        if (!$config_file_index) {
          $index_config_files();
        }

        $instance->configFiles = $get_config_files();
      }

      return self::$config_instance;
    }

    /** Create a new *Index* for the current Config Files.
     * 
     * @return bool Returns **true** on success and **false** on failure.
     */
    protected static function indexConfigFiles (): bool {
      /** @var $this */
      $instance = &self::$config_instance;
      $directory_contents = scandir(self::CONFIG_FILES_PATH);  
      $file_extension = Strings\escape_reg(self::CONFIG_FILES_EXT, '/');

      $instance->configFileIndex
               ->updateConfigurationValue(null, []);

      foreach ($directory_contents as $index => $filename) {
        if (Strings\preg_test($filename, "/{$file_extension}$/")) {
          /** @var PHPConfigurationFiles\ConfigurationFile */
          $file_contents = @include(self::CONFIG_FILES_PATH . "/$filename");

          if ($file_contents !== false && $file_contents !== 1) {
            $file_properties = (function () use ($file_contents, $index) {
              $file_properties = $file_contents->getConfigurationProperties();

              if (!isset($file_properties['alias'])) {
                $file_properties['alias'] = $index;
              }

              unset($file_properties['version']);

              return $file_properties;
            })();
            $manager = new PHPConfigurationFiles\ConfigurationManager(
              self::CONFIG_FILES_PATH . "/$filename",
              new PHPConfigurationFiles\ConfigurationFile($file_properties)
            );

            self::addConfigFileToIndex(
              $file_properties['alias'],
              $manager
            );
          }
        }
      }

      return true;
    }
    /** List the *Currently Indexed* Config Files
     * 
     * @return array Returns an `Associative Array` representing the *Currently Indexed* Config Files
     * > `string` *Config File Alias* => `string` *Config File Filename*
     */
    protected static function listIndexedConfigFiles () {
      return self::getInstance()
                 ->configFileIndex
                 ->getConfigurationValue();
    }
    /** Add a *Config File* to the *Config File Index*
     * 
     * @param string $alias The *Config File Alias* of the Config File. Used to refer to and access the Config File and its properties.
     * @param PHPConfigurationFiles\ConfigurationManager $manager The `ConfigurationManager` responsible for the Config File. 
     * A given `$manager` can have multiple *Aliases* if desired.
     * @return true Returns **true** on success.
     * @throws \Error if `$alias` has already been registered.
     */
    protected static function addConfigFileToIndex (string $alias, PHPConfigurationFiles\ConfigurationManager $manager) {
      $instance = &self::getInstance();
      $config_file_index = &$instance->configFileIndex;
      $index = $config_file_index->getConfigurationValue();
      $indexed_properties = (function () use ($manager) {
        $indexed_properties = $manager->getConfigurationProperties();

        unset($indexed_properties['alias']);
        unset($indexed_properties['version']);

        return $indexed_properties;
      })();
      
      if (array_key_exists($alias, $index)) {
        throw new \Error("Config File Alias \"{$alias}\" has already been registered.");
      }

      $index[$alias] = $indexed_properties;
      $config_file_index->updateConfigurationValue(null, $index);

      return true;
    }
    /** Remove a *Config File* from the *Config File Index*
     * 
     * @param string $alias The *Config File Alias* of the Config File being removed.
     * @return bool Returns **true** on success and **false** if the Config File `$alias` was not found.
     */
    protected static function removeConfigFileFromIndex (string $alias) {
      $instance = &self::getInstance();
      $config_file_index = &$instance->configFileIndex;
      $index = $config_file_index->getConfigurationValue();
      
      if (!array_key_exists($alias, $index)) {
        return false;
      }

      unset($index[$alias]);
      $config_file_index->updateConfigurationValue(null, $index);

      return true;
    }

    /** List all of the currently stored *Config Files*
     * 
     * @return array Returns an `Associative Array` representing the currently stored *Config Files*.
     * > `string` *Config File Alias* => `PHPConfigurationManager` *Config File Manager*
     */
    public static function listConfigFiles () {
      return self::getInstance()
                 ->configFiles;
    }
    /** Get the *Config File* represented by a `$property` string.
     * 
     * @param string $property The *Config Property `string`* being evaluated.
     * @param bool $throw_error Indicates if an `Error` should be thrown if the `$property` doesn't specify a valid *Config File*.
     * @return PHPConfigurationFiles\ConfigurationManager|false Returns the `ConfigurationManager` object responsible for the `$property` on success. 
     * If `$property` does not refer to a valid *Config File*, returns **false**.
     * @throws \Error if `$property` does not specify a valid *Config File* and `$throw_error` is set to **true**. 
     */
    public static function getConfigFile (string $property, bool $throw_error = false) {
      $instance = &self::getInstance();
      $config_files = $instance->configFiles;
      $alias = self::getConfigurationPropertyNamePieces($property)['file'];

      if (\array_key_exists($alias, $config_files)) {
        return $config_files[$alias];
      }
      else {
        if ($throw_error) {
          throw new \Error("The Config File with an alias of \"{$alias}\" was not found.");
        }
        else {
          return false;
        }
      }
    }
    /** Add a new *Config File* to the *Config*.
     * 
     * @param array $options The options used to configure the Config File.
     * 
     * | Option | Type | Default Value | Description |
     * | --- | --- | --- | --- |
     * | *filename* | `string` | | The *Name* of the Config File. The *File Extension* must match the `::CONFIG_FILES_EXT`. |
     * | *alias* | `string|int` | `Int` | An optional `string` representing the *Alias* used to refer to the *Config File's Properties*. If omitted, the `$config_file` will be assigned a *Numeric Alias*. |
     * | *type* | `string` | `::CONFIGURATION_TYPE_ARRAY` | A `CONFIGURATION_TYPE_*` class constant representing the *Configuration File Type* of the Config File. |
     * | *comment* | `string|null` | 	If applicable, the PHP Comment of the Config File. |
     * *Options without a **Default Value** are Required and cannot be omitted.*
     * @return string|int|false Returns the *Alias* for the stored `$config_file` on success, or **false** on failure.
     * @throws \Error in the following cases:
     * 
     * | Error Code | Description |
     * | --- | --- |
     * | `1` | No *filename* was provided in `$options`.
     * | `2` | The *alias* of `$options` has already been registered. |
     * @throws \UnexpectedValueException if *filename* of `$options` uses a *File Extension* other than `::CONFIG_FILES_EXT`.\
     */
    public static function addConfigFile (array $options = []) {
      $instance = &self::getInstance();
      $file_options = (function () use ($options, &$instance) {
        $default_options = [
          'alias'   => count($instance->configFiles),
          'type'    => PHPConfigurationFiles\ConfigurationFile::CONFIGURATION_TYPE_ARRAY,
          'comment' => null
        ];

        return \array_replace($default_options, $options);
      })();

      $required_ext = Strings\escape_reg(self::CONFIG_FILES_EXT, '/');

      if (!\array_key_exists('filename', $file_options)) {
        throw new \Error("A Config File Filename must be provided.");
      }
      else if (!Validations\check_pattern($file_options['filename'], "/{$required_ext}$/")) {
        throw new \UnexpectedValueException("Config File \"{$file_options['filename']}\" does not use the Config File Extension: \"{$required_ext}\".");
      }
      else if (self::getConfigFile($file_options['alias']) !== false) {
        throw new \Error("A Config File has already been registered with the alias \"{$file_options['alias']}\".");
      }

      $instance = &self::getInstance();
      $config_file_manager = new PHPConfigurationFiles\ConfigurationManager(
        self::CONFIG_FILES_PATH . "/{$file_options['filename']}",
        new PHPConfigurationFiles\ConfigurationFile($file_options)
      );

      $instance->configFiles[$file_options['alias']] = $config_file_manager;
      $instance->addConfigFileToIndex($file_options['alias'], $config_file_manager);
      
      return self::getConfigFile($file_options['alias']) !== false
             ? $file_options['alias']
             : false;
    }
    /** Remove a *Config File* from the *Config*.
     * 
     * All *Config Properties* belonging to the Config File will be removed and made unavailable for querying.
     * 
     * @param string $alias The *Alias* of the *Config File* being removed. This is returned by `addConfigFile()` and can be found using `listConfigFiles()`.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public static function removeConfigFile (string $alias) {
      $config_file = self::getConfigFile($alias);
      $instance = &self::getInstance();

      if (!isset($config_file)) {
        return false;
      }

      $config_filepath = $config_file->getConfigurationProperties()['fileinfo']['filePath'];
      
      if (file_exists($config_filepath)) {
        $config_file_removed = unlink($config_filepath);

        if (!$config_file_removed) {
          return false;
        }
      }

      unset($instance->configFiles[$alias]);
      $instance->removeConfigFileFromIndex($alias);
      
      return self::getConfigFile($alias) === false;
    }
    
    /** List all of the stored *Configuration Values*
     * 
     * @param bool $flush_index Indicates if the *Configuration Value Indexes* should be flushed and reconstructed. 
     * @return array Returns an `Array` representing all of the stored *Configuration Values*.
     */
    public static function listConfigurationValues (bool $flush_index = false): array {
      $instance = &self::getInstance();
      $values = [];

      foreach ($instance->configFiles as $alias => $manager) {
        foreach ($manager->listConfigurationValues(...\func_get_args()) as $property_name) {
          $values[] = $alias . self::CONFIGURATION_PROPERTY_DELIMITER . $property_name;
        }
      }

      return $values;
    }
    /** Check if a *Configuration Value* has been defined
     * 
     * @param string $property The *Property Name* of the Configuration Value to check for.
     * @return bool Returns **true** if `$property` has been defined, or **false** if it has not. 
     */
    public static function configurationValueExists (string $property): bool {
      $config_file = self::getConfigFile($property);

      if ($config_file) {
        $file_property_name = self::getConfigurationPropertyNamePieces($property)['property'];

        return $config_file->configurationValueExists($file_property_name);
      }

      return false;
    }
    /** Get a *Configuration Value*
     * 
     * @param string|null $property The *Property Name* of the Configuration Value to retrieve.
     * 
     * You can use the `::CONFIGURATION_PROPERTY_DELIMITER` to signify the *Config File*, followed by *`Array` Keys* or *Public `Object` Properties* to access.
     * - For example, `test.foo.bar.baz` can refer to the following `test: foo[bar]->baz`.
     * 
     * If omitted, the *Full Config Contents* will be returned.
     * @param string $secret_key If the `$property` is *Encrypted*, this is the *Secret Key* needed to Decrypt the property.
     * @return mixed Returns the *Configuration Value* represented by `$property` on success. 
     */
    public static function getConfigurationValue (string $property = null, string $secret_key = null) {
      if (isset($property)) {
        $config_file = self::getConfigFile($property, true);
  
        if ($config_file) {
          $file_property_name = self::getConfigurationPropertyNamePieces($property)['property'];
  
          return $config_file->getConfigurationValue($file_property_name, $secret_key);
        }
  
        return false;
      }
      else {
        return self::getInstance()
                   ->configFiles;
      }
    }
    /** Add a *Configuration Value* to a Config File
     * 
     * Requires the *Configuration File Type* of the Config File to be `{@see ::CONFIGURATION_TYPE_ARRAY}` or `{@see ::CONFIGURATION_TYPE_OBJECT}`.
     * 
     * @param string $property_name The *Property Name* of the Configuration Value. 
     * - The *Config File* must have already been created. See `{@see ::addConfigFile()}` if you need to create a new Config File.
     * - The property cannot already exist within the *Config File*.
     * @param mixed $property_value The *Property Value* of the Configuration Value. 
     * If the `$secret_key` is provided, this **must** be a `string`, `array`, or `object`.
     * @param string|null $secret_key If provided, a *Secret Key* used to *Encrypt* the `$property_value` when storing it.
     * - If you need a Secret Key, you can use `{@see ShiftCodesTK\Auth\Crypto\SecretKeyCrypto::generateSecretKey()}` to generate one.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public static function addConfigurationValue (string $property_name, $property_value, string $secret_key = null): bool {
      $config_file = self::getConfigFile($property_name, true);
      $file_property_name = self::getConfigurationPropertyNamePieces($property_name)['property'];

      return $config_file->addConfigurationValue($file_property_name, $property_value, $secret_key);
    }
    /** Remove a *Configuration Value* from a Config File.
     * 
     * Requires the *Configuration File Type* of the Config File to be `{@see ::CONFIGURATION_TYPE_ARRAY}` or `{@see ::CONFIGURATION_TYPE_OBJECT}`.
     * 
     * @param string $property_name The *Property Name* of the Configuration Value being removed.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public static function removeConfigurationValue (string $property_name): bool {
      $config_file = self::getConfigFile($property_name, true);
      $file_property_name = self::getConfigurationPropertyNamePieces($property_name)['property'];

      return $config_file->removeConfigurationValue($file_property_name);
    }
    /** Update the *Configuration Property* of a Config File
     * 
     * @param string|null $property_name The *Property Name* of the Configuration Value being updated. 
     * - If the *Configuration File Type* is `{@see ::CONFIGURATION_TYPE_PROPERTY}`, this argument is ignored, and can be omitted.
     * - If the *Configuration File Type* is `{@see ::CONFIGURATION_TYPE_ARRAY}` or `{@see ::CONFIGURATION_TYPE_ARRAY}`, this argument **must** be provided.
     * @param mixed $property_value The new value of the property.
     * - If a `$secret_key` is provided, this value **must** be a `string`, `array`, or `object`.
     * @param string|null $secret_key If provided, a *Secret Key* used to *Encrypt* the `$property_value` when storing it.
     * - If you need a Secret Key, you can use `{@see ShiftCodesTK\Auth\Crypto\SecretKeyCrypto::generateSecretKey()}` to generate one.
     * @return bool Returns **true** on success and **false** on failure.
     * @throws \ArugmentCountError if the `$config_file` is not provided.
     */
    public static function updateConfigurationValue (
      string $property_name = null,
      $property_value, 
      string $secret_key = null
    ): bool {
      $property_name_pieces = self::getConfigurationPropertyNamePieces($property_name);

      if (!isset($property_name_pieces['file'])) {
        throw new \ArgumentCountError("A Config File was not provided.");
      }
      
      $config_file_obj = self::getConfigFile($property_name_pieces['file'], true);

      return $config_file_obj->updateConfigurationValue($property_name_pieces['property'], $property_value, $secret_key);
    }

    /** Initialize the `Config` 
     * 
     * @param PHPConfigurationFiles\ConfigurationManager $index_manager The `ConfigurationManager` responsible for managing the *Config File Index*.
     **/
    protected function __construct (PHPConfigurationFiles\ConfigurationManager $index_manager) {
      $this->configFileIndex = $index_manager; 
    }
  }
?>