<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/install/update.php - Installer update form page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.7.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/installer/header.php";

	$app->page->setTitle($app("i18n")->getString("update"));

	$app->page->setContent(function($app) {
?>
	<h1><?php $app("i18n")("update_crispage"); ?></h1>

	<p><?php $app("i18n")("please_select_package", null, ".tar.gz, .zip"); ?></p>

	<form action="<?php echo Config::WEBROOT; ?>/installer/script/package" method="post" enctype="multipart/form-data">
		<label for="package"><?php $app("i18n")("package_c"); ?></label>
		<input type="file" class="form-control" name="package" required />
		<input type="hidden" name="ploc" value="/installer/install/update" />
		<input type="submit" class="btn btn-success mt-2" value="<?php $app("i18n")("install_update"); ?>" />
	</form>
<?php
	});

	$app->renderPage();
?>
