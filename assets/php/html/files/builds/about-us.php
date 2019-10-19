<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/php/html/min/imports/importPath.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/styles/css/min/local/about-us.min.css<?php echo $svQueryString; ?>" rel="stylesheet"></link>
    <!--// Page-Specific Metadata \\-->
    <!-- Page Title -->
    <title>About us - ShiftCodesTK</title>
    <meta name="title" content="About us - ShiftCodesTK">
    <meta property="og:title" content="About us - ShiftCodesTK">
    <meta property="twitter:title" content="About us - ShiftCodesTK">
    <!-- Page Description -->
    <meta name="description" content="ShiftCodesTK: Less time Scrolling, More time Gaming">
    <meta property="og:description" content="ShiftCodesTK: Less time Scrolling, More time Gaming">
    <meta property="twitter:description" content="ShiftCodesTK: Less time Scrolling, More time Gaming">
    <!-- Canonical Page Location -->
    <meta name="canonical" href="https://shiftcodes.tk/about-us">
    <meta property="og:url" content="https://shiftcodes.tk/about-us">
    <meta name="breadcrumbs" id="breadcrumbs" content='[{"name": "About us", "url": "/about-us"}]'>
    <!-- Page Thumbnail Image -->
    <meta property="og:image" content="https://shiftcodes.tk/assets/img/metadata/about-us.png">
    <meta property="twitter:image" content="https://shiftcodes.tk/assets/img/metadata/about-us.png">
    <!-- Page-Specific Browser Properties -->
    <link rel="manifest" href="/assets/manifests/main.webmanifest">
    <meta name="theme-color-tm" id="theme_color_tm" content="#f00">
    <!--// Shared Head Markup \\-->
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
