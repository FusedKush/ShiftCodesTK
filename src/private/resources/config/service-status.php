<?php
/**
 * A *Semver*-Compatible Version `String` representing the *Current Version* of ShiftCodesTK.
 *  
 * | Property              | Type   | Description                                                                                   |
 * | ---                   | ---    | ---                                                                                           |
 * | `service_maintenance` | `bool` | Indicates if the service is currently undergoing *Maintenance*, and should deny all requests. |
 * | `allow_dev_builds`    | `bool` | Indicates if *Development Builds* can be accessed, or should deny any requests to one.        |
 **/

return ShiftCodesTK\PHPConfigurationFiles\ConfigurationFile::__set_state(array(
   'alias' => 'service_status',
   'type' => 'array',
   'version' => '1.0.0',
   'comment' => 'A *Semver*-Compatible Version `String` representing the *Current Version* of ShiftCodesTK.
                
                | Property              | Type   | Description                                                                                   |
                | ---                   | ---    | ---                                                                                           |
                | `service_maintenance` | `bool` | Indicates if the service is currently undergoing *Maintenance*, and should deny all requests. |
                | `allow_dev_builds`    | `bool` | Indicates if *Development Builds* can be accessed, or should deny any requests to one.        |',
   'contents' => 
  array (
    'service_maintenance' => 
    ShiftCodesTK\PHPConfigurationFiles\ConfigurationProperty::__set_state(array(
       'name' => 'service_maintenance',
       'value' => false,
       'isEncrypted' => false,
       'lastModified' => '1619222971734580',
    )),
    'allow_dev_builds' => 
    ShiftCodesTK\PHPConfigurationFiles\ConfigurationProperty::__set_state(array(
       'name' => 'allow_dev_builds',
       'value' => true,
       'isEncrypted' => false,
       'lastModified' => '1619222971736156',
    )),
  ),
   'configurationValueIndex' => 
  array (
    0 => 'service_maintenance',
    1 => 'allow_dev_builds',
  ),
))
?>