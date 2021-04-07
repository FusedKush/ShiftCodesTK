<footer class="primary-footer" id="footer">
  <div class="primary content-wrapper">
    <div class="social">
      <?php
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
      ?>

      <?php foreach ($footerLinks as $link => $props) : ?>
        <div class="layer-container">
          <a
            class="external-link-icon layer-target <?= " {$link}"; ?>"
            href="<?= $props['href']; ?>"
            rel="external noopener"
            target="_blank">
            <span class="external fas fa-external-link-square-alt" aria-hidden="true"></span>
            <span class="icon <?= " {$props['icon']}"; ?>" aria-hidden="true"></span>
          </a>
          <div class="layer tooltip <?= " " . implode(" ", $props['classes']); ?>" data-layer-pos="top">
            <?= $props['tooltip']; ?>
          </div>
        </div>
      <?php endforeach; ?>
      <!-- End of Footer Links Loop -->
      <!-- <a class="external-link-icon facebook" href="https://www.facebook.com/ShiftCodesTK" rel="external noopener" target="_blank" title="ShiftCodesTK on Facebook (External Link)" aria-label="ShiftCodesTK on Facebook (External Link)"><span class="external fas fa-external-link-square-alt"></span><span class="icon fab fa-facebook-square"></span></a>
      <a class="external-link-icon twitter" href="https://twitter.com/ShiftCodesTK" rel="external noopener" target="_blank" title="ShiftCodesTK on Twitter (External Link)" aria-label="ShiftCodesTK on Twitter (External Link)"><span class="external fas fa-external-link-square-alt"></span><span class="icon fab fa-twitter-square"></span></a>
      <a class="external-link-icon report" href="https://github.com/FusedKush/ShiftCodesTK/issues" rel="external noopener" target="_blank" title="Report an Issue (External Link)" aria-label="Report an Issue (External Link)"><span class="external fas fa-external-link-square-alt"></span><span class="icon fas fa-bug"></span></a> -->
    </div>
    <div class="info">
      <a class="credit" href="/credits">
        <span class="fas fa-code" title="Coded" aria-label="Coded"></span> 
        with 
        <span class="fas fa-heart" title="Love" aria-label="Love"></span> 
        by 
        <strong>Zach Vaughan</strong>
      </a>
      <a class="version" href="/updates">
        Version 
        Version&nbsp;
        <strong class="num" id="footer_ver"><?= TK_VERSION; ?></strong>
      </a>
    </div>
    <a class="return layer-target" id="footer_return" href="#">
      <span class="fas fa-arrow-alt-circle-up"></span>
    </a>
    <div class="layer tooltip" data-layer-target="footer_return" data-layer-pos="top">
      Return to Top
    </div>
  </div>
</footer>
