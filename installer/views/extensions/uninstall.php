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

	if (!$id) $app->redirectWithMessages("/installer/extensions/list", array("type" => "error", "content" => "No ID specified"));

	$ext = $app->database->readRow("installation", $id);

	$app->extensions->unregisterExtensionByID($app->request->query["uninstall_id"]);

	switch ($ext["type"]) {
		case "view":
			unlink(Config::APPROOT . (($ext["scope"] == "backend") ? "/backend/" : "/") . "views/" . $ext["class"] . ".php");
			break;
		case "template":
			FileHelper::deleteRecurs(Config::APPROOT . "/templates/" . dirname($ext["class"]));
			break;
		case "module":
			unlink(Config::APPROOT . "/modules/" . $ext["class"] . ".json");
			unlink(Config::APPROOT . "/modules/" . $ext["class"] . ".php");
			break;
		case "plugin":
			foreach ($app->plugins->getPlugins() as $plugin) if ($plugin->class == $ext["class"]) $app->plugins->deletePlugin($plugin->id);
			unlink(Config::APPROOT . "/plugins/" . $ext["class"] . ".json");
			unlink(Config::APPROOT . "/plugins/" . $ext["class"] . ".php");
			break;
		default:
	}

	$app->redirectWithMessages("/installer/extensions/list", array("type" => "success", "content" => "Extension uninstalled"));
?>
