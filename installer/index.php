<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/index.php - Installer entry script

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	if (file_exists(__DIR__ . "/../config.php")) {
		define("IS_INSTALLED", true);
		require_once __DIR__ . "/../config.php";
	} else {
		define("IS_INSTALLED", false);
		require_once __DIR__ . "/installer_config.php";
	}

	require_once Config::APPROOT . "/installer/core/Installer.php";
	require_once Config::APPROOT . "/core/Router.php";
	$app = new Installer();
	set_exception_handler(function($e) {
		global $app;
		$app->error($e);
	});
	if (IS_INSTALLED) $app->initDatabase("JSONDatabase", array(
		"location" => Config::DB_JSON_LOC,
		"name" => Config::DB_JSON_NAME,
		"pretty" => Config::DB_JSON_PRETTY
	));
	Router::routeRequest("/installer", "default");
?>
