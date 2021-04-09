<?php
  /**
   * Responsible for invoking *Backend Request Endpoints*
   */

  $__script_type = 2;
  require(__DIR__ . "/../initialize.php");

  use const ShiftCodesTK\REQUEST_TOKEN;

  $__endpoint = (function () {
    $endpoint = $_GET['__endpoint'] ?? null;

    $invalid_endpoint = function ($status_code, $status_message) {
      $response = new \ResponseObject();
  
      $response->set($status_code);
      $response->status_message = $status_message;
      $response->send();
      exit;
    };

    if (!isset($endpoint)) {
      $invalid_endpoint(404, 'No endpoint was specified.');
    }
    else if ((strpos($endpoint, '/get/') !== false && !is_method('GET')) || (strpos($endpoint, '/post/') !== false && !is_method('POST'))) {
      $invalid_endpoint(-1, 'An invalid Request Method was used.');
    }
    else if (REQUEST_TOKEN !== $_SESSION['token'] && $endpoint !== 'get/token') {
      $invalid_endpoint(401, 'Missing or Invalid Request Token.');
    }
    
    $endpoint = \ShiftCodesTK\Paths\PHP_PATHS['endpoints'] . "/{$endpoint}";
    $endpoint = strpos($endpoint, '.php') === false ? "$endpoint.php" : $endpoint;

    if (!file_exists($endpoint)) {
      $invalid_endpoint(404, 'The specified endpoint does not exist.');
    }

    return $endpoint;
  })();

  include($__endpoint);
?>