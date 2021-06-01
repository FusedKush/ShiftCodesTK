<?php 
  use ShiftCodesTK\Strings;
?>

<?php (function () { ?>
  <?php $get_sidebar_link = function (
    string $link,
    string $title,
    string $icon,
    string $tooltip = null,
    bool $use_badge = false
  ) { ?>
    <?php
      $link_id = Strings\encode_id(
        Strings\encode_html("sidebar_link_{$title}")
      );
      $link_name_id = "{$link_id}_name";
      $link_classes = (function () use ($tooltip, $use_badge) {
        $classes = [
          'link',
          'no-focus-scroll'
        ];

        if (isset($tooltip)) {
          $classes[] = 'layer-target';
        }
        if ($use_badge) {
          $classes[] = 'use-badge';
        }

        return Strings\encode_html(
          implode(' ', $classes)
        );
      })();
      $is_current_link = (function () use ($link) {
        $current_page = \ShiftCodesTK\Router::newRouter()
          ->getRequestProperties()
          ->getRequestInfo('resourcePath');

        return $current_page === $link;
      })();
    ?>
    <li role="menuitem">
      <a 
        class="<?= $link_classes; ?>"
        id="<?= $link_id; ?>"
        href="<?= Strings\encode_html($link); ?>"
        aria-labelledby="<?= $link_name_id; ?>"
        aria-selected="<?= $is_current_link; ?>">
        <span class="<?= "icon " . Strings\encode_html($icon); ?>" aria-hidden="true"></span>
        <span class="name" id="<?= $link_name_id; ?>">
          <?= Strings\encode_html($title); ?>
        </span>

        <?php if ($use_badge) : ?>
          <?php
            $badge_name = Strings\trim($link, Strings\STR_SIDE_LEFT, "/");
            $counts = [
              'new'      => SHIFT_STATS[$badge_name]['new'] ?? 0,
              'expiring' => SHIFT_STATS[$badge_name]['expiring'] ?? 0
            ];
          ?>

          <?php if ($counts['new'] > 0 || $counts['expiring'] > 0) : ?>
            <span class="badges">
              <?php if ($counts['new'] > 0) : ?>
                <span class="badge new layer-target"><strong>New!</strong></span>
                <div class="layer tooltip" data-layer-pos="right">New SHiFT Codes!</div>
              <?php endif; ?>
              <?php if ($counts['expiring'] > 0) : ?>
                <span class="badge expiring layer-target"><strong>Expiring!</strong></span>
                <div class="layer tooltip" data-layer-pos="right">Expiring SHiFT Codes!</div>
              <?php endif; ?>
            </span>
          <?php endif; ?>
          <!-- End of Badge Count Conditional -->
        <?php endif; ?>
        <!-- End of SHiFT Badge Condition -->
      </a>
      <?php if (isset($tooltip)) : ?>
        <div class="layer tooltip" data-layer-pos="right">
          <?= Strings\encode_html($tooltip); ?>
        </div>
      <?php endif; ?>
      <!-- End of Tooltip Conditional -->
    </li>
  <?php }; // End of `$get_sidebar_link` ?>
  <?php $get_sidebar_separator = function () { ?>
    <div class="separator" role="separator"></div>
  <?php }; // End of `$get_sidebar_link` ?>

  <aside class="sidebar" id="sidebar" aria-expanded="false" hidden>
    <nav class="panel" aria-label="Sidebar">
      <!-- Header -->
      <div class="header">
        <button class="toggle bubble-parent no-focus-scroll layer-target" id="sidebar_toggle">
          <span class="bubble bubble-light"></span>
          <span class="fas fa-bars" aria-hidden="true"></span>
        </button>
        <div class="layer tooltip" data-layer-pos="right" data-layer-delay="long">Close the Sidebar</div>
        <a class="brand" href="/" aria-label="ShiftCodesTK Home">
          <span class="name">ShiftCodes<strong class="emphasis">TK</strong></span>
        </a>
      </div>
      <!-- Body -->
      <ul role="menu">
        <?php 
          $current_user = ShiftCodesTK\Users\CurrentUser::get_current_user();

          foreach (ShiftCodes::$GAME_SUPPORT as $game_id => $game_info) {
            $get_sidebar_link("/{$game_id}", $game_info['name'], 'fas fa-gamepad', null, true);
          }

          // Other Links
          $get_sidebar_separator(); 

          $get_sidebar_link('/about-us', 'About us', 'fas fa-users');
          $get_sidebar_link('/credits', 'Credits', 'fas fa-award');
          $get_sidebar_link('/help/', 'Help Center', 'fas fa-question');
        ?>
      </ul>
    </nav>
  </aside>
<?php })(); ?>
