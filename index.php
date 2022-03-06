<?php
	/*
		Crispage - A lightweight CMS for developers
		index.php - Entry script

		Author: crispycat <the@crispy.cat> <https://crispy.cat>
		Since: 0.0.1
	*/

	require_once __DIR__ . "/config.php";

	define("CRISPAGE", "0.0.6 alpha");

	ini_set("display_errors", "1");
	ini_set("display_startup_errors", "1");
	error_reporting(Config::ERRORLVL);

	require_once Config::APPROOT . "/core/Application.php";
	include_once Config::APPROOT . "/core/Router.php";
	$app = new Application();
	Router::routeRequest();
?>
