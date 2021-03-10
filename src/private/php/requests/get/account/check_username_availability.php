<?php
  $username = ShiftCodesTKDatabase::escape_string($_GET['username']);
  $queryStr = "SELECT 
               COUNT(*) 
                  AS 'count'
               FROM auth_users
               WHERE username = '{$username}'";
  $query = new ShiftCodesTKDatabaseQuery($queryStr, [ 
    'collapse_all'           => true, 
    'create_response_object' => true,
    'log_response_issues'    => true,
    'format_parameters'      => [
      'count'                   => [
        'change_type'              => 'bool'
      ]
    ]
  ]);

  $query->query();
  $response = $query->get_response();
  $response->payload = $response->payload === false;
  $response->send();
?>