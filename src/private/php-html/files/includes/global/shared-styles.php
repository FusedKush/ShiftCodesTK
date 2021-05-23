<?php
  $t_shared_styles = [ 
    'shared-styles' 
  ];
?>

<?php foreach($t_shared_styles as $t_filename) : ?>
  <link rel="stylesheet" href="<?= "/assets/css/{$t_filename}.css" . \ShiftCodesTK\VERSION_QUERY_STR; ?>">
<?php 
  endforeach; 

  unset(
    $t_shared_styles,
    $t_filename
  ); 
?>