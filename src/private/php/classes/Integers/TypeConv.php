<?php
  namespace ShiftCodesTK\Integers;
  /** 
   * The `TypeConv` is responsible for converting values between *Integer Types*.
   * 
   * Supported *Integer Types* include `strings`, `ints`, `GMP` objects, and `BigInt` objects.
   **/
  abstract class TypeConv {
    /** Get the *Integer Type* of a variable
     * 
     * @param mixed $int The integer being evaluated.
     * @return bool|false Returns the *Integer Type* of the `$int` as a `INT_TYPE_*` class constant. If `$int` is of an unknown type, returns **false**.
     */
    public static function get_int_type ($int) {
      $validTypes = [
        INT_TYPE_STRING => is_string($int) && is_numeric($int),
        INT_TYPE_INT    => is_int($int),
        INT_TYPE_GMP    => is_object($int) && is_a($int, \GMP::class),
        INT_TYPE_BIGINT => is_object($int) && is_a($int, BigInt::class),
      ];
      $validTypeIndex = array_search(true, array_values($validTypes), true);

      if ($validTypeIndex !== false) {
        return array_keys($validTypes)[$validTypeIndex];
      }

      return false;
    }
    /** Convert an *Integer Value* into a `string`.
      * 
      * @param string|int|\GMP|BigInt $int A value representing an *Integer* to be converted.
      * @return string|false Returns a `string` representing the `$int` on success. If `$int` is of an unknown type, returns **false**.
      */
    public static function to_str ($int) {
      $intType = self::get_int_type($int);

      if ($intType) {
        $intValue = $intType === INT_TYPE_BIGINT ? (string) $int : $int;

        return gmp_strval($intValue);
      }

      return false;
    }
    /** Convert a numeric value into an `int`.
      * 
      * @param string|int|\GMP|BigInt $int A value representing an *Integer* to be converted.
      * @return int|string|false 
      * Returns an `int` representing the `$int` value on success. This requires the `$int` to be within the *Platform Integer Size Limitations*. 
      * If `$int` exceeds the *Platform Integer Size Limitations*, returns a `string` representing the `$int`.
      * If `$int` is of an unknown type, returns **false**.
      */
    public static function to_int ($int) {
      $intType = self::get_int_type($int);

      if ($intType) {
        $intValue = self::to_str($intType === INT_TYPE_BIGINT ? (string) $int : $int);

        if ($intValue && \ShiftCodesTK\Validations\check_range($intValue, [ 'min' => PLATFORM_MIN, 'max' => PLATFORM_MAX ])) {
          return gmp_intval($intValue);
        }
        else {
          return $intValue;
        }
      }

      return false;
    }
    /** Convert a numeric value into a `GMP` object.
    * 
    * @param string|int|\GMP|BigInt $int A value representing an *Integer* to be converted.
    * @return \GMP|false Returns a `GMP` object representing the `$int` value on success. If `$int` is of an unknown type, returns **false**.
    */
    public static function to_gmp ($int) {
      $intType = self::get_int_type($int);

      if ($intType) {
        if ($intType == INT_TYPE_GMP) {
          return clone $int;
        }
        $intValue = (function () use ($int, $intType) {
          if ($intType === INT_TYPE_BIGINT) {
            return self::to_str($int);
          }
          
          return $int;
        })();

        return gmp_init($intValue);
      }

      return false;
    }
    /** Convert a numeric value into a `BigInt` object.
    * 
    * @param string|int|\GMP|BigInt $int A value representing an *Integer* to be converted.
    * @return BigInt|false Returns a `BigInt` object representing the `$int` value on success. If `$int` is of an unknown type, returns **false**.
    */
    public static function to_bigint ($int) {
      $intType = self::get_int_type($int);

      if ($intType) {
        $intValue = (function () use ($int, $intType) {
          if ($intType === INT_TYPE_GMP || $intType === INT_TYPE_BIGINT) {
            return self::to_str($int);
          }
          
          return $int;
        })();

        return new BigInt($intValue);
      }

      return false;
    }
  }
?>