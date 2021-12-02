<?php
  // Database
  require_once('../dbConfig.php');
  // Response
  require_once('../response.php');

  $response = new Response;
  
  // SQL
  $sqlStatement = (function () use (&$response) {
    // Request parameters
    $params = (function () use (&$response) {
      $defaultParams = [
        'gameID' => null,
        'order'  => 'default',
        'filter' => 'none',
        'limit'  => 10,
        'offset' => 0,
        'hash'   => false
      ];
      $raw_params = $_GET;
  
      foreach ($defaultParams as $key => $val) {
        if (!array_key_exists($key, $raw_params)) {
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
  
          $raw_params[$key] = $val;
        }
  
        $raw_params[$key] = htmlspecialchars($raw_params[$key]);
      }
  
      return $raw_params;
    })();

    $illegalVal = function ($parameter, $value) use (&$response) {
      $response->fatalError(3, [
        'name'      => 'Illegal Parameter Value',
        'parameter' => $parameter,
        'value'     => $value
      ]);
    };
    $checkNumeric = function ($parameter) use ($params, &$illegalVal) {
      $p = $params[$parameter];

      if (is_numeric($p)) {
        return $p;
      }
      else {
        $illegalVal($parameter, $p);
      }
    };
    $checkHash = function ($returnValue) use ($params, &$illegalVal) {
      $parameter = 'hash';
      $p = $params[$parameter];

      if ($p != 'false') {
        return preg_replace('/\#parameter/', $p, $returnValue);
      }
      else {
        return '';
      }

      // Else
      $illegalVal($parameter, $p);
    };
    
    $vars = [
      'gameID'    => (function () use ($params, &$illegalVal) {
        $parameter = 'gameID';
        $gameId = $params[$parameter];
        $ids = ['bl1', 'bl2', 'bl3', 'tps'];

        foreach ($ids as $id) {
          if ($gameId == $id) {
            return $gameId;
          }
        }

        // Else
        $illegalVal($parameter, $gameId);
      })(),
      'filter'    => (function () use ($params) {
        $parameter = 'filter';
        $filter = $params[$parameter];
        $str = '';
        $filters = [
          'new'      => 'Date(rel_date) = CURRENT_DATE()',
          'expiring' => 'Date(exp_date) = CURRENT_DATE() AND TIME(exp_date) >= CURRENT_TIME()'
        ];

        // No filter
        if (strlen($filter) == 0) {
          $str .= '(exp_date >= CURRENT_TIMESTAMP() OR exp_date IS NULL)';
        }
        // Filters
        foreach ($filters as $name => $stmt) {
          if (strpos($filter, $name) !== false) {
            if (strlen($str) > 0) {
              $str .= ' OR ';
            }

            $str .= $stmt;
          }
        }

        return $str;
      })(),
      'order'     => (function () use ($params, &$illegalVal) {
        $parameter = 'order';
        $order = $params[$parameter];
        $options = [
          'default' =>
            'CASE Date(exp_date)
                WHEN CURRENT_DATE THEN
                    CASE Date(rel_date)
                        WHEN CURRENT_DATE THEN 2
                        ELSE 1
                    END
                ELSE 0
             END DESC,
             CASE Date(rel_date)
                WHEN CURRENT_DATE THEN 1
                ELSE 0
             END DESC,
             -(exp_date IS NULL) DESC,
             rel_date DESC,
             exp_date DESC',
          'new' =>
            'rel_date DESC',
          'old' =>
            'rel_date ASC'
        ];

        foreach ($options as $value => $string) {
          if ($order == $value) {
            return $string;
          }
        }

        // Else
        $illegalVal($parameter, $order);
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

      return "SELECT
          *
       FROM
          shift_codes
       WHERE
          {$vars['hashWhere']}
          (game_id = '{$vars['gameID']}'
          AND {$vars['filter']})
       ORDER BY
          {$vars['hashOrder']}
          {$vars['order']}
       LIMIT
          {$vars['limit']}
       OFFSET
          {$vars['offset']}";
  })();

  $sql = $con->prepare($sqlStatement);

  if ($sql) {
    $sql->execute();
    $sql->store_result();

    // Record code details
    (function () use (&$sql, &$response) {
      $meta = $sql->result_metadata();
      $fields = [];
      $values = [];

      while ($field = $meta->fetch_field()) {
        $fields[] = $values[] = $field->name;
      }
      $sql->bind_result(...$values);

      while ($sql->fetch()) {
        $tz = '';
        $arr = [];

        foreach ($values as $key => $val) {
          $field = $fields[$key];

          if ($field == 'timezone') {
            $tz = $val;
          }
          if (!strpos($field, 'date') && !strpos($field, 'time') || !$val) {
            $v = $val;
          }
          else {
            $date = strpos($val, '00:00:00')
              ? new DateTime($val)
              : new DateTime($val, new DateTimeZone(timezone_name_from_abbr($tz)));

            $v = $date->format('c');
          }
          
          $arr[$key] = $v;
        }
        $response->addPayload(array_combine($fields, $arr));
      }
    })();
    $response->send();
  }
  else {
    $response->fatalError(3, [
      'name'   => 'MySQL Error'
    ]);
  }

?>
