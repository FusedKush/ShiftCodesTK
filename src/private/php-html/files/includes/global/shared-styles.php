
<?php (function () { ?>
  <?php
    $stylesheets = [ 
      'shared-styles' 
    ];
  ?>

  <?php foreach($stylesheets as $stylesheet) : ?>
    <link rel="stylesheet" href="<?= "/assets/css/{$stylesheet}.css" . \ShiftCodesTK\VERSION_QUERY_STR; ?>">
  <?php endforeach; ?>
<?php })(); ?>