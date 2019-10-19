<?php
  $loc = str_replace(".php", "", $_SERVER['SCRIPT_NAME']);
  $meta = get_meta_tags($_SERVER['SCRIPT_FILENAME']);
  $title = str_replace(" - ShiftCodesTK", "", $meta['title']);
  $description = $meta['description'];
?>
<header class="main" id="primary_header">
  <div class="intro" data-webp='{"path": "/assets/img/banners<?php echo $loc; ?>", "alt": ".jpg", "type": "bg"}'>
    <div class="content-container">
      <div class="content-wrapper">
        <div class="content short">
          <h1 class="title"><?php echo $title; ?></h1>
          <div class="description"><?php echo $description; ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="breadcrumbs">
    <div class="content-wrapper" id="breadcrumb_container"></div>
    <template id="breadcrumb_separator_template">
      <b class="separator">/</b>
    </template>
    <template id="breadcrumb_crumb_template">
      <a class="crumb tr-underline"></a>
    </template>
    <template id="breadcrumb_crumb_here_template">
      <b class="crumb"></b>
    </template>
  </div>
</header>
