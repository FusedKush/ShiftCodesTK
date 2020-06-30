<?php
  $files = ['footer', 'containers/containers', 'noscript-markup'];

  foreach ($files as $file) {
    include_once(HTML_INCLUDES_PATH . "global/files/afterContent/$file.php");
  }
?>