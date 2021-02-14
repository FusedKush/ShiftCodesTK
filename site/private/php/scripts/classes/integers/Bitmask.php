<?php
  namespace ShiftCodesTK\Integers;
  use ShiftCodesTK\Strings;

  /** Represents a *Bitmask* of *Bitwise Flags*. */
  class Bitmask {
    /** Constants */
    /** @var int Represents the minimum value a bitmask flag can have. */
    const BITMASK_MIN = -1;
    /** @var int Represents the initial value of a set of bitmask flags. A positive value is recommended to pass *falsey* checks. */
    const BITMASK_START = 1;
    /** @var string Represents the maximum value a bitmask flag can have. */
    const BITMASK_MAX = "4611686018427387904";
    /** @var int Represents the maximum number of *Bitwise Flags* allowed per-category. */
    const BITMASK_MAX_FLAGS = 64;
    /** @var int Represents the number of digits occupied by a *Full Bitmask*. */
    const BITMASK_DIGITS = 19;
    /** @var string Represents the name of the *Bitwise Flag Category* that uncategorized flags are stored under. */
    const BITMASK_DEFAULT_CATEGORY = 'uncategorized';
    /** @var string Represents the padding characters used to pack the bitmask value. */
    const BITMASK_PACKING_PADDING = 'x';
    /** @var int A `STR_SIDE_*` constant representing the side of the string the bitmask is packed on. */
    const BITMASK_PACKING_SIDE = Strings\STR_SIDE_LEFT;

    /** Properties */
    /** @var array An index of Bitwise Flag *Categories*, *Names*, and *Values*. */
    private $flag_data = [
      'categories' => [],
      'names'      => [],
      'values'     => []
    ];

    /**
     * Represents the current *Bitmask Value*.
     * @var array
     */
    protected $bitmasks = [];

    /** Static Methods */
    /** Pack a list of *Bitmasks* for transport.
     * 
     * @param BitwiseInt $bitmasks The *Bitmasks* being packed, provided as `BitwiseInt` objects.
     * @return string Returns a `string` representing the *Packed `$bitmasks`*. Emits a *Warning* if any of the `$bitmasks` are not a `BitmaskInt` object.
     */
    public static function pack_bitmasks (...$bitmasks) {
      $packedStr = '';

      foreach ($bitmasks as $index => $bitmask) {
        if (!is_object($bitmask) || !is_a($bitmask, BitwiseInt::class)) {
          trigger_error("Bitmask #" . ($index + 1) ." is not a BitwiseInt Object.", E_USER_WARNING);
          continue;
        }

        $packedStr .= Strings\pad(
          $bitmask->get_int(), 
          self::BITMASK_DIGITS, 
          self::BITMASK_PACKING_PADDING, 
          self::BITMASK_PACKING_SIDE
        );
      }

      return $packedStr;
    }
    /** Unpack a *Packed Bitmask String*.
     * 
     * @param string $bitmasks The *Packed Bitmask String* to be unpacked.
     * @return array|false Returns an `array` made up of `BitwiseInt` objects representing the unpacked `$bimasks`. Returns **false** if an invalid *Packed Bitmask String* is provided.
     */
    public static function unpack_bitmask (string $bitmasks) {
      $isValidString = (function () use ($bitmasks) {
        $bitmaskObj = new Strings\StringObj($bitmasks);

        if (($bitmaskObj->strlen() % self::BITMASK_DIGITS) === 0 || ($bitmaskObj->strlen() % self::BITMASK_DIGITS + 1) === 0) {
          $pattern = (function () {
            $padding = Strings\escape_reg(self::BITMASK_PACKING_PADDING, '/');
            $pattern = "/^(";

            if (self::BITMASK_PACKING_SIDE === Strings\STR_SIDE_LEFT || self::BITMASK_PACKING_SIDE === Strings\STR_SIDE_BOTH) {
              $pattern .= "({$padding})+";
            }

            $pattern .= "([\d\-]+)";

            if (self::BITMASK_PACKING_SIDE === Strings\STR_SIDE_RIGHT || self::BITMASK_PACKING_SIDE === Strings\STR_SIDE_BOTH) {
              $pattern .= "({$padding})+";
            }

            $pattern .= ")+$/";

            return $pattern;
          })();

          if ($bitmaskObj->preg_test($pattern)) {
            return true;
          }
        }

        return false;
      })();

      if ($isValidString) {
        $bitmaskObjs = [];
        $unpackedBitmasks = Strings\split($bitmasks, self::BITMASK_DIGITS, true)
                            ->trim(self::BITMASK_PACKING_SIDE, self::BITMASK_PACKING_PADDING);
  
        foreach ($unpackedBitmasks as $bitmasks) {
          $bitmaskObjs[] = new BitwiseInt($bitmasks);
        }
  
        return $bitmaskObjs;
      }

      return false;
    }
    /** Parse an list of *Bitwise Flags* into a categorized `array`.
     * 
     * @param array $flag_list The list of *Bitwise Flags*. 
     * - Flags are organized in the following format:
     * - - > `?string` *Flag Value* => `string` *Flag Name*
     * - - *Flag Value* refers to the value of the flag. 
     * - - - The value will be converted to a `string` if it is not already.
     * - - - {@see Bitmask::add_flag_option} for more information on adding *Bitwise Flags* to the `Bitmask`.
     * - - - If omitted, a value will automatically be assigned to the flag based on its position in the list, starting at **1**.
     * - - *Flag Name* refers to the name of the flag. 
     * - - - If the *Flag Value* is omitted, the method will search for a `constant` with the same *Flag Name*. If one is found, it will automatically inherit its value. 
     * - - If either the *Flag Value* or *Flag Name* match a value already in the same category, an *Error* will be emitted and the flag will be skipped.
     * - Multiple arrays can be provided to indicate a *Bitwise Flag Category*. Categories are organized in the following format:
     * - - > `?string` *Category Name* => `array` *Category Flags*
     * - - *Category Name* refers to the name of the category. If provided, it can be used when comparing flags.
     * - - - If omitted, and at least one other category contains a *Category Name*, the *Category Flags* will be assigned to the default `uncategorized` category. 
     * - - - If omitted and no other categories contain a custom *Category Name*, the *Category Flags* will be assigned to the *Index* in the array.
     * - - *Category Flags* refers to the list of flags that belong to the category. 
     * - - If no categories are provided, all provided flags will be stored in the `uncategorized` category. 
     * - - All categories, including the `uncategorized` category, have a maximum of **64 flags**. Attempting to add any more to the category will emit an *Error* and skip the flag.
     * @return array|false Returns a parsed `array` representing the `$flag_list`, in the following format:
     * > `string` *Category Name* => `array` *Category Flags* 
     * > > `string` *Flag Name* => `int` *Flag Value*
     * 
     * Returns **false** if the `$flag_list` is not in a valid format.
     */
    public static function parse_flag_list (array $flag_list) {
      $parsedList = [];

      $parseFlags = function ($flag_array, $category_name) use (&$parsedList) {
        $providedFlagValues = array_keys($flag_array);
        $category = &$parsedList[$category_name];

        for ($i = 0; $i < count($flag_array); $i++) {
          $providedFlagValue = $providedFlagValues[$i];
          $flagName = $flag_array[$providedFlagValue];

          if (is_array($flagName)) {
            trigger_error("Category \"{$category_name}\" has it's Array Names and Array Values flipped. The Array Value (if provided) should be the Key, and the Array Name the Value.", E_USER_WARNING);
            continue;
          }

          if (count($category) < self::BITMASK_MAX_FLAGS) {
            if (isset($category[$flagName])) {
              trigger_error("Flag \"{$flagName}\" has already been defined for Category \"{$category_name}\".");
              continue;
            }
            else if (array_search($providedFlagValue, $category) !== false) {
              trigger_error("Flag Value \"{$providedFlagValue}\" has already been defined for Category \"{$category_name}\".");
              continue;
            }

            $category[$flagName] = is_int($providedFlagValue) ? null : $providedFlagValue;
          }
          else {
            trigger_error("Flag \"{$flagName}\" was not parsed because Category \"{$category_name}\" has reached the maximum number of flags.");
            continue;
          }
        }
      };

      // Has Categories
      if (is_array($flag_list[array_key_first($flag_list)])) {
        $hasCategoryNames = false;

        foreach ($flag_list as $providedCategoryName => $categoryFlags) {
          if (is_string($providedCategoryName) && !$hasCategoryNames) {
            $hasCategoryNames = true;

            foreach ($parsedList as $parsedCategoryName => $parsedCategoryFlags) {
              if (is_int($parsedCategoryName)) {
                if (!isset($parsedList[self::BITMASK_DEFAULT_CATEGORY])) {
                  $parsedList[self::BITMASK_DEFAULT_CATEGORY] = [];
                }

                $parsedList[self::BITMASK_DEFAULT_CATEGORY] = array_merge($parsedList[self::BITMASK_DEFAULT_CATEGORY], $parsedCategoryFlags);
                unset($parsedList[$parsedCategoryName]);
              }
            }
          }

          $categoryName = (function () use ($providedCategoryName, $hasCategoryNames) {
            if ($hasCategoryNames && is_int($providedCategoryName)) {
              return self::BITMASK_DEFAULT_CATEGORY;
            }
            else {
              return $providedCategoryName;
            }
          })();

          $parsedList[$categoryName] = [];

          $parseFlags($categoryFlags, $categoryName);
        } 
      }
      // No Categories
      else {
        $parsedList[self::BITMASK_DEFAULT_CATEGORY] = [];

        $parseFlags($flag_list, self::BITMASK_DEFAULT_CATEGORY);
      }

      return $parsedList;
    }
    public static function get_bitwise_flag_value (int $flag_num, bool $use_start = false) {
      $value = false;

      if ($use_start) {
        $value = BITMASK_VALUES[$flag_num] ?? false;
      }
      else {
        $firstAssignedValueIndex = (function () {
          foreach (BITMASK_VALUES as $index => $bitmaskValue) {
            if ($bitmaskValue == self::BITMASK_START) {
              return $index;
            }
          }
  
          return 1;
        })();

        $value = BITMASK_VALUES[$firstAssignedValueIndex + $flag_num] ?? false;
      }

      return $value;
    }

    /** Magic Methods */
    /** Initialize a new *Bitmask*.
     * 
     * @param array $bitmask_flags An array of *Bitmask Categories* and/or *Bitwise Flags*. 
     * - {@see Bitmask::parse_flag_list()} for more information regarding the expected array structure.
     * @param string $bitmask_value A *Packed Bitmask String* representing the value of the *Bitmask*. 
     * - {@see Bitmask:pack_bitmasks()} if you need to generate a *Packed Bitmask String*.
     * @return Bitmask Returns the new `Bitmask` on success. May emit *Warnings* and *Errors* is invalid values are encountered in the `$bitmask_flags` or `$bitmask_value`.
     */
    public function __construct(array $bitmask_flags, $bitmask_value = '0') {
      if ($bitmask_flags) {
        $this->import_flag_options($bitmask_flags);
      }
      if ($bitmask_value) {
        $this->import_bitmasks($bitmask_value);
      }
    }

    /** Internal Methods */
    /** Save *Bitwise Flag Data* for indexing.
     * 
     * @param string $flag_name The name of the flag.
     * @param string $flag_value The value of the flag.
     * @param string $category The category the flag belongs to. Defaults to **::BITMASK_DEFAULT_CATEGORY**.
     * @return bool Returns **true** on success and **false** on failure. Emits a *Warning* if a non-existing `$category`, or existing `$flag_name` or `$flag_value` is provided.
     */
    private function save_flagdata (string $flag_name, string $flag_value, string $category = self::BITMASK_DEFAULT_CATEGORY) {
      if ($this->check_category($category)) {
        $categoryNames = &$this->flag_data['names'][$category];
        $categoryIndex = count($categoryNames);

        $categoryNames[$flag_name] = $flag_value;
        $this->flag_data['values'][$category][$flag_value] = $categoryIndex;

        return true;
      }
      else {
        trigger_error("Category \"{$category}\" has not been defined.");
      }

      return false;
    }
    /** Remove *Bitwise Flag Data* from the index.
     * 
     * **Note**: This method requires the *Bitwise Flag Data* to still be available in 
     * 
     * @param string $flag_name The name of the flag.
     * @param string|null $category The category the flag belonged to. 
     * - Uncategorized flags may be stored under **::BITMASK_DEFAULT_CATEGORY**. 
     * - If omitted, all categories will be checked and the first matching flagdata will be removed.
     * @return bool Returns **true** on success and **false** on failure. Emits a *Warning* if a non-existing `$category` or `$flag_name` is provided.
     */
    private function remove_flagdata (string $flag_name, string $category = null) {
      $checkFlagdata = function ($category_name) use ($flag_name) {
        if ($this->check_flag($flag_name, null, $category_name)) {
          $categoryNames = &$this->flag_data['names'][$category_name];
          $categoryValues = &$this->flag_data['values'][$category_name];

          unset($categoryValues[$categoryNames[$flag_name]]);
          unset($categoryNames[$flag_name]);
  
          return true;
        }

        return false;
      };

      if (isset($category)) {
        if ($this->check_category($category)) {
          if ($checkFlagdata($category)) {
            return true;
          }
        }
        else {
          trigger_error("Category \"{$category}\" has not been defined.");
        }
      }
      else {
        foreach ($this->flag_data['values'] as $categoryName => $categoryValues) {
          if ($checkFlagdata($categoryName)) {
            return true;
          }
        }
      }

      if (isset($category)) {
        trigger_error("Flag \"{$flag_name}\" could not be found for Category \"{$category}\".", E_USER_WARNING);
      }
      else {
        trigger_error("Flag \"{$flag_name}\" could not be found.", E_USER_WARNING);
      }

      return false;
    }

    /** *Bitwise Flags* Methods */
    /** Check if a *Bitwise Flag Category* has already been defined.
     * 
     * @param string $category_name The name of the category to check.
     * @return bool Returns **true** if the `$category_game` is a defined *Bitwise Flag Category*, or **false** if it is not.
     */
    public function check_category (string $category_name) {
      return isset($this->flag_data['categories'][$category_name]);
    }
    /** Add a *Bitwise Flag Category* to the `Bitmask`.
     * 
     * @param string $category_name The name of the category to add.
     * @return array|false Returns the newly-added *Bitwise Flag Category* on success. Returns **false** and emits a *Warning* if `$category_name` has already been defined, or if you try to add the `::BITMASK_DEFAULT_CATEGORY`.
     */
    public function add_category (string $category_name) {
      if ($this->check_category($category_name)) {
        if ($category_name === self::BITMASK_DEFAULT_CATEGORY) {
          trigger_error("Category \"{$category_name}\" is reserved as the default Bitmask Category.", E_USER_WARNING);
          return false;
        }
        else {
          trigger_error("Category \"{$category_name}\" already exists.", E_USER_WARNING);
          return false;
        }
      }

      $this->flag_data['names'][$category_name] = [];
      $this->flag_data['values'][$category_name] = [];
      $this->flag_data['categories'][$category_name] = $category_name;
      $this->bitmasks[$category_name] = [
        'flags'   => [],
        'bitmask' => new BitwiseInt(0, true)
      ];

      return $this->bitmasks[$category_name];
    }
    /** Remove a *Bitwise Flag Category* from the `Bitmask`.
     * 
     * **Note**: This will remove any flags registered to the category.
     * 
     * @param string $category_name The name of the category to remove.
     * @return bool Returns **true** if the category was successfully removed from the `Bitmask`. Returns **false** and emits a *Warning* if `$category_name` has not been defined, or if you try to remove the `::BITMASK_DEFAULT_CATEGORY`.
     */
    public function remove_category (string $category_name) {
      if (!$this->check_category($category_name)) {
        if ($category_name == self::BITMASK_DEFAULT_CATEGORY) {
          trigger_error("Category \"{$category_name}\" is reserved as the default Bitmask Category and cannot be removed.", E_USER_WARNING);
          return false;
        }
        else {
          trigger_error("Category \"{$category_name}\" has not been defined.", E_USER_WARNING);
          return false;
        }
      }

      unset($this->flag_data['names'][$category_name]);
      unset($this->flag_data['values'][$category_name]);
      unset($this->flag_data['categories'][$category_name]);
      unset($this->bitmasks[$category_name]);

      return true;
    }
    /** Check if a given *Flag Name* or *Flag Value* has been defined.
     * 
     * @param string|null $flag_name The *Flag Name* to search for. Can be omitted.
     * @param string|null $flag_value The *Flag Value* to search for. Can be omitted.
     * @param string|null $category The name of the category to search. 
     * - Uncategorized flags may be stored under **::BITMASK_DEFAULT_CATEGORY**. 
     * - If omitted, all categories will be checked, and any match will return **true**.
     * @return bool Returns **true** if the `$flag_name` or `$flag_value` has been defined in the given `$category`. Otherwise, returns **false**.
     * - If either `$flag_name` or `$flag_value` is provided, it must exist in the `$category` to be **true**.
     * - If both the `$flag_name` and `$flag_value` are provided, either must exist in the `$category` to be **true**.
     * - If both the `$flag_name` and `$flag_value` are omitted, returns **false**.
     */
    public function check_flag (string $flag_name = null, string $flag_value = null, string $category = null) {
      $checkCategory = function ($category_name) use ($flag_name, $flag_value) {
        if (isset($flag_name) && isset($this->flag_data['names'][$category_name][$flag_name])) {
          return true;
        }
        if (isset($flag_value) && isset($this->flag_data['values'][$category_name][$flag_value])) {
          return true;
        }

        return false;
      };

      if ($category) {
        if ($this->check_category($category)) {
          return $checkCategory($category);
        }
        else {
          trigger_error("\"{$category}\" has not been defined.", E_USER_WARNING);
        }
      }
      else {
        foreach ($this->flag_data['values'] as $categoryName => $categoryValues) {
          if ($checkCategory($categoryName)) {
            return true;
          }
        }
      }

      return false;
    }
    /** Retrieve the *Bitwise Flag Value* is a *Bitwise Flag Name* is provided, and vice versa.
     * 
     * @param string $search The search value, as a `string`: A *Bitwise Flag Name* or *Bitwise Flag Value*.
     * @param string|null $category The name of the category to search. 
     * - Uncategorized flags may be stored under **::BITMASK_DEFAULT_CATEGORY**. 
     * - If omitted, all categories will be checked, and the first match will be returned.
     * @return string|false Returns a *Bitwise Flag Value* or *Bitwise Flag Name*, the opposite of what was provided for the `$search`. Returns **false** if the flag could not be found.
     */
    public function get_flag (string $search, string $category = null) {
      $checkFlags = function ($category_name) use ($search) {
        $flags = $this->bitmasks[$category_name]['flags'];

        foreach ($flags as $flagName => $flagValue) {
          if ($flagName === $search) {
            return $flagValue;
          }
          else if ($flagValue === $search) {
            return $flagName;
          }
        }

        return false;
      };
      
      if (isset($category)) {
        return $checkFlags($category);
      }
      else {
        foreach ($this->bitmasks as $categoryName => $categoryData) {
          $flagData = $checkFlags($categoryName);

          if ($flagData !== false) {
            return $flagData;
          }
        }
      }
    }
    /** Add a *Bitwise Flag* to the `Bitmask`.
     * 
     * - Use the `add_flag()` method to apply a *Bitwise Flag* to a *Bitmask*.
     * 
     * @param string $flag_name The name of the *Bitwise Flag* to add.
     * @param mixed|null $flag_value The value of the *Bitwise Flag*.
     * - The value **-1** acts to enable *all* of the provided flags when applied to the *Bitmask*. Only use this value if you want the behavior of all of the flags.
     * - The value **0** is not allowed, as it has no effect when applying it to the *Bitmask*.
     * - If omitted, the method will search for a `constant` with the same `$flag_name`. If one is found, it will automatically inherit its value. 
     * - If a constant was not found, a value will automatically be assigned to the flag based on its position in the list, starting at **1**.
     * @param string $category The name of the *Bitwise Flag Category* the *Bitwise Flag* belongs to. Defaults to **::BITMASK_DEFAULT_CATEGORY**.
     * - If it has not been defined yet, it will automatically be created.
     * @return array|false Returns the new *Bitwise Flag* as it has been stored on success. If an error occurs, returns **false**.
     */
    public function add_flag_option (string $flag_name, string $flag_value = null, string $category = self::BITMASK_DEFAULT_CATEGORY) {
      if (!$this->check_category($category)) {
        $this->add_category($category);
      }

      if (!$this->check_flag($flag_name, $flag_value, $category)) {
        $flagValue = (function () use ($flag_value, $flag_name, $category) {
          $flagList = &$this->bitmasks[$category]['flags'];

          if (isset($flag_value)) {
            return $flag_value;
          }
          else {
            $constNames = [
              __CLASS__ . "\\{$flag_name}",
              $flag_name,
              __NAMESPACE__ . "\\{$flag_name}",
            ];

            foreach ($constNames as $constName) {
              if (defined($constName)) {
                return TypeConv::to_str(constant($constName));
              }
            }

            return self::get_bitwise_flag_value(
              count($flagList), 
              $flagList
                ? (new BigInt(array_value_first($flagList)))->equals(-1)
                : false
            );
          }
        })();
        $isValidValue = (function () use ($flagValue) {
          if (!is_numeric($flagValue)) {
            trigger_error("The provided Bitwise Flag Value is invalid.");
            return false;
          }

          $valueInt = new BitwiseInt($flagValue, true);
          $minValue = self::BITMASK_MIN;
          $maxValue = self::BITMASK_MAX;

          if ($valueInt->equals(0)) {
            trigger_error("Bitwise Flags cannot have a value of 0, as it has no effect when being applied to the Bitmask.");
          }
          else if ($valueInt->less($minValue)) {
            trigger_error("Bitwise Flag Values cannot exceed {$minValue}, {$flagValue} provided.");
          }

          else if ($valueInt->greater($maxValue)) {
            trigger_error("Bitwise Flag Values cannot exceed {$maxValue}, {$flagValue} provided.");
          }
          else if (!$valueInt->and((string) ($valueInt)->sub(1))->equals(0) && !$valueInt->equals(-1)) {
            trigger_error("Bitwise Flag Values must be a power of two, {$flagValue} provided.");
          }
          else {
            return true;
          }

          return false;
        })();

        if ($isValidValue) {
          $this->bitmasks[$category]['flags'][$flag_name] = $flagValue;
          $this->save_flagdata($flag_name, $flagValue, $category);
  
          return $this->bitmasks[$category];
        }
      }
      else {
        if (isset($this->flag_data[$category]['names'][$flag_name])) {
          trigger_error("Flag \"{$flag_name}\" has already been defined for Category \"{$category}\".", E_USER_WARNING);
        }
        else {
          trigger_error("Flag Value \"{$flag_value}\" is already being used for Category \"{$category}\".", E_USER_WARNING);
        }
      }

      return false;
    }
    /** Remove a *Bitwise Flag* from the `Bitmask`.
     * 
     * - Use the `remove_flag()` method to clear a *Bitwise Flag* from the *Bitmask*.
     * 
     * @param string|null $flag_name The name of the *Bitwise Flag* to remove. 
     * @param string $category The name of the *Bitwise Flag Category* the *Bitwise Flag* belongs to. 
     * - Uncategorized flags may be stored under **::BITMASK_DEFAULT_CATEGORY**. 
     * - If omitted, all categories will be checked, and the first match will be removed.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function remove_flag_option (string $flag_name, string $category = null) {
      $removeFlag = function ($category_name) use ($flag_name) {
        if ($this->check_flag($flag_name, null, $category_name)) {
          $categoryBitmask = &$this->bitmasks[$category_name];
          /** @var BitwiseInt */
          $bitwiseInt = &$categoryBitmask['bitmask'];
          $flagValue = $categoryBitmask['flags'][$flag_name];

          unset($categoryBitmask['flags'][$flag_name]);
          $this->remove_flagdata($flag_name, $category_name);

          if ($bitwiseInt->test_bit($flagValue)) {
            $bitwiseInt->set_bits(false, $flagValue);
          }
  
          return true;
        }

        return false;
      };

      if (isset($category)) {
        if ($this->check_category($category)) {
          if ($removeFlag($category)) {
            return true;
          }
        }
        else {
          trigger_error("Category \"{$category}\" has not been defined.", E_USER_WARNING);
        }
        if (!$this->check_category($category)) {
          $this->add_category($category);
        }
      }
      else {
        foreach ($this->bitmasks as $categoryName => $categoryBitmask) {
          if ($removeFlag($categoryName)) {
            return true;
          }
        }
      }

      if (isset($category)) {
        trigger_error("Flag \"{$flag_name}\" could not be found for Category \"{$category}\".", E_USER_WARNING);
      }
      else {
        trigger_error("Flag \"{$flag_name}\" could not be found.", E_USER_WARNING);
      }

      return false;
    }
    /** Import an array of *Bitwise Flags* into the `Bitmask`
     * 
     * Note that if the `Bitmask` already contains bitmask data, categories found in both the `$flag_list` and `Bitmask` are *removed* from the `Bitmask`, including the current *Bitmask Value* and all defined *Bitwise Flags*.
     * 
     * @param array $flag_list An `array` of *Bitmask Categories* and/or *Bitwise Flags*. 
     * - {@see Bitmask::parse_flag_list()} for more information on the expected format of the array.
     * @return array|false Returns an `array` representing the *Parsed Bitwise Flags* on success, or **false** if an error occurred.
     */
    public function import_flag_options (array $flag_list) {
      $flags = self::parse_flag_list($flag_list);

      if ($flags) {
        foreach ($flags as $category => $categoryFlags) {
          if ($this->check_category($category)) {
            $this->remove_category($category);
          }

          if ($this->add_category($category)) {
            foreach ($categoryFlags as $flagName => $flagValue) {
              $this->add_flag_option($flagName, $flagValue, $category);
            }
          }
        }

        return $flags;
      }

      return false;
    }

    /** *Bitmask Value* Methods */
    /** Set the *Bitmask Value* of a *Bitmask*.
     * 
     * @param string|int|\GMP|BigInt $bitmask The new *Bitmask Value* to set. Can be any value accepted by `TypeConv`.
     * @param string $category The name of the *Bitmask Category* the *Bitmask* belongs to. Defaults to **::BITMASK_DEFAULT_CATEGORY**.
     * @return BitwiseInt|false Returns the updated `BitwiseInt` on success, or **false** if an error occurred.
     */
    public function set_bitmask ($bitmask, string $category = self::BITMASK_DEFAULT_CATEGORY) {
      if (!$this->check_category($category)) {
        trigger_error("Category \"{$category}\" has not been defined.", E_USER_WARNING);
        return false;
      }

      /** @var BitwiseInt */
      $bitwiseInt = $this->bitmasks[$category]['bitmask'];

      $bitwiseInt->set_int($bitmask);

      return $this->bitmasks[$category]['bitmask'];
    }
    /** Import a *Packed Bitmask String* into the `Bitmask*.
     * 
     * See `::pack_bitmask()` if you need to generate a *Packed Bitmask String*.
     * 
     * @param string $bitmasks The *Packed Bitmask String* to be unpacked.
     * @return array|false Returns an `array` made up of `BitwiseInt` objects representing the unpacked `$bimasks`. Returns **false** if an error occurs.
     */
    public function import_bitmasks (string $bitmasks) {
      $bitmaskArray = self::unpack_bitmask($bitmasks);

      if ($bitmaskArray) {
        $categories = array_keys($this->flag_data['categories']);

        for ($i = 0; $i < count($bitmaskArray); $i++) {
          $bitmask = $bitmaskArray[$i];
          $category = $categories[$i];

          $this->set_bitmask($bitmask, $category);
        }

        return $bitmaskArray;
      }
      else {
        trigger_error("The provided Packed Bitmask String is invalid.", E_USER_WARNING);
      }

      return false;
    }
    /** Extract the stored *Bitmasks*.
     * 
     * See `::unpack_bitmask()` for more information on the result.
     * 
     * @return string Returns a `string` representing the packed *Bitmasks*.
     */
    public function export_bitmasks () {
      $bitmasks = (function () {
        $bitmasks = [];

        foreach ($this->bitmasks as $categoryName => $categoryData) {
          $bitmasks[] = $categoryData['bitmask'];
        }

        return $bitmasks;
      })();

      return self::pack_bitmasks(...$bitmasks);
    }
    /** Check if a *Bitwise Flag* has been set in a given *Bitmask*.
     * 
     * @param string $flag The *Flag Name* or *Flag Value* to search for.
     * @param string|null $category The name of the *Bitmask Category* to search. 
     * - If omitted, all categories will be searched, and any match will return **true**.
     * - Uncategorized flags may be stored under the **BITMASK_DEFAULT_CATEGORY** category.
     * @return bool Returns **true** if the provided `$flag` was found, or **false** if it was not.
     */
    public function has_flag (string $flag, string $category = null) {
      $flagValue = !is_numeric($flag)
                   ? $this->get_flag($flag)
                   : $flag;

      $checkFlags = function ($category_name) use ($flagValue) {
        /** @var BitwiseInt */
        $bitwiseInt = &$this->bitmasks[$category_name]['bitmask'];

        if ($flagValue === '-1') {
          return $bitwiseInt->equals(-1);
        }

        $result = $bitwiseInt->and($flagValue);

        return !$result->equals(0);
      };

      if (isset($category)) {
        if ($checkFlags($category)) {
          return true;
        }
      }
      else {
        foreach ($this->bitmasks as $categoryName => $categoryData) {
          if ($checkFlags($categoryName)) {
            return true;
          }
        }
      }

      return false;
    }
    /** Get all applied *Bitwise Flags* for a given *Bitmask*.
     * 
     * @param string $category The *Bitmask Category* the *Bitmask* belongs to.
     * - If omitted, all categories will be searched, and all applied *Bitwise Flags* will be returned.
     * - Uncategorized flags may be stored under the **BITMASK_DEFAULT_CATEGORY** category.
     * @return array|false Returns an `Associative Array` on success containing a list of *Bitwise Flags* currently applied to the *Bitmask*. 
     * - If `$category` is omitted, applied Bitwise Flags will be grouped into their *Bitmask Categories*.
     * - Returns **false** if an error occurred.
     */
    public function get_flags (string $category = null) {
      $getFlags = function ($category_name) {
        $flags = [];

        foreach ($this->bitmasks[$category_name]['flags'] as $flagName => $flagValue) {
          if ($this->has_flag($flagValue, $category_name) !== false) {
            $flags[$flagName] = $flagValue;
          }
        }

        return $flags;
      };
      
      if (isset($category)) {
        if ($this->check_category($category)) {
          return $getFlags($category);
        }
        else {
          trigger_error("\"{$category}\" is not a valid Bitwise Flag Category.", E_USER_WARNING);
        }
      }
      else {
        $flagList = [];

        foreach ($this->bitmasks as $categoryName => $categoryData) {
          $categoryFlags = $getFlags($categoryName);

          if (count($categoryFlags) > 0) {
            $flagList[$categoryName] = $categoryFlags;
          }
        }

        return $flagList;
      }

      return false;
    }
    /** Apply a *Bitwise Flag* to the *Bitmask*
     * 
     * @param string $flag The *Flag Name* or *Flag Value* to apply to the Bitmask.
     * @param string $category The *Bitmask Category* the *Bitwise Flag* belongs to. If omitted, applies the first matching *Bitwise Flag* to its respective *Bitmask*.
     * @return BitwiseInt|false Returns the updated `BitwiseInt` on success. If an error occurrs, returns **false**.
     */
    public function apply_flag (string $flag, string $category = null) {
      $applyFlag = function ($category_name) use ($flag) {
        /** @var BitwiseInt */
        $bitmask = &$this->bitmasks[$category_name]['bitmask'];
        $flagValue = !is_numeric($flag)
                     ? $this->get_flag($flag, $category_name)
                     : $flag;

        if ($flagValue !== "-1" && $bitmask->get_int() === "-1") {
          $activeFlagName = $this->get_flag("-1", $category_name);

          trigger_error("Flag \"{$flag}\" could not be added because Flag \"{$activeFlagName}\" would override it.", E_USER_WARNING);
          return null;
        }
        if (!$this->has_flag($flag, $category_name)) {
          $result = $bitmask->or($flagValue);

          if ($result !== false) {
            $bitmask = $result;
          }

          return $result;
        }

        return false;
      };
      
      if (isset($category)) {
        if ($this->check_category($category)) {
          $isValidFlag = is_numeric($flag)
                         ? $this->check_flag(null, $flag, $category)
                         : $this->check_flag($flag, null, $category);
  
          if ($isValidFlag) {
            $result = $applyFlag($category);

            if ($result !== false) {
              return $result;
            }
            else {
              trigger_error("Flag \"{$flag}\" has already been applied to the Bitmask.", E_USER_NOTICE);
            }
          }
          else {
            trigger_error("\"{$flag}\" is not a valid Bitwise Flag Name or Value for Category \"{$category}\".", E_USER_WARNING);
          }
        }
        else {
          trigger_error("\"{$category}\" is not a valid Bitwise Flag Category.", E_USER_WARNING);
        }
      }
      else {
        foreach ($this->bitmasks as $categoryName => $categoryValue) {
          $result = $applyFlag($categoryName);

          if (!isset($result)) {
            break;
          }
          else if ($result !== false) {
            return $result;
          }
        }
      }

      return false;
    }
    /** Clear a *Bitwise Flag* from the *Bitmask*
     * 
     * @param string $flag The *Flag Name* or *Flag Value* to clear from the Bitmask.
     * @param string $category The *Bitmask Category* the *Bitwise Flag* belongs to. 
     * If omitted, all categories will be searched, and the first match will be removed.
     * Uncategorized flags may be stored under the **BITMASK_DEFAULT_CATEGORY** category.
     * @return bool Returns **true** on success and **false** on failure.
     */
    public function clear_flag (string $flag, string $category = null) {
      $clearFlag = function ($category_name) use ($flag) {
        if ($this->has_flag($flag, $category_name)) {
          /** @var BitwiseInt */
          $bitmask = &$this->bitmasks[$category_name]['bitmask'];
          $flagValue = !is_numeric($flag)
                       ? $this->get_flag($flag, $category_name)
                       : $flag;

          if ($flagValue !== "-1" && $bitmask->get_int() === "-1") {
            $activeFlagName = $this->get_flag("-1", $category_name);

            trigger_error("Flag \"{$flag}\" could not be cleared because Flag \"{$activeFlagName}\" overrides it.", E_USER_WARNING);
            return null;
          }

          $flagInt = (new BitwiseInt($flagValue))->get_int();
          // $flagInt = (new BitwiseInt($flagValue))->not()->get_int();

          $result = $bitmask->xor($flagInt);
          // $result = $bitmask->and($flagInt);

          if ($result !== false) {
            $bitmask = $result;
          }

          return $result;
        }

        return false;
      };
      
      if (isset($category)) {
        if ($this->check_category($category)) {
          $isValidFlag = is_numeric($flag)
                         ? $this->check_flag(null, $flag, $category)
                         : $this->check_flag($flag, null, $category);
  
          if ($isValidFlag) {
            if ($clearFlag($category) !== false) {
              return true;
            }
          }
          else {
            trigger_error("\"{$flag}\" is not a valid Bitwise Flag Name or Value for Category \"{$category}\".", E_USER_WARNING);
          }
        }
        else {
          trigger_error("\"{$category}\" is not a valid Bitwise Flag Category.", E_USER_WARNING);
        }
      }
      else {
        foreach ($this->bitmasks as $categoryName => $categoryData) {
          $result = $clearFlag($categoryName);

          if (!isset($result)) {
            break;
          }
          else if ($result !== false) {
            return $result;
          }
        }

        trigger_error("Flag \"{$flag}\" has not been applied to the Bitmask.", E_USER_NOTICE);
      }

      return false;
    }
  }
?>