<?php
  // Database
  require_once('dbConfig.php');
  // Response
  require_once('response.php');

  $response = new Response;
  $params = (function () use (&$response) {
    $defaultVals = [
      'limit'    => 10,
      'offset'   => 0,
      'hash'     => false,
      'firstRun' => false
    ];
    $arr = [];

    foreach ($defaultVals as $key => $defaultVal) {
      if (array_key_exists($key, $_GET)) {
        $val = $_GET[$key];
        $success = function () use (&$arr, $key, $val) {
          $arr[$key] = htmlspecialchars($val);
        };
        $fail = function () use (&$arr, &$response, $defaultVals, $key, $val) {
          $response->addWarning([
            'name'      => 'Illegal Parameter Value',
            'parameter' => $key,
            'value'     => $val
          ]);
          $arr[$key] = $defaultVals[$key];
        };

        if ($key == 'firstRun') {
          if ($val == 'true' || $val == 'false') {
            $success();
          }
          else {
            $fail();
          }
        }
        else if ($key == 'limit' || $key == 'offset') {
          if (is_numeric($val)) {
            $success();
          }
          else {
            $fail();
          }
        }
        else if ($key == 'hash') {
          if ($val == 'false') {
            $arr[$key] = false;
          }
          else {
            $success();
          }
        }
      }
      else {
        $response->addWarning([
          'name'           => 'Missing Parameter',
          'parameter'      => $key,
          'inheritedValue' => $defaultVal
        ]);
        $arr[$key] = $defaultVal;
      }
    }

    return $arr;
  })();
  $cols = $data = ['version', 'date', 'type', 'notes'];
  $hash = $params['hash'];
  $args = [
    'select' => implode(', ', $cols),
    'where'  => (function() use ($hash) {

                  if ($hash)  { return "WHERE version = '{$hash}' OR 1"; }
                  else        { return ''; }
                })(),
    'order' => (function () use ($hash) {
                  if ($hash)  { return "CASE version WHEN '{$hash}' THEN 0 ELSE 1 END,"; }
                  else        { return ''; }
                })()
  ];

  // Get Changelogs
  $sql = $con->prepare("
    SELECT
      {$args['select']}
    FROM
        updates
    {$args['where']}
    ORDER BY
      {$args['order']}
      version DESC
    LIMIT
      {$params['limit']}
    OFFSET
      {$params['offset']}
  ");

  if (!$con->error) {
    $sql->execute();
    $sql->store_result();
    $sql->bind_result(...$data);
    $response->addPayload([], 'changelogs');

    while($sql->fetch()) {
      $arr = [];

      foreach ($data as $key => $val) {
        $v = $cols[$key] == 'date'
          ? (new DateTime($val))->format('c')
          : $val;

        $arr[$key] = $v;
      }
      $response->payload['changelogs'][] = array_combine($cols, $arr);
    }

    if ($params['firstRun'] == 'true') {
      $sql->prepare("SELECT
                        version
                      FROM
                        updates
                      ORDER BY
                        version DESC");

      if (!$con->error) {
        $ver = '';
        $sql->execute();
        $sql->store_result();
        $sql->bind_result($ver);
        $response->addPayload([], 'versions');

        while($sql->fetch()) {
          $response->payload['versions'][] = $ver;
        }
      }
      else {
        $response->fatalError(3, [
          'name' => 'MySQL Error',
          'Error' => $con->error
        ]);
      }
    }

    $response->send();
  }
  else {
    $response->fatalError(3, [
      'name' => 'MySQL Error',
      'Error' => $con->error
    ]);
  }
?>
