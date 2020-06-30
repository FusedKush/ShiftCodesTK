<?php
    $files = ['shared-styles'];
    $urls = [];

    foreach($files as $file) {
      $urls[] = "/assets/css/$file.css" . TK_VERSION_STR;
    }
?><?php foreach($urls as $url) : ?><link href="<?= $url; ?>"rel=stylesheet><?php endforeach; ?>