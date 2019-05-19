<?php
  // Inlined Startup Styles
  include_once('global/files/inlineStyles.php');
  // Server Version Number
  require_once($_SERVER['DOCUMENT_ROOT'] . '/assets/php/scripts/serverVersion.php');

  // Configuration
  $dir = '/assets/styles/css/min/'; // Assets Directory
  $ext = '.min.css';                // Stylesheet Extension

  // Stylesheets to Load
  $load = array(
    'shared-styles'
  );

  // Load all Stylesheets
  for ($i = 0; $i < count($load); $i++) {
    $link = $dir . $load[$i] . $ext . $svQueryString;

    echo '<link href="' . $link . '" rel="stylesheet">';
  }
?>