<?php
  $files = ['navbar', 'sidebar', 'imageViewer'];

  foreach ($files as $file) {
    include_once(HTML_INCLUDES_PATH . "global/files/beforeContent/$file.php");
  }
?>