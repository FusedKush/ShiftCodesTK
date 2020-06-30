<?php
  $page['meta'] = [
    'title'       => 'Help Center - ShiftCodesTK',
    'description' => 'ShiftCodesTK Help and Support Hub',
    'canonical'   => '/help/',
    'image'       => 'tps/5',
    'theme'       => 'main'
  ];

  require_once('../initialize.php');
?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/css/local/help/index.css<?php echo TK_VERSION_STR; ?>"rel=stylesheet><?php include_once('global/head.php'); ?><body data-theme=main><?php
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
    ?><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><main class=content-wrapper><em class=intro>How can we help you?</em><section class=articles><h2>Help Articles</h2><div class=wrapper id=article_container><?php foreach ($articles as &$article) : ?><a class=resource href="<?= $article['link']; ?>"><div class=icon><span class="<?= $article['icon']; ?>"></span></div><div class=content><strong class=title><?= $article['title']; ?></strong><div class=description><?= $article['description']; ?></div></div></a><?php endforeach; ?></div></section><section class=links><h2>External Links</h2><div class="wrapper contact"><h3>Contact us</h3><a class=resource href=https://m.me/ShiftCodesTK aria-label="Contact us on Facebook (External Link)"rel="external noopener"target=_blank title="Contact us on Facebook (External Link)"><div class=icon><span class="fab fa-facebook-messenger"></span></div><div class=content><div>Contact us</div><div>on Facebook</div></div></a><a class=resource href="https://twitter.com/messages/compose?recipient_id=3830990053"aria-label="Contact us on Twitter (External Link)"rel="external noopener"target=_blank title="Contact us on Twitter (External Link)"><div class=icon><span class="fab fa-twitter"></span></div><div class=content><div>Contact us</div><div>on Twitter</div></div></a></div><div class="wrapper support"><h3>Additional Support</h3><a class=resource href=http://support.gearboxsoftware.com/ aria-label="Visit the official Gearbox Support website (External Link)"rel="external noopener"target=_blank title="Visit the official Gearbox Support website (External Link)"><div class=icon><span class="fas fa-external-link-square-alt"></span></div><div class=content><div>Official Gearbox</div><div>Support website</div></div></a><a class=resource href=https://support.2k.com/ aria-label="Visit the official 2K Games Support website (External Link)"rel="external noopener"target=_blank title="Visit the official 2K Games Support website (External Link)"><div class=icon><span class="fas fa-external-link-square-alt"></span></div><div class=content><div>Official 2K Games</div><div>Support website</div></div></a></div></section><section class=notice><h2><span class="fas fa-exclamation-triangle"></span> Notice</h2><p>As ShiftCodesTK is not affiliated with Gearbox Software or 2K Games, help and support provided by ShiftCodesTK related to Borderlands & SHiFT is not guaranteed to be 100% accurate. For the most accurate and reliable support, visit the official <a class=themed href=http://support.gearboxsoftware.com/ aria-label="Official Gearbox Support Support Website (External Link)"rel="external noopener"target=_blank title="Official Gearbox Support website (External Link)"><span class="fas fa-external-link-square-alt"aria-label="External Link"title="External Link"> </span>Gearbox Support</a> or <a class=themed href=https://support.2k.com/ aria-label="Official 2K Games Support Website (External Link)"rel="external noopener"target=_blank title="Official 2K Games Support website (External Link)"><span class="fas fa-external-link-square-alt"aria-label="External Link"title="External Link"> </span>2K Games Support</a> websites.</section></main><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?>