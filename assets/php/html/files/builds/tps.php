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
    <title>Borderlands: The Pre-Sequel - ShiftCodesTK</title>
    <meta name="title" content="Borderlands: TPS - ShiftCodesTK">
    <meta property="og:title" content="Borderlands: The Pre-Sequel - ShiftCodesTK">
    <meta property="twitter:title" content="Borderlands: The Pre-Sequel - ShiftCodesTK">
    <!-- Page Description -->
    <meta name="description" content="SHiFT Codes for Borderlands: The Pre-Sequel">
    <meta property="og:description" content="SHiFT Codes for Borderlands: The Pre-Sequel">
    <meta property="twitter:description" content="SHiFT Codes for Borderlands: The Pre-Sequel">
    <!-- Canonical Page Location -->
    <meta name="canonical" href="https://shiftcodestk.com/tps">
    <meta property="og:url" content="https://shiftcodestk.com/tps">
    <!-- Page Images -->
    <meta name="header:image" content="tps_1">
    <meta property="og:image" content="https://shiftcodestk.com/assets/img/metadata/tps/1.png">
    <meta property="twitter:image" content="https://shiftcodestk.com/assets/img/metadata/tps/1.png">
    <!-- Page-Specific Browser Properties -->
    <link rel="manifest" href="/assets/manifests/tps.webmanifest">
    <meta name="theme-color-tm" id="theme_color_tm" content="#1e90ff">
    <!--// Shared Head Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="tps">
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
