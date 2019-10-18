<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/php/html/min/imports/importPath.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/styles/css/min/local/shift.min.css<?php echo $svQueryString; ?>" rel="stylesheet"></link>
    <!--// Page-Specific Metadata \\-->
    <!-- Page Title -->
    <title>Borderlands 2 - ShiftCodesTK</title>
    <meta name="title" content="Borderlands 2 - ShiftCodesTK">
    <meta property="og:title" content="Borderlands 2 - ShiftCodesTK">
    <meta property="twitter:title" content="Borderlands 2 - ShiftCodesTK">
    <!-- Page Description -->
    <meta name="description" content="SHiFT Codes for Borderlands 2">
    <meta property="og:description" content="SHiFT Codes for Borderlands 2">
    <meta property="twitter:description" content="SHiFT Codes for Borderlands 2">
    <!-- Canonical Page Location -->
    <meta name="canonical" href="https://shiftcodes.tk/bl2">
    <meta property="og:url" content="https://shiftcodes.tk/bl2">
    <meta name="breadcrumbs" id="breadcrumbs" content='[{"name": "Borderlands 2", "url": "/bl2"}]'>
    <!-- Page Thumbnail Image -->
    <meta property="og:image" content="https://shiftcodes.tk/assets/img/metadata/bl2.png">
    <meta property="twitter:image" content="https://shiftcodes.tk/assets/img/metadata/bl2.png">
    <!-- Page-Specific Browser Properties -->
    <link rel="manifest" href="/assets/manifests/bl2.webmanifest">
    <meta name="theme-color-tm" id="theme_color_tm" content="#ff4500">
    <!--// Shared Head Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="bl2" data-shift='{"id": "1", "name": "Borderlands 2"}'>
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <!-- SHiFT -->
    <?php include_once("./assets/php/html/min/imports/local/shift.php"); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/scripts/min/local/shift.min.js<?php echo $svQueryString; ?>"></script>
  </body>
</html>
