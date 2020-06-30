<?php
  include(FORMS_PATH . 'auth/logout.php');

  $response = &$form_authLogout->findReferencedProperty('formSubmit->response');

  (function () use (&$form_authLogout, &$response) {
    $formValidation = $form_authLogout->validateForm();

    if ($formValidation) {
      if (auth_isLoggedIn() && $form_authLogout->findReferencedProperty('formSubmit->parameters->user_id') == auth_user_id()) {
        auth_logout(true);
  
        if (!auth_isLoggedIn()) {
          return true;
        }
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