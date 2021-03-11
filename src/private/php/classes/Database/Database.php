<?php
  namespace ShiftCodesTK\Database;
  use ShiftCodesTK\Auth;

  /** Represents a connection to the ShiftCodesTK Database */
  class Database {
    use MysqliMixin;

    /**
     * @var array A list of date-time formats to use with the database.
     * 
     * | Name | Value |
     * | --- | --- |
     * | `date` | Y-m-d |
     * | `time` | H:i:s |
     * | `date_time` | Y-m-d H:i:s |
     * | `full_date` | Y-m-d H:i:s.u |
     */
    public const DATE_FORMATS = [
      'date'      => 'Y-m-d',
      'time'      => 'H:i:s',
      'date_time' => 'Y-m-d H:i:s',
      'full_date' => 'Y-m-d H:i:s.u'
    ];
    /**
     * A list of *Field Types* and their respective categories.
     * - See `$FIELD_TYPES` for the compiled list of *Field Types*.
     * - Use `get_field_type_info()` to retrieve information about a specific *Field Type*.
     */
    private const FIELD_TYPES = [
      'types'     => [
        'DECIMAL'    => 'DECIMAL',
        'TINYINT'    => 'TINY',
        'SMALLINT'   => 'SHORT',
        'INT'        => 'LONG',
        'FLOAT'      => 'FLOAT',
        'DOUBLE'     => 'DOUBLE',
        'NULL'       => 'NULL',
        'TIMESTAMP'  => 'TIMESTAMP',
        'BIGINT'     => 'LONGLONG',
        'MEDIUMINT'  => 'INT24',
        'DATE'       => 'DATE',
        'TIME'       => 'TIME',
        'DATETIME'   => 'DATETIME',
        'YEAR'       => 'YEAR',
        'DATE'       => 'NEWDATE',
        'ENUM'       => 'ENUM',
        'SET'        => 'SET',
        'TINYBLOB'   => 'TINY_BLOB',
        'MEDIUMBLOB' => 'MEDIUM_BLOB',
        'LONGBLOB'   => 'LONG_BLOB',
        'BLOB'       => 'BLOB',
        'VARCHAR'    => 'VAR_STRING',
        'STRING'     => 'STRING',
        'CHAR'       => 'CHAR',
        'INTERVAL'   => 'INTERVAL',
        'GEOMETRY'   => 'GEOMETRY',
        'JSON'       => 'JSON',
        'NUMERIC'    => 'NEWDECIMAL',
        'BIT'        => 'BIT'
      ],
      'categories' => [
        'numeric' => [
          'BIT',
          'TINYINT',
          'SMALLINT',
          'MEDIUMINT',
          'INT',
          'BIGINT',
          'DECIMAL',
          'NUMERIC',
          'FLOAT',
          'DOUBLE'
        ],
        'string'  => [
          'CHAR',
          'VARCHAR',
          'STRING'
        ],
        'date'    => [
          'DATE',
          'DATE',
          'YEAR',
          'DATETIME',
          'INTERVAL',
          'TIMESTAMP'
        ],
        'set'     => [
          'ENUM',
          'SET'
        ],
        'blob'    => [
          'TINYBLOB',
          'MEDIUMBLOB',
          'LONGBLOB',
          'BLOB'
        ],
        'misc'    => [
          'NULL',
          'GEOMETRY',
          'JSON'
        ]
      ]
    ];

    /** 
     * @var Database|null|false The active instance of the `ShiftCodesTKDatabase`.
     * - Has a value of **null** if an instance has not been created yet. 
     * - Has a value of **false** if an instance could not be created.
     **/
    private static $instance = null;
    /**
     * A list of *Field Types* and their respective information.
     * - See `FIELD_TYPES` for the definition list of *Field Types* and their respective categories.
     * - Use `get_field_type_info()` to retrieve information about a specific *Field Type*.
     */
    public static $FIELD_TYPES = [];
    /**
     * A list of *Field Types* and their respective information.
     * - See `FIELD_TYPES` for the definition list of *Field Types* and their respective categories.
     * - Use `get_field_type_info()` to retrieve information about a specific *Field Type*.
     */
    public static $FIELD_FLAGS = [];

    /**
     * Initialize the connection to the ShiftCodesTK Database
     * 
     * @return Database Returns an object representing a connection to the ShiftCodesTK Database. You can check if `$instance` is equal to **false** to check if an error occurred during initialization.
     */
    private function __construct () {
      try {
        // Compile Field Types
        (function () {
          foreach (self::FIELD_TYPES['types'] as $typeName => $constName) {
            $fullConstName = "MYSQLI_TYPE_{$constName}";
            $category = (function () use ($typeName) {
              foreach (self::FIELD_TYPES['categories'] as $categoryName => $categoryTypeList) {
                foreach ($categoryTypeList as $categoryTypeName) {
                  if ($typeName == $categoryTypeName) {
                    return $categoryName;
                  }
                }
              }
            })();

            self::$FIELD_TYPES[] = [
              'name'     => $typeName,
              'constant' => $fullConstName,
              'int'      => constant($fullConstName),
              'category' => $category
            ];
          }
        })();
        // Compile Field Flags
        (function () {
          $consts = get_defined_constants(true)['mysqli'];

          foreach ($consts as $constName => $constValue) {
            $matches = [];

            if (preg_match('/MYSQLI_(\w+)_FLAG/', $constName, $matches)) {
              self::$FIELD_FLAGS[] = [
                'name'     => str_replace('_', ' ', $matches[1]),
                'constant' => $constName,
                'int'      => $constValue
              ];
            }
          }
        })();

        $this->mysqli = new \mysqli(...array_values(\ShiftCodesTK\Secrets::get_secret('db')));
        $this->check_mysqli_errors(true);
        $this->mysqli->select_db(!\ShiftCodesTK\BUILD_INFORMATION['is_dev_branch'] ? "ShiftCodesTK" : "ShiftCodesTK_beta");
  
        // Set Session Timezone to UTC
        (function () {
          $result = $this->mysqli->query("SET @@session.time_zone = '+00:00'");

          if (!$result) {
            throw new \Error("Failed to set Session Timezone: {$this->con->connect_error}");
          }
        })();
        // Set Default Charset
        (function () {
          $result = $this->mysqli->set_charset('utf8mb4');

          if (!$result) {
            throw new \Error("Failed to update the Default Character Set Encoding.");
          }
        })();
  
        return $this;
      }
      catch (\Exception $exception) {
        throw new \mysqli_sql_exception("Database Connection Failed: {$exception->getMessage()}");
      }
    }

    /**
     * Retrieve the active instance of the `ShiftCodesTKDatabase`
     * 
     * @return ShiftCodesTKDatabase|false Returns the active instance of the `ShiftCodesTKDatabase`, or **false** if one is not available.
     */
    public static function get_instance () {
      if (self::$instance === null) {
        self::$instance = new Database();
      }

      return self::$instance;
    }
    /**
     * Retrieve the active *connection handle* for the `ShiftCodesTKDatabase`
     * 
     * @return mysqli|false Returns the `mysqli` connection handle of the `ShiftCodesTKDatabase`, or **false** if it is not available.
     */
    public static function get_handle () {
      $instance = self::get_instance();

      if ($instance) {
        return self::get_instance()->mysqli;
      }
      else {
        return false;
      }
    }
    /**
     * Begin a new *Transaction* on the database
     * - You *must* call `close_transaction()` after the query (or queries) for the changes to be pushed to the database.
     * 
     * @param int $flags A bitmask of `MYSQLI_TRANS_START` constants to be passed to the `mysqli->begin_transaction()` method.
     * - Note that by default the transaction type will be determined based on the query contents.
     * 
     * | Constant | Description |
     * | --- | --- |
     * | MYSQLI_TRANS_START_READ_ONLY | Prepends `START TRANSACTION READ ONLY` to the transaction statement, indicating that the transaction will not be updating any tables. |
     * | MYSQLI_TRANS_START_READ_WRITE | Prepends `START TRANSACTION READ WRITE` to the transaction statement, indicating that the transaction will be updating tables. |
     * | MYSQLI_TRANS_START_WITH_CONSISTENT_SNAPSHOT | Prepends `START TRANSACTION WITH CONSISTENT SNAPSHOT` to the transaction statement, indicating a [Consistent Read](https://dev.mysql.com/doc/refman/8.0/en/glossary.html#glos_consistent_read) operation. |
     * @return string|false Returns the *Transaction ID* on success, or **false** if an error occurred.
     */
    public static function start_transaction (int $flags = 0) {
      $transaction_id = Auth\random_unique_id(30, 'TR', Auth\UNIQUE_ID_TYPE_TOKEN);

      $result = self::get_handle()->begin_transaction($flags, $transaction_id);

      if ($result) {
        return $transaction_id;
      }
      else {
        return false;
      }
    }
    /**
     * Close an existing *Transaction*
     * 
     * @param string $transaction_id The *Transaction ID* returned by `start_transaction()`.
     * @param bool $transaction_result Indicates if the transaction was *successful* or not.
     * - If **true**, the transaction will be *committed*.
     * - If **false**, the transaction will be *rolled back*.
     * @param int $flags A bitmask of `MYSQLI_TRANS_COR` constants to be passed to the `mysqli->commit()` and `mysqli->rollback()` methods.
     * 
     * | Constant | Description |
     * | --- | --- |
     * | MYSQLI_TRANS_COR_AND_CHAIN | Appends `AND CHAIN` to the transaction statement, causing a new transaction to begin as soon as the current one ends. |
     * | MYSQLI_TRANS_COR_AND_NO_CHAIN | Appends `AND NO CHAIN` to the transaction statement. |
     * | MYSQLI_TRANS_COR_RELEASE | Appends `RELEASE` to the transaction statement, causing the server to disconnect after the current transaction ends. | 
     * | MYSQLI_TRANS_COR_NO_RELEASE | Appends `NO RELEASE` to the transaction statement. |
     * @return boolean Returns **true** on success, or **false** on failure.
     */
    public static function close_transaction (string $transaction_id, bool $transaction_result = true, int $flags = -1) {
      $handle = self::get_handle();

      if ($transaction_result) {
        return $handle->commit($flags, $transaction_id);
      }
      else {
        return $handle->rollback($flags, $transaction_id);
      }
    }
    /**
     * Escape a string for use in a SQL Query Statement
     * 
     * The following characters are escaped:
     * - `NUL` (ASCII 0)
     * - `\n`
     * - `\r`
     * - `\`
     * - `'`
     * - `"`
     * - `Control-Z`
     * 
     * @param string $string The string to be escaped.
     * @return string Returns the escaped string.
     */
    public static function escape_string ($string) {
      $cleanStr = $string;

      // $cleanStr = collapseWhitespace($cleanStr);
      $cleanStr = \ShiftCodesTK\Strings\collapse($cleanStr);
      $cleanStr = self::get_handle()->real_escape_string($cleanStr);

      return $cleanStr;
    }
    /**
     * Retrieve information about a given *Field Type*.
     * 
     * @param int|string $field_type The *Field Type* you are retrieving information about. 
     * - Note that this parameter is *case-insensitive*.
     * - Can be one of the following values:
     * - - **Field Type Integer**: The constant integer value of the field type, such as _253_.
     * - - **Field Type Name**: The type name of the field type, such as _"VARCHAR"_.
     * - - **Field Type Constant**: The name of the constant belonging to the field type, such as _"MYSQLI_TYPE_VAR_STRING"_.
     * @param "int|"name"|"constant" $field_type_identifier Indicates the type of value that was provided to the `$field_type`.
     * @return array|false 
     * - On success, returns an `associative array` containing information about the *Field Type*:
     * | Key | Value |
     * | --- | --- |
     * | `name` | The type name of the field type. |
     * | `constant` | The name of the constant belonging to the field type. |
     * | `int` | The integer value of the field type. |
     * | `category` | The category the field type belongs to. |
     * - If an invalid `$field_type` or `$field_type_identifier` was provided, returns **false**.
     */
    public static function get_field_type_info ($field_type, $field_type_identifier = "int") {
      foreach (self::$FIELD_TYPES as $typeInfo) {
        if ($field_type_identifier == 'category' || !isset($typeInfo[$field_type_identifier])) {
          trigger_error("\"{$field_type_identifier}\" is not a valid Field Type Identifier.");
          return false;
        }
        if (strtolower($typeInfo[$field_type_identifier]) == strtolower($field_type)) {
          return $typeInfo;
        }
      }

      return false;
    }
    /**
     * Retrieve the flags for a given field using its *Bit-Flags Integer*
     * 
     * @param int $field_flags The *Bit-Flags Integer* for the field. 
     * @param ""FLAG_STRING"|"FLAG_ARRAY"|"INFO_ARRAY" $return_value Indicates how to format the return value:
     * 
     * | Value | Return Value |
     * | --- | --- |
     * | `"FLAG_STRING"` | Returns a comma-separated `string` containing the names of all present flags. |
     * | `"FLAG_ARRAY"` | Returns an `array` made up of the names of all present flags. |
     * | `"INFO_ARRAY"` | Returns an `associative array` made up of information about all present flags. |
     * @return string|array|false On success, returns a `string` or `array` depending on the value of `$return_value`. If an error occurrs, returns **false**.
     */
    public static function get_field_flags ($field_flags, $return_value = 'FLAG_STRING') {
      $flags = [];

      foreach (self::$FIELD_FLAGS as $flagInfo) {
        if ($field_flags & $flagInfo['int']) {
          if ($return_value == 'INFO_ARRAY') {
            $flags[] = $flagInfo;
          }
          else {
            $flags[] = $flagInfo['name'];
          }
        }

      }
      
      // if (count($flags) == 0) {
      //   return false;
      // }
      if ($return_value == 'FLAG_STRING') {
        return implode(', ', $flags);
      }
      else {
        return $flags;
      }
    }
  }
?>