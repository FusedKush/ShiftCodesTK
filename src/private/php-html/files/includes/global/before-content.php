<?php
  $t_includes = [
    'navbar', 
    'sidebar', 
    'image-viewer' 
  ];

  foreach ($t_includes as $t_include) {
    require("files/before-content/{$t_include}.php");
  }

  unset(
    $t_includes,
    $t_include
  );
?>