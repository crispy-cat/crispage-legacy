<?php
	defined("CRISPAGE") or die("Application must be started from index.php!");

	$logopath = $app->getSetting("logopath", null);
	$uselogo = !empty($logopath);
	$sitename = $app->getSetting("sitename", null);
	$primary = $app->getSetting("colors.primary", "#002060");
	$secondary = $app->getSetting("colors.secondary", "#0d6efd");
	$iconsloc = $app->getSetting("icons_location", Config::WEBROOT . "/media/icons");
	$showtitle = $app->page->options["show_title"] ?? $app->getSetting("articles.show_title", "yes");
	$showsidebar = $app->page->options["show_sidebar"] ?? $app->getSetting("articles.show_sidebar", "yes");

	$app->page->metas["viewport"] = array("name" => "viewport", "content" => "width=device-width, initial-scale=1");

	$app->page->links["apple-touch-icon"] = array("rel" => "apple-touch-icon", "sizes" => "180x180", "href" => $iconsloc . "/apple-touch-icon.png");
	$app->page->links["favicon-16"] = array("rel" => "icon", "type" => "image/png", "sizes" => "16x16", "href" => $iconsloc . "/favicon-16x16.png");
	$app->page->links["favicon-32"] = array("rel" => "icon", "type" => "image/png", "sizes" => "32x32", "href" => $iconsloc . "/favicon-32x32.png");
	$app->page->links["favicon-194"] = array("rel" => "icon", "type" => "image/png", "sizes" => "194x194", "href" => $iconsloc . "/favicon-194x194.png");
	$app->page->links["android-chrome-icon"] = array("rel" => "icon", "type" => "image/png", "sizes" => "192x192", "href" => $iconsloc . "/android-chrome-192x192.png");
	$app->page->links["shortcut-icon"] = array("rel" => "shortcut icon","href" => $iconsloc . "/favicon.ico");

	$app->page->styles["bootstrap"] = array("content" => file_get_contents(Config::APPROOT . "/media/system/css/bootstrap.min.css"));
	$app->page->styles["fonts"] = array("content" => file_get_contents(__DIR__ . "/css/fonts.css"));
	$app->page->styles["bs-icons"] = array("content" => file_get_contents(Config::APPROOT . "/media/system/css/bootstrap-icons.css"));
	$app->page->styles["template"] = array("content" => file_get_contents(__DIR__ . "/css/template.css"));

	$app->page->scripts["jquery"] = array("content" => file_get_contents(Config::APPROOT . "/media/system/js/jquery.min.js"), "defer" => "");
	$app->page->scripts["bootstrap"] = array("content" => file_get_contents(Config::APPROOT . "/media/system/js/bootstrap.bundle.min.js"), "defer" => "");
?>
<!DOCTYPE html>
<html lang="<?php echo $app("i18n")->getLanguage(); ?>">
	<head>
		<title><?php echo $app->page->getBrowserTitle(); ?></title>
		<?php $app->page->renderMetas(); ?>
		<?php $app->page->renderLinks(); ?>
		<?php $app->page->renderStyles(); ?>
		<?php $app->page->renderScripts(); ?>
	</head>
	<body>
		<nav class="navbar navbar-expand-lg navbar-dark" style="background: <?php echo $primary; ?>;">
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
		<?php if ($showtitle == "yes") { ?>
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
		<?php } ?>
		<div class="container">
			<div class="row">
				<div class="<?php echo ($showsidebar == "yes") ? "col-lg-9" : "col-12"; ?>">
					<?php $app->page->renderAlerts(); ?>
					<?php $app->page->renderModules("before-content"); ?>
					<?php $app->page->renderContent(); ?>
					<?php $app->page->renderModules("after-content"); ?>
				</div>
				<?php if ($showsidebar == "yes") { ?>
					<div class="col-lg-3 bg-light p-3">
						<?php $app->page->renderModules("sidebar"); ?>
					</div>
				<?php } ?>
			</div>
			<div class="row">
				<div class="col">
					<hr />
					<?php $app->page->renderModules("footer"); ?>
				</div>
			</div>
		</div>
	</body>
</html>
