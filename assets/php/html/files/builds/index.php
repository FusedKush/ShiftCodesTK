<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/php/html/min/imports/importPath.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/styles/css/min/local/index.min.css<?php echo $svQueryString; ?>" rel="stylesheet"></link>
    <!--// Page-Specific Metadata \\-->
    <!-- Page Title -->
    <title>ShiftCodesTK</title>
    <meta property="og:title" content="ShiftCodesTK">
    <meta property="twitter:title" content="ShiftCodesTK">
    <!-- Page Description -->
    <meta name="description" content="SHiFT Codes for Borderlands and Wonderlands">
    <meta property="og:description" content="SHiFT Codes for Borderlands and Wonderlands">
    <meta property="twitter:description" content="SHiFT Codes for Borderlands and Wonderlands">
    <!-- Canonical Page Location -->
    <meta name="canonical" href="https://shiftcodestk.com">
    <meta property="og:url" content="https://shiftcodestk.com">
    <!-- Page Images -->
    <meta property="og:image" content="https://shiftcodestk.com/assets/img/metadata/bl2/2.png">
    <meta property="twitter:image" content="https://shiftcodestk.com/assets/img/metadata/bl2/2.png">
    <!-- Page-Specific Browser Properties -->
    <link rel="manifest" href="/assets/manifests/main.webmanifest">
    <meta name="theme-color-tm" id="theme_color_tm" content="#f00">
    <!-- Google Metadata (Landing Page Only) -->
    <meta name="google-site-verification" content="dmsrwqOh26nDUBkS9sCSJ4rblI5g363hbCNhvr-nW8s">
    <!--// Shared Head Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="main">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Content -->
    <main class="no-header">
      <section class="main" data-webp='{"path": "/assets/img/banners/wonderlands/2", "alt": ".jpg", "type": "bg"}'>
        <div class="content-wrapper">
          <div class="brand">
            <img class="logo" src="/assets/img/logo.svg" width="3.5em" alt="ShiftCodesTK Logo">
            <h1 class="name">ShiftCodesTK</h1>
            <div class="tagline">Less time Scrolling, More time Gaming</div>
          </div>
          <div class="action">
            <h2 class="string">SHiFT Codes for&nbsp;<span class="selected chosen wonderlands">Tiny Tina's Wonderlands</span></h2>
            <div class="link-container">
              <a
                class="button wonderlands"
                href="/wonderlands"
                data-string="Tiny Tina's Wonderlands"
                data-quote="It's time to become chaotic great.">Wonderlands</a>
              <a
                class="button bl3"
                href="/bl3"
                data-string="Borderlands 3"
                data-quote="Lets make some mayhem.">BL3</a>
              <a
                class="button bl2"
                href="/bl2"
                data-string="Borderlands 2"
                data-quote="What are you waiting for? Handsome Jack isn't going to defeat himself!">BL2</a>
              <a
                class="button bl1"
                href="/bl1"
                data-string="Borderlands: GOTY"
                data-long-string="Borderlands: Game of the Year Edition"
                data-quote="If you wanna get to the Vault first, you're gonna need to eliminate the competition.">GOTY</a>
              <a
                class="button tps"
                href="/tps"
                data-string="Borderlands: TPS"
                data-long-string="Borderlands: The Pre-Sequel"
                data-quote="Come to the moon, hunt a vault, be a hero.">TPS</a>
            </div>
          </div>
        </div>
      </section>
      <section class="faq">
        <div class="content-wrapper">
          <h2 class="title">Frequently Asked Questions</h2>
          <div class="questions">
            <div class="dropdown-panel c">
              <h3 class="primary">What is SHiFT?</h3>
              <div class="body">
                <p>SHiFT is a service created by Gearbox to reward their players with in-game loot and special events.</p>
              </div>
            </div>
            <div class="dropdown-panel c">
              <h3 class="primary">What are SHiFT Codes?</h3>
              <div class="body">
                <p>SHiFT Codes are 25-character keys that grant in-game rewards.</p>
              </div>
            </div>
            <div class="dropdown-panel c">
              <h3 class="primary">How often are SHiFT Codes released?</h3>
              <div class="body">
                <p>SHiFT Codes are typically released every Friday around 10AM PST.</p>
              </div>
            </div>
          </div>
          <div class="link">For the full list of Frequently Asked Questions, visit our&nbsp;<a class="themed" href="/help/faq">FAQ page</a></div>
        </div>
      </section>
    </main>
    <template id="secondary_section_template">
      <section class="secondary" data-webp='{"path": "/assets/img/banners/", "alt": ".jpg", "type": "bg"}'>
        <div class="content-wrapper">
          <div class="intro">
            <h2 class="title"></h2>
            <i class="quote"></i>
          </div>
          <a class="button"></a>
        </div>
      </section>
    </template>
    <template id="flag_template">
      <div class="flags">
        <div class="flag new" title="New SHiFT Codes!" aria-label="New SHiFT Codes!">
          <span class="fas fa-star"></span>
        </div>
        <div class="flag exp" title="Expiring SHiFT Codes!" aria-label="Expiring SHiFT Codes!">
          <span class="fas fa-exclamation-triangle"></span>
        </div>
      </div>
    </template>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/scripts/min/local/index.min.js<?php echo $svQueryString; ?>"></script>
  </body>
</html>
