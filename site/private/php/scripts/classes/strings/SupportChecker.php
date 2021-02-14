<?php
  namespace ShiftCodesTK\Strings;

  /** The `SupportChecker` is responsible for testing support for String and Array String methods. */
  trait SupportChecker {
    /** Test interface support for a string or array.
     * 
     * Emits a warning if a method threw an exception during execution.
     * 
     * @param string|array $var The `String` or `Array` being tested. 
     * @return array Returns an `array` made up of results of the tested methods. The resulting `StringObj` or `StringArrayObj` can be accessed via the **object** index.
     */
    public static function test_var_support ($var) {
      $patterns = '/[\w\d\p{C}\p{S}]+/';
      $methods = [
        'check_encoding'    => [],
        'get_encoding'      => [],
        'strlen'            => [],
        'char'              => [2],
        'firstchar'         => [],
        'lastchar'          => [],
        'split'             => [2],
        'explode'           => [' '],
        'substr'            => [2, -2],
        'substr_pos'        => [' '],
        'substr_check'      => [' '],
        'substr_count'      => [' '],
        'preg_match'        => [$patterns],
        'preg_test'         => [$patterns], 
        'transform'         => [TRANSFORM_CAPITALIZE_WORDS],
        'slice'             => [2, -2],
        'str_replace'       => [' ', "{ }"],
        'preg_replace'      => [$patterns, '[ $0 ]'],
        'add_plural'        => [2, true],
        'trim'              => [],
        'collapse'          => [],
        'encode_html'       => [],
        'decode_html'       => [],
        'strip_tags'        => [],
        'encode_url'        => [],
        'decode_url'        => [],
        'encode_id'         => [],
        'escape_reg'        => [],
        'escape_sql'        => []
      ];
      $object = (function () use ($var) {
        $classname = get_class();

        if ($classname == 'ShiftCodesTK\Strings\StringObj') {
          if (is_string($var)) {
            return new StringObj($var, StringObj::EDITING_MODE_STANDARD);
          }

          throw new \UnexpectedValueException("Only Strings can be tested.", 1);
        }
        else if ($classname == 'ShiftCodesTK\Strings\StringArrayObj') {
          if (is_array($var)) {
            return new StringArrayObj($var, [ 'editing_mode' => StringArrayObj::EDITING_MODE_STANDARD, 'verbose' => true ]);
          }

          throw new \UnexpectedValueException("Only Arrays can be tested.", 1);
        }
      })();
      $result = [];

      foreach ($methods as $method_name => $args) {
        try {
          $result[$method_name] = $object->$method_name(...$args);
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