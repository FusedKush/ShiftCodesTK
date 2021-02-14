<?php 
  namespace ShiftCodesTK\Timestamps;

use OutOfRangeException;
use ShiftCodesTK\Integers,
      ShiftCodesTK\Strings;

  /** Represents a *Timestamp Integer* with milliseconds */
  class TimestampInt extends Integers\BigInt {
    // /** @var string A `String Integer` representing the multiplier for `float` conversions. */
    // private const TS_STRING_MULTIPLIER = "1000"; 
    /** @var string A `String Integer` representing the multiplier for `string` conversions. */
    private const TS_STRING_MULTIPLIER = "1000000";
    // /** @var int An `int` representing the number of significant digits for `float` calculations. */
    // private const TS_STRING_DIGITS = 3;
    /** @var int An `int` representing the number of significant digits for `string` calculations. */
    private const TS_STRING_DIGITS = 6;
    /** @var string A `DateTime` *Format String* used to convert to and from `DateTime` objects. */
    private const TS_DATETIME_FORMAT = '\0\.u U';

    /** @var int Represents a *Timestamp* as an `int`. This is not to be confused with a `TimestampInt` object. E.g. `1613156277206098` */
    public const TS_TYPE_INT = 1;
    /** @var int Represents a *Timestamp* as a `float`. E.g. `1613156277.206098` */
    public const TS_TYPE_FLOAT = 2;
    /** @var int Represents a *Timestamp* as a *Timestamp String*. E.g. `0.20609800 1613156277` */
    public const TS_TYPE_STRING = 4;
    /** @var int Represents a *Timestamp* as a `DateTime` object. */
    public const TS_TYPE_DATETIME = 8;

    /** @var Bitmask The `Bitmask` object responsible for the `TS_TYPE_*` class constants. */
    private static $TS_TYPE_BITMASK = null;

    /** 
     * @var int Indicates how precise the *Milliseconds* of the timestamp are. 
     * - **3** designates a *Low-Precision* Timestamp, while **6** designates a *High-Precision* one.
     */
    private $precision = null;
    
    /** Get the *Millisecond Precision* for a *Timestamp Variable*.
     * 
     * @param int $format A `TS_TYPE_*` class constant indicating the *format* of the `$var`.
     * 
     * | Type | Example `$var` | Description |
     * | --- | --- | --- |
     * | `TS_TYPE_INT` | `1613156277206098` | Represents an `int` or `String Integer`. |
     * | `TS_TYPE_FLOAT` | `1613156277.206098` | Represents a `float` or `String Float`. |
     * | `TS_TYPE_STRING` | `0.20609800 1613156277` | Represents a *Timestamp `String`*. |
     * | `TS_TYPE_DATETIME` | | Represents a `DateTime` object. |
     * @param string|int|float|DateTime $var The variable to convert to a `TimestampInt`. The format of the `$var` must match the format provided by `$format`. 
     * @return int|false Returns an `int` representing the *Millisecond Precision* of the provided `$var` on success. Returns **false** if an error occurred.
     */
    public static function get_timestamp_precision ($format, $var) {
      $invalidTypeError = function () use ($format, $var) {
        $providedFormat = self::$TS_TYPE_BITMASK->get_flag($format);
        $providedValueType = gettype($format);

        trigger_error("The Provided Timestamp Format was \"{$providedFormat}\", \"{$providedValueType}\" provided.", E_USER_WARNING);
      };

      if ($format === self::TS_TYPE_INT) {
        if (is_int($var) || is_string($var) && is_numeric($var)) {
          $baseFullDigits = Strings\strlen(self::get_current_timestamp()->get_int());
          $varDigits = Strings\strlen($var);

          return ($baseFullDigits - $varDigits) <= 1
                 ? 6
                 : 3;
        }
        else {
          $invalidTypeError();
        }
      }
      else if ($format === self::TS_TYPE_FLOAT) {
        if (is_float($var) || is_string($var) && is_numeric($var)) {
          $pieces = Strings\explode($var, '.', 2);

          if ($pieces && count($pieces) == 2) {
            $digits = Strings\strlen($pieces[1]);

            return $digits === 6
                   ? 6
                   : 3;
          }
          else {
            trigger_error("An invalid Float or String Float was provided.", E_USER_WARNING);
          }
        }
        else {
          $invalidTypeError();
        }
      }
      else if ($format === self::TS_TYPE_STRING) {
        if (is_string($var)) {
          $pieces = Strings\explode($var, ' ');

          if ($pieces && count($pieces) == 2) {
            $digits = (new Strings\StringObj($pieces[0]))
                      ->slice(2)
                      ->trim(Strings\STR_SIDE_RIGHT, "0")
                      ->strlen();
            
            return $digits === 6
                   ? 6
                   : 3;
          } 
          else {
            trigger_error("An invalid Timestamp String was provided.", E_USER_WARNING);
          }
        }
        else {
          $invalidTypeError();
        }
      }
      else if ($format === self::TS_TYPE_DATETIME) {
        if (is_object($var) && (is_a($var, \DateTime::class) || is_a($var, \DateTimeImmutable::class))) {
          $ms = $var->format('u');
          $digits = Strings\strlen($ms);

          return $digits === 6
                 ? 6
                 : 3;
        }
        else {
          $invalidTypeError();
        }
      }
    }
    /** Create a new `TimestampInt` from a specified format.
     * 
     * @param int $format A `TS_TYPE_*` class constant indicating the *format* of the `$var`.
     * 
     * | Type | Example `$var` | Description |
     * | --- | --- | --- |
     * | `TS_TYPE_INT` | `1613156277206098` | Converts an `int` or `String Integer` into the `TimestampInt`. |
     * | `TS_TYPE_FLOAT` | `1613156277.206098` | Converts a `float` or `String Float` into the `TimestampInt`. |
     * | `TS_TYPE_STRING` | `0.20609800 1613156277` | Converts a *Timestamp `String`* into the `TimestampInt`. |
     * | `TS_TYPE_DATETIME` | | Converts a `DateTime` object into the `TimestampInt`. |
     * @param string|int|float|DateTime $var The variable to convert to a `TimestampInt`. The format of the `$var` must match the format provided by `$format`. 
     * @return TimestampInt|false Returns the new `TimestampInt` on success, or **false** if an error occurred.
     */
    public static function create_from ($format, $var) {
      if (self::$TS_TYPE_BITMASK->check_flag(null, $format)) {
        $precision = self::get_timestamp_precision($format, $var);

        if ($precision !== false) {
          $multiplier = "1" . str_repeat("0", $precision);
          $timestampInt = false;
  
          if ($format === self::TS_TYPE_INT) {
            return new TimestampInt($var, $precision);
          }
          else if ($format === self::TS_TYPE_FLOAT) {
            $timestampInt = bcmul($var, $multiplier);

            return new TimestampInt($timestampInt, $precision);
          }
          else if ($format === self::TS_TYPE_STRING) {
            $pieces = Strings\explode($var, ' ');
            /** @var BigInt 😉 */
            $secInt = (new Integers\BigInt((int) $pieces[1], true))->mul($multiplier);
            $msInt = (new Integers\BigInt((int) round(bcmul($pieces[0], $multiplier, $precision)), true));
            $timestampInt = $secInt->add($msInt)->get_int();
            
            return new TimestampInt($timestampInt, $precision);
          }
          else if ($format === self::TS_TYPE_DATETIME) {
            return TimestampInt::create_from(self::TS_TYPE_STRING, $var->format(self::TS_DATETIME_FORMAT));
          }
        }
      }
      else {
        trigger_error("The Timestamp Format \"{$format}\" is invalid.", E_USER_WARNING);
      }

      return false;
    }
    /** Get the *Current Timestamp* as a `TimestampInt`.
     * 
     * @param bool $less_precision Indicates if a *Low-Precision* Timestamp should be returned instead of a *High-Precision* one. 
     * - *Low-Precision* Timestamps are shorter than the *High-Precision* ones, saving space when the additional precision is not needed.
     * @return TimestampInt Returns a `TimestampInt` representing the *Current Timestamp*. 
     */
    public static function get_current_timestamp ($less_precision = false) {
      $timestampString = microtime($less_precision);

      return TimestampInt::create_from(
        $less_precision
          ? self::TS_TYPE_FLOAT
          : self::TS_TYPE_STRING, 
        $timestampString
        );
    }

    /** Initialize a new `TimestampInt`
     * 
     * @param string|int|\GMP|BigInt|null $timestamp The timestamp to pass to the `TimestampInt`.
     * @param int $precision If it has already been determined, the *Millisecond Precision* of the `$timestamp`. This property should be omitted if the *Timestamp Precision* should automatically be determined.
     * @return BigInt Returns the new `BigInt`.
     * @throws UnexpectedValueException if the `$timestamp` is of an unknown format.
     */
    public function __construct($timestamp = null, $precision = null) {
      if (!isset(self::$TS_TYPE_BITMASK)) {
        self::$TS_TYPE_BITMASK = new Integers\Bitmask([
          'TS_TYPE_INT',
          'TS_TYPE_FLOAT',
          'TS_TYPE_STRING',
          'TS_TYPE_DATETIME'
        ]);
      }

      $this->set_immutable(false);

      if ($timestamp) {
        $result = $this->set_timestamp($timestamp, $precision);

        if ($result === false) {
          throw new \UnexpectedValueException("The provided timestamp is in an unsupported format.");
        }
      }
    }

    /** Set the *Timestamp Integer* of the `TimestampInt`.
     * 
     * @param string|int $timestamp The timestamp to be set. Must be an `int` or *String `Int`*.
     * @param int $precision If it has already been determined, the *Millisecond Precision* of the `$timestamp`. This property should be omitted if the *Timestamp Precision* should automatically be determined.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function set_timestamp ($timestamp, $precision = null) {
      $precisionValue = $precision ?? self::get_timestamp_precision(self::TS_TYPE_INT, $timestamp);

      if ($precisionValue !== false) {
        if ($precisionValue === 3 || $precisionValue === 6) {
          $result = $this->set_int($timestamp);
  
          if ($result !== false) {
            $this->precision = $precisionValue;
            return true;
          }
        }
        else {
          trigger_error("\"{$precisionValue}\" is not a valid Timestamp Precision Value.");
        }
      }

      return false;
    }
    /** Get the `TimestampInt` in a specified format.
     * 
     * @param int $format A `TS_TYPE_*` class constant indicating the *format* to convert the `TimestampInt` into.
     * 
     * | Type | Example Result | Description |
     * | --- | --- | --- |
     * | `TS_TYPE_INT` | `1613156277206098` | Returns the `String Integer` of the `TimestampInt`. |
     * | `TS_TYPE_FLOAT` | `1613156277.206098` | Converts the `TimestampInt` into a `float` |
     * | `TS_TYPE_STRING` | `0.20609800 1613156277` | Converts the `TimestampInt` into a *Timestamp `String`* |
     * | `TS_TYPE_DATETIME` | | Converts the `TimestampInt` into a `DateTime` object. |
     * @return string|\DateTime|false 
     * On success, returns a `string` representing the `TimestampInt`. 
     * If `$format` is `TS_TYPE_DATETIME`, the `TimestampInt` is returned as a `DateTime` object.
     * Returns **false** if an error occurs.
     */
    public function get_as ($format) {
      if (self::$TS_TYPE_BITMASK->check_flag(null, $format)) {
        $divider = "1" . str_repeat("0", $this->precision);

        if ($format === self::TS_TYPE_INT) {
          return $this->get_int();
        }
        else if ($format === self::TS_TYPE_FLOAT) {
          return bcdiv((string) $this->int, $divider, $this->precision);
        }
        else if ($format === self::TS_TYPE_STRING) {
          $float = $this->get_as(self::TS_TYPE_FLOAT);
          // $float = bcdiv((string) $this->int, $divider, $this->precision);

          if ($float !== null) {
            $pieces = Strings\explode($float, '.', 2);
            
            return "0.{$pieces[1]} {$pieces[0]}";
          }
        }
        else if ($format === self::TS_TYPE_DATETIME) {
          $float = $this->get_as(self::TS_TYPE_STRING);

          if ($float !== false) {
            return \DateTime::createFromFormat(self::TS_DATETIME_FORMAT, $float, new \DateTimeZone('UTC'));
          }
        }
      }
      else {
        trigger_error("The Timestamp Format \"{$format}\" is invalid.", E_USER_WARNING);
      }

      return false;
    }
  }

  // Initialize Static Properties
  new TimestampInt();
?>