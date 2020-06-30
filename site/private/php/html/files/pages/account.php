<?php
  $page['meta'] = [
    'title'       => 'My Account - ShiftCodesTK',
    'description' => 'View and Manage your account on ShiftCodesTK',
    'canonical'   => '/account',
    'image'       => 'bl3/2',
    'theme'       => 'main'
  ];
  $page['auth'] = [
    'requireState'   => 'auth'
  ];

  include_once('initialize.php');
  ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <!--// Styles \\-->
    <!-- Shared Styles -->
    <?php include_once('global/sharedStyles.php'); ?>
    <!-- Local Styles -->
    <link href="/assets/css/local/account.css<?php echo TK_VERSION_STR; ?>" rel="stylesheet"></link>
    <!-- <link href="/assets/css/local/shift.css<?php echo TK_VERSION_STR; ?>" rel="stylesheet"></link> -->
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
          <?php
              include_once(HTML_INCLUDES_PATH . 'local/profile-card.php');

              getProfileCard([
                'showStats'   => true,
                'showActions' => true,
                'allowEdit'   => true
              ]);
          ?>
        </div>
        <div class="view" data-view="My Account">
        <h2>My Account</h2>
          <!-- Profile Card -->
          <?php
            include_once(HTML_INCLUDES_PATH . 'local/profile-card.php');

            getProfileCard([
              'showRoles' => false
            ]);
          ?>
          <!-- Password -->
          <div class="section password multi-view" data-view-type="toggle" id="update_password">
            <h3>Change your Password</h3>
            <div class="view" id="update_password_view">
              <p>Change the password you use to log into ShiftCodesTK.</p>
              <button 
                class="styled color light view-toggle" 
                title="Change your account password" 
                aria-label="Change your account password" 
                data-view="update_password_edit">
                Change Password
              </button>
            </div>
            <div class="view" id="update_password_edit">
              <?php
                // require_once(FORMS_PATH . 'account/change-password.php');

                // $updatePasswordForm->insertForm();
              ?>
            </div>
          </div>
          <!-- Login Actions -->
          <div class="section login-actions">
          <?php
            // require_once(FORMS_PATH . 'auth/logout.php');
            // // $logoutForm = new FormBase([
            // //   'name'               => 'logout',
            // //   'action'             => '/assets/requests/forms/auth/logout',
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
            // require_once(FORMS_PATH . 'account/danger-zone.php');

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
    <script async src="/assets/js/global/libs/moment.js<?php echo TK_VERSION_STR; ?>"></script>
    <script async src="/assets/js/local/account.js<?php echo TK_VERSION_STR; ?>"></script>
  </body>
</html>
