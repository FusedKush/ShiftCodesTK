<?php
  namespace ShiftCodesTK\PHPConfigurationFiles\Interfaces;

  /** The `StaticConfigurationValuesInterface` is responsible for managing PHP Configuration File *Configuration Values* in *Static* contexts. */
  interface StaticConfigurationValuesInterface {
    public static function listConfigurationValues (bool $flush_index = false): array;
    public static function configurationValueExists (string $property): bool;
    public static function getConfigurationValue (string $property = null, string $secret_key = null);
    public static function addConfigurationValue (string $property_name, $property_value, string $secret_key = null): bool;
    public static function removeConfigurationValue (string $property_name): bool;
    public static function updateConfigurationValue (string $property_name = null, $property_value, string $secret_key = null): bool;
  }
?>