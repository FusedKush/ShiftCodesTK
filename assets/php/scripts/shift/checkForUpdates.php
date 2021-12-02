<?php
  // Database
  require_once('../dbConfig.php');
  // Response
  require_once('../response.php');

  $response = new Response;
  $details = $_GET['getDetails'] == 'true';

  foreach (['creation', 'update'] as $timestamp) {
    $col = "{$timestamp}_time";
    $select = (function () use ($details, $col) {
      $str = "timezone, {$col}";

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
        {$timestamp}_time=(
          SELECT
            MAX({$col})
          FROM
            shift_codes
        )
    ");

    if ($sql) {
      $keys = (function () use ($details) {
        $keys = ['timestamp'];

        if ($details) {
          $keys[] = 'id';
          $keys[] = 'game_id';
        }

        return $keys;
      })();
      $data = array_fill_keys($keys, '');
      $tz = '';
      $sql->execute();
      $sql->store_result();

      $sql->bind_result($tz, ...$data); 
      $sql->fetch();

      // Format Timestamp
      (function () use(&$data, $tz) {
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
