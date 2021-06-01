<?php
	use ShiftCodesTK\PageConfiguration,
			ShiftCodesTK\Strings;
	use const \ShiftCodesTK\SITE_BACKGROUND_COLOR,
			\ShiftCodesTK\THEME_COLORS,
			\ShiftCodesTK\SITE_DOMAIN;

?>

<?php (function () { ?>
	<?php
		$page_prefs = [
			'bg'	   => SITE_BACKGROUND_COLOR,
			'token'  => $_SESSION['token'],
			'themes' => json_encode(THEME_COLORS),
			'domain' => SITE_DOMAIN,
			'page'	 => []
		];
		$page_configuration = PageConfiguration::getCurrentPageConfiguration();
		$page_metadata = array_merge(
			$page_configuration->getGeneralInfo(),
			[
				'title'				=> $page_configuration->getTitle(true),
				'image'				=> $page_configuration->getImage(PageConfiguration::IMAGE_FORMAT_METADATA),
				'canonical'		=> $page_configuration->getPath(PageConfiguration::PATH_FORMAT_CANONICAL),
			]
		);
		
		array_walk_recursive($page_prefs, function (&$value, $key) {
			if ($key !== 'configuration') {
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
	<meta name="theme-color" content="<?= $page_prefs['bg']; ?>">
	<!-- Custom Properties -->
	<meta name="tk-bg-color" content="<?= $page_prefs['bg']; ?>">
	<meta name="tk-request-token" content="<?= $page_prefs['token']; ?>">
	<!-- Theme Colors -->
	<meta name="tk-theme-colors" content="<?= $page_prefs['themes']; ?>">

	<!-- Page-Specific Properties -->
	<!-- Facebook & Twitter Properties -->
	<?php foreach ([ 'title', 'description', 'image' ] as $property) : ?>
		<meta property="og:<?= $property; ?>" content="<?= $page_metadata[$property]; ?>">
	<?php endforeach; ?>

	<title><?= $page_metadata['title']; ?></title>
	<!-- Canonical Page Location -->
	<meta name="canonical" href="<?= $page_metadata['canonical']; ?>">
	<meta property="og:url" content="<?= $page_metadata['canonical']; ?>">
	<!-- Browser Properties -->
	<link rel="manifest" href="/assets/manifests/<?= $page_metadata['theme']; ?>.webmanifest">
	<!-- Custom Properties -->
	<meta name="tk-theme-color" content="<?= $page_metadata['theme']; ?>">
<?php })(); ?>