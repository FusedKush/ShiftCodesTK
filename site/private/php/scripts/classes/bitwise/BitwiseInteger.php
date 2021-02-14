<?php
  namespace ShiftCodesTK\Bitwise;
  use ShiftCodesTK\Strings;

  /** Represents an `integer` that can be manipulated using *Bitwise Operations* */
  class BitwiseInteger {
    /** @var bool Indicates if the `BitwiseInteger` is *Immutable*, determining the behavior and return value of most methods. */
    protected $immutable = null;
    /** @var bool Indicates if method calls that return *Bitwise Integers* should guarantee a `string` being returned. */
    protected $always_string = null;

    /** @var int|string The computed *Bitwise Integer*. 
     * - If 64 Bits is available, the *Bitwise Integer* as an `int`. Otherwise, a `string` identical to `$integer_string`. 
     * - You can use `update_integer()` and `
     **/
    protected $integer = null;
    /** @var string The computed *Bitwise Integer*, as a `string`. Identical to `$integer`, but guaranteed to be a `string`. */
    protected $integer_string = null;
    /** @var \GMP The *Bitwise Integer*, as a `GMP` object. */
    protected $integer_gmp = null;

    /** Handle the result of a *Bitwise Calculation* on two Bitwise Integers.
     * 
     * @param string|int|\GMP $result The result of the *Bitwise Calculation*.
     * @return BitwiseInteger Returns the `BitwiseInteger` representing the result of the *Bitwise Operation*. If the `BitwiseInteger` is *Immutable*, returns a new `BitwiseInteger` representing the updated integer.
     */
    private function bitwise_calc ($result) {
      $resultInteger = is_object($result)
                       ? $this->convert_integer($result)
                       : $result;

      if ($this->immutable) {
        return $this->copy_integer($resultInteger);
      }
      else {
        $this->integer_gmp = $result;

        return $this->update_integer($result);
      }
    }

    /** Converts a *Bitwise Integer Value* into another format passed on the format it was provided in.
     * 
     * | Provided Type | Converted Type | Details |
     * | --- | --- | --- |
     * | `int`\|`float` | `string` | Converts the `$integer` into a `string`. |
     * | `string` | `int`\|`string` | If **64-Bits** are supported, returns the `$integer` as an `int`. Otherwise, returns the `$integer` as a `string`. |
     * | `GMP` | `string`\|`int` | If `$::$always_return_string` is **true**, the `GMP` object will always be converted into a `string`. Otherwise, an `int` may be returned if **64-Bits** are supported. |
     * 
     * @param int|float|string|\GMP $integer The *Bitwise Integer Value* being converted.
     * @return string|int|false Depending on the providing `$integer`, returns a `string` or `array` representing the `Bitwise Integer Value`. If an unknown value type is provided as the `$integer`, returns **false**.
     */
    protected function convert_integer ($integer) {
      if (is_int($integer) || is_float($integer)) {
        return (string) $integer;
      }
      else if (is_string($integer)) {
        if (\ShiftCodesTK\INT_TYPE == "64-Bit" && ((int) ($integer - 1) >= PHP_INT_MIN && (int) ($integer + 1) <= PHP_INT_MAX)) {
          return (int) $integer;
        }

        return $integer;
      }
      else if (is_object($integer) && is_a($integer, 'GMP')) {
        if (!$this->always_string && \ShiftCodesTK\INT_TYPE == "64-Bit") {
          return gmp_intval($integer);
        }
        else {
          return gmp_strval($integer);
        }
      }

      return false;
    }
    /** Update the stored *Bitwise Integer Value*.
     * 
     * **Updated Properties**
     * 
     * | Property Name | Updated Value |
     * | --- | --- |
     * | `$::integer` | If **64-Bits** are supported, stores the `$integer` as an `int`, otherwise stores it as a `string`. |
     * | `$::integer_str` | Stores the `$integer` as a `string`. Identical to `$integer`, but guaranteed to be a `string`. |
     * | `$::integer_gmp` | If `$::integer_gmp` hasn't been initialized yet, a new `GMP` object is created and stored for the `$integer`. |
     * 
     * @param string|int|GMP $integer The new *Bitwise Integer Value* to be saved. If omitted, the `$::integer_gmp` will be checked for changes.
     * @return BitwiseInteger Returns the updated `BitwiseInteger` on success. If the `BitwiseInteger` is *Immutable*, returns a new `BitwiseInteger` representing the updated object.
     */
    protected function update_integer ($integer = null) {
      $newValue = (function () use ($integer) {
        $newValue = $integer;

        if (!isset($integer)) {
          $newValue = $this->integer_gmp;
        }
        if (is_object($integer) && is_a($integer, 'GMP')) {
          $newValue = $this->convert_integer($integer);
        }

        return (string) $newValue;
      })();
      
      if ($newValue !== $this->integer) {
        if ($this->immutable && $this->integer) {
          return $this->copy_integer($newValue);
        }
        else {
          $this->integer = \ShiftCodesTK\INT_TYPE == '64-Bit'
                           ? $this->convert_integer($newValue)
                           : $newValue;
          $this->integer_string = $newValue;
    
          if ($this->integer_gmp === null) {
            $this->integer_gmp = gmp_init($integer, 10);
          }

          return $this;
        }
      }
    }

    /** Get the *Stored Bitwise Integer*.
     * 
     * @return string|int Returns the *Stored Bitwise Integer* as a `string` or an `int`, depending on the `always_string` mode.
     */
    public function get_integer () {
      if ($this->always_string) {
        return $this->integer_string;
      }
      else {
        return $this->integer;
      }
    }
    /** Make a copy of the current `BitwiseInteger`.
     * 
     * You can also use the `clone BitwiseInteger` method to perform the same task, however, without the option of a custom `$integer`.
     * 
     * @param string|int|float $integer If available, the new *Bitwise Integer* to be set. If omitted, the Bitwise Integer from the current `BitwiseInteger` object will be used.
     * @return BitwiseInteger Returns the copied `BitwiseInteger` object on success.
     */
    public function copy_integer ($integer = null) {
      return new BitwiseInteger($integer ?? $this->integer_string, $this->immutable, $this->always_string);
    }
    /** Gets or Sets one or more *`BitwiseInteger` Options*
     * 
     * | Option | Type | Default | Description |
     * | --- | --- | --- | --- |
     * | `immutable` | `bool` | `false` | Indicates if the *Bitwise Integer* is to be *Immutable* or *Mutable*. This will affect the behavior of most methods responsible for modifying the Bitwise Integer. |
     * | `always_string` | `bool` | `true` | Indicates if *Bitwise Integers* are to always be returned as a `string`. This will affect the behavior of any methods that return a `Bitwise Integer`. |
     * 
     * @param array $options An `array` representing the options to be set. If omitted, retrieves the current options as an `array`
     * @return array|BitwiseInteger 
     * If `$options` is omitted, returns an `array` representing the current *`BitwiseInteger` Options*. 
     * If `$options` is provided, returns the updated `BitwiseInteger` object.
     * - Emits a *Warning* if an `$option` contains an invalid value.
     */
    public function bitwise_integer_options (array $options = null) {
      if (!isset($options)) {
        return [
          'immutable'     => $this->immutable,
          'always_string' => $this->always_string
        ];
      }
      else {
        $validOptions = [ 'immutable', 'always_string' ];
  
        foreach ($validOptions as $optionName) {
          $optionValue = $options[$optionName] ?? null;
  
          if (isset($optionValue)) {
            if (is_bool($optionValue)) {
              $this->$optionName = $optionValue;
            }
            else {
              trigger_error("\"{$optionName}\" must be a bool, but an \"" . gettype($optionValue) . "\" was provided.", E_USER_WARNING);
            }
          }
        }

        return $this;
      }
    }

     /** Set or Clear bits in the *Stored Bitwise Integer*.
     * 
     * @param bool $bit_value Indicates if the `$bits` are to be *Set* (`1`) or *Cleared* (`0`). **True** will *Set* the bits while **false** will *Clear* them.
     * @param string|int $bits The bits to be set or cleared, specified by their *Index*. **0** represents the least significant bit.
     * @return BitwiseInteger Returns the updated `BitwiseInteger` once all of the `$bits` have been set or cleared. If the `BitwiseInteger` is *Immutable*, returns a new `BitwiseInteger` representing the modified integer.
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

        gmp_setbit($bitwiseInteger->integer_gmp, $bit, $bit_value);
      }

      return $this->immutable
             ? $bitwiseInteger
             : $this->update_integer();
    }
    /** Test if a specified bit is set in the *Stored Bitwise Integer*.
     * 
     * @param string|int $bit The bit to be tested, specified by its *Index*. **0** represents the least significant bit.
     * @return bool Returns **true** if the `$bit` is set in the *Stored Bitwise Integer*, or **false** if it is not.
     */
    public function test_bit ($bit) {
      return gmp_testbit($this->integer_gmp, $bit);
    }
    /** Shift the bits of the *Stored Bitwise Integer* to the left.
     * 
     * > `$::integer << $integer` Shift the bits of `$::integer`, `$integer` steps to the left.
     * 
     * @param int $integer An `int` indicating how many steps the *Bitwise Integer* should be shifted to the left. Each *step* multiplies the *Bitwise Integer* by two. 
     * @return BitwiseInteger Returns the updated `BitwiseInteger`. If the `BitwiseInteger` is *Immutable*, returns a new `BitwiseInteger` representing the updated integer.
     */
    public function lshift ($integer) {
      $result = gmp_mul($this->integer_string, gmp_pow(2, $integer));

      return $this->update_integer($result);
    }
    /** Shift the bits of the *Stored Bitwise Integer* to the right.
     * 
     * > `$::integer >> $integer` Shift the bits of `$::integer`, `$integer` steps to the right.
     * 
     * @param int $integer An `int` indicating how many steps the *Bitwise Integer* should be shifted to the right. Each *step* divides the *Bitwise Integer* by two. 
     * @return BitwiseInteger Returns the updated `BitwiseInteger`. If the `BitwiseInteger` is *Immutable*, returns a new `BitwiseInteger` representing the updated integer.
     */
    public function rshift ($integer) {
      $result = gmp_div($this->integer_string, gmp_pow(2, $integer));

      return $this->update_integer($result);
    }

    /** Calculate the Bitwise *AND* of two Bitwise Integers.
     * 
     * > `$::integer & $integer` Bits that are set in both `$::integer` and `$integer` are set. 
     * 
     * @param string|int $integer The *Bitwise Integer* to be calculated with the *Stored Bitwise Integer*.
     * @return BitwiseInteger Returns the updated `BitwiseInteger` representing the result of the *Bitwise AND Operation*. If the `BitwiseInteger` is *Immutable*, returns a new `BitwiseInteger` representing the updated integer.
     */
    public function AND ($integer) {
      $result = gmp_and($this->integer_gmp, $integer);

      return $this->bitwise_calc($result);
    }
    /** Calculate the Bitwise *OR* of two Bitwise Integers.
     * 
     * > `$::integer | $integer` Bits that are set in either `$::integer` or `$integer` are set. 
     * 
     * @param string|int $integer The *Bitwise Integer* to be calculated with the *Stored Bitwise Integer*.
     * @return BitwiseInteger Returns the updated `BitwiseInteger` representing the result of the *Bitwise OR Operation*. If the `BitwiseInteger` is *Immutable*, returns a new `BitwiseInteger` representing the updated integer.
     */
    public function OR ($integer) {
      $result = gmp_or($this->integer_gmp, $integer);

      return $this->bitwise_calc($result);
    }
    /** Calculate the Bitwise *XOR* of two Bitwise Integers.
     * 
     * > `$::integer ^ $integer` Bits that are set in `$::integer` or `$integer` but not both are set. 
     * 
     * @param string|int $integer The *Bitwise Integer* to be calculated with the *Stored Bitwise Integer*.
     * @return BitwiseInteger Returns the updated `BitwiseInteger` representing the result of the *Bitwise XOR Operation*. If the `BitwiseInteger` is *Immutable*, returns a new `BitwiseInteger` representing the updated integer.
     */
    public function XOR ($integer) {
      $result = gmp_xor($this->integer_gmp, $integer);

      return $this->bitwise_calc($result);
    }
    /** Calculate the Bitwise *NOT* of the Bitwise Integer.
     * 
     * > `~ $::integer` Bits that are set in `$::integer` are not set, and vice versa.
     * 
     * @return BitwiseInteger Returns the updated `BitwiseInteger` representing the result of the *Bitwise NOT Operation*. If the `BitwiseInteger` is *Immutable*, returns a new `BitwiseInteger` representing the updated integer.
     */
    public function NOT () {
      $bin = Strings\pad(
        base_convert($this->integer_string, 10, 2),
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

    /** Initialize a new `BitwiseInteger`
     * 
     * @param string $integer The *Bitwise Integer*. Defaults to **0**.
     * @param bool $immutable Indicates if the `BitwiseInteger` is *Immutable*. Defaults to **false**.
     * - If **true**, operations that modify the *Bitwise Integer* instead return a new `BitwiseInteger` representing the modified value, leaving the original integer unchanged.
     * - If **false**, operations that modify the *Bitwise Integer* return the current `BitwiseInteger`, updated with any changed.
     * - You can use `change_options()` to modify this value later.
     * @param bool $always_string Indicates if method calls that return *Bitwise Integers* should guarantee that a `string` is returned. Defaults to **true**.
     * - If **true**, a `string` will *always* be returned. 
     * - If **false**, the return values *may* be an `int` or `string` depending on if **64-Bits** are supported, and if the value exceeds the integer limitations.
     * - You can use `change_options()` to modify this value later.
     * @return BitwiseInteger Returns a new `BitwiseInteger` object representing the `$integer`.
     */
    public function __construct(string $integer = "0", bool $immutable = false, bool $always_string = true) {
      $this->bitwise_integer_options([
        'immutable'     => $immutable,
        'always_string' => $always_string
      ]);
      $this->update_integer($integer);

      return $this;
    }
  }
?>