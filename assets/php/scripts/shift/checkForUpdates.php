<?php
  // Database
  require_once('../dbConfig.php');
  // Response
  require_once('../response.php');
  $response = new Response;
  $timestamps = [
    'creation',
    'update'
  ];

  foreach ($timestamps as $ts) {
    $col = "${ts}_time";
    $sql = $con->prepare("SELECT
                            timezone,
                            ${col},
                            game_id
                          FROM
                            shift_codes
                          WHERE
                            ${ts}_time=(SELECT
                                          MAX(${col})
                                        FROM
                                          shift_codes)");
    if ($sql) {
      $data = array_fill_keys(['timestamp', 'game_id'], '');
      $tz = '';
      $sql->execute();
      $sql->store_result();
      $sql->bind_result($tz, $data['timestamp'], $data['game_id']);
      $sql->fetch();

      // Format Timestamp
      (function () use(&$data, &$tz) {
        $val = &$data['timestamp'];
        $date = new DateTime($val, new DateTimeZone(timezone_name_from_abbr($tz)));
        $val = $date->format('c');
      })();

      $response->addPayload($data, $col);
    }
    else {
      $response->fatalError(3, [
        'name'   => 'MySQL Error',
        'errors' => $con->error_list
      ]);
    }
  }
  $response->send();
?>
