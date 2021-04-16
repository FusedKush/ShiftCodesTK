<?php
  namespace ShiftCodesTK\PHPConfigurationFiles\Interfaces;

  /** The `ConfigurationPropertiesInterface` is responsible for managing PHP Configuration File *Configuration Properties*. */
  interface ConfigurationPropertiesInterface extends ConstantsInterface {
    public function getConfigurationProperties (
    ): array;

    public function changeConfigurationProperties (
      array $properties
    ): bool;

    public function &getConfigurationContents (
    );

    public function changeConfigurationContents (
      $contents = null
    ): bool;

    public function getConfigurationValueProperties (
      string $property_name = null
    ): array;
  }
?>