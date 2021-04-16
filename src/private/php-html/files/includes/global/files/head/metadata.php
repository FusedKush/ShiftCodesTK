<?php
	use ShiftCodesTK\PageConfiguration,
			ShiftCodesTK\Strings;
	use const \ShiftCodesTK\SITE_BACKGROUND_COLOR,
			\ShiftCodesTK\THEME_COLORS,
			\ShiftCodesTK\SITE_DOMAIN;
	
	$__metadata = [
		'bg'	   => SITE_BACKGROUND_COLOR,
		'token'  => $_SESSION['token'],
		'themes' => json_encode(THEME_COLORS),
		'domain' => SITE_DOMAIN,
		'page'	 => []
	];
	$__metadata['page']['configuration'] = PageConfiguration::getCurrentPageConfiguration();
	$__metadata['page']['metadata'] = array_merge(
		$__metadata['page']['configuration']->getGeneralInfo(),
		[
			'title'				=> $__metadata['page']['configuration']->getTitle(true),
			'image'				=> $__metadata['page']['configuration']->getImage(PageConfiguration::IMAGE_FORMAT_METADATA),
			'canonical'		=> $__metadata['page']['configuration']->getPath(PageConfiguration::PATH_FORMAT_CANONICAL),
		]
	);
	
	array_walk_recursive($__metadata, function (&$value, $key) {
		if ($key !== 'configuration' && $key !== 'themes') {
			$value = Strings\encode_html($value);
		}
	});
	
?>

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
<meta name="theme-color" content="<?= $__metadata['bg']; ?>">
<!-- Custom Properties -->
<meta name="tk-bg-color" content="<?= $__metadata['bg']; ?>">
<meta name="tk-request-token" content="<?= $__metadata['token']; ?>">
<!-- Theme Colors -->
<meta name="tk-theme-colors" content=<?= $__metadata['themes']; ?>>

<!-- Page-Specific Properties -->
<!-- Facebook & Twitter Properties -->
<?php foreach ([ 'title', 'description', 'image' ] as $property) : ?>
  <meta property="og:<?= $property; ?>" content="<?= $__metadata['page']['metadata'][$property]; ?>">
<?php endforeach; ?>

<title><?= $__metadata['page']['metadata']['title']; ?></title>
<!-- Canonical Page Location -->
<meta name="canonical" href="<?= $__metadata['page']['metadata']['canonical']; ?>">
<meta property="og:url" content="<?= $__metadata['page']['metadata']['canonical']; ?>">
<!-- Browser Properties -->
<link rel="manifest" href="/assets/manifests/<?= $__metadata['page']['metadata']['theme']; ?>.webmanifest">
<!-- Custom Properties -->
<meta name="tk-theme-color" content="<?= $__metadata['page']['metadata']['theme']; ?>">

<?php unset($__metadata); ?>