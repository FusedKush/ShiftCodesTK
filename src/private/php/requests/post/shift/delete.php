<?php
  include_once(\ShiftCodesTK\PRIVATE_PATHS['forms'] . '/shift/delete.php');
  ;
  /** The form Response Object */
  $response = &$form_deleteShiftCode->findReferencedProperty('formSubmit->response');
  /** The form validation result */
  $form_deleteShiftCodeValidation = $form_deleteShiftCode->validateForm();

  // Form is Valid
  if ($form_deleteShiftCodeValidation) {
    /** The parsed form request paramters */
    $requestParameters = $form_deleteShiftCode->findReferencedProperty('formSubmit->parameters');

    /** Check if the request is authenticated */
    $authenticatedRequest = (function () use (&$_mysqli, &$response, $requestParameters) {
      if (auth_isLoggedIn()) {
        if (auth_user_roles()['admin']) {
          return true;
        }
        else {
          /** Existing SHiFT Code Properties */
          $existingCodeOwner = (function () use (&$_mysqli, &$response, $requestParameters, $form_deleteShiftCodeType) {
            $existingCodeID = $requestParameters['code_id'];
            $query = "SELECT owner_id
                      FROM shift_codes
                      WHERE code_id = '${existingCodeID}'
                      LIMIT 1";
            
            $result = $_mysqli->query($query, [ 'collapseAll' => true ]);
  
            if ($result !== false) {
              return $result;
            }
            // Query Error
            else {
              trigger_error("An error occurred while validating SHiFT Code. Code ID: ${existingCodeID}.");
              $response->fatalError(-3);
            }
    
            return false;
          })();
    
          // Permission to update
          if ($existingCodeOwner == auth_user_id()) {
            return true;
          }
          // No permission to update
          else {
          }
        }

        $response->fatalError(401, 'You do not have permission to edit this SHiFT Code.');
      }
      // Not Logged In
      else {
        $response->fatalError(401, 'You must be logged in to perform this action.');
      }
      
      return false;
    })();

    // Request is authenticated
    if ($authenticatedRequest) {
      $codeID = $requestParameters['code_id'];

      // Delete SHiFT Code
      (function () use (&$_mysqli, &$response, $codeID) {
        $query = "UPDATE shift_codes
                    SET
                      owner_id = null,
                      code_state = 'deleted'
                    WHERE code_id = '{$codeID}'
                    LIMIT 1;
                  DELETE FROM shift_code_data
                    WHERE code_id = '{$codeID}'
                    LIMIT 1";
        $result = $_mysqli->query($query, [ 'allowMultipleQueries' => true ]);
        
        var_dump($result);

        $response->setPayload($result, '_result');

        if (!$result) {
          trigger_error("An error occurred while attempting to delete the SHiFT Code.");
          $response->fatalError(-3, errorObject('DeleteShiftCodeError', null, 'An error occurred while attempting to delete the SHiFT Code.'));
        }
      })();
      // Check SHiFT Code
      (function () use (&$_mysqli, &$response, $codeID) {
        $query = "SELECT
                    sc.code_state,
                    scd.code_id
                  FROM
                    shift_codes AS sc
                  LEFT JOIN
                    shift_code_data
                    AS scd
                    ON sc.code_id = scd.code_id
                  WHERE sc.code_id = '{$codeID}'
                  LIMIT 1";
        $result = $_mysqli->query($query, [ 'collapseAll' => true ]);

        $response->setPayload($result, '_check_result');

        // if (!$result) {
        //   trigger_error("An error occurred while attempting to delete the SHiFT Code.");
        //   $response->fatalError(-3, errorObject('DeleteShiftCodeError', null, 'An error occurred while attempting to delete the SHiFT Code.'));
        // }
      })();

      // Success
      (function () use (&$form_deleteShiftCode, &$response, $codeID) {
        $response->set(3);
        $form_deleteShiftCode->updateProperty('formResult->toast->properties', [
          'settings'    => [
            'template'     => 'formSuccess',
            'duration'     => 'infinite'
          ],
          'content'        => [
            'title'           => 'SHiFT Code Deleted',
            'body'            => "SHiFT Code {$codeID} has been successfully deleted."
          ]
        ]);
      })();
    }
  }

  $form_deleteShiftCode->buildResponse();
  $response->send();
  exit;
?>
