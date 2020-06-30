<?php
  $files = ['metadata', 'inlineStyles', 'noscript-styles'];

  foreach ($files as $file) {
    include_once(HTML_INCLUDES_PATH . "global/files/head/$file.php");
  }
?>