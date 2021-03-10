<?php 
  use ShiftCodesTK\Strings;

  $definedSidebarLinks = 0;

  /** Generate a *Sidebar Link*
   * 
   * @param string $link The URL, Path, or Destination of the Link.
   * @param string $name The Name or Content of the Link.
   * @param string $icon The *Classname* of the *Icon* representing the link, including the `fas`, `far`, etc... prefix.
   * @param string|null $tooltip If provided, the content of a *Tooltip* to assign to the Link.
   * @param bool $use_badge Indicates if SHiFT Badges are to be used for the Link.
   * @return string Returns the new *Sidebar Link*.
   */
  function get_sidebar_link (string $link, string $name, string $icon, string $tooltip = null, bool $use_badge = false) {
    GLOBAL $definedSidebarLinks;

    $definedSidebarLinks++;
    $linkID = "sidebar_link_{$definedSidebarLinks}";
    $linkNameID = "{$linkID}_name";
    $linkClasses = implode(' ', [ 
      'link', 
      'no-focus-scroll',
      isset($tooltip) ? 'layer-target' : '',
      isset($use_badge) ? 'use-badge' : ''
    ]);
    $isCurrentLink = $_SERVER['REQUEST_URI'] === $link;
?>
  <li role="menuitem">
    <a 
      class="<?= $linkClasses; ?>"
      id="<?= $linkID; ?>"
      href="<?= Strings\encode_html($link); ?>"
      aria-labelledby="<?= $linkNameID; ?>"
      aria-selected="<?= $isCurrentLink; ?>">
      <span class="<?= "icon " . Strings\encode_html($icon); ?>" aria-hidden="true"></span>
      <span class="name" id="<?= $linkNameID; ?>">
        <?= Strings\encode_html($name); ?>
      </span>

      <?php if ($use_badge) : ?>
        <?php
          $badgeName = Strings\trim($link, Strings\STR_SIDE_LEFT, "/");
          $counts = [
            'new'      => SHIFT_STATS[$badgeName]['new'] ?? 0,
            'expiring' => SHIFT_STATS[$badgeName]['expiring'] ?? 0
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
<?php } ?>
<!-- End of `get_sidebar_link()` -->
<?php function get_sidebar_separator () { ?>
  <div class="separator" role="separator"></div>
<?php } ?>
<!-- End of `get_sidebar_separator()` -->

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
        $currentUser = ShiftCodesTK\Users\CurrentUser::get_current_user();

        // SHiFT Links
        $shiftLinks = [
          'bl3' => 'Borderlands 3',
          'bl1' => 'Borderlands: GOTY',
          'bl2' => 'Borderlands 2',
          'tps' => 'Borderlands: TPS'
        ];

        foreach (ShiftCodes::$GAME_SUPPORT as $gameID => $gameData) {
          get_sidebar_link("/{$gameID}", $gameData['name'], 'fas fa-gamepad', null, true);
        }

        // Other Links
        get_sidebar_separator(); 

        get_sidebar_link('/about-us', 'About us', 'fas fa-users');
        get_sidebar_link('/credits', 'Credit', 'fas fa-award');
        get_sidebar_link('/help/', 'Help Center', 'fas fa-question');
      ?>
    </ul>
  </nav>
</aside>

