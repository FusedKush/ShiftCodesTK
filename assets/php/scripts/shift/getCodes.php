<?php
  // Database
  require_once('../dbConfig.php');
  // Response
  require_once('../response.php');
  $response = new Response;
  // Request parameters
  $defaultParams = [
    'gameID' => null,
    'order'  => 'default',
    'filter' => 'none',
    'limit'  => 10,
    'offset' => 0,
    'hash'   => false
  ];
  $params = (function () use ($response, $defaultParams) {
    $errorThrown = false;
    $arr = $_GET;

    foreach ($defaultParams as $key => $val) {
      if (!array_key_exists($key, $arr)) {
        if (!is_null($val)) {
          $response->addWarning([
            'name'           => 'Missing Parameter',
            'parameter'      => $key,
            'inheritedValue' => $val
          ]);
        }
        else {
          $response->set(3);
          $response->addError([
            'name'      => 'Missing Required Parameter',
            'parameter' => $key
          ]);
          $response->send();
        }

        $arr[$key] = $val;
      }
    }

    return $arr;
  })();

  // SQL
  $sqlStatement = (function () use ($params, $response) {
    $illegalVal = function ($parameter, $value) use ($response) {
      $response->fatalError(3, [
        'name'      => 'Illegal Parameter Value',
        'parameter' => $parameter,
        'value'     => $value
      ]);
    };
    $checkNumeric = function ($parameter) use ($params, $illegalVal) {
      $p = $params[$parameter];

      if (is_numeric($p)) {
        return $p;
      }
      else {
        $illegalVal($parameter, $p);
      }
    };
    $checkHash = function ($returnValue) use ($params, $illegalVal) {
      $parameter = 'hash';
      $p = $params[$parameter];

      if ($p != 'false') {
        if (strpos('shift_code_', $p) != -1) {
          return preg_replace('/\#parameter/', $p, $returnValue);
        }
        else {
          $illegalVal($parameter, $p);
          return '';
        }
      }
      else {
        return '';
      }
      if (strpos('shift_code_', $p) == -1)

      // Else
      $illegalVal($parameter, $p);
    };
    $vars = [
      'gameID'    => $checkNumeric('gameID'),
      'filter'    => (function () use ($params, $illegalVal) {
        $parameter = 'filter';
        $f = $params[$parameter];
        $str = '';
        $filters = [
          'new'      => 'rel_date = CURRENT_DATE()',
          'expiring' => 'exp_date = CURRENT_DATE()'
        ];

        // No filter
        if (strlen($f) == 0) {
          $str .= '(exp_date >= CURRENT_DATE() OR exp_date IS NULL)';
        }
        // Filters
        foreach ($filters as $name => $stmt) {
          if (strpos($f, $name) !== false) {
            if (strlen($str) > 0) {
              $str .= ' OR ';
            }

            $str .= $stmt;
          }
        }

        return $str;
      })(),
      'order'     => (function () use ($params, $illegalVal) {
        $parameter = 'order';
        $p = $params[$parameter];
        $options = [
          'default' =>
            'CASE exp_date
                WHEN CURRENT_DATE THEN
                    CASE rel_date
                        WHEN CURRENT_DATE THEN 1
                        ELSE 0
                    END
             END DESC,
             CASE rel_date
                WHEN CURRENT_DATE THEN 1
                ELSE 0
             END DESC,
             -(exp_date IS NULL) DESC,
             rel_date DESC,
             exp_date DESC',
          'new' =>
            'rel_date DESC',
          'old' =>
            'exp_date DESC'
          ];

        foreach ($options as $value => $string) {
          if ($p == $value) {
            return $string;
          }
        }

        // Else
        $illegalVal($parameter, $p);
      })(),
      'limit'     => $checkNumeric('limit'),
      'offset'    => $checkNumeric('offset'),
      'hashWhere' => $checkHash("id=#parameter OR"),
      'hashOrder' => $checkHash("CASE id
                                    WHEN #parameter
                                    THEN 0
                                    ELSE 1
                                 END,")
    ];

    return parseSQL(
      "SELECT
          *
       FROM
          shift_codes
       WHERE
          ${vars['hashWhere']}
          (game_id = ${vars['gameID']}
          AND (${vars['filter']}))
       ORDER BY
          ${vars['hashOrder']}
          ${vars['order']}
       LIMIT
          ${vars['limit']}
       OFFSET
          ${vars['offset']}");
  })();
  $sql = $con->prepare($sqlStatement);

  if ($sql) {
    $sql->execute();
    $sql->store_result();
    // Record code details
    (function () use ($sql, $response) {
      $meta = $sql->result_metadata();
      $fields = [];
      $values = [];

      while ($field = $meta->fetch_field()) {
        $fields[] = $values[] = $field->name;
      }
      $sql->bind_result(...$values);

      while ($sql->fetch()) {
        $arr = [];
        foreach ($values as $key => $val) {
          $arr[$key] = $val;
        }
        $response->addPayload(array_combine($fields, $arr));
      }
    })();
    $response->debug_sqlStatement = $sqlStatement;
    $response->send();
  }
  else {
    $response->fatalError(3, [
      'name'   => 'MySQL Error',
      'errors' => $con->error_list
    ]);
  }

?>
