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
        $keys = [ 'timezone', 'timestamp' ];

        if ($details) {
          $keys[] = 'id';
          $keys[] = 'game_id';
        }

        return $keys;
      })();
      $values = array_fill(0, count($keys), null);
      $sql->execute();
      $sql->store_result();

      $sql->bind_result(...$values); 
      $sql->fetch();

      $data = array_combine($keys, $values);

      // Format Timestamp
      (function () use(&$data) {
        $tz = $data['timezone'];
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
