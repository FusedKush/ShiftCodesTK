<?php
  // Server Version Number
  require_once(dirname($_SERVER['DOCUMENT_ROOT']) . '/private/php/scripts/serverVersion.php');

  // Configuration
  $dir = '/assets/js//'; // Assets Directory
  $ext = '.js';        // Script Extension

  // Stylesheets to Load
  $load = array(
    'functions',
    'shared-scripts'
  );

  // Load all Stylesheets
  for ($i = 0; $i < count($load); $i++) {
    $link = $dir . $load[$i] . $ext . $svQueryString;

    echo '<script async src="' . $link .'"></script>';
  }
?>
