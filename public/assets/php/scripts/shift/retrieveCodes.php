<?php
// Database Credentials
require_once('../dbConfig.php');

// Object Initialization
class responseObject {};
class codeObject {};

// Object Definition
$response = new responseObject;
  $response->response = array();

// Retrieve code details
function getCode ($codeID, $i) {
  global $con, $response;
  $code = new codeObject;

  // SQL Setup
  $sql = $con->prepare(
    'SELECT
        ID,
        RelDate,
        ExpDate,
        Reward,
        Source,
        CodePC,
        CodeXbox,
        CodePS
     FROM
        ShiftCodes
     WHERE
        ID = ?'
  );
  $sql->bind_param('i', $codeID);
  // SQL Execution
  $sql->execute();
  $sql->store_result();
  $sql->bind_result(
    $code->codeID,
    $code->relDate,
    $code->expDate,
    $code->reward,
    $code->source,
    $code->codePC,
    $code->codeXbox,
    $code->codePS
  );
  $sql->fetch();

  $response->response[$i] = $code;
}

// Get Code IDs and fetch code details
(function () {
  global $con;
  $codeID;
  $i = 0;

  // SQL Setup
  $sql = $con->prepare(
    'SELECT
        ID
     FROM
        ShiftCodes
     WHERE
        GameID = ?
        AND ExpDate >= CURRENT_DATE()
        OR ExpDate IS NULL
     ORDER BY
        -RelDate ASC'
  );
  $sql->bind_param('i', $gameID);
  $gameID = $_GET['gameID'];
  // SQL Execution
  $sql->execute();
  $sql->store_result();
  $sql->bind_result($codeID);

  // Loop through results and fetch code details
  while ($sql->fetch()) {
    getCode($codeID, $i);
    $i++;
  }
})();

// Submit Response
echo json_encode($response);
