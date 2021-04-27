<?php
  /**
   * Helper functions and polyfills for working with PHP `arrays`.
   */

  use ShiftCodesTK\Strings,
      ShiftCodesTK\Integers;

  /** @var int Indicates that any missing *Parent `arrays`* are automatically created if they do not exist. */
  const ARRAY_NESTED_CREATE_MISSING_PARENTS = 1;
  /** @var int Indicates that the *Nested Value* should automatically be created if it does not exist. */
  const ARRAY_NESTED_CREATE_MISSING_VALUE = 2;
  /** @var int Indicates that *Errors* should be thrown instead of returning **null**. */
  const ARRAY_NESTED_THROW_ERRORS = 4;

  /**
   * Determine if an array has *String*, *Non-Sequential*, or *Non-Zero-Indexed Keys*.
   * 
   * @param array $array The array to check.
   * @return boolean Returns **true** if `$array` is considered an `Associative Array`. Otherwise, returns **false**.
   */
  function is_array_associative (array $array) {
    if (is_array($array)) {
      $arrayCount = count($array);

      if ($arrayCount > 0) {
        if (array_keys($array) !== range(0, $arrayCount - 1)) {
          return true;
        }
      }
    }

    return false;
  }
  /**
   * Get the first value from an array
   * 
   * @param array $array The target array. Can be either an `Indexed Array` or an `Associative Array`
   * @return mixed On success, returns the first value from the `$array`. If `$array` is empty, returns **null**.
   */
  function array_value_first (array $array) {
    foreach ($array as $value) {
      return $value;
    }

    return null;
  }
  /**
   * Get the last value from an array
   * 
   * @param array $array The target array. Can be either an `Indexed Array` or an `Associative Array`
   * @return mixed On success, returns the last value from the `$array`. If `$array` is empty, returns **null**.
   */
  function array_value_last (array $array) {
    if (empty($array)) {
      return null;
    }

    return current(array_slice($array, -1, 1, true));
  }
  /** Get a *Nested Value* from a *Multi-Dimensional Array*
   * 
   * @param array $array The array being searched.
   * @param string $property The *Nested Property* to search for. 
   * - A *Dot* (`.`) represents a *Sub Array* of the parent.
   * - E.g. `foo.bar.baz` will search for 
   * ```php
   * [ "foo" => [ "bar" => [ "baz" => 1234 ] ] ]
   * ```
   * @param bool $create_missing_values Indicates that any missing *Parent `arrays`* and the *Nested Value* itself are automatically created if they do not exist.
   * Defaults to **false**.
   * - The *Nested Property Itself* is set to an *Empty String*.
   * @param bool $throw_errors Indicates if *Errors* should be thrown instead of returning **null**.
   * @return mixed Returns the value of the `$property` on success.
   * Returns **null** if the `$property` could not be found and `$create_missing_values` is **false**.
   * @throws \Error if all of the following requirements are met:
   * - The *Nested Property* or any of its *Parent Arrays* are missing
   * - `$create_missing_values` is **false**
   * - `$throw_errors` is **true**
   */
  function &array_nested_value (
    array &$array, 
    string $property, 
    bool $create_missing_values = false,
    bool $throw_errors = false
  ) {
    $namespaces = Strings\explode($property, '.');
    $nested_value = &$array;
    $nested_property = array_key_last($namespaces);
    
    foreach ($namespaces as $namespace) {
      if (!is_array($nested_value) || !array_key_exists($namespace, $nested_value)) {
        if ($namespace !== $nested_property && $create_missing_values) {
          $nested_value[$namespace] = [];
        }
        else if ($namespace === $nested_property && $create_missing_values) {
          $nested_value[$namespace] =  '';
        }
        else {
          if ($throw_errors) {
            throw new \Error("Nested Array Property \"{$namespace}\" of \"{$property}\" was not found.");
          }

          $null = null;
          return $null;
        }
      }
      
      $nested_value = &$nested_value[$namespace];
    }
    return $nested_value;
  }