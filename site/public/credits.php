<?php
  $page['meta'] = [
    'title'       => 'Credits - ShiftCodesTK',
    'description' => 'The people and projects that make ShiftCodesTK possible',
    'canonical'   => '/credits',
    'image'       => 'tps/2',
    'theme'       => 'main'
  ];

  require_once('initialize.php');
?><!doctypehtml><html lang=en><meta charset=utf-8><?php include_once('global/sharedStyles.php'); ?><link href="/assets/css/local/credits.css<?php echo TK_VERSION_STR; ?>"rel=stylesheet><?php include_once('global/head.php'); ?><body data-theme=main><?php include_once('global/beforeContent.php'); ?><?php include_once('global/main-header.php'); ?><main><section class="content-wrapper credits"><div class=banner id=banner><div class=header><div class=flag aria-label="Coded with Love by Zach Vaughan"title="Coded with Love by Zach Vaughan"><span class="fas fa-code"aria-label=Coded title=Coded></span>with<span class="fas fa-heart"aria-label=Love title=Love></span>by<strong>Zach Vaughan</strong></div></div><div class=description><p><i>ShiftCodesTK was Coded & Created, Updated & Maintained, and filled with Coffee & Love by Zach Vaughan</i></div></div><a aria-describedby=module_font_awesome_description aria-labelledby=module_font_awesome_name class=module href=https://fontawesome.com id=module_font_awesome rel="external noopener"target=_blank><div class=header><span class=icon><span class="fa-font-awesome-flag fab"aria-label="Font Awesome Flag"title="Font Awesome Flag"></span></span><span class=info><h3 id=module_font_awesome_name>FontAwesome</h3><span>fontawesome.com</span></span></div><p class=description id=module_font_awesome_description>FontAwesome provided all of the, well, <em>awesome</em> icons that can be found all across the site.</p></a><a aria-describedby=module_loading_io_description aria-labelledby=module_loading_io_name class=module href=https://loading.io id=module_loading_io rel="external noopener"target=_blank><div class=header><span class=icon><span class="fas fa-spinner"aria-label=Spinner title=Spinner></span></span><span class=info><h3>Loading.io</h3><span>loading.io</span></span></div><p class=description id=module_loading_io_description>Loading.io provided the cool, lightweight loading icons that are used on the site.</p></a><a aria-describedby=module_cloudflare_description aria-labelledby=module_cloudflare_name class=module href=https://www.cloudflare.com/ id=module_cloudflare rel="external noopener"target=_blank><div class=header><span class=icon><span class="fas fa-cloud"aria-label=Cloud title=Cloud></span></span><span class=info><h3>Cloudflare</h3><span>cloudflare.com</span></span></div><p class=description id=module_loading_io_description>Cloudflare provides many benefits that greatly improve the speed, reliability, and security of ShiftCodesTK.</p></a><div class=shoutout>All images, logos, and trademarks are the rightful property of their respective owners.</div></section></main><?php include_once('global/afterContent.php'); ?><?php include_once('global/sharedScripts.php'); ?>