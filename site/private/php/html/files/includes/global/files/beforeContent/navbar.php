<?php
  use ShiftCodesTK\Users\CurrentUser,
      ShiftCodesTK\Strings;
?>

<header class="navbar-container">
  <nav class="navbar" id="navbar" aria-label="Navigation Bar" data-at-top="true">
    <div class="loader progress-bar" id="loader_pb" role="progressbar" aria-valuemin="0" aria-valuenow="0" aria-valuemax="100">
      <div class="progress"></div>
    </div>
    <ul class="content-wrapper" role="menubar">
      <div class="left">
        <li role="menuitem">
          <button class="btn bubble-parent no-focus-scroll layer-target" id="navbar_sb" aria-label="Open the Sidebar" data-pressed="false" aria-pressed="false">
            <span class="bubble bubble-dynamic"></span>
            <span class="fas fa-bars"></span>
          </button>
          <div class="layer tooltip navbar-layer sticky" data-layer-pos="bottom">
            Sidebar
          </div>
        </li>
      </div>
      <div class="center">
        <li role="menuitem">
          <a class="btn bubble-parent no-focus-scroll layer-target" href="/" title="ShiftCodesTK Home" aria-label="ShiftCodesTK Home">
            <span class="bubble bubble-dynamic"></span>
            <span class="logo">
              <img src="/assets/img/logo.svg?v=1" alt="ShiftCodesTK Logo">
            </span>
          </a>
          <div class="layer tooltip navbar-layer sticky" data-layer-pos="bottom">
            ShiftCodesTK Home
          </div>
        </li>
      </div>
      <?php if (auth_isLoggedIn()) : ?>
        <div class="right">
          <li role="menuitem">
            <button 
            class="btn bubble-parent no-focus-scroll layer-target" 
            id="navbar_account_menu" 
            title="Account Menu"
            aria-label="Account Menu"
            aria-pressed="false">
            <span class="bubble bubble-dynamic"></span>
            <span class="fas fa-user"></span>
          </button>
            <div class="layer tooltip sticky navbar-layer" data-layer-pos="bottom">
              Your Account
            </div>
            <div class="layer dropdown sticky navbar-layer" id="navbar_account_menu_dropdown" data-layer-pos="bottom">
              <div class="title">
                <?php
                  include_once(HTML_INCLUDES_PATH . 'local/profile-card.php');

                  getProfileCard([
                    'showBorder' => false,
                    'showRoles'  => false
                  ]);
                ?>
                <!-- Global Stats Modal Toggle -->
                <?php if (isset($_GET['show_global_function_stats'])) : ?>
                  <div class="separator" aria-hidden="true"></div>
                  <li>
                    <button
                      class="choice layer-target modal-toggle auto-toggle"
                      data-modal="global_function_stats_modal">
                      <span 
                        class="<?= "box-icon fas fa-clock"; ?>"
                        aria-hidden="true">
                      </span>
                      <span class="label">Function Stats</span>
                    </button>
                    <div class="layer tooltip" data-layer-pos="left" data-layer-delay="medium">
                      Statistics related to the&nbsp;<code>dom</code>&nbsp;&&nbsp;<code>edit</code>&nbsp;helper functions
                    </div>
                  </li>
                <?php endif; ?>

                <?php foreach ($items as $item) : ?>
                  <li>
                    <a
                      class="choice layer-target"
                      href="<?= $item['link']; ?>">
                      <span 
                        class="<?= "box-icon {$item['icon']}"; ?>"
                        aria-hidden="true">
                      </span>
                      <span class="label"><?= $item['name']; ?></span>
                    </a>
                    <div class="layer tooltip" data-layer-pos="left" data-layer-delay="medium">
                      <?= $item['title']; ?>
                    </div>
                  </li>
                <?php endforeach; ?>
                <!-- End of items loop -->
                <li>
                  <?php
                    include(FORMS_PATH . 'auth/logout.php');

                    $form_authLogout->insertForm();
                  ?>
                </li>
              </ul>
            </div>
          </li>
        </div>
      <?php endif; ?>
    </ul>
  </nav>
</header>
