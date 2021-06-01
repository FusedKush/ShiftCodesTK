<?php use ShiftCodesTK\Strings; ?>
<?php (function () { ?>
  <footer class="primary-footer" id="footer">
    <div class="primary content-wrapper">
      <div class="social">
        <?php
          $footer_links = [
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

        <?php foreach ($footer_links as $footer_link => $link_props) : ?>
          <div class="layer-container">
            <a
              class="external-link-icon layer-target <?= " {$footer_link}"; ?>"
              href="<?= $link_props['href']; ?>"
              rel="external noopener"
              target="_blank">
              <span class="external fas fa-external-link-square-alt" aria-hidden="true"></span>
              <span class="icon <?= " {$link_props['icon']}"; ?>" aria-hidden="true"></span>
            </a>
            <div class="layer tooltip <?= " " . implode(" ", $link_props['classes']); ?>" data-layer-pos="top">
              <?= $link_props['tooltip']; ?>
            </div>
          </div>
        <?php endforeach; ?>
        <!-- End of Footer Links Loop -->
      </div>
      <div class="info">
        <a class="credit" href="/credits">
          <span class="fas fa-code" title="Coded" aria-label="Coded"></span> 
          with 
          <span class="fas fa-heart" title="Love" aria-label="Love"></span> 
          by 
          <strong>Zach Vaughan</strong>
        </a>
        <?php
          $build_commit = \ShiftCodesTK\BUILD_INFORMATION['last_commit']['build'];
          $site_version = ShiftCodesTK\Config::getConfigurationValue('site_version');
          $build_commits = [
            'full_commit'   => $build_commit['commit'],
            'last_commit'   => Strings\slice($build_commit['commit'], 0, 7),
            'full_parent'   => $build_commit['parent'],
            'parent_commit' => isset($build_commit['parent'])
                                ? Strings\slice($build_commit['parent'], 0, 7)
                                : null
          ];
          $build_updated = $build_commit['status'] !== 'ahead-of-remote';
        ?>

        <a class="version" href="#/updates">
          <span class="version-number">
            Version <strong id="footer_ver"><?= $site_version; ?></strong>
            <span id="footer_ver_commit_hash" class="commit-hash layer-target" data-layer-targets="footer_ver_commit_details" aria-label="<?= "Commit Hash {$build_commits['last_commit']}"; ?>">
              <?= $build_commits['last_commit']; ?>
            </span>
          </span>
        </a>
        <div class="layer panel" id="footer_ver_commit_details" data-layer-triggers="none">
          <div class="title">
            <?php if ($build_updated) : ?>
              <a 
                class="commit-hash styled layer-target" 
                href="<?= \ShiftCodesTK\BUILD_INFORMATION['repository'] . "/commit/{$build_commits['full_commit']}"; ?>"
                target="_blank"
                rel="external noopener"
              >
                <?= $build_commits['full_commit']; ?>
              </a>
              <div class="link-notice layer tooltip" data-layer-delay="long">View Commit details on <em>Github</em>.</div>
            <?php else : ?>
              <div class="commit-hash"><?= $build_commits['full_commit']; ?></div>
              <div class="alertbox warning full-width">
                <div class="icon" aria-hidden="true">
                  <span class="fas fa-exclamation-triangle"></span>
                </div>
                <div class="message">
                  <strong>Build Details Unavailable</strong>: Build is currently ahead of the Remote.
                </div>
              </div>
            <?php endif; ?>
          </div>
          <dl class="body">
            <div class="section signature">
              <span class="version">
                <dt hidden="true" aria-hidden="false">Version:</dt>
                <dd class="layer-target">
                  <code>
                    <?php if ($build_updated) : ?>
                      <a 
                        class="styled" 
                        href="<?= \ShiftCodesTK\BUILD_INFORMATION['repository'] . '/tree/' . \ShiftCodesTK\BUILD_INFORMATION['branch']['current_branch']; ?>"
                        target="_blank"
                        rel="external noopener"
                      >
                        <?= $site_version; ?>
                      </a>
                    <?php else : ?>
                      <?= $site_version; ?>
                    <?php endif; ?>
                  </code>
                </dd>
                <div class="layer tooltip" data-layer-delay="long">
                  <span><strong>Site Version</strong>:<?= " {$site_version}"; ?></span>
                  <br>
                  <span><strong>Branch</strong> <?= " " . \ShiftCodesTK\BUILD_INFORMATION['branch']['current_branch']; ?></span>
                  <?php if ($build_updated) : ?>
                    <br>
                    <br>
                    <div class="link-notice">Click to view Branch Details on <em>Github</em>.</div>
                  <?php endif; ?>
                </div>
              </span>
              <span class="timestamp">
                <dt hidden="true" aria-hidden="false">Build Time:</dt>
                <dd class="layer-target" data-relative-date="<?= $build_commit['timestamp']; ?>"></dd>
                <div class="layer tooltip" data-layer-delay="long">
                  <div>
                    <strong>Build Time</strong>: 
                    <br>
                    <?= 
                      (new DateTime($build_commit['timestamp']))
                        ->setTimezone(new DateTimeZone('UTC'))
                        ->format('F d, Y h:i:s A T'); 
                    ?>
                  </div>
                </div>
              </span>
            </div>
            <div class="section timeline">
              <span class="parent">
                <dt hidden="true" aria-hidden="false">Parent:</dt>
                <dd>
                    <code class="layer-target">
                      <?php if (isset($build_commit['parent'])) : ?>
                        <?php if ($build_updated) : ?>
                          <a 
                            class="styled" 
                            href="<?= \ShiftCodesTK\BUILD_INFORMATION['repository'] . "/commit/{$build_commits['full_parent']}"; ?>"
                            target="_blank"
                            rel="external noopener"
                          >
                            <?= $build_commits['parent_commit']; ?>
                          </a>
                        <?php else : ?>
                          <?= $build_commits['parent_commit']; ?>
                        <?php endif; ?>
                      <?php else : ?>
                        <span class="unavailable">N/A</span>
                      <?php endif; ?>
                    </code>
                    <div class="layer tooltip" data-layer-delay="long">
                      <strong>Parent Commit</strong>:
                      <br>
                      <?php if (isset($build_commit['parent'])) : ?>
                        <?= $build_commits['full_parent']; ?>

                        <?php if ($build_updated) : ?>
                          <br>
                          <br>
                          <div class="link-notice">Click to view Commit Details on <em>Github</em>.</div>
                        <?php endif; ?>
                      <?php else : ?>
                        <i>Commit does not have a Parent</i>
                      <?php endif; ?>
                    </div>
                </dd>
              </span>
              <span class="track">
                <span class="status">
                  <?php
                    $status_list = [
                      'up-to-date'      => 'Up to Date',
                      'behind-remote'   => 'Behind Remote',
                      'ahead-of-remote' => 'Ahead of Remote'
                    ];
                  ?>

                  <dt hidden="true" aria-hidden="false">Status:</dt>
                  <dd class="layer-target"><?= $status_list[$build_commit['status']]; ?></dd>
                  <div class="layer tooltip" data-layer-delay="long">
                    <em>Build is currently<?= " {$status_list[$build_commit['status']]}"; ?></em>
                    <br>
                    <br>
                    <dd>
                      <dt><strong>Build</strong>:</dt>
                      <dd><?= "<em>{$build_commits['last_commit']}</em> @ {$build_commit['timestamp']}"; ?></dd>
                      <dt><strong>Remote</strong>:</dt>
                      <dd>
                        <?= 
                          "<em>" . 
                          Strings\slice(\ShiftCodesTK\BUILD_INFORMATION['last_commit']['remote']['commit'], 0, 7) . 
                          "</em> @ " .
                          \ShiftCodesTK\BUILD_INFORMATION['last_commit']['remote']['timestamp']; 
                        ?>
                      </dd>
                    </dd>
                  </div>
                </span>
              </span>
              <span class="commit">
                <dt hidden="true" aria-hidden="false">Commit:</dt>
                <dd>
                  <code class="layer-target">
                    <?php if ($build_updated) : ?>
                      <a 
                        class="styled" 
                        href="<?= \ShiftCodesTK\BUILD_INFORMATION['repository'] . "/commit/{$build_commits['full_commit']}"; ?>"
                        target="_blank"
                        rel="external noopener"
                      >
                        <?= $build_commits['last_commit']; ?>
                      </a>
                    <?php else : ?>
                      <?= $build_commits['last_commit']; ?>
                    <?php endif; ?>
                  </code>
                  <div class="layer tooltip" data-layer-delay="long">
                    <strong>Commit Hash</strong>:
                    <br>
                    <?= $build_commits['full_commit']; ?>

                    <?php if ($build_updated) : ?>
                      <br>
                      <br>
                      <div class="link-notice">Click to view Commit Details on <em>Github</em>.</div>
                    <?php endif; ?>
                  </div>
                </dd>
              </span>
            </div>
            <?php if (isset($build_commit['commit_message']) && !empty($build_commit['commit_message'])) : ?>
              <pre class="section commit-message"><?= $build_commit['commit_message']; ?></pre>
            <?php endif; ?>
          </dl>
        </div>
      </div>
      <a class="return layer-target" id="footer_return" href="#">
        <span class="fas fa-arrow-alt-circle-up"></span>
      </a>
      <div class="layer tooltip" data-layer-target="footer_return" data-layer-pos="top">
        Return to Top
      </div>
    </div>
  </footer>
<?php })(); ?>