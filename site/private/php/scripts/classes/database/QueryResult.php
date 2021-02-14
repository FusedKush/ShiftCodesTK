<?php 
  namespace ShiftCodesTK\Database;

  /** Represents the result of a Database Query */
  class QueryResult {
    /**
     * @var mixed The Result of the Database Query. 
     * - Use the `get_result()` method of the `ShiftCodesTKDatabaseQuery` to retrieve this value.
     */
    public $result = null;
    /**
     * @var array|null An array made up of metadata related to the *queried fields*.
     * - Requires the `get_field_metadata` option to be set to **true** to be populated.
     * - Each field returned by the query is indexed with the following properties:
     * | Property | Description |
     * | --- | --- |
     * | name | The name of the column |
     * | orgname | Original column name if an alias was specified |
     * | table | The name of the table this field belongs to (if not calculated) |
     * | orgtable | Original table name if an alias was specified |
     * | def | Reserved for default value, currently always "" |
     * | db | Database (since PHP 5.3.6) |
     * | catalog | The catalog name, always "def" (since PHP 5.3.6) |
     * | max_length | The maximum width of the field for the result set. |
     * | length | The width of the field, as specified in the table definition. |
     * | charsetnr | The character set number for the field. |
     * | flags | An integer representing the bit-flags for the field. |
     * | type | The data type used for this field |
     * | decimals | The number of decimals used (for integer fields) |
     */
    public $field_metadata = null;
    /**
     * @var array|null An array made up of `ShiftCodesTKDatabaseQueryResultData` objects for each executed query.
     * - Requires the `get_result_set_data` option to be set to **true** to be populated.
     */
    public $result_set = null;

    /**
     * Initialize a new Database Query Result
     * 
     * @param array $result_data An array made up of the result data, corresponding to the properties of the `ShiftCodesTKDatabaseQueryResult` object.
     * @return \ShiftCodesTKDatabaseQueryResult|false Returns an object representing the result of the database query. If an error occurs, returns **false**.
     */
    public function __construct (array $result_data) {
      foreach ($result_data as $property_name => $property_value) {
        if (($this->$property_name ?? false) !== false) {
          trigger_error("\"{$property_name}\" is not a valid property value.", E_USER_WARNING);
          continue;
        }

        $this->$property_name = $property_value;
      }
    }
  }
?>