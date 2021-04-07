<?php 
  namespace ShiftCodesTK\PHPConfigurationFiles;

  /** Represents various Helper Methods used for working with the local components of the `ConfigurationInterface`. */
  trait LocalInterfaceHelpers {
    /** Get the *Local Configuration Properties* of the Configuration File.
     * 
     * @param array $property_list An `Associative Array` representing the *Local Configuration Properties* to be retrieved.
     * > `string` *Configuration Property Name* => `mixed` *Default Configuration Property Value*.
     * @return array Returns an `Associative Array` representing the *Local Configuration Properties* of the Configuration File.
     */
    private function getLocalConfigurationProperties (array $property_list): array {
      $properties = [];
      
      foreach ($property_list as $property => $default_value) {
        $properties[$property] = $this->$property ?? $default_value;
      }
      
      return $properties;
    }
    /** Update the *Local Configuration Properties* of the Configuration File
     * 
     * @param array $property_setters An `Associative Array` representing any *Property Setter Functions* to be invoked.
     * - The *Key* represents the *Property Name* of the *Local Configuration Property* that will utilize the Setter.
     * - The *Value* is the *Setter `Function`* that will be invoked when setting the *Local Configuration Property* represented by the **Key**.
     * - - A single argument is provided, the *Local Configuration Property Value* that is being set.
     * - - The function should add or update the *Property Value*. If the provided value is *Invalid*, an `Error` or `Exception` should be thrown.
     * @param array $property_list A list of *Local Configuration Property Names* that can be set.
     * @param array $properties An `Associative Array` of Configuration Properties to update.
     * - Providing a value of **null** for a Configuration Property will reset its value to the **Default Value**. 
     * - Configuration Properties not found in the `$property_list` are silently ignored.
     * @return true Returns **true** on success.
     * @throws \RuntimeException if a *Configuration Property* was not updated successfully.
     */
    private function changeLocalConfigurationProperties (array $property_setters, array $property_list, array $properties): bool {
      foreach ($property_list as $property => $default_value) {
        $provided_value = $properties[$property] ?? $default_value;

        if (\array_key_exists($property, $property_setters)) {
          $property_setters[$property]($provided_value);
        }
        else if (\array_key_exists($property, $properties)) {
          $original_value = $this->$property;

          if ($original_value !== $provided_value) {
            $this->$property = $provided_value;
    
            if ($this->$property === $original_value) {
              throw new \RuntimeException("Configuration Property \"{$property}\" was not updated successfully.");
            }
          }
        }
      }
      
      return true;
    }
  }
?>