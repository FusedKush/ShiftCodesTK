<?php
  use ShiftCodesTK\Users\CurrentUser,
      ShiftCodesTK\Strings;
?>

<header class="navbar-container">
  <nav class="navbar" id="navbar" aria-label="Navigation Bar" data-at-top="true">
    <div class="loader progress-bar" id="loader_pb" role="progressbar" aria-valuemin="0" aria-valuenow="0" aria-valuemax="100">
      <div class="progress disable-theme-transitions"></div>
    </div>
    <ul class="content-wrapper" role="menubar">
      <div class="left">
        <li role="menuitem">
          <button class="btn bubble-parent no-focus-scroll layer-target" id="navbar_sb" data-pressed="false" aria-pressed="false">
            <span class="bubble bubble-dynamic"></span>
            <span class="fas fa-bars"></span>
          </button>
          <div class="layer tooltip navbar-layer sticky" data-layer-pos="bottom" data-layer-delay="long">
            Open the Sidebar
          </div>
        </li>
      </div>
      <div class="center">
        <li role="menuitem">
          <a class="btn bubble-parent no-focus-scroll layer-target" href="/">
            <span class="bubble bubble-dynamic"></span>
            <span class="logo">
              <img src="/assets/img/logo.svg?v=1" alt="ShiftCodesTK Logo">
            </span>
          </a>
          <div class="layer tooltip navbar-layer sticky" data-layer-pos="bottom" data-layer-delay="long">
            ShiftCodesTK Home
          </div>
        </li>
      </div>
      <?php if (CurrentUser::is_logged_in()) : ?>
        <div class="right">
          <li role="menuitem">
            <button 
            class="btn bubble-parent no-focus-scroll layer-target" 
            id="navbar_account_menu" 
            aria-pressed="false">
              <span class="bubble bubble-dynamic"></span>
              <span class="fas fa-user"></span>
            </button>
            <div class="layer tooltip sticky navbar-layer" data-layer-pos="bottom" data-layer-delay="long">
              Open the Account Menu
            </div>
            <div class="layer dropdown sticky navbar-layer auto-toggle" id="navbar_account_menu_dropdown" data-layer-pos="bottom">
              <div class="title">
                <div 
                  class="profile-card" 
                  data-card-user="<?= Strings\encode_html(json_encode(CurrentUser::get_current_user()->get_profile_card_data())); ?>"
                  data-card-flags="CARD_HIDE_BORDER">
                </div>
              </div>
              <ul class="choice-list">
                <!-- New Code -->
                <li>
                  <a
                    class="choice layer-target"
                    href="/codes/new">
                    <span 
                      class="<?= "box-icon fas fa-plus"; ?>"
                      aria-hidden="true">
                    </span>
                    <span class="label">New Code</span>
                  </a>
                  <div class="layer tooltip" data-layer-pos="left" data-layer-delay="medium">
                    Submit a new SHiFT Code to ShiftCodesTK
                  </div>
                </li>
                <!-- My Codes -->
                <li>
                  <a
                    class="choice layer-target"
                    href="/codes/">
                    <span 
                      class="<?= "box-icon fas fa-list-alt"; ?>"
                      aria-hidden="true">
                    </span>
                    <span class="label">My Codes</span>
                  </a>
                  <div class="layer tooltip" data-layer-pos="left" data-layer-delay="medium">
                    Your submitted SHiFT Codes
                  </div>
                </li>
                <!-- Developer Playground -->
                <?php
                  $currentUser = \ShiftCodesTK\Users\CurrentUser::get_current_user();
                    
                  if ($currentUser && $currentUser->has_permission('DEVELOPER_TOOLS')) :
                ?>
                  <div class="separator" aria-hidden="true"></div>
                  <li>
                    <a
                      class="choice layer-target"
                      href="/dev/playground">
                      <span 
                        class="<?= "box-icon fas fa-laptop-code"; ?>"
                        aria-hidden="true">
                      </span>
                      <span class="label">Dev Playground</span>
                    </a>
                    <div class="layer tooltip" data-layer-pos="left" data-layer-delay="medium">
                      The&nbsp;<em>Developer Playground</em>&nbsp;is a testing ground for element styles & functionality.
                    </div>
                  </li>
                <?php endif; ?>
                <!-- End of `DEVELOPER_TOOLS` permission check -->

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

                <div class="separator" aria-hidden="true"></div>
                <!-- My Account -->
                <li>
                  <a
                    class="choice layer-target"
                    href="/account/">
                    <span 
                      class="<?= "box-icon fas fa-user"; ?>"
                      aria-hidden="true">
                    </span>
                    <span class="label">My Account</span>
                  </a>
                  <div class="layer tooltip" data-layer-pos="left" data-layer-delay="medium">
                    View and manage your ShiftCodesTK Account
                  </div>
                </li>
                <!-- Logout Button -->
                <li>
                  <?php
                    include(\ShiftCodesTK\Paths\PHP_PATHS['forms'] . '/auth/logout.php');

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
