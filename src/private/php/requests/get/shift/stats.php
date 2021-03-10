<?php
  require_once(\ShiftCodesTK\PRIVATE_PATHS['php_includes'] . '/shift_stats.php');

  $response = new ResponseObject();

  foreach (SHIFT_STATS as $type => $counts) {
    $response->setPayload($counts,  $type);
  }

  $response->send();
?>