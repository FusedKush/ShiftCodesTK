<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/php/html/min/imports/importPath.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/styles/css/min/local/help/faq.min.css<?php echo $svQueryString; ?>" rel="stylesheet"></link>
    <!--// Page-Specific Metadata \\-->
    <!-- Page Title -->
    <title>FAQ - ShiftCodesTK</title>
    <meta name="title" content="FAQ - ShiftCodesTK">
    <meta property="og:title" content="FAQ - ShiftCodesTK">
    <meta property="twitter:title" content="FAQ - ShiftCodesTK">
    <!-- Page Description -->
    <meta name="description" content="Answers to some frequently asked questions">
    <meta property="og:description" content="Answers to some frequently asked questions">
    <meta property="twitter:description" content="Answers to some frequently asked questions">
    <!-- Canonical Page Location -->
    <meta name="canonical" href="https://shiftcodestk.com/help/faq">
    <meta property="og:url" content="https://shiftcodestk.com/help/faq">
    <!-- Page Images -->
    <meta name="header:image" content="tps_4">
    <meta property="og:image" content="https://shiftcodestk.com/assets/img/metadata/tps/4.png">
    <meta property="twitter:image" content="https://shiftcodestk.com/assets/img/metadata/tps/4.png">
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
      <header class="toc dropdown-panel">
        <button class="header dropdown-panel-toggle">
          <div class="wrapper">
            <div class="title">
              <div class="icon">
                <span class="fas fa-list-alt"></span>
              </div>
              <div class="string">
                <h2 class="primary">Table of Contents</h2>
              </div>
            </div>
            <div class="indicator">
              <span class="fas fa-chevron-right"></span>
            </div>
          </div>
        </button>
        <div class="body content-container" id="table_of_contents">
        </div>
      </header>
      <section class="faq-group">
        <header>
          <h2 class="title">SHiFT</h2>
        </header>
        <div class="dropdown-panel c">
          <h3 class="primary">What is SHiFT?</h3>
          <div class="body"><p>SHiFT is a service created by Gearbox to reward their players with in-game loot and special events.</p></div>
        </div>
        <div class="dropdown-panel c">
          <h3 class="primary">Do you need an account to use SHiFT?</h3>
          <div class="body"><p>Yes. You can create a free account&nbsp;<a class="themed" href="https://shift.gearboxsoftware.com/registration/pre" rel="external noopener" target="_blank" title="Register for SHiFT (External Link)" aria-label="Register for SHiFT (External Link)"><span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link">&nbsp;</span><span class="label">here</span></a>.</p></div>
        </div>
        <div class="dropdown-panel c">
          <h3 class="primary">How old do you need to be to use SHiFT?</h3>
          <div class="body"><p>You must be at least 18 years old to sign up for SHiFT.</p></div>
        </div>
      </section>
      <section class="faq-group">
        <header>
          <h2 class="title">ShiftCodesTK</h2>
        </header>
        <div class="dropdown-panel c">
          <h3 class="primary">Is ShiftCodesTK affiliated with Gearbox or 2K?</h3>
          <div class="body"><p>Nope, ShiftCodesTK is a completely fan-made service, and is not affiliated with Gearbox Software or 2K Games in any way.</p></div>
        </div>
        <div class="dropdown-panel c">
          <h3 class="primary">Can you create a SHiFT Code for me? Can you change the properties of a SHiFT Code? Can you give me a legendary?</h3>
          <div class="body"><p>No, we have no control over the SHiFT Codes themselves, and just share the codes released by Gearbox.</p></div>
        </div>
        <div class="dropdown-panel c">
          <h3 class="primary">Why is ShiftCodesTK not updated as soon as SHiFT Codes are posted?</h3>
          <div class="body"><p>At the time of writing, ShiftCodesTK does not have access to the necessary features of the Twitter & Facebook APIs to retrieve the necessary posts and automatically parse them, as well as lacking the ability to then update our own personal pages once this information has been obtained.</p></div>
        </div>
        <div class="dropdown-panel c">
          <h3 class="primary">Do you have any information on future titles, expansions, or events related to Borderlands?</h3>
          <div class="body"><p>Nope, we have no information on anything regarding the future of Borderlands.</p></div>
        </div>
      </section>
      <section class="faq-group">
        <header>
          <h2 class="title">SHiFT Codes</h2>
        </header>
        <div class="dropdown-panel c">
          <h3 class="primary">What are SHiFT Codes?</h3>
          <div class="body"><p>SHiFT Codes are 25-character keys that grant in-game rewards.</p></div>
        </div>
        <div class="dropdown-panel c">
          <h3 class="primary">Where are SHiFT Codes released?</h3>
          <div class="body"><p>SHiFT Codes are typically released on Twitter and Facebook via official handles, but are also sometimes released during special events and livestreams.</p></div>
        </div>
        <div class="dropdown-panel c">
          <h3 class="primary">How often are SHiFT Codes released?</h3>
          <div class="body"><p>SHiFT Codes are typically released every Friday around 10AM PST.</p></div>
        </div>
        <div class="dropdown-panel c">
          <h3 class="primary">How many times can a SHiFT Code be redeemed?</h3>
          <div class="body"><p>SHiFT Codes can be redeemed once per platform, per account.</p></div>
        </div>
        <div class="dropdown-panel c">
          <h3 class="primary">Do SHiFT Codes ever expire?</h3>
          <div class="body"><p>Yes, most SHiFT Codes will be issued with an Expiration Date, typically about two to three weeks after their release. However, some SHiFT Codes have and can be issued without an Expiration Date.</p></div>
        </div>
        <div class="dropdown-panel c">
          <div class="primary">What games support SHiFT Codes?</div>
          <div class="body">
            <p>The following titles support SHiFT Codes:</p>
            <ul class="styled">
              <li>Borderlands: Game of the Year Edition</li>
              <li>Borderlands 2</li>
              <li>Borderlands: The Pre-Sequel</li>
              <li>Borderlands 3</li>
            </ul>
            <p><em>There are other titles that accept SHiFT Codes, such as Battleborn, but they are not supported on ShiftCodesTK.</em></p>
          </div>
        </div>
        <div class="dropdown-panel c">
          <div class="primary">Can I add a SHiFT Code to ShiftCodesTK?</div>
          <div class="body">
            <p>At the moment, SHiFT Codes cannot be added to ShiftCodesTK by the community. This feature will likely be implemented in the future.</p>
          </div>
        </div>
      </section>
    </main>
    <template id="toc_entry_template">
      <div class="section">
        <h3><a class="internal"></a></h3>
        <ul class="styled">
        </ul>
      </div>
    </template>
    <template id="toc_entry_listitem_template">
      <li><a class="internal"></a></li>
    </template>
    <!-- Support Footer -->
    <?php include_once('local/support-footer.php'); ?>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/scripts/min/local/help/faq.min.js<?php echo $svQueryString; ?>"></script>
  </body>
</html>
