<?php
// Database Credentials
require_once('dbConfig.php');

class Response {
  public $response = array();
}
class Update {
  public $version;
  public $date;
  public $type;
  public $notes;
}

$response = new Response;
$info = array('version', 'date', 'type', 'notes');

// SQL Preparation & Execution
$sql =
  'SELECT
      Version,
      Date,
      Type,
      Notes
   FROM
      Updates
   ORDER BY
      Version Desc'
;
$sqlResult = mysqli_query($con, $sql);

// Add Updates to Response Object
while($row = mysqli_fetch_array($sqlResult)) {
  $update = new Update;

  foreach($info as $key => $value) {
    $update->$value = $row[ucfirst($value)];
  }

  $response->response[count($response->response)] = $update;
}

// Return Update Notes
echo json_encode($response);
?>
