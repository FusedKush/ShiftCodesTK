<?php
  namespace ShiftCodesTK\Strings;
  use ShiftCodesTK\Validations;

  /** Represents a *String* that can be evaluated and manipulated. 
   * 
   * - The string can be retrieved via any of the following methods:
   * - - Casting the `StringObj` as a `string`.
   * - - Invoking the `StringObj` like a `function`.
   * - - Invoking the `get_string()` method.
   **/
  class StringObj implements StringInterface {
    use SupportChecker;

    /**  @var int When *modifying* the `string`, updates the `string` and returns the `StringObj` for method chaining. This is the default behavior. 
     * - Provided in the `__construct()` and used for the `$editing_mode` property.
     **/
    public const EDITING_MODE_CHAIN = 1;
    /**  @var int When *modifying* the `string`, updates and returns the `string`.
     * - Provided in the `__construct()` and used for the `$editing_mode` property.
     **/
    public const EDITING_MODE_STANDARD = 2;
    /**  @var int When *modifying* the `string`, makes a *copy* of the `string` before updating and returning it. 
     * - Provided in the `__construct()` and used for the `$editing_mode` property.
     **/
    public const EDITING_MODE_COPY = 4;

    /** @var int A list of Character Sets supported by the `encode_html()` and `decode_html()` methods and functions. */
    public const HTML_ENCODING_SUPPORTED_ENCODINGS = [
      'ISO-8859-1',
      'ISO8859-1',
      'ISO-8859-5',
      'ISO8859-5',
      'ISO-8859-15',
      'ISO8859-15',
      'UTF-8',
      'cp866',
      'ibm866',
      '866',
      'cp1251',
      'Windows-1251',
      'wub-1251',
      '1251',
      'cp1252',
      'Windows-1252',
      '1252',
      'KOI8-R',
      'koi8-ru',
      'koi8r',
      'BIG5',
      '950',
      'GB2312',
      '936',
      'BIG5-HKSCS',
      'Shift_JIS',
      'SJIS',
      'SJIS-win',
      'cp939',
      '932',
      'EUC-JP',
      'EUCJP',
      'eucJP-win',
      'MacRoman'
    ];

    /**  @var STRING_MODE_STRING|STRING_MODE_MB_STRING Indicates the *Resolved String Mode* currently being used for the `$string`. 
     * - If `$string_mode` is not set to `STRING_MODE_AUTO`, uses the value of `$string_mode`.
     * - If `$string_mode` is set to `STRING_MODE_AUTO`, infers one of the other values from the `$string`.
     **/
    private $resolved_string_mode = self::STRING_MODE_STRING;
    /** @var string The original, unmodified `$string`. */
    private $original_string = '';
    /** @var string Indicates the detected *Encoding* of the `$string`, if available. */
    private $encoding = '';
    
    /** @var int Indicates the *Editing Mode* to be used when *modifying* the `$string`. */
    protected $editing_mode = self::EDITING_MODE_CHAIN;
    /** @var int Indicates the *String Mode* to be used for the `$string`. */
    protected $string_mode = self::STRING_MODE_AUTO;
    /** @var string The current string, after any modifications. */
    protected $string = '';
    
    /** Methods */
    /** Retrieve a property from the `StringObj`
     * 
     * @param string $property The name of the property to retrieve 
     * @return mixed Returns the value of the property on success. Returns **null** if the property does not exist.
     */
    public function __get ($property) {
      if (isset($this->$property)) {
        return $this->$property;
      }

      return null;
    }
    /** Set a property from the `StringObj`
     * 
     * @param string $property The name of the property to set.
     * @param mixed $value The new value of the property.
     * @return mixed Returns the new value of the property on success. Returns **null** if the property does not exist, or if it cannot be set.
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$value` is not a valid value.
     */
    public function __set ($property, $value) {
      if (isset($this->$property)) {
        $editableProperties = [
          'editing_mode',
          'string_mode',
          'string'
        ];

        if (array_search($property, $editableProperties) !== false) {
          if ($property == 'string') {
            if (!Validations\check_type($value, 'string')) {
              throw new \UnexpectedValueException("\"{$value}\" is not a valid string.");
            }

            $this->string = $value;
            $this->original_string = $this->string;
            $this->encoding = $this->get_encoding();
            $this->check_string_mode();
          }
          else if ($property == 'string_mode') {
            $isValidValue = Validations\check_var($value)
                            && Validations\check_type($value, 'int')
                            && Validations\check_match($value, [
                              self::STRING_MODE_AUTO,
                              self::STRING_MODE_STRING,
                              self::STRING_MODE_MB_STRING
                            ]);

            if (!$isValidValue) {
              throw new \UnexpectedValueException("\"{$value}\" is not a valid String Mode.");
            }

            $this->string_mode = $value;
            $this->check_string_mode();
          }
          else if ($property == 'editing_mode') {
            $isValidValue = Validations\check_var($value)
                            && Validations\check_type($value, 'int')
                            && Validations\check_match($value, [
                              self::EDITING_MODE_CHAIN,
                              self::EDITING_MODE_STANDARD,
                              self::EDITING_MODE_COPY
                            ]);

            if (!$isValidValue) {
              throw new \UnexpectedValueException("\"{$value}\" is not a valid Editing Mode.");
            }

            $this->editing_mode = $value;
          }
          else {
            $this->$property = $value;
          }
        }

        return $this->$property;
      }

      return null;
    }
    /** Casting the `StringObj` as a string returns the `$string`.
     * 
     * @return string Returns the value of the `$string`.
     */
    public function __toString () {
      return $this->get_string();
    }
    /** Invoking the `StringObj` returns the `$string`.
     * 
     * @return string Returns the value of the `$string`.
     */
    public function __invoke () {
      return $this->get_string();
    }
    /** Initialize a new `StringObj` 
     * 
     * @param string $string The string to be used.
     * @param int $editing_mode An `EDITING_MODE_` class constant indicating the *Editing Mode* to be used when *modifying* the `$string`.
     * - Methods that modify the string can be found in the `MANIPULATION_METHODS` class constant.
     * 
     * | Mode | Description |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Updates the `string` and returns the `StringObj` for method chaining. This is the default behavior |
     * | `EDITING_MODE_STANDARD` | Updates and returns the `string` |
     * | `EDITING_MODE_COPY` | Makes a *copy* of the `string` before updating and returning it. |
     * - This option affects the behavior of any methods that manipulate the string.
     * @param int $string_mode A `STRING_MODE_*` class constant indicating the *String Mode* to use for the `$string`.
     * 
     * | Mode | Description |
     * | --- | --- |
     * | `STRING_MODE_AUTO` | Attempts to detect the appropriate mode to use for the string. |
     * | `STRING_MODE_STRING` | Indicates that *String Mode* should be used. |
     * | `STRING_MODE_MB_STRING` | Indicates that *Multi-Byte String Mode* should be used. |
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$string` is not a *string* or if `$string_mode` is not a valid *String Mode*.
     */
    public function __construct (string $string, int $editing_mode = self::EDITING_MODE_CHAIN, int $string_mode = self::STRING_MODE_AUTO) {
      $args = [ 'editing_mode', 'string_mode', 'string' ];

      foreach ($args as $arg) {
        if (isset($$arg)) {
          $this->__set($arg, $$arg);
        }
      }

      return $this;
    }

    /** Checks a *Regular Expression* or group of expressions to ensure they are compatible with *Multi-Byte Strings*.
     * 
     * @param string|array $pattern The *Regular Expression Pattern* to check or an `array` of patterns to be checked. 
     * @return string|array Returns the *Regular Expression Pattern* or `array` of patterns that were checked. If the string is in *Multi-Byte Mode*, the patterns were updated accordingly.
     */
    private function preg_check_pattern ($pattern) {
      $updatedPattern = $pattern;

      $getPattern = function ($pattern_string) {
        $str = $pattern_string;

        if ($this->resolved_string_mode == self::STRING_MODE_MB_STRING) {
          // Add Unicode Modifier
          $str .= 'u';
          
          // Replace groups
          $str = str_replace($str, [ '\w', '\W', '\d', '\D', '\s', '\S' ], [ '\p{L}', 'P{L}', '\p{N}', '\P{N}', '\p{Z}', '\P{Z}' ]);
        }

        return $str;
      };

      if ($this->resolved_string_mode == self::STRING_MODE_MB_STRING) {
        if (is_string($pattern)) {
          $updatedPattern = $getPattern($updatedPattern);
        }
        else if (is_array($pattern)) {
          foreach ($updatedPattern as &$regex) {
            $regex = $getPattern($regex);
          }
        }
      }

      return $updatedPattern;
    }
    /** Handles the Return Value of a *String Manipulation Method* based on the current `$editing_mode`.
     * 
     * @param string $string The modified string.
     * @return StringObj|string Returns the modified `StringObj` or `string` depending on the current `$editing_mode`.
     */
    private function handle_modify_return (string $string) {
      if ($this->editing_mode != self::EDITING_MODE_COPY) {
        $this->string = $string;
      }

      switch ($this->editing_mode) {
        case self::EDITING_MODE_CHAIN:
          return $this;
        case self::EDITING_MODE_STANDARD:
          return $string;
        case self::EDITING_MODE_COPY:
          return $string;
      }
    }
    /** Create a `StringArrayObj` for a given array, taking into account the `editing_mode` and `string_mode` properties of the current `StringObj`.
     * 
     * @param array $array The array being evaluated.
     * @return StringArrayObj Returns the new `StringArrayObj` on success.
     */
    private function get_string_array (array $array) {
      return new StringArrayObj($array, [
        'editing_mode' => $this->editing_mode,
        'string_mode' => $this->string_mode
      ]);
    }

    /** Retrieve the current or original string
     * 
     * @param bool $return_original Indicates if the *Original String* should be returned instead of the current one.
     * @return string Returns the `string` or `original_string` depending on the value of `$return_original`.
     */
    public function get_string ($return_original = false): string {
      return $this->__get(!$return_original ? 'string' : 'original_string');
    }

    /** Check the encoding for the string
     * 
     * @param string $encoding The *String Encoding* to check the string for.
     * @param bool $throw_error If **true**, throws an `Error` if the string does not match the encoding of `$encoding`.
     * @return bool Returns **true** if the string matches the *String Encoding* of `$encoding`.
     * @throws \Error If `$throw_error` is **true**, throws an `Error` if the string does not match the encoding of `$encoding`.
     */
    public function check_encoding (string $encoding = ENCODING_UTF_8, bool $throw_error = false): bool {
      $result = \mb_check_encoding($this->string, $encoding);
  
      if (!$result && $throw_error) {
        throw new \Error("String Encoding is not \"{$this->encoding}\".");
      }
  
      return $result;
    }
    /** Attempt to get the encoding for the string
     * 
     * @return string|false Returns the *Encoding* of the string on success, or **false** if the encoding could not be detected.
     */
    public function get_encoding () {
      $encoding_list = [
        ENCODING_ASCII,
        ENCODING_UTF_8,
        ENCODING_ISO_8859_1
      ];
  
      foreach ($encoding_list as $encoding) {
        if ($this->check_encoding($encoding)) {
          return $encoding;
        }
      }
  
      return \mb_detect_encoding($this->string, $encoding_list, true);
    }
    /** Checks and updates the `$resolved_string_mode` for the `$string`.
     * 
     * @return STRING_MODE_STRING|STRING_MODE_MB_STRING Returns the new *Resolved String Mode*.
     */
    public function check_string_mode () {
      if ($this->string_mode == self::STRING_MODE_AUTO) {
        if (array_search($this->encoding, [ ENCODING_ASCII, ENCODING_ISO_8859_1 ]) !== false) {
          $this->resolved_string_mode = self::STRING_MODE_STRING;
        }
        else {
          $this->resolved_string_mode = self::STRING_MODE_MB_STRING;
        }
      }
      else {
        $this->resolved_string_mode = $this->string_mode;
      }

      return $this->resolved_string_mode;
    }

    /** Get the length of the string
     * 
     * @return int Returns the number of characters in the `string`.
     */
    public function strlen (): int {
      return $this->resolved_string_mode == self::STRING_MODE_STRING
             ? \strlen($this->string)
             : \mb_strlen($this->string, $this->encoding);
    }
    /** Retrieve a character in the string
     * 
     * @param int $char Indicates the *Character Position* within the `string` of the character to be retrieved.
     * - A positive value indicates the character position relative to the *start* of the string, while a negative values are relative to the *end*.
     * - **1** refers to the first character in the string, while **-1** refers to the last. **0** is treated as **1**.
     * @return string Returns the character found in the `string` at `$char`. If `$char` exceeds the length of the `string`, returns an *Empty `String`*.
     */
    public function char ($char = 1): string {
      $charVal = $char;

      if ($charVal === 0) {
        $charVal = 1;
      }
      if ($charVal > 0) {
        $charVal--;
      }

      return $this->substr($charVal, 1);
    }
    /** Get the first character of the string
     * 
     * @return string Returns the first character found in the `string`.
     */
    public function firstchar (): string {
      return $this->char(1);
    }
    /** Get the last character of the string
     * 
     * @return string Returns the last character found in the `string`.
     */
    public function lastchar (): string {
      return $this->char(-1);
    }
    /** Convert the string's characters to an array.
     * 
     * @param int $length The maximum length of each character chunk.
     * @param bool $return_string_array Indicates if the return value should be a `StringArrayObj` instead of an `array`.
     * @return array|StringArrayObj|false On success, returns an `array` or `StringArrayObj` made up of the characters of the `string` depending on the value of `$return_string_array`. If `$length` is less than *1*, returns **false**.
     * - If `$length` is greater than the length of the `string`, the entire string will be returned as the only element of the array.
     */
    public function split (int $length = 1, bool $return_string_array = false) {
      $result = (function () use ($length) {
        if ($this->resolved_string_mode == self::STRING_MODE_STRING) {
          return \str_split($this->string, $length);
        }
        else if ($this->resolved_string_mode == self::STRING_MODE_MB_STRING) {
          if (function_exists('mb_str_split')) {
            return \mb_str_split($this->string, $length, $this->encoding);
          }
          else {
            return $this->preg_match("/(.|\r|\n){{$length}}|(.|\r|\n){1,5}$/", PREG_GLOBAL_SEARCH|PREG_RETURN_FULL_MATCH);
          }
        }
      })();

      if (!$return_string_array) {
        return $result;
      }
      else {
        return $this->get_string_array($result);
      }
    }
    /** Split the string by another string.
     * 
     * @param string $delimiter The delimiter to split the `string` by. Can be a string of delimiter characters, or a *Regular Expression Pattern*.
     * @param int|null $limit The maximum number of splits to be performed.
     * - If positive, the result array will only contain up to this number of substrings. The last substring will contain the remainder of the `string`.
     * - If negative, all substrings except the last `$limit` are returned.
     * - If **0**, this argument is treated as **1**.
     * @param bool $return_string_array Indicates if the return value should be a `StringArrayObj` instead of an `array`.
     * @return array|StringArrayObj|false Returns an `array` or `StringArrayObj` of substrings created by splitting the `string` by the `$delimiters` on success. 
     * - If `$delimiters` contains a value not contained within the `string`, returns an `array` containing the full `string`.
     * - If `$delimiters` is an *Empty `String`*, returns **false**.
     * - If a negative `$limit` is provided and truncates more than the total number of results, returns an *Empty `Array`*.
     */
    public function explode (string $delimiter = ' ', int $limit = null, bool $return_string_array = false) {
      $result = [];
      $isRegularExpression = (function () use ($delimiter) {
        $delimitersObj = new StringObj($delimiter);

        if ($patternDelimiters = $delimitersObj->preg_match('/^([^\w\d\s]).+([^\w\d\s])$/', PREG_RETURN_SUB_MATCHES)) {
          if ($patternDelimiters[0] == $patternDelimiters[1]) {
            if (@\preg_match($delimiter, '') !== false) {
              return true;
            }
          }
        }

        return false;
      })();
      $execArgs = (function () use ($delimiter, $limit, $isRegularExpression) {
        $execArgs = [
          $isRegularExpression
            ? $this->preg_check_pattern($delimiter)
            : $delimiter, 
          $this->string
        ];

        if (isset($limit) && $limit >= 0) {
          if ($limit === 0) {
            $execArgs[] = 1;
          }
          else {
            $execArgs[] = $limit;
          }
        }

        return $execArgs;
      })();

      if (empty($delimiter)) {
        return false;
      }
      if (!$isRegularExpression) {
        $result = \explode(...$execArgs);
      }
      else {
        $result = \preg_split(...$execArgs);
      }

      if (isset($limit) && $limit < 0) {
        $result = \array_slice($result, 0, 0 + $limit, true);
      }

      return $result;
    }

    /** Extract a slice from the `string`
     * 
     * This does *not* change the string. To change string, use the `slice()` method.
     * - @see StringObj::slice()
     * 
     * @param int $start Where the slice begins. 
     * - A *positive offset* counts from the beginning of the `string`, while a *negative offset* counts from the end.
     * @param int|null $length Indicates the maximum length of the slice.
     * - A *positive length* indicates the maximum number of characters after the specified `$start` to include in the slice. 
     * - A *negative length* indicates the number of characters from the end of the `string` to be omitted.
     * - If omitted, the slice will continue from the `$start` to the end of the `string`.
     * @param bool $throw_errors If **true**, an `OutOfRangeException` will be thrown if the provided arguments are invalid, instead of simply returning an *Empty `String`.
     * @return string Returns a slice of the `string` on success. If the `string` is less than `$start` characters long, or `$length` is *negative* and tries to truncate more characters than are available, returns an *Empty `String`*.
     * @throws \OutOfRangeException If `$throw_errors` is **true**, an `OutOfRangeException` will be thrown if the provided arguments are invalid.
     */
    public function substr (int $start = 0, int $length = null, bool $throw_errors = false) {
      try {
        $strlen = $this->strlen();
        $args = (function () use ($start, $length) {
          $args = [ $this->string, $start ];
  
          if (isset($length)) {
            $args[] = $length;
          }
          if ($this->resolved_string_mode == self::STRING_MODE_MB_STRING) {
            $args[] = $this->encoding;
          }
  
          return $args;
        })();
  
        if ($strlen < $start) {
          throw new \OutOfRangeException("The Start Position is {$start}, but the string is only {$strlen} characters long.");
        }
        if (isset($length) && (0 > $length) && (($strlen - $start) + $length) < 0) {
          throw new \OutOfRangeException("The Start Position is {$start}, but the string is only {$strlen} characters long and the Length is trying to remove {$length} characters.");
        }
  
        $result = $this->resolved_string_mode == self::STRING_MODE_STRING
               ? \substr(...$args)
               : \mb_substr(...$args);
  
        if (array_search($result, [ null, false, '' ], true) !== false) {
          return "";
        }
        else {
          return $result;
        }
      }
      catch (\Throwable $exception) {
        if ($throw_errors) {
          throw $exception;
        }

        return "";
      }
    }
    /** Finds the first or last occurrence of a substring within a string
     * 
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag:
     * - If the `SUBSTR_SEARCH_AS_HAYSTACK` flag is omitted, `$search` is used as the *needle*, with `string` as the *haystack*. Searches for the first or last occurrence of the `$search` substring within `string`.
     * - If the `SUBSTR_SEARCH_AS_HAYSTACK` flag is present, `$search` is used as the *haystack*, with `string` as the *needle*. Searches for the first or last occurrence of the `string` substring within `$search`.
     * @param int $offset The search offset. 
     * - A positive offset counts from the beginning of the *haystack*, while a negative offset counts from the end. This effect is *reversed* if the `SUBSTR_GET_LAST_OCCURRENCE` flag is present.
     * @param int $flags A bitmask integer representing the search flags:
     * - @see Strings\SUBSTR_SEARCH_AS_HAYSTACK
     * - @see Strings\SUBSTR_GET_LAST_OCCURRENCE
     * - @see Strings\SUBSTR_CASE_INSENSITIVE
     * | Flag | Description |
     * | --- | --- |
     * | `SUBSTR_SEARCH_AS_HAYSTACK` | The `$search` will be treated as the *haystack*. Searches for the `string` substring within the `$search`. |
     * | `SUBSTR_GET_LAST_OCCURRENCE` | The *last matching occurrence* of the **needle** within the **haystack** will be returned. |
     * | `SUBSTR_CASE_INSENSITIVE` | The search will be *case-insensitive*. |
     * @return int|false On success, returns the *first* or *last occurrence* of the *needle* within the *haystack*, dependent on the provided `$flags`. If the *needle* was not found, returns **false**.
     */
    public function substr_pos (string $search, int $offset = 0, int $flags = 0) {
      $searchAsHaystack = ($flags & SUBSTR_SEARCH_AS_HAYSTACK) != 0;
      $getLastOccurrence = ($flags & SUBSTR_GET_LAST_OCCURRENCE) != 0;
      $caseInsensitive = ($flags & SUBSTR_CASE_INSENSITIVE) != 0;
      $needle = !$searchAsHaystack
                ? $search
                : $this->string;
      $haystack = !$searchAsHaystack
                  ? $this->string
                  : $search;

      if ($caseInsensitive) {
        $needle = (new StringObj($needle))->transform(TRANSFORM_LOWERCASE)();
        $haystack = (new StringObj($haystack))->transform(TRANSFORM_LOWERCASE)();
      }

      // String Mode
      if ($this->resolved_string_mode == self::STRING_MODE_STRING) {
        $args = [ $haystack, $needle, $offset ];

        // Get First Occurrence
        if ($getLastOccurrence) {
          return \strpos(...$args);
        }
        // Get Last Occurrence
        else {
          return \strrpos(...$args);
        }
      }
      // Multi-Byte String Mode
      else if ($this->resolved_string_mode == self::STRING_MODE_MB_STRING) {
        $args = [ $haystack, $needle, $offset, $this->encoding ];

        if ($getLastOccurrence) {
          return \mb_strpos(...$args);
        }
        else {
          return \mb_strrpos(...$args);
        }
      }
    }
    /** Checks for the presence of substring within a string
     * 
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag:
     * - If the `SUBSTR_SEARCH_AS_HAYSTACK` flag is omitted, `$search` is used as the *needle*, with `string` as the *haystack*. Searches for an occurrence of the `$search` substring within `string`.
     * - If the `SUBSTR_SEARCH_AS_HAYSTACK` flag is present, `$search` is used as the *haystack*, with `string` as the *needle*. Searches for an occurrence of the `string` substring within `$search`.
     * @param int $offset The search offset. 
     * - A positive offset counts from the beginning of the *haystack*, while a negative offset counts from the end. 
     * @param int $flags A bitmask integer representing the search flags:
     * - @see Strings\SUBSTR_SEARCH_AS_HAYSTACK
     * - @see Strings\SUBSTR_CASE_INSENSITIVE
     * | Flag | Description |
     * | --- | --- |
     * | `SUBSTR_SEARCH_AS_HAYSTACK` | The `$search` will be treated as the *haystack*. Searches for the `string` substring within the `$search`. |
     * | `SUBSTR_CASE_INSENSITIVE` | The search will be *case-insensitive*. |
     * @return bool Returns **true** if the *needle* was found in the *haystack*, dependent on the provided `$flags`. Returns **false** if it was not.
     */
    public function substr_check (string $search, int $offset = 0, int $flags = 0): bool {
      return $this->substr_pos($search, $offset, $flags) !== false;
    }
    /** Counts the number of substring occurrences within a string
     * 
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag:
     * - If the `SUBSTR_SEARCH_AS_HAYSTACK` flag is omitted, `$search` is used as the *needle*, with `string` as the *haystack*. Searches for occurrences of the `$search` substring within `string`.
     * - If the `SUBSTR_SEARCH_AS_HAYSTACK` flag is present, `$search` is used as the *haystack*, with `string` as the *needle*. Searches for occurrences of the `string` substring within `$search`.
     * @param int $offset The search offset. 
     * - A positive offset countrs from the beginning of the *haystack*, while a negative offset counts from the end.
     * @param int $length The maximum length after the specified offset to search for the substring. 
     * - Outputs a warning if the `$offset` plus the `$length` is greater than the *haystack length*.
     * @param int $flags A bitmask integer representing the search flags:
     * - @see Strings\SUBSTR_SEARCH_AS_HAYSTACK
     * - @see Strings\SUBSTR_CASE_INSENSITIVE
     * | Flag | Description |
     * | --- | --- |
     * | `SUBSTR_SEARCH_AS_HAYSTACK` | The `$search` will be treated as the *haystack*. Searches for the `string` substring within the `$search`. |
     * | `SUBSTR_CASE_INSENSITIVE` | The search will be *case-insensitive*. |
     * @return int Returns the number of times the *needle* occurs in the *haystack*, dependent on the provided `$flags`.
     */
    public function substr_count (string $search, int $offset = 0, int $length = null, int $flags = 0): int {
      $searchAsHaystack = ($flags & SUBSTR_SEARCH_AS_HAYSTACK) != 0;
      $caseInsensitive = ($flags & SUBSTR_CASE_INSENSITIVE) != 0;
      $needle = !$searchAsHaystack
                ? $search
                : $this->string;
      $haystack = !$searchAsHaystack
                  ? $this->string
                  : $search;

      if ($caseInsensitive) {
        $needle = (new StringObj($needle))->transform(TRANSFORM_LOWERCASE)();
        $haystack = (new StringObj($haystack))->transform(TRANSFORM_LOWERCASE)();
      }
      if (isset($offset) && isset($length)) {
        if (($offset + $length) > (new StringObj($haystack))->strlen()) {
          trigger_error("The \"offset\" plus the \"length\" exceeds the length of the search haystack.", E_USER_WARNING);
        }
      }

      // String Mode
      if ($this->resolved_string_mode == self::STRING_MODE_STRING) {
        $args = (function () use ($haystack, $needle, $offset, $length) {
          $args = [ $haystack, $needle, $offset ];

          if (isset($length)) {
            $args[] = $length;
          }

          return $args;
        })();

        return \substr_count(...$args);
      }
      // Multi-Byte String Mode
      else if ($this->resolved_string_mode == self::STRING_MODE_MB_STRING) {
        return \mb_substr_count($haystack, $needle, $this->encoding);
      }
    }

    /** Perform a *Regular Expression Match* on the string
     * 
     * @param string $pattern The *Regular Expression Pattern*.
     * - You **cannot** use the `g` (`PCRE_GLOBAL`) modifier. To perform a *global search*, pass the `PREG_GLOBAL_SEARCH` flag to `$flags`.
     * - You should **not** specify the `u` (`PCRE_UTF8`) modifier, as it is automatically added as needed.
     * @param int $flags An integer representing the Search Flags:
     * 
     * | Flag | Description |
     * | --- | --- |
     * | `PREG_GLOBAL_SEARCH` | Performs a *Global Search*, like the `g` modifier was used. |
     * | `PREG_RETURN_FULL_MATCH` | Returns only the *full pattern match*. |
     * | `PREG_RETURN_SUB_MATCHES` | Returns only the *matched subpatterns*. |
     * | `PREG_RETURN_STRING_ARRAY_OBJ` | Returns a `StringArrayObj` representing the matches instead of an `array`. |
     * | `PREG_OFFSET_CAPTURE` | Each returned match will include the *offset* (in bytes) as the second item in the array. |
     * | | ```[ [ 'foobarbaz', 0 ], [ 'bar', 3 ] ]```
     * | `PREG_UNMATCHED_AS_NULL` | Instead of unmatched subpatterns being reported as an *empty string*, they will be reported as **null**. |
     * | | ```[ 'foobar', 'foo', 'bar', NULL ]```
     * @param int $offset Specifies where the beginning of the search should start (in bytes).
     * @return array|StringArrayObj|false 
     * - On success, returns an `array` or `StringArrayObj` made up of the search results, formatted by the provided `$flags`. 
     * - - The first item contains the text that matched the full pattern, with subsequent items containing the text that matches a captured subpattern.
     * - - If the `PREG_RETURN_FULL_MATCH` flag was passed, only the *full pattern match* will be returned.
     * - - If the `PREG_RETURN_SUB_MATCHES` flag was passed, only the *matched subpatterns* will be returned.
     * - - If the `PREG_OFFSET_CAPTURE` flag was passed, each match will include the *offset* (in bytes).
     * - - If the `PREG_UNMATCHED_AS_NULL` flag was passed, unmatched subpatterns will be reported as **null**, instead of as an *empty string*.
     * - If the `$pattern` doesn't match the `string`, returns **false**.
     */
    public function preg_match (string $pattern, int $flags = 0, int $offset = 0) {
      $pattern_str = $this->preg_check_pattern($pattern);
      $pattern_flags = (function () use ($flags) {
        $used_flags = [ PREG_OFFSET_CAPTURE, PREG_UNMATCHED_AS_NULL ];
        $pattern_flags = 0;

        foreach ($used_flags as $flag) {
          if ($flags & $flag) {
            $pattern_flags = $pattern_flags|$flag;
          }
        }
        
        return $pattern_flags;
      })();
      $matches = [];

      $search = ($flags & PREG_GLOBAL_SEARCH) == 0
                ? \preg_match($pattern_str, $this->string, $matches, $pattern_flags, $offset)
                : \preg_match_all($pattern_str, $this->string, $matches, PREG_SET_ORDER|$pattern_flags, $offset);

      if ($search) {
        $result = (function () use ($flags, $matches) {
          if ($flags & PREG_RETURN_FULL_MATCH) {
            if (($flags & PREG_GLOBAL_SEARCH) == 0) {
              return $matches[0];
            } 
            else {
              foreach ($matches as &$match) {
                $match = $match[0];
              }
            }
          }
          else if ($flags & PREG_RETURN_SUB_MATCHES) {
            if (($flags & PREG_GLOBAL_SEARCH) == 0) {
              array_shift($matches);
            }
            else {
              foreach ($matches as &$match) {
                array_shift($match);
              }
            }
          }
  
          return $matches;
        })();

        if (($flags & PREG_RETURN_STRING_ARRAY_OBJ) === 0) {
          return $result;
        }
        else {
          return $this->get_string_array($result);
        }
      }

      return false;
    }
    /** Test if the string matches a *Regular Expression*
     * 
     * @param string $pattern The *Regular Expression Pattern*.
     * - You **cannot** use the `g` (`PCRE_GLOBAL`) modifier for testing a pattern.
     * - You should **not** specify the `u` (`PCRE_UTF8`) modifier, as it is automatically added as needed.
     * @param int $offset Specifies where the beginning of the search should start (in bytes).
     * @return bool Returns **true** if the `string` matches the `$pattern`, or **false** if it does not.
     */
    public function preg_test (string $pattern, int $offset = 0): bool {
      return $this->preg_match($pattern, 0, $offset) !== false;
    }

    /** Transform the capitalization of the `string`
     * 
     * @param TRANSFORM_LOWERCASE|TRANSFORM_UPPERCASE|TRANSFORM_CAPITALIZE_WORDS|TRANSFORM_CAPITALIZE_FIRST $transformation Indicates how the string is to be transformed:
     * 
     * | Transformation | Description | 
     * | --- | --- |
     * | `TRANSFORM_LOWERCASE` | Transforms the entire string to *lowercase*. |
     * | `TRANSFORM_UPPERCASE` | Transforms the entire string to *uppercase* |
     * | `TRANSFORM_CAPITALIZE_WORDS` | Transforms the first character of each word in the string to *uppercase* |
     * | `TRANSFORM_CAPITALIZE_FIRST` | Transforms the first character of the string to *uppercase*. |
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. |
     * @throws \TypeError Throws a `TypeError` if `$transformation` is invalid.
     */
    public function transform (int $transformation) {
      $string = $this->string;

      if ($this->resolved_string_mode == self::STRING_MODE_STRING) {
        $string = (function () use ($transformation, $string) {
          switch ($transformation) {
            case TRANSFORM_LOWERCASE:
              return \strtolower($string);
            case TRANSFORM_UPPERCASE:
              return \strtoupper($string);
            case TRANSFORM_CAPITALIZE_WORDS:
              return \ucwords($string);
            case TRANSFORM_CAPITALIZE_FIRST:
              return \ucfirst($string);
          }
        })();
      }
      else if ($this->resolved_string_mode == self::STRING_MODE_MB_STRING) {
        $string = (function () use ($transformation, $string) {
          $caseConsts = [
            TRANSFORM_LOWERCASE        => MB_CASE_LOWER,
            TRANSFORM_UPPERCASE        => MB_CASE_UPPER,
            TRANSFORM_CAPITALIZE_WORDS => MB_CASE_TITLE
          ];

          if ($transformation != TRANSFORM_CAPITALIZE_FIRST) {
            return \mb_convert_case($this->string, $caseConsts[$transformation], $this->encoding);
          }
          else {
            $chars = $this->split();
            $chars[0] = \mb_strtoupper($chars[0], $this->encoding);

            return \implode('', $chars);
          }
        })();
      }

      return $this->handle_modify_return($string);
    }
    /** Slice the `string` into a piece.
     * 
     * This *changes* the string. To simply retrieve a slice of the `string`, use the `substr()` method.
     * - @see StringObj::substr()
     * 
     * - To split a string using substrings, use the `str_replace()` method.
     * - To split a string using complex searches and replacements, use the `preg_replace()` method.
     * 
     * @param int $start Where the slice begins. 
     * - A *positive offset* counts from the beginning of the `string`, while a *negative offset* counts from the end.
     * @param int $length 
     * - A *positive length* indicates the maximum number of characters after the specified `$start` to include in the slice. 
     * - A *negative length* indicates the number of characters from the end of the `string` to be omitted.
     * - If omitted, the slice will continue from the `$start` to the end of the `string`.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. |
     */
    public function slice (int $start = 0, int $length = null) {
      $slice = $this->substr($start, $length);

      // if ($slice === false) {
      //   $strlen = $this->strlen();

      //   if ($strlen < $start) {
      //     throw new \OutOfRangeException("The Start Position is {$start}, but the string is only {$strlen} characters long.");
      //   }
      //   else if (isset($length) && (0 > $length) && (($strlen - $start) + $length) < 0) {
      //     throw new \OutOfRangeException("The Start Position is {$start}, but the string is only {$strlen} characters long and the Length is trying to remove {$length} characters.");
      //   }
      // }

      return $this->handle_modify_return($slice);
    }
    /** Replace all occurrences of a search string with a replacement string within the string
     * 
     * - To split a string into pieces every variable number of characters, use the `slice()` method.
     * - To split a string using more complex searches and replacements, use the `preg_replace()` method.
     * 
     * @param string|array $search The Search *Needle*, provided as a single `string`, or as an `array` of needles.
     * @param string|array $replacement The replacement value for each `$search` match, provided as a single `string`, or as an `array` of replacements.
     * - The values of both `$search` and `$replacement` determine how the `string` is transformed:
     * 
     * | `$search` | `$replacement` | Behavior |
     * | --- | --- | --- |
     * | `string` | `string` | All occurrences of the `$search` string will be replaced by the `$replacement` string. | 
     * | `array` | `string` | All `$search` matches will be replaced by the `$replacement` string. |
     * | `string` | `array` | All occurrences of the `$search` string will be replaced by the first `$replacement` value. |
     * | `array` | `array` | All `$search` matches will be replaced by the corresponding `$replacement` value. Elements are processed first to last. | 
     * | | | If `$search` has fewer values than `$replacement`, the extra `$replacement` values will be discarded. |
     * | | | If `$replacement` has fewer values than `$search`, the extra `$search` matches will be ignored. |
     * @param bool $case_insensitive Indicates if the `$search` match(es) should be matched *Case Insensitively*.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. |
     */
    public function str_replace ($search, $replacement, $case_insensitive = false) {
      $string = $this->string;
      $count = 0;
      $execArgs = (function () use ($search, $replacement, &$count) {
        $execArgs = [ 
          $search, 
          $replacement, 
          $this->string, 
          $count 
        ];

        if (is_string($search) && is_array($replacement)) {
          $execArgs[0] = [ $search ];
        }
        if (is_array($search) && \is_array($replacement)) {
          if (count($search) > count($replacement)) {
            $execArgs[0] = array_slice($search, 0, count($replacement));
          }
        }

        return $execArgs;
      })();
      
      if (!$case_insensitive) {
        $string = \str_replace(...$execArgs);      
      }
      else {
        $string = \str_ireplace(...$execArgs);
      }

      return $this->handle_modify_return($string);
    }
    /** Perform a *Global Regular Expression Match* on the string
     * 
     * - To split a string into pieces every variable number of characters, use the `slice()` method.
     * - To split a string using simple substrings, use the `str_replace()` method.
     * 
     * @param string|array $pattern The *Regular Expression Pattern*. 
     * - This can be a single pattern in the form of a `string`, or multiple patterns in the form of an `array`. See `$replacement` for more information on multiple pattern behavior.
     * - The `g` (`PCRE_GLOBAL`) modifier does not need to be used, as the replacement is *global* by default.
     * - You should **not** specify the `u` (`PCRE_UTF8`) modifier, as it is automatically added as needed.
     * @param string|array|callback $replacement The value to replace each string matched by the `$pattern` with. 
     * - The replacement strings may contain references to a captured subpattern using the `\n` or `$n` syntax, with `n` being an integer from 0-99 that refers to the `n`th capture group from the `$pattern`.
     * - - `\0` or `$0` refers to the string matched by the whole pattern.
     * - - Captured subpatterns are counted from left to right, starting from **1**.
     * - - The `${n}` syntax can be used when a backreference is immediately followed by a number. E.g. `${1}1`
     * - This argument has two usages which *cannot be mixed*.
     * - - In the first usage, a replacement `string` is provided:
     * - - - If `$pattern` is also a `string`, strings matched by the pattern will be replaced by the `$replacement` string.
     * - - - If `$pattern` is an `array`, all pattern matches will be replaced by the `$replacement` string.
     * - - - You can also specify multiple replacements as an `array` of replacement `strings`.
     * - - - - If `$pattern` is a `string`, strings matched by the pattern will be replaced by the first `$replacement` string.
     * - - - - If `$pattern` is also an `array`, each `$pattern` match will be replaced by its `$replacement` counterpart.
     * - - - - - If there are fewer `$pattern` elements than `$replacement` elements, all extra `$replacement` strings won't be used.
     * - - - - - If there are fewer `$replacement` elements than `$pattern` elements, all extra `$pattern` matches remain unchanged. 
     * - - In the second usage, a replacement `callback` is provided.
     * - - - > `handler ( array $matches ): string`
     * - - - The callback is provided a single argument: the `$matches` of the `$pattern`. The first element is the string matched by the whole pattern, the second the first subpattern match, and so on.
     * - - - The callback should return the replacement as a `string`.
     * - - - If `$pattern` is also a `string`, strings matched by the pattern will be passed to the `$replacement` callback.
     * - - - If `$pattern` is an `array`, all pattern matches will be passed to the `$replacement` callback.
     * - - - You can also specify multiple callbacks as an `array` of replacement `callbacks`.
     * - - - - If `$pattern` is a `string`, strings matched by the pattern will be passed to the first `$replacement` callback.
     * - - - - If `$pattern` is also an `array`, each `$pattern` match will be passed to its `$replacement` callback counterpart.
     * - - - - - If there are fewer `$pattern` elements than `$replacement` elements, all extra `$replacement` callbacks won't be used.
     * - - - - - If there are fewer `$replacement` callbacks than `$pattern` elements, all extra `$pattern` matches remain unchanged. 
     * @param int $limit The maximum number of replacements that can be done for each `$pattern`. **-1** indicates that there is no limit to the number of replacements performed.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. |
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if any of the follow issues occur:
     * - The `$replacement` is an `array` that contains an element that is not a `string` or `callable` function.
     * - The `$replacement` contains a mixture of `strings` and `callable` functions.
     * @throws \Exception if an error occurred while invoking `preg_replace()`, `preg_replace_callback()`, or `preg_replace_callback_array()`.
     */
    public function preg_replace ($pattern, $replacement, $limit = -1) {
      $string = $this->string;
      $replacementType = (function () use ($replacement) {
        $checkVar = function ($var) {
          if (is_string($var)) {
            return 'STRING';
          }
          else if (is_callable($var)) {
            return 'CALLBACK';
          }
          else {
            throw new \TypeError("Replacements must be in the form of a String or Callback Function.");
          }
        };

        if (is_array($replacement)) {
          return $checkVar($replacement[0]);
        }
        else {
          return $checkVar($replacement);
        }
      })();
      /** Formatted/Updated Arguments */
      $args = [
        'pattern'     => $this->preg_check_pattern($pattern),
        'replacement' => (function () use ($pattern, $replacement, $replacementType) {
          $arg = $replacement;
          $definedType = null;

          // Check replacement values
          if (is_array($replacement)) {
            foreach ($replacement as $replacementIndex => $replacementValue) {
              $replacementNum = $replacementIndex + 1;
              $type = (function () use ($replacementNum, $replacementValue) {
                if (is_string($replacementValue)) {
                  return 'String';
                }
                else if (is_callable($replacementValue)) {
                  return 'Callback Function';
                }
                else {
                  throw new \UnexpectedValueException("Replacement #{$replacementNum} is not a String or Callback Function.");
                }
              })();

              if (!isset($definedType)) {
                $definedType = $type;
              }
              else if ($type != $definedType) {
                throw new \UnexpectedValueException("Replacements cannot be a mixture of Strings and Callback Functions. Replacement #{$replacementNum} is a {$type} while the previous replacements are {$definedType}s.");
              }
            }
          }
          else if (!is_string($replacement) && !is_callable($replacement)) {
            throw new \UnexpectedValueException("Replacement is not a String or Callback Function.");
          }

          if (is_array($pattern) && is_array($replacement)) {
            $replacementValue = $replacementType == 'STRING'
                                ? '$0'
                                : function ($matches) { return $matches[0]; };

            $arg = array_pad($arg, count($pattern), $replacementValue);
          }

          return $arg;
        })()
      ];
      $count = 0;

      if ($replacementType == 'STRING') {
        $string = \preg_replace($args['pattern'], $args['replacement'], $string, $limit, $count);
      }
      else if ($replacementType == 'CALLBACK') {
        if (!is_array($replacement)) {
          $string = \preg_replace_callback($args['pattern'], $args['replacement'], $string, $limit, $count);
        }
        else {
          $mappings = (function () use ($args) {
            $mappings = [];

            if (is_array($args['pattern'])) {
              foreach ($args['pattern'] as $index => $regex) {
                $mappings[$regex] = $args['replacement'][$index];
              }
            }
            else {
              $mappings[$args['pattern']] = $args['replacement'][0];
            }

            return $mappings;
          })();

          $string = \preg_replace_callback_array($mappings, $string, $limit, $count);
        }
      }

      if ($string === null) {
        throw new \Exception("An error occurred while attempting to modify the string.");
      }

      return $this->handle_modify_return($string);
    }
    /** Appends a plural letter to the string depending on the value of a given number.
     *
     * @param int $value The value to be evaluated. If this value is not equal to **1**, a plural letter will be appended to the string.
     * @param bool $apostrophe Indicates if an *apostrophe* (`'`) should be included with the plural letter.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. |
     */
    public function add_plural (int $value, $apostrophe = false) {
      $string = ($value !== 1
                ? ($apostrophe
                  ? "{$this->string}'s"
                  : "{$this->string}s"
                )
                : $this->string);

      return $this->handle_modify_return($string);
    }

    /** Trim whitespace, or other characters, from the beginning and/or end of the string.
     * 
     * @param STR_SIDE_BOTH|STR_LEFT|STR_RIGHT $trim_side A `STR_SIDE_*` constant indicating which sides(s) of the string are to be trimmed.
     * @param string $charlist The list of characters that will be trimmed from the string.
     * - By default, the `$charlist` is a list of whitespace characters: ` \n\r\t\v\s`.
     * - Any Character or Escaped Character supported by a *Regular Expression Character Set* can be provided, with the exception of the *Tilde* (`~`).
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     */
    public function trim (int $trim_side = STR_SIDE_BOTH, string $charlist = " \n\r\t\v\s") {
      $patterns = (function () use ($trim_side, $charlist) {
        $patterns = [];
        $charlistStr = str_replace($charlist, '~', '\~');

        if ($trim_side == STR_SIDE_LEFT) {
          $patterns[] = "~^[{$charlistStr}]+~";
        }
        if ($trim_side == STR_SIDE_RIGHT) {
          $patterns[] = "~[{$charlistStr}]+$~";
        }

        return $patterns;
      })();
      $string = preg_replace($this->string, $patterns, '');

      if (substr_check($charlist, $this->char(-1))) {
        $string = slice($string, 0, -1);
      }

      return $this->handle_modify_return($string);
    }
    /** Collapse whitespace, or other characters, within the string.
     * 
     * @param string $charlist The list of characters that will be collapsed in the string.
     * - By default, the `$charlist` is a list of whitespace characters: ` \n\r\t\v\s`.
     * - Any Character or Escaped Character supported by a *Regular Expression Character Set* can be provided, with the exception of the *Tilde* (`~`).
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     */
    public function collapse (string $charlist = " \n\r\t\v\s") {
      $charlistStr = str_replace($charlist, '~', '\~');
      $string = preg_replace($this->string, "/[{$charlistStr}]{2,}/", function ($matches) {
        return $matches[0][0];
      });

      return $this->handle_modify_return($string);
    }
    /** Pad the string to a certain length with another string
     * 
     * @param int $padding_length The desired length of the string.
     * - If negative, less than, or equal to the *length* of the `string`, no padding will be inserted.
     * @param string $padding The padding string used to pad the string.
     * - The `$padding` may be truncated if the required number of characters can't be evenly divided by its length.
     * @param STR_SIDE_BOTH|STR_SIDE_LEFT|STR_SIDE_RIGHT $padding_side A `STR_SIDE_*` constant indicating which side(s) of the string are to be padded.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     */
    public function pad (int $padding_length, string $padding = ' ', int $padding_side = STR_SIDE_RIGHT) {
      $string = $this->string;
      $funcPaddingSide = (function () use ($padding_side) {
        $funcPaddingSide = [
          STR_SIDE_BOTH  => STR_PAD_BOTH,
          STR_SIDE_LEFT  => STR_PAD_LEFT,
          STR_SIDE_RIGHT => STR_PAD_RIGHT
        ];

        return $funcPaddingSide[$padding_side];
      })();

      if ($this->resolved_string_mode == self::STRING_MODE_STRING) {
        $string = \str_pad($string, $padding_length, $padding, $funcPaddingSide);
      }
      else if ($this->resolved_string_mode == self::STRING_MODE_MB_STRING) {
        if (function_exists('mb_str_pad')) {
          $string = \mb_str_pad($string, $padding_length, $padding, $funcPaddingSide, $this->encoding);
        }
        else {
          $calculatedLength = (function () use ($padding_length) {
            $strObj = new StringObj($this->string, StringObj::EDITING_MODE_CHAIN, StringObj::STRING_MODE_STRING);

            $length = $strObj->strlen();

            $strObj->string_mode = StringObj::STRING_MODE_MB_STRING;
            $length -= $strObj->strlen();

            $length += $padding_length;

            return $length;
          })(); 

          $string = \str_pad($string, $calculatedLength, $padding, $funcPaddingSide);
        }
      }

      return $this->handle_modify_return($string);
    }
    /** Split the string into smaller chunks
     * 
     * @param int $chunk_length The length of a single chunk.
     * @param string $separator The separator character(s) to be placed between chunks.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     */
    public function chunk (int $chunk_length = 76, string $separator = "\r\n") {
      $string = $this->string;

      $string = $this->split($chunk_length);
      $string = implode($separator, $string);

      return $this->handle_modify_return($string);
    }

    /** Convert HTML Characters in the string into *HTML Entities*
     * 
     * Encodes the following characters:
     * 
     * | Character Name | Character | HTML Entity |
     * | --- | --- | --- |
     * | Ampersand | `&` | `&amp;` |
     * | Double Quote | `"` | `&quot;` |
     * | Single Quote | `'` | `&apos;` |
     * | Less Than | `<` | `&lt;` |
     * | Greater Than | `>` | `&gt;` |
     * 
     * @param bool $encode_everything Indicates if all characters with HTML Character Entity equivalents should be encoded, instead of just the special characters.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     */
    public function encode_html (bool $encode_everything = false) {
      $encoding = (function () {
        $encoding = $this->encoding != ENCODING_ASCII
                    ? $this->encoding
                    : ENCODING_UTF_8;

        if (array_search($encoding, self::HTML_ENCODING_SUPPORTED_ENCODINGS) !== false) {
          return $encoding;
        }
        else {
          return null;
        }
      })();
      $string = !$encode_everything
                ? \htmlspecialchars($this->string, ENT_HTML5|ENT_QUOTES, $encoding)
                : \htmlentities($this->string, ENT_QUOTES, $encoding);

      return $this->handle_modify_return($string);
    }
    /** Convert *HTML Entities* in the string back to their equivalent HTML Characters. 
     * 
     * Decodes the following characters:
     * 
     * | HTML Entity | Character | Character Name |
     * | --- | --- | --- |
     * | `&amp;` | `&` | Ampersand |
     * | `&quot;` | `"` | Double Quote |
     * | `&apos;` | `'` | Single Quote |
     * | `&lt;` | `<` | Less Than |
     * | `&gt;` | `>` | Greater Than |
     * 
     * @param bool $encode_everything Indicates if all HTML Character Entities should be decoded, instead of just the special characters.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     */
    public function decode_html (bool $decode_everything = false) {
      $encoding = (function () {
        $encoding = $this->encoding != ENCODING_ASCII
                    ? $this->encoding
                    : ENCODING_UTF_8;

        if (array_search($encoding, self::HTML_ENCODING_SUPPORTED_ENCODINGS) !== false) {
          return $encoding;
        }
        else {
          return null;
        }
      })();
      $string = !$decode_everything
                ? \htmlspecialchars_decode($this->string, ENT_QUOTES)
                : \html_entity_decode($this->string, ENT_QUOTES, $encoding);

      return $this->handle_modify_return($string);
    }
    /** Strip HTML & PHP tags from the string
     * 
     * @param null|int|array|string $allowed_tags A list of whitelisted tags. Can be one of multiple values:
     * - Passing **null** will strip all tags from the string.
     * - An `int` representing a *Preset Tag Threshold*:
     * - - @see Strings\STRIP_TAGS_STRICT
     * - - @see Strings\STRIP_TAGS_MEDIUM
     * - - @see Strings\STRIP_TAGS_LAX
     * - An `array` made up of the whitelisted tags.
     * - - E.g. `[ 'div', 'span', 'p' ]`
     * - A `string` made up of the whitelisted tags.
     * - - E.g. `<div><span><p>`
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$allowed_tags` is not of a valid format.
     */
    public function strip_tags ($allowed_tags = null) {
      $tagList = (function () use ($allowed_tags) {
        $thresholds = [
          STRIP_TAGS_STRICT => [
            'div',
            'span',
            'p'
          ],
          STRIP_TAGS_MEDIUM => [
            'strong',
            'b',
            'em',
            'ul',
            'ol',
            'li',
            'code',
            'pre'
          ],
          STRIP_TAGS_LAX => [
            'a',
            'button',
            'fieldset',
            'label',
            'legend',
            'input',
            'select',
            'option',
            'textarea'
          ]
        ];
        
        if (!isset($allowed_tags)) {
          return null;
        }
        else if (is_int($allowed_tags) && isset($thresholds[$allowed_tags]) || is_array($allowed_tags)) {
          $tags = [];

          if (is_int($allowed_tags)) {
            foreach ($thresholds as $const => $tresholdTags) {
              $tags = array_merge($tags, $tresholdTags);

              if ($const == $allowed_tags) {
                break;
              }
            }
          }
          else if (is_array($allowed_tags)) {
            $tags = $allowed_tags;
          }
          
          return '<' . implode('><', $tags) . '>';
        }
        else if (is_string($allowed_tags) && preg_test($allowed_tags, '/^(\<\w+\>)+$/')) {
          return $allowed_tags;
        }
        
        throw new \UnexpectedValueException("\"{$allowed_tags}\" is not a valid Tag Threshold Constant, Array, or String.");
      })();

      try {
        $string = \strip_tags($this->string, $tagList);

        return $this->handle_modify_return($string);
      }
      catch (\Throwable $exception) {
        throw new \UnexpectedValueException("\"{$allowed_tags}\" is not a valid Tag Threshold Constant, Array, or String.");
      }
    }
    /** Converts special characters in the string to their equivalent URL Character Codes.
     * 
     * **Note**: The string will be *trimmed* ({@see StringObj::trim()}) before being encoded.
     * 
     * @param bool $legacy_encode Indicates if *Legacy URL Encoding* should be performed, determining which specification should be followed when encoding the URL:
     * 
     * | Value | Specification | Description 
     * | --- | --- | --- |
     * | `false` | *RFC 3986* | All non-alphanumeric characters except `-`, `_`, `.`, & `~` are replaced with a percent (%) sign followed by two hex digits. |
     * | `true` | *RFC 1866* | All non-alphanumeric characters except `-`, `_`, & `.` are replaced with a percent (%) sign followed by two hex digits. Spaces are encoded as plus (`+`) signs. This is the same way as the `application/x-www-form-urlencoded` media type. |
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     */
    public function encode_url (bool $legacy_encode = false) {
      $string = $this->string;

      $string = trim($string);
      $string = !$legacy_encode
                ? \rawurlencode($string)
                : \urlencode($string);

      return $this->handle_modify_return($string);
    }
    /** Converts URL Character Codes in the string to their equivalent special characters.
     * 
     * @param bool $legacy_decode Indicates if *Legacy URL Decoding* should be performed, determining which specification should be followed when decoding the URL:
     * 
     * | Value | Specification | Description 
     * | --- | --- | --- |
     * | `false` | *RFC 3986* | All non-alphanumeric characters except `-`, `_`, `.`, & `~` can be decoded. |
     * | `true` | *RFC 1866* | All non-alphanumeric characters except `-`, `_`, & `.` can be decoded. Spaces are decoded from plus (`+`) signs. This is the same way as the `application/x-www-form-urlencoded` media type. |
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     */
    public function decode_url (bool $legacy_decode = false) {
      $string = $this->string;

      $string = !$legacy_decode
                ? \rawurldecode($string)
                : \urldecode($string);

      return $this->handle_modify_return($string);
    }
    /** Encode a string to be used as an identifier
     * 
     * @param int $encoding_style Indicates how the string will be encoded.
     * 
     * | Style | Example | Description |
     * | --- | --- | --- |
     * | `ENCODE_ID_SNAKE_CASE` | string_to_ID | Spaces are converted to Underscores (`_`) |
     * | `ENCODE_ID_CAMEL_CASE` | stringToID | Spaces are removed and the next immediate character is converted to *uppercase*. This is identical to `ENCODE_ID_PASCAL_CASE`, except that the first character of the string remains *lowercase*. |
     * | `ENCODE_ID_PASCAL_CASE` | StringToID | Spaces are removed and the next immediate character is converted to *uppercase*. This is identical to `ENCODE_ID_CAMEL_CASE`, except that the first character of the string is also converted to *uppercase*. |
     * | `ENCODE_ID_KEBAB_CASE` | string-to-ID | Spaces are converted to Dashes (`-`). |
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     */
    public function encode_id ($encoding_style = ENCODE_ID_SNAKE_CASE) {
      $string = $this->string;
      $replacement = function ($matches) use ($encoding_style) {
        switch ($encoding_style) {
          case ENCODE_ID_SNAKE_CASE:
            return "_{$matches[1]}";
          case ENCODE_ID_CAMEL_CASE:
            return transform($matches[1], TRANSFORM_UPPERCASE);
          case ENCODE_ID_PASCAL_CASE:
            return transform($matches[1], TRANSFORM_UPPERCASE);
          case ENCODE_ID_KEBAB_CASE:
            return "-{$matches[1]}";
        }
      };

      $string = preg_replace($string, '/[^\w\d\s]/', '');
      $string = preg_replace($string, '/\s+([\w\d])/', $replacement);

      if ($encoding_style == ENCODE_ID_PASCAL_CASE) {
        $string = transform($string, TRANSFORM_CAPITALIZE_FIRST);
      }

      return $this->handle_modify_return($string);
    }
    /** Escape a string for use in a *Regular Expression*.
     * 
     * @param null|string $delimiter The *Expression Delimiter* to also be escaped.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     */
    public function escape_reg($delimiter = null) {
      $string = preg_quote($this->string, $delimiter);

      return $this->handle_modify_return($string);
    }
    /** An *alias* for `ShiftCodesTKDatabase::escape_string()`.
     * 
     * > Escape a string for use in a SQL Query Statement.
     * 
     * > The following characters are escaped:
     * - `NUL` (ASCII 0)
     * - `\n`
     * - `\r`
     * - `\`
     * - `'`
     * - `"`
     * - `Control-Z`
     * @see ShiftCodesTKDatabase::escape_string()
     * 
     * @return StringObj|string Returns a `StringObj` or `string` depending on the `$editing_mode`:
     * 
     * | Editing Mode | Return Value |
     * | --- | --- |
     * | `EDITING_MODE_CHAIN` | Returns the `StringObj` for further manipulation. |
     * | `EDITING_MODE_STANDARD` | Returns the modified `string`. |
     * | `EDITING_MODE_COPY` | Returns a modified *copy* of the `string`. | 
     * @throws \RuntimeException Throws a `RuntimeException` if the method is called before the `Database` module has been loaded.
     */
    public function escape_sql () {
      if (!class_exists('ShiftCodesTKDatabase')) {
        throw new \RuntimeException("String could not be SQL-Escaped: The Database has not been instantiated yet! The Database module needs to be loaded first.");
      }

      $string = \ShiftCodesTKDatabase::escape_string($this->string);

      return $this->handle_modify_return($string);
    }

    /** Execute an aliased string method
     * 
     * @param string $method_name The name of the *aliased method*.
     * @param mixed $args The arguments to be passed to the *aliased method*. The string being evaluated should be passed as the first argument.
     * @return mixed Returns the value of the *aliased method*
     * @throws UnexpectedValueException Throws an `UnexpectedValueException` if `$method_name` is not a valid method name.
     */
    public static function alias ($method_name, ...$args) {
      $blacklist = [
        'preg_check_pattern',
        'handle_modify_return'
      ];
      $string = array_shift($args);
      $stringObj = new StringObj($string, self::EDITING_MODE_STANDARD);

      if (!method_exists(get_class($stringObj), $method_name)) {
        throw new \UnexpectedValueException("\"{$method_name}\" is not a valid method to be aliased.");
      }
      if (array_search($method_name, $blacklist) !== false) {
        throw new \UnexpectedValueException("\"{$method_name}\" cannot be aliased.");
      };

      return $stringObj->$method_name(...$args);
    }
    /** Test object support for a string.
     * 
     * Emits a warning if a method threw an exception during execution.
     * 
     * @param string $string The string to test. Note that longer strings are preferred, as shorter strings may cause unnecessary errors with some methods.
     * @return array Returns an `array` made up of results of the tested methods. The resulting `StringObj` can be accessed via the **string_obj** index.
     */
    public static function test_string_support (string $string) {
      $patterns = '/[\w\d\p{C}\p{S}]+/';
      $methods = [
        'check_encoding'    => [],
        'get_encoding'      => [],
        'check_string_mode' => [],
        'strlen'            => [],
        'char'              => [2],
        'firstchar'         => [],
        'lastchar'          => [],
        'split'             => [2],
        'explode'           => [' '],
        'substr'            => [2, -2],
        'substr_pos'        => [' '],
        'substr_check'      => [' '],
        'substr_count'      => [' '],
        'preg_match'        => [$patterns],
        'preg_test'         => [$patterns], 
        'transform'         => [TRANSFORM_CAPITALIZE_WORDS],
        'slice'             => [2, -2],
        'str_replace'       => [' ', "{ }"],
        'preg_replace'      => [$patterns, '[ $0 ]'],
        'add_plural'        => [2, true],
        'trim'              => [],
        'collapse'          => [],
        'encode_html'       => [],
        'decode_html'       => [],
        'strip_tags'        => [],
        'encode_url'        => [],
        'decode_url'        => [],
        'encode_id'         => [],
        'escape_reg'        => [],
        'escape_sql'        => []
      ];
      $stringObj = new StringObj($string, StringObj::EDITING_MODE_STANDARD);
      $result = [];

      foreach ($methods as $method_name => $args) {
        try {
          $result[$method_name] = $stringObj->$method_name(...$args);
        }
        catch (\Throwable $exception) {
          trigger_error("A method has failed: \"{$method_name}\". Error: {$exception->getMessage()}");
          $result[$method_name] = null;
          continue;
        }
      }

      $result['string_obj'] = $stringObj;

      return $result;
    }
  }
?>