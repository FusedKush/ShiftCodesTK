<?php
  // Database
  require_once('../dbConfig.php');
  // Response
  require_once('../response.php');
  $response = new Response;

  $counts = [];
  $stmts = [
    'total'    => 'exp_date >= CURRENT_TIMESTAMP() OR exp_date IS NULL',
    'new'      => 'Date(rel_date) = CURRENT_DATE()',
    'expiring' => 'Date(exp_date) = CURRENT_DATE() AND Time(exp_date) >= CURRENT_TIME()'
  ];

  foreach ($stmts as $name => $stmt) {
    $counts[$name] = array_fill_keys(['bl1', 'bl2', 'bl3', 'tps'], 0);
    $count = &$counts[$name];
    $gameID = 0;
    $sql = $con->prepare(
      "SELECT
          game_id
       FROM
          shift_codes
       WHERE
          (${stmt})"
    );
    $sql->execute();
    $sql->store_result();
    $sql->bind_result($gameID);

    while($sql->fetch()) {
      $count[$gameID]++;
    }
  }

  foreach ($counts as $name => &$count) {
    $response->addPayload($count, $name);
  }

  $response->send();
?>
