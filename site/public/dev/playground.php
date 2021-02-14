<?php
  $page['meta'] = [
    'title'       => 'Development Playground - ShiftCodesTK',
    'description' => 'A testing ground and reference list for elements and their associated styles & functionality.',
    'canonical'   => '/dev/playground',
    'image'       => 'bl1/4',
    'theme'       => 'main'
  ];
  $page['auth'] = [
    'requireState'   => 'auth'
  ];

  require_once('../initialize.php');
?><!doctype html><html lang=en><head><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/css/local/dev/playground.css<?php echo TK_VERSION_STR; ?>" rel=stylesheet><?php include_once('global/head.php'); ?></head><body data-theme=main><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><main class=content-wrapper><div class=multi-view data-view=playground-categories data-view-type=tabs><div class="view php-tests" data-view=Scratchpad><div class=title><h2>Scratchpad</h2></div><?php 
            use ShiftCodesTK\Validations;

            $var = "FoobarBaz";
            var_dump(new Validations\VariableEvaluator([
              'type' => 'string'
            ], $var));
          ?></div><div class="view profile-cards" data-view="Profile Cards"><div class=title><h2>Profile Cards</h2><p>Profile Cards are used to represent a User of ShiftCodesTK.</p></div><section><div class=profile-card data-card-flags=CARD_SHOW_ROLES|CARD_SHOW_STATS|CARD_SHOW_ACTIONS|CARD_ALLOW_EDITING data-card-user=149357043452></div></section></div><div class="view buttons" data-view=Buttons><?php function getButtonList ($classes = '', $pressed = false) { ?><?php
              $buttonColors = [
                'light',
                'dark',
                'info',
                'success',
                'warning',
                'danger',
                'theme',
                'class-theme bl2',
                'class-theme tps',
                'class-theme bl1',
                'class-theme bl3'
              ];
              $ariaMarkup = $pressed ? ' aria-pressed="true"' : '';

              if ($pressed) {
                $classes .= "o-pressed";
              }

              if ($classes != "") {
                $classes = " {$classes}";
              }
            ?><div class="show-children-markup button-group"><button class="styled<?= $classes; ?>"<?= $ariaMarkup; ?>><span>Styled</span></button><?php foreach ($buttonColors as $color): ?><?php
                  if (strpos($color, 'class-theme') === false) {
                    $displayColor = ucfirst($color);
                  }
                  else {
                    $displayColor = str_replace('class-theme ', '', $color);
                    $displayColor = strtoupper($displayColor);
                  }
                ?><button class="styled<?= " {$color}{$classes}"; ?>"<?= $ariaMarkup; ?>><span><?= $displayColor; ?></span></button><?php endforeach; ?></div><?php } ?><section class=buttons><div class=title><h2>Buttons</h2><code>&lt;button></code></div><div class="group default"><div class=title><h3>Default Button</h3></div><button class=show-markup>An Unstyled Button</button></div><div class="styled group"><div class=title><h3>Styled Buttons</h3><code>styled</code></div><button class="styled show-markup">A Styled Button</button></div><div class="group alignment"><div class=title><h3>Aligned Buttons</h3><code>start</code>, <code>end</code></div><div class="show-children-markup button-group"><button class="styled start">Aligned to the <i>start</i></button> <button class=styled>A <i>centered</i> button</button> <button class="styled end">Aligned to the <i>end</i></button></div></div><div class="group button-groups"><div class=title><h3>Button Groups</h3><code>button-group</code></div><div><button class=styled>Some</button> <button class=styled>Ungrouped</button> <button class=styled>Buttons</button></div><div class="button-group show-markup"><button class=styled>Some</button> <button class=styled>Grouped</button> <button class=styled>Buttons</button></div></div><div class="group color"><div class=title><h3>Colored Buttons</h3><code>color</code></div><?php getButtonList(); ?></div><div class="group pressed"><div class=title><h3>Pressed Buttons</h3><code>aria-pressed</code><p>Works best without <em>Buttons Effects</em>.</p></div><?php getButtonList('', true); ?></div></section><section class=types><div class=title><h2>Button Types</h2><code>button-effect</code></div><div class="group hover"><div class=title><h3>Hover Buttons</h3><code>hover</code></div><?php getButtonList('button-effect hover'); ?></div><div class="group outline"><div class=title><h3>Outline Buttons</h3><code>outline</code></div><?php getButtonList('button-effect outline'); ?></div><div class="group text"><div class=title><h3>Text Buttons</h3><code>text</code></div><?php getButtonList('button-effect text'); ?></div></section></div><div class="view links" data-view=Links><div class=title><h2>Links</h2></div><div class="group default"><div class=title><h3>Default Link</h3></div><ul><li><a class=show-markup href=#>A default link</a></li></ul></div><div class="styled group"><div class=title><h3>Styled Links</h3><code>styled</code></div><ul class="styled show-children-markup"><li><a class=styled href=#>A styled link</a></li><li><a class="styled info" href=#>An info link</a></li><li><a class="styled success" href=#>A success link</a></li><li><a class="styled warning" href=#>A warning link</a></li><li><a class="styled danger" href=#>A dangerous link</a></li></ul></div><div class="group alt-effect"><div class=title><h3>Appearing Links</h3><code>appear</code></div><ul class="styled show-children-markup"><li><a class="styled appear" href=#>A styled link</a></li><li><a class="styled appear info" href=#>An info link</a></li><li><a class="styled appear success" href=#>A success link</a></li><li><a class="styled appear warning" href=#>A warning link</a></li><li><a class="styled appear danger" href=#>A dangerous link</a></li></ul></div><div class="group buttons"><div class=title><h3>Button Links</h3><code>button</code></div><div class="show-children-markup button-group"><a class=button href=#>A styled button link</a> <a class="button info" href=#>An info button link</a> <a class="button button-effect hover success" href=#>A success button link</a> <a class="button button-effect outline warning" href=#>A warning button link</a> <a class="button button-effect danger text" href=#>A dangerous button link</a></div></div></div></div></main><template id=code_block_template><div class="code-block single-thread"><button class="styled button-effect copy-to-clipboard layer-target outline" disabled hidden><span class="fas box-icon fa-clipboard"></span></button><pre aria-hidden=true class=copy-content></pre><div class=presentation></div><div class=line aria-expanded=true data-depth=0 data-line=-1><span class=line-number><button class="copy-to-clipboard number" data-copy=2></button> <button class=fold><span class="fas fa-chevron-right"></span></button> </span><span class="copy-content line-content"></span></div></div></template><?php include_once('global/afterContent.php'); ?><script async src="/assets/js/local/dev/playground.js<?php echo TK_VERSION_STR; ?>"></script><?php include_once('global/sharedScripts.php'); ?></body></html>