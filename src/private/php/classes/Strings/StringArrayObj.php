<?php
  namespace ShiftCodesTK\Strings;

  /** Represents an *Array* of *Strings* that can be evaluated and manipulated. 
   * 
   * - The array can be retrieved via any of the following methods:
   * - - Invoking the `StringArrayObj` like a `function`.
   * - - Invoking the `get_array()` method.
   **/
  class StringArrayObj implements StringInterface {
    use SupportChecker;

    /**  @var int When *modifying* the `array`, updates the `array` and returns the `StringArrayObj` for method chaining. This is the default behavior. 
     * - Provided in the `__construct()` and used for the `$editing_mode` property.
     **/
    public const EDITING_MODE_CHAIN = 1;
    /**  @var int When *modifying* the `array`, updates and returns the `array`.
     * - Provided in the `__construct()` and used for the `$editing_mode` property.
     **/
    public const EDITING_MODE_STANDARD = 2;
    /**  @var int When *modifying* the `array`, makes a *copy* of the `array` before updating and returning it. 
     * - Provided in the `__construct()` and used for the `$editing_mode` property.
     **/
    public const EDITING_MODE_COPY = 4;

    /** @var bool Indicates if the full `$array` should be returned when calling *Query Methods*, instead of just the `string_array`. Non-string items return **null**. */
    private $return_full_array = false;
    /** @var int An `EDITING_MODE_` class constant indicating the *Editing Mode* to be used when *modifying* the `$array`. */
    private $editing_mode = self::EDITING_MODE_CHAIN;
    /** @var int A `STRING_MODE_*` class constant indicating the *String Mode* to use for the `$array` strings. */
    private $string_mode = self::STRING_MODE_AUTO;
    /** @var bool Indicates if errors thrown by `$array` strings should be output. */
    private $verbose = false;

    /** @var array 
     * An `array` made up of `StringObj`'s for each of the string values of the `$array`.
     * - You can use `set_string_array()` ({@see set_string_array()}) to update this property.
     **/
    private $string_array = [];
    /** @var bool Indicates if `$string_array` has more recent data than `$array`. */
    private $has_new_data = false;

    /** @var array 
     * The original, unmodified `$array`.
     * - You can use `get_array()` ({@see get_array()}) with the `$return_original` argument set to **true** to retrieve this property.
     * - You can use `set_array()` ({@see set_array()}) to set or update this property.
     **/
    protected $original_array = [];
    /** @var array 
     * The current array, after any modifications.
     * - You can use `get_array()` ({@see get_array()}) to retrieve this property.
     * - You can use `set_array()` ({@see set_array()}) to set or update this property.
     **/
    protected $array = [];
    
    /** Methods */
    /** Retrieve a property from the `StringArrayObj`
     * 
     * @param string $property The name of the property to retrieve 
     * @return mixed Returns the value of the property on success. Returns **null** if the property does not exist.
     */
    // public function __get ($property) {
    //   if (get_object_vars($this)[$property] ?? false) {
    //     $propertyValue = $this->$property;

    //     if ($property == 'array') {
    //       $stringArrayValues = (function () {
    //         $array = $this->string_array;

    //         \array_walk_recursive($array, function (&$arr_value, $arr_key) {
    //           $arr_value = $arr_value->get_string();
    //         });

    //         return $array;
    //       })();
    //       $this->array = \array_replace_recursive($this->array, $stringArrayValues);
    //     }

    //     return $this->$property;
    //   }

    //   return null;
    // }
    /** Set a property from the `StringArrayObj`
     * 
     * @param string $property The name of the property to set.
     * @param mixed $value The new value of the property.
     * @return mixed Returns the new value of the property on success. Returns **null** if the property does not exist, or if it cannot be set.
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$value` is not a valid value.
     */
    // public function __set ($property, $value) {
    //   if (isset($this->$property)) {
    //     $editableProperties = [
    //       'array',
    //       'return_full_array',
    //       'editing_mode',
    //       'string_mode',
    //       'verbose'
    //     ];

    //     if (array_search($property, $editableProperties) !== false) {
    //       if ($property == 'array') {
    //         $constraints = new \ValidationProperties([
    //           'type'     => 'array'
    //         ]);

    //         if (!$constraints->check_parameter($value)['valid']) {
    //           throw new \UnexpectedValueException("\"{$value}\" is not a valid array.");
    //         }

    //         $this->array = $value;
    //         $this->original_array = $this->array;
    //         $this->string_array = (function () {
    //           $filterArray = function ($array) use (&$filterArray) {
    //             $resultArray = $array;

    //             foreach ($resultArray as $arr_key => &$arr_value) {
    //               $keepItem = (function () use (&$arr_value, &$filterArray) {
    //                 if (\is_array($arr_value)) {
    //                   $arr_value = $filterArray($arr_value);
    //                   return true;
    //                 }
    
    //                 return is_string($arr_value);
    //               })();

    //               if ($keepItem) {
    //                 if (is_string($arr_value)) {
    //                   $arr_value = new StringObj($arr_value, $this->editing_mode, $this->string_mode);
    //                 }
    //               }
    //               else {
    //                 unset($resultArray[$arr_key]);
    //               }
    //             }

    //             return $resultArray;
    //           };

    //           return $filterArray($this->array);
    //         })();
    //       }
    //       else if ($property == 'editing_mode') {
    //         $constraints = new \ValidationProperties([
    //           'required'    => true,
    //           'type'        => 'integer',
    //           'validations' => [
    //             'match'        => [
    //               self::EDITING_MODE_CHAIN,
    //               self::EDITING_MODE_STANDARD,
    //               self::EDITING_MODE_COPY
    //             ]
    //           ]
    //         ]);

    //         if (!$constraints->check_parameter($value)['valid']) {
    //           throw new \UnexpectedValueException("\"{$value}\" is not a valid Editing Mode.");
    //         }

    //         $this->editing_mode = $value;
    //       }
    //       else if ($property == 'string_mode') {
    //         $constraints = new \ValidationProperties([
    //           'required'    => true,
    //           'type'        => 'integer',
    //           'validations' => [
    //             'match'        => [
    //               self::STRING_MODE_AUTO,
    //               self::STRING_MODE_STRING,
    //               self::STRING_MODE_MB_STRING
    //             ]
    //           ]
    //         ]);

    //         if (!$constraints->check_parameter($value)['valid']) {
    //           throw new \UnexpectedValueException("\"{$value}\" is not a valid String Mode.");
    //         }

    //         $this->string_mode = $value;
    //       }
    //       else {
    //         $this->$property = $value;
    //       }

    //       return $this->$property;
    //     }
    //   }

    //   return null;
    // }
    /** Invoking the `StringArrayObj` returns the `$array`.
     * 
     * @return string Returns the value of the `$array`.
     */
    public function __invoke () {
      return $this->get_array();
    }
    /** Initialize a new `StringArrayObj` 
     * 
     * @param string $array The array to be used.
     * @param array $options An `Associative Array` of options to be passed to the `StringArrayObj`:
     * - `bool $return_full_array` Return the full `$array` when calling *Query Methods* on the array, instead of just the `string_array`. Non-string items return **null**.
     * - - This does not affect any of the `MANIPULATION_METHODS`, or any methods that return a `bool`.
     * - `int $editing_mode` An `EDITING_MODE_` class constant indicating the *Editing Mode* to be used when *modifying* the `$array`.
     * - - Methods that modify the array can be found in the `MANIPULATION_METHODS` class constant.
     * 
     * | Mode | Description |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Updates the `string` and returns the `StringArrayObj` for method chaining. This is the default behavior |
     * | `EDITING_MODE_STANDARD` | Updates and returns the `array` |
     * | `EDITING_MODE_COPY` | Makes a *copy* of the `array` before updating and returning it. |
     * - - This option affects the behavior of any methods that manipulate the array strings.
     * `int $string_mode` A `STRING_MODE_*` class constant indicating the *String Mode* to use for the `$array` strings.
     * 
     * | Mode | Description |
     * | --- | --- |
     * | `STRING_MODE_AUTO` | Attempts to detect the appropriate mode to use for all of the strings. |
     * | `STRING_MODE_STRING` | Indicates that *String Mode* should be used for all of the strings. |
     * | `STRING_MODE_MB_STRING` | Indicates that *Multi-Byte String Mode* should be used for all of the strings. |
     * - `bool $verbose` Indicates if errors thrown by `$array` strings should be output.
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$array` is not a *array* or if `$string_mode` is not a valid *String Mode*.
     */
    public function __construct (array $array, array $options = []) {
      $optionsList = [ 
        'return_full_array', 
        'editing_mode', 
        'string_mode', 
        'verbose' 
      ];
      
      foreach ($optionsList as $option) {
        $optionValue = $options[$option] ?? null;

        if (isset($optionValue)) {
          // $this->__set($option, $optionValue);
          $this->$option = $optionValue;
        }
      }

      // $this->__set('array', $array);
      $this->set_array($array);

      return $this;
    }

    /** Set or Update the `string_array` of the array.
     * 
     * @param array $array The updated array.
     * @return bool Returns **true** on success and **false** on failure.
     * @throws UnexpectedValueException if `$array` is not an `array`.
     */
    private function set_string_array (array $array) {
      if (!is_array($array)) {
        throw new \UnexpectedValueException("\"{$array}\" is not a valid array.");
      }

      $filterArray = function ($filter_array) use (&$filterArray) {
        $result = [];

        foreach ($filter_array as $arr_key => $arr_value) {
          $keepItem = (function () use ($arr_value, $arr_key, &$filterArray, &$result) {
            if (\is_array($arr_value)) {
              if (!empty($arr_value)) {
                $filteredArray = $filterArray($arr_value);

                if (!empty($filteredArray)) {
                  $result[$arr_key] = $filteredArray;
                  return true;
                }
              }
            }

            return is_string($arr_value);
          })();

          if ($keepItem) {
            if (is_string($arr_value)) {
              $result[$arr_key] = new StringObj($arr_value, $this->editing_mode, $this->string_mode);
            }
          }
          else {
            unset($result[$arr_key]);
          }
        }

        unset($arr_value);

        return $result;
      };

      $this->string_array = $filterArray($array);

      return true;
    }
    /** Get the `string_array` of the array.
     * 
     * @param bool $return_objects Indicates if the `StringObj` objects for each string are to be returned instead of the strings themselves.
     * @return array Returns the `string_array`, the values determined by `$return_objects`.
     */
    private function get_string_array ($return_objects = false) {
      $array = $this->string_array;

      if (!$return_objects) {
        \array_walk_recursive($array, function (&$arr_value, $arr_key) {
          $arr_value = $arr_value->get_string();
        });
        unset($arr_value);
      }

      return $array;
    }
    /** Execute a *`StringObj` Method* on the `$array` strings.
     * 
     * @param string $method The name of the `StringObj` *Method* being executed. Only *public* `StringObj` methods can be called.
     * @param bool $concat_results Indicates if the results should be *Concatenated*, returning a single result. Only valie for *Query Methods* that return an `int` or `bool`.
     * @param mixed $args The `$method` *Arguments*.
     * @return mixed Returns the results of the `$method`. 
     * 
     * | Method Type | `$concat_results` | Return Value |
     * | --- | --- | --- |
     * | Query | **false** | Returns an `Associative Array` representing the results of each `StringObj` method query. |
     * | Query | **true** | Returns an `int` or `bool` representing the result of all `StringObj` method queries. |
     * | Manipulation | --- | Returns the `StringArrayObj` if the *Editing Mode* is `EDITING_MODE_CHAIN`. Otherwise, returns the updated `array`. |
     * @throws \UnexpectedValueException if `$method` is an invalid or blacklisted method.
     */
    private function exec_on_strings ($method, $concat_results = false, ...$args) {
      $blacklist = [];

      if ($concat_results) {
        $resultValue = 0;
      }

      $processArray = function ($array) use (&$processArray, $method, $concat_results, $args, &$resultValue) {
        $result = [];

        foreach ($array as $arr_key => $arr_value) {
          if (is_array($arr_value)) {
            $processedValue = $processArray($arr_value);

            if ($concat_results && $processedValue === false) {
              return false;
            }
            
            $result[$arr_key] = $processedValue;
            continue;
          }

          try {
            if (!is_callable($arr_value)) {
              // var_dump($array, $this->string_array);
            }
            $operationResult = $arr_value->$method(...$args);

            if ($concat_results) {
              if (is_bool($operationResult)) {
                $resultValue = $operationResult;

                if (!$operationResult) {
                  return false;
                }
              }
              else if (is_int($operationResult)) {
                $resultValue += $operationResult;
              }
            }
            else {
              $result[$arr_key] = $operationResult;
            }

          }
          catch (\Throwable $exception) {
            if ($this->verbose) {
              \trigger_error('Code ' . $exception->getCode() . ': ' . $exception->getMessage());
            }
            if ($concat_results) {
              return false;
            }

            continue;
          }
        }
        unset($arr_value);

        if ($concat_results) {
          return $resultValue;
        }

        return $result;
      };

      if (!method_exists(get_class($this), $method)) {
        throw new \UnexpectedValueException("\"{$method}\" is not a valid method to be called.");
      }
      if (array_search($method, $blacklist) !== false) {
        throw new \UnexpectedValueException("\"{$method}\" cannot be called.");
      };

      $results = $processArray($this->string_array);

      if (array_search($method, self::MANIPULATION_METHODS) !== false) {
        if ($this->editing_mode != self::EDITING_MODE_COPY) {
          $this->has_new_data = true;
        }
  
        switch ($this->editing_mode) {
          case self::EDITING_MODE_CHAIN:
            return $this;
          case self::EDITING_MODE_STANDARD:
            if (!$this->return_full_array) {
              return $results;
            }
            else {
              return $this->get_array();
            }
          case self::EDITING_MODE_COPY:
            return $results;
        }
      }
      else if ($concat_results || !$this->return_full_array) {
        return $results;
      }
      else if ($this->return_full_array) {
        $fullArray = $this->get_array();

        array_walk_recursive($fullArray, function (&$arr_value, $arr_key) {
          $arr_value = null;
        });
        unset($arr_value);

        $fullArray = array_replace_recursive($fullArray, $results);
        return $fullArray;
      }
    }

    /** Set or update the array
     * 
     * @param array $array The array being set. 
     * @return bool Returns **true** on success and **false** on failure.
     * @throws \UnexpectedValueException if `$array` is not an array.
     */
    public function set_array (array $array) {
      if (!is_array($array)) {
        throw new \UnexpectedValueException("\"{$array}\" is not a valid array.");
      }

      $this->array = $array;
      $this->original_array = $array;
      $this->set_string_array($array);
    }
    /** Retrieve the current or original array
     * 
     * @param bool $return_original Indicates if the *Original Array* should be returned instead of the current one.
     * @return array Returns the `array` or `original_array` depending on the value of `$return_original`.
     */
    public function get_array ($return_original = false): array {
      if ($this->has_new_data) {
        $this->has_new_data = false;
        $this->array = \array_replace_recursive($this->array, $this->get_string_array());
      }

      if (!$return_original) {
        return $this->array;
      }
      else {
        return $this->original_array;
      }
    }
    /** Join the array elements with a string
     * 
     * Unlike the native `implode()` function, this method supports *Indexed*, *Associative*, and *Multi-Dimensional Arrays*, as well as array elements of any type.
     * When joining the array elements, they are joined in the order they are encountered. Sub-arrays are joined before the next element in the main array.
     * Depending on the value of `$strings_only`, `string` and potentially `int`, `float`, and `bool` array elements are joined together with a string. All other array elements are silently ignored. 
     * 
     * @param string $glue The string used to separate joined array items. Defaults to an *Empty `String`*.
     * @param bool $strings_only Indicates if *string-like* array elements should also be joined together. 
     * - If **false**, `int`, `float`, and `bool` array elements will be *cast* to a `string` and joined together, in addition to the native `string` elements. This is the default behavior.
     * - If **true**, only native `string` array elements will be joined together. 
     * @param bool $return_string_obj Indicates if a `StringObj` for the joined string should be returned instead of just the string.
     * @return string|StringObj Returns a `string` or `StringObj` representing the joined array.
     */
    public function implode (string $glue = '', $strings_only = false, $return_string_obj = false) {
      $string = (function () use ($glue, $strings_only) {
        $array = $this->get_array();
        $string = '';
        $glueCharset = (function () use ($glue) {
          $charset = split($glue);
          $charset = array_unique($charset);

          return implode('', $charset);
        })();

        array_walk_recursive($array, function ($arr_value, $arr_key) use (&$string, $glue, $strings_only) {
          if (is_string($arr_value) || (!$strings_only && (is_numeric($arr_value) || is_bool($arr_value)))) {
            $string .= (string) "{$arr_value}{$glue}";
          }
        });

        $string = trim($string, STR_SIDE_RIGHT, $glueCharset);

        return $string;
      })();

      if (!$return_string_obj) {
        return $string;
      }
      else {
        return new StringObj($string, $this->editing_mode, $this->string_mode);
      }
    }

    /** Check the encoding for each of the strings
     * 
     * - You can use `check_all_encodings()` to test if all of the strings match the provided encoding.
     * 
     * @see StringObj::check_encoding()
     * 
     * @param string $encoding The *String Encoding* to check the strings for.
     * @param bool $throw_error If **true**, throws an `Error` if any of the provided strings do not match the encoding of `$encoding`.
     * @return array Returns an `array` repesenting if each of the strings matching the specified `$encoding`.
     * @throws \Error If `$throw_error` is **true**, throws an `Error` if one of the provided strings does not match the encoding of `$encoding`.
     */
    public function check_encoding (string $encoding = ENCODING_UTF_8, bool $throw_error = false): array {
      return $this->exec_on_strings('check_encoding', false, ...func_get_args());
    }
    /** Check the encoding for all of the strings
     * 
     * - You can use `check_encodings()` to individually test each of the strings against the provided encoding.
     * 
     * @see StringObj::check_encoding()
     * 
     * @param string $encoding The *String Encoding* to check the strings for.
     * @param bool $throw_error If **true**, throws an `Error` if any of the provided strings do not match the encoding of `$encoding`.
     * @return bool Returns **true** if all provided strings match the *String Encoding* of `$encoding`.
     * @throws \Error If `$throw_error` is **true**, throws an `Error` if one of the provided strings does not match the encoding of `$encoding`.
     */
    public function check_all_encodings (string $encoding = ENCODING_UTF_8, bool $throw_error = false): bool {
      return $this->exec_on_strings('check_encoding', true, ...func_get_args());
    }
    /** Attempt to get the encoding for all of the strings
     * 
     * @see StringObj::get_encoding()
     * 
     * @return array|false Returns an `array` representing the *Encoding* for each of the `$array` strings on success, or **false** if the encoding could not be detected for the string.
     */
    public function get_encoding () {
      return $this->exec_on_strings('get_encoding', false, ...func_get_args());
    }

    /** Get the length of the strings
     * 
     * @see StringObj::strlen()
     * 
     * @return array Returns an `array` representing the the number of characters in each of the `$array` strings.
     */
    public function strlen (): array {
      return $this->exec_on_strings('strlen', false, ...func_get_args());
    }
    /** Get the total length of all the strings
     * 
     * @see StringObj::strlen()
     * 
     * @return int Returns an `int` representing the total number of characters in all of the `$array` strings.
     */
    public function strlen_all (): int {
      return $this->exec_on_strings('strlen', true, ...func_get_args());
    }
    /** Retrieve a character in each of the strings
     * 
     * @see StringObj::char()
     * 
     * @param int $char Indicates the *Character Position* within each of the `$array` strings to be retrieved. Positive values are relative to the *start* of the string and negative values relative to the *end*.
     * @return array Returns an `array` representing the character found in each of the `$array` strings at `$char`. 
     */
    public function char ($char = 1): array {
      return $this->exec_on_strings('char', false, ...func_get_args());
    }
    /** Get the first character of each of the strings
     * 
     * @see StringObj::firstchar()
     * 
     * @return array Returns an `array` representing the first character found in each of the `$array` strings.
     */
    public function firstchar (): array {
      return $this->exec_on_strings('firstchar', false, ...func_get_args());
    }
    /** Get the last character of each of the strings
     * 
     * @see StringObj::lastchar()
     * 
     * @return array Returns an `array` representing the last character found in each of the `$array` strings.
     */
    public function lastchar (): array {
      return $this->exec_on_strings('lastchar', false, ...func_get_args());
    }
    /** Convert the characters in each of the strings to an array.
     * 
     * @see StringObj::split()
     * 
     * @param int $length The maximum length of each character chunk.
     * @param bool $return_string_array Indicates if the return value should be a `StringArrayObj` instead of an `array`.
     * @return array|StringArrayObj|false On success, returns an `array` or `StringArrayObj` for each string made up of its characters. If `$length` is less than *1*, returns **false**.
     */
    public function split (int $length = 1, bool $return_string_array = false) {
      return $this->exec_on_strings('split', false, ...func_get_args());
    }
    /** Split each of the strings by another string.
     * 
     * @see StringObj::explode()
     * 
     * @param string $delimiter The delimiter to split the `string` by. Can be a string of delimiter characters, or a *Regular Expression Pattern*.
     * @param int|null $limit The maximum number of splits to be performed.
     * @param bool $return_string_array Indicates if the return value should be a `StringArrayObj` instead of an `array`.
     * @return array|StringArrayObj|false Returns an `array` or `StringArrayObj` of substrings created by splitting the `string` by the `$delimiters` for each of the provided strings on success. If `$delimiters` is an *Empty `String`*, returns **false**.
     */
    public function explode (string $delimiter = ' ', int $limit = null, bool $return_string_array = false) {
      return $this->exec_on_strings('explode', false, ...func_get_args());
    }

    /** Extract a slice from each of the strings.
     * 
     * This does *not* change the strings. To change strings, use the `slice()` method.
     * - @see StringArrayObj::slice()
     * 
     * @see StringObj::substr()
     * 
     * @param int $start Where the slice begins. A *positive offset* counts from the beginning of the `string`, while a *negative offset* counts from the end.
     * @param int|null $length Indicates the maximum length of the slice.
     * @param bool $throw_errors If **true**, an `OutOfRangeException` will be thrown if the provided arguments are invalid, instead of simply returning an *Empty `String`.
     * @return string Returns a slice of the `string` on success. If the `string` is less than `$start` characters long, or `$length` is *negative* and tries to truncate more characters than are available, returns an *Empty `String`*.
     * @throws \OutOfRangeException If `$throw_errors` is **true**, an `OutOfRangeException` will be thrown if the provided arguments are invalid.
     */
    public function substr (int $start = 0, int $length = null, bool $throw_errors = false) {
      return $this->exec_on_strings('substr', false, ...func_get_args());
    }
    /** Finds the first or last occurrence of a substring within each of the strings
     * 
     * @see StringObj::substr_pos()
     * 
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag.
     * @param int $offset The search offset. A positive offset counts from the beginning of the *haystack*, while a negative offset counts from the end. 
     * @param int $flags A bitmask integer representing the search flags.
     * @return int|false On success, returns the *first* or *last occurrence* of the *needle* within the *haystack*, dependent on the provided `$flags`. If the *needle* was not found, returns **false**.
     */
    public function substr_pos (string $search, int $offset = 0, int $flags = 0) {
      return $this->exec_on_strings('substr_pos', false, ...func_get_args());
    }
    /** Checks for the presence of substring within each of the strings
     * 
     * - You can use `substr_check_all()` to test if the substring is present within all of the substrings.
     *
     * @see StringObj::substr_check()
     *  
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag.
     * @param int $offset The search offset. A positive offset counts from the beginning of the *haystack*, while a negative offset counts from the end. 
     * @param int $flags A bitmask integer representing the search flags.
     * @return array Returns an `array` representing the presence of the *needles* within the *haystacks* of each `$array` string, dependent on the provided `$flags`. Returns **false** if the string was not.
     */
    public function substr_check (string $search, int $offset = 0, int $flags = 0): array {
      return $this->exec_on_strings('substr_check', false, ...func_get_args());
    }
    /** Checks for the presence of substring within all of the strings
     * 
     * - You can use `substr_check()` to individually check each of the strings for the presence of the substring.
     *
     * @see StringObj::substr_check()
     *  
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag.
     * @param int $offset The search offset. A positive offset counts from the beginning of the *haystack*, while a negative offset counts from the end. 
     * @param int $flags A bitmask integer representing the search flags.
     * @return bool Returns **true** if the *needles* were found in each of the *haystacks*, dependent on the provided `$flags`. Returns **false** if it was not.
     */
    public function substr_check_all (string $search, int $offset = 0, int $flags = 0): bool {
      return $this->exec_on_strings('substr_check', true, ...func_get_args());
    }
    /** Counts the number of substring occurrences within each of the strings
     * 
     * @see StringObj::substr_count()
     * 
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag:
     * @param int $offset The search offset. A positive offset countrs from the beginning of the *haystack*, while a negative offset counts from the end.
     * @param int $length The maximum length after the specified offset to search for the substring. 
     * @param int $flags A bitmask integer representing the search flags.
     * @return array Returns an `array` representing the number of times the *needles* occur in each of the *haystacks*, dependent on the provided `$flags`.
     */
    public function substr_count (string $search, int $offset = 0, int $length = null, int $flags = 0): array {
      return $this->exec_on_strings('substr_count', false, ...func_get_args());
    }
    /** Counts the number of substring occurrences within all of the strings
     * 
     * @see StringObj::substr_count()
     * 
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag:
     * @param int $offset The search offset. A positive offset countrs from the beginning of the *haystack*, while a negative offset counts from the end.
     * @param int $length The maximum length after the specified offset to search for the substring. 
     * @param int $flags A bitmask integer representing the search flags.
     * @return int Returns an `int` representing the number of times the *needles* occur in all of the provided *haystacks*, dependent on the provided `$flags`.
     */
    public function substr_count_all (string $search, int $offset = 0, int $length = null, int $flags = 0): int {
      return $this->exec_on_strings('substr_count', true, ...func_get_args());
    }

    /** Perform a *Regular Expression Match* on each of the strings
     * 
     * @param string $pattern The *Regular Expression Pattern*.
     * @param int $flags An integer representing the Search Flags.
     * @param int $offset Specifies where the beginning of the search should start (in bytes).
     * @return array|StringArrayObj Returns an `array` or `StringArrayObj` representing the search results for each of the strings, formatted by the provided `$flags`. If the `$pattern` doesn't match the `string`, returns **false**.
     */
    public function preg_match (string $pattern, int $flags = 0, int $offset = 0): array {
      return $this->exec_on_strings('preg_match', false, ...func_get_args());
    }
    /** Test if each of the strings matches a *Regular Expression*
     * 
     * - You can use `preg_test_all()` to test if all of the strings match the regular expression.
     * 
     * @see StringObj::preg_test()
     * 
     * @param string $pattern The *Regular Expression Pattern*.
     * @param int $offset Specifies where the beginning of the search should start (in bytes).
     * @return array Returns an `array` with a `bool` representing if each of the `$array` strings matches the provided `$pattern`, or **false** if it does not.
     */
    public function preg_test (string $pattern, int $offset = 0): array {
      return $this->exec_on_strings('preg_test', false, ...func_get_args());
    }
    /** Test if all of the strings match a *Regular Expression*
     * 
     * - You can use `preg_test()` to individually test each of the strings against the regular expression.
     * 
     * @see StringObj::preg_test()
     * 
     * @param string $pattern The *Regular Expression Pattern*.
     * @param int $offset Specifies where the beginning of the search should start (in bytes).
     * @return bool Returns **true** if all of the strings match the provided `$pattern`, or **false** if they does not.
     */
    public function preg_test_all (string $pattern, int $offset = 0): bool {
      return $this->exec_on_strings('preg_test', true, ...func_get_args());
    }

    /** Transform the capitalization of each of the strings
     * 
     * @see StringObj::transform()
     * 
     * @param int $transformation A `TRANSFORM_*` constant indicating how the strings are to be transformed.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     * @throws \TypeError Throws a `TypeError` if `$transformation` is invalid.
     */
    public function transform (int $transformation) {
      return $this->exec_on_strings('transform', false, ...func_get_args());
    }
    /** Slice each of the strings into a piece.
     * 
     * This *changes* the strings. To simply retrieve a slice of each string, use the `substr()` method.
     * - @see StringArrayObj::substr()
     * 
     * - To split strings using substrings, use the `str_replace()` method.
     * - To split strings using complex searches and replacements, use the `preg_replace()` method.
     * 
     * @see StringObj::slice()
     * 
     * @param int $start Where the slice begins. A *positive offset* counts from the beginning of the `string`, while a *negative offset* counts from the end.
     * @param int $length The length of the slice.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function slice (int $start = 0, int $length = null) {
      return $this->exec_on_strings('slice', false, ...func_get_args());
    }
    /** Replace all occurrences of a search string with a replacement string within each of the strings
     * 
     * - To split strings into pieces every variable number of characters, use the `slice()` method.
     * - To split strings using more complex searches and replacements, use the `preg_replace()` method.
     * 
     * @param string|array $search The Search *Needle*, provided as a single `string`, or as an `array` of needles.
     * @param string|array $replacement The replacement value for each `$search` match, provided as a single `string`, or as an `array` of replacements.
     * @param bool $case_insensitive Indicates if the `$search` match(es) should be matched *Case Insensitively*.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function str_replace ($search, $replacement, $case_insensitive = false) {
      return $this->exec_on_strings('str_replace', false, ...func_get_args());
    }
    /** Perform a *Global Regular Expression Match* on each of the strings
     * 
     * - To split strings into pieces every variable number of characters, use the `slice()` method.
     * - To split strings using simple substrings, use the `str_replace()` method.
     * 
     * @see StringObj::preg_replace()
     * 
     * @param string|array $pattern The *Regular Expression Pattern*. 
     * @param string|array|callback $replacement The value to replace each string matched by the `$pattern` with. 
     * @param int $limit The maximum number of replacements that can be done for each `$pattern`. **-1** indicates that there is no limit to the number of replacements performed.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if any of the follow issues occur:
     * - The `$replacement` is an `array` that contains an element that is not a `string` or `callable` function.
     * - The `$replacement` contains a mixture of `strings` and `callable` functions.
     * @throws \Exception if an error occurred while invoking `preg_replace()`, `preg_replace_callback()`, or `preg_replace_callback_array()`.
     */
    public function preg_replace ($pattern, $replacement, $limit = -1) {
      return $this->exec_on_strings('preg_replace', false, ...func_get_args());
    }
    /** Appends a plural letter to each of the strings depending on the value of a given number.
     *
     * @param int $value The value to be evaluated. If this value equals **1**, a plural letter will be appended to each of the strings.
     * @param bool $apostrophe Indicates if an *apostrophe* (`'`) should be included with the plural letter.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function add_plural (int $value, $apostrophe = false) {
      return $this->exec_on_strings('add_plural', false, ...func_get_args());
    }

    /** Trim whitespace, or other characters, from the beginning and/or end of each of the strings.
     * 
     * @see StringObj::trim()
     * 
     * @param STR_SIDE_BOTH|STR_LEFT|STR_RIGHT $trim_side A `STR_SIDE_*` constant indicating which sides(s) of the string are to be trimmed.
     * @param string $charlist The list of characters that will be trimmed from the string.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function trim (int $trim_side = STR_SIDE_BOTH, string $charlist = " \n\r\t\v\s") {
      return $this->exec_on_strings('trim', false, ...func_get_args());
    }
    /** Collapse whitespace, or other characters, within each of the strings.
     * 
     * @see StringObj::collapse()
     * 
     * @param string $charlist The list of characters that will be collapsed in the string.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function collapse (string $charlist = " \n\r\t\v\s") {
      return $this->exec_on_strings('collapse', false, ...func_get_args());
    }
    /** Pad each of the strings to a certain length with another string
     * 
     * @see StringObj::pad()
     * 
     * @param int $padding_length The desired length of the strings.
     * @param string $padding The padding string used to pad the string.
     * @param STR_SIDE_BOTH|STR_SIDE_LEFT|STR_SIDE_RIGHT $padding_side A `STR_SIDE_*` constant indicating which side(s) of the strings are to be padded.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function pad (int $padding_length, string $padding = ' ', int $padding_side = STR_SIDE_RIGHT) {
      return $this->exec_on_strings('pad', false, ...func_get_args());
    }
    /** Split each of the strings into smaller chunks
     * 
     * @param int $chunk_length The length of a single chunk.
     * @param string $separator The separator character(s) to be placed between chunks.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function chunk (int $chunk_length = 76, string $separator = "\r\n") {
      return $this->exec_on_strings('chunk', false, ...func_get_args());
    }

    /** Convert HTML Characters in each of the strings into *HTML Entities*
     * 
     * @see StringObj::encode_html()
     * 
     * @param bool $encode_everything Indicates if all characters with HTML Character Entity equivalents should be encoded, instead of just the special characters.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function encode_html (bool $encode_everything = false) {
      return $this->exec_on_strings('encode_html', false, ...func_get_args());
    }
    /** Convert *HTML Entities* in each of the strings back to their equivalent HTML Characters. 
     * 
     * @see StringObj::decode_html()
     * 
     * @param bool $encode_everything Indicates if all HTML Character Entities should be decoded, instead of just the special characters.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function decode_html (bool $decode_everything = false) {
      return $this->exec_on_strings('decode_html', false, ...func_get_args());
    }
    /** Strip HTML & PHP tags from each of the strings
     * 
     * @see StringObj::strip_tags()
     * 
     * @param null|int|array|string $allowed_tags A list of whitelisted tags. Can be a predefined `STRIP_TAGS_*` constant, custom `string`, or custom `array`. **Null** strips all tags.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$allowed_tags` is not of a valid format.
     */
    public function strip_tags ($allowed_tags = null) {
      return $this->exec_on_strings('strip_tags', false, ...func_get_args());
    }
    /** Converts special characters in each of the strings to their equivalent URL Character Codes.
     * 
     * @see StringObj::encode_url()
     * 
     * @param bool $legacy_encode Indicates if *Legacy URL Encoding* should be performed, determining which specification should be followed when encoding the URL.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function encode_url (bool $legacy_encode = false) {
      return $this->exec_on_strings('encode_url', false, ...func_get_args());
    }
    /** Converts URL Character Codes in each of the strings back to their equivalent special characters.
     * 
     * @see StringObj::decode_url()
     * 
     * @param bool $legacy_decode Indicates if *Legacy URL Decoding* should be performed, determining which specification should be followed when decoding the URL:
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function decode_url (bool $legacy_decode = false) {
      return $this->exec_on_strings('decode_url', false, ...func_get_args());
    }
    /** Encode each of the strings to be used as an identifier
     * 
     * @see StringObj::encode_id()
     * 
     * @param int $encoding_style An `ENCODE_ID_*` constant indicating how the string will be encoded.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function encode_id ($encoding_style = ENCODE_ID_SNAKE_CASE) {
      return $this->exec_on_strings('encode_id', false, ...func_get_args());
    }
    /** Escape each of the strings for use in a *Regular Expression*.
     * 
     * @param null|string $delimiter The *Expression Delimiter* to also be escaped.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     */
    public function escape_reg ($delimiter = null) {
      return $this->exec_on_strings('escape_reg', false, ...func_get_args());
    }
    /** Escape each of the strings for use in a SQL Query Statement.
     * 
     * @see StringObj::escape_sql()
     * 
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringArrayObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `array`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `array`. |
     * @throws \RuntimeException Throws a `RuntimeException` if the method is called before the `Database` module has been loaded.
     */
    public function escape_sql () {
      return $this->exec_on_strings('escape_sql', false, ...func_get_args());
    }
  }
?>