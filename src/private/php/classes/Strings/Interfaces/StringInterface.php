<?php
  namespace ShiftCodesTK\Strings\Interfaces;

  use ShiftCodesTK\Strings;

  /** The `StringInterface` is responsible for the methods used to query and manipulate a string. */
  interface StringInterface
    extends StringModeConstants,
            EditingModeConstants 
  {
    /** @var array A list of *String Methods* that manipulate the string. */
    public const MANIPULATION_METHODS = [
      'transform',
      'changeCase',
      'slice',
      'strReplace',
      'pregReplace',
      'addPlural',
      'trim',
      'collapse',
      'encodeHTML',
      'decodeHTML',
      'stripTags',
      'encodeURL',
      'decodeURL',
      'encodeID',
      'encodeSQL'
    ];

    public static function determineResolvedStringMode (
      int $string_mode, 
      string $encoding
    );

    public function checkEncoding (
      string $encoding = Strings\ENCODING_UTF_8, 
      bool $throw_error = false
    );

    public function getEncoding (
    );

    public function getResolvedStringMode (
    );

    public function getStringMode (
    ): int;

    public function setStringMode (
      int $string_mode
    ): bool;

    public function getEditingMode (
    ): int;

    public function setEditingMode (
      int $editing_mode
    ): bool;

    public function strlen (
    );

    public function char (
    );

    public function firstchar (
    );

    public function lastchar (
    );

    public function split (
      int $length = 1
    );

    public function explode (
      string $delimiter = ' ', 
      int $limit = null
    );
    
    public function substr (
      int $start = 0, 
      int $length = null, 
      bool $throw_errors = false
    );

    public function substrPos (
      string $search, 
      int $offset = 0, 
      int $flags = 0
    );

    public function substrCheck (
      string $search, 
      int $offset = 0, 
      int $flags = 0
    );

    public function substrCount (
      string $search, 
      int $offset = 0, 
      int $length = null, 
      int $flags = 0
    );

    public function pregMatch (
      string $pattern, 
      int $flags = 0, 
      int $offset = 0
    );

    public function pregTest (
      string $pattern, 
      int $offset = 0
    );

    public function transform (
      int $transformation
    );

    public function changeCase (
      int $casing_style
    );

    public function slice (
      int $start = 0, 
      int $length = null
    );
    
    public function strReplace (
      $search, 
      $replacement, 
      $case_insensitive = false
    );
    
    public function pregReplace (
      $pattern, 
      $replacement, 
      $limit = -1
    );

    public function addPlural (
      int $value, 
      bool $apostrophe = false,
      string $plural_value = 's'
    );
    
    public function trim (
      int $trim_side = Strings\STR_SIDE_BOTH, 
      string $charlist = " \n\r\t\v\s"
    );

    public function collapse (
      string $charlist = " \n\r\t\v\s"
    );

    public function pad (
      int $padding_length, 
      string $padding = ' ', 
      int $padding_side = Strings\STR_SIDE_RIGHT
    );

    public function chunk (
      int $chunk_length = 76, 
      string $separator = "\r\n"
    );
    
    public function encodeHTML (
      bool $encode_everything = false
    );

    public function decodeHTML (
      bool $decode_everything = false
    );
    
    public function stripTags (
      $allowed_tags = null
    );

    public function encodeURL (
      bool $legacy_encode = false
    );

    public function decodeURL (
      bool $legacy_decode = false
    );

    public function encodeID (
      $casing_style = Strings\CASING_STYLE_SNAKE_CASE
    );

    public function escapeReg (
      $delimiter = null
    );

    public function escapeSQL ();
  }
?>