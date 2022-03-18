<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/default.php - Installer start page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/installer/header.php";

	$app->page->setTitle("Install & Manage");

	$app->page->setContent(function($app) {
?>
	<h1>Install & Manage</h1>
	<p>Welcome to the Crispage installer. Please select an option from the menu below.</p>

	<table class="table">
		<tbody>
			<tr>
				<td><a href="<?php echo Config::WEBROOT; ?>/installer/install/default"><h2>Install</h2></a></td>
				<td>Install or update Crispage.</td>
			</tr>
			<tr>
				<td><a href="<?php echo Config::WEBROOT; ?>/installer/extensions/list"><h2>Extensions</h2></a></td>
				<td>Install or uninstall extensions.</td>
			</tr>
			<tr>
				<td><a href="<?php echo Config::WEBROOT; ?>/installer/repair/default"><h2>Repair</h2></a></td>
				<td>Fix common issues with Crispage.</td>
			</tr>
		</tbody>
	</table>
<?php
	});

	$app->renderPage();
?>
