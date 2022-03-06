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

	$app->page->styles["bootstrap"] = array("content" => file_get_contents(__DIR__ . "/css/bootstrap.min.css"));
	$app->page->styles["fonts"] = array("content" => file_get_contents(__DIR__ . "/css/fonts.css"));
	$app->page->styles["bs-icons"] = array("content" => file_get_contents(__DIR__ . "/css/bootstrap-icons.css"));
	$app->page->styles["template"] = array("content" => file_get_contents(__DIR__ . "/css/template.css"));

	$app->page->scripts["jquery"] = array("content" => file_get_contents(__DIR__ . "/js/jquery.min.js"), "defer" => "");
	$app->page->scripts["bootstrap"] = array("content" => file_get_contents(__DIR__ . "/js/bootstrap.bundle.min.js"), "defer" => "");
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $app->page->getBrowserTitle(); ?></title>
		<?php $app->page->renderMetas(); ?>
		<?php $app->page->renderLinks(); ?>
	</head>
	<body>
		<nav class="navbar navbar-expand-lg navbar-dark bg-crispycat">
			<div class="container-fluid">
				<a class="navbar-brand" href="<?php echo Config::WEBROOT . "/"; ?>"><?php echo ($uselogo) ? "<img src=\"$logopath\" alt=\"$sitename\" height=\"30\" />" : $sitename; ?></a>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbar">
					<?php $app->page->renderModules("navbar"); ?>
				</div>
			</div>
		</nav>
		<div class="bg-light mb-3">
			<div class="container py-5">
				<div class="row">
					<div class="col">
						<h1 class="display-3"><?php echo $app->page->getTitle(); ?></h1>
						<hr />
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col-lg-9">
					<?php $app->page->renderAlerts(); ?>
					<?php $app->page->renderModules("before-content"); ?>
					<?php $app->page->renderContent(); ?>
					<?php $app->page->renderModules("after-content"); ?>
				</div>
				<div class="col-lg-3 bg-light p-3">
					<?php $app->page->renderModules("sidebar"); ?>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<hr />
					<?php $app->page->renderModules("footer"); ?>
				</div>
			</div>
		</div>
		<?php $app->page->renderStyles(); ?>
		<?php $app->page->renderScripts(); ?>
	</body>
</html>
