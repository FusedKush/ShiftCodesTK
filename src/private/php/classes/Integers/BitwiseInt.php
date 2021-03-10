<?php
  namespace ShiftCodesTK\Integers;
  use ShiftCodesTK\Strings;

  /** Represents a *Bitwise Integer* that can be manipulated using *Bitwise Operations* */
  class BitwiseInt extends BigInt {
    /** Constants */
    /** @var int Indicates that the result should be truncated towards **0**. */
    public const DIV_ROUND_ZERO = GMP_ROUND_ZERO;
    /** @var int Indicates that the result should be rounded towards _+infinity_. */
    public const DIV_ROUND_POSINF = GMP_ROUND_PLUSINF;
    /** @var int Indicates that the result should be rounded towards _-infinity_. */
    public const DIV_ROUND_MININF = GMP_ROUND_MINUSINF;

    /** Internal Methods */
    /** Handle the result of a *Bitwise Calculation* on two Bitwise Integers.
     * 
     * @param string|int|\GMP|BigInt $result The result of the *Bitwise Calculation*, a value accepted by `TypeConv`.
     * @return BitwiseInt Returns the `BitwiseInt` representing the result of the *Bitwise Operation*. If the `BitwiseInt` is *Immutable*, returns a new `BitwiseInt` representing the updated integer.
     */
    private function bitwise_calc ($result) {
      if ($this->immutable) {
        return $this->copy_integer(TypeConv::to_str($result));
      }
      else {
        return $this->set_int($result);
      }
    }

    /** Magic Methods */
    /** Initialize a new `BitwiseInt`
     * 
     * @param string $int The *Bitwise Integer*. Defaults to **0**.
     * @param string|int|\GMP|BigInt $int The *Bitwise Integer* to pass to the `BitwiseInt`. Can be any value accepted by `TypeConv`. Defaults to **0**.
     * @param bool $immutable Indicates if the `BitwiseInt` is *Immutable*. Defaults to **false**.
     * - If **true**, operations that modify the *Bitwise Integer* instead return a new `BitwiseInt` representing the modified value, leaving the original integer unchanged.
     * - If **false**, operations that modify the *Bitwise Integer* return the current `BitwiseInt`, updated with any changes.
     * @return BitwiseInt Returns a new `BitwiseInt` object representing the `$int`.
     */
    public function __construct($int = 0, bool $immutable = false) {
      $this->set_immutable($immutable);
      $this->set_int($int);

      return $this;
    }

    /** `BitwiseInt` Methods */
    // /** Set the `BitwiseInt` value.
    //  * 
    //  * @param string|int|\GMP|BigInt $int The new *Bitwise Integer Value* to be saved, any value accepted by `TypeConv`.
    //  * @return BitwiseInt|false 
    //  * Returns the updated `BitwiseInt` on success. 
    //  * If the `BitwiseInt` is *Immutable*, returns a new `BitwiseInt` representing the updated object.
    //  * If `$int` is of an unknown format, returns **false**.
    //  */
    // protected function set_bitwise_int ($int) {
    //   $currentValue = isset($this->int) ? $this->int->get_bigint() : null;
    //   $newValue = TypeConv::to_str($int);
      
    //   if ($newValue) {
    //     if ($newValue !== $currentValue) {
    //       if ($this->immutable && $this->integer) {
    //         return $this->copy_integer($int);
    //       }
    //       else {
    //         $this->int->set_bigint($int);
    //       }

    //       return $this;
    //     }
    //   }

    //   return false;
    // }
    // /** Get the `BitwiseInt` value.
    //  * 
    //  * @return string|int|\GMP|BigInt Returns a value representing the `BigInt`, determined by the current `::$representation`. 
    //  * 
    //  * | Representation | Description |
    //  * | --- | --- |
    //  * | `INT_TYPE_STRING` | The `BitwiseInt` will be returned as a `string`. |
    //  * | `INT_TYPE_INT` | The `BitwiseInt` will be returned as an `int` as long as it fits within the *Integer Size Constraints*. Otherwise, returned as a `string`. |
    //  * | `INT_TYPE_GMP` | The `BitwiseInt` will be returned as a `GMP` object. |
    //  * | `INT_TYPE_BIGINT` | The `BitwiseInt` will be returned as a `BigInt` object. |
    //  */
    // public function get_bitwise_int () {
    //   if ($this->format === INT_TYPE_BIGINT) {
    //     return $this->int;
    //   }
    //   else {
    //     return $this->int->get_bigint($this->format);
    //   }
    // }
    /** Make a copy of the current `BitwiseInt`.
     * 
     * You can also use the `clone BitwiseInt` method to perform the same task, however, without the option of setting a custom `$integer`.
     * 
     * @param string|int|float $int If available, the new *Bitwise Integer* to be set. If omitted, the Bitwise Integer from the current `BitwiseInt` object will be used.
     * @return BitwiseInt Returns the copied `BitwiseInt` object on success.
     */
    public function copy_integer ($int = null) {
      return new BitwiseInt($int ?? $this->int, $this->immutable);
    }

    /** Bit Setting Methods */
    /** Set or Clear bits in the *Stored Bitwise Integer*.
     * 
     * @param bool $bit_value Indicates if the `$bits` are to be *Set* (`1`) or *Cleared* (`0`). **True** will *Set* the bits while **false** will *Clear* them.
     * @param string|int $bits The bits to be set or cleared, specified by their *Index*. **0** represents the least significant bit.
     * @return BitwiseInt Returns the updated `BitwiseInt` once all of the `$bits` have been set or cleared. If the `BitwiseInt` is *Immutable*, returns a new `BitwiseInt` representing the modified integer.
     * @throws \UnexpectedValueException if any of the `$bits` are not a `string`, `int`, or `float`.
     */
    public function set_bits (bool $bit_value = true, ...$bits) {
      $bitwiseInteger = $this->immutable
                        ? $this->copy_integer()
                        : $this;

      foreach ($bits as $bit) {
        if (!is_string($bit) && !is_numeric($bit)) {
          throw new \UnexpectedValueException("\"{$bit}\" is not a String, Integer, or Float.");
        }

        gmp_setbit($bitwiseInteger->int, TypeConv::to_int($bit), $bit_value);
      }

      return $this->immutable
             ? $bitwiseInteger
             : $this;
    }
    /** Test if a specified bit is set in the *Stored Bitwise Integer*.
     * 
     * @param string|int $bit The bit to be tested, specified by its *Index*. **0** represents the least significant bit.
     * @return bool Returns **true** if the `$bit` is set in the *Stored Bitwise Integer*, or **false** if it is not.
     */
    public function test_bit ($bit) {
      return gmp_testbit($this->int, $bit);
    }
    /** Shift the bits of the *Stored Bitwise Integer* to the left.
     * 
     * > `$::int << $int` Shift the bits of `$::int`, `$int` steps to the left.
     * 
     * @param int $int An `int` indicating how many steps the `BitwiseInt` should be shifted to the left. Each *step* multiplies the `BitwiseInt` by two. 
     * @return BitwiseInt Returns the updated `BitwiseInt`. If the `BitwiseInt` is *Immutable*, returns a new `BitwiseInt` representing the updated integer.
     */
    public function lshift (int $int) {
      $result = gmp_mul($this->int, gmp_pow(2, $int));

      return $this->set_int($result);
    }
    /** Shift the bits of the `BitwiseInt` to the right.
     * 
     * > `$::int >> $int` Shift the bits of `$::int`, `$int` steps to the right.
     * 
     * @param int $int An `int` indicating how many steps the `BitwiseInt` should be shifted to the right. Each *step* divides the `BitwiseInt` by two. 
     * @return BitwiseInt Returns the updated `BitwiseInt`. If the `BitwiseInt` is *Immutable*, returns a new `BitwiseInt` representing the updated integer.
     */
    public function rshift (int $int) {
      $result = gmp_div($this->int, gmp_pow(2, $int));

      return $this->set_int($result);
    }

    /** Bitwise Calculation Methods */
    /** Calculate the Bitwise *AND* of two Bitwise Integers.
     * 
     * > `$::int & $int` Bits that are set in both `$::int` and `$int` are set. 
     * 
     * @param string|int|\GMP|BigInt $int The *Bitwise Integer* to be calculated with the `BitwiseInt`. Can be any value accepted by `TypeConv`.
     * @return BitwiseInt|false
     * Returns the updated `BitwiseInt` representing the result of the *Bitwise AND Operation*. 
     * If the `BitwiseInt` is *Immutable*, returns a new `BitwiseInt` representing the updated integer.
     * If `$int` is of an unknown type, returns **false**.
     */
    public function and ($int) {
      $intValue = TypeConv::to_str($int);

      if ($intValue !== false) {
        $result = gmp_and($this->int, $intValue);

        return $this->bitwise_calc($result);
      }

      return false;
    }
    /** Calculate the Bitwise *OR* of two Bitwise Integers.
     * 
     * > `$::int | $int` Bits that are set in either `$::int` or `$int` are set. 
     * 
     * @param string|int|\GMP|BigInt $int The *Bitwise Integer* to be calculated with the `BitwiseInt`. Can be any value accepted by `TypeConv`.
     * @return BitwiseInt|false 
     * Returns the updated `BitwiseInt` representing the result of the *Bitwise OR Operation*. 
     * If the `BitwiseInt` is *Immutable*, returns a new `BitwiseInt` representing the updated integer.
     * If `$int` is of an unknown type, returns **false**.
     */
    public function or ($int) {
      $intValue = TypeConv::to_str($int);

      if ($intValue !== false) {
        $result = gmp_or($this->int, $intValue);

        return $this->bitwise_calc($result);
      }

      return false;
    }
    /** Calculate the Bitwise *XOR* of two Bitwise Integers.
     * 
     * > `$::int ^ $int` Bits that are set in `$::int` or `$int` but not both are set. 
     * 
     * @param string|int|\GMP|BigInt $int The *Bitwise Integer* to be calculated with the `BitwiseInt`. Can be any value accepted by `TypeConv`.
     * @return BitwiseInt|false 
     * Returns the updated `BitwiseInt` representing the result of the *Bitwise XOR Operation*. 
     * If the `BitwiseInt` is *Immutable*, returns a new `BitwiseInt` representing the updated integer.
     * If `$int` is of an unknown type, returns **false**.
     */
    public function xor ($int) {
      $intValue = TypeConv::to_str($int);

      if ($intValue !== false) {
        $result = gmp_xor($this->int, $intValue);

        return $this->bitwise_calc($result);
      }

      return false;
    }
    /** Calculate the Bitwise *NOT* of the `BitwiseInt`.
     * 
     * > `~ $::int` Bits that are set in `$::int` are not set, and vice versa.
     *
     * @return BitwiseInt|false 
     * Returns the updated `BitwiseInt` representing the result of the *Bitwise NOT Operation*. 
     * If the `BitwiseInt` is *Immutable*, returns a new `BitwiseInt` representing the updated integer.
     * If `$int` is of an unknown type, returns **false**. 
     */
    public function not () {
      $bin = Strings\pad(
        base_convert($this->int, 10, 2),
        32, 
        0,
        Strings\STR_SIDE_LEFT
      );

      for ($i = 0; $i < 32; $i++) {
        if ($bin{$i} === "0") {
          $bin{$i} = "1";
        } 
        else if ($bin{$i} === "1") {
          $bin{$i} = "0";
        } 
      }
      $result = bindec($bin);

      return $this->bitwise_calc($result);
    }
  }
?>