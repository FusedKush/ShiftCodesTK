<?php
  $sn = 'localhost';
  $un = 'ShiftCodesTK';
  $pw = 'SbT09E*;={&wRj#u2nS28Fu7#O!eni]0k[os';
  $db = 'ShiftCodesTK';
  $con = mysqli_connect($sn, $un, $pw, $db);

  if(!$con) {
    die('Connection failed: ' . mysqli_connect_error());
  }
?>
