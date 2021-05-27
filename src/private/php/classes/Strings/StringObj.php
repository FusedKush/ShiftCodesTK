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
  class StringObj implements Interfaces\StringInterface {
    use Traits\StringMode,
        Traits\EditingMode,
        Traits\SupportTester;

    /** @var int A list of Character Sets supported by the `encodeHtml()` and `decode_Html()` methods and functions. */
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

    /** @var string The original, unmodified {@see StringObj::$string}. */
    private $originalString = '';
    /** @var string Indicates the detected *Encoding* of the {@see StringObj::$string}, if available. */
    private $encoding = '';
    
    /** @var string The current string, after any modifications. */
    protected $string = '';

    /** Execute an aliased string method
     * 
     * @param string $method_name The name of the *aliased method*.
     * @param mixed $args The arguments to be passed to the *aliased method*. The string being evaluated should be passed as the first argument.
     * @return mixed Returns the value of the *aliased method*
     * @throws UnexpectedValueException Throws an `UnexpectedValueException` if `$method_name` is not a valid method name.
     */
    public static function alias ($method_name, ...$args) {
      $blacklist = [
        'pregCheckPattern',
        'handleModifyReturn'
      ];
      $string = array_shift($args);
      $string_obj = new StringObj($string, self::EDITING_MODE_STANDARD);

      if (!method_exists(get_class($string_obj), $method_name)) {
        throw new \UnexpectedValueException("\"{$method_name}\" is not a valid method to be aliased.");
      }
      if (array_search($method_name, $blacklist) !== false) {
        throw new \UnexpectedValueException("\"{$method_name}\" cannot be aliased.");
      };

      return $string_obj->$method_name(...$args);
    }
    
    /** Checks a *Regular Expression* or group of expressions to ensure they are compatible with *Multi-Byte Strings*.
     * 
     * @param string|array $pattern The *Regular Expression Pattern* to check or an `array` of patterns to be checked. 
     * @return string|array Returns the *Regular Expression Pattern* or `array` of patterns that were checked. If the string is in *Multi-Byte Mode*, the patterns were updated accordingly.
     */
    private function pregCheckPattern ($pattern) {
      $updated_pattern = $pattern;
      $resolved_string_mode = $this->getResolvedStringMode();

      $get_pattern = function ($pattern_string) use ($resolved_string_mode) {
        $str = $pattern_string;

        if ($resolved_string_mode === self::STRING_MODE_MB_STRING) {
          // Add Unicode Modifier
          $str .= 'u';
          
          // Replace groups
          $str = str_replace($str, [ '\w', '\W', '\d', '\D', '\s', '\S' ], [ '\p{L}', 'P{L}', '\p{N}', '\P{N}', '\p{Z}', '\P{Z}' ]);
        }

        return $str;
      };

      if ($resolved_string_mode === self::STRING_MODE_MB_STRING) {
        if (is_string($pattern)) {
          $updated_pattern = $get_pattern($updated_pattern);
        }
        else if (is_array($pattern)) {
          foreach ($updated_pattern as &$regex) {
            $regex = $get_pattern($regex);
          }
        }
      }

      return $updated_pattern;
    }
    /** Handles the Return Value of a *String Manipulation Method* based on the current {@see StringObj::$editingMode}.
     * 
     * @param string $string The modified string.
     * @return StringObj|string Returns the modified `StringObj` or `string` depending on the current `$editingMode`.
     */
    private function handleModifyReturn (string $string) {
      if ($this->editingMode != self::EDITING_MODE_COPY) {
        $this->string = $string;
      }

      switch ($this->editingMode) {
        case self::EDITING_MODE_CHAIN:
          return $this;
        case self::EDITING_MODE_STANDARD:
          return $string;
        case self::EDITING_MODE_COPY:
          return $string;
      }
    }

    /** Create a `StringArrayObj` for a given array, taking into account the {@see StringObj::$editingMode} and {@see StringObj::stringMode} properties of the current `StringObj`.
     * 
     * @param array $array The array being evaluated.
     * @return StringArrayObj Returns the new `StringArrayObj` on success.
     */
    public function getStringArray (array $array) {
      return new StringArrayObj($array, [
        'editing_mode'  => $this->editingMode,
        'string_mode'   => $this->getStringMode()
      ]);
    }
    /** Retrieve the current or original string
     * 
     * @param bool $return_original Indicates if the *Original String* should be returned instead of the current one.
     * @return string Returns the current or original string depending on the value of `$return_original`.
     */
    public function getString ($return_original = false): string {
      return !$return_original
        ? $this->string
        : $this->originalString;
    }
    /** Set or update the string
     * 
     * @param string $string The string being set.
     * @return bool Returns `true` on success and `false` on failure.
     */
    public function setString (string $string): bool {
      $this->string = $string;
      $this->originalString = $string;
      $this->encoding = $this->getEncoding();

      return true;
    }

    /** Check the encoding for the string
     * 
     * @param string $encoding The *String Encoding* to check the string for.
     * @param bool $throw_error If **true**, throws an `Error` if the string does not match the encoding of `$encoding`.
     * @return bool Returns **true** if the string matches the *String Encoding* of `$encoding`.
     * @throws \Error If `$throw_error` is **true**, throws an `Error` if the string does not match the encoding of `$encoding`.
     */
    public function checkEncoding (
      string $encoding = ENCODING_UTF_8, 
      bool $throw_error = false
    ): bool {
      $result = \mb_check_encoding($this->string, $encoding);
  
      if (!$result && $throw_error) {
        throw new \Error("String Encoding is not \"{$this->encoding}\".");
      }
  
      return $result;
    }
    /** Attempt to get the encoding for the string
     * 
     * @return string|null Returns the *Encoding* of the string on success, or `null` if the encoding could not be detected.
     */
    public function getEncoding (): ?string {
      foreach (ENCODING_LIST as $encoding) {
        if ($this->checkEncoding($encoding)) {
          return $encoding;
        }
      }
  
      $result = \mb_detect_encoding($this->string, ENCODING_LIST, true);

      return $result !== false
        ? $result
        : null;
    }
    
    /** Get the *Resolved String Mode* of the string
     * 
     * @return int|null Returns an `int` representing the *Resolved String Mode* of the string.
     * Returns `null` if the *Resolved String Mode* could not be determined.
     */
    public function getResolvedStringMode(): ?int {
      $string_mode = $this->getStringMode();
      $encoding = $this->getEncoding();

      if (!$encoding) {
        return null;
      }

      return self::determineResolvedStringMode($string_mode, $encoding);
    }

    /** Get the length of the string
     * 
     * @return int Returns the number of characters in the `string`.
     */
    public function strlen (): int {
      return $this->getResolvedStringMode() === self::STRING_MODE_STRING
             ? \strlen($this->string)
             : \mb_strlen($this->string, $this->encoding);
    }
    /** Retrieve a character in the string
     * 
     * @param int $char Indicates the *Character Position* within the `string` of the character to be retrieved.
     * - A positive value indicates the character position relative to the *start* of the string, while a negative values are relative to the *end*.
     * - **1** refers to the first character in the string, while **-1** refers to the last. **0** is treated as **1**.
     * @return string Returns the character found in the string at `$char`. 
     * If `$char` exceeds the length of the string, returns an *Empty `String`*.
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
     * @return string Returns the first character found in the string.
     */
    public function firstchar (): string {
      return $this->char(1);
    }
    /** Get the last character of the string
     * 
     * @return string Returns the last character found in the string.
     */
    public function lastchar (): string {
      return $this->char(-1);
    }
    /** Convert the string's characters to an array.
     * 
     * @param int $length The maximum length of each character chunk.
     * @return array|null On success, returns an `array` made up of the characters of the `string`. 
     * - If `$length` is less than *1*, returns `null`.
     * - If `$length` is greater than the length of the `string`, the entire string will be returned as the only element of the array.
     */
    public function split (int $length = 1): ?array {
      $array = [];
      $resolved_string_mode = $this->getResolvedStringMode();

      if ($length < 1) {
        return null;
      }

      if ($resolved_string_mode == self::STRING_MODE_STRING) {
        $array = \str_split($this->string, $length);
      }
      else if ($resolved_string_mode == self::STRING_MODE_MB_STRING) {
        if (function_exists('mb_str_split')) {
          $array = \mb_str_split($this->string, $length, $this->encoding);
        }
        else {
          $array = $this->pregMatch("/(.|\r|\n){{$length}}|(.|\r|\n){1,5}$/", PREG_GLOBAL_SEARCH|PREG_RETURN_FULL_MATCH);
        }
      }

      if (!is_array($array)) {
        $array = null;
      }

      return $array;
    }
    /** Split the string by another string.
     * 
     * @param string $delimiter The delimiter to split the `string` by. Can be a string of delimiter characters, or a *Regular Expression Pattern*.
     * @param int|null $limit The maximum number of splits to be performed.
     * - If positive, the result array will only contain up to this number of substrings. The last substring will contain the remainder of the `string`.
     * - If negative, all substrings except the last `$limit` are returned.
     * - If **0**, this argument is treated as **1**.
     * @return array|null Returns an `array` of substrings created by splitting the `string` by the `$delimiters` on success. 
     * - If `$delimiters` contains a value not contained within the `string`, returns an `array` containing the full `string`.
     * - If `$delimiters` is an *Empty `String`*, returns `null`.
     * - If a negative `$limit` is provided and truncates more than the total number of results, returns an *Empty `Array`*.
     */
    public function explode (string $delimiter = ' ', int $limit = null): ?array {
      $result = [];
      $is_regex = (function () use ($delimiter) {
        $delimiters_obj = new StringObj($delimiter);

        if ($pattern_delimiters = $delimiters_obj->pregMatch('/^([^\w\d\s]).+([^\w\d\s])$/', PREG_RETURN_SUB_MATCHES)) {
          if ($pattern_delimiters[0] == $pattern_delimiters[1]) {
            if (@\preg_match($delimiter, '') !== false) {
              return true;
            }
          }
        }

        return false;
      })();
      $exec_args = (function () use ($delimiter, $limit, $is_regex) {
        $exec_args = [
          $is_regex
            ? $this->pregCheckPattern($delimiter)
            : $delimiter, 
          $this->string
        ];

        if (isset($limit) && $limit >= 0) {
          if ($limit === 0) {
            $exec_args[] = 1;
          }
          else {
            $exec_args[] = $limit;
          }
        }

        return $exec_args;
      })();

      if (empty($delimiter)) {
        return false;
      }
      if (!$is_regex) {
        $result = \explode(...$exec_args);
      }
      else {
        $result = \preg_split(...$exec_args);
      }

      if (isset($limit) && $limit < 0) {
        $result = \array_slice($result, 0, 0 + $limit, true);
      }

      return $result;
    }

    /** Extract a slice from the `string`
     * 
     * This does *not* change the string. To change string, use the {@see StringObj::slice()} method.
     * 
     * @param int $start Where the slice begins. 
     * - A *positive offset* counts from the beginning of the `string`, while a *negative offset* counts from the end.
     * @param int|null $length Indicates the maximum length of the slice.
     * - A *positive length* indicates the maximum number of characters after the specified `$start` to include in the slice. 
     * - A *negative length* indicates the number of characters from the end of the `string` to be omitted.
     * - If omitted, the slice will continue from the `$start` to the end of the `string`.
     * @param bool $throw_errors If **true**, an `OutOfRangeException` will be thrown if the provided arguments are invalid, instead of simply returning an *Empty `String`.
     * @return string Returns a slice of the `string` on success. 
     * If the `string` is less than `$start` characters long, or `$length` is *negative* and tries to truncate more characters than are available, returns an *Empty `String`*.
     * @throws \OutOfRangeException If `$throw_errors` is **true**, an `OutOfRangeException` will be thrown if the provided arguments are invalid.
     */
    public function substr (
      int $start = 0, 
      int $length = null, 
      bool $throw_errors = false
    ): string {
      try {
        $strlen = $this->strlen();
        $resolved_string_mode = $this->getResolvedStringMode();
        $args = (function () use ($start, $length, $resolved_string_mode) {
          $args = [ $this->string, $start ];
  
          if (isset($length)) {
            $args[] = $length;
          }
          if ($resolved_string_mode == self::STRING_MODE_MB_STRING) {
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
  
        $result = $resolved_string_mode == self::STRING_MODE_STRING
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
     * - If the `SUBSTR_SEARCH_AS_HAYSTACK` flag is omitted, `$search` is used as the *needle*, with the string as the *haystack*. 
     * Searches for the first or last occurrence of the `$search` substring within `string`.
     * - If the `SUBSTR_SEARCH_AS_HAYSTACK` flag is present, `$search` is used as the *haystack*, with the string as the *needle*. 
     * Searches for the first or last occurrence of the `string` substring within `$search`.
     * @param int $offset The search offset. 
     * - A positive offset counts from the beginning of the *haystack*, while a negative offset counts from the end. This effect is *reversed* if the `SUBSTR_GET_LAST_OCCURRENCE` flag is present.
     * @param int $flags A bitmask integer representing the search flags:
     * - {@see Strings\SUBSTR_SEARCH_AS_HAYSTACK}
     * - {@see Strings\SUBSTR_GET_LAST_OCCURRENCE}
     * - {@see Strings\SUBSTR_CASE_INSENSITIVE}
     * @return int|null On success, returns the *first* or *last occurrence* of the *needle* within the *haystack*, dependent on the provided `$flags`.
     *  If the *needle* was not found, returns `null`.
     */
    public function substrPos (
      string $search, 
      int $offset = 0, 
      int $flags = 0
    ): ?int {
      $search_as_haystack = ($flags & SUBSTR_SEARCH_AS_HAYSTACK) != 0;
      $get_last_occurrence = ($flags & SUBSTR_GET_LAST_OCCURRENCE) != 0;
      $case_insensitive = ($flags & SUBSTR_CASE_INSENSITIVE) != 0;
      $needle = !$search_as_haystack
                ? $search
                : $this->string;
      $haystack = !$search_as_haystack
                  ? $this->string
                  : $search;
      $result = null;
      $resolved_string_mode = $this->getResolvedStringMode();

      if ($case_insensitive) {
        $needle = transform($needle, TRANSFORM_LOWERCASE);
        $haystack = transform($haystack, TRANSFORM_LOWERCASE);
      }

      // String Mode
      if ($resolved_string_mode == self::STRING_MODE_STRING) {
        $args = [ $haystack, $needle, $offset ];

        // Get First Occurrence
        if ($get_last_occurrence) {
          $result = \strpos(...$args);
        }
        // Get Last Occurrence
        else {
          $result = \strrpos(...$args);
        }
      }
      // Multi-Byte String Mode
      else if ($resolved_string_mode == self::STRING_MODE_MB_STRING) {
        $args = [ $haystack, $needle, $offset, $this->encoding ];

        if ($get_last_occurrence) {
          $result = \mb_strpos(...$args);
        }
        else {
          $result = \mb_strrpos(...$args);
        }
      }

      if (!is_int($result)) {
        $result = null;
      }

      return $result;
    }
    /** Checks for the presence of substring within a string
     * 
     * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag:
     * - If the `SUBSTR_SEARCH_AS_HAYSTACK` flag is omitted, `$search` is used as the *needle*, with `string` as the *haystack*. Searches for an occurrence of the `$search` substring within `string`.
     * - If the `SUBSTR_SEARCH_AS_HAYSTACK` flag is present, `$search` is used as the *haystack*, with `string` as the *needle*. Searches for an occurrence of the `string` substring within `$search`.
     * @param int $offset The search offset. 
     * - A positive offset counts from the beginning of the *haystack*, while a negative offset counts from the end. 
     * @param int $flags A bitmask integer representing the search flags:
     * - {@see Strings\SUBSTR_SEARCH_AS_HAYSTACK}
     * - {@see Strings\SUBSTR_CASE_INSENSITIVE}
     * @return bool Returns **true** if the *needle* was found in the *haystack*, dependent on the provided `$flags`. 
     * Otherwise, returns `false`.
     */
    public function substrCheck (
      string $search, 
      int $offset = 0, 
      int $flags = 0
    ): bool {
      return $this->substrPos($search, $offset, $flags) !== false;
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
     * - {@see Strings\SUBSTR_SEARCH_AS_HAYSTACK}
     * - {@see Strings\SUBSTR_CASE_INSENSITIVE}
     * @return int Returns the number of times the *needle* occurs in the *haystack*, dependent on the provided `$flags`.
     */
    public function substrCount (
      string $search, 
      int $offset = 0, 
      int $length = null, 
      int $flags = 0
    ): int {
      $search_as_haystack = ($flags & SUBSTR_SEARCH_AS_HAYSTACK) != 0;
      $case_insensitive = ($flags & SUBSTR_CASE_INSENSITIVE) != 0;
      $needle = !$search_as_haystack
                ? $search
                : $this->string;
      $haystack = !$search_as_haystack
                  ? $this->string
                  : $search;
      $resolved_string_mode = $this->getResolvedStringMode();

      if ($case_insensitive) {
        $needle = transform($needle, TRANSFORM_LOWERCASE);
        $haystack = transform($haystack, TRANSFORM_LOWERCASE);
      }
      if (isset($offset) && isset($length)) {
        if (($offset + $length) > strlen($haystack)) {
          trigger_error("The \"offset\" plus the \"length\" exceeds the length of the search haystack.", E_USER_WARNING);
        }
      }

      // String Mode
      if ($resolved_string_mode == self::STRING_MODE_STRING) {
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
      else if ($resolved_string_mode == self::STRING_MODE_MB_STRING) {
        return \mb_substr_count($haystack, $needle, $this->encoding);
      }
    }

    /** Perform a *Regular Expression Match* on the string
     * 
     * @param string $pattern The *Regular Expression Pattern*.
     * - You **cannot** use the `g` (`PCRE_GLOBAL`) modifier. To perform a *global search*, pass the `PREG_GLOBAL_SEARCH` flag to `$flags`.
     * - You should **not** specify the `u` (`PCRE_UTF8`) modifier, as it is automatically added as needed.
     * @param int $flags A `PREG_*` constant integer representing the Search Flags.
     * - {@see PREG_GLOBAL_SEARCH}
     * - {@see PREG_RETURN_FULL_MATCH}
     * - {@see PREG_RETURN_SUB_MATCHES}
     * - {@see PREG_OFFSET_CAPTURE}
     * - {@see PREG_UNMATCHED_AS_NULL}
     * @param int $offset Specifies where the beginning of the search should start (in bytes).
     * @return array|null 
     * - On success, returns an `array` or `StringArrayObj` made up of the search results, formatted by the provided `$flags`. 
     * - - The first item contains the text that matched the full pattern, with subsequent items containing the text that matches a captured subpattern.
     * - - If the `PREG_RETURN_FULL_MATCH` flag was passed, only the *full pattern match* will be returned.
     * - - If the `PREG_RETURN_SUB_MATCHES` flag was passed, only the *matched subpatterns* will be returned.
     * - - If the `PREG_OFFSET_CAPTURE` flag was passed, each match will include the *offset* (in bytes).
     * - - If the `PREG_UNMATCHED_AS_NULL` flag was passed, unmatched subpatterns will be reported as **null**, instead of as an *empty string*.
     * - If the `$pattern` doesn't match the `string`, returns `null`.
     */
    public function pregMatch (
      string $pattern, 
      int $flags = 0, 
      int $offset = 0
    ): ?array {
      $pattern_str = $this->pregCheckPattern($pattern);
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

        return $result;
      }

      return null;
    }
    /** Test if the string matches a *Regular Expression*
     * 
     * @param string $pattern The *Regular Expression Pattern*.
     * - You **cannot** use the `g` (`PCRE_GLOBAL`) modifier for testing a pattern.
     * - You should **not** specify the `u` (`PCRE_UTF8`) modifier, as it is automatically added as needed.
     * @param int $offset Specifies where the beginning of the search should start (in bytes).
     * @return bool Returns `true` if the string matches the `$pattern`, or `false` if it does not.
     */
    public function pregTest (string $pattern, int $offset = 0): bool {
      return $this->pregMatch($pattern, 0, $offset) !== null;
    }

    /** Transform the capitalization of the `string`
     * 
     * @param int $transformation A `TRANSFORM_*` constant value indicating how the string is to be transformed.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editing_mode}.
     * @throws \TypeError if `$transformation` is invalid.
     */
    public function transform (int $transformation) {
      $string = $this->string;
      $resolved_string_mode = $this->getResolvedStringMode();

      if ($resolved_string_mode == self::STRING_MODE_STRING) {
        $string = (function () use ($transformation, $string) {
          if ($transformation === TRANSFORM_UPPERCASE) {
            return \strtoupper($string);
          }
          
          $string = strtolower($string);
  
          if ($transformation === TRANSFORM_CAPITALIZE_WORDS) {
            return \ucwords($string);
          }
          else if ($transformation === TRANSFORM_CAPITALIZE_FIRST) {
            return \ucfirst($string);
          }
          
          return $string;
        })();
      }
      else if ($resolved_string_mode == self::STRING_MODE_MB_STRING) {
        $string = (function () use ($transformation, $string) {
          $convert = function ($mode) {
            return \mb_convert_case($this->string, $mode, $this->encoding);
          };
          
          if ($transformation === TRANSFORM_UPPERCASE) {
            return $convert(MB_CASE_UPPER);
          }
          
          $string = $convert(MB_CASE_LOWER);
  
          if ($transformation === TRANSFORM_CAPITALIZE_WORDS) {
            return $convert(MB_CASE_TITLE);
          }
          else if ($transformation === TRANSFORM_CAPITALIZE_FIRST) {
            $chars = $this->split();
            $chars[0] = \mb_strtoupper($chars[0], $this->encoding);

            return \implode('', $chars);
          }
          
          return $string;
        })();
      }

      return $this->handleModifyReturn($string);
    }
    /** Change the *Case Styling* of the string
     * 
     * @param int $casing_style A `CASING_STYLE_*` namespace constant indicating how the string is to be cased.
     * - See {@see CASING_STYLE_LIST}
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     * @throws \TypeError Throws a `TypeError` if `$casing_style` is invalid.
     */
    public function changeCase (int $casing_style) {
      if (!Validations\check_match($casing_style, CASING_STYLE_LIST)) {
        throw new \TypeError("\"{$casing_style}\" is not a valid Casing Style Integer.");
      }

      $string = $this->string;
      $replacement = function ($matches) use ($casing_style) {
        switch ($casing_style) {
          case CASING_STYLE_SNAKE_CASE:
            return "_{$matches[1]}";
          case CASING_STYLE_CAMEL_CASE:
            return transform($matches[1], TRANSFORM_UPPERCASE);
          case CASING_STYLE_PASCAL_CASE:
            return transform($matches[1], TRANSFORM_UPPERCASE);
          case CASING_STYLE_KEBAB_CASE:
            return "-{$matches[1]}";
        }
      };

      $string = preg_replace($string, '/\s+([\w\d])/', $replacement);

      if ($casing_style == CASING_STYLE_PASCAL_CASE) {
        $string = transform($string, TRANSFORM_CAPITALIZE_FIRST);
      }

      return $this->handleModifyReturn($string);
    }
    /** Slice the string into a piece
     * 
     * This *modifies* the string. 
     * - To simply retrieve a slice of the string, use the {@see StringObj::substr()} method.
     * - To split a string using substrings, use the {@see StringObj::strReplace()} method.
     * - To split a string using complex searches and replacements, use the {@see StringObj::pregReplace()} method.
     * 
     * @param int $start Where the slice begins. 
     * - A *positive offset* counts from the beginning of the `string`, while a *negative offset* counts from the end.
     * @param int $length 
     * - A *positive length* indicates the maximum number of characters after the specified `$start` to include in the slice. 
     * - A *negative length* indicates the number of characters from the end of the `string` to be omitted.
     * - If omitted, the slice will continue from the `$start` to the end of the `string`.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function slice (int $start = 0, int $length = null) {
      $slice = $this->substr($start, $length);

      return $this->handleModifyReturn($slice);
    }
    /** Replace all occurrences of a search string within the string
     * 
     * - To split a string into pieces every variable number of characters, use the `slice()` method.
     * - To split a string using more complex searches and replacements, use the `preg_replace()` method.
     * 
     * @param string|array $search The Search *Needle*, provided as a single `string`, or as an `array` of needles.
     * @param string|array $replacement The replacement value for each `$search` match, provided as a single `string`, or as an `array` of replacements.
     * - The values of both `$search` and `$replacement` determine how the `string` is transformed:
     * - - If both arguments are `strings`, 
     * all occurrences of the `$search` string will be replaced by the `$replacement` string.
     * - - If the `$search` is an `array` and the `$replacement` a `string`, 
     * all `$search` matches will be replaced by the `$replacement` string.
     * - - If the `$search` is a `string` and the `$replacement` an `array`,
     * all occurrences of the `$search` string will be replaced by the first `$replacement` value.
     * - - If both arguments are `arrays`, 
     * all `$search` matches will be replaced by the corresponding `$replacement` value.
     * - - - Elements are processed first to last.
     * - - - If `$search` has fewer values than `$replacement`, the extra `$replacement` values will be discarded.
     * - - - If `$replacement` has fewer values than `$search`, the extra `$search` matches will be ignored.
     * @param bool $case_insensitive Indicates if the `$search` match(es) should be matched *Case Insensitively*.
     * Defaults to `false`.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function strReplace ($search, $replacement, $case_insensitive = false) {
      $string = $this->string;
      $count = 0;
      $exec_args = (function () use ($search, $replacement, &$count) {
        $exec_args = [ 
          $search, 
          $replacement, 
          $this->string, 
          $count 
        ];

        if (is_string($search) && is_array($replacement)) {
          $exec_args[0] = [ $search ];
        }
        if (is_array($search) && \is_array($replacement)) {
          if (count($search) > count($replacement)) {
            $exec_args[0] = array_slice($search, 0, count($replacement));
          }
        }

        return $exec_args;
      })();
      
      if (!$case_insensitive) {
        $string = \str_replace(...$exec_args);      
      }
      else {
        $string = \str_ireplace(...$exec_args);
      }

      return $this->handleModifyReturn($string);
    }
    /** Perform a *Global Regular Expression Replacement* on the string
     * 
     * - To split a string into pieces every variable number of characters, use the {@see StringObj::slice()} method.
     * - To split a string using simple substrings, use the {@see StringObj::strReplace()} method.
     * 
     * @param string|array $pattern The *Regular Expression Pattern*. 
     * - This can be a single pattern in the form of a `string`, or multiple patterns in the form of an `array`. 
     * See `$replacement` for more information on multiple pattern behavior.
     * - The `g` (`PCRE_GLOBAL`) modifier does not need to be used, as the replacement is *global* by default.
     * - You should **not** specify the `u` (`PCRE_UTF8`) modifier, as it is automatically added as needed.
     * @param string|array|callback $replacement The value to replace each string matched by the `$pattern` with. 
     * 
     * The replacement strings may contain references to a captured subpattern using the `\n` or `$n` syntax, with `n` being an integer from 0-99 that refers to the `n`th capture group from the `$pattern`.
     * - `\0` or `$0` refers to the string matched by the whole pattern.
     * - Captured subpatterns are counted from left to right, starting from **1**.
     * - The `${n}` syntax can be used when a backreference is immediately followed by a number. E.g. `${1}1`
     * 
     * This argument has two usages which *cannot be mixed*.
     * - In the first usage, a replacement `string` is provided:
     * - - If `$pattern` is also a `string`, strings matched by the pattern will be replaced by the `$replacement` string.
     * - - If `$pattern` is an `array`, all pattern matches will be replaced by the `$replacement` string.
     * - - You can also specify multiple replacements as an `array` of replacement `strings`.
     * - - - If `$pattern` is a `string`, strings matched by the pattern will be replaced by the first `$replacement` string.
     * - - - If `$pattern` is also an `array`, each `$pattern` match will be replaced by its `$replacement` counterpart.
     * - - - - If there are fewer `$pattern` elements than `$replacement` elements, all extra `$replacement` strings won't be used.
     * - - - - If there are fewer `$replacement` elements than `$pattern` elements, all extra `$pattern` matches remain unchanged. 
     * - In the second usage, a replacement `callback` is provided.
     * - - > `handler ( array $matches ): string`
     * - - The callback is provided a single argument: the `$matches` of the `$pattern`. The first element is the string matched by the whole pattern, the second the first subpattern match, and so on.
     * - - The callback should return the replacement as a `string`.
     * - - If `$pattern` is also a `string`, strings matched by the pattern will be passed to the `$replacement` callback.
     * - - If `$pattern` is an `array`, all pattern matches will be passed to the `$replacement` callback.
     * - - You can also specify multiple callbacks as an `array` of replacement `callbacks`.
     * - - - If `$pattern` is a `string`, strings matched by the pattern will be passed to the first `$replacement` callback.
     * - - - If `$pattern` is also an `array`, each `$pattern` match will be passed to its `$replacement` callback counterpart.
     * - - - - If there are fewer `$pattern` elements than `$replacement` elements, all extra `$replacement` callbacks won't be used.
     * - - - - If there are fewer `$replacement` callbacks than `$pattern` elements, all extra `$pattern` matches remain unchanged. 
     * @param int $limit The maximum number of replacements that can be done for each `$pattern`. **-1** indicates that there is no limit to the number of replacements performed.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if any of the follow issues occur:
     * - The `$replacement` is an `array` that contains an element that is not a valid `string` or `callable` function.
     * - The `$replacement` contains a mixture of `strings` and `callable` functions.
     * @throws \Exception if an error occurred while invoking `\preg_replace()`, `\preg_replace_callback()`, or `\preg_replace_callback_array()`.
     */
    public function pregReplace ($pattern, $replacement, $limit = -1) {
      $string = $this->string;
      $replacement_type = (function () use ($replacement) {
        $check_var = function ($var) {
          if (is_string($var)) {
            return 'STRING';
          }
          else if (is_callable($var)) {
            return 'CALLBACK';
          }
          else {
            throw new \UnexpectedValueException("Replacements must be in the form of a String or Callback Function.", 1);
          }
        };

        if (is_array($replacement)) {
          return $check_var($replacement[0]);
        }
        else {
          return $check_var($replacement);
        }
      })();
      /** Formatted/Updated Arguments */
      $args = [
        'pattern'     => $this->pregCheckPattern($pattern),
        'replacement' => (function () use ($pattern, $replacement, $replacement_type) {
          $arg = $replacement;
          $defined_type = null;

          // Check replacement values
          if (is_array($replacement)) {
            foreach ($replacement as $replacement_index => $replacement_value) {
              $replacement_num = $replacement_index + 1;
              $type = (function () use ($replacement_num, $replacement_value) {
                if (is_string($replacement_value)) {
                  return 'String';
                }
                else if (is_callable($replacement_value)) {
                  return 'Callback Function';
                }
                else {
                  throw new \UnexpectedValueException("Replacement #{$replacement_num} is not a String or Callback Function.", 1);
                }
              })();

              if (!isset($defined_type)) {
                $defined_type = $type;
              }
              else if ($type != $defined_type) {
                throw new \UnexpectedValueException("Replacements cannot be a mixture of Strings and Callback Functions. Replacement #{$replacement_num} is a {$type} while the previous replacements are {$defined_type}s.", 2);
              }
            }
          }
          else if (!is_string($replacement) && !is_callable($replacement)) {
            throw new \UnexpectedValueException("Replacement is not a String or Callback Function.", 1);
          }

          if (is_array($pattern) && is_array($replacement)) {
            $replacement_value = $replacement_type == 'STRING'
                                ? '$0'
                                : function ($matches) { return $matches[0]; };

            $arg = array_pad($arg, count($pattern), $replacement_value);
          }

          return $arg;
        })()
      ];
      $count = 0;

      if ($replacement_type == 'STRING') {
        $string = \preg_replace($args['pattern'], $args['replacement'], $string, $limit, $count);
      }
      else if ($replacement_type == 'CALLBACK') {
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

      return $this->handleModifyReturn($string);
    }
    /** Appends a plural value to the string depending on the value of a given number.
     *
     * @param int $value The value to be evaluated. 
     * If this value is not equal to **1**, a plural letter will be appended to the string.
     * @param bool $apostrophe Indicates if an *apostrophe* (`'`) should be included with the plural letter.
     * @param string $plural_value The plural value to append to the string.
     * Defaults to the letter `s`.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function addPlural (
      int $value, 
      bool $apostrophe = false, 
      string $plural_value = "s"
    ) {
      $string = $this->string;

      if ($value !== 1) {
        if ($apostrophe) {
          $string .= "'";
        }

        $string .= $plural_value;
      }

      return $this->handleModifyReturn($string);
    }

    /** Trim whitespace, or other characters, from the beginning and/or end of the string.
     * 
     * @param int $trim_side A `STR_SIDE_*` constant indicating which sides(s) of the string are to be trimmed.
     * - See {@see STR_SIDE_LIST} for the list of options.
     * @param string $charlist The list of characters that will be trimmed from the string.
     * - By default, the `$charlist` is a list of whitespace characters: ` \n\r\t\v\s`.
     * - Any Character or Escaped Character supported by a *Regular Expression Character Set* can be provided, with the exception of the *Tilde* (`~`).
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     * @throws \UnexpectedValueException if `$trim_side` is not a valid Trim Side `int`.
     */
    public function trim (int $trim_side = STR_SIDE_BOTH, string $charlist = " \n\r\t\v\s") {
      if (!in_array($trim_side, STR_SIDE_LIST)) {
        throw new \UnexpectedValueException("\"{$trim_side}\" is not a valid Trim Side Int.");
      }

      $patterns = (function () use ($trim_side, $charlist) {
        $patterns = [];
        $charlist_str = str_replace($charlist, '~', '\~');

        if ($trim_side == STR_SIDE_LEFT) {
          $patterns[] = "~^[{$charlist_str}]+~";
        }
        if ($trim_side == STR_SIDE_RIGHT) {
          $patterns[] = "~[{$charlist_str}]+$~";
        }

        return $patterns;
      })();
      $string = preg_replace($this->string, $patterns, '');

      if (substr_check($charlist, $this->char(-1))) {
        $string = slice($string, 0, -1);
      }

      return $this->handleModifyReturn($string);
    }
    /** Collapse whitespace, or other characters, within the string.
     * 
     * @param string $charlist The list of characters that will be collapsed in the string.
     * - By default, the `$charlist` is a list of whitespace characters: ` \n\r\t\v\s`.
     * - Any Character or Escaped Character supported by a *Regular Expression Character Set* can be provided, with the exception of the *Tilde* (`~`).
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function collapse (string $charlist = " \n\r\t\v\s") {
      $charlist_str = str_replace($charlist, '~', '\~');
      $string = preg_replace($this->string, "/[{$charlist_str}]{2,}/", function ($matches) {
        return $matches[0][0];
      });

      return $this->handleModifyReturn($string);
    }
    /** Pad the string to a certain length with another string
     * 
     * @param int $padding_length The desired length of the string.
     * - If negative, less than, or equal to the *length* of the `string`, no padding will be inserted.
     * @param string $padding The padding string used to pad the string.
     * - The `$padding` may be truncated if the required number of characters can't be evenly divided by its length.
     * @param int $padding_side A `STR_SIDE_*` constant indicating which side(s) of the string are to be padded.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function pad (
      int $padding_length, 
      string $padding = ' ', 
      int $padding_side = STR_SIDE_RIGHT
    ) {
      $string = $this->string;
      $func_padding_side = (function () use ($padding_side) {
        $func_padding_side = [
          STR_SIDE_BOTH  => STR_PAD_BOTH,
          STR_SIDE_LEFT  => STR_PAD_LEFT,
          STR_SIDE_RIGHT => STR_PAD_RIGHT
        ];

        return $func_padding_side[$padding_side];
      })();
      $resolved_string_mode = $this->getResolvedStringMode();

      if ($resolved_string_mode == self::STRING_MODE_STRING) {
        $string = \str_pad(
          $string, 
          $padding_length, 
          $padding, 
          $func_padding_side
        );
      }
      else if ($resolved_string_mode == self::STRING_MODE_MB_STRING) {
        if (function_exists('mb_str_pad')) {
          $string = \mb_str_pad(
            $string, 
            $padding_length, 
            $padding, 
            $func_padding_side, 
            $this->encoding
          );
        }
        else {
          $calculatedLength = (function () use ($padding_length) {
            $strObj = new StringObj(
              $this->string, 
              StringObj::EDITING_MODE_CHAIN, 
              StringObj::STRING_MODE_STRING
            );

            $length = $strObj->strlen();

            $strObj->setStringMode(StringObj::STRING_MODE_MB_STRING);
            $length -= $strObj->strlen();

            $length += $padding_length;

            return $length;
          })(); 

          $string = \str_pad(
            $string, 
            $calculatedLength, 
            $padding, 
            $func_padding_side
          );
        }
      }

      return $this->handleModifyReturn($string);
    }
    /** Split the string into smaller chunks
     * 
     * @param int $chunk_length The length of a single chunk.
     * @param string $separator The separator character(s) to be placed between chunks.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function chunk (int $chunk_length = 76, string $separator = "\r\n") {
      $string = $this->string;

      $string = $this->split($chunk_length);
      $string = implode($separator, $string);

      return $this->handleModifyReturn($string);
    }

    /** Convert HTML Characters in the string into *HTML Entities*
     * 
     * Encodes the following characters:
     * 
     * | Character Name | Character | HTML Entity |
     * | ---            | ---       | ---         |
     * | Ampersand      | `&`       | `&amp;`     |
     * | Double Quote   | `"`       | `&quot;`    |
     * | Single Quote   | `'`       | `&apos;`    |
     * | Less Than      | `<`       | `&lt;`      |
     * | Greater Than   | `>`       | `&gt;`      |
     * 
     * @param bool $encode_everything Indicates if all characters with HTML Character Entity equivalents should be encoded, instead of just the special characters.
     * Defaults to `false`.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function encodeHTML (bool $encode_everything = false) {
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

      return $this->handleModifyReturn($string);
    }
    /** Convert *HTML Entities* in the string back to their equivalent HTML Characters. 
     * 
     * Decodes the following characters:
     * 
     * | HTML Entity  | Character | Character Name  |
     * | ---          | ---       | ---             |
     * | `&amp;`      | `&`       | Ampersand       |
     * | `&quot;`     | `"`       | Double Quote    |
     * | `&apos;`     | `'`       | Single Quote    |
     * | `&lt;`       | `<`       | Less Than       |
     * | `&gt;`       | `>`       | Greater Than    |
     * 
     * @param bool $decode_everything Indicates if all HTML Character Entities should be decoded, instead of just the special characters.
     * Defaults to `false`.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function decodeHTML (bool $decode_everything = false) {
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

      return $this->handleModifyReturn($string);
    }
    /** Strip HTML & PHP tags from the string
     * 
     * @param null|int|array|string $allowed_tags A list of whitelisted tags. 
     * 
     * Can be one of multiple values:
     * - Passing **null** will strip all tags from the string.
     * - An `int` representing a *Preset Tag Threshold*:
     * - - @see Strings\STRIP_TAGS_STRICT
     * - - @see Strings\STRIP_TAGS_MEDIUM
     * - - @see Strings\STRIP_TAGS_LAX
     * - An `array` made up of the whitelisted tags.
     * - - E.g. `[ 'div', 'span', 'p' ]`
     * - A `string` made up of the whitelisted tags.
     * - - E.g. `<div><span><p>`
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$allowed_tags` is not of a valid format.
     */
    public function stripTags ($allowed_tags = null) {
      $tag_list = (function () use ($allowed_tags) {
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
        else if ((is_int($allowed_tags) && isset($thresholds[$allowed_tags])) || is_array($allowed_tags)) {
          $tags = [];

          if (is_int($allowed_tags)) {
            foreach ($thresholds as $const => $threshold_tags) {
              $tags = array_merge($tags, $threshold_tags);

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

      $string = \strip_tags($this->string, $tag_list);

      return $this->handleModifyReturn($string);
    }
    /** Converts special characters in the string to their equivalent URL Character Codes.
     * 
     * **Note**: The string will be *trimmed* (see {@see StringObj::trim()}) before being encoded.
     * 
     * @param bool $legacy_encode Indicates if *Legacy URL Encoding* should be performed, determining which specification should be followed when encoding the URL:
     * 
     * | Value    | Specification | Description |
     * | ---      | ---           | ---         |
     * | `false`  | *RFC 3986*    | All non-alphanumeric characters except `-`, `_`, `.`, & `~` are replaced with a percent (%) sign followed by two hex digits. |
     * | `true`   | *RFC 1866*    | All non-alphanumeric characters except `-`, `_`, & `.` are replaced with a percent (%) sign followed by two hex digits. Spaces are encoded as plus (`+`) signs. This is the same way as the `application/x-www-form-urlencoded` media type. |
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function encodeURL (bool $legacy_encode = false) {
      $string = $this->string;

      $string = trim($string);
      $string = !$legacy_encode
                ? \rawurlencode($string)
                : \urlencode($string);

      return $this->handleModifyReturn($string);
    }
    /** Converts URL Character Codes in the string to their equivalent special characters.
     * 
     * @param bool $legacy_decode Indicates if *Legacy URL Decoding* should be performed, determining which specification should be followed when decoding the URL:
     * 
     * | Value    | Specification | Description |
     * | ---      | ---           | ---         |
     * | `false`  | *RFC 3986*    | All non-alphanumeric characters except `-`, `_`, `.`, & `~` can be decoded. |
     * | `true`   | *RFC 1866*    | All non-alphanumeric characters except `-`, `_`, & `.` can be decoded. Spaces are decoded from plus (`+`) signs. This is the same way as the `application/x-www-form-urlencoded` media type. |
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function decodeURL (bool $legacy_decode = false) {
      $string = $this->string;

      $string = !$legacy_decode
                ? \rawurldecode($string)
                : \urldecode($string);

      return $this->handleModifyReturn($string);
    }
    /** Encode a string to be used as an identifier
     * 
     * @param int $casing_style A `CASING_STYLE_*` namespace constant indicating how the string is to be cased.
     * - See {@see CASING_STYLE_LIST}
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function encodeID ($casing_style = CASING_STYLE_SNAKE_CASE) {
      $string = $this->string;

      $string = preg_replace($string, '/[^\w\d\s]/', '');
      $string = change_case($string, $casing_style);

      return $this->handleModifyReturn($string);
    }
    /** Escape a string for use in a *Regular Expression*.
     * 
     * @param null|string $delimiter The *Expression Delimiter* which will also be escaped.
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringObj::$editingMode}.
     */
    public function escapeReg ($delimiter = null) {
      $string = preg_quote($this->string, $delimiter);

      return $this->handleModifyReturn($string);
    }
    /** An *alias* for {@see ShiftCodesTKDatabase::escape_string()}.
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
     * 
     * @return StringObj|string Returns a `StringObj` or `string` depending on the {@see StringArrayObj::$editingMode}.
     * @throws \RuntimeException Throws a `RuntimeException` if the method is called before the `Database` module has been loaded.
     */
    public function escapeSQL () {
      if (!class_exists('ShiftCodesTKDatabase')) {
        throw new \RuntimeException("String could not be SQL-Escaped: The Database module has not been loaded yet.");
      }

      $string = \ShiftCodesTKDatabase::escape_string($this->string);

      return $this->handleModifyReturn($string);
    }

    /** Casting the `StringObj` as a string returns the {@see StringObj::$string}.
     * 
     * @return string Returns the value of the `$string`.
     */
    public function __toString (): string {
      return $this->getString();
    }
    /** Invoking the `StringObj` returns the {@see StringObj::$string}.
     * 
     * @return string Returns the value of the `$string`.
     */
    public function __invoke (): string {
      return $this->getString();
    }
    /** Initialize a new `StringObj` 
     * 
     * @param string $string The string to be used.
     * @param int $editing_mode An `EDITING_MODE_` class constant indicating the *Editing Mode* to be used when *modifying* the `$string`.
     * - See {@see StringObj::EDITING_MODE_LIST} for the list of Editing Modes.
     * - Methods that modify the string can be found in the `MANIPULATION_METHODS` class constant.
     * @param int $string_mode A `STRING_MODE_*` class constant indicating the *String Mode* to use for the `$string`.
     * - See {@see StringObj::STRING_MODE_LIST} for the list of String Modes.
     * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$string_mode` is not a valid *String Mode* value.
     */
    public function __construct (
      string $string, 
      int $editing_mode = self::EDITING_MODE_CHAIN, 
      int $string_mode = self::STRING_MODE_AUTO
    ) {
      $this->setString($string);
      $this->setStringMode($string_mode);
      $this->setEditingMode($editing_mode);
    }
  }
?>