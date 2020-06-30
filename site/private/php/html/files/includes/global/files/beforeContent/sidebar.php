<aside class="sidebar" id="sidebar" data-expanded="false" aria-expanded="false" hidden="true" aria-hidden="true">
  <nav class="panel" aria-label="Sidebar">
    <div class="header">
      <button class="toggle bubble-parent no-focus-scroll" id="sidebar_toggle" title="Close the Sidebar" aria-label="Close the Sidebar">
        <span class="bubble bubble-light"></span>
        <span class="fas fa-bars"></span>
      </button>
      <a class="brand" href="/" title="ShiftCodesTK Home" aria-label="ShiftCodesTK Home">
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
                  <span class="badge new" title="New SHiFT Codes!" aria-label="New SHiFT Codes!"><strong>New!</strong></span>
                <?php endif; ?>
                <?php if ($counts['expiring'] > 0) : ?>
                  <span class="badge expiring" title="Expiring SHiFT Codes!" aria-label="Expiring SHiFT Codes!"><strong>Expiring!</strong></span>
                <?php endif; ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
      <?php endforeach; ?>
      <div class="separator"></div>
      <li>
        <a href="/about-us">
          <span class="fas fa-users"></span>
          <span class="name">About us</span>
        </a>
      </li>
      <li>
        <a href="/credits">
          <span class="fas fa-award"></span>
          <span class="name">Credits</span>
        </a>
      </li>
      <li>
        <a href="/help/">
          <span class="fas fa-question"></span>
          <span class="name">Help Center</span>
        </a>
      </li>
    </ul>
  </nav>
</aside>
