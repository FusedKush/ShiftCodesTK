<?php
  /**
   * Responsible for invoking *Request Scripts*
   */

  $__script_type = 2;
  require(__DIR__ . "/../initialize.php");

  use const ShiftCodesTK\REQUEST_TOKEN;

  $__request = (function () {
    $request = $_GET['__request'] ?? null;

    $invalid_request = function ($status_code, $status_message) {
      $response = new \ResponseObject();
  
      $response->set($status_code);
      $response->status_message = $status_message;
      $response->send();
      exit;
    };

    if (!isset($request)) {
      $invalid_request(404, 'No resource was specified');
    }
    else if ((strpos($request, '/get/') !== false && !is_method('GET')) || (strpos($request, '/post/') !== false && !is_method('POST'))) {
      $invalid_request(-1, 'An invalid Request Method was used');
    }
    else if (REQUEST_TOKEN !== $_SESSION['token'] && $request !== 'get/token') {
      $invalid_request(401, 'Missing or Invalid Request Token');
    }
    
    $request = \ShiftCodesTK\PRIVATE_PATHS['requests'] . "/{$request}";
    $request = strpos($request, '.php') === false ? "$request.php" : $request;

    if (!file_exists($request)) {
      $invalid_request(404, 'The specified resource could not be found');
    }

    return $request;
  })();

  include($__request);
?>