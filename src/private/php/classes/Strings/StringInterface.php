<?php
  namespace ShiftCodesTK\Strings;

  /** The `StringInterface` is responsible for the methods used to query and manipulate a string. */
  interface StringInterface {
    /** @var array A list of *String Methods* that manipulate the string. */
    public const MANIPULATION_METHODS = [
      'transform',
      'slice',
      'str_replace',
      'preg_replace',
      'add_plural',
      'trim',
      'collapse',
      'encode_html',
      'decode_html',
      'strip_tags',
      'encode_url',
      'decode_html',
      'strip_tags',
      'encode_url',
      'decode_url',
      'encode_id',
      'encode_sql'
    ];

    /** @var int Will attempt to detect the appropriate mode to use for strings. This is the default behavior. **/
    public const STRING_MODE_AUTO = 1;
    /** @var int Indicates that *String Mode* should be used for strings. **/
    public const STRING_MODE_STRING = 2;
    /** @var int Indicates that *Multi-Byte String Mode* should be used for strings. **/
    public const STRING_MODE_MB_STRING = 4;

    public function check_encoding (string $encoding = ENCODING_UTF_8, bool $throw_error = false);
    public function get_encoding ();
    public function strlen ();
    public function char ();
    public function firstchar ();
    public function lastchar ();
    public function split (int $length = 1, bool $return_string_array = false);
    public function explode (string $delimiter = ' ', int $limit = null, bool $return_string_array = false);
    public function substr (int $start = 0, int $length = null, bool $throw_errors = false);
    public function substr_pos (string $search, int $offset = 0, int $flags = 0);
    public function substr_check (string $search, int $offset = 0, int $flags = 0);
    public function substr_count (string $search, int $offset = 0, int $length = null, int $flags = 0);
    public function preg_match (string $pattern, int $flags = 0, int $offset = 0);
    public function preg_test (string $pattern, int $offset = 0);
    public function transform (int $transformation);
    public function slice (int $start = 0, int $length = null);
    public function str_replace ($search, $replacement, $case_insensitive = false);
    public function preg_replace ($pattern, $replacement, $limit = -1);
    public function add_plural (int $value, $apostrophe = false);
    public function trim (int $trim_side = STR_SIDE_BOTH, string $charlist = " \n\r\t\v\s");
    public function collapse (string $charlist = " \n\r\t\v\s");
    public function pad (int $padding_length, string $padding = ' ', int $padding_side = STR_SIDE_RIGHT);
    public function chunk (int $chunk_length = 76, string $separator = "\r\n");
    public function encode_html (bool $encode_everything = false);
    public function decode_html (bool $decode_everything = false);
    public function strip_tags ($allowed_tags = null);
    public function encode_url (bool $legacy_encode = false);
    public function decode_url (bool $legacy_decode = false);
    public function encode_id ($encoding_style = ENCODE_ID_SNAKE_CASE);
    public function escape_reg ($delimiter = null);
    public function escape_sql ();
  }
?>