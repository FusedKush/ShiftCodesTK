<?php
  namespace ShiftCodesTK\PHPConfigurationFiles\Interfaces;

  /** The `ManagerInterface` is responsible for managing *PHP Configuration Files*. */
  interface ManagerInterface extends ConfigurationInterface {
    public function readConfigurationFile ();
    public function writeConfigurationFile (): int;
    public function &getConfigurationFile ();
    public function getConfigurationFileModificationTime (): int;
    public function regenerateConfigurationFile ();
  }
?>