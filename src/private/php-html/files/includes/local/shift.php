<?php
  use ShiftCodesTK\Paths;

  $shift_game = PAGE_SETTINGS['shift']['game'];
  $badges = [
    ['name' => 'total',    'icon' => 'key'],
    ['name' => 'new',      'icon' => 'star'],
    ['name' => 'expiring', 'icon' => 'exclamation-triangle']
  ];

  foreach ($badges as &$badge) {
    $badge['cap'] = ucfirst($badge['name']);
  }


  $testCode = (ShiftCodes::getInstance()->getCodes([
    // 'code' => '119358692826',
    'code' => '119358592325',
    'limit' => 1,
    'page'  => 1, 
    'getResultSetData' => true,
    'returnFullResponse' => true,
    'getFlagCounts' => true
  ]));
  // var_dump(ShiftCodes::getInstance()->getSocialMediaPosts(), ShiftCodes::getInstance());
?>

<!-- SHiFT Resources -->
<link href="/assets/css/local/shift.css<?php echo \ShiftCodesTK\VERSION_QUERY_STR; ?>" rel="stylesheet"></link>
<script async src="/assets/js/local/shift.js<?php echo \ShiftCodesTK\VERSION_QUERY_STR; ?>"></script>

<header class="shift-header" id="shift_header">
  <div class="primary">
    <div class="content-wrapper">
      <div class="section badges">
        <?php foreach($badges as &$badge) : ?>
          <?php
            $count = 0 ;
            $classes = ['badge', $badge['name'], 'inactive', 'layer-target'];
            $str = 'SHiFT Code' . checkPlural($count);
            $title = (function () use ($badge, $count, $str) {
              if ($badge['name'] == 'total') { return "No SHiFT Codes Available"; }
              else                           { return "No {$badge['cap']} SHiFT Codes"; }
              //
              if ($count > 0) {
                $str = "$count {$badge['cap']} $str";
  
                if ($badge['name'] != 'total') {
                  $str .= ' (Click to Filter)';
                }
  
                return $str;
              }
              else {
                if ($badge['name'] == 'total') { return "No SHiFT Codes Available"; }
                else                           { return "No {$badge['cap']} SHiFT Codes"; }
              }
            })();
            $states = ' hidden ';
  
            $className = implode(' ', $classes);
            $sharedAttr = <<<EOT
              class="{$className}" 
              id="shift_header_count_{$badge['name']}" 
              data-value="{$badge['name']}"
            EOT;
  
            $classes = implode(' ', $classes);
          ?>
  
          <div 
            <?= $sharedAttr; ?>>
              <span class="fas fa-<?= $badge['icon']; ?>"></span>
              <strong class="count"><?= $count; ?></strong>
          </div>
            <div class="layer tooltip" data-layer-delay="none">
              <?= $title; ?>
            </div>
        <?php endforeach; ?>
      </div>
      <div class="section buttons">
        <?php if (auth_isLoggedIn()) : ?>
          <a
            class="layer-target button color theme"
            id="shift_header_new"
            href="/codes/new<?= $shift_game != 'all' ? "?game={$shift_game}" : ''; ?>"
            aria-label="Submit a new SHiFT Code">
            <span class="fas fa-plus-circle"></span>
            &nbsp;New
          </a>
          <div class="layer tooltip">
            Submit a new SHiFT Code
          </div>
        <?php endif; ?>
        <button 
          class="layer-target styled o-pressed" 
          id="shift_header_sort_filter" 
          aria-label="Sort & Filter" 
          disabled>
          <span class="fas fa-filter" aria-hidden="true"></span>
        </button>
        <div class="layer tooltip" data-layer-target="shift_header_sort_filter">
          Sort & Filter
        </div>
      </div>
    </div>
  </div>
  <div class="slideout" hidden>
    <div class="content-wrapper">
      <?php
        include(Paths\PHP_PATHS['forms'] . '/shift/sort-filter.php');

        getForm_shiftSortFilter($shift_game, isset(
                                              PAGE_SETTINGS['shift']['owner']) 
                                              && PAGE_SETTINGS['shift']['owner'] !== false 
                                              && PAGE_SETTINGS['shift']['owner'] == auth_user_id())->insertForm();
      ?>
    </div>
  </div>
</header>
<main class="feed content-wrapper">
  <div class="overlay" id="shift_overlay" >
    <!-- Spinner -->
    <?php include('local/spinner.php'); ?>
    <div class="error" hidden aria-hidden="true">
      <strong>
        <div>No SHiFT Codes were found</div>
        <span class="fas fa-heart-broken"></span>
      </strong>
    </div>
  </div>
  <button class="update-indicator hidden" id="shift_update_indicator" title="0 SHiFT Code updates (Click to Update)" aria-label="0 SHiFT Code updates (Click to Update)" hidden>
    <span class="counter box-icon">0</span>
    <span>Updates</span>
  </button>
  <div 
    class="shift-code-list" 
    id="shift_code_list" 
    <?= " data-shift=" . json_encode(PAGE_SETTINGS['shift']); ?>
    >
  </div>
  <div 
    class="pager" 
    id="shift_code_pager" 
    data-onclick="shift_header_sort_filter">
</main>
<!-- Marking SHiFT Codes as Redeemed Info Modal -->
<div class="modal" id="shift_code_redeeming_codes_info_modal">
  <div class="title">Marking SHiFT Codes as Redeemed</div>
  <div class="body">
    <p>
      When you mark a SHiFT Code as&nbsp;<em>Redeemed</em>, it will be labeled accordingly when you see the SHiFT Code in the future. 
      This does&nbsp;<em>not</em>&nbsp;redeem the SHiFT Code to your Gearbox SHiFT Account, and only allows you to keep track of which codes you have previously redeemed.
    </p>
    <div class="section">
      <div class="title">Things to Know</div>
      <ul class="styled">
        <li>Redeemed SHiFT Codes are only labelled for you. Other users cannot see which SHiFT Codes you have marked as redeemed.</li>
        <li>This feature depends on&nbsp;<em>cookies</em>. Blocking or clearing your cookies may cause you to lose any SHiFT Codes you have marked as redeemed. This doesn't apply to codes redeemed while signed in.</li>
        <li>
          When you mark a SHiFT Code as&nbsp;<em>redeemed</em>, the 25-character redeemable keys themselves are labeled. 
          <ul class="styled">
            <li><em>Universal</em>&nbsp;SHiFT Codes mark the&nbsp;<em>universal redemption key</em>&nbsp;as redeemed. Any submitted SHiFT Codes with the same singular redemption key are marked as redeemed.
            <li><em>Individual</em>&nbsp;SHiFT Codes mark&nbsp;<em>all redemption keys</em>&nbsp;as redeemed. Only SHiFT Codes with the exact same combination of redemption keys are marked as redeemed.
          </ul>
        </li>
      </ul>
    </div>
    <div class="section">
      <div class="title">Redeeming SHiFT Codes while Signed In</div>
      <p>SHiFT Codes can be marked as&nbsp;<em>redeemed</em>&nbsp;with or without a ShiftCodesTK Account. However, there are advantages to redeeming SHiFT Codes while being signed-in.</p>
      <div class="table-wrapper">
        <table>
          <caption>Differences with and without your ShiftCodesTK Account</caption>
          <tr>
            <th scope="col"></th>
            <th scope="col">Without an Account</th>
            <th scope="col">With an Account</th>
          </tr>
          <tr>
            <th scope="row">Where can you see redeemed SHiFT Codes?</td>
            <td>On the same device and browser where the code was redeemed</td>
            <td class="layer-target">
              <span class="layer-target">On any device or browser</span>
              <div class="layer tooltip">Requires you to be signed in to your ShiftCodesTK Account on the browser.</div>
            </td>
          </tr>
          <tr>
            <th scope="row">Will redeemed codes be lost if the browser cookies are deleted?</td>
            <td>Yes</td>
            <td class="layer-target">
              <span class="layer-target">No</span>
              <div class="layer tooltip">Requires you to re-sign in to your ShiftCodesTK Account after your cookies have been cleared.</div>
            </td>
          </tr>
          <tr>
            <th scope="row">How long are redeemed SHiFT Codes marked for?</td>
            <td>
              <span class="layer-target">6 Months</span>
              <div class="layer tooltip">This timer is reset for all redeemed SHiFT Codes any time you mark a SHiFT Code as&nbsp;<em>redeemed</em>&nbsp;or&nbsp;<em>unredeemed</em>.</div>
            </td>
            <td>Forever</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- SHiFT Code Template -->
<template id="shift_code_template">
  <?php function shiftCodeOptionsMenu ($type = 0) { ?>
    <div 
      class="layer dropdown shift-code-options-menu <?= $type == 1 ? " use-cursor" : ""; ?>" 
      id="shift_code_options_menu_dropdown"
      data-layer-name="shift_code_options_menu" 
      data-layer-pos="<?= $type == 0 ? 'top' : 'right'; ?>"
      data-layer-align="top"
      data-layer-triggers="<?= $type == 0 ? 'primary-click' : 'secondary-click'; ?>">
      <div class="title">
        <div>SHiFT Code</div>
        <em class="code-id"></em>
      </div>
      <ul class="choice-list">
        <?php
          include(Paths\PHP_PATHS['forms'] . '/shift/toggle-visibility.php');
          include(Paths\PHP_PATHS['forms'] . '/shift/delete.php');
          $choices = [
            'code-actions' => [
              // 'share' => [
              //   'icon'  => 'fa-share',
              //   'label' => 'Share this SHiFT Code',
              //   'color' => false
              // ],
              'report' => [
                'icon'  => 'fa-flag',
                'label' => 'Report a problem with this SHiFT Code',
                'color' => 'warning'
              ]
            ],
            'edit-actions' => [
              'edit' => [
                'icon'  => 'fa-pen',
                'label' => 'Edit and Update this SHiFT Code',
                'color' => false
              ],
              'toggle-visibility' => $form_shiftToggleVisibility,
              'delete'            => $form_deleteShiftCode
              // 'delete' => [
              //   'icon'  => 'fa-trash-alt',
              //   'label' => 'Permanently delete this SHiFT Code',
              //   'color' => 'danger'
              // ]
            ]
          ];
        ?>

        <?php foreach ($choices as $category => $categoryChoices) : ?>
          <div class="<?= $category; ?>">
            <?php if ($category != array_key_first($choices)) : ?>
              <div class="separator" aria-hidden="true"></div>
            <?php endif; ?>

            <?php foreach ($categoryChoices as $choice => $options) : ?>
              <?php if ($choice != 'toggle-visibility' && $choice != 'delete') : ?>
                <?php
                  $displayChoice = ucwords(str_replace('_', ' ', $choice));
                  $color = $options['color']
                          ? " color {$options['color']}"
                          : "";
                  $visibilityToggle = $choice == 'make_public' || $choice == 'make_private'
                                      ? " visibility-toggle"
                                      : "";
                  $allowDisabledLayers = $choice == 'report'
                                         ? " allow-disabled-layers"
                                         : "";
                ?>

                <li>
                  <button
                    class="choice styled button-effect text layer-target has-spinner <?= " {$color}{$visibilityToggle}{$allowDisabledLayers}"; ?>"
                    data-value="<?= $choice; ?>">
                    <span class="inline-box-icon icon fas <?= " {$options['icon']}"; ?>" aria-hidden="true"></span>
                    <span>&nbsp;&nbsp;<?= $displayChoice; ?></span>
                    <?php include('local/spinner.php'); ?>
                  </button>
                  <div class="layer tooltip" data-layer-pos="left" data-layer-delay="medium">
                    <?= $options['label']; ?>
                  </div>
                </li>
                <?php else : ?>
                  <?php
                    $options->insertForm();
                  ?>
                <?php endif; ?>
                <!-- End of visibility toggle condition -->
              <?php endforeach; ?>
              <!-- End of Choices Category Loop -->
          </div>
        <?php endforeach; ?>
        <!-- End of Choices Loop -->
      </ul>
    </div>
  <?php } ?>
  <!-- End of SHiFT Code Options Menu -->

  <div class="shift-code dropdown-panel" id="shift_code">
    <button class="header dropdown-panel-toggle" data-custom-labels='{"false": "Expand SHiFT Code", "true": "Collapse SHiFT Code"}'>
      <div class="wrapper">
        <div class="title">
          <div class="icon" title="SHiFT Code" aria-hidden="true">
            <span class="fas fa-key"></span>
          </div>
          <div class="string">
            <!-- Reward -->
            <h2 class="primary reward"></h2>
            <!-- Labels -->
            <span class="secondary labels">
              <?php
                $labels = [
                  [
                    'class' => 'basic',
                    'name'  => 'SHiFT Code',
                    'title' => 'Standard SHiFT Code for Golden Keys',
                    // 'icon'  => 'fa-key'
                  ],
                  [
                    'class' => 'rare',
                    'name'  => 'Rare SHiFT Code',
                    'title' => 'Rare SHiFT Code with an uncommon reward',
                    // 'icon'  => 'fa-trophy'
                  ],
                  [
                    'class' => 'game-label',
                    'name'  => 'Borderlands',
                    'title' => 'SHiFT Code for Borderlands',
                  ],
                  [
                    'class' => 'expired',
                    'name'  => 'Expired SHiFT Code',
                    'title' => 'This SHiFT Code has expired',
                    'icon'  => 'fa-archive'
                  ],
                  [
                    'class' => 'hidden',
                    'name'  => 'Hidden SHiFT Code',
                    'title' => 'This SHiFT Code is currently hidden and visible to <em>only you</em>',
                    'icon'  => 'fa-eye-slash'
                  ],
                  [
                    'class' => 'deleted',
                    'name'  => 'Deleted SHiFT Code',
                    'title' => 'This SHiFT Code has been deleted',
                    'icon'  => 'fa-trash-alt'
                  ],
                  [
                    'class' => 'owned',
                    'name'  => 'Owner',
                    'title' => 'You are the owner of this SHiFT Code',
                    'icon'  => 'fa-user'
                  ],
                  [
                    'class' => 'redeemed',
                    'name'  => 'Redeemed',
                    'title' => 'You have redeemed this SHiFT Code',
                    'icon'  => 'fa-clipboard-check'
                  ],
                  [
                    'class' => 'new',
                    'name'  => 'New!',
                    'title' => 'This SHiFT Code was recently released',
                    'icon'  => 'fa-star'
                  ],
                  [
                    'class' => 'expiring',
                    'name'  => 'Expiring!',
                    'title' => 'This SHiFT Code is set to expire soon',
                    'icon'  => 'fa-exclamation-triangle'
                  ],
                  [
                    'class' => 'recently-submitted',
                    'name'  => 'Recently Submitted',
                    'title' => 'This SHiFT Code was recently submitted',
                    'icon'  => 'fa-file-upload'
                  ],
                  [
                    'class' => 'recently-updated',
                    'name'  => 'Recently Updated',
                    'title' => 'This SHiFT Code was just updated',
                    'icon'  => 'fa-save'
                  ]
                ]
              ?>

              <?php foreach ($labels as $label) : ?>
                <span 
                  class="<?= "label layer-target {$label['class']}"; ?>">
                  <span><?= isset($label['icon']) ? "<span class=\"fas {$label['icon']}\" aria-hidden=\"true\"></span>" : $label['name']; ?></span>
                </span>
                <div class="layer tooltip" data-layer-delay="medium" data-layer-pos="bottom">
                  <?= $label['title']; ?>
                </div>
              <?php endforeach; ?>
              <!-- End of label loop -->
            </span>
          </div>
        </div>
        <div class="indicator" aria-hidden="true">
          <span class="fas fa-chevron-right"></span>
        </div>
        <!-- Progress Bar -->
        <div class="full-width">
          <div class="progress-bar layer-target" role="progressbar" aria-valuemin="0" aria-valuemax="100">
            <div class="progress"></div>
          </div>
          <div class="layer tooltip use-cursor follow-cursor lazy-follow" data-layer-delay="medium"></div>
        </div>
      </div>
    </button>
    <div class="body multi-view force-size" data-view-type="toggle">
      <div class="view code" id="shift_code_view_code">
        <!-- Body -->
        <div class="content-container">
          <div class="background" aria-hidden="true">
            <span class="fas fa-key"></span>
          </div>
          <!-- Release Date -->
          <div class="section release can-split">
            <dt class="title">Release Date</dt>
            <div class="content">
              <dd class="layer-target"></dd>
              <div class="layer tooltip" data-layer-delay="medium"></div>
              <span class="content simple" aria-hidden="true">
                <span></span>
              </span>
            </div>
          </div>
          <!-- Expiration Date -->
          <div class="section expiration can-split">
            <dt class="title">Expiration Date</dt>
            <div class="content">
              <dd class="layer-target"></dd>
              <div class="layer tooltip" data-layer-delay="medium"></div>
              <span class="content simple" aria-hidden="true">
                <span></span>
              </span>
            </div>
          </div>
          <!-- Source -->
          <div class="section source">
            <dt class="title">Source</dt>
            <dd class="content">
              <a 
                class="online-source tr-underline layer-target" 
                target="_blank" 
                rel="external noopener">
                <span class="fas fa-external-link-square-alt layer-target" aria-hidden="true">&nbsp;</span>
                <div class="layer tooltip" data-layer-delay="medium">
                  External Link
                </div>
              </a>
              <div class="layer tooltip">
                The online source of the SHiFT Code.
                <br>
                <br><strong>Note:</strong>&nbsp;This is an&nbsp;<em>external</em>&nbsp;link that will take you away from ShiftCodesTK.
              </div>
              <span class="physical-source layer-target"></span>
              <div class="layer tooltip">The physical source of the SHiFT Code</div>
            </dd>
          </div>
          <!-- Notes -->
          <div class="section notes">
            <dt class="title">Notes</dt>
            <dd class="content">
              <ul class="styled">
              </ul>
            </dd>
          </div>
          <div class="separator"></div>
          <!-- SHiFT Codes -->
          <div class="section shift-code">
            <!-- Code Family -->
            <dt class="title platform-family layer-target"></dt>
            <div class="layer tooltip" data-layer-align="left"></div>
  
            <!-- Platform List -->
            <ul class="platform-list" aria-label="The SHiFT Code supports the following platforms:">
              <li>
                <span class="layer-target platform">
                  <span></span>
                </span>
                <div class="layer tooltip"></div>
              </li>
            </ul>
            <div class="content">
              <!-- SHiFT Code -->
              <dd class="code copy-content"></dd>
  
              <!-- Copy to Clipboard Button -->
              <button
                class="styled class-theme copy-to-clipboard layer-target">
                <span class="fas fa-clipboard"></span>
              </button>
              <div class="layer tooltip">Copy SHiFT Code to Clipboard</div>
            </div>
          </div>
          <div class="section shift-code-usage">
            SHiFT Codes can be redeemed 
              <a class="in-game styled layer-target" target="_blank" href="/help/how-to-redeem">In-Game</a>
              <div class="layer tooltip" data-layer-delay="medium">How to Redeem SHiFT Codes In-Game</div>
            &nbsp;or&nbsp;
              <a class="online styled layer-target" target="_blank" rel="external noopener" href="https://shift.gearboxsoftware.com/rewards">
                <span class="fas fa-external-link-square-alt layer-target" aria-hidden="true">&nbsp;</span>
                <div class="layer tooltip" data-layer-delay="medium">External Link</div>
                Online
              </a>
              <div class="layer tooltip" data-layer-delay="medium">Redeem this SHiFT Code on the SHiFT Website</div>.
          </div>
        </div>
        <!-- Footer -->
        <div class="footer">      
          <div class="actions">
            <!-- Share Button -->
            <div class="action share">
              <button
                class="styled color light layer-target button-effect text auto-press"
                id="shift_code_share_button">
                <span class="icon">
                  <span class="box-icon fas fa-share" aria-hidden="true"></span>
                </span>
              </button>
              <!-- <div class="layer tooltip" id="shift_code_share_button_tooltip">
                SHiFT Code Sharing is coming soon.
              </div> -->
              <div class="layer tooltip" id="shift_code_share_button_tooltip">
                Share this SHiFT Code
              </div>
              <div class="layer panel" id="playground_layer_panel" data-layer-name="shift_code_sharing_panel" data-layer-target="playground_layer_panel_toggle" data-layer-pos="top" data-layer-triggers="primary-click">
                <div class="title">
                  <div class="primary">Share SHiFT Code</div>
                  <div class="secondary">You can use this link to share this SHiFT Code with others.</div>
                </div>
                <?php
                  $form_shareLink = new FormBase([
                    'properties' => [
                      'name'        => 'share_link_form',
                      'templates'    => [ 'SINGLE_BUTTON' ]
                    ],
                    'formProperties' => [
                      'action'          => [
                        'path'             => 'window/copyShareLink',
                        'type'             => 'js'
                      ]
                    ]
                  ]);

                  $form_shareLink->addChild('field', [
                    'properties' => [
                      'name'        => 'share_link',
                      'customHTML'  => [
                        'classes'      => [
                          'copy-content'
                        ]
                      ]
                    ],
                    'inputProperties' => [
                      'type'             => 'url',
                      'validations'      => [
                        'readonly'          => true
                      ]
                    ]
                  ]);

                  $form_shareLink->insertForm();
                ?>
                <button class="styled light copy-to-clipboard layer-target" data-copy="1"><span class="fas fa-clipboard"></span></button>
                <div class="layer tooltip">Copy the link to the clipboard</div>
              </div>
            </div>
            <!-- Redeem -->
            <div class="action redeem">
              <?php
                include_once(Paths\PHP_PATHS['forms'] . '/shift/redeem.php');

                $form_redeemShiftCode->insertForm();
              ?>
            </div>
            <!-- Options Menu -->
            <div class="action code-options">
              <button
                class="styled color light layer-target button-effect text"
                id="shift_code_options_menu">
                <span class="icon">
                  <span class="box-icon fas fa-ellipsis-h" aria-hidden="true"></span>
                </span>
              </button>
              <div class="layer tooltip" id="shift_code_options_menu_tooltip">
                SHiFT Code Options
              </div>
              <?php shiftCodeOptionsMenu(0); ?>
            </div>
          </div>
          <div class="separator" aria-hidden="true"></div>
          <div class="code-info">
            <div class="id">
              <dt>ID</dt>
              &nbsp;
              <dd class="layer-target"></dd>
              <div class="layer tooltip" data-layer-delay="medium"></div>
            </div>
            <div class="last-update">
              <dt>Last Updated</dt>
              &nbsp;
              <dd class="layer-target"></dd>
              <div class="layer tooltip" data-layer-delay="medium"></div>
            </div>
            <div class="owner">
              <dt>Submitted By</dt>
              &nbsp;
              <dd class="layer-target"></dd>
              <div class="layer tooltip" data-layer-delay="medium"></div>
            </div>
          </div>
        </div>
      </div>
      <div class="view edit" id="shift_code_view_edit">
        <div class="content-container">
          <?php
            include(Paths\PHP_PATHS['forms'] . '/shift/shift-code.php');

            $form_shiftCode = getShiftCodeForm('update');
            $form_shiftCode->insertForm();
          ?>
        </div>
      </div>
    </div>
  </div>
</template>
<!-- SHiFT Code Deletion Confirmation Modal -->
<template id="delete_shift_code_confirmation_modal_template">
  <div class="container">
    <div class="message">Are you sure you want to delete this SHiFT Code? This action&nbsp;<em>cannot be reversed</em>.</div>
    <div class="dropdown-panel c">
      <div class="primary">SHiFT Code Details</div>
      <div class="body">
        <dl class="info">
          <div class="section id">
            <dt>
              <span class="icon fas fa-key box-icon" aria-hidden="true"></span>
              <span>ID</span>
            </dt>
            <dd>
              <i class="secondary"></i>
              <div class="primary"></div>
            </dd>
          </div>
          <div class="section owner">
            <dt>
              <span class="icon fas fa-user box-icon" aria-hidden="true"></span>
              <span>Owner</span>
            </dt>
            <dd>
              <div class="primary"></div>
              <i class="secondary"></i>
            </dd>
          </div>
          <div class="section created">
            <dt>
              <span class="icon fas fa-clock box-icon" aria-hidden="true"></span>
              <span>Created</span>
            </dt>
            <dd>
              <div class="primary"></div>
              <i class="secondary"></i>
            </dd>
          </div>
          <div class="section updated">
            <dt>
              <span class="icon far fa-clock box-icon" aria-hidden="true"></span>
              <span>Updated</span>
            </dt>
            <dd>
              <div class="primary"></div>
              <i class="secondary"></i>
            </dd>
          </div>
        </dl>
      </div>
    </div>
  </div>
</template>