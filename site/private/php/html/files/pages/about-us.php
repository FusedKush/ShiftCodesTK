<?php
  $page['meta'] = [
    'title'       => 'About us - ShiftCodesTK',
    'description' => 'ShiftCodesTK: Less time Scrolling, More time Gaming',
    'canonical'   => '/about-us',
    'image'       => 'bl2/3',
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
    <link href="/assets/css/local/about-us.css<?php echo TK_VERSION_STR; ?>" rel="stylesheet"></link>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="main">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <main class="content-wrapper">
      <div class="banner">
        <img src="/assets/img/logo.svg" alt="ShiftCodesTK Logo">
        <h2 class="title">ShiftCodesTK</h2>
        <div class="tagline">Less time Scrolling, More time Gaming</div>
      </div>
      <div class="wrapper">
        <div class="section">
          <span class="icon">
            <span class="box-icon fas fa-toolbox"></span>
          </span>
          <p>ShiftCodesTK is an exercise in quality over quantity, striving to provide an experience that is consistently accessible, reliable, and rewarding.</p>
        </div>
        <div class="section">
          <span class="icon">
            <span class="box-icon fas fa-laptop-code"></span>
          </span>
          <p>
            ShiftCodesTK is also a personal Web Development playground, and has been shaped by an ever-expanding understanding of the various Web Development principles, guidelines, and standards.
          </p>
        </div>
        <div class="section">
          <span class="icon">
            <span class="box-icon fas fa-users"></span>
          </span>
          <p>
            Finally, ShiftCodesTK is built to serve the ever-loyal Borderlands community. Without the community's dedicated support of Borderlands, ShiftCodesTK would not be available to you today.
          </p>
        </div>
      </div>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
  </body>
</html>
