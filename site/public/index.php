<?php 
  $page['meta'] = [
    'title'       => 'ShiftCodesTK',
    'description' => 'SHiFT Codes for Borderlands',
    'canonical'   => '',
    'image'       => 'bl2/2',
    'theme'       => 'main'
  ]; 

  require_once('initialize.php');

  if (isset($_GET['update_code_ids']) && $_GET['update_code_ids'] == 1) {
    $codes = (function () use (&$_mysqli) {
      $query = "SELECT code_id
                FROM shift_codes
                WHERE 1";

      return $_mysqli->query($query, [ 'collapseAll' => true ]);
    })();
    $newIDs = (function () use ($codes) {
      $IDs = [];
      $i = 0;

      while (true) {
        $newID = auth_randomMetaID(10, '11');
  
        if (array_search($newID, $IDs)) {
          continue;
        }
        else {
          $IDs[] = [ $newID, $codes[$i] ];

          if (++$i == count($codes)) {
            break;
          }
          else if ($i % 5 == 0) {
            sleep(2);
          }
        }
      }

      return $IDs;
    })();

    var_dump($newIDs);
    (function () use (&$_mysqli, $newIDs) {
      $query = "UPDATE shift_codes
                SET code_id = ?
                WHERE code_id = ?";
      $result = $_mysqli->prepared_query($query, 'ss', $newIDs, [ 'collapseAll' => true ]);

      var_dump($result);
    })();
  }
  if (isset($_GET['update_code_hashes']) && $_GET['update_code_hashes'] == 1) {
    $codes = (function () use (&$_mysqli) {
      $query = "SELECT code_id, code_pc, code_xbox, code_ps
                FROM shift_codes
                WHERE 1";

      return $_mysqli->query($query, [ 'collapseAll' => true ]);
    })();
    $newHashes = (function () use ($codes) {
      $hashes = [];
      $i = 0;

      while (true) {
        $newHash = auth_randomMetaID(10, '12');
  
        if (array_search($newHash, $hashes)) {
          continue;
        }
        else {
          $hashes['shift_code_hashes'][] = [ $newHash, auth_strHash("{$codes[$i]['code_pc']} {$codes[$i]['code_xbox']} {$codes[$i]['code_ps']}") ];
          $hashes['shift_codes'][] = [ $newHash, $codes[$i]['code_id'] ];

          if (++$i == count($codes)) {
            break;
          }
          else if ($i % 5 == 0) {
            sleep(2);
          }
        }
      }

      return $hashes;
    })();

    var_dump($newHashes);
    (function () use (&$_mysqli, $newHashes) {
      $query1 = "DELETE FROM shift_code_hashes
                 WHERE 1";
      $query2 = "INSERT INTO shift_code_hashes
                 (hash_id, code_hash)
                 VALUES (?, ?)";
      $result1 = $_mysqli->query($query1, [ 'collapseAll' => true ]);
      $result2 = $_mysqli->prepared_query($query2, 'ss', $newHashes['shift_code_hashes'], [ 'collapseAll' => true ]);

      var_dump($result1, $result2);
    })();
    (function () use (&$_mysqli, $newHashes) {
      $query = "UPDATE shift_codes
                SET code_hash = ?
                WHERE code_id = ?
                LIMIT 1";
      $result = $_mysqli->prepared_query($query, 'ss', $newHashes['shift_codes'], [ 'collapseAll' => true ]);

      var_dump($result);
    })();
  }
  if (isset($_GET['update_user_ids']) && $_GET['update_user_ids'] == 1) {
    $users = (function () use (&$_mysqli) {
      $query = "SELECT user_id
                FROM auth_users
                WHERE 1";

      return $_mysqli->query($query, [ 'collapseAll' => true ]);
    })();
    $newIDs = (function () use ($users) {
      $IDs = [];
      $i = 0;

      while (true) {
        $newID = auth_randomMetaID(10, '14');
  
        if (array_search($newID, $IDs)) {
          continue;
        }
        else {
          $IDs[] = [ $newID, $newID, $newID, $newID, $newID, $newID, $users[$i] ];

          if (++$i == count($users)) {
            break;
          }
          else {
            sleep(2);
          }
        }
      }

      return $IDs;
    })();

    var_dump($newIDs);
    (function () use (&$_mysqli, $newIDs) {
      $query = "UPDATE auth_users as au
                LEFT JOIN auth_failed_logins as afl ON afl.user_id = au.user_id
                LEFT JOIN auth_records as ar ON ar.user_id = au.user_id
                LEFT JOIN auth_tokens as att ON att.user_id = au.user_id
                LEFT JOIN logs_auth_throttles as lat ON lat.user_id = au.user_id
                LEFT JOIN shift_codes as sc ON sc.owner_id = au.user_id
                SET au.user_id = ?,
                  afl.user_id = ?,
                  ar.user_id = ?,
                  att.user_id = ?,
                  lat.user_id = ?,
                  sc.owner_id = ?
                WHERE au.user_id = ?";
      $result = $_mysqli->prepared_query($query, 'sssssss', $newIDs, [ 'collapseAll' => true ]);

      var_dump($result);
    })();
  }
  if (isset($_GET['update_shift_code_platforms']) && $_GET['update_shift_code_platforms'] == 1) {
    require_once(SCRIPTS_INCLUDES_PATH . 'shift_constants.php');
    $dbPlatforms = [];
 
    foreach (SHIFT_CODE_PLATFORMS as $category => $platform) {
      $query = "SELECT platforms_${category}
                FROM shift_codes
                WHERE 1";
      $result = $_mysqli->query($query, [ 'collapseAll' => true ]);
      $dbPlatforms[$category] = array_unique($result);
    }
    foreach ($dbPlatforms as $dbCategory => $categoryPlatforms) {
      $query = "UPDATE shift_codes
                SET platforms_${dbCategory} = ?
                WHERE platforms_${dbCategory} = ?";
      $params = [];

      foreach ($categoryPlatforms as $categoryPlatform) {
        $newPlatform = (function () use ($dbCategory, $categoryPlatform) {
          $newPlatforms = explode(' / ', $categoryPlatform);

          foreach ($newPlatforms as &$newPlatform) {
            foreach (SHIFT_CODE_PLATFORMS[$dbCategory] as $name => $platform) {
              if ($platform['display_name'] == $newPlatform) {
                $newPlatform = $name;
                continue 2;
              }
            }
          }

          return implode('/', $newPlatforms);
        })();

        $params[] = [ $newPlatform, $categoryPlatform ];
      }

      var_dump($_mysqli->prepared_query($query, 'ss', $params, [ 'collapseAll' => true ]));
    }
  }
?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/css/local/index.css<?= TK_VERSION_STR; ?>"rel=stylesheet><meta content=dmsrwqOh26nDUBkS9sCSJ4rblI5g363hbCNhvr-nW8s name=google-site-verification><?php include_once('global/head.php'); ?><body data-theme=main><?php include_once('global/beforeContent.php'); ?><main class=no-header><?php 
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
      ?><?php function indexButtonMarkup ($game, $props, $counts, $sectionButton = false) { ?><?php $title = "SHiFT Codes for {$props['long-string']}"; ?><a class="button class-theme color color-on-hover layer-target<?= " $game"; ?>"href="/<?= $game; ?>"data-long-string="<?= $props['long-string']; ?>"data-string="<?= $props['string']; ?>"><span><?php
              if (!$sectionButton) { echo strtoupper($game); } 
              else                 { echo $props['string']; }
            ?></span><?php if ($counts['new'] > 0 || $counts['expiring'] > 0) : ?><div class=flags><?php if ($counts['new'] > 0) : ?><div class="flag new"aria-label="New SHiFT Codes!"title="New SHiFT Codes!"><span class="fas fa-star"></span></div><?php endif; ?><?php if ($counts['expiring'] > 0) : ?><div class="flag expiring"aria-label="Expiring SHiFT Codes!"title="Expiring SHiFT Codes!"><span class="fas fa-exclamation-triangle"></span></div><?php endif; ?></div><?php endif; ?></a><div class="layer tooltip"data-layer-delay=medium data-layer-triggers=focus><?= $title; ?></div><?php }; ?><section class=main data-webp='{"path": "/assets/img/banners/bl3/2", "alt": ".jpg", "type": "bg"}'><div class=content-wrapper><div class=brand><img alt="ShiftCodesTK Logo"class=logo src=/assets/img/logo.svg width=3.5em><h1 class=name>ShiftCodesTK</h1><div class=tagline>Less time Scrolling, More time Gaming</div></div><div class=action><h2 class=string>SHiFT Codes for <span class="bl3 chosen selected">Borderlands 3</span></h2><div class=link-container><?php foreach ($games as $game => $props) : ?><?php
                  $counts = [
                    'new' => SHIFT_STATS[$game]['new'],
                    'expiring' => SHIFT_STATS[$game]['expiring']
                  ];
                ?><?= indexButtonMarkup($game, $props, $counts); ?><?php endforeach; ?></div></div></div></section><?php foreach ($games as $game => $props) : ?><?php
          $counts = [
            'new' => SHIFT_STATS[$game]['new'],
            'expiring' => SHIFT_STATS[$game]['expiring']
          ];
        ?><section class="secondary<?= " $game"; ?>"data-webp='{"path": "/assets/img/banners/<?= $game; ?>/1", "alt": ".jpg", "type": "bg"}'><div class=content-wrapper><div class=intro><h2 class=title><?= $props['string']; ?></h2><i class=quote><?= $props['quote']; ?></i></div><?= indexButtonMarkup($game, $props, $counts, true); ?></div></section><?php endforeach; ?><section class=faq><div class=content-wrapper><h2 class=title>Frequently Asked Questions</h2><div class=questions><div class="c dropdown-panel"><h3 class=primary>What is SHiFT?</h3><div class=body><p>SHiFT is a service created by Gearbox to reward their players with in-game loot and special events.</div></div><div class="c dropdown-panel"><h3 class=primary>What are SHiFT Codes?</h3><div class=body><p>SHiFT Codes are 25-character keys that grant in-game rewards.</div></div><div class="c dropdown-panel"><h3 class=primary>How often are SHiFT Codes released?</h3><div class=body><p>SHiFT Codes are typically released every Friday around 10AM PST.</div></div></div><div class=link>For the full list of Frequently Asked Questions, visit our <a class=themed href=/help/faq>FAQ page</a></div></div></section></main><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?><script async src="/assets/js/local/index.js<?= TK_VERSION_STR; ?>"></script>