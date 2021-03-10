<?php
  $files = ['metadata', 'inlineStyles', 'noscript-styles'];

  foreach ($files as $file) {
    include_once(\ShiftCodesTK\PRIVATE_PATHS['html_includes'] . "/global/files/head/$file.php");
  }
?>