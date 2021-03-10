<?php
  $files = [ 'navbar', 'sidebar', 'imageViewer' ];

  foreach ($files as $file) {
    include_once(\ShiftCodesTK\PRIVATE_PATHS['html_includes'] . "/global/files/beforeContent/$file.php");
  }
?>