<?php
  use ShiftCodesTK\Users\CurrentUser;

  include_once(PRIVATE_PATHS['forms'] . 'account/update-profile.php');

  $form = &$form_statPrivacy;
  $response = &$form->findReferencedProperty('formSubmit->response');
  $success = &$form->findReferencedProperty('formSubmit->success');

  $formIsValid = $form->validateForm();
  
  if ($formIsValid) {
    if (CurrentUser::is_logged_in()) {
      $currentUser = CurrentUser::get_current_user();
      $params = $form->findReferencedProperty('formSubmit->parameterList');
      $result = (function () use (&$currentUser, $params) {
        try {
          return $currentUser->update_profile_stats_privacy($params['privacy_preference']);
        }
        catch (\Throwable $exception) {
          error_log($exception->getMessage());
          return false;
        }
      })();
      // $preference = ShiftCodesTKDatabase::escape_string($params['privacy_preference']);
      // $userID = auth_user_id();
      // $query = new ShiftCodesTKDatabaseQuery("
      //   UPDATE auth_user_records
      //     SET `profile_stats_preference` = '{$preference}'
      //     WHERE `user_id` = '{$userID}'
      //     LIMIT 1", 
      // [
      //   'collapse_all' => true
      // ]);
      // $queryResult = $query->query();
      // $response->setPayload($query->query, '_query');
      
      if (!$result) {
        $form->invalidateRequest(-3, errorObject('PreferenceUpdateError', 'privacy_preference', 'An error occurred while saving your preferences.'));
        error_log("An error ocurred while saving Profile Stats Privacy Preferences for user \"{$currentUser}\".");
      }
    }
    // Not logged in
    else {
      $form->invalidateRequest(-1, errorObject('NotLoggedIn', 'privacy_preference', 'You must be logged in to save your preferences.'));
    }
  }

  $form->buildResponse();
  $response->send();
?>