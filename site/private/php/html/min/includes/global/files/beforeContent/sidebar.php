<aside aria-expanded=false aria-hidden=true class=sidebar data-expanded=false hidden id=sidebar><nav aria-label=Sidebar class=panel><div class=header><button aria-label="Close the Sidebar"class="bubble-parent no-focus-scroll toggle"id=sidebar_toggle title="Close the Sidebar"><span class="bubble bubble-light"></span><span class="fas fa-bars"></span></button><a href=/ class=brand aria-label="ShiftCodesTK Home"title="ShiftCodesTK Home"><span class=name>ShiftCodes<strong class=focus>TK</strong></span></a></div><ul role=menu><?php
        $links = [
          'bl3' => 'Borderlands 3',
          'bl1' => 'Borderlands: GOTY',
          'bl2' => 'Borderlands 2',
          'tps' => 'Borderlands: TPS'
        ];
      ?><?php foreach($links as $link => $name) : ?><?php
          $counts = [
            'new' => SHIFT_STATS[$link]['new'],
            'expiring' => SHIFT_STATS[$link]['expiring']
          ];
        ?><li><a href="/<?= $link; ?>"class=use-badge><span class="fas fa-gamepad"></span><span class=name><?= $name; ?></span><?php if ($counts['new'] > 0 || $counts['expiring'] > 0) : ?><span class=badges><?php if ($counts['new'] > 0) : ?><span class="badge new"aria-label="New SHiFT Codes!"title="New SHiFT Codes!"><strong>New!</strong></span><?php endif; ?><?php if ($counts['expiring'] > 0) : ?><span class="badge expiring"aria-label="Expiring SHiFT Codes!"title="Expiring SHiFT Codes!"><strong>Expiring!</strong></span><?php endif; ?></span><?php endif; ?></a></li><?php endforeach; ?><div class=separator></div><li><a href=/about-us><span class="fas fa-users"></span><span class=name>About us</span></a><li><a href=/credits><span class="fas fa-award"></span><span class=name>Credits</span></a><li><a href=/help/ ><span class="fas fa-question"></span><span class=name>Help Center</span></a></ul></nav></aside>