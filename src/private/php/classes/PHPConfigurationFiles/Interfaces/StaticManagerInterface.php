<?php
  namespace ShiftCodesTK\PHPConfigurationFiles\Interfaces;

  /** The `StaticManagerInterface` is responsible for managing *PHP Configuration Files* in *Static* contexts. */
  interface StaticManagerInterface extends StaticConfigurationInterface {
    public static function readConfigurationFile ();
    public static function writeConfigurationFile (): int;
    public static function &getConfigurationFile ();
    public static function getConfigurationFileModificationTime (): int;
    public static function regenerateConfigurationFile ();
  }
?>