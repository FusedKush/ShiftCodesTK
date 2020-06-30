<header class=navbar-container><nav aria-label="Navigation Bar"class=navbar data-at-top=true id=navbar><div class="loader progress-bar"id=loader_pb aria-valuemax=100 aria-valuemin=0 aria-valuenow=0 role=progressbar><div class=progress></div></div><ul class=content-wrapper role=menubar><div class=left><li role=menuitem><button aria-label="Open the Sidebar"aria-pressed=false class="layer-target btn bubble-parent no-focus-scroll"id=navbar_sb data-pressed=false><span class="bubble bubble-dynamic"></span><span class="fas fa-bars"></span></button><div class="layer navbar-layer sticky tooltip"data-layer-pos=bottom>Sidebar</div></div><div class=center><li role=menuitem><a class="layer-target btn bubble-parent no-focus-scroll"href=/ aria-label="ShiftCodesTK Home"title="ShiftCodesTK Home"><span class="bubble bubble-dynamic"></span><span class=logo><img alt="ShiftCodesTK Logo"src="/assets/img/logo.svg?v=1"></span></a><div class="layer navbar-layer sticky tooltip"data-layer-pos=bottom>ShiftCodesTK Home</div></div><?php if (auth_isLoggedIn()) : ?><div class=right><li role=menuitem><button aria-label="Account Menu"aria-pressed=false class="layer-target btn bubble-parent no-focus-scroll"id=navbar_account_menu title="Account Menu"><span class="bubble bubble-dynamic"></span><span class="fas fa-user"></span></button><div class="layer navbar-layer sticky tooltip"data-layer-pos=bottom>Your Account</div><div class="layer navbar-layer sticky dropdown"data-layer-pos=bottom id=navbar_account_menu_dropdown><div class=title><?php
                  include_once(HTML_INCLUDES_PATH . 'local/profile-card.php');

                  getProfileCard([
                    'showBorder' => false,
                    'showRoles'  => false
                  ]);
                ?></div><ul class=choice-list><?php
                  $items = [
                    [
                      'icon'  => 'fas fa-plus',
                      'name'  => 'New Code',
                      'title' => 'Submit a new SHiFT Code to ShiftCodesTK',
                      'link'  => '/codes/add'
                    ],
                    [
                      'icon'  => 'fas fa-list-alt',
                      'name'  => 'My Codes',
                      'title' => 'Your submitted SHiFT Codes',
                      'link'  => '/codes/'
                    ],
                    [
                      'icon'  => 'fas fa-user',
                      'name'  => 'My Account',
                      'title' => 'View and manage your ShiftCodesTK Account',
                      'link'  => '/account'
                    ]
                  ]
                ?><?php foreach ($items as $item) : ?><li><a class="layer-target choice"href="<?= $item['link']; ?>"><span class="<?= "box-icon {$item['icon']}"; ?>"aria-hidden=true></span><span class=label><?= $item['name']; ?></span></a><div class="layer tooltip"data-layer-pos=left data-layer-delay=medium><?= $item['title']; ?></div></li><?php endforeach; ?><li><?php
                    include(FORMS_PATH . 'auth/logout.php');

                    $form_authLogout->insertForm();
                  ?></ul></div></div><?php endif; ?></ul></nav></header>