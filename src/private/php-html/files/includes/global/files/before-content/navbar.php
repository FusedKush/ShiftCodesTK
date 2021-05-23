<?php
  use ShiftCodesTK\Users\CurrentUser,
      ShiftCodesTK\Strings;
?>

<header class="navbar-container" id="navbar_container" data-at-top="true">
  <div class="loader progress-bar" id="loader_pb" role="progressbar" aria-valuemin="0" aria-valuenow="0" aria-valuemax="100">
    <div class="progress disable-theme-transitions"></div>
  </div>
  <nav class="navbar" id="navbar" aria-label="Navigation Bar">
    <ul class="content-wrapper" role="menubar">
      <li class="left" role="menuitem">
        <button 
          class="btn bubble-parent no-focus-scroll layer-target" 
          id="navbar_sb" 
          aria-label="Site Sidebar" 
          aria-pressed="false"
        >
          <span class="bubble bubble-dynamic" aria-hidden="true"></span>
          <span class="icon">
            <span class="fas fa-bars" aria-hidden="true"></span>
          </span>
        </button>
        <div class="layer tooltip navbar-layer sticky" data-layer-pos="bottom" data-layer-delay="long">
          Site Sidebar
        </div>
      </li>
      <li class="center" role="menuitem">
        <a 
          class="btn bubble-parent no-focus-scroll layer-target" 
          href="/" 
          aria-label="ShiftCodesTK Home"
        >
          <span class="bubble bubble-dynamic" aria-hidden="true"></span>
          <span class="icon">
            <img src="/assets/img/logo.svg?v=1" alt="ShiftCodesTK Logo">
          </span>
        </a>
        <div class="layer tooltip navbar-layer sticky" data-layer-pos="bottom" data-layer-delay="long">
          ShiftCodesTK Home
        </div>
      </li>
      <li class="right" role="menuitem">
        <button 
          class="btn bubble-parent no-focus-scroll layer-target" 
          id="navbar_account_menu" 
          aria-pressed="false" 
          aria-label="My Account"
        >
          <span class="bubble bubble-dynamic" aria-hidden="true"></span>
          <span class="icon">
            <?php
              $current_user = CurrentUser::is_logged_in()
                ? CurrentUser::get_current_user(true)
                : null;
            ?>
            <?php if (isset($current_user)) : ?>
              <?php
                $username = Strings\encode_html($current_user->username);
                $request_token = Strings\encode_html($_SESSION['token']);
                $profile_picture = "/assets/img/users/profiles/{$current_user->user_id}/{$current_user->profile_picture}?_request_token={$request_token}&size=128";
              ?>
              <img 
                alt="<?= $username; ?>'s Profile Picture" 
                src="<?= $profile_picture; ?>">
            <?php else : ?>
              <span class="fas fa-user"></span>
            <?php endif; ?>
          </span>
        </button>
        <div class="layer tooltip sticky navbar-layer" data-layer-pos="bottom" data-layer-delay="long">
          My Account
        </div>
        <div class="layer dropdown sticky navbar-layer auto-toggle" id="navbar_account_menu_dropdown" data-layer-pos="bottom">
        <div class="title">
        <?php if (isset($current_user)) : ?>
          <div 
            class="profile-card" 
            data-card-user="<?= Strings\encode_html(json_encode(CurrentUser::get_current_user()->get_profile_card_data())); ?>"
            data-card-flags="CARD_HIDE_BORDER">
          </div>
        <?php endif; ?>
        </div>
          <ul class="choice-list">
          <!-- Global Stats Modal Toggle -->
          <?php if (isset($_GET['show_global_function_stats'])) : ?>
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
                Statistics related to the&nbsp;<code class="dark-bg">dom</code>&nbsp;&&nbsp;<code class="dark-bg">edit</code>&nbsp;helper functions
              </div>
            </li>
            <div class="separator" aria-hidden="true"></div>
          <?php endif; ?>
          <?php if (isset($current_user)) : ?>
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
              if ($current_user->has_permission('DEVELOPER_TOOLS')) :
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
          <?php else : ?>
            <!-- Login Button -->
            <li>
              <a
                class="choice"
                href="/account/login">
                <span 
                  class="<?= "box-icon fas fa-sign-in-alt"; ?>"
                  aria-hidden="true">
                </span>
                <span class="label">Login</span>
              </a>
            </li>
            <!-- Create Account Button -->
            <li>
              <button class="choice layer-target" disabled>
                <span 
                  class="<?= "box-icon fas fa-user-plus"; ?>"
                  aria-hidden="true">
                </span>
                <span class="label">Create Account</span>
              </a>
            </li>
          <?php endif; ?>
          </ul>
        </div>
      </li>
    </ul>
  </nav>
</header>