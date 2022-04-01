<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/install/update.php - Installer update form page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.7.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/installer/header.php";

	$app->page->setTitle("Update Crispage");

	$app->page->setContent(function($app) {
?>
	<h1>Update Crispage</h1>

	<p>Please upload the update package. Update packs should be in .tar.gz or .zip format.</p>

	<form action="<?php echo Config::WEBROOT; ?>/installer/install/run_update" method="post" enctype="multipart/form-data">
		<label for="update_pack">Extension Pack:</label>
		<input type="file" class="form-control" name="update_pack" required />

		<input type="submit" class="btn btn-success mt-2" value="Install Update" />
	</form>
<?php
	});

	$app->renderPage();
?>
