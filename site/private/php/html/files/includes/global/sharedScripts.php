<?php
    $files = ['shared-scripts'];
    $urls = [];

    foreach($files as $file) {
      $urls[] = "/assets/js/$file.js" . TK_VERSION_STR;
    }
?>
<?php foreach($urls as $url) : ?>
  <script async src="<?= $url; ?>"></script>
<?php endforeach; ?>