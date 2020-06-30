<?php include_once(dirname($_SERVER["DOCUMENT_ROOT"]) . '/private/php/html/min/includes/importPath.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="assets/css/local/shift.css<?php echo $svQueryString; ?>" rel="stylesheet"></link>
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
    <meta name="canonical" href="https://shiftcodestk.com/bl2">
    <meta property="og:url" content="https://shiftcodestk.com/bl2">
    <!-- Page Images -->
    <meta name="header:image" content="bl2_1">
    <meta property="og:image" content="https://shiftcodestk.com/assets/img/metadata/bl2/1.png">
    <meta property="twitter:image" content="https://shiftcodestk.com/assets/img/metadata/bl2/1.png">
    <!-- Page-Specific Browser Properties -->
    <link rel="manifest" href="/assets/manifests/bl2.webmanifest">
    <meta name="theme-color-tm" id="theme_color_tm" content="#ff4500">
    <!--// Shared Head Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="bl2">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <!-- SHiFT -->
    <?php include_once("local/shift.php"); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/js/local/shift.js<?php echo $svQueryString; ?>"></script>
  </body>
</html>
