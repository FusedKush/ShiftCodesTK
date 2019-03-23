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
      version,
      date,
      type,
      notes
   FROM
      updates
   ORDER BY
      version Desc'
;
$sqlResult = mysqli_query($con, $sql);

// Add Updates to Response Object
while($row = mysqli_fetch_array($sqlResult)) {
  $update = new Update;

  foreach($info as $key => $value) {
    $update->$value = $row[$value];
  }

  $response->response[count($response->response)] = $update;
}

// Return Update Notes
echo json_encode($response);
?>
