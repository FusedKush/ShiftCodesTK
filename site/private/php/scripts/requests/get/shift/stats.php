<?php
  require_once(PRIVATE_PATHS['script_includes'] . 'shift_stats.php');

  $response = new ResponseObject();

  foreach (SHIFT_STATS as $type => $counts) {
    $response->setPayload($counts,  $type);
  }

  $response->send();
?>