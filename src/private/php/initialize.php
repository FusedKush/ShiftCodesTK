<?php
  /** 
   * The primary PHP Initialization File. All PHP Scripts *must* include this file at least once. 
   **/

  
  // Script Initialization

  /** @var array The PHP Scripts used for script initialization. */
  define("ShiftCodesTK\Initialize\INITIALIZATION_FILES", [
    'definition-constants',
    'loaders',
    'startup'
  ]);

  foreach (\ShiftCodesTK\Initialize\INITIALIZATION_FILES as $filename) {
    include(dirname($_SERVER["DOCUMENT_ROOT"]) . "/private/php/initialize/{$filename}.php");
  }

  // Remote Script Processing
  if (\ShiftCodesTK\SCRIPT_TYPE === \ShiftCodesTK\SCRIPT_TYPE_SCRIPT) {
    include(\ShiftCodesTK\PRIVATE_PATHS['initialize'] . '/request.php');
  }
?>