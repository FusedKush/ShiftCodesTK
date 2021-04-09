<?php
  use ShiftCodesTK\Users\CurrentUser,
      ShiftCodesTK\Strings;

  $page['meta'] = [
    'title'       => 'My Account - ShiftCodesTK',
    'description' => 'View and Manage your account on ShiftCodesTK',
    'canonical'   => '/account/',
    'image'       => 'bl3/2',
    'theme'       => 'main'
  ];
  $page['auth'] = [
    'requireState'   => 'auth'
  ];

  include_once('../initialize.php');
  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/css/local/account.css<?php echo \ShiftCodesTK\VERSION_QUERY_STR; ?>" rel="stylesheet"></link>
    <!--// Metadata \\-->
    <?php include_once('global/head.php'); ?>
  </head>
  <body data-theme="main">
    <!--// Before-Content Imports \\-->
    <?php include_once('global/beforeContent.php'); ?>
    <!-- Main Header -->
    <?php include_once('global/main-header.php'); ?>
    <!-- Main Content -->
    <main class="content-wrapper">
      <div class="multi-view" id="account" data-view-type="tabs">
        <div class="view" data-view="My Profile">
          <h2>My Profile</h2>
          <div 
            class="profile-card" 
            data-card-user="<?= Strings\encode_html(json_encode(CurrentUser::get_current_user()->get_profile_card_data())); ?>"
            data-card-flags="CARD_SHOW_ROLES|CARD_SHOW_STATS|CARD_SHOW_ACTIONS|CARD_ALLOW_EDITING">
          </div>
        </div>
        <div class="view" data-view="My Account">
        <h2>My Account</h2>
          <!-- Profile Card -->
          <div 
            class="profile-card" 
            data-card-user="<?= Strings\encode_html(json_encode(CurrentUser::get_current_user()->get_profile_card_data())); ?>">
          </div>
          <!-- Account Standing -->
          <div class="section account-standing" data-reputation="great">
            <h3>Account Standing</h3>
            <div class="reputation-bar">
              <span class="label" aria-label="Poor Account Standing">😢</span>
              <span class="progress-bar">
                <span class="progress" style="width: 100%;"></span>
              </span>
              <span class="label" aria-label="Great Account Standing">😎</span>
            </div>
            <p>Your account is currently in a&nbsp;<span class="reputation">great standing</span>! You have no recent enforcement actions against your account.</p>
            <!-- <p>Your account is currently in a&nbsp;<span class="reputation">moderate standing</span>. You have one recent enforcement action against your account. If further enforcement actions are taken against you, you may be&nbsp;<strong>permanently suspended</strong>&nbsp;from ShiftCodesTK.</p> -->
            <!-- <p>Your account is currently in a&nbsp;<span class="reputation">poor standing</span>. You have two recent enforcement actions against your account. If you have another enforcement action taken against you, you may be&nbsp;<strong>permanently suspended</strong>&nbsp;from ShiftCodesTK.</p>
            <div class="dropdown-panel c">
              <div class="icon fas fa-gavel"></div>
              <div class="primary">Jan 30, 2021</div>
              <div class="secondary">Recent Enforcement Action</div>
              <div class="body">
                <dl>
                  <div class="item">
                    <dt>Enforcement Date</dt>
                    <dd>January 30, 2021</dd>
                  </div>
                  <div class="item">
                    <dt>Enforcement Duration</dt>
                    <dd>3 Days (February 03, 2021)</dd>
                  </div>
                  <div class="item">
                    <dt>Enforcement Category</dt>
                    <dd>Inappropriate Username</dd>
                  </div>
                </dl>
                <p class="description">Your previous username did not meet our community standards and has been removed.</p>
              </div>
            </div> -->
          </div>
          <!-- Password -->
          <div class="section password multi-view" data-view-type="toggle" id="update_password">
            <h3>Change your Password</h3>
            <div class="view" id="update_password_view">
              <p>Change the password you use to log into ShiftCodesTK.</p>
              <button 
                class="styled view-toggle" 
                title="Change your account password" 
                aria-label="Change your account password" 
                data-view="update_password_edit">
                Change Password
              </button>
            </div>
            <div class="view" id="update_password_edit">
              <?php
                require_once(\ShiftCodesTK\Paths\PHP_PATHS['forms'] . '/account/change-password.php');

                $form_changePassword->insertForm();
              ?>
            </div>
          </div>
          <!-- Login Actions -->
          <div class="section login-actions">
          <?php
            // require_once(\ShiftCodesTK\Paths\PHP_PATHS['forms'] . 'auth/logout.php');
            // // $logoutForm = new FormBase([
            // //   'name'               => 'logout',
            // //   'action'             => '/api/forms/auth/logout',
            // //   'result'             => [
            // //     'redirect' => [
            // //       'enabled'  => true,
            // //       'location' => 'reload'
            // //     ]
            // //   ],
            // //   'footer' => 
            // // ]);
            // $logoutForm->updateProperty('title', 'Log Out');
            // $logoutButton->updateProperty('label', 'Logout');
            // $logoutButton->updateProperty('subtitle', 'Log out of your ShiftCodesTK Account');
            // // $logoutForm->addChild('field', [
            // //   'name' => 'logout',
            // //   'label' => 'Logout',
            // //   'subtitle' => 'Log out of your ShiftCodesTK Account.',
            // //   'input' => 'submit',
            // //   'content' => 'Logout'
            // // ]);
            // $logoutChoices->addChild('field', [
            //   // 'name' => 'logout',
            //   'value' => 'elsewhere',
            //   'label' => 'Logout Elsewhere',
            //   'subtitle' => 'Log out of your account on all authorized devices, except for this one.',
            //   'content' => 'Logout Elsewhere',
            //   'input' => 'submit',
            //   'customClasses' => [ 'color', 'warning' ]
            // ]);
            // $logoutChoices->addChild('field', [
            //   // 'name' => 'logout',
            //   'value' => 'everywhere',
            //   'label' => 'Logout Everywhere',
            //   'subtitle' => 'Log out of your account on all authorized devices, including this one.',
            //   'content' => 'Logout Everywhere',
            //   'input' => 'submit',
            //   'customClasses' => [ 'color', 'warning' ]
            // ]);
            // $logoutForm->updateProperty('footer', [
            //   'use' => false
            // ]);
            // $logoutForm->insertForm();
          ?>
          </div>
          <!-- Danger Zone -->
          <div class="section danger-zone">
          <?php
            // require_once(\ShiftCodesTK\Paths\PHP_PATHS['forms'] . 'account/danger-zone.php');

            // // $dangerZoneForm->updateProperty('footer', [
            // //   'submit' => [
            // //     'name'  => 'Delete My Account',
            // //     'label' => 'Delete your ShiftCodesTK account. This cannot be reversed.',
            // //     'class' => [ 'color', 'danger' ]
            // //   ]
            // // ]);
            // // $dangerZoneForm->updateProperty('footer', [
            // //   'submit' => [
            // //     'name'  => 'Delete Everything',
            // //     'label' => 'Delete your ShiftCodesTK account and all of your submitted SHiFT Codes. This cannot be reversed.',
            // //     'class' => [ 'color', 'danger' ]
            // //   ]
            // // ]);
            // $dangerZoneForm->updateProperty('footer', [ 'use' => false ]);
            // // $confirmation->updateProperty('hidden', true);

            // $dangerZoneForm->insertForm();
          ?>
          </div>
        </div>
      </div>
    </main>
    <!--// After-Content Imports \\-->
    <?php include_once('global/afterContent.php'); ?>
    <!--// Scripts \\-->
    <!-- Shared Scripts -->
    <?php include_once('global/sharedScripts.php'); ?>
    <!-- Local Scripts -->
    <script async src="/assets/js/local/account.js<?php echo \ShiftCodesTK\VERSION_QUERY_STR; ?>"></script>
  </body>
</html>
