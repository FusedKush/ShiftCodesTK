<?php
  namespace ShiftCodesTK\PHPConfigurationFiles\Interfaces;

  /** The `ConfigurationInterface` is responsible for using *PHP Configuration Files*. */
  interface ConfigurationInterface 
    extends ConstantsInterface,
            ConfigurationPropertiesInterface,
            ConfigurationValuesInterface
  {}
?>