<?php
  $shift_game = PAGE_SETTINGS['shift']['game'];
  $badges = [
    ['name' => 'total',    'icon' => 'key'],
    ['name' => 'new',      'icon' => 'star'],
    ['name' => 'expiring', 'icon' => 'exclamation-triangle']
  ];

  foreach ($badges as &$badge) {
    $badge['cap'] = ucfirst($badge['name']);
  }
?>

<!-- SHiFT Resources -->
<link href="/assets/css/local/shift.css<?php echo TK_VERSION_STR; ?>" rel="stylesheet"></link>
<script async src="/assets/js/local/shift.js<?php echo TK_VERSION_STR; ?>"></script>
<script async src="/assets/js/global/libs/moment.js/moment.js<?php echo TK_VERSION_STR; ?>"></script>

<header class="shift-header" id="shift_header">
  <div class="primary">
    <div class="content-wrapper">
      <div class="section badges">
        <?php foreach($badges as &$badge) : ?>
          <?php
            $count = 0 ; // SHIFT_STATS[$shift_game][strtolower($badge['name'])];
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
            $states = ' disabled hidden ';
            /*
            (function () use ($count) {
              $str = '';
  
              if ($count == 0) { return ' disabled hidden '; }
              else             { return ''; }
            })();
            */
  
            // if ($count == 0)               { $classes[] = 'inactive'; }
            // if ($badge['name'] != 'total') { $classes[] = 'o-pressed'; }
  
            $className = implode(' ', $classes);
            $sharedAttr = <<<EOT
              class="{$className}" 
              id="shift_header_count_{$badge['name']}" 
              data-value="{$badge['name']}"
            EOT;
  
            $classes = implode(' ', $classes);
          ?>
  
          <?php if ($badge['name'] == 'total') : ?>
            <div 
              <?= $sharedAttr; ?>>
                <strong class="count"><?= $count; ?></strong>
                <span class="fas fa-<?= $badge['icon']; ?>"></span>
            </div>
          <?php else : ?>
            <button 
              <?= $sharedAttr; ?>
              <?= $states; ?>
              aria-pressed="false">
                <strong class="count"><?= $count; ?></strong>
                <span class="fas fa-<?= $badge['icon']; ?>"></span>
            </button>
          <?php endif; ?>
            <div class="layer tooltip">
              <?= $title; ?>
            </div>
        <?php endforeach; ?>
      </div>
      <div class="section buttons">
        <?php if (auth_isLoggedIn()) : ?>
          <button
            class="layer-target"
            id="shift_header_add"
            aria-label="Add a new SHiFT Code">
            <span class="fas fa-plus-circle"></span>
          </button>
          <div class="layer tooltip">
            Add a new SHiFT Code
          </div>
        <?php endif; ?>
        <!-- End of Add SHiFT Code Button Conditional -->
        <?php if ($shift_game == 'all') : ?>
          <!-- <button 
            class="layer-target" 
            id="shift_header_game_filter" 
            aria-label="Filter by Game" 
            disabled>
            <span class="fas fa-filter" aria-hidden="true"></span>
          </button>
          <div class="layer tooltip" data-layer-target="shift_header_game_filter">
            Filter by Game
          </div>
          <div 
            class="layer dropdown auto-press auto-toggle" 
            id="shift_header_game_filter_dropdown" 
            data-layer-name="shift_header_game_filter" 
            data-layer-pos="bottom" 
            data-layer-align="right"
            data-layer-target="shift_header_game_filter">
            <div class="panel">
              <div class="title">Filter by Game:</div>
              <ul class="choice-list">
                <?php
                  $choices = [
                    'all' => 'Show All',
                    'bl3' => 'Borderlands 3',
                    'bl1' => 'Borderlands: GOTY',
                    'tps' => 'Borderlands: TPS',
                    'bl2' => 'Borderlands 2'
                  ];
                ?>
  
                <?php foreach ($choices as $name => $value) : ?>
                  <?php
                    $classes = 'choice styled';
  
                    if ($name != 'all') {
                      $classes .= " color color-on-hover class-theme $name";
                    }
                  ?>
  
                  <li>
                    <button 
                      class="<?= $classes; ?>" 
                      data-value="<?= $name; ?>">
                      <?= $value; ?>
                    </button>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div> -->
        <?php endif; ?>
        <!-- End of SHiFT Game Filter Conditional -->
        <!-- <button 
          class="layer-target" 
          id="shift_header_sort" 
          aria-label="Sort SHiFT Codes" 
          disabled>
          <span class="fas fa-sort-amount-down" aria-hidden="true"></span>
        </button>
        <div class="layer tooltip" data-layer-target="shift_header_sort">
          Sort SHiFT Codes
        </div>
        <div 
          class="layer dropdown auto-press auto-toggle" 
          id="shift_header_sort_dropdown" 
          data-layer-name="shift_header_sort"
          data-layer-pos="bottom" 
          data-layer-align="right"
          data-layer-target="shift_header_sort">
          <div class="panel">
            <div class="title">Sort by:</div>
            <ul class="choice-list">
              <?php $choices = [ 'default', 'newest', 'oldest' ]; ?>
  
              <?php foreach ($choices as $choice) : ?>
                <li>
                  <button 
                    class="choice" 
                    data-value="<?= $choice; ?>">
                    <?= ucfirst($choice); ?>
                  </button>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div> -->
        <button 
          class="layer-target" 
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
        include(FORMS_PATH . 'shift/sort-filter.php');

        // Filter by Game
        (function () use ($shift_game, &$form_shiftSortFilter) {
          $filter = $form_shiftSortFilter->getChild('game_filter');

          if ($shift_game == 'all') {
            $filter->updateProperty('properties', [
              'disabled' => false,
              'hidden'   => false
            ]);
          }
        })(); 
        // Filter by Code Status
        (function () use (&$form_shiftSortFilter) {
          $filter = $form_shiftSortFilter->getChild('status_filter');
          
          if (isset(PAGE_SETTINGS['shift']['owner']) && PAGE_SETTINGS['shift']['owner'] == auth_user_id()) {
            $filter->updateProperty('inputProperties->options', [ 'hidden' => 'Hidden' ]);
          }
        })(); 

        $form_shiftSortFilter->insertForm();
      ?>
    </div>
  </div>
</header>
<main class="feed content-wrapper">
  <div class="overlay" id="shift_overlay">
    <!-- Spinner -->
    <?php include(HTML_INCLUDES_PATH . '/local/spinner.php'); ?>
    <div class="error" hidden aria-hidden="true">
      <strong>
        <div>No SHiFT Codes were found</div>
        <span class="fas fa-heart-broken"></span>
      </strong>
    </div>
  </div>
  <div class="update-indicator hidden" id="shift_update_indicator" title="0 SHiFT Code updates (Click to Update)" aria-label="0 SHiFT Code updates (Click to Update)" hidden>
    <span class="counter box-icon">0</span>
    <span>Updates</span>
  </div>
  <div 
    class="shift-code-list" 
    id="shift_code_list" 
    <?= " data-shift=" . json_encode(PAGE_SETTINGS['shift']); ?>
    >
  </div>
  <div 
    class="pager" 
    id="shift_code_pager" 
    data-subtractoffset="true" 
    data-onclick="shift_header_sort" 
    data-offset="10">
</main>

<!-- SHiFT Code Template -->
<template id="shift_code_template">
  <?php function shiftCodeOptionsMenu ($type = 0) { ?>
    <div 
      class="layer dropdown shift-code-options-menu <?= $type == 1 ? " use-cursor" : ""; ?>" 
      id="shift_code_options_menu_dropdown"
      data-layer-name="shift_code_options_menu" 
      data-layer-pos="<?= $type == 0 ? 'top' : 'right'; ?>"
      data-layer-triggers="<?= $type == 0 ? 'primary-click' : 'secondary-click'; ?>">
      <div class="title">
        <div>SHiFT Code</div>
        <em class="code-id"></em>
      </div>
      <ul class="choice-list">
        <?php
          $choices = [
            'code-actions' => [
              'share' => [
                'icon'  => 'fa-share',
                'label' => 'Share this SHiFT Code',
                'color' => false
              ],
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
              'make_public' => [
                'icon'  => 'fa-eye',
                'label' => 'Mark this SHiFT Code as <strong>Public</strong>, visible to <em>everyone</em>.',
                'color' => false
              ],
              'make_private' => [
                'icon'  => 'fa-eye-slash',
                'label' => 'Mark this SHiFT Code as <strong>Private</strong>, visible to <em>only you</em>.',
                'color' => false
              ],
              'delete' => [
                'icon'  => 'fa-trash-alt',
                'label' => 'Permanently delete this SHiFT Code',
                'color' => 'danger'
              ]
            ]
          ];
        ?>

        <?php foreach ($choices as $category => $categoryChoices) : ?>
          <div class="<?= $category; ?>">
            <?php if ($category != array_key_first($choices)) : ?>
              <div class="separator" aria-hidden="true"></div>
            <?php endif; ?>

            <?php foreach ($categoryChoices as $choice => $options) : ?>
              <?php
                $displayChoice = ucwords(str_replace('_', ' ', $choice));
                $color = $options['color']
                        ? " color {$options['color']}"
                        : "";
                $visibilityToggle = $choice == 'make_public' || $choice == 'make_private'
                                    ? " visibility-toggle"
                                    : "";
              ?>

              <li>
                <button
                  class="choice styled color-text layer-target <?= " {$color}{$visibilityToggle}"; ?>"
                  data-value="<?= $choice; ?>">
                  <span class="inline-box-icon icon fas <?= " {$options['icon']}"; ?>" aria-hidden="true"></span>
                  <span>&nbsp;&nbsp;<?= $displayChoice; ?></span>
                </button>
                <div class="layer tooltip" data-layer-pos="left" data-layer-delay="medium">
                  <?= $options['label']; ?>
                </div>
              </li>
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
    <span class="overlay-hashttarget"></span>
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
                    'title' => 'Standard SHiFT Code for Golden Keys'
                  ],
                  [
                    'class' => 'rare',
                    'name'  => 'Rare SHiFT Code',
                    'title' => 'Rare SHiFT Code with an uncommon reward'
                  ],
                  [
                    'class' => 'expired',
                    'name'  => 'Expired SHiFT Code',
                    'title' => 'This SHiFT Code has expired'
                  ],
                  [
                    'class' => 'hidden',
                    'name'  => 'Hidden SHiFT Code',
                    'title' => 'This SHiFT Code is currently hidden and visible to <em>only you</em>'
                  ],
                  [
                    'class' => 'deleted',
                    'name'  => 'Deleted SHiFT Code',
                    'title' => 'This SHiFT Code has been deleted'
                  ],
                  [
                    'class' => 'game-label',
                    'name'  => 'Borderlands',
                    'title' => 'SHiFT Code for Borderlands'
                  ],
                  [
                    'class' => 'owned',
                    'name'  => 'Owner',
                    'title' => 'You are the owner of this SHiFT Code'
                  ],
                  [
                    'class' => 'redeemed',
                    'name'  => 'Redeemed',
                    'title' => 'You have redeemed this SHiFT Code'
                  ],
                  [
                    'class' => 'new',
                    'name'  => 'New!',
                    'title' => 'This SHiFT Code was recently submitted'
                  ],
                  [
                    'class' => 'expiring',
                    'name'  => 'Expiring!',
                    'title' => 'This SHiFT Code is set to expire soon'
                  ],
                  [
                    'class' => 'recently-added',
                    'name'  => 'Recently Added',
                    'title' => 'This SHiFT Code was just added'
                  ],
                  [
                    'class' => 'recently-updated',
                    'name'  => 'Recently Updated',
                    'title' => 'This SHiFT Code was just updated'
                  ]
                ]
              ?>

              <?php foreach ($labels as $label) : ?>
                <span 
                  class="<?= "label layer-target {$label['class']}"; ?>">
                  <span><?= $label['name']; ?></span>
                </span>
                <div class="layer tooltip" data-layer-delay="medium">
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
          <div class="layer tooltip use-cursor" data-layer-delay="medium"></div>
        </div>
      </div>
    </button>
    <dl class="body active">
      <div class="multi-view force-size" data-view-type="toggle">
        <div class="view code layer-target" id="shift_code_view_code">
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
          <div class="section src">
            <dt class="title">Source</dt>
            <dd class="content">
              <a 
                class="link tr-underline layer-target" 
                target="_blank" 
                rel="external noopener">
                <span class="fas fa-external-link-square-alt" title="External Link" aria-hidden="true">&nbsp;</span>
              </a>
              <div class="layer tooltip" data-layer-delay="medium">
                SHiFT Code Source (External Link)
              </div>
              <span class="no-link layer-target">N/A</span>
              <div class="layer tooltip" data-layer-delay="medium">
                No Source URL
              </div>
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
          <?php foreach (["pc", "xbox", "ps"] as $group) : ?>
            <div class="<?= "section $group"; ?>">
              <dt class="title"></dt>
              <div class="content code">
                <input
                  class="value clipboard-copy"
                  hidden
                  tabindex="-1"
                  readonly />
                <dd class="display layer-target"></dd>
                <div class="layer tooltip" data-layer-delay="medium">
                  <?= strtoupper($group) . " "; ?> SHiFT Code
                </div>
                <button
                  class="copy layer-target"
                  data-copy-target="1">
                  <span class="fas fa-clipboard"></span>
                </button>
                <div class="layer tooltip">Copy to Clipboard</div>
              </div>
            </div>
          <?php endforeach; ?>
          <!-- End of SHift Codes loop -->
          <!-- Footer -->
          <div class="footer">      
            <div class="actions">
              <!-- Redeem -->
              <button
                class="action redeem styled color layer-target"
                aria-pressed="false">
                <div class="label">
                  <span class="icon fas fa-bookmark" aria-hidden="true">&nbsp;&nbsp;</span>
                  <span>Redeem</span>
                </div>
                <?php include(HTML_INCLUDES_PATH . 'local/spinner.php'); ?>
              </button>
              <div class="layer tooltip wrapped">
                Mark this SHiFT Code as Redeemed
              </div>
              <!-- Options Menu -->
              <div class="options-menu-container">
                <button
                  class="action options-menu styled color light layer-target"
                  id="shift_code_options_menu">
                  <span class="icon fas fa-ellipsis-h" aria-hidden="true"></span>
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
        <?php shiftCodeOptionsMenu(1); ?>
        <div class="view edit" id="shift_code_view_edit">
          <?php
            include(FORMS_PATH . 'shift/shift-code.php');

            $form_shiftCode->insertForm();
          ?>
        </div>
      </div>
    </dl>
    <div class="body deleted">
      This SHiFT Code was deleted&nbsp;
      <em class="timestamp layer-target">today</em>
      <div class="layer tooltip" data-layer-delay="medium"></div>
      .
    </div>
  </div>
</template>
<!-- SHiFT Code Deletion Confirmation Modal -->
<div class="modal" id="shift_code_deletion_confirmation_modal">
  <div class="title">Delete SHiFT Code</div>
  <div class="body">
    <?php
      include_once(FORMS_PATH . 'shift/delete.php');

      $form_shiftDelete->insertForm();
    ?>
  </div>
</div>