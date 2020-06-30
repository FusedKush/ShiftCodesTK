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
?><header class=shift-header id=shift_header><div class=primary><div class=content-wrapper><div class="section badges"><?php foreach($badges as &$badge) : ?><?php
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
          ?><?php if ($badge['name'] == 'total') : ?><div<?= $sharedAttr; ?>><strong class=count><?= $count; ?></strong><span class="fas fa-<?= $badge['icon']; ?>"></span></div><?php else : ?><button aria-pressed=false<?= $sharedAttr; ?><?= $states; ?>><strong class=count><?= $count; ?></strong><span class="fas fa-<?= $badge['icon']; ?>"></span></button><?php endif; ?><div class="layer tooltip"><?= $title; ?></div><?php endforeach; ?></div><div class="section buttons"><?php if (auth_isLoggedIn()) : ?><button class=layer-target id=shift_header_add aria-label="Add a new SHiFT Code"><span class="fas fa-plus-circle"></span></button><div class="layer tooltip">Add a new SHiFT Code</div><?php endif; ?><?php if ($shift_game == 'all') : ?><?php endif; ?><button class=layer-target id=shift_header_sort_filter aria-label="Sort & Filter"disabled><span class="fas fa-filter"aria-hidden=true></span></button><div class="layer tooltip"data-layer-target=shift_header_sort_filter>Sort & Filter</div></div></div></div><div class=slideout hidden><div class=content-wrapper><?php
        include(FORMS_PATH . 'shift/sort-filter.php');

        // Filter by Game
        (function () use ($shift_game, &$form_shiftSortFilter) {
          $filter = $form_shiftSortFilter->getChild('game_filter');

          if ($shift_game == 'all') {
            $filter->updateProperty('properties', [
              'disabled' => false,
              'hidden'   => false
            ]);

            if (isset($_GET['game']) && is_array($_GET['game'])) {
              $filter->updateProperty('inputProperties->value', implode(', ', $_GET['game']));
            }
          }
        })(); 
        // Filter by Code Status
        (function () use (&$form_shiftSortFilter) {
          $filter = $form_shiftSortFilter->getChild('status_filter');

          if (isset($_GET['status']) && is_array($_GET['status'])) {
            $filter->updateProperty('inputProperties->value', implode(', ', $_GET['status']));
          }
          if (isset(PAGE_SETTINGS['shift']['owner']) && PAGE_SETTINGS['shift']['owner'] == auth_user_id()) {
            $filter->updateProperty('inputProperties->options', [ 'hidden' => 'Hidden' ]);
          }
        })(); 

        $form_shiftSortFilter->insertForm();
      ?></div></div></header><main class="content-wrapper feed"><div class=overlay id=shift_overlay><?php include(HTML_INCLUDES_PATH . '/local/spinner.php'); ?><div class=error aria-hidden=true hidden><strong><div>No SHiFT Codes were found</div><span class="fas fa-heart-broken"></span></strong></div></div><div class="hidden update-indicator"id=shift_update_indicator aria-label="0 SHiFT Code updates (Click to Update)"hidden title="0 SHiFT Code updates (Click to Update)"><span class="box-icon counter">0</span><span>Updates</span></div><div class=shift-code-list id=shift_code_list<?= " data-shift=" . json_encode(PAGE_SETTINGS['shift']); ?>></div><div class=pager id=shift_code_pager data-offset=10 data-onclick=shift_header_sort data-subtractoffset=true></div></main><template id=shift_code_template><?php function shiftCodeOptionsMenu ($type = 0) { ?><div class="layer dropdown shift-code-options-menu<?= $type == 1 ? " use-cursor" : ""; ?>"id=shift_code_options_menu_dropdown data-layer-name=shift_code_options_menu data-layer-pos="<?= $type == 0 ? 'top' : 'right'; ?>"data-layer-triggers="<?= $type == 0 ? 'primary-click' : 'secondary-click'; ?>"><div class=title><div>SHiFT Code</div><em class=code-id></em></div><ul class=choice-list><?php
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
        ?><?php foreach ($choices as $category => $categoryChoices) : ?><div class="<?= $category; ?>"><?php if ($category != array_key_first($choices)) : ?><div class=separator aria-hidden=true></div><?php endif; ?><?php foreach ($categoryChoices as $choice => $options) : ?><?php
                $displayChoice = ucwords(str_replace('_', ' ', $choice));
                $color = $options['color']
                        ? " color {$options['color']}"
                        : "";
                $visibilityToggle = $choice == 'make_public' || $choice == 'make_private'
                                    ? " visibility-toggle"
                                    : "";
              ?><li><button class="styled choice color-text layer-target<?= " {$color}{$visibilityToggle}"; ?>"data-value="<?= $choice; ?>"><span class="icon inline-box-icon fas<?= " {$options['icon']}"; ?>"aria-hidden=true></span><span>   <?= $displayChoice; ?></span></button><div class="layer tooltip"data-layer-delay=medium data-layer-pos=left><?= $options['label']; ?></div></li><?php endforeach; ?></div><?php endforeach; ?></ul></div><?php } ?><div class="dropdown-panel shift-code"id=shift_code><span class=overlay-hashttarget></span><button class="dropdown-panel-toggle header"data-custom-labels='{"false": "Expand SHiFT Code", "true": "Collapse SHiFT Code"}'><div class=wrapper><div class=title><div class=icon aria-hidden=true title="SHiFT Code"><span class="fas fa-key"></span></div><div class=string><h2 class="primary reward"></h2><span class="labels secondary"><?php
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
              ?><?php foreach ($labels as $label) : ?><span class="<?= "label layer-target {$label['class']}"; ?>"><span><?= $label['name']; ?></span></span><div class="layer tooltip"data-layer-delay=medium><?= $label['title']; ?></div><?php endforeach; ?></span></div></div><div class=indicator aria-hidden=true><span class="fas fa-chevron-right"></span></div><div class=full-width><div class="layer-target progress-bar"aria-valuemax=100 aria-valuemin=0 role=progressbar><div class=progress></div></div><div class="layer tooltip use-cursor"data-layer-delay=medium></div></div></div></button><dl class="body active"><div class="force-size multi-view"data-view-type=toggle><div class="layer-target code view"id=shift_code_view_code><div class=background aria-hidden=true><span class="fas fa-key"></span></div><div class="section can-split release"><dt class=title>Release Date</dt><div class=content><dd class=layer-target></dd><div class="layer tooltip"data-layer-delay=medium></div><span class="content simple"aria-hidden=true><span></span></span></div></div><div class="section can-split expiration"><dt class=title>Expiration Date</dt><div class=content><dd class=layer-target></dd><div class="layer tooltip"data-layer-delay=medium></div><span class="content simple"aria-hidden=true><span></span></span></div></div><div class="section src"><dt class=title>Source<dd class=content><a class="layer-target link tr-underline"rel="external noopener"target=_blank><span class="fas fa-external-link-square-alt"aria-hidden=true title="External Link"> </span></a><div class="layer tooltip"data-layer-delay=medium>SHiFT Code Source (External Link)</div><span class="layer-target no-link">N/A</span><div class="layer tooltip"data-layer-delay=medium>No Source URL</div></div><div class="section notes"><dt class=title>Notes<dd class=content><ul class=styled></ul></div><div class=separator></div><?php foreach (["pc", "xbox", "ps"] as $group) : ?><div class="<?= "section $group"; ?>"><dt class=title></dt><div class="content code"><input class="clipboard-copy value"hidden readonly tabindex=-1><dd class="layer-target display"></dd><div class="layer tooltip"data-layer-delay=medium><?= strtoupper($group) . " "; ?>SHiFT Code</div><button class="layer-target copy"data-copy-target=1><span class="fas fa-clipboard"></span></button><div class="layer tooltip">Copy to Clipboard</div></div></div><?php endforeach; ?><div class=footer><div class=actions><button class="layer-target styled action color redeem"aria-pressed=false><div class=label><span class="fas icon fa-bookmark"aria-hidden=true>  </span><span>Redeem</span></div><?php include(HTML_INCLUDES_PATH . 'local/spinner.php'); ?></button><div class="layer tooltip wrapped">Mark this SHiFT Code as Redeemed</div><div class=options-menu-container><button class="layer-target styled action color light options-menu"id=shift_code_options_menu><span class="fas icon fa-ellipsis-h"aria-hidden=true></span></button><div class="layer tooltip"id=shift_code_options_menu_tooltip>SHiFT Code Options</div><?php shiftCodeOptionsMenu(0); ?></div></div><div class=separator aria-hidden=true></div><div class=code-info><div class=id><dt>ID</dt> <dd class=layer-target></dd><div class="layer tooltip"data-layer-delay=medium></div></div><div class=last-update><dt>Last Updated</dt> <dd class=layer-target></dd><div class="layer tooltip"data-layer-delay=medium></div></div><div class=owner><dt>Submitted By</dt> <dd class=layer-target></dd><div class="layer tooltip"data-layer-delay=medium></div></div></div></div></div><?php shiftCodeOptionsMenu(1); ?><div class="view edit"id=shift_code_view_edit><?php
            include(FORMS_PATH . 'shift/shift-code.php');

            $form_shiftCode->insertForm();
          ?></div></div></dl><div class="body deleted">This SHiFT Code was deleted <em class="layer-target timestamp">today</em><div class="layer tooltip"data-layer-delay=medium></div>.</div></div></template><div class=modal id=shift_code_deletion_confirmation_modal><div class=title>Delete SHiFT Code</div><div class=body><?php
      include_once(FORMS_PATH . 'shift/delete.php');

      $form_shiftDelete->insertForm();
    ?></div></div>