<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/views/extensions/uninstall.php - Installer extension delete page

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.4.0
	*/

	defined("CRISPAGE") or die("Application must be started from index.php!");
	require_once Config::APPROOT . "/installer/header.php";

	$id = $app->request->query["uninstall_id"] ?? null;

	if (!$id) $app->redirect(Config::WEBROOT . "/installer/extensions/list?me=No ID specified");

	$ext = $app->database->readRow("installation", $id);

	$app->extensions->unregisterExtensionByID($app->request->query["uninstall_id"]);

	switch ($ext["type"]) {
		case "view":
			FileHelper::deleteRecurs(Config::APPROOT . (($ext["scope"] == "backend") ? "/backend/" : "/") . "views/" . $ext["class"] . ".php");
			break;
		case "template":
			FileHelper::deleteRecurs(Config::APPROOT . "/templates/" . dirname($ext["class"]));
			break;
		case "module":
			FileHelper::deleteRecurs(Config::APPROOT . "/modules/" . $ext["class"] . ".json");
			FileHelper::deleteRecurs(Config::APPROOT . "/modules/" . $ext["class"] . ".php");
			break;
		case "plugin":
			FileHelper::deleteRecurs(Config::APPROOT . "/plugins/" . $ext["class"] . ".json");
			FileHelper::deleteRecurs(Config::APPROOT . "/plugins/" . $ext["class"] . ".php");
			break;
		default:
	}

	$app->redirect(Config::WEBROOT . "/installer/extensions/list?ms=Extension uninstalled");
?>
