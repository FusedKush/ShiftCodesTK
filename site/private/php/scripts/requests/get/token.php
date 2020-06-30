<?php
  // require_once('../../../initialize.php');

  $response = new ResponseObject();
  $oldToken = $_GET['_token'];
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