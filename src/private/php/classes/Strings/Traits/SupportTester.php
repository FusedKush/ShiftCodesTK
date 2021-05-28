<?php
  namespace ShiftCodesTK\Strings\Traits;

  use ShiftCodesTK\Strings,
      ShiftCodesTK\Strings\StringObj,
      ShiftCodesTK\Strings\StringArrayObj;

  /** The `SupportTester` is responsible for testing support for String and Array String methods. */
  trait SupportTester {
    /** @var array[] A list of *Class Methods* to be tested with their provided *Arguments*. */

    /** Test interface support for a string or array.
     * 
     * Emits a warning if a method threw an exception during execution.
     * 
     * @param string|array $var The `String` or `Array` being tested. 
     * @return array Returns an `array` made up of results of the tested methods. 
     * - Methods not present on the object will return the string `-N/A-`.
     * - The resulting `StringObj` or `StringArrayObj` can be accessed via the **object** index.
     */
    public static function testVarSupport ($var) {
      $patterns = '/[\w\d\p{C}\p{S}]+/';
      $methods = [
        'checkEncoding'     => [],
        'checkAllEncodings' => [],
        'getEncoding'       => [],
        'strlen'            => [],
        'strlenAll'         => [],
        'char'              => [ 2 ],
        'firstchar'         => [],
        'lastchar'          => [],
        'split'             => [ 2 ],
        'explode'           => [ ' ' ],
        'substr'            => [ 2, -2 ],
        'substrPos'         => [ ' ' ],
        'substrCheck'       => [ ' ' ],
        'substrCheckAll'    => [ ' ' ],
        'substrCount'       => [ ' ' ],
        'substrCountAll'    => [ ' ' ],
        'pregMatch'         => [ $patterns ],
        'pregTest'          => [ $patterns ], 
        'pregTestAll'       => [ $patterns ],
        'transform'         => [ Strings\TRANSFORM_CAPITALIZE_WORDS ],
        'changeCase'        => [ Strings\CASING_STYLE_SNAKE_CASE ],
        'slice'             => [ 2, -2 ],
        'strReplace'        => [ ' ', "{ }" ],
        'pregReplace'       => [ $patterns, '[ $0 ]' ],
        'addPlural'         => [ 2, true ],
        'trim'              => [],
        'collapse'          => [],
        'encodeHTML'        => [],
        'decodeHTML'        => [],
        'stripTags'         => [],
        'encodeURL'         => [],
        'decodeURL'         => [],
        'encodeID'          => [],
        'escapeReg'         => [],
        'escapeSQL'         => []
      ];
      $object = (function () use ($var) {
        $classname = get_class();

        if ($classname === StringObj::class) {
          if (is_string($var)) {
            return new StringObj($var, StringObj::EDITING_MODE_STANDARD);
          }

          throw new \UnexpectedValueException("Only Strings can be tested.", 1);
        }
        else if ($classname === StringArrayObj::class) {
          if (is_array($var)) {
            return new StringArrayObj(
              $var, 
              [ 
                'editing_mode'  => StringArrayObj::EDITING_MODE_STANDARD, 
                'verbose'       => true 
              ]
          );
          }

          throw new \UnexpectedValueException("Only Arrays can be tested.", 1);
        }
      })();
      $result = [];

      foreach ($methods as $method_name => $args) {
        try {
          $result[$method_name] = method_exists(self::class, $method_name)
            ? $object->$method_name(...$args)
            : '-N/A-';
        }
        catch (\Throwable $exception) {
          trigger_error("A method has failed: \"{$method_name}\". Error: {$exception->getMessage()}");
          $result[$method_name] = null;
          continue;
        }
      }

      $result['object'] = $object;

      return $result;
    }
  }
?>