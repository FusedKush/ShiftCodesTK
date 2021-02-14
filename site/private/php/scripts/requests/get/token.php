<?php
  // require_once('../../../initialize.php');

  $response = new ResponseObject();
  $oldToken = TOKEN_HEADER;
  $currentToken = $_SESSION['token'];
  $responseToken = '';

  if ($oldToken == $currentToken) {
    $responseToken = 'unchanged';
  }
  else {
    $responseToken = $currentToken;
  }

  $response->setPayload($responseToken, 'token');
  $response->send();
?>