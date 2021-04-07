<?php
  /**
   * Responsible for invoking *Request Scripts*
   */

  use const ShiftCodesTK\REQUEST_TOKEN;

  (function () {
    $response = new \ResponseObject();
    // Requested file 
    $request = (function () {
      $request = $_GET['_request'] ?? false;
      
      if ($request) {
        $request = \ShiftCodesTK\PRIVATE_PATHS['requests'] . "/{$request}";
        $request = strpos($request, '.php') === false ? "$request.php" : $request;
      }
  
      return $request;
    })();
  
    // Nonexistent File
    if (!$request) {
      $response->fatalError(404, 'No resource was specified');
      exit;
    }
    else if (!file_exists($request)) {
      $response->fatalError(404, 'The specified resource could not be found');
      exit;
    }
    // Invalid request type
    else if (strpos($request, '/get/') !== false && !is_method('GET') || strpos($request, '/post/') !== false && !is_method('POST')) {
      $response->fatalError(-1, 'An incorrect request method was used');
      exit;
    } 
    // Missing Token
    else if (!REQUEST_TOKEN || REQUEST_TOKEN != $_SESSION['token'] && $_GET['_request'] != 'get/token') {
      $response->fatalError(401, 'Missing or Invalid Request Token');
      exit;
    }
  
    include($request);
  })();
?>