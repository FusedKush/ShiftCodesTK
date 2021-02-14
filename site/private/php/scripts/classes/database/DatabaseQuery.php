<?php
  namespace ShiftCodesTK\Database;

  /** Represents a query for the ShiftCodesTK Database */
  class DatabaseQuery {
    use MysqliMixin;

    /**
     * @var string The query string used to query the database.
     */
    public $query = null;
    /**
     * @var QueryOptions The list of options used to configure the query and returned results.
     */
    public $options = null;
    /**
     * @var PreparedVariables|null Variable bindings for use in a *Prepared Query*.
     * - The presence of this property indicates if the query is a *Prepared Query* or not.
     */
    public $prepared_variables = null;
    /**
     * @var array An array of returned `ShiftCodesTKDatabaseQueryResult` objects. Use `get_result()` to retrieve the formatted results.
     */
    public $result = null;
    /**
     * @var \ResponseObject|null The `ResponseObject` of the query. 
     * - Requires the `get_response_object` option to be **true** to be populated.
     * - The `payload` will *never* be populated within this property. Use `get_response()` to retrieve the full `ResponseObject`. 
     */
    public $response = null;

    /**
     * Create a new query for the ShiftCodesTK Database
     * 
     * @param string $query The query string used to query the database.
     * - This value is *not* automatically sanitized. You can use `ShiftCodesTKDatabase::clean_string()` to sanitize the string.
     * @param array $options An array of `ShiftCodesTKDatabaseQueryOptions` to configure the query with.
     * @param PreparedVariables|null $prepared_variables An object representing the parameters to be binded to a *prepared statement*.
     * @return DatabaseQuery|false Returns an object representing a query for the database. If an error occurred, returns **false**.
     */
    public function __construct($query, array $options = [], PreparedVariables $prepared_variables = null) {
      try {
        $queryType = $prepared_variables === null
                      ? 'standard'
                      : 'prepared';
        
        $this->mysqli = Database::get_handle();

        if ($options['create_response_object'] ?? false) {
          $this->response = new \ResponseObject();
        }

        if ($this->mysqli === false) {
          throw new \Error("A connection to the database could not be established.");
        }

        // Set Query
        (function () use ($query, $prepared_variables, $queryType, $options) {
          /** The number of queries found in the `query` */
          $queryCount = (function () use ($query) {
            $count = substr_count($query, ';');
    
            if ($count > 1) {
              return $count;
            }
            else if ($count == 1) {
              return 1;
            }
            else {
              return 1;
            }
          })();
          $replacementCount = substr_count($query, '?');
          $queryStr = $query;
  
          $queryStr = collapseWhitespace($queryStr);
  
          if (strlen($queryStr) == 0) {
            throw new \Error('Query cannot be empty');
          }
          if ($queryCount > 1) {
            if ($queryType == 'prepared') {
              throw new \Error("Multiple queries are not permitted when using Prepared Statements.");
            }
            if (!($options['allow_multiple_queries'] ?? false)) {
              throw new \Error("Multiple queries are not permitted.");
            }
          }
          if ($queryType == 'standard') {
            if ($replacementCount > 0) {
              throw new \Error("Replacements were found in the query string, but no prepared data was provided.");
            }
          }
          if ($queryType == 'prepared') {
            $replacementCount = substr_count($queryStr, '?');
            $bindingCount = strlen($prepared_variables->type_string);

            if ($replacementCount == 0) {
              throw new \Error("No replacements were found in the query string for a Prepared Statements.");
            }
            if ($replacementCount != $bindingCount) {
              throw new \Error("The number of replacements in the query statement does not match the number of bound variables. {$bindingCount} expected, but {$replacementCount} provided.");
            }
          }
  
          $this->query = $queryStr;
        })();
        // Set Options
        (function () use ($options) {
          $optionList = new QueryOptions();
          $validatedOptions = check_parameters($options, $optionList::$VALIDATION_PROPERTIES);
  
          if ($validatedOptions['valid']) {
            foreach ($validatedOptions['parameters'] as $optionName => $optionValue) {
              $optionList->$optionName = $optionValue;
            }

            $this->options = $optionList;
          }
          else {
            foreach ($validatedOptions['errors'] as $error) {
              $errorStr = print_r($error, true);
  
              throw new \Error("Invalid Option: {$errorStr}");
            }
          }
  
          foreach ($validatedOptions['warnings'] as $warning) {
            $warningStr = print_r($warning, true);
  
            if ($this->options->create_response_object && $this->options->log_response_issues) {
              $this->response->setWarning($warning);
            }

            trigger_error("Invalid ShiftCodesTKDatabaseQuery Option: {$warningStr}", E_USER_WARNING);
          }
        })();
        // Prepared Variables
        (function () use ($prepared_variables, $queryType) {
          if ($queryType == 'prepared') {
            $this->prepared_variables = $prepared_variables;
          }
        })();

        return $this;
      }
      catch (\Throwable $exception) {
        if ($options['create_response_object'] ?? false) {
          $this->response->set(500);

          if ($options['log_response_issues'] ?? false) {
            $this->response->setError(errorObject('DatabaseQueryError', null, $exception->getMessage()));
          }
        }

        trigger_error("Failed to create the Database Query: {$exception->getMessage()}");
      }
    }
    /**
     * Perform a query on the database
     * 
     * @return mixed Returns the result of the query on success. See `get_result()` for more information on this value. If the query fails, returns **false**.
     */
    public function query () {
      try {
        $this->check_mysqli_errors(true);

        /** The result of the query */
        $queryResult = [];
        /** The number of queries found in the `query` */
        $queryCount = (function () {
          $count = substr_count($this->query, ';');
  
          if ($count > 1) {
            return $count;
          }
          else if ($count == 1) {
            return 1;
          }
          else {
            return 1;
          }
        })();
        $queryType = $this->prepared_variables === null
                      ? 'standard'
                      : 'prepared';
                      
        if ($queryType == 'standard') {
          $totalItemCount = (function () {
            if ($this->options->get_result_set_data) {
              $countQueryStr = (function () {
                $queryStr = $this->query;
  
                $queryStr = explode(';', $queryStr);

                foreach ($queryStr as $index => $query) {
                  if (!preg_match('/^((?:SELECT){1}(?:[^;])+(?:;(?: ){0,1}|$){1})+$/i', $query)) {
                    array_splice($queryStr, $index, 1);
                  }
                }

                $queryStr = preg_replace([
                  '/(?:SELECT)(?:.+)(\b(?:FROM|SET)\b)(?!.*\b\1\b)/i',
                  '/(\bWHERE\b)(?!.*\b\1\b) (.+?) (?:GROUP BY|ORDER BY|LIMIT|OFFSET){0,1} (?:.+?)($|;)/i',
                  '/LIMIT {0,1}\d+/',
                  '/OFFSET {0,1}\d+/',
                  '/ ;/'
                ],
                [
                  "SELECT COUNT(*) AS 'count' $1", 
                  "$1 $2$3", 
                  "",
                  "",
                  ";"
                ], 
                $queryStr);
                $queryStr = implode(';', $queryStr);
  
                // Add final delimiter
                if (preg_match('/;([ \r\n]+$)/', $queryStr) === false) {
                  $queryStr .= ';'; 
                }
  
                return $queryStr;
              })();
              $countQueryOptions = [
                'collapse_result'        => true,
                'collapse_row'           => true,
                'allow_multiple_queries' => true
              ];
              $countQuery = new DatabaseQuery($countQueryStr, $countQueryOptions);
              $countQueryResult = $countQuery->query();
  
              return $countQueryResult;
            }
  
            return false;
          })();
          $processResultSet = function ($result_set, $i = 0) use ($queryCount, $totalItemCount) {
            $processedResult = [];
            $resultSetQueryStr = (function () use ($queryCount, $i) {
              if ($queryCount == 1) {
                return $this->query;
              }
              else {
                $matches = [];
  
                preg_match_all('/(?:[^;]+)(?:;|$){0,1}(?: {0,1})/i', $this->query, $matches, PREG_SET_ORDER);
  
                if ($matches) {
                  return $matches[$i][0];
                }
                else {
                  return $this->query;
                }
              }
            })();
            $queryReturnsResults = preg_match('/^(?:SELECT){1}/', $resultSetQueryStr);
            $fieldMetadata = (function () use ($queryReturnsResults, &$result_set) {
              if ($queryReturnsResults && !is_bool($result_set)) {
                $fieldMetadata = [];

                while ($fieldData = $result_set->fetch_field()) {
                  // $fieldName = $fieldData->name;
                  $fieldMetadata[$fieldData->name] = new QueryResultFieldMetadata($fieldData);

                  // $fieldMetadata[$fieldName] = $fieldData;
                  // $fieldMetadata[$fieldName]->type_name = ShiftCodesTKDatabase::get_field_type_info($fieldData->type)['name'];
                }

                return $fieldMetadata;
              }

              return null;
            })();
            $resultSetData = (function () use ($queryCount, $totalItemCount, $i, $resultSetQueryStr, $queryReturnsResults) {
              if ($queryReturnsResults && $totalItemCount !== false) {
                $totalResultChunkSize = $queryCount > 1 
                                        ? $totalItemCount[$i] 
                                        : $totalItemCount[0];
                if ($totalResultChunkSize !== false) {
                  return new QueryResultSetData($resultSetQueryStr, $this->mysqli->affected_rows, $totalResultChunkSize->result);
                }
              }
              
              return null;
            })();
    
            // `SELECT` Results
            if (!is_bool($result_set)) {
              while ($row = $result_set->fetch_assoc()) {
                if ($fieldMetadata) {
                  foreach ($row as $col => &$val) {
                    $typeInfo = Database::get_field_type_info($fieldMetadata[$col]->type);
  
                    if ($typeInfo) {
                      if ($typeInfo['category'] == 'numeric') {
                        $intTypes = [
                          'BIT',
                          'TINYINT',
                          'SMALLINT',
                          'MEDIUMINT',
                          'INT',
                          'BIGINT',
                          'NUMERIC'
                        ];
                        $floatTypes = [
                          'DECIMAL',
                          'FLOAT',
                          'DOUBLE'
                        ];

                        settype($val, array_search($typeInfo['name'], $floatTypes) !== false ? 'float' : 'integer');
                      }
                      else if ($typeInfo['name'] == 'JSON') {
                        $val = json_decode($val, true);
                      }
                    }
                  }
                }

                $processedResult[] = $row;
                // if ($this->options->collapse_row && count($row) == 1) {
                //   $processedResult[] = reset($row);
                // }
                // else {
                // }
              }
    
              $result_set->free_result();
            }
            // `INSERT` Results
            else if ($this->mysqli->insert_id != 0) {
              $processedResult[] = [ 'insert_id' => $this->mysqli->insert_id ];
            }
            // `UPDATE`, `DELETE`, etc... Results
            else if ($this->mysqli->affected_rows > -1) {
              $processedResult[] = [ 'affected_rows' => $this->mysqli->affected_rows ];
            }
            // Bad Result
            else {
              $processedResult = false;
            }
    
            return new QueryResult([
              'result'     => $processedResult,
              'field_metadata' => $this->options->get_field_metadata && $queryReturnsResults ? $fieldMetadata : null,
              'result_set' => $resultSetData
            ]);
          };

          // Single Query
          if ($queryCount == 1) {
            $result = $this->mysqli->query($this->query);
    
            $this->check_mysqli_errors(false, function () use (&$result) {
              if (!is_bool($result)) {
                $result->free_result();
              }
            });
  
            $queryResult[] = $processResultSet($result);
          }
          // Multi Query
          else {       
            $this->mysqli->multi_query($this->query);
            $i = -1;
  
            do {
              $i++;
              $result = $this->mysqli->store_result();
  
              $this->check_mysqli_errors(false, function () use (&$result) {
                if (!is_bool($result)) {
                  $result->free_result();
                }
              });
  
              $queryResult[] = $processResultSet($result, $i);
            }
            while ($this->mysqli->more_results() && $this->mysqli->next_result());
          }
        }
        else if ($queryType == 'prepared') {
          /** The results of a prepared query result set */
          $results = [];
          $preparedVars = $this->prepared_variables->variables;

          if (!$preparedVars) {
            throw new \Exception('You must add variables with "ShiftCodesTKDatabaseQueryPreparedVariables->change_variables()" before you can execute the query.');
          }

          $preparedStatement = $this->mysqli->prepare($this->query);
          $queryReturnsResults = preg_match('/^(?:SELECT){1}/', $this->query);
          $totalItemCount = (function () use ($queryReturnsResults) {
            if ($this->options->get_result_set_data && $queryReturnsResults) {
              $countQueryStr = (function () {
                $queryStr = $this->query;
  
                $queryStr = explode(';', $queryStr);
                $queryStr = preg_replace([
                  '/(?:SELECT)(?:.+)(\b(?:FROM|SET)\b)(?!.*\b\1\b)/i',
                  '/(\bWHERE\b)(?!.*\b\1\b) (.+?) (?:GROUP BY|ORDER BY|LIMIT|OFFSET){0,1} (?:.+?)($|;)/i',
                  '/LIMIT {0,1}\d+/',
                  '/OFFSET {0,1}\d+/',
                  '/ ;/'
                ],
                [
                  "SELECT COUNT(*) AS 'count' $1", 
                  "$1 $2$3", 
                  "",
                  "",
                  ";"
                ], 
                $queryStr);
                $queryStr = implode(';', $queryStr);
  
                // Add final delimiter
                if (preg_match('/;([ \r\n]+$)/', $queryStr) === false) {
                  $queryStr .= ';'; 
                }
  
                return $queryStr;
              })();
              $countQueryOptions = [
                'collapse_result'        => true,
                'collapse_row'           => true,
                'allow_multiple_queries' => true
              ];
              $countQuery = new DatabaseQuery($countQueryStr, $countQueryOptions, $this->prepared_variables);
              $countQueryResult = $countQuery->query();
  
              return $countQueryResult;
            }

            return false;
          })();

          $this->check_mysqli_errors();

          // Execute Prepared Statements
          for ($i = 0; $i < count($preparedVars); $i++) {
            $setResult = [];
            $vars = array_values($preparedVars[$i]);

            $preparedStatement->bind_param($this->prepared_variables->type_string, ...$vars);
            $preparedStatement->execute();
            $preparedStatement->store_result();

            $this->check_mysqli_errors();

            $fieldMetadata = (function () use ($queryReturnsResults, &$preparedStatement) {
              if ($queryReturnsResults) {
                $metadata = $preparedStatement->result_metadata();
  
                if ($metadata && !is_bool($metadata)) {
                  $fieldMetadata = [];
  
                  while ($fieldData = $metadata->fetch_field()) {
                    $fieldName = $fieldData->name;

                    $fieldMetadata[$fieldName] = $fieldData;
                    $fieldMetadata[$fieldName]->type_name = Database::get_field_type_info($fieldData->type)['name'];
                  }
  
                  $metadata->free_result();

                  return $fieldMetadata;
                }
              }

              return null;
            })();
            $resultSetData = (function () use ($totalItemCount, $i, $queryReturnsResults, &$preparedStatement) {
              if ($queryReturnsResults && $totalItemCount !== false) {
                $totalResultChunkSize = count($this->prepared_variables->variables) > 1 
                                        ? $totalItemCount[$i] 
                                        : $totalItemCount[0];

                if ($totalResultChunkSize !== false) {
                  return new QueryResultSetData(
                    $this->query, 
                    $preparedStatement->num_rows > 0
                      ? $preparedStatement->num_rows
                      : $preparedStatement->affected_rows, 
                    $totalResultChunkSize->result
                  );
                }
              }
              
              return null;
            })();
            
            // `SELECT` Results
            if ($preparedStatement->num_rows > 0) {
              $results = array_keys($fieldMetadata);
              $preparedStatement->bind_result(...$results);
              
              while ($preparedStatement->fetch()) {
                $row = [];
                
                foreach ($results as $field => &$value) {
                  $row[$field] = $value;

                  if ($fieldMetadata) {
                    $typeInfo = Database::get_field_type_info(array_values($fieldMetadata)[$field]->type);
  
                    if ($typeInfo) {
                      if ($typeInfo['category'] == 'numeric') {
                        $floatTypes = [
                          'DECIMAL',
                          'NUMERIC',
                          'FLOAT',
                          'DOUBLE'
                        ];
  
                        settype($value, array_search($typeInfo['name'], $floatTypes) !== false ? 'float' : 'integer');
                      }
                      else if ($typeInfo['name'] == 'JSON') {
                        $value = json_decode($value, true);
                      }
                    }
                  }
                }
                
                $setResult[] = array_combine(array_keys($fieldMetadata), $row);
              }
            }
            // `INSERT` Results
            else if ($preparedStatement->insert_id != 0) {
              $setResult[] = [ 'insert_id' => $preparedStatement->insert_id ];
            }
            // `UPDATE`, `DELETE`, etc... Results
            else if ($preparedStatement->affected_rows > -1) {
              $setResult[] = [ 'affected_rows' => $preparedStatement->affected_rows ];
            }
            // Bad Result
            else {
              $setResult[] = false;
            }
    
            $queryResult[] = new QueryResult([
              'result'     => $setResult,
              'field_metadata' => $this->options->get_field_metadata && $queryReturnsResults ? $fieldMetadata : null,
              'result_set' => $resultSetData
            ]);

            $preparedStatement->free_result();
          }
        }

        $this->result = $queryResult;

        return $this->get_result();
      }
      catch (\Throwable $exception) {
        if ($this->options->create_response_object) {
          $this->response->set(500);

          if ($this->options->log_response_issues) {
            $this->response->setError(errorObject('DatabaseQueryError', null, $exception->getMessage()));
          }
        }

        trigger_error("Database Query Failed: {$exception->getMessage()}");
        return false;
      }
    }
    /**
     * Retrieve the result of the query.
     * - You *must* call `query()` before you can use this method.
     * 
     * @return mixed Returns the result of the previous query. 
     * - This value is formatted based on the `collapse_query_result`, `collapse_result`, `collapse_row`, & `collapse_all` options. You can use the `collapse_all` option to collapse all arrays where only one item is returned. 
     * - The return value begins as a `multi-dimensional array`, and is modified accordingly:
     * - - `[ [ [ id => 1 ] ] ]` **<--** The outer-most array holds the *query results*. You can use the `collapse_query_result` option to collapse this array when only one query result is returned.
     * - - `[ [ [ id => 1 ] ]` **<--** The second array holds the *query result*. You can use the `collapse_result` option to collapse this array when only one result is returned.
     * - - `[ [ [ id => 1 ]` **<--** The inner-most array holds the *result date*. You can use the `collapse_row` option to collapse this array when only one row is returned.
     * - If the response data has not been retrieved yet, or if an error occurred while executing the query, returns **null**.
     */
    public function get_result () {
      $result = null;
      
      if ($this->result !== null) {
        // Process Query Results
        foreach ($this->result as $queryIndex => $queryResult) {
          $result[$queryIndex] = clone $queryResult;
          $resultData = &$result[$queryIndex]->result;
  
          // Process Result Sets
          foreach ($resultData as $resultSetIndex => &$resultSet) {
            // Process Rows
            foreach ($resultSet as $field => &$value) {
              $formatParameter = $this->options->format_parameters[$field] ?? false;
              $isDate = validateDate($value, Database::DATE_FORMATS);
  
              if ($isDate) {
                $timezone = (function () use ($resultSet, $formatParameter) {
                  $tz = $formatParameter && ($formatParameter['use_timezone'] ?? false)
                        ? $formatParameter['use_timezone']
                        : null;
                        
                  if ($tz) {
                    $timezone = $resultSet[$tz] ?? false;
  
                    if ($timezone) {
                      try {
                        $dtTimezone = new \DateTimeZone($timezone);
  
                        if ($dtTimezone) {
                          return $dtTimezone;
                        }
                      }
                      catch (\Throwable $exception) {
                        trigger_error("\"{$timezone}\" is not a valid timezone name.", E_USER_WARNING);
                      }
                    }
  
                    trigger_error("\"{$tz}\" is not a valid timezone field.", E_USER_WARNING);
                  }
  
                  return null;
                })();
                $date = new \DateTime($value, $timezone);
  
                if ($date) {
                  $value = $date->format('c'); 
                }
              }
              else if ($formatParameter && ($formatParameter['use_timezone'] ?? false)) {
                trigger_error("The \"use_timezone\" option cannot be used with \"{field}\", as it does not have a valid Date-Time value.", E_USER_WARNING);
              }
  
              if ($formatParameter && ($formatParameter['change_type'] ?? false)) {
                $type = $formatParameter['change_type'];
  
                if ($type == 'json_array' || $type == 'json_object') {
                  $value = json_decode($value, $type == 'json_array');
                }
                else {
                  settype($value, $formatParameter['change_type']);
                }
              }
            }
            
            if (($this->options->collapse_all || $this->options->collapse_row) && count($resultSet) == 1) {
              $resultSet = $resultSet[array_key_first($resultSet)];
            }
          }
          
          if ($result[$queryIndex]->field_metadata) {
            foreach ($result[$queryIndex]->field_metadata as $field => &$fieldMetadata) {
              $fieldMetadata = $fieldMetadata->get_metadata($this->options->get_field_metadata === 'ADVANCED');
            }
          }
          if (($this->options->collapse_all || $this->options->collapse_result) && count($resultData) == 1) {
            $resultData = $resultData[array_key_first($resultData)];
          }
          if (($this->options->collapse_all || $this->options->collapse_result_data)) {
            if (!$result[$queryIndex]->field_metadata && !$result[$queryIndex]->result_set) {
              $result[$queryIndex] = $result[$queryIndex]->result;
            }
          }
        }
  
        if (($this->options->collapse_all || $this->options->collapse_query_result) && count($result) == 1) {
          $result = $result[array_key_first($result)];
        }
      }

      return $result;
    }
    /**
     * Retrieve the `ResponseObject` of the query.
     * - The `create_response_object` option must be **true** for this method to work.
     * - You *must* call `query()` before the `payload` can be populated.
     * 
     * @return \ResponseObject Returns the `ResponseObject` of the query. 
     * - The response payload is formatted the same way as `get_result()`. 
     * @throws \Exception Throws an error if `create_response_object` is **false**.
     */
    public function get_response () {
      $response = false;

      if (!$this->options->create_response_object) {
        throw new \Error("Cannot retrieve query ResponseObject: \"create_response_object\" is \"false\".");
      }
      
      $response = clone $this->response;
      $result = $this->get_result();

      if ($result !== null) {
        $response->payload = $result;
      }

      return $response;
    }
  }
?>