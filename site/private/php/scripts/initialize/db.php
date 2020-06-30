<?php
  /** Database Functionality */

  /**
   * Represents a connection between PHP and the ShiftCodesTK Database, as well as associated functions
   */
  class ShiftCodesTKDatabase {
    /**
     * The connection handle
     */
    public $con;
    public $dateFormats = [];

    /**
     * Trigger a SQL Query Error
     * 
     * @return boolean Returns false after the error has been logged
     */
    private function queryError () {
      trigger_error("SQL Query Error: {$this->con->error}");
      return false;
    }
    /**
     * Perform updates to the returned set of results
     * 
     * @param array $resultSet The array of results
     * @param array $options The array of passed options
     * @return array Returns the updated array
     */
    private function update_result ($resultSet, $options) {
      $processRow = function (&$rowToProcess) use (&$processRow, $options) {
        foreach ($rowToProcess as $field => &$value) {
          if (is_array($value)) {
            $processRow($value);
            continue;
          }

          $isDate = validateDate($value, $this->dateFormats);
          $preserveDate = (!$value || (is_array($options['preserveDate']) && array_search($field, $options['preserveDate']) !== false || $options['preserveDate'] == 'all'));

          if (!$preserveDate && $isDate) {
            $date = new DateTime($value);
            $value = $date->format('c');
          }
        }
      };

      if (!is_array($resultSet)) {
        $result = [ $resultSet ];
      }
      else {
        $result = $resultSet;
      }

      foreach ($result as &$row) {
        if (is_array($row)) {
          $processRow($row);
        }
        else {
          $isDate = validateDate($row, $this->dateFormats);
          $preserveDate = (!$row || $options['preserveDate'] == 'all');

          if (!$preserveDate && $isDate) {
            $date = new DateTime($row);
            $row = $date->format('c');
          }
        }
      }

      if ($options['collapseResult'] & count($result) == 1) {
        $result = $result[0];
      }

      return $result;
    }
    /**
     * Update the options array as needed
     * 
     * @param array $options The provided options array
     * @return array Returns the updated array.
     */
    private function update_options (&$options) {
      $collapses = ['collapseResult', 'collapseQueryResult', 'collapseRow', 'collapseAll'];
      $additionalOptions = ['preserveDate', 'useTimezone'];

      foreach ($collapses as $collapse) {
        if (isset($options['collapseAll']) && $options['collapseAll']) {
          $options[$collapse] = true;
        }
        else {
          $options[$collapse] = $options[$collapse] ?? false;
        }
      }
      foreach ($additionalOptions as $opt) {
        $options[$opt] = $options[$opt] ?? false;
      }

      return $options;
    }

    /**
     * Perform a simple query against the database
     * 
     * @param string $query The SQL Statement to query the database with. 
       * - Note: The query is *not* automatically sanitized and escaped. You must do so manually.
     * @param array $options Additional options for automatically formatting the result
       * 
       * [collapseResult] boolean 
         * If set to true, a single result will automatically be collapsed to just the returned row.
         * - Only applies to SELECT queries
         * - Example: [ [ 'id' => 1 ] ] becomes [ 'id' => 1 ]
       *
       * [collapseRow] boolean 
         * If set to true, a single row result will automatically be collapsed to just the returned value.
         * - Only applies to SELECT queries
         * - Example: [ 'id' => 1 ] becomes 1 
       *
       * [collapseAll] boolean
         * If set to true, single results and single rows will automatically be collapsed.
         * - Only applies to SELECT queries
         * - Shorthand for [ 'collapseResult' => true, 'collapseRow' => true ]
         * - Example: [ [ 'id' => 1 ] ] becomes 1
       *
       * [preserveDate] array|string 
         * An array of field names whose datetime values should not be converted to ISO 8601 format. Alternatively, the keyword "all" will preserve the date of all results in the set.
         * - Note: If [collapseRow] is set to true, only the keyword "all" will have any effect.
       *
     * @return any For SELECT queries, returns the result on success, formatted based on passed options. On error, returns false.
       * For other queries, returns an returns the number of updated rows on success, or false on error.
     */
    public function query ($query, $options = []) {
      $result = [];

      $this->update_options($options);

      if (!$this->con->errno) {
        $result = [];
        $queryResult = $this->con->query(collapseWhitespace($query));

        // On Error
        if ($this->con->errno) {
          return $this->queryError();
        }

        if (!is_bool($queryResult)) {
          while ($row = mysqli_fetch_assoc($queryResult)) {
            if ($options['collapseRow'] && count($row) == 1) {
              $result[] = reset($row);
            }
            else {
              $result[] = $row;
            }
          }
    
          mysqli_free_result($queryResult);
        }
        else {
          if (mysqli_insert_id($this->con) != 0) {
            $result = mysqli_insert_id($this->con);
          }
          else if (mysqli_affected_rows($this->con) > -1) {
            $result = mysqli_affected_rows($this->con);
          }
          else {
            $result = false;
          }
        }
      }

      return $this->update_result($result, $options);
    }
    /**
     * Perform prepared queries against the database
     * 
     * @param string $query The Prepared SQL Statement to query the database with. 
     * @param string $types The type string to pass to bind_param
      * - 'i' - integer
      * - 'd' - double
      * - 's' - string
      * - 'b' - blob
     * @param array $params The values to be substituted in. For repeated queries, multiple arrays of values are used.
     * @param array $options Additional options for automatically formatting the result
       *   
       * [collapseResult] boolean 
         * If set to true, a single result will automatically be collapsed to just the returned row.
         * - Only applies to SELECT queries
         * - Example: [ [ [ 'id' => 1 ] ] ] becomes [ [ 'id' => 1 ] ]
       *
       * [collapseQueryResult] boolean
         * If set to true, a single query result will automatically be collapsed to just the returned result set.
         * - Only applies to SELECT queries
         * - Example: [ [ 'id' => 1 ] ] becomes [ 'id' => 1 ]
       *
       * [collapseRow] boolean 
         * If set to true, a single row result will automatically be collapsed to just the returned value.
         * - Only applies to SELECT queries
         * - Example: [ 'id' => 1 ] becomes 1 
       *
       * [collapseAll] boolean
         * If set to true, single results, single query results, and single rows will automatically be collapsed.
         * - Only applies to SELECT queries
         * - Shorthand for [ 'collapseResult' => true, 'collapseQueryResult' => true, 'collapseRow' => true ]
         * - Example: [ [ [ 'id' => 1 ] ] ] becomes 1
       *
       * [preserveDate] array|string 
         * An array of field names whose datetime values should not be converted to ISO 8601 format. Alternatively, the keyword "all" will preserve the date of all results in the set.
         * - Note: If [collapseRow] is set to true, only the keyword "all" will have any effect.
       *
     * @return any For SELECT queries, returns an array of results on success, formatted based on the passed options. On error, returns false.
       * For other queries, returns an array of results for each query. Each query returns the number of updated rows on success, or false on error.
     */
    public function prepared_query ($query, $types, array $params, $options = []) {
      $result = [];
      $queryError = function () {
        trigger_error("SQL Query Error: {$this->con->error}");
        return false;
      };

      $this->update_options($options);
      
      // Add index to params if needed
      if (!is_array(reset($params))) {
        $params = [ $params ];
      }
      
      if (!$this->con->errno) {
        $fields = [];
        $stmt = $this->con->prepare(collapseWhitespace($query));
        
        // On Error
        if ($this->con->error) {
          return $this->queryError();
        }

        for ($i = 0; $i < count($params); $i++) {
          $queryResult = [];
          $param = &$params[$i];

          $stmt->bind_param($types, ...$param);
          $stmt->execute();
          $stmt->store_result();

          // On Error
          if ($this->con->error) {
            return $this->queryError();
          }

          if ($stmt->num_rows > 0) {
            // Create associative array of fields
            if ($i == 0) {
              $meta = $stmt->result_metadata();

              while ($field = $meta->fetch_field()) {
                $fields[] = $field->name;
              }
            }

            $values = $fields;

            $stmt->bind_result(...$values);

            while ($stmt->fetch()) {
              $row = [];

              foreach ($values as $key => $value) {
                $row[$key] = $value;
              }

              if ($options['collapseRow'] && count($row) == 1) {
                $queryResult[] = reset($row);
              }
              else {
                $queryResult[] = array_combine($fields, $row);
              }
            }

            if ($options['collapseQueryResult'] && count($queryResult) == 1) {
              $result[] = reset($queryResult);
            }
            else {
              $result[] = $queryResult;
            }
          }
          else if ($stmt->affected_rows > -1) {
            $result[] = $stmt->affected_rows;
          } 
          else {
            $result[] = false;
          }

          $stmt->reset();
        }

        $stmt->close();
      }

      return $this->update_result($result, $options);
    }

    /**
     * Initialize a connection with the database
     * 
     * @return object Returns the connection handle
     */
    public function __construct () {
      $this->con = new mysqli(...array_values(TK_SECRETS['db']));
      $this->dateFormats['date'] = 'Y-m-d';
      $this->dateFormats['time'] = 'H:i:s';
      $this->dateFormats['dateTime'] = "{$this->dateFormats['date']} {$this->dateFormats['time']}";
      $this->dateFormats['fullDateTime'] = "{$this->dateFormats['dateTime']}.u";

      if ($this->con->connect_errno) {
        trigger_error("MySQL Connection Failed: {$this->con->connect_error}");
        exit;
      }

      // Set Session Timezone to UTC
      $sql = $this->query("SET @@session.time_zone = '+00:00'", [ 'collapseAll' => true ]);

      if ($sql === false) {
        trigger_error("Failed to set MySQL Session Timezone: {$this->con->connect_error}");
        exit;
      }

      return $this->con;
    }
  }
  /**
   * The ShiftCodesTK Database and all associated functionality
   */
  $_mysqli = new ShiftCodesTKDatabase();
?>