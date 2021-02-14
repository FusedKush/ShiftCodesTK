<?php
  include_once(PRIVATE_PATHS['forms'] . 'account/update-profile.php');

  $form = &$form_changeUsername;
  $response = &$form->findReferencedProperty('formSubmit->response');
  $success = &$form->findReferencedProperty('formSubmit->success');

  $formIsValid = $form_changeUsername->validateForm();
  
  if ($formIsValid) {
    $currentUser = \ShiftCodesTK\Users\CurrentUser::get_current_user();
    $oldUsername = $currentUser->username;
    $params = $form_changeUsername->findReferencedProperty('formSubmit->parameterList');
    $result = (function () use (&$form, $currentUser, $params, &$response) {
      try {
        return $currentUser->change_username($params['username'], true);
      }
      catch (\Throwable $exception) {
        $errorCode = (function () use ($exception) {
          switch ($exception->getCode()) {
            case 1 :
              return -2;
            case 2 :
              return -1;
            default :
              return -3;
          }
        })();
        $errorMessage = (function () use ($exception) {
          switch ($exception->getCode()) {
            case 1 :
              return 'You cannot change your username at this time.';
            case 2 :
              return 'This username is already in use.';
            case 4 :
              return 'Your username could not be updated due to an error.';
            default :
              return 'An error occurred while changing your username. Please refresh the page and try again.';
          }
        })();
        $form->invalidateRequest($errorCode, errorObject('UsernameError', 'username', $errorMessage, $params['username']));
        return false;
      }
    })();
    
    if ($result) {
      $form_changeUsername->updateProperty(
        'formResult->toast->properties->content->body', 
        "Success! Your username has been changed from <strong>{$oldUsername}</strong> to <strong>{$currentUser->username}</strong>. 
        It may take a few minutes for changes to be reflected across the site.");
      $response->setPayload($currentUser->username, 'new_username');
      $response->setPayload($currentUser->check_username_eligibility(), 'can_change_username_again');
    }
    // $newUsername = ShiftCodesTKDatabase::escape_string($params['username']);
    // $usernameChangeData = auth_user_get_username_change_data();
    // $userID = auth_user_id();

    // // User can change username
    // if (auth_user_can_change_username($usernameChangeData)) {
    //   $usernameIsUnique = (function () use ($userID, $newUsername) {
    //     $uniqueQuery = new ShiftCodesTKDatabaseQuery("
    //       SELECT COUNT(id)
    //         FROM `auth_users`
    //         WHERE 
    //           `username` = '{$newUsername}'
    //           AND `user_id` != '{$userID}'
    //         LIMIT 1",
    //       [
    //         'collapse_all' => true
    //       ]);

    //     return $uniqueQuery->query() === 0;
    //   })();

    //   // Username is unique
    //   if ($usernameIsUnique) {
    //     // Update Username
    //     $usernameQuery = new ShiftCodesTKDatabaseQuery("
    //       UPDATE `auth_users`
    //         SET `username` = '{$newUsername}'
    //         WHERE `user_id` = '{$userID}'
    //         LIMIT 1", 
    //       [ 
    //         'collapse_all' => true 
    //       ]
    //     );
  
    //     $usernameQueryResult = $usernameQuery->query();
    //     $response->setPayload($usernameQueryResult, 'username_query_result');
    
    //     // Username was changed
    //     if ($usernameQueryResult) {
    //       // Update Records
    //       $lastUsernameChange = (function () use ($usernameChangeData) {
    //         $newData = $usernameChangeData;
    //         $newData['timestamp'] = (function () {
    //           $date = new DateTime('now', new DateTimeZone('UTC'));
    
    //           return $date->format('c');
    //         })();
    //         $newData['count']++; 
  
    //         $newDataJSON = json_encode($newData);
    //         $newDataJSON = ShiftCodesTKDatabase::escape_string($newDataJSON);
  
    //         return $newDataJSON;
    //       })();
  
    //       $recordQuery = new ShiftCodesTKDatabaseQuery("
    //         UPDATE `auth_user_records`
    //         SET 
    //           `last_activity` = CURRENT_TIMESTAMP(),
    //           `last_username_change` = '{$lastUsernameChange}'
    //         WHERE `user_id` = '{$userID}'
    //         LIMIT 1;
    //       ",
    //       [
    //         'collapse_all' => true
    //       ]);
    //       $recordResult = $recordQuery->query();
    //       $response->setPayload($recordResult, '_record_result');
  
    //       // Records were updated
    //       if ($recordResult) {
    //         auth_update_user_data($userID);
    //         $form_changeUsername->updateProperty(
    //           'formResult->toast->properties->content->body', 
    //           "Success! Your username has been changed from <strong>{$oldUsername}</strong> to <strong>{$newUsername}</strong>. 
    //           It may take a few minutes for changes to be reflected across the site.");
    //         $response->setPayload($newUsername, 'new_username');
    //         $response->setPayload(auth_user_can_change_username(json_decode($lastUsernameChange, true)), 'can_change_username_again');
    //       } 
    //       // Records were not updated
    //       else {
    //         error_log("User \"{$userID}\"'s records were not successfully updated after changing their username.");
    //       }
    //     }
    //     // Username was not changed
    //     else {
    //       $form->invalidateRequest(-1, errorObject('UsernameChangeError', 'username', 'Your username could not be updated due to an error.'));
    //     }
    //   }
    //   // Username is not unique
    //   else {
    //     $form->invalidateRequest(-1, errorObject('UsernameInUse', 'username', 'This username is already in use.'));
    //   }
    // }
    // // Cannot change username
    // else {
    //   $form->invalidateRequest(-1, errorObject('CannotChangeUsername', 'username', 'You cannot change your username at this time.'));
    // }
  }

  $form->buildResponse();
  $response->send();
?>