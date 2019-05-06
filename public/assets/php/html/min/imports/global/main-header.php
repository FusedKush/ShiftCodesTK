<?php
  $loc = str_replace(".php", "", $_SERVER['SCRIPT_NAME']);
  $meta = get_meta_tags($_SERVER['SCRIPT_FILENAME']);
  $title = str_replace(" - ShiftCodesTK", "", $meta['title']);
  $description = $meta['description'];
?><header class=main data-webp='{"path": "/assets/img/banners<?php echo $loc; ?>", "name": "<?php echo $loc; ?>", "alt": ".jpg", "type": "bg"}'><div class=content-container><div class=content-wrapper><div class="content short"><h1 class=title><?php echo $title; ?></h1><div class=description><?php echo $description; ?></div></div></div></div></header>