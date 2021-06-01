<?php
  // require_once('../../reinitialize.php');
  require_once('shift_constants.php');

  use ShiftCodesTK\Validations;
  
  $response = new ResponseObject();
  $params = (function () use (&$response) {
    $properties = [
      'last_check' => new Validations\VariableEvaluator([
        'required' => true,
        'type'     => 'date'
      ]),
      'game_id' => new Validations\VariableEvaluator([
        'required'    => true,
        'type'        => 'string',
        'validations' => [
          'check_match' => array_merge(array_keys(SHIFT_GAMES), [ 'all' ])
        ]
      ])
    ];
    $param_evaluator = new Validations\GroupEvaluator($properties);
    $valid_params = $param_evaluator->check_variables($_GET);
    /** @var Validations\GroupEvaluationResult */
    $result = $param_evaluator->get_last_result(false);

    if ($result->warnings || $result->errors) {
      foreach ($result->warnings as $warning) {
        $response->setWarning($warning);
      }
      foreach ($result->errors as $error) {
        $response->setError($error);
      }
  
      $response->send();
      exit;
    }

    return $result->variables;
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