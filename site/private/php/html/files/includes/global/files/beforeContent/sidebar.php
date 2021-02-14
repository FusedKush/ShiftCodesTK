<aside class="sidebar" id="sidebar" data-expanded="false" aria-expanded="false" hidden="true" aria-hidden="true">
  <nav class="panel" aria-label="Sidebar">
    <div class="header">
      <button class="toggle bubble-parent no-focus-scroll layer-target" id="sidebar_toggle">
        <span class="bubble bubble-light"></span>
        <span class="fas fa-bars"></span>
      </button>
      <div class="layer tooltip" data-layer-pos="right" data-layer-delay="long">Close the Sidebar</div>
      <a class="brand layer-target" href="/" aria-label="ShiftCodesTK Home">
        <span class="name">ShiftCodes<strong class="focus">TK</strong></span>
      </a>
    </div>
    <ul role="menu">
    <!-- SHiFT Links -->
      <?php
        $links = [
          'bl3' => 'Borderlands 3',
          'bl1' => 'Borderlands: GOTY',
          'bl2' => 'Borderlands 2',
          'tps' => 'Borderlands: TPS'
        ];
      ?>
      <?php 
        /**
         * Generate a new sidebar link
         * 
         * @param string $name The displayed name of the link
         * @param string $link The destination url 
         * @param string $icon The icon classname
         * @param false|string $tooltip A tooltip to be displayed when hovering over the link
         * @return string Returns the new sidebar link markup
         */
        function getSidebarLink ($name, $link, $icon, $tooltip = false) { 
      ?>
        <li>
          <a href="<?= $link; ?>"
             <?php if ($tooltip !== false) { echo ' class="layer-target"'; } ?>
          >
            <span class="<?= $icon; ?>"></span>
            <span class="name"><?= $name; ?></span>
          </a>

          <?php if ($tooltip !== false) : ?>
            <div class="layer tooltip sticky" data-layer-pos="right"><?= $tooltip; ?></div>
          <?php endif; ?>
        </li>
      <?php } ?>
      <!-- End of getSidebarLink -->
      <!-- SHiFT Links -->
      <?php foreach($links as $link => $name) : ?>
        <?php
          $counts = [
            'new' => SHIFT_STATS[$link]['new'],
            'expiring' => SHIFT_STATS[$link]['expiring']
          ];
        ?>
        <li>
          <a class="use-badge" href="/<?= $link; ?>">
            <span class="fas fa-gamepad"></span>
            <span class="name"><?= $name; ?></span>
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
          </a>
        </li>
      <?php endforeach; ?>
      <div class="separator"></div>
      <?php
        getSidebarLink('About us', '/about-us', 'fas fa-users');
        getSidebarLink('Credits', '/credits', 'fas fa-award');
        getSidebarLink('Help Center', '/help/', 'fas fa-question');
      ?>
    </ul>
  </nav>
</aside>
