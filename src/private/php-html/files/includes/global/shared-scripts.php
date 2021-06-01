<?php (function () { ?>
  <?php
    $scripts = [ 'browserify-bundle', 'functions', 'shared-scripts' ];
  ?>

  <?php foreach($scripts as $script) : ?>
    <script async src="<?= "/assets/js/{$script}.js" . \ShiftCodesTK\VERSION_QUERY_STR; ?>"></script>
  <?php endforeach; ?>
<?php })(); ?>