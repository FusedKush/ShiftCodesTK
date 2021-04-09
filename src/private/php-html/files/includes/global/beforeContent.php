<?php
  $files = [ 'navbar', 'sidebar', 'imageViewer' ];

  foreach ($files as $file) {
    include_once("global/files/beforeContent/$file.php");
  }
?>