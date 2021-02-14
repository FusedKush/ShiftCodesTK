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
  ?><!doctype html><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/css/local/account.css<?php echo TK_VERSION_STR; ?>" rel=stylesheet><?php include_once('global/head.php'); ?><body data-theme=main><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><main class=content-wrapper><div class=multi-view id=account data-view-type=tabs><div class=view data-view="My Profile"><h2>My Profile</h2><div class=profile-card data-card-user="<?= Strings\encode_html(json_encode(CurrentUser::get_current_user()->get_profile_card_data())); ?>" data-card-flags=CARD_SHOW_ROLES|CARD_SHOW_STATS|CARD_SHOW_ACTIONS|CARD_ALLOW_EDITING></div></div><div class=view data-view="My Account"><h2>My Account</h2><div class=profile-card data-card-user="<?= Strings\encode_html(json_encode(CurrentUser::get_current_user()->get_profile_card_data())); ?>"></div><div class="section account-standing" data-reputation=great><h3>Account Standing</h3><div class=reputation-bar><span class=label aria-label="Poor Account Standing">😢</span><span class=progress-bar><span class=progress style=width:100%></span></span><span class=label aria-label="Great Account Standing">😎</span></div><p>Your account is currently in a <span class=reputation>great standing</span>! You have no recent enforcement actions against your account.</div><div class="section multi-view password" id=update_password data-view-type=toggle><h3>Change your Password</h3><div class=view id=update_password_view><p>Change the password you use to log into ShiftCodesTK.</p><button aria-label="Change your account password" class="styled view-toggle" data-view=update_password_edit title="Change your account password">Change Password</button></div><div class=view id=update_password_edit><?php
                require_once(PRIVATE_PATHS['forms'] . 'account/change-password.php');

                $form_changePassword->insertForm();
              ?></div></div><div class="section login-actions"><?php
            // require_once(PRIVATE_PATHS['forms'] . 'auth/logout.php');
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
          ?></div><div class="section danger-zone"><?php
            // require_once(PRIVATE_PATHS['forms'] . 'account/danger-zone.php');

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
          ?></div></div></div></main><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?><script async src="/assets/js/local/account.js<?php echo TK_VERSION_STR; ?>"></script>