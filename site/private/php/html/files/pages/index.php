<?php 
  $page['meta'] = [
    'title'       => 'ShiftCodesTK',
    'description' => 'SHiFT Codes for Borderlands',
    'canonical'   => '',
    'image'       => 'bl3/6',
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
    <link href="/assets/css/local/index.css<?= TK_VERSION_STR; ?>" rel="stylesheet"></link>
    <!-- Google Metadata (Landing Page Only) -->
    <meta name="google-site-verification" content="dmsrwqOh26nDUBkS9sCSJ4rblI5g363hbCNhvr-nW8s">
    <!--// Metadata \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="main">
    <!--// Before-Content Includes \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Content -->
    <main class="no-header">
      <?php 
        $games = [
          'bl3' => [
            'string'      => 'Borderlands 3',
            'quote'       => "Lets make some mayhem."
          ],
          'bl1' => [
            'string'      => 'Borderlands: GOTY', 
            'long-string' => 'Borderlands: Game of the Year Edition', 
            'quote'       => "If you wanna get to the Vault first, you're gonna need to eliminate the competition."
          ],
          'bl2' => [
            'string'      => 'Borderlands 2', 
            'quote'       => "What are you waiting for? Handsome Jack isn't going to defeat himself!"
          ],
          'tps' => [
            'string'      => 'Borderlands: TPS', 
            'long-string' => 'Borderlands: The Pre-Sequel',
            'quote'       => "Come to the moon, hunt a vault, be a hero."
          ],
        ];

        foreach ($games as $game => $props) {
          if (!isset($props['long-string'])) {
            $games[$game]['long-string'] = $props['string'];
          }
        }
      ?>
      <?php function indexButtonMarkup ($game, $props, $counts, $sectionButton = false) { ?>
        <?php $title = "SHiFT Codes for {$props['long-string']}"; ?>
        <a
          class="button button-effect hover class-theme <?= " $game"; ?>"
          href="/<?= $game; ?>"
          data-string="<?= $props['string']; ?>"
          data-long-string="<?= $props['long-string']; ?>"
          aria-label="<?= $title; ?>">
          <span>
            <?php
              if (!$sectionButton) { echo strtoupper($game); } 
              else                 { echo $props['string']; }
            ?>
          </span>
          <?php if ($counts['new'] > 0 || $counts['expiring'] > 0) : ?>
            <div class="flags">
            <?php if ($counts['new'] > 0) : ?>
              <div class="flag new" title="New SHiFT Codes!" aria-label="New SHiFT Codes!">
                <span class="fas fa-star"></span>
              </div>
            <?php endif; ?>
            <?php if ($counts['expiring'] > 0) : ?>
              <div class="flag expiring" title="Expiring SHiFT Codes!" aria-label="Expiring SHiFT Codes!">
                <span class="fas fa-exclamation-triangle"></span>
              </div>
            <?php endif; ?>
            </div>
          <?php endif; ?>
        </a>
      <?php }; ?>

      <section class="main" data-webp='{"path": "/assets/img/banners/bl3/6", "alt": ".jpg", "type": "bg"}'>
        <div class="content-wrapper">
          <div class="brand">
            <img class="logo" src="/assets/img/logo.svg" width="3.5em" alt="ShiftCodesTK Logo">
            <h1 class="name">ShiftCodesTK</h1>
            <div class="tagline">Less time Scrolling, More time Gaming</div>
          </div>
          <div class="actions-container">
            <h2 class="string">SHiFT Codes for&nbsp;<span class="selected chosen bl3">Borderlands 3</span></h2>
            <div class="link-container">
            <!-- Game links -->
              <?php foreach ($games as $game => $props) : ?>
                <?php
                  $counts = [
                    'new' => SHIFT_STATS[$game]['new'],
                    'expiring' => SHIFT_STATS[$game]['expiring']
                  ];
                ?>
                <?= indexButtonMarkup($game, $props, $counts); ?>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </section>
      <!---->
      <!-- Game sections -->
      <?php foreach ($games as $game => $props) : ?>
        <?php
          $counts = [
            'new' => SHIFT_STATS[$game]['new'],
            'expiring' => SHIFT_STATS[$game]['expiring']
          ];
        ?>
        <section class="secondary <?= " $game"; ?>" data-webp='{"path": "/assets/img/banners/<?= $game; ?>/1", "alt": ".jpg", "type": "bg"}'>
          <div class="content-wrapper">
            <div class="intro">
              <h2 class="title"><?= $props['string']; ?></h2>
              <i class="quote"><?= $props['quote']; ?></i>
            </div>
            <?= indexButtonMarkup($game, $props, $counts, true); ?>
          </div>
        </section>
      <?php endforeach; ?>
      <!-- Mini FAQ -->
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
          <div class="link">For the full list of Frequently Asked Questions, visit our&nbsp;<a class="styled" href="/help/faq">FAQ page</a></div>
        </div>
      </section>
    </main>
    <!--// After-Content Includes \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/js/local/index.js<?= TK_VERSION_STR; ?>"></script>
  </body>
</html>