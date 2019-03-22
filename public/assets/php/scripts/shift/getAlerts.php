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
      game_id,
      rel_date,
      exp_date
   FROM
      shift_codes
   WHERE
      exp_date >= CURRENT_DATE()
      OR exp_date IS NULL'
;
$sqlResult = mysqli_query($con, $sql);
$today = date('Y-m-d');

// Count New & Expiring SHiFT Codes
if(mysqli_num_rows($sqlResult) > 0) {
  while($row = mysqli_fetch_array($sqlResult)) {
    $gameIDString = 'game' . $row['game_id'];
    $gameID = $gameIDs->$gameIDString;

    if ($row['rel_date'] == $today) { $alertCount->new->$gameID++; }
    if ($row['exp_date'] == $today) { $alertCount->expiring->$gameID++; }
  }
}
// Add alert count to Response Object
$response->response->alerts = $alertCount;

// Return alert count
echo json_encode($response);
?>
