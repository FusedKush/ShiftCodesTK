<?php
  namespace ShiftCodesTK\Database;

  /** Represents the *Field Metadata* of a Database Query */
  class QueryResultFieldMetadata {
    /** @var array A list of properties that are considered *advanced*. */
    private const ADVANCED_METADATA = [
      'orgname',
      'orgtable',
      'db',
      'org_full_name',
      'complete_name',
      'org_complete_name',
      'def',
      'catalog',
      'max_length',
      'length',
      'charsetnr',
      'flags',
      'type',
      'decimals'
    ];

    /** @var string The name of the column. */
    public $name = '';
    /** @var string The original name of the column if an *alias* was specified. */
    public $orgname = '';
    /** @var string The name of the table the field belongs to (if not calculated). */
    public $table = '';
    /** @var string The original name of the table if an *alias* was specified. */
    public $orgtable = '';
    /** @var string The database the `$table` belongs to. */
    public $db = '';
    /** @var string The full name of the column, made up of the `$table` & `$name`. */
    public $full_name = '';
    /** @var string The original full name of the column if any *alias's* were specified. Made up of the `$orgtable` & `$orgname`. */
    public $org_full_name = '';
    /** @var string The complete name of the column, made up of the `$db`, `$table` & `$name`. */
    public $complete_name = '';
    /** @var string The original complete name of the column if any *alias's* were specified. Made up of the `$db`, `$orgtable` & `$orgname. */
    public $org_complete_name = '';
    /** 
     * @var string The default value of the field. 
     * - Currently, this value is always `""`.
     **/
    public $def = '';
    /** 
     * @var string The catalog name.
     * - Currently, this value is always `""`.
     **/
    public $catalog = '';
    /** @var int The maximum width of the field for the *Result Set*. */
    public $max_length = 0;
    /** @var int The width of the field, as specified in the table definition. */
    public $length = 0;
    /** @var int The character set number for the field. */
    public $charsetnr = 0;
    /** @var int An integer representing the bit-flags for the field. */
    public $flags = 0;
    /** @var array An array representing all of the active flags for the field. */
    public $flags_array = [];
    /** @var string An integer representing the data type used for the field. */
    public $type = 0;
    /** @var string A string representing the data type used for the field. */
    public $type_name = '';
    /** @var int The number of decimals used for *integer* fields. */
    public $decimals = 0;

    /**
     * Initialize a new Field Metadata Object
     * 
     * @param stdClass $field_metadata An object made up of field metadata, retrieved by calling `mysqli_result::fetch_field()`.
     */
    public function __construct($field_metadata) {
      foreach ($field_metadata as $property => $value) {
        if (isset($this->$property)) {
          $this->$property = $value;
        }
      }

      if ($this->orgname) {
        $this->full_name = "{$this->table}.{$this->name}";
        $this->org_full_name = "{$this->orgtable}.{$this->orgname}";
  
        $this->complete_name = "{$this->db}.{$this->table}.{$this->name}";
        $this->org_complete_name = "{$this->db}.{$this->orgtable}.{$this->orgname}";
      }

      $this->flags_array = Database::get_field_flags($this->flags, 'FLAG_ARRAY');
      $this->type_name = Database::get_field_type_info($this->type)['name'];
    }
    /**
     * Retrieve the metadata from field
     * 
     * @param bool $get_advanced_metadata Indicates if *Advanced Metadata* should be included in the result, including the following properties:
     * - `orgname`
     * - `orgtable`
     * - `def`
     * - `db`
     * - `catalog`
     * - `max_length`
     * - `length`
     * - `charsetnr`
     * - `flags`
     * - `type`
     * - `decimals`
     * @return \ShiftCodesTKDatabaseQueryFieldMetadata Returns an object representing the field metadata, the inclusion of some properties dependent on the value of `$get_advanced_metadata`. 
     */
    public function get_metadata ($get_advanced_metadata = false) {
      $result = clone $this;

      if (!$get_advanced_metadata) {
        foreach ($this::ADVANCED_METADATA as $property) {
          unset($result->$property);
        }
      }

      return $result;
    }
  }
?>