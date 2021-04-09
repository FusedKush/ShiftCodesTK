<?php
  use ShiftCodesTK\Users,
      ShiftCodesTK\Strings;

  include('local/profile-card.php');

  $userID = $_GET['user_id'] ?? false;
  $response = new ResponseObject();

  if ($userID) {
    try {
      $userData = new Users\UserRecord($userID);
      $profileCardData = $userData->get_profile_card_data();

      if ($profileCardData) {
        $response->payload = $profileCardData;
        $response->send();
        exit;
      }
      else {
        $response->setError(errorObject('ProfileCardDataFetchError', null, 'The Profile Card Data could not be retrieved due to an error.'));
      }
    }
    catch (\Throwable $exception) {
      $response->setError(errorObject('InvalidUserID', 'user_id', 'An invalid User ID was provided.', $userID));
    }
  }

  $response->set(-1);
  $response->send();
?>