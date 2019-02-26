<?php
// Database Credentials
require_once('../dbConfig.php');

class Response {
  public $response;

  public function __construct() {
    $this->response = new stdClass();
    $this->response->alerts = new stdClass();
  }
}
class GameIDs {
  public $game1 = 'bl2';
  public $game2 = 'tps';
}
class GameCounters {
  public $bl2 = 0;
  public $tps = 0;
}
class AlertCount {
  public $new;
  public $expiring;

  public function __construct() {
    $this->new = new GameCounters();
    $this->expiring = new GameCounters();
  }
}

$response = new Response();
$gameIDs = new GameIDs;
$alertCount = new AlertCount();

// SQL Preparation & Execution
$sql =
  'SELECT
      GameID,
      RelDate,
      ExpDate
   FROM
      ShiftCodes
   WHERE
      ExpDate >= CURRENT_DATE()
      OR ExpDate IS NULL'
;
$sqlResult = mysqli_query($con, $sql);
$today = date('Y-m-d');

// Count New & Expiring SHiFT Codes
if(mysqli_num_rows($sqlResult) > 0) {
  while($row = mysqli_fetch_array($sqlResult)) {
    $gameID = 'game' . $row['GameID'];

    if ($row['RelDate'] == $today) { $alertCount->new->$gameID++; }
    if ($row['ExpDate'] == $today) { $alertCount->expiring->$gameID++; }
  }
}

// Add alert count to Response Object
$response->response->alerts = $alertCount;

// Return alert count
echo json_encode($response);
?>
