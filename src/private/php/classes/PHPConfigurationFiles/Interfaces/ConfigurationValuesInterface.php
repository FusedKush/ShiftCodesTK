<?php
  namespace ShiftCodesTK\PHPConfigurationFiles\Interfaces;

  use ShiftCodesTK\PHPConfigurationFiles;

  /** The `ConfigurationValuesInterface` is responsible for managing PHP Configuration File *Configuration Values*. */
  interface ConfigurationValuesInterface {
    public function listConfigurationValues (
      bool $flush_index = false
    ): array;

    public function configurationValueExists (
      string $property
    ): bool;

    public function getConfigurationValue (
      string $property = null, 
      string $secret_key = null
    );

    public function addConfigurationValue (
      string $property_name, 
      $property_value, 
      string $secret_key = null
    ): bool;

    public function removeConfigurationValue (
      string $property_name
    ): bool;

    public function updateConfigurationValue (
      $property_value, 
      string $secret_key = null
    ): bool;
  }
?>