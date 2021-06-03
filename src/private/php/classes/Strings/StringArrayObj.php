<?php
  namespace ShiftCodesTK\Strings;

  /** Represents an *Array* of *Strings* that can be evaluated and manipulated. 
   * 
   * - The array can be retrieved via any of the following methods:
   * - - Invoking the `StringArrayObj` like a `function`.
   * - - Invoking the {@see StringArrayObj::getArray()} method.
   **/
  class StringArrayObj implements Interfaces\StringInterface {
    use Traits\StringMode,
        Traits\EditingMode,
        Traits\SupportTester;

    /** @var bool Indicates if the full `$array` should be returned when calling *Query Methods*, instead of just the `stringArray`. Non-string items return **null**. */
    protected $returnFullArray = false;
    /** @var bool Indicates if the *Array Keys* of the Array are to be operated on, instead of the values. */
    protected $arrayKeyMode = false;
    /** @var bool Indicates if errors thrown by `$array` strings should be output. */
    protected $verbose = false;

    /** @var StringObj[] An `array` made up of `StringObj`'s for each of the string values of the `$array`. **/
    protected $stringArray = [];
    /** @var bool Indicates if `$stringArray` has more recent data than `$array`. */
    protected $hasNewData = false;

    /** @var array The original, unmodified `$array`. **/
    protected $originalArray = [];
    /** @var array The current array, after any modifications. **/
    protected $array = [];

    /** Change the behavior preferences of the `StringArrayObj`
     * 
     * The following options can be changed:
     * - {@see StringArrayObj::$returnFullArray}
     * - {@see StringArrayObj::$arrayKeyMode}
     * - {@see StringArrayObj::$verbose}
     * 
     * @param string $option The option being changed.
     * @param bool $preference The new preference of the `$option`.
     * @return bool Returns `true` on success. 
     * @throws \UnexpectedValueException if the `$option` is invalid.
     */
    protected function changePreference (string $option, bool $preference): bool {
      $option_list = [
        'returnFullArray',
        'arrayKeyMode',
        'verbose'
      ];

      if (!in_array($option, $option_list, true)) {
        throw new \UnexpectedValueException("\"{$option}\" is not a valid option.");
      }

      $this->$option = $preference;
      return true;
    }
    
    /** Set or Update the `stringArray` of the array.
     * 
     * @param array $array The array being processed.
     * @return bool Returns `true` on success and `false` on failure.
     */
    protected function setStringArray (array $array): bool {
      $filter_array = function ($arr) use (&$filter_array) {
        $result = [];
        $array_key_mode = $this->arrayKeyMode;

        foreach ($arr as $arr_key => $arr_value) {
          $process_value = $array_key_mode
            ? $arr_key
            : $arr_value;

          if (is_array($arr_value) || is_string($process_value)) {
            $result[$arr_key] = [
              'key'   => $arr_key,
              'value' => $arr_value
            ];
            $result_arr = &$result[$arr_key];
            
            if (is_array($arr_value)) {
              if (!empty($arr_value)) {
                $sub_array = $filter_array($arr_value);
  
                if (!empty($sub_array)) {
                  $result_arr['value'] = $sub_array;
                }
              }
            }
            if (is_string($process_value)) {
              $subkey = $array_key_mode 
                ? 'key' 
                : 'value';
  
              $result_arr[$subkey] = new StringObj(
                $process_value, 
                $this->getEditingMode(), 
                $this->getStringMode()
              );
            }
          }
        }

        unset($arr_key, $arr_value);

        return $result;
      };

      $this->stringArray = $filter_array($array);

      return true;
    }
    /** Get the {@see StringArrayObj::stringArray} of the array.
     * 
     * @param bool $return_objects Indicates if the `StringObj` objects for each string are to be returned instead of the strings themselves.
     * @return array Returns the `$stringArray`, the values determined by `$return_objects`.
     */
    protected function getStringArray ($return_objects = false): array {
      $array = $this->stringArray;

      if (!$return_objects) {
        \array_walk_recursive($array, function (&$arr_value, $arr_key) {
          if ($arr_value instanceof StringObj) {
            $arr_value = $arr_value->getString();
          }
        });
      }

      return $array;
    }
    /** Execute a *`StringObj` Method* on the `$array` strings.
     * 
     * @param string $method The name of the `StringObj` *Method* being executed. 
     * Only *public* `StringObj` methods can be called.
     * @param bool $concat_results Indicates if the results should be *Concatenated*, returning a single result. 
     * Only valid for *Query Methods* that return an `int` or `bool`.
     * @param mixed $args The `$method` *Arguments*.
     * @return mixed Returns the results of invoking the `$method` on success. 
     * @throws \UnexpectedValueException if `$method` is an invalid or blacklisted method.
     */
    protected function execOnStrings ($method, $concat_results = false, ...$args) {
      $blacklist = [];
      $editing_mode = $this->getEditingMode();
      $result_value = null;

      if ($concat_results) {
        $result_value = 0;
      }

      $process_array = function ($array) use (&$process_array, $method, $concat_results, $args, &$result_value) {
        $result = [];

        foreach ($array as $arr_key => $arr_value) {
          if (is_array($arr_value)) {
            $processed_value = $process_array($arr_value);

            if ($concat_results && $processed_value === false) {
              return false;
            }
            
            $result[$arr_key] = $processed_value;
            continue;
          }

          try {
            if (!($arr_value instanceof StringObj)) {
              continue;
            }

            $operationResult = $arr_value->$method(...$args);

            if ($concat_results) {
              if (is_bool($operationResult)) {
                $result_value = $operationResult;

                if (!$operationResult) {
                  return false;
                }
              }
              else if (is_int($operationResult)) {
                $result_value += $operationResult;
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

        unset($arr_key, $arr_value);

        if ($concat_results) {
          return $result_value;
        }

        return $result;
      };

      if (!method_exists(get_class($this), $method)) {
        throw new \UnexpectedValueException("\"{$method}\" is not a valid method to be called.");
      }
      if (array_search($method, $blacklist) !== false) {
        throw new \UnexpectedValueException("\"{$method}\" cannot be called.");
      };

      $results = $process_array($this->stringArray);

      if (array_search($method, self::MANIPULATION_METHODS) !== false) {
        if ($editing_mode != self::EDITING_MODE_COPY) {
          $this->hasNewData = true;
        }
  
        switch ($editing_mode) {
          case self::EDITING_MODE_CHAIN:
            return $this;
          case self::EDITING_MODE_STANDARD:
            if (!$this->returnFullArray) {
              return $results;
            }
            else {
              return $this->getArray();
            }
          case self::EDITING_MODE_COPY:
            return $results;
        }
      }
      else if ($concat_results || !$this->returnFullArray) {
        return $results;
      }
      else if ($this->returnFullArray) {
        $full_array = $this->getArray();

        array_walk_recursive($full_array, function (&$arr_value, $arr_key) {
          $arr_value = null;
        });
        unset($arr_value);

        $full_array = array_replace_recursive($full_array, $results);
        return $full_array;
      }
    }

    /** Set or update the array
     * 
     * @param array $array The array being set. 
     * @return bool Returns `true` on success and `false` on failure.
     */
    public function setArray (array $array): bool {
      $this->array = $array;
      $this->originalArray = $array;
      $this->setStringArray($array);

      return true;
    }
    /** Retrieve the current or original array
     * 
     * @param bool $return_original Indicates if the *Original Array* should be returned instead of the current one.
     * @return array Returns the current or original array depending on the value of `$return_original`.
     */
    public function getArray ($return_original = false): array {
      if ($this->hasNewData) {
        $updated_array = (function () {
          $current_array = $this->array;
          $string_array = $this->getStringArray();

          $process_array = function ($arr, $string_arr) use (&$process_array) {
            $new_array = [];

            /**
             * @param string|StringObj $property
             */
            $get_property_value = function ($property) {
              if ($property instanceof StringObj) {
                return $property->getString();
              }

              return $property;
            };

            foreach ($arr as $arr_key => $arr_value) {
              $arr_strings = $string_arr[$arr_key] ?? null;

              if (!isset($arr_strings)) {
                $new_array[$arr_key] = $arr_value;
                continue;
              }

              $updated_key = $get_property_value($arr_strings['key']);
              $updated_value = $get_property_value($arr_strings['value']);

              $new_array[$updated_key] = is_array($updated_value)
                ? $process_array($arr_value, $updated_value)
                : $updated_value;
            }

            return $new_array;
          };

          return $process_array($current_array, $string_array);
        })();

        $this->hasNewData = false;
        $this->array = $updated_array;
      }

      if (!$return_original) {
        return $this->array;
      }
      else {
        return $this->originalArray;
      }
    }
    /** Join the array elements with a string
     * 
     * Unlike the native `implode()` function, this method supports *Indexed*, *Associative*, and *Multi-Dimensional Arrays*, as well as array elements of any type.
     * When joining the array elements, they are joined in the order they are encountered. Sub-arrays are joined before the next element in the main array.
     * 
     * Depending on the value of `$strings_only`; `string` and potentially `int`, `float`, and `bool` array elements are joined together with a string. 
     * All other array elements are silently ignored. 
     * 
     * @param string $glue The string used to separate joined array items. 
     * Defaults to an *Empty `String`*.
     * @param bool $strings_only Indicates if *string-like* array elements should also be joined together. 
     * - If `false`, `int`, `float`, and `bool` array elements will be *cast* to a `string` and joined together, in addition to the native `string` elements. This is the default behavior.
     * - If `true`, only native `string` array elements will be joined together. 
     * @return string Returns a `string` representing the joined array.
     */
    public function implode (string $glue = '', $strings_only = false): string {
      $array = $this->getArray();
      $string = '';
      $glue_charset = (function () use ($glue) {
        $charset = split($glue);
        $charset = array_unique($charset);

        return implode('', $charset);
      })();

      array_walk_recursive($array, function ($arr_value, $arr_key) use (&$string, $glue, $strings_only) {
        if (is_string($arr_value) || (!$strings_only && (is_numeric($arr_value) || is_bool($arr_value)))) {
          $string .= (string) "{$arr_value}{$glue}";
        }
      });

      $string = trim($string, STR_SIDE_RIGHT, $glue_charset);

      return $string;
    }

    /** Check the encoding for each of the strings
     * 
     * You can use {@see StringArrayObj::checkAllEncodings()} to test if all of the strings match the provided encoding.
     * 
     * @param string $encoding The *String Encoding* to check the strings for.
     * @param bool $throw_error If `true`, throws an `Error` if any of the provided strings do not match the encoding of `$encoding`.
     * @return bool[] Returns an `array` of `bool` values repesenting if each of the strings matching the specified `$encoding`.
     * @throws \Error If `$throw_error` is `true`, throws an `Error` if one of the provided strings does not match the encoding of `$encoding`.
     * @see StringObj::checkEncoding()
     */
    public function checkEncoding (
      string $encoding = ENCODING_UTF_8, 
      bool $throw_error = false
    ): array {
      return $this->execOnStrings('checkEncoding', false, ...func_get_args());
    }
    /** Check the encoding for all of the strings
     * 
     * You can use {@see StringArrayObj::checkEncodings()} to individually test each of the strings against the provided encoding.
     * 
     * @param string $encoding The *String Encoding* to check the strings for.
     * @param bool $throw_error If `true`, throws an `Error` if any of the provided strings do not match the encoding of `$encoding`.
     * @return bool Returns `true` if all provided strings match the *String Encoding* of `$encoding`.
     * @throws \Error If `$throw_error` is `true`, throws an `Error` if one of the provided strings does not match the encoding of `$encoding`.
     * @see StringObj::checkEncoding()
     */
    public function checkAllEncodings (
      string $encoding = ENCODING_UTF_8, 
      bool $throw_error = false
    ): bool {
      return $this->execOnStrings('checkEncoding', true, ...func_get_args());
    }
    /** Attempt to get the encoding for all of the strings
     * 
     * @return string[]|null[] Returns an `array` of `string` or `null` values representing the *Encoding* for each of the `$array` strings.
     * @see StringObj::getEncoding()
     */
    public function getEncoding (): ?array {
      return $this->execOnStrings('getEncoding', false, ...func_get_args());
    }

    /** Get the *Resolved String Mode* of each of the strings
     * 
     * @return int[]|null[] Returns an `array` of `int` or `null` values representing the *Resolved String Mode* of the strings.
     * @see StringObj::getResolvedStringMode()
     */
    public function getResolvedStringMode(): array {
      return $this->execOnStrings('getResolvedStringMode', false, ...func_get_args());
    }

    /** Get the length of the strings
     * 
     * @return int[] Returns an `array` of `int` values representing the the number of characters in each of the `$array` strings.
     * @see StringObj::strlen()
     */
    public function strlen (): array {
      return $this->execOnStrings('strlen', false, ...func_get_args());
    }
    /** Get the total length of all the strings
     * 
     * @return int Returns an `int` representing the total number of characters in all of the `$array` strings.
     * @see StringObj::strlen()
     */
    public function strlenAll (): int {
      return $this->execOnStrings('strlen', true, ...func_get_args());
    }
    /** Retrieve a character in each of the strings
     * 
     * @param int $char Indicates the *Character Position* within each of the `$array` strings to be retrieved. 
     * Positive values are relative to the *start* of the string and negative values relative to the *end*.
     * @return string[] Returns an `array` of `string` values representing the character found in each of the `$array` strings at `$char`. 
     * @see StringObj::char()
     */
    public function char ($char = 1): array {
      return $this->execOnStrings('char', false, ...func_get_args());
    }
    /** Get the first character of each of the strings
     * 
     * @return string[] Returns an `array` of `string` values representing the first character found in each of the `$array` strings.
     * @see StringObj::firstchar()
     */
    public function firstchar (): array {
      return $this->execOnStrings('firstchar', false, ...func_get_args());
    }
    /** Get the last character of each of the strings
     * 
     * @return string[] Returns an `array` of `string` values representing the last character found in each of the `$array` strings.
     * @see StringObj::lastchar()
     */
    public function lastchar (): array {
      return $this->execOnStrings('lastchar', false, ...func_get_args());
    }
    /** Convert the characters in each of the strings to an array.
     * 
     * @param int $length The maximum length of each character chunk.
     * @param bool $return_stringArray Indicates if the return value should be a `StringArrayObj` instead of an `array`.
     * @return array[]|null On success, returns an `array` for each string made up of its characters. 
     * If `$length` is less than *1*, returns `null`.
     * @see StringObj::split()
     */
    public function split (int $length = 1, bool $return_stringArray = false): ?array {
      return $this->execOnStrings('split', false, ...func_get_args());
    }
    /** Split each of the strings by another string.
     * 
     * @param string $delimiter The delimiter to split the `string` by. 
     * Can be a string of delimiter characters, or a *Regular Expression Pattern*.
     * @param int|null $limit The maximum number of splits to be performed.
     * @return array[]|null Returns an `array` of substrings created by splitting the `string` by the `$delimiters` for each of the provided strings on success. 
     * If `$delimiters` is an *Empty `String`*, returns `null`.
     * @see StringObj::explode()
     */
    public function explode (string $delimiter = ' ', int $limit = null): ?array {
      return $this->execOnStrings('explode', false, ...func_get_args());
    }

    /** Extract a slice from each of the strings.
     * 
     * This does *not* change the strings. 
     * To change strings, use the {@see StringArrayObj::slice()} method.
     * 
     * @param int $start Where the slice begins. 
     * A *positive offset* counts from the beginning of the `string`, while a *negative offset* counts from the end.
     * @param int|null $length Indicates the maximum length of the slice.
     * @param bool $throw_errors If `true`, an `OutOfRangeException` will be thrown if the provided arguments are invalid, instead of simply returning an *Empty `String`*.
     * @return string[] Returns an `array` of `string` values representing a slice of each of the strings. 
     * If a string is less than `$start` characters long, or `$length` is *negative* and tries to truncate more characters than are available, returns an *Empty `String`*.
     * @throws \OutOfRangeException If `$throw_errors` is `true`, an `OutOfRangeException` will be thrown if the provided arguments are invalid.
     * @see StringObj::substr()
     */
    public function substr (
      int $start = 0, 
      int $length = null, 
      bool $throw_errors = false
    ): array {
      return $this->execOnStrings('substr', false, ...func_get_args());
    }
    /** Finds the first or last occurrence of a substring within each of the strings
     * 
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag.
     * @param int $offset The search offset. 
     * A positive offset counts from the beginning of the *haystack*, while a negative offset counts from the end. 
     * @param int $flags A bitmask integer representing the search flags.
     * @return int[]|null[] On success, returns an `array` of `int` or `null` values representing the *first* or *last occurrence* of the *needle* within the *haystack* for each of the strings, dependent on the provided `$flags`. 
     * @see StringObj::substrPos()
     */
    public function substrPos (string $search, int $offset = 0, int $flags = 0): array {
      return $this->execOnStrings('substrPos', false, ...func_get_args());
    }
    /** Checks for the presence of substring within each of the strings
     * 
     * You can use {@see StringArrayObj::substrCheckAll()} to test if the substring is present within all of the substrings.
     *
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag.
     * @param int $offset The search offset. 
     * A positive offset counts from the beginning of the *haystack*, while a negative offset counts from the end. 
     * @param int $flags A bitmask integer representing the search flags.
     * @return bool[] Returns an `array` representing the presence of the *needles* within the *haystacks* of each `$array` string, dependent on the provided `$flags`. 
     * @see StringObj::substrCheck()
     */
    public function substrCheck (string $search, int $offset = 0, int $flags = 0): array {
      return $this->execOnStrings('substrCheck', false, ...func_get_args());
    }
    /** Checks for the presence of substring within all of the strings
     * 
     * You can use {@see StringArrayObj::substrCheck()} to individually check each of the strings for the presence of the substring.
     *
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag.
     * @param int $offset The search offset. 
     * A positive offset counts from the beginning of the *haystack*, while a negative offset counts from the end. 
     * @param int $flags A bitmask integer representing the search flags.
     * @return bool Returns `true` if the *needles* were found in each of the *haystacks*, dependent on the provided `$flags`. 
     * Otherwise, returns `false`.
     * @see StringObj::substrCheck()
     */
    public function substrCheckAll (
      string $search, 
      int $offset = 0, 
      int $flags = 0
    ): bool {
      return $this->execOnStrings('substrCheck', true, ...func_get_args());
    }
    /** Counts the number of substring occurrences within each of the strings
     * 
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag:
     * @param int $offset The search offset. 
     * A positive offset countrs from the beginning of the *haystack*, while a negative offset counts from the end.
     * @param int $length The maximum length after the specified offset to search for the substring. 
     * @param int $flags A bitmask integer representing the search flags.
     * @return int[] Returns an `array` of `int` values representing the number of times the *needles* occur in each of the *haystacks*, dependent on the provided `$flags`.
     * @see StringObj::substrCount()
     */
    public function substrCount (
      string $search, 
      int $offset = 0, 
      int $length = null, 
      int $flags = 0
    ): array {
      return $this->execOnStrings('substrCount', false, ...func_get_args());
    }
    /** Counts the number of substring occurrences within all of the strings
     * 
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag:
     * @param int $offset The search offset. 
     * A positive offset countrs from the beginning of the *haystack*, while a negative offset counts from the end.
     * @param int $length The maximum length after the specified offset to search for the substring. 
     * @param int $flags A bitmask integer representing the search flags.
     * @return int Returns an `int` representing the number of times the *needles* occur in all of the provided *haystacks*, dependent on the provided `$flags`.
     * @see StringObj::substrCount()
     */
    public function substrCountAll (
      string $search, 
      int $offset = 0, 
      int $length = null, 
      int $flags = 0
    ): int {
      return $this->execOnStrings('substrCount', true, ...func_get_args());
    }

    /** Perform a *Regular Expression Match* on each of the strings
     * 
     * @param string $pattern The *Regular Expression Pattern*.
     * @param int $flags An integer representing the Search Flags.
     * @param int $offset Specifies where the beginning of the search should start (in bytes).
     * @return string[]|array[]|null[] Returns an `array` of `string`, `array` or `null` values representing the search results for each of the strings, formatted by the provided `$flags`. If the `$pattern` doesn't match the `string`, returns `false`.
     * @see StringObj::pregMatch()
     */
    public function pregMatch (string $pattern, int $flags = 0, int $offset = 0): array {
      return $this->execOnStrings('pregMatch', false, ...func_get_args());
    }
    /** Test if each of the strings matches a *Regular Expression*
     * 
     * You can use {@see StringArrayObj::pregTestAll()} to test if all of the strings match the regular expression.
     * 
     * @param string $pattern The *Regular Expression Pattern*.
     * @param int $offset Specifies where the beginning of the search should start (in bytes).
     * @return array Returns an `array` with `bool` values representing if each of the `$array` strings matches the provided `$pattern`.
     * @see StringObj::pregTest()
     */
    public function pregTest (string $pattern, int $offset = 0): array {
      return $this->execOnStrings('pregTest', false, ...func_get_args());
    }
    /** Test if all of the strings match a *Regular Expression*
     * 
     * You can use {@see StringArrayObj::pregTest()} to individually test each of the strings against the regular expression.
     * 
     * @see StringObj::pregTest()
     * 
     * @param string $pattern The *Regular Expression Pattern*.
     * @param int $offset Specifies where the beginning of the search should start (in bytes).
     * @return bool Returns `true` if all of the strings match the provided `$pattern`, or `false` if they do not.
     */
    public function pregTestAll (string $pattern, int $offset = 0): bool {
      return $this->execOnStrings('pregTest', true, ...func_get_args());
    }

    /** Transform the capitalization of each of the strings
     * 
     * @param int $transformation A `TRANSFORM_*` constant indicating how the strings are to be transformed.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @throws \TypeError Throws a `TypeError` if `$transformation` is invalid.
     * @see StringObj::transform()
     */
    public function transform (int $transformation) {
      return $this->execOnStrings('transform', false, ...func_get_args());
    }
    /** Change the *Case Styling* of the string
     * 
     * @param int $casing_style A `CASING_STYLE_*` namespace constant indicating how the string is to be cased.
     * - See {@see CASING_STYLE_LIST}
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @throws \TypeError Throws a `TypeError` if `$casing_style` is invalid.
     * @see StringObj::changeCase()
     */
    public function changeCase (int $casing_style) {
      return $this->execOnStrings('changeCase', false, ...func_get_args());
    }
    /** Slice each of the strings into a piece
     * 
     * This *modifies* the strings. 
     * - To simply retrieve a slice of each string, use the {@see StringArrayObj::substr()} method.
     * - To split strings using substrings, use the {@see StringArrayObj::strReplace()} method.
     * - To split strings using complex searches and replacements, use the {@see StringArrayObj::pregReplace()} method.
     * 
     * @param int $start Where the slice begins. 
     * A *positive offset* counts from the beginning of the `string`, while a *negative offset* counts from the end.
     * @param int $length The length of the slice.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::slice()
     */
    public function slice (int $start = 0, int $length = null) {
      return $this->execOnStrings('slice', false, ...func_get_args());
    }
    /** Replace all occurrences of a search string with a replacement string within each of the strings
     * 
     * - To split strings into pieces every variable number of characters, use the {@see StringArrayObj::slice()} method.
     * - To split strings using more complex searches and replacements, use the {@see StringArrayObj::pregReplace()} method.
     * 
     * @param string|array $search The Search *Needle*, provided as a single `string`, or as an `array` of needles.
     * @param string|array $replacement The replacement value for each `$search` match, provided as a single `string`, or as an `array` of replacements.
     * @param bool $case_insensitive Indicates if the `$search` match(es) should be matched *Case Insensitively*.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::strReplace()
     */
    public function strReplace ($search, $replacement, $case_insensitive = false) {
      return $this->execOnStrings('strReplace', false, ...func_get_args());
    }
    /** Perform a *Global Regular Expression Replacement* on each of the strings
     * 
     * - To split strings into pieces every variable number of characters, use the {@see StringArrayObj::slice()} method.
     * - To split strings using simple substrings, use the {@see StringArrayObj::strReplace()} method.
     * 
     * 
     * @param string|array $pattern The *Regular Expression Pattern*. 
     * @param string|array|callback $replacement The value to replace each string matched by the `$pattern` with. 
     * @param int $limit The maximum number of replacements that can be done for each `$pattern`. **-1** indicates that there is no limit to the number of replacements performed.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::pregReplace()
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if any of the follow issues occur:
     * - The `$replacement` is an `array` that contains an element that is not a valid `string` or `callable` function.
     * - The `$replacement` contains a mixture of `strings` and `callable` functions.
     * @throws \Exception if an error occurred while invoking `\preg_replace()`, `\preg_replace_callback()`, or `\preg_replace_callback_array()`.
     */
    public function pregReplace ($pattern, $replacement, $limit = -1) {
      return $this->execOnStrings('pregReplace', false, ...func_get_args());
    }
    /** Appends a plural letter to each of the strings depending on the value of a given number.
     *
     * @param int $value The value to be evaluated. 
     * If this value does not equal **1**, the `$plural_letter` will be appended to each of the strings.
     * @param bool $apostrophe Indicates if an *apostrophe* (`'`) should be included with the `$plural_value`.
     * @param string $plural_value The plural value to append to the string.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::addPlural()
     */
    public function addPlural (
      int $value, 
      bool $apostrophe = false,
      string $plural_letter = 's'
    ) {
      return $this->execOnStrings('addPlural', false, ...func_get_args());
    }

    /** Trim whitespace, or other characters, from the beginning and/or end of each of the strings.
     * 
     * @param int $trim_side A `STR_SIDE_*` constant indicating which sides(s) of the string are to be trimmed.
     * @param string $charlist The list of characters that will be trimmed from the string.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::trim()
     */
    public function trim (int $trim_side = STR_SIDE_BOTH, string $charlist = " \n\r\t\v\s") {
      return $this->execOnStrings('trim', false, ...func_get_args());
    }
    /** Collapse whitespace, or other characters, within each of the strings.
     * 
     * @param string $charlist The list of characters that will be collapsed in the string.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::collapse()
     */
    public function collapse (string $charlist = " \n\r\t\v\s") {
      return $this->execOnStrings('collapse', false, ...func_get_args());
    }
    /** Pad each of the strings to a certain length with another string
     * 
     * @param int $padding_length The desired length of the strings.
     * @param string $padding The padding string used to pad the string.
     * @param int $padding_side A `STR_SIDE_*` constant indicating which side(s) of the strings are to be padded.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::pad()
     */
    public function pad (
      int $padding_length, 
      string $padding = ' ', 
      int $padding_side = STR_SIDE_RIGHT
    ) {
      return $this->execOnStrings('pad', false, ...func_get_args());
    }
    /** Split each of the strings into smaller chunks
     * 
     * @param int $chunk_length The length of a single chunk.
     * @param string $separator The separator character(s) to be placed between chunks.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::chunk()
     */
    public function chunk (int $chunk_length = 76, string $separator = "\r\n") {
      return $this->execOnStrings('chunk', false, ...func_get_args());
    }

    /** Convert HTML Characters in each of the strings into *HTML Entities*
     * 
     * @param bool $encode_everything Indicates if all characters with HTML Character Entity equivalents should be encoded, instead of just the special characters.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::encodeHTML()
     */
    public function encodeHTML (bool $encode_everything = false) {
      return $this->execOnStrings('encodeHTML', false, ...func_get_args());
    }
    /** Convert *HTML Entities* in each of the strings back to their equivalent HTML Characters. 
     * 
     * @param bool $decode_everything Indicates if all HTML Character Entities should be decoded, instead of just the special characters.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::decodeHTML()
     */
    public function decodeHTML (bool $decode_everything = false) {
      return $this->execOnStrings('decodeHTML', false, ...func_get_args());
    }
    /** Strip HTML & PHP tags from each of the strings
     * 
     * @param null|int|array|string $allowed_tags A list of whitelisted tags. 
     * - Can be a predefined `STRIP_TAGS_*` constant, custom `string`, or custom `array`. 
     * - Passing `null` will strip all tags.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$allowed_tags` is not of a valid format.
     * @see StringObj::stripTags()
     */
    public function stripTags ($allowed_tags = null) {
      return $this->execOnStrings('stripTags', false, ...func_get_args());
    }
    /** Converts special characters in each of the strings to their equivalent URL Character Codes.
     * 
     * @param bool $legacy_encode Indicates if *Legacy URL Encoding* should be performed, determining which specification should be followed when encoding the URL.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::encodeURL()
     */
    public function encodeURL (bool $legacy_encode = false) {
      return $this->execOnStrings('encode_url', false, ...func_get_args());
    }
    /** Converts URL Character Codes in each of the strings back to their equivalent special characters.
     * 
     * @param bool $legacy_decode Indicates if *Legacy URL Decoding* should be performed, determining which specification should be followed when decoding the URL.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::decodeURL()
     */
    public function decodeURL (bool $legacy_decode = false) {
      return $this->execOnStrings('decodeURL', false, ...func_get_args());
    }
    /** Encode each of the strings to be used as an identifier
     * 
     * @param int $casing_style An `ENCODE_ID_*` constant indicating how the string will be encoded.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::encodeID()
     */
    public function encodeID ($casing_style = CASING_STYLE_SNAKE_CASE) {
      return $this->execOnStrings('encodeID', false, ...func_get_args());
    }
    /** Escape each of the strings for use in a *Regular Expression*.
     * 
     * @param null|string $delimiter The *Expression Delimiter* which will also be escaped.
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @see StringObj::escapeReg()
     */
    public function escapeReg ($delimiter = null) {
      return $this->execOnStrings('escapeReg', false, ...func_get_args());
    }
    /** Escape each of the strings for use in a SQL Query Statement.
     * 
     * @return StringArrayObj|array Returns a `StringArrayObj` or `array` depending on the {@see StringArrayObj::$editingMode}.
     * @throws \RuntimeException Throws a `RuntimeException` if the method is called before the `Database` module has been loaded.
     * @see StringObj::escapeSQL()
     */
    public function escapeSQL () {
      return $this->execOnStrings('escapeSQL', false, ...func_get_args());
    }

    /** Invoking the `StringArrayObj` returns the {@see StringArrayObj::$array}.
     * 
     * @return string Returns the value of the `$array`.
     */
    public function __invoke () {
      return $this->getArray();
    }
    /** Initialize a new `StringArrayObj` 
     * 
     * @param string $array The array to be used.
     * @param array $options An `Associative Array` of options to be passed to the `StringArrayObj`.
     * - See {@see StringArrayObj::setStringMode()} for `stringMode`.
     * - See {@see StringArrayObj::setEditingMode()} for `editingMode`.
     * - See {@see StringArrayObj::changePreference()} for `returnFullArray`, `arrayKeyMode`, `verbose`.
     */
    public function __construct (array $array, array $options = []) {
      $optionsList = [ 
        'editingMode', 
        'stringMode', 
        'returnFullArray',
        'arrayKeyMode', 
        'verbose' 
      ];
      
      foreach ($optionsList as $option) {
        $optionValue = $options[$option] ?? null;

        if (isset($optionValue)) {
          switch ($option) {
            case 'stringMode' :
              $this->setStringMode($optionValue);
              break;
            case 'editingMode' :
              $this->setEditingMode($optionValue);
              break;
            default :
              $this->changePreference($option, $optionValue);
              break;
          }
        }
      }

      $this->setArray($array);

      return $this;
    }
  }
?>