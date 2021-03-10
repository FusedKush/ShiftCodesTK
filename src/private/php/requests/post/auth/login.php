<?php
  use ShiftCodesTK\Users\CurrentUser;

  require_once(\ShiftCodesTK\PRIVATE_PATHS['forms'] . '/auth/login.php');

  /** The form's response object */
  $response = &$form_authLogin->findReferencedProperty('formSubmit->response');

  // Valid Form Submission
  if ($form_authLogin->validateForm()) {
    /** Provided Parameters */
    $formParams = $form_authLogin->findReferencedProperty('formSubmit->parameters');
    $response->setPayload($formParams, '_formData');
    $loginResult = CurrentUser::login_with_credentials($formParams['email'], $formParams['password'], true);

    if ($loginResult) {
      if ($formParams['remember_me'] == 'on') {
        CurrentUser::get_current_user()->create_persistent_token();
      }
    }
    else {
      $error_code = CurrentUser::get_last_login_error()->getCode();
      
      $loginError = function ($error) use (&$form_authLogin, &$response) {
        $defaultParams = array_fill_keys(['error', 'param', 'message', 'provided', 'inherited'], null);
  
        $form_authLogin->updateProperty('formSubmit->success', false);
        $response->set(-1);
        $response->setError(errorObject(...array_values(array_replace_recursive($defaultParams, $error))));
      };

      if ($error_code == 1) {
        $loginError([
          'error' => 'invalidCredentials',
          'message' => 'Your Email Address or Password is incorrect. Please try again.'
        ]);
      }
      else if ($error_code == 2) {
        $loginError([
          'error' => 'throttledLogin',
          'message' => 'We could not log you in at this time. Please wait a few minutes and try again.'
        ]);
      }
      else {
        $loginError([
          'error'   => 'loginFailure',
          'message' => 'An error occurred while trying to you log in. Please wait a few seconds and try again.'
        ]);
      }
    }
  }

  $form_authLogin->buildResponse();
  $response->send();
?>