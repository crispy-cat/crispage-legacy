<?php
	defined("CRISPAGE") or die("Application must be started from index.php!");

	$app->page->links["installer_style"] = array("rel" => "stylesheet", "href" => Config::WEBROOT . "/templates/installer/assets/installer.css");
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo $app->page->getBrowserTitle(); ?></title>
		<?php $app->page->renderLinks(); ?>
		<?php $app->page->renderScripts(); ?>
	</head>
	<body>
		<div id="head">
			<img src="<?php echo Config::WEBROOT; ?>/media/crispage.png" height="36" />
			<h1>Crispage Installer</h1>
		</div>
		<hr />
		<div id="menu">
			<a href="<?php echo Config::WEBROOT; ?>/installer/default">Start</a>
		</div>
		<hr />
		<div id="main">
			<h1><?php echo $app->page->getTitle(); ?></h1>
			<?php $app->page->renderAlerts(); ?>
			<?php $app->page->renderContent(); ?>
		</div>
		<hr />
		<div id="foot">
			<small>&copy;<?php echo date("Y"); ?> crispycat &bull; This software is licensed under the GNU GPL v3 License.</small>
		</div>
	</body>
</html>
