<?php
  // require_once('../initialize.php');
  require_once(dirname(__DIR__) . '/initialize.php');

  use ShiftCodesTK\PageConfiguration;

  (new PageConfiguration('help/index'))
    ->setTitle('Help Center')
    ->setGeneralInfo(
      'ShiftCodesTK Help and Support Hub',
      'tps/5'
    )
    ->saveConfiguration();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/shared-styles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/css/local/help/index.css<?php echo \ShiftCodesTK\VERSION_QUERY_STR; ?>" rel="stylesheet"></link>
    <!--// Markup \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="<?= PageConfiguration::getCurrentPageConfiguration()->getGeneralInfo('theme'); ?>">
    <?php
      $articles = [
        [
          'title'       => 'Clearing your System Cache',
          'description' => 'How to clear your system cache on PC, Xbox, and Playstation',
          'link'        => 'clearing-your-system-cache'
        ],
        [
          'title'       => 'How to Redeem',
          'description' => 'How to redeem SHiFT Codes in Borderlands',
          'link'        => 'how-to-redeem'
        ],
        [
          'title'       => 'FAQ',
          'description' => 'Answers to some frequently asked questions',
          'link'        => 'faq'
        ]
      ];

      // Default icon
      foreach ($articles as &$article) {
        if (!isset($article['icon'])) {
          $article['icon'] = 'fas fa-file-alt';
        }
      }
    ?>
    <!--// Before-Content Imports \\-->
    <?php include_once('global/before-content.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <main class="content-wrapper">
      <em class="intro">How can we help you?</em>
      <section class="articles">
        <h2>Help Articles</h2>
        <div class="wrapper" id="article_container">
          <?php foreach ($articles as &$article) : ?>
            <a class="resource" href="<?= $article['link']; ?>">
              <div class="icon">
                <span class="<?= $article['icon']; ?>"></span>
              </div>
              <div class="content">
                <strong class="title"><?= $article['title']; ?></strong>
                <div class="description"><?= $article['description']; ?></div>
              </div>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
      <section class="links">
        <h2>External Links</h2>
        <div class="contact wrapper">
          <h3>Contact us</h3>
          <a class="resource" href="https://m.me/ShiftCodesTK" target="_blank" rel="external noopener" title="Contact us on Facebook (External Link)" aria-label="Contact us on Facebook (External Link)">
            <div class="icon">
              <span class="fab fa-facebook-messenger"></span>
            </div>
            <div class="content">
              <div>Contact us</div>
              <div>on Facebook</div>
            </div>
          </a>
          <a class="resource" href="https://twitter.com/messages/compose?recipient_id=3830990053" target="_blank" rel="external noopener" title="Contact us on Twitter (External Link)" aria-label="Contact us on Twitter (External Link)">
            <div class="icon">
              <span class="fab fa-twitter"></span>
            </div>
            <div class="content">
              <div>Contact us</div>
              <div>on Twitter</div>
            </div>
          </a>
        </div>
        <div class="support wrapper">
          <h3>Additional Support</h3>
          <a class="resource" href="http://support.gearboxsoftware.com/" target="_blank" rel="external noopener" title="Visit the official Gearbox Support website (External Link)" aria-label="Visit the official Gearbox Support website (External Link)">
            <div class="icon">
              <span class="fas fa-external-link-square-alt"></span>
            </div>
            <div class="content">
              <div>Official Gearbox</div>
              <div>Support website</div>
            </div>
          </a>
          <a class="resource" href="https://support.2k.com/" target="_blank" rel="external noopener" title="Visit the official 2K Games Support website (External Link)" aria-label="Visit the official 2K Games Support website (External Link)">
            <div class="icon">
              <span class="fas fa-external-link-square-alt"></span>
            </div>
            <div class="content">
              <div>Official 2K Games</div>
              <div>Support website</div>
            </div>
          </a>
        </div>
      </section>
      <section class="notice">
        <h2><span class="fas fa-exclamation-triangle"></span>&nbsp;Notice</h2>
        <p>
          As ShiftCodesTK is not affiliated with Gearbox Software or 2K Games, help and support provided by ShiftCodesTK related to Borderlands & SHiFT is not guaranteed to be 100% accurate. For the most accurate and reliable support, visit the official&nbsp;
          <a class="themed" href="http://support.gearboxsoftware.com/" target="_blank" rel="external noopener" title="Official Gearbox Support website (External Link)" aria-label="Official Gearbox Support Support Website (External Link)">
            <span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link">&nbsp;</span>
            Gearbox Support
          </a>
          &nbsp;or&nbsp;
          <a class="themed" href="https://support.2k.com/" target="_blank" rel="external noopener" title="Official 2K Games Support website (External Link)" aria-label="Official 2K Games Support Website (External Link)">
            <span class="fas fa-external-link-square-alt" title="External Link" aria-label="External Link">&nbsp;</span>
            2K Games Support
          </a>
          &nbsp;websites.
        </p>
      </section>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('global/after-content.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/shared-scripts.php'); ?>
  </body>
</html>
