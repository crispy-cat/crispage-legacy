<?php
	defined("CRISPAGE") or die("Application must be started from index.php!");

	$logopath = $app->getSetting("logopath", null);
	$uselogo = $logopath != null && $logopath != "";
	$sitename = $app->getSetting("sitename", null);

	$app->page->metas["viewport"] = array("name" => "viewport", "content" => "width=device-width, initial-scale=1");

	$app->page->links["apple-touch-icon"] = array("rel" => "apple-touch-icon", "sizes" => "180x180", "href" => Config::WEBROOT . "/media/icons/apple-touch-icon.png");
	$app->page->links["favicon-16"] = array("rel" => "icon", "type" => "image/png", "sizes" => "16x16", "href" => Config::WEBROOT . "/media/icons/favicon-16x16.png");
	$app->page->links["favicon-32"] = array("rel" => "icon", "type" => "image/png", "sizes" => "32x32", "href" => Config::WEBROOT . "/media/icons/favicon-32x32.png");
	$app->page->links["favicon-194"] = array("rel" => "icon", "type" => "image/png", "sizes" => "194x194", "href" => Config::WEBROOT . "/media/icons/favicon-194x194.png");
	$app->page->links["android-chrome-icon"] = array("rel" => "icon", "type" => "image/png", "sizes" => "192x192", "href" => Config::WEBROOT . "/media/icons/android-chrome-192x192.png");
	$app->page->links["shortcut-icon"] = array("rel" => "shortcut icon","href" => Config::WEBROOT . "/media/icons/favicon.ico");

	$app->page->styles["bootstrap"] = array("content" => file_get_contents(Config::APPROOT . "/media/system/css/bootstrap.min.css"));
	$app->page->styles["fonts"] = array("content" => file_get_contents(__DIR__ . "/css/fonts.css"));
	$app->page->styles["bs-icons"] = array("content" => file_get_contents(Config::APPROOT . "/media/system/css/bootstrap-icons.css"));
	$app->page->styles["template"] = array("content" => file_get_contents(__DIR__ . "/css/template.css"));

	$app->page->scripts["jquery"] = array("content" => file_get_contents(Config::APPROOT . "/media/system/js/jquery.min.js"), "defer" => "");
	$app->page->scripts["bootstrap"] = array("content" => file_get_contents(Config::APPROOT . "/media/system/js/bootstrap.bundle.min.js"), "defer" => "");
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $app->page->getBrowserTitle(); ?></title>
		<?php $app->page->renderMetas(); ?>
		<?php $app->page->renderLinks(); ?>
		<?php $app->page->renderStyles(); ?>
		<?php $app->page->renderScripts(); ?>
	</head>
	<body>
		<nav class="navbar navbar-expand-xl navbar-dark bg-crispycat mb-3">
			<div class="container-fluid">
				<a class="navbar-brand" href="<?php echo Config::WEBROOT; ?>/backend"><?php echo ($uselogo) ? "<img src=\"$logopath\" alt=\"$sitename\" height=\"30\" />" : $sitename; ?></a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbar">
					<ul class="navbar-nav me-auto">
						<li class="nav-item">
							<a class="nav-link" href="<?php echo Config::WEBROOT; ?>/installer/default">Installer</a>
						</li>
					</ul>
				</div>
			</div>
		</nav>
		<div class="container">
			<div class="row">
				<div class="col">
					<?php $app->page->renderAlerts(); ?>
					<?php $app->page->renderContent(); ?>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<hr />
					<small class="text-muted">Crispage v<?php echo CRISPAGE; ?> &bull; This software is licensed under the GNU GPL v3 License.</small>
				</div>
			</div>
		</div>
	</body>
</html>
