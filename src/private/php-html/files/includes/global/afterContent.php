<?php
  $files = [ 'footer', 'containers/containers', 'noscript-markup' ];

  foreach ($files as $file) {
    include_once("global/files/afterContent/$file.php");
  }
?>