<?php
  require_once(SCRIPTS_INCLUDES_PATH . 'shift_stats.php');

  $response = new ResponseObject();

  foreach (SHIFT_STATS as $type => $counts) {
    $response->setPayload($counts,  $type);
  }

  $response->send();
?>