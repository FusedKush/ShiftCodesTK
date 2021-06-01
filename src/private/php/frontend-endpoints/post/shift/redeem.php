<?php
  use ShiftCodesTK\Validations;

  /**
   * @var array Parameter validation settings
   */
  $paramSettings = [
    'code' => new Validations\VariableEvaluator([
      'type'        => 'string',
      'required'    => true,
      'validations' => [
        'check_range' => [ 
          'is' => 12 
        ]
      ]
    ]),
    'action' => new Validations\VariableEvaluator([
      'type'        => 'string',
      'required'    => true,
      'validations' => [
        'check_match' => [ 
          'redeem', 
          'remove' 
        ]
      ]
    ])
  ];
  /**
   * @var Validations\GroupEvaluationResult The validated request parameters
   */
  $validation = (function () use ($paramSettings) {
    $evaluator = new Validations\GroupEvaluator($paramSettings);
    
    $evaluator->check_variables($_POST);
    return $evaluator->get_last_result(false);
  })();
  /**
   * @var string The User's Redemption ID.
   */
  $redemptionID = redemption_get_id();

  if (!$redemptionID) {
    $redemptionID = redemption_new_id();
  }
  else {
    redemption_update_cookie($redemptionID);
  }
  
  // Invalid request parameters
  if (!$validation->result) {
    $response->set(-1);
    
    foreach ($validation->errors as $error) {
      $response->setError($error);
    }
    
    $response->send();
    exit;
  }

  // Update the database
  (function () use (&$_mysqli, &$response, $validation, $redemptionID) {
    /**
     * @var array The request parameters
     */
    $params = $validation['variables'];
    /**
     * @var string The SQL Query statement
     */
    $query = "";
    /**
     * @var array The SQL Query parameters
     */
    $queryParams = [
      $redemptionID,
      $params['code']
    ];
    /**
     * @var int|false The result of the SQL Query as the number of affected rows, or **false** on error
     */
    $result = false;
    
    if ($params['action'] == 'add') {
      $query = "INSERT INTO shift_codes_redeemed
                (redemption_id, code_hash)
                VALUES (?, ?)";
      $response->set(2);
    }
    else if ($params['action'] == 'delete') {
      $query = "DELETE FROM shift_codes_redeemed
                WHERE redemption_id = ?
                  AND code_hash = ?
                LIMIT 1";
      $response->set(1);
    }

    $result = $_mysqli->prepared_query($query, 'ss', $queryParams, [ 'collapseAll' => true ]);

    if (!$result) {
      $response->set(-3);
    }
  })();
  // Update payload
  (function () use (&$response, $validation, $redemptionID) {
    if ($response->status_code < 400) {
      /**
       * @var boolean Indicates if an informational toast should be displayed when the request has completed.
       */
      $displayToast = $redemptionID === false && $validation['variables']['action'] == 'add';
      /**
       * @var int Indicates the type of toast that should be displayed, based on if the user is logged in or not.
       */
      $toastType = auth_isLoggedIn() ? 2 : 1;

      $response->setPayload($displayToast, 'displayToast');

      if ($displayToast) {
        $response->setPayload($toastType, 'toastType');
      }
    }
  })();

  $response->send();
?>