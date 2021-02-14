<?php
  namespace ShiftCodesTK\Database;

  /** Options for ShiftCodesTK Database Queries */
  class QueryOptions {
    /**
     * @var array `ValidationProperties` configuration settings for the options.
     * - See `$VALIDATION_PROPERTIES` for the full, compiled list of validation properties.
     */
    private const VALIDATIONS = [
      'allow_multiple_queries' => [
        'type'                    => 'boolean'
      ],
      'get_result_set_data'    => [
        'type'                    => 'boolean'
      ],
      'get_field_metadata'     => [
        'type'                    => 'boolean|string',
        'validations'             => [
          'match'                    => [
            false,
            true,
            'ADVANCED'
          ]
        ]
      ],
      'create_response_object' => [
        'type'                    => 'boolean'
      ],
      'log_response_issues'    => [
        'type'                    => 'boolean'
      ],
      'collapse_query_result'  => [
        'type'                    => 'boolean'
      ],
      'collapse_result'        => [
        'type'                    => 'boolean'
      ],
      'collapse_result_data'   => [
        'type'                    => 'boolean'
      ],
      'collapse_row'           => [
        'type'                    => 'boolean'
      ],
      'collapse_all'           => [
        'type'                    => 'boolean'
      ],
      'format_parameters'      => [
        'type'                    => 'array'
      ]
    ];

    /**
     * @var array A list of compiled `ValidationProperties` objects for each of the query options.
     * - See `VALIDATIONS` for the definition list of validation properties.
     */
    public static $VALIDATION_PROPERTIES = [];

    /** 
     * @var boolean Indicates if multiple queries can be executed using a delimiter.
     * - Has no effect when using `prepared_query()`.
     **/
    public $allow_multiple_queries = false;
    /**
     * @var boolean Indicates if the `Result Set Data` for the request should be included.
     * - If **true**, result set data will be returned *for every query*. This option should be used sparingly when multiple queries are being executed.
     * - Only supports queries that return a result set, such as `SELECT`.
     * - Retrieved via the `$result_set` property of the `ShiftCodesTKDatabaseQuery`.
     */
    public $get_result_set_data = false;
    /**
     * @var boolean|"ADVANCED" Indicates if the `Field Metadata` for the request should be included.
     * - When present, field metadata will be returned for *every field included the query, for every query*. This option should be used sparingly when retrieving many fields simultaneously, or when multiple queries are being executed.
     * - Only supports queries that return a result set, such as `SELECT`.
     * - Retrieved via the `$field_metadata` property of the `ShiftCodesTKDatabaseQuery`.
     * - If the keyword **"ADVANCED"** is passed, *Advanced Metadata* will also be returned. See `ShiftCodesTKDatabaseQueryResultFieldMetadata::get_metadata()` for more information on this value.
     */
    public $get_field_metadata = false;
    /**
     * @var boolean Indicates if a `ResponseObject` should be created for the query.
     * - Retrieved via the `$response` property of the `ShiftCodesTKDatabaseQuery`.
     */
    public $create_response_object = false;
    /**
     * @var boolean Indicates if warnings and errors should be logged to the query's `ResponseObject`.
     * - Issues are retrieved via the `warnings` and `errors` properties of the `$response` property of the `ShiftCodesTKDatabaseQuery`.
     */
    public $log_response_issues = false;
    /**
     * @var boolean If **true**, a single query result will automatically be collapsed to just the returned result set.
     * - Example: `[ [ [ 'result' => [ 'id' => 1 ] ] ] ]` becomes `[ [ 'result' => [ 'id' => 1 ] ] ]`
     */
    public $collapse_query_result = false;
    /**
     * @var boolean If **true**, a single result will automatically be collapsed to just the returned row.
     * - Example: `[ [ 'result' => [ 'id' => 1 ] ] ]` becomes `[ 'result' => [ 'id' => 1 ] ]`
     */
    public $collapse_result = false;
    /**
     * @var boolean If **true**, the query result data will automatically be collapsed to just the result.
     * - Example: `[ 'result' => [ 'id' => 1 ] ]` becomes `[ 'id' => 1 ]`
     */
    public $collapse_result_data = false;
    /**
     * @var boolean If **true**, a single row will automatically be collapsed to just the returned value.
     * - Example: `[ 'id' => 1 ]` becomes `1`
     */
    public $collapse_row = false;
    /**
     * @var boolean If **true**, single results, single query results, & single rows will automatically be collapsed.
     * - This is the same as setting `$collapse_result`, `$collapse_query_result`, & `collapse_row` to **true**.
     * - Example: `[ [ [ 'result' => [ 'id' => 1 ] ] ] ]` becomes `1`
     */
    public $collapse_all = false;
    /**
     * @var array A list of fields and how they are to be transformed before being returned.
     * - Each field contains an `array` with one or more of the following values:
     * - - `true $format_date`: When **true**, the date time value will be converted to ISO 8601 format.
     * - - `string $use_timezone`: When a *timezone field name* is provided, the date time value will be modified to include the included *timezone*.
     * - - `string $change_type`: When a *variable type* is provided, the value will be typecasted to the provided type. 
     */
    public $format_parameters = [];

    /** Initialize the `ShiftCodesTKDatabaseQueryOptions` class */
    public function __construct () {
      // Compile Validation Properties
      (function () {
        if (count(self::$VALIDATION_PROPERTIES) == 0) {
          foreach ($this::VALIDATIONS as $optionName => $optionConfiguration) {
            $compiledConfiguration = array_replace_recursive($optionConfiguration, [ 'value' => $this->$optionName ]);
  
            self::$VALIDATION_PROPERTIES[$optionName] = new \ValidationProperties($compiledConfiguration);
          }
        }
      })();

      return $this;
    }
  }
?>