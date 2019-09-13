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
  public $game3 = 'bl1';
  public $game4 = 'bl3';
}
class GameCounters {
  public $bl1 = 0;
  public $bl2 = 0;
  public $tps = 0;
  public $bl3 = 0;
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

function checkCodes ($type, $badge) {
  global $con, $gameIDs, $alertCount;

  // SQL Preparation & Execution
  $sql =
    'SELECT
        game_id
     FROM
        shift_codes
     WHERE'
        . (' ') . $type . ('_date = CURRENT_DATE()')
  ;
  $sqlResult = mysqli_query($con, $sql);

  if(mysqli_num_rows($sqlResult) > 0) {
    while($row = mysqli_fetch_array($sqlResult)) {
      $gameIDString = 'game' . $row['game_id'];
      $gameID = $gameIDs->$gameIDString;

      $alertCount->$badge->$gameID++;
    }
  }
}

// Check for New SHiFT Codes
checkCodes('rel', 'new');
// Check for Expiring SHiFT Codes
checkCodes('exp', 'expiring');

// Add alert count to Response Object
$response->response->alerts = $alertCount;

// Return alert count
echo json_encode($response);
?>
