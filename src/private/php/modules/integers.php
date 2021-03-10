<?php
  /**
   * The `integers` module is responsible for managing `Integers` up to **64 Bits**, as well as *Bitwise Operations & Bitmasks*.
   */
  namespace ShiftCodesTK\Integers;

  /** Definition Constants */
  /** @var string Represents the *Minimum Value* of a *32-Bit Integer*, as a `string`. */
  const MIN_32 = "-2147483648";
  /** @var string Represents the *Maximum Value* of a *32-Bit Integer*, as a `string`. */
  const MAX_32 = "2147483647";
  /** @var string Represents the *Minimum Value* of a *64-Bit Integer*, as a `string`. */
  const MIN_64 = "-9223372036854775808";
  /** @var string Represents the *Maximum Value* of a *64-Bit Integer*, as a `string`. */
  const MAX_64 = "9223372036854775807";

  /** @var int The integer is represented by a `string`. */
  const INT_TYPE_STRING = 1;
  /** @var int The integer is represented by an `int`. */
  const INT_TYPE_INT = 2;
  /** @var int The integer is represented by a `GMP` object. */
  const INT_TYPE_GMP = 4;
  /** @var int The integer is represented by a `BigInt` object. */
  const INT_TYPE_BIGINT = 8;

  /** Current Platform Support Constants */
  /** @var int Represents the *Minimum Value* of an `int` on the current platform. */
  const PLATFORM_MIN = PHP_INT_MIN;
  /** @var int Represents the *Maximum Value* of an `int` on the current platform. */
  const PLATFORM_MAX = PHP_INT_MAX;
  /** @var bool Indicates if the current platform supports **32-Bit Integers**. */
  define("ShiftCodesTK\Integers\PLATFORM_32_BIT_SUPPORT", (new BigInt(PLATFORM_MAX))->greater(MAX_32));
  /** @var bool Indicates if the current platform supports **64-Bit Integers**. */
  define("ShiftCodesTK\Integers\PLATFORM_64_BIT_SUPPORT", (new BigInt(PLATFORM_MAX))->greater(MAX_64));

  /** @var array An `Indexed Array` of possible *Bitwise Flag Values*. */
  define("ShiftCodesTK\Integers\BITMASK_VALUES", (function () {
    $values = [
      "-1"
    ];
    
    for ($i = new BitwiseInt(1); $i->less(PLATFORM_MAX, true); $i->lshift(1)) {
      $values[] = $i->get_int();
    }

    return $values;
  })());

  /** Functions */
  /** Convert a numeric value into a `GMP` object.
    * 
    * @param string|int|\GMP|BigInt $int The integer value to convert.
    * @return \GMP|false Returns a `GMP` object representing the `$int` value on success. If `$int` is invalid, returns **false**.
    */
  function conv2gmp ($int) {
    $intValue = (function () use ($int) {
      if ((is_string($int) && is_numeric($int)) || is_int($int)) {
        return $int;
      }
      else if (is_object($int) && is_a($int, BigInt::class)) {
        return $int->get_bigint();
      }

      return false;
    })();

    if ($intValue) {
      return gmp_init($intValue);
    }

    return false;
  }
  /** Convert an `int`,`GMP` object, or `BigInt` object into a `string`.
    * 
    * @param int|\GMP|BigInt $int The integer value to convert.
    * @return string|false Returns a `string` representing the `$int` value on success. If `$int` is invalid, returns **false**.
    */
  function conv2str ($int) {
    $intValue = (function () use ($int) {
      if (is_int($int)) {
        return $int;
      }
      elseif (is_object($int)) {
        if (is_a($int, BigInt::class)) {
          return $int->get_bigint();
        }
        else if (is_a($int, \GMP::class)) {
          return $int;
        }
      }

      return false;
    })();

    if ($intValue) {
      return gmp_strval($intValue);
    }

    return false;
  }
  /** Convert a `string`, `GMP` object, or `BigInt` object into an `int`.
    * 
    * @param string|\GMP|BigInt $int The integer value to convert.
    * @return int|string|false 
    * Returns an `int` representing the `$int` value on success. 
    * This requires the `$int` to be within the *Platform Integer Size Limitations*. Otherwise, returns a `string` representing the `$int`.
    * If `$int` is invalid, returns **false**.
    */
  function conv2int ($int) {
    $intValue = (function () use ($int) {
      if (is_string($int) && is_numeric($int)) {
        return $int;
      }
      if (is_object($int)) {
        if (is_a($int, BigInt::class)) {
          return $int->get_bigint();
        }
        if (is_a($int, \GMP::class)) {
          return $int;
        }
      }

      return false;
    })();

    if ($intValue && \ShiftCodesTK\Validations\check_range($intValue, [ 'min' => PLATFORM_MIN, 'max' => PLATFORM_MAX ])) {
      return gmp_intval($intValue);
    }

    return false;
  }
?>