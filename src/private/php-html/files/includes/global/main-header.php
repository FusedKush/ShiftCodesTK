<?php
  $page['meta']['path'] = preg_replace('/(.php|index)/', '', $_SERVER['SCRIPT_NAME']);
?>
<header class="main" id="primary_header">
  <div class="intro" data-webp='{"path": "/assets/img/banners/<?= PAGE_SETTINGS['meta']['image']; ?>", "alt": ".jpg", "type": "bg"}'>
    <div class="content-container">
      <div class="content-wrapper">
        <div class="content">
          <h1 class="title"><?= str_replace(' - ShiftCodesTK', '', PAGE_SETTINGS['meta']['title']); ?></h1>
          <div class="description"><?= PAGE_SETTINGS['meta']['description']; ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="breadcrumbs ready">
    <div class="content-wrapper">
      <?php function addCrumb($matches) { ?>
        <?php
          $match = $matches[0];

          $fullPath = PAGE_SETTINGS['meta']['path'];

          if ($match != '/') {
            $link = (function () use ($match, $fullPath) {
              $results = [];

              preg_match("@.+{$match}(/|$)@", $fullPath, $results);

              return isset($results[0]) ? $results[0] : '/';
            })();
            $path = (function () use ($match, $link) {
              $p = $_SERVER['DOCUMENT_ROOT'] . $link;

              // Directly reference index page
              if (preg_match('|/$|', $p)) {
                $p .= 'index';
              }

              return "$p.php";
            })();
            $title = (function () use ($match, $path) {
              $file = new SplFileObject($path);
              $line = 1;

              while(true) {
                $file->seek($line - 1);
                $content = preg_replace('/\s+/', ' ', $file->current());
                $results = [];

                preg_match("/'title' \=\> '(.+)'/", $content, $results);

                if ($results) {
                  return str_replace(' - ShiftCodesTK', '', $results[1]);
                }
                else if ($line > 20) {
                  return ucwords($match);
                }
                else {
                  $line++;
                }
              }
            })();
          }
          else {
            $link = $path = $title = '/';
          }
        ?>
        <?php if ($match == '/') : ?>
          <b class="separator"><?= $title; ?></b>

        <?php elseif ($match == basename($fullPath)) : ?>
          <b class="crumb" title="<?= $title; ?>" aria-label="<?= $title; ?>"><?= $title; ?></b>

        <?php else : ?>
          <a class="crumb tr-underline" title="<?= $title; ?>" aria-label="<?= $title; ?>" href="<?= $link; ?>"><?= $title; ?></a>
        <?php endif ?>
      <?php } ?>
      <a class="crumb tr-underline fas fa-home" title="Home" aria-label="Home" href="/"></a>
      <?php 
        preg_replace_callback('#[^\r\n/]+|\/#', function ($matches) {
          return addCrumb($matches);
        }, preg_replace('|/$|', '', PAGE_SETTINGS['meta']['path']));
      ?>
    </div>
  </div>
</header>
