<?php
  /**
   * The domain the live site resides on
   */
  $siteDomain = 'shiftcodestk.com';
  /**
   * Theme color codes
   */
  $themeColors = [
    'bg'   => '#0f1d2c',
    'main' => '#foo',
    'bl1'  => '#dc143c',
    'bl2'  => '#ff4500',
    'tps'  => '#1e90ff',
    'bl3'  => '#ffa900'
  ];
?><meta content=ShiftCodesTK name=author><meta name=icon href=/favicon.ico type=image/x-icon><meta content="width=device-width,initial-scale=1"name=viewport><meta content=259185334481064 property=fb:app_id><meta content=1920 property=og:image:width><meta content=1080 property=og:image:height><meta content=Website property=og:type><meta content=summary property=twitter:card><meta content=@ShiftCodesTK property=twitter:site><meta content=@ShiftCodesTK property=twitter:creator><meta content="ShiftCodesTK Logo"property=twitter:image:alt><meta content="<?= $themeColors['bg']; ?>"name=theme-color><meta content="<?= $themeColors['bg']; ?>"name=tk-bg-color><meta content="<?= $_SESSION['token']; ?>"name=tk-request-token><?php
  $socialMetadata = ['title', 'description', 'image'];
?><?php foreach ($socialMetadata as $meta) : ?><meta content="<?= PAGE_SETTINGS['meta'][$meta]; ?>"property="<?= "og:$meta"; ?>"><?php endforeach; ?><title><?= PAGE_SETTINGS['meta']['title']; ?></title><meta name=canonical href="<?= "https://{$siteDomain}{PAGE_SETTINGS['meta']['canonical']}"; ?>"><meta content="<?= "https://{$siteDomain}{PAGE_SETTINGS['meta']['canonical']}"; ?>"property=og:url><link href="/assets/manifests/<?= PAGE_SETTINGS['meta']['theme']; ?>.webmanifest"rel=manifest><meta content="<?= $themeColors[PAGE_SETTINGS['meta']['theme']]; ?>"name=tk-theme-color>