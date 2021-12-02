<?php
  // Database Credentials
  require_once('dbConfig.php');

  /** @var string The global website version. */
  $serverVersion = '';
  $sql = $con->prepare("SELECT MAX(version) FROM updates");

  $sql->execute();
  $sql->store_result();
  $sql->bind_result($serverVersion);
  $sql->fetch();
  $sql->close();

  /** @var string A Query String for use within a URI containing the {@see $serverVersion}. */
  $svQueryString = '?v=' . $serverVersion; // Query String
?>
