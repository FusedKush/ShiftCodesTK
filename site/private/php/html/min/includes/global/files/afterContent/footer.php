<footer class=primary-footer id=footer><div class="content-wrapper primary"><div class=social><?php
        $footerLinks = [
          'facebook' => [
            'classes' => [ 'wrapped' ],
            'href'    => 'https://www.facebook.com/ShiftCodesTK',
            'tooltip' => 'ShiftCodesTK on Facebook (External Link)',
            'icon'    => 'fab fa-facebook-square'
          ],
          'twitter' => [
            'classes' => [],
            'href'    => 'https://twitter.com/ShiftCodesTK',
            'tooltip' => 'ShiftCodesTK on Twitter (External Link)',
            'icon'    => 'fab fa-twitter-square'
          ],
          'report' => [
            'classes' => [],
            'href'    => 'https://github.com/FusedKush/ShiftCodesTK/issues',
            'tooltip' => 'Report an Issue (External Link)',
            'icon'    => 'fas fa-bug'
          ]
        ];
      ?><?php foreach ($footerLinks as $link => $props) : ?><div class=layer-container><a class="external-link-icon layer-target<?= " {$link}"; ?>"href="<?= $props['href']; ?>"rel="external noopener"target=_blank><span class="fas external fa-external-link-square-alt"aria-hidden=true></span><span class="icon<?= " {$props['icon']}"; ?>"aria-hidden=true></span></a><div class="layer tooltip<?= " " . implode(" ", $props['classes']); ?>"data-layer-pos=top><?= $props['tooltip']; ?></div></div><?php endforeach; ?></div><div class=info><a class=credit href=/credits><span class="fas fa-code"aria-label=Coded title=Coded></span> with <span class="fas fa-heart"aria-label=Love title=Love></span> by <strong>Zach Vaughan</strong></a><a class=version href=/updates>Version <strong class=num id=footer_ver><?= TK_VERSION; ?></strong></a></div><a class="layer-target return"href=# id=footer_return><span class="fas fa-arrow-alt-circle-up"></span></a><div class="layer tooltip"data-layer-pos=top data-layer-target=footer_return>Return to Top</div></div></footer>