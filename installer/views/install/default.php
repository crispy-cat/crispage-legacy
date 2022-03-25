<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/install/default.php - Installer installation start page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/installer/header.php";

	$app->page->setTitle("Install");

	$app->page->setContent(function($app) {
?>
	<h1>Install</h1>
	<p>Please select an installation option from below.</p>

	<table class="table">
		<tbody>
			<tr>
				<td><a href="<?php echo Config::WEBROOT; ?>/installer/install/install"><h2>Install</h2></a></td>
				<td>Install Crispage.</td>
			</tr>
			<tr>
				<td><a href="<?php echo Config::WEBROOT; ?>/installer/install/update"><h2>Update</h2></a></td>
				<td>Install an update.</td>
			</tr>
		</tbody>
	</table>
<?php
	});

	$app->renderPage();
?>
