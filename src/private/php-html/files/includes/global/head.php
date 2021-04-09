<?php
  $files = ['metadata', 'inlineStyles', 'noscript-styles'];

  foreach ($files as $file) {
    include_once("global/files/head/$file.php");
  }
?>