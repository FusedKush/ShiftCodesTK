<?php
  namespace ShiftCodesTK\PHPConfigurationFiles\Interfaces;

  /** The `ConfigurationConstantsInterface` is responsible for the common constants used in the *Configuration Interfaces*.  */
  interface ConstantsInterface {
    /** @var array The *Default Configuration Options* of the object. */
    public const DEFAULT_CONFIGURATION_OPTIONS = [
      'alias'    => null,
      'type'     => self::CONFIGURATION_TYPE_ARRAY,
      'version'  => null,
      'comment'  => null,
      'contents' => null
    ];

    /** @var string The *Delimiter Character* used for *Configuration Properties*. */
    public const CONFIGURATION_PROPERTY_DELIMITER = '.';

    /** @var string Indicates that the Configuration File should hold an `array`. */
    public const CONFIGURATION_TYPE_ARRAY = 'array';
    /** @var string Indicates that the Configuration File should hold an `object`. */
    public const CONFIGURATION_TYPE_OBJECT = 'object';
    /** @var string Indicates that the Configuration File should hold a `ConfigurationProperty` object. */
    public const CONFIGURATION_TYPE_PROPERTY = 'ConfigurationProperty';
    /** @var array A list of the available `CONFIGURATION_TYPE_*` constants. */
    public const CONFIGURATION_TYPES_LIST = [
      self::CONFIGURATION_TYPE_ARRAY,
      self::CONFIGURATION_TYPE_OBJECT,
      self::CONFIGURATION_TYPE_PROPERTY
    ];
  }
?>