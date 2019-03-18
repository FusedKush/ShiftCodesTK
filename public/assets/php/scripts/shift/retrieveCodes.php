<?php
// Database Credentials
require_once('../dbConfig.php');

class Response {
  public $response = array();
};
class Code {
  public $codeID;
  public $relDate;
  public $expDate;
  public $reward;
  public $source;
  public $codePC;
  public $codeXbox;
  public $codePS;
}
class codeObject {};

$response = new Response;

// Retrieve code details
function getCode ($codeID, $i) {
  global $con, $response;
  $code = new Code;

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
  $currentCodeID;
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
        -RelDate ASC,
        ExpDate DESC'
  );
  $sql->bind_param('i', $gameID);
  $gameID = $_GET['gameID'];
  // SQL Execution
  $sql->execute();
  $sql->store_result();
  $sql->bind_result($currentCodeID);

  // Loop through results and fetch code details
  while ($sql->fetch()) {
    getCode($currentCodeID, $i);
    $i++;
  }
})();

// Return all SHiFT Codes
echo json_encode($response);
