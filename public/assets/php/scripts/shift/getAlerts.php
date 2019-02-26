<?php
// Database Credentials
require_once('../dbConfig.php');

// Object Initialization
class responseObject {};
class codeCountObject {};

// Object Definition
$response = new responseObject;
  $response->response = new responseObject;
    $response->response->alerts = new responseObject;
$newCodes = new codeCountObject;
$expCodes = new codeCountObject;

// Immediate Object Value Definitions
$newCodes->bl2 = 0;
$newCodes->tps = 0;
$expCodes->bl2 = 0;
$expCodes->tps = 0;

// Variables
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
    if($row['RelDate'] == $today) {
      if($row['GameID'] == 1) {
        $newCodes->bl2++;
      }
      else if($row['GameID'] == 2) {
        $newCodes->tps++;
      }
    }
    if($row['ExpDate'] == $today) {
      if($row['GameID'] == 1) {
        $expCodes->bl2++;
      }
      else if($row['GameID'] == 2) {
        $expCodes->tps++;
      }
    }
  }
}

// Create Response Object
$response->response->alerts->new = $newCodes;
$response->response->alerts->expiring = $expCodes;

// Send Response
echo json_encode($response);
?>
