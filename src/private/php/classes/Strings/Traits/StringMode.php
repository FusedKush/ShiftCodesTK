<?php
  namespace ShiftCodesTK\Strings\Traits;

  use ShiftCodesTK\Strings,
      ShiftCodesTK\Strings\Interfaces\StringModeConstants;

  /** Represents the *String Mode* used to operate on strings. */
  trait StringMode {
    /** @var int Indicates the *String Mode* to be used for the {@see StringObj::$string}. */
    protected $stringMode = StringModeConstants::STRING_MODE_AUTO;

    /** Determine the *Resolved String Mode* of a given *String Mode* and *String Encoding*
     * 
     * @param int $string_mode A `STRING_MODE_*` interface constant representing the *String Mode* to check.
     * @param string $encoding The *Character Encoding* to check.
     * @return int Returns an `int` representing the *Resolved String Mode* of the given `$string_mode` and `$encoding` on success.
     */
    public static function determineResolvedStringMode (int $string_mode, string $encoding) {
      if ($string_mode == StringModeConstants::STRING_MODE_AUTO) {
        $string_encodings = [
          Strings\ENCODING_ASCII, 
          Strings\ENCODING_ISO_8859_1
        ];

        if (in_array($encoding, $string_encodings)) {
          return StringModeConstants::STRING_MODE_STRING;
        }
        else {
          return StringModeConstants::STRING_MODE_MB_STRING;
        }
      }
      else {
        return $string_mode;
      }
    }

    abstract public function getResolvedStringMode (
    );

    /** Retrieve the current *String Mode*
     * 
     * @return int Returns an `int` representing the current *String Mode*.
     */
    public function getStringMode (): int {
      return $this->stringMode;
    }
    /** Set the *String Operation Mode*
     * 
     * @param int $string_mode A `STRING_MODE_*` interface constant indicating the *String Mode*.
     * @return bool Returns **true** on success.
     * @throws \UnexpectedValueException if `$string_mode` is not a valid String Mode.
     */
    public function setStringMode (int $string_mode): bool {
      if (!in_array($string_mode, StringModeConstants::STRING_MODE_LIST)) {
        throw new \UnexpectedValueException("\"{$string_mode}\" is not a valid String Mode.");
      }

      $this->stringMode = $string_mode;
      return true;
    }
  }