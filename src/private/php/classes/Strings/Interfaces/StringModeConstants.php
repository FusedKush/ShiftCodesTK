<?php
  namespace ShiftCodesTK\Strings\Interfaces;

  /** Represents constants defined for the {@see \ShiftCodesTK\Strings\Traits\StringMode}. */
  interface StringModeConstants {
    /** @var int Will attempt to detect the appropriate mode to use for strings. 
     * This is the default behavior. 
     **/
    public const STRING_MODE_AUTO = 1;
    /** @var int Indicates that *String Mode* should be used for strings. **/
    public const STRING_MODE_STRING = 2;
    /** @var int Indicates that *Multi-Byte String Mode* should be used for strings. **/
    public const STRING_MODE_MB_STRING = 4;
    
    /** @var int[] A list of the `STRING_MODE_*` interface constants. */
    public const STRING_MODE_LIST = [
      self::STRING_MODE_AUTO,
      self::STRING_MODE_STRING,
      self::STRING_MODE_MB_STRING
    ];
  }