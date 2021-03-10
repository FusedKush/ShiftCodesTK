<?php
  namespace ShiftCodesTK\Database;

  /** Represents variable bindings for use in a *Prepared Query* */
  class PreparedVariables {
    /**
     * @var string The *Type String* used to indicate the type of variables being substituted in the `query`.
     * - Each variable is represented using one of the following letters:
     * - - `i` - Integer
     * - - `d` - Double (Float)
     * - - `s` - String
     * - - `b` - Blob
     */
    public $type_string = '';
    /**
     * @var array The variables to be binded to the *Prepared Query*.
     * - Variable Sets are queried in the order they are indexed.
     * - Variables are substituted into the `query` in the order they are indexed.
     */
    public $variables = [];

    /**
     * Initialize a new set of Prepared Variables
     * 
     * @param string $type_string The *Type String* used to indicate the type of variables being substituted in the `query`.
     * - Each variable is represented using one of the following letters:
     * - - `i` - Integer
     * - - `d` - Double (Float)
     * - - `s` - String
     * - - `b` - Blob
     * @param array $variables The variables to be binded to the *Prepared Query*.
     * - Multiple sets of variables can be provided as additional arguments to the function.
     * - Alternatively, this argument can be omitted, and you can use the `change_variables()` method to execute repeated queries instead.
     * @return ShiftCodesTKDatabasePreparedVariables|false Returns an object representing the variable bindings to be used in a *Prepared Query*. If an error occurs, returns **false**.
     */
    public function __construct (string $type_string, array ...$variables) {
      try {
        if (!strlen($type_string)) {
          throw new \Error("A valid Parameter Type String was not provided.");
        }

        if ($variables) {
          foreach ($variables as $var_list) {
            if (!is_array($var_list)) {
              throw new \TypeError("Variable lists must be provided in the form of arrays.");
            }
          }
          
          $this->variables = $variables;
        }

        $this->type_string = $type_string;
  
        return $this;
      }
      catch (\Throwable $exception) {
        trigger_error("Failed to save Prepared Variables: {$exception->getMessage()}");
      }
    }
    /**
     * Update the *Prepared Variables* with new data
     * 
     * @param ShiftCodesTKDatabaseQuery|false $execute_query If a `ShiftCodesTKDatabaseQuery` is provided, it will be executed once the new variables have been bound. 
     * @param array $variables An array of *Variables* to be bound to the Prepared Statement. Multiple sets of variables can be provided as additional arguments.
     * @return ShiftCodesTKDatabaseQueryResult|bool Returns a value based on the value of `$execute_query`:
     * - If `$execute_query` is **false**, a `boolean` will be returned, returning **true** on success or **false** if an error occurred.
     * - If `$execute_query` is not **false**, returns a `ShiftCodesTKDatabaseQueryResult` on success, or **false** if an error occurred.
     */
    public function change_variables ($execute_query = false, ...$variables) {
      try {
        if (!count($variables)) {
          throw new \Error("No variables for binding were provided.");
        }

        foreach ($variables as $var_list) {
          if (!is_array($var_list)) {
            throw new \TypeError("Variable lists must be provided in the form of arrays.");
          }
        }
        
        $this->variables = $variables;

        if ($execute_query) {
          return $execute_query->query();
        }
        else {
          return true;
        }
      }
      catch (\Throwable $exception) {
        trigger_error("Failed to change Prepared Variables: {$exception->getMessage()}");
        return false;
      }
    }
  }
?>