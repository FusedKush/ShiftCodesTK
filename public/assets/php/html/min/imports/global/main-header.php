<?php
  $loc = str_replace(".php", "", $_SERVER['SCRIPT_NAME']);
  $meta = get_meta_tags($_SERVER['SCRIPT_FILENAME']);
  $title = str_replace(" - ShiftCodesTK", "", $meta['title']);
  $description = $meta['description'];
?><header class="bg-img main theme-bc"data-webp='{"path": "/assets/img/banners<?php echo $loc; ?>", "name": "<?php echo $loc; ?>", "alt": ".jpg", "type": "bg"}'><div class=container><div class="content-container flexbox flexbox-h-start flexbox-v-end wrapper"><div class="content short"><h1 class="custom-margins title"><?php echo $title; ?></h1><div class="description dim"><?php echo $description; ?></div></div></div></div></header>