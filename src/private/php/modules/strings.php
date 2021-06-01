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
  /** @var array A list of the `ENCODING_*` namespace constants. */
  const ENCODING_LIST = [
    ENCODING_ASCII,
    ENCODING_UTF_8,
    ENCODING_ISO_8859_1
  ];

  /** @var int The `$search` will be treated as the *haystack*. Searches for the `string` substring within the `$search`. **/
  const SUBSTR_SEARCH_AS_HAYSTACK = 1;
  /** @var int The *last matching occurrence* of the **needle** within the **haystack** will be returned. Only works for `substrPos()`. **/
  const SUBSTR_GET_LAST_OCCURRENCE = 2;
  /** @var int The search will be *case-insensitive*. **/
  const SUBSTR_CASE_INSENSITIVE = 4;
  /** @var array A list of the `SUBSTR_*` namespace constants. */
  const SUBSTR_LIST = [
    SUBSTR_SEARCH_AS_HAYSTACK,
    SUBSTR_GET_LAST_OCCURRENCE,
    SUBSTR_CASE_INSENSITIVE
  ];

  /** @var int Performs a *Global Search*, like the `g` modifier was used. **/
  const PREG_GLOBAL_SEARCH = 1;
  /** @var int Returns only the *full pattern match*. **/
  const PREG_RETURN_FULL_MATCH = 2;
  /** @var int Returns only the *subpattern matches*. **/
  const PREG_RETURN_SUB_MATCHES = 4;
  /** @var int Each returned match will include the *offset* (in bytes) as the second item in the array. **/
  const PREG_OFFSET_CAPTURE = \PREG_OFFSET_CAPTURE;
  /** @var int Instead of unmatched subpatterns being reported as an *empty string*, they will be reported as **null**. **/
  const PREG_UNMATCHED_AS_NULL = \PREG_UNMATCHED_AS_NULL;
  
  /** @var int Transforms the entire string to *lowercase*. 
   * - Valid for the `transform()` method and function.
   * - - @see StringObj::transform()
   * - - @see Strings\transform()
   */
  const TRANSFORM_LOWERCASE = 1;
  /** @var int Transforms the entire string to *uppercase* */
  const TRANSFORM_UPPERCASE = 2;
  /** @var int Transforms the first character of each word in the string to *uppercase*. */
  const TRANSFORM_CAPITALIZE_WORDS = 4;
  /** @var int Transforms the first character of the string to *uppercase*. */
  const TRANSFORM_CAPITALIZE_FIRST = 8;
  /** @var array A list of the `TRANSFORM_*` namespace constants. */
  const TRANSFORM_LIST = [
    TRANSFORM_LOWERCASE,
    TRANSFORM_UPPERCASE,
    TRANSFORM_CAPITALIZE_WORDS,
    TRANSFORM_CAPITALIZE_FIRST
  ];

  /** @var int Operates on *both* the beginning and end of the string. */
  const STR_SIDE_BOTH = -1;
  /** @var int Operates on the *beginning* of the string. */
  const STR_SIDE_LEFT = 1;
  /** @var int Operates on the *end* of the string. */
  const STR_SIDE_RIGHT = 2;
  /** @var array A list of the `STR_SIDE_*` namespace constants. */
  const STR_SIDE_LIST = [
    STR_SIDE_BOTH,
    STR_SIDE_LEFT,
    STR_SIDE_RIGHT
  ];

  /** @var int The present tag filter will be set to *Strict*.
   * - Allows the following tags:
   * - - `<div>`
   * - - `<span>`
   * - - `<p>`
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
   **/
  const STRIP_TAGS_LAX = 4;
  /** @var array A list of the `STRIP_TAGS_*` namespace constants. */
  const STRIP_TAGS_LIST = [
    STRIP_TAGS_STRICT,
    STRIP_TAGS_MEDIUM,
    STRIP_TAGS_LAX
  ];

  /** @var int Encodes the string using *Snake Case Styling*. */
  const CASING_STYLE_SNAKE_CASE = 1;
  /** @var int Encodes the string using *Camel Case Styling*. */
  const CASING_STYLE_CAMEL_CASE = 2;
  /** @var int Encodes the string using *Pascal Case Styling*. */
  const CASING_STYLE_PASCAL_CASE = 4;
  /** @var int Encodes the string using *Kebab Case Styling*. */
  const CASING_STYLE_KEBAB_CASE = 8;
  /** @var array A list of the `CASING_STYLE_LIST*` namespace constants. */
  const CASING_STYLE_LIST = [
    CASING_STYLE_SNAKE_CASE,
    CASING_STYLE_CAMEL_CASE,
    CASING_STYLE_PASCAL_CASE,
    CASING_STYLE_KEBAB_CASE
  ];

  /** String Method Aliases 
   * 
   * These functions are aliases to various `StringObj` querying and manipulation methods, intended for simple one-off operations on a string.
   * Refer to the aliased method's documentation for the full details of the aliased method.
   * Not all methods have aliases. Internal methods, or methods that are themselves just aliases to other functions, do not have aliases. 
   * For more complicated operations, use the `StringObj` class for more control and functionality.
   **/

  /** An *alias* of {@see StringObj::checkEncoding()}
   * 
   * > Check the encoding for a string
   * 
   * @param string $string The string being evaluated.
   * @param string $encoding The *String Encoding* to check the string for.
   * @param bool $throw_error If **true**, throws an `Error` if the `$string` does not match the encoding of `$encoding`.
   * @return bool Returns **true** if the string matches the *String Encoding* of `$encoding`.
   * @throws \Error If `$throw_error` is **true**, throws an `Error` if the string does not match the encoding of `$encoding`.
   */
  function check_encoding (
    string $string, 
    string $encoding = ENCODING_UTF_8, 
    bool $throw_error = false
  ): bool {
    return StringObj::alias('checkEncoding', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::getEncoding()}
   * 
   * > Attempt to get the encoding for a string
   * 
   * @param string $string The string being evaluated.
   * @return string|null Returns the *Encoding* of the string on success, or `null` if the encoding could not be detected.
   */
  function get_encoding (string $string): ?string {
    return StringObj::alias('getEncoding', ...func_get_args());
  }

  /** An *alias* of {@see StringObj::strlen()}
   * 
   * > Get the length of a string
   * 
   * @param string $string The string being evaluated.
   * @return int Returns the number of characters in the `$string`.
   */
  function strlen (string $string): int {
    return StringObj::alias('strlen', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::char()}
   * 
   * > Retrieve a character in the string
   * 
   * @param string $string The string being evaluated.
   * @param int $char Indicates the *Character Position* within the `string` of the character to be retrieved. 
   * @return string Returns the character found in the `string` at `$char`.
   */
  function char (string $string, int $char = 1): string {
    return StringObj::alias('char', ...\func_get_args());
  }
  /** An *alias* of {@see StringObj::firstchar()}
   * 
   * > Get the first character of a string
   * 
   * @param string $string The string being evaluated.
   * @return string Returns the first character found in the `$string`.
   */
  function firstchar (string $string): string {
    return StringObj::alias('firstchar', ...\func_get_args());
  }
  /** An *alias* of {@see StringObj::lastchar()}
   * 
   * > Get the last character of a string
   * 
   * @param string $string The string being evaluated.
   * @return string Returns the last character found in the `string`.
   */
  function lastchar (string $string): string {
    return StringObj::alias('lastchar', ...\func_get_args());
  }
  /** An *alias* of {@see StringObj::split()}
   *
   * > Convert a string's characters to an array.
   * 
   * @param string $string The string to split.
   * @param int $length The maximum length of each character chunk.
   * @return array|null On success, returns an `array` made up of the characters of the `string`. 
   * If `$length` is less than *1*, returns `null`.
   */
  function split (string $string, int $length = 1): ?array {
    return StringObj::alias('split', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::explode()}
   * 
   * > Split a string by another string
   * 
   * @param string $string The string being evaluated.
   * @param string $delimiter The delimiter to split the `string` by.
   * @param int|null $limit The maximum number of splits to be performed.
   * @return array|null Returns an `array` of substrings created by splitting the `$string` by the `$delimiters` on success. 
   * Returns `null` if `$delimiters` is an *Empty `String`*.
   */
  function explode (
    string $string, 
    string $delimiters, 
    int $limit = null
  ): ?array {
    return StringObj::alias('explode', ...\func_get_args());
  }
  
  /** An *alias* of {@see StringObj::substr()}
   * 
   * > Extract a slice from the a string
   * 
   * @param string $string The string to be sliced.
   * @param int $start Where the slice begins. 
   * @param int $length Indicates the maximum length of the slice.
   * @param bool $throw_errors If **true**, an `OutOfRangeException` will be thrown if the provided arguments are invalid, instead of simply returning **false**.
   * @return string|null Returns a slice of the `string` on success. 
   * If the `string` is less than `$start` characters long, or `$length` is *negative* and tries to truncate more characters than are available, returns an *Empty `string`*.
   * @throws \OutOfRangeException If `$throw_errors` is **true**, an `OutOfRangeException` will be thrown if the provided arguments are invalid.
   */
  function substr (
    string $string, 
    int $start = 0, 
    int $length = null, 
    bool $throw_errors = false
  ): string {
    return StringObj::alias('substr', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::substrPos()}
   * 
   * > Finds the first or last occurrence of a substring within a string
   * 
   * @param string $string The *haystack* of the search.
   * @param string $search The *needle* substring of the search.
   * @param int $offset The search offset. 
   * @param int $flags A bitmask integer representing the search flags. 
   * - Passing the `SUBSTR_SEARCH_AS_HAYSTACK` flag isn't necessary when using the alias, but will still swap the `$string` and `$search` if used.
   * @return int|null On success, returns the *first* or *last occurrence* of the *needle* within the *haystack*, dependent on the provided `$flags`. 
   * If the *needle* was not found, returns `null`.
   */
  function substr_pos (
    string $string, 
    string $search, 
    int $offset = 0, 
    int $flags = 0
  ): ?int {
    return StringObj::alias('substrPos', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::substrCheck()}
   * 
   * > Checks for the presence of substring within a string
   * 
   * @param string $string The *haystack* of the search.
   * @param string $search The *needle* substring of the search.
   * @param int $offset The search offset. 
   * @param int $flags A bitmask integer representing the search flags.
   * * - Passing the `SUBSTR_SEARCH_AS_HAYSTACK` flag isn't necessary when using the alias, but will still swap the `$string` and `$search` if used.
   * @return bool Returns `true` if the *needle* was found in the *haystack*, dependent on the provided `$flags`. 
   * Otherwise, returns `false`.
   */
  function substr_check (
    string $string, 
    string $search, 
    int $offset = 0, 
    int $flags = 0
  ): bool {
    return StringObj::alias('substrCheck', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::substrCount}
   * 
   * > Counts the number of substring occurrences within a string
   * 
   * @param string $search A string, its usage determined by the presence or absense of the `SUBSTR_SEARCH_AS_HAYSTACK` flag:
   * @param int $offset The search offset. 
   * @param int $length The maximum length after the specified offset to search for the substring. 
   * @param int $flags A bitmask integer representing the search flags.
   * @return int Returns the number of times the *needle* occurs in the *haystack*, dependent on the provided `$flags`.
   */
  function substr_count (
    string $string, 
    string $search, 
    int $offset = 0, 
    int $length = null, 
    int $flags = 0
  ): int {
    return StringObj::alias('substrCount', ...func_get_args());
  }

  /** An *alias* of {@see StringObj::pregMatch()}
   * 
   * > Perform a *Regular Expression Match* on a string
   * 
   * @param string $string The string to be searched.
   * @param string $pattern The *Regular Expression Pattern*.
   * @param int $flags An integer representing the Search Flags:
   * @param int $offset Specifies where the beginning of the search should start (in bytes).
   * @return string|array|null On success, returns a `string` or `array` representing the search results, formatted by the provided `$flags`. 
   * If the `$pattern` doesn't match the `$string`, returns `null`.
   */
  function preg_match (
    string $string, 
    string $pattern, 
    int $flags = 0, 
    int $offset = 0
  ) {
    return StringObj::alias('pregMatch', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::pregTest()}
   * 
   * > Test if a string matches a *Regular Expression*
   * 
   * @param string $string The string to be tested.
   * @param string $pattern The *Regular Expression Pattern*.
   * @param int $offset Specifies where the beginning of the search should start (in bytes).
   * @return bool Returns `true` if the `string` matches the `$pattern`, or `false` if it does not.
   */
  function preg_test (string $string, string $pattern, int $offset = 0): bool {
    return StringObj::alias('pregTest', ...func_get_args());
  }

  /** An *alias* of {@see StringObj::transform()}
   * 
   * > Transform the capitalization of the `string`
   * 
   * @param string $string The string to be transformed.
   * @param int $transformation A `TRANSFORM_*` constant value indicating how the string is to be transformed.
   * @return string Returns the transformed `$string`.
   * @throws \TypeError Throws a `TypeError` if `$transformation` is invalid.
   */
  function transform (string $string, int $transformation): string {
    return StringObj::alias('transform', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::changeCase} 
   * 
   * > Change the *Case Styling* of the `string`
   * 
   * @param string $string The string to be transformed.
   * @param int $casing_style A `CASING_STYLE_*` namespace constant indicating how the string is to be cased.
   * @return string Returns the transformed `$string`.
   * @throws \TypeError Throws a `TypeError` if `$casing_style` is invalid.
   */
  function change_case (string $string, int $casing_style): string {
    return StringObj::alias('changeCase', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::slice()}
   * 
   * > Slice a string into a piece.

   * - To split a string using substrings, use the `str_replace()` function.
   * - To split a string using complex searches and replacements, use the `preg_replace()` function.
   * 
   * @param string $string The string to be sliced.
   * @param int $start Where the slice begins. 
   * @param int $length Indicates the maximum length of the slice.
   * @return string Returns the sliced `$string`.
   * @throws \OutOfRangeException if the `string` is less than `$start` characters long, or `$length` is *negative* and tries to truncate more characters than are available.
   */
  function slice (string $string, int $start = 0, int $length = null): string {
    return StringObj::alias('slice', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::strReplace()} 
   * 
   * > Replace all occurrences of a search string with a replacement string within the string
   *
   * - To split a string into pieces every variable number of characters, use the {@see slice()} function.
   * - To split a string using more complex searches and replacements, use the {@see preg_replace()} function.
   * 
   * @param string|array $search The Search *Needle*, provided as a single `string`, or as an `array` of needles.
   * @param string|array $replacement The replacement value for each `$search` match, provided as a single `string`, or as an `array` of replacements.
   * @param bool $case_insensitive Indicates if the `$search` match(es) should be matched *Case Insensitively*.
   * @return string Returns the modified `$string`.
   * @see StringObj::strReplace()
   */
  function str_replace (
    string $string, 
    $search, 
    $replacement, 
    $case_insensitive = false
  ): string {
    return StringObj::alias('strReplace', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::pregReplace()}
   * 
   * > Perform a *Global Regular Expression Replacement* on a string
   * 
   * - To split a string into pieces every variable number of characters, use the `slice()` function.
   * - To split a string using simple substrings, use the `str_replace()` function.
   * 
   * @param string $string The string to be modified.
   * @param string|array $pattern The *Regular Expression Pattern*. 
   * @param string|array|callback $replacement The value to replace each string matched by the `$pattern` with. 
   * @param int $limit The maximum number of replacements that can be done for each `$pattern`. **-1** indicates that there is no limit to the number of replacements performed.
   * @return string Returns the modified `$string`.
   */
  function preg_replace (
    string $string, 
    $pattern, 
    $replacement, 
    $limit = -1
  ): string {
    return StringObj::alias('pregReplace', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::addPlural()}
   * 
   * > Appends a plural letter to the string depending on the value of a given number.
   * 
   * @param string $string The string to be changed.
   * @param int $value The value to be evaluated. If this value does not equal **1**, a plural letter will be appended to the string.
   * @param bool $apostrophe Indicates if an *apostrophe* (`'`) should be included with the plural letter.
   * Defaults to `false`.
   * @param string $plural_value The plural value to append to the string.
   * Defaults to the letter `s`.
   * @return string $string Returns the modified `$string`.
   */
  function add_plural (
    string $string, 
    int $value, 
    bool $apostrophe = false,
    string $plural_value = 's'
  ): string {
    return StringObj::alias('addPlural', ...func_get_args());
  }

  /** An *alias* of {@see StringObj::trim()}
   * 
   * > Trim whitespace, or other characters, from the beginning and/or end of the string.
   * 
   * @param string $string The string to be trimmed.
   * @param int $trim_side A `STR_SIDE_*` constant indicating which sides(s) of the string are to be trimmed.
   * @param string $charlist The list of characters that will be trimmed from the string.
   * @return string Returns the modified `$string`.
   */
  function trim (
    string $string, 
    int $trim_side = STR_SIDE_BOTH, 
    string $charlist = " \n\r\t\v\s"
  ): string {
    return StringObj::alias('trim', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::collapse()}
   * 
   * > Collapse whitespace, or other characters, within a string.
   * 
   * @param string $string The string to be collapsed.
   * @param string $charlist The list of characters that will be collapsed in the string.
   * @return string Returns the modified `$string`.
   */
  function collapse (string $string, string $charlist = " \n\r\t\v\s"): string {
    return StringObj::alias('collapse', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::pad()}
   * 
   * > Pad the string to a certain length with another string
   * 
   * @param string $string The string to be padded.
   * @param int $padding_length The desired length of the string.
   * @param string $padding The padding string used to pad the string.
   * @param int $padding_side A `STR_SIDE_*` constant indicating which side(s) of the string are to be padded.
   * @return string Returns the modified `$string`
   */
  function pad (
    string $string, 
    int $padding_length, 
    string $padding = ' ', 
    int $padding_side = STR_SIDE_RIGHT
  ) {
    return StringObj::alias('pad', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::chunk()}
   *  
   * > Split the string into smaller chunks
   * 
   * @param string $string The string to be chunked.
   * @param int $chunk_length The length of a single chunk.
   * @param string $separator The separator character(s) to be placed between chunks.
   * @return string Returns the modified `$string`.
   */
  function chunk (
    string $string, 
    int $chunk_length = 76, 
    string $separator = "\r\n"
  ) {
    return StringObj::alias('chunk', ...func_get_args());
  }

  /** An *alias* of {@see StringObj::encodeHTML()}
   * 
   * > Convert special HTML Characters in a string into *HTML Entities*
   * 
   * @param string $string The string to be encoded.
   * @param bool $encode_everything Indicates if all characters with HTML Character Entity equivalents should be encoded, instead of just the special characters.
   * @return string Returns the encoded `$string`.
   */
  function encode_html (string $string, bool $encode_everything = false): string {
    return StringObj::alias('encodeHTML', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::decodeHTML()}
   * 
   * > Convert *HTML Entities* in a string back to their special HTML Characters.
   * 
   * @param string $string The string to be encoded.
   * @param bool $decode_everything Indicates if all HTML Character Entities should be decoded, instead of just the special characters.
   * @return string Returns the encoded `$string`.
   */
  function decode_html (string $string, bool $decode_everything = false): string {
    return StringObj::alias('decodeHTML', ...func_get_args());
  }
  /** An *alias* of {@see StringObj::stripTags()}
   * 
   * > Strip HTML & PHP tags from a string
   * 
   * @param string $string The string to be stripped.
   * @param null|int|array|string $allowed_tags A list of whitelisted tags as an `int`, `array`, `string`, or `null`.
   * @return StringObj|string Returns the stripped `$string`.
   * @throws \UnexpectedValueException Throws an `UnexpectedValueException` if `$allowed_tags` is not of a valid format.
   */
  function strip_tags (string $string, $allowed_tags = null): string {
    return StringObj::alias('stripTags', ...func_get_args());
  }
  /** An *alias* for {@see StringObj::encodeURL()}
   * 
   * > Converts special characters in the string to their equivalent URL Character Codes.
   * 
   * @param string $string The string to be encoded.
   * @param bool $legacy_encode Indicates if *Legacy URL Encoding* should be performed, determining which specification should be followed when encoding the URL.
   * @return string Returns the modified `$string`.
   */
  function encode_url (string $string, bool $legacy_encode = false): string {
    return StringObj::alias('encodeURL', ...func_get_args());
  }
  /** An *alias* for {@see StringObj::decodeURL()}
   * 
   * > Converts URL Character Codes in the string to their equivalent special characters.
   * 
   * @param string $string The string to be decoded.
   * @param bool $legacy_decode Indicates if *Legacy URL Decoding* should be performed, determining which specification should be followed when decoding the URL.
   * @return string Returns the modified `$string`.
   */
  function decode_url (string $string, bool $legacy_decode = false): string {
    return StringObj::alias('decodeURL', ...func_get_args());
  }
  /** An *alias* for {@see StringObj::encodeID()}
   * 
   * > Encode a string to be used as an identifier
   * 
   * @param string $string The string to be encoded.
   * @param int A `CASING_STYLE_*` namespace constant indicating how the string is to be cased.
   * @return string Returns the modified `$string`.
   */
  function encode_id (string $string, $encoding_style = CASING_STYLE_SNAKE_CASE) {
    return StringObj::alias('encodeID', ...func_get_args());
  }
  /** An *alias* for {@see StringObj::escapeReg()}
   * 
   * > Escape a string for use in a *Regular Expression*.
   * 
   * @param string $string The string to be escaped.
   * @param null|string $delimiter The *Expression Delimiter* to also be escaped.
   * @return string Returns the modified `$string`.
   */
  function escape_reg(string $string, $delimiter = null) {
    return StringObj::alias('escapeReg', ...func_get_args());
  }
  /** An *alias* for {@see StringObj::escapeSQL()}
   * 
   * > Escape a string for use in a SQL Query Statement.
   * 
   * @param string $string The string to be escaped.
   * @return string Returns the modified `$string`.
   * @throws \RuntimeException Throws a `RuntimeException` if the function is called before the `Database` module has been loaded.
   */
  function escape_sql (string $string) {
    return StringObj::alias('escapeSQL', ...func_get_args());
  }
?>