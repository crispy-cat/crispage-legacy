<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/install/default.php - Installer installation start page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once \Config::APPROOT . "/installer/header.php";

	$app->page->setTitle($app("i18n")->getString("install"));

	$app->page->setContent(function($app) {
?>
	<h1><?php $app("i18n")("install"); ?></h1>
	<p><?php $app("i18n")("select_option_below"); ?></p>

	<table class="table">
		<tbody>
			<tr>
				<td><a href="<?php echo \Config::WEBROOT; ?>/installer/install/install"><h2><?php $app("i18n")("install"); ?></h2></a></td>
				<td><?php $app("i18n")("install_ex2"); ?></td>
			</tr>
			<tr>
				<td><a href="<?php echo \Config::WEBROOT; ?>/installer/install/update"><h2><?php $app("i18n")("update"); ?></h2></a></td>
				<td><?php $app("i18n")("update_ex"); ?></td>
			</tr>
		</tbody>
	</table>
<?php
	});

	$app->renderPage();
?>
