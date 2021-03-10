<?php
  $files = [ 'footer', 'containers/containers', 'noscript-markup' ];

  foreach ($files as $file) {
    include_once(\ShiftCodesTK\PRIVATE_PATHS['html_includes'] . "/global/files/afterContent/$file.php");
  }
?>