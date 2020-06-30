<!-- External Scripts -->
<script async src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js" integrity="sha256-ZsWP0vT+akWmvEMkNYgZrPHKU9Ke8nYBPC3dqONp1mY=" crossorigin="anonymous"></script>

<!-- Local Scripts -->
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