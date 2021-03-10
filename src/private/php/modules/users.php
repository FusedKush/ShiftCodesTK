<?php
  /** The namespace for ShiftCodesTK User Authentication */
  namespace ShiftCodesTK\Users;

  if (!CurrentUser::is_logged_in()) {
    // Check Persistent Login Cookie
    if ($token = CurrentUser::check_persistent_token_cookie()) {
      CurrentUser::login_with_token($token->get_token());
    }
    // Redeemed SHiFT Codes cookie
    if (getCookie('redeemed')) {
      $redeemedCodes = json_decode(getCookie('redeemed'), true);
  
      if ($redeemedCodes['isAccountBound']) {
        redemption_update_cookie();
      }
    }
  }
  else {
    $currentUser = CurrentUser::get_current_user();

    if (!$currentUser->check_user_auth()) {
      $currentUser->logout();
    }
  }
?>