<?php
  $t_before_content_includes = [
    'navbar', 
    'sidebar', 
    'image-viewer' 
  ];

  foreach ($t_before_content_includes as $t_include) {
    include("files/before-content/{$t_include}.php");
  }

  unset(
    $t_before_content_includes,
    $t_include
  );
?>