<?php
  $page['meta'] = [
    'title'       => 'Credits - ShiftCodesTK',
    'description' => 'The people and projects that make ShiftCodesTK possible',
    'canonical'   => '/credits',
    'image'       => 'tps/2',
    'theme'       => 'main'
  ];

  require_once('initialize.php');
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/css/local/credits.css<?php echo \ShiftCodesTK\VERSION_QUERY_STR; ?>" rel="stylesheet"></link>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="main">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <main>
      <section class="credits content-wrapper">
        <div class="banner" id="banner">
          <div class="header">
            <div class="flag" title="Coded with Love by Zach Vaughan" aria-label="Coded with Love by Zach Vaughan">
              <span class="fas fa-code" title="Coded" aria-label="Coded"></span>
              with
              <span class="fas fa-heart" title="Love" aria-label="Love"></span>
              by
              <strong>Zach Vaughan</strong>
            </div>
          </div>
          <div class="description">
            <p>
              <i>ShiftCodesTK was Coded & Created, Updated & Maintained, and filled with Coffee & Love by Zach Vaughan</i>
            </p>
          </div>
        </div>
        <a class="module" id="module_font_awesome" href="https://fontawesome.com" rel="external noopener" target="_blank" aria-labelledby="module_font_awesome_name" aria-describedby="module_font_awesome_description">
          <div class="header">
            <span class="icon">
              <span class="fab fa-font-awesome-flag" title="Font Awesome Flag" aria-label="Font Awesome Flag"></span>
            </span>
            <span class="info">
              <h3 id="module_font_awesome_name">FontAwesome</h3>
              <span>fontawesome.com</span>
            </span>
          </div>
          <p class="description" id="module_font_awesome_description">FontAwesome provided all of the, well,&nbsp;<em>awesome</em>&nbsp;icons that can be found all across the site.
          </p>
        </a>
        <a class="module" id="module_loading_io" href="https://loading.io" rel="external noopener" target="_blank" aria-labelledby="module_loading_io_name" aria-describedby="module_loading_io_description">
          <div class="header">
            <span class="icon">
              <span class="fas fa-spinner" title="Spinner" aria-label="Spinner"></span>
            </span>
            <span class="info">
              <h3>Loading.io</h3>
              <span>loading.io</span>
            </span>
          </div>
          <p class="description" id="module_loading_io_description">Loading.io provided the cool, lightweight loading icons that are used on the site.</p>
        </a>
        <a class="module" id="module_cloudflare" href="https://www.cloudflare.com/" rel="external noopener" target="_blank" aria-labelledby="module_cloudflare_name" aria-describedby="module_cloudflare_description">
          <div class="header">
            <span class="icon">
              <span class="fas fa-cloud" title="Cloud" aria-label="Cloud"></span>
            </span>
            <span class="info">
              <h3>Cloudflare</h3>
              <span>cloudflare.com</span>
            </span>
          </div>
          <p class="description" id="module_loading_io_description">Cloudflare provides many benefits that greatly improve the speed, reliability, and security of ShiftCodesTK.</p>
        </a>
        <div class="shoutout">All images, logos, and trademarks are the rightful property of their respective owners.</div>
      </section>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
  </body>
</html>
