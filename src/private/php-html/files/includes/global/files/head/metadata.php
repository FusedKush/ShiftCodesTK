<?php
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
<!-- Theme Colors -->
<meta name="tk-theme-colors" content=<?= clean_all_html(json_encode($themeColors)); ?>>

<!-- Page-Specific Properties -->
<?php
  $social_metadata = ['title', 'description', 'image'];
  $site_domain = \ShiftCodesTK\SITE_DOMAIN;
?>
<!-- Facebook & Twitter Properties -->
<?php foreach ($social_metadata as $meta) : ?>
  <?php
    $meta_content = PAGE_SETTINGS['meta'][$meta];

    if ($meta == 'image') {
      $meta_content = "https://{$site_domain}/assets/img/metadata/{$meta_content}";
    }
  ?>

  <meta property="<?= "og:$meta"; ?>" content="<?= $meta_content; ?>">
<?php endforeach; ?>

<title><?= PAGE_SETTINGS['meta']['title']; ?></title>
<!-- Canonical Page Location -->
<meta name="canonical" href="https://<?= $site_domain . PAGE_SETTINGS['meta']['canonical']; ?>">
<meta property="og:url" content="https://<?= $site_domain . PAGE_SETTINGS['meta']['canonical']; ?>">
<!-- Browser Properties -->
<link rel="manifest" href="/assets/manifests/<?= PAGE_SETTINGS['meta']['theme']; ?>.webmanifest">
<!-- Custom Properties -->
<meta name="tk-theme-color" content="<?= $themeColors[PAGE_SETTINGS['meta']['theme']]; ?>">