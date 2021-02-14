<?php
  // require_once('../../reinitialize.php');
  require_once(PRIVATE_PATHS['script_includes'] . 'shift_constants.php');
  
  $response = new ResponseObject();
  $params = (function () use (&$response) {
    $properties = [
      'last_check' => new ValidationProperties([
        'required' => true,
        'type'     => 'date'
      ]),
      'game_id' => new ValidationProperties([
        'required'    => true,
        'type'        => 'string',
        'validations' => [
          'match' => array_merge(array_keys(SHIFT_GAMES), [ 'all' ])
        ]
      ])
    ];
    $results = check_parameters($_GET, $properties);

    if ($results['warnings'] || $results['errors']) {
      foreach ($results['warnings'] as $warning) {
        $response->setWarning($warning);
      }
      foreach ($results['errors'] as $error) {
        $response->setWarning($error);
      }
  
      $response->send();
      exit;
    }

    return $results['parameters'];
  })();

  (function () use (&$_mysqli, &$params) {
    $date = new DateTime($params['last_check']);

    $params['last_check'] = $date->format($_mysqli->dateFormats['dateTime']);
  })();

  $filter = SHIFT_DATES['active'];
  $query = "SELECT COUNT(sc.id) as count
            FROM shift_codes
              AS sc
            LEFT JOIN shift_code_data
              AS scd
              ON sc.code_id = scd.code_id
            WHERE sc.update_time > '{$params['last_check']}'
              AND sc.code_state = 'active'
              AND scd.game_id = '{$params['game_id']}'
              AND ({$filter})";
  $result = $_mysqli->query($query, [ 'collapseAll' => true ]);

  $response->setPayload((int) $result, 'count');
  $response->send();
?>