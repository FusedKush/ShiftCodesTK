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
  $details = $_GET['getDetails'] == 'true';

  foreach ($timestamps as $ts) {
    $col = "${ts}_time";
    $select = (function () use ($details, $col) {
      $str = "timezone, ${col}";

      if ($details) {
        $str .= ", id, game_id";
      }

      return $str;
    })();
    $sql = $con->prepare("SELECT
                            ${select}
                          FROM
                            shift_codes
                          WHERE
                            ${ts}_time=(SELECT
                                          MAX(${col})
                                        FROM
                                          shift_codes)");
    if ($sql) {
      $keys = (function () use ($details) {
        $k = ['timestamp'];

        if ($details) {
          $k[] = 'id';
          $k[] = 'game_id';
        }

        return $k;
      })();
      $data = array_fill_keys($keys, '');
      $tz = '';
      $sql->execute();
      $sql->store_result();

      if ($details) { $sql->bind_result($tz, $data['timestamp'], $data['id'], $data['game_id']); }
      else          { $sql->bind_result($tz, $data['timestamp']); }

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
