<?php
	/*
		Crispage - A lightweight CMS for developers
		installer/index.php - Installer entry script

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.2.0
	*/

	require_once __DIR__ . "/../config.php";

	require_once Config::APPROOT . "/installer/core/Installer.php";
	include_once Config::APPROOT . "/core/Router.php";
	$app = new Installer();
	Router::routeRequest("/installer", "default");
?>
