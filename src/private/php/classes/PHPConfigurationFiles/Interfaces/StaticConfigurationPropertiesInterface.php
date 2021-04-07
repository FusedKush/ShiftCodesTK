<?php
  namespace ShiftCodesTK\PHPConfigurationFiles\Interfaces;

  /** The `StaticConfigurationPropertiesInterface` is responsible for managing PHP Configuration File *Configuration Properties* in *Static* contexts. */
  interface StaticConfigurationPropertiesInterface extends ConstantsInterface {
    public static function getConfigurationProperties (): array;
    public static function changeConfigurationProperties (array $properties): bool;
    public static function &getConfigurationContents ();
    public static function changeConfigurationContents ($contents = null): bool;
  }
?>