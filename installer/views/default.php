<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/default.php - Installer start page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/installer/header.php";

	$app->page->setTitle($app("i18n")->getString("install_amp_manage"));

	$app->page->setContent(function($app) {
?>
	<h1><?php $app("i18n")("install_amp_manage"); ?></h1>

	<table class="table">
		<tbody>
			<tr>
				<td><a href="<?php echo Config::WEBROOT; ?>/installer/install/default"><h2><?php $app("i18n")("install"); ?></h2></a></td>
				<td><?php $app("i18n")("install_ex"); ?></td>
			</tr>
			<tr>
				<td><a href="<?php echo Config::WEBROOT; ?>/installer/extensions/list"><h2><?php $app("i18n")("extensions"); ?></h2></a></td>
				<td><?php $app("i18n")("extensions_ex"); ?></td>
			</tr>
			<tr>
				<td><a href="<?php echo Config::WEBROOT; ?>/installer/repair/default"><h2><?php $app("i18n")("repair"); ?></h2></a></td>
				<td><?php $app("i18n")("repair_ex"); ?></td>
			</tr>
		</tbody>
	</table>
<?php
	});

	$app->renderPage();
?>
