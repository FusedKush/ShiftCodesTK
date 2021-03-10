<?php
  use ShiftCodesTK\Users\CurrentUser;

  include(\ShiftCodesTK\PRIVATE_PATHS['forms'] . '/auth/logout.php');

  $response = &$form_authLogout->findReferencedProperty('formSubmit->response');

  (function () use (&$form_authLogout, &$response) {
    $formValidation = $form_authLogout->validateForm();

    if ($formValidation) {
      CurrentUser::get_current_user()->logout(true);
     
      if (!CurrentUser::is_logged_in()) {
        return true;
      }

      // Logout Error
      $form_authLogout->updateProperty('formSubmit->success', false);
      $response->set(-3);
      $response->setError(errorObject('logoutFailed', null, 'An error occurred while trying to log you out.'));
    }
  })();

  $form_authLogout->buildResponse();
  $response->send();
?>