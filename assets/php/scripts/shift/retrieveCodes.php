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
  public $notes;
  public $platformsPC;
  public $codePC;
  public $platformsXbox;
  public $codeXbox;
  public $platformsPS;
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
        id,
        rel_date,
        exp_date,
        reward,
        source,
        notes,
        platforms_pc,
        code_pc,
        platforms_xbox,
        code_xbox,
        platforms_ps,
        code_ps
     FROM
        shift_codes
     WHERE
        id = ?'
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
    $code->notes,
    $code->platformsPC,
    $code->codePC,
    $code->platformsXbox,
    $code->codeXbox,
    $code->platformsPS,
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
        id
     FROM
        shift_codes
     WHERE
        game_id = ?
        AND exp_date >= CURRENT_DATE()
        OR exp_date IS NULL
     ORDER BY
        CASE exp_date
            WHEN CURRENT_DATE
            THEN 0
            ELSE 1
        END,
        CASE rel_date
            WHEN CURRENT_DATE
            THEN 0
            ELSE 1
        END,
        -(exp_date IS NULL) DESC,
        rel_date DESC,
        exp_date DESC'
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
