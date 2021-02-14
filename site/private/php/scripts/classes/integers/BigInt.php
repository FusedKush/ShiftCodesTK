<?php
  namespace ShiftCodesTK\Integers;
  use ShiftCodesTK\Strings;

  /** A platform-safe representation of an `Integer` up to **64-Bits** */
  class BigInt {
    use \ShiftCodesTK\Immutable;

    /** Constants */    
    /** @var int Indicates that the `BigInt` is *less than* the comparison value. */
    public const COMP_LESS_THAN = -1;
    /** @var int Indicates that the `BigInt` is *equal to* the comparison value. */
    public const COMP_EQUALS = 0;
    /** @var int Indicates that the `BigInt` is *greater than* the comparison value. */
    public const COMP_GREATER_THAN = 1;
    /** @var int Indicates that the *Sign* of the integer is *Positive*. */
    public const INT_SIGN_POSITIVE = 1;
    /** @var int Indicates that the integer is *Zero*, and currently has no *Sign*. */
    public const INT_SIGN_ZERO = 0;
    /** @var int Indicates that the *Sign* of the integer is *Negative*. */
    public const INT_SIGN_NEGATIVE = -1;
    
    /** @var int Indicates that the result should be truncated towards **0**. */
    public const DIV_ROUND_ZERO = GMP_ROUND_ZERO;
    /** @var int Indicates that the result should be rounded towards _+infinity_. */
    public const DIV_ROUND_POSINF = GMP_ROUND_PLUSINF;
    /** @var int Indicates that the result should be rounded towards _-infinity_. */
    public const DIV_ROUND_MININF = GMP_ROUND_MINUSINF;

    /** Properties */
    /** @var \GMP A `GMP` object representing the *Integer*. You can use the `get_int()` and `set_int()` methods to retrieve and set this value respectively. */
    protected $int = null;

    /** Magic Methods */
    /** Casting the `BigInt` to a `string` will return the *Integer Value*.
     * 
     * @return string Returns the string representation of the `BigInt`.
     */
    public function __toString () {
      return $this->get_int(INT_TYPE_STRING);
    }
    /** Invoking the `BigInt` like a `function` will return the *Integer Value*.
     * 
     * @param mixed $args Represents the arguments passed when the `BigInt` was invoked. 
     * - You can supply a value to the `$format` argument of `get_int()` by supplying it as the first argument when invoking the `BigInt`.
     * @return string|int Returns a `string` or `int` representation of the `BigInt`.
     */
    public function __invoke (...$args) {
      return $this->get_int($args[0] ?? INT_TYPE_STRING);
    }
    /** Initialize a new `BigInt`
     * 
     * @param string|int $int An `int` or *Numeric `string`* to set the `BigInt` to. If omitted, the `BigInt` will be set to **0**.
     * @param bool $immutable Indicates if the object should be *Immutable* (**true**) or *Mutable* (**false**).
     * @return BigInt Returns the new `BigInt`.
     * @throws \Exception if `$int` is not a valid `string` or `int`.
     */
    public function __construct($int = null, bool $immutable = false) {
      $this->set_immutable($immutable);

      if (isset($int)) {
        $result = $this->set_int($int);

        if ($result === false) {
          throw new \Exception("The provided integer is not a valid String or Integer.");
        }
      }
    }

    /** `BigInt` Methods */
    /** Get the *`BigInt` Integer Value*
     * 
     * @param int $format An `INT_TYPE_*` constant (except for `INT_TYPE_BIGINT`) indicating how the *Integer Value* should be formatted. Defaults to **INT_TYPE_STRING**.
     * @return string|int|\GMP Returns a `string`, `int`, or `GMP` object representing the `BigInt`. 
     * - If `$format` is **INT_TYPE_STRING**, the `BigInt` will be returned as a `string`.
     * - If `$format` is **INT_TYPE_INT**, the `BigInt` will be returned as an `int` as long as it fits within the *Integer Size Constraints*. Otherwise, returned as a `string`.
     * - If `$format` is **INT_TYPE_GMP**, the `BigInt` will be returned as a `GMP` object.
     * - If `$format` is invalid, returns **false**.
     */
    public function get_int (int $format = INT_TYPE_STRING) {
      $validTypes = [
        INT_TYPE_STRING => 'to_str',
        INT_TYPE_INT    => 'to_int'
      ];

      if ($format == INT_TYPE_GMP) {
        return $this->int;
      }
      else if ($typeFunc = $validTypes[$format]) {
        return TypeConv::$typeFunc($this->int);
      } 

      return false;
    }
    /** Set the *`BigInt` Integer Value*
     * 
     * @param string|int|\GMP|BigInt $int The new *Integer Value* to set. Can be any value accepted by `TypeConv`.
     * @return BigInt|false Returns the updated `BigInt` object on success. If `$int` is of an unknown type, returns **false**.
     */
    public function set_int ($int) {
      $currentValue = isset($this->int) ? TypeConv::to_str($this->int) : null; 
      $intType = TypeConv::get_int_type($int);
      $newValue = $intType === INT_TYPE_STRING
                  ? $int
                  : TypeConv::to_str($int);

      if ($newValue !== false) {
        if ($currentValue != $newValue) {
          $updatedValue = $intType === INT_TYPE_GMP
                          ? $int
                          : TypeConv::to_gmp($newValue);

          if (!isset($this->int)) {
            $this->int = $updatedValue;

            return $this;
          }
          else {
            return $this->change_immutable_object('int', $updatedValue);
          }
        }
  
        return $this;
      }

      return false;
    }
    /** Invoke a `GMP` function that has not been implemented yet using the `BigInt`.
     * 
     * The results will not automatically be applied to the `BigInt` itself.
     * 
     * @param string $func_name The name of the `GMP` function to invoke, without the `gmp_` prefix.
     * @param mixed $args The function arguments to be passed to the function. The first number argument is automatically provided as the `BigInt`.
     * @return mixed Returns the value of the invoked function on success. If `$func_name` is a function that has already been implemented, returns **null** and emits a *Notice*.
     * @throws UnexpectedValueException if `$func_name` does not match a `GMP` function.
     */
    public function gmp_call (string $func_name, ...$args) {
      $supportedFuncs = [
        'add'            => 'add',
        'and'            => 'BitwiseInteger::AND',
        'clrbit'         => 'BitwiseInteger::set_bit',
        'cmp'            => 'less, equals, greater',
        'div'            => 'div',
        'div_q'          => 'div',
        'div_qr'         => 'div',
        'div_r'          => 'div',
        'fact'           => 'fact',
        'gcd'            => 'gcd',
        'init'           => 'TypeConv::to_gmp',
        'intval'         => 'TypeConv::to_int',
        'invert'         => 'inverse',
        'lcm'            => 'lcm',
        'mod'            => 'modulo',
        'mul'            => 'mul',
        'neg'            => 'negate',
        'nextprime'      => 'next_prime',
        'or'             => 'BitwiseInteger::OR',
        'perfect_power'  => 'perfect_power',
        'perfect_square' => 'perfect_square',
        'popcount'       => 'pop',
        'pow'            => 'pow',
        'prob_prime'     => 'is_prime',
        'root'           => 'root',
        'rootrem'        => 'root',
        'setbit'         => 'BitwiseInteger::set_bit',
        'sign'           => 'sign',
        'sqrt'           => 'sqrt',
        'sqrtrem'        => 'sqrt',
        'strval'         => 'TypeConv::to_str',
        'sub'            => 'sub',
        'testbit'        => 'BitwiseInteger::test_bit',
        'xor'            => 'BitwiseInteger::XOR'
      ];

      if (function_exists("gmp_{$func_name}")) {
        $supportedFunc = $supportedFuncs[$func_name] ?? null;
        
        if (!isset($supportedFunc)) {
          return ("gmp_{$func_name}")(TypeConv::to_str($this->int), ...$args);
        }
        else {
          $notice = (function () use ($func_name, $supportedFunc) {
            $notice = "Function gmp_{$func_name}() has already been implemented. See the ";
            $supportedFuncList = Strings\explode($supportedFunc, ', ');

            foreach ($supportedFuncList as $funcName) {
              $notice .= "{$funcName}(), ";
            }

            $notice = Strings\trim($notice, Strings\STR_SIDE_RIGHT, ', ');
            $notice .= ' ';
            $notice .= (new Strings\StringArrayObj([ 'function', 'method' ]))
                       ->add_plural(count($supportedFuncList))
                       ->implode(' / ');
            $notice .= " for more information.";

            return $notice;
          })();

          trigger_error($notice, E_USER_NOTICE);
        }
      }
      else {
        throw new \UnexpectedValueException("\"{$func_name}\" is not a valid GMP Function Name.");
      }
      
      return null;
    }

    /** Math Manipulation Methods */
    /** Add a given value to the `BigInt`.
     * 
     * > `BigInt + $int`
     * 
     * @param string|int|\GMP|BigInt $int The value being added. Can be any value accepted by `TypeConv`.
     * @return BigInt|false Returns the updated `BigInt` object on success. If `$int` is of an unknown type, returns **false**.
     */
    public function add ($int) {
      $intValue = TypeConv::to_str($int);

      if ($intValue !== false) {
        return $this->change_immutable_object('int', gmp_add($this->int, $intValue));
      }

      return false;
    }
    /** Subtract a given value from the `BigInt`.
     * 
     * > `BigInt - $int`
     * 
     * @param string|int|\GMP|BigInt $int The value being subtracted. Can be any value accepted by `TypeConv`.
     * @return BigInt|false Returns the updated `BigInt` object on success. If `$int` is of an unknown type, returns **false**.
     */
    public function sub ($int) {
      $intValue = TypeConv::to_str($int);

      if ($intValue !== false) {
        return $this->change_immutable_object('int', gmp_sub($this->int, $intValue));
      }

      return false;
    }
    /** Multiply the `BigInt` by a given value.
     * 
     * > `BigInt * $int`
     * 
     * @param string|int|\GMP|BigInt $int The value to multiply the `BigInt` by. Can be any value accepted by `TypeConv`.
     * @return BigInt|false Returns the updated `BigInt` object on success. If `$int` is of an unknown type, returns **false**.
     */
    public function mul ($int) {
      $intValue = TypeConv::to_str($int);

      if ($intValue !== false) {
        return $this->change_immutable_object('int', gmp_mul($this->int, $intValue));
      }

      return false;
    }
    /** Divide the `BigInt` by a given value.
     * 
     * > `BigInt / $int`
     * 
     * @param string|int|\GMP|BigInt $int The value to divide the `BigInt` by. Can be any value accepted by `TypeConv`.
     * @param bool $get_remainder Indicates if the *Remainder* of the divison should be returned. The `BigInt` will still be set to the *rounded* result regardless of this argument.
     * @param int $rounding_behavior A `DIV_ROUND_*` constant indicating how the result is to be *rounded*:
     * 
     * | Value | Behavior |
     * | --- | --- |
     * | `DIV_ROUND_ZERO` | The result is truncated towards 0 |
     * | `DIV_ROUND_POSINF` | The result is rounded towards _+infinity_. |
     * | `DIV_ROUND_MININF` | The result is rounded towards _-infinity_. |
     * @return BigInt|array|false 
     * Returns the updated `BigInt` object on success. 
     * If `$get_remainder` is **true**, returns an `array` where the first element represents the *Quotient* and the second the *Remainder*, both as `BigInt` objects.
     * If `$int` is of an unknown type, returns **false**.
     */
    public function div ($int, bool $get_remainder = false, int $rounding_behavior = self::DIV_ROUND_ZERO) {
      $intValue = TypeConv::to_str($int);

      if ($intValue !== false) {
        $args = [ $this->int, $intValue, $rounding_behavior ];
        $result = $this->change_immutable_object('int', gmp_div_q(...$args));
        
        if ($get_remainder) {
          return [
            $result,
            new BigInt(gmp_div_r(...$args))
          ];
        }

        return $result;
      }

      return false;
    }
    /** Calculate the *Factorial* of the `BigInt`.
     * 
     * > `BigInt!`
     * 
     * @return BigInt Returns the updated `BigInt` object.
     */
    public function fact () {
      return $this->change_immutable_object('int', gmp_fact($this->int));
    }
    /** Raise the power of the `BigInt`.
     * 
     * > `BigInt ^ BigInt`
     * 
     * @param int $exp The *positive* exponent to raise the `BigInt` by.
     * @return BigInt|false Returns the updated `BigInt` object on success.
     * - If the `BigInt` and `$exp` are both **0**, the result will be **1**.
     */
    public function pow (int $exp) {
      return $this->change_immutable_object('int', gmp_pow($this->int, $exp));
    }

    /** Math Querying Methods */
    /** Get the *Greatest Common Divisor* of the `BigInt` and a given value.
     * 
     * @param string|int|\GMP|BigInt $int The value to compare with the `BigInt`. Can be any value accepted by `TypeConv`.
     * @return int|false Returns an `int` representing the *Greatest Common Divisor* of the `BigInt` and `$int`. If `$int` is of an unknown type, returns **false**.
     */
    public function gcd ($int) {
      $intValue = TypeConv::to_str($int);

      if ($intValue) {
        return TypeConv::to_int(gmp_gcd($this->int, $intValue));
      }

      return false;
    }
    /** Get the *Least Common Multiple* of the `BigInt` and a given value.
     * 
     * @param string|int|\GMP|BigInt $int The value to compare with the `BigInt`. Can be any value accepted by `TypeConv`.
     * @return int|false Returns an `int` representing the *Least Common Multiple* of the `BigInt` and `$int`. If `$int` is of an unknown type, returns **false**.
     */
    public function lcm ($int) {
      $intValue = TypeConv::to_str($int);

      if ($intValue) {
        return TypeConv::to_int(gmp_lcm($this->int, $intValue));
      }

      return false;
    }
    /** Get the *Inverse Modulo* of the `BigInt` and a given value.
     * 
     * > `- BigInt % $int`
     * 
     * @param string|int|\GMP|BigInt $int The value to compare with the `BigInt`. Can be any value accepted by `TypeConv`.
     * @return int|false|null
     * Returns an `int` representing the *Inverse Modulo* of the `BigInt` modulo `$int`. 
     * Returns **false** if an inverse does not exist. 
     * If `$int` is of an unknown type, returns **null**.
     */
    public function inverse ($int) {
      $intValue = TypeConv::to_str($int);

      if ($intValue) {
        $result = gmp_invert($this->int, $intValue);

        if ($result !== false) {
          return TypeConv::to_int($result);
        }
        
        return false;
      }
      
      return null;
    }
    /** Get the *Modulo* of the `BigInt` and a given value.
     * 
     * > `BigInt % $int`
     * 
     * @param string|int|\GMP|BigInt $int The value to compare with the `BigInt`. The sign will be ignored. Can be any value accepted by `TypeConv`.
     * @return int|false Returns an `int` representing the *Modulo* of the `BigInt` modulo `$int`. If `$int` is of an unknown type, returns **false**.
     */
    public function modulo ($int) {
      $intValue = TypeConv::to_str($int);

      if ($intValue) {
        return TypeConv::to_int(gmp_mod($this->int, $intValue));
      }
      
      return false;
    }
    /** Get the Negated `BigInt`.
     * 
     * > `-BigInt`
     * 
     * @return BigInt Returns a `BigInt` object representing the negative value of the `BigInt`. 
     */
    public function negate () {
      return TypeConv::to_bigint(gmp_neg($this->int));
    }
    /** Get the next *Prime Number* greater than the `BigInt`.
     * 
     * If the `BigInt` is negative, the result will always be **2**.
     * 
     * @return BigInt Returns a `BigInt` object representing the next *Prime Number* following the `BigInt`. 
     */
    public function next_prime () {
      return TypeConv::to_bigint(gmp_nextprime($this->int));
    }
    /** Get the *Population Count*, or number of *ON Bits*, within the `BigInt`.
     * 
     * @return BigInt Returns an `int` representing the *Population Count* of the `BigInt`. 
     */
    public function pop () {
      return gmp_popcount($this->int);
    }
    /** Get the *Resultant Root* of the `BigInt` and a given value.
     * 
     * @param int $nth The *Positive Root* to take of the `BigInt`.
     * @param bool $get_remainder Indicates if the *Remainder* of the root should be returned. 
     * @return BigInt|array 
     * Returns a `BigInt` representing the *Resultant Root* of the `BigInt`. 
     * If `$get_remainder` is **true**, returns an `array` where the first element is the *Integer Component* of the root, and the second the *Remainder*, both as `BigInt` objects.
     */
    public function root (int $nth, bool $get_remainder = false) {
      $result = gmp_rootrem($this->int, $nth);

      if (!$get_remainder) {
        return $result[0];
      }
      else {
        return [
          new BigInt($result[0]),
          new BigInt($result[1])
        ];
      }
    }
    /** Get the *Square Root* of the `BigInt`.
     * 
     * @param bool $get_remainder Indicates if the *Remainder* of the root should be returned. 
     * @return BigInt|array 
     * Returns a `BigInt` representing the *Square Root* of the `BigInt`. 
     * If `$get_remainder` is **true**, returns an `array` where the first element is the *Integer Square Root* of the root, and the second the *Remainder*, both as `BigInt` objects.
     */
    public function sqrt (bool $get_remainder = false) {
      $result = gmp_sqrtrem($this->int);

      if (!$get_remainder) {
        return $result[0];
      }
      else {
        return [
          new BigInt($result[0]),
          new BigInt($result[1])
        ];
      }
    }

    /** Math Testing Methods */
    /** Compare the `BigInt` with a given value.
     * 
     * > `BigInt < $int` 
     * > 
     * > `BigInt == $int`
     * >
     * > `BigInt > $int`
     * 
     * You can use the `less()`, `equal()`, and `greater()` methods for the same effect.
     * - @see BigInt::less()
     * - @see BigInt::equals()
     * - @see BigInt::greater()
     * 
     * @param string|int|\GMP|BigInt $int The value to compare the `BigInt` with. Can be any value accepted by `TypeConv`.
     * @return int|false Returns a `COMP_*` class constant representing how the value compares to the `BigInt`. If `$int` is of an unknown type, returns **false**.
     */
    public function comp ($int) {
      $intValue = TypeConv::to_str($int);
      
      if ($intValue !== false) {
        return gmp_cmp($this->int, $intValue);
      }

      return false;
    }
    /** Check if the `BigInt` is *less than* a given value.
     * 
     * > `BigInt < $int` _(without `$or_equal`)_
     * > 
     * > `BigInt <= $int` _(with `$or_equal`)_
     * 
     * - You can also use the `comp()` method to retrieve this value.
     * - - @see BigInt::comp()
     * 
     * @param string|int|\GMP|BigInt $int The value to compare with the `BigInt`. Can be any value accepted by `TypeConv`.
     * @param bool $or_equal Indicates if the comparison should allow *Equality* (`BigInt <= $int`)
     * @return bool|null Returns **true** if the `BigInt` is less than `$int`, or **false** if it is not. If `$int` is of an unknown type, returns **null**.
     */
    public function less ($int, $or_equal = false) {
      $comp = $this->comp($int);

      if ($comp !== false) {
        if ($or_equal) {
          return $comp !== 1;
        }
        else {
          return $comp === -1;
        }
      }

      return null;
    }
    /** Check if the `BigInt` is *equal to* a given value.
     * 
     * > `BigInt == $int`
     * 
     * - You can also use the `comp()` method to retrieve this value.
     * - - @see BigInt::comp()
     * 
     * @param string|int|\GMP|BigInt $int The value to compare with the `BigInt`. Can be any value accepted by `TypeConv`.
     * @return bool|null Returns **true** if the `BigInt` is equal to `$int`, or **false** if it is not. If `$int` is of an unknown type, returns **null**.
     */
    public function equals ($int) {
      $comp = $this->comp($int);

      if ($comp !== false) {
        return $comp === 0;
      }

      return null;
    }
    /** Check if the `BigInt` is *greater than* a given value.
     * 
     * > `BigInt > $int` _(without `$or_equal`)_
     * > 
     * > `BigInt >= $int` _(with `$or_equal`)_
     * 
     * - You can also use the `comp()` method to retrieve this value.
     * - - @see BigInt::comp()
     * 
     * @param string|int|\GMP|BigInt $int The value to compare with the `BigInt`. Can be any value accepted by `TypeConv`.
     * @param bool $or_equal Indicates if the comparison should allow *Equality* (`BigInt >= $int`)
     * @return bool|null Returns **true** if the `BigInt` is greater than `$int`, or **false** if it is not. If `$int` is of an unknown type, returns **null**.
     */
    public function greater ($int, $or_equal = false) {
      $comp = $this->comp($int);

      if ($comp !== false) {
        if ($or_equal) {
          return $comp !== -1;
        }
        else {
          return $comp === 1;
        }
      }

      return null;
    }
    /** Checks if the `BigInt` is a *Perfect Power*.
     * 
     * @return bool Returns **true** if the `BigInt` is a *Perfect Power*, or **false** if it is not.
     */
    public function perfect_power () {
      return gmp_perfect_power($this->int);
    }
    /** Checks if the `BigInt` is a *Perfect Square*.
     * 
     * @return bool Returns **true** if the `BigInt` is a *Perfect Square*, or **false** if it is not.
     */
    public function perfect_square () {
      return gmp_perfect_square($this->int);
    }
    /** Checks if the `BigInt` is *possibly* a *Prime Number*.
     * 
     * > Uses Miller-Rabin's probabilistic test to check if a number is a prime. 
     * 
     * @return bool|int 
     * Returns **true** if the `BigInt` is *definitely* a *Prime Number*.
     * Returns **1** if the `BigInt` is *possibly* a *Prime Number*.
     * Returns **false** if the `BigInt` is *definitely* not a *Prime Number*.
     */
    public function is_prime () {
      $result = gmp_prob_prime($this->int, 10);

      switch ($result) {
        case 2:
          return true;
        case 1:
          return 1;
        case 0:
          return false;
      }
    }
    /** Gets the *Sign* of the `BigInt`.
     * 
     * @return int Returns an `INT_SIGN_` class constant representing the *Sign* of the `BigInt`. 
     */
    public function sign () {
      return gmp_sign($this->int);
    }
  }
?>