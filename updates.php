<?php include_once($_SERVER['DOCUMENT_ROOT'] . '/assets/php/html/min/imports/importPath.php'); ?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/styles/css/min/local/updates.min.css<?php echo $svQueryString; ?>"rel=stylesheet><title>Updates - ShiftCodesTK</title><meta content="Updates - ShiftCodesTK"name=title><meta content="Updates - ShiftCodesTK"property=og:title><meta content="Updates - ShiftCodesTK"property=twitter:title><meta content="Recent changes and updates to ShiftCodesTK"name=description><meta content="Recent changes and updates to ShiftCodesTK"property=og:description><meta content="Recent changes and updates to ShiftCodesTK"property=twitter:description><meta name=canonical href=https://shiftcodes.tk/updates><meta content=https://shiftcodes.tk/updates property=og:url><meta content='[{"name": "Updates", "url": "/updates"}]'name=breadcrumbs id=breadcrumbs><meta content=https://shiftcodes.tk/assets/img/metadata/updates.png property=og:image><meta content=https://shiftcodes.tk/assets/img/metadata/updates.png property=twitter:image><link href=/assets/manifests/main.webmanifest rel=manifest><meta content=#f00 name=theme-color-tm id=theme_color_tm><?php include_once('global/head.php'); ?><body data-theme=main><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><header class=updates-header id=updates_header><div class=content-wrapper><div class="section current"aria-hidden=true data-hidden=true hidden><span><span class=title>Current Version:</span> <a class="currentver interal tr-underline"id=updates_header_current><strong></strong></a></span></div><div class="section jump"><button aria-disabled=true aria-haspopup=true aria-label="Jump to Changelog"aria-pressed=false autocomplete=off data-pressed=false disabled id=updates_header_jump title="Jump to Changelog"><span>Jump to <span class="fas fa-caret-down"></span></span></button><div class="dropdown-menu no-refocus"data-align=right data-pos=bottom data-target=updates_header_jump id=updates_header_jump_dropdown><div class=panel><div class=title>Jump to:</div><ul class="choice-list scrollable"></ul></div></div></div></div></header><main class=content-wrapper><template id=panel_template><section class="changelog dropdown-panel"><button class=header data-custom-labels='{"false": "Expand Changelog", "true": "Collapse Changelog"}'><div class=wrapper><div class=title><div class=icon><span class=fas></span></div><div class=string><h2 class="primary version"></h2><div class="info secondary"><span class=date></span><span class=separator>•</span><span class=type></span></div></div></div><div class=indicator><span class="fas fa-chevron-right"></span></div></div></button><div class="body content-container"></div></section></template></main><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?><script async src="/assets/scripts/min/local/updates.min.js<?php echo $svQueryString; ?>"></script>