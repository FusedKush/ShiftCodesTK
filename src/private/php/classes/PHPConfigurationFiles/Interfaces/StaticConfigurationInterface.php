<?php
  namespace ShiftCodesTK\PHPConfigurationFiles\Interfaces;

  /** The `StaticConfigurationInterface` is responsible for using *PHP Configuration Files* in *Static* contexts. */
  interface StaticConfigurationInterface extends 
    ConstantsInterface,
    StaticConfigurationPropertiesInterface,
    StaticConfigurationValuesInterface {}
?>