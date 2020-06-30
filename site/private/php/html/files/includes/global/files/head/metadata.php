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
?>

<!-- Global Properties -->
<!-- Standard Metadata -->
<meta name="author" content="ShiftCodesTK">
<meta name="icon" type="image/x-icon" href="/favicon.ico">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Facebook Metadata -->
<meta property="fb:app_id" content="259185334481064">
<meta property="og:image:width" content="1920">
<meta property="og:image:height" content="1080">
<meta property="og:type" content="Website">
<!-- Twitter Metadata -->
<meta property="twitter:card" content="summary">
<meta property="twitter:site" content="@ShiftCodesTK">
<meta property="twitter:creator" content="@ShiftCodesTK">
<meta property="twitter:image:alt" content="ShiftCodesTK Logo">
<!-- Browser Properties -->
<meta name="theme-color" content="<?= $themeColors['bg']; ?>">
<!-- Custom Properties -->
<meta name="tk-bg-color" content="<?= $themeColors['bg']; ?>">
<meta name="tk-request-token" content="<?= $_SESSION['token']; ?>">

<!-- Page-Specific Properties -->
<?php
  $socialMetadata = ['title', 'description', 'image'];
?>
<!-- Facebook & Twitter Properties -->
<?php foreach ($socialMetadata as $meta) : ?>
  <meta property="<?= "og:$meta"; ?>" content="<?= PAGE_SETTINGS['meta'][$meta]; ?>">
<?php endforeach; ?>
<title><?= PAGE_SETTINGS['meta']['title']; ?></title>
<!-- Canonical Page Location -->
<meta name="canonical" href="<?= "https://{$siteDomain}{PAGE_SETTINGS['meta']['canonical']}"; ?>">
<meta property="og:url" content="<?= "https://{$siteDomain}{PAGE_SETTINGS['meta']['canonical']}"; ?>">
<!-- Browser Properties -->
<link rel="manifest" href="/assets/manifests/<?= PAGE_SETTINGS['meta']['theme']; ?>.webmanifest">
<!-- Custom Properties -->
<meta name="tk-theme-color" content="<?= $themeColors[PAGE_SETTINGS['meta']['theme']]; ?>">