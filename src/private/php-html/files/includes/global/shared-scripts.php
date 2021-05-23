<?php
  $t_shared_scripts = [ 'browserify-bundle', 'functions', 'shared-scripts' ];
?>

<?php foreach($t_shared_scripts as $t_filename) : ?>
  <script async src="<?= "/assets/js/{$t_filename}.js" . \ShiftCodesTK\VERSION_QUERY_STR; ?>"></script>
<?php 
  endforeach; 

  unset(
    $t_shared_scripts,
    $t_filename
  ); 
?>