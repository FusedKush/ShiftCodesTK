<?php 
  $page['meta'] = [
    'title'       => 'ShiftCodesTK',
    'description' => 'SHiFT Codes for Borderlands',
    'canonical'   => '',
    'image'       => 'bl3/6',
    'theme'       => 'main'
  ]; 

  require_once('initialize.php');
?><!doctype html><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/css/local/index.css<?= TK_VERSION_STR; ?>" rel=stylesheet><meta content=dmsrwqOh26nDUBkS9sCSJ4rblI5g363hbCNhvr-nW8s name=google-site-verification><?php include_once('global/head.php'); ?><body data-theme=main><?php include_once('global/beforeContent.php'); ?><main class=no-header><?php 
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
      ?><?php function indexButtonMarkup ($game, $props, $counts, $sectionButton = false) { ?><?php $title = "SHiFT Codes for {$props['long-string']}"; ?><a class="button button-effect hover class-theme<?= " $game"; ?>" href="/<?= $game; ?>" aria-label="<?= $title; ?>" data-long-string="<?= $props['long-string']; ?>" data-string="<?= $props['string']; ?>"><span><?php
              if (!$sectionButton) { echo strtoupper($game); } 
              else                 { echo $props['string']; }
            ?></span><?php if ($counts['new'] > 0 || $counts['expiring'] > 0) : ?><div class=flags><?php if ($counts['new'] > 0) : ?><div class="flag new" aria-label="New SHiFT Codes!" title="New SHiFT Codes!"><span class="fas fa-star"></span></div><?php endif; ?><?php if ($counts['expiring'] > 0) : ?><div class="flag expiring" aria-label="Expiring SHiFT Codes!" title="Expiring SHiFT Codes!"><span class="fas fa-exclamation-triangle"></span></div><?php endif; ?></div><?php endif; ?></a><?php }; ?><section class=main data-webp='{"path": "/assets/img/banners/bl3/6", "alt": ".jpg", "type": "bg"}'><div class=content-wrapper><div class=brand><img alt="ShiftCodesTK Logo" class=logo src=/assets/img/logo.svg width=3.5em><h1 class=name>ShiftCodesTK</h1><div class=tagline>Less time Scrolling, More time Gaming</div></div><div class=actions-container><h2 class=string>SHiFT Codes for <span class="bl3 chosen selected">Borderlands 3</span></h2><div class=link-container><?php foreach ($games as $game => $props) : ?><?php
                  $counts = [
                    'new' => SHIFT_STATS[$game]['new'],
                    'expiring' => SHIFT_STATS[$game]['expiring']
                  ];
                ?><?= indexButtonMarkup($game, $props, $counts); ?><?php endforeach; ?></div></div></div></section><?php foreach ($games as $game => $props) : ?><?php
          $counts = [
            'new' => SHIFT_STATS[$game]['new'],
            'expiring' => SHIFT_STATS[$game]['expiring']
          ];
        ?><section class="secondary<?= " $game"; ?>" data-webp='{"path": "/assets/img/banners/<?= $game; ?>/1", "alt": ".jpg", "type": "bg"}'><div class=content-wrapper><div class=intro><h2 class=title><?= $props['string']; ?></h2><i class=quote><?= $props['quote']; ?></i></div><?= indexButtonMarkup($game, $props, $counts, true); ?></div></section><?php endforeach; ?><section class=faq><div class=content-wrapper><h2 class=title>Frequently Asked Questions</h2><div class=questions><div class="c dropdown-panel"><h3 class=primary>What is SHiFT?</h3><div class=body><p>SHiFT is a service created by Gearbox to reward their players with in-game loot and special events.</div></div><div class="c dropdown-panel"><h3 class=primary>What are SHiFT Codes?</h3><div class=body><p>SHiFT Codes are 25-character keys that grant in-game rewards.</div></div><div class="c dropdown-panel"><h3 class=primary>How often are SHiFT Codes released?</h3><div class=body><p>SHiFT Codes are typically released every Friday around 10AM PST.</div></div></div><div class=link>For the full list of Frequently Asked Questions, visit our <a class=styled href=/help/faq>FAQ page</a></div></div></section></main><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?><script async src="/assets/js/local/index.js<?= TK_VERSION_STR; ?>"></script>