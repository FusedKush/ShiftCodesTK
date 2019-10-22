<?php
  // Database Credentials
  require_once('dbConfig.php');

  $serverVersion = $svQueryString = '';
  $sql = $con->prepare("SELECT MAX(version) FROM updates");

  $sql->execute();
  $sql->store_result();
  $sql->bind_result($serverVersion);
  $sql->fetch();
  $sql->close();
  $svQueryString = '?v=' . $serverVersion; // Query String
?>
