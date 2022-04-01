<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/extension/install.php - Installer extension installation page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.4.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/installer/header.php";

	$app->page->setTitle("Install Extension");

	$app->page->setContent(function($app) {
?>
	<h1>Install Extension</h1>

	<p>Please upload the extension package. Extension packs should be in .tar.gz or .zip format.</p>

	<form action="<?php echo Config::WEBROOT; ?>/installer/extensions/run_installation" method="post" enctype="multipart/form-data">
		<label for="extension_pack">Extension Pack:</label>
		<input type="file" class="form-control" name="extension_pack" required />

		<input type="submit" class="btn btn-success mt-2" value="Install Extension Pack" />
	</form>
<?php
	});

	$app->renderPage();
?>
