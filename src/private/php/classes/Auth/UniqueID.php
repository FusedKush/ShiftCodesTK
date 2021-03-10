<?php
  namespace ShiftCodesTK\Auth;

  use ShiftCodesTK\Strings,
      ShiftCodesTK\Integers,
      ShiftCodesTK\Timestamps;

  /** Represents a *Unique Numeric Identifier*, commonly known as *Snowflake IDs*. */
  class UniqueID {
    /** 
     * Represents the *ID Bit Structure* of the Unique ID as an `array` in the following format:
     * > `string` *Piece* => `int` *Number of Bits*
     * @var array 
     */
    private const ID_STRUCTURE = [
      'timestamp'      => 42,
      'worker_id'      => 5,
      'process_id'     => 5,
      'generation_num' => 12
    ];
    /** 
     * Represents the *ID Bit Retrieval Comparisons* of the Unique ID Pieces as an `array` in the following format:
     * > `string` *Piece* => `int` *Bit Retrieval Comparison Value*
     * @var array
     */
    private const BIT_COMPARISONS = [
      'timestamp'      => null,
      'worker_id'      => 0x3E0000,
      'process_id'     => 0x1F000,
      'generation_num' => 0xFFF
    ];

    /** Represents the data used to manage the generation of the Unique IDs:
     * 
     * | Data | Description |
     * | --- | --- |
     * | `worker_id` | Represents the current *Worker ID*. |
     * | `process_id` | Represents the current *Process ID*. |
     * | `generation_num` | Represents the current incremental generation of the current *Session* and *Process*. |
     * @var array
     */
    private static $generation_data = [
      'worker_id'      => null,
      'process_id'     => null,
      'generation_num' => null
    ];

    /** The *Unique ID* as a `BitwiseInt` object. @var Integers\BitwiseInt  */
    public $unique_id = null;
    /** The *Unique ID*, parsed as an `array` of pieces. @var Integers\BitwiseInt */
    public $parsed_id = [
      'timestamp'      => null,
      'worker_id'      => null,
      'process_id'     => null,
      'generation_num' => null
    ];

    /** Refresh the *ID Generation Data* for the generator.
     * 
     * @return array Returns the *Updated ID Generation Data* on success.
     */
    public static function refresh_generation_data () {
      $data = [
        'previous' => self::$generation_data,
        'updated' => [
          'worker_id'      => self::$generation_data['session_id'] ?? random_id(5),
          'process_id'     => getmypid(),
          'generation_num' => 1
        ]
      ];

      if (!$data['previous']['generation_num'] || count(array_intersect(...array_values($data))) < 2) {
        self::$generation_data = $data['updated'];
      }

      return $data['updated'];
    }

    /** Generate a new *Unique ID*.
     * 
     * @return string Returns the new *Unique ID* as a `string`. 
     */
    public function generate_id () {
      $uniqueID = new Integers\BitwiseInt();
      $this->parsed_id = array_merge([ 'timestamp' => Timestamps\tktime(true) ], self::$generation_data);

      self::$generation_data['generation_num']++;

      foreach ($this->parsed_id as $piece => $piece_value) {
        $uniqueID->lshift(self::ID_STRUCTURE[$piece])
                 ->or(new Integers\BigInt($piece_value));
                //  ->get_int(Integers\INT_TYPE_STRING);
      }

      $this->unique_id = $uniqueID;

      return $this->unique_id;
    }
    /** Parse the *Unique ID* into its separate pieces.
     * 
     * @return array Returns an `array` made up of the pieces of the *Unique ID*:
     * 
     * | Key | Description |
     * | --- | --- |
     * | `timestamp` | A *Unix Timestamp* representing when the ID was generated. You can use `get_timestamp()` to retrieve the proper version of this value. |
     * | `worker_id` | The *Worker ID* of the worker that generated the ID. |
     * | `process_id` | The *Process ID* of the process that generated the ID. |
     * | `generation_num` | Represents the current incremental generation of the ID. |
     */
    public function parse_id () {
      if (!$this->parsed_id['timestamp']) {
        $comparisons = self::BIT_COMPARISONS;
        $shifts = (function () {
          $shifts = [];
          $remainingBits = 64;

          foreach (self::ID_STRUCTURE as $piece => $piece_value) {
            $remainingBits = $remainingBits - $piece_value;
            $shifts[$piece] = $remainingBits;
          }

          return $shifts;
        })();
        $uniqueID = $this->unique_id
                         ->copy_integer();
        
        $uniqueID->set_immutable(true);
        
        $parsedID = [
          'timestamp'       => ($uniqueID->rshift($shifts['timestamp'])
                                         ->get_int()),

          'worker_id'       => ($uniqueID->AND($comparisons['worker_id'])
                                         ->rshift($shifts['worker_id'])
                                         ->get_int()),

          'process_id'      => ($uniqueID->AND($comparisons['process_id'])
                                         ->rshift($shifts['process_id'])
                                         ->get_int()),

          'generation_num'  => ($uniqueID->AND($comparisons['generation_num'])
                                         ->get_int())
        ];

        $this->parsed_id = $parsedID;
      }

      return $this->parsed_id;
    }

    /** Parse or Generate a *Unique ID*.
     * 
     * @param null|string $unique_id If provided, a *Unique ID* that will be parsed and returned as a `UniqueID` object. If omitted, a new *Unique ID* will be generated.
     * @return UniqueID Returns the `UniqueID` instance representing the new or existing *Unique ID*.
     */
    public function __construct($unique_id = null) {
      $this->refresh_generation_data();

      if ($unique_id) {
        $this->unique_id = new Integers\BitwiseInt($unique_id);
        $this->parse_id();
      }
      else {
        $this->generate_id();
      }
    }
  }
?>