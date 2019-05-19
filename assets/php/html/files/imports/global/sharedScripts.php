<?php
  // Server Version Number
  require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/php/scripts/serverVersion.php');

  // Configuration
  $dir = '/assets/scripts/min/'; // Assets Directory
  $ext = '.min.js';              // Script Extension

  // Stylesheets to Load
  $load = array(
    'shared-scripts'
  );

  // Load all Stylesheets
  for ($i = 0; $i < count($load); $i++) {
    $link = $dir . $load[$i] . $ext . $svQueryString;

    echo '<script async src="' . $link .'"></script>';
  }
?>
