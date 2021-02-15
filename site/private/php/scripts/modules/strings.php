<?php
  /** The namespace for String Querying & Manipulation */
  namespace ShiftCodesTK\Strings;

  /** Constants */
  /** @var string Represents the `ASCII` Character Encoding. */
  const ENCODING_ASCII = 'ASCII';
  /** @var string Represents the `UTF-8` Character Encoding. */
  const ENCODING_UTF_8 = 'UTF-8';
  /** @var string Represents the `ISO-8859-1` Character Encoding. */
  const ENCODING_ISO_8859_1 = 'ISO-8859-1';

  /** @var int The `$search` will be treated as the *haystack*. Searches for the `string` substring within the `$search`. 
   * - Valid for the `substr_pos()`, `substr_check()`, & `substr_count()` methods and functions.
   * - - @see StringObj::substr_pos()
   * - - @see StringObj::substr_check()
   * - - @see StringObj::substr_count() 
   * - - @see Strings\substr_pos()
   * - - @see Strings\substr_check()
   * - - @see Strings\substr_count()
   **/
  const SUBSTR_SEARCH_AS_HAYSTACK = 1;
  /** @var int The *last matching occurrence* of the **needle** within the **haystack** will be returned. Only works for `substr_pos()`. 
   * - Valid for the `substr_pos()` method and function.
   * - - @see StringObj::substr_pos()
   * - - @see Strings\substr_pos()
   **/
  const SUBSTR_GET_LAST_OCCURRENCE = 2;
  /** @var int The search will be *case-insensitive*. 
   * - Valid for the `substr_pos()`, `substr_check()`, & `substr_count()` methods and functions.
   * - - @see StringObj::substr_pos()
   * - - @see StringObj::substr_check()
   * - - @see StringObj::substr_count() 
   * - - @see Strings\substr_pos()
   * - - @see Strings\substr_check()
   * - - @see Strings\substr_count()
   **/
  const SUBSTR_CASE_INSENSITIVE = 4;

  /** @var int Performs a *Global Search*, like the `g` modifier was used. 
   * - Valid for the `preg_match()` methods and function.
   * - - @see StringObj::preg_match()
   * - - @see Strings\preg_match()
   **/
  const PREG_GLOBAL_SEARCH = 1;
  /** @var int Returns only the *full pattern match*. 
   * - Valid for the `preg_match()` method and function.
   * - - @see StringObj::preg_match()
   * - - @see Strings\preg_match()
   **/
  const PREG_RETURN_FULL_MATCH = 2;
  /** @var int Returns only the *subpattern matches*. 
   * - Valid for the `preg_match()` method and function.
   * - - @see StringObj::preg_match()
   * - - @see Strings\preg_match()
   **/
  const PREG_RETURN_SUB_MATCHES = 4;
  /** @var int Returns matches as a `StringArrayObj` instead of an `array`. 
   * - Valid for the `preg_match()` method and function.
   * - - @see StringObj::preg_match()
   * - - @see Strings\preg_match()
   **/
  const PREG_RETURN_STRING_ARRAY_OBJ = 8;
  /** @var int Each returned match will include the *offset* (in bytes) as the second item in the array. 
   * - Valid for the `preg_match()` method and function.
   * - - @see StringObj::preg_match()
   * - - @see Strings\preg_match()
   **/
  const PREG_OFFSET_CAPTURE = \PREG_OFFSET_CAPTURE;
  /** @var int Instead of unmatched subpatterns being reported as an *empty string*, they will be reported as **null**. 
   * - Valid for the `preg_match()` method and function.
   * - - @see StringObj::preg_match()
   * - - @see Strings\preg_match()
   **/
  const PREG_UNMATCHED_AS_NULL = \PREG_UNMATCHED_AS_NULL;
  
  /** @var int Transforms the entire string to *lowercase*. 
   * - Valid for the `transform()` method and function.
   * - - @see StringObj::transform()
   * - - @see Strings\transform()
   */
  const TRANSFORM_LOWERCASE = 1;
  /** @var int Transforms the entire string to *uppercase* 
   * - Valid for the `transform()` method and function.
   * - - @see StringObj::transform()
   * - - @see Strings\transform()
   */
  const TRANSFORM_UPPERCASE = 2;
  /** @var int Transforms the first character of each word in the string to *uppercase*. 
   * - Valid for the `transform()` method and function.
   * - - @see StringObj::transform()
   * - - @see Strings\transform()
   */
  const TRANSFORM_CAPITALIZE_WORDS = 4;
  /** @var int Transforms the first character of the string to *uppercase*. 
   * - Valid for the `transform()` method and function.
   * - - @see StringObj::transform()
   * - - @see Strings\transform()
   */
  const TRANSFORM_CAPITALIZE_FIRST = 8;

  /** @var int Operates on *both* the beginning and end of the string.
   * - Valid for the `trim()` & `pad()` methods and functions.
   * - - @see StringObj::trim()
   * - - @see Strings\trim()
   * - - @see StringObj::pad()
   * - - @see Strings\pad()
   */
  const STR_SIDE_BOTH = -1;
  /** @var int Operates on the *beginning* of the string.
   * - Valid for the `trim()` & `pad()` methods and functions.
   * - - @see StringObj::trim()
   * - - @see Strings\trim()
   * - - @see StringObj::pad()
   * - - @see Strings\pad()
   */
  const STR_SIDE_LEFT = 1;
  /** @var int Operates on the *end* of the string.
   * - Valid for the `trim()` & `pad()` methods and functions.
   * - - @see StringObj::trim()
   * - - @see Strings\trim()
   * - - @see StringObj::pad()
   * - - @see Strings\pad()
   */
  const STR_SIDE_RIGHT = 2;

  /** @var int The present tag filter will be set to *Strict*.
   * - Allows the following tags:
   * - - `<div>`
   * - - `<span>`
   * - - `<p>`
   * - Valid for the `strip_tags()` method and function.
   * - - @see StringObj::strip_tags()
   * - - @see Strings\strip_tags()
   **/
  const STRIP_TAGS_STRICT = 1;
  /** @var int The present tag filter will be set to *Medium*.
   * - Allows the following tags:
   * - - `<div>`
   * - - `<span>`
   * - - `<p>`
   * - - `<strong>`
   * - - `<b>`
   * - - `<em>`
   * - - `<ul>`
   * - - `<ol>`
   * - - `<li>`
   * - - `<code>`
   * - - `<pre>`
   * - Valid for the `strip_tags()` method and function.
   * - - @see StringObj::strip_tags()
   * - - @see Strings\strip_tags()
   **/
  const STRIP_TAGS_MEDIUM = 2;
  /** @var int The present tag filter will be set to *Lax*.
   * - Allows the following tags:
   * - - `<div>`
   * - - `<span>`
   * - - `<p>`
   * - - `<strong>`
   * - - `<b>`
   * - - `<em>`
   * - - `<ul>`
   * - - `<ol>`
   * - - `<li>`
   * - - `<code>`
   * - - `<pre>`
   * - - `<a>`
   * - - `<button>`
   * - - `<fieldset>`
   * - - `<label>`
   * - - `<legend>`
   * - - `<input>`
   * - - `<select>`
   * - - `<option>`
   * - - `<textarea>`
   * - Valid for the `strip_tags()` method and function.
   * - - @see StringObj::strip_tags()
   * - - @see Strings\strip_tags()
   **/
  const STRIP_TAGS_LAX = 4;

  /** @var int Encodes the string using *Snake Case Styling*.
   * - Example: `string_to_ID`
   * - Valid for the `encode_id()` method and function.
   * - - @see StringObj::encode_id()
   * - - @see Strings\encode_id()
   */
  const ENCODE_ID_SNAKE_CASE = 1;
  /** @var int Encodes the string using *Camel Case Styling*.
   * - Example: `stringToID`
   * - Valid for the `encode_id()` method and function.
   * - - @see StringObj::encode_id()
   * - - @see Strings\encode_id()
   */
  const ENCODE_ID_CAMEL_CASE = 2;
  /** @var int Encodes the string using *Pascal Case Styling*.
   * - Example: `StringToID`
   * - Valid for the `encode_id()` method and function.
   * - - @see StringObj::encode_id()
   * - - @see Strings\encode_id()
   */
  const ENCODE_ID_PASCAL_CASE = 4;
  /** @var int Encodes the string using *Kebab Case Styling*.
   * - Example: `string-to-ID`
   * - Valid for the `encode_id()` method and function.
   * - - @see StringObj::encode_id()
   * - - @see Strings\encode_id()
   */
  const ENCODE_ID_KEBAB_CASE = 8;


  /** Classes */

  /** Functions 
   * 
   * Note: String Method Aliases 
   * 
   * These functions are aliases to various `StringObj` querying and manipulation methods, intended for simple one-off operations on a string.
   * Refer to the aliased method's documentation for the full details of the aliased method.
   * Not all methods have aliases. Internal methods, or methods that are themselves just aliases to other functions, do not have aliases. 
   * For more complicated operations, use the `StringObj` class for more control and functionality.
   **/
  /** An *alias* of `StringObj::check_encoding()`.
   * 
   * > Check the encoding for a string
   * @see StringObj::check_encoding()
   * 
   * @param string $string The string being evaluated.
   * @param string $encoding The *String Encoding* to check the string for.
   * @param bool $throw_error If **true**, throws an `Error` if the `$string` does not match the encoding of `$encoding`.
   * @return bool Returns **true** if the string matches the *String Encoding* of `$encoding`.
   * @throws \Error If `$throw_error` is **true**, throws an `Error` if the string does not match the encoding of `$encoding`.
   */
  function check_encoding (string $string, string $encoding = ENCODING_UTF_8, bool $throw_error = false): bool {
    return StringObj::alias('check_encoding', ...func_get_args());
  }
  /** An *alias* of `StringObj::get_encoding()`.
   * 
   * > Attempt to get the encoding for a string
   * @see StringObj::get_encoding()
   * 
   * @param string $string The string being evaluated.
   * @return string|false Returns the *Encoding* of the string on success, or **false** if the encoding could not be detected.
   */
  function get_encoding (string $string) {
    return StringObj::alias('get_encoding', ...func_get_args());
  }

  /** An *alias* of `StringObj::strlen()`.
   * 
   * > Get the length of a string
   * @see StringObj::strlen()
   * 
   * @param string $string The string being evaluated.
   * @return int Returns the number of characters in the `$string`.
   */
  function strlen (string $string): int {
    return StringObj::alias('strlen', ...func_get_args());
  }
  /** An *alias* of `StringObj::char()`.
   * 
   * > Retrieve a character in the string
   * @see StringObj::char()
   * 
   * @param string $string The string being evaluated.
   * @param int $char Indicates the *Character Position* within the `string` of the character to be retrieved. Positive values are relative to the *start* of the string and a negative value relative to the *end*.
   * @return string Returns the character found in the `string` at `$char`.
   */
  function char (string $string): string {
    return StringObj::alias('char', ...\func_get_args());
  }
  /** An *alias* of `StringObj::firstchar()`.
   * 
   * > Get the first character of a string
   * @see StringObj::firstchar()
   * 
   * @param string $string The string being evaluated.
   * @return string Returns the first character found in the `string`.
   */
  function firstchar (string $string): string {
    return StringObj::alias('firstchar', ...\func_get_args());
  }
  /** An *alias* of `StringObj::lastchar()`.
   * 
   * > Get the last character of a string
   * @see StringObj::lastchar()
   * 
   * @param string $string The string being evaluated.
   * @return string Returns the last character found in the `string`.
   */
  function lastchar (string $string): string {
    return StringObj::alias('lastchar', ...\func_get_args());
  }
  /** An *alias* of `StringObj::split()`.
   * 
   * > Convert a string's characters to an array.
   * @see StringObj::split()
   * 
   * @param string $string The string to split.
   * @param int $length The maximum length of each character chunk.
   * @param bool $return_string_array Indicates if the return value should be a `StringArrayObj` instead of an `array`.
   * @return array|StringArrayObj|false On success, returns an `array` or `StringArrayObj` made up of the characters of the `string`. If `$length` is less than *1*, returns **false**.
   */
  function split (string $string, int $length = 1, bool $return_string_array = false) {
    return StringObj::alias('split', ...func_get_args());
  }
  /** An *alias* of `StringObj::explode()`.
   * 
   * > Split a string by another string
   * @see StringObj::explode()
   * 
   * @param string $string The string being evaluated.
   * @param string $delimiter The delimiter to split the `string` by.
   * @param int|null $limit The maximum number of splits to be performed.
   * @param bool $return_string_array Indicates if the return value should be a `StringArrayObj` instead of an `array`.
   * @return array|StringArrayObj`false Returns an `array` or `StringArrayObj` of substrings created by splitting the `string` by the `$delimiters` on success. Returns **false** if `$delimiters` is an *Empty `String`*.
   */
  function explode (string $string, string $delimiters, int $limit = null, bool $return_string_array = false) {
    return StringObj::alias('explode', ...\func_get_args());
  }
  
  /** An *alias* of `StringObj::substr()`.
   * 
   * > Extract a slice from the a string
   * @see StringObj::substr()
   * 
   * @param string $string The string to be sliced.
   * @param int $start Where the slice begins. 
   * @param int $length Indicates the maximum length of the slice.
   * @param bool $throw_errors If **true**, an `OutOfRangeException` will be thrown if the provided arguments are invalid, instead of simply returning **false**.
   * @return string|false Returns a slice of the `string` on success. If the `string` is less than `$start` characters long, or `$length` is *negative* and tries to truncate more characters than are available, returns **false**.
   * @throws \OutOfRangeException If `$throw_errors` is **true**, an `OutOfRangeException` will be thrown if the provided arguments are invalid.
   */
  function substr (string $string, int $start = 0, int $length = null, bool $throw_errors = false) {
    return StringObj::alias('substr', ...func_get_args());
  }
  /** An *alias* of `StringObj::substr_pos()`.
   * 
   * > Finds the first or last occurrence of a substring within a string
   * @see StringObj::substr_pos()
   * 
   * @param string $string The *haystack* of the search.
   * @param string $search The *needle* substring of the search.
   * @param int $offset The search offset. 
   * @param int $flags A bitmask integer representing the search flags. 
   * - Passing the `SUBSTR_SEARCH_AS_HAYSTACK` flag isn't necessary when using the alias, but will still swap the `$string` and `$search` if used.
   * @return int|false On success, returns the *first* or *last occurrence* of the *needle* within the *haystack*, dependent on the provided `$flags`. If the *needle* was not found, returns **false**.
   */
  function substr_pos (string $string, string $search, int $offset = 0, int $flags = 0) {
    return StringObj::alias('substr_pos', ...func_get_args());
  }
  /** An *alias* of `StringObj::substr_check()`.
   * 
   * > Checks for the presence of substring within a string
   * @see StringObj::substr_check()
   * 
   * @param string $string The *haystack* of the search.
   * @param string $search The *needle* substring of the search.
   * @param int $offset The search offset. 
   * @param int $flags A bitmask integer representing the search flags.
   * * - Passing the `SUBSTR_SEARCH_AS_HAYSTACK` flag isn't necessary when using the alias, but will still swap the `$string` and `$search` if used.
   * @return bool Returns **true** if the *needle* was found in the *haystack*, dependent on the provided `$flags`. Returns **false** if it was not.
   */
  function substr_check (string $string, string $search, int $offset = 0, int $flags = 0): bool {
    return StringObj::alias('substr_check', ...func_get_args());
  }
  /** An *alias* of `StringObj::substr_count()`.
   * 
   * > Checks for the presence of substring within a string
   * @see StringObj::substr_count()
   * 
   * @param string $string The *haystack* of the search.
   * @param string $search The *needle* substring of the search.
   * @param int $offset The search offset. 
   * @param int $flags A bitmask integer representing the search flags.
   * * - Passing the `SUBSTR_SEARCH_AS_HAYSTACK` flag isn't necessary when using the alias, but will still swap the `$string` and `$search` if used.
   * @return bool Returns **true** if the *needle* was found in the *haystack*, dependent on the provided `$flags`. Returns **false** if it was not.
   */
  function substr_count (string $string, string $search, int $offset = 0, int $length = null, int $flags = 0): int {
    return StringObj::alias('substr_count', ...func_get_args());
  }

  /** An *alias* of `StringObj::preg_match()`
   * 
   * > Perform a *Regular Expression Match* on a string
   * @see StringObj::preg_match()
   * 
   * @param string $string The string to be searched.
   * @param string $pattern The *Regular Expression Pattern*.
   * @param int $flags An integer representing the Search Flags:
   * @param int $offset Specifies where the beginning of the search should start (in bytes).
   * @return array|StringArrayObj|false On success, returns an `array` or `StringArrayObj` made up of the search results, formatted by the provided `$flags`. If the `$pattern` doesn't match the `$string`, returns **false**.
   */
  function preg_match (string $string, string $pattern, int $flags = 0, int $offset = 0) {
    return StringObj::alias('preg_match', ...func_get_args());
  }
  /** An *alias* of `StringObj::preg_test()`
   * 
   * > Test if a string matches a *Regular Expression*
   * @see StringObj::preg_test()
   * 
   * @param string $string The string to be tested.
   * @param string $pattern The *Regular Expression Pattern*.
   * @param int $offset Specifies where the beginning of the search should start (in bytes).
   * @return bool Returns **true** if the `string` matches the `$pattern`, or **false** if it does not.
   */
  function preg_test (string $string, string $pattern, int $offset = 0): bool {
    return StringObj::alias('preg_test', ...func_get_args());
  }

  /** An *alias* of `StringObj::transform()`
   * 
   * > Transform the capitalization of the `string`
   * @see StringObj::transform()
   * 
   * @param string $string The string to be transformed.
   * @param TRANSFORM_LOWERCASE|TRANSFORM_UPPERCASE|TRANSFORM_CAPITALIZE_WORDS|TRANSFORM_CAPITALIZE_FIRST $transformation Indicates how the string is to be transformed.
   * @return string Returns the transformed `$string`.
   * @throws \TypeError Throws a `TypeError` if `$transformation` is invalid.
   */
  function transform (string $string, int $transformation): string {
    return StringObj::alias('transform', ...func_get_args());
  }
  /** An *alias* of `StringObj::slice()`
   * 
   * > Slice a string into a piece.

   * - To split a string using substrings, use the `str_replace()` function.
   * - To split a string using complex searches and replacements, use the `preg_replace()` function.

   * @see StringObj::slice()
   * 
   * @param string $string The string to be sliced.
   * @param int $start Where the slice begins. 
   * @param int $length Indicates the maximum length of the slice.
   * @return string Returns the sliced `$string`.
   * @throws \OutOfRangeException Throws an `OutOfRangeException` If the `string` is less than `$start` characters long, or `$length` is *negative* and tries to truncate more characters than are available.
   */
  function slice (string $string, int $start = 0, int $length = null): string {
    return StringObj::alias('slice', ...func_get_args());
  }
  /** An *alias* of `StringObj::str_replace()` 
   * 
   * > Replace all occurrences of a search string with a replacement string within the string
   *
   * - To split a string into pieces every variable number of characters, use the `slice()` function.
   * - To split a string using more complex searches and replacements, use the `preg_replace()` function.
   * 
   * @see StringObj::str_replace()
   * 
   * @param string|array $search The Search *Needle*, provided as a single `string`, or as an `array` of needles.
   * @param string|array $replacement The replacement value for each `$search` match, provided as a single `string`, or as an `array` of replacements.
   * @param bool $case_insensitive Indicates if the `$search` match(es) should be matched *Case Insensitively*.
   * @return StringObj|string Returns the modified `$string`.
   */
  function str_replace (string $string, $search, $replacement, $case_insensitive = false) {
    return StringObj::alias('str_replace', ...func_get_args());
  }
  /** An *alias* of `StringObj::preg_replace()`
   * 
   * > Perform a *Global Regular Expression Match* on a string
   * 
   * - To split a string into pieces every variable number of characters, use the `slice()` function.
   * - To split a string using simple substrings, use the `str_replace()` function.
   * 
   * @see StringObj::preg_replace()
   * 
   * @param string $string The string to be modified.
   * @param string|array $pattern The *Regular Expression Pattern*. 
   * @param string|array|callback $replacement The value to replace each string matched by the `$pattern` with. 
   * @param int $limit The maximum number of replacements that can be done for each `$pattern`. **-1** indicates that there is no limit to the number of replacements performed.
   * @return string Returns the modified `$string`.
   */
  function preg_replace (string $string, $pattern, $replacement, $limit = -1): string {
    return StringObj::alias('preg_replace', ...func_get_args());
  }
  /** An *alias* of `StringObj::add_plural()`
   * 
   * > Appends a plural letter to the string depending on the value of a given number.
   * @see StringObj::add_plural()
   * 
   * @param string $string The string to be changed.
   * @param int $value The value to be evaluated. If this value does not equal **1**, a plural letter will be appended to the string.
   * @param bool $apostrophe Indicates if an *apostrophe* (`'`) should be included with the plural letter.
   * @return string $string Returns the modified `$string`.
   */
  function add_plural (string $string, int $value, $apostrophe = false) {
    return StringObj::alias('add_plural', ...func_get_args());
  }

  /** An *alias* of `StringObj::trim()`, bool $return_string_array = false
   * 
   * > Trim whitespace, or other characters, from the beginning and/or end of the string.
   * @see StringObj::trim()
   * 
   * @param string $string The string to be trimmed.
   * @param STR_SIDE_BOTH|STR_LEFT|STR_RIGHT $trim_side A `STR_SIDE_*` constant indicating which sides(s) of the string are to be trimmed.
   * @param string $charlist The list of characters that will be trimmed from the string.
   * @return string Returns the modified `$string`.
   */
  function trim (string $string, int $trim_side = STR_SIDE_BOTH, string $charlist = " \n\r\t\v\s"): string {
    return StringObj::alias('trim', ...func_get_args());
  }
  /** An *alias* of `StringObj::collapse()`
   * 
   * > Collapse whitespace, or other characters, within a string.
   * @see StringObj::collapse()
   * 
   * @param string $string The string to be collapsed.
   * @param string $charlist The list of characters that will be collapsed in the string.
   * @return string Returns the modified `$string`.
   */
  function collapse (string $string, string $charlist = " \n\r\t\v\s"): string {
    return StringObj::alias('collapse', ...func_get_args());
  }
  /** An *alias* of `StringObj::pad()`
   * 
   * > Pad the string to a certain length with another string
   * @see StringObj::pad() 
   * 
   * @param string $string The string to be padded.
   * @param int $padding_length The desired length of the string.
   * @param string $padding The padding string used to pad the string.
   * @param STR_SIDE_BOTH|STR_SIDE_LEFT|STR_SIDE_RIGHT $padding_side A `STR_SIDE_*` constant indicating which side(s) of the string are to be padded.
   * @return string Returns the modified `$string`
   */
  function pad (string $string, int $padding_length, string $padding = ' ', int $padding_side = STR_SIDE_RIGHT) {
    return StringObj::alias('pad', ...func_get_args());
  }
  /** An *alias* of `StringObj::chunk()`
   *  
   * > Split the string into smaller chunks
   * @see StringObj::chunk()
   * 
   * @param string $string The string to be chunked.
   * @param int $chunk_length The length of a single chunk.
   * @param string $separator The separator character(s) to be placed between chunks.
   * @return string Returns the modified `$string`.
   */
  function chunk (string $string, int $chunk_length = 76, string $separator = "\r\n") {
    return StringObj::alias('chunk', ...func_get_args());
  }

  /** Convert special HTML Characters in a string into *HTML Entities*
   * 
   * @param string $string The string to be encoded.
   * @return string Returns the encoded `$string`.
   */
  function encode_html (string $string): string {
    return StringObj::alias('encode_html', ...func_get_args());
  }
  /** An *alias* of `StringObj::decode_html()`
   * 
   * > Convert *HTML Entities* in a string back to their special HTML Characters.
   * @see StringObj::decode_html() 
   * 
   * @param string $string The string to be encoded.
   * @return string Returns the encoded `$string`.
   */
  function decode_html (string $string): string {
    return StringObj::alias('decode_html', ...func_get_args());
  }
  /** An *alias* of `StringObj::strip_tags()`
   * 
   * > Strip HTML & PHP tags from a string
   * @see StringObj::strip_tags()
   * 
   * @param string $string The string to be stripped.
   * @param null|int|array|string $allowed_tags A list of whitelisted tags as an `int`, `array`, `string`, or **null**.
   * @return StringObj|string Returns the stripped `$string`.
   * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$allowed_tags` is not of a valid format.
   */
  function strip_tags (string $string, $allowed_tags = null): string {
    return StringObj::alias('strip_tags', ...func_get_args());
  }
  /** An *alias* for `StringObj::encode_url()`
   * 
   * > Converts special characters in the string to their equivalent URL Character Codes.
   * @see StringObj::encode_url()
   * 
   * @param string $string The string to be encoded.
   * @param bool $legacy_encode Indicates if *Legacy URL Encoding* should be performed, determining which specification should be followed when encoding the URL.
   * @return string Returns the modified `$string`.
   */
  function encode_url (string $string, bool $legacy_encode = false): string {
    return StringObj::alias('encode_url', ...func_get_args());
  }
  /** An *alias* for `StringObj::decode_url()`
   * 
   * > Converts URL Character Codes in the string to their equivalent special characters.
   * @see StringObj::decode_url()
   * 
   * @param string $string The string to be decoded.
   * @param bool $legacy_decode Indicates if *Legacy URL Decoding* should be performed, determining which specification should be followed when decoding the URL.
   * @return string Returns the modified `$string`.
   */
  function decode_url (string $string, bool $legacy_decode = false): string {
    return StringObj::alias('decode_url', ...func_get_args());
  }
  /** An *alias* for `StringObj::encode_id()`
   * 
   * > Encode a string to be used as an identifier
   * @see StringObj::encode_id()
   * 
   * @param string $string The string to be encoded.
   * @param ENCODE_ID_SNAKE_CASE|ENCODE_ID_CAMEL_CASE|ENCODE_ID_PASCAL_CASE|ENCODE_ID_KEBAB_CASE $encoding_style Indicates how the string will be encoded.
   * @return string Returns the modified `$string`.
   */
  function encode_id (string $string, $encoding_style = ENCODE_ID_SNAKE_CASE) {
    return StringObj::alias('encode_id', ...func_get_args());
  }
  /** An *alias* for `StringObj::escape_reg()`
   * 
   * > Escape a string for use in a *Regular Expression*.
   * 
   * @param string $string The string to be escaped.
   * @param null|string $delimiter The *Expression Delimiter* to also be escaped.
   * @return string Returns the modified `$string`.
   */
  function escape_reg(string $string, $delimiter = null) {
    return StringObj::alias('escape_reg', ...func_get_args());
  }
  /** An *alias* for `StringObj::escape_sql()`
   * 
   * > Escape a string for use in a SQL Query Statement.
   * @see StringObj::escape_sql()
   * 
   * @param string $string The string to be escaped.
   * @return string Returns the modified `$string`.
   * @throws \RuntimeException Throws a `RuntimeException` if the function is called before the `Database` module has been loaded.
   */
  function escape_sql (string $string) {
    return StringObj::alias('escape_sql', ...func_get_args());
  }
?>