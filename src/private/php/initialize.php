<?php
  /** 
   * The primary PHP Initialization File. All PHP Scripts *must* include this file at least once. 
   **/

  // Script Initialization
  $__initialization_files = [
    'definition-constants',
    'loaders',
    'startup'
  ];

  foreach ($__initialization_files as $filename) {
    include(dirname($_SERVER["DOCUMENT_ROOT"]) . "/private/php/initialize/{$filename}.php");
  }

  unset($__initialization_files);
?>